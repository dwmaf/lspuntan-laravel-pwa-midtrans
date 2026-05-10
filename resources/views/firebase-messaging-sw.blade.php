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

        
        event.notification.close();

        
        let targetUrl = event.notification.data && event.notification.data.url ? event.notification.data.url : '/';
        
        targetUrl = new URL(targetUrl, self.location.origin).href;

        console.log('Opening URL:', targetUrl);

        
        event.waitUntil(
            clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (windowClients) {
                
                for (let i = 0; i < windowClients.length; i++) {
                    let client = windowClients[i];
                    
                    if (client.url.indexOf(self.location.origin) !== -1) {
                        
                        return client.focus().then(function () {
                            
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
                
                if (clients.openWindow) {
                    return clients.openWindow(targetUrl);
                }
            })
        );
    });
} catch (e) {
    console.error('Service Worker Error:', e);
}