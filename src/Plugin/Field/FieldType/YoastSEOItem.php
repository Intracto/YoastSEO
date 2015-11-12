<?php


namespace Drupal\itr_yoast_seo\Plugin\Field\FieldType;


use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Class YoastSEOItem
 * Plugin implementation of the 'yoast_seo' field type
 * @package Drupal\itr_yoast\Plugin\Field\FieldType
 *
 * @FieldType(
 *   id = "yoast_seo",
 *   label = "Yoast SEO",
 *   description = @Translation("Stores the keyword and the status of the Yoast SEO result"),
 *   category = @Translation("Custom"),
 *   no_ui = TRUE
 * )
 */
class YoastSEOItem extends FieldItemBase {

  /**
   * @inheritdoc
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];
    $properties['focus_keyword'] = DataDefinition::create('string')
      ->setLabel(t('Focus keyword'));
    $properties['status'] = DataDefinition::create('integer')
      ->setLabel(t('Status'));

    return $properties;
  }

  /**
   * @inheritdoc
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $scheme = [];
    $scheme['columns'] = [
      'focus_keyword' => [
        'type' => 'varchar',
        'description' => 'The keyword used to determine the score',
        'length' => 512,
        'not null' => FALSE
      ],
      'status' => [
        'type' => 'int',
        'description' => 'The Yoast SEO score',
        'unsigned' => TRUE
      ]
    ];

    return $scheme;
  }
}