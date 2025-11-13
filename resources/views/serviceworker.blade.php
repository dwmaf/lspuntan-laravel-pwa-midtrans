@if($isDebug === false)
{{-- importScripts('/firebase-messaging-sw.js'); --}}

<?php
$version = md5_file(base_path('composer.lock'));
$staticCacheName = 'pwa-v' . $version;
$filesToCache = array_merge(
    [$offlineUrl], // Variabel $offlineUrl dari route
    $assetUrls,    // Variabel $assetUrls dari route
    [              // Daftar ikon statis
        '/images/icons/icon-72x72.png',
        '/images/icons/icon-96x96.png',
        '/images/icons/icon-128x128.png',
        '/images/icons/icon-144x144.png',
        '/images/icons/icon-152x152.png',
        '/images/icons/icon-192x192.png',
        '/images/icons/icon-384x384.png',
        '/images/icons/icon-512x512.png',
    ]
);
?>

const staticCacheName = "<?php echo $staticCacheName; ?>";
const filesToCache = <?php echo json_encode($filesToCache); ?>;
const offlineUrl = "<?php echo $offlineUrl; ?>";

self.addEventListener("install", event => {
    this.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName).then(cache => {
            console.log("Attempting to cache files:", filesToCache);
            
            const promises = filesToCache.map(url => {
                const request = new Request(url, { mode: 'no-cors' });
                return fetch(request).then(response => {
                    console.log(`Caching: ${url}`);
                    return cache.put(request, response);
                }).catch(err => {
                    console.error(`Failed to fetch and cache ${url}`, err);
                });
            });
            return Promise.all(promises);
        }).catch(err => {
            console.error("Failed to open cache:", err);
        })
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

self.addEventListener("fetch", event => {
    if (event.request.method !== 'GET' || event.request.url.includes('/fcm/token')) {
        return;
    }
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match(offlineUrl);
            })
    )
});
self.addEventListener('push', function (event) {
    const data = event.data.json();
    const title = data.notification.title;
    const options = {
        body: data.notification.body,
        icon: '/images/icons/icon-192x192.png',
        badge: '/images/icons/icon-96x96.png',
        data: {
            url: data.data.url
        }
    };
    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationClick', function (event) {
    event.notification.close();
    const urlToOpen = event.notification.data.url;
    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then(function (clientList) {
            for (let i = 0; i < clientList.length; i++) {
                let client = clientList[i];
                if (client.url === urlToOpen && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});
@else

console.log("PWA Service Worker is in development mode and is disabled.");
self.addEventListener('install', () => { self.skipWaiting(); });
self.addEventListener('activate', () => {
    self.clients.matchAll({ type: 'window' }).then(clients => {
        clients.forEach(client => { client.navigate(client.url); });
    });
});
@endif