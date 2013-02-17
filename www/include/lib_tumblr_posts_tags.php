<?php
	
	#################################################################

	function tumblr_posts_tags_create_tag($postTag){

		$hash = array();

		foreach ($postTag as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$rsp = db_insert('TumblrPostsTags', $hash);

		return $rsp;
		
	}

	#################################################################

	function tumblr_posts_tags_update_tag($tag){

		$hash = array();
		
		foreach ($tag as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$post_artisanal_id = AddSlashes($tag['post_artisanal_id']);
		$where = "post_artisanal_id='{$post_artisanal_id}'";

		$rsp = db_update('TumblrPostsTags', $hash, $where);

		return $rsp;
		
	}
	
	#################################################################

	function tumblr_posts_tags_sync_tags($post_artisanal_id, $tags){

		foreach ($tags as $element){
			$tag = tumblr_posts_tags_get_by_name($element);
			$tag_artisanal_id = $tag['tag_artisanal_id'];
				$rsp = tumblr_posts_tags_create_tag(array(
					'tag_artisanal_id' => $tag_artisanal_id,
					'post_artisanal_id' => $post_artisanal_id
					)); 
		}
		return $rsp;
		
	}
	
	#################################################################
	
	function tumblr_posts_tags_get_by_artisanal_id($artisanal_id) {
		
		$enc_artisanal_id = AddSlashes($artisanal_id);
		
		$sql = "SELECT * FROM TumblrPostsTags WHERE post_artisanal_id='{$enc_artisanal_id}'";
		return db_single(db_fetch($sql));
	}

	#################################################################
	
	function tumblr_posts_tags_get_by_name($tag) {
		
		$enc_tag = AddSlashes($tag);
		
		$sql = "SELECT * FROM TumblrTags WHERE tag='{$enc_tag}'";
		return db_single(db_fetch($sql));
	}


?>