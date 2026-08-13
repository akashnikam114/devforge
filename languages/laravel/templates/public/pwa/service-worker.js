self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open('__PROJECT_SLUG__-cache').then((cache) => {
            return cache.addAll([
                '/',
                'app-icon.png'
            ]);
        })
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});
