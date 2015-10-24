<?php


namespace Drupal\itr_yoast\Plugin\Field\FieldType;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Class YoastSEO
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
class YoastSEO extends FieldItemBase {
  /**
   * Defines field item properties.
   *
   * Properties that are required to constitute a valid, non-empty item should
   * be denoted with \Drupal\Core\TypedData\DataDefinition::setRequired().
   *
   * @return \Drupal\Core\TypedData\DataDefinitionInterface[]
   *   An array of property definitions of contained properties, keyed by
   *   property name.
   *
   * @see \Drupal\Core\Field\BaseFieldDefinition
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
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
    return [
      'columns' => [
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
      ]
    ];
  }
}