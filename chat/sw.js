self.addEventListener('push', function(event) {
    const data = event.data.json();
    const title = data.title || 'Новое сообщение';
    const options = {
        body: data.body || 'Вы получили новое сообщение.',
        icon: 'icon.png' // Укажите путь к вашему значку
        //badge: 'badge.png' // Укажите путь к вашему значку для бейджа
    };
    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});