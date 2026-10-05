<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: form.inc.php,v 1.45 2021/12/15 08:47:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset, $current_module;

require_once($class_path."/netbase/netbase.class.php");

$netbase = new netbase();
print "
<script type='text/javascript'>
<!--
function check_clean_form(form) {
	var flag=0;
	if(form.index_global.checked) flag += 1;
	if(form.index_notices.checked) flag += 2;
	if(form.clean_authors.checked) flag += 4;
	if(form.clean_editeurs.checked) flag += 8;
	if(form.clean_collections.checked) flag += 16;
	if(form.clean_subcollections.checked) flag += 32;
	if(form.clean_categories.checked) flag += 64;
	if(form.clean_series.checked) flag += 128;
	// if(form.clean_relations.checked) clean_relations est forcé ! 
	flag += 256;
	if(form.clean_notices.checked) flag += 512;
	if(form.index_acquisitions) {
		if(form.index_acquisitions.checked) flag += 1024;
	}
	if(form.gen_signature_notice.checked) flag += 2048;
	if(form.nettoyage_clean_tags.checked) flag += 4096;
	if(form.clean_categories_path.checked) flag += 8192;
	if(form.gen_date_publication_article.checked) flag += 16384;
	if(form.gen_date_tri.checked) flag += 32768;
	if(form.reindex_docnum) {
		if(form.reindex_docnum.checked) flag += 65536;
	}
	if(form.clean_opac_search_cache) {
		if(form.clean_opac_search_cache.checked) flag += 131072;
	}
	if(form.clean_cache_amende) {
		if(form.clean_cache_amende.checked) flag += 262144;
	}
	if(form.clean_cache_temporary_files) {
		if(form.clean_cache_temporary_files.checked) flag += 8589934592;
	}
	if(form.clean_cache_apcu) {
		if(form.clean_cache_apcu.checked) flag += 34359738368;
	}
	if(form.clean_entities_data) {
		if(form.clean_entities_data.checked) flag += 68719476736;
	}
	if(form.clean_titres_uniformes.checked) flag += 524288;
	if(flag == 0) {
		alert(\"".$msg["nettoyage_alert"]."\");
		return(false);
	}
	if(form.clean_categories.checked) {
		if (confirm(\"".$msg["nettoyage_alert_categ"]."\")) return true
		else return(false);
	}
	if(form.clean_notices.checked) {
		if (confirm(\"".$msg["nettoyage_alert_expl"]."\")) return true
		else return(false);
	}

	return true;
}
-->
</script>
<form class='form-$current_module' name='form_netbase' action='./clean.php' method='post'>
<h3>".htmlentities($msg["nettoyage_operations"], ENT_QUOTES, $charset)."</h3>
<!--	Contenu du form	-->
<div class='form-contenu'>";
print $netbase->get_form_proceedings();
print "
	</div>
<input type='submit' value='".$msg['502']."' class='bouton' onClick=\"return check_clean_form(this.form)\">
</form>
";


?>
