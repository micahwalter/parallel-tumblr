<?php

	loadlib("random");
	
	#################################################################

	function tumblr_users_get_by_oauth_token($token){

		$enc_token = AddSlashes($token);

		$sql = "SELECT * FROM TumblrUsers WHERE oauth_token='{$enc_token}'";
		return db_single(db_fetch($sql));
	}

	#################################################################

	function tumblr_users_get_by_user_id($user_id){

		$enc_id = AddSlashes($user_id);

		$sql = "SELECT * FROM TumblrUsers WHERE user_id='{$enc_id}'";
		return db_single(db_fetch($sql));
	}

	#################################################################

	function tumblr_users_get_by_username($tumblr_username){

		$enc_username = AddSlashes($tumblr_username);

		$sql = "SELECT * FROM TumblrUsers WHERE tumblr_username='{$enc_username}'";
		return db_single(db_fetch($sql));
	}
	
	#################################################################

	# This creates rows in both the 'users' and 'tumblrUsers' tables

	function tumblr_users_register_user($userinfo, $access_token){
		
		$password = random_string(32);

		$email = "{$userinfo->response->user->name}@donotsend-tumblr.com";

		$user = users_create_user(array(
			"username" => $userinfo->response->user->name,
			"email" => $email,
			"password" => $password,
		));

		if (! $user){
			return not_okay("dberr_user");
		}

		$tumblr_user = tumblr_users_create_user(array(
			'user_id' => $user['user']['id'],
			'oauth_token' => $access_token['oauth_token'],
			'oauth_secret' => $access_token['oauth_token_secret'],
			'tumblr_username' => $userinfo->response->user->name,
			'following' => $userinfo->response->user->following,
			'likes' => $userinfo->response->user->likes,
			'default_post_format' => $userinfo->response->user->default_post_format
		));

		if (! $tumblr_user){
			return not_okay("dberr_tumblruser");
		}

		return okay(array(
			'user' => $user,
			'tumblr_user' => $tumblr_user,
		));
	}

	#################################################################

	function tumblr_users_create_user($user){

		$hash = array();

		foreach ($user as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$rsp = db_insert('TumblrUsers', $hash);

		return $user;
	}

	#################################################################

	function tumblr_users_update_user(&$tumblr_user, $update){

		$hash = array();
		
		foreach ($update as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$enc_id = AddSlashes($tumblr_user['user_id']);
		$where = "user_id='{$enc_id}'";

		$rsp = db_update('TumblrUsers', $hash, $where);

		return $rsp;
	}

	#################################################################

?>
