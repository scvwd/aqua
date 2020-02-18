<?php

namespace Drupal\coa_migrate\Plugin\migrate\process;

use Drupal\media\Entity\Media;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateException;
use Drupal\migrate\Row;
use Drupal\migrate_plus\Plugin\migrate\process\EntityGenerate;

/**
 * Generate a media entity with specified metadata.
 *
 * This plugin is to be used by migrations which have media entity reference
 * fields.
 *
 * Available configuration keys:
 * - destination_field: the name of the file field on the media entity.
 *
 * @code
 * process:
 *   'field_files/target_id':
 *     -
 *       plugin: migration_lookup
 *       migration: my_file_migration
 *       source: field_image/0/fid
 *     -
 *       plugin: media_generate
 *       destination_field: image
 *       image_alt_source: field_image/0/alt
 *       image_title_source: field_image/0/title
 *
 * @endcode
 *
 * If image_alt_source and/or image_title_source configuration parameters
 * are provided, alt and/or title image properties will be fetched from provided
 * source fields (if available) and pushed into media entity
 *
 * @MigrateProcessPlugin(
 *   id = "media_generate"
 * )
 */
class MediaGenerate extends EntityGenerate {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrateExecutable, Row $row, $destinationProperty) {
    if (!isset($this->configuration['destination_field'])) {
      throw new MigrateException('Destination field must be set.');
    }

    if (empty($value)) {
    	return NULL;
    }

    // First check to see if we all ready have this in a media entity.
    $query = \Drupal::entityQuery('media')
	    ->condition('status', 1)
	    ->condition('field_media_image', $value);
		$mids = $query->execute();
		if ($mids != NULL && count($mids) > 0) {
			return array_pop($mids);
		}
		// Create a new nedia entity.
		$image_media = \Drupal\media\Entity\Media::create([
			'bundle' => 'image',
			'uid' => \Drupal::currentUser()->id(),
			'langcode' => \Drupal::languageManager()->getDefaultLanguage()->getId(),
			'status' => TRUE,
			'field_media_image' => [
				'target_id' => $value

				//@TODO ----- have no idea.				
				// 'target_id' => $value,
				// 'alt' => $row->getDestinationProperty($this->configuration['image_alt_source']),
				// 'title' => $row->getDestinationProperty($this->configuration['image_title_source']),
		  ],
		]);
		$image_media->save();
    return $image_media->id();
  }
}