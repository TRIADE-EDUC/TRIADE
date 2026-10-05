<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: stats_planning.class.php,v 1.4 2022/03/10 14:06:00 dgoron Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");
require_once ($class_path . "/consolidation.class.php");

class stats_planning extends scheduler_planning {

	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {
		global $charset, $msg;
				
		//paramètres pré-enregistré
		$liste_views = array();
		if (isset($param['list_view'])) {
			foreach ($param['list_view'] as $id_view) {
				$liste_views[$id_view] = $id_view;
			}
		}
		$conso = (isset($param["conso"]) && $param["conso"] ? $param["conso"] : "1");
		$date_deb = (isset($param["date_deb"]) ? $param["date_deb"] : '');
		$date_fin = (isset($param["date_fin"]) ? $param["date_fin"] : '');
		$date_ech = (isset($param["date_ech"]) ? $param["date_ech"] : '');
		
		$requete = "SELECT id_vue, date_consolidation, nom_vue, comment FROM statopac_vues";
		$res = pmb_mysql_query($requete);
		$nb_rows = pmb_mysql_num_rows($res);
		//taille du selecteur
		if ($nb_rows < 3) $nb=3;
		else if ($nb_rows > 10) $nb=10;
		else $nb = $nb_rows;
		
		$select_view = "<select id='list_view' class='saisie-50em' name='list_view[]' size='".$nb."' multiple>";
		while($row = pmb_mysql_fetch_object($res)) {
			$select_view .="<option id='".$row->id_vue."' value='".$row->id_vue."' ".(isset($liste_views[$row->id_vue]) && $liste_views[$row->id_vue] == $row->id_vue ? "selected" : "").">".htmlentities($row->nom_vue,ENT_QUOTES,$charset)."</option>";
		}
		$select_view .= "</select>";
		
		//liste des vues à consolider
		$form_task = "
		<div class='row'>
			<div class='colonne3'>
				<label for='stats'>".$this->msg["planificateur_stats_listView"]."</label>
			</div>
			<div class='colonne_suite'>".
				$select_view
			."</div>
		</div>
		<div class='row'>&nbsp;</div>";

		/*appui sur la fin de la méthode do_form de la classe stat_view*/
		$form_task .= "<div class='row'>
			<div class='colonne3'>
				<label for='stats'>".$this->msg["planificateur_stats_options"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='radio' class='radio' id='id_lot' name='conso' value='1' ".($conso == "1" ? "checked" : "")."/> 
					<label for='id_lot'>$msg[stat_last_consolidation]</label> <br><br>
				<input type='radio' class='radio' id='id_interval' name='conso' value='2' ".($conso == "2" ? "checked" : "")."/> 
					<label for='id_interval'>$msg[stat_interval_consolidation] </label><br><br>
				<input type='radio' class='radio' id='id_debut' name='conso' value='3' ".($conso == "3" ? "checked" : "")."/> 
					<label for='id_debut'>$msg[stat_echeance_consolidation]</label><br>
			</div>
		</div>";
		$btn_date_deb = "<input type='date' name='date_deb' value='!!date_deb!!'/>";
		$btn_date_fin = "<input type='date' name='date_fin' value='!!date_fin!!'/>";
		$btn_date_echeance = "<input type='date' name='date_ech' value='!!date_ech!!'/>";
			
		if (!$date_deb) // -- si nouvelle tâche = pas de params pré-enregistrés
			$date_deb = strftime("%Y-%m-%d", mktime(0, 0, 0, date('m'), date('d')-1, date('y'))); 
		$btn_date_deb=str_replace("!!date_deb!!",$date_deb,$btn_date_deb);
		$btn_date_deb=str_replace("!!date_deb_lib!!",formatdate($date_deb),$btn_date_deb);
		
		if(!$date_fin)
			$date_fin = today();			
		$btn_date_fin=str_replace("!!date_fin!!",$date_fin,$btn_date_fin);
		$btn_date_fin=str_replace("!!date_fin_lib!!",formatdate($date_fin),$btn_date_fin);
		
		if (!$date_ech)
			$date_ech = today();
		$btn_date_echeance=str_replace("!!date_ech!!",$date_ech,$btn_date_echeance);
		$btn_date_echeance=str_replace("!!date_ech_lib!!",formatdate($date_ech),$btn_date_echeance);
		
		$form_task=str_replace("!!date_deb_btn!!",$btn_date_deb,$form_task);
		$form_task=str_replace("!!date_fin_btn!!",$btn_date_fin,$form_task);
		$form_task=str_replace("!!echeance_btn!!",$btn_date_echeance,$form_task);
	 	
		return $form_task;
	}
	
	public function make_serialized_task_params() {
    	global $list_view, $conso, $date_deb, $date_fin, $date_ech;
		
    	$t = parent::make_serialized_task_params();
		
		if ($list_view) {
			foreach ($list_view as $id_vue) {
				$t["list_view"][$id_vue]=$id_vue;			
			}
		}
		$t["conso"] = $conso;
		$t["date_deb"] = $date_deb;
		$t["date_fin"] = $date_fin;
		$t["date_ech"] = $date_ech;

    	return serialize($t);
	}
}