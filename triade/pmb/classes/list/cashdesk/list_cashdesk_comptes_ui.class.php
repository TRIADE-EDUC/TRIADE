<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_cashdesk_comptes_ui.class.php,v 1.1.2.3 2024/09/11 14:19:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_cashdesk_comptes_ui extends list_cashdesk_ui {
		
	protected $tt_realisee_no=0;
	protected $tt_realisee=0;
	protected $tt_encaissement_no=0;
	protected $tt_encaissement=0;
	
	protected function add_object($row) {
	    $cashdesk = $this->get_cashdesk($row->cashdesk_id);
	    $all_transactions=$cashdesk->summarize($this->filters['date_effective_start'], $this->filters['date_effective_end'], 0, 0);
	    foreach($all_transactions as $transactions){
	        $transactions['cashdesk_id'] = $row->cashdesk_id;
	        $transactions['cashdesk_name'] = $row->cashdesk_name;
	        
	        if(empty($transactions['transactype_num'])) {
	            $transactions['transactype_num'] = str_replace('cpt_', '', $transactions['id']);
	        }
	        if(empty($transactions['transactype_name'])) {
	            $transactions['transactype_name'] = $transactions['name'];
	        }
	        if(empty($transactions['transactype_unit_price'])) {
	            $transactions['transactype_unit_price'] = $transactions['unit_price'];
	        }
	        
	        //Gestion des totaux
	        if($transactions['realisee_no']) {
	            $this->tt_realisee_no += $transactions['realisee_no'];
	        }
	        if($transactions['realisee']) {
	            $this->tt_realisee += $transactions['realisee'];
	        }
	        if($transactions['encaissement_no']) {
	            $this->tt_encaissement_no += $transactions['encaissement_no'];
	        }
	        if($transactions['encaissement']) {
	            $this->tt_encaissement += $transactions['encaissement'];
	        }
	        
	        parent::add_object((object) $transactions);
	    }
	}
	
	protected function fetch_data() {
	    parent::fetch_data();
	    $this->pager['nb_results'] = count($this->objects);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('cashdesks');
		$this->add_selected_filter('date_effective');
	}
	
	protected function init_default_settings() {
	    parent::init_default_settings();
	    $this->set_setting_display('search_form', 'unfoldable_filters', false);
	    $this->set_setting_column('transactype_name', 'align', 'left');
	}
	
// 	protected function _get_query_order() {
// 		return ' GROUP BY transactype_num '.parent::_get_query_order();
// 	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'cashdesk_name' => 'cashdesk_edition_name',
    				    'transactype_name' => 'cashdesk_edition_transac_name',
    				    'transactype_unit_price' => 'cashdesk_edition_transac_unit_price',
    				    'montant' => 'cashdesk_edition_transac_montant',
    				    'realisee_no' => 'cashdesk_edition_transac_realisee_no',
    				    'realisee' => 'cashdesk_edition_transac_realisee',
    				    'encaissement_no' => 'cashdesk_edition_transac_encaissement_no',
    				    'encaissement' => 'cashdesk_edition_transac_encaissement',
    				    'payment_method' => 'cashdesk_edition_transac_payment_method'
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
	    //$this->add_column_expand();
		$this->add_column('cashdesk_name');
		$this->add_column('transactype_name');
		$this->add_column('transactype_unit_price');
		$this->add_column('montant');
		$this->add_column('realisee_no');
		$this->add_column('realisee');
		$this->add_column('encaissement_no');
		$this->add_column('encaissement');
	}
	
	protected function init_no_sortable_columns() {
	    $this->no_sortable_columns = array(
	        'expand'
	    );
	}
	
	protected function add_column_expand() {
	    $this->columns[] = array(
	        'property' => 'expand',
	        'label' => '',
	        'html' => "<img src='".get_url_icon('plus.gif')."' class='img_plus' onClick='if (event) e=event; else e=window.event; e.cancelBubble=true; if (e.stopPropagation) e.stopPropagation(); show_transactions(\"".addslashes('!!id!!')."\"); ' style='cursor:pointer;'/>",
	        'exportable' => false
	    );
	}
	
	protected function _get_label_cell_header($name) {
		global $msg, $charset;
		
		switch ($name) {
			case 'cashdesk_edition_transac_realisee_no':
				return htmlentities($msg[$name],ENT_QUOTES,$charset)." (".$this->format_price($this->tt_realisee_no).")";
			case 'cashdesk_edition_transac_realisee':
				return htmlentities($msg[$name],ENT_QUOTES,$charset)." (".$this->format_price($this->tt_realisee).")";
			case 'cashdesk_edition_transac_encaissement_no':
				return htmlentities($msg[$name],ENT_QUOTES,$charset)." (".$this->format_price($this->tt_encaissement_no).")";
			case 'cashdesk_edition_transac_encaissement':
				return htmlentities($msg[$name],ENT_QUOTES,$charset)." (".$this->format_price($this->tt_encaissement).")";
			default:
				return parent::_get_label_cell_header($name);
		}
	}
	
	protected function _get_object_property_transactype_unit_price($object) {
		return $this->format_price($object->transactype_unit_price);
	}
	
	protected function _get_object_property_montant($object) {
		return $this->format_price($object->montant);
	}
	
	protected function get_query_cashdesk_transactions($object) {
	    $query = "select SUM(montant)as cash from transactions ";
	    $filters = $this->_get_query_filters();
	    if(!empty($filters)) {
	        $query .= $filters." and ";
	    } else {
	        $query .= " where ";
	    }
	    $query .= " cashdesk_num=".$object->cashdesk_id." and transactype_num=".$object->transactype_num;
	    return $query;
	}
	
	protected function get_cash_from_realisee($object, $realisee=0) {
		$query = $this->get_query_cashdesk_transactions($object);
		$query .= " and realisee=".$realisee;
		$res_sum=pmb_mysql_query($query);
		if($row_sum= pmb_mysql_fetch_object($res_sum)) {
			return $row_sum->cash;
		}
		return 0;
	}
	
	protected function _get_object_property_realisee_no($object) {
        return $this->format_price($object->realisee_no);
// 		$cash = $this->get_cash_from_realisee($object, 0);
// 		if($cash) {
// 			$this->tt_realisee_no += $cash;
// 			return $this->format_price($cash);
// 		}
		return '';
	}
	
	protected function _get_object_property_realisee($object) {
        return $this->format_price($object->realisee);
// 		$cash = $this->get_cash_from_realisee($object, 1);
// 		if($cash) {
// 			$this->tt_realisee += $cash;
// 			return $this->format_price($cash);
// 		}
		return '';
	}
	
	protected function _get_object_property_encaissement_no($object) {
        return $this->format_price($object->encaissement_no);
// 	    $query = $this->get_query_cashdesk_transactions($object);
// 		$query .= " and encaissement=0 and transacash_num=0";
// 		$res_sum=pmb_mysql_query($query);
// 		if($row_sum= pmb_mysql_fetch_object($res_sum)) {
// 			$this->tt_encaissement_no += $row_sum->cash;
// 			return $this->format_price($row_sum->cash);
// 		}
		return '';
	}
	
	protected function _get_object_property_encaissement($object) {
        return $this->format_price($object->encaissement);
// 	    $query = $this->get_query_cashdesk_transactions($object);
// 		$query .= " and transacash_num>0 ";
// 		$res_sum=pmb_mysql_query($query);
// 		if($row_sum= pmb_mysql_fetch_object($res_sum))	{
// 			$this->tt_encaissement += $row_sum->cash;
// 			return $this->format_price($row_sum->cash);
// 		}
		return '';
	}
	
	protected function _get_object_property_payment_method($object) {
	    global $dest;
	    
	    $display = '';
	    $data = array();
	    
	    $encaissement_payment_method = $object->encaissement_payment_method;
	    foreach ($encaissement_payment_method as $transaction) {
	        if ($transaction['transaction_payment_method_name'] && $transaction['cash']) {
	            if(!isset($data[$transaction['transaction_payment_method_name']])) {
	                $data[$transaction['transaction_payment_method_name']] = 0;
	            }
	            $data[$transaction['transaction_payment_method_name']]+= $transaction['cash'];
	        }
	    }
	    foreach ($data as $mode => $montant) {
	        if(!empty($dest)) { // TABLEAU EXCEL & AUTRES
	            if ($display) {
	                $display.= '; ';
	            }
	            $display.= $mode . ': ' . $this->format_price($montant);
	        } else {
	            if (!$display) {
	                $display.= '<br />';
	            } else {
	                $display.= '; ';
	            }
	            $display.= $mode . ': ' . $this->format_price($montant);
	        }
	    }
	    return $display;
	}
	
	protected function get_display_content_transactions_object_list($object, $indice) {
	    $display = "<tr class='".($indice % 2 ? 'odd' : 'even')."' style='display:none' id='".$object->id."'><td>&nbsp;</td><td colspan='".(count($this->columns)-1)."' style='border:1px solid'>";
	    $filters = array(
	        'cashdesks' => array($object->cashdesk_id),
	        'transactypes' => array($object->transactype_num),
	        'date_effective_start' => $this->filters['date_effective_start'],
	        'date_effective_end' => $this->filters['date_effective_end'],
	    );
	    $display .= list_cashdesk_transactions_compte_ui::get_instance($filters)->get_display_list();
	    $display .= "</td></tr>";
	    return $display;
	}
	
	protected function get_display_content_object_list($object, $indice) {
	    $display = parent::get_display_content_object_list($object, $indice);
	    //$display .= $this->get_display_content_transactions_object_list($object, $indice);
	    return $display;
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'encaissement':
				$content .= $this->_get_object_property_encaissement($object);
				$content .= $this->_get_object_property_payment_method($object);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
	    $attributes = array();
	    switch($property) {
	        case 'cashdesk_name':
	            $attributes['onmousedown'] = "window.location=\"./admin.php?categ=finance&sub=cashdesk&action=edit&id=".$object->cashdesk_id."\"";
                break;
	        case 'transactype_name':
                $attributes['onmousedown'] = "window.location=\"./admin.php?categ=finance&sub=transactype&action=edit&id=".$object->id."\"";
	            break;
	    }
	    return $attributes;
	}
	
	public function get_display_list() {
	    $display = "
		<script type='text/javascript' >
			function show_transactions(id) {
				if (document.getElementById(id).style.display=='none') {
					document.getElementById(id).style.display='';
				} else {
					document.getElementById(id).style.display='none';
				}
			}
		</script>";
	    $display .= parent::get_display_list();
	    return $display;
	}
	
	protected function get_html_title() {
	    global $msg;
	    
	    return "<h1>".$msg["1120"].": ".$msg["cashdesk_edition_menu"]."</h1>";
	}
	
	protected function get_spreadsheet_title() {
	    return "caisse.xls";
	}
	
	protected function get_display_spreadsheet_title() {
	    global $msg;
	    $this->spreadsheet->write_string(0,0,$msg["1120"].": ".$msg["cashdesk_edition_menu"]);
	}
}