<?php

	#
	# $Id$
	#

	include('include/init.php');
	loadlib("tumblr_api");
	loadlib("tumblr_users");
	loadlib("tumblr_blogs");
	loadlib("tumblr_tags");
	
	features_ensure_enabled("signin");
	login_ensure_loggedin("/account");
	
	$GLOBALS['smarty']->assign('page_title', 'Sync Posts');
	
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

		$posts = tumblr_api_get_call($access_token, 'blog/' . $base_hostname . 'posts', $params);
		$total_posts = $posts->response->blog->posts;

		while($offset < $total_posts) {	
			$params = array(
				'api_key' => $api_key,
				'offset' => $offset,
				'limit' => 20
			);

		$posts = tumblr_api_get_call($access_token, 'blog/' . $base_hostname . 'posts' , $params );
		# $all_the_tags[$i] = $posts->response->posts;
		# $rsp = tumblr_tags_sync_tags($posts->response->posts->tags);
		$offset = $offset + 20;
		};
		$i++;
	}


	$GLOBALS['smarty']->assign('all_the_tags', $posts);
		
	#
	# output
	#

	$smarty->display('page_account_sync_tags.txt');
