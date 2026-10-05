<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestions_display.inc.php,v 1.65.8.1 2025/01/30 09:08:07 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path;

require_once($class_path.'/suggestions/suggestions_controller.class.php');
require_once($class_path.'/suggestions.class.php');
require_once($class_path.'/suggestions_origine.class.php');
require_once($class_path.'/suggestions_categ.class.php');
require_once($class_path.'/suggestions_map.class.php');
require_once($class_path.'/suggestion_source.class.php');
require_once($include_path.'/templates/suggestions.tpl.php');
require_once($class_path.'/serials.class.php');
require_once($class_path.'/notice.class.php');
require_once($class_path.'/author.class.php');
require_once($class_path.'/docs_location.class.php');
require_once($include_path.'/misc.inc.php');
require_once($include_path.'/parser.inc.php');

//Affiche la liste des suggestions
function show_list_sug($id_bibli=0) {
    suggestions_controller::set_id_bibli($id_bibli);
    suggestions_controller::proceed();
}


//Affiche le formulaire de modification de suggestion
function show_form_sug($update_action) {
	global $msg, $charset;
	global $id_bibli, $id_sug;
	global $sug_map;
	global $sug_modif_form;
	global $acquisition_poids_sugg, $lk_url_sug;
	global $acquisition_sugg_categ, $acquisition_sugg_categ_default;
	global $orig_form_mod;
	global $orig_champ_modif;
	global $id_notice;
	global $acquisition_sugg_localises;
	global $deflt_docs_location;
	global $sugg_location_id;
	global $javascript_path;
	global $base_path;
	
	$form = $sug_modif_form;

	//Récupération des pondérations de suggestions
	$tab_poids = explode(",", $acquisition_poids_sugg);
	$tab_poids[0] = substr($tab_poids[0], 2); //utilisateur
	$tab_poids[1] = substr($tab_poids[1], 2); //abonné
	$tab_poids[2] = substr($tab_poids[2], 2); //visiteur

	if(!$id_sug) {	//Création de suggestion
	
		$titre = htmlentities($msg['acquisition_sug_cre'], ENT_QUOTES, $charset);
		
		//Récupération de l'utilisateur
	 	$requete_user = "SELECT userid, nom, prenom FROM users where username='".SESSlogin."' limit 1 ";
		$res_user = pmb_mysql_query($requete_user);
		$row_user=pmb_mysql_fetch_row($res_user);
		$orig = $row_user[0];
		$lib_orig = $row_user[1];
		if ($row_user[2]) $lib_orig.= ", ".$row_user[2];
				
		$form = str_replace('!!lib_orig!!', $orig_form_mod, $form);
						
		$form = str_replace('!!dat_cre!!', formatdate(today()), $form);
		$form = str_replace('!!orig!!', $orig, $form);
		$form = str_replace('!!lib_orig!!', htmlentities($lib_orig, ENT_QUOTES, $charset), $form);		
		$form = str_replace('!!typ!!', '0', $form);
		$form = str_replace('!!poi!!', $tab_poids[0], $form);
		$form = str_replace('!!poi_tot!!', $tab_poids[0], $form);
		$statut = $sug_map->getFirstStateId();
		$form = str_replace('!!statut!!', $statut, $form);
		$form = str_replace('!!lib_statut!!', $sug_map->getHtmlComment($statut), $form);
		$form = str_replace('!!list_user!!', '', $form);
		$form = str_replace('!!creator_ajout!!', '', $form);
		$form = str_replace('!!lien!!', '', $form);
		
				
		if ($acquisition_sugg_categ != '1') {
			$sel_categ="";
		} else {
			
			if (suggestions_categ::exists($acquisition_sugg_categ_default)) {
				$sugg_categ = new suggestions_categ($acquisition_sugg_categ_default);
			} else {
				$sugg_categ = new suggestions_categ('1');
			}
			$tab_categ = suggestions_categ::getCategList();
			$sel_categ = "<select class='saisie-25em' id='num_categ' name='num_categ'>";
			foreach($tab_categ as $id_categ=>$lib_categ){
				$sel_categ.= "<option value='".$id_categ."' ";
				if ($id_categ==$sugg_categ->id_categ) $sel_categ.= "selected='selected' ";
				$sel_categ.= ">";
				$sel_categ.= htmlentities($lib_categ,ENT_QUOTES, $charset)."</option>";
			}
			$sel_categ.= "</select>"; 
		}
		
		$form = str_replace('!!nombre_expl!!', '1', $form);
		
		$list_locs='';
		if ($acquisition_sugg_localises) {		
		 	$sugg_location_id=((string)$sugg_location_id==""?$deflt_docs_location:$sugg_location_id);
			if ($sugg_location_id) $temp_location=$sugg_location_id;
			else $temp_location=0;
			$locs=new docs_location();
			$list_locs=$locs->gen_combo_box_sugg($temp_location,1,"");
		}
		$form = str_replace('<!-- sel_location -->', $list_locs, $form);
		
		// si suggestion concernant une notice avec 	$id_notice en parametre, on pre-rempli les champs
		if($id_notice) {
			$notice=new notice($id_notice);
			$tit=htmlentities($notice->tit1,ENT_QUOTES, $charset);
			$edi=htmlentities($notice->ed1,ENT_QUOTES, $charset);
			$prix=$notice->prix;
			$cod=$notice->code;
			$year=$notice->year;
			$url_sug=$notice->lien;
			$as = array_search ("0", $notice->responsabilites["responsabilites"]) ;
			if ($as!== FALSE && $as!== NULL) {
				$auteur_0 = $notice->responsabilites["auteurs"][$as] ;
				$auteur = new auteur($auteur_0["id"]);
				$aut=htmlentities($auteur->display,ENT_QUOTES, $charset);
			} else {
				$aut='';
			}
			$form = str_replace('!!id_notice!!', $id_notice, $form);
		} else {
			$tit='';
			$edi='';
			$prix='';
			$cod='';
			$year='';
			$url_sug='';
			$form = str_replace('!!id_notice!!', 0, $form);
			$aut='';
		}
		$form = str_replace('!!categ!!', $sel_categ, $form);
		$form = str_replace('!!tit!!', $tit, $form);
		$form = str_replace('!!edi!!', $edi, $form);
		$form = str_replace('!!aut!!', $aut, $form);
		$form = str_replace('!!cod!!', $cod, $form);
		$form = str_replace('!!pri!!', $prix, $form);
		$form = str_replace('!!com!!', '', $form);
		$form = str_replace('!!com_gestion!!', '', $form);
		$form = str_replace('!!url_sug!!', $url_sug, $form);
		
		
		//Affichage du selecteur de source
		$req = "select * from suggestions_source order by libelle_source";
		$res= pmb_mysql_query($req);
		
		$option = "<option value='0' selected>".htmlentities($msg['acquisition_sugg_no_src'],ENT_QUOTES,$charset)."</option>";
		while(($src=pmb_mysql_fetch_object($res))){
			$option .= "<option value='".$src->id_source."' >".htmlentities($src->libelle_source,ENT_QUOTES,$charset)."</option>";
		}
		$selecteur = "<select id='sug_src' name='sug_src'>".$option."</select>";
		$form = str_replace('!!liste_source!!',$selecteur, $form); 
		$form = str_replace('!!date_publi!!',$year, $form);
		
		$pj = "<div class='row'>
					<input type='file' id='piece_jointe_sug' name='piece_jointe_sug' class='saisie-80em' size='60' />
			  </div>";
		$form= str_replace('!!div_pj!!',$pj, $form);
		
	} else {	//Modification de suggestion

		$titre = htmlentities($msg['acquisition_sug_mod'], ENT_QUOTES, $charset);

		$sug = new suggestions($id_sug);
		$q = suggestions_origine::listOccurences($id_sug);
		$list_orig = pmb_mysql_query($q);
		
		$orig = 0;
		$poids_tot = 0;
		$users = array();
		while(($row_orig = pmb_mysql_fetch_object($list_orig))) {
			if (!$orig) {
				$orig = $row_orig->origine;
				$typ = $row_orig->type_origine;
				$poids = $tab_poids[$row_orig->type_origine]; 
			}
			$users[] = $row_orig;
			$poids_tot = $poids_tot + $tab_poids[$row_orig->type_origine];
		}
		$list_user = '';
		//On parcourt tous les créateurs de suggestions
		for($i=0;$i<sizeof($users);$i++){
			$orig = $users[$i]->origine;
			$typ = $users[$i]->type_origine;
			$suppr_click = "onClick=\"if(confirm('".$msg['confirm_suppr_origine']."')){ ajax_suppr_origine('".$orig."','".$typ."');}\"";

			//Récupération du nom du créateur de la suggestion
			switch($typ){
				default:
				case '0' :
				 	$requete_user = "SELECT userid, nom, prenom FROM users where userid = '".$orig."'";
					$res_user = pmb_mysql_query($requete_user);
					$row_user=pmb_mysql_fetch_row($res_user);
					$lib_orig = $row_user[1];
					if ($row_user[2]) $lib_orig.= ", ".$row_user[2];					
					if(empty($premier_user) || !isset($premier_user)) $premier_user = $lib_orig;
					else $list_user .= $lib_orig."<img src='".get_url_icon('trash.png')."' class='align_middle' alt='basket' title=\"".$msg["origine_suppr"]."\" alt=\"".$msg["origine_suppr"]."\" $suppr_click /><br />";
					break;
				case '1' :
				 	$requete_empr = "SELECT id_empr, empr_nom, empr_prenom FROM empr where id_empr = '".$orig."'";
					$res_empr = pmb_mysql_query($requete_empr);
					$row_empr=pmb_mysql_fetch_row($res_empr);
					$lib_orig = $row_empr[1];
					if ($row_empr[2]) $lib_orig.= ", ".$row_empr[2];
					if(empty($premier_user) || !isset($premier_user)) $premier_user = $lib_orig;
					else $list_user .= $lib_orig."<img src='".get_url_icon('trash.png')."' class='align_middle' alt='basket' title=\"".$msg["origine_suppr"]."\" alt=\"".$msg["origine_suppr"]."\" $suppr_click /><br />";
					break;
				case '2' :
					if($orig) $lib_orig = $orig;
					else $lib_orig = $msg['suggest_anonyme'];
					if(empty($premier_user) || !isset($premier_user)) $premier_user = $lib_orig;
					else $list_user .= $lib_orig."<img src='".get_url_icon('trash.png')."' class='align_middle' alt='basket' title=\"".$msg["origine_suppr"]."\" alt=\"".$msg["origine_suppr"]."\" $suppr_click /><br />";
					break;
			}	
		}
		
		//Récupération du statut de la suggestion
		$lib_statut=$sug_map->getHtmlComment($sug->statut);
	
		$form = str_replace('!!dat_cre!!', formatdate($sug->date_creation), $form);
		$form = str_replace('!!orig!!', $orig, $form);
		
		//Ajout du champ de saisie du nouveau créateur
		$ajout_create = "
		<input type='text' id='creator_lib_orig' name='creator_lib_orig' class='saisie-10emr'/>
		<input type='button' id='creator_btn_orig' class='bouton_small' value='...' onclick=\"openPopUp('./select.php?what=origine&caller=sug_modif_form&param1=orig&param2=creator_lib_orig&param3=typ&param4=&param5=&param6=&callback=ajax_origine&deb_rech='+".pmb_escape()."(document.getElementById('creator_lib_orig').value), 'selector')\" />";

		$form = str_replace('!!id_sug!!', $id_sug, $form);
		if(sizeof($users)>1) {
			//on ajoute le champ à la liste
			$list_user.=$ajout_create;
			$form = str_replace('!!creator_ajout!!', '', $form);
		} else $form = str_replace('!!creator_ajout!!', "<br />".$ajout_create, $form);
		
		//Menu dépliant
		$deroul_user=gen_plus('ori',$msg['suggest_creator']. " (".(sizeof($users)-1).")",$list_user,0);
		
		if ($lib_orig) {
			$form = str_replace('!!lib_orig!!', htmlentities($premier_user, ENT_QUOTES, $charset), $form);
			if(sizeof($users)>1) $form = str_replace('!!list_user!!', $deroul_user, $form);
			else $form = str_replace('!!list_user!!', '', $form);
		} else {
			$form = str_replace('!!lib_orig!!', '&nbsp;', $form);
			$form = str_replace('!!list_user!!', '', $form);
		}
		$form = str_replace('!!typ!!', $typ, $form);
		$form = str_replace('!!poi!!', $poids, $form);
		$form = str_replace('!!poi_tot!!', $poids_tot, $form);
		$form = str_replace('!!statut!!', $sug->statut, $form);
		$form = str_replace('!!lib_statut!!', $lib_statut, $form);
		
		if ($acquisition_sugg_categ != '1') {
			$sel_categ="";
		} else {
			
			$state_name = $sug_map->getStateNameFromId($sug->statut);
			$categ = $sug_map->getState_CATEG($state_name);
			$sugg_categ = new suggestions_categ($sug->num_categ);

			if ($categ == 'YES') {
				$tab_categ = suggestions_categ::getCategList();
				$sel_categ = "<select class='saisie-25em' id='num_categ' name='num_categ'>";
				foreach($tab_categ as $id_categ=>$lib_categ){
					$sel_categ.= "<option value='".$id_categ."' ";
					if ($id_categ==$sug->num_categ) $sel_categ.= "selected='selected' ";
					$sel_categ.= ">";
					$sel_categ.= htmlentities($lib_categ,ENT_QUOTES, $charset)."</option>";
				}
				$sel_categ.= "</select>"; 
			} else {
				$sel_categ = htmlentities($sugg_categ->libelle_categ, ENT_QUOTES,$charset);
			}			
		}
		//Nombre d'exemplaire
		$form = str_replace('!!nombre_expl!!', $sug->nb, $form);
		
		//Selecteur de localisation
		$list_locs='';
		if ($acquisition_sugg_localises) {
			$sugg_location_id=$sug->sugg_location;
			if ($sugg_location_id) $temp_location=$sugg_location_id;
			else $temp_location=0;
			$locs=new docs_location();
			$list_locs=$locs->gen_combo_box_sugg($temp_location,1,"");
		}
		$form = str_replace('<!-- sel_location -->', $list_locs, $form);
		
		if($sug->num_notice && $sug->num_notice !=0){
			$req_ana = "select analysis_bulletin as bull , analysis_notice as noti from analysis where analysis_notice ='".$sug->num_notice."'";	
			$res_ana = pmb_mysql_query($req_ana);
			$num_rows_ana = pmb_mysql_num_rows($res_ana);			
			if($num_rows_ana){
				$ana = pmb_mysql_fetch_object($res_ana);
				$url_view = analysis::get_permalink($ana->noti, $ana->bull);
			} else $url_view = notice::get_permalink($sug->num_notice);
			$lien = "<a href='$url_view'> ".$msg['acquisition_sug_view_not']."</a>";
			$form = str_replace('!!lien!!',$lien, $form);
		} else $form = str_replace('!!lien!!','', $form);
		
		$form = str_replace('!!categ!!', $sel_categ, $form);
		$form = str_replace('!!tit!!', htmlentities($sug->titre, ENT_QUOTES, $charset), $form);
		$form = str_replace('!!edi!!', htmlentities($sug->editeur, ENT_QUOTES, $charset), $form);
		$form = str_replace('!!aut!!', htmlentities($sug->auteur, ENT_QUOTES, $charset), $form);
		$form = str_replace('!!cod!!', htmlentities($sug->code, ENT_QUOTES, $charset), $form);
		$form = str_replace('!!pri!!', round($sug->prix, 2), $form);
		$form = str_replace('!!com!!', htmlentities($sug->commentaires, ENT_QUOTES, $charset), $form);
		$form = str_replace('!!com_gestion!!', htmlentities($sug->commentaires_gestion, ENT_QUOTES, $charset), $form);
		
		$req = "select * from suggestions_source order by libelle_source";
		$res= pmb_mysql_query($req);
		$selected = "";
		$option = "<option value='0' selected>".htmlentities($msg['acquisition_sugg_no_src'],ENT_QUOTES,$charset)."</option>";
		while(($src=pmb_mysql_fetch_object($res))){
			 ($src->id_source == $sug->sugg_src ? $selected = " selected ": $selected ="");
			$option .= "<option value='".$src->id_source."' $selected>".htmlentities($src->libelle_source,ENT_QUOTES,$charset)."</option>";
		}
		$selecteur = "<select id='sug_src' name='sug_src'>".$option."</select>";
		$form = str_replace('!!liste_source!!',$selecteur, $form); 
		$form=str_replace("!!date_publi!!",htmlentities($sug->date_publi, ENT_QUOTES, $charset),$form);		
		
		if(!$sug->get_explnum('id')){
			$pj = "<div class='row'>
					<input type='file' id='piece_jointe_sug' name='piece_jointe_sug' class='saisie-80em' size='60' />
			  </div>";
		} else {
			$pj = "
			<input type='hidden' name='id_pj' id='id_pj' value='".$sug->get_explnum('id')."' />
			<div class='row'>".
				$sug->get_explnum('nom')."&nbsp; 
				<a href=\"$base_path/explnum_doc.php?explnumdoc_id=".$sug->get_explnum('id')."\" target=\"_blank\" title='".$msg['download'] . "'><input type='button' class='bouton' value='".$msg['fichier_menu_consulter']."' /></a>
				<a href=\"$base_path/acquisition.php?categ=sug&sub=import&explnumdoc_id=".$sug->get_explnum('id')."\" title='".$msg['acquisition_menu_sug_import']."'><input type='button' class='bouton' value='".$msg['acquisition_sugg_btn_import']."' /></a>
				<input type='submit' class='bouton' name='del_pj' id='del_pj' value='X' title='".$msg['supprimer']."' onclick='this.form.action=\"./acquisition.php?categ=sug&action=del_pj&id_bibli=".$id_bibli."&id_sug=".$id_sug."\"' />			
			</div>";
		}
		$form= str_replace('!!div_pj!!',$pj, $form);
		
		if ($sug->url_suggestion ) {
			$form = str_replace('<!-- url_sug -->', $lk_url_sug, $form);
		}
		$form = str_replace('!!url_sug!!', htmlentities($sug->url_suggestion, ENT_QUOTES, $charset), $form);	
		$form = str_replace('!!id_notice!!', $sug->num_notice, $form);
		

		// Affichage du bouton supprimer
		$bt_sup = $sug_map->getButton_DELETED($sug->statut, $id_bibli, $id_sug);
		$form = str_replace('<!-- bouton_sup -->', $bt_sup, $form);
		
		if ($sug->num_notice) {
			//Eventuellement, lien vers la notice	

		} else {
			
			// Affichage du bouton cataloguer
			$bt_cat = $sug_map->getButton_CATALOG($sug->statut, $id_bibli, $id_sug);
			$button = "<input type='radio' name='catal_type' id='not_type' value='0' checked /><label class='etiquette' for='not_type'>".htmlentities($msg['acquisition_type_mono'],ENT_QUOTES,$charset)."</label>
			<input type='radio' name='catal_type' value='1' id='art_type'/><label for='art_type' class='etiquette'>".htmlentities($msg['acquisition_type_art'],ENT_QUOTES,$charset)."</label>";
			if($sug->sugg_noti_unimarc){
				$bt_cat = str_replace('!!type_catal!!',"&nbsp;<label style='color:red'>Notice externe existante</label>",$bt_cat);
			} else $bt_cat = str_replace('!!type_catal!!',$button,$bt_cat);
			
			$form = str_replace('<!-- bouton_cat -->', $bt_cat, $form);	
		}
	}
	
	//$action ="./acquisition.php?categ=sug&action=update&id_bibli=".$id_bibli."&id_sug=".$id_sug;
	$form = str_replace('!!action!!', $update_action, $form);
	$form = str_replace('!!form_title!!', $titre, $form);
	
	print "<script type=\"text/javascript\" src=\"".$javascript_path."/tablist.js\"></script>";
	print $form;
}
?>
