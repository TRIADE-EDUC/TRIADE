<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: autoindex_term.class.php,v 1.8 2022/01/04 08:41:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once("$class_path/autoindex/autoindex_word.class.php");
require_once("$include_path/misc.inc.php");
require_once("$class_path/XMLlist.class.php");


class autoindex_term {
	
	/**
	 * Identifiant du terme (dans la table noeuds)
	 * @access protected
	 */
	public $id =0;
	
	/**
	 * Pertinence brute du terme
	 * @access protected
	 */
	public $raw_relevancy=0;
	
	/**
	 * Libellé du terme
	 * @access protected
	 */
	public $label='';
	
	/**
	 * Pertinence totale
	 * @access protected
	 */
	public $total_relevancy=0;
	
	/**
	 * Distance du terme dans un document
	 * @access protected
	 */
	public $document_distance=0;
	
	/**
	 * Identifiant du renvoi du terme 
	 */
	public $see=0;
	
	/**
	 * Identifiants des renvois voir aussi vers ce terme 
	 */
	public $see_also=array();
	
	/**
	 * Chemin du terme
	 */
	public $path='';
	
	/**
	 * Profondeur du terme
	 */
	public $deep=1;
	
	/**
	 * @param integer $id
	 * @param string $label
	 * @param array(autoindex_word) $words
	 * @param array(string) $stems 
	 */
	
	public function __construct($id, $label, $see=0, $path='', $relevancy=0) {
		global $autoindex_deep_ratio;
		
		$this->id=$id;
		$this->label=$label;
		$this->see = $see;
		$this->path = $path;
		$this->deep = strlen(preg_replace("/[0-9]/","",$path))*1+1;
		$this->raw_relevancy = $relevancy * ( 1+ $this->deep * $autoindex_deep_ratio);
		
		//Recherche des termes voir_aussi vers ce terme
		$q = "select num_noeud_orig from voir_aussi where num_noeud_dest=".$this->id;
		$r = pmb_mysql_query($q);
		if(pmb_mysql_num_rows($r)) {
			while($row = pmb_mysql_fetch_object($r)) {
				if($row->num_noeud_orig) {
					$this->see_also[]=$row->num_noeud_orig;
				}
			}
		}
		
		//TODO = Recherche du chemin le plus long contenant le terme
		//$q = "select distinct path from noeuds where path like '%".$this->path."%' ";
		
		
	}
	
	
	/**
	 * Pertinence brute + (pertinence des termes de la branche pondéré par la distance)
	 * + Pertinence voir aussi pondéré
	 * 
	 * PS :Plus un terme est profond dans l'arbre, plus il est pertinent
	 * 
	 * @param Array(Terme) terms ensemble des termes pertinents
	 * @param int max_up_distance distance max montante
	 * @param int max_down_distance distance max descendante
	
	 * @return void
	 * @access public
	 */
	public function calc_total_relevancy( &$terms=array(),  $max_up_distance=0,  $max_down_distance=0) {
		global $autoindex_max_down_ratio;
		global $autoindex_max_up_ratio;
		global $autoindex_see_also_ratio;
		if(!$this->id) return;
		
		$tr = 0;
		$tr+= $this->raw_relevancy;

		//Ajout des pertinences des termes de la même branche pondérés par la distance
		foreach($terms as $term) {
			if($this->id != $term->id && $this->path) {
				if( (strpos($term->path, $this->path)!==false) ) {
					$dist = $this->deep - $term->deep;
					$coeff=0;
					if ( ($dist > 0) && ($dist <= $max_up_distance) ) {
						$coeff = $autoindex_max_down_ratio/$dist; 
					} else if ( ($dist < 0) ) {
						$dist = abs($dist);
						if ($dist <= $max_down_distance ) {
							$coeff = $autoindex_max_up_ratio/$dist;	
						}
					}
					if($coeff) {
						$tr+= $term->raw_relevancy * $coeff;
					}
				}
			}
		}		
		
		//Ajout des pertinences des termes renvoyant vers ce terme + pondération 
		foreach($terms as $term) {
			if($this->id != $term->id) {
				if (in_array($this->id, $term->see_also)) {
					$tr+= $term->raw_relevancy * $autoindex_see_also_ratio;
				}	
			}
		}
		
		$this->total_relevancy = $tr;
		
	}
	
	  
	/**
	 * Somme du nombre de mots non vides dans le document entre chaque mot non vide
	 * constituant le terme
	 * 
	 * entre chaque mot non vide du terme
	 * 
	 * Elle est pourrie mais claire... (m1(\w)*m2(\w)*m3)
	 *
	 * @param string $full_clean_text
	 * @param string $lang
	 * 	
	 * @return void
	 * 
	 * @access public
	 */
	public function calc_term_document_distance( $full_clean_text='', $lang='fr_FR') {
		global $autoindex_distance_ratio,$autoindex_distance_type;
		
		if(!$this->id || $full_clean_text==='') return;
		$clean_label = strip_empty_words($this->label,$lang);

		
		switch($autoindex_distance_type) {
		
			case '1' :
			default :
					
				// Distance tenant compte du nombre de mots non vides entre les mots du terme dans le texte
				//$expr = str_replace(' ', "(\/w)", "/(\/w)".$clean_label."/");	>> ne marche pas si les mots sont accollés !!
				$expr = str_replace(' ', "(\s+.*?)", "/".$clean_label."\s+.*?"."/");
				$dmax = str_word_count($full_clean_text, 0, "0123456789");
				$this->document_distance = $dmax;
				$dterm = $dmax;
				if (preg_match_all($expr, ' '.$full_clean_text.' ',$matches, PREG_SET_ORDER)) {
					for($i=1;$i<count($matches);$i++) {
						$d=0;
						for($j=1;$j<count($matches[$i]);$j++) {
							if($matches[$i][$j]) {
								$d+= str_word_count($matches[$i][$j], 0, "0123456789");	
							}
						}
						if ($d<$dterm) {
							$dterm=$d;
						}
					}
					$this->document_distance = $dterm;
					$this->total_relevancy = $this->total_relevancy * (1 + (($dmax - $dterm) * $autoindex_distance_ratio/$dmax) );
				}
				break;	
		
		
			case '2' :
		
				// Distance tenant compte du nombre de caractères entre les mots du terme dans le texte
				$dmax = strlen($full_clean_text);
				$this->document_distance = $dmax;
				$dterm = 0;
				$expr = str_replace(' ', "(.*?)", "/(.*?)".$clean_label."/");	
				if (preg_match($expr, $full_clean_text,$matches )) {
					for($i=1;$i<count($matches)-1;$i++) {
						$dterm+= strlen($matches[$i]);
					}
					$this->document_distance = $dterm;
					$this->total_relevancy = $this->total_relevancy * (1 + (($dmax - $dterm) * $autoindex_distance_ratio/$dmax) );
				} 
				break;

				
			case '3' :
				// distance tenant compte de la position des mots du terme dans le texte		
				$dmax = strlen($full_clean_text);
				$this->document_distance = $dmax;
				$dterm = 0;
				$t_label = explode(' ',$clean_label);
				foreach($t_label as $k=>$l) {
					$dl = stripos($full_clean_text, $l);
					if($dl!==false) {
						$dterm+= $dl;
					} else {
						$dterm+= $dmax;				
					}
				}
				//$dl = $dl / (count($t_label));
				$this->document_distance = $dterm;
				$this->total_relevancy = $this->total_relevancy * (1 + (($dmax - $dterm) * $autoindex_distance_ratio/$dmax) );
				break;
		
			case '4' :
 				// Distance tenant compte de l'ordre et de la position des mots du terme dans le texte
				$dmax = strlen($full_clean_text);
				$this->document_distance = $dmax;
				$dterm = 0;
				$t_label = explode(' ',$clean_label);
				$old_dl=0;
				foreach($t_label as $k=>$l) {
					$dl = stripos(' '.$full_clean_text.' ', ' '.$l.' ');
					if($dl!==false && $dl>=$old_dl) {
						$dterm+= $dl-$old_dl;
						$old_dl = $dl;
					} else {
						$dterm+= $dmax;
					}
				}
				$this->document_distance = $dterm;
				$this->total_relevancy = $this->total_relevancy * (1 + (($dmax - $dterm) * $autoindex_distance_ratio/$dmax) );
				break;
		}
		
	}

	
	
	/**
	 * Tri inverse d'un tableau de termes en fonction de raw_relevancy
	 *
	 * @param autoindex_term $a
	 * @param autoindex_term $b
	 */
	public static function compare_raw_relevancies($a, $b) {
	
		$wa = $a->raw_relevancy;
		$wb = $b->raw_relevancy;
		
		$ret = 0;
		if ( $wa < $wb ) {
			$ret = 1;
		} elseif ( $wa > $wb ) {
			$ret = -1;
		}
		return $ret;
	}

	/**
	 * Tri inverse d'un tableau de termes en fonction de total_relevancy
	 *
	 * @param autoindex_term $a
	 * @param autoindex_term $b
	 */
	public static function compare_total_relevancies($a, $b) {
	
		$wa = $a->total_relevancy;
		$wb = $b->total_relevancy;
		$ret = 0;
		if ( $wa < $wb ) {
			$ret = 1;
		} elseif ( $wa > $wb ) {
			$ret = -1;
		}
		return $ret;
	}
	
}