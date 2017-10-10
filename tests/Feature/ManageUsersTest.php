<?php

namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageUsersTest extends TestCase
{
  use DatabaseTransactions;

  public function setUp()
  {
    parent::setUp();
    $this->settings = ['privacy' => [
        'public' => false,
        'showWeightTo' => ['friends', 'followedUsers'],
        'showFoodProgressTo' => ['followers']
      ],
      'appTheme' => [
        'backgroundColor' => 'dark'
      ]
    ];
  }

  /** @test */
  function an_authenticated_user_may_update_their_settings()
  {
  	$this->signIn();

  	$this->post('/app/users/my-settings', ['settings' => $this->settings]);
  				
		$this->assertEquals($this->settings, auth()->user()->fresh()->settings);
  }

  /** @test */
  function regular_users_may_not_update_other_users_settings()
  {
  	$this->signIn();

  	$otherUser = create('App\User');


  	$this->post('/app/users/my-settings', ['settings' => $this->settings]);

  	$this->assertNotEquals($this->settings, $otherUser->fresh()->settings);  	
  }

  /** @test */
  function guests_may_not_update_other_users_settings()
  {
  	$otherUser = create('App\User');

  	$this->withExceptionHandling()
  			->post('/app/users/my-settings', ['settings' => $this->settings])
  			->assertRedirect('/login');

  	$this->assertNotEquals($this->settings, $otherUser->fresh()->settings);  	
  }

  /** @test */
  function an_authenticated_user_may_only_update_privacy_and_theme_settings()
  {
    $this->signIn();

    $settings =  array_merge(
      $this->settings,
      ['permissions' => [
          'admin' => true
        ]
      ] 
    );

    $this->post('/app/users/my-settings', ['settings' => $settings]);

    $this->assertNotEquals($settings, auth()->user()->fresh()->settings);

    $expectedUserSettings = $this->settings;

    $this->assertEquals($expectedUserSettings, auth()->user()->fresh()->settings);
  }

  /** @test */
  function an_authenticated_user_does_not_lose_settings_that_they_forget_to_specify()
  {
    $this->signIn();

    auth()->user()->settings = $this->settings;
    auth()->user()->save();

    $changedSettings = [
      'privacy' => ['showFoodProgressTo' => 'followedUsers']
    ];

    $this->post('/app/users/my-settings', ['settings' => $changedSettings]);

    $expectedSettings =  $this->settings;
    $expectedSettings['privacy']['showFoodProgressTo'] = ['followers', 'followedUsers'];

    $this->assertEquals($expectedSettings, auth()->user()->fresh()->settings);
  }  
}    