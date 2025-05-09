const CACHE_NAME = 'lorphp-v1';
const OFFLINE_URL = '/offline';

const CORE_ASSETS = [
    OFFLINE_URL,
    '/',
    '/css/transitions.css',
    '/js/transitions.js',
    '/images/favicon.svg',
    '/images/logo.svg',
    '/manifest.json'
];

const CACHE_STRATEGIES = {
    NETWORK_FIRST: 'network-first',
    CACHE_FIRST: 'cache-first',
    CACHE_ONLY: 'cache-only'
};

// Define URL patterns and their caching strategies
const URL_STRATEGIES = [
    {
        pattern: /\.(js|css|png|jpg|jpeg|gif|svg|ico)$/,
        strategy: CACHE_STRATEGIES.CACHE_FIRST
    },
    {
        pattern: /^\/dashboard/,
        strategy: CACHE_STRATEGIES.NETWORK_FIRST
    },
    {
        pattern: /^\/settings/,
        strategy: CACHE_STRATEGIES.NETWORK_FIRST
    },
    // Default to network-first for all other routes
    {
        pattern: /.*/,
        strategy: CACHE_STRATEGIES.NETWORK_FIRST
    }
];

// Install service worker and cache core assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(CORE_ASSETS))
            .then(() => self.skipWaiting())
            .then(() => {
                // After core assets are cached, attempt to cache all main routes
                return caches.open(CACHE_NAME).then(cache => {
                    const routes = ['/', '/dashboard', '/settings', '/clients'];
                    return Promise.all(
                        routes.map(route => 
                            fetch(route)
                                .then(response => {
                                    if (response.ok) {
                                        return cache.put(route, response);
                                    }
                                })
                                .catch(() => {
                                    // Ignore failed prefetch attempts
                                })
                        )
                    );
                });
            })
    );
});

// Clean up old caches when a new service worker takes over
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((cacheName) => cacheName !== CACHE_NAME)
                    .map((cacheName) => caches.delete(cacheName))
            );
        })
    );
});

async function cacheFirst(request) {
    const cache = await caches.open(CACHE_NAME);
    const cached = await cache.match(request);
    if (cached) {
        return cached;
    }
    try {
        const response = await fetch(request);
        if (response.ok) {
            await cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        return null;
    }
}

async function networkFirst(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(CACHE_NAME);
            await cache.put(request, response.clone());
            return response;
        }
    } catch (error) {
        // Network failed, try cache
        const cached = await caches.match(request);
        if (cached) {
            return cached;
        }
    }
    // If both network and cache fail, return offline page for navigation
    if (request.mode === 'navigate') {
        return caches.match(OFFLINE_URL);
    }
    return null;
}

// Fetch handler
self.addEventListener('fetch', (event) => {
    // Skip cross-origin requests
    if (!event.request.url.startsWith(self.location.origin)) {
        return;
    }

    const url = new URL(event.request.url);
    const strategy = URL_STRATEGIES.find(s => s.pattern.test(url.pathname));

    event.respondWith((async () => {
        let response = null;

        switch (strategy.strategy) {
            case CACHE_STRATEGIES.CACHE_FIRST:
                response = await cacheFirst(event.request);
                break;
            case CACHE_STRATEGIES.NETWORK_FIRST:
                response = await networkFirst(event.request);
                break;
            default:
                response = await networkFirst(event.request);
        }

        if (!response) {
            if (event.request.headers.get('accept').includes('text/html')) {
                return caches.match(OFFLINE_URL);
            }
            if (event.request.headers.get('accept').includes('image/')) {
                return new Response(new Uint8Array(232), {
                    status: 200,
                    statusText: 'OK',
                    headers: {
                        'Content-Type': 'image/png',
                        'Content-Length': 232
                    }
                });
            }
            return new Response('', { status: 408, statusText: 'Request timed out.' });
        }

        return response;
    })());
});
