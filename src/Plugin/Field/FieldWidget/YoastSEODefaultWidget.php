<?php


namespace Drupal\itr_yoast_seo\Plugin\Field\FieldWidget;


use Drupal\Core\Field\Annotation\FieldWidget;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\itr_yoast_seo\Service\YoastSEOService;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
        '#attributes' => ['id' => 'score_circle', 'class' => ['wpseo-score-icon']]
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
      '#attributes' => ['id' => 'seo-status']
    ];

    return $element;
  }
}