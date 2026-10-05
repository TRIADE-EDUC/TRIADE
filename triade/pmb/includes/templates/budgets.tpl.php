<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: budgets.tpl.php,v 1.23 2023/12/20 09:43:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $budg_search_form, $current_module, $msg, $charset, $acquisition_gestion_tva, $en_tete_view_bud_form, $view_bud_form, $view_lig_rub_form, $budg_js_form, $budg_content_form, $mnt_form, $pmb_gestion_devise;
global $sel_typ_form, $rub_js_form, $rub_content_form, $mnt_rub_form, $lig_rub, $lig_rub_img, $lig_indent, $bt_add_lig;
global $autorisations_selection_content_form, $autorisations_user_content_form;

//Template de choix du budget
$budg_search_form = "
<form class='form-".$current_module."' id='search' name='search' method='post' action=\"\">
	<h3>!!form_title!!</h3>
	<!--    Contenu du form    -->
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne5'>
				<label class='etiquette'>".htmlentities($msg['acquisition_coord_lib'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='colonne_suite'>
				<!-- sel_bibli -->
			</div>
		</div>
		<div class='row'></div>
	</div>
</form>
<br />
<div class='row'>
	<div class='row'>";
		if($acquisition_gestion_tva==1){
			$en_tete_view_bud_form="
			<div class='row'>
				<div class='colonne3'><div class='colonneth'>".htmlentities($msg['acquisition_rub'], ENT_QUOTES, $charset)."</div></div>
				<div class='colonne10'><div class='colonneth'>".htmlentities($msg['acquisition_rub_mnt_tot'], ENT_QUOTES, $charset)."</div></div>
				<div class='colonne10'><div class='colonneth'>".htmlentities($msg['acquisition_rub_mnt_ava_ht'], ENT_QUOTES, $charset)."</div></div>
				<div class='colonne10'><div class='colonneth'>".htmlentities($msg['acquisition_rub_mnt_eng_ht'], ENT_QUOTES, $charset)."</div></div>
				<div class='colonne10'><div class='colonneth'>".htmlentities($msg['acquisition_rub_mnt_fac_ht'], ENT_QUOTES, $charset)."</div></div>
				<div class='colonne10'><div class='colonneth'>".htmlentities($msg['acquisition_rub_mnt_pay_ht'], ENT_QUOTES, $charset)."</div></div>
				<div class='colonne10'><div class='colonneth'>".htmlentities($msg['acquisition_rub_mnt_sol'], ENT_QUOTES, $charset)."</div></div>
			</div>	
		"; 
		}elseif($acquisition_gestion_tva==2){
			$en_tete_view_bud_form="
			<div class='row'>
				<div class='colonne3'><div class='colonneth'>".htmlentities($msg['acquisition_rub'], ENT_QUOTES, $charset)."</div></div>
				<div class='colonne10'><div class='colonneth'>".htmlentities($msg['acquisition_rub_mnt_tot'], ENT_QUOTES, $charset)."</div></div>
				<div class='colonne10'><div class='colonneth'>".htmlentities($msg['acquisition_rub_mnt_ava_ttc'], ENT_QUOTES, $charset)."</div></div>
				<div class='colonne10'><div class='colonneth'>".htmlentities($msg['acquisition_rub_mnt_eng_ttc'], ENT_QUOTES, $charset)."</div></div>
				<div class='colonne10'><div class='colonneth'>".htmlentities($msg['acquisition_rub_mnt_fac_ttc'], ENT_QUOTES, $charset)."</div></div>
				<div class='colonne10'><div class='colonneth'>".htmlentities($msg['acquisition_rub_mnt_pay_ttc'], ENT_QUOTES, $charset)."</div></div>
				<div class='colonne10'><div class='colonneth'>".htmlentities($msg['acquisition_rub_mnt_sol'], ENT_QUOTES, $charset)."</div></div>
			</div>	
		"; 
			
		}else {
			$en_tete_view_bud_form="
			<div class='ux-table-simili'>
			<div class='row'>
				<div class='colonne3'><div class='colonneth'>".htmlentities($msg['acquisition_rub'], ENT_QUOTES, $charset)."</div></div>
				<div class='colonne10'><div class='colonneth'>".htmlentities($msg['acquisition_rub_mnt_tot'], ENT_QUOTES, $charset)."</div></div>
				<div class='colonne10'><div class='colonneth'>".htmlentities($msg['acquisition_rub_mnt_ava'], ENT_QUOTES, $charset)."</div></div>
				<div class='colonne10'><div class='colonneth'>".htmlentities($msg['acquisition_rub_mnt_eng'], ENT_QUOTES, $charset)."</div></div>
				<div class='colonne10'><div class='colonneth'>".htmlentities($msg['acquisition_rub_mnt_fac'], ENT_QUOTES, $charset)."</div></div>
				<div class='colonne10'><div class='colonneth'>".htmlentities($msg['acquisition_rub_mnt_pay'], ENT_QUOTES, $charset)."</div></div>
				<div class='colonne10'><div class='colonneth'>".htmlentities($msg['acquisition_rub_mnt_sol'], ENT_QUOTES, $charset)."</div></div>
			</div>
		"; 
		}
		//Template de visualisation d'un budget
		$view_bud_form = "
		<div class='ux-simple-table-simili'>
			<div class='row'>
				<div class='colonne2'>
					<div class='colonne5'>
						<label class='etiquette'>".htmlentities($msg['acquisition_bud'],ENT_QUOTES,$charset)."</label> 
					</div>
					<div class='colonne_suite'>!!lib_bud!!</div>
				</div>
				<div class='colonne2'>
					<div class='colonne5'>
						<label class='etiquette'>".htmlentities($msg['acquisition_budg_montant'],ENT_QUOTES,$charset)."</label> 
					</div>
					<div class='colonne_suite'>!!mnt_bud!!&nbsp;!!devise!!&nbsp;!!htttc!!</div>
				</div>
			</div>

			<div class='row'>
				<div class='colonne2'>
					<div class='colonne5'>
						<label class='etiquette'>".htmlentities($msg['acquisition_budg_exer'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>!!lib_exer!!</div>
				</div>
				<div class='colonne2'>
					<div class='colonne5'>
						<label class='etiquette'>".htmlentities($msg['acquisition_budg_aff_lib'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>!!typ_bud!!</div>
				</div>
			</div>

			<div class='row'>
				<div class='colonne2'>
					<div class='colonne5'>
						<label class='etiquette'>".htmlentities($msg['acquisition_statut'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>!!sta_bud!!</div>
				</div>
				<div class='colonne2'>
					<div class='colonne5'>
						<label class='etiquette'>".htmlentities($msg['acquisition_budg_seuil'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>!!seu_bud!!&nbsp;%</div>
				</div>
			</div>
			<div class='row'>&nbsp;</div>
		</div>

		<!-- tableau rubriques budgetaires -->

		<div class='row'>
			<a href=\"javascript:expandAllImb()\"><img src='".get_url_icon('expand_all.gif')."' id='expandall' /></a>
			<a href=\"javascript:collapseAllImb()\"><img src='".get_url_icon('collapse_all.gif')."' id='collapseall' /></a>
		</div>

		$en_tete_view_bud_form

			<!-- rubriques -->
		<div class='row'>&nbsp;</div>
			<!-- totaux -->
		<div class='row'>&nbsp;</div>
		<div class='row'>&nbsp;</div>
		<script type='text/javascript'>

		function expandAllImb() {
		var tempColl    = document.getElementsByTagName('DIV');
		var tempCollCnt = tempColl.length;
		for (var i = 0; i < tempCollCnt; i++) {
			if(tempColl[i].className == 'imb')
			tempColl[i].style.display = 'block';
		}
		tempColl    = document.getElementsByTagName('IMG');
		tempCollCnt = tempColl.length;
		for (var i = 0; i < tempCollCnt; i++) {
			if(tempColl[i].name == 'imEx') {
			tempColl[i].src = imgOpened.src;
			}
		}
		}

		function collapseAllImb() {
		var tempColl    = document.getElementsByTagName('DIV');
		var tempCollCnt = tempColl.length;
		for (var i = 0; i < tempCollCnt; i++) {
			if(tempColl[i].className == 'imb')
			tempColl[i].style.display = 'none';
		}
		tempColl    = document.getElementsByTagName('IMG');
		tempCollCnt = tempColl.length;
		for (var i = 0; i < tempCollCnt; i++) {
			if(tempColl[i].name == 'imEx') {
			tempColl[i].src = imgClosed.src;
			}
		}
		}

		</script>	
	</div>
</div>
<form name='print_bud' method='post' action='./print_acquisition.php?categ=ach&sub=bud&action=print_budget&id_bibli=!!id_bibli!!&id_bud=!!id_bud!!' target='_blank'>
	<input type='button' class='bouton' value='".htmlentities($msg['acquisitions_export_excel'],ENT_QUOTES,$charset)."' onClick='this.form.submit();'>
</form>
";


//Template de visualisation d'une ligne budgetaire 
$view_lig_rub_form = "
	<div id='el!!id_rub!!Child' class='row' >
		<div class='colonne3'><div class='colonnetd'>
			<!-- marge -->
			<!-- img_plus -->
				!!lib_rub!!
		</div></div>
		<div class='colonne10'><div class='colonnetd' style='text-align:right;'>
			!!mnt_tot!!
		</div></div>
		<div class='colonne10'><div class='colonnetd' style='text-align:right;'>
			!!mnt_ava!!
		</div></div>
		<div class='colonne10'><div class='colonnetd' style='text-align:right;'>
			!!mnt_eng!!
		</div></div>
		<div class='colonne10'><div class='colonnetd' style='text-align:right;'>
			!!mnt_fac!!
		</div></div>
		<div class='colonne10'><div class='colonnetd' style='text-align:right;'>
			!!mnt_pay!!
		</div></div>
		<div class='colonne10'><div class='colonnetd' style='text-align:right;'>
			!!mnt_sol!!
		</div></div>
	</div>
	<div id='el_!!id_rub!!_Child' name='el_!!id_rub!!_Child' class='imb' style='display:none;'>
		<!-- sous_rub -->
	</div>";

//Template de visualisation du total 
$view_tot_rub_form = "	
	<div class='row'>
		<div class='colonne3'>&nbsp;</div>
		<div class='colonne10'><div class='colonneth' style='text-align:right;'>
			!!mnt_tot!!
		</div></div>
		<div class='colonne10'><div class='colonneth' style='text-align:right;'>
			!!mnt_ava!!
		</div></div>
		<div class='colonne10'><div class='colonneth' style='text-align:right;'>
			!!mnt_eng!!
		</div></div>
		<div class='colonne10'><div class='colonneth' style='text-align:right;'>
			!!mnt_fac!!
		</div></div>
		<div class='colonne10'><div class='colonneth' style='text-align:right;'>
			!!mnt_pay!!
		</div></div>
		<div class='colonne10'><div class='colonneth' style='text-align:right;'>
			!!mnt_sol!!
		</div></div>
	</div>";

//Template de visualisation d'une ligne budgetaire avec TVA
$view_lig_rub_form = "
	<div id='el!!id_rub!!Child' class='row' >
		<div class='colonne3'><div class='colonnetd'>
			<!-- marge -->
			<!-- img_plus -->
				!!lib_rub!!
		</div></div>
		<div class='colonne10'><div class='colonnetd' style='text-align:right;'>
			!!mnt_tot!!
		</div></div>
		<div class='colonne10'><div class='colonnetd' style='text-align:right;'>
			!!mnt_ava!!
		</div></div>
		<div class='colonne10'><div class='colonnetd' style='text-align:right;'>
			!!mnt_eng!!
		</div></div>
		<div class='colonne10'><div class='colonnetd' style='text-align:right;'>
			!!mnt_fac!!
		</div></div>
		<div class='colonne10'><div class='colonnetd' style='text-align:right;'>
			!!mnt_pay!!
		</div></div>
		<div class='colonne10'><div class='colonnetd' style='text-align:right;'>
			!!mnt_sol!!
		</div></div>
	</div>
	<div id='el_!!id_rub!!_Child' name='el_!!id_rub!!_Child' class='imb' style='display:none;'>
		<!-- sous_rub -->
	</div>";

//Template de visualisation du total  avec TVA
$view_tot_rub_form = "	
	<div class='row ux-simili-table-row-last'>
		<div class='colonne3'>&nbsp;</div>
		<div class='colonne10'><div class='colonneth' style='text-align:right;'>
			!!mnt_tot!!
		</div></div>
		<div class='colonne10'><div class='colonneth' style='text-align:right;'>
			!!mnt_ava!!
		</div></div>
		<div class='colonne10'><div class='colonneth' style='text-align:right;'>
			!!mnt_eng!!
		</div></div>
		<div class='colonne10'><div class='colonneth' style='text-align:right;'>
			!!mnt_fac!!
		</div></div>
		<div class='colonne10'><div class='colonneth' style='text-align:right;'>
			!!mnt_pay!!
		</div></div>
		<div class='colonne10'><div class='colonneth' style='text-align:right;'>
			!!mnt_sol!!
		</div></div>
	</div>";

//Template de modification du formulaire de budget
$budg_js_form = "
<script type='text/javascript' src='./javascript/tabform.js'></script>
<script type='text/javascript'>
function expandAllImb() {
  var tempColl    = document.getElementsByTagName('DIV');
  var tempCollCnt = tempColl.length;
  for (var i = 0; i < tempCollCnt; i++) {
     if(tempColl[i].className == 'imb')
     tempColl[i].style.display = 'block';
  }
  tempColl    = document.getElementsByTagName('IMG');
  tempCollCnt = tempColl.length;
  for (var i = 0; i < tempCollCnt; i++) {
     if(tempColl[i].name == 'imEx') {
       tempColl[i].src = imgOpened.src;
     }
  }
}

function collapseAllImb() {
  var tempColl    = document.getElementsByTagName('DIV');
  var tempCollCnt = tempColl.length;
  for (var i = 0; i < tempCollCnt; i++) {
     if(tempColl[i].className == 'imb')
     tempColl[i].style.display = 'none';
  }
  tempColl    = document.getElementsByTagName('IMG');
  tempCollCnt = tempColl.length;
  for (var i = 0; i < tempCollCnt; i++) {
     if(tempColl[i].name == 'imEx') {
       tempColl[i].src = imgClosed.src;
     }
  }
}
</script>
";

$budg_content_form = "
<div class='row'>
	<label class='etiquette' for='libelle'>".htmlentities($msg[103],ENT_QUOTES,$charset)."</label>
</div>
<div class='row'>
	<input type=text id='libelle' name='libelle' value=\"!!libelle!!\" class='saisie-60em' />
</div>
<div class='row'>
	<div class='colonne5'>
		<label class='etiquette'>".htmlentities($msg['acquisition_budg_exer'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='colonne5'>
		<label class='etiquette'>".htmlentities($msg['acquisition_budg_montant'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='colonne5'>
		<label class='etiquette'>".htmlentities($msg['acquisition_budg_aff_lib'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='colonne5'>
		<label class='etiquette' for='seuil'>".htmlentities($msg['acquisition_budg_seuil'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='colonne5'>
		<input type='hidden' id='val_statut' name='val_statut' value='!!val_statut!!' />
		<label class='etiquette'>".htmlentities($msg['acquisition_statut'],ENT_QUOTES,$charset)."</label>
	</div>
</div>
<div class='row'>
	<div class='colonne5'>
		!!exer!!
	</div>
	<div class='colonne5' >
		!!montant!!
	</div>
	<div class='colonne5' >
		!!sel_typ!!
	</div>
	<div class='colonne5'>
		<input type='text' id='seuil' name='seuil' value=\"!!seuil!!\" class='saisie-5em' style='text-align:right'/>
		<label class='etiquette'>&nbsp;%</label>
	</div>
	<div class='colonne5'>
		!!statut!!
	</div>
</div>
<div class='row'>
	<label class='etiquette' for='comment'>".htmlentities($msg['acquisition_budg_comment'],ENT_QUOTES,$charset)."</label>
</div>
<div class='row'>
	<textarea id='comment' name='comment' class='saisie-80em' cols='62' rows='6' wrap='virtual'>!!comment!!</textarea>
</div>
<div class='row'></div>
<div class='row'><hr /></div>

<!-- tableau rubriques budgetaires -->

<table>
	<th style='width:65%'>".htmlentities($msg['acquisition_rub'], ENT_QUOTES, $charset)."</th>
	<th style='width:10%'>!!lib_mnt!!</th>
	<th style='width:25%'>".htmlentities($msg['acquisition_num_cp_compta'], ENT_QUOTES, $charset)."</th>
</table>
<div class='colonne'>
	<a href=\"javascript:expandAllImb()\"><img src='".get_url_icon('expand_all.gif')."' border='0' id='expandall'></a>
	<a href=\"javascript:collapseAllImb()\"><img src='".get_url_icon('collapse_all.gif')."' border='0' id='collapseall'></a>
</div>
	<!-- rubriques -->
<div class='row'>
	<!-- bouton_lig -->
</div>
";

//Template du montant total
$mnt_form[0] = "
<span id='lib_mnt_bud' style='display:inline;'>0.00</span>
<input type='text' id='mnt_bud' name='mnt_bud' class='saisie-10em' style='text-align:right;display:none' value='0.00' />
<label class='etiquette'>&nbsp;".$pmb_gestion_devise."</label>
";
$mnt_form[1] = "
<input type='text' id='mnt_bud' name='mnt_bud' class='saisie-10em' style='text-align:right;' value='!!mnt_bud!!' />
<label class='etiquette'>&nbsp;".$pmb_gestion_devise."</label>
";

//Template du selecteur de type pour affectation globale ou par rubrique
$sel_typ_form = "
<input type='radio' name='sel_typ' value='0' checked='checked' onclick=\"document.getElementById('lib_mnt_bud').style.display='inline';document.getElementById('mnt_bud').style.display='none';\"  />".htmlentities($msg['acquisition_budg_aff_rub'], ENT_QUOTES, $charset)."
<input type='radio' name='sel_typ' value='1' onclick=\"document.getElementById('lib_mnt_bud').style.display='none';document.getElementById('mnt_bud').style.display='inline';\"/>".htmlentities($msg['acquisition_budg_aff_glo'], ENT_QUOTES, $charset);


//Template du formulaire des rubriques
$rub_js_form = "
<script type='text/javascript' src='./javascript/tabform.js'></script>
<script type='text/javascript'>
function expandAllImb() {
  var tempColl    = document.getElementsByTagName('DIV');
  var tempCollCnt = tempColl.length;
  for (var i = 0; i < tempCollCnt; i++) {
     if(tempColl[i].className == 'imb')
     tempColl[i].style.display = 'block';
  }
  tempColl    = document.getElementsByTagName('IMG');
  tempCollCnt = tempColl.length;
  for (var i = 0; i < tempCollCnt; i++) {
     if(tempColl[i].name == 'imEx') {
       tempColl[i].src = imgOpened.src;
     }
  }
}

function collapseAllImb() {
  var tempColl    = document.getElementsByTagName('DIV');
  var tempCollCnt = tempColl.length;
  for (var i = 0; i < tempCollCnt; i++) {
     if(tempColl[i].className == 'imb')
     tempColl[i].style.display = 'none';
  }
  tempColl    = document.getElementsByTagName('IMG');
  tempCollCnt = tempColl.length;
  for (var i = 0; i < tempCollCnt; i++) {
     if(tempColl[i].name == 'imEx') {
       tempColl[i].src = imgClosed.src;
     }
  }
}
</script>
";

$rub_content_form = "
<div class='row'><!-- nav_form --></div>
<div class='row'>&nbsp;</div>
<div class='row'>
	<label class='etiquette' for='libelle'>".htmlentities($msg[103],ENT_QUOTES,$charset)."</label>
</div>
<div class='row'>
	<input type=text id='libelle' name='libelle' value=\"!!libelle!!\" class='saisie-60em' />
</div>

<div class='row'>
	<!-- lib_mnt -->
	<div class='colonne4'>
		<!-- label_ncp -->
	</div>
</div>

<div class='row'>
	<!-- montant --> 
	<div class='colonne4'>
		!!ncp!!
	</div>
</div>

<div class='row'>
	<label class='etiquette' for='comment'>".htmlentities($msg['acquisition_budg_comment'],ENT_QUOTES,$charset)."</label>
</div>
<div class='row'>
	<textarea id='comment' name='comment' class='saisie-80em' cols='62' rows='6' wrap='virtual'>!!comment!!</textarea>
</div>
<div class='row'></div>


<div class='row'><hr /></div>

<!-- tableau rubriques budgetaires -->

<table>

	<th style='width:65%;'>".htmlentities($msg['acquisition_rub'], ENT_QUOTES, $charset)."</th>
	<th style='width:10%'>!!lib_mnt!!</th>
	<th style='width:25%;'>".htmlentities($msg['acquisition_num_cp_compta'], ENT_QUOTES, $charset)."</th>

</table>

<div class='colonne'>
	<a href=\"javascript:expandAllImb()\"><img src='".get_url_icon('expand_all.gif')."' border='0' id='expandall'></a>
	<a href=\"javascript:collapseAllImb()\"><img src='".get_url_icon('collapse_all.gif')."' border='0' id='collapseall'></a>
</div>

<!-- rubriques -->

<div class='row'>
	<!-- bouton_lig -->
</div>

<!-- autorisations -->		

<div class='row'></div>
";

$mnt_rub_form[0] = "
<div class='colonne4'>
	<label class='etiquette' for='mnt'>".htmlentities($msg['acquisition_rub_mnt'],ENT_QUOTES,$charset)."</label>
</div>";

$mnt_rub_form[1] = "
<div class='colonne4'>
<input type='text' id='mnt' name='mnt' class='saisie-10em' style='text-align:right' value='!!mnt_rub!!' /><label class='etiquette'>&nbsp;".$pmb_gestion_devise."</label>
</div>
";

$lig_rub[0] = "
	<div id='el!!id_rub!!Child' class='row' >
		<div class='colonne3' style='width:65%;'>
			<!-- marge -->
			<!-- img_plus -->
			<a href=\"./admin.php?categ=acquisition&sub=budget&action=modif_rub&id_bud=!!id_bud!!&id_rub=!!id_rub!!&id_parent=!!id_parent!! \" >			
				!!lib_rub!!
			</a>
		</div>
		<div class='colonne_suite' style='width:10%;text-align:right;'>
			!!mnt!!
		</div>
		<div class='colonne_suite' style='width:25%;text-align:right;'>
			!!ncp!!
		</div>
	</div>

	<div id='el_!!id_rub!!_Child' name='el_!!id_rub!!_Child' class='imb' style='display:none;'>
		<div class ='row'>
			<!-- sous_rub -->
		</div>
	</div>	
";

$lig_rub_img = get_expandBase_button('el_!!id_rub!!_');

$lig_indent = "<span class='child' >&nbsp;</span>";

$bt_add_lig = "<input class='bouton' type='button' value=' ".$msg['acquisition_rub_lig']." ' onClick=\"document.location='./admin.php?categ=acquisition&sub=budget&action=add_rub&id_bibli=!!id_bibli!!&id_bud=!!id_bud!!&id_rub=!!id_rub!!&id_parent=!!id_parent!!';\" />";


//    ----------------------------------------------------
//     entête et table autorisations
//    ----------------------------------------------------

$autorisations_selection_content_form ="	<div class='row'><hr /></div>
			<div class='row'>
				<input type='hidden' id='auto_id_list' name='auto_id_list' value='!!auto_id_list!!' >
				<label class='etiquette'>".htmlentities($msg['acquisition_autorisations'], ENT_QUOTES, $charset)."</label>
				<input type='button' class='bouton_small align_middle' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,1);'>
				<input type='button' class='bouton_small align_middle' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,0);'>
			</div>";

$autorisations_user_content_form = "
	<span class='usercheckbox'>
		<input  type='checkbox' id='user_aut[!!user_id!!]' name='user_aut[!!user_id!!]' !!checked!! value='!!user_id!!' />
	<label for='user_aut[!!user_id!!]' >!!user_name!!</label>
	</span>&nbsp;
";
