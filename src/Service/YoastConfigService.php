<?php


namespace Drupal\yoast_seo\Service;


use Drupal\Core\Render\Element;
use Drupal\Core\Routing\AccessAwareRouterInterface;
use Drupal\Core\StringTranslation\TranslationManager;
use Drupal\field\Entity\FieldConfig;
use Drupal\node\Entity\Node;

class YoastConfigService {
  /**
   * @var TranslationManager
   */
  private $translator;

  /**
   * @var AccessAwareRouterInterface
   */
  private $router;


  /**
   * Constructor
   *
   * @param \Drupal\Core\StringTranslation\TranslationManager $translationManager
   * @param \Drupal\Core\Routing\AccessAwareRouterInterface $router
   */
  public function __construct(TranslationManager $translationManager, AccessAwareRouterInterface $router) {
    $this->translator = $translationManager;
    $this->router = $router;
  }

  /**
   * Prepare the necessary configuration for the Yoast SEO plugin
   *
   * @param $form
   * @param \Drupal\node\Entity\Node $node
   *
   * @return array
   */
  public function getConfigration($form, Node $node) {
    // Retrieve Metatag field
    $form_children = Element::children($form);
    $meta = NULL;
    foreach ($form_children as $child_key) {
      if (!empty($form[$child_key]['widget']['#field_name'])) {
        $field_config = FieldConfig::loadByName('node', $node->bundle(), $form[$child_key]['widget']['#field_name']);
        if ($field_config && $field_config->getType() == 'metatag') {
          $meta = $form[$child_key];
        }
      }
    }

    $placeholder = [
        'title' => $this->translator->translate('Please click here to alter your page meta title'),
        'description' => $this->translator->translate('Please click here and alter your page meta description'),
        'url' => '/' . $this->translator->translate('example-post')
    ];

    $url = (!empty($form['path']['widget'][0]['alias']['#default_value'])) ? $form['path']['widget'][0]['alias']['#default_value'] : $placeholder['url'];
    $url = (!$url && !empty($form['path']['widget'][0]['alias']['#value'])) ? $form['path']['widget'][0]['alias']['#value'] : $url;
    $keyword = $node->field_node_yoast_seo->get(0)->get('focus_keyword')->getValue();
    $page_title = ($meta) ? $meta['widget'][0]['basic']['title']['#default_value'] : $placeholder['title'];
    $page_title = ($page_title) ? $page_title : $placeholder['title'];
    $page_desc = ($meta) ? $meta['widget'][0]['basic']['description']['#default_value'] : $placeholder['description'];
    $page_desc = ($page_desc) ? $page_desc : $placeholder['description'];

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
        'focus_keyword' => 'focus_keyword',
        'seo_status' => 'seo-status',
        'seo_content' => 'seo-content',
        'page_title' => ($meta) ? $meta['widget'][0]['basic']['title']['#id'] : '',
        'node_title' => $form['title']['widget'][0]['value']['#id'],
        'description' => ($meta) ? $meta['widget'][0]['basic']['description']['#id'] : '',
        'url' => $form['path']['widget'][0]['alias']['#id'],
      ],
      'base_root' => $this->getBaseRoot(),
    ];

    return $config;
  }

  private function getBaseRoot() {
    global $base_root;

    return $base_root;
  }
}