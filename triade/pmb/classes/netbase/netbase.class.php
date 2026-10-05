<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: netbase.class.php,v 1.35.2.4.2.1 2025/03/04 16:43:11 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
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
define('INDEX_DATE_FLOT'				, 17179869184);
define('CLEAN_CACHE_APCU'				, 34359738368);
define('CLEAN_ENTITIES_DATA'			, 68719476736);
define('CLEAN_DOCNUM_THUMBNAIL'			, 137438953472);
define('GEN_ARK'			            , 274877906944);
define('CLEAN_AUTOLOAD_FILES'           , 549755813888);
define('GEN_DOCNUM_THUMBNAIL'           , (2**40));
define('AI_INDEXATION'                  , (2**41));
define('INDEX_SPHINX_RECORDS'			, (2**42));
define('INDEX_SPHINX_AUTHORITIES'		, (2**43));
define('INDEX_SPHINX_CONCEPTS'		    , (2**44));

class netbase {

	protected static $labels_proceedings;

	protected static $executime_time_ratio;

	protected static $controller_url_base = './clean.php';

	public function __construct() {

	}

	public static function proceed() {

	}

	public static function get_label_proceeding($spec) {
		global $msg;

		if(empty(static::$labels_proceedings)) {
			static::$labels_proceedings = array(
					INDEX_GLOBAL => $msg['nettoyage_index_global'],
					INDEX_NOTICES => $msg['nettoyage_index_notices'],
					CLEAN_AUTHORS => $msg['nettoyage_clean_authors'],
					CLEAN_PUBLISHERS => $msg['nettoyage_clean_editeurs'],
					CLEAN_COLLECTIONS => $msg['nettoyage_clean_collections'],
					CLEAN_SUBCOLLECTIONS => $msg['nettoyage_clean_subcollections'],
					CLEAN_CATEGORIES => $msg['nettoyage_clean_categories'],
					CLEAN_SERIES => $msg['nettoyage_clean_series'],
					CLEAN_TITRES_UNIFORMES => $msg['nettoyage_clean_titres_uniformes'],
					CLEAN_INDEXINT => $msg['nettoyage_clean_indexint'],
					CLEAN_RELATIONS => $msg["nettoyage_clean_relations"],
					CLEAN_NOTICES => $msg['nettoyage_clean_expl'],
					INDEX_ACQUISITIONS => $msg['nettoyage_reindex_acq'],
					GEN_SIGNATURE_NOTICE => $msg['gen_signature_notice'],
					GEN_SIGNATURE_DOCNUM => $msg['gen_signature_docnum'],
					GEN_PHONETIQUE => $msg['gen_phonetique'],
					NETTOYAGE_CLEAN_TAGS => $msg['nettoyage_clean_tags'],
					CLEAN_CATEGORIES_PATH => $msg['clean_categories_path'],
					GEN_DATE_PUBLICATION_ARTICLE => $msg['gen_date_publication_article'],
					GEN_DATE_TRI => $msg['gen_date_tri'],
					INDEX_DOCNUM => $msg['docnum_reindexer'],
					INDEX_RDFSTORE => $msg["nettoyage_rdfstore_reindex"],
					INDEX_SYNCHRORDFSTORE => $msg["nettoyage_synchrordfstore_reindex"],
					INDEX_FAQ => $msg["nettoyage_faq_reindex"],
					INDEX_CMS => $msg["nettoyage_cms_reindex"],
					INDEX_CONCEPT => $msg["nettoyage_concept_reindex"],
					HASH_EMPR_PASSWORD => $msg['hash_empr_password'],
					DELETE_EMPR_PASSWORDS => $msg["delete_empr_passwords"],
					INDEX_AUTHORITIES => $msg['nettoyage_index_authorities'],
					GEN_ARK => $msg['ark_netbase_generate'],
					INDEX_DATE_FLOT => $msg['nettoyage_index_date_flot'],
					CLEAN_RECORDS_THUMBNAIL => $msg['clean_records_thumbnail'],
					CLEAN_OPAC_SEARCH_CACHE => $msg["clean_opac_search_cache"],
					CLEAN_CACHE_AMENDE => $msg["clean_cache_amende"],
					CLEAN_CACHE_TEMPORARY_FILES => $msg["clean_cache_temporary_files"],
					CLEAN_CACHE_APCU => $msg["clean_cache_apcu"],
					CLEAN_AUTOLOAD_FILES => $msg["clean_autoload_files"],
					GEN_DOCNUM_THUMBNAIL => $msg["gen_docnum_thumbnail"],
					AI_INDEXATION => $msg["admin_netbase_ai_indexation"],
                    INDEX_SPHINX_RECORDS => $msg['nettoyage_index_sphinx_records'],
                    INDEX_SPHINX_AUTHORITIES => $msg['nettoyage_index_sphinx_authorities'],
                    INDEX_SPHINX_CONCEPTS => $msg['nettoyage_index_sphinx_concepts'],
				);
			}
		return static::$labels_proceedings[$spec];
	}

	public function get_js_form_proceedings() {
	    return "<script>
            function netbase_proceeding_dependency(node_id, dependency_node_id) {
                let node = document.getElementById(node_id);
                let dependency_node = document.getElementById(dependency_node_id);
                if (node && dependency_node) {
                    if(node.checked) {
                        dependency_node.checked = true;
                    } else {
                        dependency_node.checked = false;
                    }
                }
            }
        </script>";
	}

	protected function get_proceeding_checkbox_content_form($spec, $name, $value='', $dependency=array()) {
	    global $charset;

	    $onchange = '';
	    if (!empty($dependency)) {
	        $onchange = "netbase_proceeding_dependency('".$name."', '".$dependency['name']."');";
	    }
	    return "<input type='checkbox' value='".$spec."' id='".$name."' name='".$name."' ".(isset($value) && $value == $spec ? "checked" :"")." ".(!empty($onchange) ? "onchange=\"".$onchange."\"" : "").">&nbsp;<label for='".$name."' >".htmlentities($this->get_label_proceeding($spec), ENT_QUOTES, $charset)."</label>";
	}

	protected function get_proceeding_content_form($spec, $name, $value='', $dependency=array()) {
		$content_form = "
		<div class='row'>
            ".$this->get_proceeding_checkbox_content_form($spec, $name, $value, $dependency)."
		</div>";
		if (!empty($dependency)) {
		    $content_form .= "<div class='row'>";
		    $content_form .= "<span style='font-size:1.5em'>&#10551;</span>";
		    $content_form .= $this->get_proceeding_checkbox_content_form($dependency['spec'], $dependency['name'], $dependency['value']);
		    $content_form .= "</div>";
		}
		return $content_form;
	}

	public function get_form_proceedings($proceedings=array()) {
		global $msg, $charset, $acquisition_active, $pmb_indexation_docnum;
		global $pmb_gestion_financiere, $pmb_gestion_amende;
		global $pmb_synchro_rdf;
		global $faq_active, $cms_active;
		global $thesaurus_concepts_active;
		global $pmb_explnum_controle_doublons;
		global $pmb_ark_activate;
		global $CACHE_ENGINE;
		global $pmb_docnum_img_folder_id, $ai_active;
		global $pmb_clean_mode, $sphinx_active;

		if ($proceedings) {
			foreach ($proceedings as $name=>$value) {
				${$name} = $value;
			}
		}
		//Gestion des dépendances
		if ($pmb_clean_mode && $sphinx_active) {
		    if(!empty($proceedings['index_global'])) {
		        $proceedings['index_sphinx_records'] = INDEX_SPHINX_RECORDS;
		    }
		    if(!empty($proceedings['index_concept'])) {
		        $proceedings['index_sphinx_concepts'] = INDEX_SPHINX_CONCEPTS;
		    }
		    if(!empty($proceedings['index_authorities'])) {
		        $proceedings['index_sphinx_authorities'] = INDEX_SPHINX_AUTHORITIES;
		    }
		}

		$form_proceedings = $this->get_js_form_proceedings();
		// Réindexer
		$form_proceedings .= "<h3>".$msg['nettoyage_operations_reindex']."</h3>";

		$dependency_index_global = array();
		if ($pmb_clean_mode && $sphinx_active) {
		    $dependency_index_global = array(
		        'spec' => INDEX_SPHINX_RECORDS,
		        'name'=> 'index_sphinx_records',
		        'value' => $proceedings['index_sphinx_records'] ?? ''
		    );
		}
		$form_proceedings .= $this->get_proceeding_content_form(INDEX_GLOBAL, 'index_global', ($proceedings['index_global'] ?? ''), $dependency_index_global);

		$form_proceedings .= $this->get_proceeding_content_form(INDEX_NOTICES, 'index_notices', ($proceedings['index_notices'] ?? ''));
		if ($acquisition_active) {
			$form_proceedings .= $this->get_proceeding_content_form(INDEX_ACQUISITIONS, 'index_acquisitions', ($proceedings['index_acquisitions'] ?? ''));
		}
		if($pmb_indexation_docnum){
			$form_proceedings .= $this->get_proceeding_content_form(INDEX_DOCNUM, 'reindex_docnum', ($proceedings['reindex_docnum'] ?? ''));
		}
		$form_proceedings .= $this->get_proceeding_content_form(INDEX_RDFSTORE, 'index_rdfstore', ($proceedings['index_rdfstore'] ?? ''));
		if($pmb_synchro_rdf){
			$form_proceedings .= $this->get_proceeding_content_form(INDEX_SYNCHRORDFSTORE, 'index_synchrordfstore', ($proceedings['index_synchrordfstore'] ?? ''));
		}
		if($faq_active){
			$form_proceedings .= $this->get_proceeding_content_form(INDEX_FAQ, 'index_faq', ($proceedings['index_faq'] ?? ''));
		}
		if($cms_active){
			$form_proceedings .= $this->get_proceeding_content_form(INDEX_CMS, 'index_cms', ($proceedings['index_cms'] ?? ''));
		}
		if($thesaurus_concepts_active==1){
		    $dependency_index_concept = array();
		    if ($pmb_clean_mode && $sphinx_active) {
		        $dependency_index_concept = array(
		            'spec' => INDEX_SPHINX_CONCEPTS,
		            'name'=> 'index_sphinx_concepts',
		            'value' => $proceedings['index_sphinx_concepts'] ?? ''
		        );
		    }
		    $form_proceedings .= $this->get_proceeding_content_form(INDEX_CONCEPT, 'index_concept', ($proceedings['index_concept'] ?? ''), $dependency_index_concept);
		}
		$dependency_index_authorities = array();
		if ($pmb_clean_mode && $sphinx_active) {
		    $dependency_index_authorities = array(
		        'spec' => INDEX_SPHINX_AUTHORITIES,
		        'name'=> 'index_sphinx_authorities',
		        'value' => $proceedings['index_sphinx_authorities'] ?? ''
		    );
		}
		$form_proceedings .= $this->get_proceeding_content_form(INDEX_AUTHORITIES, 'index_authorities', ($proceedings['index_authorities'] ?? ''), $dependency_index_authorities);
		$form_proceedings .= $this->get_proceeding_content_form(INDEX_DATE_FLOT, 'index_date_flot', ($proceedings['index_date_flot'] ?? ''));

		if ($ai_active) {
			$form_proceedings .= $this->get_proceeding_content_form(AI_INDEXATION, 'ai_indexation', ($proceedings['ai_indexation'] ?? ''));
		}

		// Supprimer
		$form_proceedings .= "
			<br />
			<h3>".$msg['nettoyage_operations_delete']."</h3>";
		$form_proceedings .= $this->get_proceeding_content_form(CLEAN_AUTHORS, 'clean_authors', ($proceedings['clean_authors'] ?? ''));
		$form_proceedings .= $this->get_proceeding_content_form(CLEAN_PUBLISHERS, 'clean_editeurs', ($proceedings['clean_editeurs'] ?? ''));
		$form_proceedings .= $this->get_proceeding_content_form(CLEAN_COLLECTIONS, 'clean_collections', ($proceedings['clean_collections'] ?? ''));
		$form_proceedings .= $this->get_proceeding_content_form(CLEAN_SUBCOLLECTIONS, 'clean_subcollections', ($proceedings['clean_subcollections'] ?? ''));
		$form_proceedings .= $this->get_proceeding_content_form(CLEAN_CATEGORIES, 'clean_categories', ($proceedings['clean_categories'] ?? ''));
		$form_proceedings .= $this->get_proceeding_content_form(CLEAN_SERIES, 'clean_series', ($proceedings['clean_series'] ?? ''));
		$form_proceedings .= $this->get_proceeding_content_form(CLEAN_TITRES_UNIFORMES, 'clean_titres_uniformes', ($proceedings['clean_titres_uniformes'] ?? ''));
		$form_proceedings .= $this->get_proceeding_content_form(CLEAN_INDEXINT, 'clean_indexint', ($proceedings['clean_indexint'] ?? ''));
		$form_proceedings .= $this->get_proceeding_content_form(CLEAN_NOTICES, 'clean_notices', ($proceedings['clean_notices'] ?? ''));

		// Nettoyer
		$form_proceedings .= "
			<br />
			<h3>".$msg['nettoyage_operations_clean']."</h3>
			<div class='row'>
				<input type='hidden' value='256' name='clean_relations' />
				<input type='checkbox' value='256' name='clean_relationschk' checked disabled='disabled'/>&nbsp;<label for='clean_relations'>".htmlentities($msg["nettoyage_clean_relations"], ENT_QUOTES, $charset)."</label>
			</div>
			".$this->get_proceeding_content_form(NETTOYAGE_CLEAN_TAGS, 'nettoyage_clean_tags', ($proceedings['nettoyage_clean_tags'] ?? ''))."
			";
		if (thumbnail::is_valid_folder('record') && pmb_mysql_num_rows(pmb_mysql_query("select notice_id from notices where thumbnail_url like 'data:image%'"))) {
			$form_proceedings .= $this->get_proceeding_content_form(CLEAN_RECORDS_THUMBNAIL, 'clean_records_thumbnail', ($proceedings['clean_records_thumbnail'] ?? ''));
		}
		if ($pmb_docnum_img_folder_id) {
		    $res_size = pmb_mysql_query("SELECT SUM(length(explnum_vignette)) AS size FROM explnum WHERE length(explnum_vignette) > 1000");
		    if (pmb_mysql_num_rows($res_size)) {
		        $row_size = pmb_mysql_fetch_assoc($res_size);
		        if ($row_size["size"]) {
    		        $free_space = 0;
    		        $query_rep = "select repertoire_path from upload_repertoire where repertoire_id ='".thumbnail::get_parameter_img_folder_id("docnum")."'";
    		        $result_rep = pmb_mysql_query($query_rep);
    		        if(pmb_mysql_num_rows($result_rep)){
    		            $row_rep=pmb_mysql_fetch_assoc($result_rep);
    		            $free_space = disk_free_space($row_rep["repertoire_path"]);
    		        }
    		        $form_proceedings .= "
    				<div class='row'>
    					<input
                            type='checkbox'
                            value='".CLEAN_DOCNUM_THUMBNAIL."'
                            name='clean_docnum_thumbnail'
                            id='clean_docnum_thumbnail' ".(isset($clean_docnum_thumbnail) && $clean_docnum_thumbnail == CLEAN_DOCNUM_THUMBNAIL ? "checked" :"")."
                            ".(($row_size["size"] > $free_space) && ($free_space !== false) ? "disabled='disabled'" : "")."
                        >&nbsp;<label for='clean_docnum_thumbnail' class='etiquette'>
                            ".htmlentities($msg["clean_docnum_thumbnail"], ENT_QUOTES, $charset)." (~".round($row_size["size"] / 1000000)."mo)
                        </label>
                        ".(($row_size["size"] > $free_space) && ($free_space !== false) ? "<span class='erreur'>".$msg["clean_docnum_thumbnail_error"]."</span>" : "")."
    				</div>";
		        }
		    }
		}
		$form_proceedings .= "
				<div class='row'>
					<input type='checkbox' value='68719476736' name='clean_entities_data' id='clean_entities_data' ".(isset($clean_entities_data) && $clean_entities_data == "68719476736" ? "checked" :"").">&nbsp;<label for='clean_entities_data' class='etiquette'>".htmlentities($msg["clean_entities_data"], ENT_QUOTES, $charset)."</label>
				</div>";

		// Générer
		$form_proceedings .= "
			<br />
			<h3>".$msg['nettoyage_operations_generate']."</h3>";
		$form_proceedings .= $this->get_proceeding_content_form(GEN_SIGNATURE_NOTICE, 'gen_signature_notice', ($proceedings['gen_signature_notice'] ?? ''));
		$form_proceedings .= $this->get_proceeding_content_form(GEN_PHONETIQUE, 'gen_phonetique', ($proceedings['gen_phonetique'] ?? ''));
		$form_proceedings .= $this->get_proceeding_content_form(CLEAN_CATEGORIES_PATH, 'clean_categories_path', ($proceedings['clean_categories_path'] ?? ''));
		$form_proceedings .= $this->get_proceeding_content_form(GEN_DATE_PUBLICATION_ARTICLE, 'gen_date_publication_article', ($proceedings['gen_date_publication_article'] ?? ''));
		$form_proceedings .= $this->get_proceeding_content_form(GEN_DATE_TRI, 'gen_date_tri', ($proceedings['gen_date_tri'] ?? ''));
		if ($pmb_explnum_controle_doublons) {
			$form_proceedings .= $this->get_proceeding_content_form(GEN_SIGNATURE_DOCNUM, 'gen_signature_docnum', ($proceedings['gen_signature_docnum'] ?? ''));
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("show columns from aut_link like 'id_aut_link'")) == 0) {
		  $form_proceedings .= "
				<div class='row'>
					<input type='checkbox' value='4294967296' name='gen_aut_link' id='gen_aut_link' ".(isset($gen_aut_link) && $gen_aut_link == "4294967296" ? "checked" :"").">&nbsp;<label for='gen_aut_link'>".htmlentities($msg["gen_aut_link"], ENT_QUOTES, $charset)."</label>
				</div>";
		}
		if ($pmb_ark_activate) {
			$form_proceedings .= $this->get_proceeding_content_form(GEN_ARK, 'gen_ark', ($proceedings['gen_ark'] ?? ''));
		}
		$form_proceedings .= $this->get_proceeding_content_form(GEN_DOCNUM_THUMBNAIL, 'gen_docnum_thumbnail', ($proceedings['gen_docnum_thumbnail'] ?? ''));

		// Vider
		$form_proceedings .= "
			<br />
			<h3>".$msg['nettoyage_operations_empty']."</h3>";
		$form_proceedings .= $this->get_proceeding_content_form(CLEAN_OPAC_SEARCH_CACHE, 'clean_opac_search_cache', ($proceedings['clean_opac_search_cache'] ?? ''));
		if($pmb_gestion_financiere && $pmb_gestion_amende){
			$form_proceedings .= $this->get_proceeding_content_form(CLEAN_CACHE_AMENDE, 'clean_cache_amende', ($proceedings['clean_cache_amende'] ?? ''));
		}
		$form_proceedings .= $this->get_proceeding_content_form(CLEAN_CACHE_TEMPORARY_FILES, 'clean_cache_temporary_files', ($proceedings['clean_cache_temporary_files'] ?? ''));
		//Cache APCU activé ?
		if(($CACHE_ENGINE == 'apcu') && extension_loaded('apcu') && ini_get('apc.enabled')){
			$form_proceedings .= $this->get_proceeding_content_form(CLEAN_CACHE_APCU, 'clean_cache_apcu', ($proceedings['clean_cache_apcu'] ?? ''));
		}
		//autoload
		$form_proceedings .= $this->get_proceeding_content_form(CLEAN_AUTOLOAD_FILES, 'clean_autoload_files', ($proceedings['clean_autoload_files'] ?? ''));

		// Mot de passe
		$form_proceedings .= "
			<br />
			<h3>".$msg['nettoyage_operations_password']."</h3>";
		$form_proceedings .= $this->get_proceeding_content_form(HASH_EMPR_PASSWORD, 'hash_empr_password', ($proceedings['hash_empr_password'] ?? ''));
		if (pmb_mysql_num_rows(pmb_mysql_query("show tables like 'empr_passwords'"))) {
			$form_proceedings .= $this->get_proceeding_content_form(DELETE_EMPR_PASSWORDS, 'delete_empr_passwords', ($proceedings['delete_empr_passwords'] ?? ''));
		}
		return $form_proceedings;
	}

	public static function get_display_progress_title($title) {
	    global $charset;

	    return "<br /><br /><h2 class='center'>".htmlentities($title, ENT_QUOTES, $charset)."</h2>";
	}

	public static function get_display_progress_v_state($title, $content='') {
	    global $charset;

	    $v_state = "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($title, ENT_QUOTES, $charset);
	    if ($content) {
	        $v_state .= " : ".htmlentities($content, ENT_QUOTES, $charset);
	    }
        return $v_state;
	}

	public static function get_display_progress_subtitle($title) {
	    global $charset;

	    return "<div class='row center'>".htmlentities($title, ENT_QUOTES, $charset)."</div>";
	}

	/**
	 * affichage du % d'avancement et de l'état
	 * @param number $start
	 * @param number $count
	 */
	public static function get_display_progress($start=0, $count=0) {
		// calcul pourcentage avancement
		if(!empty($count)) {
			$percent = floor(($start/$count)*100);
		} else {
			$percent = 100;
		}
		return static::build_display_progress($percent);
	}

	/**
	 * Permet de faire un affichage du % d'avancement
	 *
	 * @param int|float $percent
	 * @return string
	 */
	public static function build_display_progress($percent) {
		$percent = floor($percent);

		return "
			<div class='row center jauge'>
				<progress id='file' max='100' value='".$percent."' style='width:100%'> ".$percent."% </progress>
			</div>
			<div class='center'>$percent%</div>";
	}

	public static function get_display_final_progress() {
		return static::get_display_progress();
	}

	public static function get_current_state_form($v_state, $spec, $index_quoi='', $next=0, $count=0, $pass='', $step_position=0) {
		global $current_module;
		$form = "
			<form class='form-$current_module' name='current_state' action='".static::$controller_url_base."' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
				<input type='hidden' name='spec' value=\"$spec\">
				<input type='hidden' name='start' value=\"$next\">
				<input type='hidden' name='count' value=\"$count\">
				<input type='hidden' name='index_quoi' value=\"".$index_quoi."\">
				".($pass != '' ? "<input type='hidden' name='pass2' value=\"".$pass."\">" : "")."
                <input type='hidden' name='step_position' value=\"".$step_position."\">
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
		<form class='form-$current_module' name='process_state' action='".static::$controller_url_base."' method='post'>
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

	public static function get_execution_time_ratio($spec) {
		if(empty(static::$executime_time_ratio)) {
			static::$executime_time_ratio = array(
					INDEX_GLOBAL => 95,
					INDEX_NOTICES => 95,
					CLEAN_AUTHORS => 5,
					CLEAN_PUBLISHERS => 5,
					CLEAN_COLLECTIONS => 5,
					CLEAN_SUBCOLLECTIONS => 5,
					CLEAN_CATEGORIES => 5,
					CLEAN_SERIES => 5,
					CLEAN_TITRES_UNIFORMES => 5,
					CLEAN_INDEXINT => 5,
					CLEAN_RELATIONS => 5,
					CLEAN_NOTICES => 5,
					INDEX_ACQUISITIONS => 10,
					GEN_SIGNATURE_NOTICE => 10,
					GEN_SIGNATURE_DOCNUM => 10,
					GEN_PHONETIQUE => 10,
					NETTOYAGE_CLEAN_TAGS => 5,
					CLEAN_CATEGORIES_PATH => 5,
					GEN_DATE_PUBLICATION_ARTICLE => 10,
					GEN_DATE_TRI => 10,
					INDEX_DOCNUM => 75,
					INDEX_RDFSTORE => 15,
					INDEX_SYNCHRORDFSTORE => 15,
					INDEX_FAQ => 15,
					INDEX_CMS => 25,
					INDEX_CONCEPT => 25,
					HASH_EMPR_PASSWORD => 5,
					DELETE_EMPR_PASSWORDS => 5,
					INDEX_AUTHORITIES => 95,
					GEN_ARK => 10,
					INDEX_DATE_FLOT => 15,
					CLEAN_RECORDS_THUMBNAIL => 5,
					CLEAN_OPAC_SEARCH_CACHE => 5,
					CLEAN_CACHE_AMENDE => 5,
					CLEAN_CACHE_TEMPORARY_FILES => 5,
					CLEAN_CACHE_APCU => 5,
					CLEAN_AUTOLOAD_FILES => 5,
					GEN_DOCNUM_THUMBNAIL => 10,
					AI_INDEXATION => 10,
                    INDEX_SPHINX_RECORDS => 65,
                    INDEX_SPHINX_AUTHORITIES => 65,
                    INDEX_SPHINX_CONCEPTS => 65
			);
		}
		return static::$executime_time_ratio[$spec];
	}

	public static function set_controller_url_base($controller_url_base) {
	    static::$controller_url_base = $controller_url_base;
	}
}

