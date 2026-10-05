<?php
use Pmb\Common\Library\Navbar\Navbar;

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.33.8.3 2025/01/24 16:34:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $page, $sub, $msg, $option_show_notice_fille, $option_show_expl;

require_once($class_path."/search.class.php");
require_once($class_path."/mono_display_expl.class.php");
require_once($class_path."/acces.class.php");

if(!isset($page)) $page = '';
$sc=new search(true,"search_fields_expl");
$sc->init_links();

$option_show_notice_fille = intval($option_show_notice_fille);
$option_show_expl = intval($option_show_expl);

switch ($sub) {
	case "launch":
		if ((string)$page=="" || $page==0) {
		    $_SESSION["CURRENT"]= (is_countable($_SESSION["session_history"]) ? count($_SESSION["session_history"]) : 0);
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["URI"]="./catalog.php?categ=search&mode=8";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["POST"]=$_POST;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["GET"]=$_GET;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["GET"]["sub"]="";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["POST"]["sub"]="";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_QUERY"]=$sc->make_human_query();
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_TITLE"]=$msg["search_exemplaire"];
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["SEARCH_TYPE"]="EXPL";
			$_POST["page"]=1;
			$page=1;
		}
		
		$table=$sc->get_results("./catalog.php?categ=search&mode=8&sub=launch","./catalog.php?categ=search&mode=8&option_show_notice_fille=$option_show_notice_fille&option_show_expl=$option_show_expl",true);
		print_results($sc,$table,"./catalog.php?categ=search&mode=8&sub=launch&option_show_notice_fille=$option_show_notice_fille&option_show_expl=$option_show_expl","./catalog.php?categ=search&mode=8&option_show_notice_fille=$option_show_notice_fille&option_show_expl=$option_show_expl",true);
		if ($_SESSION["CURRENT"]!==false) {
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EXPL"]["URI"]="./catalog.php?categ=search&mode=8";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EXPL"]["POST"]=$_POST;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EXPL"]["GET"]=$_GET;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EXPL"]["PAGE"]=$page;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EXPL"]["HUMAN_QUERY"]=$sc->make_human_query();
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EXPL"]["SEARCH_TYPE"]="expl";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EXPL"]['TEXT_LIST_QUERY']='';
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EXPL"]["TEXT_QUERY"]="";
		}
	break;
	default:
		print $sc->show_form("./catalog.php?categ=search&mode=8&option_show_notice_fille=$option_show_notice_fille&option_show_expl=$option_show_expl","./catalog.php?categ=search&mode=8&sub=launch");
	break;
}

function print_results($sc,$table,$url,$url_to_search_form,$hidden_form=true,$search_target="") {
    global $begin_result_liste;
    global $nb_per_page_search;
    global $page;
    global $charset;
    global $msg;
//     global $pmb_nb_max_tri;
//     global $affich_tris_result_liste;
	global $option_show_expl,$option_show_notice_fille;
	global $gestion_acces_active, $gestion_acces_user_notice;
	global $PMBuserid;
	global $explr_invisible, $pmb_droits_explr_localises;
	
	//droits d'acces lecture notice
	if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
		$ac= new acces();
		$dom_1= $ac->setDomain(1);
		$usr_prf = $dom_1->getUserProfile($PMBuserid);
		if (!is_array($usr_prf)) {
		    $usr_prf = [$usr_prf];
		}
		
		$requete = "delete from $table using $table, exemplaires, acces_res_1 ";
		$requete.= "where ";
		$requete.= "$table.expl_id=exemplaires.expl_id ";
		$requete.= "and expl_bulletin=0 ";
		$requete.= "and expl_notice = res_num ";
		$requete.= "and usr_prf_num IN (".implode(",", $usr_prf).") and (((res_rights ^ res_mask) & 4)=0) ";
		pmb_mysql_query($requete);

		$requete = "delete from $table using $table, exemplaires, bulletins, acces_res_1 ";
		$requete.= "where ";
		$requete.= "$table.expl_id=exemplaires.expl_id ";
		$requete.= "and expl_notice=0 ";
		$requete.= "and expl_bulletin=bulletin_id ";
		$requete.= "and bulletin_notice=res_num ";
		$requete.= "and usr_prf_num IN (".implode(",", $usr_prf).") and (((res_rights ^ res_mask) & 4)=0) ";
		pmb_mysql_query($requete);
		
	}
	
	//visibilité des exemplaires
	if ($pmb_droits_explr_localises && $explr_invisible) {
		$requete = "delete from $table using $table, exemplaires ";
		$requete.= "where ";
		$requete.= "$table.expl_id=exemplaires.expl_id ";
		$requete.= "and expl_location in ($explr_invisible)";
		pmb_mysql_query($requete);
	}
			
	$page = intval($page);
	if($page) {
	    $start_page = $nb_per_page_search * ($page-1);
	} else {
	    $start_page = 0;
	}
    $requete="select count(1) from $table"; 
    $res = 	pmb_mysql_query($requete);
    if($res)
    	$nb_results=pmb_mysql_result(pmb_mysql_query($requete),0,0);
    else $nb_results=0;

    $requete="select $table.* from ".$table.", exemplaires where exemplaires.expl_id=$table.expl_id";     
	if ( $nb_results > $nb_per_page_search ) {
		$requete .= " limit ".$start_page.", ".$nb_per_page_search;
	}

    //Y-a-t-il une erreur lors de la recherche ?
    if ($sc->error_message) {
    	error_message_history("", $sc->error_message, 1);
    	exit();
    }
    
    if ($hidden_form) print $sc->make_hidden_search_form($url);

    $resultat=pmb_mysql_query($requete);

    $human_requete = $sc->make_human_query();
    
    print "<strong>".$msg["search_search_exemplaire"]."</strong> : ".$human_requete ;

	if ($nb_results) {
		print " => ".$nb_results." ".$msg["search_expl_nb_result"]."<br />\n";
		print $begin_result_liste;
		if ($sc->rec_history) {
			//Affichage des liens paniers et impression
			$current=$_SESSION["CURRENT"];
			if ($current!==false) {
				print "&nbsp;<a href='#' onClick=\"openPopUp('./print_cart.php?current_print=$current&action=print_prepare&object_type=EXPL','print'); return false;\"><img src='".get_url_icon('basket_small_20x20.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_add_to_cart"]."\" title=\"".$msg["histo_add_to_cart"]."\"></a>&nbsp;";
//				if ($nb_results<=$pmb_nb_max_tri) print $affich_tris_result_liste;
			}
		}
	} else print "<br />".$msg["1915"]." ";

	print searcher::get_quick_actions("EXPL");
	print "<br/><input type='button' class='bouton' onClick=\"document.search_form.action='$url_to_search_form'; document.search_form.target='$search_target'; document.search_form.submit(); return false;\" value=\"".$msg["search_back"]."\"/>";
	
	// transformation de la recherche en multicritères: on reposte tout avec mode=6
	print "&nbsp;<input  type='button' class='bouton' onClick='document.search_transform.submit(); return false;' value=\"".$msg["search_expl_to_notice_transformation"]."\"/>";
	print searcher::get_check_uncheck_all_buttons();
	print "<form name='search_transform' action='./catalog.php?categ=search&mode=6&sub=launch'  method='post' style='display:none;'>";	
	foreach($_POST as $key =>$val) {
		if($val) {
			if(is_array($val)) {
				foreach($val as $cle=>$val_array) {
					if(is_array($val_array)){
						foreach($val_array as $valeur){
							print "<input type='hidden' name=\"".$key."[".$cle."][]\" value='".htmlentities($valeur,ENT_QUOTES,$charset)."'/>";
						}
					} else print "<input type='hidden' name='".$key."[]' value='".htmlentities($val_array,ENT_QUOTES,$charset)."'/>";
				}
			}
			else print "<input type='hidden' name='$key' value='$val'/>";
		}		
	}	
	print "</form>"; 
	
	if($resultat){			
	    while ($r=pmb_mysql_fetch_object($resultat)) {
	    	$requete2="SELECT expl_bulletin FROM exemplaires WHERE expl_id='".$r->expl_id."'";
	    	$res=pmb_mysql_query($requete2);
	    	if($res && pmb_mysql_num_rows($res) && pmb_mysql_result($res,0,0)){
	    		$nt = new mono_display_expl('',$r->expl_id, 6, $sc->link_bulletin, $option_show_expl, $sc->link_expl_bull, '', $sc->link_explnum,1, 0, 1, !$option_show_notice_fille, "", 1);
	    	}else{
	    		$nt = new mono_display_expl('',$r->expl_id, 6, $sc->link, $option_show_expl, $sc->link_expl, '', $sc->link_explnum,1, 0, 1, !$option_show_notice_fille, "", 1);
	    	}	
	    	echo "<div class='row'>".$nt->result."</div>";
	    }
	}
    
    //Gestion de la pagination
    if ($nb_results) {
        if (!$page) {
            $current_page = 1 ;
        } else {
            $current_page = intval($page);
        }
        $navbar = new Navbar($current_page, $nb_results, $nb_per_page_search);
        if (!$hidden_form) {
            $navbar->setHiddenFormName('search_form', true);
        } else {
            $navbar->setHiddenFormName('search_form');
        }
        print "<div id='results_pager' class='center'>".$navbar->render()."</div>";
    }  	
}