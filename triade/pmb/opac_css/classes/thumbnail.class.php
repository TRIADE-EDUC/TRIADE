<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: thumbnail.class.php,v 1.4 2018-03-28 06:57:17 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class thumbnail {
	
	protected static $image;
	
	protected static $url_image;
		
	public static function get_image($code, $thumbnail_url) {
		global $charset;
		global $opac_show_book_pics;
		global $opac_book_pics_url;
		global $opac_book_pics_msg;
		
		if(!isset(static::$image[$code."_".$thumbnail_url])) {
			if ($code || $thumbnail_url) {
				if ($opac_show_book_pics=='1' && ($opac_book_pics_url || $thumbnail_url)) {
					if ($thumbnail_url) {
						$title_image_ok="";
					} else {
						$title_image_ok = htmlentities($opac_book_pics_msg, ENT_QUOTES, $charset);
					}
					static::$image[$code."_".$thumbnail_url] = "<img class='vignetteimg align_right' src='".static::get_url_image($code, $thumbnail_url)."' alt=\"".$title_image_ok."\" hspace='4' vspace='2' style='max-width : 140px; max-height: 200px;' >";
				} else {
					static::$image[$code."_".$thumbnail_url] = "";
				}
			} else {
				static::$image[$code."_".$thumbnail_url] = "";
			}
		}
		return static::$image[$code."_".$thumbnail_url];
	}
	
	public static function get_url_image($code, $thumbnail_url) {
		global $opac_show_book_pics;
		global $opac_book_pics_url;
		global $opac_url_base;
		
		if(!isset(static::$url_image[$code."_".$thumbnail_url])) {
			if (($code || $thumbnail_url) && ($opac_show_book_pics=='1' && ($opac_book_pics_url || $thumbnail_url))) {
				static::$url_image[$code."_".$thumbnail_url] = getimage_url($code, $thumbnail_url);
			} else {
				static::$url_image[$code."_".$thumbnail_url] = '';
			}
		}
		return static::$url_image[$code."_".$thumbnail_url];
	}
} // fin de déclaration de la classe thumbnail