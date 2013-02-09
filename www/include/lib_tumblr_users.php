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

	function tumblr_users_register_user($tumblr_username, $following, $likes, $default_post_format, $oauth_token='', $oauth_secret=''){

		$password = random_string(32);

		$email = "{$tumblr_username}@donotsend-tumblr.com";

		$user = users_create_user(array(
			"username" => $tumblr_username,
			"email" => $email,
			"password" => $password,
		));

		if (! $user){
			return not_okay("dberr_user");
		}

		$tumblr_user = tumblr_users_create_user(array(
			'user_id' => $user['id'],
			'oauth_key' => $oauth_key,
			'oauth_secret' => $oauth_secret,
			'tumblr_username' => $tumblr_username,
			'following' => $following,
			'likes' => $likes,
			'default_post_format' => $default_post_format
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

		if (! $rsp['ok']){
			return null;
		}

		# $cache_key = "tumblr_user_{$user['tumblr_id']}";
		# cache_set($cache_key, $user, "cache locally");

		$cache_key = "tumblr_user_{$user['id']}";
		cache_set($cache_key, $user, "cache locally");

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

		if ($rsp['ok']){

			$tumblr_user = array_merge($tumblr_user, $update);

			# $cache_key = "tumblr_user_{$tumblr_user['tumblr_id']}";
			# cache_unset($cache_key);

			$cache_key = "tumblr_user_{$tumblr_user['user_id']}";
			cache_unset($cache_key);
		}

		return $rsp;
	}

	#################################################################

?>
