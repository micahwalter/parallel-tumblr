<?php
	
	loadlib("artisanal_integers");
	loadlib("tumblr_tags");

	#################################################################

	function tumblr_posts_create_post($post){

		$hash = array();

		foreach ($post as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$rsp = db_insert('TumblrPosts', $hash);

		return $post;
		
	}

	#################################################################

	function tumblr_posts_update_post($post){

		$hash = array();
		
		foreach ($post as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$post_id = AddSlashes($blog['post_id']);
		$where = "post_id='{$post_id}'";

		$rsp = db_update('TumblrPosts', $hash, $where);

		return $rsp;
		
	}

	#################################################################

	function tumblr_posts_sync_posts($posts, $blog_artisanal_id){

		$blog_posts = $posts->response->posts;
		
		foreach ($blog_posts as $element ){
			
			$post = tumblr_posts_get_by_post_id($element->id);
			
			if(! $post ) {
				$provider = 'brooklyn';
				$new_artisanal = artisanal_integers_create($provider);
				
				$rsp = tumblr_posts_create_post(array(
					'post_artisanal_id' => $new_artisanal['integer'],
					'blog_artisanal_id' => $blog_artisanal_id,
					'blog_name' => $element->blog_name,
					'post_id' => $element->id,
					'post_url'=> $element->post_url,
					'slug' => $element->slug,
					'type' => $element->type,
					'timestamp' => $element->timestamp,
					'date' => $element->date,
					'format' => $element->format,
					'reblog_key' => $element->reblog_key,
					'bookmarklet' => $element->bookmarkley,
					'mobile' => $element->mobile,
					'source_url' => $element->source_url,
					'source_title' => $element->source_title,
					'liked' => $element->liked,
					'note_count' => $element->note_count,
					'state' => $element->state,
					'short_url' => $element->short_url,
					'image_permalink' => $element->image_permalink,
					'can_reply' => $element->can_reply,
					'title' => $element->title,
					'body' => $element->body,
					'text' => $element->text,
					'source' => $element->source,
					'link_url' => $element->link_url,
					'description' => $element->description,
					'dialogue' => $element->dialog
				)); 
			} else {
				$rsp = tumblr_posts_update_post(array(
					'blog_name' => $element->blog_name,
					'post_id' => $element->id,
					'post_url'=> $element->post_url,
					'slug' => $element->slug,
					'type' => $element->type,
					'timestamp' => $element->timestamp,
					'date' => $element->date,
					'format' => $element->format,
					'reblog_key' => $element->reblog_key,
					'bookmarklet' => $element->bookmarkley,
					'mobile' => $element->mobile,
					'source_url' => $element->source_url,
					'source_title' => $element->source_title,
					'liked' => $element->liked,
					'note_count' => $element->note_count,
					'state' => $element->state,
					'short_url' => $element->short_url,
					'image_permalink' => $element->image_permalink,
					'can_reply' => $element->can_reply,
					'title' => $element->title,
					'body' => $element->body,
					'text' => $element->text,
					'source' => $element->source,
					'link_url' => $element->link_url,
					'description' => $element->description,
					'dialogue' => $element->dialog
						
				));	
			}
		}
		return $rsp;
		
	}

	#################################################################
	
	function tumblr_posts_get_by_post_id($post_id) {
		
		$enc_post_id = AddSlashes($post_id);
		
		$sql = "SELECT * FROM TumblrPosts WHERE post_id='{$enc_post_id}'";
		return db_single(db_fetch($sql));
	}

	#################################################################
	
	function tumblr_posts_get_by_artisanal_id($artisanal_id) {
		
		$enc_artisanal_id = AddSlashes($artisanal_id);
		
		$sql = "SELECT * FROM TumblrPosts WHERE post_artisanal_id='{$enc_artisanal_id}'";
		return db_single(db_fetch($sql));
	}

	#################################################################
	
	function tumblr_posts_get_posts($more=array()) {
				
		$sql = "SELECT * FROM TumblrPosts ORDER BY timestamp DESC";
		return db_fetch_paginated($sql, $more);
	}
	
	#################################################################
	
	function tumblr_posts_get_posts_by_blog_name($blog_name) {
		
		$enc_blog_name = AddSlashes($blog_name);
				
		$sql = "SELECT * FROM TumblrPosts WHERE blog_name='{$enc_blog_name}' ORDER BY timestamp DESC";
		return db_fetch($sql);
	}

	#################################################################
	
	function tumblr_posts_get_posts_by_blog_id($blog_id, $more=array()) {
		
		$enc_blog_id = AddSlashes($blog_id);
				
		$sql = "SELECT * FROM TumblrPosts WHERE blog_artisanal_id='{$enc_blog_id}' ORDER BY timestamp DESC";
		return db_fetch_paginated($sql, $more);
	}

	#################################################################
	
	function tumblr_posts_get_from_url() {
		
		if ($id = get_int64("id")){
			$post = tumblr_posts_get_by_artisanal_id($id);
		}
		
		return $post;	
	}
	

?>