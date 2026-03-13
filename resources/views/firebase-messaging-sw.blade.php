try {
    importScripts("https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js");
    importScripts("https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js");

    const firebaseConfig = {
        apiKey: "{!! config('services.fcm.api_key') !!}",
        authDomain: "{!! config('services.fcm.auth_domain') !!}",
        projectId: "{!! config('services.fcm.project_id') !!}",
        storageBucket: "{!! config('services.fcm.storage_bucket') !!}",
        messagingSenderId: "{!! config('services.fcm.messaging_sender_id') !!}",
        appId: "{!! config('services.fcm.app_id') !!}",
        measurementId: "{!! config('services.fcm.measurement_id') !!}"
    };

    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    messaging.onBackgroundMessage(function (payload) {
        console.log('[firebase-messaging-sw.js] Received background message ', payload);

        // Data sekarang dikirim di dalam payload.data
        const notificationTitle = payload.data?.title || 'Notifikasi Baru';
        const notificationOptions = {
            body: payload.data?.body || '',
            icon: '/logo-lsp.png',
            data: {
                url: payload.data?.url || '/'
            }
        };

        return self.registration.showNotification(notificationTitle, notificationOptions);
    });

    self.addEventListener('notificationclick', function (event) {
        console.log('[Service Worker] Notification click Received.');

        // 1. Tutup notifikasi di bar notifikasi
        event.notification.close();

        // 2. Ambil URL dari payload data
        // Pastikan fallback ke root '/' jika data kosong
        let targetUrl = event.notification.data && event.notification.data.url ? event.notification.data.url : '/';
        // Jadikan absolute URL
        targetUrl = '/asesi/sertifikasi/10/342/applied/show';
        targetUrl = new URL(targetUrl, self.location.origin).href;

        console.log('Opening URL:', targetUrl);

        // 3. Logika Buka Tab
        event.waitUntil(
            clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (windowClients) {
                // Cek apakah ada tab aplikasi kita yang sudah terbuka
                for (let i = 0; i < windowClients.length; i++) {
                    let client = windowClients[i];
                    // Cek base URL (origin) saja biar aman
                    if (client.url.indexOf(self.location.origin) !== -1) {
                        // Fokus dulu, baru pindah halaman
                        return client.focus().then(function () {
                            // Kirim pesan "telepati" ke halaman web yang lagi terbuka
                            client.postMessage({
                                action: 'NAVIGATE_FROM_NOTIF',
                                url: targetUrl
                            });
                            // if ('navigate' in client) {
                            //     return client.navigate(targetUrl);
                            // }
                        });
                    }
                }
                // Jika tidak ada tab terbuka, buka window baru
                if (clients.openWindow) {
                    return clients.openWindow(targetUrl);
                }
            })
        );
    });
} catch (e) {
    console.error('Service Worker Error:', e);
}