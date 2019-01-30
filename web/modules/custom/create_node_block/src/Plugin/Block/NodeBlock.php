<?php

namespace Drupal\create_node_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\create_node_block\ArticleServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountInterface;
/**
 * Provides a 'NodeBlock' block.
 *
 * @Block(
 *  id = "node_block",
 *  admin_label = @Translation("Node block"),
 * )
 */
class NodeBlock extends BlockBase implements ContainerFactoryPluginInterface {


  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;
  /**
   * Constructs a new NodeBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   */

  /**
   * @var \Drupal\create_node_block\ArticleServiceInterface
   */
  protected $articleService;


  /**
   * NodeBlock constructor.
   *
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   * @param \Drupal\create_node_block\ArticleServiceInterface $articleService
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
	  ConfigFactoryInterface $config_factory,
    ArticleServiceInterface $articleService

  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
    $this->articleService = $articleService;
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('article.service')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#description' => $this->t('Title of node'),
      '#default_value' => $this->configuration['title'],
      '#maxlength' => 255,
      '#size' => 64,
    ];


    $form['article'] = [
      '#type' => 'select',
      '#title' => t('Select Article'),
      '#options' => $this->articleService->getOptions(),
      '#default_value' => $this->configuration['article'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['title'] = $form_state->getValue('title');
    $this->configuration['article'] = $form_state->getValue('article');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $user = \Drupal::currentUser();
    $user_roles = $user->getRoles();
    $roles_permissions = user_role_permissions(['authenticated']);
    //dump($roles_permissions); die();
    $article_id = (int)$this->configuration['article'];

    $build = [
      '#title' => $this->configuration['title'],
      '#node' => $this->articleService->getArticle($article_id),
      '#theme' => 'create_node_block',
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

}
