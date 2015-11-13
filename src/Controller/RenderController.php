<?php


namespace Drupal\itr_yoast_seo\Controller;


use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class RenderController implements ContainerInjectionInterface {

  /**
   * @var RendererInterface
   */
  private $renderer;


  /**
   * @param \Drupal\Core\Render\RendererInterface $renderer
   */
  public function __construct(RendererInterface $renderer) {
    $this->renderer = $renderer;
  }

  /**
   * @inheritdoc
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('renderer')
    );
  }

  /**
   * Returns a rendered node as a json object
   *
   * @param \Drupal\node\Entity\Node $node
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function renderNode(Node $node) {
    $node_view = node_view($node);
    $output = $this->renderer->render($node_view);

    return new JsonResponse($output->jsonSerialize());
  }
}