<?php

namespace Drupal\eu_oss_catalogue_section\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Converts a false/true value into a 0/1 boolean.
 *
 * @see \Drupal\migrate\Plugin\MigrateProcessInterface
 *
 * @MigrateProcessPlugin(
 *   id = "convert_boolean",
 *   handle_multiples = TRUE
 * )
 */
class ConvertBoolean extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    switch (strtolower($value)) {
      case 'false':
        $value = 0;
        break;

      case 'true':
        $value = 1;
        break;
    }
    return $value;
  }

}
