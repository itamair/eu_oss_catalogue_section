<?php

namespace Drupal\git_projects_federator\Plugin\migrate\source;

use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_plus\Plugin\migrate\source\Url;
use Drupal\migrate\Row;
use Drupal\Core\Database\Database;

/**
 * Source plugin for retrieving via URLs members data for each Git Project.
 *
 * @MigrateSource(
 *   id = "github_projects_urls"
 * )
 */
class GitHubProjectsUrls extends Url {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * The GitLab Helper Service.
   *
   * @var \Drupal\git_projects_federator\Services\GitHubHelper
   */
  protected \Drupal\git_projects_federator\Services\GitHubHelper $gitHubHelper;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
    $this->gitHubHelper = \Drupal::service('github_helper');
    $this->sourceUrls = $this->gitHubHelper->getGitHubProjectsPagesUrls($configuration["github_owner"]);
    $this->httpClient = \Drupal::httpClient();
  }
}
