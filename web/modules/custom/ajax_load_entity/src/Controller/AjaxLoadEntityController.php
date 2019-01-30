<?php

namespace Drupal\ajax_load_entity\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class AjaxLoadEntityController.
 */
class AjaxLoadEntityController extends ControllerBase {

  /**
   * This function will fetch a loaded entity of the requested type in the requested view mode.
   *
   * @param $method
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   * @param \Drupal\Core\Entity\EntityInterface $entity
   * @param \Drupal\Core\Entity\EntityViewModeInterface $view_mode
   *
   * @return mixed $response
   */
  public function getEntity($method, $entity_type, EntityInterface $entity, $view_mode) {

   // echo 'weszlo'; die();
    dump($entity->get('title')->value); die();
    // If nojs is the method we will need to redirect the user.
    $redirect = $method === 'nojs';

    if (!$redirect) {
      // We have javascript so let's grab the entityViewBuilder service
      $view_builder = $this->entityTypeManager()->getViewBuilder($entity_type);

      // Get the render array of this entity in the specified view mode.
      $render = $view_builder->view($entity, $view_mode);

      // To workaround the issue where the ReplaceCommand actually REMOVES the HTML element
      // selected by the selector given to the ReplaceCommand, we need to wrap our content
      // in a div that same ID, otherwise only the first click will work. (Since the ID will
      // no longer exist on the page)
      $build = [
        '#type' => 'container',
        '#attributes' => [
          'id' => 'ajax-load-entity',
        ],
        'entity' => $render,
      ];

      // Now we return an AjaxResponse with the ReplaceCommand to place our entity on the page.
      $response = new AjaxResponse();
      $response->addCommand(new ReplaceCommand('#ajax-load-entity', $build));
    } else {
      // Javascript is not working/disabled so let's just route the person to the canonical
      // route for the entity.
      $response = new RedirectResponse(Url::fromRoute("entity.{$entity_type}.canonical", ["{$entity_type}" => $entity->id()]), 302);
    }

    return $response;
  }

}
