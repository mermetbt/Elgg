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
	protected $sha1_method;

	/**
	 * Constructor
	 *
	 * @param string $consumerKey
	 * @param string $consumerSecret
	 */
	public function __construct($consumerKey, $consumerSecret) {

		$this->sha1_method = new DropBox_OAuthSignatureMethod_HMAC_SHA1();

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
	function http($url, $args = array()) {

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
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		
		/**
		 * This is needed to remove problems with certificate 
		 * authentication against the dropbox server. 
		 */
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
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
		$err = curl_error($ch);
		$errno = curl_errno($ch);

		curl_close($ch);
		
		return array('rc' => $rc, 
			     'html' => $html, 
			     'errno' => $errno,
			     'err' => $err,
			     'info' => $info);
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

		if (!$arguments['post']) {
			$args = array_merge($args, $arguments);
		}

		/* Generate the HTTP request */
		$request = DropBox_OAuthRequest::from_consumer_and_token($this->consumer, NULL, $method, $uri, $args);

		/* Generate the signature of the request. */
		$request->sign_request($sign, $this->consumer, $tokensign);

		/* Convert the request into an URL. */
		$reqUrl = $request->to_url();

		$arguments['headers'] = $httpHeaders;

		/* Execute the request. */
		$out = $this->http($reqUrl, $arguments);

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
				throw new Dropbox_Exception_Forbidden('Forbidden. Bad or expired token.');
			case '403':
				throw new Dropbox_Exception_Forbidden('Forbidden. This could mean a bad OAuth request, or a file or folder already existing at the target location.');
			/* Not Found */
			case '404' :
				throw new Dropbox_Exception_NotFound('Resource at uri: ' . $uri . ' could not be found');
			/* Request method */    
			case '405' :
				throw new Dropbox_Exception_RequestMethod('Request method not expected.');
			case '503' :
				throw new Dropbox_Exception_TooManyRequest('Too many request.');
			/* Full */
			case '507' :
				throw new Dropbox_Exception_OverQuota('This dropbox is full');
			default:
				$trace_msg = 'Unknow return code from the Dropbox server : ' . $out['rc'] . "<br>\n";
				$trace_msg .= 'Curl error number : ' . $out['errno'] . "<br>\n";
				$trace_msg .= 'Curl error message : ' . $out['err'] . "<br>\n";
			        $trace_msg .= 'Info : ' . "\n";
				foreach($out['info'] AS $key => $val) {
				    $trace_msg .= ' - ' . $key . ' : ' . $val . "\n";
				}
				$trace_msg .= 'Html : ' . $out['html'] . "\n";
				$trace_msg .= 'Request URL : ' . $reqUrl . "\n";
				$trace_msg .= 'Argument(s) : ' . "\n";
				foreach($arguments AS $key => $val) {
				    $trace_msg .= ' - ' . $key . ' : ';
				    if(is_array($val)) {
					foreach($val AS $k => $v) {
					    $trace_msg .= ' |---- ' . $k . ' => ' . $v . "\n";
					}
				    }
				    else
					$trace_msg .= $val . "\n";
				}
				throw new Dropbox_Exception($trace_msg);
		}
	}

	/**
	 * Requests the OAuth request token.
	 *
	 * @return void
	 */
	public function getRequestToken($oauth_callback = NULL) {
		$parameters = array();
		if (!empty($oauth_callback)) {
			$parameters['oauth_callback'] = $oauth_callback;
		}
		try {
			$request = $this->fetch(self::URI_REQUEST_TOKEN, $parameters);
			$token = DropBox_OAuthUtil::parse_parameters($request['body']);
			$this->setToken($token['oauth_token'], $token['oauth_token_secret']);
			return $token;
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
	public function getAccessToken($oauth_verifier = false) {
		$parameters = array();
		if (!empty($oauth_verifier)) {
		    $parameters['oauth_verifier'] = $oauth_verifier;
		}	    
		$request = $this->fetch(self::URI_ACCESS_TOKEN, $parameters);
		$token = DropBox_OAuthUtil::parse_parameters($request['body']);
		$this->setToken($token['oauth_token'], $token['oauth_token_secret']);
		return $token;
	}

}
