<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bbcode.inc.php,v 1.6 2019/06/18 07:57:08 btafforeau Exp $

require_once ($include_path . "/misc.inc.php");
	
function handle_url_tag($url, $link = '', $bbcode = false){
	
	$full_url = str_replace(array(' ', '\'', '`', '"'), array('%20', '', '', ''), $url);
	if (strpos($url, 'www.') === 0)			// If it starts with www, we add http://
		$full_url = 'http://'.$full_url;
	else if (strpos($url, 'ftp.') === 0)	// Else if it starts with ftp, we add ftp://
		$full_url = 'ftp://'.$full_url;
	else if (!preg_match('#^([a-z0-9]{3,6})://#', $url)) 	// Else if it doesn't start with abcdef://, we add http://
		$full_url = 'http://'.$full_url;

	
	if (!$bbcode)	$link = ($link == '' || $link == $url) ? ((strlen($url) > 55) ? substr($url, 0 , 39).'...'.substr($url, -10) : $url) : stripslashes($link);

	if ($bbcode){
		if ($full_url == $link)
			return '[url]'.$link.'[/url]';
		else
			return '[url='.$full_url.']'.$link.'[/url]';
	}
	else
		return '<a href="'.$full_url.'">'.$link.'</a>';
}
	
function handle_img_tag($url, $is_signature = false, $alt = null) {
	
	if ($alt == null)	$alt = $url;
	$img_tag = '<span ><img src="'.$url.'" /></span>';
	return $img_tag;
}	
	
function do_bbcode($text){
	$pattern = $replace = $patterns_and_callbacks = array();
	
	$text=nl2br($text);
	
	if (strpos($text, '[quote') !== false){
		$text = preg_replace_callback('#\[quote=(&quot;|"|\'|)(.*?)\\1\]#', function($matches) {
			return "</p><div class='quotebox'><cite>".str_replace(array('[', '\\"'), array('&#91;', '"'), $matches[2])." ".$lang_common['wrote'].":</cite><blockquote><p>";
		}, $text);
		$text = preg_replace('#\[quote\]\s*#', '</p><div class="quotebox"><blockquote><p>', $text);
		$text = preg_replace('#\s*\[\/quote\]#', '</p></blockquote></div><p>', $text);
	}
	
	$patterns_and_callbacks['#\[img\]((ht|f)tps?://)([^\s<"]*?)\[/img\]#'] = function($matches){
		return handle_img_tag($matches[1].$matches[3], false);
	};
	$patterns_and_callbacks['#\[img=([^\[]*?)\]((ht|f)tps?://)([^\s<"]*?)\[/img\]#'] = function($matches){
		return handle_img_tag($matches[2].$matches[4], false, $matches[1]);
	};

	$pattern[] = '#\[b\](.*?)\[/b\]#ms';
	$pattern[] = '#\[i\](.*?)\[/i\]#ms';
	$pattern[] = '#\[u\](.*?)\[/u\]#ms';
	$pattern[] = '#\[code\](.*?)\[/code\]#ms';
	$pattern[] = '#\[colou?r=([a-zA-Z]{3,20}|\#[0-9a-fA-F]{6}|\#[0-9a-fA-F]{3})](.*?)\[/colou?r\]#ms';
	$pattern[] = '#\[h\](.*?)\[/h\]#ms';

	$replace[] = '<strong>$1</strong>';
	$replace[] = '<em>$1</em>';
	$replace[] = '<span class="bbu">$1</span>';
	$replace[] = '<pre>$1</pre>';
	$replace[] = '<span style="color: $1">$2</span>';
	$replace[] = '</p><h5>$1</h5><p>';


	$patterns_and_callbacks['#\[url\]([^\[]*?)\[/url\]#'] = function($matches){
		return handle_url_tag($matches[1]);
	};
	$patterns_and_callbacks['#\[url=([^\[]+?)\](.*?)\[/url\]#'] = function($matches){
		return handle_url_tag($matches[1], $matches[2]);
	};
	$pattern[] = '#\[email\]([^\[]*?)\[/email\]#';
	$pattern[] = '#\[email=([^\[]+?)\](.*?)\[/email\]#';

	$replace[] = '<a href="mailto:$1">$1</a>';
	$replace[] = '<a href="mailto:$1">$2</a>';
	
	$pattern[] = '#\[red\](.*?)\[/red\]#ms';
	$pattern[] = '#\[li\](.*?)\[/li\]#ms';
	
	$replace[] = '<font color=\'#FF0000\'>$1</font>';
	$replace[] = '<li style=\'list-style-type:disc;\'>$1</li>';

	$text = preg_replace($pattern, $replace, $text);
	
	if (function_exists("preg_replace_callback_array")) {
		// Cette fonction n'arrive qu'en PHP7
		$text = preg_replace_callback_array($patterns_and_callbacks, $text);
	} else {
		foreach ($patterns_and_callbacks as $pat => $callback) {
			$text = preg_replace_callback($pat, $callback, $text);
		}
	}
	
	return $text;
}
?>