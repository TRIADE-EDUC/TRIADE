<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: odilotk.class.php,v 1.7.4.1 2025/04/16 12:16:52 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
	die("no access");

global $class_path, $base_path, $include_path;
require_once ($class_path . "/connecteurs.class.php");
require_once ($class_path . "/curl.class.php");

class odilotk extends connector {

	// Variables internes pour la progression de la récupération des notices
	public $n_recu; // Nombre de notices reçues
	public $n_total; // Nombre total de notices à recevoir
	public $n_total_inactive; // Nombre de notices désactivées chez Odilo
	protected $limit = 100;
	protected $page = 0;

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::get_id()
	 */
	public function get_id()
	{
		return "odilotk";
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::is_repository()
	 */
	public function is_repository()
	{
	    return connector::REPOSITORY_YES;
	}

	public function source_get_property_form($source_id) {
		global $charset;

		$params = $this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			// Affichage du formulaire avec $params["PARAMETERS"]
			$vars = unserialize($params["PARAMETERS"]);
			foreach ( $vars as $key => $val ) {
				global ${$key};
				${$key} = $val;
			}
		}

		// Champ perso de notice à utiliser
		$form = "<div class='row'>
				<div class='colonne3'><label for='source_name'>" . $this->msg["odilotk_source_field"] . "</label></div>
				<div class='colonne-suite'>
					<select name='cp_field'>";
		$query = "select idchamp, titre from notices_custom where datatype='small_text'";
		$result = pmb_mysql_query($query);
		if ($result && pmb_mysql_num_rows($result)) {
			while ( $row = pmb_mysql_fetch_object($result) ) {
				$form .= "
    					<option value='" . $row->idchamp . "' " . ($row->idchamp == $cp_field ? "selected='selected'" : "") . ">" . htmlentities($row->titre, ENT_QUOTES, $charset) . "</option>";
			}
		} else {
			$form .= "
    					<option value='0'>" . $this->msg["odilotk_no_field"] . "</option>";
		}
		$form .= "
    				</select>
				</div>
			</div>";

		$form .= "<div class='row'></div>";
		return $form;
	}

	public function make_serialized_source_properties($source_id) {
		global $cp_field;

		$t["cp_field"] = $cp_field;

		$this->sources[$source_id]["PARAMETERS"] = serialize($t);
	}

	/**
	 * Formulaire des propriétés générales
	 */
	public function get_property_form() {
		global $charset;
		$this->fetch_global_properties();
		// Affichage du formulaire en fonction de $this->parameters
		if ($this->parameters) {
			$keys = unserialize($this->parameters);
			$endpoint_url = $keys['endpoint_url'];
			$api_endpoint_url = $keys['api_endpoint_url'];
			$client_id = $keys['client_id'];
			$client_secret = $keys['client_secret'];
			$shared_secret_key = $keys['shared_secret_key'];
		} else {
			$endpoint_url = '';
			$api_endpoint_url = '';
			$client_id = '';
			$client_secret = '';
			$shared_secret_key = '';
		}
		$r = "<div class='row'>
				<div class='colonne3'><label for='endpoint_url'>" . $this->msg["odilotk_endpoint_url"] . "</label></div>
				<div class='colonne-suite'><input type='text' class='saisie-50em' id='endpoint_url' name='endpoint_url' value='" . htmlentities($endpoint_url, ENT_QUOTES, $charset) . "'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='api_endpoint_url'>" . $this->msg["odilotk_api_endpoint_url"] . "</label></div>
				<div class='colonne-suite'><input type='text' class='saisie-50em' id='api_endpoint_url' name='api_endpoint_url' value='" . htmlentities($api_endpoint_url, ENT_QUOTES, $charset) . "' placeholder='" . $this->msg['odilotk_api_endpoint_url_placeholder'] . "'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='client_id'>" . $this->msg["odilotk_client_id"] . "</label></div>
				<div class='colonne-suite'><input type='text' class='saisie-50em' id='client_id' name='client_id' value='" . htmlentities($client_id, ENT_QUOTES, $charset) . "'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='client_secret'>" . $this->msg["odilotk_client_secret"] . "</label></div>
				<div class='colonne-suite'><input type='password' class='saisie-50em' id='client_secret' name='client_secret' value='" . htmlentities($client_secret, ENT_QUOTES, $charset) . "'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='shared_secret_key'>" . $this->msg["odilotk_shared_secret_key"] . "</label></div>
				<div class='colonne-suite'><input type='password' class='saisie-50em' id='shared_secret_key' name='shared_secret_key' value='" . htmlentities($shared_secret_key, ENT_QUOTES, $charset) . "'/></div>
			</div>";
		return $r;
	}

	public function make_serialized_properties() {
		global $endpoint_url, $api_endpoint_url, $client_id, $client_secret, $shared_secret_key;
		// Mise en forme des paramètres à partir de variables globales (mettre le résultat dans $this->parameters)
		$keys = array();

		$keys['endpoint_url'] = $endpoint_url;
		$keys['api_endpoint_url'] = $api_endpoint_url;
		$keys['client_id'] = $client_id;
		$keys['client_secret'] = $client_secret;
		$keys['shared_secret_key'] = $shared_secret_key;
		$this->parameters = serialize($keys);
	}

	public function maj_entrepot($source_id, $callback_progress = "", $recover = false, $recover_env = "") {global $form_from;
		global $form_radio;
		global $form_from;
		global $charset, $base_path;

		$this->fetch_global_properties();
		$keys = unserialize($this->parameters);

		$this->callback_progress = $callback_progress;
		$this->source_id = $source_id;
		$this->n_recu = 0;

		$curl = new Curl();
		$curl->set_option('CURLOPT_USERPWD', $keys['client_id'] . ':' . $keys['client_secret']);
		$response = $curl->post($keys['api_endpoint_url'] . '/token', array(
				'grant_type' => 'client_credentials'
		));
		if ($response->headers['Status-Code'] != 200) {
			$this->error = true;
			$this->error_message = $this->msg["odilotk_authentication_failed"];
		}

		$content = json_decode($response->body);
		$authentication_token = $content->token;
		$authentication_type = $content->type;

		$curl->options = array();
		$curl->set_option('CURLOPT_HTTPHEADER', array(
				'Authorization: ' . $authentication_type . ' ' . $authentication_token
		));

		$this->n_total = 0;
		$this->n_total_inactive = 0;

		$source_params = $this->unserialize_source_params($source_id);
		$p = $source_params["PARAMETERS"];

		$query = "select name from notices_custom where idchamp = " . $p['cp_field'];
		$result = pmb_mysql_query($query);
		if ($row = pmb_mysql_fetch_object($result)) {
			$cp_odilotk = array(
					'cp_odilotk' => $row->name
			);
		} else {
			$cp_odilotk = array();
		}

		$modification_date = '';
		if ($form_radio == 'last_sync') {
			$sql = " SELECT MAX(UNIX_TIMESTAMP(date_import)) FROM entrepot_source_" . $source_id;
			$res = pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
			$modification_date = date("Y-m-d", $res);
		} else if ($form_radio == 'date_sync') {
			$modification_date = $form_from;
		}

		do {
			$response = $curl->get($keys['api_endpoint_url'] . '/records?limit=' . $this->limit . '&offset=' . ($this->limit * $this->page) . ($modification_date ? '&modificationDate='.$modification_date : ''));
			if ($response->headers['Status-Code'] != 200) {
				$this->error = true;
				$this->error_message = $this->msg["odilotk_authentication_failed"];
			}
			$content = json_decode($response->body);
			$this->n_total += count($content);

			foreach ( $content as $record ) {
				if (! $record->active) {
					$this->n_total--;
					$this->n_total_inactive++;
					$this->progress();
					continue;
				}
				$this->rec_record($this->odilotk_2_uni($record, $cp_odilotk), $source_id, '');
			}
			$this->page++;
			if (! $this->n_total) {
				return $this->n_total;
			}
		} while ( count($content) );
		return $this->n_recu;
	}

	public function progress() {
		$callback_progress = $this->callback_progress;
		if ($this->n_total) {
			$percent = ($this->n_recu / $this->n_total);
			$nlu = $this->n_recu;
			$ntotal = $this->n_total . ' (' . $this->n_total_inactive . ' ' . $this->msg['odilotk_records_inactive'] . ')';
		} else {
			$percent = 0;
			$nlu = $this->n_recu;
			$ntotal = "inconnu";
		}
		call_user_func($callback_progress, $percent, $nlu, $ntotal);
	}

	public function odilotk_2_uni($nt, $cp) {
		global $charset;

		$unimarc = array();

		// Construction du 001
		$unimarc["001"][0] = md5(serialize($nt));

		// id odilotk
		if ($cp['cp_odilotk']) {
			$unimarc["900"][0]["a"][0] = $nt->id;
			$unimarc["900"][0]["n"][0] = $cp['cp_odilotk'];
		}

		// source
		$unimarc["801"][0]["a"][0] = 'EN';
		$unimarc["801"][0]["b"][0] = 'OdiloTK';

		// title
		if (! empty($nt->title)) {
			// Dans la majorité des cas, il y a l'auteur après un /, on l'enlève
			$title = $nt->title;
			if (strpos($title, '/') !== false) {
				$title = substr($title, 0, strrpos($title, '/'));
			}
			$unimarc["200"][0]["a"][0] = $title;
		}

		// author
		if (! empty($nt->author)) {
			// 2 types de présentation de l'auteur ("Doe, John" et "John Doe")
			if (strpos($nt->author, ',') !== false) {
				// "Doe, John"
				$author_names = explode(',', $nt->author);
				$unimarc['700'][0]['a'][0] = trim($author_names[0]);
				$unimarc['700'][0]['b'][0] = trim($author_names[1]);
				$unimarc['700'][0]['4'][0] = '070';
			} else {
				// "John Doe"
				$author_names = explode(' ', $nt->author);
				$unimarc['700'][0]['a'][0] = trim(array_pop($author_names));
				$unimarc['700'][0]['b'][0] = implode(' ', $author_names);
				$unimarc['700'][0]['4'][0] = '070';
			}
		}

		// thumbnail
		if (! empty($nt->coverImageUrl)) {
			$unimarc["896"][0]["a"][0] = $nt->coverImageUrl;
		}

		if (! empty($nt->description)) {
			$unimarc["330"][0]["a"][0] = $nt->description;
		}

		if (! empty($nt->formats)) {
			$unimarc["215"][0]["d"][0] = implode(', ', $nt->formats);
		}

		if (! empty($nt->gradeLevel)) {
			// TODO
		}

		if (! empty($nt->isbn)) {
			$unimarc["010"][0]["a"][0] = $nt->isbn;
		}

		if (! empty($nt->language)) {
			$unimarc["101"][0]["a"][0] = $nt->language;
		}

		if (! empty($nt->publicationDate)) {
			$unimarc["210"][0]["d"][0] = str_replace('.', '', $nt->publicationDate);
		}

		if (! empty($nt->publisher)) {
			$unimarc["210"][0]["c"][0] = str_replace(',', '', $nt->publisher);
		}

		if (! empty($nt->releaseDate)) {
			// TODO
		}

		if (! empty($nt->subject)) {
			global $pmb_keyword_sep;
			$subjects = explode('/', $nt->subject);
			$unimarc["610"][0]["a"][0] = '';
			for($i = 0; $i < count($subjects); $i++) {
				if ($unimarc["610"][0]["a"][0]) {
					$unimarc["610"][0]["a"][0] .= $pmb_keyword_sep;
				}
				$unimarc["610"][0]["a"][0] .= trim($subjects[$i]);
			}
		}

		if (! empty($nt->type)) {
			// TODO
		}

		return $unimarc;
	}

	public function rec_record($record, $source_id, $search_id) {
		global $charset, $base_path, $url, $search_index;

		$date_import = date("Y-m-d H:i:s", time());

		// Recherche du 001
		$ref = $record["001"][0];
		// Mise à jour
		if ($ref) {
			$ref_exists = $this->has_ref($source_id, $ref);
			if ($ref_exists)
				return false;

				// Si conservation des anciennes notices, on regarde si elle existe
			$ref_exists = false;
			if (! $this->del_old) {
				$ref_exists = $this->has_ref($source_id, $ref);
			}
			// Si pas de conservation des anciennes notices, on supprime
			if ($this->del_old) {
				$this->delete_from_entrepot($source_id, $ref);
				$this->delete_from_external_count($source_id, $ref);
			}
			if (($this->del_old) || ((! $this->del_old) && (! $ref_exists))) {
				// Insertion de l'entête
				$n_header["rs"] = "*";
				$n_header["ru"] = "*";
				$n_header["el"] = "1";
				$n_header["bl"] = "m";
				$n_header["hl"] = "0";
				$n_header["dt"] = "a";

				// Récupération d'un ID
				$recid = $this->insert_into_external_count($source_id, $ref);
				foreach ( $n_header as $hc => $code ) {
					$this->insert_header_into_entrepot($source_id, $ref, $date_import, $hc, $code, $recid, $search_id);
				}

				$field_order = 0;
				foreach ( $record as $field => $val ) {
					for($i = 0; $i < count($val); $i++) {
						if (is_array($val[$i])) {
							foreach ( $val[$i] as $sfield => $vals ) {
								for($j = 0; $j < count($vals); $j++) {
									if ($charset != "utf-8")
										$vals[$j] = encoding_normalize::utf8_decode($vals[$j]);
									$this->insert_content_into_entrepot($source_id, $ref, $date_import, $field, $sfield, $field_order, $j, $vals[$j], $recid, $search_id);
								}
							}
						} else {
							if ($charset != "utf-8")
								$vals[$i] = encoding_normalize::utf8_decode($vals[$i]);
							$this->insert_content_into_entrepot($source_id, $ref, $date_import, $field, '', $field_order, 0, $val[$i], $recid, $search_id);
						}
						$field_order++;
					}
				}
				$this->rec_isbd_record($source_id, $ref, $recid);
			}
			$this->n_recu++;
			$this->progress();
		}
		return true;
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::getSynchroForm()
	 */
	public function getSynchroForm($source_id, $sync_form = "sync_form")
	{
		global $form_from;
		global $form_radio;

		$source_id = intval($source_id);
		$sync_form = in_array($sync_form, [ "sync_form", "planificateur_form"]) ? $sync_form : "sync_form";

		$sql = " SELECT MAX(UNIX_TIMESTAMP(date_import)) FROM entrepot_source_" . $source_id;
		$res = pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
		$latest_date_database_string = $res ? formatdate(date("Y-m-d", $res)) : "<i>" . $this->msg["odilotk_nonotice_sync"] . "</i>";

		$dateuntil = "";
		$form = "<blockquote>";
		$form .= "
				" . $this->msg["odilotk_get_notices"] . "
				<br /><br />
				<input type='radio' name='form_radio' value='last_sync' " . ((($form_radio == "last_sync") || !$form_radio) ? "checked" : "") . " />" . $this->msg["odilotk_last_sync"] . " <br />
				<input type='radio' name='form_radio' value='date_sync' " . (($form_radio == "date_sync") ? "checked" : "") . " />" . $this->msg["odilotk_sync_from"] . "
				<input type='hidden' name='form_from' value='" . ($form_from ? $form_from : date("Y-m-d", $form_from)) . "' />
				<input type=\"text\" readonly size=\"10\" name=\"form_from_lib\" value=\"" . (($form_from != '') ? formatdate($form_from) : formatdate(date("Y-m-d", $form_from))) . "\">
				<input class='bouton' type='button' name='form_from_button' value='".$this->msg['odilotk_form_from_select']."' onClick=\"openPopUp('./select.php?what=calendrier&caller=$sync_form&date_caller=" . date("Ymd", $form_from) . "&param1=form_from&param2=form_from_lib&auto_submit=NO&date_anterieure=YES', 'date_adhesion', 250, 300, -2, -2, 'toolbar=no, dependent=yes, resizable=yes')\"   />
    			<br />
    					";

		$form .= "<br />" . sprintf($this->msg["odilotk_syncinfo_date_baserecent"], $latest_date_database_string) . "<br /><br />";

		$form .= "</blockquote>";
		return $form;
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::get_maj_environnement()
	 */
	public function get_maj_environnement($source_id) {
		global $form_from;
		global $form_radio;
		$envt=array();
		$envt["form_from"]=$form_from;
		$envt["form_radio"]=$form_radio;
		return $envt;
	}

	/**
	 * Permet de verifier les donnees passees dans l'environnement
	 *
	 * @param int $source_id
	 * @param array $env
	 * @return array
	 */
	public function check_environnement($source_id, $env) {

	    if (! in_array($env['form_radio'], [
	        'last_sync',
	        'date_sync'
	    ], true)) {
	        $env['form_radio'] = 'last_sync';
	    }

	    // Format accepte : "Y-m-d"
	    if (! preg_match("/^[0-9]{4}(-[0-9]{2}){2}$/", $env['form_from'])) {
	        $env['form_from'] = date("Y-m-d", time());
	    }

	    $clean_env = [];
	    $clean_env['form_radio'] = $env['form_radio'] ?? "";
	    $clean_env['form_from'] = $env['form_from'];
	    $clean_env['form_from_lib'] = formatdate($env['form_from']);

	    return $clean_env;
	}
}
