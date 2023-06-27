<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: liste_lecture.tpl.php,v 1.46 2019-05-29 11:23:32 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $sub;
global $liste_lecture_prive;
global $liste_lecture_public;
global $liste_gestion;
global $liste_demande;
global $liste_lecture_gestion_boutons;
global $opac_show_suggest;
global $opac_allow_multiple_sugg;
global $liste_lecture_consultation;
global $opac_avis_allow;
global $allow_avis;
global $msg;

$liste_lecture_prive = "
<script>
	function confirm_delete() {
		result = confirm(pmbDojo.messages.getMessage('opac', 'list_lecture_confirm_suppr'));
		if(result) {
			return true;
		} else {
			return false;
		}
	}
	function liste_lecture_restrict(elem) {
		var url = './empr.php?tab=lecture&lvl=private_list';
		if(elem.checked) {
			url += '&sub=my_list';
		}
		document.location = url;
	}
</script>
<h3><span>".$msg['list_lecture_private']."</span></h3>
<form class='form_liste_lecture' name='liste_lecture_search' method='post' action='./index.php?lvl=more_results&mode=extended&search_type_asked=extended_search' >	
	<div class='form-contenu'>
		<div id='aut_see' class='row'>
			<div class='row'>
				".$msg['list_lecture_search_all_list']."
				<br /><input class='text_query' type='text' size='65' name='field_1_f_42[]' id='user_query' value=''>
				".($opac_avis_allow && $allow_avis ? "<input id='avis_search' type='checkbox' value='1' name='avis_search' > <label for='avis_search'>".$msg['list_lecture_avis_search']."</label>" : "")."
			</div>
			<div class='row'>
				<input name='search[]' value='s_7' type='hidden'>
				<input name='op_0_s_7' value='EQ' type='hidden'>
				<input name='field_0_s_7[]' value='0' type='hidden'>
				<input name='search[]' value='f_42' type='hidden'>		
				<input name='inter_1_f_42' value='and' type='hidden'>
				<input name='op_1_f_42' value='BOOLEAN' type='hidden'>
				<div id='avis_search_part' >
					<input name='search[]' value='s_8' type='hidden'>	
					<input name='inter_2_s_8' value='or' type='hidden'>
					<input name='op_2_s_8' value='EQ' type='hidden'>
					<input name='field_2_s_8[]' id='avis_search_query' value='' type='hidden'>		
				</div>
				<input name='explicit_search' value='1' type='hidden'>		
				<input name='launch_search' value='1' type='hidden'>
				<input class='boutonrechercher'  type='submit' value='".$msg[10]."' 
					onclick=\"if(document.getElementById('user_query').value=='')document.getElementById('user_query').value='*'; document.getElementById('avis_search_query').value=document.getElementById('user_query').value; if(document.getElementById('avis_search').checked==false) document.getElementById('avis_search_part').innerHTML=''; \">	
				<input type='hidden' name='list_type' value='".$sub."' />
			</div>			
		</div>	
	</div>		
</form>	
<script type='text/javascript'>document.getElementById('user_query').focus();</script>
<div class='row'>
	<input id='my_list' type='checkbox' value='1' !!my_list_checked!! name='my_list' onclick=\"liste_lecture_restrict(this);\" > <label for='my_list'>".$msg['list_lecture_created_by_me']."</label>
</div>
<div class='row' id='div_mylist'>
	!!listes!!
</div>
";

$liste_lecture_public = "
<script type='text/javascript' src='./includes/javascript/liste_lecture.js'></script>
<script type='text/javascript'>
	function demandeEnCours(){
		alert(pmbDojo.messages.getMessage('opac', 'list_lecture_already_requested'));
	}
</script>
<h3><span>".$msg['list_lecture_public']."</span></h3>
<form  name='liste_lecture_public' method='post' action='./empr.php' >	
<input type='hidden' id='lvl' name='lvl' />
<input type='hidden' id='sub' name='sub' />
<input type='hidden' id='act' name='act' />
<input type='hidden' id='page' name='page' value='' />
	<div id='public_list'>
		<div id='list_cadre' style='border: 1px solid rgb(204, 204, 204); overflow: auto; height: 250px;padding:2px;'>
			!!public_list!!
		</div>
	</div>
	<br />
</form>
";


$liste_gestion = "
<script type='text/javascript' src='./includes/javascript/ajax.js'></script>
<script src='./includes/javascript/liste_lecture.js' type='text/javascript'></script>
<script src='./includes/javascript/http_request.js' type='text/javascript'></script>
<script type='text/javascript'>
	function delete_from_liste(id_liste,idempr){
		
		var conf = confirm(\"".$msg['list_lecture_delete_subscriber']."\");
		if(conf){
			var action = new http_request();
			var url = './ajax.php?module=ajax&categ=liste_lecture&id='+id_liste+'&id_empr_to_deleted='+idempr+'&quoifaire=delete_empr';
			action.request(url);
			if(action.get_status() == 0){
				document.getElementById('inscrit_list').innerHTML = action.get_text();
			}
			
		} else return false;	
	}
	
	function confirm_delete_noti(){
		var is_check=false;
		var elts = document.getElementsByName('notice[]') ;
		if (!elts) is_check = false ;
		var elts_cnt  = (typeof(elts.length) != 'undefined')
                  ? elts.length
                  : 0;
		if (elts_cnt) {
			for (var i = 0; i < elts_cnt; i++) { 		
				if (elts[i].checked) {
					res = confirm(pmbDojo.messages.getMessage('opac', 'list_lecture_confirm_delete'));
					if(res) 
						return true;
					else 
						return false;
				}
			}
		} 
		if(!is_check){
			alert(pmbDojo.messages.getMessage('opac', 'list_lecture_no_ck'));
			return false;
		}
        
		return is_check;
	}
	
	function test_form(form){
		if(form.list_name.value.length == 0){
			alert(pmbDojo.messages.getMessage('opac', 'list_lecture_name_dont_filled'));
			return false;
		}  else {
			var action = new http_request();
			var url = './ajax.php?module=ajax&categ=liste_lecture&quoifaire=unicite_nom_liste';
			action.request(url, true, 'id_liste='+document.getElementById('id_liste').value+'&nom_liste='+document.getElementById('list_name').value);
			if(action.get_status() == 0){
				if(action.get_text()!='0') {
					alert(pmbDojo.messages.getMessage('opac', 'list_lecture_name_exists'));
					return false;
				}
			}				
		}
		return true;
	}
	
	function confirm_delete() {
		result = confirm(pmbDojo.messages.getMessage('opac', 'list_lecture_confirm_suppr'));
		if(result) {
			return true;
		} else
			return false;
	}
   			
	function activerConfidentiel() {
		if(document.getElementById('cb_share').checked){
			document.getElementById('cb_confidential').disabled=false;
			document.getElementById('lab_conf').style.color = \"black\";
		} else {
			document.getElementById('cb_confidential').disabled=true;
			document.getElementById('lab_conf').style.color = \"gray\";
		}
	}
</script>
<form class='form_liste_lecture' name='liste_lecture' method='post' action='index.php?lvl=show_list&sub=view&id_liste=!!id_liste!!'>
	<input type='hidden' id='act' name='act' />
	<input type='hidden' id='notice_filtre' name='notice_filtre' value='!!notice_filtre!!' />
	<input type='hidden' id='id_liste' name='id_liste' value='!!id_liste!!' />
	<div class='row'>
		<input type='button' class='bouton' name='cancel' onclick='document.location=\"./empr.php?tab=lecture&lvl=private_list\";' value='".$msg['list_lecture_back']."' />					
		!!print_btn!!
	</div>
	<br />
	<div class='row'>
		!!liste_lecture_gestion_boutons!!	
	</div>
	<div class='row'>			
	</div>	
	<div class='form-contenu'>
	    <div class='reading_list_container'>
            <h3><span>!!titre_liste!!</span></h3>
        	<div class='row'>
    			<div class='colonne2'>
    				<div class='row'>
    					<label class='etiquette'>".$msg['list_lecture_name']."</label>
    				</div>
    				<div class='row'>
    					<input type='text' class='saisie-20em' id='list_name' name='list_name' value='!!name_list!!' />
    				</div>
    				<div class='row'>
    					<label class='etiquette'>".$msg['list_lecture_comment']."</label>
    				</div>
    				<div class='row'>
    					<textarea name='list_comment' rows='2' cols='50'>!!list_comment!!</textarea>
    				</div>
    				<div class='row reading_list_share_and_confidential'>
    					<input type='checkbox' id='cb_share' name='cb_share' !!checked!! onclick=\"activerConfidentiel()\" /><label for='cb_share'>".$msg['list_lecture_share_with_users']."</label>
    					( <input type='checkbox' id='cb_confidential' name='cb_confidential' !!disabled_conf!! !!checked_conf!! /><label id='lab_conf' style=\"color:!!color_conf!!\" for='cb_confidential'>".$msg['list_lecture_confidential']."</label> ) 
    				</div>
    				<div class='row reading_list_readonly'>
    					<input type='checkbox' id='cb_readonly' name='cb_readonly' !!checked_only!!  /><label for='cb_readonly'>".$msg['list_lecture_readonly']."</label> 
    				</div>
    				<div class='reading_list_tag'>
        				<div class='row'>
        					<label class='etiquette'>".$msg['list_lecture_tag']."</label>
        				</div>
        				<div class='row'>
        					<select data-dojo-type='dijit/form/ComboBox' id='list_tag' name='list_tag'>
        						!!list_tag!!
        					</select>
        				</div>
        			</div>
    			</div>
    			<div class='colonne2'>
    				!!add_empr!!
    				!!inscrit_list!!							
    			</div>
				<div class='row'>
					<input type='submit' class='bouton' name='save_list' onclick='this.form.act.value=\"save\";this.form.action=\"empr.php?tab=lecture&lvl=private_list\";return test_form(this.form);' value='".$msg['list_lecture_save']."' />
				</div>	
    			<div class='row'></div>					
    		</div>
        </div>
		<hr />
        !!search!!
		!!liste_notice!!			
	</div>
</form>
<script type='text/javascript'>ajax_parse_dom();</script>
";

$liste_lecture_gestion_boutons = "<div class='left'>
			<input type='submit' class='bouton' name='list_in' onclick='this.form.act.value=\"list_in\";' value='".$msg['list_lecture_list_in']."' />
			<input type='submit' class='bouton' name='list_out' onclick='this.form.act.value=\"list_out\";this.form.target=\"cart_info\";this.form.action=\"cart_info.php?lvl=listlecture&id=!!id_liste!!\";' value='".$msg['list_lecture_list_out']."' />";
if($opac_show_suggest && $opac_allow_multiple_sugg)
	 $liste_lecture_gestion_boutons .= "	<input type='submit' class='bouton' name='multi_sugg' onclick='this.form.act.value=\"transform_list\";this.form.action=\"empr.php?lvl=make_multi_sugg\";' value='".$msg['transform_list_to_multisugg']."' />";						
$liste_lecture_gestion_boutons .= "</div>
		<div class='right'>
			<input type='submit' class='bouton' name='suppr_checked' onclick='this.form.act.value=\"suppr_ck\";return confirm_delete_noti(); return false;' value='".$msg['list_lecture_suppr_checked']."' />
			<input type='submit' class='bouton' name='suppr' onclick='this.form.act.value=\"suppr\";this.form.action=\"empr.php?tab=lecture&lvl=private_list\";return confirm_delete();' value='".$msg['list_lecture_suppression']."' />
		</div>";

$liste_lecture_consultation="
<form class='form_liste_lecture' name='liste_lecture' method='post' action='./index.php?lvl=show_list&sub=consultation&id_liste=!!id_liste!!' >	
	<input type='hidden' id='act' name='act' />
	<input type='hidden' id='notice_filtre' name='notice_filtre' value='!!notice_filtre!!' />
	<input type='hidden' id='id_liste' name='id_liste' value='!!id_liste!!' />
	<div class='row'>
		<input type='button' class='bouton' name='cancel' onclick='document.location=\"./empr.php?tab=lecture&lvl=private_list\";' value='".$msg['list_lecture_back']."' />
		!!print_btn!!
		!!abo_btn!!
		<input type='submit' class='bouton' name='list_out' onclick='this.form.act.value=\"list_out\";this.form.target=\"cart_info\";this.form.action=\"cart_info.php?lvl=listlecture&sub=consult&id=!!id_liste!!\";' value='".$msg['list_lecture_list_out']."' />
		!!add_noti_btn!!
	</div>
	<div class='row'>			  
	</div>
</form>
<h3><span> !!nom_liste!! !!proprio!!</span></h3>
<form class='form_liste_lecture' name='liste_lecture_search' method='post' action='./index.php?lvl=show_list&sub=consultation&id_liste=!!id_liste!!' >	
	<div class='form-contenu'>
		<div id='aut_see' class='row'>
			<div class='row'>
				<label><strong>!!liste_comment!!</strong></label>
			</div>
		</div>
		!!search!!
		!!liste_notice!!
	</div>		
</form>	
<script type='text/javascript'>document.getElementById('user_query').focus();</script>				
";

$liste_demande = "
<h3><span>$msg[list_lecture_demande]</span></h3>
<form  name='liste_lecture_demande' method='post' action='./empr.php' >	
<input type='hidden' id='lvl' name='lvl' />
<input type='hidden' id='sub' name='sub' />
<input type='hidden' id='action' name='act' />
	<div id='demande_list'>
		<div id='list_cadre' style='border: 1px solid rgb(204, 204, 204); overflow: auto; height: 200px;padding:2px;'>
			!!demande_list!!
		</div>
	</div>
	<br />
	<div id='refus_dmde' style='diplay:none'>
	</div>
	<br />
	<div class='row'>
		!!accepter_btn!!
		!!refuser_btn!!
	</div>
</form>
";
?>