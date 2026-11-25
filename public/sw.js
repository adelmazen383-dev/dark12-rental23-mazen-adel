// =========================================================================
// Service Worker for Background Push Notifications (Works even when closed)
// منصة دَارِك لتنظيم الإيجار
// =========================================================================

const SW_VERSION = 'dark-sw-v6';
const BASE_URL = self.location.pathname.startsWith('/app/')
    ? (self.location.origin + '/app')
    : self.location.origin;

self.addEventListener('install', function (event) {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(self.clients.claim());
});

// Push Event: Received from Web Push server (FCM / APNs / WNS / Autopush) even when browser/app is closed
self.addEventListener('push', function (event) {
    let data = {
        title: 'منصة دَارِك - إشعار جديد 🔔',
        body: 'وصلك طلب جديد بخصوص شقتك',
        url: BASE_URL + '/investor/bookings',
        icon: BASE_URL + '/images/icon-192.png',
        badge: BASE_URL + '/images/icon-192.png',
        tag: 'investor-alert'
    };

    if (event.data) {
        try {
            const parsed = event.data.json();
            data = { ...data, ...parsed };
        } catch (e) {
            try {
                data.body = event.data.text() || data.body;
            } catch (_) {}
        }
    }

    const title = data.title || 'منصة دَارِك';
    const notifUrl = data.url || (BASE_URL + '/investor/bookings');
    const notifIcon = data.icon || (BASE_URL + '/images/icon-192.png');
    const notifBadge = data.badge || (BASE_URL + '/images/icon-192.png');

    const showNotificationWithFallback = async function () {
        // Attempt 1: Rich notification with vibration and actions
        try {
            await self.registration.showNotification(title, {
                body: data.body,
                icon: notifIcon,
                badge: notifBadge,
                vibrate: [300, 100, 300, 100, 400],
                dir: 'rtl',
                lang: 'ar',
                data: { url: notifUrl },
                tag: data.tag || ('dark-alert-' + Date.now()),
                renotify: true,
                requireInteraction: true,
                actions: [
                    { action: 'open', title: 'عرض التفاصيل' }
                ]
            });
            return;
        } catch (richErr) {
            console.warn('Rich notification failed, trying simple notification:', richErr);
        }

        // Attempt 2: Minimal safe notification (guaranteed on all mobile browsers)
        try {
            await self.registration.showNotification(title, {
                body: data.body,
                icon: notifIcon,
                data: { url: notifUrl },
                tag: 'dark-alert-' + Date.now()
            });
        } catch (fatalErr) {
            console.error('Fatal showNotification error:', fatalErr);
        }
    };

    event.waitUntil(showNotificationWithFallback());
});

// Notification Click Event: Focus open window or open new tab to targetUrl
self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const targetUrl = event.notification.data?.url || (BASE_URL + '/investor/bookings');

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url && client.url.startsWith(self.location.origin) && 'focus' in client) {
                    return client.focus().then(function (focused) {
                        if (focused && 'navigate' in focused) {
                            return focused.navigate(targetUrl).catch(function () {});
                        }
                    }).catch(function () {
                        if (clients.openWindow) return clients.openWindow(targetUrl);
                    });
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});

// Handle browser push subscription rotation seamlessly in background
self.addEventListener('pushsubscriptionchange', function (event) {
    event.waitUntil((async function () {
        try {
            let appServerKey = event.oldSubscription && event.oldSubscription.options
                ? event.oldSubscription.options.applicationServerKey
                : null;

            if (!appServerKey) {
                const keyRes = await fetch(BASE_URL + '/investor/api/vapid-key', {
                    credentials: 'include',
                    headers: { 'Accept': 'application/json' }
                });
                if (keyRes.ok) {
                    const keyData = await keyRes.json();
                    appServerKey = urlBase64ToUint8Array(keyData.publicKey);
                }
            }

            if (!appServerKey) return;

            const newSub = await self.registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: appServerKey
            });

            const subJson = newSub.toJSON();
            await fetch(BASE_URL + '/investor/api/push-subscribe', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    endpoint: subJson.endpoint,
                    keys: subJson.keys
                })
            }).catch(function () {});
        } catch (e) {
            // Re-synced next time page is opened
        }
    })());
});

// Periodic sync fallback if registered
self.addEventListener('periodicsync', function (event) {
    if (event.tag === 'dark-poll-messages') {
        event.waitUntil(pollForNewAlerts());
    }
});

async function pollForNewAlerts() {
    try {
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
                    icon: BASE_URL + '/images/icon-192.png',
                    badge: BASE_URL + '/images/icon-192.png',
                    data: { url: msg.url },
                    tag: 'sw-poll-msg-' + msg.id,
                    vibrate: [300, 100, 300]
                }
            );
        }

        if (data.unread_bookings_count > 0 && data.latest_booking) {
            const b = data.latest_booking;
            await self.registration.showNotification(
                `🎉 طلب حجز #${b.reference} - شقة ${b.apartment_number}`,
                {
                    body: `${b.customer_name} | ${b.total_amount} ريال | ${b.status}`,
                    icon: BASE_URL + '/images/icon-192.png',
                    badge: BASE_URL + '/images/icon-192.png',
                    data: { url: b.url },
                    tag: 'sw-poll-book-' + b.id,
                    vibrate: [300, 100, 300, 100, 400]
                }
            );
        }
    } catch (e) {}
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}
