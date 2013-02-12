<?php

	#################################################################

	function tumblr_followers_create_follower($blog){

		$hash = array();

		foreach ($blog as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$rsp = db_insert('TumblrFollowers', $hash);

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

	function tumblr_followers_update_follower($blog){

		$hash = array();
		
		foreach ($blog as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$enc_id = AddSlashes($blog['blog_name']);
		$where = "blog_name='{$enc_id}'";
		
		$enc_name = AddSlashes($blog['name']);
		$where = $where + "AND name='{$enc_name}'";

		$rsp = db_update('TumblrFollowers', $hash, $where);

		if ($rsp['ok']){

			# $cache_key = "tumblr_user_{$tumblr_user['tumblr_id']}";
			# cache_unset($cache_key);

			$cache_key = "tumblr_blogs_{$tumblr_blogs['user_id']}";
			cache_unset($cache_key);
		}

		return $rsp;
	}
	
	#################################################################
	
	function tumblr_followers_sync_followers($followers, $blogname){
		
		$users = $followers->response->users;
		
		foreach ($users as $element ){
			
			$follower = tumblr_followers_get_by_blog_name_and_name($element->name, $blogname);
			
			if(! $follower ) {
				$rsp = tumblr_followers_create_follower(array(
					'blog_name' => $blogname,
					'name' => $element->name,
					'url' => $element->url,
					'updated' => $element->updated
				
				)); 
			} else {
				$rsp = tumblr_followers_update_follower(array(
					'blog_name' => $blogname,
					'name' => $element->name,
					'url' => $element->url,
					'updated' => $element->updated
				));
				
			} 
		}
		return 0;
	}		
	
	#################################################################
	
	function tumblr_followers_get_by_blog_name_and_name($name, $blogname) {
		
		$enc_blogname = AddSlashes($blogname);
		$enc_name = AddSlashes($name);
		
		$sql = "SELECT * FROM TumblrFollowers WHERE blog_name='{$enc_blogname}' AND name='{$enc_name}'";
		return db_single(db_fetch($sql));
	}
	

?>