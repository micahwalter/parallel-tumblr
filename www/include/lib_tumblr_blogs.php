<?php

	#################################################################

	function tumblr_blogs_create_blog($blog){

		$hash = array();

		foreach ($blog as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$rsp = db_insert('TumblrBlogs', $hash);

		if (! $rsp['ok']){
			return null;
		}

		# $cache_key = "tumblr_user_{$user['tumblr_id']}";
		# cache_set($cache_key, $user, "cache locally");

		$cache_key = "tumblr_blog_{$user['id']}";
		cache_set($cache_key, $user, "cache locally");

		return $blog;
	}

	#################################################################
	

	#################################################################

	function tumblr_blogs_update_blogs(&$tumblr_blogs, $update){

		$hash = array();
		
		foreach ($update as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$enc_id = AddSlashes($tumblr_blogs['user_id']);
		$where = "user_id='{$enc_id}'";

		$rsp = db_update('TumblrBlogs', $hash, $where);

		if ($rsp['ok']){

			$tumblr_blogs = array_merge($tumblr_blogs, $update);

			# $cache_key = "tumblr_user_{$tumblr_user['tumblr_id']}";
			# cache_unset($cache_key);

			$cache_key = "tumblr_blogs_{$tumblr_blogs['user_id']}";
			cache_unset($cache_key);
		}

		return $rsp;
	}

	#################################################################
	
?>