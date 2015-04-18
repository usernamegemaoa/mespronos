<?php

/**
 * @file
 * Contains Drupal\mespronos\Entity\Form\LeagueForm.
 */

namespace Drupal\mespronos\Entity\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\Language;

/**
 * Form controller for the League entity edit forms.
 *
 * @ingroup mespronos
 */
class LeagueForm extends ContentEntityForm {
  /**
   * Overrides Drupal\Core\Entity\EntityFormController::buildForm().
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\mespronos\Entity\League */
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;$entity->
    $form['langcode'] = array(
      '#title' => t('Language'),
      '#type' => 'language_select',
      '#default_value' => $entity->getUntranslated()->language()->getId(),
      '#languages' => Language::STATE_ALL,
    );

    return $form;
  }

  /**
   * Overrides \Drupal\Core\Entity\EntityFormController::submit().
   */
  public function submit(array $form, FormStateInterface $form_state) {
    // Build the entity object from the submitted values.
    $entity = parent::submit($form, $form_state);

    return $entity;
  }

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::save().
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = $entity->save();

    if ($status) {
      drupal_set_message($this->t('La compétition "%label" a été enregistrée.', array(
        '%label' => $entity->label(),
      )));
    }
    else {
      drupal_set_message($this->t('The %label League was not saved.', array(
        '%label' => $entity->label(),
      )));
    }
    $form_state->setRedirect('entity.league.collection');
  }

}
