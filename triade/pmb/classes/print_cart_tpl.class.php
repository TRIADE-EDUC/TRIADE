<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: print_cart_tpl.class.php,v 1.1 2017-10-13 13:31:05 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/print_cart_tpl.tpl.php");

class print_cart_tpl {
	private $id = 0;
	private $name;
	private $header;
	private $footer;
	
	public function __construct($id=0) {
		$this->id = $id+0;
		$this->fetch_data();
	}
	
	private function fetch_data() {
		global $dbh;
		
		$this->name = '';
		$this->header = '';
		$this->footer = '';
		
		$req = "select * from print_cart_tpl where id_print_cart_tpl=". $this->id;
		$resultat = pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {
			$r = pmb_mysql_fetch_object($resultat);		
			$this->id = $r->id_print_cart_tpl;	
			$this->name = $r->print_cart_tpl_name;	
			$this->header = $r->print_cart_tpl_header;	
			$this->footer = $r->print_cart_tpl_footer;	
		} else {
			$this->id = 0;
		}
	}

	public function get_id() {
		return $this->id;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_header() {
		return $this->header;
	}
	
	public function get_footer() {
		return $this->footer;
	}
	
	public function set_name($name) {
		$this->name = $name;
	}
	
	public function set_header($header) {
		$this->header = $header;
	}

	public function set_footer($footer) {
		$this->footer = $footer;
	}
	
	public function proceed() {
		global $action;
		global $f_name, $f_header, $f_footer;
		
		switch($action) {
			case 'form':
				print $this->get_form();
				break;
			case 'save':				
				$this->name = stripslashes($f_name);
				$this->header = stripslashes($f_header);
				$this->footer = stripslashes($f_footer);
				print $this->save();
				print $this->get_list();
				break;
			case 'delete':
				print $this->delete();
				print $this->get_list();
				break;
			case 'duplicate':
				print $this->get_form(true);
				break;		
			default:
				print $this->get_list();
				break;
		}		
	}
       
	private function get_form($duplicate = false) {
		global $cart_tpl_form_tpl, $msg, $charset;
		
		$tpl = $cart_tpl_form_tpl;
		if($this->id){
			if (!$duplicate) {
				$tpl = str_replace('!!msg_title!!', $msg['admin_print_cart_tpl_form_edit'], $tpl);
				$tpl = str_replace('!!delete!!', "<input type='button' class='bouton' value='".$msg['admin_print_cart_tpl_delete']."' onclick=\"document.getElementById('action').value='delete';this.form.submit();\"  />", $tpl);
				$tpl = str_replace("!!duplicate!!","<input class='bouton' type='button' value=' ".$msg["admin_print_cart_tpl_duplicate"]." ' onclick=\"document.getElementById('action').value='duplicate';this.form.submit();\" />",$tpl);
			} else {
				$tpl = str_replace('!!msg_title!!', $msg['admin_print_cart_tpl_form_add'], $tpl);
				$tpl = str_replace('!!delete!!', "", $tpl);
				$tpl = str_replace("!!duplicate!!", "", $tpl);
			}
		}else{ 
			$tpl = str_replace('!!msg_title!!', $msg['admin_print_cart_tpl_form_add'], $tpl);
			$tpl = str_replace('!!delete!!', "", $tpl);
			$tpl = str_replace("!!duplicate!!", "", $tpl);
		}
	
		$tpl = str_replace('!!name!!', htmlentities($this->name, ENT_QUOTES, $charset), $tpl);
		$tpl = str_replace('!!header!!', htmlentities($this->header, ENT_QUOTES, $charset), $tpl);
		$tpl = str_replace('!!footer!!', htmlentities($this->footer, ENT_QUOTES, $charset), $tpl);
		if ($duplicate) {
			$this->id = 0;
		}
		$tpl = str_replace('!!id!!', $this->id, $tpl);
		 
		return $tpl;
	}

	public function save() {
		global $dbh;
		
		$fields = "
			print_cart_tpl_name='".addslashes($this->name)."',
			print_cart_tpl_header='".addslashes($this->header)."',
			print_cart_tpl_footer='".addslashes($this->footer)."'
		";		
		if(!$this->id){ // Ajout
			$req = "INSERT INTO print_cart_tpl SET ".$fields ;	
			pmb_mysql_query($req, $dbh);
			$this->id = pmb_mysql_insert_id($dbh);
		} else {
			$req = "UPDATE print_cart_tpl SET ".$fields." where id_print_cart_tpl=".$this->id;	
			pmb_mysql_query($req, $dbh);				
		}	
		$this->fetch_data();
	}	
	
	public function delete() {
		global $dbh;
		
		$req="DELETE from print_cart_tpl WHERE id_print_cart_tpl=".$this->id;
		pmb_mysql_query($req, $dbh);	
		
		$this->fetch_data();	
	}	
		
	public function get_list() {
		global $dbh, $cart_tpl_list_tpl, $cart_tpl_list_line_tpl, $msg;
			
		$odd_even = "odd";
		$tpl_list = '';
		$req = "select * from print_cart_tpl order by print_cart_tpl_name";
		$resultat = pmb_mysql_query($req);
		if (pmb_mysql_num_rows($resultat)) {
			while($r = pmb_mysql_fetch_object($resultat)) {		
				$tpl_elt = $cart_tpl_list_line_tpl;
				if($odd_even=='odd') $odd_even = "even"; else $odd_even = "odd";
				$tpl_elt = str_replace('!!odd_even!!', $odd_even, $tpl_elt);	
				$tpl_elt = str_replace('!!name!!', $r->print_cart_tpl_name, $tpl_elt);	
				$tpl_elt = str_replace('!!header!!', $r->print_cart_tpl_header, $tpl_elt);	
				$tpl_elt = str_replace('!!footer!!', $r->print_cart_tpl_footer, $tpl_elt);	
				$tpl_elt = str_replace('!!id!!', $r->id_print_cart_tpl, $tpl_elt);	
				$tpl_list.= $tpl_elt;	
			}
			return str_replace('!!list!!', $tpl_list, $cart_tpl_list_tpl);
		}		
		return str_replace('!!list!!', '', $cart_tpl_list_tpl);;
	}	
	
} 

