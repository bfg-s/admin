self.addEventListener('push', event => {
    const notification = event.data.json();
    // {"title": "hi", "body": "somesing amaising", "url": "./?message=123"}
    event.waitUntil(self.registration.showNotification(notification.title, {
        body: notification.body,
        icon: '/admin/img/favicon.png',
        data: {
            notifUrl: notification.url
        },
    }));
});

self.addEventListener('notificationclick', event => {
    if (event.notification.data.notifUrl) {
        event.waitUntil(clients.openWindow(event.notification.data.notifUrl));
    }
});
