<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_search_perso.class.php,v 1.7 2024/04/08 13:07:48 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/search_universes/search_segment_search_perso.tpl.php");

class search_segment_search_perso {
	
	protected $num_segment;
	
	protected $search_perso;
	
	protected $url_base;

	protected $opac;

	protected $order;
	
	public function __construct($num_segment = 0){
		$this->num_segment = intval($num_segment);
		$this->fetch_data();
		$this->url_base = "./index.php?lvl=search_segment&id=".$this->num_segment;
	}
	
	protected function fetch_data() {
		$this->search_perso = array();
		$this->opac = 1;
		$this->order = 0;
		if ($this->num_segment) {
			$query = "SELECT num_search_perso FROM search_segments_search_perso WHERE num_search_segment = '".$this->num_segment."' and search_segment_search_perso_opac = 1 order by search_segment_search_perso_order";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while($row = pmb_mysql_fetch_assoc($result)) {
					$this->search_perso[] = $row['num_search_perso'];
				}
			}
		}
	}
	
	public function get_search_perso() {
		return $this->search_perso;
	}
	
	public function get_form($type = 'record') {
	    global $charset, $base_url;
	    global $segment_search_perso_list_form, $segment_search_perso_list_line_form;
	    
	    $lst = "";
	    $query = "SELECT * FROM search_persopac WHERE search_type = '".$type."' ORDER BY search_order, search_name";
	    $result = pmb_mysql_query($query);
	    $i = 0;
	    while ($row = pmb_mysql_fetch_assoc($result)) {
	        //if ($i % 2) $pair_impair = "even"; else $pair_impair = "odd";
	        $pair_impair = ($i % 2 ? "even" : "odd");
	        $line = $segment_search_perso_list_line_form;
	        $line = str_replace('!!search_perso_class!!', $pair_impair, $line);
	        $line = str_replace('!!search_perso_type!!', 'segment_search_perso[]', $line);
	        $line = str_replace('!!search_perso_checked!!', (in_array($row['search_id'], $this->search_perso) ? "checked" : ""), $line);
	        $line = str_replace('!!search_perso_id!!', $row['search_id'], $line);
	        $line = str_replace('!!search_perso_name!!', htmlentities($row['search_name'], ENT_QUOTES, $charset), $line);
	        $line = str_replace('!!search_perso_shortname!!', htmlentities($row['search_shortname'], ENT_QUOTES, $charset), $line);
	        $line = str_replace('!!search_perso_human!!', $row['search_human'], $line);
	        $line = str_replace('!!search_perso_link!!', $base_url."/admin.php?categ=opac&sub=search_persopac&section=liste&action=form&id=".$row['search_id'], $line);
	        $lst.= $line;
	        $i++;
	    }
	    
	    $segment_search_perso_list_form = str_replace('!!search_perso_list!!', $lst, $segment_search_perso_list_form);
	    return $segment_search_perso_list_form;
	}
		
	public function set_properties_from_form(){
        global $segment_search_perso;
        $this->search_perso = array();
	    if (!empty($segment_search_perso)) {
	        $this->search_perso = $segment_search_perso;
	    }
	}
	
	public function save() {
		static::delete($this->num_segment);
		foreach($this->search_perso as $order=>$num_search_perso) {
			$query = 'INSERT INTO search_segments_search_perso SET
				num_search_segment = '.$this->num_segment.',
				num_search_perso = "'.$num_search_perso.'",
				search_segment_search_perso_opac = "1",
				search_segment_search_perso_order = "'.$order.'"';
			pmb_mysql_query($query);
		}
	}
	
	public static function delete($id=0) {
		$id = intval($id);
		if (!$id) {
			return;
		}
		$query = "delete from search_segments_search_perso where num_search_segment = ".$id;
		pmb_mysql_query($query);
	}
	
	// fonction générant le form de saisie
	public function do_list() {
	    global $search_segment_persopac_table,$search_segment_persopac_line;
	    $forms_search = '';
	    // liste des lien de recherche directe
	    $liste="";
	    // pour toute les recherche de l'utilisateur
	    $my_search = $this->get_search_instance();
	    $i = 0;
	    foreach($this->search_perso as $id) {
	        $pair_impair = ($i % 2 ? "even" : "odd");
	        //composer le formulaire de la recherche
	        $search_perso = new search_persopac($id);
	        
	        $my_search->unserialize_search($search_perso->query);
	        $forms_search.= $my_search->make_hidden_search_form($this->url_base."&search_segment_type=search_perso&limitsearch=1","search_form".$search_perso->id);
	        
	        $td_javascript="  onmousedown=\"javascript:document.forms['search_form".$search_perso->id."'].submit();\" ";
	        $tr_surbrillance = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";
	        
	        $line = str_replace('!!td_javascript!!',$td_javascript , $search_segment_persopac_line);
	        $line = str_replace('!!tr_surbrillance!!',$tr_surbrillance , $line);
	        $line = str_replace('!!pair_impair!!',$pair_impair , $line);
	        
	        $line =str_replace('!!id!!', $search_perso->id, $line);
	        $line = str_replace('!!name!!', $search_perso->name, $line);
	        $line = str_replace('!!human!!', $search_perso->human, $line);
	        $line = str_replace('!!shortname!!', $search_perso->shortname, $line);
	        
	        $liste.=$line;
	        $i++;
	    }
	    $search_segment_persopac_table = str_replace('!!lignes_tableau!!',$liste , $search_segment_persopac_table);
	    return $forms_search.$search_segment_persopac_table;
	}
	
	protected function get_search_instance() {
	    if ($this->num_segment) {
	        $segment = search_segment::get_instance($this->num_segment);
	        return $segment->get_opac_search_instance();
	    }
	    return  new search('search_fields');;
	}
	
	public function get_tab(search_persopac $search_persopac) {
	    global $onglet_persopac;
	    global $search_index;
	    
	    $tab = "<li ".(!empty($onglet_persopac) && $onglet_persopac == $search_persopac->id ? " id='current' aria-current='page' " : "")." >
			<a href=\"javascript:document.forms['search_form".$search_persopac->id."'].submit();\" data-search-perso-id='".$search_persopac->id."'>".($search_persopac->shortname ? $search_persopac->shortname : $search_persopac->name)."</a>";
	    
	    $my_search = $this->get_search_instance();
	    $values = [
	        "search_perso_rmc" => $search_persopac->query,
	        "search_index" => $search_index,
	    ];
	    $tab .= $my_search->make_hidden_search_segment_form($this->url_base."&search_segment_type=extended_search&onglet_persopac=".$search_persopac->id."&no_segment_search=1",$values, "search_form".$search_persopac->id);
	    $tab .= "</li>";
	    return $tab;
	}
	
	public function get_forms_list() {
	    global $search_type_asked;
	    
	    if ((isset($search_type_asked) && $search_type_asked == 'external_search')) {
	        return '';
	    }
	    
	    $my_search = $this->get_search_instance();
	    $forms_search='';
	    $links='';
	    foreach($this->search_perso as $id) {
	        $search_persopac = new search_persopac($id);
	        $forms_search.= $my_search->make_hidden_search_segment_form($this->url_base."&search_segment_type=extended_search&onglet_persopac=".$search_persopac->id."&no_search=1",$search_persopac->query, "search_form".$search_persopac->id);
	        $libelle= $search_persopac->name;
	        $links.="
				<span>
					<a href=\"javascript:document.forms['search_form".$search_persopac->id."'].submit();\" data-search-perso-id='".$search_persopac->id."'>$libelle</a>
				</span>
                <br/>";
	    } 
	    $my_search->pull();
	    return $forms_search.$links;
	}
}