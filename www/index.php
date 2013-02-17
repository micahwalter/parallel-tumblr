<?
	#
	# $Id$
	#

	include('include/init.php');
	loadlib("tumblr_api");
	loadlib("tumblr_users");
	loadlib("tumblr_blogs");
	loadlib("artisanal_integers");
	loadlib('tumblr_posts');
	loadlib("tumblr_following");
		
	$tumblr_user = tumblr_users_get_by_username($GLOBALS['cfg']['user']['username']);
	$GLOBALS['smarty']->assign('tumblr_user', $tumblr_user);
	$blogs = tumblr_blogs_get_by_user_id($GLOBALS['cfg']['user']['id']);
	$blogs = $blogs['rows'];
	$GLOBALS['smarty']->assign('blogs', $blogs);
		
	$access_token = array(
		'oauth_token' => $tumblr_user['oauth_token'],
		'oauth_token_secret' => $tumblr_user['oauth_secret']
	);
		
	$api_key = $GLOBALS['cfg']['tumblr_api_key'];
		
	$params = array(
		'api_key' => $api_key,
	);
		
	$primary_url = tumblr_blogs_get_primary_blog($GLOBALS['cfg']['user']['id']);
		
	$regex = '/(?<!href=["\'])http:\/\//';
	$base_hostname = preg_replace($regex, '', $primary_url['url']);
		
	$avatar = tumblr_api_get_avatar($access_token, 'blog/' . $base_hostname . 'avatar' , $params );
	$avatar = $avatar->response->avatar_url;		
	$GLOBALS['smarty']->assign('avatar', $avatar);
			
	$rsp = tumblr_following_get_by_id($GLOBALS['cfg']['user']['id']);

	$GLOBALS['smarty']->assign('following', $rsp);		
	
	#
	# output
	#

	$smarty->display('page_index.txt');
