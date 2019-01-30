<?php

namespace Drupal\create_node_block;

/**
 * Interface ArticleServiceInterface.
 */
interface ArticleServiceInterface {
  public function getList();

  public function getOptions();

  public function getArticle($id);

}
