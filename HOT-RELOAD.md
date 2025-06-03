# Hot Reload for Development

This setup enables hot reload for your dev environment. When you change files in `public/`, `src/`, `routes.php`, or `config/`, all connected browsers will auto-refresh.

## Usage

1. **Install dependencies:**

   ```pwsh
   npm install ws
   ```

2. **Start the hot reload server (in a separate terminal):**

   ```pwsh
   node dev-hot-reload-server.js
   ```

3. **Ensure your environment is set to development:**

   - Set `APP_ENV=dev` or `APP_DEBUG=1` in your environment variables or `.env` file.

4. **Open your app in the browser.**
   - The client script (`public/js/hot-reload-client.js`) will connect automatically and reload the page on changes.

## How it works

- `dev-hot-reload-server.js` watches for file changes and notifies clients via WebSocket.
- `public/js/hot-reload-client.js` listens for reload messages and refreshes the page.
- The client script is only loaded if `APP_ENV=dev` or `APP_DEBUG` is set.

---

**Note:**

- This is for development only. Do not use in production.
- You can add/remove watched directories in `dev-hot-reload-server.js` as needed.
