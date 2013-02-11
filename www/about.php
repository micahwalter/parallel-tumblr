<?
	#
	# $Id$
	#

	include('include/init.php');

	$GLOBALS['smarty']->assign('page_title', 'About');
	
	#
	# output
	#

	$smarty->display('page_about.txt');
