<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: level1_records_extended_search.class.php,v 1.4.4.1 2025/01/30 11:20:35 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/search/level1/level1_records_search.class.php");

class level1_records_extended_search extends level1_records_search {

	protected $instance_search;

	protected function get_hidden_search_form_name() {
    	return 'search_form';
    }

	protected function get_affiliate_template() {
		global $search_extented_result_affiliate_lvl1;

		return $search_extented_result_affiliate_lvl1;
	}

	protected function get_affiliate_label() {
		return $this->get_instance_search()->make_human_query();
	}

	protected function get_display_link_affiliate() {
		global $msg;

		if($this->get_nb_results()){
			return "<a href='#' onclick=\"document.".$this->get_hidden_search_form_name().".action = '".$this->get_form_action()."&mode=extended&tab=catalog'; enable_operators(); document.".$this->get_hidden_search_form_name().".submit();return false;\">".$msg['suite']."&nbsp;<img src='".get_url_icon('search.gif')."' style='border:0px' /></a>";
		}else {
			return "";
		}
	}

	protected function get_display_result() {
		global $msg;

		$display = "<strong>".$this->get_instance_search()->make_human_query()."</strong> ".$this->get_nb_results()." ".$msg['results']." ";
		$display .= "<a href=\"#\" onclick=\"document.".$this->get_hidden_search_form_name().".action='".$this->get_form_action()."&mode=extended'; enable_operators(); document.".$this->get_hidden_search_form_name().".submit(); return false;\">".$msg['suite']."&nbsp;<img src='".get_url_icon('search.gif')."' style='border:0px' /></a><br /><br />";
		return $display;
	}

    public function proceed() {
    	global $msg;
    	global $search;
    	global $opac_allow_affiliate_search, $allow_search_affiliate_and_external;
    	global $search_error_message;

    	$flag=false;
    	//Vérification des champs vides
    	//Y-a-t-il des champs ?
    	if (empty($search)) {
    		$search_error_message=$msg["extended_use_at_least_one"];
    		$flag=true;
    	} else {
    		//Vérification des champs vides
    	    $nb_search = is_countable($search) ? count($search) : 0;
    	    $nb_search_empty = 0;
    	    for ($i=0; $i<$nb_search; $i++) {
    			$op="op_".$i."_".$search[$i];
    			global ${$op};

    			$field_="field_".$i."_".$search[$i];
    			global ${$field_};
    			$field=${$field_};

    			$field1_="field_".$i."_".$search[$i]."_1";
    			global ${$field1_};
    			$field1=${$field1_};

    			$es=$this->get_instance_search();
    			$s=explode("_",$search[$i]);
    			if ($s[0]=="f") {
    				$champ=$es->fixedfields[$s[1]]["TITLE"];
    			} elseif ($s[0]=="s") {
    				$champ=$es->specialfields[$s[1]]["TITLE"];
    			} else {
    				$champ=$es->pp->t_fields[$s[1]]["TITRE"];
    			}
    			if (((string)$field[0]=="" && (string)$field1[0]=="") && (!$es->op_empty[${$op}])) {
    			    if(empty($search_error_message)) {
    			        $search_error_message=sprintf($msg["extended_empty_field"],$champ);
    			    }
    				$nb_search_empty++;
    			}
    	    }
    	    if($nb_search == $nb_search_empty) {
    		    $flag=true;
    	    } else {
    	        $search_error_message = '';
    	    }
    	}
    	if (!$flag) {
    		$searcher_extended = new searcher_extended();
    		if($nb_search_empty) {
    		    $es=$this->get_instance_search();
    		    $es->reduct_search();
    		}
    		$searcher_extended->get_result();
    		$this->nb_results =  $searcher_extended->get_nb_results();

    		if($opac_allow_affiliate_search && $allow_search_affiliate_and_external){
    			print $this->get_display_result_affiliate();
    		}else{
    			if($this->get_nb_results()) {
    				print $this->get_display_result();
    			}
    		}
    		$this->search_log($this->nb_results);
    	}
    }

    public function get_instance_search() {
    	if(!isset($this->instance_search)) {
    		$this->instance_search = new search();
    	}
    	return $this->instance_search;
    }
}
?>