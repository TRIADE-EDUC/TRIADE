<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: translation.class.php,v 1.7 2017-07-27 10:09:39 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))	die("no access");

/**
 * Classe permettant de gérer les traductions de libellé
 * Utilise la table translation, croisée avec le nom de la table et du champ à traduire
 * Mémorise et récupère le texte dans la lange voulue
 * 
 "CREATE TABLE translation (
    trans_table VARCHAR( 255 ) NOT NULL default '',
    trans_field VARCHAR( 255 ) NOT NULL default '',
    trans_lang VARCHAR( 255 ) NOT NULL default '',
   	trans_num INT( 8 ) UNSIGNED NOT NULL default 0 ,
    trans_text VARCHAR( 255 ) NOT NULL default '',
    PRIMARY KEY trans (trans_table,trans_field,trans_lang,trans_num),
    index i_lang(trans_lang)
   )";  
 */
class translation {

	protected static $text_fields = array();
	
	public function __construct() {
	}
	
	/**
	 * Retourne la traduction d'un champ dans la langue voulue, ou le libellé par défaut
	 * @param int $id Identifiant de l'entité
	 * @param string $trans_table Table de référence
	 * @param string $trans_field Champ de référence
	 * @param string $text Libellé par défaut
	 * @param string $mylang Langue voulue
	 * @return string
	 */
	public static function get_text($id, $trans_table, $trans_field, $text="", $mylang="") {
		global $lang, $dbh;
		
		if(!$mylang) {
			$mylang = $lang;
		}
		self::get_text_fields($trans_table, $mylang, $id);
		if (!isset(self::$text_fields[$trans_table][$id][$mylang][$trans_field])) {
			self::$text_fields[$trans_table][$id][$mylang][$trans_field] = $text;
		}
		return self::$text_fields[$trans_table][$id][$mylang][$trans_field];
	}
	
	/**
	 * Retourne la traduction des champs dans la langue voulue
	 * @param unknown $table
	 * @param unknown $lang
	 * @param unknown $num
	 * @param string $text
	 */
	public static function get_text_fields($table, $lang, $num){
		if(!isset(self::$text_fields[$table])){
			self::$text_fields[$table] = array();
		}
		if(!isset(self::$text_fields[$table][$num])){
			self::$text_fields[$table][$num] = array();
		}
		if(isset(self::$text_fields[$table][$num][$lang])){
			return self::$text_fields[$table][$num][$lang];
		}
		self::$text_fields[$table][$num][$lang] = array();
	
		$query = "SELECT trans_field, trans_small_text, trans_text FROM translation WHERE trans_table='".addslashes($table)."' and trans_lang='".addslashes($lang)."' and trans_num='".$num."' ";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while(($row = pmb_mysql_fetch_assoc($result))) {
				self::$text_fields[$table][$num][$lang][$row['trans_field']] = ($row['trans_small_text'] ? $row['trans_small_text'] : $row['trans_text']);
			}
		}
		return self::$text_fields[$table][$num][$lang];
	}
}
