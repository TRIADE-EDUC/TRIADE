<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: xerfi.class.php,v 1.6.10.1 2025/04/16 12:16:50 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
global $charset;

require_once __DIR__."/xerfi_client.class.php";
require_once "{$class_path}/encoding_normalize.class.php";

class xerfi extends connector {

	protected $xerfi_wsdl_url = '';
	protected $xerfi_login = '';
	protected $xerfi_pwd = '';
	protected $xerfi_maxCount = xerfi_client::MAXCOUNT_DEFAULT;

	protected $xerfi_client = null;

	protected $xerfi_config = [];
	protected $xerfi_response = [];
	protected $xerfi_errors = [];
	protected $buffer = [];

	public function __construct($connector_path="")
	{
		parent::__construct($connector_path);
		$this->get_xerfi_config();
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::get_id()
	 */
	public function get_id()
	{
		return "xerfi";
	}

	protected function unserialize_source_params($source_id) {

		$params = parent::unserialize_source_params($source_id);
		if(!empty($params['PARAMETERS']['xerfi_wsdl_url'])) {
			$this->xerfi_wsdl_url = $params['PARAMETERS']['xerfi_wsdl_url'];
		}
		if(!empty($params['PARAMETERS']['xerfi_login'])) {
			$this->xerfi_login = $params['PARAMETERS']['xerfi_login'];
		}
		if(!empty($params['PARAMETERS']['xerfi_pwd'])) {
			$this->xerfi_pwd = $params['PARAMETERS']['xerfi_pwd'];
		}
		if(!empty($params['PARAMETERS']['xerfi_maxCount'])) {
			$this->xerfi_maxCount = $params['PARAMETERS']['xerfi_maxCount'];
		}
		return $params;
	}

	protected function get_client() {
		$this->xerfi_client = new xerfi_client($this->xerfi_login, $this->xerfi_pwd, $this->xerfi_wsdl_url);
	}

	//Formulaire des propriétés générales
	public function source_get_property_form($source_id) {

		global $charset;

		$this->unserialize_source_params($source_id);

		if(!$this->xerfi_wsdl_url) {
			$this->xerfi_wsdl_url = xerfi_client::WSDL_URL_DEFAULT;
		}

		$form = "
			<div class='row'>&nbsp;</div>
				<h3>".$this->msg['xerfi_ws']."</h3>
			<div class='row'>&nbsp;</div>

			<div class='row'>
				<div class='colonne3'>
					<label for='xerfi_wsdl_url'>".$this->msg["xerfi_wsdl_url"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='text' name='xerfi_wsdl_url' id='xerfi_wsdl_url' class='saisie-80em' value='".htmlentities($this->xerfi_wsdl_url,ENT_QUOTES,$charset)."'/>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='xerfi_login' >".$this->msg["xerfi_login"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='text' name='xerfi_login' id='xerfi_login' class='saisie-30em' value='".htmlentities($this->xerfi_login,ENT_QUOTES,$charset)."'  />
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='xerfi_pwd' >".$this->msg["xerfi_pwd"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='password' name='xerfi_pwd' id='xerfi_pwd' class='saisie-30em' autocomplete='off' value='".htmlentities($this->xerfi_pwd,ENT_QUOTES,$charset)."'  />
					<span class='fa fa-eye' onclick='toggle_password(this, \"xerfi_pwd\");' ></span>
				</div>
			</div>
			<div class='row'></div>

			<div class='row'>&nbsp;</div>
    			<h3>".$this->msg['xerfi_search_params']."</h3>
    		<div class='row'>&nbsp;</div>
			<div class='row'>
			<div class='colonne3'>
				<label for='xerfi_maxCount'>".$this->msg["xerfi_maxCount"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' name='xerfi_maxCount' id='xerfi_maxCount' class='saisie-5em' value='".$this->xerfi_maxCount."' />
			</div>
		</div>";

		return $form;
	}

	public function make_serialized_source_properties($source_id) {

		global $xerfi_wsdl_url, $xerfi_login, $xerfi_pwd;
		global $xerfi_maxCount;

		if(empty($xerfi_wsdl_url)) {
			$xerfi_wsdl_url = '';
		}
		if(empty($xerfi_login)) {
			$xerfi_login = '';
		}
		if(empty($xerfi_pwd)) {
			$xerfi_pwd = '';
		}
		$xerfi_maxCount = intval($xerfi_maxCount);
		if(empty($xerfi_maxCount)) {
			$xerfi_maxCount = $this->xerfi_maxCount;
		}

		$this->sources[$source_id]['PARAMETERS'] = serialize(
				[
						'xerfi_wsdl_url'	=> stripslashes($xerfi_wsdl_url),
						'xerfi_login'		=> stripslashes($xerfi_login),
						'xerfi_pwd'			=> stripslashes($xerfi_pwd),
						'xerfi_maxCount'	=> $xerfi_maxCount,
				]
				);
	}


	public function search($source_id, $query, $search_id) {

		// 		$t0 = hrtime(true);

		$this->unserialize_source_params($source_id);
		$this->get_client();

		$connector_queries = [];
		foreach($query as $mterm) {
			foreach($mterm->values as $value) {
				$aq = new analyse_query($value);
				$boolean_query = $this->build_boolean_query($aq->tree);
				$connector_queries[] = ['value'=>$boolean_query];
			}
		}
		$this->xerfi_response = [];
		foreach($connector_queries as $connector_query) {
			$this->do_query($connector_query );
		}

		// 		$t1 = hrtime(true);

		$this->prepare_records($source_id, $search_id);
		$this->rec_records();

		// 		$t2 = hrtime(true);

		// 		var_dump("tps rech = ".($t1 - $t0)/1000000000);
		// 		var_dump("tps conv = ".($t2 - $t1)/1000000000);
		// 		var_dump("tps total = ".($t2 - $t0)/1000000000);

	}


	protected function build_boolean_query($tree) {

		if(!is_array($tree) || empty($tree)) {
			return '';
		}
		$query = '';
		for($i=0 ; $i<count($tree) ; $i++){
			if($tree[$i]->literal !=2 ){

				if($query != ''){
					$query.= ',';
				}
				if($tree[$i]->sub){
					$query.= $this->build_boolean_query($tree[$i]->sub);
				}else{
					$query.= encoding_normalize::utf8_normalize($tree[$i]->word);
				}
			}
		}
		return $query;
	}


	protected function do_query($connector_query) {


		$query['query'] = $connector_query['value'];
		$query['maxCount'] = $this->xerfi_maxCount;

		$result = $this->xerfi_client->search(
				$query['query'],
				$query['maxCount'],
				);

		if($result) {
			$this->xerfi_response[] = $this->xerfi_client->get_result();
		} else {
			$this->xerfi_errors[] = $this->xerfi_client->get_errors();
		}
		return;
	}


	protected function prepare_records($source_id, $search_id) {

		if( !is_array($this->xerfi_response) || empty($this->xerfi_response)) {
			return;
		}

		foreach($this->xerfi_response as $response) {
			foreach($response as $record) {
				$this->prepare_record($record, $source_id, $search_id);
			}
		}

	}


	protected function prepare_record($record, $source_id, $search_id) {

		if( !is_array($record) || empty(($record)) ) {
			return;
		}

		if(empty($record['Code']) || empty($record['Title'])) {
			return;
		}

		$ref = $record['Code'];

		$date_import=date("Y-m-d H:i:s",time());

		//Id deja existant
		if($this->has_ref($source_id, $ref, $search_id)){
			return;
		}

		//type doc et entetes
		$unimarc_headers = [
				"rs" => "*",
				"ru" => "*",
				"el" => "*",
				"bl" => "m",
				"hl" => "0",
				"dt" => "a",
		];

		if( !empty($record['TYPEDOCUMENT']) ) {
			if(array_key_exists($record['TYPEDOCUMENT'], $this->xerfi_config['xerfi']['TYPEDOCUMENT_to_bl_hl_dt'])) {
				$headers = $this->xerfi_config['xerfi']['TYPEDOCUMENT_to_bl_hl_dt'][$record['TYPEDOCUMENT']];
				$unimarc_headers['bl'] = $headers[0];
				$unimarc_headers['hl'] = $headers[1];
				$unimarc_headers['dt'] = $headers[2];
			}
		}

		$unimarc_record = [];
		$fo = 0;
		$so = 0;

		//Code
		$unimarc_record[] = [
				'ufield' => '001',
				'usubfield' => '',
				'value' => $ref,
				'field_order' => $fo,
				'subfield_order' => $so,
		];
		$fo++;

		//Language
		if( !empty($record['Language']) ) {
			$language = '';
			if(array_key_exists($record['Language'], $this->xerfi_config['xerfi']['language_text_to_language_code'])) {
				$language = $this->xerfi_config['xerfi']['language_text_to_language_code'][$record['Language']];
			}
			if($language) {
				$unimarc_record[] = [
						'ufield' => '101',
						'usubfield' => 'a',
						'value' => $language,
						'field_order' => $fo,
						'subfield_order' => $so,
				];
				$fo++;
			}
		}

		//Title
		$unimarc_record[] = [
				'ufield' => '200',
				'usubfield' => 'a',
				'value' => encoding_normalize::utf8_decode($record['Title']),
				'field_order' => $fo,
				'subfield_order' => $so,
		];
		$fo++;

		//Author > Editeur
		if( !empty($record['Author']) ) {
			$unimarc_record[] = [
					'ufield' => 210,
					'usubfield' => 'c',
					'value' => encoding_normalize::utf8_decode($record['Author']),
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			$fo++;
		}

		//Date
		if( !empty($record['Date']) ) {

			$year = substr($record['Date'],6);
			$unimarc_record[] = [
					'ufield' => '210',
					'usubfield' => 'd',
					'value' => $year,
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			$fo++;

			$unimarc_record[] = [
					'ufield' => '219',
					'usubfield' => 'd',
					'value' => $record['Date'],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			$fo++;

		}

		//Essentiel > Résumé
		if( !empty($record['Essentiel']) ) {
			$unimarc_record[] = [
					'ufield' => '330',
					'usubfield' => 'a',
					'value' => encoding_normalize::utf8_decode($record['Essentiel']),
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			$fo++;
		}

		//URL
		if( !empty($record['URL']) ) {
			$unimarc_record[] = [
					'ufield' => '856',
					'usubfield' => 'u',
					'value' => $record['URL'],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			$fo++;
		}

		//Secteur/Sector
		if( !empty($record['Secteur']['Sector'] && is_array($record['Secteur']['Sector']) ) ) {
			foreach($record['Secteur']['Sector'] as $sector) {
				if(!empty($sector)) {
					$unimarc_record[] = [
							'ufield' => '606',
							'usubfield' => 'a',
							'value' => encoding_normalize::utf8_decode($sector),
							'field_order' => $fo,
							'subfield_order' => $so,
					];
					$fo++;
				}
			}
		}

		if(empty($unimarc_record)) {
			return;
		}

		$this->buffer['search_id'] = $search_id;
		$this->buffer['source_id'] = $source_id;
		$this->buffer['date_import'] = $date_import;
		$this->buffer['records'][$ref]['header'] = $unimarc_headers;
		$this->buffer['records'][$ref]['content'] = $unimarc_record;
	}


	protected function rec_records() {

		if(empty($this->buffer)) {
			return;
		}
		foreach($this->buffer['records'] as $ref=>$record) {
			$this->buffer['records'][$ref]['recid'] = $this->insert_into_external_count($this->buffer['source_id'], $ref);
		}

		$this->insert_records_into_entrepot($this->buffer);
	}


	protected function get_xerfi_config () {

		if(!empty($this->xerfi_config)) {
			return $this->xerfi_config;
		}
		$contents = '';
		$xerfi_config_file = __DIR__.'/xerfi.json';
		$xerfi_config_file_subst = __DIR__.'/xerfi_subst.json';

		if(is_readable($xerfi_config_file_subst)) {
			$contents = file_get_contents($xerfi_config_file_subst);
		}
		if(!$contents) {
			if(is_readable($xerfi_config_file)) {
				$contents = file_get_contents($xerfi_config_file);
			}
		}
		if(!$contents) {
			return $this->xerfi_config;
		}
		$this->xerfi_config = json_decode($contents, true);
		return $this->xerfi_config;

	}


}
