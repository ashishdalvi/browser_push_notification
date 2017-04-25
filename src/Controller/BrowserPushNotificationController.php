<?php

namespace Drupal\browser_push_notification\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\browser_push_notification\Model\SubscriptionsDatastorage;
use Drupal\browser_push_notification\Model\NotificationsDatastorage;

/**
 * Controller routines for Browser push notification.
 */
class BrowserPushNotificationController extends ControllerBase {

  protected $database;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
        $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * This event will be triggered when browser got a push notification.
   */
  public function getNotificationRoute() {

    $notificationData = NotificationsDatastorage::loadAll();
    if (!empty($notificationData)) {
      foreach ($notificationData as $key => $notification) {
        $data['notification']['title'] = $notification->title;
        $data['notification']['body'] = $notification->body;
        $data['notification']['icon'] = $notification->icon;
        $data['notification']['url'] = $notification->url;
      }
    }
    return new JsonResponse($data);
  }

  /**
   * This event will be triggered when user subscribe for notification.
   */
  
  public function subscribe() {
    $data['Authorization'] = \Drupal::request()->request->get('authorization');
    $data['cryptokey'] = \Drupal::request()->request->get('cryptokey');
    $entry['subscription_endpoint'] = \Drupal::request()->request->get('endpoint');
    $auth_string = $data['Authorization'];
    $entry['subscription_data'] = serialize(array('auth' => $auth_string, 'crypto_key' => $data['cryptokey']));
    $entry['registered_on'] = strtotime(date('Y-m-d H:i:s'));
    $result = SubscriptionsDatastorage::insert($entry);
    return new JsonResponse($notification_send);
  }

   /**
   * List of all subscribed users.
   */
  
  public function subscriptionList() {
    // The table description.
    $header = [
      [
        'data' => $this->t('Id'),
      ],
      ['data' => $this->t('Subscription Endpoint')],
      [
        'data' => $this->t('Registeration Date'),
      ],
    ];
    $getFields = [
      'id',
      'subscription_endpoint',
      'registered_on',
    ];
    $query = $this->database->select(SubscriptionsDatastorage::$browserSubscriptionTable);
    $query->fields(SubscriptionsDatastorage::$browserSubscriptionTable, $getFields);
    // Limit the rows to 50 for each page.
    $pager = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')
        ->limit(SubscriptionsDatastorage::$browserSubscriptionCount);
    $result = $pager->execute();

    // Populate the rows.
    $rows = [];
    foreach ($result as $row) {
      $rows[] = [
        'data' => [
          'id' => $row->id,
          'register_id' => $row->subscription_endpoint,
          'date' => date('d/m/Y', $row->registered_on)
        ],
      ];
    }
    if (empty($rows))
      $markup = 'No record found.';
    else
      $markup = 'List of All Subscribed Users.';
    $build = [
      '#markup' => $this->t($markup),
    ];
    // Generate the table.
    $build['config_table'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];

    $build['pager'] = [
      '#type' => 'pager',
    ];
    return $build;
  }

}
