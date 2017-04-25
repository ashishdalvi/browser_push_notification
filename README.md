Browser Push Notification
===================

This module will send push notification to browsers using Push Api and Service-worker.
It will work only in Html5 supported latest browsers.

INSTRUCTIONS:
--------------

1. Site should be have the SSL certificate because service-worker will works only in
   secure domains.
2. Enable the module browser_push_notification.
3. Write the gcm_sender_id in the manifest.json.
4. Copy below two files to the root of the drupal because these are the
mandatory files
	a.) js_config/service-worker.js
	b.) js_config/manifest.json
