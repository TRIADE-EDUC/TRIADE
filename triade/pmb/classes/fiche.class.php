<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fiche.class.php,v 1.20 2019-06-05 06:41:21 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/fiche.tpl.php");
require_once($class_path."/parametres_perso.class.php");
require_once ($class_path."/spreadsheetPMB.class.php");

class fiche{

	public $id_fiche = 0;
	public $p_perso = "";
	public $liste_ids =array();

	public function __construct($id=0){
		global $prefix;
		$this->id_fiche = $id+0;

		$this->p_perso = new parametres_perso($prefix);
	}

	/*
	 * Formulaire d'édition d'une fiche
	 */
	public function show_edit_form(){

		global $form_edit_fiche,$msg, $charset, $act,$base_path;
		global $perso_word,$page,$nb_per_page,$i_search;

		if($act == 'save_and_new') {
			$perso_ = $this->p_perso->show_editable_fields(0);
		} else {
			$perso_ = $this->p_perso->show_editable_fields($this->id_fiche);	
		}
		
		if (!$this->p_perso->no_special_fields) {
			$perso="";
			$perso.=$perso_['CHECK_SCRIPTS']; 
			for ($i=0; $i<count($perso_['FIELDS']); $i++) {
				$p=$perso_['FIELDS'][$i];
				$perso.="<div id='pperso_".$p['NAME']."'  title=\"".htmlentities($p['TITRE'],ENT_QUOTES, $charset)."\">
							<div class='row'><label for='".$p['NAME']."' class='etiquette'>".htmlentities($p['TITRE'],ENT_QUOTES, $charset)." </label>".$p["COMMENT_DISPLAY"]."</div>
							<div class='row'>".$p['AFF']."</div>
						 </div>";
			}
		}
		if($act != 'save_and_new')$form_edit_fiche=str_replace('!!hidden_id!!',$this->id_fiche,$form_edit_fiche);
		else $form_edit_fiche=str_replace('!!hidden_id!!','',$form_edit_fiche);
		if(!$this->id_fiche  || $act=='save_and_new'){
			$btn = "<input type='submit' class='bouton' value='".htmlentities($msg['fiche_save_and_new'],ENT_QUOTES,$charset)."' onclick='this.form.act.value=\"save_and_new\"; return(check_form());' />";
			$form_edit_fiche=str_replace('!!btn!!',$btn, $form_edit_fiche);
			$form_edit_fiche=str_replace('!!form_titre!!', $msg['fichier_form_saisie'], $form_edit_fiche);
			$form_edit_fiche=str_replace('!!btn_cancel!!','',$form_edit_fiche);
			$form_edit_fiche=str_replace('!!btn_del!!','',$form_edit_fiche);
			$form_edit_fiche=str_replace('!!form_action!!',"$base_path/fichier.php?categ=saisie", $form_edit_fiche);
		} else {
			$form_edit_fiche=str_replace('!!form_action!!',"$base_path/fichier.php?categ=consult&mode=search&sub=update&perso_word=$perso_word&i_search=$i_search&page=$page&nb_per_page=$nb_per_page&idfiche=".$this->id_fiche, $form_edit_fiche);
			$btn = "<input type='submit' class='bouton' value='".htmlentities($msg['77'],ENT_QUOTES,$charset)."' onclick='this.form.act.value=\"update\";return(check_form());' />";
			$form_edit_fiche=str_replace('!!btn!!',$btn, $form_edit_fiche);
			$form_edit_fiche=str_replace('!!form_titre!!', $msg['fichier_form_modify'], $form_edit_fiche);
			$form_edit_fiche=str_replace('!!btn_cancel!!',"<input type='button' class='bouton' value='".htmlentities($msg[76],ENT_QUOTES,$charset)."'
			onclick=\"document.location='./fichier.php?categ=consult&mode=search&sub=view&perso_word=$perso_word&idfiche=".$this->id_fiche."&i_search=$i_search&page=$page&nb_per_page=$nb_per_page';\" />",$form_edit_fiche);
			$form_edit_fiche=str_replace('!!btn_del!!',"<input type='button' class='bouton' value='".htmlentities($msg[63],ENT_QUOTES,$charset)."'
			onclick=\"document.location='./fichier.php?categ=consult&mode=search&sub=del&perso_word=$perso_word&idfiche=".$this->id_fiche."&i_search=$i_search&page=$page&nb_per_page=$nb_per_page';\" />",$form_edit_fiche);
		}
		$form_edit_fiche=str_replace('!!perso_fields!!', $perso, $form_edit_fiche);
		$form_edit_fiche=str_replace('!!visibility_prec!!',"style='display:none'",$form_edit_fiche);
		$form_edit_fiche=str_replace('!!visibility_suiv!!',"style='display:none'",$form_edit_fiche);
		$form_edit_fiche=str_replace('!!action_prec!!','',$form_edit_fiche);
		$form_edit_fiche=str_replace('!!action_suiv!!','',$form_edit_fiche);
 		$form_edit_fiche=str_replace('<!-- focus -->',"<script type='text/javascript'>ajax_parse_dom();document.getElementById('".$perso_['FIELDS'][0]['NAME']."').focus();</script>",$form_edit_fiche);
		return $form_edit_fiche;

	}

	/*
	 * Affiche le formulaire de consultation d'une fiche
	 */
	public function show_fiche_form(){

		global $form_edit_fiche,$msg,$charset;
		global $perso_word,$page;
		global $nb_per_page,$i_search;
		
		if(!$this->id_fiche) return;

		$values = $this->get_values($this->id_fiche);
		$this->get_info_navigation();

		foreach($values as $key=>$val){
			$display .= "<div class='row'>
				<div class='colonne3'>
					<label class='etiquette'>".htmlentities($this->p_perso->t_fields[$key]['TITRE']." : ",ENT_QUOTES,$charset)."</label>
				</div>
				<div class='colonne_suite'>";
			for($i=0;$i<count($val);$i++){
				$display.= "<span>".$val[$i]."</span>";
			}
			$display .= "</div></div>";
		}

		$btn = "<input type='button' class='bouton' value='".htmlentities($msg[62],ENT_QUOTES,$charset)."' onclick=\"
			document.location='./fichier.php?categ=consult&mode=search&sub=edit&perso_word=$perso_word&page=".$this->page."&nb_per_page=$nb_per_page&i_search=".$i_search."&idfiche=".$this->id_fiche."';\" />";
		$form_edit_fiche=str_replace('!!perso_fields!!', $display, $form_edit_fiche);
		$form_edit_fiche=str_replace('!!btn!!',$btn, $form_edit_fiche);
		$form_edit_fiche=str_replace('!!btn_cancel!!',"<input type='button' class='bouton' value='".htmlentities($msg[76],ENT_QUOTES,$charset)."'
			onclick=\"document.location='./fichier.php?categ=consult&mode=search&perso_word=$perso_word&page=".$this->page."&nb_per_page=$nb_per_page';\" />",$form_edit_fiche);
		$form_edit_fiche=str_replace('!!form_titre!!', $msg['fichier_form_consult'], $form_edit_fiche);
		$form_edit_fiche=str_replace('!!hidden_id!!',$this->id_fiche,$form_edit_fiche);


		if($this->fiche_prec){
			$form_edit_fiche=str_replace('!!visibility_prec!!',"",$form_edit_fiche);
			$form_edit_fiche=str_replace('!!action_prec!!',
			"onclick=\"document.location='./fichier.php?categ=consult&mode=search&sub=view&perso_word=$perso_word&idfiche=".$this->fiche_prec."&i_search=".$this->i_fiche_prec."&nb_per_page=$nb_per_page';\"",$form_edit_fiche);
		} else {
			$form_edit_fiche=str_replace('!!visibility_prec!!',"style='display:none';",$form_edit_fiche);
		}
		if($this->fiche_suiv){
			$form_edit_fiche=str_replace('!!visibility_suiv!!',"",$form_edit_fiche);
			$form_edit_fiche=str_replace('!!action_suiv!!',
			"onclick=\"document.location='./fichier.php?categ=consult&mode=search&sub=view&perso_word=$perso_word&idfiche=".$this->fiche_suiv."&i_search=".$this->i_fiche_suiv."&nb_per_page=$nb_per_page';\"",$form_edit_fiche);
		} else {
			$form_edit_fiche=str_replace('!!visibility_suiv!!',"style='display:none';",$form_edit_fiche);
		}

		$form_edit_fiche=str_replace('!!visibility_prec!!',"style='display:none';",$form_edit_fiche);
		$form_edit_fiche=str_replace('!!visibility_suiv!!',"style='display:none';",$form_edit_fiche);
		$form_edit_fiche=str_replace('!!action_prec!!',"",$form_edit_fiche);
		$form_edit_fiche=str_replace('!!action_suiv!!',"",$form_edit_fiche);
		$form_edit_fiche=str_replace('!!btn_del!!',"",$form_edit_fiche);

		return $form_edit_fiche;
	}


	/*
	 * Permmet lors de l'affichage d'une fiche de trouvé les infos de navigation en tenant compte de la recherche:
	 * id fiche précédante,
	 * id fiche suivante
	 * page du retour à la liste, et du retour sur effacement
	 */
	public function get_info_navigation(){
		global $dbh,$perso_word;
		global $i_search;
		global $nb_per_page_search,$nb_per_page;
		
		if(!($nb_per_page*1)){
			$nb_per_page=$nb_per_page_search;
		}
		$search_word = str_replace('*','%',$perso_word);

		$requete = "SELECT count(1) FROM fiche where infos_global like '%".$search_word."%' or index_infos_global like '%".$perso_word."%'";
		$res = pmb_mysql_query($requete, $dbh);
		$nbr_lignes = pmb_mysql_result($res, 0, 0);

		if(!$i_search) $limit="limit 0, 2";
		else $limit="limit ".($i_search-1).", 3";

		$req = "select id_fiche from fiche where infos_global like '%".$search_word."%' or index_infos_global like '%".$perso_word."%' $limit ";
		$res = pmb_mysql_query($req,$dbh);
		$this->fiche_prec=0;
		$this->fiche_suiv=0;
		if ($nb=pmb_mysql_num_rows($res)) {
			while($fic = pmb_mysql_fetch_object($res)){
				$result[] = $fic->id_fiche;
			}
			if($i_search<1 && $nb>1){
				$this->fiche_suiv=$result[1];
				$this->i_fiche_suiv=$i_search+1;
			}
			if($i_search && $nb>1){
				$this->fiche_prec=$result[0];
				$this->i_fiche_prec=$i_search-1;
				if($nb>2){
					$this->fiche_suiv=$result[2];
					$this->i_fiche_suiv=$i_search+1;
				}
			}
		}
		$this->page=(int)(($i_search)/$nb_per_page)+1;
	}

	/*
	 * Enregistrement d'une fiche
	 */
	public function save(){
		global $prefix, $dbh,$msg,$charset;

		if(!$this->id_fiche){
			$req = "insert into fiche set infos_global='', index_infos_global=''";
			pmb_mysql_query($req,$dbh);
			$this->id_fiche = pmb_mysql_insert_id();
			print "<div class='row'><b>".htmlentities($msg['fiche_saved'],ENT_QUOTES,$charset)."</b></div>";
		} else {
			$req = "update fiche set infos_global='', index_infos_global='' where id_fiche='".$this->id_fiche."'";
			pmb_mysql_query($req,$dbh);
		}
		//On met à jour les champs persos
		$this->p_perso->check_submited_fields();
		$this->p_perso->rec_fields_perso($this->id_fiche);

		//On met à jour l'index de la fiche
		$this->update_global_index($this->id_fiche);
	}

	/*
	 * suppression d'une fiche
	 */
	public function delete(){
		global $dbh;
		$req = "delete from fiche where id_fiche = ".$this->id_fiche;
		pmb_mysql_query($req,$dbh);
		$req = "delete from ".$this->p_perso->prefix."_custom_values where  ".$this->p_perso->prefix."_custom_origine = ".$this->id_fiche;
		pmb_mysql_query($req,$dbh);
	}

	/*
	 * Mis à jour de l'index d'une fiche
	 */
	public function update_global_index($id){
		global $dbh, $prefix;

		$infos_global = '';
		$infos_global_index = '';
		$mots_perso=$this->p_perso->get_fields_recherche($id);
		if($mots_perso) {
			$infos_global.= $mots_perso.' ';
			$infos_global_index.= strip_empty_words($mots_perso).' ';
		}
		$req = "update fiche set infos_global='".addslashes($infos_global)."', index_infos_global='".addslashes($infos_global_index)."' where id_fiche=$id";
		pmb_mysql_query($req,$dbh);
	}

	/*
	 * Reindexation globale
	 */
	public function reindex_all(){
		global $dbh;

		$req = "select id_fiche from fiche";
		$res = pmb_mysql_query($req,$dbh);
		while($fiche = pmb_mysql_fetch_object($res)){
			$this->update_global_index($fiche->id_fiche);
		}
	}

	/*
	 * Affiche le formulaire de reindexation
	 */
	public function show_reindex_form(){
		global $form_reindex;

		print $form_reindex;
	}

	/*
	 * Affiche le formulaire/tableau résultat de recherche dans les champs persos
	 */
	public function show_search_list($action='',$url_base='',$page=1){
		global $form_search, $fichier_menu_display, $perso_word, $prefix;
		global $dbh, $msg,$charset;
		global $nb_per_page_search,$nb_per_page;
		global $dest;
		$search_word = str_replace('*','%',$perso_word);
		
		if(!($nb_per_page*1)){
			$nb_per_page=$nb_per_page_search;
		}
		if(!$page) $page=1;
		$debut =($page-1)*$nb_per_page;
		$requete = "SELECT count(1) FROM fiche where infos_global like '%".$search_word."%' or index_infos_global like '%".$perso_word."%'";
		$res = pmb_mysql_query($requete, $dbh);
		$nbr_lignes = pmb_mysql_result($res, 0, 0);

		$req = "select id_fiche from fiche where infos_global like '%".$search_word."%' or index_infos_global like '%".$perso_word."%' ";
		if(!isset($dest) || !$dest){
			$req .= " LIMIT ".$debut.",".$nb_per_page." ";
		}
		$res = pmb_mysql_query($req,$dbh);

		while($fic = pmb_mysql_fetch_object($res)){
			$result[$fic->id_fiche] = $this->get_values($fic->id_fiche,1);
		}
		$form_search = str_replace("!!nb_per_page!!",$nb_per_page,$form_search);
		$form_search = str_replace("!!perso_word!!",htmlentities(stripslashes($perso_word),ENT_QUOTES,$charset),$form_search);
		if(!$result){
			$form_search = str_replace("!!message_result!!",sprintf($msg['fichier_no_result_found'],$perso_word),$form_search);
			print $form_search;
		} else {
			$nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page, $page, 10, false, true);
			$form_search = str_replace("!!message_result!!","",$form_search);
			if($dest == "TABLEAUHTML"){
				print $this->display_results_tableau($result,"",$debut,true);
			}elseif($dest == "TABLEAU"){
				$this->print_results_tableau($result);
			}else{
				print $form_search;
				print $this->display_results_tableau($result,"",$debut);
				print $nav_bar;
			}
		}
	}

	/*
	 * On récupère les valeurs des champs visibles correspondant à la fiche
	 */
	public function get_values($id_fiche,$visible=0){
		global $dbh,$charset;
		$values=array();
		
		$tabl_val=$this->p_perso->show_fields($id_fiche);
		foreach ( $tabl_val["FIELDS"] as $key => $value ) {
       		if((!$visible || $value["OPAC_SHOW"]) && ($value["AFF"] !== "")){
       			$values[$value["ID"]][]=$value["AFF"];
       		}
		}
		return $values;
	}

	public function display_results_tableau($liste_result,$back_url="",$i_search_deb=0,$export = false){

		global $dbh, $charset, $msg;
		global $perso_word,$page;
		global $nb_per_page;
		$req = "select * from ".$this->p_perso->prefix."_custom where multiple=1 order by ordre";//where multiple=1";
		$res = pmb_mysql_query($req,$dbh);
		if($export){
			$display = "<table id='result_table' style='width:100%'><tr>";
		}else{
			$display = "<script type='text/javascript' src='./javascript/sorttable.js'></script>\n<table id='result_table' style='width:100%' class=\"sortable\"><tr>";
		}
		
		$nb_field=0;
		$field_id=array();
		while($champ = pmb_mysql_fetch_object($res)){
			$field_id[]=$champ->idchamp;
			if($champ->multiple)$display .= "<th>".htmlentities($champ->titre,ENT_QUOTES,$charset)."</th>";
			$field_visible[$nb_field++]=$champ->multiple;
		}
		$display .= "</tr>";
		$cpt_ligne=0;
		foreach($liste_result as $index=>$liste){
			if(!$cpt_ligne++%2)		$class = "class='odd'";
			else $class = "class='even'";
			$this->liste_ids[] = $index;
			if($export){
				$display .= "<tr>";
			}else{
				$display .= "<tr style='cursor: pointer' $class onclick=\"document.location='./fichier.php?categ=consult&mode=search&sub=view&perso_word=$perso_word&nb_per_page=$nb_per_page&page=$page&idfiche=$index&i_search=".$i_search_deb++."';\">";
			}
			foreach($field_id as $idchamp ){
			 	$display.= "<td>";
				if($liste[$idchamp]){
					$cpt=0;
					foreach($liste[$idchamp] as $cle=>$valeur){
						if($cpt)$display.="<br />";
						$display.= $valeur;
						$cpt++;
					}
				}
			 	$display.= "</td>";
			}
			$display .= "</tr>";
		}
		$display .= "</table>";

		if($this->liste_ids)
			$display.="<input type='hidden' id='liste_ids' name='liste_ids' value='".implode(",",$this->liste_ids)."' />";

		if($back_url){
			$display .= "
			<div class='row'>
				<input type='button' class='bouton' value='".htmlentities($msg['fichier_result_list_return'],ENT_QUOTES,$charset)."' onclick='document.location=\"$back_url\"' />
			</div>
			";
		}
		return $display;
	}

	public function print_results_tableau($liste_result){
		global $dbh, $charset;
		
		$worksheet = new spreadsheetPMB();
		$bold = array(
				'font'  => array(
						'bold'  => true
				)
		);
		
		$req = "select * from ".$this->p_perso->prefix."_custom where multiple=1 order by ordre";
		$res = pmb_mysql_query($req,$dbh);
		$num_col=0;
		$num_ligne=0;
		$field_id=array();
		while($champ = pmb_mysql_fetch_object($res)){
			$field_id[]=$champ->idchamp;
			$worksheet->write($num_ligne,$num_col,$champ->titre,$bold);
			$num_col++;
		}
		$num_ligne++;
		foreach($liste_result as $idfiche=>$liste){
			$num_col=0;
			foreach($field_id as $idchamp ){
			 	$val="";
				if($liste[$idchamp]){
					foreach($liste[$idchamp] as $cle=>$valeur){
						if($val)$val.="\n";
						$val.= $valeur;
					}
				}
				$worksheet->write($num_ligne,$num_col,html_entity_decode($val, ENT_QUOTES, $charset));
			 	$num_col++;
			}
			$num_ligne++;
		}
	
		$worksheet->download('Tableau.xls');
	}
}