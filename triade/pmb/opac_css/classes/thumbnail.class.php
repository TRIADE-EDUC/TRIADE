<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: thumbnail.class.php,v 1.8 2023/08/16 14:02:17 dbellamy Exp $

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
					static::$image[$code."_".$thumbnail_url] = "<img class='vignetteimg align_right' src='".static::get_url_image($code, $thumbnail_url)."' alt=\"".$title_image_ok."\" style='max-width : 140px; max-height: 200px;' >";
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

	public static function get_parameter_img_folder_id($object_type = 'record') {
	    switch ($object_type) {
	        case 'authority':
	            global $pmb_authority_img_folder_id;
	            return $pmb_authority_img_folder_id;
	            break;
	        case 'docnum':
	            global $pmb_docnum_img_folder_id;
	            return $pmb_docnum_img_folder_id;
	            break;
	        default:
	            global $pmb_notice_img_folder_id;
	            return $pmb_notice_img_folder_id;
	            break;
	    }
	}

	public static function get_thumbnail_url($object_id, $object_type) {
	    global $opac_url_base;
	    $object_id = intval($object_id);
	    $thumbnail_url = $opac_url_base."getimage.php?noticecode=&vigurl=";
	    switch ($object_type) {
	        case 'shelve':
	            $thumbnail_url .= "&etagere_id=".$object_id;
	            break;
	        case 'authority':
	            $thumbnail_url .= "&authority_id=".$object_id;
	            break;
	        case 'record':
	        default:
	            $thumbnail_url .= "&notice_id=".$object_id;
	            break;
	    }
	    return $thumbnail_url;
	}

	public static function get_img_prefix($object_type = 'record') {
	    switch ($object_type) {
	        case 'shelve':
	            return "img_etag_";
	            break;
	        case 'authority':
	            return "img_authority_";
	            break;
	        case 'docnum':
	            return "img_docnum_";
	            break;
	        default:
	            return "img_";
	            break;
	    }
	}
} // fin de déclaration de la classe thumbnail