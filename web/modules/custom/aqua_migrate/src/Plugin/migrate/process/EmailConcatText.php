<?php

namespace Drupal\coa_migrate\Plugin\migrate\process;

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
 *     plugin: email_concat_text
 *     source: field_email
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "email_concat_text"
 * )
 */
class EmailConcatText extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
    public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
      $email_val = $value;
        if(!isset($email_val)){
          return NULL;
        }
        return $email_val['email']."@austintexas.gov";
    }
}
