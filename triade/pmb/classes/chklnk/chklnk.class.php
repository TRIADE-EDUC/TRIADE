<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: chklnk.class.php,v 1.18.4.1 2025/03/17 08:15:42 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once ($include_path."/templates/chklnk.tpl.php");
require_once ($class_path."/caddie.class.php");
require_once ($class_path."/curl.class.php");
require_once ($class_path."/progress_bar.class.php");

require_once ($class_path."/chklnk/chklnk_records.class.php");
require_once ($class_path."/chklnk/chklnk_vign.class.php");
require_once ($class_path."/chklnk/chklnk_custom_fields.class.php");
require_once ($class_path."/chklnk/chklnk_enum.class.php");
require_once ($class_path."/chklnk/chklnk_bull.class.php");
require_once ($class_path."/chklnk/chklnk_authors.class.php");
require_once ($class_path."/chklnk/chklnk_publishers.class.php");
require_once ($class_path."/chklnk/chklnk_collections.class.php");
require_once ($class_path."/chklnk/chklnk_subcollections.class.php");
require_once ($class_path."/chklnk/chklnk_authorities_thumbnail.class.php");

class chklnk {

	protected $caddie_instance;

	protected $caddie_type;

	protected static $curl;

	protected static $progress_bar;

	protected static $queries;

	protected static $filtering_parameters;

	protected static $parameters;

	protected static $curl_timeout;

    public function __construct() {
    }

    public static function get_parameter($name) {
    	if(!isset(static::$parameters[$name])) {
    		static::$parameters[$name] = array(
    				'chk' => 1,
    				'ajt' => 0,
    				'idcaddie' => 0,
    		);
    	}
    	return static::$parameters[$name];
    }

    public static function init_filtering_parameters() {
    	static::$filtering_parameters = array(
    			'chkrestrict' => 1,
    			'caddies_noti' => array(),
    			'caddies_bull' => array(),
    			'caddies_expl' => array()
    	);
    }

    public static function init_parameters() {
    	static::$parameters = array(
    			'noti' => static::get_parameter('noti'),
    			'vign' => static::get_parameter('vign'),
    			'cp' => static::get_parameter('cp'),
    			'enum' => static::get_parameter('enum'),
    			'bull' => static::get_parameter('bull'),
    			'cp_etatcoll' => static::get_parameter('cp_etatcoll'),
    			'autaut' => static::get_parameter('autaut'),
    			'autpub' => static::get_parameter('autpub'),
    			'autcol' => static::get_parameter('autcol'),
    			'autsco' => static::get_parameter('autsco'),
    			'authorities_thumbnail' => static::get_parameter('authorities_thumbnail'),
    			'editorialcontentcp' => static::get_parameter('editorialcontentcp'),
    	);
    }

    public static function init_curl_timeout() {
    	global $pmb_curl_timeout;

    	static::$curl_timeout = (int) $pmb_curl_timeout;
    }

    public static function init_curl() {
    	global $chkcurltimeout;

    	@set_time_limit(0) ;
    	static::$curl = new Curl();

    	if(!isset(static::$curl_timeout)) {
    		static::init_curl_timeout();
    	}

    	$chkcurltimeout = intval($chkcurltimeout);
    	if($chkcurltimeout) {
    		static::$curl->timeout = $chkcurltimeout;
    	} elseif(static::$curl_timeout) {
    		static::$curl->timeout = static::$curl_timeout;
    	} else {
    		static::$curl->timeout = 5;
    	}
    	static::$curl->limit=1000;//Limite à 1Ko
    	pmb_mysql_query("set wait_timeout=3600");
    }

    public static function init_progress_bar() {
    	static::$progress_bar=new progress_bar();
    	static::$progress_bar->pas=10;
    }

    public static function init_queries() {
    	static::$queries['notice'] = array();
    	static::$queries['vign'] = array();
    	static::$queries['explnum_noti'] = array();
    	static::$queries['explnum_bull'] = array();
    	static::$queries['cp'] = array();
    	static::$queries['cp_etatcoll'] = array();

    	$requete_notice ="select notice_id as id, lien as link from notices !!JOIN!! where lien!='' and lien is not null";
    	$requete_vign ="select notice_id as id, thumbnail_url as link from notices !!JOIN!! where thumbnail_url!='' and thumbnail_url is not null";
    	$requete_explnum_noti = "select notice_id as id, notice_id, explnum_url as link, explnum_id from notices !!JOIN!! join explnum on explnum_notice=notice_id and explnum_notice != 0 where explnum_mimetype = 'URL'";
    	$requete_explnum_bull = "select bulletin_id as id, bulletin_id, concat(notices.tit1,' ',bulletin_numero,' ',date_date) as tit, explnum_url as link, explnum_id, notices.notice_id from notices join bulletins on notices.notice_id=bulletin_notice !!JOIN!! join explnum on explnum_bulletin=bulletin_id and explnum_bulletin != 0 where explnum_mimetype = 'URL'";
    	$requete_cp = "select distinct notice_id as id from notices join notices_custom_values on notice_id = notices_custom_origine join notices_custom on idchamp = notices_custom_champ !!JOIN!! where type in ('url','resolve')";
    	$requete_cp_etatcoll = "select distinct notice_id, collstate_id from notices join collections_state on id_serial = notice_id join collstate_custom_values on collstate_id = collstate_custom_origine join collstate_custom on idchamp = collstate_custom_champ !!JOIN!! where type in ('url','resolve')";

    	//on s'occupe des restrictions
    	if(static::$filtering_parameters['chkrestrict']){
    		//pour les paniers de notice
    		if(is_array(static::$filtering_parameters['caddies_noti']) && count(static::$filtering_parameters['caddies_noti'])){
    			$paniers_ids = implode(",", static::$filtering_parameters['caddies_noti']);
    			//restriction aux notices des paniers
    			$limit_noti = "join caddie_content as c1 on c1.caddie_id in ($paniers_ids) and notice_id = c1.object_id";
    			//restriction aux bulletins des notices de bulletins des paniers
    			$limit_noti_bull = "join notices as n1 on n1.niveau_biblio = 'b' and n1.niveau_hierar = '2' and num_notice = n1.notice_id join caddie_content as c2 on n1.notice_id = c2.object_id and c2.caddie_id in ($paniers_ids)";

    			static::$queries['notice'][] =str_replace("!!JOIN!!",$limit_noti,$requete_notice);
    			static::$queries['vign'][] =str_replace("!!JOIN!!",$limit_noti,$requete_vign);
    			static::$queries['explnum_noti'][]= str_replace("!!JOIN!!",$limit_noti,$requete_explnum_noti);
    			static::$queries['explnum_bull'][]=str_replace("!!JOIN!!",$limit_noti_bull,$requete_explnum_bull);
    			static::$queries['cp'][] = str_replace("!!JOIN!!",$limit_noti,$requete_cp);
    			static::$queries['cp_etatcoll'][] = str_replace("!!JOIN!!",$limit_noti,$requete_cp_etatcoll);
    		}
    		//pour les paniers de bulletins
    		if(is_array(static::$filtering_parameters['caddies_bull']) && count(static::$filtering_parameters['caddies_bull'])){
    			$paniers_ids = implode(",",static::$filtering_parameters['caddies_bull']);
    			//restriction aux bulletins du paniers
    			$limit_bull = "join caddie_content as c3 on c3.caddie_id in ($paniers_ids) and bulletin_id = c3.object_id";
    			//restriction aux notices de bulletins associées aux bulletins des paniers
    			$limit_bull_noti = "join bulletins as b1 on b1.num_notice = notice_id join caddie_content as c4 on c4.caddie_id in ($paniers_ids) and c4.object_id = b1.bulletin_id";

    			static::$queries['notice'][] =str_replace("!!JOIN!!",$limit_bull_noti,$requete_notice);
    			static::$queries['vign'][] =str_replace("!!JOIN!!",$limit_bull_noti,$requete_vign);
    			static::$queries['explnum_noti'][]= str_replace("!!JOIN!!",$limit_bull_noti,$requete_explnum_noti);
    			static::$queries['explnum_bull'][]=str_replace("!!JOIN!!",$limit_bull,$requete_explnum_bull);
    			static::$queries['cp'][] = str_replace("!!JOIN!!",$limit_noti,$requete_cp);
    		}
    		//pour les paniers d'exemplaires
    		if(is_array(static::$filtering_parameters['caddies_expl']) && count(static::$filtering_parameters['caddies_expl'])){
    			$paniers_ids = implode(",",static::$filtering_parameters['caddies_expl']);
    			//restriction aux notices associées au exemplaires des paniers
    			$limit_expl_noti = "join exemplaires as e1 on e1.expl_notice = notice_id and e1.expl_notice != 0 join caddie_content as c5 on c5.caddie_id in ($paniers_ids) and e1.expl_id = c5.object_id";
    			//restrictions aux bulletin associés au exemplaires des paniers
    			$limit_expl_bull = "join exemplaires as e2 on e2.expl_bulletin = bulletin_id join caddie_content as c6 on c6.caddie_id in ($paniers_ids) and e2.expl_id = c6.object_id";
    			//restriction aux notices de bulletins associées aux bulletins dont les exemplaires sont dans le paniers
    			$limit_expl_bull_noti ="join bulletins as b2 on b2.num_notice = notice_id join exemplaires as e3 on e3.expl_bulletin = b2.bulletin_id join caddie_content as c7 on c7.caddie_id in ($paniers_ids) and e3.expl_id = c7.object_id";

    			static::$queries['notice'][] =str_replace("!!JOIN!!",$limit_expl_noti,$requete_notice);
    			static::$queries['notice'][] =str_replace("!!JOIN!!",$limit_expl_bull_noti,$requete_notice);
    			static::$queries['vign'][] =str_replace("!!JOIN!!",$limit_expl_noti,$requete_vign);
    			static::$queries['vign'][] =str_replace("!!JOIN!!",$limit_expl_bull_noti,$requete_vign);
    			static::$queries['explnum_noti'][]= str_replace("!!JOIN!!",$limit_expl_noti,$requete_explnum_noti);
    			static::$queries['explnum_bull'][]=str_replace("!!JOIN!!",$limit_expl_bull,$requete_explnum_bull);
    			static::$queries['cp'][] =str_replace("!!JOIN!!",$limit_expl_noti,$requete_cp);
    			static::$queries['cp'][] =str_replace("!!JOIN!!",$limit_expl_bull_noti,$requete_cp);
    		}
    	}else{
    		//si on a pas restreint par panier,
    		static::$queries['notice'][] =str_replace("!!JOIN!!","",$requete_notice);
    		static::$queries['vign'][] =str_replace("!!JOIN!!","",$requete_vign);
    		static::$queries['explnum_noti'][]= str_replace("!!JOIN!!","",$requete_explnum_noti);
    		static::$queries['explnum_bull'][]=str_replace("!!JOIN!!","",$requete_explnum_bull);
    		static::$queries['cp'][] = str_replace("!!JOIN!!","",$requete_cp);
    		static::$queries['cp_etatcoll'][] = str_replace("!!JOIN!!","",$requete_cp_etatcoll);
    	}
    }

    public static function proceed_parameter($name, $class_name, $caddie_type='NOTI') {
    	if (static::$parameters[$name]['chk']) {
    		$class_name_instance = new $class_name();
    		if (static::$parameters[$name]['ajt']) {
    			$caddie_instance = caddie_root::get_instance_from_object_type($caddie_type, static::$parameters[$name]['idcaddie']);
    			$class_name_instance->set_caddie_instance($caddie_instance);
    			$class_name_instance->set_caddie_type($caddie_type);
    		}
    		$class_name_instance->process();
    	}
    }

    public static function proceed_custom_field_parameter($name, $sub_type, $caddie_type='NOTI') {
    	if (static::$parameters[$name]['chk']) {
    		$chklnk_custom_fields = new chklnk_custom_fields();
    		$chklnk_custom_fields->set_sub_type($sub_type);
    		if (static::$parameters[$name]['ajt']) {
    			$caddie_instance = caddie_root::get_instance_from_object_type($caddie_type, static::$parameters[$name]['idcaddie']);
    			$chklnk_custom_fields->set_caddie_instance($caddie_instance);
    			$chklnk_custom_fields->set_caddie_type($caddie_type);
    		}
    		$chklnk_custom_fields->process();
    	}
    }

    public static function proceed() {
    	global $cms_active;

    	static::proceed_parameter('noti', 'chklnk_records');

    	static::proceed_parameter('vign', 'chklnk_vign');

    	static::proceed_custom_field_parameter('cp', 'notices');

    	static::proceed_parameter('enum', 'chklnk_enum');

		static::proceed_parameter('bull', 'chklnk_bull', 'BULL');

		static::proceed_custom_field_parameter('cp_etatcoll', 'collstate');

		static::proceed_parameter('autaut', 'chklnk_authors', 'AUTHORS');

		static::proceed_parameter('autpub', 'chklnk_publishers', 'PUBLISHERS');

		static::proceed_parameter('autcol', 'chklnk_collections', 'COLLECTIONS');

		static::proceed_parameter('autsco', 'chklnk_subcollections', 'SUBCOLLECTIONS');

		static::proceed_parameter('authorities_thumbnail', 'chklnk_authorities_thumbnail', 'MIXED');

		if($cms_active && static::$parameters['editorialcontentcp']['chk']){
			$chklnk_custom_fields = new chklnk_custom_fields();
			$chklnk_custom_fields->set_sub_type('cms_editorial');
			$chklnk_custom_fields->process();
		}
    }

    protected function get_query_caddie($type='NOTI') {
    	global $PMBuserid;

    	$instance = caddie_root::get_instance_from_object_type($type);
    	return "SELECT ".$instance::$field_name.", name FROM ".$instance::$table_name." where type='".$type."' and (autorisations='$PMBuserid' or autorisations like '$PMBuserid %' or autorisations like '% $PMBuserid %' or autorisations like '% $PMBuserid') order by name ";
    }

    protected function get_filtering_selector($name, $type='NOTI') {
    	$restrict_selector="<select name=\"filtering_parameters[".$name."][]\" id=\"filtering_parameters_".$name."\" multiple size='10'>";
    	$result = pmb_mysql_query($this->get_query_caddie($type));
    	if(pmb_mysql_num_rows($result)) {
    		while($row = pmb_mysql_fetch_object($result)) {
    			$restrict_selector .= "<option value='".$row->idcaddie."' ".(in_array($row->idcaddie, static::$filtering_parameters[$name]) ? "selected='selected'" : "")." >".$row->name."</option>";
    		}
    	}
    	$restrict_selector .= "</select>";
    	return $restrict_selector;
    }

    protected function get_selector($name, $type='NOTI') {
    	if(!isset(static::$parameters[$name]['idcaddie'])) static::$parameters[$name]['idcaddie'] = 0;
    	return gen_liste ($this->get_query_caddie($type), "idcaddie", "name", "parameters[".$name."][idcaddie]", "", static::$parameters[$name]['idcaddie'], "", "","","",0);
    }

    public static function set_filtering_parameters($filtering_parameters) {
    	if(!isset(static::$filtering_parameters)) {
    		static::init_filtering_parameters();
    	}
    	if(!empty($filtering_parameters)) {
	    	foreach($filtering_parameters as $key=>$value) {
	    		static::$filtering_parameters[$key] = (isset($filtering_parameters[$key]) && $filtering_parameters[$key] ? $filtering_parameters[$key] : 0);
	    	}
    	}
    }

    public static function set_parameters($parameters) {
    	if(!isset(static::$parameters)) {
    		static::init_parameters();
    	}
    	foreach(static::$parameters as $name=>$parameter) {
    		foreach($parameter as $key=>$value) {
    			static::$parameters[$name][$key] = (isset($parameters[$name][$key]) && $parameters[$name][$key] ? $parameters[$name][$key] : 0);
    		}
    	}
    }

    public static function set_curl_timeout($curl_timeout) {
        static::$curl_timeout = (int) $curl_timeout;
    }

    protected function get_checkbox_checking_input_form($property) {
    	global $msg;

    	if(!isset(static::$parameters[$property]['chk'])) static::$parameters[$property]['chk'] = 1;
    	return "
    		<input type='checkbox' ".(static::$parameters[$property]['chk'] ? "checked='checked'" : "")." id='parameters_".$property."_chk' name='parameters[".$property."][chk]' value='1'>
			&nbsp;<label class='etiquette' for='parameters_".$property."_chk'>".$msg['chklnk_chk_'.$property]."</label>&nbsp;";
    }

    protected function get_checkbox_adding_form($property, $type) {
    	global $msg;

    	if(!isset(static::$parameters[$property]['ajt'])) static::$parameters[$property]['ajt'] = 0;
    	return "
    		<blockquote>
				<input type='checkbox' id='parameters_".$property."_ajt' name='parameters[".$property."][ajt]' value='1' ".(static::$parameters[$property]['ajt'] ? "checked='checked'" : "").">
				&nbsp;<label for='parameters_".$property."_ajt' style='all:unset'>".$msg['chklnk_choix_caddie_'.$property]."</label>
		        ".$this->get_selector($property, $type)."
			</blockquote>";
    }

    protected function get_line_content($property, $type='NOTI') {
    	return "
    		<div class='row'>
				".$this->get_checkbox_checking_input_form($property)."
				".$this->get_checkbox_adding_form($property, $type)."
			</div>";
    }

    public function get_content_form() {
    	global $admin_chklnk_content_form;

    	$form = $admin_chklnk_content_form;
    	$form = str_replace('!!chkrestrict!!', (static::$filtering_parameters['chkrestrict'] ? "checked='checked'" : ""), $form);
    	$form = str_replace('!!restrict_by_basket_noti!!', $this->get_filtering_selector('caddies_noti'), $form);
    	$form = str_replace('!!restrict_by_basket_bull!!', $this->get_filtering_selector('caddies_bull', 'BULL'), $form);
    	$form = str_replace('!!restrict_by_basket_expl!!', $this->get_filtering_selector('caddies_expl', 'EXPL'), $form);

    	$records_content = $this->get_line_content('noti');
    	$records_content .= $this->get_line_content('vign');
    	$records_content .= $this->get_line_content('cp');
    	$records_content .= $this->get_line_content('enum');
    	$records_content .= $this->get_line_content('bull', 'BULL');
    	$records_content .= $this->get_line_content('cp_etatcoll');

    	$form = str_replace('!!records_content!!', $records_content, $form);

    	$authorities_content = $this->get_line_content('autaut', 'AUTHORS');
    	$authorities_content .= $this->get_line_content('autpub', 'PUBLISHERS');
    	$authorities_content .= $this->get_line_content('autcol', 'COLLECTIONS');
    	$authorities_content .= $this->get_line_content('autsco', 'SUBCOLLECTIONS');

    	$authorities_content .= $this->get_line_content('authorities_thumbnail', 'MIXED');

    	$form = str_replace('!!authorities_content!!', $authorities_content, $form);

    	if(!isset(static::$curl_timeout)) {
    		static::init_curl_timeout();
    	}
    	$form = str_replace('!!pmb_curl_timeout!!', static::$curl_timeout, $form);

    	return $form;
    }

    public function get_form() {
    	global $admin_chklnk_form;

    	$form = $admin_chklnk_form;
    	$form = str_replace('!!chklnk_content_form!!', $this->get_content_form(), $form);
    	return $form;
    }

    protected function get_title() {
    	return '';
    }

    protected function get_query() {
    	return '';
    }

    protected function get_label_progress_bar() {
    	return '';
    }

    protected function get_element_display($element, $error='') {
    	return "<div class='row'><a href=\"".$this->get_element_edit_link($element)."\" target=\"_blank\">".$this->get_element_label($element)."</a>&nbsp;<a href=\"".$element->link."\" target=\"_blank\">".$element->link."</a> <span class='erreur'>".$error."</span></div>";
    }

    public function check_link($element) {
    	global $msg;

    	$message = '';
    	if(!isset(static::$curl)) {
    		static::init_curl();
    	}
    	$response = static::$curl->get($element->link);
    	if (!$response) {
    		$message .= $this->get_element_display($element, static::$curl->error);
    		if (isset($this->caddie_instance) && $this->caddie_instance->get_idcaddie()) {
    			$this->caddie_instance->add_item($element->id,$this->caddie_type);
    		}
    	} else {
    		$response_status = substr($response->headers['Status-Code'], 0, 1);
    		if ($response_status != '2' && $response_status != '3') {
	    		if($response->headers['Status-Code']){
	    			$tmp=static::$curl->reponsecurl[$response->headers['Status-Code']];
	    		}else{
	    			$tmp=$msg["curl_no_status_code"];
	    		}
	    		$message .= $this->get_element_display($element, $response->headers['Status-Code']." -&gt; ".$tmp);
	    		if (isset($this->caddie_instance) && $this->caddie_instance->get_idcaddie()) {
	    			$this->caddie_instance->add_item($element->id,$this->caddie_type);
	    		}
    		}
    	}
    	return $message;
    }

    protected function process_element($element) {
    	return $this->check_link($element);
    }

    public function process() {
    	global $msg;

    	print "
    		<div class='row'>
    			<hr />
    		</div>
    		<div class='row'>
    			<label class='etiquette' >".$this->get_title()."</label>
    			".(isset($this->caddie_instance) ? "&nbsp;".(($this->caddie_instance->name != "") ? $msg['chklnk_caddie_destination']:'')."<a href=\"./catalog.php?categ=caddie&sub=gestion&quoi=panier&action=&object_type=".$this->caddie_type."&idcaddie=".$this->caddie_instance->get_idcaddie()."\">".$this->caddie_instance->name."</a>" : '')."
    		</div>
			<div class='row'>";

    	$query = $this->get_query();
    	$result = pmb_mysql_query($query);

    	static::$progress_bar->count = pmb_mysql_num_rows($result);
    	static::$progress_bar->nb_progress_call=0;
    	static::$progress_bar->set_text($this->get_label_progress_bar());

    	if ($result && static::$progress_bar->count) {
    		while ($row = pmb_mysql_fetch_object($result)) {
    			print $this->process_element($row);
    			static::$progress_bar->progress();
    			flush();
    		}
    	} else {
    		static::$progress_bar->set_percent(100);
    	}
    	print "</div>";
    	flush();
    }

    public function set_caddie_instance($caddie_instance) {
    	$this->caddie_instance = $caddie_instance;
    }

    public function set_caddie_type($caddie_type) {
    	$this->caddie_type = $caddie_type;
    }

    public static function update_curl_timeout_parameter() {
    	global $pmb_curl_timeout;

    	if(!isset(static::$curl)) {
    		static::init_curl();
    	}
    	if (static::$curl->timeout != $pmb_curl_timeout) {
    		$query = "update parametres set valeur_param='".static::$curl->timeout."' where type_param='pmb' and sstype_param='curl_timeout'";
    		pmb_mysql_query($query);
    	}
    }

    public function process_scheduler() {
    	$response = array(
    			'title' => $this->get_title(),
    			'links' => array()
    	);
    	$query = $this->get_query();
    	$result = pmb_mysql_query($query);
    	if ($result) {
    		while ($row = pmb_mysql_fetch_object($result)) {
    			$message = $this->process_element($row);
    			if($message) {
    				$response['links'][] = strip_tags($message, '<a><span>');
    			}
    		}
    	}
    	return $response;
    }

	protected function get_element_edit_link($element)
	{}

	protected function get_element_label($element)
	{}
}
