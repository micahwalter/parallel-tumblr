<?php
	
	loadlib("artisanal_integers");

	#################################################################

	function tumblr_photos_create_photo($photo){

		$hash = array();

		foreach ($photo as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$rsp = db_insert('TumblrPhotos', $hash);

		return $rsp;
		
	}

	#################################################################

	function tumblr_photos_update_photo($photo){

		$hash = array();
		
		foreach ($photo as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$photo_artisanal_id = AddSlashes($photo['photo_artisanal_id']);
		$where = "photo_artisanal_id='{$photo_artisanal_id}'";

		$rsp = db_update('TumblrPhotos', $hash, $where);

		return $rsp;
		
	}

	#################################################################

	function tumblr_photos_sync_photos($photos){

		if (! $photos) {
			return 0;
		}

		foreach ($photos as $element){
						
				$photo = tumblr_photos_get_by_url($element->original_zise_url);
			
				if(! $photo ) {
					$provider = 'brooklyn';
					$artisanal = artisanal_integers_create($provider);
				
					$rsp = tumblr_photos_create_photo(array(
						'photo_artisanal_id' => $artisanal['integer'],
						'caption' => $element->caption,
						'original_size_url' => $element->original_size->url,
						'original_width' => $element->original_size->width,
						'original_height' => $element->original_size->height
						)); 
				} else {
					$rsp = tumblr_photos_update_photo(array(
						'caption' => $element->caption,
						'original_size_url' => $element->original_size->url,
						'original_width' => $element->original_size->width,
						'original_height' => $element->original_size->height
					));	
				}
		}
		return $rsp;
		
	}

	#################################################################
	
	function tumblr_photos_get_by_artisanal_id($artisanal_id) {
		
		$enc_artisanal_id = AddSlashes($artisanal_id);
		
		$sql = "SELECT * FROM TumblrPhotos WHERE photo_artisanal_id='{$enc_artisanal_id}'";
		return db_single(db_fetch($sql));
	}

	#################################################################
	
	function tumblr_photos_get_by_url($url) {
		
		$enc_url = AddSlashes($url);
		
		$sql = "SELECT * FROM TumblrPhotos WHERE original_size_url='{$enc_url}'";
		return db_single(db_fetch($sql));
	}

?>