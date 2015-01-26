<?php
App::uses('Facebook', 'FacebookGraphApiBehavior.Model');
App::uses('Model', 'FacebookGraphApiBehavior.FacebookGraphApiBehaviorAppModel');
App::uses('Behavior', 'FacebookGraphApiBehavior.FacebookGraphApiBehaviorAppModel');
require_once( CakePlugin::path('FacebookGraphApiBehavior'). 'Config/FacebookGraphApiBehaviorTestCredentials.php');

/**
 * Facebook Test Case
 *
 */
class FacebookGraphApiBehaviorTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		//'plugin.facebook_graph_api_behavior.facebook'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->appConfig = new FacebookGraphApiBehaviorTestCredentials();
		$this->Model = ClassRegistry::init('FacebookGraphApiBehavior.FacebookGraphApiBehaviorAppModel');
		$this->Model->Behaviors->load('FacebookGraphApiBehavior.FacebookGraphApi', array(
					'appId' => $this->appConfig->appId,
					'appSecret' => $this->appConfig->appSecret,
					));
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Facebook);
		parent::tearDown();
	}

	/**
	 * @see /path/to/this-plugin/Vendor/facebook/php-sdk-v4/tests/FacebookSessionTest.php
	 */
	public function testIdentify(){
		$response =$this->Model->identify($this->appConfig->userToken);
		$this->assertTrue($response instanceof Facebook\GraphSessionInfo);
		$this->assertNotNull($response->getAppId());
		$this->assertEquals($this->appConfig->appName, $response->getApplication());
		return $this->Model;
	}

	/**
	 * testing 'get' request.
	 * @depends testIdentify
	 */
	public function testGetMe($Model){
		$expect = ['name' => $this->appConfig->userName, 'id' => $this->appConfig->userId];
		$actual  = $Model->get('/me', array('fields' => 'name'));
		$this->assertEquals($expect, $actual);
	}
	
	/**
	 * testing get specify field value, after request.
	 * @depends testIdentify
	 */
	public function testGetProperty($Model){
		$Model->get('/facebook');
		$actual = $Model->getProperty('username');
		$this->assertEquals('facebook', $actual);
	}

	/**
	 * @depends testIdentify
	 */
	public function testPost($Model){
		$content = ['message' => 'ナイス ショット@'.date('Y-m-d H:i:s'), 'privacy' => json_encode(['value' => 'SELF']) ];
		$Model->post('/me/feed', $content);
		$this->assertNotNull($Model->getProperty('id'));
	}

}
