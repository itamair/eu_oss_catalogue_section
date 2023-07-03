<?php

namespace Drupal\git_projects_federator\Plugin\migrate_plus\data_parser;

use Drupal\git_projects_federator\Services\GitLabHelper;
use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Obtain JSON data for migration.
 *
 * @DataParser(
 *   id = "json_gitlab",
 *   title = @Translation("JSON GitLab parser")
 * )
 */
class JsonGitLab extends Json {

  /**
   * The GitLab Helper Service.
   *
   * @var \Drupal\git_projects_federator\Services\GitLabHelper
   */
  protected \Drupal\git_projects_federator\Services\GitLabHelper $gitLabHelper;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, GitLabHelper $gitlab_helper) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->gitLabHelper = $gitlab_helper;
    $this->urls = $this->gitLabHelper->getGitLabProjectsPagesUrls($configuration["gitlab_domain"]);
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
      $container->get('gitlab_helper')
    );
  }
}
