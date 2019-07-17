const electron = require('electron');
const fetch = require('node-fetch');
const WebSocket = require('ws')
const { app, BrowserView, BrowserWindow } = electron;

// Keep a global reference of the window object, if you don't, the window will
// be closed automatically when the JavaScript object is garbage collected.
let win;

let width;
let height;
let presentations = [];

function createWindow() {
    let size = electron.screen.getPrimaryDisplay().workAreaSize;
    width = size.width;
    height = size.height;
    // Create the browser window.
    win = new BrowserWindow({
        width: width,
        height: height,
        webPreferences: {
            nodeIntegration: true
        }
    });

    // and load the index.html of the app.
    win.loadFile('index.html');

    // Open the DevTools.
    // win.webContents.openDevTools();

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
                    carousels: [],
                    style: null,
                    next: null,
                    duration: null
                };
                for (const key in json[presentationId]) {
                    if (key === 'carousels') {
                        const carousels = json[presentationId][key];
                        for (const carouselId in carousels) {
                            const frames = carousels[carouselId];
                            for (let i = 0; i < frames.length; i++) {
                                const frame = frames[i];
                                viewData.carousels.push({ [carouselId]: frame });
                            }

                        }
                    }
                    if (key === 'style') {
                        viewData.style = json[presentationId][key];
                    }
                    if (key === 'next') {
                        viewData.next = json[presentationId][key];
                    }
                    if (key === 'duration') {
                        viewData.duration = json[presentationId][key];
                    }
                }
                presentations[presentationId] = viewData;
            }

            win.on('resize', function () {
                let size = win.getSize();
                width = size[0];
                height = size[1];
                initApp();
            });

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
let activeViews = [], loadingViews = [];
function initApp() {
    showPresentation(Object.keys(presentations)[0]); // begin by showing first presentation
}

function showPresentation(presentationId, targetFrame = null) {
    if (targetFrame === null) {
        if (loadingViews.length === 0) { // first iteration; load next view as well
            makeViews(presentations[presentationId]); // adds views to loadingViews 
        }
        drawViews(); // moves views from loadingViews to activeViews, adding them to win
        makeViews(presentations[presentations[presentationId].next]);
    } else {
        loadingViews = [];
        clearTimeouts();
        makeViews(presentations[presentationId]);
        drawViews();
    }
    let timeout = setTimeout(function () {
        showPresentation(presentations[presentationId].next);
        console.log(process.getCPUUsage());
    }, presentations[presentationId].duration);
    var date = new Date();
    console.log(date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds(), timeout);
    activeTimeouts.push(timeout);
}

function makeViews(viewData) {
    for (let i = 0; i < viewData.carousels.length; i++) {
        const carousels = viewData.carousels[i];
        const coords = { x: 0, y: 0 };
        for (const key in carousels) {
            const frame = carousels[key];
            const style = viewData.style[key];
            let viewWidth = width * (style.width / 100);
            let viewHeight = height * (style.height / 100);
            let view = new BrowserView({ webPreferences: {
                nodeIntegration: false
                // sandbox: true
            }});
            view.webContents.loadURL(frame.url);
            let viewObject = { x: coords.x, y: coords.y, width: viewWidth, height: viewHeight, view: view };
            loadingViews.push(viewObject);
            coords.x = coords.x + viewWidth;
            coords.y = coords.y + viewHeight;
        }
    }
}

function drawViews() {
    clearViews();
    while (loadingViews.length > 0) {
        let viewObject = loadingViews.pop();
        let view = viewObject.view;
        win.addBrowserView(view);
        delete viewObject.view;
        view.setBounds(viewObject);
        activeViews.push(view);
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
        win.removeBrowserView(view);
        view.destroy();
        view = null;
    }
}

// open websocket conn to receive commands from remote controllers
let conn = new WebSocket("wss://environmentaldashboard.org/digital-signage/websockets/display/2");
let WS_READY = true;
conn.onerror = function () {
    app.relaunch();
    app.exit(0);
};
conn.onclose = function () {
    app.relaunch();
    app.exit(0);
};
conn.onmessage = function (e) {
    if (WS_READY === false) {
        return;
    } else {
        WS_READY = false;
    }
    let frameId = parseInt(e.data);
    let target = 'frame' + frameId;
    for (let key in presentations) {
        let presentation = presentations[key];
        let carousels = presentation.carousels;
        for (let carouselId in carousels) {
            let carousel = carousels[carouselId];
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
