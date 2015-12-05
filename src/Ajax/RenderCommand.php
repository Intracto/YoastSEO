<?php


namespace Drupal\itr_yoast_seo\Ajax;

use Drupal\Core\Ajax\CommandInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\itr_yoast_seo\Service\EntityRenderService;

class RenderCommand implements CommandInterface {

  /**
   * @var array
   */
  private $form;

  /**
   * @var FormStateInterface
   */
  private $form_state;

  /**
   * @var string
   */
  private $selector;

  /**
   * @var string
   */
  private $key;

  /**
   * @var EntityRenderService
   */
  private $renderService;

  /**
   * @var EntityInterface
   */
  private $entity;


  public function __construct($selector, $key, $form, FormStateInterface $form_state) {
    $this->form = $form;
    $this->form_state = $form_state;
    $this->entity = $form_state->getFormObject()->getEntity();
    $this->selector = $selector;
    $this->key = $key;

    $this->renderService = \Drupal::getContainer()->get('entity.renderer');
  }

  /**
   * Return an array to be run through json_encode and sent to the client.
   */
  public function render() {
    try {
      $content = $this->renderService->previewEntity($this->entity, $this->form, $this->form_state);
    } catch (\Exception $e) {
      $content = '';
    }

    return [
      'command' => 'renderEntity',
      'selector' => $this->selector,
      'key' => $this->key,
      'content' => $content
    ];
  }
}