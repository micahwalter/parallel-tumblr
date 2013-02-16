<?
	#
	# $Id$
	#

	include('include/init.php');
	loadlib('tumblr_posts');
	
	features_ensure_enabled("signin");
	login_ensure_loggedin("/account");
	
	$more = array(
		'per_page' => 3
	);

	if ($page = get_int32("page")){
		$more['page'] = $page;
	}

	if ($filter = get_str("filter")){
		$more['filter'] = $filter;
	}

	
	$rsp = tumblr_posts_get_posts($more);
	
	$GLOBALS['smarty']->assign('posts', $rsp);
	
	$GLOBALS['smarty']->assign('page_title', 'Posts');
	
	#
	# output
	#

	$pagination_url = "{$GLOBALS['cfg']['site_abs_root_url']}";
	$GLOBALS['smarty']->assign("pagination_url", $pagination_url);

	$smarty->display('page_posts.txt');
