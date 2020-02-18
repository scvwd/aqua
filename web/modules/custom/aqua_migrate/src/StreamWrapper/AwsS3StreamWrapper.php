<?php

namespace Drupal\coa_migrate\StreamWrapper;

use Aws\S3\StreamWrapper;
use Drupal\Core\StreamWrapper\StreamWrapperInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Site\Settings;
use Aws\S3\S3Client;

/**
 * Defines a Drupal s3 (s3://) stream wrapper class.
 *
 * Provides support for storing files on the amazon s3 file system with the
 * Drupal file interface.
 */
class AwsS3StreamWrapper extends StreamWrapper implements StreamWrapperInterface {

  use StringTranslationTrait;

  /**
   * The AWS SDK for PHP S3Client object.
   *
   * @var \Aws\S3\S3Client
   */
  protected $s3 = NULL;

  /**
   * Module configuration for stream.
   *
   * @var array
   */
  private $config = [];

  /**
   * Instance uri referenced as "<scheme>://key".
   *
   * @var string
   */
  protected $uri = NULL;

  public function __construct() {
    $this->s3 = $this->getClient();
    $this->register($this->s3);
  }

  /**
   * {@inheritdoc}
   */
  private function getClient() {
    $this->config = $this->getConfig();
    return new S3Client($this->config);
  }

  /**
   * {@inheritdoc}
   */
  private function getConfig() {
    $access_key = Settings::get('s3.access_key', '');
    $secret_key = Settings::get('s3.secret_key', '');
    $client_config['credentials'] = [
      'key' => $access_key,
      'secret' => $secret_key,
    ];
    $client_config['region'] = Settings::get('s3.region', 'us-east-1');
    $client_config['signature'] = Settings::get('s3.signature', 'v4');
    $client_config['version'] = Settings::get('s3.version', '2006-03-01');
    $client_config['bucket'] = Settings::get('s3.bucket', 'city-clerk');
    return $client_config;
  }

  /**
   * {@inheritdoc}
   */
  public static function getType() {
    return StreamWrapperInterface::NORMAL;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->t('S3 File System');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('Amazon Simple Storage Service.');
  }

  /**
   * Gets the path that the wrapper is responsible for.
   *
   * This function isn't part of DrupalStreamWrapperInterface, but the rest
   * of Drupal calls it as if it were, so we need to define it.
   *
   * @return string
   *   The empty string. Since this is a remote stream wrapper,
   *   it has no directory path.
   *
   * @see \Drupal\Core\File\LocalStream::getDirectoryPath()
   */
  public function getDirectoryPath() {
    return '';
  }

  /**
   * {@inheritdoc}
   *
   * Sets the stream resource URI. URIs are formatted as "<scheme>://filepath".
   *
   * @param string $uri
   *   The URI that should be used for this instance.
   */
  public function setUri($uri) {
    $this->uri = $uri;
  }

  /**
   * {@inheritdoc}
   *
   * Returns the stream resource URI, which looks like "<scheme>://filepath".
   *
   * @return string
   *   The current URI of the instance.
   */
  public function getUri() {
    return $this->uri;
  }

  /**
   * {@inheritdoc}
   *
   * This wrapper does not support realpath().
   *
   * @return bool
   *   Always returns FALSE.
   */
  public function realpath() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   *
   * Returns a web accessible URL for the resource.
   *
   * The format of the returned URL will be different depending on how the S3
   * integration has been configured on the S3 File System admin page.
   *
   * @return string
   *   A web accessible URL for the resource.
   */
  public function getExternalUrl() {
    $s3_key = str_replace('\\', '/', file_uri_target($this->uri));
    return $this->s3->getObjectUrl($this->config['bucket'], $s3_key);
  }

  /**
   * {@inheritdoc}
   *
   * This wrapper does not support flock().
   *
   * @return bool
   *   Always Returns FALSE.
   *
   * @see http://php.net/manual/en/streamwrapper.stream-lock.php
   */
  public function stream_lock($operation) {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   *
   * Gets the name of the parent directory of a given path.
   *
   * This method is usually accessed through:
   * \Drupal::service('file_system')->dirname(),
   * which wraps around the normal PHP dirname() function, since it doesn't
   * support stream wrappers.
   *
   * @param string $uri
   *   An optional URI.
   *
   * @return string
   *   The directory name, or FALSE if not applicable.
   *
   * @see \Drupal::service('file_system')->dirname()
   */
  public function dirname($uri = NULL) {
    if (!isset($uri)) {
      $uri = $this->uri;
    }
    $scheme = \Drupal::service('file_system')->uriScheme($uri);
    $dirname = dirname(file_uri_target($uri));

    // When the dirname() call above is given '$scheme://', it returns '.'.
    // But '$scheme://.' is an invalid uri, so we return "$scheme://" instead.
    if ($dirname == '.') {
      $dirname = '';
    }

    return "$scheme://$dirname";
  }

  /**
   * {@inheritdoc}
   *
   * Since Windows systems do not allow it and it is not needed for most use
   * cases anyway, this method is not supported on local files and will trigger
   * an error and return false. If needed, custom subclasses can provide
   * OS-specific implementations for advanced use cases.
   */
  public function stream_set_option($option, $arg1, $arg2) {
    trigger_error('stream_set_option() not supported for local file based stream wrappers', E_USER_WARNING);
    return FALSE;
  }

  /**
   * Sets metadata on the stream.
   *
   * @param string $path
   *   A string containing the URI to the file to set metadata on.
   * @param int $option
   *   One of:
   *   - STREAM_META_TOUCH: The method was called in response to touch().
   *   - STREAM_META_OWNER_NAME: The method was called in response to chown()
   *     with string parameter.
   *   - STREAM_META_OWNER: The method was called in response to chown().
   *   - STREAM_META_GROUP_NAME: The method was called in response to chgrp().
   *   - STREAM_META_GROUP: The method was called in response to chgrp().
   *   - STREAM_META_ACCESS: The method was called in response to chmod().
   * @param mixed $value
   *   If option is:
   *   - STREAM_META_TOUCH: Array consisting of two arguments of the touch()
   *     function.
   *   - STREAM_META_OWNER_NAME or STREAM_META_GROUP_NAME: The name of the owner
   *     user/group as string.
   *   - STREAM_META_OWNER or STREAM_META_GROUP: The value of the owner
   *     user/group as integer.
   *   - STREAM_META_ACCESS: The argument of the chmod() as integer.
   *
   * @return bool
   *   Returns TRUE on success or FALSE on failure. If $option is not
   *   implemented, FALSE should be returned.
   *
   * @see http://www.php.net/manual/streamwrapper.stream-metadata.php
   */
// @codingStandardsIgnoreStart
  public function stream_metadata($path, $option, $value) {
// @codingStandardsIgnoreEnd
    // We don't really do any of these, but we want to reassure the calling code
    // that there is no problem with chown or chgrp, even though we do not
    // actually support these.
    return TRUE;
  }

  /**
   * Truncate stream.
   *
   * Will respond to truncation; e.g., through ftruncate().
   *
   * @param int $new_size
   *   The new size.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   *
   * @todo
   *   Allow truncating the stream.
   *   https://www.drupal.org/project/examples/issues/2992398
   */
  // @codingStandardsIgnoreStart
  public function stream_truncate($new_size) {
    // @codingStandardsIgnoreEnd
    return FALSE;
  }

  /**
   * {@inheritdoc}
   *
   * Support for fopen(), file_get_contents(), file_put_contents() etc.
   *
   * @param string $uri
   *   The URI of the file to open.
   * @param string $mode
   *   The file mode. Only 'r', 'w', 'a', and 'x' are supported.
   * @param int $options
   *   A bit mask of STREAM_USE_PATH and STREAM_REPORT_ERRORS.
   * @param string $opened_path
   *   An OUT parameter populated with the path which was opened.
   *   This wrapper does not support this parameter.
   *
   * @return bool
   *   TRUE if file was opened successfully. Otherwise, FALSE.
   *
   * @see http://php.net/manual/en/streamwrapper.stream-open.php
   */
  public function stream_open($uri, $mode, $options, &$opened_path) {
    $this->setUri($uri);
    return parent::stream_open($uri, $mode, $options, $opened_path);
  }

}
