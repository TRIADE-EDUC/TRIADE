<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: legifrance_dila.class.php,v 1.5.10.1 2025/04/16 12:16:51 dbellamy Exp $

global $class_path;

require_once __DIR__."/legifrance_dila_client.class.php";

class legifrance_dila extends connector {

	protected $legifrance_dila_ws_url = '';
	protected $legifrance_dila_oauth_token_endpoint = '';
	protected $legifrance_dila_client_id = '';
	protected $legifrance_dila_client_secret = '';

	protected $legifrance_dila_fonds = [""];
	protected $legifrance_dila_limit = legifrance_dila_client::SEARCH_PAGESIZE_MAX;
	protected $legifrance_dila_sort = [];
	protected $legifrance_dila_search_facette_all_fond = [];
	protected $legifrance_dila_client = null;

	protected $queries = [];

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::get_id()
	 */
	public function get_id()
	{
		return "legifrance_dila";
	}

	protected function unserialize_source_params($source_id) {

		$params = parent::unserialize_source_params($source_id);
		if(!empty($params['PARAMETERS']['legifrance_dila_ws_url'])) {
			$this->legifrance_dila_ws_url = $params['PARAMETERS']['legifrance_dila_ws_url'];
		} else {
			$this->legifrance_dila_ws_url = legifrance_dila_client::WSURL_DEFAULT;
		}
		if(!empty($params['PARAMETERS']['legifrance_dila_oauth_token_endpoint'])) {
			$this->legifrance_dila_oauth_token_endpoint = $params['PARAMETERS']['legifrance_dila_oauth_token_endpoint'];
		} else {
			$this->legifrance_dila_oauth_token_endpoint = legifrance_dila_client::OAUTH_TOKEN_ENDPOINT_DEFAULT;
		}
		if(!empty($params['PARAMETERS']['legifrance_dila_client_id'])) {
			$this->legifrance_dila_client_id = $params['PARAMETERS']['legifrance_dila_client_id'];
		}
		if(!empty($params['PARAMETERS']['legifrance_dila_client_secret'])) {
			$this->legifrance_dila_client_secret = $params['PARAMETERS']['legifrance_dila_client_secret'];
		}
		if(!empty($params['PARAMETERS']['legifrance_dila_limit'])) {
			$this->legifrance_dila_limit = $params['PARAMETERS']['legifrance_dila_limit'];
		}
		if(!empty($params['PARAMETERS']['legifrance_dila_fonds'])) {
			$this->legifrance_dila_fonds = $params['PARAMETERS']['legifrance_dila_fonds'];
		}
		if(!empty($params['PARAMETERS']['legifrance_dila_sort'])) {
			$this->legifrance_dila_sort = $params['PARAMETERS']['legifrance_dila_sort'];
		}
		if(!empty($params['PARAMETERS']['legifrance_dila_search_facette_all_fond'])) {
			$this->legifrance_dila_search_facette_all_fond = $params['PARAMETERS']['legifrance_dila_search_facette_all_fond'];
		}
		return $params;
	}


	protected function get_client() {
		$this->legifrance_dila_client = new legifrance_dila_client($this->legifrance_dila_client_id, $this->legifrance_dila_client_secret, $this->legifrance_dila_ws_url, $this->legifrance_dila_oauth_token_endpoint);
	}


	//Formulaire des propriétés générales
	public function source_get_property_form($source_id) {

		global $charset;

		$this->unserialize_source_params($source_id);

		$form = "
			<div class='row'>&nbsp;</div>
			<h3>".$this->msg['legifrance_dila_ws']."</h3>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='legifrance_dila_ws_url'>".$this->msg["legifrance_dila_ws_url"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='text' name='legifrance_dila_ws_url' id='legifrance_dila_ws_url' class='saisie-80em' value='".htmlentities($this->legifrance_dila_ws_url,ENT_QUOTES,$charset)."'/>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='legifrance_dila_oauth_token_endpoint'>".$this->msg["legifrance_dila_oauth_token_endpoint"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='text' name='legifrance_dila_oauth_token_endpoint' id='legifrance_dila_oauth_token_endpoint' class='saisie-80em' value='".htmlentities($this->legifrance_dila_oauth_token_endpoint,ENT_QUOTES,$charset)."'/>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='legifrance_dila_client_id' >".$this->msg["legifrance_dila_client_id"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='text' name='legifrance_dila_client_id' id='legifrance_dila_client_id' class='saisie-30em' autocomplete='off' value='".htmlentities($this->legifrance_dila_client_id,ENT_QUOTES,$charset)."'  />
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='legifrance_dila_client_secret' >".$this->msg["legifrance_dila_client_secret"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='password' name='legifrance_dila_client_secret' id='legifrance_dila_client_secret' class='saisie-30em' autocomplete='off' value='".htmlentities($this->legifrance_dila_client_secret,ENT_QUOTES,$charset)."'  />
				<span class='fa fa-eye' onclick='toggle_password(this, \"legifrance_dila_client_secret\");' ></span>
				</div>
			</div>";

		$fonds_selector = $this->get_fonds_selector();

		$form.= "
			<div class='row'>&nbsp;</div>
    			<h3>".$this->msg['legifrance_dila_search_params']."</h3>
			<div class='row'>&nbsp;</div>
	        <div class='row'>
	        	<div class='colonne3'>
	        		<label for='legifrance_dila_limit'>".$this->msg["legifrance_dila_limit"]."</label>
	        	</div>
	        	<div class='colonne_suite'>
	        		<input type='text' name='legifrance_dila_limit' id='legifrance_dila_limit' class='saisie-5em' value='".$this->legifrance_dila_limit."' />
	        	</div>
	        </div>
	        <div class='row'>
	        	<div class='colonne3'>
	        		<label for='legifrance_dila_fonds'>".$this->msg["legifrance_dila_fonds"]."</label>
	        	</div>
	        	<div class='colonne_suite'>
	        		$fonds_selector
	        	</div>
	        </div>
			<div class='row'>&nbsp;</div>";


	        		return $form;
	}


	protected function get_fonds_selector() {

		$availables_fonds = legifrance_dila_client::SEARCH_FOND_AVAILABLES_VALUES;
		$this->get_client();
		$legifrance_dila_search_facette_all_fond = $this->legifrance_dila_client->get_config()["SEARCH_FACETTE_ALL_FOND"]['valeurs'];

		$selector = "
			<div class='row'>
				<div class ='colonne3'>
				</div>
				<div class='colonne_suite'>";
		$selector.= "
			<table class='modern'>
    			<tbody>
					<thead>
						<tr>
							<th>".$this->msg['legifrance_dila_fonds_name']."</th>
							<th>".$this->msg['legifrance_dila_fonds_sort']."</th>
							<th>".$this->msg['legifrance_dila_fonds_filter']."</th>
						</tr>
					</thead>";
		//selecteur de fonds
		foreach($availables_fonds as $v_fonds) {
			$selector.= "
	    			<tr>
						<td>
    						<input type='checkbox' name='legifrance_dila_fonds[]' id='legifrance_dila_fonds_".$v_fonds."' value='".$v_fonds."' ";
			if(in_array($v_fonds, $this->legifrance_dila_fonds)) {
				$selector.= "checked ";
			}
			$selector.= "/>
						<label for='legifrance_dila_fonds_".$v_fonds."'>".$this->msg["legifrance_dila_fonds_".$v_fonds]."</label>
					</td>";

			//selecteur de tri par fonds
			$selector.= "
						<td>
							<select name='legifrance_dila_sort[".$v_fonds."]' >";
			foreach(legifrance_dila_client::SEARCH_SORT_AVAILABLE_VALUES[$v_fonds] as $v_sort) {
				$selector.= "<option value='".$v_sort."' ";
				if($v_sort === $this->legifrance_dila_sort[$v_fonds]) {
					$selector.= "selected ";
				}
				$selector.=">";
				$selector.= $this->msg["legifrance_dila_sort_".$v_sort];
				$selector.= "</option>";

			}
			$selector.= "</select>
						</td>";

			//selecteur de facette pour le fonds ALL
			$selector.= "
						<td>";
			if('ALL' == $v_fonds) {
				foreach($legifrance_dila_search_facette_all_fond as $v_facette) {
					$selector.= "<input type='checkbox' name='legifrance_dila_search_facette_all_fond[]' id='legifrance_dila_search_facette_all_fond_".$v_facette."' value='".$v_facette."' ";
					if(in_array($v_facette, $this->legifrance_dila_search_facette_all_fond)) {
						$selector.= "checked ";
					}

					$selector.= "/>
								<label for='legifrance_dila_search_facette_all_fond_".$v_facette."'>".$this->msg["legifrance_dila_search_facette_all_fond_".$v_facette]."</label>
								<br />";
				}
			}

			$selector.= "
						</td>
					</tr>";

		}
		$selector.= "
				</tbody>
			</table>";
		$selector.= "
			</div>
			</div>";
		return $selector;
	}



	public function make_serialized_source_properties($source_id) {

		global $legifrance_dila_ws_url;
		global $legifrance_dila_oauth_token_endpoint;
		global $legifrance_dila_client_id;
		global $legifrance_dila_client_secret;
		global $legifrance_dila_limit;
		global $legifrance_dila_fonds;
		global $legifrance_dila_sort;
		global $legifrance_dila_search_facette_all_fond;

		if(empty($legifrance_dila_ws_url)) {
			$legifrance_dila_ws_url = '';
		}
		if(empty($legifrance_dila_oauth_token_endpoint)) {
			$legifrance_dila_oauth_token_endpoint = '';
		}
		if(empty($legifrance_dila_client_id)) {
			$legifrance_dila_client_id = '';
		}
		if(empty($legifrance_dila_client_secret)) {
			$legifrance_dila_client_secret = '';
		}
		if(!isset($legifrance_dila_limit)) {
			$legifrance_dila_limit = legifrance_dila_client::SEARCH_PAGESIZE_MAX;
		}
		$legifrance_dila_limit = intval($legifrance_dila_limit);
		if(empty($legifrance_dila_fonds) || !is_array($legifrance_dila_fonds)) {
			$legifrance_dila_fonds = ["ALL"];
		}
		$tmp_legifrance_dila_sort = [];
		foreach(legifrance_dila_client::SEARCH_FOND_AVAILABLES_VALUES as $v_fonds) {
			if( empty($legifrance_dila_sort[$v_fonds]) ) {
				$tmp_legifrance_dila_sort[$v_fonds] = '';
			}
			$tmp_legifrance_dila_sort[$v_fonds] = $legifrance_dila_sort[$v_fonds];
		}
		$legifrance_dila_sort = $tmp_legifrance_dila_sort;
		if(empty($legifrance_dila_search_facette_all_fond) || !is_array($legifrance_dila_search_facette_all_fond)) {
			$legifrance_dila_search_facette_all_fond = [];

		}
		$this->sources[$source_id]['PARAMETERS'] = serialize(
				[
						'legifrance_dila_ws_url'					=> stripslashes($legifrance_dila_ws_url),
						'legifrance_dila_oauth_token_endpoint'		=> stripslashes($legifrance_dila_oauth_token_endpoint),
						'legifrance_dila_client_id'					=> stripslashes($legifrance_dila_client_id),
						'legifrance_dila_client_secret'				=> stripslashes($legifrance_dila_client_secret),
						'legifrance_dila_limit'						=> $legifrance_dila_limit,
						'legifrance_dila_fonds'						=> $legifrance_dila_fonds,
						'legifrance_dila_sort'						=> $legifrance_dila_sort,
						'legifrance_dila_search_facette_all_fond'	=> $legifrance_dila_search_facette_all_fond,
				]
				);
	}


	public function search($source_id, $query, $search_id) {

		//     	$t0 = hrtime(true);

		$this->unserialize_source_params($source_id);
		$this->get_client();

		$legifrance_dila_search_fields = $this->legifrance_dila_client->get_config()["search_fields"];

		$first_queries = [];

		foreach($query as $mterm) {

			if(empty($legifrance_dila_search_fields[$mterm->ufield])) {
				continue;
			}
			foreach($legifrance_dila_search_fields[$mterm->ufield] as $criterion) {
				foreach($mterm->values as $value) {
					$tmp_queries = $this->build_first_queries($criterion, $value);
					if(!empty($tmp_queries)) {
						$first_queries = array_merge($first_queries, $tmp_queries);
					}
				}
			}
		}
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

		//  	$t1 = hrtime(true);

		$this->prepare_records($source_id, $search_id);
		$this->rec_records();

		//     	$t2 = hrtime(true);

		//      var_dump("tps rech = ".($t1 - $t0)/1000000000);
		//      var_dump("tps conv = ".($t2 - $t1)/1000000000);
		//      var_dump("tps total = ".($t2 - $t0)/1000000000);
	}


	protected function build_first_queries($criterion, $value) {

		$queries = [];
		$i = 0;
		foreach($this->legifrance_dila_fonds as $fond) {
			if(in_array($criterion, legifrance_dila_client::SEARCH_TYPECHAMP_AVAILABLE_VALUES[$fond])) {
				$queries[$i]['Query']['fond'] = $fond;
				$queries[$i]['Query']['champs'] = [
						0=> [
								"typeChamp" 	=> $criterion,
								"operateur" 	=> legifrance_dila_client::SEARCH_OPERATEUR_DEFAULT,
								"criteres" 		=> [
										0 => [
												"operateur" 	=> legifrance_dila_client::SEARCH_OPERATEUR_DEFAULT,
												"typeRecherche"	=> legifrance_dila_client::SEARCH_TYPERECHERCHE_DEFAULT,
												"valeur"		=> encoding_normalize::utf8_normalize($value)
										]
								]
						]
				];
				$queries[$i]['Query']['filtres'] = [];
				if( 'ALL' === $fond && !empty($this->legifrance_dila_search_facette_all_fond) ) {
					$queries[$i]['Query']['filtres'] = [
							0=> [
									"facette" => "FOND",
									"valeurs" => $this->legifrance_dila_search_facette_all_fond,
							],
					];
				}
				$queries[$i]['Query']['operateur'] = legifrance_dila_client::SEARCH_OPERATEUR_DEFAULT;
				$queries[$i]['Query']['pageNumber'] = legifrance_dila_client::SEARCH_PAGENUMBER_DEFAULT;
				$queries[$i]['Query']['pageSize'] = min($this->legifrance_dila_limit, legifrance_dila_client::SEARCH_PAGESIZE_MAX);
				$queries[$i]['Query']['sort'] = legifrance_dila_client::SEARCH_SORT_DEFAULT;
				if( !empty($this->legifrance_dila_sort[$fond]) ) {
					$queries[$i]['Query']['sort'] = $this->legifrance_dila_sort[$fond];
				}
				$queries[$i]['Query']['typePagination'] = legifrance_dila_client::SEARCH_TYPEPAGINATION_DEFAULT;
				$i++;
			}
		}
		return $queries;
	}


	protected function build_next_queries($last_queries) {

		$next_queries = [];
		$nb_results_to_retrieve = $this->legifrance_dila_limit;
		foreach($last_queries as $lq) {
			$page_number = $lq['Query']['pageNumber'];
			$nb_results_per_page = $lq['Query']['pageSize'];
			$nb_results_on_last_page = count($lq['Response']['Content']['results']);
			$nb_retrieved_results = (($page_number-1)*$nb_results_per_page) + $nb_results_on_last_page;
			$nb_total_results = $lq['Response']['Content']['totalResultNumber'];

			if( ($nb_retrieved_results < $nb_results_to_retrieve) && ($nb_retrieved_results < $nb_total_results) ) {
				$tmp_lq['Query'] = $lq['Query'];
				$tmp_lq['Query']['pageNumber']++;
				$next_queries[] = $tmp_lq;
			}
		}
		return $next_queries;
	}


	protected function run_queries($queries) {

		foreach($queries as $query) {
			$this->legifrance_dila_client->add_search_query(
					$query['Query']['fond'],
					$query['Query']['champs'],
					$query['Query']['filtres'],
					$query['Query']['operateur'],
					$query['Query']['pageNumber'],
					$query['Query']['pageSize'],
					$query['Query']['sort'],
					$query['Query']['typePagination']
					);
		}
		$this->legifrance_dila_client->run_queries();
		return $this->legifrance_dila_client->get_result();
	}


	protected function prepare_records($source_id, $search_id) {

		if( !is_array($this->queries) || empty($this->queries)) {
			return;
		}
		foreach($this->queries as $query) {

			if( !empty($query['Response']['Content']['results']) ) {
				foreach($query['Response']['Content']['results'] as $record) {
					$this->prepare_record($record, $source_id, $search_id);
				}
			}
		}
	}


	protected function prepare_record($record, $source_id, $search_id) {

		if( !is_array($record) || empty(($record)) ) {
			return;
		}
		//verification presence id (Id document) ?
		if(empty($record['titles'][0]['id'])) {
			return;
		}
		$ref = $record['titles'][0]['id'];

		//Id deja existant
		if($this->has_ref($source_id, $ref, $search_id)){
			return;
		}

		//Verification presence Titre ?
		if(empty($record['titles'][0]['title'])) {
			return;
		}
		$date_import=date("Y-m-d H:i:s",time());

		//Collecte données
		$data = [];

		//Title
		$data['title'] = $record['titles'][0]['title'];

		//Type
		$data['type'] = '';
		if( !empty($record['type']) ) {
			$data['type'] = strtolower($record['type']);
		}
		//Nature
		$data['nature'] = '';
		if( !empty($record['nature']) ) {
			$data['nature'] = strtoupper($record['nature']);
		}

		//origine
		$data['origin'] = '';
		if( !empty($record['origin']) ) {
			$data['origin'] = strtoupper($record['origin']);
		}

		//Typdoc
		$data['typdoc'] = '';
		switch ($data['origin']) {
			//ACCO
			case "ACCO" :
				$data['typdoc'] = $this->msg['legifrance_dila_typdoc_ACCO'];
				break;
				//CETAT
			case "CETAT" :
				$data['typdoc'] = $this->msg['legifrance_dila_typdoc_CETAT'];
				break;
				//CIRC
			case "CIRC" :
				$data['typdoc'] = $this->msg['legifrance_dila_typdoc_CIRC'];
				break;
				//CNIL
			case "CNIL" :
				$data['typdoc'] = $this->msg['legifrance_dila_typdoc_CNIL'];;
				break;
				//CONSTIT
			case "CONSTIT" :
				$data['typdoc'] = $this->msg['legifrance_dila_typdoc_CONSTIT'];
				break;
				//JORF
			case "JORF" :
				$data['typdoc'] = $this->msg['legifrance_dila_typdoc_JORF'];
				break;
				//JUFI
			case "JUFI" :
				$data['typdoc'] = $this->msg['legifrance_dila_typdoc_JUFI'];
				break;
				//JURI
			case "JURI" :
				$data['typdoc'] = $this->msg['legifrance_dila_typdoc_JURI'];
				break;
				//KALI
			case "KALI" :
				$data['typdoc'] = $this->msg['legifrance_dila_typdoc_KALI'];
				break;
				//CODE et LODA
			case "LEGI" :
				$data['typdoc'] = $this->msg['legifrance_dila_typdoc_LEGI'];
				break;
		}


		//Etat
		$data['etat'] = '';
		if( !empty($record['etat']) ) {
			$data['etat'] = strtoupper($record['etat']);
		}

		//Résumé
		$data['abstract'] = '';
		if( !empty($record['text']) ) {
			$data['abstract'] = $record['text'];
		}

		//Chronical Id
		$data['cid'] = '';
		if( !empty($record['titles'][0]['cid']) ) {
			$data['cid'] = $record['titles'][0]['cid'];
		}

		//NOR
		$data['nor'] = '';
		if( !empty($record['nor'])) {
			$data['nor'] = $record['nor'];
		}

		//Date
		$data['datetime'] = '';
		$data['date'] = '';
		if( !empty($record['date']) ) {
			$data['datetime'] = $record['date'];
			$computed_date = static::compute_date($record['date']);
			if(!empty($computed_date)) {
				$data['date'] = $computed_date;
			}
		}

		//Date diffusion
		$data['date_diffusion'] = '';
		if( !empty($record['dateDiffusion']) ) {
			$computed_date = static::compute_date($record['dateDiffusion']);
			if(!empty($computed_date)) {
				$data['date_diffusion'] = $computed_date;
			}
		}

		//Date publication
		$data['date_publication'] = '';
		if( !empty($record['datePublication']) ) {
			$computed_date = static::compute_date($record['datePublication']);
			if(!empty($computed_date)) {
				$data['date_publication'] = $computed_date;
			}
		}

		//Date signature
		$data['date_signature'] = '';
		if( !empty($record['dateSignature']) ) {
			$computed_date = static::compute_date($record['dateSignature']);
			if(!empty($computed_date)) {
				$data['date_signature'] = $computed_date;
			}
		}

		//Numéro
		$data['num'] = '';
		if( !empty($record['num']) ) {
			$data['num'] = $record['num'];
		}
		//Numéro parution
		$data['num_parution'] = '';
		if( !empty($record['numParution']) ) {
			$data['num_parution'] = $record['numParution'];
		}

		//JorfText
		$data['jorf_text'] = '';
		if( !empty($record['jorfText']) ) {
			$data['jorf_text'] = $record['jorfText'];
		}

		//Raison sociale
		$data['raison_sociale'] = '';
		if( !empty($record['raisonSociale']) ) {
			$data['raison_sociale'] = $record['raisonSociale'];
		}

		//Reference
		$data['reference'] = '';
		if( !empty($record['reference']) ) {
			$data['reference'] = $record['reference'];
		}


		$data['url'] = '';
		if( $data['cid'] && $data['origin'] ) {
			switch ($data['origin']) {
				//ACCO
				case "ACCO" :
					$data['url'] = "https://www.legifrance.gouv.fr/acco/id/".$data['cid'];
					break;
					//CETAT
				case "CETAT" :
					$data['url'] = "https://www.legifrance.gouv.fr/ceta/id/".$data['cid'];
					break;
					//CIRC
				case "CIRC" :
					$data['url'] = "https://www.legifrance.gouv.fr/circulaire/id/".$data['cid'];
					break;
					//CNIL
				case "CNIL" :
					$data['url'] = "https://www.legifrance.gouv.fr/cnil/id/".$data['cid'];
					break;
					//CONSTIT
				case "CONSTIT" :
					$data['url'] = "https://www.legifrance.gouv.fr/cons/id/".$data['cid'];
					break;
					//JORF
				case "JORF" :
					$data['url'] = "https://www.legifrance.gouv.fr/jorf/id/".$data['cid'];
					break;
					//JUFI
				case "JUFI" :
					$data['url'] = "https://www.legifrance.gouv.fr/jufi/id/".$data['cid'];
					break;
					//JURI
				case "JURI" :
					$data['url'] = "https://www.legifrance.gouv.fr/juri/id/".$data['cid'];
					break;
					//KALI
				case "KALI" :
					$data['url'] = "https://www.legifrance.gouv.fr/conv_coll/id/".$data['cid'];
					break;
					//CODE et LODA
				case "LEGI" :
					$data['url'] = "https://www.legifrance.gouv.fr/loda/id/".$data['cid'];
					break;
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

		$unimarc_record = [];
		$fo = 0;
		$so = 0;

		//id (Id document)
		$unimarc_record[] = [
				'ufield' => '001',
				'usubfield' => '',
				'value' => $ref,
				'field_order' => $fo,
				'subfield_order' => $so,
		];

		//Title
		$unimarc_record[] = [
				'ufield' => '200',
				'usubfield' => 'a',
				'value' => $data['title'],
				'field_order' => $fo,
				'subfield_order' => $so,
		];

		//Nature
		if( $data['nature'] ) {
			$unimarc_record[] = [
					'ufield' => '200',
					'usubfield' => 'e',
					'value' => $data['nature'],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
		}

		//Résumé
		if( $data['abstract'] ) {
			$unimarc_record[] = [
					'ufield' => '330',
					'usubfield' => 'a',
					'value' => $data['abstract'],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
		}

		//URL vers Legifrance
		if( $data['url'] ) {
			$unimarc_record[] = [
					'ufield' => '856',
					'usubfield' => 'u',
					'value' => $data['url'],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
		}

		//Typdoc
		if( $data['typdoc']) {
			$unimarc_record[] = [
					'ufield' => '900',
					'usubfield' => 'a',
					'value' => $data['typdoc'],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
		}

		//Origine 900b
		if( $data['origin']) {
			$unimarc_record[] = [
					'ufield' => '900',
					'usubfield' => 'b',
					'value' => $data['origin'],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
		}

		//Type 900c
		if( $data['type']) {
			$unimarc_record[] = [
					'ufield' => '900',
					'usubfield' => 'c',
					'value' => $data['type'],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
		}

		//Datetime 900d (date-time unix sur 13 car. ou AAAA-MM-JJ)
		if( $data['datetime']) {
			$unimarc_record[] = [
					'ufield' => '900',
					'usubfield' => 'd',
					'value' => $data['datetime'],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
		}

		//Section id
		if( $data['section_id']) {
			$unimarc_record[] = [
					'ufield' => '900',
					'usubfield' => 'e',
					'value' => $data['section_id'],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
		}

		//ID 901a
		$unimarc_record[] = [
				'ufield' => '901',
				'usubfield' => 'a',
				'value' => $ref,
				'field_order' => $fo,
				'subfield_order' => $so,
		];

		//Chronical Id 901b
		if( $data['cid'] ) {
			$unimarc_record[] = [
					'ufield' => '901',
					'usubfield' => 'b',
					'value' => $data['cid'],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
		}

		//NOR 901c
		if( $data['nor'] ) {
			$unimarc_record[] = [
					'ufield' => '901',
					'usubfield' => 'c',
					'value' => $data['nor'],
					'field_order' => $fo,
					'subfield_order' => $so,
			];
		}

		//ELI 901d >> indisponible

		//Numéro 200h >> indisponible

		//Juridiction 901j >> indisponible

		//Date debut 210d - 219d - 902b >> indisponible

		//Date décision 210d - 219d - 902b >> indisponible


		$done = false;
		//Dates
		if( $data['date'] ) {
			$date = $data['date'];
			$done = true;
		}
		if( !$done && $data['date_diffusion'] ) {
			$date = $data['date_diffusion'];
			$done = true;
		}
		if( !$done && $data['date_publication'] ) {
			$date = $data['date_diffusion'];
			$done = true;
		}
		if( !$done && $data['date_signature'] ) {
			$date = $data['date_diffusion'];
			$done = true;
		}
		if($done) {
			$unimarc_record[] = [
					'ufield' => '210',
					'usubfield' => 'd',
					'value' => $date->format('Y'),
					'field_order' => $fo,
					'subfield_order' => $so,
			];
			$unimarc_record[] = [
					'ufield' => '219',
					'usubfield' => 'd',
					'value' => $date->format('Y-m-d'),
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

	/**
	 * transformation date au format YYYY-MM-DD
	 * @param mixed $date
	 * @return DateTime
	 */
	static protected function compute_date($date) {

		if( preg_match("#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#", $date) ) {
			$datetime = DateTime::createFromFormat('Y-m-d', $date);
			return $date;
		}
		if(preg_match("#^[0-9]{13}$#", $date) ) {
			$datetime = DateTime::createFromFormat('U', (string) $date/1000);
			return $datetime;
		}
		return '';
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


	/**
	 * Liste les appels de fonctions autorisés en ajax
	 * @return array
	 */
	public function get_ajax_allowed_methods() {
		return [
				"get_content",
				"search"
		];
	}


	/**
	 * function de recuperation de contenu
	 *
	 * @param int $source_id
	 * @param string $type
	 * @param string $text_id
	 * @return string
	 */
	public function get_content(int $source_id, string $type, string $text_id, string $date = '', string $section_id = '') {

		$this->unserialize_source_params($source_id);
		$this->get_client();
		$type = strtoupper($type);
		switch($type) {
			case 'ACCO' :
				$this->legifrance_dila_client->add_consult_text_acco_query($text_id);
				break;
			case 'CIRC' :
				$this->legifrance_dila_client->add_consult_text_circ_query($text_id);
				break;
			case 'CNIL' :
				$this->legifrance_dila_client->add_consult_text_cnil_query($text_id);
				break;
			case 'JORF' :
				$this->legifrance_dila_client->add_consult_text_jorf_query($text_id);
				break;
			case 'CETAT' :
			case 'JUFI' :
			case 'JURI' :
				$this->legifrance_dila_client->add_consult_text_juri_query($text_id);
				break;
			case 'KALI' :
				$this->legifrance_dila_client->add_consult_text_kali_query($text_id);
				break;
			case 'CODE' :
				$this->legifrance_dila_client->add_consult_text_code_query($date, $text_id, $section_id);
				break;
			case 'LEGI' :
				$this->legifrance_dila_client->add_consult_text_legi_query($text_id, $date);
				break;
			case 'LODA' :
				$this->legifrance_dila_client->add_consult_text_loda_query($text_id, $date);
				break;
			case 'ARTICLE' :
				$this->legifrance_dila_client->add_consult_article_query($text_id);

		}
		$this->legifrance_dila_client->run_queries();
		$result = $this->legifrance_dila_client->get_result();

		if('200' != $result[0]['Status']) {

			$ret = [
					'error'	=> 1,
					'error_msg' => $result[0]['Status'],
					'content' => '',
			];
		} else {
			$ret = [
					'error' => 0,
					'error_msg' => '',
					'content' => $result[0]['Content'],
			];
		}
		return $ret;

	}


}
