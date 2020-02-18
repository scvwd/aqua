<?php

namespace Drupal\coa_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Maps D7 scheduler publish on dates.
 *
 * Example:
 *
 * @code
 * process:
 *   field_address:
 *     plugin: scheduler_publish_on
 *     source: nid
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "scheduler_publish_on"
 * )
 */
class SchedulerPublishOn extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
    public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
        // Connect to the database defined by key 'migrate'.
        $database = \Drupal\Core\Database\Database::getConnection('default', 'drupal_7');
        $result = $database->select('scheduler', 's')
          ->fields('s', ['nid', 'publish_on'])
          ->where('s.nid = :nid', array(':nid' => $value))
          ->execute()
          ->fetchAssoc();
        if (!isset($result['publish_on']) || !is_numeric($result['publish_on']) || $result == NULL || $result['publish_on'] == 0) {
          return NULL;
        }
        return $result['publish_on'];
    }
}
