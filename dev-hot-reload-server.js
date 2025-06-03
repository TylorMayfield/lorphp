// dev-hot-reload-server.js
// Watches for file changes and notifies clients via WebSocket

const fs = require("fs");
const path = require("path");
const WebSocket = require("ws");

const WATCH_PATHS = [
  path.join(__dirname, "public"),
  path.join(__dirname, "src"),
  path.join(__dirname, "routes.php"),
  path.join(__dirname, "config"),
];

const PORT = 35729; // Standard LiveReload port
const wss = new WebSocket.Server({ port: PORT });

console.log(`[HotReload] WebSocket server running on ws://localhost:${PORT}`);

// Track WebSocket clients
wss.on("connection", (ws) => {
  console.log("[HotReload] Client connected");
});

// WebSocket keepalive
setInterval(() => {
  wss.clients.forEach((ws) => {
    if (ws.readyState === WebSocket.OPEN) {
      ws.ping();
    }
  });
}, 30000);

// Debounced reload
let reloadTimeout;
function scheduleReload() {
  clearTimeout(reloadTimeout);
  reloadTimeout = setTimeout(() => {
    wss.clients.forEach((client) => {
      if (client.readyState === WebSocket.OPEN) {
        client.send("reload");
      }
    });
    console.log("[HotReload] Reload message sent to clients");
  }, 300);
}

// Watch logic
function watchPath(filePath) {
  try {
    const stat = fs.statSync(filePath);

    if (stat.isDirectory()) {
      fs.readdirSync(filePath).forEach((entry) => {
        const fullEntry = path.join(filePath, entry);
        watchPath(fullEntry);
      });

      fs.watch(filePath, { recursive: false }, (eventType, filename) => {
        if (filename) {
          scheduleReload();
        }
      });
    } else if (stat.isFile()) {
      fs.watch(filePath, {}, () => {
        scheduleReload();
      });
    }
  } catch (err) {
    console.error(`[HotReload] Failed to watch ${filePath}:`, err.message);
  }
}

// Start watching
WATCH_PATHS.forEach(watchPath);

console.log("[HotReload] Watching for changes...");
