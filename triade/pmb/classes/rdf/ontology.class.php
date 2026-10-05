<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ontology.class.php,v 1.8 2024/03/22 15:31:05 qvarin Exp $


if (stristr ($_SERVER['REQUEST_URI'], ".class.php"))
	die ("no access");

require_once ("$class_path/rdf/rdf.class.php");

class ontology_parser {
	
	public $ontology_file='';
	public $parser;
	public $t_resources=array();
	public $t_objects=array();
	public $t_properties=array();
	public $current=0;
	public $path_tag=array();
	public $text='';

	
	/* Eléments de l'ontologie pmb
	 * 
	 * pmb:noAssertionProperty		Indique une propriété ne devant pas être utilisée pour une déclaration
	 * 
	 * pmb:datatype					Précise le type de données à représenter
	 * pmb:small_text				Type de donnée "small text" => "Input"
	 * pmb:text						Type de donnée "text" => "Textarea"
	 * 
	 * pmb:displayLabel				Label à afficher dans une liste
	 */
	 
	
	
	
	
	// Tableau avec les namespaces les plus courants
	public $t_ns = array(	"skos:"	=> "http://www.w3.org/2004/02/skos/core#",
							"dc:"	=> "http://purl.org/dc/elements/1.1",
							"dct:"	=> "http://purl.org/dc/terms/",
							"owl:"	=> "http://www.w3.org/2002/07/owl#",
							"rdf:"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
							"rdfs:"	=> "http://www.w3.org/2000/01/rdf-schema#",
							"xsd:"	=> "http://www.w3.org/2001/XMLSchema#",
							"pmb:"	=> "http://www.pmbservices.fr/ontology#"
					);  
	
	
	// Tableau types propriétés
	public $t_is_a_property = array(	'rdf:Property',
										'owl:ObjectProperty',
										'owl:FunctionalProperty',
										'owl:DatatypeProperty',
										'owl:AnnotationProperty',
									);
	
	
	// Tableau types ontology
	public $t_is_a_ontology = array(	'owl:Ontology',
									);
	
	
	
	public function __construct ($ontology_file) {
		
		global $class_path;
		
		$this->ontology_file=$ontology_file;
		$this->run();
	}
	
	
	protected function run () {

		global $charset;
		
		$xml=file_get_contents ($this->ontology_file, "r") or die ("Can't find XML file $this->ontology_file");
		
		$this->parser=xml_parser_create ('utf-8');
		xml_set_object ($this->parser, $this);
		xml_parser_set_option ($this->parser, XML_OPTION_TARGET_ENCODING, $charset);
		xml_parser_set_option ($this->parser, XML_OPTION_CASE_FOLDING, FALSE);
		xml_parser_set_option ($this->parser, XML_OPTION_SKIP_WHITE, TRUE);
		xml_set_element_handler ($this->parser, "tag_start", "tag_end");
		xml_set_character_data_handler ($this->parser, "texte");
		
		if ( !xml_parse ($this->parser, $xml, TRUE)) {
			die (sprintf ("erreur XML %s à la ligne: %d", xml_error_string (xml_get_error_code ($this->parser)), xml_get_current_line_number ($this->parser)));
		}
		xml_parser_free ($this->parser);

		$this->finalize();
	}
	
	
	public function to_ns($text='') {
		
		$r=$text;
		foreach($this->t_ns as $k=>$v) {
			$r = str_replace($v,$k,$r);
		} 
		return $r;
	}
	

	public function from_ns($text='') {
	
		$r=$text;
		foreach($this->t_ns as $k=>$v) {
			$r = str_replace($k,$v,$r);
		}
		return $r;
	}

	
	protected function is_a_property($res_id) {

		if (is_array($this->t_resources[$res_id]['rdf:Description']['rdf:type']['rdf:resource'])) {
			foreach($this->t_resources[$res_id]['rdf:Description']['rdf:type']['rdf:resource'] as $k=>$v) {
				if(in_array($v,$this->t_is_a_property)) {
					return true;
				}
			}
		}
		return false;
	}

	
	protected function is_a_ontology($res_id) {
	
		if (is_array($this->t_resources[$res_id]['rdf:Description']['rdf:type']['rdf:resource'])) {
			foreach($this->t_resources[$res_id]['rdf:Description']['rdf:type']['rdf:resource'] as $k=>$v) {
				if(in_array($v,$this->t_is_a_ontology)) {
					return true;
				}
			}
		}
		return false;
	}
	
	
	protected function is_a_nodeid($res_id) {
	
		if (is_array($this->t_resources[$res_id]['rdf:Description']['rdf:nodeID'])) {
			return true;
		}
		return false;
	}
	
	
	protected function is_used_for_assertion($res_id) {
		if (is_array($this->t_resources[$res_id]['rdf:Description']['rdf:type']['rdf:resource'])) {
			foreach($this->t_resources[$res_id]['rdf:Description']['rdf:type']['rdf:resource'] as $k=>$v) {
				if($v=='pmb:noAssertionProperty') {
					return false;
				}
			}
		}
		return true;
	}
	
	
	protected function get_domains($res_id) {

		$r=array();
		if (is_array($this->t_resources[$res_id]['rdf:Description']['rdfs:domain']['rdf:resource'])) {
				$r = $this->t_resources[$res_id]['rdf:Description']['rdfs:domain']['rdf:resource'];
		}
		return $r;
		
	}
	
	
	protected function get_uri($res_id) {

		$r='';
		if (is_array($this->t_resources[$res_id]['rdf:Description']['rdf:about'])) {
			$r = $this->t_resources[$res_id]['rdf:Description']['rdf:about'][0];
		}
		return $r;
		
	}
	

	protected function get_id($res_id) {

		$r='';
		if (is_array($this->t_resources[$res_id]['rdf:Description']['rdf:nodeID'])) {
			$r = $this->t_resources[$res_id]['rdf:Description']['rdf:nodeID'][0];
		}
		return $r;
		
	}
	
	protected function get_pmb_datatype ($res_id) {
		
		$r='';
		if($this->t_resources[$res_id]['rdf:Description']['pmb:datatype']['rdf:resource'][0]) {
			$r = $this->t_resources[$res_id]['rdf:Description']['pmb:datatype']['rdf:resource'][0];
		}
		return $r;
	}
	
	
	public function get_pmb_display_label($res_uri='') {

		$r='';
		$res_uri=$this->to_ns($res_uri);
		
		if($this->t_resources[$this->t_objects[$res_uri]['res_id']]['rdf:Description']['pmb:displayLabel']['rdf:resource'][0]) {
			$r = $this->t_resources[$this->t_objects[$res_uri]['res_id']]['rdf:Description']['pmb:displayLabel']['rdf:resource'][0];
		} else if($this->t_resources[$this->t_properties[$res_uri]['res_id']]['rdf:Description']['pmb:displayLabel']['rdf:resource'][0]) {
			$r = $this->t_resources[$this->t_properties[$res_uri]['res_id']]['rdf:Description']['pmb:displayLabel']['rdf:resource'][0];
		}
		return $r;
	}

	public function get_pmb_search_label($res_uri='') {

		$r='';
		$res_uri=$this->to_ns($res_uri);
		if($this->t_resources[$this->t_objects[$res_uri]['res_id']]['rdf:Description']['pmb:searchLabel']['rdf:resource']) {
			$r = $this->t_resources[$this->t_objects[$res_uri]['res_id']]['rdf:Description']['pmb:searchLabel']['rdf:resource'];
		} else if($this->t_resources[$this->t_properties[$res_uri]['res_id']]['rdf:Description']['pmb:searchLabel']['rdf:resource']) {
			$r = $this->t_resources[$this->t_properties[$res_uri]['res_id']]['rdf:Description']['pmb:searchLabel']['rdf:resource'];
		}
		return $r;
	}
	
	protected function get_range($res_id) {

		$r=array();
		if($this->t_resources[$res_id]['rdf:Description']['rdfs:range']['rdf:resource']) {
			
			$r = $this->t_resources[$res_id]['rdf:Description']['rdfs:range']['rdf:resource'];
			
		} else if($this->t_resources[$res_id]['rdf:Description']['rdfs:range']['rdf:nodeID'][0]) {
		
			$node_id = $this->t_resources[$res_id]['rdf:Description']['rdfs:range']['rdf:nodeID'][0];
			$res_id = $this->t_nodeids[$node_id]['res_id'];
			
			if($uo_id=$this->is_a_unionof($res_id)) {
				$r = $this->get_unionof($uo_id);
			}
			
		}		
		return $r;
		
	}
	

	protected function is_a_unionof($res_id) {
		
		$r=false;
		if ($this->t_resources[$res_id]['rdf:Description']['owl:unionOf']['rdf:nodeID'][0]) {
			$r = $this->t_resources[$res_id]['rdf:Description']['owl:unionOf']['rdf:nodeID'][0];
		}
		return $r;
	}
	
	
	protected function get_unionof($node_id) {
		
		$r=array();
		$res_id = $this->t_nodeids[$node_id]['res_id'];
		$is_last=false;
		while(!$is_last) {
			$x = $this->get_first($res_id);
			if(!$x) {
				$is_last=true;
			} else {
				$r[]=$x;
			}
			$y = $this->get_rest($res_id);
			if($y) {
				$res_id = $this->t_nodeids[$y]['res_id'];
			} else {
				$is_last=true;
			}
		}
		return $r;
	}
	
	
	protected function get_first($res_id) {
		
		$r='';
		if($this->t_resources[$res_id]['rdf:Description']['rdf:first']['rdf:resource'][0]) {
			$r=$this->t_resources[$res_id]['rdf:Description']['rdf:first']['rdf:resource'][0];
		}
		return $r;
	}
	
	
	protected function get_rest($res_id) {
		$r='';
		if($this->t_resources[$res_id]['rdf:Description']['rdf:rest']['rdf:resource'][0] && $this->t_resources[$res_id]['rdf:Description']['rdf:rest']['rdf:resource'][0]!='rdf:nil') {
			return $r;
		}
		if($this->t_resources[$res_id]['rdf:Description']['rdf:rest']['rdf:nodeID'][0]) {
			$r = $this->t_resources[$res_id]['rdf:Description']['rdf:rest']['rdf:nodeID'][0];
		}
		return $r;
	}
	
	
	protected function get_cardinalities($res_id) {
		
		$r=array();
		$sc = $this->get_subclassof($res_id);
		if(count($sc)) {
			foreach($sc as $kc=>$vc) {
				$restriction_id = $this->t_nodeids[$vc]['res_id'];
				if($this->is_a_restriction($restriction_id)) {
					//propriete
					$p = $this->get_onproperty($restriction_id);
					if ($p) {
						$min = $this->get_mincardinality($restriction_id);
						$max = $this->get_maxcardinality($restriction_id);
						$r[$p]['min']=$min;
						$r[$p]['max']=$max;
					}
				}
			}
		}
		return $r;
	}
	
	
	protected function get_onproperty($res_id) {
		
		$r='';
		if($this->t_resources[$res_id]['rdf:Description']['owl:onProperty']['rdf:resource'][0]) {
			$r=$this->t_resources[$res_id]['rdf:Description']['owl:onProperty']['rdf:resource'][0];
		}
		return $r;
	}
	

	protected function get_mincardinality($res_id) {
	
		$r=0;
		if($this->t_resources[$res_id]['rdf:Description']['owl:minCardinality']['rdf:datatype'][0] && 
				$this->t_resources[$res_id]['rdf:Description']['owl:minCardinality']['value'][0] &&
				$this->t_resources[$res_id]['rdf:Description']['owl:minCardinality']['rdf:datatype'][0]=='xsd:nonNegativeInteger') {
			$r=$this->t_resources[$res_id]['rdf:Description']['owl:minCardinality']['value'][0];
		}
		return $r;
	}
	
	
	protected function get_maxcardinality($res_id) {
	
		$r=0;
		if($this->t_resources[$res_id]['rdf:Description']['owl:maxCardinality']['rdf:datatype'][0] && 
				$this->t_resources[$res_id]['rdf:Description']['owl:maxCardinality']['value'][0] &&
				$this->t_resources[$res_id]['rdf:Description']['owl:maxCardinality']['rdf:datatype'][0]=='xsd:nonNegativeInteger') {
			$r=$this->t_resources[$res_id]['rdf:Description']['owl:maxCardinality']['value'][0];
		}
		return $r;
	}
	
	
	protected function is_a_restriction($res_id) {
	
		if(is_array($this->t_resources[$res_id]['rdf:Description']['rdf:type']['rdf:resource']) &&
				in_array('owl:Restriction', $this->t_resources[$res_id]['rdf:Description']['rdf:type']['rdf:resource']) ){
			return true;
		}
		return false;
	}
	

	protected function get_subclassof($res_id) {
	
		$r=array();
		if(is_array($this->t_resources[$res_id]['rdf:Description']['rdfs:subClassOf']['rdf:nodeID'])) {
			$r=$this->t_resources[$res_id]['rdf:Description']['rdfs:subClassOf']['rdf:nodeID'];
		}
		return $r;
	}
	
	
	protected function get_disjointwith($res_id) {
	
		$r=array();
		if(is_array($this->t_resources[$res_id]['rdf:Description']['owl:disjointWith']['rdf:resource'])) {
			$r=$this->t_resources[$res_id]['rdf:Description']['owl:disjointWith']['rdf:resource'];
		}
		return $r;
	}
	
	
	public function get_object_properties($object_uri='') {
		
		$r = array();
		$object_uri=$this->to_ns($object_uri);
		if(array_key_exists($object_uri,$this->t_objects)) {
			$r = $this->t_objects[$object_uri]['properties'];
		}
		return $r;
	}
	
	
	protected function finalize() {
		
	
		//recherche des proprietes, objets et autres noeuds 
		$this->t_properties=array();
		foreach($this->t_resources as $res_id=>$res) {
			
			if($this->is_a_property($res_id)) {
				if($uri=$this->get_uri($res_id)) {
					$this->t_properties[$uri]=array('res_id'=>$res_id);
				} 
			} elseif ( !($this->is_a_ontology($res_id)) && !($this->is_a_nodeid($res_id)) ) {
				if($uri=$this->get_uri($res_id)) {
					$this->t_objects[$uri]=array('res_id'=>$res_id);
				}
			} elseif ($this->is_a_nodeid($res_id)) {
				if($id=$this->get_id($res_id)) {
					$this->t_nodeids[$id]=array('res_id'=>$res_id);
				}
			}
		}

		
		//affectation des proprietes aux objets
		foreach($this->t_properties as $uri=>$res) {
			
			$res_id=$res['res_id'];
			if($this->is_used_for_assertion($res_id)) {
				$d=$this->get_domains($res_id);
			
				if (count($d)) {
					foreach($d as $k1=>$v1) {
						if(is_array($this->t_objects[$v1])) {
							$this->t_objects[$v1]['properties'][] = $uri;
						}
					}
				} else {
					foreach($this->t_objects as $k1=>$v1) {
						if(is_array($this->t_objects[$k1])) {
							$this->t_objects[$k1]['properties'][] = $uri;
						}
					}
				}
			}		
		}		

		//recherche des informations necessaires a l'utilisation des objects et proprietes
		foreach($this->t_properties as $uri=>$res) {

			//pmb_datatype
			$this->t_properties[$uri]['pmb_datatype'] = $this->get_pmb_datatype($res['res_id']); 
			
			//range
			$this->t_properties[$uri]['range'] = $this->get_range($res['res_id']);
			
			//disjointwith
			$this->t_properties[$uri]['disjointwith'] = $this->get_disjointwith($res['res_id']);
			
		}
		
		foreach($this->t_objects as $uri=>$res) {
			
			//cardinalities
			$c = $this->get_cardinalities($res['res_id']);
			if(count($c)) {
				$this->t_objects[$uri]['cardinalities']=$c;
			}
			
			//disjointwith
			$this->t_objects[$uri]['disjointwith'] = $this->get_disjointwith($res['res_id']);
		
		}
		
		
		
	}
	
	
	protected function tag_start ($parser, $tag, $att) {

		global $msg;
		
		if(count($this->path_tag)==1 && $tag!='rdf:Description') {
			$this->t_resources[$this->current]['rdf:Description']['rdf:type']['rdf:resource'][]=$this->to_ns($tag);
			$tag='rdf:Description';
		} 
		$this->path_tag[]=$tag;
		
		//au premier niveau, on recupère les namespaces
		if(count($this->path_tag)==1) {
			if (count($att)) {
				foreach($att as $k=>$v) {
					if (stripos($k,'xmlns:')===0) {
						$ns=str_replace('xmlns:','',$k);
						$this->t_ns[$ns.':']=$v;
					}
				}
			}
		}
		
		//au 2eme niveau, ce sont les déclarations
		if(count($this->path_tag)>1) {
			$t=array_slice($this->path_tag,1);
			if(count($att)) {
				foreach($att as $k=>$v) {
					$s = '$this->t_resources[$this->current][\''.implode('\'][\'',$t).'\'][$k][]=$this->to_ns($v);';
					eval ($s);
 				}
			}
		
		}
		
		$this->text='';
	}
	
	
	protected function tag_end ($parser, $tag) {
		
		if(count($this->path_tag)==2) {
			$this->current++;
		}

		if(count($this->path_tag) && trim($this->text)!=='') {
			$t=array_slice($this->path_tag,1);
			$s = '$this->t_resources[$this->current][\''.implode('\'][\'',$t).'\'][\'value\'][]=$this->text;';
			eval ($s);
		}
		array_pop($this->path_tag);
		$this->text='';
	}
	
	
	protected function texte ($parser, $data) {

		if ( !count ($this->path_tag)) {
			return;
		}
		$this->text.=$data;
	}
	
	
}


require_once ("$include_path/templates/ontology.tpl.php");

class ontology_handler {
	
	public $op=NULL;
	public $os=NULL;
	
	public $error=false;
	public $error_msg=array();
	
	public $handled=FALSE;
	public $result=NULL;
	
	public $property_form_handled=FALSE;
	public $property_result=NULL;

	public $params = array();
	public $limit = 50;
	
	
//TODO a revoir pour mettre en paramètre dans pmb	
	public $t_lang = array(	'0'		=> '',
							'fr'	=> 'fre',
							'en'	=> 'eng',
// 							'es'	=> 'spa',
// 							'ru'	=> 'rus',
				);
			
	public $t_languages=array();
	
	public function __construct($ontology_parser){
		$this->op=$ontology_parser;		
		$this->sparql=new sparql();
		
		global $lang;
		$ml = new marc_list('lang');
		$this->t_languages=$ml->table;
	}


	public function format($ns_uri='') {
		$ns_uri = str_replace(':','_',strtolower($ns_uri));
		return $ns_uri;
	}
	
	
	public function get_handler($name='', $params = []) {
		
		$this->handled=FALSE;
		if($params['object']) {
			$obj=$this->format($params['object']);
			$func = $name.'_'.$obj;
			if(method_exists($this,$func)) {
				$this->handled=TRUE;
				$this->$func($params);
			}
		} 
	}

	
	public function get_property_form_handler($property_uri='', $params = []) {
	
		$this->property_form_handled=FALSE;
		if($property_uri) {
			$prop=$this->format($property_uri);
			$func = 'get_property_form_'.$prop;
			if(method_exists($this,$func)) {
				$this->property_form_handled=TRUE;
				$this->$func($params);
			}
		}
	}
	
	
	/*
	 * 
	 * @param $params['object'] = nom de l'objet
	 *
	 * @param $params['page'] = page à afficher
	 * @param $params['limit'] = nb d'objets à afficher
	 * 
	 * @param $params['url_base'] = url de base des pages
	 * 
	 * 
	 */
//TODO reste à gérer l'affichage par ordre alphabétique !!! et le formulaire de recherche
	
	public function showlist ($params=array()) {
		
		//un handler specifique ?
		$this->get_handler('showlist', $params);
				
		if(!$this->handled && $params['object'] && array_key_exists($params['object'], $this->op->t_objects) ) {
			
			//recuperation et verification parametres
			$this->params=array(
					'object'	=> $params['object'],
					'page'		=> 1,
					'offset'	=> 0,
					'limit'		=> $this->limit,
					);

			if($params['page']) {
				$this->params['page']=$params['page'];
			}
			$this->params['page']+=0;
			if(!$this->params['page']) {
				$this->params['page']=1;
				$this->params['offset']=0;
			}
				
			if($params['limit']) {
				$this->params['limit']=$params['limit'];
			}
			$this->params['limit']+=0;
			if(!$this->params['limit']) {
				$this->params['limit']=$this->limit;
			}
			if ($this->params['page']>1 ) {
				$this->params['offset']=((($this->params['page'])*1-1)*$this->params['limit']);
			}
			
			$this->params['url_base'] = $params['url_base'];
			
			global $msg, $charset;
			global $ontology_tpl;
		
			$fname=$this->format($this->params['object']);
			
			//comptage des objets
			$count = $this->count_objects($this->params);
			
			//récupération des objets
			$objs = $this->get_objects($this->params);
	
			//récupération des proprietes a afficher dans la liste
			$p = $this->op->get_pmb_display_label($this->params['object']);
			if($p && count($objs)) {
				foreach($objs as $ko=>$vo) {		
					$op = $this->get_object_properties($vo['s'],array($p));
					$objs[$ko]['properties'] = $op;
				}		
			}
			
			//Affichage entete liste 			
			$tpl=$ontology_tpl['list'];
			if ($msg['ontology_'.$fname]) {
				$m = $msg['ontology_'.$fname];
			} else {
				$m = $this->params['object'];
			}
			$tpl=str_replace('!!list_header!!',$m,$tpl );	
			
			
			//Generation de l'affichage des lignes
			if (count($objs)) {
				
				$parity=1;
								
				foreach($objs as $ko=>$vo) {
 					
					$row = $ontology_tpl['odd_row'];
					if ($parity % 2) {
						$row = $ontology_tpl['even_row'];
					}
					$parity += 1;
					
					$tpl = str_replace('<!-- rows -->',$row.'<!-- rows -->',$tpl);
					
					$uri = $vo['s'];
					
					//lien edition
 					if($this->params['url_base']) {
 						$form_edit_js=$ontology_tpl['edit_js'];
 						$form_edit_js=str_replace('!!edit_url!!',$this->params['url_base'].'edit&uri='.rawurlencode($uri), $form_edit_js);
 						$tpl=str_replace('!!edit_url!!', $form_edit_js, $tpl);
 					} else {
						$tpl=str_replace('!!edit_url!!', '', $tpl);
					}
					$t_label=array('uri' => $uri);
					
					foreach($vo['properties'] as $kp=>$vp) {
						if($vp['p']==$p){
							$t_label[$vp['o lang']] = $vp['o'];
						}
					}
					$label = $t_label['uri'];
					foreach ($this->t_lang as $kl=>$vl) {
						if (array_key_exists($kl, $t_label)) {
							$label = $t_label[$kl];
							break;
						}
					}
					if ($charset=='iso-8859-1') {
						$label=encoding_normalize::utf8_decode($label);
					}
					
					$label = htmlentities($label,ENT_QUOTES,$charset);
					$title = htmlentities($t_label['uri'],ENT_QUOTES,$charset);
						
					$tpl = str_replace('!!row_content!!',$label,$tpl);
					$tpl = str_replace('!!row_title!!',$title,$tpl);
						
 				}
 				
 				//Affichage pagination
 				$pagination_bar='';
 				if ($this->params['url_base']) {
 					$pagination_bar = aff_pagination ($this->params['url_base'], $count, $this->params['limit'], $this->params['page']);
 				}
 				$tpl = str_replace('<!-- pagination -->', $ontology_tpl['pagination'], $tpl);
 				$tpl = str_replace('<!-- pagination -->', $pagination_bar,$tpl);
			}
			
			//Affichage du bouton ajouter
			if($this->params['url_base']) {
				$tpl = str_replace('<!-- add_button -->',$ontology_tpl['add_button'],$tpl);
				$tpl = str_replace('!!add_url!!',$this->params['url_base'].'add',$tpl);
				if($msg['ontology_'.$fname.'_toadd']) {
					$m = $msg['ontology_'.$fname.'_toadd'];
				} else {
					$m = sprintf($msg['ontology_object_add'],$this->params['object']);
				}
				$tpl = str_replace('!!add_msg!!', $m, $tpl);
			}
		}
		$this->result=$tpl;
		return $this->result;
	}

	
	/*
	 *
	 * @param $params['object'] = nom de l'objet
	 * @param $params['object_uri'] = uri de l'objet à modifier
	 * 
	 * @param $params['url_base'] = url de base des pages
	 * 
	 */
	
	public function showform ($params=array()) {

		//un handler specifique ?
		$this->get_handler('showform', $params);
		
		if(!$this->handled && $params['object'] && array_key_exists($params['object'], $this->op->t_objects) ) {
			
			//recuperation et verification parametres
			$this->params=$params;
			
			$fname=$this->format($this->params['object'] );
			
			$object_uri='';
			$raw_object_uri='';
			if ($this->params['object_uri']) {
				$object_uri = $this->params['object_uri'];
				$raw_object_uri = rawurlencode($this->params['object_uri']);
			}
			
			global $msg, $charset;
			global $ontology_tpl;

			$tpl = $ontology_tpl['form'];
				
			//Affichage URI
			$f = $ontology_tpl['label'];
			$f = str_replace('!!label!!', $msg['ontology_object_uri'],$f);
			$f = str_replace('!!for_id!!', "for='object_uri'",$f);
			$f.= $ontology_tpl['object_uri'];
			$f = str_replace('!!object_uri!!', $object_uri,$f);
			$f = str_replace('!!raw_object_uri!!', $raw_object_uri,$f);
			$tpl = str_replace('<!-- fields -->',$f.'<!-- fields -->',$tpl);
			
			
			//recuperation des proprietes definies par l'ontologie pour le type d'objet donne
			$pdo = $this->op->get_object_properties($this->params['object']);
// 			print '-->propriétés définies par l\'ontologie<br />';
// 			highlight_string(print_r($pdo,true));
				
			$peb=array();
			//récuperation des proprietes enregistrees en base
			if ($this->params['object_uri']) {
				$peb = $this->get_object_properties($this->params['object_uri']);
				
				if($charset!='utf-8') {
					foreach($peb as $k=>$v) {
						if($v['o type']=='literal') {
							$peb[$k]['o']=encoding_normalize::utf8_decode($v['o']);
						}
					}
				}
				
			}
//  			print '-->proprietes enregistrees en base<br />';
//  			highlight_string(print_r($peb,true));			
			
 			//Affichage des proprietes
			foreach($pdo as $k=>$v){
				$f = $this->get_property_form($v,$peb);
				$tpl = str_replace('<!-- fields -->',$f.'<!-- fields -->',$tpl);
			}
			
			//Affichage titre			
			if ($this->params['object_uri']) {
				if($msg['ontology_'.$fname.'_edit']){
					$m = $msg['ontology_'.$fname.'_edit'];
				} else {
					$m = sprintf($msg['ontology_object_edit'],$this->params['object']);
				}
			} else {
				if($msg['ontology_'.$fname.'_add']){
					$m = $msg['ontology_'.$fname.'_add'];
				} else {
					$m = sprintf($msg['ontology_object_add'],$this->params['object']);
				}
			}
			$tpl=str_replace('!!form_title!!',$m,$tpl );	
			
			//Affichage des boutons
			$tpl = str_replace('<!-- buttons -->',$ontology_tpl['buttons'],$tpl);
			$tpl = str_replace('<!-- cancel_button -->',$ontology_tpl['cancel_button'],$tpl);
			$tpl = str_replace('<!-- rec_button -->',$ontology_tpl['rec_button'],$tpl);
			if ($this->params['object_uri']) {
				$tpl = str_replace('<!-- del_button -->',$ontology_tpl['del_button'],$tpl);
			}
			$this->result=$tpl;
				
		}
		return $this->result;
	}
	
	
	public function recform ($params=array()) {
		
		//un handler specifique ?
		$this->get_handler('recform',$params);
		
		if(!$this->handled && $params['object'] && array_key_exists($params['object'], $this->op->t_objects)) {
			
			$this->params=$params;
		
		}
		return $this->result;
	}
	
	
	public function delform($params=array()) {
		
		//un handler specifique ?
		$this->get_handler('delform', $params);
		
		if(!$this->handled && $params['object'] && array_key_exists($params['object'], $this->op->t_objects)) {
			
			$this->params=$params;
		
		}
		return $this->result;
	}
	
	
	protected function get_property_form($property_uri='', $peb=array()) {

		//un handler specifique ?
		$this->get_property_form_handler($property_uri, $peb);
		
		if(!$this->property_form_handled && $property_uri) {
			
			$fname=$this->format($property_uri);
			
			global $msg, $charset;
			global $ontology_tpl;
			
			$tpl='';
			$tpl_mod='';
			
			$p_pmb_datatype = $this->op->t_properties[$property_uri]['pmb_datatype'];
			$p_range = $this->op->t_properties[$property_uri]['range'];
			$p_min_cardinality = $this->op->t_objects[$this->params['object']]['cardinalities'][$property_uri]['min'];
			$p_max_cardinality = $this->op->t_objects[$this->params['object']]['cardinalities'][$property_uri]['max'];
			$o_disjointwith = $this->op->t_objects[$this->params['object']]['disjointwith'];
			$p_disjointwith = $this->op->t_properties[$property_uri]['disjointwith'];
				
			//possibilite de creer +sieurs proprietes
			$display_add_button=true;
			if($p_max_cardinality==1) {
				$display_add_button=false;
			}

			if($p_pmb_datatype=='pmb:text') {
				$tpl_mod = $ontology_tpl['text'];
			} else if ($p_pmb_datatype=='pmb:small_text') {
				$tpl_mod = $ontology_tpl['small_text'];
			}

			//zone texte
			if ($tpl_mod) {
				
				if($msg['ontology_'.$fname]) {
					$label=$msg['ontology_'.$fname];
				} else {
					$label=$property_uri;
				}
				$tpl.=$ontology_tpl['label'];
				$tpl = str_replace('!!label!!', $label,$tpl);
				
				$t_values=array();
							foreach($peb as $k=>$v) {
					if( ($v['p']==$property_uri) && $v['o'] /*&& array_key_exists($v['o lang'],$this->t_lang)  */) {
						if ($v['o lang'] && array_key_exists($v['o lang'],$this->t_lang)) { 
							$t_values[$v['o lang']][]=$v['o'];
						} else if (!$v['o lang']) {
							$t_values['0'][]=$v['o'];
						}
					}
				}	
								
				$m=$msg['ontology_p_lang'];
				$index=0;
				
				foreach($this->t_lang as $k=>$v) {
					
					$i_lang=0;
					$code_lang=$k;
					if($k) {
						$i_lang = $k;
					}

					//propriete existante en base ?
					if(array_key_exists($i_lang, $t_values)) {

						$index=0;
						
						foreach($t_values[$i_lang] as $kv => $vv) {
							
							$value = htmlentities($vv, ENT_QUOTES, $charset);

							$tpl.=$tpl_mod;
							$tpl = str_replace('!!value!!', $value,$tpl);
							
							if ($i_lang) {
								$tpl = str_replace('<!-- lang -->', sprintf($m, $this->t_languages[$this->t_lang[$i_lang]]), $tpl);
							} else {
								$tpl = str_replace('<!-- lang -->', '', $tpl);
							}
							
							//bouton effacer
							$tpl = str_replace('<!-- p_del_button -->', $ontology_tpl['p_del_button'], $tpl);
							
							//bouton ajout  si + d'une propriete possible 
							if ($display_add_button && !$index) {
								$tpl = str_replace('<!-- p_add_button -->', $ontology_tpl['p_add_button'], $tpl);
							} else {
								$tpl = str_replace('<!-- p_add_button -->', '', $tpl);
							} 						
							$tpl = str_replace('!!index!!', $index, $tpl);

							$index++;
							
						}
						
					} else {

						$index=0;
						$value = '';
						
						$tpl.=$tpl_mod;
						$tpl = str_replace('!!value!!', $value,$tpl);
						if ($i_lang) {
							$tpl = str_replace('<!-- lang -->', sprintf($m, $this->t_languages[$this->t_lang[$i_lang]]), $tpl);
						} else {
							$tpl = str_replace('<!-- lang -->', '', $tpl);
						}
						
						//bouton effacer
						$tpl = str_replace('<!-- p_del_button -->', $ontology_tpl['p_del_button'], $tpl);
						
						//bouton ajout  si + d'une propriete possible
						if ($display_add_button) {
							$tpl = str_replace('<!-- p_add_button -->', $ontology_tpl['p_add_button'], $tpl);
						} else {
							$tpl = str_replace('<!-- p_add_button -->', '', $tpl);
						}
						
						$tpl = str_replace('!!index!!', $index, $tpl);

						$index++;
					
					}
					
					//javascript associe
					$tpl.=$ontology_tpl['p_script'];
						
					//div englobant
					$tpl = str_replace('<!-- p_content -->',$tpl , $ontology_tpl['p_div']);
						
					$tpl = str_replace('!!fname!!', $fname, $tpl);
					$tpl = str_replace('!!lang!!', (($i_lang)?$i_lang:0), $tpl);
					$tpl = str_replace('!!nb!!', $index, $tpl);
				}
				
				
			} else {
			
				//zone objet
				
				if($msg['ontology_'.$fname]) {
					$label=$msg['ontology_'.$fname];
				} else {
					$label=$property_uri;
				}
				$tpl.= $ontology_tpl['label'];
				$tpl = str_replace('!!label!!', $label,$tpl);
				
				//objets cible
				$raw_range='';
				if (count($p_range)) {
					$raw_range=rawurlencode(implode(',',$p_range));
				}
						
				//recuperation 
				$t_values=array();
				$i=0;
				foreach($peb as $k=>$v) {
					
					if ($v['p']==$property_uri) {
						
						//uri de la propriete
						$t_values[$i]['uri'] = $v['o'];
						
						//type d'objet de la propriete
						if (count($p_range)==1) {
							$pt = $p_range[0];
						} else {
							$pt = $this->get_object_type($v['o']);
						}
						$t_values[$i]['type'] = $pt;
						
						//label a afficher pour la propriete
						$pl = $this->get_object_label($t_values[$i]['type'], $v['o']);
						if ($pl) {
							$t_values[$i]['value'] = htmlentities($pl,ENT_QUOTES,$charset);
						} else {
							$t_values[$i]['value'] = $v['o'];
						}
						$i++;
					}
				}

				$index=0;
				//propriete existante en base ?	
				if (count($t_values)) {
					
					foreach($t_values as $kv=>$vv) {

						
						$tpl.= $ontology_tpl['object'];
						$tpl = str_replace('!!value!!', $vv['value'], $tpl);
						$tpl = str_replace('!!raw_value!!', rawurlencode($vv['uri']), $tpl);
						
						//bouton effacer
						$tpl = str_replace('<!-- p_del_button -->', $ontology_tpl['object_p_del_button'], $tpl);
						
						//bouton selecteur sur la premiere valeur
						$tpl = str_replace('<!-- p_sel_button -->', $ontology_tpl['object_p_sel_button'], $tpl);
						
						//bouton ajout  si + d'une propriete possible 
						if ($display_add_button && !$index) {
							$tpl = str_replace('<!-- p_add_button -->', $ontology_tpl['object_p_add_button'], $tpl);
						} else {
							$tpl = str_replace('<!-- p_add_button -->', '', $tpl);
						} 						
						$tpl = str_replace('!!index!!', $index, $tpl);
	
						$index++;
					}
					
				} else {
					
					$tpl.=$ontology_tpl['object'];
					$tpl = str_replace('!!value!!', '', $tpl);
					$tpl = str_replace('!!raw_value!!', '', $tpl);
					
					//bouton effacer
					$tpl = str_replace('<!-- p_del_button -->', $ontology_tpl['object_p_del_button'], $tpl);

					//bouton selecteur
					$tpl = str_replace('<!-- p_sel_button -->', $ontology_tpl['object_p_sel_button'], $tpl);
					
					//bouton ajout  si + d'une propriete possible 
					if ($display_add_button && !$index) {
						$tpl = str_replace('<!-- p_add_button -->', $ontology_tpl['object_p_add_button'], $tpl);
					} else {
						$tpl = str_replace('<!-- p_add_button -->', '', $tpl);
					} 						
					$tpl = str_replace('!!index!!', $index, $tpl);

					$index++;
				}
				
				//javascript associe
				$tpl.=$ontology_tpl['object_p_script'];
				
				//div englobant
				$tpl = str_replace('<!-- p_content -->',$tpl , $ontology_tpl['object_p_div']);
				
				$tpl = str_replace('!!fname!!', $fname, $tpl);
				$tpl = str_replace('!!nb!!', $index, $tpl);
				$tpl = str_replace('!!raw_range!!', $raw_range, $tpl);
					
			}			
			
			return $tpl;
		}
				
	}
	
	
	/*
	 *
	* @param $params['objects'] = array() ; nom des objets
	* @param $params['object_uri'] = uri de l'objet à modifier
	*
	* @param $params['user_input'] = texte cherché
	* @param $params['page'] = page à afficher
	* @param $params['limit'] = nb d'objets à afficher
	*  
	* @param $params['url_base'] = url de base des pages
	*
	*/
	
	public function showselectform ($params=array()) {
		
		if(!$this->params['limit']) {
			$this->params['limit']=0;
		}
		
		if(!$this->params['offset']) {
			$this->params['offset']=0;
		}
		$this->params=$params;
		
			
		$res = $this->search_objects($this->params);
		if(!is_array($res)){
			print $res;
			print "<br />";
			return ;
		}else{
			print "objets demandes -> ".print_r($this->params['objects'],true)."<br />";
		}
		
	}
	
	
	/** 
	 * @abstract 
	 * Retourne une liste d'objets sous forme de tableau selon le type demandé
	 * et la recherche effectuée
	 * 
	 * @param	array 	$params
	 * array	$params['objects']			: tableau de types d'objet
	 * string	$params['user_input']		: chaine recherchée	
	 * 
	 * @return array
	 * [index]	=>	[subject_uri]			=> uri de l'objet
	 * 				
	 * 
	 */
	public function search_objects($params=array()) {
		global $msg;
		
		$this->params=$params;
		
		$result=array();
		
		if (!$params['objects'] || !is_array($params['objects']) || !count($params['objects'])) {
			return $result;
		}else{
			//On ne garde dans le tableau que les objects possibles
			$object_final=array();
			foreach ( $params['objects'] as $value ) {
	       		if(array_key_exists($value, $this->op->t_objects)){
	       			$object_final[]=$value;
	       		}
			}
			$params['objects']=$object_final;
		}
		
		if(!$params['user_input']) {
			return $msg["ontology_selector_do_search"];
		}
		
		//On prepare la requete
		$aq=new analyse_query(stripslashes($params['user_input']));
		if ($aq->error) {
			return return_error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
		}
		$members=$aq->get_query_members("rdfstore_index","object_val","object_index","subject_uri");
		
		// On restreint la recherche aux objets demandés et aux labels définis pour les recherches dans skos_pmb.rdf
		$restrict=array();
		foreach ( $params['objects'] as $key => $value ) {
			$restrict[$key]="( subject_type='".addslashes($this->op->from_ns($value))."' ";
			$search_label = array();
       		$search_label = $this->op->get_pmb_search_label($value);
       		if($search_label && is_array($search_label) && count($search_label)){
       			$restrict[$key].=" AND (";
       			$tmp="";
       			foreach ( $search_label as $value2 ) {
       				if($tmp) $tmp.= " OR ";
       				$tmp.= "predicat_uri='".addslashes($this->op->from_ns($value2))."'";
				}
				$restrict[$key].=$tmp.")";
       		}
       		$restrict[$key].=")";
		}
		$restrict_f="";
		if(count($restrict)){
			$restrict_f="(".implode(" OR ",$restrict).")";
		}
		$requete="select *,".$members["select"]." as pert from rdfstore_index where ".$members["where"].($restrict_f ?" AND ".$restrict_f:"")." ".$members["post"];
		$res=pmb_mysql_query($requete);
		if($res && pmb_mysql_num_rows($res)){
			while ($ligne = pmb_mysql_fetch_object($res)) {
				$result[]=array("subject_uri"=>$ligne->subject_uri,"subject_type"=>$this->op->to_ns($ligne->subject_type));
			}
		}else{
			return $msg["ontology_selector_not_result"];
		}
		
		return $result;
	}
	
	
	/** 
	 * @abstract 
	 * Retourne une liste d'objets sous forme de tableau selon le type demandé
	 * 
	 * @param	array 	$params
	 * string	$params['object']	: type d'objet
	 * int		$params['limit']	: nombre d'objets à retourner
	 * int		$params['offset']	: offset de départ
	 * 
	 * string	$params['filter']	: filtre à appliquer
	 * 
	 * @return array
	 * [index]	=>	[s]			=> uri de l'objet
	 * 				[s type]	=> 'uri'
	 * 
	 */
	protected function get_objects($params=array()) {

		$res=array();

		if (!$params['object']) {
			return $res;
		}
		if (!$params['limit']) {
			$params['limit']=$this->limit;
		} 
		$params['limit'] = intval($params['limit']);
		if (!$params['offset']) {
			$params['offset']=0;
		}
		$params['offset'] = intval($params['offset']);
		
		$s_filter='';
		if(is_string($params['filter'])) {
			$s_filter = $params['filter'];
		}
		if(is_array($params['filter']) && count($params['filter'])) {
			$s_filter = '';
		}
		
		$q =
		$this->sparql->get_prefix_text()."
		SELECT ?s 
		WHERE {
			?s a ".$params['object']." .
		} ".
		(($params['limit'])?"LIMIT ".$params['limit']." ":"").
		(($params['offset'])?"OFFSET ".$params['offset']." ":"");
		$r = $this->sparql->query($q);
		
		if ($r['result']['rows'] && count($r['result']['rows'])) {
			foreach($r['result']['rows'] as $k=>$v) {
				foreach($v as $k1=>$v1) {
					$res[$k][$k1]=$v1;
				}
			}
		}
		return $res;
	}

	
	/**
	 * @abstract 
	 * Compte le nombre d'objets selon le type demandé
	 * 
	 * @param array $params
	 * string $params['object'] : type d'objet
	 * 
	 * @return int
	 * 
	 */
	protected function count_objects($params=array()) {
	
		$res=0;
		if(!$params['object']) {
			return $res;
		}
		$q =
		$this->sparql->get_prefix_text()."
		SELECT ?s
		WHERE {
			?s a ".$params['object']."
		}";

		$r = $this->sparql->query($q);
		
		if (is_array($r['result']['rows'])) {
			$res=count($r['result']['rows']);
		}
		return $res;
	}
	
	
	/** 
	 * @abstract 
	 * Retourne la liste des propriétés d'un objet sous forme de tableau
	 * 
	 * @param	string 	$object_uri	: uri de l'objet
	 * 
	 * @param	array	$property_uris	: liste des propriétés cherchées
	 * 
	 * @return	array
	 * [index]	=>	[p]			=> prédicat de la propriété
	 * 				[p type]	=> 'uri'
	 * 				[o]			=> uri ou contenu de la propriété
	 * 				[o type]	=> type de propriété ('uri', literal, ...) 
	 * 				[o lang]	=> code langue si literal
	 * 
	 */
	protected function get_object_properties($object_uri='', $property_uris=array()) {
	
		$res=array();
		if(!$object_uri) {
			return $res;
		}
		$filter='';
		if (count($property_uris)) {
			foreach($property_uris as $k=>$v) {
				$property_uris[$k]=$this->op->from_ns($v);
			}
			$filter = "FILTER (REGEX(?p, '^".implode("$|^",$property_uris)."$', 'i'))";
		}
		$q =
		$this->sparql->get_prefix_text()."
		SELECT ?p ?o
		WHERE {
			<".$object_uri."> ?p ?o .
			$filter 
		} 
		";	
		$r = $this->sparql->query($q);
		
		if ($r['result']['rows'] && count($r['result']['rows'])) {
			foreach($r['result']['rows'] as $k=>$v) {
				foreach($v as $k1=>$v1) {
					$res[$k][$k1]=$this->op->to_ns($v1);
				}
			}
		}

		return $res;
	}
	
	/*
	 * Chaque resource ne peut avoir qu'un type...
	 */
	public function get_object_type($object_uri='') {
	
		$res='';
		if(!$object_uri) {
			return $res;
		}
		$q =
		$this->sparql->get_prefix_text()."
		SELECT ?t
		WHERE {
			<".$object_uri."> a ?t .
		}
		";
		$r = $this->sparql->query($q);
		
		if ($r['result']['rows'][0]['t']) {
			$res=$this->op->to_ns($r['result']['rows'][0]['t']);
		}
		
		return $res;
	}
	
	
	public function get_object_label($object, $object_uri='', $lang='fr') {

		global $charset;
		
		$res='';
		if(!$object_uri) {
			return $res;
		}

		$lang = substr($lang,0,2);
		$object_uri = $this->op->from_ns($object_uri);
		$as_label = $this->op->get_pmb_display_label($object);
		$q =
		$this->sparql->get_prefix_text()."
		SELECT ?l 
		WHERE {
			<".$object_uri."> $as_label ?l .
			FILTER(lang(?l) = '$lang') .
		}
		";
		$r = $this->sparql->query($q);

		if ($r['result']['rows'][0]['l']) {
			$res=$this->op->to_ns($r['result']['rows'][0]['l']);
			if ($charset=='iso-8859-1') {
				$res = encoding_normalize::utf8_decode($res);
			}
		}
		
		return $res;
	}
	
	
}


class skos_handler extends ontology_handler {
	
	public function __construct($ontology_parser) {
		parent::__construct($ontology_parser);
	}

}
?>