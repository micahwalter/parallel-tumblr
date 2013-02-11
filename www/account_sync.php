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
		$GLOBALS['smarty']->assign('userinfo', $userinfo);
		
		tumblr_blogs_sync_blogs($userinfo);
	}
		
	#
	# output
	#

	$smarty->display('page_account_sync.txt');