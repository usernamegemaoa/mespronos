<?php

/**
 * @file
 * Contains Drupal\mespronos\Tests\MespronosLeagueTest.
 */

namespace Drupal\mespronos\Tests;

use Drupal\mespronos\Entity\RankingDay;
use Drupal\simpletest\WebTestBase;
use Drupal\mespronos\Entity\Sport;
use Drupal\mespronos\Entity\League;
use Drupal\mespronos\Entity\Team;
use Drupal\mespronos\Entity\Day;
use Drupal\mespronos\Entity\Game;
use Drupal\mespronos\Entity\Bet;

/**
 * Provides automated tests for the mespronos module.
 * @group mespronos
 */
class MespronosRankingDayTest extends WebTestBase {
  public $sport;
  public $league;
  public $team1;
  public $team2;
  public $day;
  public $game;
  public $bet;
  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => "MesPronos RankingDay functionality",
      'description' => 'Test Unit for user permissions.',
      'group' => 'MesPronos',
    );
  }

  static public $modules = array(
    'mespronos',
  );

  public function setUp() {
    parent::setUp();
    $this->sport = Sport::create(array(
      'name' => 'Football',
    ));
    $this->sport->save();

    $this->league = League::create(array(
      'sport' => $this->sport->id(),
      'name' => 'test championnat',
      'betting_type' => 'score',
      'classement' => true,
      'status' => 'active',
    ));
    $this->league->save();

    $this->team1 = Team::create(array(
      'name' => 'team1',
    ));
    $this->team1->save();

    $this->team2   = Team::create(array(
      'name' => 'team2',
    ));
    $this->team2->save();

    $this->day = Day::create(array(
      'league' => $this->league->id(),
      'number' => 1,
      'name' => 'day test',
    ));
    $this->day->save();

    $dateO = new \DateTime();
    $date = $dateO->format('Y-m-d\TH:i:s');

    $this->game = Game::create(array(
      'team_1' => $this->team1->id(),
      'team_2' => $this->team2->id(),
      'day' => $this->day->id(),
      'game_date' => $date,
    ));
    $this->game->save();

    $this->bet = Bet::create(array(
      'better' => 1,
      'game' => $this->game->id(),
      'score_team_1' => 1,
      'score_team_2' => 1,
      'points' => 10,
    ));
    $this->game->save();
  }

  public function testCreationRankingDay() {
    $rankingDay = RankingDay::create([
      'better' => 1,
      'day' => $this->day->id(),
      'games_betted' => 5,
      'points' => 10
    ]);
    $this->assertTrue($rankingDay->save(),t('Ranking day saving return true'));
  }

  public function testMethodRemoveRankingDay() {
    $this->assertEqual(0,RankingDay::removeRanking($this->day),t('Remove ranking return 0 when no ranking exit'));
    $rankingDay = RankingDay::create([
      'better' => 1,
      'day' => $this->day->id(),
      'games_betted' => 5,
      'points' => 10
    ]);
    $rankingDay->save();
    $this->assertEqual(1,RankingDay::removeRanking($this->day),t('Remove ranking return 1 when a ranking exit'));
    $this->assertEqual(0,RankingDay::removeRanking($this->day),t('And then 0 after deletion'));
  }

  public function testCreationWithExistingBet() {
    $dataFetched = RankingDay::getData($this->day);
    $this->assertEqual(1,count($dataFetched),t('Data fetched is an array of 1 line'));
    $dataRow = array_pop($dataFetched);
    $this->assertEqual(10,$dataRow['points'],t('Points are right'));
    $this->assertEqual(1,$dataRow['better'],t('better is right'));
    $this->assertEqual(1,$dataRow['nb_bet'],t('bet number is right'));

    //$rankingDay = RankingDay::createRanking($this->day);
    //$this->assertEqual($rankingDay->get('points')->value,10,t('Points are correctly setted'));
  }

}