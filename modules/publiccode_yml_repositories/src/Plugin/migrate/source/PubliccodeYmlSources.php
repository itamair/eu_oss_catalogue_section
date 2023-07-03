<?php

namespace Drupal\publiccode_yml_repositories\Plugin\migrate\source;

use Drupal\migrate_plus\Plugin\migrate\source\Url;
use Drupal\migrate\Row;
use Symfony\Component\Yaml\Yaml;

/**
 * Source plugin for setting PubliccodeYml Sources.
 *
 * @MigrateSource(
 *   id = "publiccode_yml_sources"
 * )
 */
class PubliccodeYmlSources extends Url {

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    if ($row->hasSourceProperty('publiccodeYml')) {
      try{
        $publiccode_yml_values = Yaml::parse($row->getSourceProperty('publiccodeYml'));
        // Set a default langcode as italian.
        $langcode = 'it';
        if (isset($publiccode_yml_values) &&
          array_key_exists('description', $publiccode_yml_values) &&
          array_key_exists('en', $publiccode_yml_values['description'])) {
          $langcode = 'en';
        }
        $row->setSourceProperty('langcode', $langcode);

        if (isset($publiccode_yml_values) &&
          array_key_exists('name', $publiccode_yml_values) && !empty($publiccode_yml_values['name'])) {
          $name = $publiccode_yml_values['name'];
        }
        else {
          $name = $row->getSourceProperty("id");
        }

        $row->setSourceProperty('name', $name);

      }
      catch (\Exception $e) {
        watchdog_exception('publiccode_yml_repositories', $e);
      }
    }
    return parent::prepareRow($row);
  }

}
