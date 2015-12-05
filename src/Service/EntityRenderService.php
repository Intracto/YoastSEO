<?php


namespace Drupal\itr_yoast_seo\Service;


use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\CreatedItem;
use Drupal\Core\Form\FormStateInterface;
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

  /**
   * @var EntityInterface
   */
  private $entity;


  public function __construct(EntityTypeManager $typeManager, RendererInterface $renderer) {
    $this->typeManager = $typeManager;
    $this->renderer = $renderer;
  }

  /**
   * Given an entity, it will return the rendered version in the 'full' display mode
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @return \Drupal\Component\Render\MarkupInterface
   */
  public function previewEntity(EntityInterface $entity, array $form, FormStateInterface $form_state) {
    $this->entity = $entity;
    $this->entity->in_preview = TRUE;

    // Remove button and internal Form API values from sent values.
    $form_state->cleanValues();
    $this->entity = $this->buildEntity($form, $form_state);

    $this->entity->setNewRevision(FALSE);

    /** @var EntityViewBuilderInterface $view_builder */
    $view_builder = $this->typeManager
      ->getViewBuilder($this->entity->getEntityTypeId());
    $view_builder->resetCache([$this->entity]);
    $build = $view_builder->view($this->entity);

    // Don't render cache previews
    unset($build['#cache']);

    $build['#entity_type'] = $this->entity->getEntityTypeId();
    $build['#' . $entity->getEntityTypeId()] = $this->entity;

    return $this->renderer->render($build);
  }

  /**
   * Builds an updated entity object based upon the submitted form values.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @return \Drupal\Core\Entity\EntityInterface
   *
   * @see EntityFormInterface::buildEntity
   */
  private function buildEntity(array $form, FormStateInterface $form_state) {
    $entity = clone $this->entity;
    $this->copyFormValuesToEntity($entity, $form, $form_state);

    // Invoke all specified builders for copying form values to entity properties
    if (isset($form['#entity_builders'])) {
      foreach ($form['#entity_builders'] as $function) {
        call_user_func_array($function, [$entity->getEntityTypeId(), $entity, &$form, &$form_state]);
      }
    }

    return $entity;
  }

  /**
   * Copies top-level form values to entity properties
   *
   * This should not change existing entity properties that are not being edited
   * by this form.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @see EntityForm::copyFormValuesToEntity
   */
  private function copyFormValuesToEntity(EntityInterface $entity, array $form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    if ($this->entity instanceof EntityWithPluginCollectionInterface) {
      // Do not manually update values represented by plugin collections.
      $values = array_diff_key($values, $this->entity->getPluginCollections());
    }

    foreach ($values as $key => $value) {
      if ($key == 'created' && !($value instanceof DrupalDateTime)) {
        /** @var CreatedItem $created */
        $created = $entity->get('created')->get(0);
        $value[0]['value'] = $created->getValue()['value'];
      }

      try {
        $entity->set($key, $value);
      } catch (\Exception $e) {
        // Do nothing with values that can not be set
      }
    }
  }
}