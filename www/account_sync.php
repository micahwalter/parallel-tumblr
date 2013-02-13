<?
	#
	# $Id$
	#

	include('include/init.php');
	loadlib("tumblr_users");
	loadlib("tumblr_blogs");
	loadlib("tumblr_following");
	loadlib("tumblr_followers");
	loadlib("tumblr_posts");
	loadlib("tumblr_api");

	$GLOBALS['smarty']->assign('page_title', 'Sync');

	if ($GLOBALS['cfg']['user']['id']){
		$tumblr_user = tumblr_users_get_by_username($GLOBALS['cfg']['user']['username']);
		$access_token = array(
			'oauth_token' => $tumblr_user['oauth_token'],
			'oauth_token_secret' => $tumblr_user['oauth_secret']
			);
		
		$userinfo = tumblr_api_get_call($access_token, 'user/info');		
		
		$rsp = tumblr_blogs_sync_blogs($userinfo);
		
		$offset = 0;
		$following = tumblr_api_get_call($access_token, 'user/following');
		$total_following = $following->response->total_blogs;
		
		while($offset < $total_following) {
			$params = array(
				'offset' => $offset,
				'limit' => 20
			);
		
			$following = tumblr_api_get_call($access_token, 'user/following', $params );
			$rsp = tumblr_following_sync_following($following);
			$offset = $offset + 20;
		};
		
		###### get followers for all your blogs
		$blogs_count = count($userinfo->response->user->blogs);
		$i = 0;
		while ($i < $blogs_count ) {
			$regex = '/(?<!href=["\'])http:\/\//';
			$base_hostname = preg_replace($regex, '', $userinfo->response->user->blogs[$i]->url);		
			
			$offset = 0;
			$api_key = $GLOBALS['cfg']['tumblr_api_key'];
			$params = array(
				'api_key' => $api_key,
				'offset' => $offset,
				'limit' => 20
			);
		
			$followers = tumblr_api_get_call($access_token, 'blog/' . $base_hostname . 'followers', $params);
			$total_followers = $followers->response->total_users;
		
			while($offset < $total_followers) {	
				$params = array(
					'api_key' => $api_key,
					'offset' => $offset,
					'limit' => 20
				);
		
			$followers = tumblr_api_get_call($access_token, 'blog/' . $base_hostname . 'followers' , $params );
			$rsp = tumblr_followers_sync_followers($followers, $userinfo->response->user->blogs[$i]->name);
			$offset = $offset + 20;
			};
			
			$offset = 0;
			$api_key = $GLOBALS['cfg']['tumblr_api_key'];
			$params = array(
				'api_key' => $api_key,
				'offset' => $offset,
				'limit' => 20
			);			
			
			$posts = tumblr_api_get_call($access_token, 'blog/' . $base_hostname . 'posts' , $params);
			$total_posts = $posts->response->blog->posts;
			
			while($offset < $total_posts) {	
				$params = array(
					'api_key' => $api_key,
					'offset' => $offset,
					'limit' => 20
				);
			$posts = tumblr_api_get_call($access_token, 'blog/' . $base_hostname . 'posts' , $params);
			$rsp = tumblr_posts_sync_posts($posts);
			$offset = $offset + 20;
			}
			
			$i++;
		};		
		
		
				
		###### get avatar
		$params = array(
			'api_key' => $api_key,
		);
		
		$avatar = tumblr_api_get_avatar($access_token, 'blog/micahwalter.tumblr.com/avatar' , $params );
		$avatar = $avatar->response->avatar_url;		
		$GLOBALS['smarty']->assign('userinfo', $avatar);
		
		
		$GLOBALS['smarty']->assign('posts', $rsp);
	}
		
	#
	# output
	#

	$smarty->display('page_account_sync.txt');