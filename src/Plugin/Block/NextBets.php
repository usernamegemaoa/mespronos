<?php

/**
 * @file
 * Contains Drupal\mespronos\Plugin\Block\NextBets.
 */

namespace Drupal\mespronos\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Form\FormStateInterface;
use Drupal\mespronos\Controller\BettingController;
use Drupal\mespronos\Entity\Controller\DayController;
use Drupal\mespronos\Entity\Controller\UserInvolveController;
use Drupal\mespronos\Entity\League;
use Drupal\mespronos\Entity\Day;
use Drupal\mespronos\Entity\Controller\BetController;

/**
 * Provides a 'NextBets' block.
 *
 * @Block(
 *  id = "next_bets",
 *  admin_label = @Translation("next_bets"),
 * )
 */
class NextBets extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['number_of_days_to_display'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Number of days to display'),
      '#description' => $this->t(''),
      '#default_value' => isset($this->configuration['number_of_days_to_display']) ? $this->configuration['number_of_days_to_display'] : 5,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['number_of_days_to_display'] = $form_state->getValue('number_of_days_to_display');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $user_uid =  \Drupal::currentUser()->id();
    $user_involvements = array();
    $days = DayController::getNextDaysToBet($this->configuration['number_of_days_to_display']);
    $rows = [];
    foreach ($days  as $day_id => $day) {
      $league_id = $day->entity->get('league')->first()->getValue()['target_id'];
      if(!isset($leagues[$league_id])) {
        $leagues[$league_id] = League::load($league_id);
      }
      $league = $leagues[$league_id];
      if(!isset($user_involvements[$league_id])) {
        $user_involvements[$league_id] = UserInvolveController::isUserInvolve($user_uid ,$league_id);
      }
      $day->involve = $user_involvements[$league_id];

      $game_date = \DateTime::createFromFormat('Y-m-d\TH:i:s',$day->day_date);
      $now_date = new \DateTime();

      $i = $game_date->diff($now_date);
      $bets_done = BetController::betsDone(\Drupal::currentUser(),$day->entity);

      $action_links = BettingController::getActionBetLink($day->entity,$league,$user_uid,$user_involvements[$league_id]);

      $row = [
        $league->label(),
        $day->entity->label(),
        $day->nb_game,
        $day->nb_game_left,
        $bets_done,

        $i->format('%a') >0 ? $this->t('@d days, @GH@im',array('@d'=>$i->format('%a'),'@G'=>$i->format('%H'),'@i'=>$i->format('%i'))) : $this->t('@GH@im',array('@G'=>$i->format('%H'),'@i'=>$i->format('%i'))),
        $action_links,
      ];
      $rows[] = $row;
    }
    $footer = [
      'data' => array(
        array(
          'data' => Link::fromTextAndUrl(
            $this->t('See all upcoming bets'),
            new Url('mespronos.nextbets')
          ),
          'colspan' => 7
        )
      )
    ];
    $header = [
      $this->t('League',array(),array('context'=>'mespronos')),
      $this->t('Day',array(),array('context'=>'mespronos')),
      $this->t('Games',array(),array('context'=>'mespronos')),
      $this->t('Games to play',array(),array('context'=>'mespronos')),
      $this->t('Bets done',array(),array('context'=>'mespronos')),
      $this->t('Time left',array(),array('context'=>'mespronos')),
      '',
    ];
    return [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
      '#footer' => $footer,
    ];

  }

}
