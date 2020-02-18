<?php

namespace Drupal\coa_migrate;

/**
 * Defines a RDI object.
 *
 * @package Drupal\coa_migrate.
 *
 * Extends /RecursiveDirectoryIterator to:
 * - filter file types
 */
class RecursiveDirectoryIterator extends \RegexIterator implements \Countable {

  /**
   * {@inheritdoc}
   */
  public function __construct($iterator, $regex, $mode) {
    call_user_func_array(['parent', '__construct'], func_get_args());
  }

  /**
   * {@inheritdoc}
   */
  public function current() {
    $row = parent::current();
    $cid = md5(basename(dirname($row[0], 1)) . basename($row[0]));
    // if ($cache = \Drupal::cache()->get($cid)) {
    //   return $cache->data;
    // }
    // else {
      $row['id'] = $cid;
      $row['path'] = $row[0];
      $row['filename'] = basename($row[0]);
      $row['dirup1'] = basename(dirname($row[0], 1));
      $buffer1 = file_get_contents($row[0]);
      $buffer1 = str_replace("ÔøΩ", "-", $buffer1);
      $bufRepl = '/<.--- START OF THE FOOTER TABLE 1 --->.*<!--- END OF FOOTER TABLE 1 --->.*<\/table>/s';
      $buffer1 = preg_replace($bufRepl, '', $buffer1);
      // Parse out page title from header.
      $pageTitle = $strPageTitle = '';
      $boolPageTitle = preg_match('/@@([^@@]+)@@/', $buffer1, $pageTitle);
      if ($pageTitle) {
        $strPageTitle = $pageTitle[1];
      }
      else {
        $strPageTitle = $row['filename'];
      }
      $buffer1 = '<center>' . $buffer1 . '</center>';
      $row['title'] = $strPageTitle;
      $row['body'] = mb_convert_encoding($buffer1, 'UTF-8', mb_detect_encoding($buffer1, 'UTF-8, ISO-8859-1', TRUE));
      // \Drupal::cache()->set($cid, $row);
      return $row;
    // }
  }

  /**
   * Return a count of all available source records.
   */
  public function count() {
    $items_all = iterator_to_array($this);
    $items = [];
    foreach ($items_all as $key => $value) {
      $items[$value['id']] = $value;
    }
    return count($items);
  }

}
