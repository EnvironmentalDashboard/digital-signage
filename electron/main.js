/**
 * Imports
 */
const electron = require('electron');
const fetch = require('node-fetch');
const WebSocket = require('ws');
const { app, BrowserView, BrowserWindow, ipcMain } = electron;


/**
 * Global variables
 */
const baseHttp = (process.env.APP_ENV === 'dev') ? 'http://localhost:5000' : 'https://environmentaldashboard.org';
const baseWs = (process.env.APP_ENV === 'dev') ? 'ws://localhost:5001' : 'wss://environmentaldashboard.org';
let win; // window will be closed automatically when the JavaScript object is garbage collected if no global reference kept
let width;
let height;
let presentations = {};
let conn; // websocket connection
let paused = false;
let currentPres = null;


/**
 * Initialize app
 */
app.on('ready', () => {
    createLandingWindow();
});
app.on('open-url', function (event, data) { // this will catch clicks on links such as <a href="communityhub://3">open in display 3</a>
    event.preventDefault();
    createWindow(new URL(data).host);
});
app.setAsDefaultProtocolClient('communityhub');
ipcMain.on('asynchronous-message', (event, arg) => {
    let id = parseInt(arg);
    if (id > 0) {
        createWindow(id);
    }
});

function createLandingWindow() {
    let size = electron.screen.getPrimaryDisplay().size;
    // Create the browser window
    win = new BrowserWindow({
        width: size.width,
        height: size.height,
        webPreferences: {
            nodeIntegration: true
        }
    });
    win.loadFile('setup.html');
}

function createWindow(displayId) {
    let size = electron.screen.getPrimaryDisplay().size;
    width = size.width;
    height = size.height;
    // Create the browser window
    win = new BrowserWindow({
        width: width,
        height: height,
        webPreferences: {
            nodeIntegration: true
        }
    });

    // Load the index.html of the app
    win.loadFile('index.html');

    win.setFullScreen(true);

    // Emitted when the window is closed.
    win.on('closed', () => {
        // Dereference the window object, usually you would store windows
        // in an array if your app supports multi windows, this is the time
        // when you should delete the corresponding element.
        win = null;
    });

    fetch(baseHttp + '/digital-signage/display/' + displayId + '/present')
        .then(res => res.json())
        .then(json => {
            for (const presentationId in json) {
                let viewData = {
                    carousels: {},
                    views: {},
                    next: null,
                    duration: null
                };
                for (const key in json[presentationId]) {
                    let x = 0;
                    let y = 0;
                    if (key === 'carousels') {
                        const carousels = json[presentationId][key];
                        for (const carouselId in carousels) {
                            if (viewData.carousels[carouselId] === undefined) {
                                viewData.carousels[carouselId] = [];
                            }
                            const frames = carousels[carouselId];
                            for (let i = 0; i < frames.length; i++) {
                                const frame = frames[i];
                                viewData.carousels[carouselId].push(frame);
                            }
                            let view = new BrowserView({
                                webPreferences: {
                                    nodeIntegration: false
                                    // sandbox: true
                                }
                            });
                            win.addBrowserView(view);
                            let viewWidth = width * (json[presentationId]['style'][carouselId].width / 100);
                            let viewHeight = height * (json[presentationId]['style'][carouselId].height / 100);
                            view.setBounds({ x: x, y: y, width: viewWidth, height: viewHeight });
                            y += viewHeight;
                            viewData.views = Object.assign({
                                [carouselId]: view
                            }, viewData.views);
                        }
                    }
                    if (key === 'style' || key === 'next' || key === 'duration') {
                        viewData[key] = json[presentationId][key];
                    }
                }
                presentations[presentationId] = viewData;
            }

            // open websocket conn to receive commands from remote controllers
            conn = new WebSocket(baseWs + "/digital-signage/websockets/display/" + displayId);
            conn.onerror = function () {
                console.log('Connection error');
                app.quit();
            };
            conn.onclose = function () {
                console.log('Connection close');
                app.quit();
            };
            conn.onmessage = commandReceiver;

            initApp();

        });
}


/**
 * Application code; rotates frames in carousels in a cycle of presentations
 */
let activeTimeouts = [];
let activeViews = [];
function initApp() {
    showPresentation(Object.keys(presentations)[0]); // begin by showing first presentation
}

function showPresentation(presentationId, targetFrame = null) {
    currentPres = presentationId;
    clearTimeouts();
    clearViews();
    setViews(presentations[presentationId], targetFrame);
    let timeout = setTimeout(() => {
        showPresentation(presentations[presentationId].next);
        // console.log(process.getCPUUsage());
    }, presentations[presentationId].duration);
    // let date = new Date();
    // console.log(date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds(), timeout);
    activeTimeouts.push(timeout);
}

function setViews(viewData, targetFrame) {

    for (const key in viewData.views) {
        const view = viewData.views[key];
        const carousels = viewData.carousels[key];
        let i = 0;
        if (targetFrame === null) {
            var firstFrame = carousels[i];
        } else {
            for (; i < carousels.length; i++) {
                if (carousels[i].id === targetFrame) {
                    var firstFrame = carousels[i];
                    break;
                }
            }
        }
        view.webContents.loadURL(firstFrame.url);
        win.addBrowserView(view);
        activeViews.push(view);
        if (carousels.length > 1) { // more frames to animate in this carousel
            animateFrames(view, carousels, i);
        }
    }
}

function animateFrames(view, carousel, current) {
    // assumes first frame already shown
    for (let i = 0; i < carousel.length; i++) {
        const frame = carousel[i];
        if (frame.id === carousel[current].next) {
            let timeout = setTimeout(() => {
                view.webContents.loadURL(frame.url);
                win.addBrowserView(view);
                animateFrames(view, carousel, (current === (carousel.length - 1)) ? 0 : current + 1);
            }, carousel[current].dur);
            activeTimeouts.push(timeout);
            break;
        }
    }

}

function clearTimeouts() {
    while (activeTimeouts.length > 0) {
        let timeout = activeTimeouts.pop();
        clearTimeout(timeout);
    }
}

function clearViews() {
    while (activeViews.length > 0) {
        let view = activeViews.pop();
        // view.webContents.destroy();
        view.webContents.loadURL('about:blank');
        win.removeBrowserView(view);

    }
}

function commandReceiver(e) {
    let ws_ready = true;
    if (ws_ready === false) {
        return;
    } else {
        ws_ready = false;
    }
    var button = JSON.parse(e.data);
    if (button.type === 1) {
        const target = 'frame' + button.trigger;
        for (const key in presentations) {
            const presentation = presentations[key];
            const carousels = presentation.carousels;
            for (const carouselId in carousels) {
                const carousel = carousels[carouselId];
                for (const twigKey in carousel) {
                    if (carousel.hasOwnProperty(twigKey)) {
                        const frame = carousel[twigKey];
                        if (frame.id === target) {
                            ws_ready = true;
                            showPresentation(key, frame.id);
                            return;
                        }
                    }
                }
            }
        }
    } else if (button.type === 2) { // pause
        ws_ready = true;
        if (!paused) {
            clearTimeouts();
            paused = true;
        } else {
            showPresentation(currentPres);
            paused = false;
        }
        return;
    } else {
        console.log('Unrecognized button type ' + button.type);
    }
    ws_ready = true;

}
