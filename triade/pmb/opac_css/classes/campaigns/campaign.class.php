<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: campaign.class.php,v 1.9 2023/11/14 17:01:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/campaigns/campaign_proxy.class.php");
require_once($class_path."/campaigns/campaign_recipients.class.php");
require_once($include_path."/templates/campaigns/campaign.tpl.php");
require_once($class_path."/campaigns/views/campaign_view.class.php");

class campaign {
	
	/**
	 * Identifiant de la campagne
	 * @var integer
	 */
	protected $id;
	
	/**
	 * Type
	 * @var string
	 */
	protected $type;
	
	/**
	 * Libellé de la campagne
	 * @var string
	 */
	protected $label;
	
	/**
	 * Date
	 * @var DateTime
	 */
	protected $date;
	
	/**
	 * Date formatée
	 * @var string
	 */
	protected $formatted_date;
	
	/**
	 * Utilisateur PMB associé
	 * @var integer
	 */
	protected $num_user;
	
	/**
	 * Descripteurs / Catégories
	 * @var array
	 */
	protected $descriptors;
	
	/**
	 * Tags / Mots-clés
	 * @var array
	 */
	protected $tags;
	
	/**
	 * 
	 * @var campaign_recipients $recipients
	 */
	protected $recipients;
	
	protected $campaign_view;
	
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_data();
	}
	
	/**
	 * Data
	 */
	protected function fetch_data() {
		global $PMBuserid;
		
		$this->type = '';
		$this->label = '';
		$this->date = date('Y-m-d H:i:s');
		$this->formatted_date = formatdate($this->date);
		$this->num_user = $PMBuserid;
		if ($this->id) {
			$query = 'select * from campaigns where id_campaign = '.$this->id;
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->type = $row->campaign_type;
				$this->label = $row->campaign_label;
				$this->date = $row->campaign_date;
				$this->formatted_date = formatdate($this->date, 1);
				$this->num_user = $row->campaign_num_user;
			}
		}
	}
	
	/**
	 * Formulaire
	 */
	public function get_form(){
		global $campaign_form_tpl;
		
		$form = $campaign_form_tpl;
		$form = str_replace("!!button_delete!!",'',$form);
		$form = str_replace("!!id!!",$this->id,$form);
		return $form;
	}

	/**
	 * Provenance du formulaire
	 */
	public function set_properties_from_form(){
		global $campaign_label;
		
		$this->label = stripslashes($campaign_label);
	}

	/**
	 * Sauvegarde
	 */
	public function save(){
		if($this->id) {
			$query = 'update campaigns set ';
			$fields_in_create = '';
			$where = 'where id_campaign= '.$this->id;
		} else {
			$query = 'insert into campaigns set ';
			$fields_in_create = '
					campaign_date = "'.$this->date.'",
			';
			$where = '';
		}
		$query .= $fields_in_create;
		$query .= '
				campaign_type = "'.addslashes($this->type).'",
				campaign_label = "'.addslashes($this->label).'",
				campaign_num_user = "'.$this->num_user.'"
				'.$where;
		$result = pmb_mysql_query($query);
		if($result) {
			if(!$this->id) {
				$this->id = pmb_mysql_insert_id();
			}
			$this->save_descriptors();
			$this->save_tags();
			return true;
		} else {
			return false;
		}
	}
	
	public function save_descriptors() {
		static::delete_descriptors($this->id);
		if (!empty($this->descriptors)){
    		for($i=0 ; $i<count($this->descriptors) ; $i++){
    			$rqt = "insert into campaigns_descriptors set num_campaign = '".$this->id."', num_noeud = '".$this->descriptors[$i]."', campaign_descriptor_order='".$i."'";
    			pmb_mysql_query($rqt);
    		}
		}
	}
	
	public function save_tags() {
		static::delete_tags($this->id);
		if (!empty($this->tags)){
    		for($i=0 ; $i<count($this->tags) ; $i++){
    			$rqt = "insert into campaigns_tags set num_campaign = '".$this->id."', num_tag = '".$this->tags[$i]."', campaign_tag_order='".$i."'";
    			pmb_mysql_query($rqt);
    		}
		}
	}
	
	public function send_mail($id_empr, $to_nom="", $to_mail="", $obj="", $corps="", $from_name="", $from_mail="", $headers="", $copie_CC="", $copie_BCC="", $faire_nl2br=0, $pieces_jointes=array(), $reply_name="", $reply_mail="", $is_mailing=false, $type="") {
		$campaign_recipients = $this->get_recipients();
		$recipient_instance = $campaign_recipients->add($id_empr, $to_mail, $corps);
		
		$corps = campaign_proxy::proxyfication($recipient_instance, $corps);
	
		return mailpmb($to_nom, $to_mail, $obj, $corps, $from_name, $from_mail, $headers, $copie_CC, $copie_BCC, $faire_nl2br, $pieces_jointes, $reply_name, $reply_mail, $is_mailing, $type, $this->get_id());
	}
	
	/**
	 * Suppression
	 */
	public static function delete($id){
		$id = intval($id); 
		if($id) {
			static::delete_descriptors($id);
			static::delete_tags($id);
			$query = "delete from campaigns where id_campaign = ".$id;
			pmb_mysql_query($query);
			return true;
		}
		return false;
	}
	
	public static function delete_descriptors($id) {
		$id = intval($id);
		$query = "delete from campaigns_descriptors where num_campaign = '".$id."'";
		pmb_mysql_query($query);
	}
	
	public static function delete_tags($id) {
		$id = intval($id);
		$query = "delete from campaigns_tags where num_campaign = '".$id."'";
		pmb_mysql_query($query);
	}
	
	public function get_id() {
		return $this->id;
	}

	public function get_type() {
		return $this->type;
	}
	
	public function get_label() {
		return $this->label;
	}

	public function get_date() {
		return $this->date;
	}
	
	public function get_formatted_date() {
		return $this->formatted_date;
	}
	
	public function get_dated_label() {
		return $this->label."<br/>(".$this->formatted_date.")";
	}
	
	public function get_descriptors() {
		if(!isset($this->descriptors)) {
			// les descripteurs...
			$rqt = "select num_noeud from campaigns_descriptors where num_campaign = '".$this->id."' order by campaign_descriptor_order";
			$res = pmb_mysql_query($rqt);
			if(pmb_mysql_num_rows($res)){
				while($row = pmb_mysql_fetch_assoc($res)){
					$this->descriptors[] = $row['num_noeud'];
				}
			}
		}
		return $this->descriptors;
	}
	
	public function get_tags() {
		if(!isset($this->tags)) {
			$this->tags=array();
			$rqt = "select num_tag from campaigns_tags where num_campaign = '".$this->id."' order by campaign_tag_order";
			$res = pmb_mysql_query($rqt);
			if(pmb_mysql_num_rows($res)){
				while($row = pmb_mysql_fetch_assoc($res)){
					$this->tags[] = $row['num_tag'];
				}
			}
		}
		return $this->tags;
	}
	
	/**
	 * 
	 * @return campaign_recipients
	 */
	public function get_recipients() {
		if(!isset($this->recipients)) {
			$this->recipients = new campaign_recipients($this->id);
		}
		return $this->recipients;
	}
	
	public function get_view() {
		global $charset;
		global $campaign_view_tpl;
		
		$view = $campaign_view_tpl;
		$view = str_replace("!!title!!", htmlentities($this->label, ENT_QUOTES, $charset)." (".$this->get_formatted_date().")", $view);
		
		$campaign_view = new campaign_view($this->id);
		
		$content_view = "
		<div class='row'>
			<div class='campaign_view_box'>
				<span class='campaign_view_recipients_number'>
					".$campaign_view->get_recipients_number()."
				</span>
			</div>
			<div class='campaign_view_box'>
				<span class='campaign_view_opening_rate'>
					".$campaign_view->get_opening_rate()."
				</span>
			</div>
			<div class='campaign_view_box'>
				<span class='campaign_view_clicks_rate'>
					".$campaign_view->get_clicks_rate()."
				</span>
			</div>
			<div class='campaign_view_box'>
				<span class='campaign_view_no_email_sent'>
					".$campaign_view->get_no_email_sent_number()."
				</span>
			</div>
		</div>
		<div class='row'>";
		
		//Affichage de l'histogramme d'ouverture et de clics par jours
		$content_view .= "
			<div class='campaign_view_graph'>
				".$campaign_view->get_instance('ClusteredColumns')->get_opening_and_clicks_by_days(date('Y-m-d', strtotime($this->date)), date('Y-m-d'))."
			</div>";
		
		//Affichage du nombre d'ouverture et de clics par jours
		$content_view .= "
			<div class='campaign_view_graph'>
				".$campaign_view->get_instance('Lines')->get_opening_and_clicks_by_hours(date('Y-m-d', strtotime($this->date)), date('Y-m-d'))."
			</div>";
		
		$content_view .= "
		</div>
		<div class='row'>";
		
		//Affichage de l'histogramme de clics par lien
		$content_view .= "
			<div class='campaign_view_graph'>
				".$campaign_view->get_instance('Columns')->get_clicks_by_links()."
			</div>";

		//Affichage du nombre de clics par type de lien
		$content_view .= "
			<div class='campaign_view_graph'>
				".$campaign_view->get_instance('Pie')->get_clicks_by_links_type()."
			</div>";
		
		$content_view .= "
		</div>
		<div class='row'>";
		
		//Affichage du nombre d'ouverture par localisation
		$content_view .= "
			<div class='campaign_view_graph'>
				".$campaign_view->get_instance('Pie')->get_opening_by_recipients('location')."
			</div>";
		
		//Affichage du nombre d'ouverture par catégorie
		$content_view .= "
			<div class='campaign_view_graph'>
				".$campaign_view->get_instance('Pie')->get_opening_by_recipients('categ')."
			</div>";
		
		$content_view .= "
		</div>";
		
		$view = str_replace("!!content_view!!", $content_view, $view);
		$view = str_replace("!!id!!", $this->id, $view);
		
		return $view;
	}
	
	public function set_id($id) {
		$this->id = intval($id);
	}
	
	public function set_type($type) {
		$this->type = $type;
	}
	
	public function set_label($label) {
		$this->label = $label;
	}
	
	public function set_descriptors($descriptors) {
		$this->descriptors = $descriptors;
	}
	
	public function set_tags($tags) {
		$this->tags = $tags;
	}
	
	public function get_campaign_view() {
		if(!isset($this->campaign_view)) {
			$this->campaign_view = new campaign_view($this->id);
		}
		return $this->campaign_view;
	}
}