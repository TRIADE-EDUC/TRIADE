<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_sort.class.php,v 1.17.2.1 2024/09/10 13:17:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path.'/templates/search_universes/search_segment_sort.tpl.php');
require_once "$class_path/fields/sort_fields.class.php";

class search_segment_sort {
	
	protected $num_segment;
	
	protected $human_query;
	
	protected $sort;
	
	protected $type;
	
	protected $table_tempo;
	
	protected $sort_fields;
	
	protected $principal_fields;
	
	protected $pperso_fields;
	
	protected $default_sort;
		
	public function __construct($num_segment = 0){
		$this->num_segment = intval($num_segment);
		$this->fetch_data();
	}
	
	protected function fetch_data() {
	    $this->type = '';
		if ($this->num_segment) {
			$query = '
			    SELECT search_segment_sort, search_segment_type
			    FROM search_segments 
			    WHERE id_search_segment = "'.$this->num_segment.'"
			';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_assoc($result);
				$this->sort = $this->parse_sort(stripslashes($row['search_segment_sort']));
				$this->type = $row['search_segment_type'];
			}
		}
	}
	
	/*
	 * Permet de récupérer le tri par défaut dans la chaine de tris du segment et de l'y retirer
	 */
	protected function parse_sort($sort_string){
	    $sort_string = translation::get_translated_text($this->num_segment, 'search_segments', 'segment_sort', $sort_string);
	    $sort_array = explode("||", $sort_string);
	    $this->default_sort = 0;
	    foreach ($sort_array as $key=>$sort) {
	        if (is_numeric(trim($sort))) {
	            $this->default_sort = trim($sort);
	            unset($sort_array[$key]);
	        }
	    }
	    return implode("||", $sort_array);
	}

	public function get_sort() {
	    return $this->sort;
	}

	public function get_human_query() {
	    if (isset($this->human_query)) {
	        return $this->human_query;
	    }
	    if (empty($this->sort)) {
	        return '';
	    }
	    $this->get_sort_fields();
	    $fields = encoding_normalize::json_decode($this->sort, true);
	    $this->human_query = $this->sort_fields->get_human_query($fields);
	    return $this->human_query;
	}
	
	public function get_form() {
	    global $search_segment_sort_form, $msg, $charset;
	    
	    $languages = translation::get_languages();
	    $i = 0;
	    $select_fields = '<table id="sort_table"><tbody><tr><th style="width: 15%;">'.$msg['segment_sort_label_default_sort'].'</th><th style="width: 65%;">'.$msg['nom_tri'].'</th><th style="width: 10%;">'.$msg['segment_sort_label_edit_sort'].'</th><th style="width: 10%;">'.$msg['63'].'</th></tr>';
	    $sorts = explode('||', $this->get_sort());
	    foreach ($sorts as $sort) {
	        $exploded_sort = explode('|', $sort);
            $sort_name = '';
	        if (!empty($exploded_sort[1])) {
	            $sort_name = trim($exploded_sort[1]);
	        }
	        
            // Si pas de tri définit on met a la place un message et on envoi des valeurs par défaut
	        $display = "";
	        if ($sorts[0] === "" || strpos ($sorts[0] ,"segment_sort_name_default")) {
	            $select_fields .= $this->get_segment_sort_default();
                $display = "none";
	        }
	        $class_name = "odd";
	        if ($i%2==0) {
	            $class_name = "even";
	        }
	        $edit_pic = get_url_icon('b_edit.png');
	        $sort_list = trim($exploded_sort[0]);
	        $select_fields .= "<tr class=\"$class_name\" id=\"sort_container_$i\" style='display:$display' >";
	        $select_fields .= "<td><input id='default_sort_$i' type='radio' name='default_sort_radio' value='$i' ".($this->default_sort==$i?'checked':'')."  /></td>";
	        $select_fields .= "<td>";
	        $select_fields .= "<label id='segment_sort_label_$i'> " . clean_string($sort_name) ." </label>";
	        $select_fields .= "<input id='segment_sort_name_$i' name='segment_sort_name_$i' class='saisie-30em' type='hidden' value='" . htmlentities(clean_string($sort_name), ENT_QUOTES, $charset) . "' readonly=''/>";
	        $select_fields .= "<input id='segment_sort_list_$i' type='hidden' name='sort_list_$i' value='$sort_list'/>";
	        if (!empty($languages)) {
	            foreach ($languages as $language) {
	                if (empty($language['is_current_lang'])) {
	                    $translated_sort_name = $this->get_translated_sort($sort_name, $i, $language['code']);
	                    if($translated_sort_name == 'segment_sort_name_default') {
	                        $translated_sort_name = '';
	                    }
	                    $select_fields .= "<input id='".$language['code']."_segment_sort_name_$i' name='".$language['code']."_segment_sort_name_$i' type='hidden' value='" . htmlentities(clean_string($translated_sort_name), ENT_QUOTES, $charset) . "' />";
	                }
	            }
	        }
	        $select_fields .= "</td>";
	        $select_fields .= "<td>";
	        $display = ($display == "" ? "block":"none");
	        $select_fields .= "<a id='edit_segment_sort_$i' alt=\"".htmlentities($msg['modif_tri'],ENT_QUOTES,$charset)."\" title=\"".htmlentities($msg['modif_tri'],ENT_QUOTES,$charset)."\" style=\"cursor: pointer; display:$display; \" onclick='editSort($i)'>";
	        $select_fields .= "<img src='".$edit_pic."'  />"; 
	        $select_fields .= "</a>";
	        $select_fields .= "</td>";
	        $select_fields .= "<td>";
	        $select_fields .= "<input id='segment_sort_delete_$i' class='bouton' type='button' value='X' onclick='deleteSort($i)' />";
	        $select_fields .= "</td>";
	        
	        $select_fields .= "</tr>";
	        $i++;
	    }
	    
	    $select_fields .= "</tbody></table>";
	    $select_fields .= "<input id='segment_sort_add_button' class='bouton' type='button' value='Ajouter un tri' onclick='add_sort_field(".($i-1).")' />";
	    $select_fields .= "<input id='nb_segment_sort' type='hidden' name='nb_segment_sort' value='$i'/>";
	    $select_fields .= "<input id='sort_string' type='hidden' name='sort_string' value=''/>";
	    $search_segment_sort_form = str_replace('!!segment_sort_select_fields!!', $select_fields, $search_segment_sort_form);
	    $search_segment_sort_form = str_replace('!!segment_sort_fields_javascript!!', $this->get_sort_fields_javascript(), $search_segment_sort_form);

	    return $search_segment_sort_form;
	}
		
	public function set_properties_from_form(){
	    $this->get_sort_fields();
	    $this->sort = encoding_normalize::json_encode($this->sort_fields->format_fields());
	    $this->human_query = $this->get_human_query();
	}
	
	public function get_sort_from_form($language='') {
	    $sort = '';
	    $i = 0;
	    global $nb_segment_sort, $default_sort_radio;
        for ($i = 0; $i < $nb_segment_sort; $i++) {
            if (!empty($language)) {
                global ${$language.'_segment_sort_name_'.$i};
                $temp_sort_name = ${$language.'_segment_sort_name_'.$i};
            } else {
                global ${'segment_sort_name_'.$i};
                $temp_sort_name = ${'segment_sort_name_'.$i};
            }
            global ${'sort_list_'.$i};
            $temp_sort_list = ${'sort_list_'.$i};

            $sort_list='';
            if ($temp_sort_name === "segment_sort_name_default" || empty($temp_sort_name)) {
                continue;
            }
            //Si on a déjà un tri, on les sépare par un double pipe
            if (!empty($sort)) {
                $sort .= ' || ';
            }
            if (isset($temp_sort_list) && !empty($temp_sort_list)) {
                //on recompose la chaine de caratères pour la table de segment_search en base
                $sort_list = $temp_sort_list;
                $sort .= $sort_list . '|' . $temp_sort_name;
            } else {
                //Si on a pas de tri défini on utilise le tri par défaut
                $sort .= $this->get_default_sort($temp_sort_name);
            }
        }
        
	    if (empty($sort)) {
	        //si aucun tri n'est défini on récupère le tri par défaut du type de segment
	        $sort = $this->get_default_sort();
	    }
	    if (isset($default_sort_radio)) {
	        $sort .= "||".$default_sort_radio;
	    }
	    return $sort;
	}
	
	private function get_default_sort($sort_name=null) {
	    $default_sort_name = 'segment_sort_name_default';
	    if ($sort_name) {
	        $default_sort_name = $sort_name;
	    }
	    if (TYPE_NOTICE == $this->type) {
	        $sort = "d_num_6 | $default_sort_name";
	    } elseif (TYPE_EXTERNAL == $this->type){
	        $sort = '';
	    } else {
	        $sort = "d_num_1 | $default_sort_name";
	    }
	    return $sort;
	}
	
	public function update() {
	    if (!$this->num_segment ) {
	        return false;
	    }
		$query = '
		    UPDATE search_segments 
		    SET search_segment_sort = "'.addslashes($this->sort).'"
		    WHERE id_search_segment = "'.$this->num_segment.'"';
		return pmb_mysql_query($query);
	}
	
	public function delete_sort(){
	    $this->sort = "";
	    $this->human_query = "";
	}
	
	private function get_sort_fields() {
	    if (!isset($this->sort_fields)) {
	        $this->sort_fields = new sort_fields($this->get_indexation_type(), $this->get_indexation_path());
	    }
	    return $this->sort_fields;
	}
	
	private function get_indexation_path() {
	    global $include_path;
	    $string_type = entities::get_string_from_const_type($this->type);
	    switch ($string_type) {
	        case 'ontology' :
	            break;
	        case 'notices' :
	            return $include_path."/indexation/notices/champs_base.xml";
	        default :
	            return $include_path."/indexation/authorities/$string_type/champs_base.xml";
	    }
	}
	
	private function get_indexation_type() {
	    switch ($this->type) {
	        case TYPE_NOTICE :
	            return "notices";
	        case TYPE_EXTERNAL :
	            return "notices_externes";
	        default :
	            return "authorities";
	    }
	}
	
	private function get_sub_type() {
	    return entities::get_aut_table_from_type($this->type);
	}
	
	public function sort_data($data, $offset = 0, $limit = 0, $query_searcher = '') {
	    $query = $this->appliquer_tri($this->num_segment,$query_searcher,$this->params['REFERENCEKEY'],$offset,$limit);
	    $res = pmb_mysql_query($query);
	    if($res && pmb_mysql_num_rows($res)){
	        $this->result=array();
	        while($row = pmb_mysql_fetch_object($res)){
        	    $this->result[] = $row->{ $this->params["REFERENCEKEY"] };
	        }
	    }	
	    return $this->result;
	}
	
	public function add_session_currentSegment($id){
	    $_SESSION['sort_segment_'.$this->num_segment.'currentSort'] = $id;   
	    return true;
	}

	public function show_tris_selector_segment() {
        global $search_index, $msg;
        
        if (!empty($_SESSION['sort_segment_'.$this->num_segment.'currentSort'])){
            $sort_seg = $_SESSION['sort_segment_'.$this->num_segment.'currentSort'];
        } else {
            $sort_seg = 0;
        }
	    $sorts = array();
	    $sorts = explode('||',$this->sort);
        $html = '<label for="segment_sort">' . $msg['list_applied_sort'] . '</label>
                 <select onChange=applySort(this.options[this.selectedIndex].value) name="segment_sort" id="segment_sort">';
        foreach ($sorts as $sort_id => $sort){
            if (!empty(explode('|',$sort)[1])){
                $sort_name = explode('|',$sort)[1];
            } else {
                $sort_name = '';
            }
            $html .= '<option  value="'.$sort_id.'"'.  (($sort_seg == $sort_id) ? " selected" : "").'" >'.$sort_name.'</option>';
        }
	    $html .= "</select></span>
            <script>
            function applySort(value){
                dojo.xhrPost({
					url : './ajax.php?module=ajax&categ=search_segment&action=add_session_currentSegment&num_segment=".$this->num_segment."&segment_sort='+value,
				});	                
                document.location = 'index.php?lvl=search_segment&id=".$this->num_segment."&search_index=".$search_index."&segment_sort='+value;
            }

            </script><span class=\"espaceResultSearch\">&nbsp;</span>";
	    return $html; 
	}
	
	/**
	 * Ajoute les tris par défaut éventuellement saisis en paramètre
	 */
	public function add_default_sort(){
	    if ($this->sort) {
	        if (empty($_SESSION['sort_segment_'.$this->num_segment.'_list']) || $_SESSION['sort_segment_'.$this->num_segment.'_list'] != $this->sort) {
	            $_SESSION['sort_segment_'.$this->num_segment.'_list'] = $this->sort;
	            $_SESSION['sort_segment_'.$this->num_segment.'flag'] = 0;
	        }
	        //on vérifie l'existence d'un flag : que la recherche par défaut ne revienne pas si l'utilisateur l'a supprimée par le formulaire
	        if(empty($_SESSION['sort_segment_'.$this->num_segment.'flag'])){
	            $tmpArray = explode("||",$this->sort);
	            foreach($tmpArray as $tmpElement){
	                if(trim($tmpElement)){
	                    if (strstr($tmpElement,'|')) {
	                        $tmpSort=explode("|",$tmpElement);
	                        $this->add_session_sort($tmpSort[0],$tmpSort[1]);
	                    } else {
	                        $this->add_session_sort($tmpElement);
	                    }
	                }
	            }
	            $_SESSION['sort_segment_'.$this->num_segment.'flag']=1;
	        }
	    }
	}

	private function add_session_sort($sortDes, $sortName =''){
	    global $charset;
	    $_SESSION["sort_segment_".$this->num_segment][]= [
	        "name" => htmlentities($sortName,ENT_QUOTES,$charset),
	        "des"  => htmlentities($sortDes,ENT_QUOTES,$charset)  
	    ];
	}
	
	public function get_select_fields_sort($sort, $i) {
	    global $msg, $charset;
	    
	    $sort_principal_fields = '';
	    $sort_pperso = '';
	    
	    $field_id = 0;
	    if (isset(explode('_', $sort)[2])) {
    	    $field_id = clean_string(explode('_', $sort)[2]);
	    }
	    
	    $type = $this->type;
	    if ($this->type > 1000) {
	        $type = TYPE_AUTHPERSO;
	    }
	    
	    $this->get_principal_fields(entities::get_string_from_const_type($type));
	    if (!empty($this->principal_fields)) {
	        $sort_principal_fields .= "<optgroup label='" . $msg['champs_principaux_query'] . "'>";
	        foreach ($this->principal_fields['FIELD'] as $field) {
	            $selected = ($field['ID'] == $field_id ? 'selected' : '');
	            $label = (isset($msg[$field['NAME']]) ? $msg[$field['NAME']] : '');
	            $sort_principal_fields .= "<option value='" . $field['TYPE'] . "_" . $field['ID'] . "' $selected>" . htmlentities($label, ENT_QUOTES, $charset) . "</option>";
	        }
	        $sort_principal_fields .= "</optgroup>";
	    }
	    
	    $this->get_pperso_fields(parametres_perso::get_pperso_prefix_from_type($type));
	    if (!empty($this->pperso_fields->t_fields)) {
	        $options = '';
	        foreach ($this->pperso_fields->t_fields as $id => $field) {
	            if (!empty($field['OPAC_SORT'])) {
        	        $selected = ("cp$id" == $field_id ? 'selected' : '');
        	        $value = $field['OPTIONS'][0]['FOR'] . "_cp$id";
        	        if ($field['OPTIONS'][0]['FOR'] == 'date_box') {
        	            $value = "date_cp$id";
        	        }
        	        $options .= "<option value='$value' $selected>" . htmlentities($field['TITRE'], ENT_QUOTES, $charset) . "</option>";
	            }
    	    }
    	    if (!empty($options)) {
    	        $sort_pperso .= "<optgroup label='" . htmlentities($msg['authority_champs_perso'], ENT_QUOTES, $charset) . "'>$options</optgroup>";
    	    }
	    }
	    
	    return "<select id='segment_sort_fields_$i' name='segment_sort_fields[]' class='saisie-30em'>
	               $sort_principal_fields
	               $sort_pperso
	            </select>";
	}
	
	public function get_select_direction_sort($sort, $i) {
	    global $msg;
	    
	    $direction_sort = clean_string(explode('_', $sort)[0]);
	    
	    return "<select id='segment_sort_direction_$i' name='segment_sort_direction[]' class='saisie-10em'>
	               <option value='c' " . ($direction_sort != 'd' ? 'selected' : '') . ">" . $msg['list_applied_sort_asc'] . "</option>
	               <option value='d' " . ($direction_sort == 'd' ? 'selected' : '') . ">" . $msg['list_applied_sort_desc'] . "</option>
	            </select>";
	}
	
	public function get_translated_sort($sort_name, $i, $language) {
	    $translated_text = translation::get_translated_text($this->num_segment, 'search_segments', 'segment_sort', $sort_name, $language);
	    if (!empty($translated_text)) {
	        $translated_sorts = explode('||',$translated_text);
	        if (!empty($translated_sorts[$i]) && !empty(explode('|',$translated_sorts[$i])[1])){
	            return trim(explode('|',$translated_sorts[$i])[1]);
	        }
	    }
	    return $sort_name;
	}
	
	public function get_principal_fields($type) {
	    global $include_path;
	    
	    if ($type == "notices_externes"){
	        $type = "external";
	    }
	    
	    if (isset($this->principal_fields)) {
	        return;
	    }
	    
	    $nomfichier = "$include_path/sort/$type/sort.xml";
	    
	    if (file_exists("$include_path/sort/$type/sort_subst.xml")) {
	        $nomfichier = "$include_path/sort/$type/sort_subst.xml";
	        $fp = fopen($nomfichier, "r");
	    } elseif (file_exists($nomfichier)) {
	        $fp = fopen($nomfichier, "r");
	    }
	    
	    if ($fp) {
	        $xml = fread($fp, filesize($nomfichier));
	        fclose($fp);
	        $params = _parser_text_no_function_($xml, "SORT", $nomfichier);
	        $this->principal_fields = $params;
	    }
	    return $this->principal_fields;
	}
	
	public function get_pperso_fields($type) {
	    if (!isset($this->pperso_fields)) {
    	    $this->pperso_fields = new parametres_perso($type);
	    }
	    return $this->pperso_fields;
	}
	
	public function get_sort_fields_javascript() {
	    global $categ;
	    return "
	        <script type='text/javascript'>
                    var global_sort_index = '';
                        
                    var callback = function () {
                        index_tri = global_sort_index;
                        var sorts_string = document.getElementById('segment_sort_list_'+index_tri).value;
                        var sort_name = document.getElementById('segment_sort_name_'+index_tri).value;
                        let string_sort = sorts_string+'|'+sort_name;
                        if (sorts_string.length === 0 && sort_name.length === 0) {
                            string_sort = '';
                        }
                        autoFillForm(index_tri,string_sort);
                        document.getElementById('history').style.display = '';
                   }
                   function autoFillForm(index_tri, string_sort) {
                        var iframeDocument = document.getElementById('history').contentWindow.document;
                        if(iframeDocument.getElementById('popup_index_tri')) {
                            iframeDocument.getElementById('popup_index_tri').value = index_tri;
                        }
                        if(iframeDocument.getElementById('popup_sort_string')) {
                            iframeDocument.getElementById('popup_sort_string').value = string_sort;
                        }
                        if(iframeDocument.getElementById('popup_action_tri')) {
                            iframeDocument.getElementById('popup_action_tri').value = 'modif';
                        }
                        iframeDocument.getElementsByName('sort_form')[0].method = 'POST';
                        iframeDocument.getElementsByName('sort_form')[0].submit();
                        document.getElementById('history').removeEventListener(\"load\", callback);
        	       }
        	       function editSort(index_tri) {
                        global_sort_index = index_tri;
                        document.getElementById('history').addEventListener(\"load\", callback);
                        var typeSegment = document.getElementById('segment_type').value;
                        document.getElementById('history').src='./sort.php?type_tri='+typeSegment+'&categ={$categ}&num_segment=".$this->num_segment.$this->get_sort_param()."';
        	      }
                  function deleteSort(i) {
                        document.getElementById('sort_container_'+i).remove();
						let default_checkbox = document.getElementById('default_sort_0');
						if (default_checkbox) {
							default_checkbox.checked = 'true';
						}
                  }
                  
	            function add_sort_field(sort_index) {
                   //on vérifie si on ajoute le premier input
	               let target_index = parseInt(sort_index) + 1;
                   if(sort_index == 0 && document.getElementById('segment_sort_name_default')){
                        document.getElementById('segment_sort_name_0').value = document.getElementById('segment_sort_label_default').innerHTML;
                        document.getElementById('segment_sort_list_0').value = document.getElementById('segment_sort_list_default').value;
                        document.getElementById('segment_sort_label_0').innerHTML = document.getElementById('segment_sort_label_default').innerHTML;
                        document.getElementById('edit_segment_sort_0').style.display='block';
                        document.getElementById('segment_sort_line_default').remove();
                        document.getElementById('sort_container_0').style.display = '';
                        //return false;
                    }

	               let nb_sort = document.getElementById('nb_segment_sort').value;
                   document.getElementById('nb_segment_sort').value = parseInt(nb_sort)+1;

	               let addSort = document.getElementById('segment_sort_add_button');
                   addSort.removeAttribute('onclick');
                   addSort.onclick = function() {add_sort_field(target_index) };

	               let container = document.getElementById('sort_table').children[0];
	               let row = document.getElementById('sort_container_' + sort_index).cloneNode(true);
                   row.id = 'sort_container_' + target_index;

                   let default_sort_radio = row.children[0].children[0];
                   default_sort_radio.value = target_index;
                   default_sort_radio.id = 'default_sort_'+target_index;
                   default_sort_radio.removeAttribute('checked');

                   let label_sort = row.children[1].children[0];
	               label_sort.id = 'segment_sort_label_' + target_index;
	               label_sort.innerHTML = '';

	               let sort_name = row.children[1].children[1];
	               sort_name.id = 'segment_sort_name_' + target_index;
	               sort_name.name = 'segment_sort_name_' + target_index;
	               sort_name.value = '';

	               let sort_list = row.children[1].children[2];
	               sort_list.id = 'segment_sort_list_' + target_index;
	               sort_list.name = 'sort_list_' + target_index;
                   sort_list.value = '';

                   let edit_button = row.children[2].children[0];
                   edit_button.removeAttribute('onclick');
	               edit_button.onclick = function() {editSort(target_index) };
	               edit_button.id = 'edit_segment_sort_'+target_index;

	               let del_button = row.children[3].children[0];
                   del_button.style.display='';
	               del_button.removeAttribute('onclick');
	               del_button.onclick = function() { deleteSort(target_index) };
	               del_button.id = 'segment_sort_delete_' + target_index;
	               container.appendChild(row);
	            }
	        </script>";
	}
	
	private function get_segment_sort_default() {
	    global $msg, $charset;
	    
	    // On test quel type on a car la pert n'est pas defini pareil dans le xml
	    // Par defaut on met une pert DESC
	    $sort = 'd_num_1';
	    $message = $msg['segment_sort_label_default'];
	    if (TYPE_NOTICE == $this->type) {
	        $sort = 'd_num_6';
	    }
	    if (TYPE_EXTERNAL == $this->type){
	        $sort = '';
	        $message = $msg['segment_sort_label_no_default'];
	    }

	    return "<tr id='segment_sort_line_default' class='odd'>
                   <td>
                        <input id='default_sort_0' type='radio' name='default_sort' value='0' checked='' />
                   </td>
                   <td>
                	   <label id='segment_sort_label_default'> " . $message ." </label>
                	   <input class='saisie-30em' id='segment_sort_name_default' name='segment_sort_name_0' type='hidden' value='segment_sort_name_default'/>
                	   <input id='segment_sort_list_default' name='sort_list_0' type='hidden' value='" . $sort . "' />
                   </td>
                   <td>
                       <img style='display:none; cursor:pointer;' src='".get_url_icon('b_edit.png')."' alt=\"".htmlentities($msg['modif_tri'],ENT_QUOTES,$charset)."\" title=\"".htmlentities($msg['modif_tri'],ENT_QUOTES,$charset)."\"' />
                    </td>
                   <td>
                       <input style='display:none;' id='segment_sort_delete_default' class='bouton' type='button' value='X' onclick='document.getElementById(\"segment_sort_name_default\").value = \"\";document.getElementById(\"segment_sort_list_0\").value = \"\"' />
                    </td>
        	   </tr>";
	}
	
	private function get_sort_param() {
	    $sort_param = "";
	    if($this->type > 10000){
	        // Nothing to do
	    }else if ($this->type > 1000) {
	        $sort_param = "&id_authperso=".($this->type - 1000);
	    }
	    return $sort_param;
	}
}