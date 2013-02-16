<?php

	#################################################################

	function tumblr_following_create_following($blog){

		$hash = array();

		foreach ($blog as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$rsp = db_insert('TumblrFollowing', $hash);

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

		return $rsp;
	}
	
	#################################################################
	
	function tumblr_following_sync_following($following, $artisanal_id){
		
		$blogs = $following->response->blogs;
		
		foreach ($blogs as $element ){
			
			$api_key = $GLOBALS['cfg']['tumblr_api_key'];

			$params = array(
				'api_key' => $api_key,
			);

			$regex = '/(?<!href=["\'])http:\/\//';
			$base_hostname = preg_replace($regex, '', $element->url);

			$avatar = tumblr_api_get_avatar($access_token, 'blog/' . $base_hostname . 'avatar' , $params );
			
			$following = tumblr_following_get_by_name_and_id($element->name, $GLOBALS['cfg']['user']['id']);
			
			if(! $following ) {
				$rsp = tumblr_following_create_following(array(
					'user_id' => $GLOBALS['cfg']['user']['id'],
					'user_artisanal_id' => $artisanal_id,
					'name' => $element->name,
					'url' => $element->url,
					'avatar_url' => $avatar->response->avatar_url,
					'updated' => $element->updated
				
				)); 
			} else {
				$rsp = tumblr_following_update_following(array(
					'user_id' => $GLOBALS['cfg']['user']['id'],
					'user_artisanal_id' => $artisanal_id,
					'name' => $element->name,
					'url' => $element->url,
					'avatar_url' => $avatar->response->avatar_url,
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

	#################################################################
	
	function tumblr_following_get_by_id($user_id) {
		
		$enc_user_id = AddSlashes($user_id);
		
		$sql = "SELECT * FROM TumblrFollowing WHERE user_id='{$enc_user_id}'";
		return db_fetch($sql);
	}
	

?>