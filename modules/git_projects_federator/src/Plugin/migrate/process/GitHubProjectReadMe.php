<?php

namespace Drupal\git_projects_federator\Plugin\migrate\process;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\ClientInterface;
use Drupal\git_projects_federator\Services\GitHubHelper;
use GuzzleHttp\Exception\RequestException;

/**
 * Returns the Content of the Read.me file of specific UN GitLab Project.
 *
 * @see \Drupal\migrate\Plugin\MigrateProcessInterface
 *
 * @MigrateProcessPlugin(
 *   id = "github_project_readme"
 * )
 */
class GitHubProjectReadMe extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected \GuzzleHttp\ClientInterface $httpClient;

  /**
   * Project names to skip, for import issues.
   */
  protected $projectNamesToSkip = [
    'datos.gob.es',
  ];

  /**
   * The GitHub Helper Service.
   *
   * @var \Drupal\git_projects_federator\Services\GitHubHelper
   */
  protected \Drupal\git_projects_federator\Services\GitHubHelper $gitHubHelper;

  /**
   * Constructs a GitHubProjectReadMe plugin.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   * @param \Drupal\git_projects_federator\Services\GitHubHelper $github_helper
   *   The GitHub Helper
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $http_client, GitHubHelper $github_helper) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
    $this->gitHubHelper = $github_helper;
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
      $container->get('github_helper')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($project_name, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $readme_md = '';

    if (!in_array($project_name, $this->projectNamesToSkip)){

      $github_owner = $row->getSource()["github_owner"];
      $project_info = $this->gitHubHelper->gitHubProjectInfo($github_owner, $project_name);
      if (!empty($project_info) && isset($project_info['default_branch'])) {
        $url = 'https://raw.githubusercontent.com/' . $github_owner . '/' . $project_name . '/' . $project_info['default_branch'] . '/README.md';
        $options = [
          'headers' => $this->gitHubHelper->getRequestHeaders(),
        ];
        try {
          $readme_md_response = $this->httpClient->get($url, $options);
          $readme_md = $readme_md_response->getBody()->getContents();

          // @todo: we have collation importing issues with
          // $this->projectNamesToSkip, so we need better and working
          // workaround below.

          // Eventually string utf8 encode the $readme_md if detected in
          // 'ISO-8859-1' format.
          /*        if(mb_detect_encoding($readme_md) !== 'UTF-8') {
                    $readme_md = utf8_encode($readme_md);
                  }*/

          // mb_convert_encoding($readme_md, 'UTF-8', 'UTF-8');

        }
        catch (RequestException $e) {
          /*        \Drupal::logger('git_projects')->warning($this->t('@message. No Readme file found for Git Project id:@id', [
                    '@message' => $e->getMessage(),
                    '@id' => $project_id,
                  ]));*/
        }
      }
    }
    return $readme_md;
  }

}
