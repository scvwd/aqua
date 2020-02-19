<?php

namespace Drupal\aqua_migrate\Plugin\migrate\process;

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
 *     plugin: depfeaturevalue
 *     source: nid
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "depfeaturevalue"
 * )
 */
class DepFeatureValue extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
    public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
        // Connect to the database defined by key 'migrate'.
        $database = \Drupal\Core\Database\Database::getConnection('default', 'drupal_7');
        $result = $database->select('field_data_field_dept_feature_views', 'v')
          ->where('v.entity_id = :nid', array(':nid' => $value))
          ->where('v.bundle = :c_type', array(':c_type' => 'department'))
          ->fields('v', ['field_dept_feature_views_view_id'])
          ->execute()
          ->fetchAll();
        $featurevalue = [];
        $newblock = ['dept_page_blocks:panel_pane_11', 'dept_page_blocks:panel_pane_22', 'extra_page_blocks:panel_pane_11'];
        $blogblock = ['dept_page_blocks:panel_pane_32', 'department_features:panel_pane_2', 'department_features:panel_pane_5'];

        foreach ($result as $value) {
          if (in_array($value->field_dept_feature_views_view_id, $newblock) && !in_array('news', $featurevalue)) {
            $featurevalue[] = 'news';
          }
          elseif (in_array($value->field_dept_feature_views_view_id, $blogblock) && !in_array('blogs', $featurevalue)) {
            $featurevalue[] = 'blogs';
          }
        }
        return $featurevalue;
    }
}
