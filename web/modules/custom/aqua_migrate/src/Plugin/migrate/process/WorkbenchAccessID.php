<?php

namespace Drupal\aqua_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Maps D7 workbench access ID values to D8 entity reference field tid.
 *
 * Example:
 *
 * @code
 * process:
 *   field_address:
 *     plugin: workbench_access_id
 *     source: nid
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "workbench_access_id"
 * )
 */
class WorkbenchAccessID extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
    public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
        // Connect to the database defined by key 'migrate'.
        $db = \Drupal\Core\Database\Database::getConnection('default', 'drupal_7');
        $access = $db->select('workbench_access_node', 'w')
          ->where('w.nid = :nid', array(':nid' => $value))
          ->fields('w', array(
            'access_id'))
          ->execute()
          ->fetchAll();
        if ($access == NULL) {
          return NULL;
        }
        $accessID = [];
        foreach ($access as $value) {
          $accessID[] = $value->access_id;
        }
        return $accessID;
    }
}
