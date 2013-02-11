<?
	#
	# $Id$
	#

	include('include/init.php');
	loadlib("tumblr_users");
	loadlib("tumblr_blogs");

	#
	# this is so we can test the logging output
	#

	if ($_GET['log_test']){
		log_error("This is an error!");
		log_fatal("Fatal error!");
	}


	#
	# this is so we can test the HTTP library
	#

	if ($_GET['http_test']){
		$ret = http_get("http://google.com");
	}

	if ($GLOBALS['cfg']['user']['id']){
		$tumblr_user = tumblr_users_get_by_username($GLOBALS['cfg']['user']['username']);
		$GLOBALS['smarty']->assign('tumblr_user', $tumblr_user);
		$blogs = tumblr_blogs_get_by_user_id($GLOBALS['cfg']['user']['id']);
		$blogs = $blogs['rows'];
		$GLOBALS['smarty']->assign('blogs', $blogs);
		
	}
	
	#
	# output
	#

	$smarty->display('page_index.txt');
