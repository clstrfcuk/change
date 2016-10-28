<?php

	$pspSitemapVideosOpt = array(
		'youtube'		=> array(
			'mysql'		=> 'youtube\.com|youtube-nocookie\.com|youtu\.be',
			'default'	=> '
				(?:
					(?:
						youtu\.be\/
						|
						youtube\.com\/
						|
						youtube-nocookie\.com\/
					)
					(?:
						v\/
						|
						watch\?.*v=|embed\/
						|
					)
				)
				([\w\-]+)
			'
		)
		,'dailymotion' 	=> array(
			'mysql'		=> 'dailymotion\.com|dai\.ly',
			'default'	=> '
				(?:
					(?:
						dailymotion\.com\/
						(?:embed\/|)(?:video\/)
					)
					|
					(?:
						dai\.ly\/
					)
				)
				([a-zA-Z0-9\-]+)
			'
		)
		,'vimeo' 		=> array(
			'mysql'		=> 'vimeo\.com',
			'default'	=> '
				(?:
					(?:
						player\.vimeo\.com
						(?:[\/\w]*\/videos?)?\/
					)
					|
					(?:
						(?:https?):\/\/
						(?:[\w]+\.)*vimeo\.com\/
						(?:moogaloop\.swf\?clip_id=)?
					)
				)
				([0-9\-]+)
			'
		)
		,'metacafe'		=> array(
			'mysql'		=> 'metacafe\.com',
			'default'	=> '
				(?:
					metacafe\.com\/
					(?:embed|watch)\/
				)
				([0-9\-]+)
			'
		)
		,'veoh'			=> array(
			'mysql'		=> 'veoh\.com',
			'default'	=> '
				(?:
					veoh\.com\/
					(?:videos|watch)\/
				)
				([\w\-]+)
			'
		)
		,'screenr'		=> array(
			'mysql'		=> 'screenr\.com',
			'default'	=> '
				(?:
					screenr\.com
					(?:\/embed)?\/
				)
				([\w\-]+)
			'
		)
		,'blip'			=> array(
			'mysql'		=> 'blip\.tv',
			'default'	=> '
				(?:
					blip\.tv\/
					(?:
						play\/
						|
						[\w\-]+\/[\w\-]+\-
						|
					)
					([\w\-]+)
				)
			'
		)
		,'dotsub'		=> array(
			'mysql'		=> 'dotsub\.com',
			'default'	=> '
				(?:
					dotsub\.com\/
					(?:view|media)\/
				)
				([\w\-]+)
				(?:\/embed\/|)
			'
		)
		,'viddler'		=> array(
			'mysql'		=> 'viddler\.com',
			'default'	=> '
				(?:
					viddler\.com
					(?:\/v|\/embed)\/
				)
				([\w\-]+)
			'
		)
		,'wistia'		=> array(
			'mysql'		=> 'wistia\.com',
			'default'	=> '
				(?:
					(?:
						fast\.wistia\.com\/embed\/iframe
						|
						home\.wistia\.com\/medias
					)
					\/
				)
				([\w\-]+)
			'
		)
		,'vzaar'		=> array(
			'mysql'		=> 'vzaar\.com',
			'default'	=> '
				(?:
					(?:
						vzaar\.com\/videos
						|
						view\.vzaar\.com
					)
					\/
					([\w\-]+)
					(video|player|download|flashplayer)\/?
				)
			'
		)
		,'flickr'		=> array(
			'mysql'		=> 'flickr\.com',
			'default'	=> '
				(?:
					(?:
						flickr\.com\/.*
					)
					\/
					(\d+)
					\/
				)
			'
		)
	);
