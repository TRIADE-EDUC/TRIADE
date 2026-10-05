<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_diffusion.class.php,v 1.6 2023/09/12 12:25:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/bannette_diffusion.tpl.php");

class bannette_diffusion{
	
	protected $id = 0;
	
	protected $num_bannette = 0;
	
	protected $mail_object = '';
	
	protected $mail_content = '';
	
	protected $date = '';
	
	protected $records = array();
	
	protected $deleted_records = array();
	
	protected $recipients = array();
	
	protected $failed_recipients = array();
	
	protected $equations = array();
	
	protected $bannette;
	
	public function __construct($id=0) {
		$this->id = intval($id);		
		$this->fetch_data();
	}
	
	public function fetch_data() {		
		$query = "SELECT * FROM bannettes_diffusions WHERE id_diffusion = ".$this->id;
		$result = pmb_mysql_query($query);
		while($row=pmb_mysql_fetch_object($result)) {
			$this->num_bannette = $row->diffusion_num_bannette;
			$this->mail_object = $row->diffusion_mail_object;
			$this->mail_content = $row->diffusion_mail_content;
			$this->date = $row->diffusion_date;
			$records = encoding_normalize::json_decode($row->diffusion_records, true);
			if(is_array($records)) {
				$this->records = $records;
			}
			$deleted_records = encoding_normalize::json_decode($row->diffusion_deleted_records, true);
			if(is_array($deleted_records)) {
				$this->deleted_records = $deleted_records;
			}
			$recipients = encoding_normalize::json_decode($row->diffusion_recipients, true);
			if(is_array($recipients)) {
				$this->recipients = $recipients;
			}
			$failed_recipients = encoding_normalize::json_decode($row->diffusion_failed_recipients, true);
			if(is_array($failed_recipients)) {
				$this->failed_recipients = $failed_recipients;
			}
			$equations = encoding_normalize::json_decode($row->diffusion_equations, true);
			if(is_array($equations)) {
				$this->equations = $equations;
			}
		}
	}
	
	protected function get_display_deleted_records($pattern) {
		global $msg, $charset;
		
		$display = '';
		$records = array_keys($this->deleted_records, $pattern);
		if(!empty($records)) {
			$display .= "<h3>".htmlentities($msg['bannette_diffusion_deleted_records_'.$pattern], ENT_QUOTES, $charset)."</h3>";
			$elements_records_list_ui = new elements_records_list_ui($records, count($records), false);
			$display .= $elements_records_list_ui->get_elements_list();
		}
		return $display;
	}
	
	public function get_display_view() {
		global $msg, $charset;
		global $bannette_diffusion_view_tpl;
		
		$display = $bannette_diffusion_view_tpl;
		$display = str_replace("!!date!!", formatdate($this->date, 1), $display);
		$display = str_replace("!!number_records!!", $this->get_number_records(), $display);
		$display = str_replace("!!number_recipients!!", $this->get_number_recipients(), $display);
		$display = str_replace("!!number_deleted_records!!", $this->get_number_deleted_records(), $display);
		$display = str_replace("!!number_sent_mail!!", $this->get_number_sent_mail(), $display);
		
		//Affichage de l'objet du mail
		$display = str_replace("!!mail_object!!", $this->mail_object, $display);
		
		//Affichage du contenu du mail
		$display = str_replace("!!mail_content!!", $this->mail_content, $display);
		
		//Liste des destinataires
		if(!empty($this->recipients)) {
			$empr_ids = $this->recipients;
		} else {
			$empr_ids = array(0);
		}
		$list_readers_bannette_diffusion_ui = list_readers_bannette_diffusion_ui::get_instance(array('id_diffusion' => $this->id,'empr_ids' => $empr_ids));
		$display = str_replace("!!recipients!!", $list_readers_bannette_diffusion_ui->get_display_list(), $display);
		
		//Liste des notices purgées
		$deleted_records_list_ui = '';
		if(!empty($this->deleted_records)) {
			$deleted_records_list_ui = "
				<span class='bannette_diffusion_view_deleted_records'>
					<h2>".htmlentities($msg['bannette_diffusion_deleted_records'], ENT_QUOTES, $charset)."</h2>";
			$records_empty = array_keys($this->deleted_records, 'empty'); // action vider
			if(!empty($records_empty)) {
				$elements_records_list_ui = new elements_records_list_ui($records_empty, count($records_empty), false);
				$deleted_records_list_ui .= $elements_records_list_ui->get_elements_list();
			} else {
				$deleted_records_list_ui .= $this->get_display_deleted_records('access_rights');
				$deleted_records_list_ui .= $this->get_display_deleted_records('equations');
				$deleted_records_list_ui .= $this->get_display_deleted_records('cumulative_limit');
			}
			$deleted_records_list_ui .= "
				</span>";
		}
		$display = str_replace("!!deleted_records!!", $deleted_records_list_ui, $display);
		
		//Liste des équations
		$equations = '';
		if(!empty($this->equations)) {
			foreach ($this->equations as $indice=>$equation) {
			    $nb_records = (!empty($equation['records']) && count($equation['records']) <= 100 ? count($equation['records']) : 0); 
				$equations .= "
				<div class='row'>
					<strong>".$equation['name']."</strong>
					<p><span style='margin-left:1em;'>=&gt; ".$equation['human_query']."</span>
                     ".($nb_records ? "<button class='bouton' onclick=\"bannette_diffusion_see_records(".$indice.")\">Voir les notices</button>" : "")."
                    </p>
				</div>";
				//Liste des notices par équation - 100 notices max
				if($nb_records) {
				    $elements_records_list_ui = new elements_records_list_ui($equation['records'], count($equation['records']), false);
				    $equations .= "
                    <div id='bannette_diffusion_equation_".$indice."_records' class='row' style='margin-left:3em;display:none;'>
                       <span>
                            ".$elements_records_list_ui->get_elements_list()."
                        </span>
                    </div>";
				}
			}
		}
		$display = str_replace("!!equations!!", $equations, $display);
		
		return $display;
	}
	
	public function save() {
	    $date = new \DateTime();
		$query = "INSERT INTO bannettes_diffusions SET
			diffusion_num_bannette = '".$this->num_bannette."',
			diffusion_mail_object = '".addslashes($this->mail_object)."',
			diffusion_mail_content = '".addslashes($this->mail_content)."',
			diffusion_records = '".encoding_normalize::json_encode($this->records)."',
			diffusion_deleted_records = '".encoding_normalize::json_encode($this->deleted_records)."',
			diffusion_recipients = '".encoding_normalize::json_encode($this->recipients)."',
			diffusion_failed_recipients = '".encoding_normalize::json_encode($this->failed_recipients)."',
			diffusion_equations = '".encoding_normalize::json_encode($this->equations)."',
			diffusion_date = '".$date->format('Y-m-d H:i:s')."'";
		pmb_mysql_query($query);
		$this->id = pmb_mysql_insert_id();
	}
	
	public function get_number_sent_mail() {
		return ($this->get_number_recipients()-$this->get_number_failed_recipients())." / ".$this->get_number_recipients();
	}
	
	public function get_diffusion_state($id_empr) {
		global $msg;
		
		if(in_array($id_empr, $this->recipients)) {
			if(!in_array($id_empr, $this->failed_recipients)) {
				return $msg['bannette_diffusion_state_sent'];
			}
		}
		return $msg['bannette_diffusion_state_no_sent'];
	}
	
	public static function delete($id) {
		$id = intval($id);
		$query = "DELETE FROM bannettes_diffusions WHERE id_diffusion = ".$id;
		pmb_mysql_query($query);
		return true;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_num_bannette() {
		return $this->num_bannette;
	}
	
	public function set_num_bannette($num_bannette) {
		$this->num_bannette = $num_bannette;
		return $this;
	}
	
	public function get_mail_object() {
		return $this->mail_object;
	}
	
	public function set_mail_object($mail_object) {
		$this->mail_object = $mail_object;
		return $this;
	}
	
	public function get_mail_content() {
		return $this->mail_content;
	}
	
	public function set_mail_content($mail_content) {
		$this->mail_content = $mail_content;
		return $this;
	}
	
	public function get_date() {
		return $this->date;
	}
	
	public function get_records() {
		return $this->records;
	}
	
	public function set_records($records) {
		$this->records = $records;
		return $this;
	}
	
	public function add_record($record=0) {
		$this->records[] = $record;
	}
	
	public function get_number_records() {
		return count($this->records);
	}
	
	public function get_deleted_records() {
		return $this->deleted_records;
	}
	
	public function set_deleted_records($deleted_records) {
		$this->deleted_records = $deleted_records;
		return $this;
	}
	
	public function add_deleted_records($records=array(), $pattern='') {
		foreach ($records as $record) {
			$this->deleted_records[$record] = $pattern;
		}
	}
	
	public function get_number_deleted_records($pattern='') {
		return count($this->deleted_records);
	}
	
	public function get_recipients() {
		return $this->recipients;
	}
	
	public function set_recipients($recipients) {
		$this->recipients = $recipients;
		return $this;
	}
	
	public function add_recipient($recipient) {
		$this->recipients[] = $recipient;
	}
	
	public function get_number_recipients() {
		return count($this->recipients);
	}
	
	public function get_failed_recipients() {
		return $this->failed_recipients;
	}
	
	public function set_failed_recipients($failed_recipients) {
		$this->failed_recipients = $failed_recipients;
		return $this;
	}
	
	public function add_failed_recipient($failed_recipient) {
		$this->failed_recipients[] = $failed_recipient;
	}
	
	public function get_number_failed_recipients() {
		return count($this->failed_recipients);
	}
	
	public function get_equations() {
		return $this->equations;
	}
	
	public function set_equations($equations) {
		$this->equations = $equations;
		return $this;
	}
	
	public function add_equation($equation) {
		$this->equations[] = $equation;
	}
	
	public function get_bannette() {
		if(!isset($this->bannette)) {
			$this->bannette = new bannette($this->num_bannette);
		}
		return $this->bannette;
	}
}
