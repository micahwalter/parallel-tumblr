<?
	#
	# $Id$
	#

	include('include/init.php');
	loadlib("tumblr_api");
	loadlib("tumblr_users");
	loadlib("tumblr_following");
	
	features_ensure_enabled("signin");
	login_ensure_loggedin("/account");
	
	$GLOBALS['smarty']->assign('page_title', 'Sync Following');
	
	if (! $GLOBALS['cfg']['user']['id']){
		exit();
	}
	
	$tumblr_user = tumblr_users_get_by_username($GLOBALS['cfg']['user']['username']);
	$access_token = array(
		'oauth_token' => $tumblr_user['oauth_token'],
		'oauth_token_secret' => $tumblr_user['oauth_secret']
	);

	$offset = 0;
	$following = tumblr_api_get_call($access_token, 'user/following');
	$total_following = $following->response->total_blogs;
		
	while($offset < $total_following) {
		$params = array(
			'offset' => $offset,
			'limit' => 20
		);
		
		$following = tumblr_api_get_call($access_token, 'user/following', $params );
		$rsp = tumblr_following_sync_following($following, $tumblr_user['artisanal_id']);
		$offset = $offset + 20;
	};
	
	$following = tumblr_following_get_by_id($GLOBALS['cfg']['user']['id']);

	$GLOBALS['smarty']->assign('following', $following);
		
	#
	# output
	#

	$smarty->display('page_account_sync_following.txt');