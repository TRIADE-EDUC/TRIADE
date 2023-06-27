<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: stat_view.class.php,v 1.23 2017-11-21 12:00:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$include_path/templates/stat_opac.tpl.php");
require_once ($class_path . "/parse_format.class.php");
require_once("$include_path/misc.inc.php");
require_once("$include_path/user_error.inc.php");
require_once ($class_path . "/consolidation.class.php");
require_once ($class_path . "/stat_query.class.php");

class stat_view {
	
	public $action='';
	public $section='';
	
	/**
	 * Constructeur
	 */
	public function __construct($section='',$act=''){
		$this->action = $act;
		$this->section = $section;
	}
	
	/**
	 * Execution des différentes actions
	 */
	public function proceed(){
		global $msg, $id_col, $col_name, $expr_col, $expr_filtre, $view_name, $view_comment, $id_view; 
		global $id, $id_req, $move, $conso, $date_deb,$date_fin,$date_ech, $list_ck,$remove_data;
		
		if($id)
			$id_req=$id;
		
		switch($this->section){
			case 'view_list':
				switch($this->action){
					case 'save_view':
						//Enregistrement/Insertion d'une vue
						$this->save_view($id_view,$view_name,$view_comment);
						print $this->do_form();
					break;
					case 'suppr_view':
						//Suppression d'une vue
						$this->delete_view($id_view);
						print $this->do_form();
					break;
					case 'consolide_view':
						if($date_deb>$date_fin)
							error_form_message($msg['stat_wrong_date_interval']);
						elseif(!$list_ck)
							error_form_message($msg['stat_no_view_selected']);
						else { 
							$consolidation = new consolidation($conso,$date_deb,$date_fin,$date_ech, $list_ck,$remove_data);
							$consolidation->make_consolidation();
						}
						print $this->do_form();
					break;
					case 'reinit':
						//Réinitialisation de la vue
						$this->reinitialiser_view($id_view);
						print $this->do_form();
					break;
					//Actions liées aux requêtes
					case 'configure':
					case 'update_config':				
					case 'update_request':				
					case 'exec_req':
					case 'final':
						//Actions liées aux requêtes
						$stq = new stat_query($id_req,$this->action,$id_view);
						$stq->proceed();
						break;
					case 'save_request':				
					case 'suppr_request':
						$stq = new stat_query($id_req,$this->action,$id_view);
						$stq->proceed();
						print $this->do_form();
						break;
					default:
						print $this->do_form();
					break;
				}
				
			break;	
			case 'view_gestion':
				switch($this->action){
					case 'add_view':
						//ajout d'une vue
						//print $this->do_addview_form();
						break;					
					case 'update_view':
						//MaJ vue
						switch($move){
							case 'up':
								//Déplacer un élément dans la liste des colonnes
								$this->monter_element($id_col);
							break;
							case 'down':
								//Déplacer un élément dans la liste des colonnes
								$this->descendre_element($id_col);
							break;
						}	
					break;
					case 'save_col':
						//Enregistrement/Insertion d'une colonne
						$this->save_col($id_col,$col_name,$expr_col,$expr_filtre,$id_view);
					break;
					case 'suppr_col':
						//Suppression d'une colonne
						$this->delete_col($id_col);
					break;	
				}
				print $this->do_addview_form($id_view);
			break;
			case 'colonne':
				switch($this->action){
					case 'add_col':
						//ajout d'une colonne
						print $this->do_col_form();
					break;
					case 'save_col':
						//Enregistrement/Insertion d'une colonne
						$this->save_col($id_col,$col_name,$expr_col,$expr_filtre,$id_view);
						print $this->do_addview_form($id_view);
					break;
					case 'update_col':
						//MaJ colonne
						print $this->do_col_form($id_col);
					break;
					case 'suppr_col':
						//Suppression d'une colonne
						$this->delete_col($id_col);
						print $this->do_addview_form($id_view);
					break;	
				}
			break;
			case 'query':
				//Actions liées aux requêtes
				$stq = new stat_query($id_req,$this->action,$id_view);
				$stq->proceed();
			break;
			case 'import':
				//Formulaire import de requete
				print $this->do_import_req_form($id_view);
			break;
			case 'importsuite':
				//Import de requete
				$this->do_import_req($id_view);
			break;
			default:
			break;
		}
	}
	
	/**
	 * On fait appel au formulaire qui affiche la liste des vues
	 */
	public function do_form(){
		global $stat_opac_view_form, $msg;	
		global $msg, $dbh;
 		global $charset;
 		global $javascript_path;
		global $open_view,$alert_consolid;
 		
	 	print "
			<script type=\"text/javascript\" src=\"".$javascript_path."/tablist.js\"></script>
			<span class='item-expand'>
				<a href=\"javascript:expandAll()\"><img src='".get_url_icon('expand_all.gif')."' style='border:0px' id=\"expandall\" /></a>
				<a href=\"javascript:collapseAll()\"><img src='".get_url_icon('collapse_all.gif')."' style='border:0px' id=\"collapseall\" /></a>
			</span>
			";

	 	$requete_vue = "select * from statopac_vues order by date_consolidation desc, nom_vue";
	 	$res = pmb_mysql_query($requete_vue,$dbh);
	 	$vue_affichage="";	
		if(pmb_mysql_num_rows($res) == 0){			
			$stat_opac_view_form = str_replace('!!liste_vues!!',$msg['stat_no_view_created'],$stat_opac_view_form);
			$stat_opac_view_form = str_replace('!!options_conso!!','',$stat_opac_view_form);
			$stat_opac_view_form = str_replace('!!btn_consolide!!','',$stat_opac_view_form);
			return $stat_opac_view_form;
		} else {		
			$vue_affichage="";
			$parity=1;
			$btn_consolide= "<input class='bouton' type='submit' value=\"".$msg['stat_consolide_view']."\" onClick=\"this.form.act.value='consolide_view'; document.view.action='./admin.php?categ=opac&sub=stat&section=view_list'\"/>";
			while(($vue = pmb_mysql_fetch_object($res))){
				$min_date='';
				$max_date='';
				$view_scope = htmlentities($msg['stat_view_no_scope'],ENT_QUOTES,$charset);
				$min_date=$vue->date_debut_log;
				$max_date=$vue->date_fin_log;
				if ($min_date!='0000-00-00 00:00:00' && $max_date!='0000-00-00 00:00:00') {
					$view_scope = sprintf(htmlentities($msg['stat_view_scope'],ENT_QUOTES,$charset),formatdate($min_date),formatdate($max_date));
				}
				$rqt="select * from statopac_request where num_vue='".addslashes($vue->id_vue)."' order by name";
				$result = pmb_mysql_query($rqt, $dbh);
				$liste_requete ="";
				while(($request = pmb_mysql_fetch_object($result))){
					if ($parity % 2) {
						$pair_impair = "even";
					} else {
						$pair_impair = "odd";
					}
					$parity++;
					$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
					$td_action = " onmousedown=\"document.location='./admin.php?categ=opac&sub=stat&section=query&act=update_request&id_req=$request->idproc&id_view=$vue->id_vue';\" ";
					$btn_exec = "<input type='submit' class='bouton_small' name='exec_request' value='$msg[708]' onClick='document.view.action=\"./admin.php?categ=opac&sub=stat&section=view_list\";this.form.act.value=\"exec_req\"; this.form.id_req.value=\"$request->idproc\"; this.form.id_view.value=\"$vue->id_vue\";'/>";
					$btn_save = "<input type='submit' class='bouton_small' name='save_request' value='".$msg["procs_bt_export"]."' onClick='document.view.action=\"./export.php?quoi=stat\";this.form.act.value=\"save_req\"; this.form.id_req.value=\"$request->idproc\"; this.form.id_view.value=\"$vue->id_vue\";'/>";
					$liste_requete.="<tr class='$pair_impair'  $tr_javascript style='cursor: pointer'>
										<td style='width:10px'>$btn_exec</td>
										<td $td_action><strong>$request->name</strong><br /><small>$request->comment</small></td><td>
						";	
					if (preg_match_all("|!!(.*)!!|U",$request->requete,$query_parameters)) $liste_requete.="<a href='admin.php?categ=opac&sub=stat&section=view_list&act=configure&id_req=".$request->idproc."'>".$msg["procs_options_config_param"]."</a>";
					$liste_requete.="</td><td style='width:10px'>$btn_save</td></tr>";					
				}
				
				$tab_list="<table><tr><th colspan='4'>".htmlentities($vue->nom_vue,ENT_QUOTES, $charset)."</th></tr>".$liste_requete."</table>";
				$lien = "<a href='./admin.php?categ=opac&sub=stat&section=view_gestion&act=update_view&id_view=$vue->id_vue'>".htmlentities($vue->nom_vue,ENT_QUOTES, $charset) ."</a>";
				$space = "<small><span style='margin-right: 3px;'><img src='".get_url_icon('spacer.gif')."' style='width:10px' height='10' /></span></small>";
				$checkbox = "<input type='checkbox' class='checkbox' id='box$vue->id_vue' name='list_ck[]' value='$vue->id_vue' />"; 				
				$btn = "<div class='row'>
						<input class='bouton_small' type='button' value=\"".$msg['stat_add_request']."\" onClick=\"document.location='./admin.php?categ=opac&sub=stat&section=query&act=update_request&id_view=$vue->id_vue';\" />
						<input class='bouton_small' type='button' value=\"".$msg['stat_imp_request']."\" onClick=\"document.location='./admin.php?categ=opac&sub=stat&section=import&id_view=$vue->id_vue';\" />
					</div>";
				$date_conso='';
				if ($vue->date_consolidation!=='0000-00-00 00:00:00') {
					$date_conso = sprintf($msg['stat_view_date_conso'],formatdate($vue->date_consolidation,true),$view_scope);
				}	
				$libelle_titre = $space.$checkbox.$space.$lien.$space.$date_conso;
				$maximise = false;
				if ($open_view == $vue->id_vue) {
					$maximise = true;
				}
				$vue_affichage.=gen_plus($vue->id_vue,$libelle_titre,$tab_list.$btn,$maximise);
			}
			
			
			//Liste des options de consolidation
			$min_date='';
			$max_date='';
			$stat_scope = htmlentities($msg['stat_no_scope'],ENT_QUOTES,$charset);
			$q_sc = 'select min(date_log) as min_date, max(date_log) as max_date from statopac';
			$r_sc = pmb_mysql_query($q_sc,$dbh);
			if ($r_sc && pmb_mysql_num_rows($r_sc)) {
				$res_sc=pmb_mysql_fetch_object($r_sc);
				$min_date=$res_sc->min_date;
				$max_date=$res_sc->max_date;
				if ($min_date!='0000-00-00 00:00:00' && $min_date!='0000-00-00 00:00:00') {
					$stat_scope = sprintf(htmlentities($msg['stat_scope'],ENT_QUOTES,$charset),formatdate($res_sc->min_date),formatdate($res_sc->max_date));
				}
			}
			$options = "<div id='opt_consoParent' class='notice-parent'>";
			$options .= "<img id='opt_consoImg' class='img_plus' style='border:0px; margin:3px 3px' onClick=\"expandBase('opt_conso',true);return false;\" title='requete' name='imEx' src=\"".get_url_icon('plus.gif')."\" />";
			$options .= "$space <span class='notice-heada'>".htmlentities($msg['stat_options_consolidation'],ENT_QUOTES,$charset)."</span>";
			$options .= "$space $stat_scope";
			$options .= "</div>";	
			$options_contenu ="<div class='row'>
					<input type='radio' class='radio' id='id_lot' name='conso' value='1' checked='checked' onClick=\"document.getElementById('remove_data').checked=false;\" /> 
						<label for='id_lot'>$msg[stat_last_consolidation]</label><br /><br />
					<input type='radio' class='radio' id='id_interval' name='conso' value='2' onClick=\"document.getElementById('remove_data').checked=false;\" /> 
						<label for='id_interval'>$msg[stat_interval_consolidation] </label><br /><br />
					<input type='radio' class='radio' id='id_debut' name='conso' value='3' onClick=\"document.getElementById('remove_data').checked=false;\" /> 
						<label for='id_debut'>$msg[stat_echeance_consolidation]</label><br /><br />
					<input type='checkbox' name='remove_data' id='remove_data' value='1'/>
						<label for='remove_data'>$msg[stat_remove_data]</label><br />
					</div>
			";
			$options.="<div id='opt_consoChild' class='notice-child' style='margin-bottom: 6px; display: none;'>$options_contenu</div>";
			$stat_opac_view_form=str_replace("!!options_conso!!",$options,$stat_opac_view_form);
			$stat_opac_view_form=str_replace("!!liste_vues!!",$vue_affichage,$stat_opac_view_form);
			$stat_opac_view_form=str_replace("!!btn_consolide!!",$btn_consolide,$stat_opac_view_form);
			
			$btn_date_deb = "<input type='hidden' name='date_deb' value='!!date_deb!!'/><input type='button' name='date_deb_lib' class='bouton_small' value='!!date_deb_lib!!'   
				onClick=\"openPopUp('./select.php?what=calendrier&caller=view&date_caller=!!date_deb!!&param1=date_deb&param2=date_deb_lib&auto_submit=NO&date_anterieure=YES', 'calendar');\" />";
			$btn_date_fin = "<input type='hidden' name='date_fin' value='!!date_fin!!'/><input type='button' name='date_fin_lib' class='bouton_small'   value='!!date_fin_lib!!'
				onClick=\"openPopUp('./select.php?what=calendrier&caller=view&date_caller=!!date_fin!!&param1=date_fin&param2=date_fin_lib&auto_submit=NO&date_anterieure=YES', 'calendar');\" />";
			$btn_date_echeance = "<input type='hidden' name='date_ech' value='!!date_ech!!'/><input type='button' name='date_ech_lib' class='bouton_small' value='!!date_ech_lib!!'  
				onClick=\"openPopUp('./select.php?what=calendrier&caller=view&date_caller=!!date_ech!!&param1=date_ech&param2=date_ech_lib&auto_submit=NO&date_anterieure=YES', 'calendar');\" />";
			
			$date_debut = strftime("%Y-%m-%d", mktime(0, 0, 0, date('m'), date('d')-1, date('y'))); 
			$btn_date_deb=str_replace("!!date_deb!!",$date_debut,$btn_date_deb);
			$btn_date_deb=str_replace("!!date_deb_lib!!",formatdate($date_debut),$btn_date_deb);
			$date_fin = today();			
			$btn_date_fin=str_replace("!!date_fin!!",$date_fin,$btn_date_fin);
			$btn_date_fin=str_replace("!!date_fin_lib!!",formatdate($date_fin),$btn_date_fin);
			$date_echeance = today();
			$btn_date_echeance=str_replace("!!date_ech!!",$date_echeance,$btn_date_echeance);
			$btn_date_echeance=str_replace("!!date_ech_lib!!",formatdate($date_echeance),$btn_date_echeance);
			$stat_opac_view_form=str_replace("!!date_deb_btn!!",$btn_date_deb,$stat_opac_view_form);
			$stat_opac_view_form=str_replace("!!date_fin_btn!!",$btn_date_fin,$stat_opac_view_form);
			$stat_opac_view_form=str_replace("!!echeance_btn!!",$btn_date_echeance,$stat_opac_view_form);
			
			if ($alert_consolid) {
				$stat_opac_view_form.=display_notification($msg["stat_import_consolide"]);
			}
	 	}
	 	
		return $stat_opac_view_form;
		
	}
	
	/**
	 * On fait appel au formulaire d'ajout d'une vue
	 */
	public function do_addview_form($vue_id=''){
		global $stat_view_addview_form;
		global $msg, $charset;
		global $dbh;
		
		if(!$vue_id){
			$stat_view_addview_form=str_replace("!!name_view!!",'',$stat_view_addview_form);
			$stat_view_addview_form=str_replace("!!view_comment!!",'',$stat_view_addview_form);
			$stat_view_addview_form=str_replace("!!table_colonne!!",'',$stat_view_addview_form);
			$stat_view_addview_form=str_replace("!!bouton_add_col!!",'',$stat_view_addview_form);
			$stat_view_addview_form=str_replace("!!bouton_reinit_view!!",'',$stat_view_addview_form);
			$stat_view_addview_form=str_replace("!!btn_suppr!!",'',$stat_view_addview_form);
			$stat_view_addview_form=str_replace("!!view_title!!",$msg["stat_view_create_title"],$stat_view_addview_form);
			$stat_view_addview_form=str_replace("!!id_view!!",'',$stat_view_addview_form);
						
			return $stat_view_addview_form;
			
		} else {
			$btn_add_col = "<input class='bouton' type='submit'  value=\"".$msg['stat_add_col']."\" onClick='this.form.act.value=\"add_col\"; document.addview.action=\"./admin.php?categ=opac&sub=stat&section=colonne&action=addcol\";'/>";
			$bouton_reinit_view="<input class='bouton' type='submit'  value=\"".$msg['stat_reinit_view']."\" onClick='this.form.act.value=\"reinit\";'/>";
			$btn_suppr = "<input class='bouton' type='submit'  value='$msg[63]' onClick='if(confirm_delete()) this.form.act.value=\"suppr_view\";'/>";
			
			$requete = "select nom_vue, comment from statopac_vues where id_vue='".addslashes($vue_id)."'";
			$resultat = pmb_mysql_query($requete, $dbh);
			while(($vue=pmb_mysql_fetch_object($resultat))){
				$stat_view_addview_form=str_replace("!!name_view!!",htmlentities($vue->nom_vue,ENT_QUOTES,$charset),$stat_view_addview_form);
				$stat_view_addview_form=str_replace("!!view_comment!!",htmlentities($vue->comment,ENT_QUOTES, $charset),$stat_view_addview_form);
			}			
			$stat_view_addview_form=str_replace("!!bouton_add_col!!",$btn_add_col,$stat_view_addview_form);
			$stat_view_addview_form=str_replace("!!bouton_reinit_view!!",$bouton_reinit_view,$stat_view_addview_form);
			$stat_view_addview_form=str_replace("!!btn_suppr!!",$btn_suppr,$stat_view_addview_form);
			$stat_view_addview_form=str_replace("!!id_view!!",$vue_id,$stat_view_addview_form);
				
			$res="";		
			$requete="select id_col, nom_col, expression, filtre, ordre, datatype from statopac_vues_col where num_vue='".$vue_id."' order by ordre";
			$resultat=pmb_mysql_query($requete, $dbh);
			
			if(pmb_mysql_num_rows($resultat) == 0){
				$res="<div class='row'>".$msg["stat_no_col_associate"]."</div>";
				$stat_view_addview_form=str_replace("!!table_colonne!!",$res,$stat_view_addview_form);		
				$stat_view_addview_form=str_replace("!!view_title!!",$msg["stat_view_modif_title"],$stat_view_addview_form);
				return $stat_view_addview_form;
			} else {
				$res="<table style='width:100%'>\n";
				$res.="<tr><th>".$msg["stat_col_order"]."</th><th>".$msg["stat_col_name"]."</th><th>".$msg["stat_col_expr"]."</th><th>".$msg["stat_col_filtre"]."</th><th>".$msg['stat_col_type']."</th>";
				$parity=1;
				$n=0;
				while ($r=pmb_mysql_fetch_object($resultat)) {
					if ($parity % 2) {
						$pair_impair = "even";
					} else {
						$pair_impair = "odd";
					}
		
					$parity+=1;
					$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\"  ";
					$action_td=" onmousedown=\"document.location='./admin.php?categ=opac&sub=stat&section=colonne&act=update_col&id_col=$r->id_col&id_view=$vue_id';\" ";
					$res.="<tr class='$pair_impair' style='cursor: pointer' $tr_javascript>";
					$res.="<td class='center'>";
				    $res.="<input type='button' class='bouton_small' value='-' onClick='document.location=\"./admin.php?categ=opac&sub=stat&section=view_gestion&act=update_view&move=down&id_col=".$r->id_col."&id_view=$vue_id\"'/></a>";
				    $res .= "<input type='button' class='bouton_small' value='+' onClick='document.location=\"./admin.php?categ=opac&sub=stat&section=view_gestion&act=update_view&move=up&id_col=".$r->id_col."&id_view=$vue_id\"'/>";
					$res.="</td>";
					$res.="<td $action_td class='center'><b>".htmlentities($r->nom_col,ENT_QUOTES,$charset)."</b></td>
						<td $action_td class='center'>".htmlentities($r->expression,ENT_QUOTES,$charset)."</td>
						<td $action_td class='center'>".htmlentities($r->filtre,ENT_QUOTES,$charset)."</td>
						<td $action_td class='center'>".htmlentities($r->datatype,ENT_QUOTES,$charset)."</td>";
				}
				$res.="</tr></table>";
				$stat_view_addview_form=str_replace("!!table_colonne!!",$res,$stat_view_addview_form);
				$stat_view_addview_form=str_replace("!!view_title!!",$msg["stat_view_modif_title"],$stat_view_addview_form);
			}
		}
		return $stat_view_addview_form;
	}
	
	/**
	 * On fait appel au formulaire d'ajout de colonne
	 */
	public function do_col_form($id_col=''){
		global $stat_view_addcol_form, $msg, $charset, $id_view; 
		global $dbh;
		
		$datatype_list=array("small_text"=>"Texte","text"=>"Texte large","integer"=>"Entier","date"=>"Date","datetime"=>"Date/Heure","float"=>"Nombre &agrave; virgule");
		if(!$id_col)	{
			$stat_view_addcol_form=str_replace("!!col_name!!",'',$stat_view_addcol_form);
			$stat_view_addcol_form=str_replace("!!expr_col!!",'',$stat_view_addcol_form);
			$stat_view_addcol_form=str_replace("!!btn_suppr!!",'',$stat_view_addcol_form);
			$stat_view_addcol_form=str_replace("!!expr_filtre!!",'',$stat_view_addcol_form);
			$stat_view_addcol_form=str_replace("!!id_view!!",$id_view,$stat_view_addcol_form);
			$stat_view_addcol_form=str_replace("!!id_col!!",$id_col,$stat_view_addcol_form);
			$stat_view_addcol_form=str_replace("!!col_title!!",$msg["stat_col_create_title"],$stat_view_addcol_form);
						
			//liste des type de données
			$t_list="<select name='datatype'>\n";
			reset($datatype_list);
			foreach ($datatype_list as $key=>$val){
				$t_list.="<option value='".$key."'";
				$t_list.=">".$val."</option>\n";
			}
			$t_list.="</select>\n";
			$stat_view_addcol_form=str_replace("!!datatype!!",$t_list,$stat_view_addcol_form);
			
			return $stat_view_addcol_form;
		} else {
			$requete="select nom_col, expression, filtre, datatype from statopac_vues_col where id_col='".$id_col."'";
			$resultat=pmb_mysql_query($requete, $dbh);
			while (($col=pmb_mysql_fetch_object($resultat))){
				$col_name = htmlentities($col->nom_col,ENT_QUOTES,$charset);
				$expr = htmlentities($col->expression,ENT_QUOTES,$charset);
				$filtre = htmlentities($col->filtre,ENT_QUOTES,$charset);
				$datatype = htmlentities($col->datatype,ENT_QUOTES,$charset);
			}
			$stat_view_addcol_form=str_replace("!!col_name!!",$col_name,$stat_view_addcol_form);
			$stat_view_addcol_form=str_replace("!!expr_col!!",$expr,$stat_view_addcol_form);
			$stat_view_addcol_form=str_replace("!!expr_filtre!!",$filtre,$stat_view_addcol_form);
			$btn_suppr = "<input class='bouton' type='submit'  value='$msg[63]' onClick='if(confirm_delete()) this.form.act.value=\"suppr_col\"';/>";
			$stat_view_addcol_form=str_replace("!!btn_suppr!!",$btn_suppr,$stat_view_addcol_form);
			$stat_view_addcol_form=str_replace("!!col_title!!",$msg["stat_col_modif_title"],$stat_view_addcol_form);
			$stat_view_addcol_form=str_replace("!!id_view!!",$id_view,$stat_view_addcol_form);
			$stat_view_addcol_form=str_replace("!!id_col!!",$id_col,$stat_view_addcol_form);
			
			//liste des types de données
			$t_list="<select name='datatype'>\n";
			reset($datatype_list);
			foreach ($datatype_list as $key=>$val){
				$t_list.="<option value='".$key."'";
				if ($datatype==$key) $t_list.=" selected";
				$t_list.=">".$val."</option>\n";
			}
			$t_list.="</select>\n";
			$stat_view_addcol_form=str_replace("!!datatype!!",$t_list,$stat_view_addcol_form);
			
		}
		
		return $stat_view_addcol_form;
	}
	
	/**
	 * On insere ou enregistre une colonne
	 */
	public function save_col($id_col='', $col_name='',$expr_col='',$expr_filtre='', $vue_id=''){
		global $datatype;
		global $dbh;
		
		if((!$id_col) && $vue_id){
			$req_ordre = "select max(ordre) from statopac_vues_col where num_vue='".addslashes($vue_id)."'";
			$resultat = pmb_mysql_query($req_ordre, $dbh);
			if($resultat) $order = pmb_mysql_result($resultat,0,0);
			else $order=0;
			$ordre = $order+1;
			$req = "INSERT INTO statopac_vues_col(nom_col,expression,filtre,num_vue, ordre,datatype) VALUES ('".$col_name."', '".$expr_col."','".$expr_filtre."','".$vue_id."','".$ordre."', '".$datatype."')";
			$resultat=pmb_mysql_query($req, $dbh);
		} else {
			$rqt="select * from statopac_vues_col where nom_col='".$col_name."' and expression='".$expr_col."' and num_vue='".$vue_id."' and filtre='".$expr_filtre."' and datatype='".$datatype."'";
			$res_exist = pmb_mysql_query($rqt, $dbh);
			if(pmb_mysql_num_rows($res_exist)){
				$modif=0;
			} else $modif=1;
			$req = "UPDATE statopac_vues_col SET nom_col='".$col_name."', expression='".$expr_col."', num_vue='".$vue_id."', filtre='".$expr_filtre."', datatype='".$datatype."', maj_flag=$modif  WHERE id_col='".$id_col."'";
			$resultat=pmb_mysql_query($req, $dbh);
		}
	} 
	
	/**
	 * On insere ou enregistre une vue
	 */
	public function save_view($vue_id='', $view_name='',$view_comment=''){
		global $dbh;
		
		if(!$vue_id){
			$req = "INSERT INTO statopac_vues(nom_vue,comment) VALUES ('".$view_name."', '".$view_comment."')";
			pmb_mysql_query($req, $dbh);
		} else {
			$req = "UPDATE statopac_vues SET nom_vue='".$view_name."', comment='".$view_comment."' WHERE id_vue='".$vue_id."'";
			pmb_mysql_query($req, $dbh);
		}
	}
	
	/**
	 * Supprime une vue et ces colonnes associées
	 */
	public function delete_view($vue_id){
		global $dbh;
		
		if($vue_id){
			$req="DELETE FROM statopac_vues where id_vue='".$vue_id."'";
			$resultat=pmb_mysql_query($req, $dbh);
			$req="DELETE FROM statopac_vues_col where num_vue='".$vue_id."'";
			$resultat=pmb_mysql_query($req, $dbh);
			$req="DELETE FROM statopac_request where num_vue='".$vue_id."'";
			$resultat=pmb_mysql_query($req, $dbh);
			$req="DROP TABLE statopac_vue_".$vue_id;
			$resultat=pmb_mysql_query($req, $dbh);
		}
	}
	
	/**
	 * Réinitialise la vue à zéro
	 */
	public function reinitialiser_view($vue_id=''){
		global $dbh;
		
		if($vue_id){
			$req="DELETE FROM statopac_vues_col where num_vue='".$vue_id."'";
			$resultat=pmb_mysql_query($req, $dbh);
			$req="DELETE FROM statopac_request where num_vue='".$vue_id."'";
			$resultat=pmb_mysql_query($req, $dbh);
			$req="DELETE FROM statopac_vue_".$vue_id;
			$resultat=pmb_mysql_query($req, $dbh);
			$req="update statopac_vues set date_consolidation='0000-00-00 00:00:00', date_debut_log='0000-00-00 00:00:00', date_fin_log='0000-00-00 00:00:00' where num_vue='".$vue_id."'";
			$resultat=pmb_mysql_query($req, $dbh);
		}
	}
	
	/**
	 * Supprime une colonne
	 */
	public function delete_col($id_col){
		global $dbh;
		
		if($id_col){
			$req="SELECT nom_col,num_vue FROM statopac_vues_col WHERE id_col='".$id_col."'";
			$res=pmb_mysql_query($req, $dbh);
			if(pmb_mysql_num_rows($res)){
				//On supprime la colonne de la vue
				$id_vue=pmb_mysql_result($res,0,1);
				pmb_mysql_query("ALTER TABLE statopac_vue_".$id_vue." DROP `".pmb_mysql_result($res,0,0)."`", $dbh);
				$req="DELETE FROM statopac_vues_col where id_col='".$id_col."'";
				$resultat=pmb_mysql_query($req, $dbh);
				//On recalcule l'ordre des colonnes
				$req="SELECT id_col FROM statopac_vues_col WHERE num_vue ='".$id_vue."' ORDER BY ordre";
				$res=pmb_mysql_query($req, $dbh);
				if(pmb_mysql_num_rows($res)){
					$ordre=1;
					while ($ligne=pmb_mysql_fetch_object($res)) {
						pmb_mysql_query("UPDATE statopac_vues_col SET ordre='".$ordre."' WHERE id_col='".$ligne->id_col."'", $dbh);
						$ordre++;
					}
				}
			}	
		}
	}

	/**
	 * Changer l'ordre dans la liste en montant un élément
	 */
	public function monter_element($col_id=''){
		global $dbh;
		
		$requete="select ordre from statopac_vues_col where id_col='".$col_id."'";
		$resultat=pmb_mysql_query($requete, $dbh);
		$ordre=pmb_mysql_result($resultat,0,0);
		$requete="select max(ordre) as ordre from statopac_vues_col where ordre<".addslashes($ordre);
		$resultat=pmb_mysql_query($requete, $dbh);
		$ordre_max=@pmb_mysql_result($resultat,0,0);
		if ($ordre_max) {
			$requete="select id_col from statopac_vues_col where ordre='".addslashes($ordre_max)."' limit 1";
			$resultat=pmb_mysql_query($requete, $dbh);
			$idcol_max=pmb_mysql_result($resultat,0,0);
			$requete="update statopac_vues_col set ordre='".addslashes($ordre_max)."' where id_col='".$col_id."'";
			pmb_mysql_query($requete, $dbh); 
			$requete="update statopac_vues_col set ordre='".addslashes($ordre)."' where id_col='".addslashes($idcol_max)."'";
			pmb_mysql_query($requete, $dbh);
		}
	}
	
	/**
	 * Changer l'ordre dans la liste en descendant un élément
	 */
	public function descendre_element($col_id=''){
		global $dbh;
		
		$requete="select ordre from statopac_vues_col where id_col='".$col_id."'";
		$resultat=pmb_mysql_query($requete, $dbh);
		$ordre=pmb_mysql_result($resultat,0,0);
		$requete="select min(ordre) as ordre from statopac_vues_col where ordre>".addslashes($ordre);
		$resultat=pmb_mysql_query($requete, $dbh);
		$ordre_min=@pmb_mysql_result($resultat,0,0);
		if ($ordre_min) {
			$requete="select id_col from statopac_vues_col where ordre='".addslashes($ordre_min)."' limit 1";
			$resultat=pmb_mysql_query($requete, $dbh);
			$idcol_min=pmb_mysql_result($resultat,0,0);
			$requete="update statopac_vues_col set ordre='".addslashes($ordre_min)."'  where id_col='".$col_id."'";
			pmb_mysql_query($requete, $dbh);
			$requete="update statopac_vues_col set ordre='".addslashes($ordre)."'  where id_col='".addslashes($idcol_min)."'";
			pmb_mysql_query($requete, $dbh);
		}
	}
	
	
	
	
	
	/**
	 * Verification de la presence et de la syntaxe des parametres de la requete
	 * retourne true si OK, le nom du parametre entre parentheses sinon
	 */
	public function check_param($requete) {
		$query_parameters=array();
		//S'il y a des termes !!*!! dans la requête alors il y a des paramètres
		if (preg_match_all("|!!(.*)!!|U",$requete,$query_parameters)) {
			for ($i=0; $i<count($query_parameters[1]); $i++) {
				if (!preg_match("/^[A-Za-z][A-Za-z0-9_]*$/",$query_parameters[1][$i])) {
					return "(".$query_parameters[1][$i].")";
				}
			}
		}
		return true;
	}
	
	/**
	 * On fait appel au formulaire d'ajout d'une requete à la vue
	 */
	public function do_import_req_form($vue_id=''){
		global $stat_view_import_req_form;
		global $msg, $charset;
		
		$action="./admin.php?categ=opac&sub=stat&section=importsuite&id_view=".$vue_id;
		$stat_view_import_req_form=str_replace("!!action!!",$action,$stat_view_import_req_form);
		
		return $stat_view_import_req_form;
	}

	/**
	 * On importe la requête à la vue
	 */
	public function do_import_req($vue_id=''){
		global $dbh, $msg, $charset;
		
		if($vue_id){
			$erreur=0;
			$userfile_name = $_FILES['f_fichier']['name'];
			$userfile_temp = $_FILES['f_fichier']['tmp_name'];
			$userfile_moved = basename($userfile_temp);
			
			$userfile_name = preg_replace("/ |'|\\|\"|\//m", "_", $userfile_name);
			
			// création
			if (move_uploaded_file($userfile_temp,'./temp/'.$userfile_moved)) {
				$fic=1;
			}
			
			if (!$fic) {
				$erreur=$erreur+10;
			}else{
				$fp = fopen('./temp/'.$userfile_moved , "r" );
				$contenu = fread ($fp, filesize('./temp/'.$userfile_moved));
				if (!$fp || $contenu=="") $erreur=$erreur+100; ;
				fclose ($fp) ;
			}
			
			//Vérification du contenu du fichier
			$arrayCols=array();
			$tmpLignes=explode("\n",$contenu);
			foreach ($tmpLignes as $ligne){
				if(preg_match('`^\#col=(.+)`',$ligne,$out)){
					$arrayCols[]=unserialize($out[1]);
				}
			}
			if(!count($arrayCols)){
				$erreur=5;
			}
			
			if(!$erreur){
				
				//Traitement encodage fichier
				if(strpos($contenu,'#charset=iso-8859-1')!==false && $charset=='utf-8'){
					//mise à jour de l'encodage du contenu
					$contenu = utf8_encode($contenu);
					//mise à jour de l'entête des paramètres
					$contenu = str_replace('<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>', '<?xml version=\"1.0\" encoding=\"utf-8\"?>', $contenu) ;
				}elseif(strpos($contenu,'#charset=utf-8')!==false && $charset=='iso-8859-1'){
					//mise à jour de l'encodage du contenu
					$contenu = utf8_decode($contenu);
					//mise à jour de l'entête des paramètres
					$contenu = str_replace('<?xml version=\"1.0\" encoding=\"utf-8\"?>', '<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>', $contenu) ;
				}
				//On distingue les différentes parties, la requête se trouve en $arrayFichier[2]
				preg_match('`(.+requete=\')(.+)(\', comment=\'.+)`s',$contenu,$arrayFichier);
				unset($arrayFichier[0]);
				//On va vérifier les colonnes de la vue existante
				$nbColAjout=0;
				foreach ($arrayCols as $col){
					$res = pmb_mysql_query("SELECT * FROM statopac_vues_col WHERE num_vue=".$vue_id." AND expression='".addslashes($col[1])."'", $dbh);
					if($res){
						if(!pmb_mysql_num_rows($res)){
							//on va ajouter une colonne, on vérifie qu'il n'y a pas déjà une colonne avec le même nom
							$ok = false;
							$suffixe=0;
							while(!$ok){
								$res2 = pmb_mysql_query("SELECT * FROM statopac_vues_col WHERE num_vue=".$vue_id." AND nom_col='".addslashes($col[0]).($suffixe?$suffixe:"")."'", $dbh);
								if($res2){
									if(!pmb_mysql_num_rows($res2)){
										$ok=true;
										if($suffixe){
											$arrayFichier[2] = preg_replace('`(?<=\W)'.$col[0].'(?<!\W)`',$col[0].$suffixe,$arrayFichier[2]);
											$col[0]=$col[0].$suffixe;
										}
									}
								}else{
									echo pmb_mysql_error()."<br />";
								}
								$suffixe++;
							}
							pmb_mysql_query("INSERT INTO statopac_vues_col
										SET nom_col='".addslashes($col[0])."',
										expression='".addslashes($col[1])."',
										filtre='".addslashes($col[2])."',
										datatype='".addslashes($col[3])."',
										num_vue=".$vue_id, $dbh);
							$nbColAjout++;
						}else{
							//une colonne existe déjà avec la même fonction : on adapte la requête qu'on importe
							$row=pmb_mysql_fetch_object($res);
							$arrayFichier[2] = str_replace('`(?<=\W)'.$col[0].'(?<!\W)`',$row->nom_col,$arrayFichier[2]);
						}
					}else{
						echo pmb_mysql_error()."<br />";
					}
				}
				
				//Ajout requete
				$contenu=implode("",$arrayFichier);
				pmb_mysql_query($contenu, $dbh) ;
				if (pmb_mysql_error()) {
					echo pmb_mysql_error()."<br /><br />".htmlentities($contenu,ENT_QUOTES, $charset)."<br /><br />" ;
				}else{
					$idStat = pmb_mysql_insert_id();

					//maj num_vue sur requete
					pmb_mysql_query("UPDATE statopac_request SET num_vue=".$vue_id." WHERE idproc=".$idStat, $dbh);
					
					$add_url='';
					if($nbColAjout){
						$add_url='&alert_consolid=1';
					}
					print "<script type=\"text/javascript\">document.location='./admin.php?categ=opac&sub=stat&section=view_list&open_view=".$vue_id.$add_url."';</script>";
				}
			
			} else {
				print "<h1>".$msg['stat_import_invalide']."</h1>
						<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"./admin.php?categ=opac&sub=stat&section=import&id_view=".$vue_id."\" >
						Error code = $erreur
						<input type='submit' class='bouton' name=\"id_form\" value=\"Ok\" />
						</form>";
			}
			print "</div>";
			
			//On efface le fichier temporaire
			if ($userfile_name) {
				unlink('./temp/'.$userfile_moved);
			}
		}else{
			$erreur=1;
			print "<h1>".$msg['stat_import_invalide']."</h1>
			<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"./admin.php?categ=opac&sub=stat&section=import&id_view=".$vue_id."\" >
			Error code = $erreur
			<input type='submit' class='bouton' name=\"id_form\" value=\"Ok\" />
			</form>";
		}

	}
	
	
}
?>