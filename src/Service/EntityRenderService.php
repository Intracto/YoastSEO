<?php


namespace Drupal\itr_yoast_seo\Service;


use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Render\RendererInterface;

class EntityRenderService {

  /**
   * @var EntityTypeManager
   */
  private $typeManager;

  /**
   * @var RendererInterface
   */
  private $renderer;


  public function __construct(EntityTypeManager $typeManager, RendererInterface $renderer) {
    $this->typeManager = $typeManager;
    $this->renderer = $renderer;
  }

  /**
   * Given an entity, it will return the rendered version in the 'full' display mode
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   * @return \Drupal\Component\Render\MarkupInterface
   */
  public function previewEntity(EntityInterface $entity) {
    /** @var EntityViewBuilderInterface $view_builder */
    $view_builder = $this->typeManager->getViewBuilder($entity->getEntityTypeId());
    $view_builder->resetCache([$entity]);

    $element = $view_builder->view($entity);
    $element['#entity_type'] = $entity->getEntityTypeId();
    $element['#' . $entity->getEntityTypeId()] = $entity;

    return $this->renderer->render($element);
  }
}