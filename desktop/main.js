const { app, BrowserWindow, Menu, shell, dialog, ipcMain, nativeTheme } = require('electron');
const path = require('path');
const { autoUpdater } = require('electron-updater');

let mainWindow;
let splashWindow;

const PANEL_URL = 'http://localhost';
const DOCS_URL = 'https://hexactyl-docs.vercel.app';

function createSplashWindow() {
    splashWindow = new BrowserWindow({
        width: 500,
        height: 350,
        frame: false,
        alwaysOnTop: true,
        resizable: false,
        transparent: true,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true
        }
    });

    splashWindow.loadFile(path.join(__dirname, 'splash.html'));
    splashWindow.center();
}

function createMainWindow() {
    mainWindow = new BrowserWindow({
        width: 1400,
        height: 900,
        minWidth: 800,
        minHeight: 600,
        show: false,
        icon: path.join(__dirname, 'icon.ico'),
        title: 'Hexactyl Panel',
        webPreferences: {
            preload: path.join(__dirname, 'preload.js'),
            nodeIntegration: false,
            contextIsolation: true,
            sandbox: true,
            webviewTag: false,
            enableRemoteModule: false
        }
    });

    mainWindow.loadURL(PANEL_URL);

    mainWindow.once('ready-to-show', () => {
        if (splashWindow) {
            splashWindow.close();
            splashWindow = null;
        }
        mainWindow.show();
    });

    mainWindow.webContents.setWindowOpenHandler(({ url }) => {
        shell.openExternal(url);
        return { action: 'deny' };
    });

    mainWindow.on('closed', () => {
        mainWindow = null;
    });

    mainWindow.webContents.on('did-fail-load', (event, errorCode, errorDescription) => {
        mainWindow.webContents.loadURL(`data:text/html,
            <html>
            <head>
                <style>
                    body {
                        font-family: 'Segoe UI', sans-serif;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                        background: #0f172a;
                        color: #e2e8f0;
                    }
                    .container {
                        text-align: center;
                        max-width: 500px;
                        padding: 40px;
                    }
                    h1 { color: #f97316; font-size: 28px; margin-bottom: 10px; }
                    p { color: #94a3b8; line-height: 1.6; }
                    .status { color: #ef4444; font-weight: 600; margin: 20px 0; }
                    a {
                        color: #f97316;
                        text-decoration: none;
                        padding: 10px 24px;
                        border: 2px solid #f97316;
                        border-radius: 8px;
                        display: inline-block;
                        margin-top: 16px;
                        transition: all 0.2s;
                    }
                    a:hover { background: #f97316; color: #fff; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>Hexactyl</h1>
                    <p>Could not connect to the panel server.</p>
                    <div class="status">Error: ${errorDescription}</div>
                    <p>Make sure your Hexactyl panel is running at:<br><strong>${PANEL_URL}</strong></p>
                    <a href="javascript:void(0)" onclick="location.href='${PANEL_URL}'">Retry Connection</a>
                </div>
            </body>
            </html>
        `);
    });

    const menuTemplate = [
        {
            label: 'File',
            submenu: [
                {
                    label: 'Reload',
                    accelerator: 'CmdOrCtrl+R',
                    click: () => {
                        if (mainWindow) mainWindow.reload();
                    }
                },
                {
                    label: 'Open Developer Tools',
                    accelerator: 'CmdOrCtrl+Shift+I',
                    click: () => {
                        if (mainWindow) mainWindow.webContents.toggleDevTools();
                    }
                },
                { type: 'separator' },
                {
                    label: 'Quit',
                    accelerator: 'CmdOrCtrl+Q',
                    click: () => app.quit()
                }
            ]
        },
        {
            label: 'View',
            submenu: [
                { role: 'zoomIn', label: 'Zoom In' },
                { role: 'zoomOut', label: 'Zoom Out' },
                { role: 'resetZoom', label: 'Reset Zoom' },
                { type: 'separator' },
                { role: 'togglefullscreen', label: 'Toggle Fullscreen' }
            ]
        },
        {
            label: 'Help',
            submenu: [
                {
                    label: 'Hexactyl Documentation',
                    click: () => shell.openExternal(DOCS_URL)
                },
                {
                    label: 'GitHub Repository',
                    click: () => shell.openExternal('https://github.com/Hexactyl-Projects/hexactyl')
                },
                { type: 'separator' },
                {
                    label: 'Check for Updates',
                    click: () => {
                        autoUpdater.checkForUpdatesAndNotify();
                    }
                },
                { type: 'separator' },
                {
                    label: 'About Hexactyl',
                    click: () => {
                        dialog.showMessageBox(mainWindow, {
                            type: 'info',
                            title: 'About Hexactyl Panel',
                            message: 'Hexactyl Panel',
                            detail: `Version: ${app.getVersion()}\nElectron: ${process.versions.electron}\nChrome: ${process.versions.chrome}\nNode.js: ${process.versions.node}\n\nA modern game server management panel.\n\nhttps://hexactyl-docs.vercel.app`,
                            buttons: ['OK']
                        });
                    }
                }
            ]
        }
    ];

    const menu = Menu.buildFromTemplate(menuTemplate);
    Menu.setApplicationMenu(menu);

    autoUpdater.autoDownload = true;
    autoUpdater.autoInstallOnAppQuit = true;
}

app.whenReady().then(() => {
    createSplashWindow();

    setTimeout(() => {
        createMainWindow();
    }, 2000);

    app.on('activate', () => {
        if (BrowserWindow.getAllWindows().length === 0) {
            createMainWindow();
        }
    });
});

app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') {
        app.quit();
    }
});

autoUpdater.on('update-available', (info) => {
    dialog.showMessageBox(mainWindow, {
        type: 'info',
        title: 'Update Available',
        message: 'A new version of Hexactyl is available.',
        detail: `Version ${info.version} is ready to install.`,
        buttons: ['Install', 'Later']
    }).then((result) => {
        if (result.response === 0) {
            autoUpdater.downloadUpdate();
        }
    });
});

autoUpdater.on('update-downloaded', (info) => {
    dialog.showMessageBox(mainWindow, {
        type: 'info',
        title: 'Update Ready',
        message: 'The update has been downloaded.',
        detail: 'The application will restart to apply the update.',
        buttons: ['Restart Now', 'Later']
    }).then((result) => {
        if (result.response === 0) {
            autoUpdater.quitAndInstall();
        }
    });
});

autoUpdater.on('error', (error) => {
    console.error('Auto-update error:', error);
});

ipcMain.handle('get-app-version', () => {
    return app.getVersion();
});
