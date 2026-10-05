<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: synchro_rdf.class.php,v 1.12.8.1 2025/04/30 08:32:19 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/noeuds.class.php");
require_once($class_path."/categories.class.php");
require_once($class_path."/notice.class.php");

class synchro_rdf{
	// proprietes
	public $config = array(
			'db_host' => SQL_SERVER,
			'db_name' => DATA_BASE,
			'db_user' => USER_NAME,
			'db_pwd' => USER_PASS,
			'store_name' => 'synchroRdf',
			'max_errors' => 100,
			'store_strip_mb_comp_str' => 0,
			'endpoint_features' => array(
				'select',
				//'construct',
				//'ask',
				//'describe',
				//'load',
				'insert',
				'delete',
				//'dump' 
			)
	);
	public $baseURI = "http://www.sigb.net/";
	public $baseUriConcept;
	public $baseUriThesaurus;
	public $baseUriManifestation;
	public $baseUriExpression;
	public $baseUriOeuvre;
	public $baseUriAuteur;
	
	public $entiteMapping=array();
	public $auteurMapping=array();
	public $bulletinMapping=array();

	public $store;
	public $prefix="PREFIX dc: <http://purl.org/dc/elements/1.1/> \n
			PREFIX rdagroup1elements: <http://rdvocab.info/Elements/> \n
			PREFIX rdagroup2elements: <http://RDVocab.info/ElementsGr2/> \n
			PREFIX rdarelationships: <http://rdvocab.info/RDARelationshipsWEMI/> \n
			PREFIX bnf-onto: <http://data.bnf.fr/ontology/bnf-onto/> \n
			PREFIX frbr-rda: <http://rdvocab.info/uri/schema/FRBRentitiesRDA/> \n
			PREFIX foaf: <http://xmlns.com/foaf/0.1/> \n
			PREFIX ore: <http://www.openarchives.org/ore/terms/> \n
			PREFIX skos: <http://www.w3.org/2004/02/skos/core#> \n";	

	// constructeur
	public function __construct($session_id=0,$activateEndpoint=false,$altBaseUri='') {
		global $charset;
		
		//Pour créer des tables temporaires
		if($session_id){
			$this->config['store_name']=$session_id.$this->config['store_name'];
		}
		
		//endpoint pour connecteurs
		if($activateEndpoint){
			
			$this->store = ARC2::getStoreEndpoint($this->config);
			$this->store->go();
		}else{
			
			//on initialise les tables mysql
			$this->initStore();
			if($charset=='utf-8'){
				pmb_mysql_query("SET NAMES 'utf8'");
			}
			//on initialise les uri
			if(trim($altBaseUri)){
				$this->baseURI=$altBaseUri;
			}
			$this->prefix.="
			PREFIX pmb: <".$this->baseURI.">\n\n";
			$this->baseUriConcept = $this->baseURI."concept#";
			$this->baseUriThesaurus = $this->baseURI."thesaurus#";
			$this->baseUriManifestation = $this->baseURI."manifestation#";
			$this->baseUriExpression = $this->baseURI."expression#";
			$this->baseUriOeuvre = $this->baseURI."oeuvre#";
			$this->baseUriOeuvreBulletin = $this->baseURI."oeuvre#fromBulletin";
			$this->baseUriAuteur = $this->baseURI."auteur#";
			
			//on charge les correspondances rdf
			$this->loadMapping();
		}
		
		return;
	}
	
	public function loadMapping(){
		global $class_path;
	
		$xmlFile = $class_path."/synchro_rdf.xml";
		$fp=fopen($xmlFile,"r");
		if ($fp) {
			$xml=fread($fp,filesize($xmlFile));
			fclose($fp);
		}
		$mapping=_parser_text_no_function_($xml,"MAPPING");
	
	
		foreach($mapping['OBJECT'] as $object){
			$target=(isset($object['TARGET']) ? $object['TARGET'] : '');
			$targetList=explode(",",$target);
			$arrayName=$object['TYPE']."Mapping";
			if($object['TYPE']=='entite'){
				$arrayFields=array();
				foreach($object['RDFFIELD'] as $field){
					$detail=array();
					foreach($field['FIELD'] as $fieldBis){
						$detail[$fieldBis['CODE_CHAMP']."_".$fieldBis['CODE_SS_CHAMP']."_".$fieldBis['ORDRE']]=1;
					}
					$arrayFields[$field['NAME']]=array(
							'function'=>$field['FUNCTION'],
							'lang'=>(isset($field['LANG']) ? $field['LANG'] : ''),
							'distinct'=>$field['DISTINCT'],
							'detail'=>$detail
					);
				}
				foreach($targetList as $target){
					$this->entiteMapping[$target][$object['NAME']]=array(
							'uniqueVar'=>(isset($object['UNIQUEVAR']) ? $object['UNIQUEVAR'] : ''),
							'definition'=>$object['DEFINITIONTRIPLET'][0],
							'fields'=>$arrayFields,
							'links'=>(isset($object['LINK']) ? $object['LINK'] : ''),
							'authors'=>(isset($object['AUTHORS']) ? $object['AUTHORS'] : '')
					);
				}
			}else{
				$this->$arrayName=$object;
			}
		}
		//Deuxième passe pour les mêmes entités mais en cas particuliers
		foreach($mapping['OBJECTBIS'] as $object){
			$target=$object['TARGET'];
			$targetList=explode(",",$target);
			$arrayName=$object['TYPE']."Mapping";
			if($object['TYPE']=='entite'){
				$arrayFields=array();
				foreach($object['RDFFIELD'] as $field){
					$detail=array();
					foreach($field['FIELD'] as $fieldBis){
						$detail[$fieldBis['CODE_CHAMP']."_".$fieldBis['CODE_SS_CHAMP']."_".$fieldBis['ORDRE']]=1;
					}
					$arrayFields[$field['NAME']]=array(
							'function'=>$field['FUNCTION'],
							'lang'=>(isset($field['LANG']) ? $field['LANG'] : ''),
							'distinct'=>$field['DISTINCT'],
							'detail'=>$detail
					);
				}
				foreach($targetList as $target){
					$this->entiteMapping[$target][$object['NAME']]=array(
							'uniqueVar'=>(isset($object['UNIQUEVAR']) ? $object['UNIQUEVAR'] : ''),
							'definition'=>$object['DEFINITIONTRIPLET'][0],
							'fields'=>$arrayFields,
							'links'=>(isset($object['LINK']) ? $object['LINK'] : ''),
							'authors'=>(isset($object['AUTHORS']) ? $object['AUTHORS'] : '')
					);
				}
			}else{
				$this->$arrayName=$object;
			}
		}
	
		return;
	}
	
	public function initStore(){
		$this->store = ARC2::getStore($this->config);
		if (!$this->store->isSetUp()) {
			$this->store->setUp();
		}
		return;
	}
	
	public function truncateStore(){
		$this->store->reset();
		return;
	}
	
	public function exportStoreXml(){
		//Récupération des préfixes
		$ns = array();
		$tmpArray=explode("\n",$this->prefix);
		foreach($tmpArray as $prefix){
			if(preg_match('`PREFIX (.+): \<(.+)\>`',$prefix,$out)){
				$ns[$out[1]]=$out[2];
			}
		}

		$conf = array('ns' => $ns);
		$ser = ARC2::getRDFXMLSerializer($conf);
		$all = $this->store->query("SELECT ?s ?p ?o WHERE { ?s ?p ?o }");
		$rdfxml2 = $ser->getSerializedTriples($all["result"]['rows']);

		return $rdfxml2;
	}
	
	public function existsUri($uri){
		$q ="SELECT * WHERE {
  				".$uri." rdf:type ?o .
			}
			LIMIT 1";

		$r = $this->store->query($q);
		if (is_array($r['result']['rows'])) {
			if(count($r['result']['rows'])){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	public function existsTriple($arrayTriple){
		$q =$this->prefix."SELECT * WHERE {
  				".$arrayTriple[0]." ".$arrayTriple[1]." ".$arrayTriple[2]." .
  				?s ?p ?o
			}
			LIMIT 1";

		$r = $this->store->query($q);
		if (is_array($r['result']['rows'])) {
			if(count($r['result']['rows'])){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	public function deleteTriple($s,$p,$o,$filter=''){
		$q =$this->prefix."DELETE {
				   ".$s." ".$p." ".$o.".
				}";
		if(trim($filter)){
			$q.=" WHERE {
				".$s." ".$p." ".$o.".
				FILTER (".$filter.")
				}";
		}
		$rows = $this->store->query($q);

		if ($errs = $this->store->getErrors()) {
			echo "<br>Erreurs: <br>";
			echo "<pre>";print_r($errs);echo "</pre><br>";
		}
		return;
	}
	
	public function storeTriples($arrayTriples){
		if(count($arrayTriples)){
			
			$q = $this->prefix."INSERT INTO <pmb> {\n";
			$qt=count($arrayTriples)-1;
			foreach($arrayTriples as $inc=>$triplet){
				$q.=$triplet[0].' '.$triplet[1].' '.$triplet[2];
				if($inc!=$qt){
					$q.=".";
				}
				$q.="\n";
			}
			$q.= "}\n";
			$rows = $this->store->query($q);

			if ($errs = $this->store->getErrors()) {
				echo "<br>Erreurs: <br>";
				echo "<pre>";print_r($errs);echo "</pre><br>";
			}
		}
		return;
	}
	
	public function updateTripleLinks($uri1,$uri2){
		//DELETE-INSERT-WHERE ne fonctionnant pas sur notre version actuelle de sparql, on est obligés de faire une procédure au lieu d'une simple requête
		
		$arrayTriples=array();
		//Pour la première passe, on distingue les URI ou non en objet
		$q ="SELECT * WHERE {
  				".$uri1." ?p ?o .
  			   FILTER ( !isIRI(?o) )
			}";
		$r = $this->store->query($q);
		if (is_array($r['result']['rows'])) {
			if(count($r['result']['rows'])){
				foreach($r['result']['rows'] as $row){
					$triple=array();
					$triple[0]=$uri1;
					$triple[1]="<".$row['p'].">";
					$triple[2]=$row['o'];
					$arrayTriples[0][]=$triple;
				}
			}
		}
		$q ="SELECT * WHERE {
  				".$uri1." ?p ?o .
  			   FILTER ( isIRI(?o) )
			}";
		$r = $this->store->query($q);
		if (is_array($r['result']['rows'])) {
			if(count($r['result']['rows'])){
				foreach($r['result']['rows'] as $row){
					$triple=array();
					$triple[0]=$uri1;
					$triple[1]="<".$row['p'].">";
					$triple[2]="<".$row['o'].">";
					$arrayTriples[0][]=$triple;
				}
			}
		}
		
		$q ="SELECT * WHERE {
  				?s ?p ".$uri1." .
			}";
		$r = $this->store->query($q);
		if (is_array($r['result']['rows'])) {
			if(count($r['result']['rows'])){
				foreach($r['result']['rows'] as $row){
					$triple=array();
					$triple[0]="<".$row['s'].">";
					$triple[1]="<".$row['p'].">";
					$triple[2]=$uri1;
					$arrayTriples[1][]=$triple;
				}
			}
		}
		
		if(count($arrayTriples)){
			//suppression-modification
			foreach($arrayTriples as $k=>$arrayV){
				if(count($arrayV)){
					foreach($arrayV as $kbis=>$v){
						$this->deleteTriple($v[0],$v[1],$v[2]);
						if(!$k){
							$arrayTriples[$k][$kbis][0]=$uri2;
						}else{
							$arrayTriples[$k][$kbis][2]=$uri2;
						}
					}
				}
			}
			//ajout
			foreach($arrayTriples as $k=>$arrayV){
				if(count($arrayV)){
					$this->storeTriples($arrayV);
				}
			}
		}
		return;
	}
	
	public function updateAuthority($id,$typeAuthority){
		if($typeAuthority=='oeuvre'){
			$query="SELECT ntu_num_notice as idNotice FROM notices_titres_uniformes WHERE ntu_num_tu=".$id." LIMIT 1";
			$baseUri=$this->baseUriOeuvre;
		}elseif($typeAuthority=='auteur'){
			$query="SELECT responsability_notice as idNotice FROM responsability WHERE responsability_author=".$id." LIMIT 1";
			$baseUri=$this->baseUriAuteur;
		}elseif($typeAuthority=='editeur'){
			//cas spécifique des éditeurs : on met à jour le contenu de chaque notice l'utilisant
			$res=pmb_mysql_query("SELECT notice_id FROM notices WHERE ed1_id=".$id);
			while($row=pmb_mysql_fetch_object($res)){
				$this->delRdf($row->notice_id,0);
				$this->addRdf($row->notice_id,0);
			}
			return;
		}elseif($typeAuthority=='thesaurus'){
			$this->delThesaurusDefinition($id);
			$this->storeThesaurusDefinition($id);
			return;
		}else{
			return;
		}
	
		//S'il y a une notice avec le titre uniforme ou l'auteur, l'oeuvre ou l'auteur est dans le graphe rdf : on met à jour
		$res=pmb_mysql_query($query);
		if(pmb_mysql_num_rows($res)){
			$row=pmb_mysql_fetch_object($res);
			//On récupère le rdf de la notice
			$arrayRdfNotice=$this->getRdfNotice($row->idNotice);
			$okTrouve=false;
			foreach($arrayRdfNotice as $typeObject=>$objects){
				foreach($objects as $uri=>$detail){
					if(preg_match('`^'.str_replace('/','\/',$baseUri).'(\d+)$`',$uri,$out)){
						if($out[1]==$id){
							$this->deleteTriple("<".$uri.">","?p","?o","!isIRI(?o)");
							$this->deleteTriple("<".$uri.">","rdf:type","?o");
							$this->storeTriples($detail['definition']);
							$this->storeTriples($detail['data']);
							$okTrouve=true;
							break;
						}
					}
				}
				if($okTrouve){
					break;
				}
			}
		}
		return;
	}
	
	public function replaceAuthority($fromId,$toId,$typeAuthority){
		if($typeAuthority=='oeuvre'){
			$query="SELECT ntu_num_notice as idNotice FROM notices_titres_uniformes WHERE ntu_num_tu=".$toId." LIMIT 1";
			$baseUri=$this->baseUriOeuvre;
		}elseif($typeAuthority=='auteur'){
			$query="SELECT responsability_notice as idNotice FROM responsability WHERE responsability_author=".$toId." LIMIT 1";
			$baseUri=$this->baseUriAuteur;
		}elseif($typeAuthority=='editeur'){
			//cas spécifique des éditeurs : on met à jour le contenu de chaque notice l'utilisant
			$res=pmb_mysql_query("SELECT notice_id FROM notices WHERE ed1_id=".$toId);
			while($row=pmb_mysql_fetch_object($res)){
				$this->delRdf($row->notice_id,0);
				$this->addRdf($row->notice_id,0);
			}
			return;
		}else{
			return;
		}
	
		//L'autorité est présente dans le graphe ?
		$uriFrom=$baseUri.$fromId;
		$uriTo=$baseUri.$toId;
		if($this->existsUri("<".$uriFrom.">")){
			//On efface la définition et les datas
			$this->deleteTriple("<".$uriFrom.">","rdf:type","?o");
			$this->deleteTriple("<".$uriFrom.">","?p","?o","!isIRI(?o)");
			//Il reste à mettre à jour les liens concernés avec la nouvelle autorité
			if(!$this->existsUri("<".$uriTo.">")){
				//La nouvelle autorité n'est pas dans le graphe : on récupère son contenu depuis une notice liée
				$res=pmb_mysql_query($query);
				$row=pmb_mysql_fetch_object($res);
				$arrayRdfNotice=$this->getRdfNotice($row->idNotice);
				$okTrouve=false;
				foreach($arrayRdfNotice as $typeObject=>$objects){
					foreach($objects as $uri=>$detail){
						if(preg_match('`^'.str_replace('/','\/',$baseUri).'(\d+)$`',$uri,$out)){
							if($out[1]==$toId){
								$this->storeTriples($detail['definition']);
								$this->storeTriples($detail['data']);
								$okTrouve=true;
								break;
							}
						}
					}
					if($okTrouve){
						break;
					}
				}
			}
			//update liens
			$this->updateTripleLinks("<".$uriFrom.">","<".$uriTo.">");
		}
		return;
	}
	
	public function getRdfNotice($idNotice){
		$arrayTriples=array();
		$arrayNotice=array();
		$exportedUris=array();
		
		$res=pmb_mysql_query("SELECT * FROM notices_fields_global_index WHERE id_notice=".$idNotice." ORDER BY id_notice, code_champ, code_ss_champ, ordre") or die();
		while($row=pmb_mysql_fetch_object($res)){
			$arrayNotice[$row->code_champ][$row->code_ss_champ][$row->ordre]=array(
					'lang'=>$row->lang,
					'value'=>$row->value,
					'authority_num'=>$row->authority_num
			);
		}
		
		$notice=new notice($idNotice);
		$niveauB=strtolower($notice->niveau_biblio);
		
		$titreManifestation='';
		//On parcourt les objets du mapping
		foreach($this->entiteMapping[$niveauB] as $entiteName=>$entiteDetail){
			//L'entité est répétable ? (oeuvres)
			$tmpArray=explode("_",$entiteDetail['definition']['IDROW']);
			if(trim($tmpArray[2])){
				$maxOrdre=(int)$tmpArray[2];
			}else{
				$maxOrdre=count($arrayNotice[$tmpArray[0]][$tmpArray[1]]);
			}
			//Pour chaque occurence
			for($ordre=1;$ordre<=$maxOrdre;$ordre++){
				//on crée l'uri de l'entité
				$baseNameEntite="baseUri".ucfirst($entiteName);
				if($entiteDetail['definition']['IDFIELD']=='id_notice'){
					$uri=$this->$baseNameEntite.$idNotice;
				}else{
					$tmpRow=$entiteDetail['definition']['IDROW'];
					$tmpCodes=explode("_",$tmpRow);
					$uri=$this->$baseNameEntite.$arrayNotice[$tmpCodes[0]][$tmpCodes[1]][$ordre][$entiteDetail['definition']['IDFIELD']];
					if(!isset($arrayNotice[$tmpCodes[0]][$tmpCodes[1]][$ordre][$entiteDetail['definition']['IDFIELD']])){
						continue;
					}
				}
				$uriManifestation=$this->baseUriManifestation.$idNotice;
				//on vérifie que l'entité n'a pas déjà été exportée
				if((!count($exportedUris))||(!in_array($uri,$exportedUris))){
					//on crée l'entité
					//1-définition
					$triplet=array();
					$triplet[0]='<'.$uri.'>';
					$triplet[1]=$entiteDetail['definition']['DT1'];
					$triplet[2]=$entiteDetail['definition']['DT2'];
					$arrayTriples[$entiteName][$uri]['definition'][]=$triplet;
					//2-les champs
					foreach($entiteDetail['fields'] as $fieldName=>$fieldDetail){
						$currentValues=array();
						$distinctValues=array();
						foreach($fieldDetail['detail'] as $tmpRow=>$tmp){
							$tmpCodes=explode("_",$tmpRow);
							if(trim($tmpCodes[2])){
								if($tmpValue=($arrayNotice[$tmpCodes[0]][$tmpCodes[1]][$tmpCodes[2]]['value'])){
									$currentValues[]=array('code'=>$tmpRow,'value'=>$tmpValue);
								}
							}else{
								if($tmpArray=($arrayNotice[$tmpCodes[0]][$tmpCodes[1]])){
									foreach($tmpArray as $arrayValues){
										if($fieldDetail['distinct']=="1"){
											if($arrayValues['lang']==$fieldDetail['lang']){
												$distinctValues[]=array('code'=>$tmpRow,'value'=>$arrayValues['authority_num']);
											}
										}else{
											if($arrayValues['lang']==$fieldDetail['lang']){
												$currentValues[]=array('code'=>$tmpRow,'value'=>$arrayValues['authority_num']);
											}
										}
									}
								}
							}
						}
						if(count($distinctValues)){
							foreach($distinctValues as $values){
								$triplet=array();
								$triplet[0]='<'.$uri.'>';
								$triplet[1]=$fieldName;
								if($function=trim($fieldDetail['function'])){
									$triplet[2]='"'.addslashes($this->$function($values['value'])).'"';
								}else{
									$triplet[2]='"'.addslashes($values['value']).'"';
								}
								$arrayTriples[$entiteName][$uri]['data'][]=$triplet;
							}
						}elseif(count($currentValues)){
							$triplet=array();
							$triplet[0]='<'.$uri.'>';
							$triplet[1]=$fieldName;
							if($function=trim($fieldDetail['function'])){
								//Il y a une fonction : on l'applique sur les valeurs des triplets du champ
								$triplet[2]='"'.addslashes($this->$function($currentValues)).'"';
							}else{
								$triplet[2]='"'.addslashes($currentValues[0]['value']).'"';
							}
							$arrayTriples[$entiteName][$uri]['data'][]=$triplet;
						}
						//On récupère le titre de la manifestation en cas de notice sans titre uniforme (donc sans oeuvre)
						if(($entiteName=='manifestation') && ($triplet[1]=='dc:title')){
							$titreManifestation=$triplet[2];
						}
					}
				}
				//on crée les liens
				if(is_array($entiteDetail['links']) && count($entiteDetail['links'])){
					foreach($entiteDetail['links'] as $link){
						$triplet=array();
						$triplet[0]='<'.$uriManifestation.'>';
						$triplet[1]=$link['TYPE'];
						$triplet[2]='<'.$uri.'>';
						$arrayTriples[$entiteName][$uri]['links'][]=$triplet;
					}
				}
				//on enregistre le fait que l'entité a déjà été créée
				$exportedUris[]=$uri;
				//cas particulier des auteurs liés
				if(is_array($entiteDetail['authors']) && count($entiteDetail['authors'])){
					foreach($entiteDetail['authors'][0]['FIELD'] as $author){
						if(!empty($arrayNotice[$author['CODE_CHAMP']][$author['CODE_SS_CHAMP']])){
							foreach($arrayNotice[$author['CODE_CHAMP']][$author['CODE_SS_CHAMP']] as $auteurNotice){
								$uriAuteur=$this->baseUriAuteur.$auteurNotice[$author['IDFIELD']];
								$triplet=array();
								$triplet[0]='<'.$uri.'>';
								$triplet[1]=$author['LINK'];
								$triplet[2]='<'.$uriAuteur.'>';
								$arrayTriples['author'][$uriAuteur]['links'][]=$triplet;
								if((!count($exportedUris))||(!in_array($uriAuteur,$exportedUris))){
									//L'auteur n'a pas encore été défini
									$res=pmb_mysql_query("SELECT * FROM ".$this->auteurMapping['TABLE']." WHERE ".$this->auteurMapping['KEY']."=".$auteurNotice[$author['IDFIELD']]) or die();
									if(pmb_mysql_num_rows($res)){
										$row=pmb_mysql_fetch_object($res);
										$authorType=$row->{$this->auteurMapping['AUTHORTYPE']};
										if(count($this->auteurMapping['DEFINITIONTRIPLET'.$authorType])){
											//définition
											$triplet=array();
											$triplet[0]='<'.$uriAuteur.'>';
											$triplet[1]=$this->auteurMapping['DEFINITIONTRIPLET'.$authorType][0]['DT1'];
											$triplet[2]=$this->auteurMapping['DEFINITIONTRIPLET'.$authorType][0]['DT2'];
											$arrayTriples['author'][$uriAuteur]['definition'][]=$triplet;
											//propriétés
											foreach($this->auteurMapping['RDFFIELD'.$authorType] as $field){
												$ajoutTriplet=true;
												$triplet=array();
												$triplet[0]='<'.$uriAuteur.'>';
												$triplet[1]=$field['NAME'];
												if($value=trim($row->{$field['FIELD'][0]['NAME']})){
													$arrayValues=array();
													foreach($field['FIELD'] as $myField){
														$arrayValues[]=array('value'=>trim($row->{$myField['NAME']}));
													}
													if($function=trim($field['FUNCTION'])){
														//Il y a une fonction : on l'applique sur les valeurs des triplets du champ
														if($tmp=$this->$function($arrayValues)){
															$triplet[2]='"'.addslashes($tmp).'"';
														}else{
															$ajoutTriplet=false;
														}
													}else{
														$triplet[2]='"'.addslashes($arrayValues[0]['value']).'"';
													}
													if($ajoutTriplet){
														$arrayTriples['author'][$uriAuteur]['data'][]=$triplet;
													}
												}
											}
										}
									}
									//on enregistre le fait que l'entité a déjà été créée
									$exportedUris[]=$uriAuteur;
								}
							}
						}
					}
				}
			}
		}
		//Cas des périodiques : pas de lien vers la manifestation !
		unset($arrayTriples['oeuvre'][$uri]['links']);
		//Cas des articles
		if($niveauB=='a'){
			$res=pmb_mysql_query("SELECT analysis_bulletin FROM analysis WHERE analysis_notice=".$idNotice);
			$row=pmb_mysql_fetch_object($res);
			//liens
			$triplet=array();
			$triplet[0]='<'.$this->baseUriOeuvreBulletin.$row->analysis_bulletin.'>';
			$triplet[1]='ore:aggregates';
			$triplet[2]='<'.$this->baseUriOeuvre.$idNotice.'>';
			$arrayTriples['oeuvre'][$this->baseUriOeuvre.$idNotice]['links'][]=$triplet;
			$triplet=array();
			$triplet[0]='<'.$this->baseUriOeuvre.$idNotice.'>';
			$triplet[1]='ore:isAggregatedBy';
			$triplet[2]='<'.$this->baseUriOeuvreBulletin.$row->analysis_bulletin.'>';
			$arrayTriples['oeuvre'][$this->baseUriOeuvre.$idNotice]['links'][]=$triplet;
		}
		//Cas des monographies sans titre uniforme
		if($niveauB=='m'){
			if(!isset($arrayTriples['oeuvre'])){
				//On crée une oeuvre de toutes pièces
				$uriOeuvre=$this->baseUriOeuvre."fromNotice".$idNotice;
				//Définition
				$triplet=array();
				$triplet[0]='<'.$uriOeuvre.'>';
				$triplet[1]='rdf:type';
				$triplet[2]='frbr-rda:Work';
				$arrayTriples['oeuvre'][$uriOeuvre]['definition'][]=$triplet;
				//Data
				$triplet=array();
				$triplet[0]='<'.$uriOeuvre.'>';
				$triplet[1]='dc:title';
				$triplet[2]=$titreManifestation;
				$arrayTriples['oeuvre'][$uriOeuvre]['definition'][]=$triplet;
				//Lien
				$triplet=array();
				$triplet[0]='<'.$uriManifestation.'>';
				$triplet[1]='rdarelationships:workManifested';
				$triplet[2]='<'.$uriOeuvre.'>';
				$arrayTriples['oeuvre'][$uriOeuvre]['links'][]=$triplet;
				//auteurs liés à l'oeuvre
				$resAuteurs=pmb_mysql_query("SELECT DISTINCT responsability_author FROM responsability WHERE responsability_notice=".$idNotice." AND responsability_type IN (0,1)");
				while($rowAuteurs=pmb_mysql_fetch_object($resAuteurs)){
					$uriAuteur=$this->baseUriAuteur.$rowAuteurs->responsability_author;
					$triplet=array();
					$triplet[0]='<'.$uriOeuvre.'>';
					$triplet[1]='dc:contributor';
					$triplet[2]='<'.$uriAuteur.'>';
					$arrayTriples['author'][$uriAuteur]['links'][]=$triplet;
					$resAuteur=pmb_mysql_query("SELECT * FROM ".$this->auteurMapping['TABLE']." WHERE ".$this->auteurMapping['KEY']."=".$rowAuteurs->responsability_author);
					if(pmb_mysql_num_rows($resAuteur)){
						$rowAuteur=pmb_mysql_fetch_object($resAuteur);
						$authorType=$rowAuteur->{$this->auteurMapping['AUTHORTYPE']};
						if(count($this->auteurMapping['DEFINITIONTRIPLET'.$authorType])){
							//définition
							$triplet=array();
							$triplet[0]='<'.$uriAuteur.'>';
							$triplet[1]=$this->auteurMapping['DEFINITIONTRIPLET'.$authorType][0]['DT1'];
							$triplet[2]=$this->auteurMapping['DEFINITIONTRIPLET'.$authorType][0]['DT2'];
							$arrayTriples['author'][$uriAuteur]['definition'][]=$triplet;
							//propriétés
							foreach($this->auteurMapping['RDFFIELD'.$authorType] as $field){
								$ajoutTriplet=true;
								$triplet=array();
								$triplet[0]='<'.$uriAuteur.'>';
								$triplet[1]=$field['NAME'];
								if($value=trim($rowAuteur->{$field['FIELD'][0]['NAME']})){
									$arrayValues=array();
									foreach($field['FIELD'] as $myField){
										$arrayValues[]=array('value'=>trim($rowAuteur->{$myField['NAME']}));
									}
									if($function=trim($field['FUNCTION'])){
										//Il y a une fonction : on l'applique sur les valeurs des triplets du champ
										if($tmp=$this->$function($arrayValues)){
											$triplet[2]='"'.addslashes($tmp).'"';
										}else{
											$ajoutTriplet=false;
										}
									}else{
										$triplet[2]='"'.addslashes($arrayValues[0]['value']).'"';
									}
									if($ajoutTriplet){
										$arrayTriples['author'][$uriAuteur]['data'][]=$triplet;
									}
								}
							}
						}
					}
				}
			}
		}
		
		return $arrayTriples;
	}
	
	public function getRdfBulletin($idBulletin){
		$arrayTriples=array();
		
		$res=pmb_mysql_query("SELECT * FROM bulletins WHERE bulletin_id=".$idBulletin);
		$row=pmb_mysql_fetch_object($res);
		$uriOeuvreBulletin=$this->baseUriOeuvreBulletin.$idBulletin;
		//1-oeuvre
			//définition
			$triplet=array();
			$triplet[0]='<'.$uriOeuvreBulletin.'>';
			$triplet[1]=$this->bulletinMapping['DEFINITIONTRIPLET'][0]['DT1'];
			$triplet[2]=$this->bulletinMapping['DEFINITIONTRIPLET'][0]['DT2'];
			$arrayTriples['oeuvre'][$uriOeuvreBulletin]['definition'][]=$triplet;
			//propriétés
			foreach($this->bulletinMapping['RDFFIELD'] as $field){
				$triplet=array();
				$triplet[0]='<'.$uriOeuvreBulletin.'>';
				$triplet[1]=$field['NAME'];
				$arrayValues=array();
				foreach ($field['FIELD'] as $fieldName){
				    if($tmp=trim($row->{$fieldName['NAME']})){
						$arrayValues[]['value']=$tmp;
					}
				}
				if($function=trim($field['FUNCTION'])){
					$value=$this->$function($arrayValues);
				}else{
					$value=$arrayValues[0]['value'];
				}
				if(trim($value)){
					$triplet[2]='"'.addslashes($value).'"';
					$arrayTriples['oeuvre'][$uriOeuvreBulletin]['data'][]=$triplet;
				}
			}
		//2-auteurs liés à l'oeuvre
			$resAuteurs=pmb_mysql_query("SELECT DISTINCT responsability_author FROM responsability WHERE responsability_notice=".$row->num_notice." AND responsability_type IN (0,1)");
			while($rowAuteurs=pmb_mysql_fetch_object($resAuteurs)){
				$uriAuteur=$this->baseUriAuteur.$rowAuteurs->responsability_author;
				$triplet=array();
				$triplet[0]='<'.$uriOeuvreBulletin.'>';
				$triplet[1]='dc:contributor';
				$triplet[2]='<'.$uriAuteur.'>';
				$arrayTriples['author'][$uriAuteur]['links'][]=$triplet;
				$resAuteur=pmb_mysql_query("SELECT * FROM ".$this->auteurMapping['TABLE']." WHERE ".$this->auteurMapping['KEY']."=".$rowAuteurs->responsability_author);
				if(pmb_mysql_num_rows($resAuteur)){
					$rowAuteur=pmb_mysql_fetch_object($resAuteur);
					$authorType=$rowAuteur->{$this->auteurMapping['AUTHORTYPE']};
					if(count($this->auteurMapping['DEFINITIONTRIPLET'.$authorType])){
						//définition
						$triplet=array();
						$triplet[0]='<'.$uriAuteur.'>';
						$triplet[1]=$this->auteurMapping['DEFINITIONTRIPLET'.$authorType][0]['DT1'];
						$triplet[2]=$this->auteurMapping['DEFINITIONTRIPLET'.$authorType][0]['DT2'];
						$arrayTriples['author'][$uriAuteur]['definition'][]=$triplet;
						//propriétés
						foreach($this->auteurMapping['RDFFIELD'.$authorType] as $field){
							$ajoutTriplet=true;
							$triplet=array();
							$triplet[0]='<'.$uriAuteur.'>';
							$triplet[1]=$field['NAME'];
							if($value=trim($rowAuteur->{$field['FIELD'][0]['NAME']})){
								$arrayValues=array();
								foreach($field['FIELD'] as $myField){
									$arrayValues[]=array('value'=>trim($rowAuteur->{$myField['NAME']}));
								}
								if($function=trim($field['FUNCTION'])){
									//Il y a une fonction : on l'applique sur les valeurs des triplets du champ
									if($tmp=$this->$function($arrayValues)){
										$triplet[2]='"'.addslashes($tmp).'"';
									}else{
										$ajoutTriplet=false;
									}
								}else{
									$triplet[2]='"'.addslashes($arrayValues[0]['value']).'"';
								}
								if($ajoutTriplet){
									$arrayTriples['author'][$uriAuteur]['data'][]=$triplet;
								}
							}
						}
					}
				}
			}
		//3-manif/expression
		if($row->num_notice){
			$arrayRdfNotice=$this->getRdfNotice($row->num_notice);
			$arrayTriples=array_merge_recursive($arrayTriples,$arrayRdfNotice);
			//liens
			$triplet=array();
			$triplet[0]='<'.$this->baseUriManifestation.$row->num_notice.'>';
			$triplet[1]='rdarelationships:workManifested';
			$triplet[2]='<'.$uriOeuvreBulletin.'>';
			$arrayTriples['oeuvre'][$uriOeuvreBulletin]['links'][]=$triplet;
		}
		//4-périodique : liens
			$triplet=array();
			$triplet[0]='<'.$this->baseUriOeuvre.$row->bulletin_notice.'>';
			$triplet[1]='ore:aggregates';
			$triplet[2]='<'.$uriOeuvreBulletin.'>';
			$arrayTriples['oeuvre'][$uriOeuvreBulletin]['links'][]=$triplet;
			$triplet=array();
			$triplet[0]='<'.$uriOeuvreBulletin.'>';
			$triplet[1]='ore:isAggregatedBy';
			$triplet[2]='<'.$this->baseUriOeuvre.$row->bulletin_notice.'>';
			$arrayTriples['oeuvre'][$uriOeuvreBulletin]['links'][]=$triplet;
		
		return $arrayTriples;
	}
	
	public function addRdf($idNotice,$idBulletin){
		if($idNotice){
			$arrayRdf=$this->getRdfNotice($idNotice);
		}else{
			$arrayRdf=$this->getRdfBulletin($idBulletin);
		}
		//le rdf est composé de types d'objet (oeuvre, manifestation, expression, auteur)
		foreach($arrayRdf as $typeObject=>$objects){
			//pour chaque objet
			foreach($objects as $uri=>$detail){
				if(!$this->existsUri('<'.$uri.'>')){
					$this->storeTriples($detail['definition']);
					$this->storeTriples($detail['data']);
				}
				if(is_array($detail['links']) && count($detail['links'])){
					foreach($detail['links'] as $link){
						if(!$this->existsTriple($link)){
							$this->storeTriples(array($link));
						}
					}
				}
			}
		}
		return;
	}
	
	public function getUris($idNotice,$idBulletin){
		$arrayUris=array();
		if($idNotice){
			$arrayUris['oeuvre'][]=$this->baseUriOeuvre."fromNotice".$idNotice;
			$arrayUris['manifestation'][]=$this->baseUriManifestation.$idNotice;
			$arrayUris['expression'][]=$this->baseUriExpression.$idNotice;
			$notice=new notice($idNotice);
			$niveauB=strtolower($notice->biblio_level);
			if($niveauB=="m"){
				$res=pmb_mysql_query("SELECT ntu_num_tu FROM notices_titres_uniformes WHERE ntu_num_notice=".$idNotice);
				if(pmb_mysql_num_rows($res)){
					while($row=pmb_mysql_fetch_object($res)){
						$arrayUris['oeuvre'][]=$this->baseUriOeuvre.$row->ntu_num_tu;
					}
				}
			}elseif(($niveauB=="a") || ($niveauB=="s")){
				$arrayUris['oeuvre'][]=$this->baseUriOeuvre.$idNotice;
			}
		}else{
			$arrayUris['oeuvre'][]=$this->baseUriOeuvre."fromBulletin".$idBulletin;
			$res=pmb_mysql_query("SELECT num_notice FROM bulletins WHERE bulletin_id=".$idBulletin);
			$row=pmb_mysql_fetch_object($res);
			if($row->num_notice){
				$arrayUris['manifestation'][]=$this->baseUriManifestation.$row->num_notice;
				$arrayUris['expression'][]=$this->baseUriExpression.$row->num_notice;
			}
		}
		
		return $arrayUris;
	}
	
	public function delRdf($idNotice,$idBulletin){
		if($idNotice){
			$arrayListUri=$this->getUris($idNotice,0);
		}else{
			$arrayListUri=$this->getUris(0,$idBulletin);
		}
		//On supprime les manifestations (les liens sont automatiquement supprimés)
		if(count($arrayListUri['manifestation'])){
			foreach($arrayListUri['manifestation'] as $uri){
				$this->deleteTriple('<'.$uri.'>', '?p', '?o');
			}
		}
		//On supprime les expressions (les liens sont automatiquement supprimés)
		if(count($arrayListUri['expression'])){
			foreach($arrayListUri['expression'] as $uri){
				$this->deleteTriple('<'.$uri.'>', '?p', '?o');
			}
		}
		//Cas de figure des oeuvres
		if($idNotice){
			$notice=new notice($idNotice);
			$niveauB=strtolower($notice->biblio_level);
			switch($niveauB){
				case "s" :
					if(count($arrayListUri['oeuvre'])){
						foreach($arrayListUri['oeuvre'] as $uri){
							//on efface l'oeuvre
							$this->deleteTriple('<'.$uri.'>', '?p', '?o');
						}
					}
					break;
				case "a" :
					if(count($arrayListUri['oeuvre'])){
						foreach($arrayListUri['oeuvre'] as $uri){
							//on efface l'oeuvre
							$this->deleteTriple('<'.$uri.'>', '?p', '?o');
							//on efface aussi les liens
							$this->deleteTriple('?s', '?p', '<'.$uri.'>');
						}
					}
					break;
				case "b" :
					if(count($arrayListUri['oeuvre'])){
						foreach($arrayListUri['oeuvre'] as $uri){
							//on efface l'oeuvre
							$this->deleteTriple('<'.$uri.'>', '?p', '?o');
							//on efface aussi les liens
							$this->deleteTriple('?s', '?p', '<'.$uri.'>');
						}
					}
					break;
				case "m" :
					//Cas très particulier : on ne supprime que si le titre uniforme n'est pas utilisé par une autre notice
					//sinon, on ne supprime que les liens de tous les auteurs de la notice qui ne sont plus utilisés
					if(count($arrayListUri['oeuvre'])){
						foreach($arrayListUri['oeuvre'] as $uri){
							preg_match('`^'.str_replace('/','\/',$this->baseUriOeuvre).'(.+)$`',$uri,$tmpArray);
							$idOeuvre=$tmpArray[1];
							//Il est important de laisser les apostrophes sur la requête car on peut avoir soit un id=X, soit un id=fromNoticeX
							$res=pmb_mysql_query("SELECT ntu_num_notice FROM notices_titres_uniformes WHERE ntu_num_tu='".$idOeuvre."' AND ntu_num_notice<>".$idNotice) or die("SELECT ntu_num_notice FROM notices_titres_uniformes WHERE ntu_num_tu='".$idOeuvre."' AND ntu_num_notices<>".$idNotice);
							if(!pmb_mysql_num_rows($res)){
								//Pas d'autre notice liée
								//on efface l'oeuvre
								$this->deleteTriple('<'.$uri.'>', '?p', '?o');
								//on efface aussi les liens
								$this->deleteTriple('?s', '?p', '<'.$uri.'>');
							}else{
								//On va chercher tous les auteurs liés à l'oeuvre dans le graphe
								$arrayAuteursOeuvre=array();
								$q =$this->prefix."SELECT ?o WHERE {
									   { <".$uri."> dc:contributor ?o . }
									UNION { <".$uri."> dc:creator ?o . }
									}";
								$rBis = $this->store->query($q);
								if (is_array($rBis['result']['rows'])) {
									if(count($rBis['result']['rows'])){
										foreach($rBis['result']['rows'] as $resultBis){
											if(preg_match('`^'.str_replace('/','\/',$this->baseUriAuteur).'(\d+)$`',$resultBis['o'],$out)){
												$arrayAuteursOeuvre[]=$out[1];
											}
										}
									}
								}
								//On va chercher tous les auteurs liés aux autres notices liées à l'oeuvre
								$arrayAuteursNotices=array();
								$res=pmb_mysql_query("SELECT DISTINCT responsability_author FROM responsability WHERE responsability_notice IN (
										SELECT DISTINCT ntu_num_notice FROM notices_titres_uniformes
											WHERE ntu_num_tu=".$idOeuvre." AND ntu_num_notice<>".$idNotice."
										)");
								while($row=pmb_mysql_fetch_object($res)){
									$arrayAuteursNotices[]=$row->responsability_author;
								}
								//Pour chaque auteur présent dans $arrayAuteursOeuvre et non présent dans $arrayAuteursNotices : on supprime le lien
								$diff = array_diff($arrayAuteursOeuvre,$arrayAuteursNotices);
								if(count($diff)){
									foreach($diff as $idAuteur){
										$this->deleteTriple("<".$uri.">","?p","<".$this->baseUriAuteur.$idAuteur.">");
									}
								}
							}
						}
					}
					break;
				default : //ne devrait pas arriver
					break;
			}
		}else{
			//on efface une oeuvre de bulletin
			if(count($arrayListUri['oeuvre'])){
				foreach($arrayListUri['oeuvre'] as $uri){
					//on efface l'oeuvre
					$this->deleteTriple('<'.$uri.'>', '?p', '?o');
					//on efface aussi les liens
					$this->deleteTriple('?s', '?p', '<'.$uri.'>');
				}
			}
		}
		//on efface les auteurs n'étant plus utilisés
		$this->cleanAuthors();
		return;
	}
	
	public function cleanAuthors(){
		//on va chercher tous les auteurs du graphe non utilisés
		$q =$this->prefix."SELECT ?s WHERE {
				{ 
					{ ?s rdf:type foaf:Person. }
			    	UNION { ?s rdf:type foaf:Organization. } 
				}
			    OPTIONAL { ?o dc:contributor ?s }
			    OPTIONAL { ?o1 dc:creator ?s }
			    OPTIONAL { ?o2 foaf:focus ?s }
			    FILTER (!bound(?o))
			    FILTER (!bound(?o1))
			    FILTER (!bound(?o2))
			}";

		$r = $this->store->query($q);
		if (is_array($r['result']['rows'])) {
			if(count($r['result']['rows'])){
				foreach($r['result']['rows'] as $result){
					$this->deleteTriple("<".$result['s'].">","?p","?o");
				}
			}
		}
	}

	/*
	 * Méthodes thésaurus
	 */
	
	public function storeThesaurusDefinition($idThes){
		$arrayRdfThes=$this->getRdfThesaurus($idThes);
		$this->storeTriples($arrayRdfThes);
		return;
	}
	
	public function delThesaurusDefinition($idThes){
		$uriThes=$this->baseUriThesaurus.$idThes;
		$this->deleteTriple('<'.$uriThes.'>','?p','?o');
	}
	
	public function storeConcept($idNoeud){
		$arrayRdfConcept=$this->getRdfConcept($idNoeud);
		$this->storeTriples($arrayRdfConcept);
		return;
	}
	
	public function delConcept($idNoeud){
		$uriConcept=$this->baseUriConcept.$idNoeud;
		$this->deleteTriple('<'.$uriConcept.'>','?p','?o');
		return;
	}
	
	public function getRdfThesaurus($idThes){
		$arrayTriples=array();
		$uriThes=$this->baseUriThesaurus.$idThes;
		
		$thes = new thesaurus($idThes);
		
		//Type
		$triple=array();
		$triple[0]='<'.$uriThes.'>';
		$triple[1]="rdf:type";
		$triple[2]="skos:ConceptScheme";
		$arrayTriples[]=$triple;
		//Label
		$triple=array();
		$triple[0]='<'.$uriThes.'>';
		$triple[1]="skos:prefLabel";
		$triple[2]='"'.addslashes($thes->getLibelle()).'"';
		$arrayTriples[]=$triple;
		//topConcepts
		$resBis=pmb_mysql_query("SELECT id_noeud FROM noeuds WHERE num_parent='".$thes->num_noeud_racine."' AND  num_renvoi_voir='0' AND autorite != 'ORPHELINS' AND num_thesaurus='".$idThes."'");
		while($rowBis=pmb_mysql_fetch_object($resBis)){
			$triple=array();
			$triple[0]='<'.$uriThes.'>';
			$triple[1]="skos:hasTopConcept";
			$triple[2]='<'.$this->baseUriConcept.$rowBis->id_noeud.'>';
			$arrayTriples[]=$triple;
		}
		
		return $arrayTriples;
	}
	
	public function getRdfConcept($idNoeud){
		global $lang;
		
		$arrayTriples=array();
		
		$noeud=new noeuds($idNoeud);
		$thes=new thesaurus($noeud->num_thesaurus);
		$uriConcept=$this->baseUriConcept.$idNoeud;
		$uriThes=$this->baseUriThesaurus.$noeud->num_thesaurus;
		
		//Si le noeud possède un renvoi-voir, la catégorie n'est pas dans le graphe, il n'y a que son libellé en altLabel sur le renvoi
		if($noeud->num_renvoi_voir){
			return $arrayTriples;
		}
		
		//Type
		$triple=array();
		$triple[0]='<'.$uriConcept.'>';
		$triple[1]="rdf:type";
		$triple[2]="skos:Concept";
		$arrayTriples[]=$triple;
		//Appartenance au schéma
		$triple=array();
		$triple[0]='<'.$uriConcept.'>';
		$triple[1]="skos:inScheme";
		$triple[2]='<'.$uriThes.'>';
		$arrayTriples[]=$triple;
		//Catégorie
		$categ=new categories($idNoeud,$thes->langue_defaut);
		//Label
		$triple=array();
		$triple[0]='<'.$uriConcept.'>';
		$triple[1]="skos:prefLabel";
		$triple[2]='"'.addslashes($categ->libelle_categorie).'"';
		$arrayTriples[]=$triple;
		//Note application
		if($tmp = trim($categ->note_application)){
			$triple=array();
			$triple[0]='<'.$uriConcept.'>';
			$triple[1]="skos:scopeNote";
			$triple[2]='"'.$tmp.'"';
			$arrayTriples[]=$triple;
		}
		//Commentaire public
		if($tmp = trim($categ->comment_public)){
			$triple=array();
			$triple[0]='<'.$uriConcept.'>';
			$triple[1]="skos:note";
			$triple[2]='"'.$tmp.'"';
			$arrayTriples[]=$triple;
		}
		//Noeud
		if($noeud->num_parent){
			if($thes->num_noeud_racine == $noeud->num_parent){
				$triple=array();
				$triple[0]='<'.$uriConcept.'>';
				$triple[1]="skos:topConceptOf";
				$triple[2]='<'.$uriThes.'>';
				$arrayTriples[]=$triple;
			}else{
				$triple=array();
				$triple[0]='<'.$uriConcept.'>';
				$triple[1]="skos:broader";
				$triple[2]='<'.$this->baseUriConcept.$noeud->num_parent.'>';
				$arrayTriples[]=$triple;
			}
		}
		//Les renvois
		$res=noeuds::listTargets($idNoeud);
		if(pmb_mysql_num_rows($res)){
			while($row=pmb_mysql_fetch_array($res)){
				$renvoi=new categories($row[0],$thes->langue_defaut);
				$triple=array();
				$triple[0]='<'.$uriConcept.'>';
				$triple[1]="skos:altLabel";
				$triple[2]='"'.addslashes($renvoi->libelle_categorie).'"';
				$arrayTriples[]=$triple;
			}
		}
			
		//Gestion des enfants : on veut les enfants, même avec renvois (poly-hiérarchie)
		$res=noeuds::listChilds($idNoeud,1);
		if(pmb_mysql_num_rows($res)){
			while($row=pmb_mysql_fetch_array($res)){
				$enfant=new noeuds($row[0]);
				if($enfant->num_renvoi_voir){
					$triple=array();
					$triple[0]='<'.$uriConcept.'>';
					$triple[1]="skos:narrower";
					$triple[2]='<'.$this->baseUriConcept.$enfant->num_renvoi_voir.'>';
					$arrayTriples[]=$triple;
				}else{
					$triple=array();
					$triple[0]='<'.$uriConcept.'>';
					$triple[1]="skos:narrower";
					$triple[2]='<'.$this->baseUriConcept.$row[0].'>';
					$arrayTriples[]=$triple;
				}
			}
		}
			
		//Les voir aussi
		$res=$noeud->listUsedInSeeAlso();
		if(pmb_mysql_num_rows($res)){
			while($row=pmb_mysql_fetch_array($res)){
				$triple=array();
				$triple[0]='<'.$uriConcept.'>';
				$triple[1]="skos:related";
				$triple[2]='<'.$this->baseUriConcept.$row[0].'>';
				$arrayTriples[]=$triple;
			}
		}
			
		return $arrayTriples;
	}
	
	/*
	 * Méthodes de traitement des champs
	 */
	
	private function dateIso8601($arrayValues){
		//Il ne peut y avoir qu'une date
		$date=$arrayValues[0]['value'];
		if(preg_match('`^(\d{4})\-.*`',$date,$out)){
			$date=$out[1];
		}
		if(preg_match('`^(\d{2})\/(\d{2})\/(\d{4})$`',$date,$out)){
			return $out[3]."-".$out[2]."-".$out[1]."T00:00:00";
		}elseif(preg_match('`^(\d{2})\/(\d{4})$`',$date,$out)){
			return $out[2]."-".$out[1]."-01T00:00:00";
		}elseif(preg_match('`.*(\d{4}).*`',$date,$out)){
			return $out[1]."-01-01T00:00:00";
		}else{
			return false;
		}
	}
	
	private function typeDocBnf($arrayValues){
		//Il ne peut y avoir qu'un type doc
		$typDoc=$arrayValues[0]['value'];
		switch($typDoc){
			case "a" :
			case "b" :
			case "c" :
			case "d" :
				return "Text";
				break;
			case "e" :
			case "f" :
			case "g" :
			case "k" :
				return "Image";
				break;
			case "i" :
			case "j" :
				return "Sound";
				break;
			case "l" :
			case "m" :
				return "Interactive Resource";
				break;
			default :
				return "Text";
		}
	}
	
	private function doIsbdTitle($arrayValues){
		$titles=array();
		foreach($arrayValues as $value){
			$titles[(int)substr($value['code'],0,1)-1]=$value['value'];
		}
		$value=$titles[0];
		if(isset($titles[2]) && trim($titles[2])){
			$value .= " = ".$titles[2];
		}
		if(isset($titles[3]) && trim($titles[3])){
			$value .= " : ".$titles[3];
		}
		if(isset($titles[1]) && trim($titles[1])){
			$value .= " ; ".$titles[1];
		}
		return $value;
	}
	
	private function addUriConcept($value){
		return $this->baseUriConcept.$value;
	}
	
	private function concatTitreBulletin($arrayValues){
		$value='';
		foreach($arrayValues as $valueTitre){
			if(trim($valueTitre['value'])){
				if(trim($value)){
					$value.=" - ";
				}
				$value.=$valueTitre['value'];
			}
		}
		return $value;
	}
	
	private function authorName($arrayValues){
		$value = '';
		if(count($arrayValues)>1){
			foreach($arrayValues as $valueTitre){
				if(trim($valueTitre['value'])){
					if(trim($value)){
						$value.=", ";
					}
					$value.=$valueTitre['value'];
				}
			}
		}else{
			$value=$arrayValues[0]['value'];
		}		
		return $value;
	}
	
}