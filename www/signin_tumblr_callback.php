<?php

	include("include/init.php");
	loadlib("tumblr_api");

	$GLOBALS['smarty']->assign('nav_tab', 'signin');
	
	session_start();
	require_once('include/tumblroauth/tumblroauth.php');

	$consumer_key = $GLOBALS['cfg']['tumblr_api_key'];
	$consumer_secret = $GLOBALS['cfg']['tumblr_api_secret'];

	$tum_oauth = new TumblrOAuth($consumer_key, $consumer_secret, $_SESSION['request_token'], $_SESSION['request_token_secret']);

	$access_token = $tum_oauth->getAccessToken($_REQUEST['oauth_verifier']);

	unset($_SESSION['request_token']);
	unset($_SESSION['request_token_secret']);

	if (200 == $tum_oauth->http_code) {
  		// good to go
	} else {
  		die('Unable to authenticate');
	}

	// we still need to store the request token and secret in the DB with the user account info...

	$smarty->display('page_signin_done.txt');
	
?>
