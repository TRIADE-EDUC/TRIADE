<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: harvest.class.php,v 1.17 2024/01/05 11:37:04 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}


class harvest
{

    /* Identifiant du profil */
	public $id=0;

    /* Tableau des champs de recherche externes */
    public static $search_fields = null;

    public static $search_field_ids = [];

    /* Tableau des champs persos */
    protected static $notice_custom_fields = null;

    /* Tableau des sources de recherches */
    protected static $sources = null;


    public function __construct($id = 0)
    {
		$this->id=intval($id);
        static::getSearchFieldsBySource();
        static::getNoticeCustomFields();
	}

	/**
	 * Recupere la liste des champs de recherche externe
	 *
	 * @return []
	 */
	protected static function getSearchFields()
	{
	    if(!is_null(static::$search_fields)) {
	        return static::$search_fields;
	    }

	    $sc = new search(false,"search_fields_unimarc");

	    static::$search_fields = [];
	    static::$search_field_ids = [];
	    foreach($sc->fixedfields as $fixed_field) {
	        if ( empty($fixed_field["UNIMARCFIELD"]) || ('FORBIDDEN' == $fixed_field["UNIMARCFIELD"]) || (!$fixed_field["VISIBLE"]) ) {
	            continue;
	        }
	        $key = $fixed_field["UNIMARCFIELD"];
	        $id = $fixed_field["ID"];
            static::$search_fields[$key] = $fixed_field["TITLE"];
	        static::$search_field_ids[$key] = $id;
	    }
        return static::$search_fields;
	}


    /**
	 * Recupere la liste des champs persos de notice
	 *
	 * @return []
	 */
	protected static function getNoticeCustomFields()
	{
	    if(!is_null(static::$notice_custom_fields)) {
	        return static::$notice_custom_fields;
	    }
        static::$notice_custom_fields = [];

	    $pp = new parametres_perso('notices');
	    foreach($pp->t_fields as $id => $t_field) {
	        static::$notice_custom_fields[] = [
	            'id' => 'd_' . $id,
	            'title' => $t_field['TITRE'],
	        ];
	    }
	    return static::$notice_custom_fields;
	}


    /**
     * Recupere la liste des sources
     *
     * @return []
     */
    protected static function getSources()
    {
        if(!is_null(static::$sources)) {
            return static::$sources;
        }
        $connectors = connecteurs::get_instance();
        static::$sources = $connectors->getSearchableSourceList();
        return static::$sources;
    }


    /**
     * Recupere la liste des champs interrogeables par source
     *
     * @return []
     */
    protected static function getSearchFieldsBySource()
    {
        global $base_path;

        if(!is_null(static::$sources)) {
            return static::$sources;
        }
        static::getSources();
        static::getSearchFields();

        $unimarc_search_fields = array_keys(static::$search_fields);
        $already_seen_classes = [];
        foreach(static::$sources as $k => $source) {

            $class = $source['id_connector'];
            $class_filepath = $base_path . '/admin/connecteurs/in/' . $class . '/' . $class .'.class.php';
            if(array_key_exists($class, $already_seen_classes)) {
                static::$sources[$k]['search_fields'] = $already_seen_classes[$class];
                continue;
            }
            // Recuperation champs de recherche definis pour la source
            if(file_exists($class_filepath) ) {
                require_once $class_filepath;
                $source_search_fields = $unimarc_search_fields;
                $specific_unimarc_search_fields = $class::getSpecificUnimarcSearchFields();
                if( !empty($specific_unimarc_search_fields) ) {
                    $source_search_fields = $specific_unimarc_search_fields;

                    // Elimination des champs de recherche de la source non pris en charge
                    for( $i=count($source_search_fields) ; $i >= 0 ; $i--) {
                        if( !array_key_exists($source_search_fields[$i], static::$search_fields) ) {
                            unset($source_search_fields[$i]);
                        }
                    }
                }

                static::$sources[$k]['search_fields'] = $source_search_fields;
                $already_seen_classes[$class] = $source_search_fields;
            }
        }
        return static::$sources;
    }

    /**
     * Retourne un selecteur html de recolteur
     *
     * @param string $sel_name
     * @param number $sel_id
     *
     * @return string
     */
    public static function getSelector($sel_name = '', $sel_id = 0 )
    {
        global $charset;

        $list = static::getList();
        if( !count($list)) {
            return '';
        }
        $tpl = "<select name='$sel_name' >";
        foreach($list as $id => $name) {
            $tpl .= "<option value='".$id."' ". (($id==$sel_id) ? "selected" : "") . ">" . htmlentities($name, ENT_QUOTES, $charset) ."</option>";
        }
        $tpl.= "</select>";
        return $tpl;
    }


    /**
     * Retourne la liste des recolteurs
     *
     * @return array
     */
    public static function getList()
    {
        $q = "select id_harvest_profil, harvest_profil_name from harvest_profil order by harvest_profil_name";
        $r = pmb_mysql_query($q);

        if (! pmb_mysql_num_rows($r)) {
            return [];
        }
        $list = [];
        while($row = pmb_mysql_fetch_assoc($r)) {
            $list[$row['id_harvest_profil']] = $row['harvest_profil_name'];
        }
        return $list;
    }

}

