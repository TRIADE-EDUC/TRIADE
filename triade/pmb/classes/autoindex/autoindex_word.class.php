<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: autoindex_word.class.php,v 1.5.8.1 2025/03/14 08:07:35 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/stemming.class.php");

class autoindex_stem {


	/**
	 * Langue du stem
	 * @access public
	 */
	public $lang = 'fr_FR';

	/**
	 * Libellé du stem
	 *
	 * @access public
	 */
	public $label='';

	/**
	 * Ponderation complémentaire du stem
	 *
	 * @access public
	 */
	public $pond=1;

	/**
	 * Pertinence du stem
	 *
	 * @access public
	 */
	public $relevancy=0;


	/**
	 * Fréquence du mot dans le document
	 * @access public
	 */
	public $frequency=0;


	public function __construct($label='', $frequency=0, $lang='fr_FR', $pond=1) {

		$this->label=$label;
		$this->frequency=$frequency;
		$this->lang = $lang;
		$this->pond = $pond;

	}


	/**
	 * @param float relevancy Pertinence calculée par le fonds
	 * @return void
	 * @access public
	 */
	public function set_relevancy($relevancy=0) {

		$this->relevancy = $relevancy;
	}


	/**
	 * Tri inverse d'un tableau d'objets en fonction de la propriété relevancy
	 *
	 * @param object $a
	 * @param object $b
	 */
	public static function compare_relevancies($a, $b) {

		$wa = $a->relevancy;
		$wb = $b->relevancy;

		$ret = 0;
		if ( $wa < $wb ) {
			$ret = 1;
		} elseif ( $wa > $wb ) {
			$ret = -1;
		}
		return $ret;
	}

}



class autoindex_word extends autoindex_stem {

	/**
	 * Identifiant du mot (dans la table words)
	 * @access protected
	 */
	public $id =0;

	/**
	 * Identifiant du mot sans langue (dans la table words)
	 * @access protected
	 */
	public $wo_lang_id =0;

	public $stem;


	public function __construct($label='', $frequency=0, $lang='fr_FR', $wo_lang=true, $pond=1) {
		$this->label=$label;
		$this->frequency=$frequency;
		$this->lang = $lang;
		$this->pond = $pond;

		//par défaut, on met la langue ;-)
		$q ="select id_word, stem from words where word='".addslashes($this->label)."' ";
		if ($lang) {
			$q.= "and lang='".addslashes($this->lang)."' ";
		}
		$q.= " limit 1";
		//echo $q."\r\n";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r)) {
			$row = pmb_mysql_fetch_object($r);
			$this->id = $row->id_word;
			$this->stem = $row->stem;

		}

		if($lang && $wo_lang){

			//on ajoute aussi l'identifiant des mots sans langue
			$q1 ="select id_word, stem from words where word='".addslashes($this->label)."' and lang='' limit 1";
			//echo $q1."\r\n";
			$r1 = pmb_mysql_query($q1);
			if (pmb_mysql_num_rows($r1)) {
				$row = pmb_mysql_fetch_object($r1);
				if($this->id) {
					$this->wo_lang_id = $row->id_word;
				} else {
					$this->id = $row->id_word;
					$this->lang = '';
				}
				$this->stem = $row->stem;
			}
		}

		//calcul du stem si besoin
		if(!$this->stem && $this->lang=='fr_FR') {
			$stemming = new stemming($this->label);
			$this->stem = $stemming->stem;
		}
	}
}
