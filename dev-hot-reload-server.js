// dev-hot-reload-server.js
// Watches for file changes and notifies clients via WebSocket

const fs = require('fs');
const path = require('path');
const WebSocket = require('ws');

const WATCH_DIRS = [
  path.join(__dirname, 'public'),
  path.join(__dirname, 'src'),
  path.join(__dirname, 'routes.php'),
  path.join(__dirname, 'config'),
];

const PORT = 35729; // Standard LiveReload port
const wss = new WebSocket.Server({ port: PORT });

console.log(`[HotReload] WebSocket server running on ws://localhost:${PORT}`);

wss.on('connection', ws => {
  console.log('[HotReload] Client connected');
});

function broadcastReload() {
  wss.clients.forEach(client => {
    if (client.readyState === WebSocket.OPEN) {
      client.send('reload');
    }
  });
  console.log('[HotReload] Reload message sent to clients');
}

function watchRecursive(dir) {
  if (!fs.existsSync(dir)) return;
  const stat = fs.statSync(dir);
  if (stat.isFile()) {
    fs.watchFile(dir, { interval: 500 }, () => {
      broadcastReload();
    });
    return;
  }
  fs.readdirSync(dir).forEach(file => {
    const fullPath = path.join(dir, file);
    if (fs.statSync(fullPath).isDirectory()) {
      watchRecursive(fullPath);
    } else {
      fs.watchFile(fullPath, { interval: 500 }, () => {
        broadcastReload();
      });
    }
  });
}

WATCH_DIRS.forEach(watchRecursive);

console.log('[HotReload] Watching for changes...');
