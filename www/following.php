<?
	#
	# $Id$
	#

	include('include/init.php');
	loadlib('tumblr_following');
	
	features_ensure_enabled("signin");
	login_ensure_loggedin("/account");

	$rsp = tumblr_following_get_by_id($GLOBALS['cfg']['user']['id']);

	$GLOBALS['smarty']->assign('following', $rsp);
	
	$GLOBALS['smarty']->assign('page_title', 'Following');
	
	#
	# output
	#

	$smarty->display('page_following.txt');
