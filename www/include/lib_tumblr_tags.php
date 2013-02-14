<?php
	
	loadlib("artisanal_integers");

	#################################################################

	function tumblr_tags_create_tag($tag){

		$hash = array();

		foreach ($tag as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$rsp = db_insert('TumblrTags', $hash);

		if (! $rsp['ok']){
			return null;
		}

		# $cache_key = "tumblr_user_{$user['tumblr_id']}";
		# cache_set($cache_key, $user, "cache locally");

		$cache_key = "tumblr_blog_{$user['id']}";
		cache_set($cache_key, $user, "cache locally");

		return $post;
		
	}

	#################################################################

	function tumblr_tags_update_tag($tag){

		$hash = array();
		
		foreach ($tag as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$post_id = AddSlashes($tag['artisanal_id']);
		$where = "artisanal_id='{$artisanal_id}'";

		$rsp = db_update('TumblrTags', $hash, $where);

		if ($rsp['ok']){

			# $cache_key = "tumblr_user_{$tumblr_user['tumblr_id']}";
			# cache_unset($cache_key);

			$cache_key = "tumblr_blogs_{$tumblr_blogs['user_id']}";
			cache_unset($cache_key);
		}

		return $rsp;
		
	}

	#################################################################

	function tumblr_tags_sync_tags($data){

		$tags = $data->tags;
		
		foreach ($tags as $element ){
			
			$tag = tumblr_tags_get_by_artisanal_id($element->artisnala_id);
			
			if(! $tag ) {
				$provider = 'brooklyn';
				$artisanal = artisanal_integers_create($provider);
				
				$rsp = tumblr_tags_create_tag(array(
					'artisanal_id' => $artisanal['integer'],
					'tag' => $tag['tag']
				)); 
			} else {
				$rsp = tumblr_posts_update_post(array(
					'tag' => $tag['tag']	
				));	
			}
		}
		return $rsp;
		
	}

	#################################################################
	
	function tumblr_tags_get_by_artisanal_id($artisinal_id) {
		
		$enc_artisinal_id = AddSlashes($artisinal_id);
		
		$sql = "SELECT * FROM TumblrTags WHERE artisinal_id='{$enc_artisinal_id}'";
		return db_single(db_fetch($sql));
	}

?>