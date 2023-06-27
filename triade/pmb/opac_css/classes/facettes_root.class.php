<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes_root.class.php,v 1.43 2019-06-11 08:53:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/facette_search_compare.class.php");
require_once($class_path."/encoding_normalize.class.php");
require_once($class_path."/map/map_facette_controler.class.php");

abstract class facettes_root {
	/**
	 * Objets séparées par des virgules
	 * @var string
	 */
	public $objects_ids;
	
	/**
	 * Liste des facettes
	 * @var array
	 */
	public $facettes;
	
	/**
	 * Liste des facettes calculées
	 * @var array
	 */
	public $tab_facettes;
	
	/**
	 * Flag pour indiquer qu'au moins une des facettes sera affichée
	 * @var boolean
	 */
	public $exists_with_results = false;
	
	/**
	 * Mode d'affichage (extended/external)
	 * @var string
	 */
	public $mode = 'extended';
	
	/**
	 * Comparateur de notice activé (oui/non)
	 * @var string
	 */
	protected static $compare_notice_active;
	
	/**
	 * Instance
	 * @var facette_search_compare
	 */
	protected $facette_search_compare;
	
	/**
	 * Liste des facettes sélectionnées
	 * @var array
	 */
	protected $clicked;
	
	/**
	 * Liste des facettes non sélectionnées
	 * @var array
	 */
	protected $not_clicked;
	
	/**
	 * Liste des valeurs par facette hors limite
	 */
	protected $facette_plus;
	
	protected static $facet_type;
	
	protected static $url_base;
	
	public function __construct($objects_ids = ''){
		$this->objects_ids = $objects_ids;
		$this->facette_existing();
		$this->nb_results_by_facette();
	}
	
	protected function get_query() {
	    $query = "SELECT * FROM ".static::$table_name." WHERE facette_visible=1";
	    if (!empty(static::$facet_type)) {
	        $query .= " AND facette_type = '".static::$facet_type."'";
	    }
	    $query .= " ORDER BY facette_order, facette_name";
	    
	    return $query;
	}
	protected function facette_existing(){
		global $opac_view_filter_class;
		
		$this->facettes = array();
		$query = $this->get_query();
		$result = pmb_mysql_query($query);
		while($row = pmb_mysql_fetch_object($result)){
			if($opac_view_filter_class) {
				if(!$opac_view_filter_class->is_selected(static::$table_name, $row->id_facette+0))  continue;
			}
			$this->facettes[] = array(
				'id'=> $row->id_facette+0,
				'type'=> $row->facette_type,
				'name'=>$row->facette_name,
				'id_critere'=>$row->facette_critere+0,
				'id_ss_critere'=>$row->facette_ss_critere+0,
				'nb_result'=>$row->facette_nb_result+0,
				'limit_plus'=>$row->facette_limit_plus+0,
				'type_sort'=>$row->facette_type_sort+0,
				'order_sort'=>$row->facette_order_sort+0,
				'datatype_sort'=>$row->facette_datatype_sort
			);
		}
	}
	
	public function nb_results_by_facette(){
		global $msg;
		
		$this->tab_facettes = array();
		if($this->objects_ids != ""){
			foreach ($this->facettes as $facette) {
				$query = $this->get_query_by_facette($facette['id_critere'], $facette['id_ss_critere'], $facette['type']);
				if ($facette['type_sort']==0) {
					$query .= " nb_result";
				} else {
					if ($facette['datatype_sort']== 'date') {
						$query .= " STR_TO_DATE(value,'".$msg['format_date']."')";
					} else {
						$query .= " value";
					}
				}
				if($facette['order_sort']==0){
					$query .= " asc";
				} else {
					$query .= " desc";
				}
				if($facette['nb_result']>0){
					$query .= " LIMIT"." ".$facette['nb_result'];
				}
				$result = pmb_mysql_query($query);
				$j=0;
				$array_tmp = array();
				$array_value = array();
				$array_nb_result = array();
				if($result && pmb_mysql_num_rows($result)){
					while($row = pmb_mysql_fetch_object($result)){
						$array_tmp[$j] =  $row->value." "."(".($row->nb_result+0).")";
						$array_value[$j] = $row->value;
						$array_nb_result[$j] = ($row->nb_result+0);
						$j++;
					}
					$this->exists_with_results = true;
				}
				$this->tab_facettes[] = array(
						'name' => $facette['name'],
						'facette' => $array_tmp,
						'code_champ' => $facette['id_critere'],
						'code_ss_champ' => $facette['id_ss_critere'],
						'value' => $array_value,
						'nb_result' => $array_nb_result,
						'size_to_display' => $facette['limit_plus']
				);
			}
		}
	}
	
	public static function see_more($json_facette_plus){
		global $charset;
		
		$arrayRetour = array();
		for($j=0; $j<count($json_facette_plus['facette']); $j++){
			$facette_id = facette_search_compare::gen_compare_id($json_facette_plus['name'],$json_facette_plus['value'][$j],$json_facette_plus['code_champ'],$json_facette_plus['code_ss_champ'],$json_facette_plus['nb_result'][$j]);
			$facette_value = encoding_normalize::json_encode(array($json_facette_plus['name'], $json_facette_plus['value'][$j], $json_facette_plus['code_champ'], $json_facette_plus['code_ss_champ'], $facette_id, $json_facette_plus['nb_result'][$j]));
			$arrayRetour[]= array(
					'facette_libelle' => htmlentities(static::get_formatted_value($json_facette_plus['code_champ'],$json_facette_plus['code_ss_champ'], $json_facette_plus['value'][$j]),ENT_QUOTES,$charset),
					'facette_number' => htmlentities($json_facette_plus['nb_result'][$j],ENT_QUOTES,$charset),
					'facette_id' => $facette_id,
					'facette_value' => htmlentities($facette_value,ENT_QUOTES,$charset),
					'facette_link' => static::get_link_not_clicked($json_facette_plus['name'], $json_facette_plus['value'][$j], $json_facette_plus['code_champ'], $json_facette_plus['code_ss_champ'], $facette_id, $json_facette_plus['nb_result'][$j])
			);
		}
		return encoding_normalize::json_encode($arrayRetour);
	}
	
	public static function destroy_dom_node() {
		if($_SESSION["cms_build_activate"]) {
			return "";
		} else {
			return "
				<script type='text/javascript'>
							require(['dojo/ready', 'dojo/dom-construct'], function(ready, domConstruct){
								ready(function(){
									domConstruct.destroy('facette');
								});
							});
				</script>";
		}
	}
	
	public static function get_nb_facettes() {
	    $query = "SELECT count(id_facette) FROM ".static::$table_name." WHERE facette_visible=1";
	    if (!empty(static::$facet_type)) {
	        $query .= " AND facette_type = '".static::$facet_type."'";
	    }
		$result = pmb_mysql_query($query);
		return pmb_mysql_result($result, 0);
	}
	
	protected static function get_ajax_url() {
		global $base_path;
		
		return $base_path."/ajax.php?module=ajax&categ=".static::$table_name."&sub=get_data&facet_type=".static::$facet_type;
	}
	
	public static function call_ajax_facettes($additional_content = "") {
		$ajax_facettes = $additional_content;		
		if(static::get_nb_facettes()) {
			$ajax_facettes .= static::get_facette_wrapper();
			$ajax_facettes .="
				<div id='facette_wrapper'>
					<img src='".get_url_icon('patience.gif')."'/>
					<script type='text/javascript'>
						var req = new http_request();
						req.request(\"".static::get_ajax_url()."\",false,null,true,function(data){
							var response = JSON.parse(data);
							document.getElementById('facette_wrapper').innerHTML=response.display;
						    require(['dojo/query', 'dojo/dom-construct'], function(query, domConstruct){
    						    query('#facette_wrapper script').forEach(function(node) {
                					domConstruct.create('script', {
                						innerHTML: node.innerHTML,
                						type: 'text/javascript'
                					}, node, 'replace');
                				});
						    });
							if(!response.exists_with_results) {
								require(['dojo/ready', 'dojo/dom-construct'], function(ready, domConstruct){
									ready(function(){
						                if (document.getElementById('segment_searches')) {
										    domConstruct.destroy('facette_wrapper');
						                } else {
						                    domConstruct.destroy('facette');
						                }
						    
									});
								});
							}
							if(response.map_location) {
								if(document.getElementById('map_location_search')) {
									document.getElementById('map_location_search').innerHTML=response.map_location;
									if(typeof(dojo) == 'object'){
										dojo.require('dojo.parser');
										dojo.parser.parse(document.getElementById('map_location_search'));
									}
								}
							}
						});
					</script>
				</div>";
		}
		return $ajax_facettes;
	}
	
	public static function make_facette($objects_ids) {
		global $opac_facettes_ajax, $opac_map_activate;
		
		$return = "";
		$class_name = static::class;
		$facettes = new $class_name($objects_ids);

		if (!$opac_facettes_ajax && ($opac_map_activate == 1 || $opac_map_activate == 3)) {
			$return .= "<div id='map_location_search'>" . $facettes->get_map_location() . "</div>";
		}
		if ($facettes->exists_with_results || count($facettes->get_clicked())) {
			$return .= static::get_facette_wrapper();
			$return .= $facettes->create_ajax_table_facettes();
		} else {
			$return .= self::destroy_dom_node();
		}
		return $return;
	}
	
	public function get_expls_location($notices_ids) {
		global $opac_show_exemplaires;

		if(!$notices_ids) {
			return array();
		}
		if($opac_show_exemplaires){
			$query = "SELECT DISTINCT id_location, id_notice from ( " . $this->get_query_expl($notices_ids) . " UNION " . $this->get_query_explnum($notices_ids) . " ) as sub";
		} else {
			$query = $this->get_query_explnum($notices_ids);
		}
		$expls_location = array(
				'ids' => array(),
				'notices_number' => array()
		);
		$result = pmb_mysql_query($query);
		if ($result && pmb_mysql_num_rows($result)) {			
			while ($row = pmb_mysql_fetch_object($result)) {
				$expls_location['ids'][] = $row->id_location;
				if(!isset($expls_location['notices_number'][$row->id_location])) {
					$expls_location['notices_number'][$row->id_location] = 0;
				}
				$expls_location['notices_number'][$row->id_location] ++;
			}
			$expls_location['ids'] = array_unique($expls_location['ids']);   
		}
		return $expls_location;
	}
        
	public function get_map_location() {
		global $opac_url_base,$opac_map_activate;
		global $opac_sur_location_activate;
		global $msg;

		if(!$opac_map_activate || $opac_map_activate==2 || !strlen($_SESSION["tab_result"])) {
			return '';
		}
		$expls_location = $this->get_expls_location($_SESSION["tab_result"]);
		if (count($expls_location['ids']) > 1) {
			$surlocations_ids = array();
			$query = "SELECT DISTINCT idlocation, location_libelle, surloc_num, surloc_libelle
				FROM docs_location
				LEFT JOIN sur_location on surloc_id=surloc_num
				WHERE idlocation IN ( \"" . implode('","',$expls_location['ids']) . "\")";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$tab_locations = array();
				$tab_surlocations = array();
				$location_ids = array();
				$i = 0;
				while ($row = pmb_mysql_fetch_object($result)) {
					$location_ids[] = $row->idlocation;
					$tab_locations[$i]["id"] = $row->idlocation;
					$tab_locations[$i]['libelle'] = $row->location_libelle;
					$tab_locations[$i]['name'] = $msg['extexpl_location'];
					$tab_locations[$i]['notices_number'] = $expls_location['notices_number'][$row->idlocation];
					$tab_locations[$i]['surloc_num'] = $row->surloc_num;
					$tab_locations[$i]['code_champ'] = 90;
					$tab_locations[$i]['code_ss_champ'] = 4;
					$tab_locations[$i]['url'] = static::format_url('lvl=more_results&mode=extended&facette_test=1');
					$tab_locations[$i]['param'] = '&check_facette[]=["' . $tab_locations[$i]['name'] . '","' . $tab_locations[$i]['libelle'] . '",90,4,"",0]';
					$i++;
					if($row->surloc_num && !array_key_exists($row->surloc_num,$tab_surlocations) && $opac_sur_location_activate) {
						$surlocations_ids[] = $row->surloc_num;
						$tab_surlocations[$row->surloc_num]["id"] = $row->surloc_num;
						$tab_surlocations[$row->surloc_num]['libelle'] = $row->surloc_libelle;
						$tab_surlocations[$row->surloc_num]['name'] = $msg['extexpl_surlocation'];
						$tab_surlocations[$row->surloc_num]['code_champ'] = 90;
						$tab_surlocations[$row->surloc_num]['code_ss_champ'] = 9;
						$tab_surlocations[$row->surloc_num]['url'] = static::format_url('lvl=more_results&mode=extended&facette_test=1');
						$tab_surlocations[$row->surloc_num]['param'] = '&check_facette[]=["' . $tab_surlocations[$row->surloc_num]['name'] . '","' . $tab_surlocations[$row->surloc_num]['libelle'] . '",90,9,"",0]';
					}
					if($row->surloc_num && $opac_sur_location_activate) {
					    if (!isset($tab_surlocations[$row->surloc_num]['notices_number'])) $tab_surlocations[$row->surloc_num]['notices_number'] = 0;
						$tab_surlocations[$row->surloc_num]['notices_number'] += $expls_location['notices_number'][$row->idlocation];
					}
				}
				if (count($surlocations_ids) <= 1) {
					return map_facette_controler::get_map_facette_location($location_ids,$tab_locations, array(),array());
				} else {
					$tab_locations_without_surloc = array();
					$ids_loc_without_surloc = array();
					foreach ($tab_locations as $location) {
						if (!$location['surloc_num']) {
							$ids_loc_without_surloc[] = $location["id"];
							$tab_locations_without_surloc[] = $location;
						}
					}					
					return map_facette_controler::get_map_facette_location($ids_loc_without_surloc,$tab_locations_without_surloc, $surlocations_ids, $tab_surlocations);
				}
			}
		} else {
			return '';
		}
	}
	

	public static function make_ajax_facette($objects_ids){
	    $class_name = static::class;
		$facettes = new $class_name($objects_ids);
		
		$facettes_exists_with_or_without_results = false;
		if($facettes->exists_with_results || count($facettes->get_clicked())){
			$facettes_exists_with_or_without_results = true;
		}
		return array(
			'exists_with_results' => ($_SESSION["cms_build_activate"] ? true : $facettes_exists_with_or_without_results),
			'display' => $facettes->create_ajax_table_facettes(),
			'map_location' =>  $facettes->get_map_location()
		);
	}

	public static function get_facette_wrapper(){
		global $base_path, $msg;
		$script ="
		<script src='".$base_path."/includes/javascript/select.js' type='text/javascript'></script>
		<script type='text/javascript'> 		
			function test(elmt_id){
				var elmt_list=document.getElementById(elmt_id);
				for(i in elmt_list.rows){
					if(elmt_list.rows[i].firstElementChild && elmt_list.rows[i].firstElementChild.nodeName!='TH'){
						if(elmt_list.rows[i].style.display == 'none'){
							elmt_list.rows[i].style.display = 'block';
						}else{
							elmt_list.rows[i].style.display = 'none';
						}
					}
				}
			}
			
			function facette_see_more(id,json_facette_plus){
				
				var myTable = document.getElementById('facette_list_'+id);
				
				if (json_facette_plus == null) {
					var childs = myTable.childNodes;
					var nb_childs = childs.length;
					
					for(var i = 0; i < nb_childs; i++){
						if (childs[i].getAttribute('facette_ajax_loaded')!=null) {
							if (childs[i].getAttribute('style')=='display:block') {
								childs[i].setAttribute('style','display:none');
								childs[i].setAttribute('expanded','false');
							} else {
								childs[i].setAttribute('style','display:block');
								childs[i].setAttribute('expanded','true');
							}
						}
					}
				
					var see_more_less = document.getElementById('facette_see_more_less_'+id);
					see_more_less.innerHTML='';
					var span = document.createElement('span');
					if (see_more_less.getAttribute('etat')=='plus') {
						span.className='facette_moins_link';
						span.innerHTML='".$msg["facette_moins_link"]."';
						see_more_less.setAttribute('etat','moins');
					} else {
						span.className='facette_plus_link';
						span.innerHTML='".$msg["facette_plus_link"]."';
						see_more_less.setAttribute('etat','plus');		
					}
					see_more_less.appendChild(span);
					
				} else {
					var req = new http_request();
					var sended_datas={'json_facette_plus':json_facette_plus};
					req.request(\"./ajax.php?module=ajax&categ=".static::$table_name."&sub=see_more\",true,'sended_datas='+encodeURIComponent(JSON.stringify(sended_datas)),true,function(data){
						
						var jsonArray = JSON.parse(data);
						//on supprime la ligne '+'
						myTable.tBodies[0].removeChild(myTable.rows[myTable.rows.length-1]);
						//on ajoute les lignes au tableau
						for(var i=0;i<jsonArray.length;i++) {
							var tr = document.createElement('tr');
							tr.setAttribute('style','display:block');
							tr.setAttribute('expanded','true');
							tr.setAttribute('facette_ajax_loaded','1');
				        	var td = tr.appendChild(document.createElement('td'));
				        	td.innerHTML = \"<span class='facette_coche'><input type='checkbox' name='check_facette[]' value='\" + jsonArray[i]['facette_value'] + \"'></span>\";
				        	var td2 = tr.appendChild(document.createElement('td'));
				        	td2.innerHTML = \"<a class='facette_link' onclick='\" + jsonArray[i]['facette_link'] + \"' style='cursor:pointer;'>\"
												+ \"<span class='facette_libelle'>\" + jsonArray[i]['facette_libelle'] + \"</span> \"
												+ \"<span class='facette_number'>[\" + jsonArray[i]['facette_number'] + \"]</span>\"
												+ \"</a>\";
				        	myTable.appendChild(tr);
	
						}
						//Ajout du see_less
						var tr = document.createElement('tr');
						tr.setAttribute('style','display:block');
						tr.setAttribute('see_less','1');
						tr.setAttribute('class','facette_tr_see_more');
							
						var td = tr.appendChild(document.createElement('td'));
						td.setAttribute('colspan','3');
							
						var ahref = td.appendChild(document.createElement('a'));
						ahref.setAttribute('id','facette_see_more_less_'+id);
						ahref.setAttribute('etat','moins');
						ahref.setAttribute('onclick','javascript:facette_see_more(' + id + ',null);');
						ahref.setAttribute('style','cursor:pointer');
						ahref.innerHTML='';
							
						var span = document.createElement('span');
						span.className='facette_moins_link';
						span.innerHTML='".$msg["facette_moins_link"]."';								
						ahref.appendChild(span);
								
						myTable.appendChild(tr);
					});
				}
			}";
			if(static::get_compare_notice_active()){
				$compare_class_name = static::$compare_class_name;
				$script .= $compare_class_name::get_compare_wrapper();
			}
		$script.="</script>";
		return $script;	
	}
	
	public static function checked_facette_search(){
		global $param_delete_facette;
	
		$session_values = static::get_session_values();
		if (!is_array($session_values)){
			$session_values = array();
		}
		//Suppression facette
		if($param_delete_facette!=""){
			//On évite le rafraichissement de la page
			static::delete_session_value($param_delete_facette);
		} else {
			$tmpArray = array();
			$check_facette = static::get_checked();
			foreach ($check_facette as $key => $facet_values) {
				$ajout=true;
				if (count($tmpArray)) {
					foreach ($tmpArray as $prev_key => $prev_values) {
					    //On test le champ et le sous champ
						if (($prev_values[2] == $facet_values[2]) && ($prev_values[3] == $facet_values[3])) {
							$tmpArray[$prev_key][1][] = $facet_values[1];
							$ajout=false;
							break;
						}
					}
				}
				if ($ajout) {
					$tmpItem = array();
					$tmpItem[0] = $facet_values[0];
					$tmpItem[1] = array($facet_values[1]);
					$tmpItem[2] = $facet_values[2];
					$tmpItem[3] = $facet_values[3];
					$tmpArray[] = $tmpItem;
				}
			}
			//ajout facette : on vérifie qu'elle n'est pas déjà en session (rafraichissement page)
			$trouve = false;
			if (count($session_values)) {
				foreach ($session_values as $k=>$v) {
					if ($tmpArray == $v) {
						$trouve = true;
						break;
					}
				}
			}
			if (!$trouve && count($tmpArray)) {
				$session_values[] = $tmpArray;
			}
			static::set_session_values($session_values);
		}
		static::make_facette_search_env();
	}
	
	public static function get_nb_result_groupby($facettes){
		$nb_result=0;
		foreach($facettes as $facette){
			$nb_result+=$facette['nb_result'];
		}
		return $nb_result;
	}
	
	public function get_clicked() {
		if(!isset($this->clicked)) {
			$session_values = static::get_session_values();
			if(is_array($session_values)) {
				$this->clicked = $session_values;
			} else {
				$this->clicked = array();
			}
		}
		return $this->clicked;
	}
	
	public function get_not_clicked() {
		$this->not_clicked = array();
		$this->facette_plus = array();
		foreach ($this->tab_facettes as $keyFacette=>$vTabFacette) {
			$affiche = true;
			foreach ($vTabFacette['value'] as $keyValue=>$vLibelle) {
// 				$clicked = false;
// 				foreach ($this->get_clicked() as $vSessionFacette) {
// 					foreach ($vSessionFacette as $vDetail) {
// 						if (($vDetail[2]==$vTabFacette['code_champ']) && ($vDetail[3]==$vTabFacette['code_ss_champ']) && (in_array($vLibelle,$vDetail[1]))) {
// 							$clicked = true;
// 							break;
// 						}
// 					}
// 				}
// 				if (!$clicked) {
					$key = $vTabFacette['name']."_".$this->facettes[$keyFacette]['id'];
					if ($vTabFacette['size_to_display'] == '-1') {
						$this->not_clicked[$key][]=array('see_more' => true);
						$affiche = false;
					} elseif ($vTabFacette['size_to_display']!='0') {
						if (isset($this->not_clicked[$key]) && count($this->not_clicked[$key])>=$vTabFacette['size_to_display']) {
							$this->not_clicked[$key][]=array('see_more' => true);
							$affiche = false;
						}
					}
					if ($affiche) {
						$this->not_clicked[$key][]=array(
								'libelle' => $vLibelle,
								'code_champ' => $vTabFacette['code_champ'],
								'code_ss_champ' => $vTabFacette['code_ss_champ'],
								'nb_result' => $vTabFacette['nb_result'][$keyValue]
						);
					} else {
						$this->facette_plus[$this->facettes[$keyFacette]['id']]['facette'][]=$vLibelle." "."(".$vTabFacette['nb_result'][$keyValue].")";
						$this->facette_plus[$this->facettes[$keyFacette]['id']]['value'][]=$vLibelle;
						$this->facette_plus[$this->facettes[$keyFacette]['id']]['nb_result'][]=$vTabFacette['nb_result'][$keyValue];
						$this->facette_plus[$this->facettes[$keyFacette]['id']]['code_champ']=$vTabFacette['code_champ'];
						$this->facette_plus[$this->facettes[$keyFacette]['id']]['code_ss_champ']=$vTabFacette['code_ss_champ'];
						$this->facette_plus[$this->facettes[$keyFacette]['id']]['name']=$vTabFacette['name'];
						if(static::get_compare_notice_active()){
							$id=facette_search_compare::gen_compare_id($vTabFacette['name'],$vLibelle,$vTabFacette['code_champ'],$vTabFacette['code_ss_champ'],$vTabFacette['nb_result'][$keyValue]);
							$facette_compare=$this->get_facette_search_compare();
							if(isset($facette_compare->facette_compare[$id]) && $facette_compare->facette_compare[$id]){
								$facette_compare->set_available_compare($id,true);
							}
						}
					}
// 				}
			}
		}
		return $this->not_clicked;
	}
	
	public function get_facette_plus() {
		return $this->facette_plus;
	}
	
	protected function get_display_clicked() {
	    global $msg, $charset;
		
		$display_clicked = "<table id='active_facette'>";
		$n = 0;
		foreach ($this->clicked as $k=>$v) {
			($n % 2)?$pair_impair="odd":$pair_impair="even";
			$n++;
			$display_clicked .= "
						<tr class='".$pair_impair."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\">
							<td>";
			$tmp=0;
			foreach($v as $vDetail){
				foreach($vDetail[1] as $vDetailLib){
					if($tmp){
						$display_clicked .= "<br>";
					}
					$display_clicked .= htmlentities($vDetail[0]." : ".static::get_formatted_value($vDetail[2], $vDetail[3], $vDetailLib),ENT_QUOTES,$charset);
					$tmp++;
				}
			}
			$display_clicked .= "
							</td>
							<td>
								<a onclick='".static::get_link_delete_clicked($k, count($this->clicked))."' style='cursor:pointer'>
									<img src='".get_url_icon('cross.png')."' alt='".$msg["facette_delete_one"]."'/>
								</a>
							</td>
						</tr>";
		}
		$display_clicked .= "
					</table>
					<div class='reinitialize-facettes'>
						<a onclick='".static::get_link_reinit_facettes()."' style='cursor:pointer' title='".$msg['facette_reset_all']."' class='reinitialize-facettes-link right'>".$msg['reset']." <i alt=\"\" class='fa fa-undo' aria-hidden='true'></i></a>
					</div>";
		return $display_clicked;
	}
	
	protected function get_display_not_clicked() {
		global $charset;
		global $msg;
		
		$display_not_clicked = '';
		if(is_array($this->not_clicked) && count($this->not_clicked)) {
			if(static::get_compare_notice_active()){
				$facette_compare=$this->get_facette_search_compare();
			}
			$display_not_clicked .= "<input class='bouton bouton_filtrer_facette_haut filter_button' type='button' value='".htmlentities($msg["facette_filtre"],ENT_QUOTES,$charset)."' name='filtre2' ".$this->get_filter_button_action().">";
			foreach ($this->not_clicked as $tmpName=>$facette) {
				$flagSeeMore = false;
				$tmpArray = explode("_",$tmpName);
				$idfacette = array_pop($tmpArray);
				$name = get_msg_to_display(implode("_",$tmpArray));
			
				$currentFacette=current($facette);
			
				$idGroupBy=facette_search_compare::gen_groupby_id($name,$currentFacette['code_champ'],$currentFacette['code_ss_champ']);
			
				$groupBy=facette_search_compare::gen_groupby($name,$currentFacette['code_champ'],$currentFacette['code_ss_champ'],$idGroupBy);
			
				$display_not_clicked .= "<table id='facette_list_".$idfacette."'>";
				$display_not_clicked .= "<thead>";
				$display_not_clicked .= "<tr>";
				if(static::get_compare_notice_active() && count($facette_compare->facette_compare)){
					$display_not_clicked .= "
							<th style='width=\"90%\' onclick='javascript:test(\"facette_list_".$idfacette."\");' colspan='2'>
								".htmlentities($name,ENT_QUOTES,$charset)."
							</th>";
					$display_not_clicked.=facette_search_compare::get_groupby_row($facette_compare,$groupBy,$idGroupBy);
					if($facette_compare->facette_groupby[$idGroupBy]){
						$facette_compare->set_available_groupby($idGroupBy,true);
					}
				}else{
					$display_not_clicked .= "
							<th onclick='javascript:test(\"facette_list_".$idfacette."\");'>
								".htmlentities($name,ENT_QUOTES,$charset)."
							</th>";
				}
				$display_not_clicked .= "</tr>";
				$display_not_clicked .= "</thead>";
				$display_not_clicked .= "<tbody>";
			
				$j=0;
				foreach ($facette as $detailFacette) {
					if (!isset($detailFacette['see_more'])) {
						$id=facette_search_compare::gen_compare_id($name,$detailFacette['libelle'],$detailFacette['code_champ'],$detailFacette['code_ss_champ'],$detailFacette['nb_result']);
							
						$cacValue = encoding_normalize::json_encode(array($name,$detailFacette['libelle'],$detailFacette['code_champ'],$detailFacette['code_ss_champ'],$id,$detailFacette['nb_result']));
						if(static::get_compare_notice_active()){
							if(!isset($facette_compare->facette_compare[$id]) || !sizeof($facette_compare->facette_compare[$id])){
								$onclick='select_compare_facette(\''.htmlentities($cacValue,ENT_QUOTES,$charset).'\')';
								$img='double_section_arrow_16.png';
							}else{
								$facette_compare->set_available_compare($id,true);
								$onclick='';
								$img='vide.png';
							}
						}
						$link = static::get_link_not_clicked($name, $detailFacette['libelle'], $detailFacette['code_champ'], $detailFacette['code_ss_champ'], $id, $detailFacette['nb_result']);
						$display_not_clicked .= "
								<tr style='display: block;' class='facette_tr'>
									<td class='facette_col_coche'>
										<span class='facette_coche'>
											<input type='checkbox' name='check_facette[]' value='".htmlentities($cacValue,ENT_QUOTES,$charset)."'>
										</span>
									</td>
									<td  class='facette_col_info'>
										<a ".$this->on_facet_click($link)." style='cursor:pointer' rel='nofollow' class='facet-link'>
											<span class='facette_libelle'>
												".htmlentities(static::get_formatted_value($detailFacette['code_champ'], $detailFacette['code_ss_champ'], $detailFacette['libelle']),ENT_QUOTES,$charset)."
											</span>
											<span class='facette_number'>
												[".htmlentities($detailFacette['nb_result'],ENT_QUOTES,$charset)."]
											</span>
										</a>
									</td>
								</tr>";
						$j++;
					} elseif(!$flagSeeMore) {
						$display_not_clicked .= "
								<tr class='facette_tr_see_more'>
									<td colspan='3'>
										<a href='javascript:facette_see_more(".$idfacette.",".encoding_normalize::json_encode($this->facette_plus[$idfacette]).");'><span class='facette_plus_link'>".$msg["facette_plus_link"]."</span></a>
									</td>
								</tr>";
						$flagSeeMore = true;
					}
				}
				$display_not_clicked .= "</tbody>";
				$display_not_clicked .="</table>";
			}
			$display_not_clicked .= "<input type='hidden' value='' id='filtre_compare_facette' name='filtre_compare'>";
			$display_not_clicked .= "<input class='bouton bouton_filtrer_facette_bas filter_button' type='button' value='".htmlentities($msg["facette_filtre"],ENT_QUOTES,$charset)."' name='filtre' ".$this->get_filter_button_action().">";
			if(static::get_compare_notice_active()){
				$display_not_clicked .= "<input class='bouton' type='button' value='".htmlentities($msg["facette_compare"],ENT_QUOTES,$charset)."' name='compare' onClick='valid_facettes_compare()'>";
			}
		}
		return $display_not_clicked;
	}
	
	protected function get_filter_button_action() {
	    return "onClick='valid_facettes_multi()'";
	}
	
	protected function on_facet_click($link = '') {	    
	    return "onclick='".$link."'";
	}
	
	public function create_ajax_table_facettes(){
		global $base_path;
		global $charset;
		global $mode;
		global $msg;
		
		if(static::get_compare_notice_active()){
			$facette_compare=$this->get_facette_search_compare();
		}
		
		$table = "<form name='facettes_multi' class='facettes_multis' method='POST' action='".$this->get_action_form()."'>";
		if(count($this->get_clicked())){
			$table .= "<h3>".htmlentities($msg['facette_active'],ENT_QUOTES,$charset)."</h3>".$this->get_display_clicked()."<br/>";
		}
		
		if(static::get_compare_notice_active()){
			//Le tableau des critères de comparaisons
			if(count($facette_compare->facette_compare)){
				$table_compare=$facette_compare->gen_table_compare();
				
				$table .= "<h3 class='facette_compare_MainTitle'><table><tr><td style='width:90%;'>".htmlentities($msg['facette_list_compare_crit'],ENT_QUOTES,$charset)."</td>
				<td><a onclick='".static::get_link_back(true)."' class='facette_compare_raz'><img alt='".htmlentities($msg['facette_compare_reinit'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['facette_compare_reinit'],ENT_QUOTES,$charset)."' width='18px' height='18px' src='".get_url_icon('cross.png')."'/></a></td></tr></table>
				</h3><table id='facette_compare'>".$table_compare."</table><br/>";
				
				//Le tableau des critères de comparaisons
				if(count($facette_compare->facette_groupby)){
					$table_groupby=$facette_compare->gen_table_groupby();
				}
				$table .= "<h3 class='facette_compare_SubTitle'><img id='facette_compare_not_clickable' src='".get_url_icon('group_by.png')."'/> ".htmlentities($msg['facette_list_groupby_crit'],ENT_QUOTES,$charset)."</h3><table id='facette_groupby'>".$table_groupby."</table><br/>";
			}
			
			//le bouton de retour
			if(isset($_SESSION['filtre_compare']) && $_SESSION['filtre_compare']=='compare'){
				$table .= "<input type='button' class='bouton backToResults' value='".htmlentities($msg['facette_compare_search_result'],ENT_QUOTES,$charset)."' onclick='".static::get_link_back()."'/><br /><br />";
			}elseif((!isset($_SESSION['filtre_compare']) || $_SESSION['filtre_compare']!='compare') && count($facette_compare->facette_compare)){
				$table .= "<input type='button' class='bouton' value='".htmlentities($msg['facette_compare_search_compare'],ENT_QUOTES,$charset)."' onclick='valid_compare();'/><br /><br />";
			}
		}
		
		if(count($this->get_not_clicked())){
			if(static::get_compare_notice_active()){
				$table .= "<div id='facettes_help'></div>";
				$table .= "<h3 class='facette_compare_listTitle'>".htmlentities($msg['facette_list_compare'],ENT_QUOTES,$charset)." &nbsp;<img onclick='open_popup(document.getElementById(\"facettes_help\"),\"".htmlentities($msg['facette_compare_helper_message'],ENT_QUOTES,$charset)."\");' height='18px' width='18px' title='".htmlentities($msg['facette_compare_helper'],ENT_QUOTES,$charset)."' alt='".htmlentities($msg['facette_compare_helper'],ENT_QUOTES,$charset)."' src='".get_url_icon('quest.png')."'/></h3>".$this->get_display_not_clicked()."<br/>";
			}else{
				$table .= "<h3 class='facette_compare_listTitle'>".htmlentities($msg['facette_list'],ENT_QUOTES,$charset)."</h3>".$this->get_display_not_clicked()."<br/>";
			}
		}
		$table .= "</form>";
		return $table;
	}
	
	protected function get_action_form() {
	    return static::format_url("lvl=more_results&mode=".$this->mode."&facette_test=1");
	}
	
	public static function session_filtre_compare(){
		global $filtre_compare;
		
		$_SESSION['filtre_compare']=$filtre_compare;
	}
	
	public static function get_checked() {
		global $charset;
		global $name;
		global $value;
		global $champ;
		global $ss_champ;
		global $check_facette;		
		//si rien en multi-sélection, il n'y a qu'une seule facette de cliquée
		//on l'ajoute au tableau pour avoir un traitement unique après
		if(!isset($check_facette) || !count($check_facette)){
			$check_facette = array();
			if (!empty($name) && isset($value)) {
			    $check_facette[] = array(stripslashes($name),stripslashes($value),$champ,$ss_champ);
			}
		}else{
			//le tableau est addslashé automatiquement
			foreach($check_facette as $k=>$v){
				$check_facette[$k]=json_decode(stripslashes($v));
				//json_encode/decode ne fonctionne qu'avec des données utf-8
				if ($charset!='utf-8') {
					foreach($check_facette[$k] as $key=>$value){
						$check_facette[$k][$key]=utf8_decode($check_facette[$k][$key]);
					}
				}
			}
		}
		return $check_facette;
	}
	
	public function get_facette_search_compare() {
		if(!isset($this->facette_search_compare)) {
			$this->facette_search_compare = new facette_search_compare();
		}
		return $this->facette_search_compare;
	}
	
	public function get_json_datas() {
		$datas = array(
				'clicked' => $this->get_clicked(),
				'not_clicked' => $this->get_not_clicked(),
				'facette_plus' => $this->get_facette_plus()
		);
		return encoding_normalize::json_encode($datas);
	}
	
	public static function get_compare_notice_active() {
		if(!isset(static::$compare_notice_active)) {
			global $opac_compare_notice_active;
			static::$compare_notice_active = $opac_compare_notice_active*1;
		}
		return static::$compare_notice_active;
	}
	
	public static function set_url_base($url_base) {
		static::$url_base = $url_base;
	}
	
	protected static function format_url($url) {
		global $base_path;
		
		if(!isset(static::$url_base)) {
			static::$url_base = $base_path.'/index.php?';
		}
		if(strpos(static::$url_base, "lvl=search_segment")) {
			return static::$url_base.str_replace('lvl', '&action', $url);
		} else {
			return static::$url_base.$url;
		}
	}
	
	public static function set_facet_type($type) {
	    static::$facet_type = $type;
	}
}// end class
