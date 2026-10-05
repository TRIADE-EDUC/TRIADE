<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: openurl_instance.class.php,v 1.3 2019/07/11 10:24:50 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/openurl/descriptors/openurl_descriptors_kev_mtx.class.php");
require_once($class_path."/openurl/entities/openurl_entities.class.php");
require_once($class_path."/openurl/context_object/openurl_context_object_kev_mtx_ctx.class.php");
require_once($class_path."/openurl/transport/openurl_transport_http.class.php");
//pour récup les infos de notice
require_once($base_path."/admin/convert/export.class.php");
require_once($class_path."/export_param.class.php");

//Cette s'occupe de toute la gestion des classes OpenURL, donc en théorie, si je m'en sors, cette seule classe peut suffir à l'interface...

class openurl_instance {
	public $notice_id = 0;				// identifiant de la notice
	public $notice_externe_id = 0;		// identifiant de la notice externe
	public $parent_id = 0;				// identifiant de la notice parente
	public $params = array();			// jeu de paramètres...
	public $notice_infos = array();			// informations sur la notice
	public $parent_infos = array();			// informations sur la notice parente, si disponible...
	public $serialization = "";		// mode de sérialization
	public $referent;					// entité referent
	public $referringEntity;			// entité referring_entity
	public $requester;					// entité requester
	public $serviceType;				// entité service_type
	public $resolver;					// entité resolver
	public $referrer;					// entité referrer
	public $contextObject;				// l'objet contextuel
	public $transport;					// object pour le transport
	public $source_id;					// id de la source

	public function __construct($id=0,$id_externe=0,$params=array(),$source_id=0){
		$this->notice_id = $id;
		$this->notice_externe_id = $id_externe;
		$this->params = $params;
		$this->source_id = $source_id;
		$this->fetch_data();
	}
	
	public function fetch_data(){
		//si on a rien, on peut pas travailler...
		if(!$this->notice_id && !$this->notice_externe_id)
			return false;

		if($this->notice_id){
			//pour une notice de la base...
			
			//récupère les param d'exports
			$export_param = new export_param();
			$param = $export_param->get_parametres($export_param->context);
			//petit nettoyage pour un bon fonctionnement...
			foreach($param as $key => $value){
				$param[str_replace("export_","",$key)] = $param[$key];
			}
			//maintenant que c'est en ordre, on peut y aller!
			$export = new export(array($this->notice_id),array(),array());
			$export->get_next_notice("",array(),array(),false,$param);
			$this->notice_infos = $export->xml_array;
	
			//on regarde si on veut aussi les infos de la notice contenante...
			if ($this->params['entities']['referring_entity']['allow'] == "yes"){
				//il nous faut déjà l'identifiant du parent, s'il existe...
				$this->parent_id = 0;
				switch($this->notice_infos['bl']['value'].$this->notice_infos['hl']['value']){
					case "a2" :
					case "s2" :
						$field = "461";
						break;
					default :
						$field = "463";
						break;
				}
				foreach($this->notice_infos['f'] as $f){
					switch($f['c']){
						case $field:
							foreach($f['s'] as $s){
								switch($s['c']){
									case "9" :
										if(strpos($s['value'],"id:")!==false){
											$this->parent_id = str_replace("id:","",$s['value']);
										}
										break;
								}
							}
							break;
					}
				}			
				if($this->parent_id){
					$export_parent = new export(array($this->parent_id),array(),array());
					$export_parent->get_next_notice("",array(),array(),false,$param);
					$this->parent_infos=$export_parent->xml_array;
				}	
			}
		}else{
			//pour une notice externe
			//TODO : récup notice infos
		}

		//on récup la sérialization pour gérer nos objets
		switch($this->params['serialization']){
			case "kev" :
			default :
				$this->serialization = "kev_mtx";
				break;
		}
	}

	public function getReferent(){
		$descriptors = array();
		foreach($this->params['entities']['referent'] as $desc => $type){
			if(is_array($type)){
				foreach($type as $key => $asked){
					if($asked != 0){
						//si le descripteur "$key" est demandé
						$class_desc = "openurl_descriptor_".$desc."_".$this->serialization."_".$key;
						$descriptors[] = new $class_desc($this->notice_infos);
					}
				}
			}else{
				if($type != 0){
					if($this->notice_infos['bl']['value'] == "m") $item = "book";
					else $item = "journal";
					$class_desc = "openurl_descriptor_".$desc."_".$this->serialization."_".$item;
					if($desc == "byref"){
						$this->getTransport();
						$descriptors[] = new $class_desc($this->notice_infos,$this->source_id,$this->params['transport']['byref_url']);
					}else{
						$descriptors[] = new $class_desc($this->notice_infos);
					}
				}
			}
		}
		$this->referent = new openurl_entity_referent();
		foreach($descriptors as $desc){
			$this->referent->addDescriptor($desc);
		}
		return $this->referent;
	}

	public function getReferringEntity(){
		if($this->parent_id){
			$descriptors = array();
			
			foreach($this->params['entities']['referring_entity']['elem'] as $desc => $type){
				if(is_array($type)){
					foreach($type as $key => $asked){
						if($asked != 0){
							//si le descripteur "$key" est demandé
							$class_desc = "openurl_descriptor_".$desc."_".$this->serialization."_".$key;
							$descriptors[] = new $class_desc($this->parent_infos);
						}
					}
				}else{
					if($type != 0){
						if($this->notice_infos['bl']['value'] == "m") $item = "book";
						else $item = "journal";
						$class_desc = "openurl_descriptor_".$desc."_".$this->serialization."_".$item;
						if($desc == "byref"){
							$this->getTransport();
							$descriptors[] = new $class_desc($this->parent_infos,$this->source_id,$this->params['transport']['byref_url']);
						}else{
							$descriptors[] = new $class_desc($this->parent_infos);
						}
					}
				}	
			}
			$this->referringEntity = new openurl_entity_referring_entity();
			foreach($descriptors as $desc){
				$this->referringEntity->addDescriptor($desc);
			}
			return $this->referringEntity;
			
		}else {
			return false;
		}
	}

	public function getRequester(){
		if($this->params['entities']['requester']['allow'] == "yes"){
			$class_desc = "openurl_descriptor_identifier_".$this->serialization."_requester";
			$desc = new $class_desc($this->params['entities']['requester']['value']);
			$this->requester = new openurl_entity_requester();
			$this->requester->addDescriptor($desc);
			return $this->requester;
		}else{
			return false;
		}
	}

	public function getServiceType(){
		if($this->params['entities']['service_type']['allow'] == "yes"){
			$class_desc = "openurl_descriptor_byval_".$this->serialization."_service_type";
			$desc = new $class_desc($this->params['entities']['service_type']['values']);
			$this->serviceType = new openurl_entity_service_type();
			$this->serviceType->addDescriptor($desc);
			return $this->serviceType;	
		}else {
			return false;
		}
	}

	public function getResolver(){
			if($this->params['entities']['resolver']['allow'] == "yes"){
			$class_desc = "openurl_descriptor_identifier_".$this->serialization."_resolver";
			$desc = new $class_desc($this->params['entities']['resolver']['value']);
			$this->resolver = new openurl_entity_resolver();
			$this->resolver->addDescriptor($desc);
			return $this->resolver;
		}else {
			return false;
		}
	}

	public function getReferrer(){
		if($this->params['entities']['referrer']['allow'] == "yes"){
			$class_desc = "openurl_descriptor_identifier_".$this->serialization."_referrer";
			$desc = new $class_desc($this->params['entities']['referrer']['value']);
			$this->referrer = new openurl_entity_referrer();
			$this->referrer->addDescriptor($desc);
			return $this->referrer;
		}else {
			return false;
		}
	}

	public function generateEntities(){
		$entities = array();
		$entities[] = $this->getReferent();
		
		if($this->parent_id){
			$entities[] = $this->getReferringEntity();
		}
		if($this->params['entities']['requester']['allow'] == "yes"){
			$entities[] = $this->getRequester();
		}
		if($this->params['entities']['service_type']['allow'] == "yes"){
			$entities[] = $this->getServiceType();
		}
		if($this->params['entities']['resolver']['allow'] == "yes"){
			$entities[] = $this->getResolver();
		}
		if($this->params['entities']['referrer']['allow'] == "yes"){
			$entities[] = $this->getReferrer();
		}
		return $entities;
	}

	public function getContextObject(){
		switch($this->serialization){
			case "kev_mtx" : 
					$this->contextObject = new openurl_context_object_kev_mtx_ctx();
				break;
		}
	}
	
	public function generateContextObject(){
		$this->getContextObject();
		$entities = $this->generateEntities();
		foreach($entities as $entity){
			$this->contextObject->addEntity($entity);
		}
	}
	
	public function getTransport(){
		if(!$this->transport){
			$class = "openurl_transport_".$this->params['transport']['method']."_".$this->params['transport']['protocole'];
			if($this->params['transport']['method']=="byref"){
				$this->transport = new $class($this->params['transport']['param'],$this->notice_id,$this->source_id,$this->params['transport']['byref_url']);
			}else $this->transport = new $class($this->params['transport']['param']);
		}
	}
	
	public function generateTransport(){
		$this->getTransport();
		if(!$this->contextObject) $this->generateContextObject();
		$this->transport->addContext($this->contextObject);
	}
	
	public function getInFrame($width,$height){
		$this->generateTransport();
		return  "<iframe style='width:".$width."px;height:".$height."px' src='".$this->transport->generateURL()."'></iframe>";
	}
}