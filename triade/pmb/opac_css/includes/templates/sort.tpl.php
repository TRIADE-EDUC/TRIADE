<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sort.tpl.php,v 1.21 2024/04/12 09:23:50 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $show_tris_form;
global $liste_criteres_tri;
global $show_sel_form;
global $msg;
global $opac_rgaa_active;
  		
$show_tris_form ="<script>
		var sort_all_checked = false;
		
		function check_uncheck_all_sort() {
			if (sort_all_checked) {
				setCheckboxes('cases_a_cocher', 'cases_suppr', false);
				sort_all_checked = false;
				document.getElementById('show_sort_checked_all').value = pmbDojo.messages.getMessage('sort', 'show_sort_check_all');
				document.getElementById('show_sort_checked_all').title = pmbDojo.messages.getMessage('sort', 'show_sort_check_all');
			} else {
				setCheckboxes('cases_a_cocher', 'cases_suppr', true);
				sort_all_checked = true;
				document.getElementById('show_sort_checked_all').value = pmbDojo.messages.getMessage('sort', 'show_sort_uncheck_all');
				document.getElementById('show_sort_checked_all').title = pmbDojo.messages.getMessage('sort', 'show_sort_uncheck_all');
			}
			return false;
		}
		
		function setCheckboxes(the_form, the_objet, do_check) {
			 var elts = document.forms[the_form].elements[the_objet+'[]'] ;
            if (typeof(elts) == 'undefined') {
                elts = document.querySelectorAll('.opac_sorts_ui_selection');
            }
			 var elts_cnt = (typeof(elts.length) != 'undefined') ? elts.length : 0;
			 if (elts_cnt) {
				for (var i = 0; i < elts_cnt; i++) {
			 		elts[i].checked = do_check;
			 	} // end for
			 } else {
			 	elts.checked = do_check;
			 } 
			 return true;
		}
	
		function verifCheckboxes(the_form, the_objet) {
			var bool=false;
			var elts = document.forms[the_form].elements[the_objet+'[]'] ;
            if (typeof(elts) == 'undefined') {
                elts = document.querySelectorAll('.opac_sorts_ui_selection');
            }
			var elts_cnt  = (typeof(elts.length) != 'undefined')
	                  ? elts.length
	                  : 0;
	
			if (elts_cnt) {
					
				for (var i = 0; i < elts_cnt; i++) { 		
					if (elts[i].checked)
					{
						bool = true;
					}
				}
			} else {
					if (elts.checked)
					{
						bool = true;
					}
			}
			return bool;
		}
		
		function sortSupprIds(the_form, the_objet) {
			var ids= '';
			var elts = document.forms[the_form].elements[the_objet+'[]'] ;
            if (typeof(elts) == 'undefined') {
                elts = document.querySelectorAll('.opac_sorts_ui_selection');
            }
			var elts_cnt  = (typeof(elts.length) != 'undefined') ? elts.length : 0;
	
			if (elts_cnt) {
				for (var i = 0; i < elts_cnt; i++) { 		
					if (elts[i].checked) {
						if (ids == '') ids += elts[i].value;
						else ids += ','+elts[i].value;
					}
				}
			} else {
					if (elts.checked) {
						if (ids == '') ids += elts.value;
						else ids += ','+elts.value;
					}
			}
			return ids;
		}
	</script>
    <!--bouton close-->
";

if($opac_rgaa_active){
	$show_tris_form .= "
	<div id='tris'>
		!!title!!
		<div id='sort_check_option' >
			<input type='button' class='bouton' id='show_sort_checked_all' value=\"".$msg["show_sort_check_all"]."\" onClick=\"check_uncheck_all_sort();\" />&nbsp;
			<input type='button' class='bouton' id='show_remove_checked_element' value=\"".$msg["suppr_elts_sort_coch"]."\" onClick=\"if (verifCheckboxes('cases_a_cocher','cases_suppr')){ !!action_suppr_tris!! return false;}\" />
			<input type='button' class='bouton' id='show_sort_unchecked_all' value='".$msg['tri_inactif']."' aria-label='".$msg['aria_label_sort_uncheck_all']."' title='".$msg['tri_inactif']."' onClick='parent.location=\"./index.php?!!page_en_cours1!!&get_last_query=".(isset($_SESSION["last_query"]) ? $_SESSION["last_query"] : '')."&sort=\";return false;'></td>
		</div>
		<div id='sort_form'>
			<form name='cases_a_cocher' method='post' action='./index.php?lvl=sort&raz_sort=1&page_en_cours=!!page_en_cours!!'>
				!!liste_tris!!
			</form>
		</div>
	</div>
	";
}else{
	$show_tris_form .= "
	<div id='tris'>
		<br />
		!!title!!
		<table style='width:100%' role='presentation'>
			<tr>
				<td>
					<input type='button' class='bouton' id='show_sort_checked_all' value=\"".$msg["show_sort_check_all"]."\" onClick=\"check_uncheck_all_sort();\" />&nbsp;
					<input type='button' class='bouton' value=\"".$msg["suppr_elts_sort_coch"]."\" onClick=\"if (verifCheckboxes('cases_a_cocher','cases_suppr')){ !!action_suppr_tris!! return false;}\" />
				</td>
			</tr>
			<tr>
				<td style='vertical-align:top'>
					<form name='cases_a_cocher' method='post' action='./index.php?lvl=sort&raz_sort=1&page_en_cours=!!page_en_cours!!'>
					!!liste_tris!!
					</form>
				</td>
			</tr>
			<tr>
				<td><input type='button' class='bouton' value='".$msg['tri_inactif']."' alt='' aria-label='".$msg['aria_label_sort_uncheck_all']."' title='".$msg['tri_inactif']."' onClick='parent.location=\"./index.php?!!page_en_cours1!!&get_last_query=".(isset($_SESSION["last_query"]) ? $_SESSION["last_query"] : '')."&sort=\";return false;'></td>
			</tr>
		</table>
	</div>
	";
}


if($opac_rgaa_active){
	$liste_criteres_tri ="
		<div class='d-flex flex-wrap sort_row'>
			<div class='sort_col flex-1'>
				<span><label for='liste_critere!!idLigne!!'>".$msg['tri_par']."</label></span>

				<select id='liste_critere!!idLigne!!' name='liste_critere!!idLigne!!' onchange='set_critere_type(this, !!idLigne!!);'>
					<option value='' selected>".$msg['sort_by_placeholder']."</option>
					!!liste_criteres!!
				</select>
			</div>
			<div class='sort_col flex-1'>
				<span><label for='croit_decroit!!idLigne!!'>".$msg['sort_order']."</label></span>

				<select id='croit_decroit!!idLigne!!' name='croit_decroit!!idLigne!!'>
					<option value='' selected>".$msg['sort_order_placeholder']."</option>
					<option value='c'>".$msg['tri_croissant']."</option>
					<option value='d'>".$msg['tri_decroissant']."</option>
				</select>
			</div>
			<div class='sort_col flex-1'>
				<span><label for='num_text!!idLigne!!'>".$msg['sort_string']."</label></span>

				<select id='num_text!!idLigne!!' name='num_text!!idLigne!!'>
					<option value='' selected>".$msg['sort_string_placeholder']."</option>
					<option value='text'>".$msg['tri_alpha']."</option>
					<option value='num'>".$msg['tri_num']."</option>
				</select>
			</div>
		</div>
	";
}else{
	$liste_criteres_tri ="
		<tr>
			<td>
				<span>".$msg['puis_par_tri']."</span>
			</td>
			<td>
				<select name='liste_critere!!idLigne!!' onchange='set_critere_type(this, !!idLigne!!);'>
					<option value='' selected>&nbsp;</option>!!liste_criteres!!
				</select>
			</td>
			<td>
				<select name='croit_decroit!!idLigne!!'>
					<option value='c'>".$msg['tri_croissant']."</option>
					<option value='d'>".$msg['tri_decroissant']."</option>
				</select>
			</td>
			<td>
				<select name='num_text!!idLigne!!'>
					<option value='text'>".$msg['tri_alpha']."</option>
					<option value='num'>".$msg['tri_num']."</option>
				</select>
			</td>
		</tr>
	";
}

$show_sel_form ="
	<script>
		function set_critere_type(obj, indice) {
			var type = obj.options[obj.options.selectedIndex].getAttribute('data-type');
			var type_selector = document.getElementsByName('num_text'+indice)[0];
			if(type && type_selector) {
				type_selector.value = type;
			}
		}
	</script>";

if($opac_rgaa_active){
	$show_sel_form .= "
	<div id='creer_tri'>
		<h3><span>".$msg['definir_tri']."</span></h3>
		<form name='creer_sort_form' method='post' action='./index.php?lvl=sort&modif_sort=1&page_en_cours=!!page_en_cours!!'>
			<div class='d-flex flex-wrap sort_row'>
				<div class='sort_col flex-1'>
					<span><label for='liste_critere0'>".$msg['tri_par']."</label></span>

					<select id='liste_critere0' name='liste_critere0' onchange='set_critere_type(this, 0);'>
						<option value='' selected>".$msg['sort_by_placeholder']."</option>
						!!liste_criteres!!
					</select>
				</div>
				<div class='sort_col flex-1'>
					<span><label for='croit_decroit0'>".$msg['sort_order']."</label></span>

					<select id='croit_decroit0' name='croit_decroit0'>
						<option value='' selected>".$msg['sort_order_placeholder']."</option>
						<option value='c'>".$msg['tri_croissant']."</option>
						<option value='d'>".$msg['tri_decroissant']."</option>
					</select>
				</div>
				<div class='sort_col flex-1'>
					<span><label for='num_text0'>".$msg['sort_string']."</label></span>
					<select id='num_text0' name='num_text0'>
						<option value='' selected>".$msg['sort_string_placeholder']."</option>
						<option value='text'>".$msg['tri_alpha']."</option>
						<option value='num'>".$msg['tri_num']."</option>
					</select>
				</div>
			</div>
			!!liste_criteres_tri!!
			<div class='w-100 sort_submit_row'>
				<input type='submit' class='bouton' value='".$msg['appliq_enreg_tri']."' alt='' title='".$msg['appliq_enreg_tri']."'>
			</div>
		</form>	
	</div>
	";
}else{
	$show_sel_form .= "
		<div id='creer_tri'>
			<h3><span>".$msg['definir_tri']."</span></h3>
			<form name='creer_sort_form' method='post' action='./index.php?lvl=sort&modif_sort=1&page_en_cours=!!page_en_cours!!'>
				<table role='presentation'>
					<tr>
						<td>
							<span>".$msg['tri_par']."</span>
						</td>
						<td>
							<select name='liste_critere0' onchange='set_critere_type(this, 0);'>
								<option value='' selected>&nbsp;</option>!!liste_criteres!!
							</select>
						</td>
						<td>
							<select name='croit_decroit0'>
								<option value='c'>".$msg['tri_croissant']."</option>
								<option value='d'>".$msg['tri_decroissant']."</option>
							</select>
						</td>
						<td>
							<select name='num_text0'>
								<option value='text'>".$msg['tri_alpha']."</option>
								<option value='num'>".$msg['tri_num']."</option>
							</select>
						</td>
					</tr>
						!!liste_criteres_tri!!
					<tr>
						<td colspan='4'>
							<input type='submit' class='bouton' value='".$msg['appliq_enreg_tri']."' alt='".$msg['appliq_enreg_tri']."' title='".$msg['appliq_enreg_tri']."'>
						</td>
					</tr>
				</table>
			</form>	
		</div>
	";
}