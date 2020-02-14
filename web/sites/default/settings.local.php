<?php


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
 
$settings['config_sync_directory'] = '../config/sync';

$settings['trusted_host_patterns'] = array(
  '^scvwd\.lndo\.site$',
  '^localhost$',
);
