<?php

namespace App\Tests\Controller;

use App\Model\Migration;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SpiceController extends WebTestCase
{
    protected $_application;

    protected $client;

    protected $faker;

    public function setUp():void
    {
        $this->faker = Factory::create();
        $this->client = static::createClient();
        $kernel = static::$kernel;
        $this->_application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
        $this->_application->setAutoExit(false);
        $this->runConsole("eloquent:migrate:install");
        $this->runConsole("eloquent:migrate");
    }

    protected function runConsole($command, array $options = [])
    {
        $options["-e"] = "test";
        $options["-q"] = null;
        $options = array_merge($options, array('command' => $command));
        return $this->_application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
    }
}
