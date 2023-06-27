<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_perso.tpl.php,v 1.1 2018-07-27 08:54:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

//-------------------------------------------
//	$sel_header : header
//-------------------------------------------
$sel_header = "
<div class='row'>
	<label for='titre_select_indexint' class='etiquette'>!!select_title!!</label>
	</div>
<div class='row'>
";

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

$jscript_ = "
<script type='text/javascript'>
<!--
function set_parent_w(f_caller, id_value, libelle_value,w)
{
	dyn=$dyn;
	nomchamp='$perso_name';
	if (dyn) {
		n_chp=get_parent_value(f_caller, 'n_'+nomchamp);
		flag = 1;
		//Vérification que la valeur du champ perso n'est pas déjà sélectionnée
		for (i=0; i<n_chp; i++) {	
			if (w.opener.document.getElementById('f_'+nomchamp+'_'+i).value==libelle_value) {
				alert('".$msg["persovalue_already_in_use"]."');
				flag = 0;
				break;
			}
		}
		if (flag) {
			for (i=0; i<n_chp; i++) {
				if ((w.opener.document.getElementById('f_'+nomchamp+'_'+i).value==0)||(w.opener.document.getElementById('f_'+nomchamp+'_'+i).value=='')) break;
			}

			try{
				if (i==n_chp) w.opener.add_$perso_name();
			} catch(e){
				i=0;
				w.opener.document.getElementById('f_'+nomchamp+'_'+i).value=reverse_html_entities(libelle_value);
				closeCurrentEnv();
			}
			w.opener.document.getElementById(nomchamp+'_'+i).value = id_value;
			w.opener.document.getElementById('f_'+nomchamp+'_'+i).value = reverse_html_entities(libelle_value);
		}
	} else {
		set_parent_value(f_caller, '".$p1."', id_value);
		set_parent_value(f_caller, '".$p2."', reverse_html_entities(libelle_value));
		closeCurrentEnv();
	}
}
-->
</script>
";

$jscript = $jscript_."
<script type='text/javascript'>
<!--
function set_parent(f_caller, id_value, libelle_value)
{
	set_parent_w(f_caller, id_value, libelle_value,parent);
}
-->
</script>
";

//-------------------------------------------
//	$sel_search_form : module de recherche
//-------------------------------------------
$sel_search_form ="
<form name='search_form' method='post' action='$base_url'>
<input type='text' name='f_user_input' value=\"!!deb_rech!!\" />
&nbsp;
<input type='submit' class='bouton_small' value='$msg[142]' /><br />
</form>
<script type='text/javascript'>
<!--
	document.forms['search_form'].elements['f_user_input'].focus();
-->
</script>
";


//-------------------------------------------
//	$sel_footer : footer
//-------------------------------------------
$sel_footer = "
</div>
";
