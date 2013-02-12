<?php

	include("include/init.php");
	
	loadlib("http");
	loadlib("random");
	loadlib("tumblr_api");
	loadlib("tumblr_users");
	loadlib("tumblr_blogs");
	

	if ($GLOBALS['cfg']['user']['id']){
		header("location: {$GLOBALS['cfg']['abs_root_url']}");
		exit();
	}


	if (! $GLOBALS['cfg']['enable_feature_signin']){
		$GLOBALS['smarty']->display("page_signin_disabled.txt");
		exit();
	}
	
	// now we do the tumblr auth stuff
	
	$access_token = tumblr_api_get_auth_token();
		
	// if we are this far, we have authed to tumblr, yay!
	// now we need to get the user's info
	
	$userinfo = tumblr_api_call($access_token, 'user/info');
	$username = $userinfo->response->user->name;
	$token = $access_token['oauth_token'];
	$secret = $access_token['oauth_token_secret'];
		
	# The first thing we do is check to see if we already have an account
	# matching that user's tumblr username.

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
				'likes' => $userinfo->response->user->likes,
				'following' => $userinfo->response->user->following,
				'default_post_format' => $userinfo->response->user->default_post_format,
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
	# databases: Users and TumblrUsers that are joined by the primary
	# key on the Users table.

	else {

		$rsp = tumblr_users_register_user($userinfo, $access_token);

		if (! $rsp['ok']){

			$GLOBALS['error'][ $rsp['error'] ] = 1;
			$GLOBALS['smarty']->display("page_auth_callback_tumblr_tumblroauth.txt");
			exit();
		}

		$user = $rsp['user']['user'];
	}

	# Okay, now finish logging the user in (setting cookies, etc.) and
	# redirecting them to some specific page if necessary.

	login_do_login($user);
	exit();
	
	
?>
