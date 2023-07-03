<?php

namespace Drupal\git_projects_federator\Plugin\migrate_plus\data_parser;

use Drupal\git_projects_federator\Services\GitHubHelper;
use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Obtain JSON data for migration.
 *
 * @DataParser(
 *   id = "json_github",
 *   title = @Translation("JSON GitHub parser")
 * )
 */
class JsonGitHub extends Json {

  /**
   * The GitHub Helper Service.
   *
   * @var \Drupal\git_projects_federator\Services\GitHubHelper
   */
  protected \Drupal\git_projects_federator\Services\GitHubHelper $gitHubHelper;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, GitHubHelper $github_helper) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->gitHubHelper= $github_helper;
    $this->urls = $this->gitHubHelper->getGitHubProjectsPagesUrls($configuration["github_owner"]);
  }

  /**
   * {@inheritdoc}
   * The HTTP client.
   * @param \Drupal\git_projects_federator\Services\GitLabHelper $gitlab_helper
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('github_helper')
    );
  }
}
