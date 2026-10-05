<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ontologies.class.php,v 1.6 2023/02/17 13:45:34 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class ontologies
{

	protected $ontologies = array();
	
    public function __construct()
    {
		$this->fetch_datas();
	}
	
    protected function fetch_datas()
    {
		$query = "select id_ontology from ontologies order by ontology_name";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$this->ontologies[$row->id_ontology] = new ontology($row->id_ontology);
			}
		}
	}
	
	public function get_modelling_menu(){
		global $charset;
		$menu = "";
		
		foreach($this->ontologies as $ontology){
			$menu.="
		<span".ongletSelect("categ=ontologies&ontology_id=".$ontology->get_id()).">
			<a title='".htmlentities($ontology->get_name(),ENT_QUOTES,$charset)."' href='./modelling.php?categ=ontologies&ontology_id=".$ontology->get_id()."'>
				".htmlentities($ontology->get_name(),ENT_QUOTES,$charset)."
			</a>
		</span>";
		}
		return $menu;
	}
	
    public function admin_proceed($action, $id)
    {
		switch($action){
			case 'add' :
				$ontology = new ontology();
				print $ontology->get_form();
				break;
			case 'edit' :
				print $this->ontologies[$id]->get_form();
				break;
			case "delete" :
				if(is_object($this->ontologies[$id])){
					if($this->ontologies[$id]->delete()){
						unset($this->ontologies[$id]);
					}else{
						//loupé
					}
				}
				print $this->get_list();
				break;
			case 'save' :
				if(!isset($this->ontologies[$id])){
					$ontology = new ontology($id);
					$ontology->get_values_from_form();
					$this->ontologies[$ontology->save()] = $ontology;
				}else{
					$this->ontologies[$id]->get_values_from_form();
					$this->ontologies[$id]->save();
				}
			default :
				print $this->get_list();
				break;
		}
	}
	
    public function get_list()
    {
		global $charset,$ontologies_list,$ontologies_list_item;
		
		$list = $ontologies_list;
		$parity=1;
		$items = "";
		foreach($this->ontologies as $ontology){
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$parity += 1;
			$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onclick=\"document.location='./modelling.php?categ=ontologies&sub=general&act=edit&ontology_id=".$ontology->get_id()."';\" ";
			$item = str_replace("!!tr_javascript!!",$tr_javascript,$ontologies_list_item);
			$item = str_replace("!!label!!",htmlentities($ontology->get_name(),ENT_QUOTES,$charset),$item);
            $item = str_replace("!!description!!", htmlentities($ontology->get_description(), ENT_QUOTES, $charset), $item);
            $item = str_replace("!!id!!", htmlentities($ontology->get_id(), ENT_QUOTES, $charset), $item);
			$items.=$item;
		}
		$list = str_replace("!!items!!",$items,$list);
		return $list;
	}
	
    public function get_semantic_menu()
    {
	    global $charset, $ontology_id;
		$menu="";
		foreach($this->ontologies as $ontology){
			$menu.="
				<li ".( $ontology_id == $ontology->get_id() ? "class='active'" : "" )."><a href='./semantic.php?ontology_id=".$ontology->get_id()."'>".htmlentities($ontology->get_name(),ENT_QUOTES,$charset)."</a></li>";
		}
		return $menu;
	}
	
    public function get_other_ontologies($ontology_id = 0)
    {
		$ontologies = array();
		if($ontology_id == 0) {
			global $ontology_id;
		}
		foreach($this->ontologies as $ontology){
			if($ontology->get_id() != $ontology_id){
				$ontologies[$ontology->get_base_uri()] = $ontology->get_name();
			}
		}
		$ontologies['http://www.w3.org/2004/02/skos/core'] = "PMB-SKOS";
		return $ontologies;
	}
	
    public function get_other_ontologies_classes($ontology_id = 0)
    {
		$ontologies = array();
		if($ontology_id == 0) {
			global $ontology_id;
		}
		foreach($this->ontologies as $ontology){
			if($ontology->get_id() != $ontology_id){
				$ontologies[] = array(
					'group_name' => $ontology->get_name(),
					'items' => 	$ontology->get_classes()
				);
			}
		}
		$ontologies[] = array(
			'group_name' => 'PMB-SKOS',
			'items' => array(
				'http://www.w3.org/2004/02/skos/core#Concept' => "Concept",
				'http://www.w3.org/2004/02/skos/core#ConceptScheme' => "Schéma",
				'http://www.w3.org/2004/02/skos/core#Collection' => "Collection",
				'http://www.w3.org/2004/02/skos/core#OrderedCollection' => "Collection Ordonnée"
			)
		);
		return $ontologies;
	}
	
    public function looking_for_use_in_concepts()
    {
		$used = array();
		foreach($this->ontologies as $ontologies){
			$used = array_merge($used,$ontologies->get_classes_for_concepts());
		}
		return $used;
	}
	/**
	 *
	 * @param string $name
	 * @return ontology|boolean
	 */
	public static function get_ontology_by_pmbname($pmbname)
	{
	    $query = "select id_ontology from ontologies where ontology_pmb_name = '" . $pmbname . "'";
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
	        $id = pmb_mysql_result($result, 0, 0);
	        return new ontology($id);
	    }
	    return false;
	}
	
	public static function get_ontology_id_from_class_uri($uri)
	{
	    $tmp = substr($uri, strrpos($uri, '/') + 1);
	    $onto = substr($tmp, 0, strpos($tmp, '#'));
	    if (is_numeric($onto)) {
	        return intval($onto);
	    }
	  
	    return 0;
	}
	
	public function get_available_segments()
	{
	    $segments = [];
	    foreach ($this->ontologies as $ontology) {
	        /**
	         * var ontology $ontology
	         */
	        $segments[$ontology->get_name()] = $ontology->get_available_segments();
	    }
	    return $segments;
	}
}