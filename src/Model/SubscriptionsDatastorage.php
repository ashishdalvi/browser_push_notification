<?php

namespace Drupal\browser_push_notification\Model;

/**
 * Class SubscriptionsDatastorage.
 *
 * @package Drupal\browser_push_notification\Model
 */
class SubscriptionsDatastorage {
  
  public static $browserSubscriptionTable = 'browser_subscriptions';
  public static $browserSubscriptionCount = 5;
  
   /**
   * Save an entry in the database.
   *
   * @param array $entry
   *   An array containing all the fields of the database record.
   *
   * @return int
   *   The number of updated rows.
   *
   * @throws \Exception
   *   When the database insert fails.
   */

  public static function insert($entry) {
    $return_value = NULL;
    $arguments = array(':endpoint' =>"$entry[subscription_endpoint]");
    $subscription_exist =  db_select(self::$browserSubscriptionTable)
          ->fields('browser_subscriptions')
          ->where('subscription_endpoint=:endpoint',$arguments)
          ->execute()
          ->fetchAll();
    if ($subscription_exist) {
      return $subscription_exist;
    }
    try {
      $return_value = db_insert('browser_subscriptions')
          ->fields($entry)
          ->execute();
    }
    catch (\Exception $e) {
      drupal_set_message(t('db_insert failed. Message = %message, query= %query', array(
        '%message' => $e->getMessage(),
        '%query' => $e->query_string,
              )
          ), 'error');
    }
    return $return_value;
  }

  /*
   * Load all client subscription details to send notification
   */
  
  public static function loadAll() {
    // Read all fields from the browser_subscriptions table.
    $select = db_select(self::$browserSubscriptionTable, 'browser_subscriptions');
    $select->fields('browser_subscriptions');
    return $select->execute()->fetchAll();
  }
  
   /*
   * Batch process to start subscription
   *
   * @param array $subscriptionData  
   */
  
  public function sendNotificationStart($subscriptionData) {
    if (!empty($subscriptionData)) {
      foreach ($subscriptionData as $key => $subscription) {
        $subscription_data = unserialize($subscription->subscription_data);
        $authorization = $subscription_data['auth'];
        $crypto_key = $subscription_data['crypto_key'];
        $subscription_endpoint = $subscription->subscription_endpoint;
        if (!empty($authorization) && !empty($crypto_key) && !empty($subscription_endpoint)) {
          $notification_send = self::sendNotification($authorization, $crypto_key, $subscription_endpoint);
        }
      }
    }
  }

   /*
   * Load all client subscription details to send notification
   *
   * @param string $authorization  
   *  authorization key of subscribed browers.
   
   * @param string $crypto_key
   *  authorization Crypto-Key
   * 
   * @param string $endpoint
   * 
   *  Endpoint of subscription
   */
  
  public function sendNotification($authorization, $crypto_key, $endpoint) {
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_POST, 1); // -X
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE); // --data-binary
    $auth = "Authorization: $authorization";
    $cry_key = "Crypto-Key: $crypto_key";
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($auth, "TTL: 60", $cry_key, "Content-Length:0"));
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); // -0
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    $send = curl_exec($ch);
    curl_close($ch);
    return $send;
  }
  
  /*
   * Batch End process
   *
   */
  
  public function NotificationFinished() {
    return true;
  }
}
