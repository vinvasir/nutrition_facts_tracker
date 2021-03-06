<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use MaddHatter\LaravelFullcalendar\Event as FCEvent;

class Day extends Model implements FCEvent
{
  protected $fillable = ['user_id', 'date', 'weight', 'good_food_count', 'bad_food_count'];

  protected $with = ['foods'];

  protected $carbonizedDate = null;

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function foods()
  {
    return $this->hasMany(Food::class);
  }

  public function setProgress()
  {
    $food_progress = ($this->good_food_count - $this->bad_food_count) / 10;
        
    $this->food_goal_progress = $food_progress;

    $this->save();
  }

  public function addFood($requestParams)
  {
    return $this->foods()->create($requestParams);
  }


  public function path()
  {
    return "/app/days/" . $this->id;
  }

  public function getTitle()
  {
    $dateLine = $this->getCarbonizedDate()->toFormattedDateString();
    $progressLine = ($this->food_goal_progress * 100) . "% of diet goal";
    $weightLine = $this->weight . " lb";
    return $dateLine . "\n" . $progressLine . "\n" . $weightLine;
  }

   public function isAllDay() 
  {
     return true;
  }

  public function getStart()
  {
    return $this->getCarbonizedDate();
  }

  public function getEnd()
  {
    // use a different Carbon instance for this method
    // because addDay() mutates the Carbon instance
    // in-place
    $c = new Carbon($this->date);
    return $c->addDay()->subSecond();
  }

  /**
     * Optional FullCalendar.io settings for this event
     *
     * @return array
     */
  public function getEventOptions()
  {
      return [
          'url' => $this->path(),
    //etc
      ];
  }

  protected function getCarbonizedDate()
  {
    if (!$this->carbonizedDate && $this->date) {
      $this->carbonizedDate = new Carbon($this->date);
    }

    return $this->carbonizedDate;
  } 
}
