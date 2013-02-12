<?php

	#
	# $Id$
	#

	loadlib("http");
	require_once('tumblroauth/tumblroauth.php');
	
	#################################################################

	function tumblr_api_get_auth_url(){
		
		session_start();

		$consumer_key = $GLOBALS['cfg']['tumblr_api_key'];
		$consumer_secret = $GLOBALS['cfg']['tumblr_api_secret'];

		$callback_url = $GLOBALS['cfg']['abs_root_url']."auth_callback_tumblr_oauth.php";

		$tum_oauth = new TumblrOAuth($consumer_key, $consumer_secret);

		$request_token = $tum_oauth->getRequestToken($callback_url);

		$_SESSION['request_token'] = $token = $request_token['oauth_token'];
		$_SESSION['request_token_secret'] = $request_token['oauth_token_secret'];

		switch ($tum_oauth->http_code) {
		  case 200:
		    $url = $tum_oauth->getAuthorizeURL($token);
		    break;
		default:
		    echo 'Could not connect to Tumblr. Refresh the page or try again later.';
		}
		
		return $url;
		
	}
	
	#################################################################
	
	function tumblr_api_get_auth_token(){
		
		session_start();

		$consumer_key = $GLOBALS['cfg']['tumblr_api_key'];
		$consumer_secret = $GLOBALS['cfg']['tumblr_api_secret'];

		$tum_oauth = new TumblrOAuth($consumer_key, $consumer_secret, $_SESSION['request_token'], $_SESSION['request_token_secret']);

		$access_token = $tum_oauth->getAccessToken($_REQUEST['oauth_verifier']);

		unset($_SESSION['request_token']);
		unset($_SESSION['request_token_secret']);

		if (200 == $tum_oauth->http_code) {
	  		// good to go
		} else {
	  		die('Unable to authenticate'); // TODO: do something better than this here
		}
		
		return $access_token;
	}
	
	
	#################################################################
		
	function tumblr_api_get_call($access_token, $method, $params = NULL){
		
		session_start();

		$consumer_key = $GLOBALS['cfg']['tumblr_api_key'];
		$consumer_secret = $GLOBALS['cfg']['tumblr_api_secret'];

		$tum_oauth = new TumblrOAuth($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);

		$api_response = $tum_oauth->get($method, $params);

		// Check for an error.
		if (200 == $tum_oauth->http_code) {
		  // good to go
		} else {
		  die('Unable to get info: ' . print_r($tum_oauth));
		}
			
		return $api_response;	
		
	}

	#################################################################
		
	function tumblr_api_post_call($access_token, $method, $params = NULL){
		
		session_start();

		$consumer_key = $GLOBALS['cfg']['tumblr_api_key'];
		$consumer_secret = $GLOBALS['cfg']['tumblr_api_secret'];

		$tum_oauth = new TumblrOAuth($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);

		$api_response = $tum_oauth->post($method, $params);

		// Check for an error.
		if (200 == $tum_oauth->http_code) {
		  // good to go
		} else {
		  die('Unable to get info: '. $tum_oauth->http_code);
		}
			
		return $api_response;	
		
	}
	
	#################################################################
		
	function tumblr_api_get_avatar($access_token, $method, $params = NULL){
		
		session_start();

		$consumer_key = $GLOBALS['cfg']['tumblr_api_key'];
		$consumer_secret = $GLOBALS['cfg']['tumblr_api_secret'];

		$tum_oauth = new TumblrOAuth($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);

		$api_response = $tum_oauth->get($method, $params);
	
		return $api_response;	
		
	}

			
?>