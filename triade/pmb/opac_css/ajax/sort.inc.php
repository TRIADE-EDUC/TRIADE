<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sort.inc.php,v 1.12 2024/04/10 14:35:29 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/sort.class.php");

include($include_path."/templates/sort.tpl.php");

global $opac_rgaa_active;

switch($sub){
	case 'get_sort':
		$display = '';
		
		if (empty($sort_name)) {
		    $sort_name = "notices";
		}
		$sort = new sort($sort_name,'session');
		
		//Si vidage historique des tris demandé ?
		if ($raz_sort && $suppr_ids) {
			$suppr_sort_ids = array();
			$suppr_sort_ids = explode(",",$suppr_ids);
			if (count($suppr_sort_ids)) {
				$sort->supprimer($suppr_sort_ids);
			}
		}
		
		if (isset($_GET['page_en_cours'])) {
		    $page_en_cours=strip_tags($_GET['page_en_cours']);
		}
		
		if (isset($params)) {
		    $params_tab = unserialize(rawurldecode(stripslashes($params)));
		    if (count($params_tab)) {
		        foreach ($params_tab as $param_name => $param_value) {
		            $page_en_cours.= '&' . $param_name . '=' . $param_value;
		        }
		    }
		}
		
		if (isset($_GET['modif_sort'])) {
			$temp=array();
			for ($i=0;$i<=4;$i++) {
				if ($_POST['liste_critere'.$i]!="") {
					$temp[$i]=$_POST['croit_decroit'.$i]."_".$_POST['num_text'.$i]."_".$_POST['liste_critere'.$i];	
				}	
			}

			if (count($temp)!=0) {
				$display .= $sort->sauvegarder('','',$temp);
				if (substr($display,0,8)=="<script>") {
					$tmpStr = $sort->show_tris_form();
					if($opac_rgaa_active){
						$tmpStr = str_replace("<!--bouton close-->","<button type='button' class='close button-unstylized' title='".$msg["close"]."' onClick='parent.kill_frame_expl();return false;'><img src='".get_url_icon('close.gif')."' alt='' class='align_right'><span class='visually-hidden'>".$msg['rgaa_modal_close']."</span></button></div>",$tmpStr);
					}else{
						$tmpStr = str_replace("<!--bouton close-->","<a href='#' onClick='parent.kill_frame_expl();return false;'><img src='".get_url_icon('close.gif')."' alt='".$msg["close"]."' style='border:0px' class='align_right'></a></div>",$tmpStr);
					}
			    	$tmpStr = str_replace("!!page_en_cours!!",urlencode($page_en_cours),$tmpStr);
			    	$tmpStr = str_replace("!!page_en_cours1!!",$page_en_cours,$tmpStr);
			    	$tmpStr = str_replace("!!action_suppr_tris!!", "get_sort_content(1, sortSupprIds('cases_a_cocher','cases_suppr'));", $tmpStr);
					$display .= $tmpStr;
					$tmpStr = $sort->show_sel_form();
		    		$tmpStr = str_replace("!!page_en_cours!!",urlencode($page_en_cours),$tmpStr);
					$tmpStr = str_replace("!!page_en_cours1!!",$page_en_cours,$tmpStr);
					$display .= $tmpStr;
				} else {
					$temp_tri=$_SESSION["nb_sortnotices"]-1;
					$display .= "<script> document.location='./index.php?".$page_en_cours."&get_last_query=".$_SESSION["last_query"]."&sort=".$temp_tri."';</script>";	
				}	
			} else {
				$display .= "<script> document.location='./index.php?".$page_en_cours."&get_last_query=".$_SESSION["last_query"]."';</script>";	
			}
		} else {
			$tmpStr = $sort->show_tris_form();
			if($opac_rgaa_active){
				$tmpStr = str_replace("<!--bouton close-->","<button type='button' class='close button-unstylized' title='".$msg["close"]."' onClick='parent.kill_sort_frame();return false;'><img src='".get_url_icon('close.gif')."' alt='' class='align_right'><span class='visually-hidden'>".$msg['rgaa_modal_close']."</span></button></div>",$tmpStr);
			}else{
				$tmpStr = str_replace("<!--bouton close-->","<a href='#' class='close' onClick='parent.kill_sort_frame();return false;'><img src='".get_url_icon('close.gif')."' alt='".$msg["close"]."' style='border:0px' class='align_right'></a></div>",$tmpStr);
			}
			$tmpStr=str_replace("!!page_en_cours!!",urlencode($page_en_cours),$tmpStr);
			$tmpStr=str_replace("!!page_en_cours1!!",$page_en_cours,$tmpStr);
			$tmpStr = str_replace("!!action_suppr_tris!!", "get_sort_content(1, sortSupprIds('cases_a_cocher','cases_suppr'));", $tmpStr);
			$display .= $tmpStr;
			$tmpStr = $sort->show_sel_form();
			$tmpStr = str_replace("!!page_en_cours!!",urlencode($page_en_cours),$tmpStr);
			$tmpStr = str_replace("!!page_en_cours1!!",$page_en_cours,$tmpStr);
			$display .= $tmpStr;
		}
		break;
}

ajax_http_send_response($display);
return;

?>