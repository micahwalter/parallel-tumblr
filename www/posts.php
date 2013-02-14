<?
	#
	# $Id$
	#

	include('include/init.php');
	loadlib('tumblr_posts');
	
	features_ensure_enabled("signin");
	login_ensure_loggedin("/account");
	
	if ($page = get_int32("page")){
		$more['page'] = $page;
	}
	
	$rsp = tumblr_posts_get_posts();
	
	$GLOBALS['smarty']->assign('posts', $rsp);
	
	$GLOBALS['smarty']->assign('page_title', 'Posts');
	
	#
	# output
	#

	$pagination_url = "{$GLOBALS['cfg']['site_abs_root_url']}posts/";
	$GLOBALS['smarty']->assign("pagination_url", $pagination_url);

	$smarty->display('page_posts.txt');
