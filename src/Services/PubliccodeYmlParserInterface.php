<?php

namespace Drupal\eu_oss_catalogue_section\Services;

/**
 * Provides a PubliccodeYml Parser Interface.
 */
interface PubliccodeYmlParserInterface {

  /**
   * Converts the Publiccode.Yml string/row content into array of values.
   *
   * @param string $publiccode_yml_string_value
   *   The publiccode.yml string content.
   *
   * @return array
   *   The publiccode.yml array values.
   */
  public function publiccodeYmlArrayValues(string $publiccode_yml_string_value): array;

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
