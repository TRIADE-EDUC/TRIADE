<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: oai_protocol.class.php,v 1.12 2019-01-22 16:30:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;

require_once($class_path."/xml_dom.class.php");

//Gestion des dates
class iso8601 {
	public $granularity;
	
	public function __construct($granularity="YYYY-MM-DD") {
		$this->granularity=$granularity;
	}
	
	public function unixtime_to_iso8601($time) {
		$granularity=str_replace("T","\\T",$this->granularity);
		$granularity=str_replace("Z","\\Z",$granularity);
		$granularity=str_replace("YYYY","Y",$granularity);
		$granularity=str_replace("DD","d",$granularity);
		$granularity=str_replace("hh","H",$granularity);
		$granularity=str_replace("mm","i",$granularity);
		$granularity=str_replace("MM","m",$granularity);
		$granularity=str_replace("ss","s",$granularity);
		$date=date($granularity,$time);
		return $date;
	}
	
	public function iso8601_to_unixtime($date) {
		$parts=explode("T",$date);
		if (count($parts)==2) {
			$day=$parts[0]; 
			$time=$parts[1];
		} else {
			$day=$parts[0];
		}
		$days=explode("-",$day);
		if ($this->granularity=="YYYY-MM-DDThh:mm:ssZ") {
			if ($time) $times=explode(":",$time);
			if ($times[2]) {
				if (substr($times[2],strlen($times[2])-1,1)=="Z") $times[2]=substr($times[2],0,strlen($times[2])-1);
			}
		}
		$unixtime=mktime($times[0],$times[1],$time[2],$days[1],$days[2],$days[0]);
		return $unixtime;
	}
}

//Manipulation des enregistrements
class oai_record {
	public $srecord;			//Enregistrement d'origine
	public $header;			//Entête
	public $metadata;			//Enregistrement parsé
	public $unimarc;			//Enregistrement converti en unimarc
	public $about;				//About
	public $handler;			//Handler pour parser les métadatas
	public $prefix;			//Forçage du handler demandé
	public $base_path;			//Chemin de base pour les feuilles XSLT
	public $xslt_transform;	//Feuille de style pour transformer l'enregistrement en unimarc	
	public $error;
	public $error_message;
	public $charset;
	
	public function __construct($record,$charset="iso-8859-1",$base_path="",$prefix="",$xslt_transform="",$sets_names="") {
		$this->srecord=$record;
		$this->charset=$charset;
		$this->prefix=$prefix;
		$this->base_path=$base_path;
		$this->xslt_transform=$xslt_transform;
		
		$precord=new xml_dom($record,$charset);
		if ($precord->error) {
			$this->error=true;
			$this->error_message=$precord->error_message;
		} else {
			//Header
			$this->header["IDENTIFIER"]=$precord->get_value("record/header/identifier");
			$this->header["DATESTAMP"]=$precord->get_value("record/header/datestamp");
			$this->header["SETSPECS"]=$precord->get_values("record/header/setSpec");
			$this->header["STATUS"]=$precord->get_values("record/header/status");
			//Enregistrement
			$this->metadata=$precord->get_value("record/metadata");
			//About
			$this->about=$precord->get_value("record/about");
			
			$nmeta=$precord->get_node("record/metadata");
			//Conversion éventuelle en unimarc
			if (!$this->prefix) {
				//Recherche du premier fils élément
				for ($i=0; $i<count($nmeta["CHILDS"]); $i++) {
					if ($nmeta["CHILDS"][$i]["TYPE"]==1) {
						$handler=explode(":",$nmeta["CHILDS"][$i]["NAME"]);
						$this->handler=$handler[0];
						break;
					}
				}
			} else {
				$this->handler=$this->prefix;
			}
			//Petit truchement pour récupérer le nom des sets
			if (count($this->header["SETSPECS"])) {
				$hd=$precord->get_node("record/header");
				for ($i=0; $i<count($this->header["SETSPECS"]);$i++) {
					$setName=array();
					$setName["NAME"]="setName";
					$setName["ATTRIBS"]=array();
					$setName["TYPE"]=1;
					$setName["CHILDS"][0]["DATA"]=($sets_names[$this->header["SETSPECS"][$i]]?$sets_names[$this->header["SETSPECS"][$i]]:$this->header["SETSPECS"][$i]);
					$setName["CHILDS"][0]["TYPE"]=2;
					$hd["CHILDS"][]=$setName;
				}
			}	
			//Récupération de la feuille xslt si elle n'a pas été fournie
			if (!$this->xslt_transform) {
				if (file_exists($this->base_path."/".$this->handler.".xsl")) {
					$this->xslt_transform=file_get_contents($this->base_path."/".$this->handler.".xsl");
				}
			}
			//Si on peut nécéssaire, on transforme en unimarc
			if ($this->xslt_transform) {
				$this->unimarc=$this->to_unimarc("<record><header>".$precord->get_datas($hd)."</header><metadata>".$this->metadata."</metadata></record>");
			}
		}
	}
	
	public function to_unimarc($metatdata) {
		//$xsl=file_get_contents("/home/ftetart/public_html/php_dev/admin/connecteurs/in/oai/dc2uni.xsl");
		
		/* Allocation du processeur XSLT */
		$xh = xslt_create();
		xslt_set_encoding($xh, $this->charset);
		$notice="<?xml version='1.0' encoding='".$this->charset."'?>\n".$metatdata;
		
		/* Traitement du document */
		$arguments = array(
	   	  '/_xml' => $notice,
	   	  '/_xsl' => $this->xslt_transform
		);
		$result = xslt_process($xh, 'arg:/_xml', 'arg:/_xsl', NULL, $arguments); 
		return $result;
	}
}

//Environnement de parse & parser d'une ressource
class oai_parser {
	public $depth;					//Profondeur courante d'analyse
	public $cur_elt;				//Enregistrement courant
	public $last_elt;				//Tableau des derniers éléments parsés pour chaque niveau
	public $verb;					//Verbe en cours (récupéré de la réponse)
	public $tree;					//Arbre des éléments de niveau 1
	public $error,$error_message;	//Erreurs
	public $laction;				//Dernière action du parser : open = "un tag vient d'être ouvert mais pas fermé", close = "Un tag ouvert vient d'être fermé"
	public $rtoken;				//Resumption Token : [expirationDate], [completeListSize], [cursor], [token]
	public $rec_callback;			//Fonction de callback pour un enregistrement
	public $records;				//Tableau des enregistrements récupérés
	public $charset;				//Charset de sortie
	public $oai_atoms=array(		//Eléments répétitifs attendus pour chaque verb
		"GetRecord"=>"record",
		"ListIdentifiers"=>"header",
		"ListMetadataFormats"=>"metadataFormat",
		"ListRecords"=>"record",
		"ListSets"=>"set"
	);
	
	//Fonctions appelées lors du parse d'une réponse
	public function oai_startElement($parser, $name, $attrs) {
		$this->laction="open";
		if (!$this->error) {
			switch ($name) {
				case "OAI-PMH":
					if ($this->depth!=0) {
						$this->error=true;
						$this->error_message="Unknown OAI Response";
					} else {
						$this->last_elt[$this->depth]=$name;
					}
					break;
				case "responseDate":
					if ($this->depth!=1) {
						$this->error=true;
						$this->error_message="Unknown OAI Response";
					} else {
						$this->last_elt[$this->depth]=$name;
					}
					break;
				case "request":
					if ($this->depth!=1) {
						$this->error=true;
						$this->error_message="Unknown OAI Response";
					} else {
						$this->last_elt[$this->depth]=$name;
						if ($attrs["verb"]) $this->verb=$attrs["verb"];
					}
					break;
				case "error":
					if ($this->depth!=1) {
						$this->error=true;
						$this->error_message="Unknown OAI Response";
					} else {
						$this->last_elt[$this->depth]=$name;
					}
					break;
				case $this->verb:
					if ($this->depth!=1) {
						$this->error=true;
						$this->error_message="Unknown OAI Response";
					} else {
						$this->last_elt[$this->depth]=$name;
						$this->cur_elt="";
					}
					break;
				default:
					if (($this->last_elt[1]!=$this->verb)||($this->depth==1)) {
						$this->error=true;
						$this->error_message="Unknown XML Response : tag is invalid : ".$name;
					}
					break;
			}
			if ($this->depth>=2) {
				if ($this->depth==2) {
					if (($this->verb!="Identify")&&($name!=$this->oai_atoms[$this->verb])) {
						if ($name!="resumptionToken") {
							$this->error=true;
							$this->error_message="Bad pattern response for verb : ".$this->verb;
						}
					} else {
						if ($this->verb!="Identify")
							$this->cur_elt="";
					}
				} 
				if (($name=="resumptionToken")&&($this->depth==2)) {
					$this->rtoken["expirationDate"]=$attrs["expirationDate"];
					$this->rtoken["completeListSize"]=$attrs["completeListSize"];
					$this->rtoken["cursor"]=$attrs["cursor"];
				} else {
					//$this->cur_elt.="\n";
					//for ($i = 0; $i < $this->depth; $i++) {
			   		// 	$this->cur_elt.="  ";
					//}
					$this->cur_elt.="<$name";
					foreach($attrs as $key=>$val) { 
						$this->cur_elt.=" ".$key."=\"".htmlspecialchars($val,ENT_NOQUOTES,$this->charset)."\" ";
					}
					$this->cur_elt.=">";
				}
			} else {
				$f["NAME"]=$name;
				$f["ATTRIB"]=$attrs;
				$this->tree[$this->depth][]=$f;
			}
		}
		$this->depth++;
	}
	
	public function oai_charElement($parser,$char) {
		if (($this->laction=="open")&&(!$this->error)) {
			if ($this->depth<=2) {
				$this->tree[$this->depth-1][count($this->tree[$this->depth-1])-1]["CHAR"].=$char;
			} else {
				if ($this->rtoken) {
					$this->rtoken["token"].=$char;
				} else {
					$this->cur_elt.=htmlspecialchars($char,ENT_NOQUOTES,$this->charset);
				}
			}
		}
	}
	
	public function oai_endElement($parser, $name) {  	  
		$this->laction="close";
		if (!$this->error) {
			if ($this->depth<=2) {
				if ($this->last_elt[$this->depth-1]!=$name) {
					$this->error=true;
					$this->error_message="Unknown OAI Response";
				} else {
					unset($this->last_elt[$this->depth]);
				}
			} else {
				if ($this->depth>2) {
					if (!$this->rtoken)
						$this->cur_elt.="</".$name.">";
				}
				if (!$this->rtoken) {
					if (($this->depth==3)&&($this->verb!="Identify")) {
						if (!$this->rec_callback)
							$this->records[]=$this->cur_elt;
						else {
							if (substr(strtolower($this->charset),0,10)=="iso-8859-1") $c=true; else $c=false;
							$rec_callback=$this->rec_callback;
							if (!is_array($rec_callback))
								$rec_callback(($c?utf8_decode($this->cur_elt):$this->cur_elt));
							else {
								$c=&$rec_callback[0];
								$f=$rec_callback[1];
								$c->$f(($c?utf8_decode($this->cur_elt):$this->cur_elt));
							}
						}
					}
				}
			}
		}
		$this->depth--;
	}
	
	public function __construct($rcallback="",$charset="iso-8859-1") {
		$this->depth=0;
		$this->rtoken="";
		$this->rec_callback=$rcallback;
		$this->charset=$charset;
	}
}

//Gestion bas niveau du protocol
class oai_protocol {
	public $url_base;				//Url de base
	public $clean_base_url;		//Nettoyer les urls renvoyées dans le tag request
    public $error=false;
    public $error_message="";
    public $error_oai_code="";		//Code d'erreur OAI
    public $response_date;			//Date de réponse
    public $request;				//Requête
    public $rtoken;    			//Paramètre du "Resumption Token"
    public $next_request;			//Requête à rappeller si Resumption Token
    public $records=array();		//Enregistrements lus
    public $charset="iso-8859-1";
    public $time_out;				//Temps maximum d'interrogation de la source
    public $xml_parser;			//Ressource parser
    public $retry_after;			//Délais avant rééssai
    					
    public function __construct($charset="iso-8859-1",$url="",$time_out="",$clean_base_url=0) {
    	$this->charset=$charset;
    	$this->time_out=$time_out;
    	$this->clean_base_url=$clean_base_url;
    	if ($url) $this->analyse_response($url);
    }
    
    public function parse_xml($ch,$data) {
    	if (!$this->retry_after) {
	    	//Parse de la ressource
	    	if (!xml_parse($this->xml_parser, $data)) {
	       		$this->error_message=sprintf("XML error: %s at line %d",xml_error_string(xml_get_error_code($this->xml_parser)),xml_get_current_line_number($this->xml_parser));
	       		$this->error=true;
	       		return strlen($data);
	    	} else if ($s->error) {
	    		$this->error_message=$s->error_message;
	    		$this->error=true;
	    		return strlen($data);
	    	}
    	}
    	return strlen($data);
	}
    
    public function verif_header($ch,$headers) {
    	$h=explode("\n",$headers);
    	for ($i=0; $i<count($h); $i++) {
    		$v=explode(":",$h[$i]);
    		if ($v[0]=="Retry-After") { $this->retry_after=$v[1]*1; }
    	}
    	return strlen($headers);
    }
    
    //Analyse d'une resource
    public function analyse_response($url,$rcallback="") {
    	//Remise à zéro des erreurs
    	$this->error=false;
    	$this->error_message="";
    	//remise à zero des enregistrements
    	if ($url!=$this->next_request) $this->records=array();
    	$this->next_request="";
    	$this->rtoken="";
    	
    	//Initialisation de la ressource
    	$ch = curl_init();
		// configuration des options CURL
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_WRITEFUNCTION,array(&$this,"parse_xml"));
		curl_setopt($ch, CURLOPT_HEADERFUNCTION,array(&$this,"verif_header"));	
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		if ($this->time_out) curl_setopt($ch, CURLOPT_TIMEOUT,$this->time_out);
    	//Réinitialisation du "retry_after"
		$this->retry_after="";    	
    	
		configurer_proxy_curl($ch,$url);	
		
    	//Explosion des arguments de la requête pour ceux qui ne respectent pas la norme !!
    	$query=substr($url,strpos($url,"?")+1);
    	$query=explode("&",$query);
    	for ($i=0; $i<count($query); $i++) {
    		if (strpos($query[$i],"verb")!==false) {
    			$verb=substr($query[$i],5);
    			break;
    		}
    	}
    	
    	//Itinitalisation de l'environnement d'état du parser
		$s=new oai_parser($rcallback,$this->charset);
    	
    	//Si le verb est affecté, on prérempli histoire d'aider un peu... :-)
    	if ($verb) $s->verb=$verb;
    	
    	//Initialisation du parser
		$this->xml_parser=xml_parser_create("utf-8");
		xml_set_object($this->xml_parser,$s);
		xml_parser_set_option( $this->xml_parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parser_set_option( $this->xml_parser, XML_OPTION_SKIP_WHITE, 1 );
		xml_set_element_handler($this->xml_parser, "oai_startElement", "oai_endElement");
		xml_set_character_data_handler($this->xml_parser,"oai_charElement");
		
		$n_try=0;
		$cexec=curl_exec($ch);
		while (($cexec)&&($this->retry_after)&&($n_try<3)) {
			$n_try++; 
			sleep((int)$this->retry_after*1);
			$this->retry_after="";
			$cexec=curl_exec($ch);
		}
		if (!$cexec) {
			$this->error=true;
			$this->error_message=curl_error($ch);
		}
		xml_parser_free($this->xml_parser);
		$this->xml_parser="";
		curl_close($ch);
		
		if ($this->error) { $this->error_message.=" - ".$url; unset($s); return; }
		
		//Affectation des éléments de réponse
		if (substr(strtolower($this->charset),0,10)=="iso-8859-1") $c=true; else $c=false;
		//Test de l'url base
		if ($this->clean_base_url) {
			$p=strpos($s->tree[1][1]["CHAR"],"?");
			if ($p!==false) $s->tree[1][1]["CHAR"]=substr($s->tree[1][1]["CHAR"],0,$p);
		}
		$this->response_date=$c?utf8_decode($s->tree[1][0]["CHAR"]):$s->tree[1][0]["CHAR"];
		$this->url_base=$c?utf8_decode($s->tree[1][1]["CHAR"]):$s->tree[1][1]["CHAR"];
		$this->request["URL_BASE"]=$c?utf8_decode($s->tree[1][1]["CHAR"]):$s->tree[1][1]["CHAR"];
		if(isset($s->tree[1][1]["ATTRIB"]) && is_array($s->tree[1][1]["ATTRIB"])) {
			foreach ($s->tree[1][1]["ATTRIB"] as $key=>$val) {
				if ($key!="resumptionToken")
					$this->request["ATTRIBS"][$key]=$c?utf8_decode($val):$val;
			}
		}
		$this->verb=$c?utf8_decode($s->tree[1][1]["ATTRIB"]["verb"]):$s->tree[1][1]["ATTRIB"]["verb"];
		$this->rtoken=$s->rtoken;
		
		if ($s->tree[1][2]["NAME"]=="error") {
			$this->error=true;
			$this->error_message="OAI Error, the server tell : ".$s->tree[1][2]["ATTRIB"]["code"]." : ".$s->tree[1][2]["CHAR"];
			$this->error_oai_code=$s->tree[1][2]["ATTRIB"]["code"];
		}
		
		//Si c'est la requête identify
		if ($this->verb=="Identify") {
			$this->records[0]=$c?utf8_decode($s->cur_elt):$s->cur_elt;
		} else {
			if (!$rcallback) {
				for ($i=0; $i<count($s->records); $i++) {
					$this->records[]=$c?utf8_decode($s->records[$i]):$s->records[$i];
				}
			}
		}
		if ($this->rtoken["token"]) {
			$this->next_request=$this->request["URL_BASE"]."?verb=".$s->verb;
			$is_first=true;
			/*foreach ($this->request["ATTRIBS"] as $key=>$val) {
				if (!$is_first) $this->next_request.="&"; else $is_first=false;
				$this->next_request.=$key."=".rawurlencode($val);
			}*/
			$this->next_request.="&resumptionToken=".rawurlencode($this->rtoken["token"]);
		}	
		//Supression de l'environnement d'état !
		unset($s);
    }
}

class oai20 {
	public $error;
	public $error_message;
	public $error_oai_code;
	public $no_connect=true;		//La connexion n'est as active avec l'entrepot
	public $url_base;				//Url de base du service OAI
	public $clean_base_url;		//Nettoyer les urls renvoyées dans le tag request
	public $charset;				//Encodage désiré de sortie
	public $prt;					//Protocol
	public $repositoryName;		//Nom de l'entrepôt
	public $baseURL;				//Url de base retournée
	public $protocolVersion;		//Version du protocole
	public $earliestDatestamp;		//Date de la notice la plus ancienne
	public $deletedRecord;			//Gestion des enregistrements supprimés
	public $granularity;			//Granularité
	public $description;			//Description si trouvée
	public $adminEmail;			//Email admin du service
	public $compression;			//Types de compression
	public $h_sets;				//Sets hierarchisés
	public $sets;					//Sets bruts
	public $metadatas;				//Formats des metadatas disponibles
	public $unsupported_features;	//Fonctionalités non supportées (SETS)
	public $last_query;			//Dernière requête effectué
	public $time_out;				//Time out total avant erreur d'une commande
	
	public function __construct($url_base,$charset="iso-8859-1",$time_out="",$clean_base_url=0) {
		//Evitons d'afficher les vilains warning qui trainent
		ini_set('display_errors', 0);
		//Initialisation du service
		$this->url_base=$url_base;
		$this->charset=$charset;
		$this->time_out=$time_out;
		$this->clean_base_url=$clean_base_url;
		//C'est parti : initialisation !
		$this->prt=new oai_protocol($this->charset,$this->url_base."?verb=Identify",$this->time_out,$this->clean_base_url);
		if ($this->prt->error) {
			$this->error=true;
			$this->error_message="Protocol error : ".$this->prt->error_message;
			return;
		} else {
			$this->no_connect=false;
			//Parse 
			$identity=new xml_dom("<Identity>".$this->prt->records[0]."</Identity>");
			$this->repositoryName=$identity->get_value("Identity/repositoryName");
			$this->baseURL=$identity->get_value("Identity/baseURL");
			$this->protocolVersion=$identity->get_value("Identity/protocolVersion");
			$this->earliestDatestamp=$identity->get_value("Identity/earliestDatestamp");
			$this->deletedRecord=$identity->get_value("Identity/deletedRecord");
			$this->granularity=$identity->get_value("Identity/granularity");
			$this->adminEmail=$identity->get_value("Identity/adminEmail");
			$this->compression=$identity->get_value("Identity/compression");
			$descriptions=$identity->get_nodes("Identity/description");
			if ($descriptions) {
				for ($i=0; $i<count($descriptions); $i++) {
					if ($this->description=$identity->get_value("oai_dc:dc/dc:description",$descriptions[$i])) break;
				}
			}
			//Récupération des metadatas et sets
			$this->list_sets();
			if ($this->error) 
				$this->no_connect=true; 
			else {
				$this->list_metadata_formats();
				if ($this->error)
					$this->no_connect=true; 
			}
				
			//if ($node) print $identity->get_datas($node);
			//print $this->prt->records[0];
		}
	}
	
	public function set_clean_base_url($clean_base_url) {
		$this->clean_base_url=$clean_base_url;
	}
	
	public function clear_error() {
		$this->error=false;
		$this->error_message="";
		$this->error_oai_code="";
	}
	
	public function send_request($url,$callback="",$callback_progress="") {
		$this->last_query=$url;
		$this->prt->analyse_response($url,$callback);
		while ((!$this->prt->error)&&($this->prt->next_request)) {
			if ($callback_progress) {
				if (!is_array($callback_progress))
					$callback_progress($this->last_query,$this->prt->rtoken);
				else {
					$c=&$callback_progress[0];
					$f=$callback_progress[1];
					$c->$f($this->last_query,$this->prt->rtoken);
				}
			}
			$this->last_query=$this->prt->next_request;
			$this->prt->analyse_response($this->prt->next_request,$callback);
		}
		if ($this->prt->error) {
			$this->error=true;
			$this->error_message=$this->prt->error_message;
			$this->error_oai_code=$this->prt->error_oai_code;
		}
	}
	
	public function has_feature($feature) {
		return (!$this->unsupported_features[$feature]);
	}
	
	public function check_metadata($metadata_prefix) {
		//Vérification du metadata
		$found=false;
		for ($i=0; $i<count($this->metadatas); $i++) {
			if ($this->metadatas[$i]["PREFIX"]==$metadata_prefix) {
				$found=true;
				break;
			}
		}
		return $found;
	}
	
	public function list_sets($callback="",$callback_progress="") {
		$this->clear_error();
		$this->send_request($this->url_base."?verb=ListSets",$callback,$callback_progress);
		$this->sets=array();
		$this->h_sets=array();
		if (!$this->error) {
			if (!$callback) {
				for ($i=0; $i<count($this->prt->records); $i++) {
					$record=new xml_dom($this->prt->records[$i],$this->charset);
					if (!$record->error) {
						$set=$record->get_value("set/setSpec");
						$set_name=$record->get_value("set/setName");
						$set_description=$record->get_value("set/setDescription/oai_dc:dc/dc:description");
						$this->sets[$set] = array(
								'name' => $set_name,
								'description' => $set_description
						);
						$set=explode(":",$record->get_value("set/setSpec"));
						$path="";
						for ($j=0; $j<count($set)-1; $j++) {
							$path.="[\"".$set[$j]."\"][\"CHILDS\"]";
						}
						eval("\$this->h_sets".$path."[\"".$set[$j]."\"][\"NAME\"]=\$set_name;");
					}
				}
			}
		} else {
			if ($this->error_oai_code=="noSetHierarchy") {
				$this->error=false;
				$this->unsupported_features["SETS"]=true;
			}
		}
		asort($this->sets);
		return $this->sets;
	}
	
	public function list_metadata_formats($identifier="",$callback="",$callback_progress="") {
		$this->clear_error();
		$url=$this->url_base."?verb=ListMetadataFormats";
		if ($identifier) $url.="&identifier=".rawurlencode($identifier);
		$this->send_request($url,$callback_progress);
		$metadatas=array();
		if (!$this->error) {
			if (!$callback) {
				for ($i=0; $i<count($this->prt->records); $i++) {
					$record=new xml_dom($this->prt->records[$i],$this->charset);
					if (!$record->error) {
						$m=array();
						$m["PREFIX"]=$record->get_value("metadataFormat/metadataPrefix");
						$m["SCHEMA"]=$record->get_value("metadataFormat/schema");
						$m["NAMESPACE"]=$record->get_value("metadataFormat/metadataNamespace");
						$metadatas[]=$m;
					}
				}
				if ($identifier=="") $this->metadatas=$metadatas;
			}
		}
		return $metadatas;
	}
	
	public function list_records($from,$until,$set,$metadata_prefix,$callback="",$callback_progress="") {
		$this->clear_error();
		$records=array();
		//Conversion des from et until en fonction de la granularité
		$iso8601=new iso8601($this->granularity);
		if ($from) $from=$iso8601->unixtime_to_iso8601($from);
		if ($until) $until=$iso8601->unixtime_to_iso8601($until);
		//Vérification du metadata
		if ($this->check_metadata($metadata_prefix)) {
			$url=$this->url_base."?verb=ListRecords&metadataPrefix=".rawurlencode($metadata_prefix);
			if ($from) $url.="&from=".$from;
			if ($until) $url.="&until=".$until;
			if ($set) $url.="&set=".rawurlencode($set);
			$this->send_request($url,$callback,$callback_progress);
			if (!$this->error) {
				if (!$callback) {
					for ($i=0; $i<count($this->prt->records); $i++) {
						$records[]=$this->prt->records[$i];
					}
				}
			}
		} else {
			$this->error=true;
			$this->error_message="Unknow metadata prefix : ".$metadata_prefix;
		}
		if (!$callback) return $records;
	}
	
	public function list_identifiers($from,$until,$set,$metadata_prefix,$callback="",$callback_progress="") {
		$this->clear_error();
		$records=array();
		//Conversion des from et until en fonction de la granularité
		$iso8601=new iso8601($this->granularity);
		if ($from) $from=$iso8601->unixtime_to_iso8601($from);
		if ($until) $until=$iso8601->unixtime_to_iso8601($until);
		//Vérification du metadata
		if ($this->check_metadata($metadata_prefix)) {
			$url=$this->url_base."?verb=ListIdentifiers&metadataPrefix=".rawurlencode($metadata_prefix);
			if ($from) $url.="&from=".$from;
			if ($until) $url.="&until=".$until;
			if ($set) $url.="&set=".rawurlencode($set);
			$this->send_request($url,$callback,$callback_progress);
			if (!$this->error) {
				if (!$callback) {
					for ($i=0; $i<count($this->prt->records); $i++) {
						$records[]=$this->prt->records[$i];
					}
				}
			}
		} else {
			$this->error=true;
			$this->error_message="Unknow metadata prefix : ".$metadata_prefix;
		}
		if (!$callback) return $records;
	}
	
	public function get_record($identifier,$metadata_prefix,$callback="",$callback_progress="") {
		$this->clear_error();
		$record="";
		//Vérification du préfixe
		if ($this->check_metadata($metadata_prefix)) {
			$this->send_request($this->url_base."?verb=GetRecord&identifier=".rawurlencode($identifier)."&metadataPrefix=".rawurlencode($metadata_prefix),$callback,$callback_progress);
			if (!$this->error) {
				if (!$callback) {
					$record=$this->prt->records[0];
				}
			}
		} else {
			$this->error=true;
			$this->error_message="Unknow metadata prefix : ".$metadata_prefix;
		}
		return $record;
	}
}
?>
