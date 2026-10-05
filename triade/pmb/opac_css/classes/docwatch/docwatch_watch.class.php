<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_watch.class.php,v 1.19.4.1 2025/04/29 09:37:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/docwatch/docwatch_root.class.php");
// require_once($class_path."/docwatch/docwatch_category.class.php");
// require_once($class_path."/docwatch/datasources/docwatch_datasource.class.php");
// require_once($class_path."/docwatch/datasources/docwatch_datasource_notices.class.php");
// require_once($class_path."/docwatch/datasources/docwatch_datasource_notices_rss.class.php");
// require_once($class_path."/docwatch/datasources/docwatch_datasource_articles.class.php");
// require_once($class_path."/docwatch/datasources/docwatch_datasource_sections.class.php");
// require_once($class_path."/docwatch/datasources/docwatch_datasource_rss.class.php");
// require_once($class_path."/docwatch/docwatch_item.class.php");

/**
 * class docwatch_watch
 *
 */
class docwatch_watch extends docwatch_root{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * Identifiant de la veille dans la base de données
	 *
	 */
	protected $id;

	/**
	 * Nom de la veille
	 * @access protected
	 */
	protected $title;

	/**
	 * Date de dernier remplissage
	 * @access protected
	 */
	protected $last_date;

	/**
	 * Créateur de la veille. Attention, on ne met que les items notices visibles par
	 * le documentaliste s'ils viennent de PMB
	 * @access protected
	 */
	protected $owner;

	/**
	 * Tableau des utilisateurs autorisés
	 * @access protected
	 */
	protected $allowed_users;
	/**
	 *
	 * @access protected
	 */
	protected $num_category;

	/**
	 * Tableau des items contenus dans la veille
	 * @access protected
	 */
	protected $items;


	/**
	 * Temps de validité des items de la veille
	 * @access protected
	 */
	protected $ttl;

	/**
	 * Description de la source
	 * @access protected
	 */
	protected $desc;

	/**
	 * Url du logo de la source
	 * @access protected
	 */
	protected $logo_url;

	/**
	 * Tableau des sources de données autorisées
	 * @access protected
	 */
	protected $datasources = array();

	/**
	 * Tableau des instances de sources de données autorisées
	 * @access protected
	 */
	protected $datasources_objects = array();

	/**
	 * Tableau des parametre de la veille
	 * @access protected
	 */
	protected $parameters = array();

	/**
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_datas();
	} // end of member function __construct


	/**
	 * Fetch datas
	 *
	 */
	public function fetch_datas(){
		$this->title = "";
		$this->last_date = "0000-00-00 00:00:00";
		$this->num_category = 0;
		$this->allowed_users = array();
		$this->owner = 0;
		$this->ttl = 0;
		$this->desc = "";
		$this->logo_url = "";
		$this->parameters = array();
		$this->watch_rss_link = "";
		$this->watch_rss_lang = "";
		$this->watch_rss_copyright = "";
		$this->watch_rss_editor = "";
		$this->watch_rss_webmaster = "";
		$this->watch_rss_image_title = "";
		$this->watch_rss_image_website = "";
		if($this->id){
			//Query
			$query = "select * from docwatch_watches where id_watch = '".$this->id."'";
			$result=pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->title = $row->watch_title;
				$this->last_date = $row->watch_last_date;
				$this->num_category = $row->watch_num_category;
				$this->allowed_users = explode(",",$row->watch_allowed_users);
				$this->owner = $row->watch_owner;
				$this->ttl = $row->watch_ttl;
				$this->desc = $row->watch_desc;
				$this->logo_url = $row->watch_logo_url;
				$this->watch_rss_link = $row->watch_rss_link;
				$this->watch_rss_lang = $row->watch_rss_lang;
				$this->watch_rss_copyright = $row->watch_rss_copyright;
				$this->watch_rss_editor = $row->watch_rss_editor;
				$this->watch_rss_webmaster = $row->watch_rss_webmaster;
				$this->watch_rss_image_title = $row->watch_rss_image_title;
				$this->watch_rss_image_website = $row->watch_rss_image_website;

				$query = "select id_datasource, datasource_type from docwatch_datasources where datasource_num_watch = ".$this->id;
				$result = pmb_mysql_query($query);
				if($result && pmb_mysql_num_rows($result)){
					$this->datasources = array();
					while ($row=pmb_mysql_fetch_object($result)) {
						$this->datasources[intval($row->id_datasource)] = $row->datasource_type;
					}
				}
			}
		}
	}

	/**
	 * Renvoie le formulaire
	 *
	 */
	public function get_form(){
		return "";
	}

	/**
	 * Set les propriétés de l'instance depuis le formulaire
	 *
	 * @return void
	 * @access public
	 */
	public function set_from_form() {
		global $datasources_choice;
		global $docwatch_watch_title;
		global $docwatch_watch_owner;
		global $docwatch_watch_allowed_users;
		global $docwatch_watch_num_category;
		global $docwatch_watch_ttl;
		global $docwatch_watch_desc;
		global $docwatch_watch_logo_url;
		global $docwatch_watch_watch_rss_link;
		global $docwatch_watch_watch_rss_lang;
		global $docwatch_watch_watch_rss_copyright;
		global $docwatch_watch_watch_rss_editor;
		global $docwatch_watch_watch_rss_webmaster;
		global $docwatch_watch_watch_rss_image_title;
		global $docwatch_watch_watch_rss_image_website;

		if (is_array($datasources_choice) && count($datasources_choice)) {
			foreach ($datasources_choice as $datasource_choice) {
				$this->parameters['datasources'][] = $datasource_choice;
			}
		}

		$this->title = strip_tags(stripslashes($docwatch_watch_title));
		$this->owner = $docwatch_watch_owner;
		$this->allowed_users = $docwatch_watch_allowed_users;
		$this->num_category = $docwatch_watch_num_category;
		$this->ttl = $docwatch_watch_ttl;
		$this->desc = $docwatch_watch_desc;
		$this->logo_url = $docwatch_watch_logo_url;
		$this->watch_rss_link = $docwatch_watch_watch_rss_link;
		$this->watch_rss_lang = $docwatch_watch_watch_rss_lang;
		$this->watch_rss_copyright = $docwatch_watch_watch_rss_copyright;
		$this->watch_rss_editor = $docwatch_watch_watch_rss_editor;
		$this->watch_rss_webmaster = $docwatch_watch_watch_rss_webmaster;
		$this->watch_rss_image_title = $docwatch_watch_watch_rss_image_title;
		$this->watch_rss_image_website = $docwatch_watch_watch_rss_image_website;

	} // end of member function set_from_form

	/**
	 * Sauvegarde des propriétés
	 *
	 * @return void
	 * @access public
	 */
	public function save(){
		if($this->id){
			$query = "update docwatch_watches set ";
			$clause = " where id_watch = ".$this->id;
		}else{
			$query = "insert into docwatch_watches set ";
			$clause= "";
		}
		$query.= "
			watch_title = '".addslashes($this->title)."',
			watch_owner = '".$this->owner."',
			watch_allowed_users = '".implode(",", $this->allowed_users)."',
			watch_num_category = '".$this->num_category."',
			watch_last_date = now(),
			watch_ttl = '".$this->ttl."',
			watch_desc = '".addslashes($this->desc)."',
			watch_logo_url = '".$this->logo_url."',
			watch_rss_link = '".addslashes($this->watch_rss_link)."',
			watch_rss_lang = '".addslashes($this->watch_rss_lang)."',
			watch_rss_copyright = '".addslashes($this->watch_rss_copyright)."',
			watch_rss_editor = '".addslashes($this->watch_rss_editor)."',
			watch_rss_webmaster = '".addslashes($this->watch_rss_webmaster)."',
			watch_rss_image_title = '".addslashes($this->watch_rss_image_title)."',
			watch_rss_image_website = '".addslashes($this->watch_rss_image_website)."'
			".$clause;

		$result = pmb_mysql_query($query);
		if($result){
			if(!$this->id){
				$this->id = pmb_mysql_insert_id();
			}
			if($this->parameters['datasources']){
				foreach ($this->parameters['datasources'] as $datasource_type) {
					if (in_array($datasource_type, $this->datasources)) {
						$datasource_id = array_search($datasource_type,$this->datasources);
					} else {
						$datasource_id = 0;
					}
					$datasource = new $datasource_type($datasource_id);
					$datasource->set_num_watch($this->id);
					$result = $datasource->save();
					if($result){
						$this->datasources[$datasource->get_id()] = $datasource_type;
					}
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * Méthode de suppression
	*/
	public function delete(){
		if($this->id){
			//on commence par éliminer les sources de données et sélecteurs associés...
			$query = "select id_datasource from docwatch_datasources where datasource_num_watch = ".$this->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$query = "select id_selector from docwatch_selectors where selector_num_datasource = ".$row->id_datasource;
					$sel_result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($sel_result)){
						$query = "delete from docwatch_selectors where selector_num_datasource = ".$row->id_datasource;
						pmb_mysql_query($query);
					}
					$query = "delete from docwatch_datasources where datasource_num_watch = ".$this->id;
					pmb_mysql_query($query);
				}
			}
			//il faut ensuite éliminer les items..
			$query = "select id_item from docwatch_items where item_num_watch = ".$this->id;
			$result = pmb_mysql_query($query);
			if($result){
				while($row = pmb_mysql_fetch_object($result)){
					$docwatch_item = new docwatch_item($row->id_item);
					$docwatch_item->delete();
				}
			}
			$query = "delete from docwatch_watches where id_watch = ".$this->id;
			$result = pmb_mysql_query($query);
			if($result){
				return true;
			}else{
				return false;
			}
		}
	}

	/**
	 * Récupération des données de la source...
	*/
	public function get_datas(){

	}

	/**
	 * Retourne les sources de données associées
	 *
	 */
	public function get_datasources() {
		if(!count($this->datasources_objects)) {
			foreach($this->datasources as $id => $classe){
				$this->datasources_objects[] = new $classe($id);
			}
			//TODO: checkifisuptodate
		}
	}

	public function get_items() {
		return $this->items;
	}

	/**
	 * Renvoie le nombre d'items collectés
	 *
	 * @return int
	 * @access public
	 */
	public function fill_items( ) {
	} // end of member function fill_items

	/**
	 *
	 *
	 * @param int item_id Identifiant de l'item à  supprimer

	 * @return bool
	 * @access public
	 */
	public function del_item( $item_id ) {
	} // end of member function del_item

	/**
	 *
	 *
	 * @return bool
	 * @access public
	 */
	public function del_outdated( ) {
		$query = "select id_item from docwatch_items where date_add(item_added_date, INTERVAL ".$this->ttl." hour) < now() and item_num_watch = '".$this->id."'";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)){
				$item = new docwatch_item($row->id_item);
				$item->mark_as_deleted();
			}
			return true;
		}
		return false;
	} // end of member function del_outdated

	/**
	 * del_outdated + fill_items
	 *
	 * @return bool
	 * @access public
	 */
	public function sync( ) {
		$this->del_outdated();
		$this->get_datasources();
		foreach($this->datasources_objects as $datasource){
			if(!$datasource->get_is_up_to_date()){
				//TODO: Do update (with get new items)
				//la methode sync va appeler les mises a jour des items dans la datasource
				$datasource->sync($this->owner);
			}
		}
		$this->update_last_date();
	} // end of member function sync

	public function update_last_date(){
		$this->set_last_date(date("Y-m-d H:i:s"));
		$query = "update docwatch_watches set watch_last_date = '".$this->last_date."' where id_watch = '".$this->id."'";
		if(!pmb_mysql_query($query)){
			return false;
		}
		return true;
	}

	public function get_id() {
		return $this->id;
	}

	public function set_id($id) {
		$this->id = intval($id);
	}

	public function get_title() {
		return $this->title;
	}

	public function set_title($title) {
		$this->title = $title;
	}

	public function get_last_date() {
		return $this->last_date;
	}

	public function set_last_date($last_date) {
		$this->last_date = $last_date;
	}

	public function get_owner() {
		return $this->owner;
	}

	public function set_owner($owner) {
		$this->owner = intval($owner);
	}

	public function get_allowed_users() {
		return $this->allowed_users;
	}

	public function set_allowed_users($allowed_users) {
		foreach ($allowed_users as $key => $value){
			$allowed_users[$key] = intval($value);
		}
		$this->allowed_users = $allowed_users;
	}

	public function get_num_category() {
		return $this->num_category;
	}

	public function set_num_category($num_category) {
		$this->num_category = intval($num_category);
	}

	public function set_items($items) {
		$this->items = $items;
	}

	public function get_ttl() {
		return $this->ttl;
	}

	public function set_ttl($ttl) {
		$this->ttl = intval($ttl);
	}

	public function get_desc() {
		return $this->desc;
	}

	public function set_desc($desc) {
		$this->desc = $desc;
	}

	public function get_logo_url() {
		return $this->logo_url;
	}

	public function set_logo_url($logo_url) {
		$this->logo_url = $logo_url;
	}

	public function get_watch_rss_link() {
		return $this->watch_rss_link;
	}

	public function set_watch_rss_link($watch_rss_link) {
		$this->watch_rss_link = $watch_rss_link;
	}

	public function get_watch_rss_lang() {
		return $this->watch_rss_lang;
	}

	public function set_watch_rss_lang($watch_rss_lang) {
		$this->watch_rss_lang = $watch_rss_lang;
	}

	public function get_watch_rss_copyright() {
		return $this->watch_rss_copyright;
	}

	public function set_watch_rss_copyright($watch_rss_copyright) {
		$this->watch_rss_copyright = $watch_rss_copyright;
	}

	public function get_watch_rss_editor() {
		return $this->watch_rss_editor;
	}

	public function set_watch_rss_editor($watch_rss_editor) {
		$this->watch_rss_editor = $watch_rss_editor;
	}

	public function get_watch_rss_webmaster() {
		return $this->watch_rss_webmaster;
	}

	public function set_watch_rss_webmaster($watch_rss_webmaster) {
		$this->watch_rss_webmaster = $watch_rss_webmaster;
	}

	public function get_watch_rss_image_title() {
		return $this->watch_rss_image_title;
	}

	public function set_watch_rss_image_title($watch_rss_image_title) {
		$this->watch_rss_image_title = $watch_rss_image_title;
	}

	public function get_watch_rss_image_website() {
		return $this->watch_rss_image_website;
	}

	public function set_watch_rss_image_website($watch_rss_image_website) {
		$this->watch_rss_image_website = $watch_rss_image_website;
	}

	public function get_informations(){
		$datas = new stdClass();
		$datas->id = $this->id;
		$datas->type = "watch";
		$datas->title = $this->title;
		$datas->num_category = $this->num_category;
		$datas->owner = $this->owner;
		$datas->ttl = $this->ttl;
		$datas->desc = $this->desc;
		$datas->logo_url = $this->logo_url;
		$datas->last_date = $this->last_date;
		$datas->watch_rss_link = $this->watch_rss_link;
		$datas->watch_rss_lang = $this->watch_rss_lang;
		$datas->watch_rss_copyright = $this->watch_rss_copyright;
		$datas->watch_rss_editor = $this->watch_rss_editor;
		$datas->watch_rss_webmaster = $this->watch_rss_webmaster;
		$datas->watch_rss_image_title = $this->watch_rss_image_title;
		$datas->watch_rss_image_website = $this->watch_rss_image_website;
		$datas->sources = array();
		$query = "select id_datasource, datasource_title from docwatch_datasources where datasource_num_watch = ".$this->id;
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)){
			while ($row=pmb_mysql_fetch_object($result)) {
				$datas->sources[] = array("id"=>1*$row->id_datasource, "title"=>$row->datasource_title, "type"=>"source");
			}
		}
		return $datas;
	}

	public function get_normalized_watch(){
		global $opac_url_base;
		$categories = array();
		if ($this->num_category) {
			$query = "select id_category, category_title from docwatch_categories where id_category = ".$this->num_category;
			$result = pmb_mysql_query($query);
			if($result && pmb_mysql_num_rows($result)){
				while ($row=pmb_mysql_fetch_object($result)) {
					$categories = array("id"=>$row->id_category, "title"=>$row->category_title);
				}
			}
		}
		$items = array();
		if(!empty($this->items)){
			foreach($this->items as $item){
				$items[] = $item->get_normalized_item();
			}
		}

		$logo = new docwatch_logo($this->id);

		return array(
			'id' => $this->id,
			'title' => $this->title,
			'desc' => $this->desc,
			'logo' => $logo->format_datas(),
			'logo_url' => $this->logo_url,
			'last_date' => $this->last_date,
			'rss_link' => $opac_url_base."docwatch.php?id=".$this->get_id(),
			'watch_rss_link' => $this->watch_rss_link,
			'watch_rss_lang' => $this->watch_rss_lang,
			'watch_rss_copyright' => $this->watch_rss_copyright,
			'watch_rss_editor' => $this->watch_rss_editor,
			'watch_rss_webmaster' => $this->watch_rss_webmaster,
			'watch_rss_image_title' => $this->watch_rss_image_title,
			'watch_rss_image_website' => $this->watch_rss_image_website,
			'category' => $categories,
			'items' => $items
		);
	}

	public function get_normalized_datasources(){
		$this->get_datasources();
		$array_datasources_normalized = array();
		$array_retour = array();
		foreach($this->datasources_objects as $datasource){
			$array_datasources_normalized[] = $datasource->get_normalized_datasource();
		}
		$array_retour["sources"] = $array_datasources_normalized;
		$array_retour["watch_id"] = $this->id;
		return $array_retour;
	}

	public function get_normalized_items(){
		$array_items_normalized = array();
		$array_retour = array();
		if($this->items){
			foreach($this->items as $item){
				$array_items_normalized[] = $item->get_normalized_item();
			}
		}
		$array_retour["items"] = $array_items_normalized;
		$array_retour["watch_id"] = $this->id;
		return $array_retour;
	}

	public function fetch_items($interesting_only=false, $limit=0){
		$query = "select id_item from docwatch_items where item_num_watch = ".$this->id." and item_status != '2' and item_status != '3'";
		if($interesting_only){
			$query.= " and item_interesting= 1";
		}
		$query.=" order by item_publication_date DESC";
		$limit = intval($limit);
		if($limit) {
		    $query .= " limit ".$limit;
		}
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				if(!$this->items){
					$this->items = array();
				}
				if(!isset($this->items[$row->id_item]) || !$this->items[$row->id_item]){
					$this->items[$row->id_item] = new docwatch_item($row->id_item);
				}
			}
		}
	}

	public static function get_available_datasources(){
		global $msg;
		return array(
			array(
				'class' => 'docwatch_datasource_articles',
				'label'=>$msg['dsi_docwatch_datasource_articles']
			),
			array(
				'class' => 'docwatch_datasource_sections',
				'label'=>$msg['dsi_docwatch_datasource_sections']
			),
			array(
				'class' => 'docwatch_datasource_notices',
				'label'=>$msg['dsi_docwatch_datasource_notices']
			),
			array(
				'class' => 'docwatch_datasource_notices_rss',
				'label'=>$msg['dsi_docwatch_datasource_notices_rss']
			),
			array(
				'class' => 'docwatch_datasource_rss',
				'label'=>$msg['dsi_docwatch_datasource_rss']
			)
		);

	}

	/**
	 * Renvoie une structure XML au format RSS
	 *
	 */
	public function get_xmlrss(){
		global $charset, $pmb_bdd_version;

		if (!$this->id) return;
		$xmlrss = "<?xml version=\"1.0\" encoding=\"".$charset."\"?>
			<!-- RSS generated by PMB on ".addslashes(date("D, d/m/Y H:i:s"))." -->
			<rss version=\"2.0\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">
				<channel>
					<title>".htmlspecialchars ($this->title,ENT_QUOTES, $charset)."</title>
					<link>".htmlspecialchars ($this->watch_rss_link,ENT_QUOTES, $charset)."</link>
					<description>".htmlspecialchars ($this->desc,ENT_QUOTES, $charset)."</description>
					<language>".htmlspecialchars ($this->watch_rss_lang,ENT_QUOTES, $charset)."</language>
					<copyright>".htmlspecialchars ($this->watch_rss_copyright,ENT_QUOTES, $charset)."</copyright>
					<managingEditor>".htmlspecialchars ($this->watch_rss_editor,ENT_QUOTES, $charset)."</managingEditor>
					<webMaster>".htmlspecialchars ($this->watch_rss_webmaster,ENT_QUOTES, $charset)."</webMaster>
					<generator>PMB Version ".$pmb_bdd_version."</generator>
					<lastBuildDate>".addslashes(date("D, d M Y H:i:s O",strtotime($this->last_date)))."</lastBuildDate>
					<ttl>".$this->ttl."</ttl>
					<category></category>\n";
		if ($this->logo_url || $this->watch_rss_image_title || $this->watch_rss_image_website) {
			$xmlrss .= "					<image>
						<url>".htmlspecialchars ($this->logo_url,ENT_QUOTES, $charset)."</url>
						<title>".htmlspecialchars ($this->watch_rss_image_title,ENT_QUOTES, $charset)."</title>
						<link>".htmlspecialchars ($this->watch_rss_image_website,ENT_QUOTES, $charset)."</link>
					</image>";
		}
		$xmlrss .= "		!!items!!
				</channel>
			</rss>";

		$xmlrss = str_replace("!!items!!", $this->get_items_xmlrss(), $xmlrss);
		if($charset=='utf-8') {
			$xmlrss = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'.
					'|[\x00-\x7F][\x80-\xBF]+'.
					'|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.
					'|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'.
					'|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/',
					'', $xmlrss );
		} else {
			$xmlrss = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]/',
					'', $xmlrss );
		}
		return $xmlrss;
	}

	public function get_items_xmlrss() {
		global $charset;

		$items_xmlrss = "";

		if (is_array($this->items) && count($this->items)) {
			foreach ($this->items as $item) {
				if ($item->get_interesting()) {
					if ($item->get_logo_url() != "") {
						$image = "<img src='".$item->get_logo_url()."' alt='' class='align_right vignetteimg' style='max-width: 140px;max-height: 200px;'/>";
					} else {
						$image = "";
					}
					$items_xmlrss .= "
					<item>
						<title>".htmlspecialchars ($item->get_title(),ENT_QUOTES, $charset)."</title>
						<pubDate>".date("D, d M Y H:i:s O",strtotime($item->get_publication_date()))."</pubDate>
						<link>".htmlspecialchars ($item->get_url(),ENT_QUOTES, $charset)."</link>
						<description>
						".htmlspecialchars(strip_tags($image.$item->get_summary(),"<table><tr><td><br/><img>"),ENT_QUOTES, $charset)."
						</description>";
					$descriptors = $item->get_descriptors();
					if (is_array($descriptors) && count($descriptors)) {
						foreach ($descriptors as $descriptor) {
							$items_xmlrss .= "
						<category>".htmlspecialchars ($descriptor['label'],ENT_QUOTES, $charset)."</category>";
						}
					}
					$items_xmlrss .= "
					</item>";
				}
			}
		}
		return $items_xmlrss;
	}

} // end of docwatch_watch
