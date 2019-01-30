<?php

namespace Drupal\create_node_block;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Class ArticleService.
 */
class ArticleService implements ArticleServiceInterface {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;


  /**
   * @var Array
   */
  protected $collection;

  /**
   * Constructs a new ArticleService object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * @return \Drupal\Core\Entity\EntityInterface[]|\Drupal\create_node_block\Array
   */
  public function getList() {
    $collection = $this->entityTypeManager->getStorage('node')->loadByProperties(['type' => 'article','status'=>1]);
    if (!isset($this->collection)) {
      $this->collection = $collection;
    }
    return $this->collection;
  }

  /**
   * @return array
   */
  public function getOptions() {
    $list = [];
    foreach ($this->getList() as $item) {
      $list[$item->id()] = $item->get('title')->value;
    }
    return $list;
  }

  /**
   * @param $id
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   */
  public function getArticle($id) {
    $node = $this->entityTypeManager->getStorage('node')->load($id);
    return $node;
  }

}
