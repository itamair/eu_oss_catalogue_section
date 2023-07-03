<?php

namespace Drupal\git_projects_federator\Services;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Component\Serialization\Json;

/**
 * Provides a GitLab Helper Service.
 */
class GitLabHelper {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected \GuzzleHttp\ClientInterface $httpClient;

  /**
   * The Number of the Element to retrieve for each page.
   *
   * @var int
   */
  protected int $perPage;

  /**
   * The GitLab Projects Endpoint.
   *
   * @var string
   */
  protected string $projectsEndPoint;

  /**
   * The GitLab Users Endpoint.
   *
   * @var string
   */
  protected string $usersEndPoint;

  /**
   * The GitLab Projects Pages Urls list.
   *
   * @var array
   */
  public array $gitLabProjectsPagesUrls;

  /**
   * The GitLab Users Pages Urls list.
   *
   * @var array
   */
  public array $gitLabUsersPagesUrls;

  /**
   * Constructor of the GitLab Helper Service.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   */
  public function __construct(
    ClientInterface $http_client
  ) {
    $this->perPage = 50;
    $this->httpClient = $http_client;
  }

  /**
   * Get GitLab Project Info.
   *
   *  @param string $gitlab_domain
   *   The gitlab domain string.
   *
   * @param int $id
   *   The project id.
   *
   * @return array
   *   The GitLab Projects Pages Urls list.
   */
  public function gitLabProjectInfo(string $gitlab_domain, int $id): array {
    $project_end_point = $gitlab_domain . '/api/v4/projects/' . $id;
    try {
      $response = $this->httpClient->get($project_end_point);
      return JSON::decode($response->getBody()->getContents());
    }
    catch (ConnectException $e) {
      return [];
    }
  }

  /**
   * Return the GitLab Projects Endpoint.
   *
   *  @param string $gitlab_domain
   *   The gitlab domain string.
   *
   * @return string
   *   The Projects Endpoint path.
   */
  public function getProjectsEndpoint(string $gitlab_domain): string {
    return $gitlab_domain . '/api/v4/projects';
  }

  /**
   * Return the GitLab Users Endpoint.
   *
   *  @param string $gitlab_domain
   *   The gitlab domain string.
   *
   * @return string
   *   The Users Endpoint path.
   */
  public function getUsersEndPoint(string $gitlab_domain): string {
    return $gitlab_domain . '/api/v4/users';
  }

  /**
   * Get the GitLab Projects Pages Urls list.
   *
   *  @param string $gitlab_domain
   *   The gitlab domain string.
   *
   * @return array
   *   The GitLab Projects Pages Urls list.
   */
  public function getGitLabProjectsPagesUrls(string $gitlab_domain): array {
    $page = 1;
    $total_pages = 1;
    $gitlab_projects_pages_urls = [];
    $options = [
      'query' => [
        'per_page' => $this->perPage,
      ],
    ];

    // Generate the gitlab projects pages endpoints urls, based on the perPage
    // configuration and the total projects found.
    while ($page <= $total_pages) {
      $options['query']['page'] = $page;
      try {
        $gitlab_projects_pages_urls_response = $this->httpClient->get($this->getProjectsEndpoint($gitlab_domain), $options);
        $gitlab_projects_pages_urls_response_headers = $gitlab_projects_pages_urls_response->getHeaders();
        // Re-fetch each cycle the number of total pages, to eventually catch
        // meantime updates.
        $total_pages = $gitlab_projects_pages_urls_response_headers['X-Total-Pages'][0];
        $options['query']['page'] = $page;
        $gitlab_projects_pages_urls[] = $this->getProjectsEndpoint($gitlab_domain) . '?' . UrlHelper::buildQuery($options['query']);
        $page++;

      }
      catch (ConnectException $e) {
        \Drupal::messenger()->addError(t('@e. Migrations cannot be performed because of connection failure to: @hostname', [
          '@e' => $e->getMessage(),
          '@hostname' => $gitlab_domain,
        ]));
        return [];
      }
    }
    return $gitlab_projects_pages_urls;
  }

  /**
   * Return the GitLab Users Pages Urls list.
   *
   *  @param string $gitlab_domain
   *   The gitlab domain string.
   *
   * @return array
   *   The GitLab Users Pages Urls list.
   */
  public function getGitLabUsersPagesUrls(string $gitlab_domain): array {
    $page = 1;
    $total_pages = 1;
    $gitlab_users_pages_urls = [];
    $options = [
      'query' => [
        'per_page' => $this->perPage,
      ],
    ];

    // Generate the gitlab projects pages endpoints urls, based on the perPage
    // configuration and the total projects found.
    while ($page <= $total_pages) {
      $options['query']['page'] = $page;
      try {
        $gitlab_users_pages_urls_response = $this->httpClient->get($this->getProjectsEndpoint($gitlab_domain), $options);
        $gitlab_users_pages_urls_response_headers = $gitlab_users_pages_urls_response->getHeaders();
        // Re-fetch each cycle the number of total pages, to eventually catch
        // meantime updates.
        $total_pages = $gitlab_users_pages_urls_response_headers['X-Total-Pages'][0];
        $options['query']['page'] = $page;
        $gitlab_users_pages_urls[] = $this->getUsersEndPoint($gitlab_domain) .'?' . UrlHelper::buildQuery($options['query']);
        $page++;

      }
      catch (ConnectException $e) {
        \Drupal::messenger()->addError(t('@e. Migrations cannot be performed because of connection failure to: @hostname', [
          '@e' => $e->getMessage(),
          '@hostname' => $gitlab_domain,
        ]));
        return [];
      }
    }
    return $gitlab_users_pages_urls;
  }

}
