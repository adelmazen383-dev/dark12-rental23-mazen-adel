// =========================================================================
// Service Worker for Background Push Notifications (Works even when closed)
// =========================================================================

self.addEventListener('install', function (event) {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(self.clients.claim());
});

// Push Event: Received from Web Push server even if browser/site is closed
self.addEventListener('push', function (event) {
    let data = {
        title: 'منصة دَارِك - إشعار جديد 🔔',
        body: 'وصلك تنبيه أو استفسار جديد بخصوص شقتك',
        url: '/investor/chats',
        tag: 'investor-alert'
    };

    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: '/client-entry-qr.png',
        badge: '/client-entry-qr.png',
        vibrate: [300, 100, 300, 100, 400],
        data: {
            url: data.url || '/investor/chats'
        },
        tag: data.tag || 'alert-' + Date.now(),
        renotify: true,
        requireInteraction: true,
        actions: [
            { action: 'open', title: 'عرض المحادثة 💬' }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Notification Click Event: Focus or open window
self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    const targetUrl = event.notification.data?.url || '/investor/chats';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url && 'focus' in client) {
                    client.navigate(targetUrl);
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
