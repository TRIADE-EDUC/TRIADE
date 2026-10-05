<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_collection.tpl.php,v 1.31 2021/04/06 14:23:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

require_once($base_path."/selectors/templates/sel_authorities.tpl.php");

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

global $dyn;
global $jscript;
global $jscript_common_authorities_unique, $jscript_common_authorities_link;
global $jscript_common_selector;
global $selfrom, $p1, $p2, $p3, $p4, $p5, $p6;
global $selector_collection_form;

if($selfrom=="rmc") {
	$jscript = $jscript_common_selector;
} else {
	if ($dyn==3) {
		$jscript = $jscript_common_authorities_unique;
	}elseif ($dyn==2) { // Pour les liens entre autorités
		$jscript = $jscript_common_authorities_link;
	}else {
	    //(f_caller=='formulaire') : particularité pour les appels depuis les procédures (./includes/fields.inc.php)
		$jscript = "
		<script type='text/javascript'>
		<!--
		function set_parent(f_caller, id_coll, libelle_coll, callback, id_ed, libelle_ed){
            if(f_caller=='formulaire'){
                set_parent_value(f_caller, '".$p1."', id_coll);
        		set_parent_value(f_caller, '".$p2."', libelle_coll ? reverse_html_entities(libelle_coll) : '');
            } else {
                if (callback == 'vedette_composee_callback') {
        			set_parent_value(f_caller, '".$p1."', id_coll);
        			set_parent_value(f_caller, '".$p2."', libelle_coll ? reverse_html_entities(libelle_coll) : '');
    			} else {
                    set_parent_value(f_caller, '".$p1."', id_ed);
        			set_parent_value(f_caller, '".$p2."', libelle_ed ? reverse_html_entities(libelle_ed) : '');
        			set_parent_value(f_caller, '".$p3."', id_coll);
        			set_parent_value(f_caller, '".$p4."', libelle_coll ? reverse_html_entities(libelle_coll) : '');
        			set_parent_value(f_caller, '".$p5."', '');
        			set_parent_value(f_caller, '".$p6."', '');
                }
            }		
			closeCurrentEnv();
		}
		-->
		</script>
		";
	}
}

// ------------------------------------------
// 	$selector_collection_form : form saisie collection
// ------------------------------------------
$selector_collection_form = "
<script type='text/javascript'>
<!--
	function test_form(form){
		if(form.collection_nom.value.length == 0){
			alert(\"$msg[166]\");
			return false;
		}
		if (form.ed_id.value == 0){
			alert(\"$msg[172]\");
			return false;
		}
		return true;
	}
-->
</script>
<form name='saisie_collection' method='post' action=\"!!base_url!!&action=update\">
<h3>$msg[163]</h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette'>$msg[67]</label>
		</div>
	<div class='row'>
		<input type='text' size='40' name='collection_nom' value=\"!!deb_saisie!!\" >
		</div>
	<div class='row'>
		<label class='etiquette'>$msg[164]</label>
		</div>
	<div class='row'>
		<input type='text' class='saisie-30emr' name='ed_libelle' value='' readonly >
		<input class='bouton_small' type='button' onclick=\"openPopUp('./select.php?what=editeur&caller=saisie_collection&p1=ed_id&p2=ed_libelle&p3=dcoll_id&p4=dcoll_lib&p5=dsubcoll_id&p6=dsubcoll_lib', 'selector')\" title='$msg[157]' value='$msg[parcourir]' />
		<input type='button' class='bouton_small' value='$msg[raz]' onclick=\"this.form.ed_libelle.value=''; this.form.ed_id.value='0'; \" />
		<input type='hidden' name='ed_id' value='0'>
		<input type='hidden' name='dcoll_id'>
		<input type='hidden' name='dcoll_lib'>
		<input type='hidden' name='dsubcoll_id'>
		<input type='hidden' name='dsubcoll_lib'>
		</div>
	<div class='row'>
		<label class='etiquette'>$msg[165]</label>
		</div>
	<div class='row'>
		<input type='text' size='40' name='issn' value='' maxlength='12'>
		</div>
	</div>
<div class='row'>
	<input type='button' id='btcancel' class='bouton_small' value='$msg[76]' onClick=\"document.location='!!base_url!!';\">
	<input type='submit' id='btsubmit' value='$msg[77]' class='bouton_small' onClick=\"return test_form(this.form)\">
	</div>
</form>
<script type='text/javascript'>
	document.forms['saisie_collection'].elements['collection_nom'].focus();
</script>
";
