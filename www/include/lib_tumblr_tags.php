<?php
	
	loadlib("artisanal_integers");

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

		$post_id = AddSlashes($tag['artisanal_id']);
		$where = "artisanal_id='{$artisanal_id}'";

		$rsp = db_update('TumblrTags', $hash, $where);

		return $rsp;
		
	}

	#################################################################

	function tumblr_tags_sync_tags($tags){

		if (! $tags) {
			return 0;
		}

		foreach ($tags as $element){
						
				$theTag = tumblr_tags_get_by_tag($element);
			
				if(! $theTag ) {
					$provider = 'brooklyn';
					$artisanal = artisanal_integers_create($provider);
				
					$rsp = tumblr_tags_create_tag(array(
						'artisanal_id' => $artisanal['integer'],
						'tag' => $element
						)); 
				} else {
					$rsp = tumblr_tags_update_tag(array(
						'tag' => $element	
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

	#################################################################
	
	function tumblr_tags_get_by_tag($tag) {
		
		$enc_tag = AddSlashes($tag);
		
		$sql = "SELECT * FROM TumblrTags WHERE tag='{$enc_tag}'";
		return db_fetch($sql);
	}

	#################################################################
	
	function tumblr_tags_get_tags() {
				
		$sql = "SELECT * FROM TumblrTags";
		return db_fetch($sql);
	}

?>