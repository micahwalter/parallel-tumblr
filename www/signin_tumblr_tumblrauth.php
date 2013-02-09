<?php

	include("include/init.php");
	loadlib("tumblr_api");
	
	if ($GLOBALS['cfg']['user']['id']){
		header("location: {$redir}");
		exit();
	}

	if (! $GLOBALS['cfg']['enable_feature_signin']){
		$GLOBALS['smarty']->display("page_signin_disabled.txt");
		exit;
	}
	
	
	$url = tumblr_api_auth_url();
	
	exit();
?>