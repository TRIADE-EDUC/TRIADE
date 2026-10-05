<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: thumbnail.class.php,v 1.24.2.3.2.1 2025/02/06 10:53:23 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Thumbnail\Models\ThumbnailSourcesHandler;
use Pmb\Common\Library\Image\CacheImage;
use Pmb\Common\Helper\HelperEntities;
use Pmb\Common\Library\CSRF\CollectionCSRF;

global $include_path;
require_once($include_path."/templates/thumbnail.tpl.php");

class thumbnail {

	protected static $image;

	protected static $url_image;

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

	public static function get_parameter_img_pics_max_size($object_type = 'record') {
		switch ($object_type) {
			case 'authority':
				global $pmb_authority_img_pics_max_size;
				return $pmb_authority_img_pics_max_size;
				break;
			default:
				global $pmb_notice_img_pics_max_size;
				return $pmb_notice_img_pics_max_size;
				break;
		}
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

	public static function create($object_id, $object_type = 'record') {
		$thumbnail_url = '';
		// vignette de la notice uploadé dans un répertoire
		if(isset($_FILES['f_img_load']['name']) && $_FILES['f_img_load']['name'] && static::get_parameter_img_folder_id($object_type) && $object_id){
			$query = "select repertoire_path from upload_repertoire where repertoire_id ='".static::get_parameter_img_folder_id($object_type)."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row=pmb_mysql_fetch_object($result);
				$filename_output=$row->repertoire_path.static::get_img_prefix($object_type).$object_id;
			}
			if (($fp=@fopen($_FILES['f_img_load']['tmp_name'], "rb")) && $filename_output) {
				$image="";
				$size=0;
				$flag=true;
				while (!feof($fp)) {
					$image.=fread($fp,4096);
					$size=strlen($image);
				}
				fclose($fp);
				if ($img=imagecreatefromstring($image)) {
					$parameter_img_pics_max_size = static::get_parameter_img_pics_max_size($object_type);
					if(!($parameter_img_pics_max_size*1)) $parameter_img_pics_max_size=100;
					$redim=false;
					if (imagesx($img) >= imagesy($img)) {
						if(imagesx($img) <= $parameter_img_pics_max_size){
							$largeur=imagesx($img);
							$hauteur=imagesy($img);
						}else{
							$redim=true;
							$largeur=$parameter_img_pics_max_size;
							$hauteur = ($largeur*imagesy($img))/imagesx($img);
						}
					} else {
						if(imagesy($img) <= $parameter_img_pics_max_size){
							$hauteur=imagesy($img);
							$largeur=imagesx($img);
						}else{
							$redim=true;
							$hauteur=$parameter_img_pics_max_size;
							$largeur = ($hauteur*imagesx($img))/imagesy($img);
						}
					}
					if($redim){
						$dest = imagecreatetruecolor($largeur,$hauteur);

						// On gere la transparence
						imageSaveAlpha($dest, true);
						imageAlphaBlending($dest, false);

						imagecopyresampled($dest, $img, 0, 0, 0, 0, $largeur, $hauteur,imagesx($img),imagesy($img));
						imagepng($dest, $filename_output, 9, (defined('PNG_ALL_FILTERS') ? PNG_ALL_FILTERS : null));
						imagedestroy($dest);
					}else{
						imagepng($img, $filename_output, 9, (defined('PNG_ALL_FILTERS') ? PNG_ALL_FILTERS : null));
					}
					imagedestroy($img);
					$thumbnail_url=static::get_thumbnail_url($object_id, $object_type);
					$manag_cache=array();
					switch ($object_type) {
						case 'shelve':
							$manag_cache = getimage_cache(0, $object_id);
							break;
						case 'authority':
							$manag_cache = getimage_cache(0, 0, $object_id);
							break;
						case 'record':
						default:
							$manag_cache = getimage_cache($object_id);
							break;
					}
					//On détruit l'image si elle est en cache
					global $pmb_img_cache_folder;
					if ($pmb_img_cache_folder) {
						if($manag_cache["location"] && preg_match("#^".$pmb_img_cache_folder."(.+)$#",$manag_cache["location"])){
							unlink($manag_cache["location"]);
							global $opac_img_cache_folder;
							if($opac_img_cache_folder && file_exists(str_replace($pmb_img_cache_folder, $opac_img_cache_folder, $manag_cache["location"]))){
								unlink(str_replace($pmb_img_cache_folder, $opac_img_cache_folder, $manag_cache["location"]));
							}
						}
					}
				}
			}
		}
		return $thumbnail_url;
	}

	/**
	 * Permet de supprimer la vignette en cache pour une entite donnee
	 *
	 * @param int $object_id
	 * @param int $object_type
	 * @return boolean
	 */
	public static function clearCache(int $object_id, int $object_type) {
	    $namespace = HelperEntities::get_entities_namespace();
	    if (!isset($namespace[$object_type])) {
	        throw new \InvalidArgumentException("Unknown object type !");
	    }

		global $opac_img_cache_type, $pmb_img_cache_type;
		if(!empty($opac_img_cache_type) && in_array($opac_img_cache_type, ['png', 'webp'])) {
			global $pmb_img_cache_type;
			$pmb_img_cache_type = $opac_img_cache_type;
		} else {
			// Le parametre pmb_img_cache_type n'existe pas il faut le definir
			$pmb_img_cache_type = "png";
		}

	    $filename = CacheImage::generateFilename($namespace[$object_type], $object_id);
		return CacheImage::deleteWithoutExtension($filename);
	}

	public static function create_from_base64($object_id, $object_type = 'record', $thumbnail_base64='') {
		// vignette de la notice uploadé dans un répertoire
		if(static::get_parameter_img_folder_id($object_type) && $object_id){
			$query = "select repertoire_path from upload_repertoire where repertoire_id ='".static::get_parameter_img_folder_id($object_type)."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row=pmb_mysql_fetch_object($result);
				$filename_output=$row->repertoire_path.static::get_img_prefix($object_type).$object_id;
			}
			if(is_dir($row->repertoire_path) && $filename_output) {
				$details = explode(',', $thumbnail_base64);
				$ini =substr($details[0], 11);
				$type = explode(';', $ini);
				if($type[0]) {
					$created = file_put_contents($filename_output, base64_decode($details[1]));
					if($created) {
						return true;
					}
				}
			}
		}
		return false;
	}

	//Suppression de la vignette de la notice si il y en a une d'uploadée
	public static function delete($object_id, $object_type = 'record') {
		if(static::get_parameter_img_folder_id($object_type)){
			$query = "select repertoire_path from upload_repertoire where repertoire_id ='".static::get_parameter_img_folder_id($object_type)."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$rep=pmb_mysql_fetch_object($result);
				$img=$rep->repertoire_path.static::get_img_prefix($object_type).$object_id;
				@unlink($img);
			}
		}
	}

	public static function is_valid_folder($object_type='record') {
		$is_valid = false;
		if(static::get_parameter_img_folder_id($object_type)){
			$req = "select repertoire_path from upload_repertoire where repertoire_id ='".static::get_parameter_img_folder_id($object_type)."'";
			$res = pmb_mysql_query($req);
			if(pmb_mysql_num_rows($res)){
				$rep=pmb_mysql_fetch_object($res);
				if(is_dir($rep->repertoire_path)){
					$is_valid = true;
				}
			}
		}
		return $is_valid;
	}

	public static function get_message_folder($object_type='record') {
		global $msg;

		$message_folder="";
		if(static::get_parameter_img_folder_id($object_type)){
			if(!static::is_valid_folder($object_type)){
				if (SESSrights & ADMINISTRATION_AUTH){
					$requete = "select * from parametres where gestion=0 and type_param='pmb' and sstype_param='notice_img_folder_id' ";
					$res = pmb_mysql_query($requete);
					if($param=pmb_mysql_fetch_object($res)) {
						$message_folder=" <a class='erreur' href='./admin.php?categ=param&action=modif&id_param=".$param->id_param."' >".$msg['notice_img_folder_admin_no_access']."</a> ";
					}
				}else{
					$message_folder=$msg['notice_img_folder_no_access'];
				}
			}
		}
		return $message_folder;
	}

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
					static::$image[$code."_".$thumbnail_url] = "<img class='vignetteimg align_right' src='".static::get_url_image($code, $thumbnail_url)."' title=\"".$title_image_ok."\" style='max-width : 140px; max-height: 200px;' />";
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

		if(!isset(static::$url_image[$code."_".$thumbnail_url])) {
			if ($code || $thumbnail_url) {
				if ($opac_show_book_pics=='1' && ($opac_book_pics_url || $thumbnail_url)) {
					static::$url_image[$code."_".$thumbnail_url] = getimage_url($code, $thumbnail_url);
				} else {
					static::$url_image[$code."_".$thumbnail_url] = get_url_icon("no_image.jpg");
				}
			} else {
				static::$url_image[$code."_".$thumbnail_url] = '';
			}
		}
		return static::$url_image[$code."_".$thumbnail_url];
	}

	public static function get_js_function_chklnk_tpl() {
		global $js_function_chklnk_tpl;
		
		$chklnk_tpl = $js_function_chklnk_tpl;
		
		$collectionCSRF = new CollectionCSRF();
		$tokens = json_encode($collectionCSRF->getArrayTokens());
		
		$chklnk_tpl = str_replace('!!tokens_csrf!!', $tokens, $chklnk_tpl);
		
		return $chklnk_tpl;
	}

	public static function get_form($object_type, $value = '') {
		global $msg, $charset;
		$form = static::get_js_function_chklnk_tpl();
		$form .= "
			<div id='el4Child_0' title='".htmlentities($msg["notice_thumbnail_url"],ENT_QUOTES, $charset)."' movable='yes'>
				<!--    URL vignette speciale    -->
				<div id='el4Child_0a' class='row'>
					<label for='".$object_type."_thumbnail_url' class='etiquette'>".$msg['notice_thumbnail_url']."</label>
				</div>
				<div id='el4Child_0b' class='row'>
					<div id='f_thumbnail_check' style='display:inline'></div>
					<input type='text' class='saisie-80em' id='".$object_type."_thumbnail_url' name='".$object_type."_thumbnail_url' rows='1' wrap='virtual' value=\"".$value."\" onchange='chklnk_f_thumbnail_url(this);' />
				</div>
			</div>";
		if(static::get_parameter_img_folder_id($object_type)){
			$message_folder = static::get_message_folder($object_type);
			$form .= "
				<div id='el4Child_1' title='".htmlentities($msg['notice_img_load'],ENT_QUOTES, $charset)."' movable='yes'>
					<!--    Vignette upload    -->
					<div id='el4Child_1a' class='row'>
						<label for='f_img_load' class='etiquette'>$msg[notice_img_load]</label>".$message_folder."
					</div>
					<div id='el4Child_1b' class='row'>
						<input type='file' class='saisie-80em' id='f_img_load' name='f_img_load' rows='1' wrap='virtual' value='' />
					</div>
				</div>";
		}
		return $form;
	}

	public static function do_image(&$entree, $notice) {
		global $charset;
		global $pmb_book_pics_show ;
		global $pmb_book_pics_msg;
		// pour url OPAC en diff DSI
		global $prefix_url_image ;
		global $pmb_url_base;
		global $use_opac_url_base;
		if(!isset($prefix_url_image)){
			$prefix_url_image = "./";
		}
		$notice_id = 0;
		if (!empty($notice->notice_id)) {
		    $notice_id = intval($notice->notice_id);
		}
		if ($pmb_book_pics_show) {
			$url_image_ok = '';
		    if (!empty($notice->is_external)) {
		        $thumbnail_type = TYPE_EXTERNAL;
				if (!empty($notice->code)) {
					$url_image_ok = getimage_url($notice->code);
		    	}
		    } else {
				$thumbnail_type = TYPE_NOTICE;
				$thumbnailSourcesHandler = new ThumbnailSourcesHandler();
			    if($use_opac_url_base) {
			        $url_image_ok = $thumbnailSourcesHandler->generateSrcBase64($thumbnail_type, $notice_id);
			    } else {
		        	$url_image_ok = $thumbnailSourcesHandler->generateUrl($thumbnail_type, $notice_id);
		    	}
			}

			if($pmb_book_pics_msg) {
				$title_image_ok = htmlentities($pmb_book_pics_msg, ENT_QUOTES, $charset);
			}else {
				$title_image_ok = htmlentities($notice->tit1, ENT_QUOTES, $charset);
			}
			$image = "<img class='img_notice align_right' id='PMBimagecover".$notice->notice_id."' src='".$url_image_ok."' loading='lazy' alt=\"".$title_image_ok."\" />";
		} else {
			$image="";
		}
		$entree = "
        <table style='width:100%' role='presentation'>
            <tr>
                <td style='vertical-align:top'>$entree</td>";
		if ($image) {
			$entree .= "<td style='vertical-align:top' class='align_right'>$image</td>" ;
		}
		$entree .= "
            </tr>
        </table>" ;
	}

	public static function get_thumbnail_url($object_id, $object_type) {
	    global $opac_url_base;
	    global $pmb_url_base;
	    global $use_opac_url_base;

	    $url_base = $pmb_url_base;
	    if (!empty($use_opac_url_base)) {
	        $url_base = $opac_url_base;
	    }
	    $object_id = intval($object_id);
	    $thumbnail_url = $url_base."getimage.php?noticecode=&vigurl=";
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
} // fin de déclaration de la classe thumbnail