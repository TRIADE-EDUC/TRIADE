<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.3 2022/06/13 13:27:23 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $msg,$lang,$charset,$base_path,$class_path,$include_path;


class dynamic_value {
	public $id;
	public $n_ligne;
	public $params;
	/**
	 * 
	 * @var search
	 */
	public $search;
	
	private $var_types = [
	    "global" => "global_variable",
	];

	//Constructeur
    public function __construct($id,$n_ligne,$params,&$search) {
    	$this->id=$id;
    	$this->n_ligne=$n_ligne;
    	$this->params=$params;
    	$this->search=&$search;
    }
    
    //fonction de récupération des opérateurs disponibles pour ce champ spécial (renvoie un tableau d'opérateurs)
    public function get_op() {
        global $msg;
    	return [
    	    "EQ" => "=",
    	    "GT" => $msg["gt_query"],
    	    "LT" => $msg["lt_query"],
    	    "GTEQ" => $msg["gteq_query"],
    	    "LTEQ" => $msg["lteq_query"],
    	    "BOOLEAN" => $msg["expr_bool_query"],
    	];
    }
    
    //fonction de récupération de l'affichage de la saisie du critère
    public function get_input_box() {
    	global $msg;
    	global $charset;
    	global $get_input_box_id;
    	global $base_path;
        
    	
    	//$this->s = new search(false,"search_simple_fields.xml");
    	
    	//Récupération de la valeur de saisie
    	$field_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$field_};
    	$field=${$field_};
    	
    	$fieldvar_="fieldvar_".$this->n_ligne."_s_".$this->id;
    	global ${$fieldvar_};
    	$fieldvar=${$fieldvar_};
    	
    	$html =" 
        <label for='" . $field_ . "'>".htmlentities($msg["opac_view_list_human_query"], ENT_QUOTES, $charset)."</label>
        <select id='" . $field_ . "' name='" . $field_. "[]' >";
    	$html .= $this->get_criterion_options($field[0]);
    	$html .="
        </select>
        <hr/>
        <label for='" . $fieldvar_ . "_0'>".htmlentities($msg["variable_type"], ENT_QUOTES, $charset)."</label>
        <select id='" . $fieldvar_ . "_0' name='" . $fieldvar_ . "[0]'>";
    	$html .= $this->get_var_types_options($fieldvar[0]);
    	$html .="
        </select>
        <label for='" . $fieldvar_ . "_1'>".htmlentities($msg["variable_name"], ENT_QUOTES, $charset)."</label>
    	<input type='text' id='" . $fieldvar_ . "_1' name='" . $fieldvar_ . "[1]' value='" . $fieldvar[1] . "'><br>
    	";
    	return $html;
    }
    
    private function get_var_types_options($selected = "") {
        global $msg;
        $html = "";
        foreach ($this->var_types as $value => $label) {
            $html .= "<option value='$value' ".($selected == $value ? "selected" : "").">".(isset($msg[$label]) ? $msg[$label] : $label)."</option>";
        }
        return $html;
    }
    
    private function get_criterion_options($selected = "") {
        
        $html = "";
        $list_criterion = $this->search->get_list_criteria();
        foreach ($list_criterion as $group => $criterion) {
            if (count($criterion)) {
                $html .= "<optgroup label='$group'>";
                foreach ($criterion as $criteria) {
                    $html .= "<option value='".$criteria["id"]."' ".($selected == $criteria["id"] ? "selected" : "").">".$criteria["label"]."</option>";
                }
                $html .= "</optgroup>";
            }
        }
        return $html;
    }
    
    //fonction de conversion de la saisie en quelque chose de compatible avec l'environnement
    public function transform_input() {
    }
    
    //fonction de création de la requête (retourne une table temporaire)
    public function make_search() {
    	global $search;
    	global $base_path;
    	
    	$field_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$field_};
    	$field=${$field_};
    	
    	$fieldvar_="fieldvar_".$this->n_ligne."_s_".$this->id;
    	global ${$fieldvar_};
    	$fieldvar=${$fieldvar_};
    	
    	//opérateur
    	$op_ = "op_".$this->n_ligne."_s_".$this->id;
    	global ${$op_};
    	$special_op=${$op_};
    	
    	$this->search->push();
    	
    	$search[0]=$field[0];
    	
    	
    	$field_value = $this->get_field_value($fieldvar);
    	
    	//contenu de la recherche
    	$field = "field_0_".$search[0];
    	$field_array_ = array();
    	$field_array_[0] = $field_value;
    	global ${$field};
    	${$field} = $field_array_;
    	
    	//opérateur
    	$op="op_0_".$search[0];
    	global ${$op};
    	${$op}=$special_op;
    	
    	//opérateur inter-champ
    	$inter="inter_0_".$search[0];
    	global ${$inter};
    	${$inter}="";
    	
    	//variables auxiliaires
    	$fieldvar_="fieldvar_0_".$search[0];
    	global ${$fieldvar_};
    	${$fieldvar_}="";
    	$fieldvar=${$fieldvar_};

        $table_tempo=$this->search->make_search("tempo_s_".$this->id);
        $this->search->pull();
        
        return $table_tempo;
    }
    
    private function get_field_value($fieldvar) {
        if (empty($fieldvar) || (count($fieldvar) != 2)) {
            return "";
        }
        switch ($fieldvar[0]) {
            case "global":
            default :
                global ${$fieldvar[1]};
                if (isset(${$fieldvar[1]})) {
                    return ${$fieldvar[1]};
                }
                break;
        }
        return "";
    }
    
    public function make_unimarc_query() {
		return array();
    }
    	    
    //fonction de traduction littérale de la requête effectuée (renvoie un tableau des termes saisis)
    public function make_human_query() {
		global $base_path,$charset;
		global $msg;
    	
		//Récupération de la valeur de saisie
		$field_ = "field_".$this->n_ligne."_s_".$this->id;
		global ${$field_};
		$field = ${$field_};
		
		$s=explode("_",$field[0]);
	    $title = '';
		if ($s[0]=="f") {
		    if (isset($this->search->fixedfields[$s[1]]["TITLE"])) {
		        $title = $this->search->fixedfields[$s[1]]["TITLE"];
		    }
		} elseif(!empty($this->pp) && array_key_exists($s[0],$this->pp)){
		    $title=$this->search->pp[$s[0]]->t_fields[$s[1]]["TITRE"];
		} elseif ($s[0]=="s") {
		    $title=$this->search->specialfields[$s[1]]["TITLE"];
		} elseif ($s[0]=="authperso") {
		    $title=$this->search->authpersos[$s[1]]['name'];
		}
		
		$fieldvar_ = "fieldvar_".$this->n_ligne."_s_".$this->id;
		global ${$fieldvar_};
    	$fieldvar = ${$fieldvar_};
    	
    	$texte = (isset($msg[$this->var_types[$fieldvar[0]]]) ? $msg[$this->var_types[$fieldvar[0]]] : $this->var_types[$fieldvar[0]]).' "'.$fieldvar[1].'"';
    	
    	$html = "<i><strong>".htmlentities($title,ENT_QUOTES,$charset)."</strong> : ".$texte."</i>";
    	
    	$litteral = array($html);
    	return $litteral;
    }
    
    //fonction de vérification du champ saisi ou sélectionné
    public function is_empty($valeur) {
    	
    }
    
     //fonction de découpage d'une chaine trop longue
    public function cutlongwords($valeur,$size=50) {
    	if (strlen($valeur)>=$size) {
    		$pos=strrpos(substr($valeur,0,$size)," ");
    		if ($pos) {
    			$valeur=substr($valeur,0,$pos+1)."...";
    		} 
    	}
    	return $valeur;		
    }
    
    public static function check_visibility() {
        global $entity_type;
        if (!empty($entity_type) && $entity_type == "segment") {
            return true;
        }
        return false;
    }
}