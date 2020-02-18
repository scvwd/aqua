<?php

namespace Drupal\coa_migrate\Plugin\migrate_plus\data_fetcher;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\migrate_plus\DataFetcherPluginBase;
use Drupal\migrate\Plugin\MigrationInterface;

/**
 * Retrieve data from a local path or general URL for migration.
 *
 * @DataFetcher(
 *   id = "customfile",
 *   title = @Translation("Custom File")
 * )
 */
class CustomFile extends DataFetcherPluginBase {

  /**
   * {@inheritdoc}
   */
  public function setRequestHeaders(array $headers) {
    // Does nothing.
  }

  /**
   * {@inheritdoc}
   */
  public function getRequestHeaders() {
    // Does nothing.
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getResponse($url) {
    if(strpos($url, '/boards_commissions/logs/calendar.xml')) {
      $url = "s3://city-clerk";
    }
    $directory = new \RecursiveDirectoryIterator($url);
    $url = $url."/".$directory.'/boards_commissions/logs/calendar.xml';
    if(!file_exists($url)){
      throw new InvalidPluginDefinitionException(sprintf('Directoty path '.$url. ' does not exist.'));
    }

    $response = file_get_contents($url);
    if ($response === FALSE) {
      throw new MigrateException('file parser plugin: could not retrieve data from ' . $url);
    }
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function getResponseContent($url) {
    $response = $this->getResponse($url);
    return $response;
  }

}
