<?php

namespace Drupal\git_projects_federator\Plugin\migrate\source;

use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_plus\Plugin\migrate\source\Url;
use Drupal\migrate\Row;
use Drupal\Core\Database\Database;

/**
 * Source plugin for retrieving via URLs members data for each Git Lab Project.
 *
 * @MigrateSource(
 *   id = "gitlab_projects_urls"
 * )
 */
class GitLabProjectsUrls extends Url {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * The GitLab Helper Service.
   *
   * @var \Drupal\git_projects_federator\Services\GitLabHelper
   */
  protected \Drupal\git_projects_federator\Services\GitLabHelper $gitLabHelper;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
    $this->gitLabHelper = \Drupal::service('gitlab_helper');
    $this->sourceUrls = $this->gitLabHelper->getGitLabProjectsPagesUrls($configuration["gitlab_domain"]);
    $this->httpClient = \Drupal::httpClient();
  }
}
