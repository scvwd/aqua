<?php

namespace Drupal\coa_migrate;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\awssdk\Awssdk;

/**
 * Provides a MyModuleSubscriber.
 */
class CoaMigrateSubscriber implements EventSubscriberInterface {

  /**
   * Init S3 stream wrapper.
   */
  public function initS3StreamWrapper(GetResponseEvent $event) {
    drupal_set_message('MyModule: subscribed');
    $aws = new Awssdk();
    // \Aws\S3\S3Client.
    $s3 = $aws->createS3(['region' => 'us-east-1', 'version' => 'latest']);
    $s3->registerStreamWrapper();
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // $events[KernelEvents::REQUEST][] = ['initS3StreamWrapper', 20];
    // return $events;
  }

}
