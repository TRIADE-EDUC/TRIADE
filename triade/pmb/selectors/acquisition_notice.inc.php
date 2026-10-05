<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: acquisition_notice.inc.php,v 1.32 2019/12/16 16:28:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "inc.php")) die("no access");

global $notice_statut_query, $doctype_query;

// la variable $caller, passée par l'URL, contient le nom du form appelant
$base_url = "./select.php?what=acquisition_notice";
$base_url.= "&caller=$caller";		//nom de la fenetre appellante
$base_url.= "&cr=$cr";				//numero de ligne appellante
$base_url.= "&no_display=$no_display";
$base_url.= "&bt_ajouter=$bt_ajouter";
$base_url.= "&callback=$callback";

// contenu popup selection 
require_once("$class_path/sel_searcher.class.php");
require_once("$base_path/selectors/templates/sel_searcher_templates.tpl.php");

if (!$typ_query) {
	$typ_query='notice';
}

$tab_choice = array(0=>'notice', 1=>'bulletin', 2=>'abt', 3=>'article', 4=>'frais');

switch ($typ_query) {

	case 'notice' :
		$sh=new sel_searcher_notice_mono($base_url); 
		
		$sh->tab_choice = $tab_choice;
		$sh->elt_b_list = $elt_b_list_notice;
		$sh->elt_r_list = $elt_r_list_notice;
		$sh->elt_r_list_values = array(0=>'result', 1=>'nb_expl');
		$sh->action = "<a href='#' onclick=\"set_parent('!!notice_id!!', '!!code!!', '!!titre!!', '!!auteur1!!', '!!editeur1!!', '!!ed_date!!', '!!collection!!', '!!prix!!', '".$callback."');\">!!display!!</a> ";
		$sh->action_values = array(0=>'notice_id', 1=>'code', 2=>'titre', 3=>'auteur1', 4=>'editeur1', 5=>'ed_date', 6=>'collection', 7=>'prix');
		$sh->back_script = "
		<script type='text/javascript'>
			<!--
			function set_parent(notice_id, code, titre, auteur1, editeur1, ed_date, collection, prix, callback) {
			
				var ex=window.parent.act_lineAlreadyExists(0, notice_id, '1');
				var q,cr;
				var v=1;
				if (ex!=false) {
					q=window.parent.document.forms['$caller'].elements['qte['+ex+']'];
					v=q.value;
					r=prompt('".addslashes($msg['acquisition_act_mod_qte'])."', v);
					if (r) {
						q.value=r;
						window.parent.act_calc();
						return false;
					} return false;
				}
				if (window.parent.mod==1) {
					cr='$cr';
				} else {
					cr = window.parent.act_getEmptyLine(); 
				} 
				window.parent.mod=0;
				window.parent.document.forms['$caller'].elements['typ_lig['+cr+']'].value = '1';
				window.parent.document.forms['$caller'].elements['id_prod['+cr+']'].value = notice_id;
				window.parent.document.forms['$caller'].elements['code['+cr+']'].value = reverse_html_entities(code);
				var taec=titre;
				if (auteur1 != '') taec=taec+'\\n'+auteur1;
				if (editeur1 != '') taec=taec+'\\n'+editeur1;
				if (editeur1 != '' && ed_date != '') taec=taec+', '+reverse_html_entities(ed_date);
				else if (ed_date != '') taec=taec+'\\n'+reverse_html_entities(ed_date);
				if (collection != '') taec=taec+'\\n'+collection; 
				window.parent.document.forms['$caller'].elements['lib['+cr+']'].value = reverse_html_entities(taec);
				window.parent.document.forms['$caller'].elements['prix['+cr+']'].value = reverse_html_entities(prix);
				window.parent.act_calc();
				q=window.parent.document.forms['$caller'].elements['qte['+cr+']'];
				q.value=v;
				q.focus();
				if(callback) {
					window.parent[callback](cr);
				}
			}
			
			function check_uncheck(do_check){
				if(do_check==1){
					document.getElementById('searcher_results_check_all').value='".$msg['searcher_results_uncheck_all']."';
					document.getElementById('searcher_results_check_all').onclick=function(){check_uncheck(0);};
				}else{
					document.getElementById('searcher_results_check_all').value='".$msg['searcher_results_check_all']."';
					document.getElementById('searcher_results_check_all').onclick=function(){check_uncheck(1);};
				}
				var elts = document.forms['searcher_results_check_form'].elements['sel_searcher_select_[]'] ;
				var elts_cnt = (typeof(elts.length) != 'undefined') ? elts.length : 0;
				if (elts_cnt) {
					for (var i = 0; i < elts_cnt; i++) {
						elts[i].checked = do_check;
					}
				} else {
					elts.checked = do_check;
				}
				return true;
			}
			
			function add_selection(){
				var arrayChecked = [];
				var elts = document.forms['searcher_results_check_form'].elements['sel_searcher_select_[]'] ;
				var elts_cnt = (typeof(elts.length) != 'undefined') ? elts.length : 0;
				if (elts_cnt) {
					for (var i = 0; i < elts_cnt; i++) {
						if(elts[i].checked){
							arrayChecked.push(elts[i].value);
						}
					}
				} else {
					if(elts.checked){
						arrayChecked.push(elts.value);
					}
				}
				if(arrayChecked.length>0){
					for (var i = 0; i < arrayChecked.length; i++) {
						var elt = document.getElementById('sel_searcher_select_[' + arrayChecked[i] + ']') ;
						set_parent(elt.getAttribute('attr_notice_id'),
									elt.getAttribute('attr_code'),
									elt.getAttribute('attr_titre'),
									elt.getAttribute('attr_auteur1'),
									elt.getAttribute('attr_editeur1'),
									elt.getAttribute('attr_ed_date'),
									elt.getAttribute('attr_collection'),
									elt.getAttribute('attr_prix'),
									'".$callback."');
					}
				}
			}
			-->
		</script>";
		$sh->back_script_show_all = "
		<script type='text/javascript'>
			<!--
			function results_show_all(){
				window.resizeTo(1024,parent.innerHeight);
				document.forms['form_query'].results_show_all.value=1;
				document.forms['form_query'].submit();
			}
			-->
		</script>";

		//extension de la recherche
		//statut
		$q ="select distinct id_notice_statut, gestion_libelle from notice_statut order by 2 " ;
		if (!isset($notice_statut_query) || !$notice_statut_query) {
			$notice_statut_query=$deflt_notice_statut;
		}
		$notice_statut_form = gen_liste($q, 'id_notice_statut', 'gestion_libelle', 'notice_statut_query', '', $notice_statut_query, '', '', '-1', $msg['tous_statuts_notice'] , 0);
		$extended_query=$notice_statut_form;
		//type document
		if (!isset($doctype_query) || !$doctype_query) {
			$doctype_query=$xmlta_doctype;
		}
		$doctype_form = new marc_select('doctype', 'doctype_query', $doctype_query, '',  '-1', $msg['tous_types_docs']);
		$extended_query.=$doctype_form->display;
		$extended_query.="<input type='hidden' name='results_show_all' id='results_show_all' value='0'>";
		
		if ($deb_rech!='') {
			$elt_query=$deb_rech; 
			$sh->etat='first_search';
		}
		$sh->run();
		break;

	case 'article' :
		$sh=new sel_searcher_notice_article($base_url); 
		
		$sh->tab_choice = $tab_choice;
		$sh->elt_b_list = $elt_b_list_article;
		$sh->elt_r_list = $elt_r_list_article;
		$sh->elt_r_list_values = array(0=>'result');
		$sh->action = "<a href='#' onclick=\"set_parent('!!notice_id!!', '!!titre!!', '!!auteur1!!', '!!in_bull!!', '!!prix!!', '".$callback."');\">!!display!!</a> ";
		$sh->action_values = array(0=>'notice_id', 1=>'titre', 3=>'auteur1', 4=>'in_bull', 5=>'prix');
		$sh->back_script = "
		<script type='text/javascript'>
			<!--
			function set_parent(notice_id, titre, auteur1, in_bull, prix, callback) {
			
				var ex=window.parent.act_lineAlreadyExists(0, notice_id, '5');
				var q,cr;
				var v=1;
				if (ex!=false) {
					q=window.parent.document.forms['$caller'].elements['qte['+ex+']'];
					v=q.value;
					r=prompt('".addslashes($msg['acquisition_act_mod_qte'])."', v);
					if (r) {
						q.value=r;
						window.parent.act_calc();
						return false;
					} return false;
				}
				if (window.parent.mod==1) {
					cr='$cr';
				} else {
					cr = window.parent.act_getEmptyLine(); 
				} 
				window.parent.mod=0;
				window.parent.document.forms['$caller'].elements['typ_lig['+cr+']'].value = '5';
				window.parent.document.forms['$caller'].elements['id_prod['+cr+']'].value = notice_id;
				var taec=titre;
				if (auteur1 != '') taec=taec+'\\n'+auteur1;
				if (in_bull != '') taec=taec+'\\n'+in_bull;
				window.parent.document.forms['$caller'].elements['lib['+cr+']'].value = reverse_html_entities(taec);
				window.parent.document.forms['$caller'].elements['prix['+cr+']'].value = reverse_html_entities(prix);
				window.parent.act_calc();
				q=window.parent.document.forms['$caller'].elements['qte['+cr+']'];
				q.value=v;
				q.focus();
				if(callback) {
					window.parent[callback](cr);
				}
			}
			
			function check_uncheck(do_check){
				if(do_check==1){
					document.getElementById('searcher_results_check_all').value='".$msg['searcher_results_uncheck_all']."';
					document.getElementById('searcher_results_check_all').onclick=function(){check_uncheck(0);};
				}else{
					document.getElementById('searcher_results_check_all').value='".$msg['searcher_results_check_all']."';
					document.getElementById('searcher_results_check_all').onclick=function(){check_uncheck(1);};
				}
				var elts = document.forms['searcher_results_check_form'].elements['sel_searcher_select_[]'] ;
				var elts_cnt = (typeof(elts.length) != 'undefined') ? elts.length : 0;
				if (elts_cnt) {
					for (var i = 0; i < elts_cnt; i++) {
						elts[i].checked = do_check;
					}
				} else {
					elts.checked = do_check;
				}
				return true;
			}
			
			function add_selection(){
				var arrayChecked = [];
				var elts = document.forms['searcher_results_check_form'].elements['sel_searcher_select_[]'] ;
				var elts_cnt = (typeof(elts.length) != 'undefined') ? elts.length : 0;
				if (elts_cnt) {
					for (var i = 0; i < elts_cnt; i++) {
						if(elts[i].checked){
							arrayChecked.push(elts[i].value);
						}
					}
				} else {
					if(elts.checked){
						arrayChecked.push(elts.value);
					}
				}
				if(arrayChecked.length>0){
					for (var i = 0; i < arrayChecked.length; i++) {
						var elt = document.getElementById('sel_searcher_select_[' + arrayChecked[i] + ']') ;
						set_parent(elt.getAttribute('attr_notice_id'),
									elt.getAttribute('attr_titre'),
									elt.getAttribute('attr_auteur1'),
									elt.getAttribute('attr_in_bull'),
									elt.getAttribute('attr_prix'),
									'".$callback."');
					}
				}
			}
			-->
		</script>";
		$sh->back_script_show_all = "
		<script type='text/javascript'>
			<!--
			function results_show_all(){
				window.resizeTo(1024,parent.innerHeight);
				document.forms['form_query'].results_show_all.value=1;
				document.forms['form_query'].submit();
			}
			-->
		</script>";

		//extension de la recherche
		//statut
		$q ="select distinct id_notice_statut, gestion_libelle from notice_statut order by 2 " ;
		if (!$notice_statut_query) {
			$notice_statut_query=$deflt_notice_statut;
		}
		$notice_statut_form = gen_liste($q, 'id_notice_statut', 'gestion_libelle', 'notice_statut_query', '', $notice_statut_query, '', '', '-1', $msg['tous_statuts_notice'] , 0);
		$extended_query=$notice_statut_form;
		//type document
		if (!$doctype_query) {
			$doctype_query=$xmlta_doctype;
		}
		$doctype_form = new marc_select('doctype', 'doctype_query', $doctype_query, '',  '-1', $msg['tous_types_docs']);
		$extended_query.=$doctype_form->display;
		$extended_query.="<input type='hidden' name='results_show_all' id='results_show_all' value='0'>";
		
		if ($deb_rech!='') {
			$elt_query=$deb_rech; 
			$sh->etat='first_search';
		}
		$sh->run();
		break;
		
	case 'bulletin' :
		$sh=new sel_searcher_bulletin($base_url); 
		
		$sh->tab_choice = $tab_choice;
		$sh->elt_b_list = $elt_b_list_bulletin;
		$sh->elt_r_list = $elt_r_list_bulletin;
		$sh->elt_r_list_values = array(0=>'result', 1=>'nb_expl');
		$sh->action = "<a href='#' onclick=\"set_parent('!!bulletin_id!!', '!!titre!!',  '!!editeur1!!', '!!numero!!', '!!aff_date!!', '!!prix!!', '!!code!!', '".$callback."');\">!!display!!</a> ";
		$sh->action_values = array(0=>'bulletin_id', 1=>'titre', 2=>'editeur1', 3=>'numero', 4=>'aff_date', 5=>'prix', 6=>'code');
		$sh->back_script = "
		<script type='text/javascript'>
			<!--
			function set_parent(bulletin_id, titre, editeur1, numero, aff_date, prix, code, callback) {

				var ex=window.parent.act_lineAlreadyExists(0, bulletin_id, '2');
				var q,cr;
				var v=1;
				if (ex!=false) {
					q=window.parent.document.forms['$caller'].elements['qte['+ex+']'];
					v=q.value;
					r=prompt('".addslashes($msg['acquisition_act_mod_qte'])."', v);
					if (r) {
						q.value=r;
						window.parent.act_calc();
						return false;
					} return false;
				}
				if (window.parent.mod==1) {
					cr='$cr';
				} else {
					cr = window.parent.act_getEmptyLine(); 
				} 
				window.parent.mod=0;
				window.parent.document.forms['$caller'].elements['typ_lig['+cr+']'].value = '2';
				window.parent.document.forms['$caller'].elements['id_prod['+cr+']'].value = bulletin_id;
				window.parent.document.forms['$caller'].elements['code['+cr+']'].value = reverse_html_entities(code);
				var tnde=titre;
				if (numero!='') tnde=tnde+'.\\n'+numero;
				if (aff_date!='') tnde=tnde+aff_date;
				if (editeur1!='') tnde=tnde+'\\n'+editeur1;
				window.parent.document.forms['$caller'].elements['lib['+cr+']'].value = reverse_html_entities(tnde);
				window.parent.document.forms['$caller'].elements['prix['+cr+']'].value = reverse_html_entities(prix);
				window.parent.act_calc();
				q=window.parent.document.forms['$caller'].elements['qte['+cr+']'];
				q.value=v;
				q.focus();
				if(callback) {
					window.parent[callback](cr);
				}
			}
			
			function check_uncheck(do_check){
				if(do_check==1){
					document.getElementById('searcher_results_check_all').value='".$msg['searcher_results_uncheck_all']."';
					document.getElementById('searcher_results_check_all').onclick=function(){check_uncheck(0);};
				}else{
					document.getElementById('searcher_results_check_all').value='".$msg['searcher_results_check_all']."';
					document.getElementById('searcher_results_check_all').onclick=function(){check_uncheck(1);};
				}
				var elts = document.forms['searcher_results_check_form'].elements['sel_searcher_select_[]'] ;
				var elts_cnt = (typeof(elts.length) != 'undefined') ? elts.length : 0;
				if (elts_cnt) {
					for (var i = 0; i < elts_cnt; i++) {
						elts[i].checked = do_check;
					}
				} else {
					elts.checked = do_check;
				}
				return true;
			}
			
			function add_selection(){
				var arrayChecked = [];
				var elts = document.forms['searcher_results_check_form'].elements['sel_searcher_select_[]'] ;
				var elts_cnt = (typeof(elts.length) != 'undefined') ? elts.length : 0;
				if (elts_cnt) {
					for (var i = 0; i < elts_cnt; i++) {
						if(elts[i].checked){
							arrayChecked.push(elts[i].value);
						}
					}
				} else {
					if(elts.checked){
						arrayChecked.push(elts.value);
					}
				}
				if(arrayChecked.length>0){
					for (var i = 0; i < arrayChecked.length; i++) {
						var elt = document.getElementById('sel_searcher_select_[' + arrayChecked[i] + ']') ;
						set_parent(elt.getAttribute('attr_bulletin_id'),
									elt.getAttribute('attr_titre'),
									elt.getAttribute('attr_editeur1'),
									elt.getAttribute('attr_numero'),
									elt.getAttribute('attr_aff_date'),
									elt.getAttribute('attr_prix'),
									elt.getAttribute('attr_code'),
									'".$callback."');
					}
				}
			}
			-->
		</script>";
		if(!$aut_id){
			$sh->back_script_show_all = "
			<script type='text/javascript'>
				<!--
				function results_show_all(){
					window.resizeTo(1024,parent.innerHeight);
					document.forms['form_query'].results_show_all.value=1;
					document.forms['form_query'].submit();
				}
				-->
			</script>";
		}else{
			$sh->back_script_show_all = "
			<script type='text/javascript'>
				<!--
				function results_show_all(){
					window.resizeTo(1024,parent.innerHeight);
					window.location='!!base_url!!&typ_query=!!cur_typ_query!!&etat=aut_search&aut_type=perio&aut_id=!!aut_id!!&results_show_all=1';
				}
				-->
			</script>";
		}
		
		$sh->aut_b_list=$aut_b_list_bulletin;
		$sh->aut_r_list=$aut_r_list_bulletin;
		$sh->aut_r_list_values = array(0=>'result', 1=>'nb_bull');
		$extended_query="<input type='hidden' name='results_show_all' id='results_show_all' value='0'>";
		if ($deb_rech!='') {
			$elt_query=$deb_rech; 
			$sh->etat='first_search';
		}
		$sh->run();
		break;
		
	case 'frais' :
		$sh=new sel_searcher_frais($base_url); 
		
		$sh->tab_choice = $tab_choice;
		$sh->elt_b_list= $elt_b_list_frais; 
		$sh->elt_r_list= $elt_r_list_frais;
		$sh->elt_r_list_values = array(0=>'result', 1=>'lib_montant');
		$sh->action = "<a href='#' onclick=\"set_parent('!!id_frais!!', '!!libelle!!', '!!montant!!','!!taux_tva!!', '".$callback."');\">!!display!!</a> ";
		$sh->action_values = array(0=>'id_frais', 1=>'libelle', 2=>'montant', 3=>'taux_tva');
		$sh->back_script = "
		<script type='text/javascript'>
			<!--
			function set_parent(id_frais, libelle, montant, taux_tva, callback) {
			
				var ex=window.parent.act_lineAlreadyExists(0, id_frais, '3');
				var q,cr;
				var v=1;
				if (ex!=false) {
					q=window.parent.document.forms['$caller'].elements['qte['+ex+']'];
					v=q.value;
					r=prompt('".addslashes($msg['acquisition_act_mod_qte'])."', v);
					if (r) {
						q.value=r;
						window.parent.act_calc();
						return false;
					} return false;
				}
				if (window.parent.mod==1) {
					cr='$cr';
				} else {
					cr = window.parent.act_getEmptyLine(); 
				} 
				window.parent.mod=0;
				window.parent.document.forms['$caller'].elements['typ_lig['+cr+']'].value = '3';
				window.parent.document.forms['$caller'].elements['id_prod['+cr+']'].value = id_frais;
				window.parent.document.forms['$caller'].elements['code['+cr+']'].value = '';
				window.parent.document.forms['$caller'].elements['lib['+cr+']'].value = reverse_html_entities(libelle);
				window.parent.document.forms['$caller'].elements['prix['+cr+']'].value = reverse_html_entities(montant);
				try {
					window.parent.document.forms[f_caller].elements['tva['+cr+']'].value = reverse_html_entities(taux_tva);
				} catch(err){}
				window.parent.act_calc();
				q=window.parent.document.forms['$caller'].elements['qte['+cr+']'];
				q.value=v;
				q.focus();
				if(callback) {
					window.parent[callback](cr);
				}
			}
			
			function check_uncheck(do_check){
				if(do_check==1){
					document.getElementById('searcher_results_check_all').value='".$msg['searcher_results_uncheck_all']."';
					document.getElementById('searcher_results_check_all').onclick=function(){check_uncheck(0);};
				}else{
					document.getElementById('searcher_results_check_all').value='".$msg['searcher_results_check_all']."';
					document.getElementById('searcher_results_check_all').onclick=function(){check_uncheck(1);};
				}
				var elts = document.forms['searcher_results_check_form'].elements['sel_searcher_select_[]'] ;
				var elts_cnt = (typeof(elts.length) != 'undefined') ? elts.length : 0;
				if (elts_cnt) {
					for (var i = 0; i < elts_cnt; i++) {
						elts[i].checked = do_check;
					}
				} else {
					elts.checked = do_check;
				}
				return true;
			}
			
			function add_selection(){
				var arrayChecked = [];
				var elts = document.forms['searcher_results_check_form'].elements['sel_searcher_select_[]'] ;
				var elts_cnt = (typeof(elts.length) != 'undefined') ? elts.length : 0;
				if (elts_cnt) {
					for (var i = 0; i < elts_cnt; i++) {
						if(elts[i].checked){
							arrayChecked.push(elts[i].value);
						}
					}
				} else {
					if(elts.checked){
						arrayChecked.push(elts.value);
					}
				}
				if(arrayChecked.length>0){
					for (var i = 0; i < arrayChecked.length; i++) {
						var elt = document.getElementById('sel_searcher_select_[' + arrayChecked[i] + ']') ;
						set_parent(elt.getAttribute('attr_id_frais'),
									elt.getAttribute('attr_libelle'),
									elt.getAttribute('attr_montant'),
									elt.getAttribute('attr_taux_tva'),
									'".$callback."');
					}
				}
			}
			-->
		</script>";
		$sh->back_script_show_all = "
		<script type='text/javascript'>
			<!--
			function results_show_all(){
				window.resizeTo(1024,parent.innerHeight);
				document.forms['form_query'].results_show_all.value=1;
				document.forms['form_query'].submit();
			}
			-->
		</script>";
		
		if (isset($elt_query) && $elt_query == '') {
			$elt_query = '*'; 
			$sh->etat = 'first_search';
		} 
		$extended_query="<input type='hidden' name='results_show_all' id='results_show_all' value='0'>";
		$sh->run();
		break;		

	case 'abt' :
		if($autorun==1){
			$deb_rech="*";
		}
		$sh=new sel_searcher_abt($base_url); 
		
		$sh->tab_choice = $tab_choice;
		$sh->elt_b_list = $elt_b_list_abt;
		$sh->elt_r_list = $elt_r_list_abt;
		$sh->elt_r_list_values = array(0=>'result', 1=>'aff_date_echeance', 2=>'aff_nb_recipients');
		$sh->action = "<a href='#' onclick=\"set_parent('!!abt_id!!', '!!code!!', '!!titre!!',  '!!editeur1!!', '!!periodicite!!', '!!duree!!', '!!aff_date_debut!!', '!!prix!!', '!!abt_name!!', '".$callback."');\">!!display!!</a> ";
		$sh->action_values = array(0=>'abt_id', 1=>'code', 2=>'titre', 3=>'editeur1', 4=>'periodicite', 5=>'duree', 6=>'aff_date_debut', 7=>'prix', 8=>'abt_name');
		$sh->back_script = "
		<script type='text/javascript'>
			<!--
			function set_parent(abt_id, code, titre, editeur1, periodicite, duree, aff_date_debut, prix, abt_name, callback) {

				var ex=window.parent.act_lineAlreadyExists(0, abt_id, '4');
				var q,cr;
				var v=1;
				if (ex!=false) {
					q=window.parent.document.forms['$caller'].elements['qte['+ex+']'];
					v=q.value;
					r=prompt('".addslashes($msg['acquisition_act_mod_qte'])."', v);
					if (r) {
						q.value=r;
						window.parent.act_calc();
						return false;
					} return false;
				}
				if (window.parent.mod==1) {
					cr='$cr';
				} else {
					cr = window.parent.act_getEmptyLine(); 
				} 
				window.parent.mod=0;
				window.parent.document.forms['$caller'].elements['typ_lig['+cr+']'].value = '4';
				window.parent.document.forms['$caller'].elements['id_prod['+cr+']'].value = abt_id;
				window.parent.document.forms['$caller'].elements['code['+cr+']'].value = reverse_html_entities(code);
				var tabt='".addslashes($msg['pointage_label_abonnement'])."'; 
				tabt=tabt+' '+duree+' ".addslashes($msg['abonnements_periodicite_unite_mois'])."';
				tabt=tabt+'\\n".addslashes($msg['abonnements_date_debut'])." : '+aff_date_debut;
				tabt=tabt+'\\n'+titre;
				if (editeur1!='') tabt=tabt+'\\n'+editeur1;";
		if($acquisition_show_abt_in_cmde){
			$sh->back_script.="tabt=tabt+'\\n'+abt_name;";
		}
		$sh->back_script.="
				window.parent.document.forms['$caller'].elements['lib['+cr+']'].value = reverse_html_entities(tabt);
				window.parent.document.forms['$caller'].elements['prix['+cr+']'].value = reverse_html_entities(prix);
				window.parent.act_calc();
				q=window.parent.document.forms['$caller'].elements['qte['+cr+']'];
				q.value=v;
				q.focus();
				if(callback) {
					window.parent[callback](cr);
				}
			}
			
			function check_uncheck(do_check){
				if(do_check==1){
					document.getElementById('searcher_results_check_all').value='".$msg['searcher_results_uncheck_all']."';
					document.getElementById('searcher_results_check_all').onclick=function(){check_uncheck(0);};
				}else{
					document.getElementById('searcher_results_check_all').value='".$msg['searcher_results_check_all']."';
					document.getElementById('searcher_results_check_all').onclick=function(){check_uncheck(1);};
				}
				var elts = document.forms['searcher_results_check_form'].elements['sel_searcher_select_[]'] ;
				var elts_cnt = (typeof(elts.length) != 'undefined') ? elts.length : 0;
				if (elts_cnt) {
					for (var i = 0; i < elts_cnt; i++) {
						elts[i].checked = do_check;
					}
				} else {
					elts.checked = do_check;
				}
				return true;
			}
			
			function add_selection(){
				var arrayChecked = [];
				var elts = document.forms['searcher_results_check_form'].elements['sel_searcher_select_[]'] ;
				var elts_cnt = (typeof(elts.length) != 'undefined') ? elts.length : 0;
				if (elts_cnt) {
					for (var i = 0; i < elts_cnt; i++) {
						if(elts[i].checked){
							arrayChecked.push(elts[i].value);
						}
					}
				} else {
					if(elts.checked){
						arrayChecked.push(elts.value);
					}
				}
				if(arrayChecked.length>0){
					for (var i = 0; i < arrayChecked.length; i++) {
						var elt = document.getElementById('sel_searcher_select_[' + arrayChecked[i] + ']') ;
						set_parent(elt.getAttribute('attr_abt_id'),
									elt.getAttribute('attr_code'),
									elt.getAttribute('attr_titre'),
									elt.getAttribute('attr_editeur1'),
									elt.getAttribute('attr_ed_periodicite'),
									elt.getAttribute('attr_duree'),
									elt.getAttribute('attr_aff_date_debut'),
									elt.getAttribute('attr_prix'),
									elt.getAttribute('attr_abt_name'),
									'".$callback."');
					}
				}
			}
			-->
		</script>";
		$sh->back_script_show_all = "
		<script type='text/javascript'>
			<!--
			function results_show_all(){
				window.resizeTo(1024,parent.innerHeight);
				document.forms['form_query'].results_show_all.value=1;
				document.forms['form_query'].submit();
			}
			-->
		</script>";
		$sh->back_script_order = "
		<script type='text/javascript'>
			<!--
			function sel_searcher_before_click_submit(){
				document.getElementById('specific_order').value = 0;
			}
			function specific_order(choix){
				document.getElementById('specific_order').value = choix;
				document.forms['form_query'].submit();
			}
			-->
		</script>";
		//extension de la recherche
		//localisation
		$q ="select distinct idlocation, location_libelle from docs_location, docsloc_section where num_location=idlocation order by 2 " ;
		if (!isset($location_query) || !$location_query) {
			$location_query=$deflt_bulletinage_location;
		}
		$location_form = gen_liste($q, "idlocation", "location_libelle", 'location_query', "", $location_query, "", "", '-1', $msg['all_location'] , 0);
		$extended_query=$location_form;
		//echeance
		if (isset($date_ech_query) && $date_ech_query=='-1') {
			$date_ech_query_lib=$msg['parperso_nodate'];
		} elseif (empty($date_ech_query)) {
			$q = "select date_add(curdate(), interval ".$pmb_abt_end_delay." day) ";
			$r = pmb_mysql_query($q, $dbh);
			$date_ech_query=pmb_mysql_result($r, 0, 0);
			$date_ech_query_lib=format_date($date_ech_query);
		} else {
			$date_ech_query_lib=format_date($date_ech_query);
		}
		
		$date_ech_form =htmlentities($msg['acquisition_abt_ech'], ENT_QUOTES, $charset)."&nbsp;&lt;<input type='hidden' id='date_ech_query' name='date_ech_query' value='".$date_ech_query."' />
			<input type='button' id='date_ech_query_lib' class='bouton_small' value='".$date_ech_query_lib."' onclick=\"var date_c='';if (this.form.elements['date_ech_query'].value!='-1') date_c=this.form.elements['date_ech_query'].value; openPopUp('./select.php?what=calendrier&caller='+this.form.name+'&date_caller='+date_c+'&param1=date_ech_query&param2=date_ech_query_lib&auto_submit=NO&date_anterieure=YES', 'calendar')\" />
			<input type='button' class='bouton_small' style='width:25px;' value='".$msg['raz']."' onclick=\"this.form.elements['date_ech_query_lib'].value='".$msg['parperso_nodate']."'; this.form.elements['date_ech_query'].value='-1';\" />";
		$extended_query.=$date_ech_form;
		$extended_query.="<input type='hidden' name='results_show_all' id='results_show_all' value='0'>";
		if (empty($specific_order)) {
			$specific_order = 0;
		}
		$extended_query.="<input type='hidden' name='specific_order' id='specific_order' value='".$specific_order."'/>";
		
		if ($deb_rech!='') {
			$elt_query=$deb_rech; 
			$sh->etat='first_search';
		}

		$sh->run();
		break;

	case 'panier' :
		break;
		
	default	:
		print 'No query type defined<br />';
		break;
}

?>