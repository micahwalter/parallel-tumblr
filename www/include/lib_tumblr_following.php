<?php

	#################################################################

	function tumblr_following_create_following($blog){

		$hash = array();

		foreach ($blog as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$rsp = db_insert('TumblrFollowing', $hash);

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

	function tumblr_following_update_following($blog){

		$hash = array();
		
		foreach ($blog as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$enc_id = AddSlashes($blog['user_id']);
		$where = "user_id='{$enc_id}'";
		
		$enc_name = AddSlashes($blog['name']);
		$where = $where + "AND name='{$enc_name}'";

		$rsp = db_update('TumblrFollowing', $hash, $where);

		if ($rsp['ok']){

			# $cache_key = "tumblr_user_{$tumblr_user['tumblr_id']}";
			# cache_unset($cache_key);

			$cache_key = "tumblr_blogs_{$tumblr_blogs['user_id']}";
			cache_unset($cache_key);
		}

		return $rsp;
	}
	
	#################################################################
	
	function tumblr_following_sync_following($following){
		
		$blogs = $following->response->blogs;
		
		foreach ($blogs as $element ){
			
			$following = tumblr_following_get_by_name_and_id($element->name, $GLOBALS['cfg']['user']['id']);
			
			if(! $following ) {
				$rsp = tumblr_following_create_following(array(
					'user_id' => $GLOBALS['cfg']['user']['id'],
					'name' => $element->name,
					'url' => $element->url,
					'updated' => $element->updated
				
				)); 
			} else {
				$rsp = tumblr_following_update_following(array(
					'user_id' => $GLOBALS['cfg']['user']['id'],
					'name' => $element->name,
					'url' => $element->url,
					'updated' => $element->updated
				));
				
			} 
		}
		return 0;
	}		
	
	#################################################################
	
	function tumblr_following_get_by_name_and_id($blogname, $user_id) {
		
		$enc_blogname = AddSlashes($blogname);
		$enc_user_id = AddSlashes($user_id);
		
		$sql = "SELECT * FROM TumblrFollowing WHERE name='{$enc_blogname}' AND user_id='{$enc_user_id}'";
		return db_single(db_fetch($sql));
	}
	

?>