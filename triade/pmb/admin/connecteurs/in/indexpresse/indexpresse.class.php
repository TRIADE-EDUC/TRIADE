<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexpresse.class.php,v 1.14.4.1 2025/04/16 12:16:51 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once "{$class_path}/multicurl.class.php";

class indexpresse extends connector {

	const MAXCOUNT_DEFAULT = 100;
	const NB_RESULTS_PER_PAGE = 140;

	protected $indexpresse_url ='';
	protected $indexpresse_auth_mode = 1;
	protected $indexpresse_login = '';
	protected $indexpresse_pwd = '';
	protected $indexpresse_connector = 'delphes';
	protected $indexpresse_search_fields = [];
	protected $indexpresse_maxCount = indexpresse::MAXCOUNT_DEFAULT;

	protected $indexpresse_curl_channel = false;

	protected $indexpresse_first_queries = [];
	protected $indexpresse_next_queries = [];
	protected $indexpresse_errors = [];
	protected $buffer = [];

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::get_id()
	 */
	public function get_id()
	{
		return "indexpresse";
	}

	protected function unserialize_source_params($source_id) {

		$params = parent::unserialize_source_params($source_id);
		if(!empty($params['PARAMETERS']['indexpresse_url'])) {
			$this->indexpresse_url = $params['PARAMETERS']['indexpresse_url'];
		}
		if(!empty($params['PARAMETERS']['indexpresse_auth_mode'])) {
			$this->indexpresse_auth_mode = $params['PARAMETERS']['indexpresse_auth_mode'];
		}
		if(!empty($params['PARAMETERS']['indexpresse_login'])) {
			$this->indexpresse_login = $params['PARAMETERS']['indexpresse_login'];
		}
		if(!empty($params['PARAMETERS']['indexpresse_pwd'])) {
			$this->indexpresse_pwd = $params['PARAMETERS']['indexpresse_pwd'];
		}
		if(!empty($params['PARAMETERS']['indexpresse_connector'])) {
			$this->indexpresse_connector = $params['PARAMETERS']['indexpresse_connector'];
		}
		if(!empty($params['PARAMETERS']['indexpresse_maxCount'])) {
			$this->indexpresse_maxCount = $params['PARAMETERS']['indexpresse_maxCount'];
		}
		return $params;
	}

	//Formulaire des propriétés générales
	public function source_get_property_form($source_id) {

		global $charset;

		$this->unserialize_source_params($source_id);

		$form="
			<div class='row'>&nbsp;</div>
				<h3>".$this->msg['indexpresse_ws']."</h3>
			<div class='row'>&nbsp;</div>

			<div class='row'>
				<div class='colonne3'>
					<label for='indexpresse_url'>".$this->msg["indexpresse_url"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='text' name='indexpresse_url' id='indexpresse_url' class='saisie-80em' value='".htmlentities($this->indexpresse_url,ENT_QUOTES,$charset)."'/>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label >".$this->msg["indexpresse_auth_mode"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='radio' name='indexpresse_auth_mode' id='indexpresse_auth_by_ip' value='1' ".(($this->indexpresse_auth_mode==1)?"checked":"")." >".
					"<label for='indexpresse_auth_by_ip' >".$this->msg["indexpresse_auth_by_ip"]."</label>
					</input>
					<input type='radio' name='indexpresse_auth_mode' id='indexpresse_auth_by_account' value='2' ".(($this->indexpresse_auth_mode==2)?"checked":"")." >".
					"<label for='indexpresse_auth_by_account'>".$this->msg["indexpresse_auth_by_account"]."</label>
					</input>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='indexpresse_login' >".$this->msg["indexpresse_login"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='text' name='indexpresse_login' id='indexpresse_login' class='saisie-30em' value='".htmlentities($this->indexpresse_login,ENT_QUOTES,$charset)."'  />
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='indexpresse_pwd' >".$this->msg["indexpresse_pwd"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='password' name='indexpresse_pwd' id='indexpresse_pwd' class='saisie-30em' autocomplete='off' value='".htmlentities($this->indexpresse_pwd,ENT_QUOTES,$charset)."'  />
					<span class='fa fa-eye' onclick='toggle_password(this, \"indexpresse_pwd\");' ></span>
				</div>
			</div>";

		$form.= "
			<div class='row'>&nbsp;</div>
	    		<h3>".$this->msg['indexpresse_search_params']."</h3>
	    	<div class='row'>&nbsp;</div>

	        <div class='row'>
	        	<div class='colonne3'>
	        		<label for='indexpresse_maxCount'>".$this->msg["indexpresse_maxCount"]."</label>
	        	</div>
	        	<div class='colonne_suite'>
	        		<input type='text' name='indexpresse_maxCount' id='indexpresse_maxCount' class='saisie-5em' value='".$this->indexpresse_maxCount."' />
	        	</div>
	        </div>
			<div class='row'>&nbsp;</div>";

		return $form;
	}

	public function make_serialized_source_properties($source_id) {

		global $indexpresse_url, $indexpresse_auth_mode, $indexpresse_login, $indexpresse_pwd;
		global $indexpresse_connector, $indexpresse_maxCount;

		if(empty($indexpresse_url)) {
			$indexpresse_url = '';
		}
		if(empty($indexpresse_auth_mode)) {
			$indexpresse_auth_mode = 1;
		}
		$indexpresse_auth_mode = intval($indexpresse_auth_mode);
		if($indexpresse_auth_mode !=1 && $indexpresse_auth_mode!=2 ) {
			$indexpresse_auth_mode = 1;
		}
		if(empty($indexpresse_login)) {
			$indexpresse_login = '';
		}
		if(empty($indexpresse_pwd)) {
			$indexpresse_pwd = '';
		}
		if(empty($indexpresse_connector)) {
			$indexpresse_connector = 'delphes';
		}
		if(!isset($indexpresse_maxCount)) {
			$indexpresse_maxCount = indexpresse::MAXCOUNT_DEFAULT;
		} else {
			$indexpresse_maxCount = intval($indexpresse_maxCount);
		}

		$this->sources[$source_id]['PARAMETERS'] = serialize(
				[
						'indexpresse_url'		=> stripslashes($indexpresse_url),
						'indexpresse_auth_mode'	=> $indexpresse_auth_mode,
						'indexpresse_login'		=> stripslashes($indexpresse_login),
						'indexpresse_pwd'		=> stripslashes($indexpresse_pwd),
						'indexpresse_connector'	=> $indexpresse_connector,
						'indexpresse_maxCount'	=> $indexpresse_maxCount,
				]
				);
	}

	public function search($source_id, $query, $search_id) {

		//     	$t0 = hrtime(true);

		$this->unserialize_source_params($source_id);

		$this->get_indexpresse_search_fields();

		$indexpresse_search_fields = $this->indexpresse_search_fields[$this->indexpresse_connector]['search_fields'];

		$this->indexpresse_first_queries = [];
		foreach($query as $mterm) {

			if(empty($indexpresse_search_fields[$mterm->ufield])) {
				continue;
			}

			foreach($indexpresse_search_fields[$mterm->ufield] as $criterion) {
				foreach($mterm->values as $value) {
					$this->indexpresse_first_queries[]['query'] = [$criterion=>$value];
				}
			}

		}

		$this->do_first_queries();
		$this->build_next_queries();
		$this->do_next_queries();

		// 		$t1 = hrtime(true);

		$this->prepare_records($source_id, $search_id);
		$this->rec_records();

		// 		$t2 = hrtime(true);

		// 		var_dump("tps rech = ".($t1 - $t0)/1000000000);
		// 		var_dump("tps conv = ".($t2 - $t1)/1000000000);
		// 		var_dump("tps total = ".($t2 - $t0)/1000000000);

	}

	protected function do_first_queries() {

		$curl_ch = $this->get_curl_channel();

		foreach($this->indexpresse_first_queries as $first_query) {

			if($this->indexpresse_auth_mode==2) {
				$first_query['query']['login'] = $this->indexpresse_login;
				$first_query['query']['password'] = $this->indexpresse_pwd;
			}
			$first_query['query']['size'] = indexpresse::NB_RESULTS_PER_PAGE;
			$first_query['query'] = encoding_normalize::utf8_decode($first_query['query']);
			$curl_ch->add_get($this->indexpresse_url, $first_query['query']);
		}

		$curl_ch->run();
		$curl_responses = $curl_ch->get_responses();

		foreach($curl_responses as $k => $curl_response) {

			if ($curl_response['headers']['Status-Code']!='200') {

				$this->indexpresse_first_queries[$k]['status'] = ['curl'=>$curl_response['headers']['Status']];

			} else {

				$response = json_decode(json_encode(simplexml_load_string($curl_response['body'], "SimpleXMLElement", LIBXML_NOCDATA | LIBXML_COMPACT)),TRUE);
				$this->indexpresse_first_queries[$k]['status'] = '200';
				$this->indexpresse_first_queries[$k]['response'] = $response;
				$this->indexpresse_first_queries[$k]['notices_count'] = $response['@attributes']['notices_count'];
				$this->indexpresse_first_queries[$k]['notices_to_retrieve'] = min($response['@attributes']['notices_count'], $this->indexpresse_maxCount);
				$this->indexpresse_first_queries[$k]['nb_next_queries'] = ceil($this->indexpresse_first_queries[$k]['notices_to_retrieve'] / indexpresse::NB_RESULTS_PER_PAGE) - 1;
			}
		}
		$curl_ch->reset();
	}

	protected function build_next_queries() {

		$this->indexpresse_next_queries = [];
		$index = 0;
		foreach($this->indexpresse_first_queries as $first_query) {
			for($i = 0; $i < $first_query['nb_next_queries']; $i++ ) {
				$this->indexpresse_next_queries[$index]['query'] = $first_query['query'];
				$this->indexpresse_next_queries[$index]['query']['page'] = $i + 2;
				$index++;
			}
		}
	}

	protected function do_next_queries() {

		$curl_ch = $this->get_curl_channel();
		$curl_ch->set_mode(multicurl::MODE_MULTI);
		$index = 0;
		$nb_next_queries = count($this->indexpresse_next_queries);

		while( $index < $nb_next_queries ) {

			$nb_queries = 0;
			while($nb_queries < multicurl::MAX_QUERIES && !empty($this->indexpresse_next_queries[$index] )) {
				$next_query = $this->indexpresse_next_queries[$index];
				if($this->indexpresse_auth_mode==2) {
					$next_query['query']['login'] = $this->indexpresse_login;
					$next_query['query']['password'] = $this->indexpresse_pwd;
				}
				$next_query['query']['size'] = indexpresse::NB_RESULTS_PER_PAGE;
				$next_query['query'] = encoding_normalize::utf8_decode($next_query['query']);
				$curl_ch->add_get($this->indexpresse_url, $next_query['query']);
				$nb_queries++;
				$index++;
			}

			$curl_ch->run();
			$curl_responses = $curl_ch->get_responses();

			foreach($curl_responses as $k => $curl_response) {

				if ($curl_response['headers']['Status-Code'] != '200') {

					$this->indexpresse_next_queries[$k]['status'] = ['curl'=>$curl_response['headers']['Status']];

				} else {

					$response = json_decode(json_encode(simplexml_load_string($curl_response['body'], "SimpleXMLElement", LIBXML_NOCDATA | LIBXML_COMPACT)),TRUE);
					$this->indexpresse_next_queries[$k]['status'] = '200';
					$this->indexpresse_next_queries[$k]['response'] = $response;

				}
			}

			$curl_ch->reset();

		}
	}


	protected function prepare_records($source_id, $search_id) {

		if( !is_array($this->indexpresse_first_queries) || empty($this->indexpresse_first_queries)) {
			return;
		}

		foreach($this->indexpresse_first_queries as $first_query) {
			foreach($first_query['response']['notice'] as  $record) {
				$this->prepare_record($record, $source_id, $search_id);
			}
		}

		if( !is_array($this->indexpresse_next_queries) || empty($this->indexpresse_next_queries)) {
			return;
		}
		foreach($this->indexpresse_next_queries as $next_query) {
			foreach($next_query['response']['notice'] as  $record) {
				$this->prepare_record($record, $source_id, $search_id);
			}
		}

	}

	protected function prepare_record($record, $source_id, $search_id) {

		if( !is_array($record) || empty(($record)) ) {
			return;
		}
		if(empty($record['id']) || empty($record['titre'])) {
			return;
		}
		$ref = $record['id'];

		$date_import=date("Y-m-d H:i:s",time());

		//Id deja existant
		if($this->has_ref($source_id, $ref, $search_id)){
			return;
		}

		$type_notice = "Article";
		if(!empty($record['type_notice']) && is_string($record['type_notice'])) {
			$type_notice = $record['type_notice'];
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

		$fields = [
				"id",
				"titre",
				"resume",
				"auteur",
				"permalien",
				"revue",
				"numero_bulletin",
				"date_bulletin",
				"date",
				"pagination",
				"societe",
				"desc_delphes",
				"desc_geo",
		];

		$unimarc_record = [];
		$fo = 0;

		$article_content = [];

		foreach($fields as $k) {

			if(empty($record[$k])) {
				continue;
			}

			$fo++;
			$so = 0;
			switch($k) {
				case 'id' :
					$unimarc_record[] = [
					'ufield' => '001',
					'usubfield' => '',
					'value' => $record[$k],
					'field_order' => $fo,
					'subfield_order' => $so,
					];
					break;

				case 'titre' :
					$unimarc_record[] = [
					'ufield' => '200',
					'usubfield' => 'a',
					'value' => $record[$k],
					'field_order' => $fo,
					'subfield_order' => $so,
					];
					break;

				case 'resume' :
					$unimarc_record[] = [
					'ufield' => '330',
					'usubfield' => 'a',
					'value' => $record[$k],
					'field_order' => $fo,
					'subfield_order' => $so,
					];
					break;

				case 'permalien' :
					$unimarc_record[] = [
					'ufield' => '856',
					'usubfield' => 'u',
					'value' => $record[$k],
					'field_order' => $fo,
					'subfield_order' => $so,
					];
					break;

				case 'auteur' :
					$ufield = '701';
					if (is_string($record[$k])) {
						$ufield = '700';
						$record[$k] = [$record[$k]];
					}
					foreach($record[$k] as $aut) {

						$aut_name = $aut;
						$aut_firstname = '';
						$aut_length = mb_strlen($aut);
						$in_par = mb_strpos($aut, '(');
						$out_par = mb_strrpos($aut, ')');
						if($in_par!==false && $out_par!==false) {
							$aut_name = mb_substr($aut,0,$in_par);
							$aut_firstname = mb_substr($aut, $in_par+1, $aut_length-$out_par-2);
						}
						$unimarc_record[] = [
								'ufield' => $ufield,
								'usubfield' => 'a',
								'value' => $aut_name,
								'field_order' => $fo,
								'subfield_order' => $so,
						];
						$unimarc_record[] = [
								'ufield' => $ufield,
								'usubfield' => 'b',
								'value' => $aut_firstname,
								'field_order' => $fo,
								'subfield_order' => $so,
						];
						$fo++;
					}
					break;

				case 'revue' :
					if($type_notice != 'Article') {
						break;
					}
					$article_content['revue'] = $record[$k];
					break;

				case 'numero_bulletin' :
					if($type_notice != 'Article') {
						break;
					}
					$article_content['numero_bulletin'] = $record[$k];
					break;

				case 'date_bulletin' :
					if($type_notice != 'Article') {
						break;
					}
					$article_content['date_bulletin'] = $record[$k];
					break;

				case 'date' :
					$date = DateTime::createFromFormat('Ymd', $record[$k]);
					$year = '';
					if(!$date) {
						$date = DateTime::createFromFormat('d/m/Y', $record[$k]);
					}
					if($date) {
						$record[$k] = $date->format('d/m/Y');
						$year = $date->format('Y');
					}
					if($year) {
						$unimarc_record[] = [
								'ufield' => '210',
								'usubfield' => 'd',
								'value' => $year,
								'field_order' => $fo,
								'subfield_order' => $so,
						];
					}
					$unimarc_record[] = [
							'ufield' => '219',
							'usubfield' => 'd',
							'value' => $record[$k],
							'field_order' => $fo,
							'subfield_order' => $so,
					];

					if($type_notice == 'Article' && empty($article_content['date_bulletin'])) {
						$article_content['date_bulletin'] = $record[$k];
					}
					break;

				case 'pagination' :
					$unimarc_record[] = [
					'ufield' => '215',
					'usubfield' => 'a',
					'value' => $record[$k],
					'field_order' => $fo,
					'subfield_order' => $so,
					];
					break;

				case "societe" :
					if(is_string($record[$k])) {
						$record[$k] = [$record[$k]];
					}

					foreach($record[$k] as $desc) {
						$unimarc_record[] = [
								'ufield' => '601',
								'usubfield' => 'a',
								'value' => $desc,
								'field_order' => $fo,
								'subfield_order' => $so,
						];
						$fo++;
					}
					break;
				case "desc_delphes" :
					if(is_string($record[$k])) {
						$record[$k] = [$record[$k]];
					}

					foreach($record[$k] as $desc) {
						$unimarc_record[] = [
								'ufield' => '606',
								'usubfield' => 'a',
								'value' => $desc,
								'field_order' => $fo,
								'subfield_order' => $so,
						];
						$fo++;
					}
					break;
				case "desc_geo" :
					if(is_string($record[$k])) {
						$record[$k] = [$record[$k]];
					}

					foreach($record[$k] as $desc) {
						$unimarc_record[] = [
								'ufield' => '607',
								'usubfield' => 'a',
								'value' => $desc,
								'field_order' => $fo,
								'subfield_order' => $so,
						];
						$fo++;
					}
					break;
				default :
					$fo--;
			}
		}

		$unimarc_record[] = [
				'ufield' => '101',
				'usubfield' => 'a',
				'value' => 'fre',
				'field_order' => $fo,
				'subfield_order' => $so,
		];

		if(!count($unimarc_record)) {
			return;
		}

		//verifications article
		if( $type_notice == 'Article'
				&& !empty($article_content['revue'])
				&& !empty($article_content['date_bulletin'])
				&& !empty($article_content['numero_bulletin']) ) {

					$unimarc_headers["bl"] = "a";
					$unimarc_headers["hl"] = "2";
					$unimarc_headers["dt"] = "p";

					$unimarc_record[] = [
							'ufield' => '461',
							'usubfield' => 't',
							'value' => $article_content['revue'],
							'field_order' => $fo,
							'subfield_order' => $so,
					];
					$unimarc_record[] = [
							'ufield' => '461',
							'usubfield' => '9',
							'value' => 'lnk:perio',
							'field_order' => $fo,
							'subfield_order' => $so,
					];
					$fo++;
					$unimarc_record[] = [
							'ufield' => '463',
							'usubfield' => 'v',
							'value' => $article_content['numero_bulletin'],
							'field_order' => $fo,
							'subfield_order' => $so,
					];
					$unimarc_record[] = [
							'ufield' => '463',
							'usubfield' => '9',
							'value' => 'lnk:bull',
							'field_order' => $fo,
							'subfield_order' => $so,
					];
					$fo++;
					$date = DateTime::createFromFormat('Ymd', $article_content['date_bulletin']);
					if(!$date) {
						$date = DateTime::createFromFormat('d/m/Y', $article_content['date_bulletin']);
					}
					if($date) {
						$article_content['date_bulletin'] = $date->format('d/m/Y');
						$unimarc_record[] = ['463', 'd', $date->format('Ymd')];
					}
					$unimarc_record[] = [
							'ufield' => '463',
							'usubfield' => 'e',
							'value' => $article_content['date_bulletin'],
							'field_order' => $fo,
							'subfield_order' => $so,
					];
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


	protected function get_curl_channel() {

		if(!$this->indexpresse_curl_channel) {
			$this->indexpresse_curl_channel = new multicurl();
			$this->indexpresse_curl_channel->set_external_configure_function('configurer_proxy_curl');
			//$this->indexpresse_curl_channel->set_mode(multicurl::MODE_MONO);
			$this->indexpresse_curl_channel->set_mode(multicurl::MODE_MULTI);
		}
		return $this->indexpresse_curl_channel;
	}

	protected function get_indexpresse_search_fields () {

		if(!empty($this->indexpresse_search_fields)) {
			return $this->indexpresse_search_fields;
		}
		$contents = '';
		$search_fields_file = __DIR__.'/indexpresse.json';
		$search_fields_file_subst = __DIR__.'/indexpresse_subst.json';

		if(is_readable($search_fields_file_subst)) {
			$contents = file_get_contents($search_fields_file_subst);
		}
		if(!$contents) {
			if(is_readable($search_fields_file)) {
				$contents = file_get_contents($search_fields_file);
			}
		}
		if(!$contents) {
			return $this->indexpresse_search_fields;
		}
		$this->indexpresse_search_fields = json_decode($contents, true);
		return $this->indexpresse_search_fields;

	}

}
