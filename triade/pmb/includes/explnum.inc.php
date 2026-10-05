<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum.inc.php,v 1.148.2.3.2.3 2025/05/02 12:00:29 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

use Pmb\Digitalsignature\Models\DocnumCertifier;

global $class_path;
require_once ("$class_path/curl.class.php");
require_once ($class_path . "/cache_factory.class.php");

if (! function_exists('file_put_contents'))
{
    function file_put_contents($filename, $data) {
        $f = @fopen($filename, 'w');
        if (! $f) {
            return false;
        } else {
            $bytes = fwrite($f, $data);
            fclose($f);
            return $bytes;
        }
    }
}

// charge le tableau des extensions/mimetypes, on en a besoin en maj comme en affichage
function create_tableau_mimetype()
{
    global $lang, $charset, $KEY_CACHE_FILE_XML;
    global $include_path, $base_path;
    global $_mimetypes_bymimetype_, $_mimetypes_byext_;

    if (! empty($_mimetypes_bymimetype_)) {
        return;
    }
    $_mimetypes_bymimetype_ = array();
    $_mimetypes_byext_ = array();

    if (file_exists($include_path . "/mime_types/" . $lang . "_subst.xml")) {
        $fic_mime_types = $include_path . "/mime_types/" . $lang . "_subst.xml";
    } else {
        $fic_mime_types = $include_path . "/mime_types/" . $lang . ".xml";
    }
    $fileInfo = pathinfo($fic_mime_types);
    $fileName = preg_replace("/[^a-z0-9]/i", "", $fileInfo['dirname'] . $fileInfo['filename'] . $charset);
    $tempFile = $base_path . "/temp/XML" . $fileName . ".tmp";
    $dejaParse = false;

    $cache_php = cache_factory::getCache();
    $key_file = "";
    if ($cache_php) {
        $key_file = getcwd() . $fileName . filemtime($fic_mime_types);
        $key_file = $KEY_CACHE_FILE_XML . md5($key_file);
        if ($tmp_key = $cache_php->getFromCache($key_file)) {
            if ($cache = $cache_php->getFromCache($tmp_key)) {
                if (count($cache) == 2) {
                    $_mimetypes_bymimetype_ = $cache[0];
                    $_mimetypes_byext_ = $cache[1];
                    $dejaParse = true;
                }
            }
        }
    } else {
        if (file_exists($tempFile)) {
            // Le fichier XML original a-t-il ete modifie ulterieurement ?
            if (filemtime($fic_mime_types) > filemtime($tempFile)) {
                // on va re-generer le pseudo-cache
                unlink($tempFile);
            } else {
                $dejaParse = true;
            }
        }
        if ($dejaParse) {
            $tmp = fopen($tempFile, "r");
            $cache = unserialize(fread($tmp, filesize($tempFile)));
            fclose($tmp);
            if (count($cache) == 2) {
                $_mimetypes_bymimetype_ = $cache[0];
                $_mimetypes_byext_ = $cache[1];
            } else {
                // SOUCIS de cache...
                unlink($tempFile);
                $dejaParse = false;
            }
        }
    }

    if (! $dejaParse) {
        require_once ("$include_path/parser.inc.php");
		$fonction = array ("MIMETYPE" => "__mimetype__");
        _parser_($fic_mime_types, $fonction, "MIMETYPELIST");

        if ($key_file) {
			$key_file_content=$KEY_CACHE_FILE_XML.md5(serialize(array($_mimetypes_bymimetype_,$_mimetypes_byext_)));
			$cache_php->setInCache($key_file_content, array($_mimetypes_bymimetype_,$_mimetypes_byext_));
            $cache_php->setInCache($key_file, $key_file_content);
        } else {
            $tmp = fopen($tempFile, "wb");
            fwrite($tmp, serialize(array(
                $_mimetypes_bymimetype_,
                $_mimetypes_byext_
            )));
            fclose($tmp);
        }
    }
}

function __mimetype__($param)
{
    global $_mimetypes_bymimetype_, $_mimetypes_byext_;

    $mimetype_rec = array();
    $mimetype_rec["plugin"] = $param["PLUGIN"];
    $mimetype_rec["icon"] = $param["ICON"];
    $mimetype_rec["label"] = (isset($param["LABEL"]) ? $param["LABEL"] : '');
    $mimetype_rec["embeded"] = $param["EMBEDED"];

    $_mimetypes_bymimetype_[$param["NAME"]] = $mimetype_rec;

    for ($i = 0; $i < count($param["EXTENSION"]); $i ++) {
        $mimetypeext_rec = array();
        $mimetypeext_rec = $mimetype_rec;
        $mimetypeext_rec["mimetype"] = $param["NAME"];
        if (isset($param["EXTENSION"][$i]["LABEL"])) {
            $mimetypeext_rec["label"] = $param["EXTENSION"][$i]["LABEL"];
        }
        $_mimetypes_byext_[$param["EXTENSION"][$i]["value"]] = $mimetypeext_rec;
    }
}

function extension_fichier($fichier)
{
    $f = strrev($fichier);
    $ext = substr($f, 0, strpos($f, "."));
    return strtolower(strrev($ext));
}

function icone_mimetype($mimetype, $ext)
{
    global $_mimetypes_bymimetype_, $_mimetypes_byext_;
    // trouve l'icone associee au mimetype
    // sinon trouve l'icone associee a l'extension
    if (isset($_mimetypes_bymimetype_[$mimetype]["icon"]) && $_mimetypes_bymimetype_[$mimetype]["icon"]) {
        return $_mimetypes_bymimetype_[$mimetype]["icon"];
    }
    if (isset($_mimetypes_byext_[$ext]["icon"]) && $_mimetypes_byext_[$ext]["icon"]) {
        return $_mimetypes_byext_[$ext]["icon"];
    }
    return "unknown.gif";
}

function trouve_mimetype($fichier, $ext = '')
{
    global $_mimetypes_byext_;

    if ($ext != '') {
        // chercher le mimetype associe a l'extension : si trouvee nickel, sinon : ""
        if (! empty($_mimetypes_byext_[$ext]["mimetype"])) {
            return $_mimetypes_byext_[$ext]["mimetype"];
        }
    }
    if (extension_loaded('fileinfo') && is_file($fichier)) {
        $mime_type = mime_content_type($fichier);
        if (! empty($mime_type)) {
            return $mime_type;
        }
    }
    return '';
}

function reduire_image($userfile_name)
{
    global $pmb_vignette_x;
    global $pmb_vignette_y;
    global $base_path;
    global $pmb_curl_available;

    if (! $pmb_vignette_x) $pmb_vignette_x = 100;
    if (! $pmb_vignette_y) $pmb_vignette_y = 100;
    $fichier_tmp = '';
    $contenu_vignette = '';

    if (file_exists("$base_path/temp/$userfile_name")) {
        $source_file = realpath("$base_path/temp/$userfile_name");
    } else {
        // Il s'agit d'une url, on copie le fichier en local
        $nom_temp = session_id() . microtime();
        $nom_temp = str_replace(' ', '_', $nom_temp);
        $nom_temp = str_replace('.', '_', $nom_temp);
        $fichier_tmp = $base_path . "/temp/" . $nom_temp;
        if ($pmb_curl_available && ! file_exists($userfile_name)) {
            $aCurl = new Curl();
            $aCurl->timeout = 10;
            $aCurl->set_option('CURLOPT_SSL_VERIFYPEER', false);
            $aCurl->save_file_name = $fichier_tmp;
            $aCurl->get($userfile_name);
        } else if (file_exists($userfile_name)) {
            $handle = fopen($userfile_name, "rb");
            $filecontent = stream_get_contents($handle);
            fclose($handle);
            $fd = fopen($fichier_tmp, "w");
            fwrite($fd, $filecontent);
            fclose($fd);
        }
        $source_file = realpath($fichier_tmp);
    }

    if (! $source_file) {
        return $contenu_vignette;
    }

    $rotation = 0;

    if (extension_loaded('exif') && (false !== exif_imagetype($source_file)) ) {
        $exif = @exif_read_data($source_file);
        $orientation = $exif['Orientation'] ?? 0;
        switch ($orientation) {
            case 6: // rotate 90 degrees CW
                $rotation = 90;
                break;
            case 8: // rotate 90 degrees CCW
                $rotation = -90;
                break;
        }
    }
    
    $error = true;
    if (extension_loaded('imagick')) {
        mysql_set_wait_timeout(3600);
        $error = false;
        try {
            $img = new Imagick();
            $img->readImage($source_file . "[0]");
            $img->setImageBackgroundColor('white');
            if($rotation) {
                $img->rotateimage('white', $rotation);
            }

            // Imagick >= 3.4.4
            if (method_exists('Imagick', 'mergeImageLayers') && method_exists('Imagick', 'setImageAlphaChannel') && defined('Imagick::ALPHACHANNEL_REMOVE')) {
                $img->setImageAlphaChannel(imagick::ALPHACHANNEL_REMOVE);
                $img->mergeImageLayers(imagick::LAYERMETHOD_FLATTEN);

                // Imagick < 3.4.4
            } elseif (method_exists('Imagick', 'flattenImages')) {
                $img = $img->flattenImages();
            }

            if (($img->getImageWidth() > $pmb_vignette_x) || ($img->getImageHeight() > $pmb_vignette_y)) { // Si l'image est trop grande on la reduit
                $img->thumbnailimage($pmb_vignette_x, $pmb_vignette_y, true);
            }
            $img->setImageFormat("png");
            $img->setCompression(Imagick::COMPRESSION_LZW);
            $img->setCompressionQuality(90);
            $contenu_vignette = $img->getImageBlob();
        } catch (Exception $ex) {
            $error = true;
        }
    }

    if ($error) {
        $source_file = str_replace("[0]", "", $source_file);
        $size = @getimagesize($source_file);
        /*
         * ".gif"=>"1",
         * ".jpg"=>"2",
         * ".jpeg"=>"2",
         * ".png"=>"3",
         * ".swf"=>"4",
         * ".psd"=>"5",
         * ".bmp"=>"6");
         */

        if (isset($size[2])) {
            switch ($size[2]) {
                case 1:
                    $src_img = imagecreatefromgif($source_file);
                    break;
                case 2:
                    $src_img = imagecreatefromjpeg($source_file);
                    break;
                case 3:
                    $src_img = imagecreatefrompng($source_file);
                    break;
                case 6:
                    $src_img = imagecreatefromwbmp($source_file);
                    break;
                default:
                    break;
            }
        }

        $erreur_vignette = 0;
        if (! empty($src_img)) {

            if($rotation) {
                $src_img = imagerotate($src_img, -$rotation, 0);
            }

            $rs = $pmb_vignette_x / $pmb_vignette_y;
            $taillex = imagesx($src_img);
            $tailley = imagesy($src_img);
            if (! $taillex || ! $tailley)
                return "";
            if (($taillex > $pmb_vignette_x) || ($tailley > $pmb_vignette_y)) {
                $r = $taillex / $tailley;
                if (($r < 1) && ($rs < 1)) {
                    // Si x plus petit que y et taille finale portrait
                    // Si le format final est plus large en proportion
                    if ($rs > $r) {
                        $new_h = $pmb_vignette_y;
                        $new_w = $new_h * $r;
                    } else {
                        $new_w = $pmb_vignette_x;
                        $new_h = $new_w / $r;
                    }
                } else if (($r < 1) && ($rs >= 1)) {
                    // Si x plus petit que y et taille finale paysage
                    $new_h = $pmb_vignette_y;
                    $new_w = $new_h * $r;
                } else if (($r > 1) && ($rs < 1)) {
                    // Si x plus grand que y et taille finale portrait
                    $new_w = $pmb_vignette_x;
                    $new_h = $new_w / $r;
                } else {
                    // Si x plus grand que y et taille finale paysage
                    if ($rs < $r) {
                        $new_w = $pmb_vignette_x;
                        $new_h = $new_w / $r;
                    } else {
                        $new_h = $pmb_vignette_y;
                        $new_w = $new_h * $r;
                    }
                }
            } else {
                $new_h = $tailley;
                $new_w = $taillex;
            }
            $dst_img = imagecreatetruecolor($pmb_vignette_x, $pmb_vignette_y);
            ImageSaveAlpha($dst_img, true);
            ImageAlphaBlending($dst_img, false);
            imagefilledrectangle($dst_img, 0, 0, $pmb_vignette_x, $pmb_vignette_y, imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
            imagecopyresized($dst_img, $src_img, round(($pmb_vignette_x - $new_w) / 2), round(($pmb_vignette_y - $new_h) / 2), 0, 0, $new_w, $new_h, ImageSX($src_img), ImageSY($src_img));
            imagepng($dst_img, "$base_path/temp/" . SESSid);
            $fp = fopen("$base_path/temp/" . SESSid, "r");
            $contenu_vignette = fread($fp, filesize("$base_path/temp/" . SESSid));
            if (! $fp || $contenu_vignette == "")
                $erreur_vignette ++;
            fclose($fp);
            unlink("$base_path/temp/" . SESSid);
        }
    }

    if ($fichier_tmp && file_exists($fichier_tmp)) {
        unlink($fichier_tmp);
    }

    return $contenu_vignette;
}

function construire_vignette($vignette_name = '', $userfile_name = '', $url = '')
{
    $contenu_vignette = "";
    $eh = events_handler::get_instance();
    $event = new event_explnum("explnum", "contruire_vignette");
    $eh->send($event);
    $contenu_vignette = $event->get_contenu_vignette();
    if ($contenu_vignette) {
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
    return $contenu_vignette;
}

// fonction retournant les infos d'exemplaires numeriques pour une notice ou un bulletin donne
function show_explnum_per_notice($no_notice, $no_bulletin, $link_expl = '', $param_aff = array(), $return_count = false, $context_dsi_id_bannette = 0)
{

    // params :
    // $link_expl= lien associe a l'exemplaire avec !!explnum_id!! a mettre a jour
    global $charset;
    global $use_dsi_diff_mode;
    global $base_path, $msg;
    global $pmb_docnum_img_folder_id;
    global $pmb_explnum_order;
    global $pmb_url_base, $use_opac_url_base, $opac_url_base;
    global $pmb_digital_signature_activate;

    if (! $no_notice && ! $no_bulletin)
        return "";

    if (($use_dsi_diff_mode == 1) && ! explnum::allow_opac($no_notice, $no_bulletin)) { // Si je suis en dsi je regarde les droits opac sur les explnum
        return "";
    }

    global $_mimetypes_bymimetype_, $_mimetypes_byext_;
    create_tableau_mimetype();

    // recuperation du nombre d'exemplaires
    $requete = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier, explnum_docnum_statut
			FROM explnum WHERE ";
    if ($no_notice)
        $requete .= "explnum_notice='$no_notice' ";
    else
        $requete .= "explnum_bulletin='$no_bulletin' ";
    if ($no_notice)
        $requete .= "union SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier, explnum_docnum_statut
			FROM explnum, bulletins
			WHERE bulletin_id = explnum_bulletin
			AND bulletins.num_notice='" . $no_notice . "'";
    if ($pmb_explnum_order)
        $requete .= " order by " . $pmb_explnum_order;
    else
        $requete .= " order by explnum_mimetype, explnum_id ";
    $res = pmb_mysql_query($requete);
    $nb_ex = pmb_mysql_num_rows($res);
    if ($return_count) {
        return $nb_ex;
    }
    $map_display = '';
    if ($nb_ex) {
        // on recupere les donnees des exemplaires
        $i = 1;
        $ligne_finale = '';

        global $pmb_digital_signature_activate;
        if ($pmb_digital_signature_activate) {
            $ligne_finale .= DocnumCertifier::getJsCheck();
            $certifier = new DocnumCertifier(null);
        }

        while (($expl = pmb_mysql_fetch_object($res))) {
            // couleur de l'img en fonction du statut
            if ($expl->explnum_docnum_statut) {
                $rqt_st = "SELECT * FROM explnum_statut WHERE  id_explnum_statut='" . $expl->explnum_docnum_statut . "' ";
                $Query_statut = pmb_mysql_query($rqt_st) or die($rqt_st . " " . pmb_mysql_error());
                $r_statut = pmb_mysql_fetch_object($Query_statut);
                $class_img = " class='docnum_" . $r_statut->class_html . "' ";
                if ($expl->explnum_docnum_statut > 1) {
                    $txt = $r_statut->opac_libelle;
                } else
                    $txt = "";

                $statut_libelle_div = "
					<div id='zoom_statut_docnum" . $expl->explnum_id . "' style='border: 2px solid rgb(85, 85, 85); background-color: rgb(255, 255, 255); position: absolute; z-index: 2000; display: none;'>
						<b>$txt</b>
					</div>
				";
            } else {
                $class_img = " class='docnum_statutnot1' ";
                $txt = "";
            }

            if ($i == 1)
                $ligne = "<tr><td id='explnum_" . $expl->explnum_id . "' class='docnum center' style='width:25%'>!!1!!</td><td class='docnum center' style='width:25%'>!!2!!</td><td class='docnum center' style='width:25%'>!!3!!</td><td class='docnum center' style='width:25%'>!!4!!</td></tr>";
            $tlink = '';
            if ($link_expl) {
                $tlink = str_replace("!!explnum_id!!", $expl->explnum_id, $link_expl);
                $tlink = str_replace("!!notice_id!!", $expl->explnum_notice, $tlink);
                $tlink = str_replace("!!bulletin_id!!", $expl->explnum_bulletin, $tlink);
            }
            $alt = htmlentities($expl->explnum_nom . " - " . $expl->explnum_mimetype, ENT_QUOTES, $charset);

            global $prefix_url_image;
            if ($prefix_url_image)
                $tmpprefix_url_image = $prefix_url_image;
            else
                $tmpprefix_url_image = "./";

            if ($expl->explnum_vignette || ! empty($pmb_docnum_img_folder_id)) {
                $thumbnail_url = explnum::get_thumbnail_url($expl->explnum_vignette, $expl->explnum_id);
                $obj = "<img src='" . $thumbnail_url . "' alt='$alt' title='$alt' style='border:0px' loading='lazy'>";
            } else { // trouver l'icone correspondant au mime_type
                $obj = "<img src='" . $tmpprefix_url_image . "images/mimetype/" . icone_mimetype($expl->explnum_mimetype, $expl->explnum_extfichier) . "' alt='$alt' title='$alt' style='border:0px' loading='lazy'>";
            }

            $obj_suite = "$statut_libelle_div
				<a  href='#' onmouseout=\"z=document.getElementById('zoom_statut_docnum" . $expl->explnum_id . "'); z.style.display='none'; \" onmouseover=\"z=document.getElementById('zoom_statut_docnum" . $expl->explnum_id . "'); z.style.display=''; \">
					<div class='vignette_doc_num' ><img $class_img width='10' height='10' src='" . $tmpprefix_url_image . "images/spacer.gif'></div>
				</a>
			";
            $expl_liste_obj = "<span class='center'>";
            $expl_liste_obj .= "<a href='" . $tmpprefix_url_image . "doc_num.php?explnum_id=$expl->explnum_id' alt='$alt' title='$alt' target='_blank'>" . $obj . "</a>$obj_suite<br />";

            if (isset($_mimetypes_byext_[$expl->explnum_extfichier]["label"]) && $_mimetypes_byext_[$expl->explnum_extfichier]["label"])
                $explmime_nom = $_mimetypes_byext_[$expl->explnum_extfichier]["label"];
            elseif (isset($_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"]) && $_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"])
                $explmime_nom = $_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"];
            else
                $explmime_nom = $expl->explnum_mimetype;
            if (isset($param_aff["mine_type"]))
                $explmime_nom = "";

            $expl_name = htmlentities($expl->explnum_nom, ENT_QUOTES, $charset);

            // test sur la signature de doc num
            if ($pmb_digital_signature_activate) {
                $explnum = new explnum($expl->explnum_id);
                $certifier->setEntity($explnum);

                if ($certifier->checkSignExists()) {
                    $file_link = $certifier->getCmsFilePath();
                    $tlink = "";
                    $expl_name = "
                        <span style='cursor: default' title='" . $msg['digital_signature_already_signed_docnum'] . "'>" . $expl_name . "
                            <span id='docnum_check_sign_" . $expl->explnum_id . "'></span>
                        </span>
                        <script>
                            certifier.chksign(" . $expl->explnum_id . ", 'docnum', false, '" . $file_link . "');
                        </script>
                    ";
                }
            }

            // if ($certifier->checkSignExists() && !$pmb_digital_signature_activate) {
            // $tlink = "";
            // $expl_name = "
            // <span style='cursor: default' title='" . $msg['digital_signature_already_signed_docnum']. "'>"
            // .$expl_name. "
            // <span id='docnum_check_sign_" . $expl->explnum_id . "'></span>
            // </span>
            // ";
            // }

            if ($tlink) {
                $expl_name = "<a class='docnum_name_link' href='$tlink'>" . $expl_name . "</a>";
            }

            $expl_liste_obj .= $expl_name;

            // Regime de licence
            $expl_liste_obj .= explnum_licence::get_explnum_licence_picto($expl->explnum_id);

            $expl_liste_obj .= "<div class='explnum_type'>" . htmlentities($explmime_nom, ENT_QUOTES, $charset) . "</div>";
            // recherche des concepts...
            $query = "select num_concept,value from index_concept join skos_fields_global_index on num_concept = id_item and code_champ = 1  where num_object = " . $expl->explnum_id . " and type_object = 11 order by order_concept";
            $result = pmb_mysql_query($query);
            $concept = "";
            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_object($result)) {
                    if ($concept) {
                        $concept .= " / ";
                    }
                    if (SESSrights & AUTORITES_AUTH) {
                        $concept .= "<a href='" . $base_path . "/autorites.php?categ=see&sub=concept&id=" . $row->num_concept . "' title='" . addslashes($msg['concept_menu'] . ": " . htmlentities($row->value, ENT_QUOTES, $charset)) . "'>" . htmlentities($row->value, ENT_QUOTES, $charset) . "</a>";
                    } else {
                        $concept .= "<span title='" . addslashes($msg['concept_menu'] . ": " . htmlentities($row->value, ENT_QUOTES, $charset)) . "'>" . htmlentities($row->value, ENT_QUOTES, $charset) . "</span>";
                    }
                }
            }

            $expl_liste_obj .= $concept . "</span>";
            $ligne = str_replace("!!$i!!", $expl_liste_obj, $ligne);
            $i ++;
            if ($i == 5) {
                $ligne_finale .= $ligne;
                $i = 1;
            }
        }
        if (! $ligne_finale)
            $ligne_finale = $ligne;
        elseif ($i != 1)
            $ligne_finale .= $ligne;

        $ligne_finale = str_replace('!!2!!', "&nbsp;", $ligne_finale);
        $ligne_finale = str_replace('!!3!!', "&nbsp;", $ligne_finale);
        $ligne_finale = str_replace('!!4!!', "&nbsp;", $ligne_finale);
    } else
        return "";
    $entry = $map_display . "<table class='docnum' role='presentation'>$ligne_finale</table>";
    return $entry;
}

function extract_metas($filename, $mimetype, $tmp = false)
{
    global $base_path, $class_path;
    global $charset;
    // $metas = array();
    switch ($mimetype) {
        // EPub
        case "application/epub+zip":
            // Exiftool ne donnerait rien, mais un Epub contient toutes les infos qui nous faut !
            require_once ($class_path . "/epubData.class.php");
            $epub = new epub_Data($filename);
            $metas = $epub->metas;
            break;
        case "application/pdf":
            // exec("exiftool -struct -J -q ".$filename,$metas);
            // $metas = json_decode(implode("\n",$metas),true);
            exec("exiftool " . $filename, $tab);
            $metas = array();
            foreach ($tab as $row) {
                $elem = explode(":", $row);
                $key = trim(str_replace(" ", "", array_shift($elem)));
                $value = trim(implode(":", $elem));
                if ($charset != "utf-8") {
                    $key = encoding_normalize::utf8_decode($key);
                    $value = encoding_normalize::utf8_decode($value);
                }
                $metas[$key] = $value;
            }
            break;
        default:
            $type = substr($mimetype, 0, strpos($mimetype, "/"));
            switch ($type) {
                case "image":
                case "video":
                case "audio":
                    exec("exiftool " . $filename, $tab);
                    $metas = array();
                    foreach ($tab as $row) {
                        $elem = explode(":", $row);
                        $key = trim(str_replace(" ", "", array_shift($elem)));
                        $value = trim(implode(":", $elem));
                        if ($charset != "utf-8" && mb_detect_encoding($value) == 'UTF-8') {
                            $key = encoding_normalize::utf8_decode($key);
                            $value = encoding_normalize::utf8_decode($value);
                        }
                        $metas[$key] = $value;
                    }
                    break;

                case "text":
                    // pas de metas pertinentes sur un fichier texte...
                    break;
                default:
                    if (! extension_fichier(basename($filename))) {
                        $new_name = basename($filename) . "temp"; // Pour eviter que si pas d'extension on perde le fichier
                    } else {
                        $new_name = str_replace(extension_fichier(basename($filename)), "pdf", basename($filename));
                    }
                    $new_path = dirname($filename) . "/" . $new_name;
                    exec("curl http://localhost:8080/converter/converted/" . $new_name . " -F \"inputDocument=@$filename\" > " . $new_path); // Ne doit marcher que dans un cas tres precis, pas vrai Arnaud
                    $metas = extract_metas($new_path, "application/pdf", true);
                    break;
            }

            break;
    }
    if ($tmp)
        unlink($filename);
    return $metas;
}

