<?php


namespace Drupal\itr_yoast_seo\Form;


use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\itr_yoast_seo\Service\YoastSEOService;
use Drupal\node\NodeForm as BaseNodeForm;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NodeForm extends BaseNodeForm {

  /**
   * @var YoastSEOService
   */
  private $yoastService;


  /**
   * @inheritdoc
   */
  public function __construct(EntityManagerInterface $entity_manager, PrivateTempStoreFactory $temp_store_factory, YoastSEOService $yoast_service) {
    parent::__construct($entity_manager, $temp_store_factory);
    $this->yoastService = $yoast_service;
  }

  /**
   * @inheritdoc
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('user.private_tempstore'),
      $container->get('yoast_seo_service')
    );
  }

  /**
   * @inheritdoc
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    // Global fieldset
    $form['itr_yoast_seo'] = [
      '#type' => 'fieldset',
      '#title' => t('ITR Yoast SEO'),
      '#weight' => 20
    ];

    // Keyword
    $form['itr_yoast_seo']['keyword'] = [
      '#type' => 'textfield',
      '#title' => t('Focus keyword'),
      '#attributes' => ['id' => 'focus_keyword']
    ];
    // Score
    $form['itr_yoast_seo']['overall'] = [
        '#type' => 'container',
        '#attributes' => ['id' => 'overallScore']
    ];
    $form['itr_yoast_seo']['overall']['title'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['score_title']]
    ];
    $form['itr_yoast_seo']['overall']['circle'] = [
        '#type' => 'container',
        '#attributes' => ['id' => 'score_circle', 'class' => ['wpseo-score-icon']]
    ];

    // Snippet editor
    $form['itr_yoast_seo']['snippet'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Snippet editor'),
      '#attributes' => ['id' => 'snippet']
    ];

    // Content analysis
    $form['itr_yoast_seo']['output_wrapper'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Content analysis')
    ];
    $form['itr_yoast_seo']['output_wrapper']['output'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'output']
    ];

    $form['itr_yoast_seo']['seo_status'] = [
      '#type' => 'hidden',
      '#attributes' => ['id' => 'seo-status']
    ];

    $form['#after_build'][] = '::afterBuild';

    return $form;
  }

  public function afterBuild(array $element, FormStateInterface $form_state) {
    parent::afterBuild($element, $form_state);

    $element['#attached']['drupalSettings']['itr_yoast_seo'] = $this->yoastService->getConfigration($element);
    $element['#attached']['library'][] = 'itr_yoast_seo/yoast_seo';
    $element['#attached']['library'][] = 'itr_yoast_seo/init';

    return $element;
  }
}