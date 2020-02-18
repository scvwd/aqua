<?php

namespace Drupal\coa_migrate\Plugin\migrate\process;

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
 *     plugin: find_parent_blog
 *     source: nid
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "find_parent_blog"
 * )
 */
class FindParentBlog extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
    public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
        // Connect to the database defined by key 'migrate'.
        $db = \Drupal\Core\Database\Database::getConnection('default', 'drupal_7');

        $title = $db->select('field_data_field_group_title', 'f')
          ->where('f.entity_id =:id', array(':id' => $value))
          ->fields('f', array('field_group_title_value'))
          ->execute()
          ->fetchAssoc();
        if (isset($title['field_group_title_value'])) {
          $blog_name = $db->select('field_data_field_group_title', 'f')
            ->where('f.field_group_title_value =:title', array(':title' => $title['field_group_title_value']))
            ->condition('f.bundle', 'blog_name')
            ->fields('f', array('entity_id'))
            ->execute()
            ->fetchAssoc();
        }
        if (isset($blog_name['entity_id'])) {
          return $blog_name['entity_id'];
        }
        
        return NULL;
    }
}
