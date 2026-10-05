<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: eds.class.php,v 1.14.4.4 2025/04/16 12:16:52 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $charset, $msg;

require_once __DIR__."/eds_client.class.php";

class eds extends connector {

	protected $eds_url ='';
	protected $eds_login = '';
	protected $eds_pwd = '';
	protected $eds_profile = '';

	protected $eds_maxCount = eds_client::MAXCOUNT_DEFAULT;
	protected $eds_languages = [];

	protected $eds_client = null;

	protected $eds_config = [];
	protected $eds_response = [];
	protected $eds_errors = [];

	protected $queries = [];
	protected $buffer = [];

	public function __construct($connector_path="")
	{
		parent::__construct($connector_path);
		$this->get_eds_config();
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::get_id()
	 */
	public function get_id()
	{
		return "eds";
	}

	public static function getSpecificUnimarcSearchFields()
	{
	    return ['XXX', '010$a', '200$a', '60X', '7XX'];
	}

	protected function unserialize_source_params($source_id) {

		$params = parent::unserialize_source_params($source_id);
		if(!empty($params['PARAMETERS']['eds_url'])) {
			$this->eds_url = $params['PARAMETERS']['eds_url'];
		}
		if(!empty($params['PARAMETERS']['eds_login'])) {
			$this->eds_login = $params['PARAMETERS']['eds_login'];
		}
		if(!empty($params['PARAMETERS']['eds_pwd'])) {
			$this->eds_pwd = $params['PARAMETERS']['eds_pwd'];
		}
		if(!empty($params['PARAMETERS']['eds_profile'])) {
			$this->eds_profile = $params['PARAMETERS']['eds_profile'];
		}
		if(!empty($params['PARAMETERS']['eds_maxCount'])) {
			$this->eds_maxCount = $params['PARAMETERS']['eds_maxCount'];
		}
		if(isset($params['PARAMETERS']['eds_languages']) && is_array($params['PARAMETERS']['eds_languages'])) {
			$this->eds_languages = $params['PARAMETERS']['eds_languages'];
		}
		return $params;
	}

	protected function get_client() {
		$this->eds_client = new eds_client($this->eds_login, $this->eds_pwd, $this->eds_profile, $this->eds_url);
	}

	//Formulaire des propriétés générales
	public function source_get_property_form($source_id) {

		global $charset;

		$this->unserialize_source_params($source_id);

		if(!$this->eds_url) {
			$this->eds_url = eds_client::WSURL_DEFAULT;
		}

		$form = "
			<div class='row'>&nbsp;</div>
				<h3>".$this->msg['eds_ws']."</h3>
			<div class='row'>&nbsp;</div>

			<div class='row'>
				<div class='colonne3'>
					<label for='eds_url'>".$this->msg["eds_url"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='text' name='eds_url' id='eds_url' class='saisie-80em' value='".htmlentities($this->eds_url,ENT_QUOTES,$charset)."'/>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='eds_login' >".$this->msg["eds_login"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='text' name='eds_login' id='eds_login' class='saisie-30em' value='".htmlentities($this->eds_login,ENT_QUOTES,$charset)."'  />
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='eds_pwd' >".$this->msg["eds_pwd"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='password' name='eds_pwd' id='eds_pwd' class='saisie-30em' autocomplete='off' value='".htmlentities($this->eds_pwd,ENT_QUOTES,$charset)."'  />
					<span class='fa fa-eye' onclick='toggle_password(this, \"eds_pwd\");' ></span>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='eds_profile' >".$this->msg["eds_profile"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='text' name='eds_profile' id='eds_profile' class='saisie-30em' value='".htmlentities($this->eds_profile,ENT_QUOTES,$charset)."'  />
				</div>
			</div>
			<div class='row'></div>";

		$form.= "
			<div class='row'>&nbsp;</div>
	    		<h3>".$this->msg['eds_search_params']."</h3>
	    	<div class='row'>&nbsp;</div>

			<div class='row'>
					<div class='colonne3'>
						<label for='eds_maxCount'>".$this->msg["eds_maxCount"]."</label>
					</div>
					<div class='colonne_suite'>
						<input type='text' name='eds_maxCount' id='eds_maxCount' class='saisie-5em' value='".$this->eds_maxCount."' />
					</div>
				</div>";

		$languages_selector = $this->get_languages_selector();

		$form.= "
    	<div class='row'>
    		<div class='colonne3'>
    			<label for='eds_languages' >".$this->msg["eds_languages"]."</label>
    		</div>
    		<div class='colonne_suite'>
				$languages_selector
    		</div>
    	</div>
		<div class='row'></div>";

				return $form;
	}

	protected function get_languages_selector() {

		global $charset;

		$availables_languages = $this->eds_config['eds']['languages'];
		$selector = "<select id='eds_languages' name='eds_languages[]' multiple size='5'>";
		foreach($availables_languages as $k=>$v) {
			$selector.= "<option value='".$k."' ";
			if(in_array($v, $this->eds_languages)) {
				$selector.= "selected ";
			}
			$selector.=">";
			$selector.= htmlentities($v, ENT_QUOTES, $charset);
			$selector.= "</option>";
		}
		$selector.= "</select>";

		return $selector;
	}


	public function make_serialized_source_properties($source_id) {

		global $eds_url, $eds_login, $eds_pwd, $eds_profile;
		global $eds_maxCount, $eds_languages;

		$this->unserialize_source_params($source_id);

		if(empty($eds_url)) {
			$eds_url = '';
		}
		if(empty($eds_login)) {
			$eds_login = '';
		}
		if(empty($eds_pwd)) {
			$eds_pwd = '';
		}
		if(empty($eds_profile)) {
			$eds_profile = '';
		}

		if(!isset($eds_maxCount)) {
			$eds_maxCount = eds_client::MAXCOUNT_DEFAULT;
		} else {
			$eds_maxCount = intval($eds_maxCount);
		}

		$eds_languages_values = [];
		if(is_array($eds_languages) && !empty($eds_languages)) {
			$availables_languages = $this->eds_config['eds']['languages'];
			foreach($eds_languages as $v) {
				if(array_key_exists($v, $availables_languages)) {
					$eds_languages_values[] = $availables_languages[$v];
				}
			}
		}

		$this->sources[$source_id]['PARAMETERS'] = serialize(
				[
						'eds_url'			=> stripslashes($eds_url),
						'eds_login'			=> stripslashes($eds_login),
						'eds_pwd'			=> stripslashes($eds_pwd),
						'eds_profile'		=> stripslashes($eds_profile),
						'eds_maxCount'		=> $eds_maxCount,
						'eds_languages'		=> $eds_languages_values,
				]
				);
	}


	public function search($source_id,$query,$search_id) {

		//    	$t0 = hrtime(true);

		$this->unserialize_source_params($source_id);
		$this->get_client();

		$eds_search_fields = $this->eds_config['eds']['search_fields'];

		$first_queries = [];
		$this->last_queries = [];

		foreach($query as $mterm) {

			if(empty($eds_search_fields[$mterm->ufield])) {
				continue;
			}

			foreach($eds_search_fields[$mterm->ufield] as $criterion) {
				foreach($mterm->values as $value) {
					$aq = new analyse_query($value);
					$boolean_query = $this->build_boolean_query($aq->tree);
					$first_queries[] = ['Criterion'=>$criterion, 'Value'=>$boolean_query];
				}
			}

		}

		$first_queries = $this->add_queries_with_limiters($first_queries);
		$first_queries = $this->add_language_limiters_to_queries($first_queries);
		$first_queries = $this->add_page_number_to_queries($first_queries, 1);

		$first_responses = $this->run_queries($first_queries);
		foreach($first_queries as $k=>$fq) {
			$first_queries[$k]['Response'] = $first_responses[$k];
		}
		unset($first_responses);

		$this->queries = $first_queries;
		$last_queries = $first_queries;
		unset($first_queries);
		do {
			$next_queries = $this->build_next_queries($last_queries);
			if(!empty($next_queries)) {
				$next_responses = $this->run_queries($next_queries);
				foreach($next_queries as $k=>$nq) {
					$next_queries[$k]['Response'] = $next_responses[$k];
				}
				$this->queries = array_merge($this->queries, $next_queries);
				$last_queries = $next_queries;
			}
		} while ( !empty($next_queries) );
		unset($last_queries);

		$this->eds_client->endsession();

		// 		$t1 = hrtime(true);

		$this->prepare_records($source_id, $search_id);
		$this->rec_records();

		//    	$t2 = hrtime(true);

		//     	var_dump("tps rech = ".($t1 - $t0)/1000000000);
		//     	var_dump("tps conv = ".($t2 - $t1)/1000000000);
		//     	var_dump("tps total = ".($t2 - $t0)/1000000000);
	}


	protected function add_queries_with_limiters($queries) {

		if(empty($this->eds_config['eds']['query_limiters'])) {
			return $queries;
		}
		$final_queries = $queries;
		foreach($final_queries as $query) {
			foreach($this->eds_config['eds']['query_limiters'] as $id=>$limiter) {
				$values = explode(',', $limiter[1]);
				$query['Limiters']=[];
				$query['Limiters'][] = [
						"Id" => $id,
						"Values" => $values,
				];

				$final_queries[] = $query;
			}
		}
		return $final_queries;
	}


	protected function add_language_limiters_to_queries($queries) {
		if(empty($this->eds_config['eds']['language_limiters'])) {
			return $queries;
		}
		$final_queries = [];
		foreach($queries as $query) {
			foreach($this->eds_config['eds']['language_limiters'] as $id=>$limiter) {
				$values = explode(',', $limiter[1]);
				$query['Limiters'][] = [
						"Id" => $id,
						"Values" => $values,
				];
				$final_queries[] = $query;
			}
		}
		return $final_queries;
	}


	protected function add_page_number_to_queries($queries, $page = 1) {

		$page = intval($page);
		if(!$page) {
			$page = 1;
		}
		$final_queries = [];
		foreach($queries as $query) {
			$query['PageNumber'] = $page;
			$final_queries[] = $query;
		}
		return $final_queries;
	}


	protected function build_next_queries($last_queries) {

		$next_queries = [];
		$nb_results_to_retrieve = $this->eds_maxCount;

		foreach($last_queries as $lq) {
			$page_number = $lq['Response']['Content']['SearchRequest']['RetrievalCriteria']['PageNumber'];
			$nb_results_per_page = $lq['Response']['Content']['SearchRequest']['RetrievalCriteria']['ResultsPerPage'];
			$nb_results_on_last_page = count($lq['Response']['Content']['SearchResult']['Data']['Records']);
			$nb_retrieved_results = (($page_number-1)*$nb_results_per_page) + $nb_results_on_last_page;
			$nb_total_results = $lq['Response']['Content']['SearchResult']['Statistics']['TotalHits'];

			if( ($nb_retrieved_results < $nb_results_to_retrieve) && ($nb_retrieved_results < $nb_total_results) ) {
				unset($lq['Response']);
				$lq['PageNumber']++;
				$next_queries[] = $lq;
			}
		}
		return $next_queries;
	}


	protected function build_boolean_query($tree) {

		if(!is_array($tree) || empty($tree)) {
			return '';
		}

		$query = '';
		for($i=0 ; $i<count($tree) ; $i++){

			if($tree[$i]->literal !=2 ){

				if($query != ''){
					$query.= ' ';
				}
				if($tree[$i]->operator == "or"){
					$query.= 'or ';
				}
				if($tree[$i]->operator == "and"){
					$query.= 'and ';
				}
				if($tree[$i]->not){
					$query.= 'not ';
				}
				if($tree[$i]->sub){
					$query.=' ('.$this->build_boolean_query($tree[$i]->sub).')';
				}else{
					if($tree[$i]->literal){
						$query.= '"'.encoding_normalize::utf8_normalize($tree[$i]->word).'"';
					} else {
						$query.= encoding_normalize::utf8_normalize($tree[$i]->word);
					}
				}
			}
		}
		return $query;
	}


	protected function run_queries($queries) {

		foreach($queries as $query) {

			$final_query['Queries'] = [[
					"FieldCode"	=>	$query['Criterion'],
					"Term" => $query['Value'],

			]];
			$final_query['SearchMode'] = eds_client::SEARCHMODE_DEFAULT;
			$final_query['ResultsPerPage'] =  eds_client::RESULTSPERPAGE_DEFAULT;
			$final_query['PageNumber'] = $query['PageNumber'];
			$final_query['Sort'] = eds_client::SORT_DEFAULT;
			$final_query['Highlight'] = eds_client::HIGHLIGHT_DEFAULT;
			$final_query['IncludeFacets'] = eds_client::INCLUDEFACETS_DEFAULT;
			$final_query['FacetFilters'] = eds_client::FACETFILTERS_DEFAULT;
			$final_query['View'] = eds_client::VIEW_DEFAULT;
			$final_query['Actions'] = eds_client::ACTIONS_DEFAULT;
			$final_query['limiters'] = eds_client::LIMITERS_DEFAULT;
			if(!empty($query['Limiters'])) {
				$final_query['Limiters'] = $query['Limiters'];
			}
			$final_query['Expanders'] = eds_client::EXPANDERS_DEFAULT;
			$final_query['PublicationId'] = eds_client::PUBLICATIONID_DEFAULT;
			$final_query['RelatedContent'] = eds_client::RELATEDCONTENT_DEFAULT;
			$final_query['AutoSuggest'] = eds_client::AUTOSUGGEST_DEFAULT;
			$final_query['AutoCorrect'] = eds_client::AUTOCORRECT_DEFAULT;
			$final_query['IncludeImageQuickView'] = eds_client::INCLUDEIMAGEQUICKVIEW_DEFAULT;

			$this->eds_client->add_search_request(
					$final_query['Queries'],
					$final_query['SearchMode'],
					$final_query['ResultsPerPage'],
					$final_query['PageNumber'],
					$final_query['Sort'],
					$final_query['Highlight'],
					$final_query['IncludeFacets'],
					$final_query['FacetFilters'],
					$final_query['View'],
					$final_query['Actions'],
					$final_query['Limiters'],
					$final_query['Expanders'],
					$final_query['PublicationId'],
					$final_query['RelatedContent'],
					$final_query['AutoSuggest'],
					$final_query['AutoCorrect'],
					$final_query['IncludeImageQuickView']
					);
		}
		$this->eds_client->run_search();

		return $this->eds_client->get_result();

	}


	protected function prepare_records($source_id, $search_id) {

		if( !is_array($this->queries) || empty($this->queries)) {
			return;
		}
		foreach($this->queries as $query) {
			foreach($query['Response']['Content']['SearchResult']['Data']['Records'] as $record) {
				$record['Limiters'] = $query['Limiters'];
				$this->prepare_record($source_id, $search_id, $record);
			}
		}
	}


	protected function prepare_record($source_id, $search_id, $record) {

		global $charset;
		if( !is_array($record) || empty(($record)) ) {
			return;
		}

		$data = [];

		//Lecture Header
		foreach($record['Header'] as $k=>$record_header) {
			$data['Header_'.$k] = $record_header;
		}
		//verification presence An (Id document) ?
		if(empty($data['Header_An'])) {
			return;
		}
		$ref = $data['Header_An'];

		$date_import=date("Y-m-d H:i:s",time());

		//Id deja existant
		if($this->has_ref($source_id, $ref, $search_id)){
			$this->update_record_limiters($record['Limiters'], $source_id, $search_id, $ref);
			return;
		}

		//Lecture Items
		foreach($record['Items'] as $record_item) {
			$data['Items_'.$record_item['Name']][] = encoding_normalize::utf8_decode(html_entity_decode(strip_tags($record_item['Data']), ENT_QUOTES,'utf-8'));
		}

		//Verification presence Titre ?
		if(empty($data['Items_Title'])) {
			return;
		}

		//Lecture Fulltext/CustomLinks
		if(!empty($record['FullText']['CustomLinks'])) {
			foreach($record['FullText']['CustomLinks'] as $k=>$record_link) {
				if($record_link['Category'] == 'fullText' && !empty($record_link['Url'])) {
					$data['FullText_CustomLinks'][$k] = $record_link['Url'];
				}
			}
		}

		//Lecture RecordInfo/BibRecord/BibEntity/Identifiers
		if( !empty($record['RecordInfo']['BibRecord']['BibEntity']['Identifiers']) ) {
			foreach($record['RecordInfo']['BibRecord']['BibEntity']['Identifiers'] as $k=>$record_identifier) {
				if( !empty($record_identifier['Type']) && $record_identifier['Type'] == 'doi' && !empty($record_identifier['Value']) ) {
					$data['DOI'][$k] = encoding_normalize::utf8_decode($record_identifier['Value']);
				}
			}
		}

		//Lecture RecordInfo/BibRecord/BibEntity/Languages
		if( !empty($record['RecordInfo']['BibRecord']['BibEntity']['Languages']) ) {
			foreach($record['RecordInfo']['BibRecord']['BibEntity']['Languages'] as $k=>$record_language) {
				if(!empty($record_language['Code'])) {
					$data['Languages'][$k] = encoding_normalize::utf8_decode($record_language['Code']);
				}
				if(!empty($record_language['Text'])) {
					$data['Languages'][$k] = encoding_normalize::utf8_decode($record_language['Text']);
				}
			}
		}

		//Lecture RecordInfo/BibRecord/BibEntity/PhysicalDescription/Pagination/PageCount
		if( !empty($record['RecordInfo']['BibRecord']['BibEntity']['PhysicalDescription']['Pagination']['PageCount']) ) {
			$data['PageCount'] = $record['RecordInfo']['BibRecord']['BibEntity']['PhysicalDescription']['Pagination']['PageCount'];
		}

		//Lecture RecordInfo/BibRecord/BibEntity/PhysicalDescription/Pagination/StartPage
		if( !empty($record['RecordInfo']['BibRecord']['BibEntity']['PhysicalDescription']['Pagination']['StartPage']) ) {
			$data['StartPage'] = $record['RecordInfo']['BibRecord']['BibEntity']['PhysicalDescription']['Pagination']['StartPage'];
		}
		//Lecture RecordInfo/BibRecord/BibEntity/Subjects
		if( !empty($record['RecordInfo']['BibRecord']['BibEntity']['Subjects']) ) {
			foreach($record['RecordInfo']['BibRecord']['BibEntity']['Subjects'] as $k=>$record_subject) {
				if(!empty($record_subject['SubjectFull'])) {
					$data['Subjects'][$k] = encoding_normalize::utf8_decode($record_subject['SubjectFull']);
				}
			}
		}

		//Lecture RecordInfo/BibRecord/BibRelationships/HasContributorRelationships
		if( !empty($record['RecordInfo']['BibRecord']['BibRelationships']['HasContributorRelationships']) ) {
			foreach($record['RecordInfo']['BibRecord']['BibRelationships']['HasContributorRelationships'] as $k=>$record_contributor) {
				if(!empty($record_contributor['PersonEntity']['Name']['NameFull'])) {
					$data['PersonEntity'][$k] = encoding_normalize::utf8_decode($record_contributor['PersonEntity']['Name']['NameFull']);
				}
			}
		}

		//Lecture RecordInfo/BibRecord/BibRelationships/IsPartOfRelationships/
		if( !empty($record['RecordInfo']['BibRecord']['BibRelationships']['IsPartOfRelationships']) ) {
			foreach($record['RecordInfo']['BibRecord']['BibRelationships']['IsPartOfRelationships'] as $record_rel) {

				//Lecture RecordInfo/BibRecord/BibRelationships/IsPartOfRelationships/BibEntity/Dates
				if(!empty($record_rel['BibEntity']['Dates'])) {
					foreach($record_rel['BibEntity']['Dates'] as $k=>$record_date) {
						if($record_date['Y']) {
							$data['Year'][$k] = $record_date['Y'];
						}
						if($record_date['D'] && $record_date['M'] && $record_date['Y'] ) {
							$data['DateYMD'][$k] = $record_date['Y'].'-'.$record_date['M'].'-'.$record_date['D'];
						}
						if(!empty($record_date['Text'])) {
							$data['DateText'][$k] = encoding_normalize::utf8_decode($record_date['Text']);
						}
						if(!empty($record_date['Type'])) {
							$data['DateType'][$k] = encoding_normalize::utf8_decode($record_date['Type']);
						}
					}
				}

				//Lecture RecordInfo/BibRecord/BibRelationships/IsPartOfRelationships/BibEntity/Identifiers
				if(!empty($record_rel['BibEntity']['Identifiers'])) {
					foreach($record_rel['BibEntity']['Identifiers'] as $k=>$record_identifier) {
						if($record_identifier['Type']=='issn-print' || $record_identifier['Type']=='issn-electronic' ) {
							$data['ISSN'][$k] = encoding_normalize::utf8_decode($record_identifier['Value']);
						}
						if($record_identifier['Type']=='isbn-print' || $record_identifier['Type']=='isbn-electronic') {
							$data['ISBN'][$k] = encoding_normalize::utf8_decode($record_identifier['Value']);
						}
					}
				}
				//Lecture RecordInfo/BibRecord/BibRelationships/IsPartOfRelationships/BibEntity/Numbering
				if(!empty($record_rel['BibEntity']['Numbering'])) {
					foreach($record_rel['BibEntity']['Numbering'] as $k=>$record_numbering) {
						if($record_numbering['Type']=='volume' ) {
							$data['Volume'] = encoding_normalize::utf8_decode($record_numbering['Value']);
						}
						if($record_numbering['Type']=='issue' ) {
							$data['Issue'] = encoding_normalize::utf8_decode($record_numbering['Value']);
						}
					}
				}

				//Lecture RecordInfo/BibRecord/BibRelationships/IsPartOfRelationships/BibEntity/Titles
				if(!empty($record_rel['BibEntity']['Titles'])) {
					foreach($record_rel['BibEntity']['Titles'] as $k=>$record_titles) {
						if($record_titles['TitleFull']) {
							$data['Relationship_Titles'][$k] = encoding_normalize::utf8_decode(html_entity_decode($record_titles['TitleFull'], ENT_QUOTES, 'utf-8'));
						}
					}
				}
			}

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

		if( !empty($data['Header_PubTypeId']) ) {

			if(array_key_exists($data['Header_PubTypeId'], $this->eds_config['eds']['publication_type_id_to_bl_hl_dt'])) {
				$headers = $this->eds_config['eds']['publication_type_id_to_bl_hl_dt'][$data['Header_PubTypeId']];
				$unimarc_headers['bl'] = $headers[0];
				$unimarc_headers['hl'] = $headers[1];
				$unimarc_headers['dt'] = $headers[2];
			}
		}

		$unimarc_record = [];
		$fo = 0;
		$so = 0;

		//An (Id document)
		$unimarc_record[] = [
				'ufield' => '001',
				'usubfield' => '',
				'value' => $ref,
				'field_order' => $fo,
				'subfield_order' => $so,
		];
		$fo++;

		//ISBN
		if( !empty($data['ISBN']) ) {
			foreach($data['ISBN'] as $isbn) {
				$unimarc_record[] = [
						'ufield' => '010',
						'usubfield' => 'a',
						'value' => $isbn,
						'field_order' => $fo,
						'subfield_order' => $so,
				];
				$fo++;
			}
		}

		//ISSN
		if( !empty($data['ISSN']) ) {
			foreach($data['ISSN'] as $issn) {
				$unimarc_record[] = [
						'ufield' => '011',
						'usubfield' => 'a',
						'value' => $issn,
						'field_order' => $fo,
						'subfield_order' => $so,
				];
				$fo++;
			}
		}

		//Languages
		if( !empty($data['Languages']) ) {
			foreach($data['Languages'] as $language) {
				if(!empty($language)) {
					if(array_key_exists($language, $this->eds_config['eds']['language_text_to_language_code'])) {
						$language = $this->eds_config['eds']['language_text_to_language_code'][$language];
					}
					$unimarc_record[] = [
							'ufield' => '101',
							'usubfield' => 'a',
							'value' => $language,
							'field_order' => $fo,
							'subfield_order' => $so,
					];
				}
				$fo++;
			}
		}

		//Title
		$unimarc_record[] = [
				'ufield' => '200',
				'usubfield' => 'a',
				'value' => $data['Items_Title'][0],
				'field_order' => $fo,
				'subfield_order' => $so,
		];
		$fo++;

		//TitleAlt
		if( !empty($data['TitleAlt']) ) {
			$unimarc_record[] = [
					'ufield' => '200',
					'usubfield' => 'd',
					'value' => $data['Items_TitleAlt'][0],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			$fo++;
		}

		//Relationship_Titles > Editeur
		if( !empty($data['Relationship_Titles']) && $unimarc_headers['bl']=='m') {
			$unimarc_record[] = [
					'ufield' => '210',
					'usubfield' => 'c',
					'value' => $data['Relationship_Titles'][0],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			$fo++;
		}

		//DateYMD > Année
		if( !empty($data['Year'][0]) ) {
			$unimarc_record[] = [
					'ufield' => '210',
					'usubfield' => 'd',
					'value' => $data['Year'][0],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			$fo++;
		}

		//DateYMD > Date de publication
		$formated_date = '';
		if( !empty($data['DateYMD'][0]) ) {
			$date = DateTime::createFromFormat('Y-m-d', $data['DateYMD'][0]);
			if($date) {
				$formated_date = $date->format('d/m/Y');
			}
			if(!$date) {
				$formated_date = $data['DateYMD'];
			}
			$unimarc_record[] = [
					'ufield' => '219',
					'usubfield' => 'd',
					'value' => $formated_date,
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			$fo++;
		}

		//StartPage + PageCount > Nombre de pages
		$pag = 0;
		if( !empty($data['StartPage']) || !empty($data['PageCount']) ) {

			if( !empty($data['PageCount']) && empty($data['StartPage']) ) {
				$pag = $data['PageCount']." p.";
			} elseif ( !empty($data['StartPage']) &&  ( empty($data['PageCount']) || $data['PageCount']==1 )) {
				$pag = "p. ".$data['StartPage'];
			} else {
				$pag = "p. ".$data['StartPage'];
				if(is_numeric($data['StartPage'])) {
					$pag.= '-'. (string) intval($data['StartPage']+$data['PageCount']);
				}
			}
			$unimarc_record[] = [
					'ufield' => '215',
					'usubfield' => '4',
					'value' => $pag,
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			$fo++;
		}

		//Abstract
		if( !empty($data['Items_Abstract']) ) {
			foreach($data['Items_Abstract'] as $abstract) {
				$unimarc_record[] = [
						'ufield' => '330',
						'usubfield' => 'a',
						'value' => $abstract,
						'field_order' => $fo,
						'subfield_order' => $so,
				];
				$fo++;
			}
		}

		//RelationShip_Titles > Periodique
		if( !empty($data['Relationship_Titles']) && $unimarc_headers['bl']=='a') {
			$unimarc_record[] = [
					'ufield' => '461',
					'usubfield' => 't',
					'value' => $data['Relationship_Titles'][0],
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
		}

		//Volume + Issue
		if( ( !empty($data['Volume']) || !empty($data['Issue']) ) && $unimarc_headers['bl']=='a') {
			$vol_num = '';
			if(!empty($data['Volume'])) {
				$vol_num = $this->msg['eds_vol'].$data['Volume'];
			}
			if(!empty($data['Issue'][0])) {
				if(!empty($vol_num)) {
					$vol_num.= " ";
				}
				$vol_num.= $this->msg['eds_num'].$data['Issue'];
			}
			$unimarc_record[] = [
					'ufield' => '463',
					'usubfield' => 'v',
					'value' => $vol_num,
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
		}

		//DateYMD > Date bulletin
		if($formated_date && $unimarc_headers['bl']=='a') {

			$unimarc_record[] = [
					'ufield' => '463',
					'usubfield' => 'd',
					'value' => $data['DateYMD'][0],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			$unimarc_record[] = [
					'ufield' => '463',
					'usubfield' => 'e',
					'value' => $formated_date,
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			$fo++;
		}

		//Subjects
		if(!empty($data['Subjects']) ) {
			foreach($data['Subjects'] as $subject) {
				$unimarc_record[] = [
						'ufield' => '606',
						'usubfield' => 'a',
						'value' => $subject,
						'field_order' => $fo,
						'subfield_order' => $so,
				];
				$fo++;
			}
		}

		//PersonEntity > Auteurs
		if( !empty($data['PersonEntity']) ) {

			if(count($data['PersonEntity']) == 1) {
				$ufield = '700';
			} else {
				$ufield = '701';
			}

			foreach($data['PersonEntity'] as $aut) {

				$tab_aut = explode(',', $aut);
				$aut_name = '';
				if(!empty($tab_aut[0])) {
					$aut_name = $tab_aut[0];
				}
				$aut_firstname = '';
				if(!empty($tab_aut[1])) {
					$aut_firstname = $tab_aut[1];
				}

				$unimarc_record[] = [
						'ufield' => $ufield,
						'usubfield' => 'a',
						'value' => $aut_name,
						'field_order' => $fo,
						'subfield_order' => $so,
				];
				if(!empty($aut_firstname)) {
					$unimarc_record[] = [
							'ufield' => $ufield,
							'usubfield' => 'b',
							'value' => $aut_firstname,
							'field_order' => $fo,
							'subfield_order' => $so,
					];
				}
				$fo++;
			}
		}

		//FullText_CustomLinks > Liens
		if( !empty($data['FullText_CustomLinks']) ) {
			foreach($data['FullText_CustomLinks'] as $link) {
				$unimarc_record[] = [
						'ufield' => '856',
						'usubfield' => 'u',
						'value' => $link,
						'field_order' => $fo,
						'subfield_order' => $so,
				];
				$fo++;
			}
		}

		//Header_PubType > Type de publication
		if( !empty($data['Header_PubType']) ) {
			$unimarc_record[] = [
					'ufield' => '900',
					'usubfield' => 'a',
					'value' => $data['Header_PubType'],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			if( !empty($this->eds_config['eds']['field_to_cp']['Header_PubType']) ) {
				$unimarc_record[] = [
						'ufield' => '900',
						'usubfield' => 'n',
						'value' => $this->eds_config['eds']['field_to_cp']['Header_PubType'],
						'field_order' => $fo,
						'subfield_order' => $so,
				];
				$unimarc_record[] = [
						'ufield' => '900',
						'usubfield' => 't',
						'value' => 'text',
						'field_order' => $fo,
						'subfield_order' => $so,
				];
			}
			$fo++;
		}

		//Header_DbLabel > Source
		if( !empty($data['Header_DbLabel']) ) {
			$unimarc_record[] = [
					'ufield' => '900',
					'usubfield' => 'a',
					'value' => $data['Header_DbLabel'],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			if( !empty($this->eds_config['eds']['field_to_cp']['Header_DbLabel']) ) {
				$unimarc_record[] = [
						'ufield' => '900',
						'usubfield' => 'n',
						'value' => $this->eds_config['eds']['field_to_cp']['Header_DbLabel'],
						'field_order' => $fo,
						'subfield_order' => $so,
				];
				$unimarc_record[] = [
						'ufield' => '900',
						'usubfield' => 't',
						'value' => 'text',
						'field_order' => $fo,
						'subfield_order' => $so,
				];
			}
			$fo++;
		}

		//DOI
		if( !empty($data['DOI']) ) {
			foreach($data['DOI'] as $doi) {
				$unimarc_record[] = [
						'ufield' => '900',
						'usubfield' => 'a',
						'value' => $doi,
						'field_order' => $fo,
						'subfield_order' => $so,
				];
				$unimarc_record[] = [
						'ufield' => '900',
						'usubfield' => 'b',
						'value' => "http://dx.doi.org/".$doi,
						'field_order' => $fo,
						'subfield_order' => $so,
				];
				$unimarc_record[] = [
						'ufield' => '900',
						'usubfield' => 'c',
						'value' => $doi."|2",
						'field_order' => $fo,
						'subfield_order' => $so,
				];
				if( !empty($this->eds_config['eds']['field_to_cp']['DOI']) ) {
					$unimarc_record[] = [
							'ufield' => '900',
							'usubfield' => 'n',
							'value' => $this->eds_config['eds']['field_to_cp']['DOI'],
							'field_order' => $fo,
							'subfield_order' => $so,
					];
					$unimarc_record[] = [
							'ufield' => '900',
							'usubfield' => 't',
							'value' => 'resolve',
							'field_order' => $fo,
							'subfield_order' => $so,
					];
				}
				$fo++;
			}
		}

		//Dépôt d'archives institutionnel uniquement (dépôt HAL, DbId=ir01198a)
		if( !empty($data['Header_DbId']) && $data['Header_DbId']=='ir01198a' ) {
			$unimarc_record[] = [
					'ufield' => '903',
					'usubfield' => 'a',
					'value' => 'msg:40',
					'field_order' => $fo,
					'subfield_order' => $so,
			];
		} else {
			$unimarc_record[] = [
					'ufield' => '903',
					'usubfield' => 'a',
					'value' => 'msg:39',
					'field_order' => $fo,
					'subfield_order' => $so,
			];
		}

		if(empty($unimarc_record)) {
			return;
		}

		$this->buffer['search_id'] = $search_id;
		$this->buffer['source_id'] = $source_id;
		$this->buffer['date_import'] = $date_import;
		$this->buffer['records'][$ref]['header'] = $unimarc_headers;
		$this->buffer['records'][$ref]['content'] = $unimarc_record;
		$this->init_record_limiters($ref);
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


	protected function init_record_limiters($ref) {

		if(empty($this->eds_config['eds']['query_limiters'])) {
			return ;
		}

		foreach(array_keys($this->eds_config['eds']['query_limiters']) as $id) {
			$ufield = $this->eds_config['eds']['query_limiters'][$id][2];
			$usubfield = 'a';
			$value = 'msg:39';
			$this->buffer['records'][$ref]['content']["$ufield-$usubfield-0-0"] = [
					'ufield' => $ufield,
					'usubfield' => $usubfield,
					'value' => $value,
					'field_order' => 0,
					'subfield_order' => 0,
			];

		}
	}


	protected function update_record_limiters($limiters, $ref) {

		if(empty($this->eds_config['eds']['query_limiters'])) {
			return ;
		}

		foreach($limiters as $limiter) {
			if(!empty($this->eds_config['eds']['query_limiters'][$limiter['Id']])) {
				$ufield = $this->eds_config['eds']['query_limiters'][$limiter['Id']][2];
				$usubfield = 'a';
				$value = 'msg:40';
				$this->buffer['records'][$ref]['content']["$ufield-$usubfield-0-0"] = [
						'ufield' => $ufield,
						'usubfield' => $usubfield,
						'value' => $value,
						'field_order' => 0,
						'subfield_order' => 0,
				];
			}
		}
	}

	protected function get_eds_config () {

		if(!empty($this->eds_config)) {
			return $this->eds_config;
		}
		$contents = '';
		$search_fields_file = __DIR__.'/eds.json';
		$search_fields_file_subst = __DIR__.'/eds_subst.json';

		if(is_readable($search_fields_file_subst)) {
			$contents = file_get_contents($search_fields_file_subst);
		}
		if(!$contents) {
			if(is_readable($search_fields_file)) {
				$contents = file_get_contents($search_fields_file);
			}
		}
		if(!$contents) {
			return $this->eds_config;
		}
		$this->eds_config = json_decode($contents, true);

		if(!empty($this->eds_config['eds']['query_limiters'])) {
			foreach($this->eds_config['eds']['query_limiters'] as $k=>$limiter) {
				$this->eds_config['eds']['query_limiters'][$limiter[0]] = $limiter;
				unset($this->eds_config['eds']['query_limiters'][$k]);
			}
		}
		if(!empty($this->eds_config['eds']['language_limiters'])) {
			foreach($this->eds_config['eds']['language_limiters'] as $k=>$limiter) {
				$this->eds_config['eds']['language_limiters'][$limiter[0]] = $limiter;
				unset($this->eds_config['eds']['language_limiters'][$k]);
			}
		}
		return $this->eds_config;
	}

}
