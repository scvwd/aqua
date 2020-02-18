<?php

namespace Drupal\coa_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Maps D7 location values to D8 address values.
 *
 * Example:
 *
 * @code
 * process:
 *   field_xyz:
 *     plugin: embedfeaturevalue
 *     source: nid
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "embedfeaturevalue"
 * )
 */
class EmbedFeatureValue extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
    public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
        // Connect to the database defined by key 'migrate'.
        $database = \Drupal\Core\Database\Database::getConnection('default', 'drupal_7');
        $result = $database->select('field_data_field_dept_feature_views', 'v')
          ->where('v.entity_id = :nid', array(':nid' => $value))
          ->where('v.bundle = :c_type', array(':c_type' => 'department'))
          ->where('v.field_dept_feature_views_view_id = :view_id', array(':view_id' => 'department_features:panel_pane_1'))
          ->fields('v', ['field_dept_feature_views_arguments'])
          ->execute()
          ->fetchAll();
        $videoValue = [];
        foreach ($result as $value) {
          $videoValue[] = $value->field_dept_feature_views_arguments;
        }
        return $videoValue;
    }
}
