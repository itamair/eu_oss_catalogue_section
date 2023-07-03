<?php

namespace Drupal\git_projects_federator\Plugin\migrate\process;

use Drupal\migrate_file\Plugin\migrate\process\ImageImport;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\file\Entity\File;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\node\Entity\Node;

/**
 * Imports a Code Europe Project Icon image from Projects source.
 *
 * Extends the regular image_import plugin.
 *
 * @see \Drupal\migrate_file\Plugin\migrate\process\ImageImport.php
 *
 * @MigrateProcessPlugin(
 *   id = "gitlab_project_icon_import"
 * )
 */
class GitLabProjectIconImport extends ImageImport {

  /**
   * Check if a source exists.
   */
  protected function sourceExists($path) {
    if ($this->isLocalUri($path)) {
      return is_file($path);
    }
    else {
      // @FIXME: Find a way to fetch and parse the Gravatar icons
      // Skip at the moment Gravatar icons, as we don't know how to parse them.
      if (strpos($path, 'gravatar') === FALSE
        && @exif_imagetype($path)) {
        try {
          \Drupal::httpClient()->head($path);
          return TRUE;
        }
        catch (ServerException $e) {
          return FALSE;
        }
        catch (ClientException $e) {
          return FALSE;
        }
        catch (ConnectException $e) {
          return FALSE;
        }
      }
      else {
        return FALSE;
      }
    }
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\migrate\MigrateSkipProcessException
   *   Thrown if the source property is not set and rest of the process should
   *   be skipped.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    // Ignore this setting.
    $this->configuration['id_only'] = FALSE;
    // Run the parent transform to do all the file handling.
    $value = parent::transform($value, $migrate_executable, $row, $destination_property);
    $destination_nid = $row->getDestinationProperty('nid');

    // Check if, with null value, the Field Icon already exists and keep it.
    if ($value === NULL && isset($destination_nid) && $node = Node::load($destination_nid)) {
      $file = $node->get('field_icon')->entity;
      if ($file instanceof File) {
        $value = [
          'target_id' => $file->id(),
        ];
        foreach (['title', 'alt', 'width', 'height'] as $key) {
          if ($property = $this->configuration[$key]) {
            $value[$key] = $this->getPropertyValue($property, $row);

          }
        }
      }
      else {
        throw new MigrateSkipProcessException();
      }
    }
    elseif ($value === NULL) {
      throw new MigrateSkipProcessException();
    }

    return $value;

  }

}
