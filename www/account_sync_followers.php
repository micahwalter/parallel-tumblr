<?php

	#
	# $Id$
	#

	include('include/init.php');
	loadlib("tumblr_api");
	loadlib("tumblr_users");
	loadlib("tumblr_blogs");
	loadlib("tumblr_followers");
	
	features_ensure_enabled("signin");
	login_ensure_loggedin("/account");
	
	$GLOBALS['smarty']->assign('page_title', 'Sync Followers');
	
	if (! $GLOBALS['cfg']['user']['id']){
		exit();
	}
	
	$tumblr_user = tumblr_users_get_by_username($GLOBALS['cfg']['user']['username']);
	$access_token = array(
		'oauth_token' => $tumblr_user['oauth_token'],
		'oauth_token_secret' => $tumblr_user['oauth_secret']
	);
	
	$tumblr_blogs = tumblr_blogs_get_by_user_id($GLOBALS['cfg']['user']['id']);
	
	$blogs_count = count($tumblr_blogs['rows']);
		
	$i = 0;
	while ($i < $blogs_count ) {
		$regex = '/(?<!href=["\'])http:\/\//';
		$base_hostname = preg_replace($regex, '', $tumblr_blogs['rows'][$i]['url']);		

		$offset = 0;
		$api_key = $GLOBALS['cfg']['tumblr_api_key'];
		$params = array(
			'api_key' => $api_key,
			'offset' => $offset,
			'limit' => 20
		);

		$followers = tumblr_api_get_call($access_token, 'blog/' . $base_hostname . 'followers', $params);
		$total_followers = $followers->response->total_users;

		while($offset < $total_followers) {	
			$params = array(
				'api_key' => $api_key,
				'offset' => $offset,
				'limit' => 20
			);

		$followers = tumblr_api_get_call($access_token, 'blog/' . $base_hostname . 'followers' , $params );
		$rsp = tumblr_followers_sync_followers($followers, $tumblr_blogs['rows'][$i]['name']);
		$offset = $offset + 20;
		};
		$i++;
	}

	
	$i = 0;
	while ($i < $blogs_count ) {		

		$all_the_followers[$i] = tumblr_followers_get_by_blog_name($tumblr_blogs['rows'][$i]['name']);
		$i++;
	}
	
	$GLOBALS['smarty']->assign('followers', $all_the_followers);
		
	#
	# output
	#

	$smarty->display('page_account_sync_followers.txt');
