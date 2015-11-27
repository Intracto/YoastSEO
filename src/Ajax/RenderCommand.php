<?php


namespace Drupal\itr_yoast_seo\Ajax;

use Drupal\Core\Ajax\CommandInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\itr_yoast_seo\Service\EntityRenderService;

class RenderCommand implements CommandInterface {

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


  public function __construct($selector, $key, FormStateInterface $form_state) {
    $this->form_state = $form_state;
    $this->selector = $selector;
    $this->key = $key;

    $this->renderService = \Drupal::getContainer()->get('entity.renderer');
  }

  /**
   * Return an array to be run through json_encode and sent to the client.
   */
  public function render() {
    $node = $this->form_state->getFormObject()->getEntity();

    return [
      'command' => 'renderEntity',
      'selector' => $this->selector,
      'key' => $this->key,
      'content' => $this->renderService->previewEntity($node)
    ];
  }
}