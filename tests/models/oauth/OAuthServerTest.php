<?php
require_once "PHPFrame.php";
require_once preg_replace("/(.*tests\/).+/", "$1TestCases.php", __FILE__);

class OAuthServerTest extends MVCTestCase
{
    private $_oauth_clients_mapper, $_oauth_client;

    public function setUp()
    {
        $this->_oauth_clients_mapper = new OAuthClientsMapper($this->app()->db());

        // Create test client
        $this->_oauth_client = new OAuthClient();
        $this->_oauth_client->name("Mashine Test Client");
        $this->_oauth_client->version("1.0");
        $this->_oauth_client->vendor("Mashine Project");

        $this->_oauth_clients_mapper->insert($this->_oauth_client);

        $this->fixture(new OAuthServer(
            $this->_oauth_clients_mapper,
            "mashine/api/oauth/request_token"
        ));
    }

    public function tearDown()
    {
        $this->_oauth_clients_mapper->delete($this->_oauth_client);
    }

    public function test_handleConsumer()
    {
        $provider = new StdClass();
        $provider->consumer_key = $this->_oauth_client->key();

        $this->assertEquals(
            OAUTH_OK,
            $this->fixture()->handleConsumer($provider)
        );
    }

    public function test_handleConsumerKeyUnknown()
    {
        $provider = new StdClass();
        $provider->consumer_key = "";

        $this->assertEquals(
            OAUTH_CONSUMER_KEY_UNKNOWN,
            $this->fixture()->handleConsumer($provider)
        );
    }

    public function test_handleConsumerKeyRefusedThrottled()
    {
        $this->_oauth_client->status("throttled");
        $this->_oauth_clients_mapper->insert($this->_oauth_client);

        $provider = new StdClass();
        $provider->consumer_key = $this->_oauth_client->key();

        $this->assertEquals(
            OAUTH_CONSUMER_KEY_REFUSED,
            $this->fixture()->handleConsumer($provider)
        );
    }

    public function test_handleConsumerKeyRefusedBlacklisted()
    {
        $this->_oauth_client->status("blacklisted");
        $this->_oauth_clients_mapper->insert($this->_oauth_client);

        $provider = new StdClass();
        $provider->consumer_key = $this->_oauth_client->key();

        $this->assertEquals(
            OAUTH_CONSUMER_KEY_REFUSED,
            $this->fixture()->handleConsumer($provider)
        );
    }
}
