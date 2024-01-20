<?php

namespace App\Tests\Controller;

use App\Model\Migration;
use App\Model\User;
use Faker\Factory;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use WouterJ\EloquentBundle\Facade\Db;

class SpiceController extends WebTestCase
{
    protected $_application;

    protected $client;

    protected $faker;

    protected $accessToken;

    public function setUp():void
    {
        $this->faker = Factory::create();
        $this->client = static::createClient();
        $kernel = static::$kernel;
        $this->_application = new Application($kernel);
        $this->_application->setAutoExit(false);

        try{
            if (Db::table('migrations')->exists()) {
                $this->runConsole("eloquent:migrate");
            }
        } catch (QueryException $exception) {
            $this->runConsole("doctrine:schema:update", ["--force"=>null, "--complete"=>null]);
            $this->runConsole("league:oauth2-server:create-client", [
                'name' => "tester",
                'identifier' => 'oAuth2Id',
                'secret' => 'oAuth2Secret',
                "--grant-type" => ["password", "refresh_token"]
            ]);

            $this->runConsole("eloquent:migrate:install");
            $this->runConsole("eloquent:migrate", ["--step" => null]);

            $user = new User();
            $user->login = 'spicer';


            $hashedPassword = $this->getContainer()->get(UserPasswordHasher::class)->hashPassword($user, 'rsm');
            $user->password = $hashedPassword;
            $user->client_id = 'oAuth2Id';

            $user->save();
        }

        $this->client->request('POST', '/token', [
            'grant_type' => 'password',
            'client_id' => 'oAuth2Id',
            'client_secret' => 'oAuth2Secret',
            'scope' => 'spice',
            'username' => 'spicer',
            'password' => 'rsm'
        ]);

        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->accessToken = $responseContent->access_token;

        parent::setUp();
    }

    public function tearDown():void
    {
        $this->runConsole("eloquent:migrate:rollback");
        parent::tearDown();
    }

    protected function runConsole($command, array $options = [])
    {
        $options["--env"] = "test";
        $options["-q"] = null;
        $options = array_merge(array('command' => $command), $options);
        return $this->_application->run(new ArrayInput($options));
    }
}
