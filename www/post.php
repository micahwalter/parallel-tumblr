<?
	#
	# $Id$
	#

	include('include/init.php');
	loadlib('tumblr_posts');
	
	features_ensure_enabled("signin");
	login_ensure_loggedin("/account");

	$rsp = tumblr_posts_get_from_url();
	
	$GLOBALS['smarty']->assign('post', $rsp);
	
	$GLOBALS['smarty']->assign('page_title', $rsp['post_artisanal_id']);
	
	#
	# output
	#

	$smarty->display('page_post.txt');
