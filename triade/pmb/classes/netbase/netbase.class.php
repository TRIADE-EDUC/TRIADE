<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: netbase.class.php,v 1.15 2019-04-29 11:58:18 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/thumbnail.class.php");

// definitions
define('INDEX_GLOBAL'					, 1);
define('INDEX_NOTICES'					, 2);
define('CLEAN_AUTHORS'					, 4);
define('CLEAN_PUBLISHERS'				, 8);
define('CLEAN_COLLECTIONS'				, 16);
define('CLEAN_SUBCOLLECTIONS'			, 32);
define('CLEAN_CATEGORIES'				, 64);
define('CLEAN_SERIES'					, 128);
define('CLEAN_RELATIONS'				, 256);
define('CLEAN_NOTICES'					, 512);
define('INDEX_ACQUISITIONS'				, 1024);
define('GEN_SIGNATURE_NOTICE'			, 2048);
define('NETTOYAGE_CLEAN_TAGS'			, 4096);
define('CLEAN_CATEGORIES_PATH'			, 8192);
define('GEN_DATE_PUBLICATION_ARTICLE'	, 16384);
define('GEN_DATE_TRI'					, 32768);
define('INDEX_DOCNUM'					, 65536);
define('CLEAN_OPAC_SEARCH_CACHE'		, 131072);
define('CLEAN_CACHE_AMENDE'				, 262144);
define('CLEAN_TITRES_UNIFORMES'			, 524288);
define('CLEAN_INDEXINT'			        , 1048576);
define('GEN_PHONETIQUE'			        , 2097152);
define('INDEX_RDFSTORE'					, 4194304);
define('INDEX_SYNCHRORDFSTORE'			, 8388608);
define('INDEX_FAQ'						, 16777216);
define('INDEX_CMS'						, 33554432);
define('INDEX_CONCEPT'					, 67108864);
define('HASH_EMPR_PASSWORD'				, 134217728);
define('INDEX_AUTHORITIES'				, 268435456);
define('GEN_SIGNATURE_DOCNUM'			, 536870912);
define('DELETE_EMPR_PASSWORDS'			, 1073741824);
define('CLEAN_RECORDS_THUMBNAIL'		, 2147483648);
define('GEN_AUT_LINK'		            , 4294967296);
define('CLEAN_CACHE_TEMPORARY_FILES'	, 8589934592);

class netbase {

	public function __construct() {

	}

	public function proceed() {

	}

	public function get_form_proceedings($proceedings=array()) {
		global $msg, $charset, $acquisition_active, $pmb_indexation_docnum;
		global $pmb_gestion_financiere, $pmb_gestion_amende;
		global $pmb_synchro_rdf;
		global $faq_active, $cms_active;
		global $thesaurus_concepts_active;
		global $pmb_explnum_controle_doublons;
		
		if ($proceedings) {
			foreach ($proceedings as $name=>$value) {
				${$name} = $value;
			}
		}

		// Réindexer
		$form_proceedings = "
			<h3>".$msg['nettoyage_operations_reindex']."</h3>
			<div class='row'>
				<input type='checkbox' value='1' id='index_global' name='index_global' ".(isset($index_global) && $index_global == "1" ? "checked" :"").">&nbsp;<label for='index_global' >".htmlentities($msg["nettoyage_index_global"], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				<input type='checkbox' value='2' id='index_notices' name='index_notices' ".(isset($index_notices) && $index_notices == "2" ? "checked" :"").">&nbsp;<label for='index_notices'>".htmlentities($msg["nettoyage_index_notices"], ENT_QUOTES, $charset)."</label>
			</div>";
		if ($acquisition_active) {
			$form_proceedings .= "
				<div class='row'>
					<input type='checkbox' value='1024' id='index_acquisitions' name='index_acquisitions' ".(isset($index_acquisitions) && $index_acquisitions == "1024" ? "checked" :"").">&nbsp;<label for='index_acquisitions'>".htmlentities($msg["nettoyage_reindex_acq"], ENT_QUOTES, $charset)."</label>
				</div>";
		}
		if($pmb_indexation_docnum){
			$form_proceedings .= "
				<div class='row'>
					<input type='checkbox' value='65536' id='reindex_docnum' name='reindex_docnum' ".(isset($reindex_docnum) && $reindex_docnum == "65536" ? "checked" :"").">&nbsp;<label for='reindex_docnum'>".htmlentities($msg["docnum_reindexer"], ENT_QUOTES, $charset)."</label>
				</div>";
		}
		$form_proceedings .= "
			<div class='row'>
				<input type='checkbox' value='4194304' id='index_rdfstore' name='index_rdfstore' ".(isset($index_rdfstore) && $index_rdfstore == "4194304" ? "checked" :"").">&nbsp;<label for='index_rdfstore'>".htmlentities($msg["nettoyage_rdfstore_reindex"], ENT_QUOTES, $charset)."</label>
			</div>";
		if($pmb_synchro_rdf){
			$form_proceedings .= "
				<div class='row'>
					<input type='checkbox' value='8388608' id='index_synchrordfstore' name='index_synchrordfstore' ".(isset($index_synchrordfstore) && $index_synchrordfstore == "8388608" ? "checked" :"").">&nbsp;<label for='index_synchrordfstore'>".htmlentities($msg["nettoyage_synchrordfstore_reindex"], ENT_QUOTES, $charset)."</label>
				</div>";
		}
		if($faq_active){
			$form_proceedings .= "
				<div class='row'>
					<input type='checkbox' value='16777216' id='index_faq' name='index_faq' ".(isset($index_faq) && $index_faq == "16777216" ? "checked" :"").">&nbsp;<label for='index_faq'>".htmlentities($msg["nettoyage_faq_reindex"], ENT_QUOTES, $charset)."</label>
				</div>";
		}
		if($cms_active){
			$form_proceedings .= "
				<div class='row'>
					<input type='checkbox' value='33554432' id='index_cms' name='index_cms' ".(isset($index_cms) && $index_cms == "33554432" ? "checked" :"").">&nbsp;<label for='index_cms'>".htmlentities($msg["nettoyage_cms_reindex"], ENT_QUOTES, $charset)."</label>
				</div>";
		}
		if($thesaurus_concepts_active==1){
			$form_proceedings .= "
				<div class='row'>
					<input type='checkbox' value='67108864' id='index_concept' name='index_concept' ".(isset($index_concept) && $index_concept == "67108864" ? "checked" :"").">&nbsp;<label for='index_concept'>".htmlentities($msg["nettoyage_concept_reindex"], ENT_QUOTES, $charset)."</label>
				</div>";
		}
		$form_proceedings .= "
			<div class='row'>
				<input type='checkbox' value='268435456' name='index_authorities' ".(isset($index_authorities) && $index_authorities == "268435456" ? "checked" :"").">&nbsp;<label for='index_authorities'>".htmlentities($msg["nettoyage_index_authorities"], ENT_QUOTES, $charset)."</label>
			</div>";

		// Supprimer
		$form_proceedings .= "
			<br />
			<h3>".$msg['nettoyage_operations_delete']."</h3>
			<div class='row'>
				<input type='checkbox' value='4' id='clean_authors' name='clean_authors' ".(isset($clean_authors) && $clean_authors == "4" ? "checked" :"").">&nbsp;<label for='clean_authors'>".htmlentities($msg["nettoyage_clean_authors"], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				<input type='checkbox' value='8' id='clean_editeurs' name='clean_editeurs' ".(isset($clean_editeurs) && $clean_editeurs == "8" ? "checked" :"").">&nbsp;<label for='clean_editeurs'>".htmlentities($msg["nettoyage_clean_editeurs"], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				<input type='checkbox' value='16' id='clean_collections' name='clean_collections' ".(isset($clean_collections) && $clean_collections == "16" ? "checked" :"").">&nbsp;<label for='clean_collections'>".htmlentities($msg["nettoyage_clean_collections"], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				<input type='checkbox' value='32' id='clean_subcollections' name='clean_subcollections' ".(isset($clean_subcollections) && $clean_subcollections == "32" ? "checked" :"").">&nbsp;<label for='clean_subcollections'>".htmlentities($msg["nettoyage_clean_subcollections"], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				<input type='checkbox' value='64' id='clean_categories' name='clean_categories' ".(isset($clean_categories) && $clean_categories == "64" ? "checked" :"").">&nbsp;<label for='clean_categories'>".htmlentities($msg["nettoyage_clean_categories"], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				<input type='checkbox' value='128' id='clean_series' name='clean_series' ".(isset($clean_series) && $clean_series == "128" ? "checked" :"").">&nbsp;<label for='clean_series'>".htmlentities($msg["nettoyage_clean_series"], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				<input type='checkbox' value='524288' id='clean_titres_uniformes' name='clean_titres_uniformes' ".(isset($clean_titres_uniformes) && $clean_titres_uniformes == "524288" ? "checked" :"").">&nbsp;<label for='clean_titres_uniformes'>".htmlentities($msg["nettoyage_clean_titres_uniformes"], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				<input type='checkbox' value='1048576' id='clean_indexint' name='clean_indexint' ".(isset($clean_indexint) && $clean_indexint == "1048576" ? "checked" :"").">&nbsp;<label for='clean_indexint'>".htmlentities($msg["nettoyage_clean_indexint"], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				<input type='checkbox' value='512' id='clean_notices' name='clean_notices' ".(isset($clean_notices) && $clean_notices == "512" ? "checked" :"").">&nbsp;<label for='clean_notices'>".htmlentities($msg["nettoyage_clean_expl"], ENT_QUOTES, $charset)."</label>
			</div>";

		// Nettoyer
		$form_proceedings .= "
			<br />
			<h3>".$msg['nettoyage_operations_clean']."</h3>
			<div class='row'>
				<input type='hidden' value='256' name='clean_relations' />
				<input type='checkbox' value='256' name='clean_relationschk' checked disabled='disabled'/>&nbsp;<label for='clean_relations'>".htmlentities($msg["nettoyage_clean_relations"], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				<input type='checkbox' value='4096' id='nettoyage_clean_tags' name='nettoyage_clean_tags' ".(isset($nettoyage_clean_tags) && $nettoyage_clean_tags == "4096" ? "checked" :"").">&nbsp;<label for='nettoyage_clean_tags'>".htmlentities($msg["nettoyage_clean_tags"], ENT_QUOTES, $charset)."</label>
			</div>";
		if (thumbnail::is_valid_folder('record') && pmb_mysql_num_rows(pmb_mysql_query("select notice_id from notices where thumbnail_url like 'data:image%'"))) {
			$form_proceedings .= "
				<div class='row'>
					<input type='checkbox' value='2147483648' name='clean_records_thumbnail' id='clean_records_thumbnail' ".(isset($clean_records_thumbnail) && $clean_records_thumbnail == "2147483648" ? "checked" :"").">&nbsp;<label for='clean_records_thumbnail' class='etiquette'>".htmlentities($msg["clean_records_thumbnail"], ENT_QUOTES, $charset)."</label>
				</div>";
		}
		
		// Générer
		$form_proceedings .= "
			<br />
			<h3>".$msg['nettoyage_operations_generate']."</h3>
			<div class='row'>
				<input type='checkbox' value='2048' id='gen_signature_notice' name='gen_signature_notice' ".(isset($gen_signature_notice) && $gen_signature_notice == "2048" ? "checked" :"").">&nbsp;<label for='gen_signature_notice'>".htmlentities($msg["gen_signature_notice"], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				<input type='checkbox' value='2097152' id='gen_phonetique' name='gen_phonetique' ".(isset($gen_phonetique) && $gen_phonetique == "2097152" ? "checked" :"").">&nbsp;<label for='gen_phonetique'>".htmlentities($msg["gen_phonetique"], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				<input type='checkbox' value='8192' id='clean_categories_path' name='clean_categories_path' ".(isset($clean_categories_path) && $clean_categories_path == "8192" ? "checked" :"").">&nbsp;<label for='clean_categories_path'>".htmlentities($msg["clean_categories_path"], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				<input type='checkbox' value='16384' id='gen_date_publication_article' name='gen_date_publication_article' ".(isset($gen_date_publication_article) && $gen_date_publication_article == "16384" ? "checked" :"").">&nbsp;<label for='gen_date_publication_article'>".htmlentities($msg["gen_date_publication_article"], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				<input type='checkbox' value='32768' id='gen_date_tri' name='gen_date_tri' ".(isset($gen_date_tri) && $gen_date_tri == "32768" ? "checked" :"").">&nbsp;<label for='gen_date_tri'>".htmlentities($msg["gen_date_tri"], ENT_QUOTES, $charset)."</label>
			</div>";
		if ($pmb_explnum_controle_doublons) {
			$form_proceedings .= "
				<div class='row'>
					<input type='checkbox' value='536870912' name='gen_signature_docnum' id='gen_signature_docnum' ".(isset($gen_signature_docnum) && $gen_signature_docnum == "536870912" ? "checked" :"").">&nbsp;<label for='gen_signature_docnum'>".htmlentities($msg["gen_signature_docnum"], ENT_QUOTES, $charset)."</label>
				</div>";
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("show columns from aut_link like 'id_aut_link'")) == 0) {
		  $form_proceedings .= "
				<div class='row'>
					<input type='checkbox' value='4294967296' name='gen_aut_link' id='gen_aut_link' ".(isset($gen_aut_link) && $gen_aut_link == "4294967296" ? "checked" :"").">&nbsp;<label for='gen_aut_link'>".htmlentities($msg["gen_aut_link"], ENT_QUOTES, $charset)."</label>
				</div>";
		}
		// Vider
		$form_proceedings .= "
			<br />
			<h3>".$msg['nettoyage_operations_empty']."</h3>
			<div class='row'>
				<input type='checkbox' value='131072' id='clean_opac_search_cache' name='clean_opac_search_cache' ".(isset($clean_opac_search_cache) && $clean_opac_search_cache == "131072" ? "checked" :"").">&nbsp;<label for='clean_opac_search_cache'>".htmlentities($msg["clean_opac_search_cache"], ENT_QUOTES, $charset)."</label>
			</div>";
		if($pmb_gestion_financiere && $pmb_gestion_amende){
			$form_proceedings .= "
				<div class='row'>
					<input type='checkbox' value='262144' id='clean_cache_amende' name='clean_cache_amende' ".(isset($clean_cache_amende) && $clean_cache_amende == "262144" ? "checked" :"").">&nbsp;<label for='clean_cache_amende'>".htmlentities($msg["clean_cache_amende"], ENT_QUOTES, $charset)."</label>
				</div>";
		}
		$form_proceedings .= "
			<div class='row'>
				<input type='checkbox' value='8589934592' id='clean_cache_amende' name='clean_cache_temporary_files' ".(isset($clean_cache_temporary_files) && $clean_cache_temporary_files == "8589934592" ? "checked" :"").">&nbsp;<label for='clean_cache_temporary_files'>".htmlentities($msg["clean_cache_temporary_files"], ENT_QUOTES, $charset)."</label>
			</div>";

		// Mot de passe
		$form_proceedings .= "
			<br />
			<h3>".$msg['nettoyage_operations_password']."</h3>
			<div class='row'>
				<input type='checkbox' value='134217728' id='hash_empr_password' name='hash_empr_password' ".(isset($hash_empr_password) && $hash_empr_password == "134217728" ? "checked" :"").">&nbsp;<label for='hash_empr_password'>".htmlentities($msg["hash_empr_password"], ENT_QUOTES, $charset)."</label>
			</div>";
		if (pmb_mysql_num_rows(pmb_mysql_query("show tables like 'empr_passwords'"))) {
			$form_proceedings .= "
				<div class='row'>
					<input type='checkbox' value='1073741824' name='delete_empr_passwords' id='delete_empr_passwords' ".(isset($delete_empr_passwords) && $delete_empr_passwords == "1073741824" ? "checked" :"").">&nbsp;<label for='delete_empr_passwords' class='etiquette'>".htmlentities($msg["delete_empr_passwords"], ENT_QUOTES, $charset)."</label>
				</div>";
		}
		return $form_proceedings;
	}

	/**
	 * affichage du % d'avancement et de l'état
	 * @param number $start
	 * @param number $count
	 */
	public static function get_display_progress($start=0, $count=0) {
		// taille de la jauge pour affichage
		$jauge_size = GAUGE_SIZE;
		$jauge_size .= "px";

		// définition de l'état de la jauge
		$state = floor($start / ($count / $jauge_size));
		$state .= "px";
		// mise à jour de l'affichage de la jauge
		$display = "<table border='0' class='' style='width:".$jauge_size."' cellpadding='0'><tr><td class='jauge' style='width:100%'>";
		$display .= "<div class='jauge'><img src='".get_url_icon('jauge.png')."' style='height:16px; width:".$state."'></div></td></tr></table>";

		// calcul pourcentage avancement
		$percent = floor(($start/$count)*100);

		// affichage du % d'avancement et de l'état
		$display .= "<div class='center'>$percent%</div>";
		return $display;
	}

	public static function get_display_final_progress() {
		global $table_size;

		// taille de la jauge pour affichage
		$jauge_size = GAUGE_SIZE;
		$jauge_size .= "px";

		$display = "
			<table border='0' class='' style='width:".$table_size."' cellpadding='0'>
				<tr>
					<td class='jauge'>
						<img src='".get_url_icon('jauge.png')."' width='$jauge_size' height='16'>
					</td>
				</tr>
			</table>
			<div class='center'>100%</div>";
		return $display;
	}

	public static function get_current_state_form($v_state, $spec, $index_quoi='', $next=0, $count=0) {
		global $current_module;
		$form = "
			<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
				<input type='hidden' name='spec' value=\"$spec\">
				<input type='hidden' name='start' value=\"$next\">
				<input type='hidden' name='count' value=\"$count\">
				<input type='hidden' name='index_quoi' value=\"".$index_quoi."\">
			</form>
			<script type=\"text/javascript\"><!--
				setTimeout(\"document.forms['current_state'].submit()\",1000);
			-->
			</script>";
		return $form;
	}

	public static function get_process_state_form($v_state, $spec, $affected='', $pass='') {
		global $current_module;
		$form = "
		<form class='form-$current_module' name='process_state' action='./clean.php' method='post'>
			<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
			<input type='hidden' name='spec' value=\"$spec\">";
		if($affected != '') $form .= "<input type='hidden' name='affected' value=\"$affected\">";
		if($pass != '') $form .= "<input type='hidden' name='pass2' value=\"".$pass."\">";
		$form .= "
		</form>
		<script type=\"text/javascript\"><!--
			document.forms['process_state'].submit();
			-->
		</script>";
		return $form;
	}
} // fin de déclaration de la classe netbase
