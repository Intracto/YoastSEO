<?php


namespace Drupal\itr_yoast_seo\Plugin\Field\FieldWidget;


use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Field\Annotation\FieldWidget;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\itr_yoast_seo\Ajax\RenderCommand;

/**
 * Class YoastSEODefaultWidget
 * Plugin implementation of the 'yoast_seo_default' widget
 * @package Drupal\itr_yoast_seo\Field\FieldWidget
 *
 * @FieldWidget(
 *   id = "yoast_seo_default",
 *   label = @Translation("Yoast SEO keyword"),
 *   field_types = {
 *     "yoast_seo"
 * }
 * )
 */
class YoastSEODefaultWidget extends WidgetBase {

  /**
   * @inheritdoc
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    // Global fieldset
    $element['#type'] = 'fieldset';

    // Focus keyword
    $element['focus_keyword'] = [
      '#type' => 'textfield',
      '#default_value' => $items[$delta]->focus_keyword,
      '#title' => $this->t('Focus keyword'),
      '#attributes' => ['id' => 'focus_keyword']
    ];

    // Allow a hidden html element to set the preview of the node
    $element['content'] = [
      '#type' => 'hidden',
      '#attributes' => ['id' => 'seo-content'],
      '#ajax' => [
        'callback' => [$this, 'renderPreview'],
        'selector' => '#seo-content',
        'event' => 'seo-content-refresh',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Calculating score...')
        ]
      ]
    ];

    // Score
    $element['overall'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'overallScore'],
      'title' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['score_title']]
      ],
      'circle' => [
        '#type' => 'container',
        '#attributes' => [
          'id' => 'score_circle',
          'class' => ['wpseo-score-icon']
        ]
      ]
    ];

    // Snippet editor
    $element['snippet'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Snippet editor'),
      '#attributes' => ['id' => 'snippet']
    ];

    // Content analysis
    $element['analysis_wrapper'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Content analysis'),
      'analysis' => [
        '#type' => 'container',
        '#attributes' => ['id' => 'output']
      ]
    ];

    // Status
    $element['status'] = [
      '#type' => 'hidden',
      '#default_value' => $items[$delta]->status,
      '#attributes' => ['id' => 'seo-status'],
    ];

    $element['preview_wrapper'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Preview'),
      'preview' => [
        '#type' => 'container',
        '#attributes' => ['id' => 'preview--wrapper']
      ]
    ];

    $form['#attached']['library'][] = 'itr_yoast_seo/commands';

    return $element;
  }

  /**
   * Ajax callback that will render the node
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function renderPreview(array &$form, FormStateInterface $form_state) {
    $ajax_response = new AjaxResponse();

    $render_command = new RenderCommand('#seo-content', 'seo-content', $form_state);
    $ajax_response->addCommand($render_command);

    return $ajax_response;
  }
}