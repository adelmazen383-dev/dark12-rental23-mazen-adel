// =========================================================================
// Service Worker for Instant Background Push Notifications (iOS PWA, Android, Desktop)
// منصة دارك لتنظيم الإيجار
// =========================================================================

const SW_VERSION = 'dark-sw-v7';
const BASE_URL = self.location.pathname.startsWith('/app/')
    ? (self.location.origin + '/app')
    : self.location.origin;

self.addEventListener('install', function (event) {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(self.clients.claim());
});

// Push Event: Triggered by APNs (iPhone/Safari), FCM (Android/Chrome), WNS (Edge), Autopush (Firefox)
// even when the website and browser are completely closed.
self.addEventListener('push', function (event) {
    let data = {
        title: 'منصة دارك - إشعار جديد 🔔',
        body: 'لديك تحديث جديد في منصة دارك',
        url: BASE_URL + '/',
        icon: BASE_URL + '/images/icon-192.png',
        badge: BASE_URL + '/images/favicon-96.png',
        tag: 'dark-alert-' + Date.now()
    };

    if (event.data) {
        try {
            const parsed = event.data.json();
            data = { ...data, ...parsed };
        } catch (e) {
            try {
                const txt = event.data.text();
                if (txt) data.body = txt;
            } catch (_) {}
        }
    }

    const title = data.title || 'منصة دارك';
    const notifUrl = normalizeTargetUrl(data.url);
    const notifIcon = data.icon || (BASE_URL + '/images/icon-192.png');
    const notifBadge = data.badge || (BASE_URL + '/images/favicon-96.png');

    const handlePush = async function () {
        // Notify any open windows immediately so they can update live UI & play chime
        try {
            const openClients = await self.clients.matchAll({ type: 'window', includeUncontrolled: true });
            openClients.forEach(function (client) {
                try {
                    client.postMessage({
                        type: 'DARK_PUSH_RECEIVED',
                        payload: {
                            title: title,
                            body: data.body,
                            url: notifUrl,
                            tag: data.tag
                        }
                    });
                } catch (_) {}
            });
        } catch (_) {}

        // Update app badge if supported (iOS 16.4+ PWA & Android)
        try {
            if (self.navigator && typeof self.navigator.setAppBadge === 'function') {
                await self.navigator.setAppBadge(1);
            }
        } catch (_) {}

        // Attempt 1: Standard rich notification (Android / Windows / macOS)
        try {
            await self.registration.showNotification(title, {
                body: data.body,
                icon: notifIcon,
                badge: notifBadge,
                vibrate: [250, 100, 250, 100, 350],
                dir: 'rtl',
                lang: 'ar',
                silent: false,
                data: { url: notifUrl },
                tag: data.tag || ('dark-alert-' + Date.now()),
                renotify: true
            });
            return;
        } catch (richErr) {
            console.warn('Rich notification fallback triggered:', richErr);
        }

        // Attempt 2: Ultra-compatible minimal notification (guaranteed on all iOS Safari PWA builds)
        // Apple APNs strictly requires showNotification() to resolve inside waitUntil().
        try {
            await self.registration.showNotification(title, {
                body: data.body,
                icon: notifIcon,
                data: { url: notifUrl }
            });
        } catch (fatalErr) {
            console.error('Fatal showNotification error:', fatalErr);
        }
    };

    event.waitUntil(handlePush());
});

// Notification Click Event: Focus existing PWA/tab or open new window to targetUrl
self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    // Clear app badge on notification open
    try {
        if (self.navigator && typeof self.navigator.clearAppBadge === 'function') {
            self.navigator.clearAppBadge().catch(function () {});
        }
    } catch (_) {}

    const targetUrl = normalizeTargetUrl(event.notification.data && event.notification.data.url);

    event.waitUntil((async function () {
        try {
            const clientList = await clients.matchAll({ type: 'window', includeUncontrolled: true });
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url && client.url.startsWith(self.location.origin) && 'focus' in client) {
                    try {
                        const focused = await client.focus();
                        if (focused && typeof focused.navigate === 'function') {
                            try {
                                await focused.navigate(targetUrl);
                                return;
                            } catch (_) {}
                        }
                        try {
                            (focused || client).postMessage({ type: 'DARK_NAVIGATE', url: targetUrl });
                            return;
                        } catch (_) {}
                    } catch (_) {}
                }
            }

            if (clients.openWindow) {
                return await clients.openWindow(targetUrl);
            }
        } catch (e) {
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        }
    })());
});

// Handle browser push subscription rotation seamlessly in background (works for both Investor & Client)
self.addEventListener('pushsubscriptionchange', function (event) {
    event.waitUntil((async function () {
        try {
            let appServerKey = event.oldSubscription && event.oldSubscription.options
                ? event.oldSubscription.options.applicationServerKey
                : null;

            if (!appServerKey) {
                const keyRes = await fetch(BASE_URL + '/client/api/vapid-key', {
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
            const oldEndpoint = event.oldSubscription ? event.oldSubscription.endpoint : null;

            const rotateRes = await fetch(BASE_URL + '/api/push-rotate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    old_endpoint: oldEndpoint,
                    endpoint: subJson.endpoint,
                    keys: subJson.keys
                })
            });

            if (!rotateRes.ok) {
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
            }
        } catch (e) {
            // Re-synced next time page is opened
        }
    })());
});

// Periodic sync fallback if supported (Android Chrome)
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
                    badge: BASE_URL + '/images/favicon-96.png',
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
                    badge: BASE_URL + '/images/favicon-96.png',
                    data: { url: b.url },
                    tag: 'sw-poll-book-' + b.id,
                    vibrate: [300, 100, 300, 100, 400]
                }
            );
        }
    } catch (e) {}
}

function normalizeTargetUrl(rawUrl) {
    if (!rawUrl) return BASE_URL + '/';
    if (/^https?:\/\//i.test(rawUrl)) return rawUrl;
    if (rawUrl.startsWith('/')) return self.location.origin + rawUrl;
    return BASE_URL + '/' + rawUrl;
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
