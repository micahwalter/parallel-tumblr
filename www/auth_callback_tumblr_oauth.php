<?php

	include("include/init.php");
	
	loadlib("http");
	loadlib("random");
	loadlib("tumblr_api");
	loadlib("tumblr_users");
	

	if ($GLOBALS['cfg']['user']['id']){
		header("location: {$GLOBALS['cfg']['abs_root_url']}");
		exit();
	}


	if (! $GLOBALS['cfg']['enable_feature_signin']){
		$GLOBALS['smarty']->display("page_signin_disabled.txt");
		exit();
	}
	
	$code = get_str("code");
	$redir = get_str("redir");
	
	// now we do the tumblr auth stuff
		
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
  		die('Unable to authenticate'); // TODO: do something better than this here
	}

	// if we are this far, we have authed to tumblr, yay!
	// now we need to get the user's info
	
	$tum_oauth = new TumblrOAuth($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);

	$userinfo = $tum_oauth->get('user/info');

	// Check for an error.
	if (200 == $tum_oauth->http_code) {
	  // good to go
	} else {
	  die('Unable to get info');
	}
	
	$username = $userinfo->response->user->name;
	$likes = $userinfo->response->user->likes;
	$following = $userinfo->response->user->following;
	$default_post_format = $userinfo->response->user->default_post_format;
	$token = $access_token['oauth_token'];
	$secret = $access_token['oauth_token_secret'];
	
	# The first thing we do is check to see if we already have an account
	# matching that user's Flickr NSID.

	$tumblr_user = tumblr_users_get_by_username($username);

	if ($tumblr_user){
		
		$user = users_get_by_id($tumblr_user['user_id']);
		$change = 0;

		if (! $tumblr_user['oauth_token']){
			$change = 1;
		}

		if ($tumblr_user['oauth_token'] != $token){
			$change = 1;
		}

		if ($change){

			$update = array(
				'likes' => $likes,
				'following' => $following,
				'default_post_format' => $default_post_format,
				'oauth_token' => $token,
				'oauth_secret' => $secret
			);

			$rsp = tumblr_users_update_user($tumblr_user, $update);

			if (! $rsp['ok']){
				$GLOBALS['error']['dberr_tumblruser_update'] = 1;
				$GLOBALS['smarty']->display("page_auth_callback_tumblr_tumblrauth.txt");
				exit();
			}
		}
	}

	# If we don't ensure that new users are allowed to create
	# an account (locally).

	else if (! $GLOBALS['cfg']['enable_feature_signup']){
		$GLOBALS['smarty']->display("page_signup_disabled.txt");
		exit();
	}

	# Hello, new user! This part will create entries in two separate
	# databases: Users and FlickrUsers that are joined by the primary
	# key on the Users table.

	else {

		$password = random_string(32);

		$user = users_create_user(array(
			"username" => $username,
			"email" => "{$username}@donotsend-tumblr.com",
			"password" => $password,
		));

		if (! $user){
			$GLOBALS['error']['dberr_user'] = 1;
			$GLOBALS['smarty']->display("page_auth_callback_tumblr_tumblrauth.txt");
			exit();
		}

		$tumblr_user = tumblr_users_create_user(array(
			'user_id' => $user['id'],
			'tumblr_username' => $username,
			'likes' => $likes,
			'following' => $following,
			'default_post_format' => $default_post_format,
			'oauth_token' => $token,
			'oauth_secret' => $secret,
		));

		if (! $tumblr_user){
			$GLOBALS['error']['dberr_tumblruser'] = 1;
			$GLOBALS['smarty']->display("page_auth_callback_tumblr_tumblrauth.txt");
			exit();
		}
	}

	# Okay, now finish logging the user in (setting cookies, etc.) and
	# redirecting them to some specific page if necessary.

	$redir = (isset($extra['redir'])) ? $extra['redir'] : '';
	
	login_do_login($user, $redir);
	exit();
	
	
?>
