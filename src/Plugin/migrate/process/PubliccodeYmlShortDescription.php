<?php

namespace Drupal\eu_oss_catalogue_section\Plugin\migrate\process;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\publiccode_yml_repositories\Services\PubliccodeYmlParserInterface;

/**
 * Returns the Description It value of the PubliccodeYm content.
 *
 * @see \Drupal\migrate\Plugin\MigrateProcessInterface
 *
 * @MigrateProcessPlugin(
 *   id = "publiccode_yml_short_description"
 * )
 */
class PubliccodeYmlShortDescription extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The Publiccode Yml Parser Service.
   *
   * @var \Drupal\publiccode_yml_repositories\Services\PubliccodeYmlParserInterface
   */
  protected $publiccodeYmlParser;

  /**
   * Constructs a PubliccodeYmlDescription plugin.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\publiccode_yml_repositories\Services\PubliccodeYmlParserInterface $publiccode_yml_parser
   *   The field plugin manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PubliccodeYmlParserInterface $publiccode_yml_parser) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->publiccodeYmlParser = $publiccode_yml_parser;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('publiccode_yml_parser')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Get the Description array block from the PubliccodeYaml content.
    $description_block = $this->publiccodeYmlParser->descriptionBlock($value);
    $description = '';
    $langcode = $row->getSourceProperty('langcode') ?? 'it';
    if (!empty($description_block) &&
      array_key_exists($langcode, $description_block) &&
      array_key_exists('longDescription', $description_block[$langcode]) &&
      !empty($description_block[$langcode]['shortDescription'])) {
      $description = $description_block[$langcode]['shortDescription'];
    }
    return $description;
  }

}
