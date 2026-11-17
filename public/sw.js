// =========================================================================
// Service Worker for Background Push Notifications (Works even when closed)
// منصة دَارِك لتنظيم الإيجار
// =========================================================================

const SW_VERSION = 'dark-sw-v3';
const BASE_URL = 'https://tower-rental.vercel.app';

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
        url: BASE_URL + '/investor/chats',
        tag: 'investor-alert'
    };

    if (event.data) {
        try {
            const parsed = event.data.json();
            data = { ...data, ...parsed };
        } catch (e) {
            data.body = event.data.text() || data.body;
        }
    }

    const options = {
        body: data.body,
        icon: BASE_URL + '/images/dark-logo.jpg',
        badge: BASE_URL + '/client-entry-qr.png',
        vibrate: [300, 100, 300, 100, 400],
        data: {
            url: data.url || (BASE_URL + '/investor/chats')
        },
        tag: data.tag || ('alert-' + Date.now()),
        renotify: true,
        requireInteraction: true,
        actions: [
            { action: 'open', title: '💬 عرض المحادثة والرد' },
            { action: 'dismiss', title: 'إغلاق' }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Notification Click Event: Focus or open window
self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    if (event.action === 'dismiss') return;

    const targetUrl = event.notification.data?.url || (BASE_URL + '/investor/chats');

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            // Try to find already-open tab
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if ('focus' in client) {
                    client.navigate(targetUrl);
                    return client.focus();
                }
            }
            // Open new tab
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});

// Background Sync: Poll for new messages when site is closed (periodic-sync fallback)
self.addEventListener('periodicsync', function (event) {
    if (event.tag === 'dark-poll-messages') {
        event.waitUntil(pollForNewMessages());
    }
});

async function pollForNewMessages() {
    try {
        // We store the investor auth cookie — if logged in, fetch unread count
        const res = await fetch(BASE_URL + '/investor/api/unread-alerts', {
            credentials: 'include',
            headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) return;
        const data = await res.json();

        if (data.unread_messages_count > 0 && data.latest_message) {
            const msg = data.latest_message;
            await self.registration.showNotification(
                `📩 رسالة جديدة من (${msg.sender_name}) - شقة ${msg.apartment_number}`,
                {
                    body: msg.snippet,
                    icon: BASE_URL + '/images/dark-logo.jpg',
                    badge: BASE_URL + '/client-entry-qr.png',
                    data: { url: msg.url },
                    tag: 'sw-poll-msg-' + msg.id,
                    vibrate: [300, 100, 300],
                    requireInteraction: true,
                }
            );
        }
    } catch (e) {
        // Ignore fetch errors (offline / no session)
    }
}
