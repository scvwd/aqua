<?php

namespace Drupal\coa_migrate;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Overrides migration configuration.
 */
class CoaMigrateOverrides implements ConfigFactoryOverrideInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs the object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function loadOverrides($names) {
    if (in_array('migrate_plus.migration.import_node_events_xml', $names)) {
      $overrides = [];
      $config = $this->configFactory->getEditable('migrate_plus.migration.import_node_events_xml');
      $path = $config->get('source.path');
      if ($path && file_exists($path)) {
        $cid = 'migrate_plus_migration_import_node_events_xml';
        if ($cache = \Drupal::cache()->get($cid)) {
          $urls = $cache->data;
        }
        else {
          $directory = new \RecursiveDirectoryIterator($path);
          $iterator = new \RecursiveIteratorIterator($directory);
          $files = new \RegexIterator($iterator, '/^.+.*\.xml$/', \RecursiveRegexIterator::GET_MATCH);
          $urls = [];
          foreach ($files as $key => $value) {
            $urls[] = $key;
          }
          \Drupal::cache()->set($cid, $urls, strtotime('+6 hours'));
        }
        if ($urls) {
          $overrides['migrate_plus.migration.import_node_events_xml']['source']['urls'] = $urls;
          return $overrides;
        }
      }
    }
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheSuffix() {
    return 'coa_migrate_overrides_3';
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata($name) {
    return new CacheableMetadata();
  }

  /**
   * {@inheritdoc}
   */
  public function createConfigObject($name, $collection = StorageInterface::DEFAULT_COLLECTION) {
    return NULL;
  }

}
