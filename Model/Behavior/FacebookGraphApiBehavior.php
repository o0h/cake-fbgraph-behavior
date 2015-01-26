<?php

define('FACEBOOK_SDK_V4_SRC_DIR', CakePlugin::path('FacebookGraphApiBehavior').'Vendor/facebook/php-sdk-v4/src/Facebook/');
require_once CakePlugin::path('FacebookGraphApiBehavior'). 'Vendor/facebook/php-sdk-v4/autoload.php';
require_once CakePlugin::path('FacebookGraphApiBehavior'). 'Config/facebook.php';

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;

class FacebookGraphApiBehavior extends ModelBehavior{

	static public $graphApiVer = null;
	public function setup(Model $model, $config = array()){
		parent::setup($model, $config);
		FacebookSession::setDefaultApplication($config['appId'],  $config['appSecret']);
		self::$graphApiVer = '';
		//@TODO: AppInfoのinitialize, configのロード
	}

	private $__FbSession;
	private $__lastResponse;

	/**
	 * set session.
	 * @param $token (user|page|app) access_token;
	 */
	public function identify($Model, $token){
		$this->__FbSession = new FacebookSession($token);
		return $this->__FbSession->getSessionInfo();
	}

	public function get($Model, $path, $parameters = null, $version = null, $etag = null){
		return $this->api($Model, 'GET', $path, $parameters, $version, $etag);
	}

	public function post($Model, $path, $parameters = null, $version = null, $etag = null){
		return $this->api($Model, 'POST', $path, $parameters, $version, $etag);
	}

	public function delete(){
		// code...
	}

	public function getLastResponse(){
		return clone $this->__lastResponse;
	}

	public function getProperty($Model, $name){
		$obj = $this->getLastResponse();
		if(!$obj instanceOf Facebook\FacebookResponse){
			return false;
		}
		$res = $obj->getGraphObject()->getProperty($name);
		return $res;
	}

	public function api($Model, $method, $path, $parameters = null, $version = null, $etag = null){
		$request = new FacebookRequest(
			$this->__FbSession,
			$method,
			$path,
			$parameters,
			$version ? : self::$graphApiVer,
			$etag);
		$response = $request->execute();;
		$this->__lastResponse = $response;
		return $response->getGraphObject()->asArray();
	}
}
