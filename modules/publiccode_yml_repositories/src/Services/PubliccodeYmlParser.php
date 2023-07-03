<?php

namespace Drupal\publiccode_yml_repositories\Services;

use Drupal\Component\Serialization\Yaml;

/**
 * Provides a PubliccodeYmlParser Service class.
 */
class PubliccodeYmlParser implements PubliccodeYmlParserInterface {

  /**
   * The Yaml Serialization service.
   *
   * @var \Drupal\Component\Serialization\Yaml
   */
  protected Yaml $serializationYaml;

  /**
   * Constructs a PubliccodeYmlParser Service class.
   *
   * @param \Drupal\Component\Serialization\Yaml $serialization_yaml
   *   The Yaml Serialization service.
   */
  public function __construct(Yaml $serialization_yaml) {
    $this->serializationYaml = $serialization_yaml;
  }

  /**
   * {@inheritdoc}
   */
  public function descriptionBlock(string $yml_value): array {
    $description_block = [];
    try {
      $publiccode_yml_values = $this->serializationYaml->decode($yml_value);
      if (array_key_exists('description', $publiccode_yml_values)) {
        $description_block = $publiccode_yml_values['description'];
      }
    }
    catch (\Exception $e) {

    }
    return $description_block;
  }
}
