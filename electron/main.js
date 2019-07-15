const electron = require('electron');
const fetch = require('node-fetch');
const { app, BrowserView, BrowserWindow } = electron;

// Keep a global reference of the window object, if you don't, the window will
// be closed automatically when the JavaScript object is garbage collected.
let win;

function createWindow() {
    const { width, height } = electron.screen.getPrimaryDisplay().workAreaSize;
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
        .then(presentations => {
            win.on('resize', function () {
                let size = win.getSize();
                let width = size[0];
                let height = size[1];
                initApp(presentations, width, height);
            });

            initApp(presentations, width, height);

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
function initApp(presentations, width, height) {
    showPresentation(presentations, Object.keys(presentations)[0], width, height);


    // let view = new BrowserView();
    // win.setBrowserView(view);
    // view.setBounds({ x: 0, y: 0, width: width, height: height });
    // view.webContents.loadURL('https://environmentaldashboard.org/digital-signage/display/2/present');
}

function showPresentation(presentations, presentationId, width, height, targetFrame = null) {
    let viewData = {
        frames: [],
        style: null
    };
    for (const key in presentations[presentationId]) {
        if (key === 'carousels') {
            const frameCollection = presentations[presentationId][key];
            for (const collectionId in frameCollection) {
                const frames = frameCollection[collectionId];
                for (let i = 0; i < frames.length; i++) {
                    const frame = frames[i];
                    viewData.frames.push({ [collectionId]: frame });
                }

            }
        }
        if (key === 'style') {
            viewData.style = presentations[presentationId][key];
        }
    }
    clearTimeouts();
    clearPresentations();
    drawViews(viewData, height, width);
    var timeout = setTimeout(function () {
        showPresentation(presentations, presentations[presentationId].next, width, height);
        console.log(process.getCPUUsage());
    }, presentations[presentationId].duration);
    console.log(timeout);
    activeTimeouts.push(timeout);
}

function drawViews(viewData, height, width) {
    for (let i = 0; i < viewData.frames.length; i++) {
        const frameCollection = viewData.frames[i];
        const coords = { x: 0, y: 0 };
        for (const key in frameCollection) {
            const frame = frameCollection[key];
            const style = viewData.style[key];
            let viewWidth = width * (style.width / 100);
            let viewHeight = height * (style.height / 100);
            let view = new BrowserView();
            win.setBrowserView(view);
            view.setBounds({ x: coords.x, y: coords.y, width: viewWidth, height: viewHeight });
            view.webContents.loadURL(frame.url);
            activeViews.push(view);
            coords.x = coords.x + viewWidth;
            coords.y = coords.y + viewHeight;
        }
    }
}

function clearTimeouts() {
    while (activeTimeouts.length > 0) {
        let timeout = activeTimeouts.pop();
        clearTimeout(timeout);
    }
}

function clearPresentations() {
    while (activeViews.length > 0) {
        let view = activeViews.pop();
        view.destroy();
        view = null;
    }
}