<?
	#
	# $Id$
	#

	include('include/init.php');
	loadlib("tumblr_users");
	loadlib("tumblr_blogs");
	loadlib("tumblr_api");

	$GLOBALS['smarty']->assign('page_title', 'Sync');

	if ($GLOBALS['cfg']['user']['id']){
		$tumblr_user = tumblr_users_get_by_username($GLOBALS['cfg']['user']['username']);
		$access_token = array(
			'oauth_token' => $tumblr_user['oauth_token'],
			'oauth_token_secret' => $tumblr_user['oauth_secret']
			);
		$userinfo = tumblr_api_get_user_info($access_token);		
		$rsp = tumblr_blogs_sync_blogs($userinfo);
		
		# $blogs = tumblr_blogs_get_by_user_id($GLOBALS['cfg']['user']['id']);
		
		$GLOBALS['smarty']->assign('userinfo', $rsp);
	}
		
	#
	# output
	#

	$smarty->display('page_account_sync.txt');