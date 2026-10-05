<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: autoindex_document.class.php,v 1.13.8.1 2025/02/12 12:34:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once("$class_path/autoindex/autoindex_word.class.php");
require_once("$class_path/autoindex/autoindex_term.class.php");
require_once("$class_path/autoindex/autoindex_documents_collection.class.php");

require_once("$include_path/misc.inc.php");
require_once("$class_path/XMLlist.class.php");


$autoindex_params = array(
		'max_relevant_words'=>20,				//Nombre maximum de mots de la notice à prendre en compte pour le calcul
		'autoindex_stem_ratio'=>0.8,			//Ratio de pondération des lemmes
		'max_relevant_terms'=>10,				//Nombre de termes à conserver aprés tri
		'autoindex_max_up_distance'=>2,
		'autoindex_max_up_ratio'=>0.01,
		'autoindex_max_down_distance'=>2,
		'autoindex_max_down_ratio'=>0.01,
		'autoindex_see_also_ratio'=>0.01,
		'autoindex_deep_ratio'=>0.05,
		'autoindex_distance_type'=>1,
		'autoindex_distance_ratio'=>0.5,
);

global $thesaurus_auto_index_search_param;
if($thesaurus_auto_index_search_param){

	$tmp = explode(";",$thesaurus_auto_index_search_param);
	foreach($tmp as $param){
		$pos = stripos($param,'=');
		if($pos) {
			$p = trim(substr($param,0,$pos));
			$v = intval(str_replace(',','.',trim(substr($param,$pos+1))));

			if(array_key_exists($p,$autoindex_params) && $v) {
				$autoindex_params[$p] = $v;
			}
			//C'est quoi cette horreur !!!
			//@eval("\$".$param_command.";");
		}
	}
}

foreach($autoindex_params as $p=>$v) {
	${$p}=$v;
}



class autoindex_document {

	/**
	 * texte brut à analyser
	 * @var array
	 * @access protected
	 */
	protected $raw_text=array();

	/**
	 *
	 */
	protected $clean_text='';

	protected $full_clean_text='';

	/**
	 * langue du document
	 * @var string
	 * @access protected
	 */
	protected $lang='fr_FR';

	/**
	 * Permet de préciser si l'on recherche aussi dans les mots sans langue
	 */
	protected $wo_lang = true;

	/**
	 * Fonds des documents
	 * @var autoindex_documents_collection
	 * @access protected
	 */
	protected $collection;

	/**
	 * tableau des mots distincts du document avec fréquence et ponderation
	 * @var array(autoindex_word)
	 * @access protected
	 */
	public $words = array();

	/**
	 * tableau des stems distincts du document avec fréquence
	 * @var array(autoindex_stem)
	 * @access protected
	 */
	protected $stems = array();

	/**
	 * Identifiant du thésaurus
	 * @var integer
	 * @access protected
	 */
	protected $id_thesaurus = 0;

	/**
	 * tableau des termes pertinents pour le document
	 * @var array(autoindex_term)
	 * @access protected
	 */
	public $terms = array();


	public function __construct() {

	}


	/**
	 * calcule la pertinence d'un mot dans le fond
	 * fréquence mot dans le document  * log(frequence inverse dans le fond)
	 *
	 * @param autoindex_word word Mot pour lequel calculer la pertinence
	 * @return float
	 * @access public
	 */
	public function calc_word_relevancy($word) {

		$wr = 0;
		$w_idf = $this->collection->calc_inverse_frequency($word);

		if ($w_idf) {
			$wf = $word->frequency;
			$wr = $wf * log($w_idf,10);
		}
		return $wr;
	}


	/**
	 * calcule la pertinence d'un stem dans le fond
	 * fréquence stem dans le document  * log(frequence inverse dans le fond)
	 *
	 * @param autoindex_stem : Stem pour lequel calculer la pertinence
	 * @return float
	 * @access public
	 */
	public function calc_stem_relevancy($stem) {

		global $autoindex_stem_ratio;

		$sr = 0;
		$s_idf = $this->collection->calc_stem_inverse_frequency($stem);

		if ($s_idf) {
			$sf = $stem->frequency;
			$sr = $sf * log($s_idf,10) * $autoindex_stem_ratio;
		}
		return $sr;
	}


	/**
	 * Trouve les mots pertinents du document
	 *
	 * récupère le texte brut, effectue un nettoyage
	 * calcule la fréquence des mots dans le document
	 *
	 * @return void
	 * @access public
	 */
	public function find_revelants_words() {
		global $max_relevant_words;
		$tab_words = array();

		if(is_array($this->raw_text) && count($this->raw_text)) {
			foreach ($this->raw_text as $v) {

				//nettoyage texte brut
				$this->clean_text = strip_empty_words($v['value'], $this->lang);
				$this->full_clean_text.= $this->clean_text.' ';
				$pond = $v['pond'];

	//TODO
	// echo $this->clean_text."<br />";
	// echo $pond."<br />";

				//calcul de la fréquence des mots dans le document
				$tab_clean_text = array();
				if(strlen($this->clean_text)) {
					$tab_clean_text = explode(' ',$this->clean_text);
				}
				if (count($tab_clean_text)) {
					foreach($tab_clean_text as $v) {
						if ($v!=='') {
							if (!isset($tab_words[$v]) || !$tab_words[$v]) {
								$tab_words[$v]['frequency'] = 0;
								$tab_words[$v]['pond'] = 0;
							}
							if( $pond > $tab_words[$v]['pond']) {
								$tab_words[$v]['pond'] = $pond;
							}
							$tab_words[$v]['frequency']+=1;
						}
					}
				}
				unset($tab_clean_text);
			}
		}
//TODO
// echo "Mots du document avec leur fréquence et leur pondération par champ (".count($tab_words).") =<br />";
// highlight_string(print_r($tab_words,true));
// echo "<br />";

		//instanciation des mots utilisés
		$tmp_words = array();
		if(count($tab_words)) {
			foreach($tab_words as $label=>$v) {
				$w = new autoindex_word($label, $v['frequency'],$this->lang, $this->wo_lang, $v['pond']);
				$w_r = $this->calc_word_relevancy($w);
				$w_r= $w_r * $w->pond;
				$w->set_relevancy($w_r);
				$tmp_words[]=$w;
			}
		}
		unset($tab_words);
// TODO
// echo "Mots du document (".count($tmp_words).") =<br />";
// highlight_string(print_r($tmp_words,true));
// echo "<br />";

 		//limitation du nombre de mots
 		if(count($tmp_words)) {
			usort($tmp_words, array("autoindex_word", "compare_relevancies"));
			$done=false;
			$i=0;
			while (!$done) {
				$this->words[$i] = $tmp_words[$i];
				$i++;
				if($i==$max_relevant_words || $i==count($tmp_words)) {
					$done=true;
				}
			}
 		}
// TODO
// echo "Mots pertinents du document (".count($this->words).") =<br />";
// highlight_string(print_r($this->words,true));
// echo "<br />";


		//calcule la fréquence et la pertinence des stems dans le document
		$tab_stems = array();
		if(count($tmp_words)) {
			foreach($tmp_words as $word) {
				if (!isset($tab_stems[$word->stem]) || !$tab_stems[$word->stem]) {
					$tab_stems[$word->stem]['frequency']=0;
					$tab_stems[$word->stem]['pond'] = 0;
				}
				if( $word->pond > $tab_stems[$word->stem]['pond']) {
					$tab_stems[$word->stem]['pond'] = $word->pond;
				}
				$tab_stems[$word->stem]['frequency']+=1;

			}
		}
		unset ($tmp_words);

		$tmp_stems=array();
		if(count($tab_stems)) {
			foreach($tab_stems as $label=>$v) {
				$s = new autoindex_stem($label, $v['frequency'], $this->lang,$v['pond']);
				$s_r = $this->calc_stem_relevancy($s);
				$s_r= $s_r * $s->pond;
				$s->set_relevancy($s_r);
				$tmp_stems[]=$s;
			}
		}
// TODO
// echo "Stems du document (".count($tmp_stems).") =<br />";
// highlight_string(print_r($tmp_stems,true));
// echo "<br />";


		//limitation du nombre de stems
		if(count($tmp_stems)) {
			usort($tmp_stems, array("autoindex_stem", "compare_relevancies"));
			$done=false;
			$i=0;
			while (!$done) {
				$this->stems[$i] = $tmp_stems[$i];
				$i++;
				if($i==$max_relevant_words || $i==count($tmp_stems)) {
					$done=true;
				}
			}
		}
		unset($tmp_stems);
// TODO
// echo "Stems pertinents du document (".count($this->stems).") =<br />";
// highlight_string(print_r($this->stems,true));
// echo "<br />";

	}


	/**
	 * Retrouve les termes du thésaurus contenant au moins un mot (ou un synonyme)
	 * pertinent et leur pertinence brute.
	 *
	 * En 2 passes :
	 * La première avec les mots exacts.
	 * La 2ème avec les stemmes de mot du document et les stemmes des mots des termes.
	 * Si pas déjà trouvé avec les mots, on ajoute en sous-pondérant (ratio à définir)
	 *
	 * @return void
	 * @access public
	 */
	public function get_relevants_terms() {
		$this->terms = array();
		if(count($this->words)) {
			$terms = array();
// $terms1 = array();
// $terms2 = array();

			$q_restrict = '1 ';
			$q0 = "select group_concat(id_noeud) from noeuds where autorite in ('TOP','ORPHELINS','NONCLASSES')";
			$r0 = pmb_mysql_query($q0);
			if(pmb_mysql_num_rows($r0)) {
				$q_restrict= 'num_noeud not in('.pmb_mysql_result($r0,0,0).') ';
			}

			//recherche des termes pour lesquels on a une correspondance exacte avec l'un des mots du document
			$used_word_ids=array();
			foreach($this->words as $word) {

				if($word->id) {
					$used_word_ids[] = $word->id;
				}
				if($word->wo_lang_id) {
					$used_word_ids[] = $word->wo_lang_id;
				}

				$q1 = "select num_noeud, libelle_categorie, num_renvoi_voir, path from noeuds join categories on num_noeud=id_noeud where $q_restrict ";
				if ($this->id_thesaurus) {
					$q1.= "and noeuds.num_thesaurus=".$this->id_thesaurus." ";
				}
				if ($this->lang) {
					$q1.= "and langue='".$this->lang."' ";
				}
				$q1.= "and index_categorie like '% ".$word->label." %' ";
				$r1 = pmb_mysql_query($q1);
				if(pmb_mysql_num_rows($r1)) {
					while($row1 = pmb_mysql_fetch_object($r1)) {
						if(!isset($terms[$row1->num_noeud]) || !$terms[$row1->num_noeud]) {
// $terms1[]=$row1->libelle_categorie;
							$terms[$row1->num_noeud]['label']=$row1->libelle_categorie;
							$terms[$row1->num_noeud]['see']=$row1->num_renvoi_voir;
							$terms[$row1->num_noeud]['path']=$row1->path;
							$terms[$row1->num_noeud]['words']=array();
							$terms[$row1->num_noeud]['stems']=array();
							$terms[$row1->num_noeud]['relevancy']=0;
						}
						$terms[$row1->num_noeud]['words'][] = $word->label;
						$terms[$row1->num_noeud]['relevancy'] = $terms[$row1->num_noeud]['relevancy'] + $word->relevancy;
					}
				}
			}

//TODO
// echo "Termes contenant exactement un des mots pertinents du document (".count($terms1).") =<br />";
// highlight_string(print_r($terms1,true));
// echo "<br />";

				//recherche des termes pour lesquels on a une correspondance avec l'un des stems du document
				foreach($this->stems as $stem) {

					$q2_restrict = "1 ";
					if(count($used_word_ids)) {
						$q2_restrict = "id_word not in (".implode(',',$used_word_ids).")";
					}
					$q2 = "select word from words where $q2_restrict and stem='".addslashes($stem->label)."' ";
					if ($this->lang) {
						$q2.= "and lang='".$this->lang."' ";
					}
					$r2 = pmb_mysql_query($q2);
					if (!pmb_mysql_num_rows($r2) && $this->wo_lang) {
						$q2 = "select word from words where $q2_restrict and stem='".addslashes($stem->label)."' and lang='' ";
						$r2 = pmb_mysql_query($q2);
					}
					if (pmb_mysql_num_rows($r2)) {
						while ($row2 = pmb_mysql_fetch_object($r2)) {
							$q3 = "select num_noeud, libelle_categorie, num_renvoi_voir, path from noeuds join categories on num_noeud=id_noeud where $q_restrict ";
							if ($this->id_thesaurus) {
								$q3.= "and noeuds.num_thesaurus=".$this->id_thesaurus." ";
							}
							if ($this->lang) {
								$q3.= "and langue='".$this->lang."' ";
							}
							$q3.= "and index_categorie like '% ".$row2->word." %' ";
							$r3 = pmb_mysql_query($q3);
							if(pmb_mysql_num_rows($r3)) {
								while($row3 = pmb_mysql_fetch_object($r3)) {
									if(!$terms[$row3->num_noeud]) {
// $terms2[]=$row3->libelle_categorie;
										$terms[$row3->num_noeud]['label']=$row3->libelle_categorie;
										$terms[$row3->num_noeud]['see']=$row3->num_renvoi_voir;
										$terms[$row3->num_noeud]['path']=$row3->path;
										$terms[$row3->num_noeud]['words']=array();
										$terms[$row3->num_noeud]['stems']=array();
										$terms[$row3->num_noeud]['relevancy']=0;
									}
									$terms[$row3->num_noeud]['stems'][]=$stem->label.' --> '.$row2->word;
									$terms[$row3->num_noeud]['relevancy'] = $terms[$row3->num_noeud]['relevancy'] + $stem->relevancy;
								}
							}
						}
					}

				}

//TODO
// echo "Termes pours lesquels on a une correspondance avec l'un des stems pertinents du document (".count($terms2).") =<br />";
// print_r($terms2);
// echo "<br />";



			foreach($terms as $id=>$term) {
				$this->terms[]= new autoindex_term($id, $term['label'], $term['see'], $term['path'], $term['relevancy']);
			}

			//tri des termes sur raw_revelancy
			usort($this->terms, array("autoindex_term", "compare_raw_relevancies"));

//TODO
// echo "Termes contenant exactement un des mots, ou la racine d'un des mots pertinents du document (".count($this->terms)."), triés par raw_relevancy =<br />";
// print_r($this->terms);
// echo "<br />";

		}

	}


	/**
	 * 1 - calcul pour chaque terme sa pertinence totale
	 * 2 - appel du tri
	 *
	 *
	 * @return void
	 * @access public
	 */
	public function calc_total_terms_relevancy() {
		global $autoindex_max_up_distance;
		global $autoindex_max_down_distance;

		if(count($this->terms)) {
			foreach($this->terms as $term) {
				$term->calc_total_relevancy($this->terms, $autoindex_max_up_distance, $autoindex_max_down_distance);
			}
		}
	}


	/**
	 * 1 - Calcule pour chaque terme la somme des distances dans le document
	 *
	 * 2 - Si Dmax >0, Pour chaque terme où une distance a pu être calculée :
	 *  Ajustement de la pertinence de chaque terme avec : pertinence totale + (((Dmax
	 * - Dterme) / Dmax) * pertinence totale)
	 *
	 * 3 - appel du tri
	 *
	 * @return void
	 * @access public
	 */
	public function calc_document_terms_distances() {
		if(count($this->terms)) {
			foreach($this->terms as $term) {
				$term->calc_term_document_distance($this->full_clean_text);
			}
		}
	}


	/**
	 * Tri du tableau de termes par pertinence + Ecrêtage selon le seuil défini
	 * @param float level Seuil pour l'écrêtage du tableau

	 * @return void
	 * @access protected
	 */
	public function sort_terms( $level=0, $sort_function='compare_total_relevancies') {
		global $max_relevant_terms;

		//suppression des termes renvoyés déjà présents
		$ids=array();
		$sees=array();
		foreach($this->terms as $k=>$term) {
			$ids[$k]=$term->id;
			$sees[$k]=$term->see;
		}
		foreach($sees as $k_see=>$see) {
			$k_id = array_search($see,$ids);
			if($k_id!==false) {
				if($this->terms[$k_see]->total_relevancy > $this->terms[$k_id]->total_relevancy) {
					$this->terms[$k_id]->total_relevancy = $this->terms[$k_see]->total_relevancy;
				}
				unset ($this->terms[$k_see]);
			}
		}
		usort($this->terms, array("autoindex_term", $sort_function));

		if($level) {
			$done=false;
			while(!$done) {
				$term = end($this->terms);
				if($term && ($term->total_relevancy <= $level) ) {
					array_pop($this->terms);
				} else {
					$done=true;
				}
			}
		} elseif($max_relevant_terms) {
			while(count($this->terms) > $max_relevant_terms) {
				array_pop($this->terms);
			}
		}
	}


}