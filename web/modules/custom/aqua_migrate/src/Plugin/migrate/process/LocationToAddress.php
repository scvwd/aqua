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
 *     plugin: location_to_address
 *     source: field_location
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "location_to_address"
 * )
 */
class LocationToAddress extends ProcessPluginBase {

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
            'name',
            'street',
            'additional',
            'city',
            'province',
            'postal_code',
            'country'))
          ->execute()
          ->fetchAssoc();
        $address = [
          'given_name' => '',
          'additional_name' => '',
          'family_name' => '',
          'organization' => $location['name'],
          'address_line1' => $location['street'],
          'address_line2' => $location['additional'],
          'postal_code' => trim($location['postal_code']),
          'sorting_code' => '',
          'dependent_locality' => '',
          'locality' => $location['city'],
          'administrative_area' => $location['province'],
          'country_code' => strtoupper($location['country']),
        ];
        return $address;
    }
}
