<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum.inc.php,v 1.123 2018-11-27 13:17:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/curl.class.php");
require_once("$class_path/indexation_docnum.class.php");
require_once("$class_path/upload_folder.class.php");
require_once("$class_path/explnum.class.php");
require_once("$class_path/acces.class.php");
require_once ($class_path."/map/map_locations_controler.class.php");
require_once($class_path."/cache_factory.class.php");

if (!function_exists('file_put_contents')) {
    function file_put_contents($filename, $data) {
        $f = @fopen($filename, 'w');
        if (!$f) {
            return false;
        } else {
            $bytes = fwrite($f, $data);
            fclose($f);
            return $bytes;
        }
    }
}


// charge le tableau des extensions/mimetypes, on en a besoin en maj comme en affichage
function create_tableau_mimetype() {
	
	global $lang, $charset, $KEY_CACHE_FILE_XML;
	global $include_path,$base_path;
	global $_mimetypes_bymimetype_, $_mimetypes_byext_ ;
	
	if (!empty($_mimetypes_bymimetype_) && sizeof($_mimetypes_bymimetype_)) return;
	$_mimetypes_bymimetype_ = array();
	$_mimetypes_byext_ = array();

	if (file_exists($include_path."/mime_types/".$lang."_subst.xml")){
		$fic_mime_types = $include_path."/mime_types/".$lang."_subst.xml";
	}else{
		$fic_mime_types = $include_path."/mime_types/".$lang.".xml";	
	}
	$fileInfo = pathinfo($fic_mime_types);
	$fileName = preg_replace("/[^a-z0-9]/i","",$fileInfo['dirname'].$fileInfo['filename'].$charset);
	$tempFile = $base_path."/temp/XML".$fileName.".tmp";
	$dejaParse = false;
	
	$cache_php=cache_factory::getCache();
	$key_file="";
	if ($cache_php) {
		$key_file=getcwd().$fileName.filemtime($fic_mime_types);
		$key_file=$KEY_CACHE_FILE_XML.md5($key_file);
		if($tmp_key = $cache_php->getFromCache($key_file)){
			if($cache = $cache_php->getFromCache($tmp_key)){
				if(count($cache) == 2){
					$_mimetypes_bymimetype_= $cache[0];
					$_mimetypes_byext_= $cache[1];
					$dejaParse = true;
				}
			}
		}
			
	}else{
		if (file_exists($tempFile) ) {
			//Le fichier XML original a-t-il été modifié ultérieurement ?
			if (filemtime($fic_mime_types) > filemtime($tempFile)) {
				//on va re-générer le pseudo-cache
				unlink($tempFile);
			} else {
				$dejaParse = true;
			}
		}
		if ($dejaParse) {
			$tmp = fopen($tempFile, "r");
			$cache = unserialize(fread($tmp,filesize($tempFile)));
			fclose($tmp);
			if(count($cache) == 2){
				$_mimetypes_bymimetype_= $cache[0];
				$_mimetypes_byext_= $cache[1];
			}else{
				//SOUCIS de cache...
				unlink($tempFile);
				$dejaParse=false;
			}
		}
	}
	
	if(!$dejaParse){
		require_once ("$include_path/parser.inc.php") ;
		$fonction = array ("MIMETYPE" => "__mimetype__");
		_parser_($fic_mime_types, $fonction, "MIMETYPELIST" ) ;
		
		if ($key_file) {
			$key_file_content=$KEY_CACHE_FILE_XML.md5(serialize(array($_mimetypes_bymimetype_,$_mimetypes_byext_)));
			$cache_php->setInCache($key_file_content, array($_mimetypes_bymimetype_,$_mimetypes_byext_));
			$cache_php->setInCache($key_file,$key_file_content);
		}else{
			$tmp = fopen($tempFile, "wb");
			fwrite($tmp,serialize(array(
				$_mimetypes_bymimetype_,
				$_mimetypes_byext_
			)));
			fclose($tmp);
		}
	}
}


function __mimetype__($param) {
	
	global $_mimetypes_bymimetype_, $_mimetypes_byext_ ;
	
	$mimetype_rec = array() ;
	$mimetype_rec["plugin"] = $param["PLUGIN"] ;
	$mimetype_rec["icon"] = $param["ICON"] ;
	$mimetype_rec["label"] = (isset($param["LABEL"]) ? $param["LABEL"] : '');
	$mimetype_rec["embeded"] = $param["EMBEDED"] ;
	
	$_mimetypes_bymimetype_[$param["NAME"]] = $mimetype_rec ;
	
	for ($i=0; $i<count($param["EXTENSION"]) ; $i++  ) {
		$mimetypeext_rec = array() ;
		$mimetypeext_rec = $mimetype_rec ;
		$mimetypeext_rec["mimetype"] = $param["NAME"] ;
		if (isset($param["EXTENSION"][$i]["LABEL"])) {
			$mimetypeext_rec["label"] =  $param["EXTENSION"][$i]["LABEL"] ;
		}
		$_mimetypes_byext_[$param["EXTENSION"][$i]["value"]] = $mimetypeext_rec ;
	}
}

function extension_fichier($fichier) {
	
	$f = strrev($fichier);
	$ext = substr($f, 0, strpos($f,"."));
	return strtolower(strrev($ext));
}

function icone_mimetype ($mimetype, $ext) {
	
	global $_mimetypes_bymimetype_, $_mimetypes_byext_ ;
	// trouve l'icone associee au mimetype
	// sinon trouve l'icone associee a l'extension
	if (isset($_mimetypes_bymimetype_[$mimetype]["icon"]) && $_mimetypes_bymimetype_[$mimetype]["icon"]) return $_mimetypes_bymimetype_[$mimetype]["icon"] ;
	if (isset($_mimetypes_byext_[$ext]["icon"]) && $_mimetypes_byext_[$ext]["icon"]) return $_mimetypes_byext_[$ext]["icon"] ;
	return "unknown.gif" ;
}

function trouve_mimetype ($fichier, $ext='') {
	
	global $_mimetypes_byext_ ;
	
	if ($ext!='') {
		// chercher le mimetype associe a l'extension : si trouvee nickel, sinon : ""
		if ($_mimetypes_byext_[$ext]["mimetype"]) return $_mimetypes_byext_[$ext]["mimetype"] ;
	}
	if (extension_loaded('mime_magic')) {
		$mime_type = mime_content_type($fichier) ;
		if ($mime_type) return $mime_type ;
	}
	return '';
}

function reduire_image ($userfile_name) {
	global $pmb_vignette_x;
	global $pmb_vignette_y;
	global $base_path;
	global $pmb_curl_available;
	
	if (!$pmb_vignette_x) $pmb_vignette_x = 100 ;
	if (!$pmb_vignette_y) $pmb_vignette_y = 100 ;
	$src_image = '';
	$fichier_tmp = '';
	
	if(file_exists("$base_path/temp/$userfile_name")){
		$bidon = "$base_path/temp/$userfile_name";
		$source_file = realpath($bidon).'[0]';
	} else {
		$bidon = $userfile_name;
		//Il s'agit d'une url, on copie le fichier en local
		$nom_temp = session_id().microtime();
		$nom_temp = str_replace(' ','_',$nom_temp);
		$nom_temp = str_replace('.','_',$nom_temp);
		$fichier_tmp = $base_path."/temp/".$nom_temp;
		if ($pmb_curl_available && !file_exists($userfile_name)) {
			$aCurl = new Curl();
			$aCurl->timeout=10;
			$aCurl->set_option('CURLOPT_SSL_VERIFYPEER',false);
			$aCurl->save_file_name=$fichier_tmp; 
			$aCurl->get($userfile_name);
		} else if(file_exists($userfile_name)) {
			$handle = fopen($userfile_name, "rb");
			$filecontent = stream_get_contents($handle);
			fclose($handle);
			$fd = fopen($fichier_tmp,"w");
			fwrite($fd,$filecontent);
			fclose($fd);
		}
		$source_file = realpath($fichier_tmp);
		if ($source_file) {
		  $source_file.= '[0]';
		} else {
		    $source_file = '';
		}
	}
	
	if($source_file=='') {
	    $contenu_vignette = '';
	    return $contenu_vignette;
	}
	
	$error = true;
	if(extension_loaded('imagick')) {
		mysql_set_wait_timeout(3600);
		$error=false;
		try {		
			$img = new Imagick();
			$img->readImage($source_file);
			
			$img->setImageBackgroundColor('white');
			$img = $img->flattenImages();
			
			if(($img->getImageWidth() > $pmb_vignette_x) || ($img->getImageHeight() > $pmb_vignette_y)){// Si l'image est trop grande on la réduit
				$img->thumbnailimage($pmb_vignette_x,$pmb_vignette_y,true);
			}
			$img->setImageFormat( "png" );
			$img->setCompression(Imagick::COMPRESSION_LZW);
			$img->setCompressionQuality(90);
			$contenu_vignette = $img->getImageBlob();			
		} catch(Exception $ex) {
			$error=true;
		}		
		if($fichier_tmp && file_exists($fichier_tmp)){
			unlink($fichier_tmp);
		}
	}
	if ($error) {
		$size =@getimagesize($bidon);
		/*   ".gif"=>"1",
	         ".jpg"=>"2",
	         ".jpeg"=>"2",
	         ".png"=>"3",
	         ".swf"=>"4",
	         ".psd"=>"5",
	         ".bmp"=>"6");
		*/	
		switch ($size[2]) {
			case 1:
				$src_img = imagecreatefromgif($bidon);
			 	break;
			case 2:
				$src_img = imagecreatefromjpeg($bidon);
				break;
			case 3:
				$src_img = imagecreatefrompng($bidon);
				break;
			case 6:
				$src_img = imagecreatefromwbmp($bidon);
				break;
			default:
				break;
		}
		$erreur_vignette = 0 ;
		if (!empty($src_img)) {
			$rs=$pmb_vignette_x/$pmb_vignette_y;
			$taillex=imagesx($src_img);
			$tailley=imagesy($src_img);
			if (!$taillex || !$tailley) return "" ;
			if (($taillex>$pmb_vignette_x)||($tailley>$pmb_vignette_y)) {
				$r=$taillex/$tailley;
				if (($r<1)&&($rs<1)) {
					//Si x plus petit que y et taille finale portrait 
					//Si le format final est plus large en proportion
					if ($rs>$r) {
						$new_h=$pmb_vignette_y; 
						$new_w=$new_h*$r; 
					} else {
						$new_w=$pmb_vignette_x;
						$new_h=$new_w/$r;
					}
				} else if (($r<1)&&($rs>=1)){ 
					//Si x plus petit que y et taille finale paysage
					$new_h=$pmb_vignette_y;
					$new_w=$new_h*$r;  
				} else if (($r>1)&&($rs<1)) {
					//Si x plus grand que y et taille finale portrait
					$new_w=$pmb_vignette_x;
					$new_h=$new_w/$r;
				} else {
					//Si x plus grand que y et taille finale paysage
					if ($rs<$r) {
						$new_w=$pmb_vignette_x;
						$new_h=$new_w/$r;
					} else {
						$new_h=$pmb_vignette_y;
						$new_w=$new_h*$r;
					}
				}
			} else {
				$new_h = $tailley ;
				$new_w = $taillex ;
			}
			$dst_img=imagecreatetruecolor($pmb_vignette_x,$pmb_vignette_y);
			ImageSaveAlpha($dst_img, true);
			ImageAlphaBlending($dst_img, false);
			imagefilledrectangle($dst_img,0,0,$pmb_vignette_x,$pmb_vignette_y,imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
			imagecopyresized($dst_img,$src_img,round(($pmb_vignette_x-$new_w)/2),round(($pmb_vignette_y-$new_h)/2),0,0,$new_w,$new_h,ImageSX($src_img),ImageSY($src_img));
			imagepng($dst_img, "$base_path/temp/".SESSid);
			$fp = fopen("$base_path/temp/".SESSid , "r" ) ;
			$contenu_vignette = fread ($fp, filesize("$base_path/temp/".SESSid));
			if (!$fp || $contenu_vignette=="") $erreur_vignette++ ;
			fclose ($fp) ;
			unlink("$base_path/temp/".SESSid);
		} else {
			$contenu_vignette = '' ;
		}
	}
	return $contenu_vignette ;
}

function construire_vignette($vignette_name='', $userfile_name='', $url='') {
	$contenu_vignette = "";
	$eh = events_handler::get_instance();
	$event = new event_explnum("explnum","contruire_vignette");
	$eh->send($event);
	$contenu_vignette = $event->get_contenu_vignette();
	if($contenu_vignette) {
		return $contenu_vignette;
	}
	if ($vignette_name) {
		$contenu_vignette = reduire_image($vignette_name);
	} elseif ($userfile_name) {
		$contenu_vignette = reduire_image($userfile_name);
	} elseif ($url) {
		$contenu_vignette = reduire_image($url);
	} else {
		$contenu_vignette = "";
	}
	return $contenu_vignette ;
}

function explnum_update($f_explnum_id, $f_notice, $f_bulletin, $f_nom, $f_url, $retour, $conservervignette=0, $f_statut_chk=0) {
	
	global $dbh, $msg,$scanned_image,$scanned_image_ext ;
	global $current_module, $pmb_explnum_statut;
	global $ck_index, $scanned_texte, $up_place, $path, $id_rep;
	
	create_tableau_mimetype() ;
	
	if ($f_explnum_id) {
		$requete = "UPDATE explnum SET ";
		$limiter = " WHERE explnum_id='$f_explnum_id' ";
	} else {
		$requete = "INSERT INTO explnum SET ";
		$limiter = "";
	}
	print "<div class=\"row\"><h1>$msg[explnum_doc_associe]</h1>";
	
	$erreur=0;
	$userfile_name = $_FILES['f_fichier']['name'] ;
	$userfile_temp = $_FILES['f_fichier']['tmp_name'] ;
	$userfile_moved = basename($userfile_temp);
	
	$vignette_name = $_FILES['f_vignette']['name'] ;
	$vignette_temp = $_FILES['f_vignette']['tmp_name'] ;
	$vignette_moved = basename($vignette_temp);
	
	$userfile_name = preg_replace("/ |'|\\|\"|\//m", "_", $userfile_name);
	$vignette_name = preg_replace("/ |'|\\|\"|\//m", "_", $vignette_name);
	
	$userfile_ext = '';
	if ($userfile_name) {
		$userfile_ext = extension_fichier($userfile_name);
	}
	
	if ($f_explnum_id) {
		// modification
		// si $userfile_name est vide on ne fera pas la maj du data
		if (($scanned_image)||($userfile_name)) {
			//Avant tout, y-a-t-il une image extérieure ?
			if ($scanned_image) {
				//Si oui !
				$tmpid=str_replace(" ","_",microtime());
				$fp=@fopen("./temp/scanned_$tmpid.".$scanned_image_ext,"w+");
				if ($fp) {
					fwrite($fp,base64_decode($scanned_image));
					$nf=1;
					$part_name="scanned_image_".$nf;
					global ${$part_name};
					while (${$part_name}) {
						fwrite($fp,base64_decode(${$part_name}));
						$nf++;
						$part_name="scanned_image_".$nf;
						global ${$part_name};
					}
					fclose($fp);
					$fic=1;
					$maj_data = 1;
					$userfile_name="scanned_$tmpid.".$scanned_image_ext;
					$userfile_ext=$scanned_image_ext;
					$userfile_moved = $userfile_name;
					$f_url="";
				} else $erreur++;
			} else if ($userfile_name) {
				if (move_uploaded_file($userfile_temp,'./temp/'.$userfile_moved)) {					
					$fic=1;
					$f_url="";
					$maj_data = 1;
					move_uploaded_file($vignette_temp,'./temp/'.$vignette_moved) ;
					
				} else {
					$erreur++;
				}
			}
			$contenu_vignette = construire_vignette($vignette_moved, $userfile_moved) ;
			$maj_vignette = 1 ;
			$mimetype = trouve_mimetype($userfile_moved, $userfile_ext) ;
			if (!$mimetype) $mimetype="application/data";
			$maj_mimetype = 1 ;
		} else {
			if ($vignette_name) {
				move_uploaded_file($vignette_temp,'./temp/'.$vignette_moved) ;
				$contenu_vignette = construire_vignette($vignette_moved, $userfile_moved) ;
				$maj_vignette = 1 ;
			}
			if ($f_url) {
				move_uploaded_file($vignette_temp,'./temp/'.$vignette_moved) ;
				$contenu_vignette = construire_vignette($vignette_moved, $userfile_moved) ;
				$maj_vignette = 1 ;
				$mimetype="URL";
				$maj_mimetype = 1 ;
				$contenu="";
				$maj_data=1 ;
			}
		}
	} else {
		// creation
		//Y-a-t-il une image exterieure ?
		if ($scanned_image) {
			//Si oui !
			$tmpid=str_replace(" ","_",microtime());
			$fp=@fopen("./temp/scanned_$tmpid.".$scanned_image_ext,"w+");
			if ($fp) {
				fwrite($fp,base64_decode($scanned_image));
				$nf=1;
				$part_name="scanned_image_".$nf;
				global ${$part_name};
				while (${$part_name}) {
					fwrite($fp,base64_decode(${$part_name}));
					$nf++;
					$part_name="scanned_image_".$nf;
					global ${$part_name};
				}
				fclose($fp);
				$fic=1;
				$maj_data = 1;
				$userfile_name="scanned_$tmpid.".$scanned_image_ext;
				$userfile_ext=$scanned_image_ext;
				$userfile_moved = $userfile_name;
				$f_url="";
			} else $erreur++;
		} else if (move_uploaded_file($userfile_temp,'./temp/'.$userfile_moved)) {
			$fic=1;
			$f_url="";
			$maj_data = 1;
		} elseif (!$f_url) $erreur++;
	
		move_uploaded_file($vignette_temp,'./temp/'.$vignette_moved) ;
		$contenu_vignette = construire_vignette($vignette_moved, $userfile_moved);
		$maj_vignette = 1 ;
		
		if (!$f_url && !$fic) $erreur++ ; 
		if ($f_url) {
			$mimetype = "URL" ;
		} else {
			$mimetype = trouve_mimetype($userfile_moved,$userfile_ext) ;
			if (!$mimetype) $mimetype="application/data";
		}
		$maj_mimetype = 1 ;
	}
	
	
	
	$upfolder = new upload_folder($id_rep);
	if ($fic) {
		$is_upload = false;
		if(!$f_explnum_id && ($path && $up_place)){
			if($upfolder->isHashing()){
				$rep = $upfolder->hachage($userfile_name);
				@mkdir($rep);
				$path = $upfolder->formate_path_to_nom($rep);
				$file_name = $rep.$userfile_name;				
			} else {				 
				$file_name = $upfolder->formate_nom_to_path($path).$userfile_name;
			}
			$path = $upfolder->formate_path_to_save($path);
			$file_name = $upfolder->encoder_chaine($file_name);
			rename('./temp/'.$userfile_moved,$file_name);
			$is_upload = true;
		} else $file_name = './temp/'.$userfile_moved;
		$fp = fopen($file_name , "r" ) ;
		$contenu = fread ($fp, filesize($file_name));
		if (!$fp || $contenu=="") $erreur++ ;
		fclose ($fp) ;
	}
	
	//Dans le cas d'une modification, on regarde si il y a eu un déplacement du stockage
	if ($f_explnum_id){	
		$explnum = new explnum($f_explnum_id);		
		if($explnum->isEnBase() && ($up_place && $path)){
			$explnum->remove_from_base($path,$id_rep);
			$contenu="";
			$is_upload = false;
		} elseif($explnum->isEnUpload() && (!$up_place)){
			$contenu = $explnum->remove_from_upload();
			$id_rep=0;
			$path="";
		} elseif($explnum->isEnUpload() && ($up_place && $path)){
			$path = $explnum->change_rep_upload($upfolder, $upfolder->formate_nom_to_path($path));
			$path = $upfolder->formate_path_to_save($upfolder->formate_path_to_nom($path));
		}
	}
		
	if (!$f_nom) {
		if ($userfile_name) $f_nom = $userfile_name ;
		elseif ($f_url) $f_nom = $f_url ;
		else $f_nom = "-x-x-x-x-" ;
	}

	if ($userfile_name && !$is_upload) unlink($file_name);
	if ($vignette_name) unlink('./temp/'.$vignette_moved);
	        
	if (!$erreur) {
		$requete .= " explnum_notice='$f_notice'";
		$requete .= ", explnum_bulletin='$f_bulletin'";
		$requete .= ", explnum_nom='$f_nom'";
		$requete .= ", explnum_url='$f_url'";
		if ($maj_mimetype)
			$requete .= ", explnum_mimetype='".$mimetype. "' ";
		if ($maj_data ) {
			if(!$is_upload ) $requete .= ", explnum_data='".addslashes($contenu)."'";
			$requete .= ", explnum_nomfichier='".addslashes($userfile_name)."'";
			$requete .= ", explnum_extfichier='".addslashes($userfile_ext)."'";
		}
		if ($maj_vignette && !$conservervignette) {
			$requete .= ", explnum_vignette='".addslashes($contenu_vignette)."'";
		}
		if ($pmb_explnum_statut=='1') {
			$requete.= ", explnum_statut='".(($f_statut_chk)?$f_statut_chk:1)."'";
		}	
		$requete.= ", explnum_repertoire='".$id_rep."'";
		$requete.= ", explnum_path='".$path."'";
		
		$requete .= $limiter;
		pmb_mysql_query($requete, $dbh) ;
		
		
		//Indexation du document
		global $pmb_indexation_docnum;
				   			
		if($pmb_indexation_docnum){										
			if(!$f_explnum_id && $ck_index){			
				$id_explnum = pmb_mysql_insert_id();
				$indexation = new indexation_docnum($id_explnum, $scanned_texte);
				$indexation->indexer();
			} elseif($f_explnum_id && $ck_index){
				$indexation = new indexation_docnum($f_explnum_id, $scanned_texte);
				$indexation->indexer();				
			} elseif($f_explnum_id && !$ck_index){
				$indexation = new indexation_docnum($f_explnum_id);
				$indexation->desindexer();	
			}			 
		}		
		
		// on reaffiche l'ISBD
		print "<div class='row'><div class='msg-perio'>".$msg['maj_encours']."</div></div>";
		$id_form = md5(microtime());
		if (pmb_mysql_error()) {
			echo "MySQL error : ".pmb_mysql_error() ;
			print "
				<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" >
					<input type='submit' class='bouton' name=\"id_form\" value=\"Ok\">
					</form>";
			print "</div>";
			exit ;
		}
		print "
		<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" style=\"display:none\">
			<input type=\"hidden\" name=\"id_form\" value=\"$id_form\">
			</form>";
		print "<script type=\"text/javascript\">document.dummy.submit();</script>";

	} else {
		eval("\$bid=\"".$msg['explnum_erreurupload']."\";");
		print "<div class='row'><div class='msg-perio'>".$bid."</div></div>";
		print "
			<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" >
				<input type='submit' class='bouton' name=\"id_form\" value=\"Ok\">
			</form>";
	}
		
	print "</div>";
}


function explnum_add_from_url($f_notice_id, $f_bulletin_id, $f_nom, $f_url, $overwrite=true, $source_id=0, $filename='', $f_path='', $f_statut) {
	
	global $dbh, $base_path;
	
	if($f_bulletin_id){
		$f_notice_id = 0;
	}else if (!$f_bulletin_id && $f_notice_id){
		$f_bulletin_id = 0;
	}
	if (!$overwrite) {
		$sql_find = "SELECT count(*) FROM explnum WHERE explnum_notice = ".$f_notice_id." AND explnum_nom = '".addslashes($f_nom)."'";
		$res = pmb_mysql_query($sql_find, $dbh);
		$count = pmb_mysql_result($res, 0, 0);
		if ($count)
			return;		
	}
	
	$sql_delete = "DELETE FROM explnum WHERE explnum_notice = ".$f_notice_id." AND explnum_nom = '".addslashes($f_nom)."' ";
	pmb_mysql_query($sql_delete, $dbh);
	
	$original_filename = basename($f_url);
	if(strripos($original_filename,'.') !== false) {
		$extension = substr($original_filename,strripos($original_filename,'.')*1+1);
	} elseif(strripos($filename,'.') !== false) {
		$extension = substr($filename,strripos($filename,'.')*1+1);
	} else {
		$extension = substr($f_nom,strripos($f_nom,'.')*1+1);
	}
	$tmp_filename = explnum::static_rename($extension);
	if ($filename) {
		$new_filename=$filename;
	} else {
		$new_filename=$tmp_filename;
	}
	//copie en répertoire temporaire
	$r=false;
	if (file_exists($f_url) && filesize($f_url)) {	//document en repertoire
		$r = copy($f_url, $base_path.'/temp/'.$tmp_filename);
	} else {	//url
		$aCurl = new Curl();
		$aCurl->set_option('CURLOPT_SSL_VERIFYPEER',false);
		$content = $aCurl->get($f_url);
		$content = $content->body;
		$r = file_put_contents($base_path."/temp/".$tmp_filename, $content);
	}

	if ($r) {
		
		//construction vignette
		$vignette = construire_vignette('', $tmp_filename);
		create_tableau_mimetype();
		$mimetype = trouve_mimetype("$base_path/temp/".$tmp_filename, $extension);	
		
		//si la source du connecteur est précisée, on regarde si on a pas un répertoire associé
		$rep_upload=0;
		if ($source_id){
			$check_rep = "select rep_upload from connectors_sources where source_id = ".$source_id;
			$res = pmb_mysql_query($check_rep);
			if(pmb_mysql_num_rows($res)){
				$rep_upload = pmb_mysql_result($res,0,0);
			}
		}
		if($rep_upload != 0){
			$upload_folder = new upload_folder($rep_upload);
			$rep_path = $upload_folder->get_path($new_filename);
			if ($f_path && file_exists($rep_path.$f_path)) {
				$rep_path=$rep_path.$f_path.'/';
			}

			if(file_exists($upload_folder->encoder_chaine($rep_path.$new_filename))){
				$suffix=1;
				$ext = extension_fichier($new_filename);
				$file = str_replace(".".$ext,"",basename($new_filename));
				while (file_exists($upload_folder->encoder_chaine($rep_path.$file."_".$suffix.".".$ext))){
					$suffix++;
				}
				$new_filename = $file."_".$suffix.".".$ext;
			}
			rename("$base_path/temp/".$tmp_filename,$upload_folder->encoder_chaine($rep_path.$new_filename));
			$path =$upload_folder->formate_path_to_save($upload_folder->formate_path_to_nom($rep_path));
			$insert_sql = "INSERT INTO explnum (explnum_notice, explnum_bulletin, explnum_nom, explnum_nomfichier, explnum_mimetype, explnum_extfichier, explnum_vignette, explnum_repertoire, explnum_path, explnum_docnum_statut) VALUES (";
			$insert_sql .= $f_notice_id.",";
			$insert_sql .= $f_bulletin_id.",";
			$insert_sql .= "'".addslashes($f_nom)."',";
			$insert_sql .= "'".addslashes($new_filename)."',";
			$insert_sql .= "'".addslashes($mimetype)."',";
			$insert_sql .= "'".addslashes($extension)."',";
			$insert_sql .= "'".addslashes($vignette)."',";
			$insert_sql .= "'".addslashes($rep_upload)."',";
			$insert_sql .= "'".addslashes($path)."',";
			$insert_sql .= "'".(($f_statut)?$f_statut:1)."'";
			$insert_sql .= ")";		
		} else {			
			$insert_sql = "INSERT INTO explnum (explnum_notice, explnum_bulletin, explnum_nom, explnum_nomfichier, explnum_mimetype, explnum_extfichier, explnum_data, explnum_vignette, explnum_docnum_statut) VALUES (";
			$insert_sql .= $f_notice_id.",";
			$insert_sql .= $f_bulletin_id.",";
			$insert_sql .= "'".addslashes($f_nom)."',";
			$insert_sql .= "'".addslashes($new_filename)."',";
			$insert_sql .= "'".addslashes($mimetype)."',";
			$insert_sql .= "'".addslashes($extension)."',";
			$insert_sql .= "'".addslashes($content)."',";
			$insert_sql .= "'".addslashes($vignette)."',";
			$insert_sql .= "'".(($f_statut)?$f_statut:1)."'";
			$insert_sql .= ")";
		}
		if(pmb_mysql_query($insert_sql, $dbh)){
			$docnum_id = pmb_mysql_insert_id($dbh);
			if($docnum_id){
				$index = new indexation_docnum($docnum_id);
				$index->indexer();
			}
		}
		
/*
		$aCurl = new Curl();
		$content = $aCurl->get($f_url);
		$content = $content->body;
		
		$origine=str_replace(" ","",microtime());
		$origine=str_replace("0.","",$origine);
		$original_filename = basename($f_url);
		if( $filename != "") $afilename = $filename;
		else $afilename = $origine.$original_filename;
		if (!$original_filename)
			$original_filename = $afilename;
			
		file_put_contents("$base_path/temp/".$afilename, $content);
*/
/*		
		$vignette = construire_vignette('', $afilename);
		create_tableau_mimetype();
		$afilename_ext=extension_fichier($afilename);
		$mimetype = trouve_mimetype("$base_path/temp/".$afilename, $afilename_ext);
		$extension = strrchr($afilename, '.');
		
		//si la source du connecteur est précisée, on regarde si on a pas un répertoire associé
		if ($source_id){
			$check_rep = "select rep_upload from connectors_sources where source_id = ".$source_id;
			$res = pmb_mysql_query($check_rep);
			if(pmb_mysql_num_rows($res)){
				$rep_upload = pmb_mysql_result($res,0,0);
			}
		}
*/
		/*
			if($rep_upload != 0){
			$upload_folder = new upload_folder($rep_upload);
			$rep_path = $upload_folder->get_path($afilename);
			if ($f_path && file_exists($rep_path.$f_path)) {
				$rep_path=$rep_path.$f_path.'/';
			}
			
			copy("$base_path/temp/".$afilename,$rep_path.$afilename);
			$path =$upload_folder->formate_path_to_save($upload_folder->formate_path_to_nom($rep_path));
			$insert_sql = "INSERT INTO explnum (explnum_notice, explnum_nom, explnum_nomfichier, explnum_mimetype, explnum_extfichier, explnum_vignette, explnum_repertoire, explnum_path) VALUES (";
			$insert_sql .= $f_notice_id.",";
			$insert_sql .= "'".addslashes($f_nom)."',";
			$insert_sql .= "'".addslashes($afilename)."',";
			$insert_sql .= "'".addslashes($mimetype)."',";
			$insert_sql .= "'".addslashes($extension)."',";
			$insert_sql .= "'".addslashes($vignette)."',";
			$insert_sql .= "'".addslashes($rep_upload)."',";
			$insert_sql .= "'".addslashes($path)."'";
			$insert_sql .= ")";		
		}else{
			$insert_sql = "INSERT INTO explnum (explnum_notice, explnum_nom, explnum_nomfichier, explnum_mimetype, explnum_extfichier, explnum_data, explnum_vignette) VALUES (";
			$insert_sql .= $f_notice_id.",";
			$insert_sql .= "'".addslashes($f_nom)."',";
			$insert_sql .= "'".addslashes($afilename)."',";
			$insert_sql .= "'".addslashes($mimetype)."',";
			$insert_sql .= "'".addslashes($extension)."',";
			$insert_sql .= "'".addslashes($content)."',";
			$insert_sql .= "'".addslashes($vignette)."'";
			$insert_sql .= ")";
		}
		pmb_mysql_query($insert_sql, $dbh);
		
		unlink("$base_path/temp/".$afilename);	
*/
	}
}


function explnum_add_url($f_notice_id, $f_bulletin_id, $f_nom, $f_url, $overwrite=true, $f_statut=0) {
	
	global $dbh;
	if($f_bulletin_id){
		$f_notice_id = 0;
	}else if (!$f_bulletin_id && $f_notice_id){
		$f_bulletin_id = 0;
	}
	if (!$overwrite) {
		$sql_find = "SELECT count(*) FROM explnum WHERE explnum_notice = ".$f_notice_id." AND explnum_nom = '".addslashes($f_nom)."'";
		$res = pmb_mysql_query($sql_find, $dbh);
		$count = pmb_mysql_result($res, 0, 0);
		if ($count)
			return;		
	}
	$sql_delete = "DELETE FROM explnum WHERE explnum_notice = ".$f_notice_id." AND explnum_nom = '".addslashes($f_nom)."'";
	pmb_mysql_query($sql_delete, $dbh);
	
	$original_filename = basename($f_url);
	$extension = strrchr($original_filename, '.');
	$insert_sql = "INSERT INTO explnum (explnum_notice, explnum_bulletin, explnum_nom, explnum_nomfichier, explnum_url, explnum_mimetype, explnum_extfichier, explnum_docnum_statut, explnum_vignette) VALUES (";
	$insert_sql .= $f_notice_id.",";
	$insert_sql .= $f_bulletin_id.",";
	$insert_sql .= "'".addslashes($f_nom)."',";
	$insert_sql .= "'".addslashes($original_filename)."',";
	$insert_sql .= "'".addslashes($f_url)."',";
	$insert_sql .= "'"."URL"."',";
	$insert_sql .= "'".addslashes($extension)."',";
	$insert_sql .= "'".(($f_statut)?$f_statut:1)."',";
	$insert_sql .= "'".addslashes(construire_vignette('', '', $f_url))."'";
	$insert_sql .= ")";
	
	if(pmb_mysql_query($insert_sql, $dbh)){
		$docnum_id = pmb_mysql_insert_id($dbh);
		if($docnum_id){
			$index = new indexation_docnum($docnum_id);
			$index->indexer();
		}
	}
}


// fonction retournant les infos d'exemplaires numeriques pour une notice ou un bulletin donne
function show_explnum_per_notice($no_notice, $no_bulletin, $link_expl='',$param_aff=array(),$return_count = false, $context_dsi_id_bannette = 0) {
	
	// params :
	// $link_expl= lien associe a l'exemplaire avec !!explnum_id!! a mettre a jour
	global $dbh;
	global $charset;
	global $use_dsi_diff_mode;
	global $base_path,$msg;
	global $pmb_map_activate;
	global $pmb_explnum_order;
	
	if (!$no_notice && !$no_bulletin) return "";

	if(($use_dsi_diff_mode == 1) && !explnum_allow_opac($no_notice, $no_bulletin)){//Si je suis en dsi je regarde les droits opac sur les explnum
		return "";
	}

	global $_mimetypes_bymimetype_, $_mimetypes_byext_ ;
	create_tableau_mimetype() ;

	// recuperation du nombre d'exemplaires
	$requete = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier, explnum_docnum_statut 
			FROM explnum WHERE ";
	if ($no_notice) $requete .= "explnum_notice='$no_notice' ";
		else $requete .= "explnum_bulletin='$no_bulletin' ";
	if($no_notice)
		$requete .= "union SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier, explnum_docnum_statut
			FROM explnum, bulletins
			WHERE bulletin_id = explnum_bulletin
			AND bulletins.num_notice='".$no_notice."'";
	if ($pmb_explnum_order) $requete .= " order by ".$pmb_explnum_order;
	else $requete .= " order by explnum_mimetype, explnum_id ";
	$res = pmb_mysql_query($requete);
	$nb_ex = pmb_mysql_num_rows($res);
	if ($return_count) {
		return $nb_ex;
	}
	$map_display = '';
	if($nb_ex) {
		// on recupere les donnees des exemplaires
		$i = 1 ;
		$ligne_finale = '';
		while (($expl = pmb_mysql_fetch_object($res))) {
			// couleur de l'img en fonction du statut
			if ($expl->explnum_docnum_statut) {
				$rqt_st = "SELECT * FROM explnum_statut WHERE  id_explnum_statut='".$expl->explnum_docnum_statut."' ";
				$Query_statut = pmb_mysql_query($rqt_st, $dbh)or die ($rqt_st. " ".pmb_mysql_error()) ;
				$r_statut = pmb_mysql_fetch_object($Query_statut);
				$class_img = " class='docnum_".$r_statut->class_html."' ";
				if ($expl->explnum_docnum_statut>1) {
					$txt = $r_statut->opac_libelle;
				}else $txt="";
				
				$statut_libelle_div="
					<div id='zoom_statut_docnum".$expl->explnum_id."' style='border: 2px solid rgb(85, 85, 85); background-color: rgb(255, 255, 255); position: absolute; z-index: 2000; display: none;'>
						<b>$txt</b>
					</div>
				";
				
			} else {
				$class_img = " class='docnum_statutnot1' " ;
				$txt = "" ;
			}
			
			if ($i==1) $ligne="<tr><td id='explnum_".$expl->explnum_id."' class='docnum center' width='25%'>!!1!!</td><td class='docnum center' width='25%'>!!2!!</td><td class='docnum center' width='25%'>!!3!!</td><td class='docnum center' width='25%'>!!4!!</td></tr>" ;
			$tlink = '';
			if ($link_expl) {
				$tlink = str_replace("!!explnum_id!!", $expl->explnum_id, $link_expl);
				$tlink = str_replace("!!notice_id!!", $expl->explnum_notice, $tlink);					
				$tlink = str_replace("!!bulletin_id!!", $expl->explnum_bulletin, $tlink);					
			} 
			$alt = htmlentities($expl->explnum_nom." - ".$expl->explnum_mimetype,ENT_QUOTES, $charset) ;
			
			global $prefix_url_image ;
			if ($prefix_url_image) $tmpprefix_url_image = $prefix_url_image; 
				else $tmpprefix_url_image = "./" ;
	
			if ($expl->explnum_vignette){
				$obj="<img src='".$tmpprefix_url_image."vig_num.php?explnum_id=".$expl->explnum_id;
				if ($context_dsi_id_bannette) {
					$obj.= "&context_dsi_id_bannette=".$context_dsi_id_bannette;
				}
				$obj.="' alt='$alt' title='$alt' border='0'>";
			} else { // trouver l'icone correspondant au mime_type
				$obj="<img src='".$tmpprefix_url_image."images/mimetype/".icone_mimetype($expl->explnum_mimetype, $expl->explnum_extfichier)."' alt='$alt' title='$alt' border='0'>";
			}
				
			$obj_suite="$statut_libelle_div
				<a  href='#' onmouseout=\"z=document.getElementById('zoom_statut_docnum".$expl->explnum_id."'); z.style.display='none'; \" onmouseover=\"z=document.getElementById('zoom_statut_docnum".$expl->explnum_id."'); z.style.display=''; \">
					<div class='vignette_doc_num' ><img $class_img width='10' height='10' src='".$tmpprefix_url_image."images/spacer.gif'></div>
				</a>
			";			
			$expl_liste_obj = "<span class='center'>";
			$expl_liste_obj .= "<a href='".$tmpprefix_url_image."doc_num.php?explnum_id=$expl->explnum_id' alt='$alt' title='$alt' target='_blank'>".$obj."</a>$obj_suite<br />" ;
			
			if (isset($_mimetypes_byext_[$expl->explnum_extfichier]["label"]) && $_mimetypes_byext_[$expl->explnum_extfichier]["label"]) $explmime_nom = $_mimetypes_byext_[$expl->explnum_extfichier]["label"] ;
			elseif (isset($_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"]) && $_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"]) $explmime_nom = $_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"] ;
			else $explmime_nom = $expl->explnum_mimetype ;
			if(isset($param_aff["mine_type"])) $explmime_nom="";
			if ($tlink) {
				$expl_liste_obj .= "<a class='docnum_name_link' href='$tlink'>";
				$expl_liste_obj .= htmlentities($expl->explnum_nom,ENT_QUOTES, $charset)."</a>";
			} else {
				$expl_liste_obj .= htmlentities($expl->explnum_nom,ENT_QUOTES, $charset);
			}
			// Régime de licence
			$expl_liste_obj.= explnum_licence::get_explnum_licence_picto($expl->explnum_id);
			
			$expl_liste_obj.= "<div class='explnum_type'>".htmlentities($explmime_nom,ENT_QUOTES, $charset)."</div>";
			//recherche des concepts...
			$query = "select num_concept,value from index_concept join skos_fields_global_index on num_concept = id_item and code_champ = 1  where num_object = ".$expl->explnum_id." and type_object = 11 order by order_concept";
			$result = pmb_mysql_query($query,$dbh);
			$concept= "";
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					if($concept){
						$concept.=" / ";
					}
					if (SESSrights & AUTORITES_AUTH){
						$concept.="<a href='".$base_path."/autorites.php?categ=see&sub=concept&id=".$row->num_concept."' title='".addslashes($msg['concept_menu'].": ".htmlentities($row->value,ENT_QUOTES,$charset))."'>".htmlentities($row->value,ENT_QUOTES,$charset)."</a>";
					}else{
						$concept.="<span title='".addslashes($msg['concept_menu'].": ".htmlentities($row->value,ENT_QUOTES,$charset))."'>".htmlentities($row->value,ENT_QUOTES,$charset)."</span>";
					}
				}
			}
			
			$expl_liste_obj .= $concept."</span>";
			$ligne = str_replace("!!$i!!", $expl_liste_obj, $ligne);
			$i++;
			if ($i==5) {
				$ligne_finale .= $ligne ;
				$i=1;
			}
		}
		if (!$ligne_finale) $ligne_finale = $ligne ;
		elseif ($i!=1) $ligne_finale .= $ligne ;
		
		$ligne_finale = str_replace('!!2!!', "&nbsp;", $ligne_finale);
		$ligne_finale = str_replace('!!3!!', "&nbsp;", $ligne_finale);
		$ligne_finale = str_replace('!!4!!', "&nbsp;", $ligne_finale);
		
	} else return "";
	$entry = $map_display . "<table class='docnum'>$ligne_finale</table>";
	return $entry;
}

function extract_metas($filename,$mimetype,$tmp = false){
	global $base_path,$class_path;
	global $charset;
	//$metas = array();
	switch($mimetype){
		//EPub
		case "application/epub+zip" :
			//Exiftool ne donnerait rien, mais un Epub contient toutes les infos qui nous faut !
			require_once($class_path."/epubData.class.php");
			$epub = new epub_Data($filename);
			$metas = $epub->metas;
			break;
		case "application/pdf" :
//			exec("exiftool -struct -J -q ".$filename,$metas);
//			$metas = json_decode(implode("\n",$metas),true);
			exec("exiftool ".$filename,$tab);
			$metas = array();
			foreach($tab as $row){
				$elem = explode(":",$row);
				$key = trim(str_replace(" ","",array_shift($elem)));
				$value = trim(implode(":",$elem));
				if($charset != "utf-8"){
					$key = utf8_decode($key);
					$value = utf8_decode($value);
				}
				$metas[$key] = $value;
			}
			break;
		default :
			$type = substr($mimetype,0,strpos($mimetype,"/"));
			switch ($type){
				case "image" :
				case "video" :
				case "audio" :
					exec("exiftool ".$filename,$tab);
					$metas = array();
					foreach($tab as $row){
						$elem = explode(":",$row);
						$key = trim(str_replace(" ","",array_shift($elem)));
						$value = trim(implode(":",$elem));
						if($charset != "utf-8" && mb_detect_encoding($value) == 'UTF-8'){
							$key = utf8_decode($key);
							$value = utf8_decode($value);
						}
						$metas[$key] = $value;
					}
					break;
					
				case "text" :
					//pas de métas pertinantes sur une fichier texte...
					break;
				default :
					if(!extension_fichier(basename($filename))){
						$new_name=basename($filename)."temp";//Pour éviter que si pas d'extension on perde le fichier
					}else{
						$new_name = str_replace(extension_fichier(basename($filename)),"pdf",basename($filename));
					}
					$new_path = dirname($filename)."/".$new_name;
					exec("curl http://localhost:8080/converter/converted/".$new_name." -F \"inputDocument=@$filename\" > ".$new_path);//Ne doit marcher que dans un cas très précis, pas vrai Arnaud
					$metas = extract_metas($new_path,"application/pdf",true);
					break;
			}

			break;
	}
	if($tmp) unlink($filename);
	return $metas;
}

// fonction qui permet de savoir si les exemplaires numériques pour une notice ou un bulletin donné sont affichable à l'OPAC
function explnum_allow_opac($no_notice, $no_bulletin) {
	// params :
	global $dbh;
	global $gestion_acces_active,$gestion_acces_empr_notice,$opac_show_links_invisible_docnums;
	
	if (!$no_notice && !$no_bulletin) return false;
	
	$docnum_visible = true;
	$id_for_right = $no_notice;
	if($no_bulletin){
		$query = "select num_notice,bulletin_notice from bulletins where bulletin_id = ".$no_bulletin;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$infos = pmb_mysql_fetch_object($result);
			if($infos->num_notice){
				$id_for_right = $infos->num_notice;
			}else{
				$id_for_right = $infos->bulletin_notice;	
			}
		}
	}
	if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
		$ac= new acces();
		$dom_2= $ac->setDomain(2);
		$docnum_visible = $dom_2->getRights(0,$id_for_right,16);
	} else {
		$requete = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM notices, notice_statut WHERE notice_id ='".$id_for_right."' and id_notice_statut=statut ";
		$myQuery = pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($myQuery)) {
			$statut_temp = pmb_mysql_fetch_object($myQuery);
			if(!$statut_temp->explnum_visible_opac)	$docnum_visible=false;
			if(($statut_temp->explnum_visible_opac_abon) && (!$opac_show_links_invisible_docnums))	$docnum_visible=false;
		} else 	$docnum_visible=false;
	}
	return $docnum_visible;
}

// fonction retournant les infos d'exemplaires numeriques en relation avec une notice donne
function show_explnum_in_relation($no_notice, $link_expl='',$param_aff=array()) {
	global $dbh;
	global $charset;
	global $base_path,$msg;
	global $_mimetypes_bymimetype_, $_mimetypes_byext_ ;
	global $prefix_url_image ;
	
	if (!$no_notice) return "";
	
	create_tableau_mimetype() ;

	// recuperation du nombre d'exemplaires
	$requete = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier, explnum_docnum_statut
			FROM explnum 
			JOIN index_concept on index_concept.num_object = explnum_id AND index_concept.type_object=11
			JOIN vedette_link on vedette_link.num_object=index_concept.num_concept AND vedette_link.type_object=1
			JOIN vedette_object on vedette_object.num_vedette=vedette_link.num_vedette AND vedette_object.object_type=10 and vedette_object.object_id='$no_notice'
			ORDER BY explnum_mimetype, explnum_id ";
	$res = pmb_mysql_query($requete, $dbh) or die ($requete." ".pmb_mysql_error());
	$nb_ex = pmb_mysql_num_rows($res);

	if($nb_ex) {
		// on recupere les donnees des exemplaires
		$i = 1 ;
		$ligne_finale = '';
		while (($expl = pmb_mysql_fetch_object($res))) {
				
			// couleur de l'img en fonction du statut
			if ($expl->explnum_docnum_statut) {
				$rqt_st = "SELECT * FROM explnum_statut WHERE  id_explnum_statut='".$expl->explnum_docnum_statut."' ";
				$Query_statut = pmb_mysql_query($rqt_st, $dbh)or die ($rqt_st. " ".pmb_mysql_error()) ;
				$r_statut = pmb_mysql_fetch_object($Query_statut);
				$class_img = " class='docnum_".$r_statut->class_html."' ";
				if ($expl->explnum_docnum_statut>1) {
					$txt = $r_statut->opac_libelle;
				} else $txt="";

				$statut_libelle_div="
					<div id='zoom_statut_docnum".$expl->explnum_id."' style='border: 2px solid rgb(85, 85, 85); background-color: rgb(255, 255, 255); position: absolute; z-index: 2000; display: none;'>
					<b>$txt</b>
					</div>
					";
			} else {
				$class_img = " class='docnum_statutnot1' " ;
				$txt = "" ;
			}
				
			if ($i==1) $ligne="<tr><td class='docnum' width='25%'>!!1!!</td><td class='docnum' width='25%'>!!2!!</td><td class='docnum' width='25%'>!!3!!</td><td class='docnum' width='25%'>!!4!!</td></tr>" ;
			$tlink = '';
			if ($link_expl) {
				$tlink = str_replace("!!explnum_id!!", $expl->explnum_id, $link_expl);
				$tlink = str_replace("!!notice_id!!", $expl->explnum_notice, $tlink);
				$tlink = str_replace("!!bulletin_id!!", $expl->explnum_bulletin, $tlink);
			}
			$alt = htmlentities($expl->explnum_nom." - ".$expl->explnum_mimetype,ENT_QUOTES, $charset) ;
				
			if ($prefix_url_image) $tmpprefix_url_image = $prefix_url_image;
			else $tmpprefix_url_image = "./" ;

			if ($expl->explnum_vignette) $obj="<img src='".$tmpprefix_url_image."vig_num.php?explnum_id=$expl->explnum_id' alt='$alt' title='$alt' border='0'>";
			else // trouver l'icone correspondant au mime_type
				$obj="<img src='".$tmpprefix_url_image."images/mimetype/".icone_mimetype($expl->explnum_mimetype, $expl->explnum_extfichier)."' alt='$alt' title='$alt' border='0'>";

			$obj_suite="$statut_libelle_div
			<a  href='#' onmouseout=\"z=document.getElementById('zoom_statut_docnum".$expl->explnum_id."'); z.style.display='none'; \" onmouseover=\"z=document.getElementById('zoom_statut_docnum".$expl->explnum_id."'); z.style.display=''; \">
			<div class='vignette_doc_num' ><img $class_img width='10' height='10' src='".get_url_icon('spacer.gif')."'></div>
			</a>
			";
			$expl_liste_obj = "";
			$expl_liste_obj .= "<a href='".$tmpprefix_url_image."doc_num.php?explnum_id=$expl->explnum_id' alt='$alt' title='$alt' target='_blank'>".$obj."</a>$obj_suite<br />" ;
					
			if ($_mimetypes_byext_[$expl->explnum_extfichier]["label"]) $explmime_nom = $_mimetypes_byext_[$expl->explnum_extfichier]["label"] ;
			elseif ($_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"]) $explmime_nom = $_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"] ;
			else $explmime_nom = $expl->explnum_mimetype ;
			if(isset($param_aff["mine_type"])) $explmime_nom="";
			if ($tlink) {
				$expl_liste_obj .= "<a href='$tlink'>";
				$expl_liste_obj .= htmlentities($expl->explnum_nom,ENT_QUOTES, $charset)."</a><div class='explnum_type'>".htmlentities($explmime_nom,ENT_QUOTES, $charset)."</div>";
			} else {
				$expl_liste_obj .= htmlentities($expl->explnum_nom,ENT_QUOTES, $charset)."<div class='explnum_type'>".htmlentities($explmime_nom,ENT_QUOTES, $charset)."</div>";
			}
			//recherche des concepts...
			$query = "select num_concept,value from index_concept join skos_fields_global_index on num_concept = id_item and code_champ = 1  where num_object = ".$expl->explnum_id." and type_object = 11 order by order_concept";
			$result = pmb_mysql_query($query,$dbh);
			$concept= "";
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					if($concept){
						$concept.=" / ";
					}	
					if (SESSrights & AUTORITES_AUTH){
						$concept.="<a href='".$base_path."/autorites.php?categ=see&sub=concept&id=".$row->num_concept."' title='".addslashes($msg['concept_menu'].": ".htmlentities($row->value,ENT_QUOTES,$charset))."'>".htmlentities($row->value,ENT_QUOTES,$charset)."</a>";
					}else{
						$concept.="<span title='".addslashes($msg['concept_menu'].": ".htmlentities($row->value,ENT_QUOTES,$charset))."'>".htmlentities($row->value,ENT_QUOTES,$charset)."</span>";
					}
				}
			}	
		
			$expl_liste_obj .= $concept."";
			$ligne = str_replace("!!$i!!", $expl_liste_obj, $ligne);
			$i++;
			if ($i==5) {
				$ligne_finale .= $ligne ;
				$i=1;
			}
		}
		if (!$ligne_finale) $ligne_finale = $ligne ;
		elseif ($i!=1) $ligne_finale .= $ligne ;

		$ligne_finale = str_replace('!!2!!', "&nbsp;", $ligne_finale);
		$ligne_finale = str_replace('!!3!!', "&nbsp;", $ligne_finale);
		$ligne_finale = str_replace('!!4!!', "&nbsp;", $ligne_finale);

	} else return "";
	$entry = "<table class='docnum'>$ligne_finale</table>";
	return $entry;
}
?>