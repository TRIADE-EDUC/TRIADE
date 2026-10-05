<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: review_data.class.php,v 1.2 2022/08/04 14:12:59 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once $class_path."/avis.class.php";
require_once $class_path."/entities.class.php";
require_once $class_path."/cms/cms_editorial_data.class.php";
require_once $class_path."/record_datas.class.php";
require_once $class_path."/emprunteur_datas.class.php";

class review_data {
	
	/**
	 * Identifiant
	 * @var integer
	 */
	private $id = 0;
	
	/**
	 * emprunteur associe a l'avis
	 * @var emprunteur_datas
	 */
	private $empr = null;
	
	/**
	 * objet sur lequel porte l'avis
	 * @var mixed
	 */
	private $object = null;
	
	/**
	 * liste de lecture associee a l'avis
	 * @var liste_lecture
	 */
	private $reading_list = null;

	public function __construct($id)
	{
	    $this->id = intval($id);
		$this->fetch_data();
	}
	
	private function fetch_data()
	{
	    global $msg;
	    if ($this->id) {
	        $query = "SELECT A.*, DATE_FORMAT(A.dateajout,'".$msg['format_date']."') as create_date FROM avis A WHERE A.id_avis = ".$this->id;
	        $result = pmb_mysql_query($query);
	        if (pmb_mysql_num_rows($result)) {
	            $row = pmb_mysql_fetch_assoc($result);
	            foreach ($row as $field => $value) {
	                $this->{$field} = $value;
	            }
	        }
	    }
	}
	
	/**
	 * on retourne les proprietes dynamiquement
	 * @param string $name
	 * @return mixed|NULL
	 */
	public function __get($name)
	{
	    if(method_exists($this, "get_".$name)) {
	        return call_user_func_array(array($this, "get_".$name), []);;
	    }
	    if(isset($this->{$name})) {
	        return $this->{$name};
	    }
	    return null;
	}
	
	/**
	 * renvoi les infos de l'emprunteur
	 * @return emprunteur_datas
	 */
	public function get_empr() 
	{
	    if (!empty($this->empr)) {
	        return $this->empr;
	    }
	    if (!empty($this->num_empr)) {
	        $this->empr = new emprunteur_datas($this->num_empr);
	    }
	    return $this->empr;
	}
	
	/**
	 * renvoi l'instance de l'objet associe a l'avis
	 * @return mixed|cms_editorial_data|record_datas
	 */
	public function get_object() 
	{
	    if (!empty($this->object)) {
	        return $this->object;
	    }
	    if (!empty($this->num_notice) && !empty($this->type_object)) {
	        switch ($this->type_object) {
	            case AVIS_ARTICLES :
	                $this->object = new cms_editorial_data($this->num_notice, "article");
	                break;
	            case AVIS_SECTIONS :
	                $this->object = new cms_editorial_data($this->num_notice, "section");
	                break;
	            case AVIS_RECORDS :
	                $this->object = new record_datas($this->num_notice);
	                break;
	        }
	    }
	    return $this->object;
	}	
	
	/**
	 * renvoi l'instance de la liste de lecture associee
	 * @return liste_lecture
	 */
	public function get_reading_list()
	{
	    if (!empty($this->reading_list)) {
	        return $this->reading_list;
	    }
	    if (!empty($this->avis_num_liste_lecture)) {
	        $this->reading_list = new liste_lecture($this->avis_num_liste_lecture);
	    }
	    return $this->reading_list;
	}
	
	/**
	 * Liste les methodes, utile pour les templates django
	 * @return []
	 */
	public function get_methods_infos() {
	    return entities::get_methods_infos($this);
	}
	
	/**
	 * Liste les proprietes, utile pour les templates django
	 * @return []
	 */
	public function get_properties_infos() {
	    return entities::get_properties_infos($this);
	}
}