<?php

namespace Drupal\browser_push_notification\Model;

/**
 * Class NotificationsDatastorage.
 *
 * @package Drupal\browser_push_notification\Model
 */
class NotificationsDatastorage {

  /**
   * Save an entry in the database.

   * @param array $entry
   *   An array containing all the fields of the database record.
   *
   * @return int
   *   The number of updated rows.
   *
   * @throws \Exception
   *   When the database insert fails.
   *
   */

  public static function insert($entry) {
    $return_value = NULL;
    self::delete();
    try {
      $return_value = db_insert('browser_notifications')
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

  /**
   * Delete an entry from the database.
   *
   * @see db_delete()
   */
  public static function delete() {
    db_delete('browser_notifications')
        ->execute();
  }

  /*
   * Load all notification details to push notification
   */
  
  public static function loadAll() {
    // Read all fields from the browser_notifications table.
    $select = db_select('browser_notifications', 'notification_data');
    $select->fields('notification_data');
    return $select->execute()->fetchAll();
  }

}
