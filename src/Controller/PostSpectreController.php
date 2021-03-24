<?php

namespace Drupal\post_spectre\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for post_spectre routes.
 */
class PostSpectreController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
