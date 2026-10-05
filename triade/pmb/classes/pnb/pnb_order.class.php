<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb_order.class.php,v 1.13 2024/01/08 14:08:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/templates/pnb/pnb_param.tpl.php");
require_once($class_path.'/mono_display.class.php');

class pnb_order {
	
	protected $id;
	protected $order_id;
	protected $line_id;
	protected $num_notice;
	protected $notice;
	protected $loan_max_duration;
	protected $nb_loans;
	protected $nb_simultaneous_loans;
	protected $nb_consult_in_situ;
	protected $nb_consult_ex_situ;
	protected $offer_date;
	protected $offer_date_end;
	protected $offer_duration;
	protected $offer_formated_date;
	protected $offer_formated_date_end;
	protected $nb_current_loans;
	public static $loans_infos = [];
	private static $expl_infos = [];
	
	public function __construct($id = 0){
		$this->id = intval($id);
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		if ($this->id) {
			$query = "SELECT * FROM pnb_orders WHERE id_pnb_order = '".$this->id."'	";			
			$result = pmb_mysql_query($query);
			if ($row = pmb_mysql_fetch_assoc($result)) {
				$this->order_id = $row['pnb_order_id_order'];
				$this->line_id = $row['pnb_order_line_id'];
				$this->num_notice = $row['pnb_order_num_notice'];
				$this->loan_max_duration = $row['pnb_order_loan_max_duration'];
				$this->nb_loans = $row['pnb_order_nb_loans'];
				$this->nb_simultaneous_loans = $row['pnb_order_nb_simultaneous_loans'];
				$this->nb_consult_in_situ = $row['pnb_order_nb_consult_in_situ'];
				$this->nb_consult_ex_situ = $row['pnb_order_nb_consult_ex_situ'];
				$this->offer_date = $row['pnb_order_offer_date'];
				$this->offer_date_end = $row['pnb_order_offer_date_end'];
				$this->offer_duration = $row['pnb_order_offer_duration'];
				$this->offer_formated_date = format_date($row['pnb_order_offer_date']);
				if($row['pnb_order_offer_duration'] == 999999) {
					$this->offer_formated_date_end = '-'; // infini
				} else {
					$this->offer_formated_date_end = format_date($row['pnb_order_offer_date_end']);
				}
			} else {
				$this->id = 0;
			}
		}
	}
	
	public function get_id() {
		return $this->id;
	}

	public function get_order_id() {
		return $this->order_id;
	}
	
	public function get_line_id() {
		return $this->line_id;
	}	
		
	public function get_loan_max_duration() {
		return $this->loan_max_duration;
	}
	
	public function get_nb_loans() {
		return $this->nb_loans;
	}
	
	public function get_nb_simultaneous_loans() {
		return $this->nb_simultaneous_loans;
	}
	
	public function get_nb_consult_in_situ() {
		return $this->nb_consult_in_situ;
	}
	
	public function get_nb_consult_ex_situ() {
		return $this->nb_consult_ex_situ;
	}
	
	public function get_offer_date() {
		return $this->offer_date;
	}

	public function get_offer_date_end() {
		return $this->offer_date_end;
	}	

	public function get_offer_formated_date() {
		return $this->offer_formated_date;
	}
	
	public function get_offer_formated_date_end() {
		return $this->offer_formated_date_end;
	}
	
	public function get_offer_duration() {
		return $this->offer_duration;
	}

	public function get_num_notice() {
		return $this->num_notice;
	}
	
	public function get_notice() {
		if (empty($this->num_notice)) return '';
		if (empty($this->notice)) {
    		$notice = new mono_display($this->num_notice);
    		$this->notice = $notice->header;
		}
		return $this->notice;
	}
	
	public function get_exemplaires(){
	    if(isset(self::$expl_infos[$this->get_line_id()] )){
    	    return self::$expl_infos[$this->get_line_id()];
	    }
	    self::$expl_infos[$this->get_line_id()] = [];
	    $query = "SELECT pnb_order_expl_num FROM pnb_orders_expl WHERE pnb_order_num = ".$this->id;
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
           while ($row = pmb_mysql_fetch_assoc($result)) {
               self::$expl_infos[$this->get_line_id()][] = $row['pnb_order_expl_num'];
           }
        }
        return self::$expl_infos[$this->get_line_id()];
	}
	
	public function get_nb_current_loans() {
	    $query = "SELECT count(pret_idexpl) FROM pnb_orders_expl join pnb_orders on pnb_order_num= id_pnb_order join pret on pret_pnb_flag=1 and pret_idexpl = pnb_order_expl_num WHERE id_pnb_order = '" . $this->id. "' ";
 	    $result = pmb_mysql_query($query);
 	    if(pmb_mysql_num_rows($result)){
 	        return pmb_mysql_result($result,0,0);
 	    }
	    return 0;
	}
	
	public function get_loans_completed_number() {
	    if(!isset(self::$loans_infos[$this->get_line_id()] )) {
	        $loans_infos = dilicom::get_instance()->get_loan_status(array($this->get_line_id()));
	        
	        if(is_array($loans_infos["loanResponseLine"]) && count($loans_infos["loanResponseLine"])) {
	            self::$loans_infos[$this->get_line_id()] = $loans_infos["loanResponseLine"][0];
	        }
	    }
	    if (isset(self::$loans_infos[$this->get_line_id()]['nta'])) {
	        return self::$loans_infos[$this->get_line_id()]['nta'];
	    }
	    return 0;
	}
	
	public function get_loans_in_progress() {
	    if(!isset(self::$loans_infos[$this->get_line_id()] )) {
	        $loans_infos = dilicom::get_instance()->get_loan_status(array($this->get_line_id()));
	        
	        if(is_array($loans_infos["loanResponseLine"]) && count($loans_infos["loanResponseLine"])) {
	            self::$loans_infos[$this->get_line_id()] = $loans_infos["loanResponseLine"][0];
	        }
	    }
	    if (isset(self::$loans_infos[$this->get_line_id()]['nus1'])) {
	        return self::$loans_infos[$this->get_line_id()]['nus1'];
	    }
	    return '0';
	}
	
	public static function get_loans_number_by_order_line_id($line_id) {
	    $query = "SELECT count(id_pnb_loan) as loans_number FROM pnb_loans WHERE pnb_loan_order_line_id = '" . $line_id . "' ";	
		$result = pmb_mysql_query($query);
		return pmb_mysql_result($result, 0, 0);
	}
	
	public static function delete_pnb_order($pnb_order_line_id){
	    $query = "delete from pnb_loans where pnb_loan_order_line_id ='".addslashes($pnb_order_line_id)."'";
	    pmb_mysql_query($query);
	    $query = "delete from pnb_orders where pnb_order_line_id ='".addslashes($pnb_order_line_id)."'";
	    pmb_mysql_query($query);
	}
}