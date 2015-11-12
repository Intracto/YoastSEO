<?php


namespace Drupal\itr_yoast_seo\Plugin\Field\FieldWidget;


use Drupal\Core\Field\Annotation\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

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
    $element['focus_keyword'] = $element + [
        '#type' => 'textfield',
        '#default_value' => $items[$delta]->value
      ];

    return $element;
  }
}