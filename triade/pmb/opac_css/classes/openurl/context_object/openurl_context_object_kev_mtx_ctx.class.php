<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: openurl_context_object_kev_mtx_ctx.class.php,v 1.5 2023/08/17 09:47:57 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/openurl/context_object/openurl_context_object.class.php");
require_once($class_path.'/openurl/descriptors/openurl_descriptors_kev_mtx.class.php');
require_once($class_path.'/openurl/serialize/openurl_serialize_kev_mtx.class.php');

class openurl_context_object_kev_mtx_ctx extends openurl_context_object {

    public function __construct() {
    	parent::__construct();
    	$this->uri = $this->uri.":kev:mtx:ctx"; 
    	self::$serialize = "kev_mtx";
    	$infos=array();
    	$infos['ctx_ver'] = "Z39.88-2004";
    	$infos['ctx_tim'] = date("Y-m-d");
    	$infos['ctx_enc'] = parent::$uri."/enc:ISO-8859-1";
    	$this->infos = $infos;
    }
    
    public function addEntity($entity){
    	$this->entities[] = $entity;
    }
    
	public function serialize_infos($debug=false){
		if($debug){
			highlight_string("ContextObject :".print_r($this->infos,true));
		}
		$this->context = openurl_serialize_kev_mtx::serialize($this->infos);
		//on ajoute les entités
		foreach($this->entities as $entity){
			$entity_serialized = $entity->serialize_infos($debug);
			if($entity_serialized != ""){
				$this->context .= "&".$entity_serialized;
			}
		}
		return $this->context;
	}
	
	public function unserialize($str){
		$params = $this->explodeSerializedStr($str);
		global $openurl_map;
		$referent = $referring_entity = $requester = $service_type = $resolver = $referrer = array();
		foreach($params as $key => $value){
			switch(substr($key,0,3)){
				case "rft" :
					$referent[$key] = $value;
					break;
				case "rfe" :
					$referring_entity[$key] = $value;
					break;
				case "req" :
					$requester[$key] = $value;
					break;
				case "svc" :
					$service_type[$key] = $value;
					break;
				case "res" :
					$resolver[$key] = $value;
					break;
				case "rfr" :
					$referrer[$key] = $value;
					break;
			}
		}
		$this->referent = new openurl_entity_referent();
		$this->referent->unserialize($referent,"kev_mtx");
		if(count($referring_entity)){
			$this->referring_entity = new openurl_entity_referring_entity();
			$this->referring_entity->unserialize($referring_entity,"kev_mtx");
		}
		if(count($requester)){
			$this->requester = new openurl_entity_requester();
			$this->requester->unserialize($requester,"kev_mtx");
		}
		if(count($service_type)){
			$this->service_type = new openurl_entity_service_type();
			$this->service_type->unserialize($service_type,"kev_mtx");
		}
		if(count($resolver)){
			$this->resolver = new openurl_entity_resolver();
			$this->resolver->unserialize($resolver,"kev_mtx");
		}
		if(count($referrer)){
			$this->referrer = new openurl_entity_referrer();
			$this->referrer->unserialize($referrer,"kev_mtx");
		}
		$this->getServices();
	}
	
	public function explodeSerializedStr($str){
		$value_name = $value = $tmp = "";
		$params = array();
		for($i=0 ; $i<strlen($str) ; $i++){
			switch($str[$i]){
				case "=" :
					$value_name = $tmp;
					$tmp = "";
					break;
				case "&" :
					$value = $tmp;
					$tmp='';
					if(!isset($params[$value_name])){
						$params[$value_name] = array(
							rawurldecode($value)
						);
					}else{
						$params[$value_name][] = rawurldecode($value);
					}
					$value = $value_name = "";
					break;
				default :
					$tmp.= $str[$i];
					break;
			}
		}
		if($value_name!="" && $tmp!=""){
			if(!isset($params[$value_name])){
				$params[$value_name] = array(
					rawurldecode($tmp)
				);
			}else{
				$params[$value_name][] = rawurldecode($tmp);
			}		
		}
		return $params;	
	}
	
	public function getServices(){
		//pour le moment, juste la recherche
		$this->getSearch();
	}
	
	public function getSearch(){
		global $opac_url_base;
		global $search;
		$search = array();	
				
		$openurl_referent_search= $this->generateEntitySearch($this->referent);
		if($this->referring_entity){
			$openurl_referring_entity_search= $this->generateEntitySearch($this->referring_entity);
		}else $openurl_referring_entity_search="";
		
		global $search;
		$search = array();
		
		//id recherche OpenURL
		$search[0] = "s_2";
		global $op_0_s_2;
		$op_0_s_2 = $rft_search[$i][$j]['op'];
		global $field_0_s_2;
		$field_0_s_2[0] = $openurl_referent_search;		
		global $inter_0_s_2;
		$inter_0_s_2 = "and";
		if($openurl_referring_entity_search != ""){
			$search[1] = "s_2";
			global $op_1_s_2;
			$op_1_s_2 = $rfe_search[$i][$j]['op'];
			global $field_1_s_2;
			$field_1_s_2[0] = $openurl_referring_entity_search;		
			global $inter_1_s_2;
			$inter_1_s_2 = "and";
		}
		
		$s = new search();
		print $s->make_hidden_search_form($opac_url_base."index.php?lvl=search_result&search_type_asked=extended_search","search_form","",false);
		print "
			<input type='hidden' name='launch_search' value='1' />
			<input type='hidden' name='page' value='' />
		</form>
		
		<img src='".$opac_url_base."images/ajax-loader.gif' />
		<style>
			img{
				position : absolute;
				top : 20%;
				left : 50%;
				margin-left : -16px;
				z-index:1000;
			}			
		</style>
		<script>
			window.onload = function (){
				document.search_form.launch_search.value=1;
				document.search_form.submit();
			}
		</script>";
	}
	
	public function generateEntitySearch($entity_search){
		global $search;
		$search = $ent_search = array();
		foreach($entity_search->descriptors as $desc){
			if (count($desc->search_infos)>1){
				array_unshift($ent_search,$desc->search_infos);
			}else{
				array_push($ent_search,$desc->search_infos);
			}
		}
		if(count($ent_search)>0){
			$n = 0;
			for ($i=0 ; $i<count($ent_search) ; $i++){
				for ($j=0 ; $j<count($ent_search[$i]) ; $j++){
					$search[$n]= "f_".$ent_search[$i][$j]['id'];
					$op = "op_".$n."_".$search[$n];
					global ${$op};
					${$op} = $ent_search[$i][$j]['op'];
					$field = "field_".$n."_".$search[$n];
					global ${$field};
					${$field}[0] = $ent_search[$i][$j]['value'];
					if(count($ent_search[$i][$j]['var'])>0){
						$fieldvar = "fieldvar_".$n."_".$search[$n];
						global ${$fieldvar};
						for($k=0 ; $k<count($ent_search[$i][$j]['var']) ; $k++){
							${$fieldvar}[$ent_search[$i][$j]['var'][$k]['name']][0] = $ent_search[$i][$j]['var'][$k]['value'];
						}
					}
					$inter="inter_".$n."_".$search[$n];
					global ${$inter};
					if($n>0){
						${$inter} = ($i>0 ? "or" : "and");
					}
					$n++;
				}
			}
			$s = new search();
			$entity_search = $s->serialize_search();
			$s->destroy_global_env();
		}else{
			$entity_search = "";
		}
		return $entity_search;		
	}
}