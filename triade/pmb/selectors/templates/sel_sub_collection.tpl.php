<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_sub_collection.tpl.php,v 1.31 2019/01/03 09:52:17 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

require_once($base_path."/selectors/templates/sel_authorities.tpl.php");

// templates du sélecteur sous-collections

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

global $dyn;
global $jscript;
global $jscript_common_authorities_unique, $jscript_common_authorities_link;
global $jscript_common_selector;
global $selfrom, $p1, $p2, $p3, $p4, $p5, $p6;
global $selector_sub_collection_form;

if($selfrom=="rmc") {
	$jscript = $jscript_common_selector;
} else {	
	if ($dyn==3) {
		$jscript = $jscript_common_authorities_unique;
	}elseif ($dyn==2) { // Pour les liens entre autorités
		$jscript = $jscript_common_authorities_link;
	}else {
		$jscript = "
		<script type='text/javascript'>
		<!--
		function set_parent(f_caller, idSubColl, libelleSubColl, callback, idParent, idLibelleParent, idEd, libelleEd)
		{
            if(callback == 'vedette_composee_callback') {
    			set_parent_value(f_caller, '".$p1."', idSubColl);
    			set_parent_value(f_caller, '".$p2."', libelleSubColl ? reverse_html_entities(libelleSubColl) : '');
			} else {
    			set_parent_value(f_caller, '".$p1."', idEd);
    			set_parent_value(f_caller, '".$p2."', libelleEd ? reverse_html_entities(libelleEd) : '');
    			set_parent_value(f_caller, '".$p3."', idParent);
    			set_parent_value(f_caller, '".$p4."', idLibelleParent ? reverse_html_entities(idLibelleParent) : '');
    			set_parent_value(f_caller, '".$p5."', idSubColl);
    			set_parent_value(f_caller, '".$p6."', libelleSubColl ? reverse_html_entities(libelleSubColl) : '');
            }
			closeCurrentEnv();
		}
		-->
		</script>
		";
	}
}

// ------------------------------------------
// 	$selector_sub_collection_form : form saisie collection
// ------------------------------------------
$selector_sub_collection_form = "
<script type='text/javascript'>
<!--
	function test_form(form){
		if(form.collection_nom.value.length == 0){
				alert(\"$msg[166]\");
				return false;
			}
		if(form.coll_id.value == 0){
				alert(\"$msg[180]\");
				return false;
			}
		return true;
	}
-->
</script>
<form name='saisie_sub_collection' method='post' action=\"!!base_url!!&action=update\">
<h3>$msg[177]</h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette'>$msg[67]</label>
		</div>
	<div class='row'>
		<input type='text' size='40' name='collection_nom' value=\"!!deb_saisie!!\" />
		</div>
	<div class='row'>
		<label class='etiquette'>$msg[179]</label>
		</div>
	<div class='row'>
		<input type='text' class='saisie-30emr' name='coll_libelle' value='' readonly />
		
		<input class='bouton_small' type='button' onclick=\"openPopUp('./select.php?what=collection&caller=saisie_sub_collection&p1=ed_id&p2=ed_libelle&p3=coll_id&p4=coll_libelle&p5=dsubcoll_id&p6=dsubcoll_lib', 'selector')\" title='$msg[157]' value='$msg[parcourir]' />
		<input type='button' class='bouton_small' value='$msg[raz]' onclick=\"this.form.ed_libelle.value=''; this.form.ed_id.value='0'; this.form.coll_libelle.value=''; this.form.coll_id.value='0'; \" />
		
		<input type='hidden' name='coll_id' value='0'>
		</div>
	<div class='row'>
		<label class='etiquette'>$msg[164]</label>
		</div>
	<div class='row'>
		<input type='text' class='saisie-30emr' name='ed_libelle' value='' readonly />
		<input type='hidden' name='dsubcoll_id'>
		<input type='hidden' name='dsubcoll_lib'>
		<input type='hidden' name='ed_id' value='0'>
		</div>
	<div class='row'>
		<label class='etiquette'>$msg[165]</label>
		</div>
	<div class='row'>
		<input type='text' size='40' name='issn' value='' maxlength='12'>
		</div>
	</div>
<div class='row'>
	<input type='button' id='btcancel' class='bouton_small' value='$msg[76]' onClick=\"document.location='!!base_url!!'\">
	<input type='submit' id='btsubmit' value='$msg[77]' class='bouton_small' onClick=\"return test_form(this.form)\">
	</div>
</form>
<script type='text/javascript'>
	document.forms['saisie_sub_collection'].elements['collection_nom'].focus();
</script>
";
