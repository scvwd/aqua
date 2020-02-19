<?php

namespace Drupal\aqua_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Maps D7 scheduler unpublish on dates.
 *
 * Example:
 *
 * @code
 * process:
 *   field_address:
 *     plugin: scheduler_unpublish_on
 *     source: nid
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "scheduler_unpublish_on"
 * )
 */
class SchedulerUnpublishOn extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
    public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
        // Connect to the database defined by key 'migrate'.
        $database = \Drupal\Core\Database\Database::getConnection('default', 'drupal_7');
        $result = $database->select('scheduler', 's')
          ->fields('s', ['nid', 'unpublish_on'])
          ->where('s.nid = :nid', array(':nid' => $value))
          ->execute()
          ->fetchAssoc();
        if (!isset($result['unpublish_on']) || !is_numeric($result['unpublish_on']) || $result == NULL || $result['unpublish_on'] == 0) {
          return NULL;
        }
        return $result['unpublish_on'];
    }
}
