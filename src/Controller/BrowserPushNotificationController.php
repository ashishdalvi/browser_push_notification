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
  
  public function subscribe()
  {
    $data['Authorization']=\Drupal::request()->request->get('authorization');
    $data['cryptokey']=\Drupal::request()->request->get('cryptokey');
    $entry['subscription_endpoint']=\Drupal::request()->request->get('endpoint');
    $auth_string = $data['Authorization'];
    $entry['subscription_data'] = serialize(array('auth'=>$auth_string,'crypto_key'=>$data['cryptokey']));
    $entry['registered_on'] = strtotime(date('Y-m-d H:i:s'));
    $result = SubscriptionsDatastorage::insert($entry);
    return new JsonResponse($notification_send);

  }

  public function subscriptionList()
  {
    $build['pager'] = [
      '#type' => 'pager',
    ];

    return $build;
  }
  /**
   * {@inheritdoc}
   */
  public function userList() {
    /*$header = [
      // We make it sortable by ID or Created Date.
      [
        'data' => $this->t('Id'),
        'field' => 'id',
        'sort' => 'asc',
      ],
      ['data' => $this->t('Register Id')],
      [
        'data' => $this->t('Register Date'),
        'field' => 'registered_on',
        'sort' => 'asc',
      ],
    ];

    $getFields = [
      'id',
      'register_id',
      'registered_on',
    ];
    $query = $this->database->select(ChromeApiCall::$chromeNotificationTable);
    $query->fields(ChromeApiCall::$chromeNotificationTable, $getFields);
    // The actual action of sorting the rows is here.
    $table_sort = $query->extend('Drupal\Core\Database\Query\TableSortExtender')
      ->orderByHeader($header);
    // Limit the rows to 20 for each page.
    $pager = $table_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')
      ->limit(ChromeApiCall::$chromeNotificationViewNumber);
    $result = $pager->execute();

    // Populate the rows.
    $rows = [];
    foreach ($result as $row) {
      $rows[] = [
        'data' => [
          'id' => $row->id,
          'register_id' => $row->register_id,
          'date' => $row->registered_on,
        ],
      ];
    }

    // The table description.
    $build = [
      '#markup' => $this->t('List of All Registered Device'),
    ];

    // Generate the table.
    $build['config_table'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];

    // Finally add the pager.
    $build['pager'] = [
      '#type' => 'pager',
    ];

    return $build;*/
  }
}
