<?php

	loadlib("artisanal_integers");

	#################################################################

	function tumblr_blogs_get_by_name($blogname){

		$enc_blogname = AddSlashes($blogname);

		$sql = "SELECT * FROM TumblrBlogs WHERE name='{$enc_blogname}'";
		return db_single(db_fetch($sql));
	}	
	
	#################################################################
	
	function tumblr_blogs_get_primary_blog($tumblr_user_id){
		
		$enc_user_id = AddSlashes($tumblr_user_id);
		
		$sql = "SELECT url FROM TumblrBlogs WHERE user_id='{$enc_user_id}' AND TumblrBlogs.primary='1'";
		return db_single(db_fetch($sql));
	}

	

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

	function tumblr_blogs_update_blog($blog){

		$hash = array();
		
		foreach ($blog as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$enc_id = AddSlashes($blog['name']);
		$where = "name='{$enc_id}'";

		$rsp = db_update('TumblrBlogs', $hash, $where);

		if ($rsp['ok']){

			#$tumblr_blogs = array_merge($tumblr_blogs, $update);

			# $cache_key = "tumblr_user_{$tumblr_user['tumblr_id']}";
			# cache_unset($cache_key);

			$cache_key = "tumblr_blogs_{$tumblr_blogs['user_id']}";
			cache_unset($cache_key);
		}

		return $rsp;
	}
	
	#################################################################

	function tumblr_blogs_get_by_user_id($user_id){

		$enc_id = AddSlashes($user_id);

		$sql = "SELECT * FROM TumblrBlogs WHERE user_id='{$enc_id}'";
		return db_fetch($sql);
	}

	#################################################################
	
	function tumblr_blogs_sync_blogs($userinfo){
				
		$blogs = $userinfo->response->user->blogs;
		
		foreach ($blogs as $element ){
			
			$blog = tumblr_blogs_get_by_name($element->name);
			
			if(! $blog ) {
				$provider = 'brooklyn';
				$artisanal = artisanal_integers_create($provider);
				
				$rsp = tumblr_blogs_create_blog(array(
					'artisanal_id' => $artisanal['integer'],
					'user_id' => $GLOBALS['cfg']['user']['id'],
					'name' => $element->name,
					'url' => $element->url,
					'followers' => $element->followers,
					'primary' => $element->primary,
					'title' => $element->title,
					'description' => $element->description,
					'admin' => $element->admin,
					'updated' => $element->updated,
					'posts' => $element->posts,
					'messages' => $element->messages,
					'queue' => $element->queue,
					'drafts' => $element->drafts,
					'share_likes' => $element->share_likes,
					'ask' => $element->ask,
					'ask_anon' => $element->ask_anon,
					'tweet' => $element->tweet,
					'facebook' => $element->facebook,
					'facebook_opengraph_enabled' => $element->facebook_opengraph_enabled,
					'type' => $element->type,
				)); 
			} else {
				$rsp = tumblr_blogs_update_blog(array(
					'user_id' => $GLOBALS['cfg']['user']['id'],
					'name' => $element->name,
					'url' => $element->url,
					'followers' => $element->followers,
					'primary' => $element->primary,
					'title' => $element->title,
					'description' => $element->description,
					'admin' => $element->admin,
					'updated' => $element->updated,
					'posts' => $element->posts,
					'messages' => $element->messages,
					'queue' => $element->queue,
					'drafts' => $element->drafts,
					'share_likes' => $element->share_likes,
					'ask' => $element->ask,
					'ask_anon' => $element->ask_anon,
					'tweet' => $element->tweet,
					'facebook' => $element->facebook,
					'facebook_opengraph_enabled' => $element->facebook_opengraph_enabled,
					'type' => $element->type,
				));
				
			} 
		}
		return $rsp;
	}	
	
?>