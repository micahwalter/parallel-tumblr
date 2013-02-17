<?php
	
	#################################################################

	function tumblr_tags_create_tag($tag){

		$hash = array();

		foreach ($tag as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$rsp = db_insert('TumblrTags', $hash);

		return $rsp;
		
	}

	#################################################################

	function tumblr_tags_update_tag($tag){

		$hash = array();
		
		foreach ($tag as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$tag_id = AddSlashes($tag['tag_id']);
		$where = "tag_id='{$tag_id}'";

		$rsp = db_update('TumblrTags', $hash, $where);

		return $rsp;
		
	}

	#################################################################

	function tumblr_tags_sync_tags($posts){

		foreach ($posts as $element){
						
			$tags = $element->tags;
			
				foreach($tags as $tag){
					$checkTag = tumblr_tags_get_by_tag($tag);
						if(! $checkTag ) {
							$rsp = tumblr_tags_create_tag(array(
								'tag' => $tag
								)); 
						} 
				}
		}	
		
		return $rsp;
		
	}

	#################################################################
	
	function tumblr_tags_get_by_tag($tag) {
		
		$enc_tag = AddSlashes($tag);
		
		$sql = "SELECT * FROM TumblrTags WHERE tag='{$enc_tag}'";
		return db_single(db_fetch($sql));
	}

	#################################################################
	
	function tumblr_tags_get_tags() {
				
		$sql = "SELECT * FROM TumblrTags";
		return db_fetch($sql);
	}

?>