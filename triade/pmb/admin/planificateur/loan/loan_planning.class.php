<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: loan_planning.class.php,v 1.3.16.1 2025/03/13 16:40:33 qvarin Exp $

global $class_path, $include_path;
require_once($include_path."/parser.inc.php");
require_once($class_path."/scheduler/scheduler_planning.class.php");
//require_once($class_path."/docs_location.class.php");
require_once($class_path."/filter_list.class.php");
//require_once($class_path."/amende.class.php");

define('LOAN_ALL_ACTIONS','1');
define('LOAN_PRINT_MAIL','2');
define('LOAN_CSV_MAIL','3');

class loan_planning extends scheduler_planning {

	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {
		global $pmb_lecteurs_localises, $empr_sort_rows, $empr_show_rows, $empr_filter_rows, $deflt2docs_location;

		//Automatisation sur les prêts
		$form_task = "
		<div class='row'>
			<div class='colonne3'>
				<label for='loan'>".$this->msg["planificateur_loan_generate"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='radio' name='chk_loan' value='".LOAN_ALL_ACTIONS."' ".(($param["chk_loan"] == LOAN_ALL_ACTIONS) ? "checked" : "")."/>".$this->msg["loan_all_actions"]."
				<br /><input type='radio' name='chk_loan' value='".LOAN_PRINT_MAIL."' ".(($param["chk_loan"] == LOAN_PRINT_MAIL) || (!$param["chk_loan"])  ? "checked" : "")."/>".$this->msg["loan_print_mail"]."
				<br /><input type='radio' name='chk_loan' value='".LOAN_CSV_MAIL."' ".(($param["chk_loan"] == LOAN_CSV_MAIL)  ? "checked" : "")."/>".$this->msg["loan_csv_mail"]."
			</div>
		</div>
		<div class='row'>&nbsp;</div>";

		if (($empr_sort_rows)||($empr_show_rows)||($empr_filter_rows)) {
			if ($pmb_lecteurs_localises) $localisation=",l";
			$filter=new filter_list("empr","empr_list","b,n,c,g","b,n,c,g".$localisation.",2,3,cs","n,g");
			if ($pmb_lecteurs_localises) {
				$lo="f".$filter->fixedfields["l"]["ID"];
				global ${$lo};
				if (!${$lo}) {
					$tableau=array();
					$tableau[0]=$deflt2docs_location;
					${$lo}=$tableau;
				}
			}
			$filter->fixedcolumns="b,n,c";
			$filter->original_query=$requete;
			$filter->multiple=1;

			$filter->select_original="table_filter_tempo.empr_nb,empr_mail";
			$filter->original_query="select id_empr,count(pret_idexpl) as empr_nb from empr,pret where pret_retour<now() and pret_idempr=id_empr group by empr.id_empr";
			$filter->from_original="";
			$filter->activate_filters();
			if (!$filter->error) {
				$t_filters = explode(",",$filter->filtercolumns);
				foreach ($t_filters as $i=>$f) {
					if ((substr($s[$i],0,1)=="#")&&($filter->params["REFERENCE"][0]["DYNAMICFIELDS"]=="yes")) {
						//Faut-il adapter les champs perso ??

					} elseif (array_key_exists($t_filters[$i],$filter->fixedfields)) {
						$filters_selectors="f".$filter->fixedfields[$f]["ID"];
					} else {
						$filters_selectors="f".$filter->specialfields[$f]["ID"];
					}

					global ${$filters_selectors};
					if ($param[$filters_selectors]) {
						$tableau=array();
						foreach ($param[$filters_selectors] as $categ) {
							$tableau[$categ] = $categ;
						}
						${$filters_selectors} = $tableau;
					}
				}

				$form_task .= "<div class='row'>
				<div class='colonne3'>
					<label for='loan'>".$this->msg["planificateur_loan_filters"]."</label>
				</div>
				<div class='colonne_suite'>
					".$filter->display_filters()."
					</div>
				</div>
				<div class='row'>&nbsp;</div>";

				$t_sort = explode(",",$filter->sortablecolumns);
				//parcours des selecteurs de tris
	    		for ($j=0;$j<=count($t_sort)-1;$j++) {
	    			$sort_selector="sort_list_".$j;
	    			global ${$sort_selector};
					if ($param[$sort_selector]) {
						${$sort_selector} = $param[$sort_selector];
					}
	    		}
				$form_task .= "<div class='row'>
				<div class='colonne3'>
					<label for='loan'>".$this->msg["planificateur_loan_tris"]."</label>
				</div>
				<div class='colonne_suite'>
					".$filter->display_sort()."
					</div>
				</div>
				<div class='row'>&nbsp;</div>";
			} else {
				$form_task .= $filter->error_message;
			}
		}
		return $form_task;
	}

	public function make_serialized_task_params() {
    	global $chk_loan,$empr_location_id;
    	global $f6, $f8, $f5, $f11, $f2, $f3;
    	global $sort_list_0, $sort_list_1;

		$t = parent::make_serialized_task_params();

		if ($chk_loan) {
			$t["chk_loan"]=$chk_loan;
		}
		if (!empty($f6)) {
			for ($i=0; $i<count($f6); $i++) {
				$t["f6"]=$f6;
			}
		}
		if (!empty($f8)) {
			for ($i=0; $i<count($f8); $i++) {
				$t["f8"]=$f8;
			}
		}
		if (!empty($f11)) {
			for ($i=0; $i<count($f11); $i++) {
				$t["f11"]=$f11;
			}
		}
		if (!empty($f5)) {
			for ($i=0; $i<count($f5); $i++) {
				$t["f5"]=$f5;
			}
		}
		if (!empty($f2)) {
			for ($i=0; $i<count($f2); $i++) {
				$t["f2"]=$f2;
			}
		}
		if (!empty($f3)) {
			for ($i=0; $i<count($f3); $i++) {
				$t["f3"]=$f3;
			}
		}
		$t["sort_list_0"] = $sort_list_0;
		$t["sort_list_1"] = $sort_list_1;
		$t["empr_location_id"] = $empr_location_id;

    	return serialize($t);
	}
}