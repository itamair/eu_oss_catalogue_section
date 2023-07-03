<?php

namespace Drupal\publiccode_yml_repositories\Services;

/**
 * Provides a geocoder factory method interface.
 */
interface PubliccodeYmlParserInterface {

  /**
   * Extracts the OSS Description Block, as array of languages variants.
   *
   * @param string $yml_value
   *   The publiccode.yml_content string.
   *
   * @return array
   *   The language codes associative array of OSS Descriptions.
   */
  public function descriptionBlock(string $yml_value): array;

}
