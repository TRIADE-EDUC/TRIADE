<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: editions_datasource.class.php,v 1.7 2018-11-07 13:28:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class editions_datasource {
	public $datas = array();
	public $table= "";
	public $struct_format = array();
	public $filename = "datasources";
	public $datasource = "";
	
	public function __construct($datasource=""){
		$this->datasource = $datasource;
		$this->fetch_datas();
	}

	protected function fetch_datas(){
		if($this->datasource){
			global $include_path;
			
			$list = array();
			$file =$include_path."/editions/".$this->filename."_subst.xml";
			if(!file_exists($file)){
				$file = $include_path."/editions/".$this->filename.".xml";
			}
			$dom = new domDocument();
			$dom->load($file);
			$datasources = $dom->getElementsByTagName("datasource");
			for($i=0 ; $i<$datasources->length ; $i++){
				if($datasources->item($i)->getAttribute('id') == $this->datasource){
					$datasource = $datasources->item($i);
					break;
				}
			}
			if(isset($datasource)) {
				$this->table = $datasource->getElementsByTagName('table')->item(0)->nodeValue;
				$fields = $datasource->getElementsByTagName("field");
				for($i=0 ; $i<$fields->length ; $i++){
					$id = $fields->item($i)->getAttribute('id');
					$this->struct_format[$id] = array(
						'field' => $fields->item($i)->getAttribute('field'),
						'id' => $id,
						'label' => $this->get_label($fields->item($i)->getAttribute('label')),
						'type' => $fields->item($i)->getAttribute('type'),
						'repeat' => $fields->item($i)->getAttribute('repeat'),
						'field_alias' => $fields->item($i)->getAttribute('field_alias'),
						'input' => $fields->item($i)->getAttribute('input')
					);
					$join = $fields->item($i)->getElementsByTagName("join");
					if($join->length){
						$this->struct_format[$id]['join'] = $join->item(0)->nodeValue;
						$this->struct_format[$id]['field_join'] = $join->item(0)->getAttribute('field_join');
						$this->struct_format[$id]['field_group'] = $join->item(0)->getAttribute('field_group');
						$this->struct_format[$id]['authorized_null'] = $join->item(0)->getAttribute('authorized_null');
					}
					$before_joins = $fields->item($i)->getElementsByTagName("before_joins");
					if($before_joins->length){
						for($j=0 ; $j<$before_joins->length ; $j++){
							$this->struct_format[$id]['before_joins'][] = $before_joins->item($j)->nodeValue;
						}
					}
					$vals = $fields->item($i)->getElementsByTagName("values");
					if($vals->length){
						$this->struct_format[$id]['value_type']=$vals->item(0)->getAttribute('type');
						$this->struct_format[$id]['value_object']=$vals;
					}
				}
			}
		}
	}
	
	public function redo_values($fields){
		if($this->struct_format[$fields]['value_type'] && empty($this->struct_format[$fields]['value'])){
			$methode="get_list_".$this->struct_format[$fields]['value_type'];
			$vals=$this->struct_format[$fields]['value_object'];
			$this->struct_format[$fields]['value']=call_user_func(array($this,$methode),$vals);
		}
		return $this->struct_format;
	}
	
	public function  get_list_xml($object){
		global $class_path;
		$tab_return=array();
		$vals = $object->item(0)->getElementsByTagName("value");
		if($vals->length > 0){
			require_once("$class_path/marc_table.class.php");
			$list = new marc_list($vals->item(0)->nodeValue);
			if(count ($list->table)){
				foreach ( $list->table as $key => $value ) {
       				$tab_return[$key] = $value;
				}
			}
		}
		return $tab_return;
	}
	
	public function  get_list_enum($object){
		$tab_return=array();
		$vals = $object->item(0)->getElementsByTagName("value");
		if($vals->length > 0){
			for($j=0 ; $j<$vals->length ; $j++){
				$tab_return[$vals->item($j)->getAttribute("code")] = $this->get_label($vals->item($j)->nodeValue);
			}
		}
		return $tab_return;
	}
	
	public function get_list_query($object){
		global $dbh;
		$tab_return=array();
		$query=$object->item(0)->nodeValue;
		if($query){
			if(preg_match_all("/!!(.*?)!!/",$query,$matches)){//Pour le cas ou j'ai besoin de message dans la requete
				if(count($matches[1])){
					foreach ( $matches[1] as $value ) {
       					$query=str_replace("!!".$value."!!",$this->get_label($value),$query);
					}
				}
			}
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$tab_return[$row->id] = $row->list_value;
				}
			}
		}
		return $tab_return;
	}
	
	public function get_datas($params,$params_values){
		$datas = $label = array();
		//on commence par les libellé...
		foreach($params['fields']['content'] as $field){
			$label[] = $this->struct_format[$field]['label'];
		}
		$datas[]=$label;
		$requete=$this->generate_query($params,$params_values);
		
		$result = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_row($result)){
				$values =array();
				foreach($row as $i =>$val){
					if(!$params['fields']['content'][$i]){
						//Si le champs des résultats ne fait pas partie des champs à afficher je ne le mets pas dans les résultats 
						//(passe ici pour le cas où on filtre sur un champ avec un alias)
					}elseif(isset($this->struct_format[$params['fields']['content'][$i]]['value_type']) && $this->struct_format[$params['fields']['content'][$i]]['value_type'] && $this->struct_format[$params['fields']['content'][$i]]['value_type'] != "query"){
						if($tmp=$this->struct_format[$params['fields']['content'][$i]]['value'][$val]){//Si j'ai la correspondance
							$values[] =  $tmp;
						}else{
							if(($sep=$this->struct_format[$params['fields']['content'][$i]]['repeat'])){//Si le résultat est répété avec un séparateur
								$tmp2=explode($sep, $val);
								foreach ( $tmp2 as $key => $value ) {
       								if($tmp3=$this->struct_format[$params['fields']['content'][$i]]['value'][$value]){
										$tmp2[$key] =  $tmp3;//Je mets le libellé correspondant au code si je le trouve
									}
								}
								$values[] =  implode($sep,$tmp2);
							}else{
								$values[] =  $val;
							}
						}
					}elseif(isset($this->struct_format[$params['fields']['content'][$i]]['type']) && $this->struct_format[$params['fields']['content'][$i]]['type'] == "date"){
						$values[] = formatdate($val);
					}else{
						$values[] = $val;
					}
				}
				$datas[]=$values;
			}
		}
		return $datas;
	}
	
	public function generate_query($params,$params_values){
		global $class_path;
		$query =  $from = $join = $where = $order = $having = "";
		$joins = $group= $select = array();
		if(count($params['fields']['content'])){
			foreach($params['fields']['content'] as $field){
				$select[]= $this->struct_format[$field]['field'];
				
				if(isset($this->struct_format[$field]['before_joins']) && $tmp=$this->struct_format[$field]['before_joins']){
					foreach ( $tmp as $value ) {
       					$joins[]=$value;
					}
				}
				if(isset($this->struct_format[$field]['join']) && $tmp=$this->struct_format[$field]['join']){
					$joins[]=$tmp;
				}
				if(isset($this->struct_format[$field]['field_group']) && $tmp=$this->struct_format[$field]['field_group']){
					$group[]=$tmp;
				}
			}
			if(count($params['filters']['content'])){
				
				foreach($params['filters']['content'] as $field){
					if($this->struct_format[$field]['input'] == "list"){
						require_once($class_path."/editions_state_filter_list.class.php");
						$filter = new editions_state_filter_list($this->struct_format[$field],$params_values['filters'][$field]);
					}else{
						$class = "editions_state_filter_".$this->struct_format[$field]['type'];
						require_once($class_path."/".$class.".class.php");
						$filter = new $class($this->struct_format[$field],$params_values['filters'][$field]);
					} 
					$condition = $filter->get_sql_filter(); 
					if($condition!= ""){
						if($this->struct_format[$field]['field_alias'] && !$this->struct_format[$field]['field_join']){
							//Si je filtre sur un alias il me faut le champ avec l'alias dans les résultats
							$select[]=  $this->struct_format[$field]['field'];
							
							if($having) $having.=" and ";
							$having.= $condition; 
						}else{
							if($where) $where.=" and ";
							$where.= $condition; 
						}
						if(isset($this->struct_format[$field]['before_joins']) && $tmp=$this->struct_format[$field]['before_joins']){
							foreach ( $tmp as $value ) {
		       					$joins[]=$value;
							}
						}
						if($this->struct_format[$field]['join']){
							$joins[]= $this->struct_format[$field]['join'];
						}
						if(isset($this->struct_format[$field]['field_group']) && $tmp=$this->struct_format[$field]['field_group']){
							$group[]=$tmp;
						}
					}
				}
				if($where){
					$where = " where ".$where;
				}
				if($having){
					$having = " HAVING ".$having;
				}
			}
			if(count($params['orders']['content'])){
				foreach($params['orders']['content'] as $field){
					if($order) $order.=", ";
					$crit = new editions_state_order($this->struct_format[$field],$params_values['orders'][$field]);
					$order.= $crit->get_sql_filter();
				}
				if($order){
					$order = " order by ".$order;
				}
			}
		}
		$select = array_unique($select);
		$joins = array_unique($joins);
		$group = array_unique($group);
		$group_by="";
		if(count($group)){
			$group_by=" GROUP BY ".implode(",",$group)." ";
		}
		$select_text="SELECT ";
		if(count($select)){
			$select_text.=implode(", ",$select);
		}
		return $select_text." from ".$this->table." ".implode(" ",$joins)." ".$where.$group_by.$having.$order;
	}
	
	public function get_datasources_list(){
		global $include_path;
		
		$list = array();
		$file =$include_path."/editions/".$this->filename."_subst.xml";
		if(!file_exists($file)){
			$file = $include_path."/editions/".$this->filename.".xml";
		}
		$dom = new domDocument();
		$dom->load($file);
		$datasources = $dom->getElementsByTagName("datasource");
		for($i=0 ; $i<$datasources->length ; $i++){
			$list[$datasources->item($i)->getAttribute('id')] = $this->get_label($datasources->item($i)->getAttribute('name'));
		}	
		return $list;	
	}
	
	public function get_struct_format(){
		return $this->struct_format;
	}
	
	public function get_label($val){
		global $msg,$charset;
		$val_return="";
		if(preg_match("/^msg:(.*)/",$val,$matches)){
			$val_return=$msg[$matches[1]];
		}else{
			if($charset == "utf-8"){
				$val_return=$val;
			}else{
				$val_return=utf8_decode($val);
			}
			
		}
		return $val_return;
	}
	
}