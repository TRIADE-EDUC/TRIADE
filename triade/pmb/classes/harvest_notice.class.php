<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: harvest_notice.class.php,v 1.12.4.1 2025/04/30 09:49:41 qvarin Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Harvest\Models\HarvestModel;

global $base_path, $class_path, $include_path;

require_once ($include_path . "/templates/harvest_notice.tpl.php");
//pour récup les infos de notice
require_once ($base_path . "/admin/convert/export.class.php");
require_once ($class_path . "/export_param.class.php");

require_once ($class_path . "/harvest.class.php");
require_once ($class_path . "/harvest_profil_import.class.php");
require_once ($base_path . "/admin/convert/xml_unimarc.class.php");
require_once ($class_path . "/z3950_notice.class.php");
require_once ("$include_path/isbn.inc.php");

//require_once($base_path."/admin/import/func_customfields.inc.php");
class harvest_notice
{

	/* Id notice */
	protected $notice_id = 0;

	/* Notice format tableau unimarc */
	protected $unimarc_notice = [];

	/* Notices externes */
	protected $external_notices = [];

	/* Champs a ajouter */
	protected $new_fields = [];

	protected $new_field_source = 0;

	protected $new_field_order = 0;

	protected $new_subfield_order = 0;

	protected $new_field_index = 0;

	protected $new_field_has_value = [];

	/* Notice finale format tableau unimarc */
	protected $final_unimarc_notice = [];

	/* Id recolteur */
	protected $harvest_id = 0;

	/* modele recolteur */
	protected $harvest_model = null;

	/* profil recolteur */
	protected $harvest_profile = null;

	protected $first_flags = [];

	protected $prec_flags = [];

	protected $repeatable = [];

	protected $noticeCustomFields = [];

	/* modele import */
	protected $harvest_import_model = null;

	/* Id profil d'import */
	protected $harvest_import_id = 0;

	/* objet profil d'import */
	protected $harvest_import_profile = null;

	/* Recherches externes */
	protected $search_queries = [];

	/* Resultat des recherches */
	protected $search_results = [];

	/* Formulaire z3950 notice */
	protected $z3950_notice_form = '';

	public function __construct($notice_id = 0, $harvest_id = 0, $harvest_import_id = 0)
	{
		$this->notice_id = intval($notice_id);
		$this->harvest_id = intval($harvest_id);
		$this->harvest_import_id = intval($harvest_import_id);
		if (!$this->notice_id || !$this->harvest_id || !$this->harvest_import_id) {
			return false;
		}
	}


	/**
	 * Transfo de la notice a modifier en format tableau unimarc
	 *
	 * @return array
	 */
	protected function getUnimarcNotice()
	{
		if (!$this->notice_id) {
			return;
		}
		//Recupere les parametres d'exports
		$export_param = new export_param();
		$param = $export_param->get_parametres($export_param->context);
		foreach ($param as $key => $value) {
			$param[str_replace("export_", "", $key)] = $value;
		}
		$export = new export(array($this->notice_id), [], []);
		$export->get_next_notice("", [], [], false, $param);
		$this->unimarc_notice = $export->xml_array;
		return $this->unimarc_notice;
	}


	/**
	 * Lancement moissonnage
	 */
	public function runHarvest()
	{
		if (!$this->notice_id) {
			return;
		}

		//Verifie que la notice existe
		$q = "select notice_id from notices where notice_id = " . $this->notice_id;
		$r = pmb_mysql_query($q);
		if (!pmb_mysql_num_rows($r)) {
			return;
		}

		//Recupere la notice a amender en format tableau unimarc
		$this->getUnimarcNotice();
		// var_dump($this->unimarc_notice);

		//Recupere l'objet moissonneur
		$this->getHarvestProfile();
		//var_dump($this->harvest_profile['groups']);
		//var_dump($this->first_flags);
		//var_dump($this->prec_flags);
		//var_dump($this->repeatable);

		//Recupere le profil d'import
		$this->getImportProfile();
		//var_dump($this->harvest_import_profile);

		//Construit les recherches a executer
		$this->buildSearchQueries();
		//var_dump($this->search_queries);

		//Lancement des recherches
		$this->runSearchQueries();
		//var_dump($this->external_notices);

		//$this->hack();

		//Merge notices externes
		$this->mergeNotices();
		//var_dump($this->final_unimarc_notice);

		//Generation z3950_notice
		$this->buildZ3950Notice();
	}


	/**
	 * Recupere l'objet moissonneur
	 *
	 * @return []
	 */
	protected function getHarvestProfile()
	{
		$this->harvest_model = new HarvestModel($this->harvest_id);
		$this->harvest_profile = $this->harvest_model->getProfile();

		$this->first_flags = [];
		$this->prec_flags = [];
		$this->repeatable = [];
		$this->noticeCustomFields = $this->harvest_model->getNoticeCustomFields();

		foreach ($this->harvest_profile['groups'] as $v_group) {

			$this->repeatable[$v_group['ufield']] = '0';
			if (!empty($v_group['repeatable']) && 'yes' == $v_group['repeatable']) {
				$this->repeatable[$v_group['ufield']] = '1';
			}
			$this->first_flags[$v_group['ufield']] = $v_group['firstFlag'] ?? '0';

			foreach ($v_group['fields'] as $v_field) {
				if (!empty($v_field['source'])) {
					$this->prec_flags[$v_field['source']][$v_field['ufield']] = $v_field['precFlag'] ?? '0';
				}
			}
		}

		return $this->harvest_profile;
	}


	/**
	 * Recupere le profil d'import
	 *
	 * @return []
	 */
	protected function getImportProfile()
	{
		//Recupere le profil d'import
		$this->harvest_import_model = new HarvestModel($this->harvest_import_id);
		$this->harvest_import_profile = $this->harvest_import_model->getImportProfile();

		return $this->harvest_import_profile;
	}

	protected function buildZ3950Notice()
	{
		global $msg, $charset;
		if (!$this->notice_id) {
			return '';
		}
		if (empty($this->final_unimarc_notice)) {
			return '';
		}
		// conversion du tableau en xml
		$export = new export($this->notice_id);
		$export->xml_array = $this->final_unimarc_notice;
		$export->toxml();
		$notice_xml = $export->notice;

		// conversion du xml en unimarc
		$xml_unimarc = new xml_unimarc();
		$xml_unimarc->XMLtoiso2709_notice($notice_xml, $charset);
		$notice = $xml_unimarc->notices_[0];

		$z = new z3950_notice("unimarc", $notice);
		$z->libelle_form = $msg["notice_connecteur_remplace_catal"];

		if ($z->bibliographic_level == "a" && $z->hierarchic_level == "2") { // article
			//$form=$z->get_form("catalog.php?categ=update&id=".$notice_id,$notice_id,'button',true);
		} else {
			$form = $z->get_form("catalog.php?categ=harvest&action=record&notice_id=" . $this->notice_id, $this->notice_id, 'button');
		}

		$form = str_replace("<!--!!form_title!!-->", "<h3>" . sprintf($msg["harvest_notice_build_title"], $this->notice_id, '') . "</h3>", $form);

		$this->z3950_notice_form = $form;
		return $this->z3950_notice_form;
	}

	public function getZ3950NoticeForm()
	{
		return $this->z3950_notice_form;
	}

	public function getFinalNoticeUnimarc()
	{
		return $this->final_unimarc_notice;
	}

	protected function mergeNotices()
	{
		// Copie de la notice unimarc
		$this->final_unimarc_notice = $this->unimarc_notice;

		//On ne garde qu'une seule notice externe par source
		$external_notices = [];
		foreach ($this->external_notices as $source_id => $values) {
			$first_key = array_key_first($values);
			$external_notices[$source_id][0] = $values[$first_key];
		}
		$this->external_notices = $external_notices;
		unset($external_notices);

		foreach ($this->harvest_import_profile['groups'] as $ufield_usubfield => $field_group) {

			if ($this->repeatable[$ufield_usubfield] == '0' && $field_group['flag'] == '2') {
				$field_group['flag'] = '0';
			}

			switch ($field_group['flag']) {

				//Replace
				case '1':
					// var_dump('ufield = '.$ufield_usubfield.' >> replace');
					$this->deleteFieldFromUnimarcNotice($ufield_usubfield);
					$this->formatNewField($ufield_usubfield);
					break;

				//Add
				case '2':
					// var_dump('ufield = '.$ufield_usubfield.' >> add');

					$this->formatNewField($ufield_usubfield);
					break;

				//Ignore
				default:
					// var_dump('ufield = '.$ufield_usubfield. ' >> ignore');
					break;
			}
		}


		$this->addNewFieldsToUnimarcNotice();
	}


	/**
	 * Supprime les champs a remplacer dans la notice unimarc
	 *
	 * @param string $ufield_usubfield : code-champ[$code sous-champ]
	 *
	 */
	protected function deleteFieldFromUnimarcNotice($ufield_usubfield = "")
	{
		if (empty($this->final_unimarc_notice)) {
			return;
		}

		$tmp = explode('$', $ufield_usubfield);
		$ufield = $tmp[0];
		$usubfield = ($tmp[1]) ?? '';

		if ($ufield == 900) {
			return $this->deleteCustomFieldFromUnimarcNotice($ufield_usubfield);
		}

		foreach ($unimarc_notice['f'] as $kf => $current_field) {

			$current_ufield = $current_field['c'];

			if ($ufield != $current_ufield) {
				// Le $current_ufield n'est pas le bon $ufield, on ne le supprime pas
				continue;
			}

			if (empty($usubfield)) {
				// On a pas de sous champ on supprime le champ
				unset($this->final_unimarc_notice['f'][$kf]);
				continue;
			}

			if (!isset($current_field['s'])) {
				continue;
			}

			foreach ($current_field['s'] as $ksf => $current_subfield) {
				$current_usubfield = $current_subfield['c'];
				if ($usubfield == $current_usubfield) {
					unset($this->final_unimarc_notice['f'][$kf]['s'][$ksf]);
				}
			}
		}
	}


	/**
	 * Formate les champs a ajouter a la notice unimarc
	 *
	 * @param string $ufield_usubfield : code-champ[$code sous-champ]
	 *
	 */
	protected function formatNewField($ufield_usubfield = "")
	{
		if (empty($this->unimarc_notice)) {
			return;
		}

		$tmp = explode('$', $ufield_usubfield);
		$ufield = $tmp[0];
		$usubfield = ($tmp[1]) ?? '';
		if ($ufield == 900) {
			return $this->formatNewCustomField($ufield_usubfield);
		}

		$group = $this->harvest_profile['groups'][$ufield_usubfield] ?? [];
		foreach ($group['fields'] as $field) {

			$repeatable = ($this->repeatable[$ufield_usubfield]) ?? '0';
			$firstFlag = ($this->first_flags[$ufield_usubfield]) ?? '0';
			if (('0' == $repeatable || $firstFlag == "1") && !empty($this->new_field_has_value[$ufield_usubfield])) {
				// Champ est non repetable ou on recupere seulement la première valeur et on a deja une valeur, on stop.
				break;
			}

			// On ne prend la valeur que si rien n'a deja ete trouve
			$prec_flag = ($this->prec_flags[$field['source']][$ufield_usubfield]) ?? '0';
			if (('1' == $prec_flag) && !empty($this->new_field_has_value[$ufield_usubfield])) {
				continue;
			}

			$tmp = explode('$', $field['ufield']);
			$searchUField = $tmp[0];
			$searchUSubField = $tmp[1] ?? '';

			foreach ($this->external_notices[$field['source']] ?? [] as $external_notice) {
				foreach ($external_notice as $unimarc_field) {
					if (
						($unimarc_field['value'] != '') &&
						(
							(($searchUField == $unimarc_field['ufield']) && ($searchUSubField == '')) ||
							(($searchUField == $unimarc_field['ufield']) && ($searchUSubField == $unimarc_field['usubfield']))
						)
					) {

						$field_order = $unimarc_field['field_order'] ?? 0;
						$subfield_order = $unimarc_field['subfield_order'] ?? 0;

						if (
							($field['source'] != $this->new_field_source) ||
							($field_order != $this->new_field_order) ||
							($subfield_order != $this->new_subfield_order)
						) {
							$this->new_field_source = $field['source'];
							$this->new_field_order = $field_order;
							$this->new_subfield_order = $subfield_order;
							$this->new_field_index++;
						}

						if ($unimarc_field['usubfield'] == '') {
							$this->new_fields['f'][$this->new_field_index]['c'] = $unimarc_field['ufield'];
							$this->new_fields['f'][$this->new_field_index]['ind'] = $unimarc_field['field_ind'];
							$this->new_fields['f'][$this->new_field_index]['value'] = $unimarc_field['value'];
						} else {

							$this->new_fields['f'][$this->new_field_index]['c'] = $unimarc_field['ufield'];
							$this->new_fields['f'][$this->new_field_index]['ind'] = $unimarc_field['field_ind'];
							$this->new_fields['f'][$this->new_field_index]['s'][] = ['c' => $unimarc_field['usubfield'], 'value' => $unimarc_field['value']];
						}
						$this->new_field_has_value[$ufield_usubfield] = 1;
					}
				}
			}
		}

	}


	/**
	 * Ajoute les nouveaux champs a la notice unimarc finale
	 *
	 */
	protected function addNewFieldsToUnimarcNotice()
	{

		// dedoublonnage des champs
		/*
		 $new_fields = $this->new_fields;
		 foreach($new_fields['f'] as $k => $v_field) {
		 var_dump ($v_field);die;





		 }
		 */
		// merge
		$this->final_unimarc_notice['f'] = array_merge($this->final_unimarc_notice['f'], $this->new_fields['f']);
	}


	/**
	 * Construction des recherches externes
	 *
	 * @return void
	 */
	protected function buildSearchQueries()
	{
		$this->search_queries = [];

		//liste des champs a interroger par sources
		$sources = [];
		foreach ($this->harvest_profile['groups'] as $group) {
			foreach ($group['fields'] as $field) {
				if (!empty($field['source'])) {
					$source_name = $this->harvest_model->getSourceById($field['source'])['name'] ?? '';
                    $sources[$field['source']][] = [
                        'ufield' => $field['ufield'],
                        'source_id' => $field['source'],
                        'source_name' => $source_name
                    ];
				}
			}
		}
		//var_dump('liste des champs a interroger par sources', $sources);

		$search_field_ids = $this->harvest_model->getSearchFieldIds();
		//var_dump('tableau correspondance unimarcfield => field_id ', $search_field_ids);

		$search_identifiers = [];
		foreach ($sources as $source_id => $source) {
			$source_info = $this->harvest_model->getSourceById($source_id);
			$search_identifiers[$source_info['field']][] = $source_id;
		}
		//var_dump('liste des sources a traiter par champs identifiants de recherches ', $search_identifiers);

		//construction des requetes
		//identifiant commun = 1 seule recherche sur +sieurs sources
		//sinon, une recherche par champ identifiant

		foreach ($search_identifiers as $ks => $source_ids) {
			$search_query = [];

			//Source
			$search_query['search_0'] = "s_2";
			$search_query['op_0_s2'] = "EQ";
			$search_query['field_0_s2'] = $source_ids;


			//Champ identifiant
			$search_field_id = 'f_' . $search_field_ids[$ks];
			$search_query['search_1'] = $search_field_id;
			$search_query['inter_1_f_x'] = 'and';

			//TODO l'opérateur est a verifier selon les champs de recherche
			$search_query['op_1_f_x'] = 'STARTWITH';

			//recherche de la valeur du champ identifiant dans la notice
			$fs = explode('$', $ks);
			$f = $fs[0];
			$s = ($fs[1]) ?? '';
			$v = '';
			foreach ($this->unimarc_notice['f'] as $kf => $field) {

				if ($f == $field['c']) {

					//champ trouve
					if ($s && is_array($field['s'])) {
						foreach ($field['s'] as $subfield) {
							//sous-champ trouve
							//var_dump($subfield);
							if ($s == $subfield['c']) {
								$v = $subfield['value'];
							}
						}
					} elseif (!empty($field['value'])) {
						$v = $field['value'];
					}
				}
			}

			if ($v) {
				$search_query['field_1_f_x'] = $v;
				$this->search_queries[] = $search_query;
			}
		}
	}


	/**
	 * Lancement des recherches externes
	 *
	 * @return array
	 */
	protected function runSearchQueries()
	{
		$this->search_results = [];
		if (empty($this->search_queries)) {
			return [];
		}

		$this->external_notices = [];

		foreach ($this->search_queries as $search_query) {

			global $search;
			$search = [];
			//Champ sources (s_2)
			$search[] = $search_query['search_0'];
			global $op_0_s_2;
			$op_0_s_2 = $search_query['op_0_s2'];
			global $field_0_s_2;
			$field_0_s_2 = $search_query['field_0_s2'];

			//Champ de recherche
			$search_field = $search_query['search_1'];
			$search[] = $search_field;

			$inter_1_f_x = 'inter_1_' . $search_field;
			global ${$inter_1_f_x};
			${$inter_1_f_x} = $search_query['inter_1_f_x'];

			$op_1_f_x = 'op_1_' . $search_field;
			global ${$op_1_f_x};
			${$op_1_f_x} = $search_query['op_1_f_x'];

			$field_1_f_x = 'field_1_' . $search_field;
			global ${$field_1_f_x};
			${$field_1_f_x} = [$search_query['field_1_f_x']];

			global $explicit_search;
			$explicit_search = "1";

			$s = new search(false, 'search_fields_unimarc');
			$sr = $s->make_search();

			$q = 'select * from ' . $sr;
			$r = pmb_mysql_query($q);

			while ($row = pmb_mysql_fetch_assoc($r)) {

				$recid = $row['notice_id'];

				// Source
				$q_source = "select source_id FROM external_count WHERE rid=" . $recid;
				$r_source = pmb_mysql_query($q_source);
				$source_id = pmb_mysql_result($r_source, 0, 0);

				//Notice externe
				$q_external_notice = "select * from entrepot_source_" . $source_id . " where recid=" . $recid . " order by ufield,field_order,usubfield,subfield_order,value";
				$r_external_notice = pmb_mysql_query($q_external_notice);
				$i = count($this->external_notices);
				while ($row_external_notice = pmb_mysql_fetch_assoc($r_external_notice)) {
					$this->external_notices[$source_id][$i][] = $row_external_notice;
				}
			}
		}

		return $this->external_notices;
	}

	public function recordNotice($notice_id)
	{
		global $msg, $charset;

		$z = new z3950_notice("form");
		$ret = $z->update_in_database($notice_id);
		print "
        <div class='row'>
            <div class='msg-perio'>" . htmlentities($msg["maj_encours"], ENT_QUOTES, $charset) . "</div>
        </div>
        <script >document.location='" . notice::get_permalink($notice_id) . "'</script>";
		printr($ret);
	}

	public function getSelector()
	{
		global $harvest_notice_tpl, $harvest_notice_tpl_error;
		global $msg, $charset;

		$harvest_selector = harvest::getSelector('harvest_id', 0);
		if (empty($harvest_selector)) {
			$tpl = $harvest_notice_tpl_error;
			$tpl = str_replace('<!-- error_msg -->', htmlentities($msg['harvest_notice_error_no_harvest'], ENT_QUOTES, $charset), $tpl);
			return $tpl;
		}

		$import_profile_selector = harvest_profil_import::getSelector('profil_id', 0);
		if (empty($import_profile_selector)) {
			$tpl = $harvest_notice_tpl_error;
			$tpl = str_replace('<!-- error_msg -->', htmlentities($msg['harvest_notice_error_no_import_profile'], ENT_QUOTES, $charset), $tpl);
			return $tpl;
		}

		$tpl = $harvest_notice_tpl;
		$tpl = str_replace('!!sel_harvest!!', $harvest_selector, $tpl);

		$tpl = str_replace('!!sel_profil!!', $import_profile_selector, $tpl);

		$tpl = str_replace('!!notice_id!!', $this->notice_id, $tpl);
		return $tpl;
	}

	private function hack()
	{
		$this->external_notices[5][0][] = ['connector_id' => 'z3950', 'source_id' => '5', 'ref' => 'FRBNF44480967000000X', 'date_import' => '2024-01-04 09:37:13', 'ufield' => '610',
			'field_ind' => '', 'usubfield' => 'a', 'field_order' => 35, 'subfield_order' => '0', 'value' => 'FRBNF44480967000000X', 'i_value' => ' frbnf44480967000000x ', 'recid' => '370',
			'search_id' => '176d3eb3e97171b521a8fd1c68f257ad'];
	}

	/**
	 * Suppression d'un champ perso
	 *
	 * @param string $ufield_usubfield
	 * @return boolean|void
	 */
	protected function deleteCustomFieldFromUnimarcNotice($ufield_usubfield = "")
	{
		if (empty($this->final_unimarc_notice)) {
			return false;
		}

		$tmp = explode('$', $ufield_usubfield);
		$ufield = $tmp[0];
		$idCustomField = $tmp[1] ?? '';
		if ($ufield != 900 || empty($idCustomField) || empty($this->noticeCustomFields[$idCustomField])) {
			return false;
		}

		$uSubField = $this->noticeCustomFields[$idCustomField]['NAME'];
		$unimarc_notice = $this->final_unimarc_notice;

		foreach ($unimarc_notice['f'] as $kf => $current_field) {

			$current_ufield = $current_field['c'];

			if ($ufield != $current_ufield) {
				// Le $current_ufield n'est pas le bon $ufield, on ne le supprime pas
				continue;
			}

			if (!isset($current_field['s'])) {
				continue;
			}

			foreach ($current_field['s'] as $current_subfield) {
				if ($current_subfield['c'] == 'n' && $current_subfield['value'] == $uSubField) {
					unset($this->final_unimarc_notice['f'][$kf]);
				}
			}
		}
	}


	/**
	 * Formate les champs perso a ajouter a la notice unimarc
	 *
	 * @param string $ufield_usubfield : code-champ[$code sous-champ]
	 */
	protected function formatNewCustomField($ufield_usubfield = "")
	{
		$tmp = explode('$', $ufield_usubfield);
		$ufield = $tmp[0];
		$idCustomField = $tmp[1] ?? '';
		if ($ufield != 900 || empty($idCustomField) || empty($this->noticeCustomFields[$idCustomField])) {
			return false;
		}

		$group = $this->harvest_profile['groups'][$ufield_usubfield] ?? [];
		foreach ($group['fields'] as $field) {

			$repeatable = ($this->repeatable[$ufield_usubfield]) ?? '0';
			$firstFlag = ($this->first_flags[$ufield_usubfield]) ?? '0';
			if (('0' == $repeatable || $firstFlag == "1") && !empty($this->new_field_has_value[$ufield_usubfield])) {
				// Champ est non repetable ou on recupere seulement la première valeur et on a deja une valeur, on stop.
				break;
			}

			// On ne prend la valeur que si rien n'a deja ete trouve
			$prec_flag = ($this->prec_flags[$field['source']][$ufield_usubfield]) ?? '0';
			if (('1' == $prec_flag) && !empty($this->new_field_has_value[$ufield_usubfield])) {
				continue;
			}

			$tmp = explode('$', $field['ufield']);
			$searchUField = $tmp[0];
			$searchUSubField = $tmp[1] ?? '';

			foreach ($this->external_notices[$field['source']] ?? [] as $external_notice) {
				foreach ($external_notice as $unimarc_field) {
					if (
						($unimarc_field['value'] != '') &&
						(
							(($searchUField == $unimarc_field['ufield']) && ($searchUSubField == '')) ||
							(($searchUField == $unimarc_field['ufield']) && ($searchUSubField == $unimarc_field['usubfield']))
						)
					) {

						$field_order = $unimarc_field['field_order'] ?? 0;
						$subfield_order = $unimarc_field['subfield_order'] ?? 0;

						if (
							($field['source'] != $this->new_field_source) ||
							($field_order != $this->new_field_order) ||
							($subfield_order != $this->new_subfield_order)
						) {
							$this->new_field_source = $field['source'];
							$this->new_field_order = $field_order;
							$this->new_subfield_order = $subfield_order;
							$this->new_field_index++;
						}

						$this->new_fields['f'][$this->new_field_index]['c'] = '900';
						$this->new_fields['f'][$this->new_field_index]['ind'] = $unimarc_field['field_ind'] ?? '';
						$this->new_fields['f'][$this->new_field_index]['s'][] = ['c' => 'a', 'value' => $unimarc_field['value']];
						$this->new_fields['f'][$this->new_field_index]['s'][] = ['c' => 'l', 'value' => $this->noticeCustomFields[$idCustomField]['TITRE']];
						$this->new_fields['f'][$this->new_field_index]['s'][] = ['c' => 'n', 'value' => $this->noticeCustomFields[$idCustomField]['NAME']];
						$this->new_fields['f'][$this->new_field_index]['s'][] = ['c' => 't', 'value' => $this->noticeCustomFields[$idCustomField]['TYPE']];
						$this->new_field_has_value[$ufield_usubfield] = 1;
					}
				}
			}
		}
	}
}

