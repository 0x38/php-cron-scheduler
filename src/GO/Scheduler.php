<?php namespace GO;

use GO\Job\JobFactory;

use GO\Services\DateTime;

class Scheduler
{

  /**
   * Timezone
   */
  private $timezone = 'Europe/Dublin';

  /**
   * Where to send the output of the job
   */
  private $output = '/dev/null';

  private $jobs = [];

  private $time;


  /**
   * Init the datetime
   *
   */
  public function __construct()
  {
    $this->dt = DateTime::get();
    $this->dt->setTimezone($this->timezone);

    $this->time = time();
  }

  /**
   * Set the timezone
   *
   * @param [string] timezone
   *
   */
  public function setTimezone($timezone)
  {
    $this->dt->setTimezone($timezone);
  }

  /**
   * Set where to send the output
   *
   * @param [string] $output - path file or folder, if a folder is specified,
   *                           in that folder will be created several files,
   *                           one for each scheduled command
   *
   */
  public function setOutput($output)
  {
    $this->output = $output;
  }

  /**
   * PHP job
   *
   * @param [string] $command
   * @param [array] $args
   *
   * @return instance of GO\Job\Job
   *
   */
  public function php($command, array $args = [])
  {
    return $this->jobs[] = JobFactory::factory('GO\Job\Php', $command, $args);
  }

  /**
   * I'm feeling lucky
   * -----------------
   * Guess the job to run by the file extension
   *
   * @param [string] $command
   * @param [array] $args
   *
   * @return instance of GO\Job\Job
   *
   */
  public function command($command, array $args = [])
  {
    $file = basename($command);
  }

  /**
   * Raw job
   *
   * @param [string] $command
   *
   * @return instance of GO\Job\Job
   *
   */
  public function raw($command)
  {
    return $this->jobs[] = JobFactory::factory('GO\Job\Raw', $command);
  }

  /**
   * Closure job
   *
   * @param [closure] $closure
   *
   * @return instance of GO\Job\Job
   *
   */
  public function call($closure)
  {
    return $this->jobs[] = JobFactory::factory('GO\Job\Closure', $command);
  }

  public function run()
  {
    $output = [];

    foreach ($this->jobs as $job) {
      if ($job->isDue()) {
        $output[] = $job->exec();
      }
    }

    return $output;
  }

}
