<?
	#
	# $Id$
	#

	include('include/init.php');
	loadlib("tumblr_api");
	loadlib("tumblr_users");
	loadlib("tumblr_blogs");
	
	features_ensure_enabled("signin");
	login_ensure_loggedin("/account");
	
	$GLOBALS['smarty']->assign('page_title', 'Sync Blogs');
	
	if (! $GLOBALS['cfg']['user']['id']){
		exit();
	}
	
	$tumblr_user = tumblr_users_get_by_username($GLOBALS['cfg']['user']['username']);
	$access_token = array(
		'oauth_token' => $tumblr_user['oauth_token'],
		'oauth_token_secret' => $tumblr_user['oauth_secret']
		);
		
	$userinfo = tumblr_api_get_call($access_token, 'user/info');		
		
	$rsp = tumblr_blogs_sync_blogs($userinfo);

	$GLOBALS['smarty']->assign('rsp', $rsp);
	
	$blogs = tumblr_blogs_get_by_user_id($GLOBALS['cfg']['user']['id']);
	
	$GLOBALS['smarty']->assign('blogs', $blogs);
	
	
	#
	# output
	#

	$smarty->display('page_account_sync_blogs.txt');