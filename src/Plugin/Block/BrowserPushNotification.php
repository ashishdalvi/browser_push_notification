<?php

namespace Drupal\browser_push_notification\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'BrowserPushNotification' block.
 *
 * @Block(
 *  id = "browser_push_notification",
 *  admin_label = @Translation("Browser push notification"),
 * )
 */
class BrowserPushNotification extends BlockBase {

  /**
   * {@inheritdoc}
   */
  
  public function build() {
    $build = [];
    $build['#theme'] = array('#theme' => 'theme_browser_push_notification', '#base_path' => base_path());
   return $build;
  }

}
