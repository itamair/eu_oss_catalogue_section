<?php

namespace Drupal\publiccode_yml_repositories\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\publiccode_yml_repositories\PubliccodeSourcePluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Publiccode yml repositories routes.
 */
class PubliccodeSourcesController extends ControllerBase {

  /**
   * The PubliccodeSourcePluginManager manager service.
   *
   * @var \Drupal\publiccode_yml_repositories\PubliccodeSourcePluginManager
   */
  protected $publiccodeSourceManager;

  /**
   * The controller constructor.
   *
   * @param \Drupal\publiccode_yml_repositories\PubliccodeSourcePluginManager $publiccode_source_manager
   *   The PubliccodeSourcePluginManager manager service.
   */
  public function __construct(PubliccodeSourcePluginManager $publiccode_source_manager) {
    $this->publiccodeSourceManager = $publiccode_source_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.publiccode_source')
    );
  }

  /**
   * Builds the response.
   */
  public function build() {
    $publiccode_sources_definitions = $this->publiccodeSourceManager->getDefinitions();

    $build['sources'] = [];

    foreach ($publiccode_sources_definitions as $k => $source) {
      $build['sources'][$k] = [
        "#type" => "html_tag",
        "#tag" => "div",
        [
          "#type" => "html_tag",
          "#tag" => "span",
          "#value" => $source['label'],
        ],
        [
          "#type" => "html_tag",
          "#tag" => "span",
          "#value" => "(" . $this->t("Type:") . " " . $source['type'] . ")",
        ],
      ];
    }
    return $build;
  }

}
