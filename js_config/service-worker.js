'use strict';
self.addEventListener('push', function(event) {
  console.log('Received push');
  var baseUrl = self.registration.scope ;
  console.log(baseUrl);
  var notification_url = baseUrl + 'get_notification';
  event.waitUntil(
    fetch(notification_url).then(function(response) {
        if (response.status !== 200) {
            console.log('Problem. Status Code: ' + response.status);  
            throw new Error();  
        }
        // Examine the text in the response  
        return response.json().then(function(result) { 
            if (result.error || !result.notification) {
                console.error('The API returned an error.', result.error);  
                throw new Error();  
            }
            var title = result.notification.title;  
            var body = result.notification.body;  
            var icon = result.notification.icon;  
            var notificationTag = result.notification.tag;
            var url = result.notification.url;
            var notificationOptions = {
                body: body,
                icon: icon,
                badge: notificationTag,
                tag: title,
                data: {
                    url: url
                }
            };
            return self.registration.showNotification(title,notificationOptions);
        });
    })
)
});

self.addEventListener('notificationclick', function(event) {
  event.notification.close();

  let clickResponsePromise = Promise.resolve();
  if (event.notification.data && event.notification.data.url) {
    clickResponsePromise = clients.openWindow(event.notification.data.url);
  }
});

self.addEventListener('notificationclose', function(event) {
  event.waitUntil(
    Promise.all([
      //self.analytics.trackEvent('notification-close')
    ])
  );
});
