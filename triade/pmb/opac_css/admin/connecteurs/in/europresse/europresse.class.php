<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: europresse.class.php,v 1.11.4.1 2025/04/16 12:16:52 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $lang;

require_once __DIR__."/europresse_client.class.php";

class europresse extends connector {

	protected $europresse_url ='';
	protected $europresse_login = '';
	protected $europresse_pwd = '';

	protected $europresse_client = null;

	protected $europresse_documentBase = europresse_client::DOCUMENTBASE_DEFAULT;
	protected $europresse_domain = 0;
	protected $europresse_includes = europresse_client::INCLUDES_DEFAULT;
	protected $europresse_excludes = europresse_client::EXCLUDES_DEFAULT;
	protected $europresse_maxCount = europresse_client::MAXCOUNT_DEFAULT;
	protected $europresse_docUrl = europresse_client::DOCURL_DEFAULT;
	protected $europresse_dateRange = europresse_client::DATERANGE_DEFAULT;
	protected $europresse_startDate = europresse_client::STARTDATE_DEFAULT;
	protected $europresse_endDate = europresse_client::ENDDATE_DEFAULT;
	protected $europresse_fields = europresse_client::FIELDS_DEFAULT;
	protected $europresse_sort = europresse_client::SORT_DEFAULT;

	protected $europresse_config = [];
	protected $europresse_response = [];
	protected $europresse_errors = [];
	protected $buffer = [];

	public function __construct($connector_path="")
	{
		parent::__construct($connector_path);
		$this->get_europresse_config();
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::get_id()
	 */
	public function get_id()
	{
		return "europresse";
	}

	protected function unserialize_source_params($source_id) {

		$params = parent::unserialize_source_params($source_id);
		if(!empty($params['PARAMETERS']['europresse_url'])) {
			$this->europresse_url = $params['PARAMETERS']['europresse_url'];
		}
		if(!empty($params['PARAMETERS']['europresse_login'])) {
			$this->europresse_login = $params['PARAMETERS']['europresse_login'];
		}
		if(!empty($params['PARAMETERS']['europresse_pwd'])) {
			$this->europresse_pwd = $params['PARAMETERS']['europresse_pwd'];
		}
		if(!empty($params['PARAMETERS']['europresse_documentBase'])) {
			$this->europresse_documentBase = $params['PARAMETERS']['europresse_documentBase'];
		}
		if(!empty($params['PARAMETERS']['europresse_domain'])) {
			$this->europresse_domain = $params['PARAMETERS']['europresse_domain'];
		}
		if(!empty($params['PARAMETERS']['europresse_dateRange'])) {
			$this->europresse_dateRange = $params['PARAMETERS']['europresse_dateRange'];
		}
		if(!empty($params['PARAMETERS']['europresse_maxCount'])) {
			$this->europresse_maxCount = $params['PARAMETERS']['europresse_maxCount'];
		}
		return $params;
	}

	protected function get_client() {
		$this->europresse_client = new europresse_client($this->europresse_login, $this->europresse_pwd, $this->europresse_url);
	}

	//Formulaire des propriétés générales
	public function source_get_property_form($source_id) {

		global $charset;

		$this->unserialize_source_params($source_id);

		$this->get_client();
		if(!$this->europresse_url) {
			$this->europresse_url = $this->europresse_client::WSURL_DEFAULT;
		}

		$form = "
			<div class='row'>&nbsp;</div>
				<h3>".$this->msg['europresse_ws']."</h3>
			<div class='row'>&nbsp;</div>

			<div class='row'>
				<div class='colonne3'>
					<label for='europresse_url'>".$this->msg["europresse_url"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='text' name='europresse_url' id='europresse_url' class='saisie-80em' value='".htmlentities($this->europresse_url,ENT_QUOTES,$charset)."'/>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='europresse_login' >".$this->msg["europresse_login"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='text' name='europresse_login' id='europresse_login' class='saisie-30em' value='".htmlentities($this->europresse_login,ENT_QUOTES,$charset)."'  />
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='europresse_pwd' >".$this->msg["europresse_pwd"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='password' name='europresse_pwd' id='europresse_pwd' class='saisie-30em' autocomplete='off' value='".htmlentities($this->europresse_pwd,ENT_QUOTES,$charset)."'  />
					<span class='fa fa-eye' onclick='toggle_password(this, \"europresse_pwd\");' ></span>
				</div>
			</div>
			<div class='row'></div>";

		if (!($this->europresse_url && $this->europresse_login && $this->europresse_pwd) ) {

			$form.= "
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<h3 >".$this->msg['europresse_save_to_continue']."</h3>
			</div>
			<div class='row'></div>";

			return $form;
		}

		$form.= "
		<div class='row'>&nbsp;</div>
    		<h3>".$this->msg['europresse_search_params']."</h3>
    	<div class='row'>&nbsp;</div>";

		$domain_selector = $this->get_domain_selector();
		$dateRange_selector = $this->get_dateRange_selector();

		if(empty($domain_selector)) {

			$form.= "
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<h3 >".$this->msg['europresse_ws_error']."</h3>
			</div>
			<div class='row'></div>";
			return $form;
		}

		$form.= "
    	<div class='row'>
    		<div class='colonne3'>
    			<label for='europresse_domain' >".$this->msg["europresse_domain"]."</label>
    		</div>
    		<div class='colonne_suite'>
				$domain_selector
    		</div>
    	</div>";

				$form.= "
    	<div class='row'>
    		<div class='colonne3'>
    			<label for='europresse_dateRange' >".$this->msg["europresse_dateRange"]."</label>
    		</div>
    		<div class='colonne_suite'>
				$dateRange_selector
    		</div>
    	</div>";

				$form.= "
		<div class='row'>
			<div class='colonne3'>
				<label for='europresse_maxCount'>".$this->msg["europresse_maxCount"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' name='europresse_maxCount' id='europresse_maxCount' class='saisie-5em' value='".$this->europresse_maxCount."' />
			</div>
		</div>
		<div class='row'></div>";

				return $form;
	}

	protected function get_domain_selector() {

		global $lang, $charset;
		$r = $this->europresse_client->get_domains($lang);
		if(empty($r)) {
			return $r;
		}
		$selected = $this->europresse_domain;
		$selector = "<select id='europresse_domain' name='europresse_domain' >";
		$selector.= "<option value='0' ".((0==$selected)?"selected":"").">".$this->msg['europresse_domain_All']."</option>";
		foreach($r as $k=>$v) {
			$selector.= "<option value='".$k."' ".(($k==$selected)?"selected":"").">";
			$selector.= htmlentities($v, ENT_QUOTES, $charset);
			$selector.= "</option>";
		}
		$selector.= "</select>";

		return $selector;
	}

	protected function get_dateRange_selector() {

		$r = $this->europresse_client::DATERANGE_AVAILABLE_VALUES;
		$selected = $this->europresse_dateRange;
		$selector = "<select id='europresse_dateRange' name='europresse_dateRange' >";
		foreach($r as $v) {
			$selector.= "<option value='".$v."' ".(($v==$selected)?"selected":"").">";
			$selector.= $this->msg['europresse_dateRange_'.$v];
			$selector.= "</option>";
		}
		$selector.= "</select>";

		return $selector;
	}

	public function make_serialized_source_properties($source_id) {

		global $europresse_url, $europresse_login,$europresse_pwd;
		global $europresse_documentBase, $europresse_domain, $europresse_dateRange, $europresse_maxCount;

		if(empty($europresse_url)) {
			$europresse_url = '';
		}
		if(empty($europresse_login)) {
			$europresse_login = '';
		}
		if(empty($europresse_pwd)) {
			$europresse_pwd = '';
		}
		if( empty($europresse_documentBase) || !in_array($europresse_documentBase, europresse_client::DOCUMENTBASE_AVAILABLE_VALUES)) {
			$europresse_documentBase = europresse_client::DOCUMENTBASE_DEFAULT;
		}
		$europresse_domain = intval($europresse_domain);
		if(empty($europresse_domain)) {
			$europresse_domain = $this->europresse_domain;
		}
		if(empty($europresse_dateRange) || !in_array($europresse_dateRange, europresse_client::DATERANGE_AVAILABLE_VALUES) ) {
			$europresse_dateRange =  europresse_client::DATERANGE_DEFAULT;
		}
		$europresse_maxCount = intval($europresse_maxCount);
		if(empty($europresse_maxCount)) {
			$europresse_maxCount = $this->europresse_maxCount;
		}

		$this->sources[$source_id]['PARAMETERS'] = serialize(
				[
						'europresse_url'			=> stripslashes($europresse_url),
						'europresse_login'			=> stripslashes($europresse_login),
						'europresse_pwd'			=> stripslashes($europresse_pwd),
						'europresse_documentBase'	=> $europresse_documentBase,
						'europresse_domain'			=> $europresse_domain,
						'europresse_dateRange'		=> $europresse_dateRange,
						'europresse_maxCount'		=> $europresse_maxCount,
				]
				);
	}


	public function search($source_id,$query,$search_id) {

		//     	$t0 = hrtime(true);

		$this->unserialize_source_params($source_id);

		$this->get_europresse_config();
		$europresse_search_fields = $this->europresse_config['europresse']['search_fields'];

		$connector_queries = [];
		foreach($query as $mterm) {

			if(empty($europresse_search_fields[$mterm->ufield])) {
				continue;
			}

			foreach($europresse_search_fields[$mterm->ufield] as $criterion) {
				foreach($mterm->values as $value) {
					$connector_queries[] = ['criterion'=>$criterion, 'value'=>$value];
				}
			}

		}

		$this->europresse_response = [];
		foreach($connector_queries as $connector_query) {
			$this->do_query($connector_query);
		}

		//     	$t1 = hrtime(true);

		$this->prepare_records($source_id, $search_id);
		$this->rec_records();

		//   		$t2 = hrtime(true);

		//     	var_dump("tps rech = ".($t1 - $t0)/1000000000);
		//     	var_dump("tps conv = ".($t2 - $t1)/1000000000);
		//     	var_dump("tps total = ".($t2 - $t0)/1000000000);

	}


	protected function do_query($connector_query) {

		global $lang;
		$this->get_client();

		$query = array();
		$query['query'] = $connector_query['criterion']. encoding_normalize::utf8_normalize($connector_query['value']);
		$query['documentBase'] = $this->europresse_documentBase;
		$query['domainId'] = $this->europresse_domain;
		$query['includes'] = $this->europresse_includes;
		$query['excludes'] = $this->europresse_excludes;
		$query['maxCount'] = $this->europresse_maxCount;
		$query['docUrl'] = $this->europresse_docUrl;
		$query['dateRange'] = $this->europresse_dateRange;
		$query['startDate'] = $this->europresse_startDate;
		$query['endDate'] = $this->europresse_endDate;
		$query['fields'] = $this->europresse_fields;
		$query['sort'] = $this->europresse_sort;
		$query['CsLanguage'] = $lang;

		$result = $this->europresse_client->documents_search(
				$query['query'],
				$query['documentBase'],
				$query['domainId'],
				$query['includes'],
				$query['excludes'],
				$query['maxCount'],
				$query['docUrl'],
				$query['dateRange'],
				$query['startDate'],
				$query['endDate'],
				$query['fields'],
				$query['sort'],
				$query['CsLanguage']
				);

		if($result) {
			$this->europresse_response[] = $this->europresse_client->get_result()['result'];
		} else {
			$this->europresse_errors[] = $this->europresse_client->get_errors();
		}
		return;
	}


	protected function prepare_records($source_id, $search_id) {

		if( !is_array($this->europresse_response) || empty($this->europresse_response)) {
			return;
		}

		foreach($this->europresse_response as $response) {
			foreach($response as $record) {
				$this->prepare_record($record, $source_id, $search_id);
			}
		}

	}


	protected function prepare_record($record, $source_id, $search_id) {

		if( !is_array($record) || empty(($record)) ) {
			return;
		}
		if(empty($record['documentId']) || empty($record['title'])) {
			return;
		}

		$ref = $record['documentId'];

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

		$unimarc_record = [];
		$fo = 0;
		$so = 0;

		//documentId
		$unimarc_record[] = [
				'ufield' => '001',
				'usubfield' => '',
				'value' => $ref,
				'field_order' => $fo++,
				'subfield_order' => $so,
		];

		//Language
		if( isset($record['language']) && $record['language'] ) {
			$language = '';
			if(array_key_exists($record['language'], $this->europresse_config['europresse']['language_text_to_language_code'])) {
				$language = $this->europresse_config['europresse']['language_text_to_language_code'][$record['language']];
			}
			if($language) {
				$unimarc_record[] = [
						'ufield' => '101',
						'usubfield' => 'a',
						'value' => $language,
						'field_order' => $fo++,
						'subfield_order' => $so,
				];
			}
		}

		//title
		$unimarc_record[] = [
				'ufield' => '200',
				'usubfield' => 'a',
				'value' => $record['title'],
				'field_order' => $fo++,
				'subfield_order' => $so,
		];

		//publicationName
		if( isset($record['publicationName']) && $record['publicationName'] ) {
			$unimarc_record[] = [
					'ufield' => 210,
					'usubfield' => 'c',
					'value' => $record['publicationName'],
					'field_order' => $fo++,
					'subfield_order' => $so,
			];
		}

		//Année
		//publicationDate
		if( isset($record['publicationDate']) && $record['publicationDate'] ) {
			$date = DateTime::createFromFormat('Y-m-d\TH:i:s', $record['publicationDate']);
			$formated_date = '';
			if($date) {
				$formated_date = $date->format('d/m/Y');
				$year = $date->format('Y');
			}
			if(!$date) {
				$formated_date = $record['publicationDate'];
			}
			if($year) {
				$unimarc_record[] = [
						'ufield' => '210',
						'usubfield' => 'd',
						'value' => $year,
						'field_order' => $fo++,
						'subfield_order' => $so,
				];
			}
			$unimarc_record[] = [
					'ufield' => '219',
					'usubfield' => 'd',
					'value' => $formated_date,
					'field_order' => $fo++,
					'subfield_order' => $so,
			];
		}

		//byLine & inContext
		$abstract = '';
		if( isset($record['byLine']) && $record['byLine'] ) {
			$abstract.= $record['byLine'];
		}
		if( isset($record['inContext']) && $record['inContext'] ) {

			if($abstract) {
				$abstract.= ' - ';
			}
			$abstract.= strip_tags($record['inContext']);
			$unimarc_record[] = [
					'ufield' => '330',
					'usubfield' => 'a',
					'value' => $abstract,
					'field_order' => $fo++,
					'subfield_order' => $so,
			];
		}

		//externalLinks
		if( isset($record['externalLinks']) && is_array($record['externalLinks']) && count($record['externalLinks']) ) {

			foreach($record['externalLinks'] as $externalLink) {
				$unimarc_record[] = [
						'ufield' => '856',
						'usubfield' => 'u',
						'value' => $externalLink,
						'field_order' => $fo++,
						'subfield_order' => $so,
				];
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


	protected function get_europresse_config () {

		if(!empty($this->europresse_config)) {
			return $this->europresse_config;
		}
		$contents = '';
		$search_fields_file = __DIR__.'/europresse.json';
		$search_fields_file_subst = __DIR__.'/europresse_subst.json';

		if(is_readable($search_fields_file_subst)) {
			$contents = file_get_contents($search_fields_file_subst);
		}
		if(!$contents) {
			if(is_readable($search_fields_file)) {
				$contents = file_get_contents($search_fields_file);
			}
		}
		if(!$contents) {
			return $this->europresse_config;
		}
		$this->europresse_config = json_decode($contents, true);
		return $this->europresse_config;

	}


}
