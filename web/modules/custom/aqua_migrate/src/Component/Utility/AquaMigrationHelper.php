<?php

namespace Drupal\coa_migrate\Component\Utility;

/**
 * @file
 */
class CoAMigrationHelper{

  public static function lookupNodeId($title){
    $query = \Drupal::entityQuery('node')
        ->condition('title', $title)
        ->condition('type', 'city_clerk_content');
    $nids = $query->execute();
    if ($nid = reset($nids)) {
      \Drupal::logger('coa_migrate')->notice('@type: node %title (<a href="/node/@nid" target="_blank">@nid</a>).',
        [
          '@type' => 'HTML Imported',
          '@nid' => $nid,
          '%title' => $title,
        ]);
      return $nid;
    }
    return NULL;
  }
  public static function eventLookupNodeId($title){
    $query = \Drupal::entityQuery('node')
        ->condition('title', $title)
        ->condition('type', 'calender_events');
    $nids = $query->execute();
    if ($nid = reset($nids)) {
      \Drupal::logger('coa_migrate')->notice('@type: node %title (<a href="/node/@nid" target="_blank">@nid</a>).',
        [
          '@type' => 'HTML Imported',
          '@nid' => $nid,
          '%title' => $title,
        ]);
      return $nid;
    }
    return NULL;
  }
}
