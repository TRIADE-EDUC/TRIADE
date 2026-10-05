<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc.tpl.php,v 1.32.4.1 2025/05/06 09:11:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $biblio_name, $msg, $charset, $current_module, $serialcirc_circ_pdf_diffusion;
global $serialcirc_circ_form, $serialcirc_pointage_form;
global $serialcirc_circ_cb_notfound, $serialcirc_circ_cb_info;

if(!isset($biblio_name)) $biblio_name = '';

$serialcirc_circ_form = "
	<script type='text/javascript' src='./javascript/serialcirc.js'></script>
	<script type='text/javascript'>
		function form_serialcirc_circ_get_info_cb(){
			serialcirc_circ_get_info_cb(document.forms['saisie_cb_ex'].elements['form_cb_expl'].value,'serialcirc_pointage_zone'); 
			document.forms['saisie_cb_ex'].elements['form_cb_expl'].value='';			
			document.forms['saisie_cb_ex'].elements['form_cb_expl'].focus();
		}
	</script>
	<h1>".htmlentities($msg["serialcirc_circ_title"],ENT_QUOTES,$charset)."</h1>
	<h3>".htmlentities($msg["serialcirc_circ_title_form"],ENT_QUOTES,$charset)."</h3>		
	<form class='form-$current_module' name='saisie_cb_ex' method='post' action='!!form_action!!' onSubmit=\"form_serialcirc_circ_get_info_cb();	return false;\" >
		<h3>".htmlentities($msg["serialcirc_circ_cb_doc"],ENT_QUOTES,$charset)."</h3>
		<div class='form-contenu'>
			<div class='row'>
				<label class='etiquette' for='form_cb_expl'>!!message!!</label>
			</div>
			<div class='row'>
				<input class='saisie-20em' type='text' id='form_cb_expl' name='form_cb_expl' value=''  />
				<input type='button' class='bouton' value='$msg[502]'  
				onClick=\"form_serialcirc_circ_get_info_cb();	return false;\" />
			</div>
			<div  class='row' id='serialcirc_pointage_zone'>			
			</div>		
		</div>
	</form>
	<script type='text/javascript'>	
		document.forms['saisie_cb_ex'].elements['form_cb_expl'].focus();
        const form_cb_expl_input = document.getElementById('form_cb_expl');
        if (form_cb_expl_input) {
            form_cb_expl_input.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); // Empeche la soumission par defaut
                    form_serialcirc_circ_get_info_cb();
                    
                }
            });
        }
	</script>
	
";

$serialcirc_pointage_form="
	<div class='row'>
		!!liste_alerter!!
		!!liste_circuler!!
		!!liste_circulation!!
		!!liste_retard!!
	</div>		
";

$serialcirc_circ_cb_notfound="
	<br />
	<div class='erreur'>$msg[540]</div>
		<div class='row'>
		<div class='colonne10'>
			<img src='".get_url_icon('error.gif')."' class='align_left'>
			</div>
		<div class='colonne80'>
			<strong>".htmlentities($msg["serialcirc_circ_cb_notfound"],ENT_QUOTES,$charset)."</strong>
		</div>
	</div>
";
$serialcirc_circ_cb_info="
	<div class='row'>
		<strong>!!date!! - !!periodique!! - !!numero!! - !!abonnement!!</strong>
	</div>
	<div class='row'>
		".htmlentities($msg["serialcirc_circ_cb_first_diff"],ENT_QUOTES,$charset)."	!!destinataire!!
	</div>	
	<div class='row'>
		<input type=\"button\" class='bouton' value='".htmlentities($msg["serialcirc_circ_list_bull_circulation_imprimer_bt"],ENT_QUOTES,$charset)."' onClick=\"my_serialcirc_print_list_circ('!!zone!!','!!expl_id!!'); return false;\"/>&nbsp;
		<input type=\"button\" class='bouton' value='".htmlentities($msg["serialcirc_circ_list_bull_circulation_annuler_bt"],ENT_QUOTES,$charset)."' onClick=\"my_serialcirc_delete_circ('!!zone!!','!!expl_id!!'); return false;\"/>&nbsp;
	</div>
";		


$serialcirc_circ_pdf_diffusion="
	<style type='text/css'>
	table.listcirc {
		border-width: 5px;
		border-spacing: 0px;
		border-style: outset;
		border-color: gray;
		border-collapse: separate;
		background-color: rgb(255, 250, 250);
	}
	table.listcirc th {
		border-width: 1px;
		padding: 4px;
		margin:0px;
		border-style: solid;
		border-color: gray;
		background-color: white;
		-moz-border-radius: ;
	}
	table.listcirc td {
		border-width: 1px;
		padding: 4px;
		margin:0px;
		border-style: solid;
		border-color: gray;
		background-color: white;
		-moz-border-radius: ;
	}
	</style>
	<page backtop='10mm' backbottom='10mm' backleft='10mm' backright='10mm'>
	<span style='font-size: 18pt;'>	
	    <strong>!!periodique!! - !!numero!! - !!date!!</strong>
	    <br/>
	    ".htmlentities($msg["serialcirc_circ_list_bull_circulation_cb"],ENT_QUOTES,$charset)." <b>!!expl_cb!!</b><br/>
	    ".$msg["serialcirc_print_date"]."
	    <br/>    
	    <br/>
		<table class='listcirc' style='width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt; border-spacing: 5px;'>
			<tbody> <tr>!!th!!</tr>			 
			!!table_contens!!
			</tbody>
		</table>
	</span>
	</page>
";