<?php

/**
 * @file
 * Contains Drupal\mespronos\Controller\DefaultController.
 */

namespace Drupal\mespronos\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class DefaultController.
 *
 * @package Drupal\mespronos\Controller
 */
class BetController extends ControllerBase {
  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function index() {
    return [
        '#type' => 'markup',
        '#markup' => $this->t('Hello World!', [])
    ];
  }

  public function bet($day) {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Hello World!', [])
    ];
  }

}
