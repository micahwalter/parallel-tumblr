<?
	#
	# $Id$
	#

	include('include/init.php');
	loadlib("tumblr_users");
	loadlib("tumblr_blogs");
	loadlib("tumblr_following");
	loadlib("tumblr_api");

	$GLOBALS['smarty']->assign('page_title', 'Sync');

	if ($GLOBALS['cfg']['user']['id']){
		$tumblr_user = tumblr_users_get_by_username($GLOBALS['cfg']['user']['username']);
		$access_token = array(
			'oauth_token' => $tumblr_user['oauth_token'],
			'oauth_token_secret' => $tumblr_user['oauth_secret']
			);
		
		$userinfo = tumblr_api_get_call($access_token, 'user/info');		
		
		$rsp = tumblr_blogs_sync_blogs($userinfo);
		
		$offset = 0;
		$following = tumblr_api_get_call($access_token, 'user/following');
		$total_following = $following->response->total_blogs;
		
		while($offset < $total_following) {
			$params = array(
				'offset' => $offset,
				'limit' => 20
			);
		
			$following = tumblr_api_get_call($access_token, 'user/following', $params );
			$rsp = tumblr_following_sync_following($following);
			$offset = $offset + 20;
		};
		
		### some notes here on doing an "api_key" call vs. an oAuth call. whatever
		# $api_key = $GLOBALS['cfg']['tumblr_api_key'];
		# $params = array(
		#	'api_key' => $api_key,
		# );
		
		# $blog_info = tumblr_api_get_call($access_token, 'blog/micahwalter.tumblr.com/info', $params);		
				
		$GLOBALS['smarty']->assign('userinfo', $offset);
	}
		
	#
	# output
	#

	$smarty->display('page_account_sync.txt');