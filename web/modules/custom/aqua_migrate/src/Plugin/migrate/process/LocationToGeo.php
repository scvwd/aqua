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
 *   field_address:
 *     plugin: location_to_geo
 *     source: field_location
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "location_to_geo"
 * )
 */
class LocationToGeo extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   * $value is the array containing the lid for this location
   */
    public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
        $lid = $value['lid'];
        // Connect to the database defined by key 'migrate'.
        $db = \Drupal\Core\Database\Database::getConnection('default', 'drupal_7');
        $location = $db->select('location', 'l')
          ->where('l.lid =  :lid', array(':lid' => $lid))
          ->fields('l', array(
            'latitude',
            'longitude'))
          ->execute()
          ->fetchAssoc();
        if ( $location['longitude'] == '0.000000' && $location['latitude'] == '0.000000' ) {
          return NULL;
        }

        $geo = [
          'value' => "POINT (" . $location['longitude'] . " " . $location['latitude'] . ")",
        ];
        return $geo;
    }
}
