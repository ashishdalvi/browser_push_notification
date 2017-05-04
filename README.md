Browser Push Notification
===================

This module will send push notification to browsers using Push WebApi 
and service-worker.
It will work only in Html5 supported Chrome and Firefox browsers.

REQUIREMENTS:
--------------
PHP 5.6
gmp
mbstring
curl
openssl
PHP 7.1 is recommended for better performance.

INSTRUCTIONS:
--------------

1. Site should be have the SSL certificate because service-worker
   will works only in secure domains.
2. Install dependent library web push 
   composer require minishlink/web-push. For more details 
   https://github.com/web-push-libs/web-push-php
3. Enable the module browser_push_notification.
4. Copy js/serviceWorker.js into root of the drupal installation
   eg:https://test.com/serviceWorker.js
5. Copy Application Publick Key & Private Key from 
   https://web-push-codelab.appspot.com and set it in 
   browser push notification config settings form.  
6. Browser compatibility
   Chrome:42+
   Firefox:44+
