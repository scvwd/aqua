<?php

// Drupal 8 database settings.
$databases['default']['default'] = array (
  'database' => 'drupal8',
  'username' => 'drupal8',
  'password' => 'drupal8',
  'prefix' => '',
  'host' => 'database',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

// Drupal 7 database settings.
$databases['d7']['default'] = array (
  'database' => 'drupal7',
  'username' => 'drupal7',
  'password' => 'drupal7',
  'prefix' => '',
  'host' => 'd7',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

// Config export sync directory.
$settings['config_sync_directory'] = '../config/sync';

// Trusted host patterns.
$settings['trusted_host_patterns'] = array(
  '^scvwd\.lndo\.site$',
  '^localhost$',
);

// Enable development services.
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/default/lando.services.yml';

// Disable performance settings.
$config['system.performance']['cache']['page']['max_age'] = 0;
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
$settings['cache']['bins']['page'] = 'cache.backend.null';
$settings['extension_discovery_scan_tests'] = FALSE;

// Reduce the kint levels to avoid out of memory errors.
if (file_exists(DRUPAL_ROOT . '/modules/contrib/devel/kint/kint/Kint.class.php')) {
  require_once DRUPAL_ROOT . '/modules/contrib/devel/kint/kint/Kint.class.php';
  Kint::$maxLevels = 4;
}

// Make sure config_split is active for local development.
$config['config_split.config_split.ignore_development_settings']['status'] = TRUE;
