// hot-reload-client.js
// Inject this script into your HTML or bundle it with your frontend JS
(function () {
  var ws = new WebSocket("ws://localhost:35729");
  ws.onmessage = function (event) {
    if (event.data === "reload") {
      console.log("[HotReload] Change detected, reloading...");
      window.location.reload();
    }
  };
  ws.onopen = function () {
    console.log("[HotReload] Connected to dev server");
  };
  ws.onclose = function () {
    console.log("[HotReload] Disconnected from dev server");
  };
})();
