<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_docnumslist_datasource_docnumslist.class.php,v 1.15.2.1 2025/01/17 10:40:46 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_docnumslist_datasource_docnumslist extends cms_module_common_datasource_jsonrest {
	protected static $nb_row=0;
    private $docnums_table;
 
	public function __construct($id=0){
		parent::__construct($id);
		$this->sortable = false;
		$this->limitable = false;
	}
	
	public function get_available_selectors(){
		return array(
			'cms_module_common_selector_generic_records' 
		);
	}
	
	public function get_form(){
		$facette_search = new facette_search();
		$form = parent::get_form();
		$form.= '
		<div class="row">
			<div class="colonne3">
				<label for="'.$this->get_form_value_name('facette').'">'.$this->format_text($this->msg['cms_module_docnumslist_datasource_docnumslist_facette_form']).'</label>
			</div>
			<div class="colonne_suite">
				<label>'.$this->format_text($this->msg['cms_module_docnumslist_datasource_docnumslist_facette_crit']).'</label>
				'. $facette_search->create_list_fields().'
				<div id="liste2"></div>
				<input type="button" class="bouton" value="'.$this->format_text($this->msg['add']).'" onclick="valid_facette()">
			</div>
		</div>
		<div class="row">
			<div><label>'.$this->format_text($this->msg['cms_module_docnumslist_datasource_docnumslist_facette_active']).'</label></div>
			<div class="colonne_suite" id="ds_facettes">'.
			     		$this->generate_table()
			.'</div>
		</div>
		<script src="./javascript/http_request.js"></script>
			<script>
				var crit_label = '.json_encode(encoding_normalize::utf8_normalize($facette_search->fields_sort())).';		
				function valid_facette(){
					var crit = document.getElementById("list_crit").value;
					var table_crit = document.getElementById("defined_crits");
				    
				    var subcrit = "";
					if (document.getElementById("list_ss_champs")) {
						subcrit = document.getElementById("list_ss_champs").value;
					}
					
				    var input_crit = document.createElement("input");
					input_crit.setAttribute("type","hidden");
					input_crit.setAttribute("name","'.$this->get_form_value_name("crit").'[]");
					input_crit.setAttribute("value",crit);		
					
					var input_subcrit = document.createElement("input");
					input_subcrit.setAttribute("type","hidden");
					input_subcrit.setAttribute("name","'.$this->get_form_value_name("subcrit").'[]");
					input_subcrit.setAttribute("value",subcrit);		

					var tr = document.createElement("tr");
					var td_inputs = document.createElement("td");    
                    var td_delete = document.createElement("td");
                    var label_crit = document.createElement("label");
					
					var input_delete = document.createElement("input");
					input_delete.setAttribute("type", "button");
					input_delete.setAttribute("value", "X");
					input_delete.setAttribute("class", "bouton");
					input_delete.addEventListener("click", delete_line);
					 
					    
				    label_crit.innerHTML = crit_label[crit];
					
					td_inputs.appendChild(label_crit);
                    td_inputs.appendChild(input_crit);
					td_inputs.appendChild(input_subcrit);

                    td_delete.appendChild(input_delete);
					    
					tr.appendChild(td_inputs);
					tr.appendChild(td_delete);
					
					table_crit.appendChild(tr);
				}
				function load_subfields(id_ss_champs){
					var lst = document.getElementById("list_crit");
					var id = lst.value;
					var id_subfields = id_ss_champs;
					var xhr_object=  new http_request();					
					xhr_object.request("./ajax.php?module=admin&categ=opac&section=lst_facette",true,"list_crit="+id+"&sub_field="+id_subfields,true,cback,0,0)
				}
				function cback(response){						
					var div = document.getElementById("liste2");
					div.innerHTML = response;
				}
			    function delete_line(evt){
                    var td = evt.originalTarget.parentNode;
					var tr = td.parentNode;
					var tBody = tr.parentNode;
                    tBody.removeChild(tr);	    	        
			    }
			</script>';
		return $form;
	} 
	
	public function save_form(){
		$this->parameters['crit'] = $this->get_value_from_form('crit');
		$this->parameters['subcrit'] = $this->get_value_from_form('subcrit');
		return parent::save_form();
	}
	
	/**
	 * Génération du tableau de critères enregistrés pour la source
	 * @return string
	 */
	private function generate_table(){
        $return_table = '<table id="defined_crits" name="defined_crits">';
        $return_table.= '<th>'.$this->format_text($this->msg['cms_module_docnumslist_datasource_docnumslist_facette_crit']).'</th><th></th>';
	    if(isset($this->parameters['crit'])){
	        $facette_search = new facette_search(); 
	        foreach($this->parameters['crit'] as $index => $crit){
	            $return_table.='<tr>
	                <td><label>'.$this->format_text($facette_search->fields_sort()[$crit]).'</label> 
	                   <input type="hidden" value="'.$crit.'" name="'.$this->get_form_value_name("crit").'[]"/> 
	                   <input type="hidden" value="'.($this->parameters['subcrit'][$index]*1).'" name="'.$this->get_form_value_name("subcrit").'[]"/>'.'
	                </td>
	                <td><input class="bouton" type="button" onclick="delete_line(event)" value="X"/></td>
	            </tr>';
	    	}
	    }
	    $return_table.='</table>';
	    return $return_table;
	}
	
	
	public function store_proceed($content){
		global $parent;
			
		if($parent && $this->datas[$parent]){
			return $this->datas[$parent];
		}
		if($parent){
			switch($parent){
				case "root" :
	 				return $this->get_groups($parent, $content['selector_value'], 0);
					break;
				default :
					$item = $this->find_item($parent);
					if(isset($this->parameters['crit'][($item['lvl']+1)])){
						return $this->get_groups($parent, $item['records'], $item['lvl']+1);
					}else{
						return $this->get_explnums_from_records($item['records'],$parent);
					}
					break;
			}
		}		
		return array(
			array(
				'id' => "root",
				'children' => true
			)
		);
	}
	
	private function get_groups($parent,$records,$lvl){
		global $lang;		
		pmb_mysql_query("SET SESSION group_concat_max_len = 1000000");
		$req = 'select group_concat(id_notice) as notices_ids, value from notices_fields_global_index where lang in ("", "'.$lang.'")
	        and id_notice in ("'.implode('","', $records).'") and code_champ="'.($this->parameters['crit'][$lvl]*1).'"';
		if($this->parameters['subcrit'][$lvl]){
			$req.= ' and code_ss_champ ="'.$this->parameters['subcrit'][$lvl].'"';
		}
		$req.= ' group by value';
		$result = pmb_mysql_query($req);
		$result_array =array();
		while($row = pmb_mysql_fetch_object($result)) {
			self::$nb_row++;
			$exploded_notices_ids = explode(',',$row->notices_ids); //Array d'ids de notices retourné par la requête
			$records = array_diff($records, $exploded_notices_ids); //On stock les ids de notices non traités pour les placer dans une catégorie "inconnu"
			$temp = array(
				'id' => self::$nb_row,
				'name' => $row->value,
				'parent' => $parent,
				'lvl' => $lvl,
				'records' => $exploded_notices_ids,
			    'children' => (isset($this->parameters['crit'][$lvl+1]) ? true : ( (is_countable($exploded_notices_ids) && count($exploded_notices_ids) > 0) ? true : false))
			);
			$result_array[] = $temp;
		}
		if(is_countable($records) && count($records)){ //Si il reste des notices non traitées, on les places dans une catégorie "inconnu"
			self::$nb_row++;
 			$temp = array(
 				'id' => self::$nb_row,
				'name'=>$this->msg['cms_module_docnumslist_datasource_docnumslist_unknown'],
				'parent' => $parent,
				'lvl' => $lvl,
 				'records' => $records,
 			    'children' => (isset($this->parameters['crit'][$lvl+1]) ? true : ( (is_countable($records) && count($records)) > 0 ? true : false))
 			);
			$result_array[] = $temp;
		}
		$this->datas[$parent] = $result_array;
		$this->save_store();
		return $result_array;
	}
	
	private function find_item($id){
		foreach($this->datas as $items){
			foreach($items as $item){
				if($item['id'] == $id){
					return $item;
				}
			}
		}
	} 
	
	private function get_explnums_from_records($records,$parent){
		$docnums_ids = array();
		
		/**
		 * Va récupérer les docnums des monographie, des notices de bulletins et des notices de perio
		*/
		$req_notices = 'select explnum.explnum_id,notices.tit1 as explnum_nom, notices.tit4 as cplt from explnum
	    join notices on notices.notice_id = explnum.explnum_notice where explnum_notice in ("'.implode('","', $records).'") and explnum_bulletin=0';
		 
		/**
		 * Va récupérer les docnums des monographie, des notices de bulletins et des notices de perio
		*/
		$req_noti_bulletin = 'select explnum.explnum_id, notices.tit1 as explnum_nom, notices.tit4 as cplt from explnum
		join bulletins on explnum.explnum_bulletin = bulletins.bulletin_id and explnum.explnum_notice = 0 
		join notices on bulletins.num_notice = notices.notice_id 
	    where notices.notice_id in ("'.implode('","', $records).'") ';
		 
		/**
		 * Récupération des documents numériques des bulletins d'un periodique
		 */
		$req_bulletin_from_perio = 'select explnum.explnum_id, notices.tit1 as explnum_nom, notices.tit4 as cplt from explnum
        join bulletins on bulletins.bulletin_id = explnum.explnum_bulletin
        join notices on notices.notice_id = bulletins.bulletin_notice
        and notices.niveau_hierar = "1" and notices.niveau_biblio = "s" and notices.notice_id in ("'.implode('","', $records).'")';
		
		/**
		 * Récupération des documents numériques des articles d'un périodique
		 */
		$req_art_from_perio = 'select explnum.explnum_id,notice_art.tit1 as explnum_nom, notice_art.tit4 as cplt  from explnum
        join analysis on analysis.analysis_notice = explnum.explnum_notice and explnum.explnum_bulletin = 0
        join bulletins on analysis.analysis_bulletin = bulletins.bulletin_id
        join notices as notice_art on notice_art.notice_id = analysis.analysis_notice
        join notices as notice_serial on notice_serial.notice_id = bulletins.bulletin_notice
        and notice_art.niveau_hierar = "2" and notice_art.niveau_biblio = "a"
        and notice_serial.niveau_hierar = "1" and notice_serial.niveau_biblio = "s" and notice_serial.notice_id in ("'.implode('","', $records).'")';
		
		$final_req = 'select explnum_id,explnum_nom, cplt from (('.$req_notices.') ';
		$final_req.= 'union ('.$req_bulletin_from_perio.') ';
		$final_req.= 'union ('.$req_noti_bulletin.') ';
		$final_req.= 'union ('.$req_art_from_perio.')) as uni order by explnum_nom desc, cplt asc';
		
		$result = pmb_mysql_query($final_req);
		while($row = pmb_mysql_fetch_object($result)) {
			$docnums_ids[] = array(
				'id' => 'explnum'.$row->explnum_id,
				'name' => $row->explnum_nom,
				'children' => false,
				'explnum_id' => $row->explnum_id,
				'parent' => $parent
			);
		}
		return $docnums_ids;
	}
	
	public function set_nb_row($nb_row){
		self::$nb_row = $nb_row;
	}
	
	protected function save_store(){
		$selector = $this->get_selected_selector();
		file_put_contents("./temp/".$this->get_store_hash(), serialize(array(
			'id' => $this->id,
			'classname' => get_class($this),
			'selector_value' => $this->filter_datas('notices',$selector->get_value()),
			'datas' => $this->datas,
			'nb_row' => self::$nb_row
		)));
	}
}