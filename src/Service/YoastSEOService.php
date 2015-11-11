<?php


namespace Drupal\itr_yoast_seo\Service;


use Drupal\Core\Render\Element;
use Drupal\Core\StringTranslation\TranslationManager;
use Drupal\field\Entity\FieldConfig;

class YoastSEOService {
  /**
   * @var TranslationManager
   */
  private $translator;


  public function __construct(TranslationManager $translationManager) {
    $this->translator = $translationManager;
  }

  public function getConfigration($form) {
    // Retrieve Metatag field
    $form_children = Element::children($form);
    $meta = NULL;
    foreach ($form_children as $child_key) {
      if (!empty($form[$child_key]['widget']['#field_name'])) {
        $field_config = FieldConfig::loadByName('node', 'article', $form[$child_key]['widget']['#field_name']);
        if ($field_config && $field_config->getType() == 'metatag') {
          $meta = $form[$child_key];
        }
      }
    }

    $placeholder = [
        'title' => $this->translator->translate('Place click here to alter your page meta title'),
        'description' => $this->translator->translate('Please click here and alter your page meta description'),
        'url' => $this->translator->translate('example-post')
    ];

    $url = (!empty($form['path']['widget'][0]['alias']['#default_value'])) ? $form['path']['widget'][0]['alias']['#default_value'] : $placeholder['url'];
    $url = (!$url && !empty($form['path']['widget'][0]['alias']['#value'])) ? $form['path']['widget'][0]['alias']['#value'] : $url;
    // @todo replace with keyword on entity
    $keyword = (!empty($form['itr_yoast_seo']['keyword']['#default_value'])) ? $form['itr_yoast_seo']['keyword']['#default_value'] : '';
    $page_title = ($meta) ? $meta['widget'][0]['basic']['title']['#default_value'] : $placeholder['title'];
    $page_title = ($page_title) ? $page_title : $placeholder['title'];
    $page_desc = ($meta) ? $meta['widget'][0]['basic']['description']['#default_value'] : $placeholder['description'];
    $page_desc = ($page_desc) ? $page_desc : $placeholder['description'];
    global $base_root;

    $config = [
      'targets' => [
        'output' => 'output',
        'overall' => 'overallScore',
        'snippet' => 'snippet'
      ],
      'default_text' => [
        'url' => $url,
        'keyword' => $keyword,
        'page_title' => $page_title,
        'page_description' => $page_desc,
      ],
      'placeholder_text' => $placeholder,
      'field_ids' => [
        'focus_keyword' => $form['itr_yoast_seo']['keyword']['#attributes']['id'],
        'seo_status' => $form['itr_yoast_seo']['seo_status']['#attributes']['id'],
        'page_title' => ($meta) ? $meta['widget'][0]['basic']['title']['#id'] : '',
        'node_title' => $form['title']['widget'][0]['value']['#id'],
        'description' => ($meta) ? $meta['widget'][0]['basic']['description']['#id'] : '',
        'url' => $form['path']['widget'][0]['alias']['#id'],
      ],
      'base_root' => $base_root
    ];

    return $config;
  }
}