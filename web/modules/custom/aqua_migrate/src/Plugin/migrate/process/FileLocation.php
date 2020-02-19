<?php

namespace Drupal\aqua_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Converts a file path to thats whats needed.
 *
 * Example:
 *
 * @code
 * process:
 *   new_filepath:
 *     plugin: file_location
 *     source: filepath
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "file_location"
 * )
 */
class FileLocation extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
    public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
        if ($value != NULL && is_string($value)) {
          return str_replace('sites/default/files/', '', $value);
        }
        return NULL;
    }
}
