<?php

use Slim\Http\Environment;
use Slim\Http\Request;
use PHPUnit\Framework\TestCase;

class OrganizationTest extends TestCase
{
    protected $app;
    public function setUp()
    {
        $settings = require  __DIR__ . '/../../config/config.php';
        $app = new \Slim\App($settings);
        require __DIR__ . '/../../config/dependencies.php';
        require __DIR__ . '/../../config/middleware.php';
        require __DIR__ . '/../../config/routes.php';
        $this->app = $app;
    }

    public function testPost()
    {
        $body = json_decode('
               {
               "org_name":"Paradise Island",
               "daughters":[
                  {
                      "org_name":"Banana tree",
                     "daughters":[
                        {
                            "org_name":"Yellow Banana"
                        },
                        {
                            "org_name":"Brown Banana"
                        },
                        {
                            "org_name":"Black Banana"
                        }
                     ]
                  },
                  {
                      "org_name":"Big banana tree",
                     "daughters":[
                        {
                            "org_name":"Yellow Banana"
                        },
                        {
                            "org_name":"Brown Banana"
                        },
                        {
                            "org_name":"Green Banana"
                        },
                        {
                            "org_name":"Black Banana",
                           "daughters":[
                              {
                                  "org_name":"Phoneutria Spider"
                              }
                           ]
                        }
                     ]
                  }
               ]
            }'
            , true);
        $env = Environment::mock([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/api/v1/organizations',
            'CONTENT_TYPE' => 'application/javascript',
            'SERVER_PORT' => 8080,
        ]);
        $req = Request::createFromEnvironment($env)->withParsedBody($body);
        $this->app->getContainer()['request'] = $req;
        $response = $this->app->run(true);
        $this->assertSame(201, $response->getStatusCode());
    }

    public function testGet()
    {
        $expected = json_decode('
            [
                {
                    "org_name": "Banana tree",
                    "relation_type": "parent"
                },
                {
                    "org_name": "Big banana tree",
                    "relation_type": "parent"
                },
                {
                    "org_name": "Brown Banana",
                    "relation_type": "sister"
                },
                {
                    "org_name": "Green Banana",
                    "relation_type": "sister"
                },
                {
                    "org_name": "Phoneutria Spider",
                    "relation_type": "daughter"
                },
                {
                    "org_name": "Yellow Banana",
                    "relation_type": "sister"
                }
            ]'
            , true);
        $env = Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/api/v1/organizations/Black Banana',
            'SERVER_PORT' => 8080,
        ]);
        $req = Request::createFromEnvironment($env);
        $this->app->getContainer()['request'] = $req;
        $response = $this->app->run(true);
        $this->assertSame( 200, $response->getStatusCode());
        $result = json_decode($response->getBody(), true);
        $this->assertSame($result, $expected);
    }

    public function testGetWrongMethod()
    {
        $env = Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/api/v1/organizations/',
            'SERVER_PORT' => 8080,
        ]);
        $req = Request::createFromEnvironment($env);
        $this->app->getContainer()['request'] = $req;
        $response = $this->app->run(true);
        $this->assertSame( 405, $response->getStatusCode());
    }

    public function testGetNonExistingEntry()
    {
        $env = Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/api/v1/organizations/NonExisting',
            'SERVER_PORT' => 8080,
        ]);
        $req = Request::createFromEnvironment($env);
        $this->app->getContainer()['request'] = $req;
        $response = $this->app->run(true);
        $this->assertSame( 204, $response->getStatusCode());
    }
}
