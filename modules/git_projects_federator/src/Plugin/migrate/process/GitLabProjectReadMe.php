<?php

namespace Drupal\git_projects_federator\Plugin\migrate\process;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\ClientInterface;
use Drupal\git_projects_federator\Services\GitLabHelper;
use GuzzleHttp\Exception\RequestException;

/**
 * Returns the Content of the Read.me file of specific UN GitLab Project.
 *
 * @see \Drupal\migrate\Plugin\MigrateProcessInterface
 *
 * @MigrateProcessPlugin(
 *   id = "gitlab_project_readme"
 * )
 */
class GitLabProjectReadMe extends ProcessPluginBase implements ContainerFactoryPluginInterface {

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
   *   The GitLab Helper
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $http_client, GitLabHelper $gitlab_helper) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
    $this->gitLabHelper = $gitlab_helper;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
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
  public function transform($project_id, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $readme_md = '';
    $gitlab_domain = $row->getSource()["gitlab_domain"];
    $project_info = $this->gitLabHelper->gitLabProjectInfo($gitlab_domain, $project_id);
    if (!empty($project_info) && isset($project_info['default_branch'])) {
      $url = $this->gitLabHelper->getProjectsEndpoint($gitlab_domain) . '/' . $project_id . '/repository/files/README.md/raw?ref=' . $project_info['default_branch'];
      $options = [];
      try {
        $readme_md_response = $this->httpClient->get($url, $options);
        $readme_md = $readme_md_response->getBody()->getContents();
      }
      catch (RequestException $e) {
/*        \Drupal::logger('git_projects')->warning($this->t('@message. No Readme file found for Git Project id:@id', [
          '@message' => $e->getMessage(),
          '@id' => $project_id,
        ]));*/
      }
    }
    return $readme_md;
  }

}
