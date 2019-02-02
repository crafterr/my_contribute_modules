<?php

namespace Drupal\school\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeTypeInterface;

/**
 * Class SchoolController.
 */
class SchoolController extends ControllerBase {

  /**
   * Add.
   *
   * @return string
   *   Return Hello string.
   */
  public function add(NodeTypeInterface $node_type) {
    $entityManager = \Drupal::entityTypeManager();
    $node = $entityManager->getStorage('node')->create([
      'type' => $node_type->id(),
    ]);


    $form = $this->entityFormBuilder()->getForm($node);

    return $form;

  }

}
