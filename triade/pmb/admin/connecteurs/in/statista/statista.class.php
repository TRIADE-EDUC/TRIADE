<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: statista.class.php,v 1.4.10.1 2025/04/16 12:16:51 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $charset;

require_once __DIR__."/statista_client.class.php";

class statista extends connector {

	protected $statista_url = '';
	protected $statista_api_key = '';

	protected $statista_content_type = statista_client::CONTENT_TYPE_DEFAULT;
	protected $statista_platform = statista_client::PLATFORM_DEFAULT;
	protected $statista_limit = statista_client::LIMIT_DEFAULT;
	protected $statista_premium = statista_client::PREMIUM_DEFAULT;

	protected $statista_client = null;

	protected $statista_config = [];
	protected $statista_response = [];
	protected $statista_errors = [];
	protected $buffer = [];

	public function __construct($connector_path="")
	{
		parent::__construct($connector_path);
		$this->get_statista_config();
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::get_id()
	 */
	public function get_id()
	{
		return "statista";
	}

	protected function unserialize_source_params($source_id) {

		$params = parent::unserialize_source_params($source_id);
		if(!empty($params['PARAMETERS']['statista_url'])) {
			$this->statista_url = $params['PARAMETERS']['statista_url'];
		} else {
			$this->statista_url = statista_client::WSURL_DEFAULT;
		}
		if(!empty($params['PARAMETERS']['statista_api_key'])) {
			$this->statista_api_key = $params['PARAMETERS']['statista_api_key'];
		}
		if(!empty($params['PARAMETERS']['statista_content_type'])) {
			$this->statista_content_type = $params['PARAMETERS']['statista_content_type'];
		} else {
			$this->statista_content_type = statista_client::CONTENT_TYPE_DEFAULT;
		}
		if(!empty($params['PARAMETERS']['statista_platform'])) {
			$this->statista_platform = $params['PARAMETERS']['statista_platform'];
		} else {
			$this->statista_platform = statista_client::PLATFORM_DEFAULT;
		}
		if(!empty($params['PARAMETERS']['statista_premium'])) {
			$this->statista_premium = $params['PARAMETERS']['statista_premium'];
		} else {
			$this->statista_premium = statista_client::PREMIUM_DEFAULT;
		}
		if(!empty($params['PARAMETERS']['statista_limit'])) {
			$this->statista_limit = $params['PARAMETERS']['statista_limit'];
		} else{
			$this->statista_limit = statista_client::LIMIT_DEFAULT;
		}
		return $params;
	}

	protected function get_client() {
		$this->statista_client = new statista_client($this->statista_api_key, $this->statista_url);
	}

	//Formulaire des propriétés générales
	public function source_get_property_form($source_id) {

		global $charset;

		$this->unserialize_source_params($source_id);

		$form = "
			<div class='row'>&nbsp;</div>
				<h3>".$this->msg['statista_ws']."</h3>
			<div class='row'>&nbsp;</div>

			<div class='row'>
				<div class='colonne3'>
					<label for='statista_url'>".$this->msg["statista_url"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='text' name='statista_url' id='statista_url' class='saisie-80em' value='".htmlentities($this->statista_url,ENT_QUOTES,$charset)."'/>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='statista_api_key' >".$this->msg["statista_api_key"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='text' name='statista_api_key' id='statista_api_key' class='saisie-30em' value='".htmlentities($this->statista_api_key,ENT_QUOTES,$charset)."'  />
				</div>
			</div>
			<div class='row'></div>";

		$form.= "
			<div class='row'>&nbsp;</div>
	    		<h3>".$this->msg['statista_search_params']."</h3>
	    	<div class='row'>&nbsp;</div>";

		$content_type_selector = $this->get_content_type_selector();

		$form.= "
	    	<div class='row'>
	    		<div class='colonne3'>
	    			<label for='statista_content_type' >".$this->msg['statista_content_type']."</label>
	    		</div>
	    		<div class='colonne_suite'>
	    			{$content_type_selector}
	    		</div>
	    	</div>";

	    			$platform_selector = $this->get_platform_selector();

	    			$form.= "
	    	<div class='row'>
	    		<div class='colonne3'>
	    			<label for='statista_platform' >".$this->msg['statista_platform']."</label>
	    		</div>
	    		<div class='colonne_suite'>
	    			{$platform_selector}
	    		</div>
	    	</div>

			<div class='row'>
				<div class='colonne3'>
					<label for='statista_premium'>".$this->msg["statista_premium"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='checkbox' name='statista_premium' id='statista_premium' value='1' ".(($this->statista_premium === 1)?"checked":"")."/>
				</div>
			</div>

			<div class='row'>
				<div class='colonne3'>
					<label for='statista_limit'>".$this->msg["statista_limit"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='text' name='statista_limit' id='statista_limit' class='saisie-5em' value='".$this->statista_limit."' />
				</div>
			</div>
			<div class='row'></div>";

	    			return $form;
	}

	protected function get_content_type_selector() {

		$availables_platform = statista_client::CONTENT_TYPE_AVAILABLE_VALUES;
		$selector = "<select id='statista_content_type' name='statista_content_type' >";
		foreach($availables_platform as $k=>$v) {
			$selector.= "<option value='".$k."' ";
			if($k == $this->statista_content_type) {
				$selector.= "selected='selected' ";
			}
			$selector.=">";
			$selector.= $this->msg['statista_content_type_'.$v];
			$selector.= "</option>";
		}
		$selector.= "</select>";
		return $selector;
	}

	protected function get_platform_selector() {

		$availables_platform = statista_client::PLATFORM_AVAILABLE_VALUES;
		$selector = "<select id='statista_platform' name='statista_platform' >";
		foreach($availables_platform as $k=>$v) {
			$selector.= "<option value='".$k."' ";
			if($k == $this->statista_platform) {
				$selector.= "selected='selected' ";
			}
			$selector.=">";
			$selector.= $this->msg['statista_platform_'.$v];
			$selector.= "</option>";
		}
		$selector.= "</select>";
		return $selector;
	}


	public function make_serialized_source_properties($source_id) {

		global $statista_url, $statista_api_key;
		global $statista_content_type, $statista_platform, $statista_premium, $statista_limit;

		$this->unserialize_source_params($source_id);

		if(empty($statista_url)) {
			$statista_url = '';
		}
		if(empty($statista_api_key)) {
			$statista_api_key = '';
		}
		if(empty($statista_content_type)) {
			$statista_content_type = statista_client::CONTENT_TYPE_DEFAULT;
		} else {
			$statista_content_type = intval($statista_content_type);
		}
		if(empty($statista_platform)) {
			$statista_platform = statista_client::PLATFORM_DEFAULT;
		} else {
			$statista_platform = intval($statista_platform);
		}
		if(!isset($statista_premium)) {
			$statista_premium = statista_client::PREMIUM_DEFAULT;
		} else {
			$statista_premium = intval($statista_premium);
		}
		if(!isset($statista_limit)) {
			$statista_limit = statista_client::LIMIT_DEFAULT;
		} else {
			$statista_limit = intval($statista_limit);
		}
		$statista_limit = min($statista_limit, statista_client::LIMIT_MAX);

		$this->sources[$source_id]['PARAMETERS'] = serialize(
				[
						'statista_url'				=> stripslashes($statista_url),
						'statista_api_key'			=> stripslashes($statista_api_key),
						'statista_content_type'		=> $statista_content_type,
						'statista_platform'			=> $statista_platform,
						'statista_premium'			=> $statista_premium,
						'statista_limit'			=> $statista_limit,
				]
				);
	}


	public function search($source_id, $query, $search_id) {

		//    	$t0 = hrtime(true);

		$this->unserialize_source_params($source_id);
		$this->get_client();

		$statista_search_fields = $this->statista_config['statista']['search_fields'];
		$connector_queries = [];
		foreach($query as $mterm) {

			if(empty($statista_search_fields[$mterm->ufield])) {
				continue;
			}

			foreach($statista_search_fields[$mterm->ufield] as $criterion) {
				foreach($mterm->values as $value) {
					$aq = new analyse_query($value);
					$tmp_connector_queries = $this->build_connector_queries($aq->tree);
					$connector_queries = array_merge($connector_queries, $tmp_connector_queries);
				}
			}

		}
		$connector_queries = array_unique($connector_queries);
		$this->statista_response = [];
		foreach($connector_queries as $id_query=>$connector_query) {
			$this->do_query($connector_query, $id_query);
		}

		//     	$t1 = hrtime(true);

		$this->prepare_records($source_id, $search_id);
		$this->rec_records();

		//     	$t2 = hrtime(true);

		//     	var_dump("tps rech = ".($t1 - $t0)/1000000000);
		//     	var_dump("tps conv = ".($t2 - $t1)/1000000000);
		//     	var_dump("tps total = ".($t2 - $t0)/1000000000);

	}


	protected function build_connector_queries($tree) {

		if(!is_array($tree) || empty($tree)) {
			return '';
		}
		$connector_queries = [];

		for($i=0 ; $i<count($tree) ; $i++){

			if($tree[$i]->literal !=2 ) {

				if ($tree[$i]->sub){
					$connector_queries = array_merge($connector_queries, $this->build_connector_queries($tree[$i]->sub));
				} else {
					if(!empty($tree[$i]->word)){
						$connector_queries[] = encoding_normalize::utf8_normalize($tree[$i]->word);
					}
				}
			}
		}
		return $connector_queries;
	}


	protected function do_query($connector_query, $id_query) {

		$result = $this->statista_client->search(
				$connector_query,
				$this->statista_content_type,
				statista_client::PLATFORM_AVAILABLE_VALUES[$this->statista_platform],
				$this->statista_limit,
				statista_client::DATE_FROM_DEFAULT,
				statista_client::DATE_TO_DEFAULT,
				$this->statista_premium,
				statista_client::SORT_DEFAULT,
				);

		if($result) {
			$search_result = $this->statista_client->get_result();
			if(!empty($search_result)) {
				$this->statista_response[] = $search_result;
			}
		} else {
			$this->statista_errors[] = $this->statista_client->get_errors();
		}
		return;
	}


	protected function prepare_records($source_id, $search_id) {

		if( !is_array($this->statista_response) || empty($this->statista_response)) {
			return;
		}
		foreach($this->statista_response as $query_response) {
			foreach($query_response['items'] as $record) {
				$this->prepare_record($record, $source_id, $search_id);
			}
		}
	}


	protected function prepare_record($record, $source_id, $search_id) {

		global $charset;

		if( !is_array($record) || empty(($record)) ) {
			return;
		}

		//identifier et title ?
		if( empty($record['identifier']) || empty($record['title']) ) {
			return;
		}

		$record = encoding_normalize::utf8_decode($record);

		$ref = $record['identifier'];

		$date_import=date("Y-m-d H:i:s",time());

		//Id deja existant
		if($this->has_ref($source_id, $ref, $search_id)){
			return;
		}

		//Transfo Unimarc

		//type doc et entetes
		$unimarc_headers = [
				"rs" => "*",
				"ru" => "*",
				"el" => "*",
				"bl" => "m",
				"hl" => "0",
				"dt" => "a",
		];

		$unimarc_record = [];
		$fo = 0;
		$so = 0;

		//identifier
		$unimarc_record[] = [
				'ufield' => '001',
				'usubfield' => '',
				'value' => $record['identifier'],
				'field_order' => $fo,
				'subfield_order' => $so,
		];
		$fo++;

		//langue (reprise de la configuration)
		if(in_array(statista_client::PLATFORM_AVAILABLE_VALUES[$this->statista_platform], array_keys($this->statista_config['statista']['platform_to_languages']))) {
			$unimarc_record[] = [
					'ufield' => '101',
					'usubfield' => 'a',
					'value' => $this->statista_config['statista']['platform_to_languages'][statista_client::PLATFORM_AVAILABLE_VALUES[$this->statista_platform]],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			$fo++;
		}

		//title
		$unimarc_record[] = [
				'ufield' => '200',
				'usubfield' => 'a',
				'value' => $record['title'],
				'field_order' => $fo,
				'subfield_order' => $so,
		];
		$fo++;

		//date + annee
		$year = '';
		$formated_date = '';
		if( !empty($record['date']) ) {

			$date = DateTime::createFromFormat('Y-m-d', $record['date']);
			if($date) {
				$year = $date->format('Y');
				$formated_date = $date->format('d/m/Y');
			} else {
				$year = $record['date'];
			}
		}

		//publishers
		if(!empty($record['publishers'][0]['title'])) {
			$unimarc_record[] = [
					'ufield' => '210',
					'usubfield' => 'c',
					'value' => $record['publishers'][0]['title'],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			$fo++;
		}

		//annee
		if($year) {
			$unimarc_record[] = [
					'ufield' => '210',
					'usubfield' => 'd',
					'value' => $year,
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			$fo++;
		}


		//date
		if($formated_date ) {
			$unimarc_record[] = [
					'ufield' => '219',
					'usubfield' => 'd',
					'value' => $formated_date,
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			$fo++;
		}

		//subject
		if (! empty ( $record['subject'] )) {
			$unimarc_record[] = [
					'ufield' => '327',
					'usubfield' => 'a',
					'value' => $record['subject'],
					'field_order' => $fo,
					'subfield_order' => $so
			];
			$fo ++;
		}


		//description
		if (! empty ( $record['description'] )) {
			$unimarc_record[] = [
					'ufield' => '330',
					'usubfield' => 'a',
					'value' => $record['description'],
					'field_order' => $fo,
					'subfield_order' => $so
			];
			$fo ++;
		}

		//link
		if( !empty($record['link']) ) {
			$unimarc_record[] = [
					'ufield' => '856',
					'usubfield' => 'u',
					'value' => $record['link'],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			$fo++;
		}

		//sources
		if(!empty($record['sources'])) {
			if( !empty($this->statista_config['statista']['field_to_cp']['sources']) ) {
				$field = $this->statista_config['statista']['field_to_cp']['sources'];
				foreach($record['sources'] as $source) {

					$unimarc_record[] = [
							'ufield' => $field[0],
							'usubfield' => $field[1],
							'value' => $source['title'],
							'field_order' => $fo,
							'subfield_order' => $so,
					];
					$unimarc_record[] = [
							'ufield' => $field[0],
							'usubfield' => 'n',
							'value' => $field[2],
							'field_order' => $fo,
							'subfield_order' => $so,
					];
					$unimarc_record[] = [
							'ufield' => $field[0],
							'usubfield' => 't',
							'value' => 'text',
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


	protected function get_statista_config () {

		if(!empty($this->statista_config)) {
			return $this->statista_config;
		}
		$contents = '';
		$search_fields_file = __DIR__.'/statista.json';
		$search_fields_file_subst = __DIR__.'/statista_subst.json';

		if(is_readable($search_fields_file_subst)) {
			$contents = file_get_contents($search_fields_file_subst);
		}
		if(!$contents) {
			if(is_readable($search_fields_file)) {
				$contents = file_get_contents($search_fields_file);
			}
		}
		if(!$contents) {
			return $this->statista_config;
		}
		$this->statista_config = json_decode($contents, true);
		return $this->statista_config;

	}


}
