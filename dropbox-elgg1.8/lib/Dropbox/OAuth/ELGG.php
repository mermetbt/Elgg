<?php

/**
 * Dropbox OAuth ELGG adapter.
 *
 * This class allows the Dropbox API to be connected with the OAuth
 * plugin from Elgg.
 *
 * @package Dropbox
 * @copyright Copyright (C) 2011 Timothé Mermet-Buffet.
 * @author Timothé Mermet-Buffet
 * 
 */

include('OAuth.php');


/**
 * This class is used to sign all requests to dropbox.
 *
 * This specific class uses the Elgg OAuth plugin.
 * This package need the oauth plugin for Elgg.
 */
class Dropbox_OAuth_ELGG extends Dropbox_OAuth {

	/**
	 * OAuth object (will be removed in the future)
	 *
	 * @var OAuth
	 */
	protected $OAuth;
	/**
	 * Request object.
	 *
	 * @var OAuthRequest request
	 */
	protected $request;
	/**
	 * Consumer object.
	 *
	 * @var OAuthConsumer consumer
	 */
	protected $consumer;

	/**
	 * Constructor
	 *
	 * @param string $consumerKey
	 * @param string $consumerSecret
	 */
	public function __construct($consumerKey, $consumerSecret) {

		/* Check if the class OAuthConsumer (contained in oauth Elgg plugin) is available. */
		if (!class_exists('DropBox_OAuthConsumer'))
			throw new Dropbox_Exception('The OAuthConsumer class could not be found! Did you install and enable the oauth extension?');

		/* The consumer contains the key and secret values. */
		$this->consumer = new DropBox_OAuthConsumer($consumerKey, $consumerSecret);
	}

	/**
	 * Sets the request token and secret.
	 *
	 * The tokens can also be passed as an array into the first argument.
	 * The array must have the elements token and token_secret.
	 *
	 * @param string|array $token
	 * @param string $token_secret
	 * @return void
	 */
	public function setToken($token, $token_secret = null) {

		parent::setToken($token, $token_secret);
		//$this->request->setToken($this->oauth_token, $this->oauth_token_secret);
	}

	/**
	 * $url: the URL to get
	 * $args: array of other optional arguments as follows:
	 *    username: username for HTTP auth
	 *    password: password for HTTP auth
	 *    post: boolean, is this a post? (defaults to False)
	 *    data: HTTP body for POST
	 *    headers: HTTP headers
	 */
	function url_getter_getUrl($url, $args = array()) {

		global $CONFIG;

		$userAgent = '(Elgg ' . $CONFIG->release . ')';
		
		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_HTTPHEADER, "Host: api.dropbox.com");
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_VERBOSE, true);

		if (!empty($args['username']) && !empty($args['password'])) {
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($ch, CURLOPT_USERPWD, $args['username'] . ':' . $args['password']);
		}

		if (!empty($args['post'])) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $args['data']);
		}

		if (!empty($args['headers'])) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $args['headers']);
		}

		$html = curl_exec($ch);

		$rc = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$info = curl_getinfo($ch);
		curl_close($ch);

		return array('rc' => $rc, 'html' => $html);
	}

	/**
	 * Fetches a secured oauth url and returns the response body.
	 *
	 * @param string $uri
	 * @param mixed $arguments
	 * @param string $method
	 * @param array $httpHeaders
	 * @return string
	 */
	public function fetch($uri, $arguments = array(), $method = 'GET', $httpHeaders = array()) {
		
		/* Creating the signature generator */
		$sign = new DropBox_OAuthSignatureMethod_HMAC_SHA1();

		/* Get the token */
		$token = $this->getToken();

		$args = array();

		/* If the token isn't empty, we use it only. */
		if (!empty($token['token']) && !empty($token['token_secret'])) {
			$args = array_merge($args, array('oauth_token' => $token['token']));
			$tokensign = new DropBox_OAuthToken($token['token'], $token['token_secret']);
		} else {
			$tokensign = NULL;
		}

		if(!$arguments['post']) {
			$args = array_merge($args, $arguments);
		}

		/* Generate the HTTP request */
		$request = DropBox_OAuthRequest::from_consumer_and_token($this->consumer,
						NULL,
						$method,
						$uri, $args);

		/* Generate the signature of the request. */
		$request->sign_request($sign, $this->consumer, $tokensign);

		/* Convert the request into an URL. */
		$reqUrl = $request->to_url();

		$arguments['headers'] = $httpHeaders;

		/* Execute the request. */
		$out = $this->url_getter_getUrl($reqUrl, $arguments);

		/* Handle the different HTTP codes. */
		switch ($out['rc']) {
			/* All is OK */
			case '200':
			case '304':
				return array(
					'httpStatus' => $out['rc'],
					'body' => $out['html']);

			case '400':
				throw new Dropbox_Exception_Forbidden('Operation attempted not allowed by token type. Root parameter is not full access or Sandbox.');
			/* Forbidden */
			case '401':
				throw new Dropbox_Exception_Forbidden('Forbidden. Username or password incorrect.');
			case '403':
				throw new Dropbox_Exception_Forbidden('Forbidden. This could mean a bad OAuth request, or a file or folder already existing at the target location.');
			/* Not Found */
			case '404' :
				throw new Dropbox_Exception_NotFound('Resource at uri: ' . $uri . ' could not be found');
			/* Full */
			case '507' :
				throw new Dropbox_Exception_OverQuota('This dropbox is full');
			default:
				throw new Dropbox_Exception('Unknow : ' . $out['rc']);
		}
	}

	/**
	 * Requests the OAuth request token.
	 *
	 * @return void
	 */
	public function getRequestToken() {

		try {

			$tokens = $this->OAuth->getRequestToken(self::URI_REQUEST_TOKEN);
			$this->setToken($tokens['oauth_token'], $tokens['oauth_token_secret']);
			return $this->getToken();
		} catch (DropBox_OAuthException $e) {

			throw new Dropbox_Exception_RequestToken('We were unable to fetch request tokens. This likely means that your consumer key and/or secret are incorrect.', 0, $e);
		}
	}

	/**
	 * Requests the OAuth access tokens.
	 *
	 * This method requires the 'unauthorized' request tokens
	 * and, if successful will set the authorized request tokens.
	 *
	 * @return void
	 */
	public function getAccessToken() {

		$uri = self::URI_ACCESS_TOKEN;
		$tokens = $this->OAuth->getAccessToken($uri);
		$this->setToken($tokens['oauth_token'], $tokens['oauth_token_secret']);
		return $this->getToken();
	}

}
