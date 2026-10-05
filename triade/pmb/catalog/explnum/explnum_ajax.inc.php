<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_ajax.inc.php,v 1.28.4.1 2025/03/14 08:13:26 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $quoifaire, $id, $id_repertoire, $explnum_id, $speaker_id, $author_id, $segment_id, $start, $end, $segments_ids;
global $selectorIndex, $protocol, $base_path, $uploadDir, $fnc, $bul_id, $msg, $acces_m, $gestion_acces_active, $gestion_acces_user_notice;
global $PMBuserid, $charset, $deletion_link, $f_notice, $f_explnum_id, $nberrors, $conservervignette, $f_statut_chk, $book_lender_id, $retour;
global $forcage, $save_status, $f_bulletin, $f_nom, $f_url, $f_explnum_statut, $f_url_vignette;
global $pmb_explnum_controle_doublons;

require_once($class_path.'/explnum_associate_svg.class.php');
require_once($class_path.'/explnum_licence/explnum_licence.class.php');
require_once($class_path.'/storages/storages.class.php');
require_once($include_path.'/bull_info.inc.php');
switch($quoifaire){

    case 'exist_file':
        existing_file($id,$id_repertoire);
        break;
    case 'get_associate_svg':
        get_associate_svg($explnum_id);
        break;
    case 'get_associate_js':
        get_associate_js($explnum_id);
        break;
    case 'update_associate_author':
        update_associate_author($speaker_id, $author_id);
        break;
    case 'update_associate_speaker':
        update_associate_speaker($segment_id, $speaker_id);
        break;
    case 'add_new_speaker':
        add_new_speaker($explnum_id);
        break;
    case 'delete_associate_speaker':
        delete_associate_speaker($speaker_id);
        break;
    case 'add_new_segment':
        add_new_segment($explnum_id, $speaker_id, $start, $end);
        break;
    case 'delete_segments':
        delete_segments($segments_ids);
        break;
    case 'update_segment_time':
        update_segment_time($segment_id, $start, $end);
        break;
    case 'get_licence_profiles':
        $id = intval($id);
        $selectorIndex =intval($selectorIndex);
        $explnum_licence = new explnum_licence($id);
        print $explnum_licence->get_profiles_form_list(array(), $selectorIndex);
        break;
    case 'get_licence_tooltip':
    	$id = intval($id);
        print explnum_licence::get_explnum_licence_tooltip($id);
        break;
    case 'get_licence_as_pdf':
    	$id = intval($id);
        print explnum_licence::get_explnum_licence_as_pdf($id);
        break;
    case 'get_licence_quotation':
    	$id = intval($id);
        print explnum_licence::get_explnum_licence_quotation($id);
        break;
    case 'upload_docnum':
        $protocol = $_SERVER["SERVER_PROTOCOL"];
        $uploadDir = $base_path."/temp/";
        switch ($fnc){
            case 'upl':
                if (is_dir($uploadDir)) {
                	if (is_writable($uploadDir)) {
                    	$explnum = explnum::create_doc_from_file();
                    	if(is_object($explnum)) {
	                        $link_expl = $explnum->get_display_link();
	                        $link_expl = str_replace('!!analysis_id!!', $explnum->explnum_notice, $link_expl);
	                        $link_expl = str_replace('!!bul_id!!', (isset($bul_id) ? $bul_id : ''), $link_expl);
	                        print encoding_normalize::json_encode(
	                            array(
	                                'response' => show_explnum_per_notice($explnum->explnum_notice, $explnum->explnum_bulletin, $link_expl),
	                                'title' => '<b>'.$msg['explnum_docs_associes'].'</b> ('.show_explnum_per_notice($explnum->explnum_notice, $explnum->explnum_bulletin, $explnum->get_display_link(),array(),true).')',
	   	                            'bull_display' => (!empty($bul_id) ? get_analysis($bul_id) : ''),
                            		'has_doublons' => ($pmb_explnum_controle_doublons ? count($explnum->has_doublons()) : 0)
	                            )
                            );
	                        //indexation de la notice liee
	                        if (!empty($explnum->explnum_notice)) {
	                            notice::update_index($explnum->explnum_notice, "explnum");
	                        }
                    	}
                        /**
                         * TODO: check explnum bulletin ou explnum notice
                         * Faire le traitement en fonction
                         */

                    }else{
                        header($protocol.' 405 Method Not Allowed');
                        exit('Upload directory is not writable.');
                    }
                }else{
                    header($protocol.' 404 Not Found');
                    exit('Upload directory does not exist.');
                }
                break;
            case 'del':
                break;
            case 'resume':
                break;
            case 'getNumWrittenBytes':
                break;
        }
        break;
    case 'get_form':
        //verification des droits de modification notice
        $acces_m=1;
        if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
            require_once("$class_path/acces.class.php");
            $ac= new acces();
            $dom_1= $ac->setDomain(1);
            $acces_m = $dom_1->getRights($PMBuserid,$id,8);
        }

        if ($acces_m==0) {

            error_message('', htmlentities($dom_1->getComment('mod_enum_error'), ENT_QUOTES, $charset), 1, '');

        } else {
            /**
             * TODO: Créer une méthode statique dans la classe
             * explnum permettant de retourner le bon lien de suppression
             * suivant le type d'entité
             *
             */
            if(isset($bul_id)){ //Cas d'un bulletin
                $deletion_link = "./catalog.php?categ=serials&sub=bulletinage&action=explnum_delete&bul_id=".$bul_id."&explnum_id=".$explnum_id;
            }else{
                $deletion_link = "./catalog.php?categ=del_explnum&id=$id&explnum_id=$explnum_id";
            }
            $nex = new explnum($explnum_id, $id, $bul_id);
            print encoding_normalize::utf8_normalize($nex->explnum_form("./catalog.php?categ=explnum_update&sub=update&id=$explnum_id", "./catalog.php?categ=isbd&id=$id",$deletion_link));
        }
        break;
    case 'update':
        $acces_m=1;
        if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
            require_once("$class_path/acces.class.php");
            $ac= new acces();
            $dom_1= $ac->setDomain(1);
            $acces_m = $dom_1->getRights($PMBuserid,$f_notice,8);
        }
        if ($acces_m==0) {
            if (!$f_explnum_id) {
                print encoding_normalize::json_encode(
                    array(
                        'status' => false,
                        'message' => error_message('', htmlentities($dom_1->getComment('mod_noti_error'), ENT_QUOTES, $charset), 1, '', true)
                    )
                );
            } else {
                print encoding_normalize::json_encode(
                    array(
                        'status' => false,
                        'message' => error_message('', htmlentities($dom_1->getComment('mod_enum_error'), ENT_QUOTES, $charset), 1, '', true)
                    )
                );
            }
        }
        $p_perso=new parametres_perso("explnum");
        $nberrors=$p_perso->check_submited_fields();

        if ($nberrors) {
            print encoding_normalize::json_encode(
                array(
                    'status' => false,
                    'message' => error_message_history($msg["notice_champs_perso"],$p_perso->error_message,1)
                )
                );
            exit();
        }
        if(!isset($conservervignette)){
            $conservervignette = 0;
        }
        if(!isset($f_statut_chk)){
            $f_statut_chk = 0;
        }
        if(!isset($book_lender_id)){
            $book_lender_id = array();
        }
        $explnum = new explnum($id);
        $explnum->set_p_perso($p_perso);
        if(!isset($retour)) $retour = '';
        if(!isset($forcage)) $forcage = '';
        $save_status = $explnum->mise_a_jour($f_notice, $f_bulletin, $f_nom, $f_url, $retour, $conservervignette, $f_statut_chk, $f_explnum_statut, $book_lender_id, $forcage, $f_url_vignette);
        if($save_status){
            print encoding_normalize::json_encode(
                array(
                    'status' => true,
                    'response' => show_explnum_per_notice($explnum->explnum_notice, $explnum->explnum_bulletin, $explnum->get_display_link()),
                    'title' => '<b>'.$msg['explnum_docs_associes'].'</b> ('.show_explnum_per_notice($explnum->explnum_notice, $explnum->explnum_bulletin, $explnum->get_display_link(),array(),true).')',
                    'record_id' => $explnum->explnum_notice
                )
                );
        }
        break;
}

function existing_file($id,$id_repertoire){
    global $fichier,$charset;

    if(!$id){
        $rqt = "select repertoire_path, explnum_path, repertoire_utf8, explnum_nomfichier as nom, explnum_extfichier as ext from explnum join upload_repertoire on explnum_repertoire=repertoire_id  where explnum_repertoire='$id_repertoire' and explnum_nomfichier ='$fichier'";
        $res = pmb_mysql_query($rqt);

        if(pmb_mysql_num_rows($res)){
            $expl = pmb_mysql_fetch_object($res);
            $path = str_replace('//','/',$expl->repertoire_path.$expl->explnum_path);
            if($expl->repertoire_utf8)
                $path = encoding_normalize::utf8_normalize($path);

                if($expl->ext)
                    $file = substr($expl->nom,0,strpos($expl->nom,"."));
                    else $file = $expl->nom;
                    $exist = false;
                    $i=0;
                    while(!$exist){
                        $i++;
                        $filename = ($i ? $file."_".$i : $file).($expl->ext ? ".".$expl->ext : "");
                        if(!file_exists($path.$filename)){
                            //retour ajax : utf8
                            print ($charset=='utf-8'?$filename:encoding_normalize::utf8_normalize($filename));
                            $exist = true;
                        }
                    }
        } else print "0";
    } else print "0";
}

function get_associate_svg($explnum_id) {
    $explnum_associate_svg = new explnum_associate_svg($explnum_id);
    $svg = $explnum_associate_svg->getSvg(true);
    ajax_http_send_response($svg,"text/xml");
}

function get_associate_js($explnum_id) {
    $explnum_associate_svg = new explnum_associate_svg($explnum_id);
    $js = $explnum_associate_svg->getJs(true);
    ajax_http_send_response($js,"text/xml");
}

function update_associate_author($speaker_id, $author_id) {
    $query = 'update explnum_speakers set explnum_speaker_author = '.$author_id.' where explnum_speaker_id = '.$speaker_id;
    pmb_mysql_query($query);
}

function update_associate_speaker($segment_id, $speaker_id) {
    $query = 'update explnum_segments set explnum_segment_speaker_num = '.$speaker_id.' where explnum_segment_id = '.$segment_id;
    pmb_mysql_query($query);
}

function add_new_speaker($explnum_id) {
    $query = 'insert into explnum_speakers (explnum_speaker_explnum_num, explnum_speaker_speaker_num) values ('.$explnum_id.', "PMB")';
    pmb_mysql_query($query);
}

function delete_associate_speaker($speaker_id) {
    $query = 'delete from explnum_speakers where explnum_speaker_id = '.$speaker_id;
    pmb_mysql_query($query);
}

function add_new_segment($explnum_id, $speaker_id, $start, $end) {
    if (!$speaker_id) {
        $query = 'insert into explnum_speakers (explnum_speaker_explnum_num, explnum_speaker_speaker_num) values ('.$explnum_id.', "PMB")';
        pmb_mysql_query($query);
        $speaker_id = pmb_mysql_insert_id();
    }
    $duration = $end - $start;
    $query = 'insert into explnum_segments (explnum_segment_explnum_num, explnum_segment_speaker_num, explnum_segment_start, explnum_segment_duration, explnum_segment_end) value ('.$explnum_id.', '.$speaker_id.', '.$start.', '.$duration.', '.$end.')';
    pmb_mysql_query($query);
}

function delete_segments($segments_ids) {
    $query = 'delete from explnum_segments where explnum_segment_id in ('.$segments_ids.')';
    pmb_mysql_query($query);
}

function update_segment_time($segment_id, $start, $end) {
    $query = 'update explnum_segments set ';

    if ($start) {
        $query .= 'explnum_segment_start = '.$start.', ';
    } else {
        $select = 'select explnum_segment_start from explnum_segments where explnum_segment_id = '.$segment_id;
        $result = pmb_mysql_query($select);
        if ($result && pmb_mysql_num_rows($result)) {
            if ($row = pmb_mysql_fetch_object($result)) {
                $start = $row->explnum_segment_start;
            }
        }
    }

    if ($end) {
        $query .= 'explnum_segment_end = '.$end.', ';
    } else {
        $select = 'select explnum_segment_end from explnum_segments where explnum_segment_id = '.$segment_id;
        $result = pmb_mysql_query($select);
        if ($result && pmb_mysql_num_rows($result)) {
            if ($row = pmb_mysql_fetch_object($result)) {
                $end = $row->explnum_segment_end;
            }
        }
    }

    $duration = $end - $start;

    $query .= 'explnum_segment_duration = '.$duration.' where explnum_segment_id = '.$segment_id;
    pmb_mysql_query($query);
}

?>