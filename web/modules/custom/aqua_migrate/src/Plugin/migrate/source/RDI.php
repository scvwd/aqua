<?php

namespace Drupal\aqua_migrate\Plugin\migrate\source;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Utility\NestedArray;
use Drupal\migrate\MigrateException;
use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\migrate\Plugin\MigrationInterface;

/**
 * Source for Recursive Directory Iterator.
 *
 * @MigrateSource(
 *   id = "rdi",
 *   source_module = "aqua_migrate"
 * )
 */
class RDI extends SourcePluginBase implements ConfigurablePluginInterface {

  /**
   * List of available source fields.
   *
   * Keys are the field machine names as used in field mappings, values are
   * descriptions.
   *
   * @var array
   */
  protected $fields = [];

  /**
   * List of key fields, as indexes.
   *
   * @var array
   */
  protected $keys = [];

  /**
   * The iterator class to read the directory hierarchy.
   *
   * @var string
   */
  protected $iteratorClass = '';

  /**
   * The RecursiveDirectoryIterator object that reads files.
   *
   * @var \RecursiveDirectoryIterator
   */
  protected $directory = NULL;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
    $this->setConfiguration($configuration);

    // Path is required.
    if (empty($this->getConfiguration()['path'])) {
      throw new MigrateException('You must declare the "path" to the source directory itegrator.');
    }

    // Filter is required.
    if (empty($this->getConfiguration()['filter'])) {
      throw new MigrateException('You must declare the "filter" to filter content.');
    }

    $this->iteratorClass = $this->getConfiguration()['iterator_class'];
  }

  /**
   * Return a string representing the source file path.
   *
   * @return string
   *   The file path.
   */
  public function __toString() {
    return $this->getConfiguration()['path'];
  }

  /**
   * {@inheritdoc}
   */
  public function initializeIterator() {
    set_time_limit(0);
    if (!file_exists($this->getConfiguration()['path'])) {
      throw new InvalidPluginDefinitionException($this->getPluginId(), sprintf('Directoty path (%s) does not exist.', $this->getConfiguration()['path']));
    }
    $directory = new \RecursiveDirectoryIterator($this->getConfiguration()['path']);
    $iterator = new \RecursiveIteratorIterator($directory);
    return new $this->iteratorClass($iterator, '/' . $this->getConfiguration()['filter'] . '/i', \RecursiveRegexIterator::GET_MATCH);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'id' => [
        'type' => 'string',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'id' => 'ID',
      'path' => 'File path',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * Gets the directory object.
   *
   * @return \RecursiveDirectoryIterator
   *   The directory object.
   */
  public function getDirectory() {
    return $this->directory;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    // We must preserve integer keys for column_name mapping.
    $this->configuration = NestedArray::mergeDeepArray([$this->defaultConfiguration(), $configuration], TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'fields' => [],
      'keys' => [],
      'path' => '',
      'filter' => '',
      'iterator_class' => 'Drupal\aqua_migrate\RecursiveDirectoryIterator',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return [];
  }

}
