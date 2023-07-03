<?php

declare(strict_types = 1);

namespace Drupal\git_projects_federator\Plugin\migrate_plus\data_parser;

use Drupal\git_projects_federator\Services\GitLabHelper;
use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Obtain JSON data for migration.
 *
 * @DataParser(
 *   id = "json_gitlab_with_publiccode_yml",
 *   title = @Translation("JSON GitLab with Publiccode.yml")
 * )
 */
class JsonGitLabWithPubliccodeYml extends Json {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected \GuzzleHttp\ClientInterface $httpClient;

  /**
   * The GitLab Helper Service.
   *
   * @var \Drupal\git_projects_federator\Services\GitLabHelper
   */
  protected \Drupal\git_projects_federator\Services\GitLabHelper $gitLabHelper;

  /**
   * Constructs a GitLabProjectReadMe plugin.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   * @param \Drupal\git_projects_federator\Services\GitLabHelper $gitlab_helper
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $http_client, GitLabHelper $gitlab_helper) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
    $this->gitLabHelper = $gitlab_helper;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client'),
      $container->get('gitlab_helper')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function fetchNextRow(): void {
    $current = $this->iterator->current();
    if ($current) {
      foreach ($this->fieldSelectors() as $field_name => $selector) {
        $field_data = $current;
        $field_selectors = explode('/', trim( $selector, '/'));
        foreach ($field_selectors as $field_selector) {
          if (is_array($field_data) && array_key_exists($field_selector, $field_data)) {
            $field_data = $field_data[$field_selector];
          }
          else {
            $field_data = '';
          }
        }
        $this->currentItem[$field_name] = $field_data;
      }
      if (!empty($this->configuration['include_raw_data'])) {
        $this->currentItem['raw'] = $current;
      }
      $this->iterator->next();
    }
  }

}
