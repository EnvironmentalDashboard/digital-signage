const electron = require('electron');
const fetch = require('node-fetch');
const WebSocket = require('ws');
const { app, BrowserView, BrowserWindow } = electron;

// Keep a global reference of the window object, if you don't, the window will
// be closed automatically when the JavaScript object is garbage collected.
let win;

let width;
let height;
let presentations = {};

function createWindow() {
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

    fetch('https://environmentaldashboard.org/digital-signage/display/2/present/json')
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
                            let view = new BrowserView({ webPreferences: {
                                nodeIntegration: false
                                // sandbox: true
                            }});
                            win.addBrowserView(view);
                            let viewWidth = width * (json[presentationId]['style'][carouselId].width / 100);
                            let viewHeight = height * (json[presentationId]['style'][carouselId].height / 100);
                            view.setBounds({x: x, y: y, width: viewWidth, height: viewHeight });
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

            initApp();

        });
}

// This method will be called when Electron has finished
// initialization and is ready to create browser windows.
// Some APIs can only be used after this event occurs.
app.on('ready', createWindow);

// Quit when all windows are closed.
app.on('window-all-closed', () => {
    // On macOS it is common for applications and their menu bar
    // to stay active until the user quits explicitly with Cmd + Q
    if (process.platform !== 'darwin') {
        app.quit();
    }
});

app.on('activate', () => {
    // On macOS it's common to re-create a window in the app when the
    // dock icon is clicked and there are no other windows open.
    if (win === null) {
        createWindow();
    }
});


let activeTimeouts = [];
let activeViews = [];
function initApp() {
    showPresentation(Object.keys(presentations)[0]); // begin by showing first presentation
}

function showPresentation(presentationId, targetFrame = null) {
    clearTimeouts();
    clearViews();
    setViews(presentations[presentationId], targetFrame);
    let timeout = setTimeout(() => {
        showPresentation(presentations[presentationId].next);
        console.log(process.getCPUUsage());
    }, presentations[presentationId].duration);
    let date = new Date();
    console.log(date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds(), timeout);
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
                animateFrames(view, carousel, (current === (carousel.length-1)) ? 0 : current + 1);
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

// open websocket conn to receive commands from remote controllers
const conn = new WebSocket("wss://environmentaldashboard.org/digital-signage/websockets/display/2");
let WS_READY = true;
conn.onerror = function () {
    console.log('Connection error');
    app.quit();
};
conn.onclose = function () {
    console.log('Connection close');
    app.quit();
};
conn.onmessage = function (e) {
    if (WS_READY === false) {
        return;
    } else {
        WS_READY = false;
    }
    const frameId = parseInt(e.data);
    const target = 'frame' + frameId;
    for (const key in presentations) {
        const presentation = presentations[key];
        const carousels = presentation.carousels;
        for (const carouselId in carousels) {
            const carousel = carousels[carouselId];
            for (const twigKey in carousel) {
                if (carousel.hasOwnProperty(twigKey)) {
                    const frame = carousel[twigKey];
                    if (frame.id === target) {
                        WS_READY = true;
                        showPresentation(key, frame.id);
                        return;
                    }       
                }
            }
        }
    }
    WS_READY = true;

};
