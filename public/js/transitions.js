// Create offline notification element
function createOfflineNotification() {
    const notification = document.createElement('div');
    notification.id = 'offline-notification';
    notification.style.cssText = `
        position: fixed;
        bottom: 16px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #1F2937;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        z-index: 9999;
        display: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    `;
    notification.textContent = 'You are currently offline';
    document.body.appendChild(notification);
    return notification;
}

document.addEventListener('DOMContentLoaded', () => {
    const offlineNotification = createOfflineNotification();
    
    // Update online/offline status
    function updateOnlineStatus() {
        if (navigator.onLine) {
            offlineNotification.style.display = 'none';
        } else {
            offlineNotification.style.display = 'block';
        }
    }

    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
    updateOnlineStatus(); // Initial check

    // Only run if the View Transitions API is supported and we're not in an iframe
    if (!document.startViewTransition || window.self !== window.top) return;

    // Wait for the initial page load to complete
    window.addEventListener('load', () => {
        // Handle all link clicks
        document.addEventListener('click', async e => {
            const link = e.target.closest('a');
            if (!link || !link.href || link.target === '_blank') return;

            // Only handle links to the same origin
            if (new URL(link.href).origin !== location.origin) return;

            e.preventDefault();

            try {
                if (!navigator.onLine) {
                    // Try to get from cache first when offline
                    const cache = await caches.open('lorphp-v1');
                    const cachedResponse = await cache.match(link.href);
                    
                    if (cachedResponse) {
                        await document.startViewTransition(async () => {
                            const text = await cachedResponse.text();
                            document.documentElement.innerHTML = text;
                        });
                        return;
                    } else {
                        // If not in cache and offline, redirect to offline page
                        window.location.href = '/offline';
                        return;
                    }
                }

                // Online navigation with View Transitions API
                await document.startViewTransition(async () => {
                    const response = await fetch(link.href);
                    if (!response.ok) throw new Error('Navigation failed');
                    
                    const text = await response.text();
                    document.documentElement.innerHTML = text;
                });
            } catch (error) {
                console.error('Navigation error:', error);
                if (!navigator.onLine) {
                    window.location.href = '/offline';
                } else {
                    window.location.href = link.href;
                }
            }
        });
    });
});
