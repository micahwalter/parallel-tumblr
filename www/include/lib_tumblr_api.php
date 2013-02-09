<?php

	#
	# $Id$
	#

	loadlib("http");
	
	#################################################################

	function tumblr_api_auth_url(){
		
		session_start();

		require_once('tumblroauth/tumblroauth.php');

		$consumer_key = $GLOBALS['cfg']['tumblr_api_key'];
		$consumer_secret = $GLOBALS['cfg']['tumblr_api_secret'];

		$callback_url = $GLOBALS['cfg']['abs_root_url']."/signin_tumblr_callback.php";

		$tum_oauth = new TumblrOAuth($consumer_key, $consumer_secret);

		$request_token = $tum_oauth->getRequestToken($callback_url);

		$_SESSION['request_token'] = $token = $request_token['oauth_token'];
		$_SESSION['request_token_secret'] = $request_token['oauth_token_secret'];

		switch ($tum_oauth->http_code) {
		  case 200:
		    $url = $tum_oauth->getAuthorizeURL($token);
		    header('Location: ' . $url);

		    break;
		default:
		    echo 'Could not connect to Tumblr. Refresh the page or try again later.';
		}
		exit();
		
	}
			
?>