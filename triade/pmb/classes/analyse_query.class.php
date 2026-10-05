<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: analyse_query.class.php,v 1.108.2.3 2024/07/04 13:46:07 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/stemming.class.php");

//Structure de stockage d'un terme
class term {
	public $word; 		//mot (si pas sous expression)
	public $operator;	//opérateur (and, or ou vide)
	public $sub;		//sous expression = tableau de term
	public $not;		//Négation du terme (not)
	public $literal;	//Le terme est entier (il y avait des guillemets)
	public $start_with; //L'expression doit commencer par
	public $pound; 		//poids du terme
	public $special_term;	//on spécifie si le terme particulier

	//Constructeur
	public function __construct($word,$literal,$not,$start_with,$operator,$sub,$pound=1,$special_term='') {
		$this->word=$word;
		$this->operator=$operator;
		$this->sub=$sub;
		$this->not=$not;
		$this->literal=$literal;
		$this->start_with=$start_with;
		$this->pound=$pound;
		$this->special_term = $special_term;
	}
}

//Classe d'analyse d'une requête booléenne
class analyse_query {
	public $current_car;		//Caractère courant analysé
	public $parenthesis;		//Est-ce une sous expression d'une expression ?
	public $operator="";		//Opérateur du terme en cours de traitement
	public $neg=0;				//Négation appliquée au terme en cours de traitement
	public $guillemet=0;		//Le terme en cours de traitement est-il entouré par des guillemets
	public $start_with=0;		//Le terme en cours est-il à traiter avec commence par ?
	public $input;				//Requete à analyser
	public $term="";			//Terme courant
	public $literal=0;			//Le terme en cours de traitement est-il litéral ?
	public $tree=array();		//Arbre de résultat
	public $error=0;			//Y-a-t-il eu un erreur pendant le traitement
	public $error_message="";	//Message d'erreur
	public $input_html="";		//Affichage html de la requête initiale (éventuellement avec erreur surlignée)
	public $search_linked_words=1;	//rechercher les mots liés pour le mot
	public $keep_empty;			//on garde les mots vides
	public $positive_terms; 	//liste des termes positifs
	public $empty_words;		//Liste des mots vides présents dans la recherche initiale
	public $stemming;
	public $search_fixed_and_operator;
	protected $default_operator;
	protected $allow_term_troncat_search;
	protected $exclude_fields;
	protected $tmp_prefix = 'gestion_searcher_';
	protected $search_relevant_with_frequency;
	protected static $synonymes;

	//Constructeur
    public function __construct($input,$debut=0,$parenthesis=0,$search_linked_words=1,$keep_empty=0,$stemming=false) {
    	global $pmb_default_operator;
    	global $pmb_allow_term_troncat_search;
		global $pmb_search_exclude_fields;
		global $pmb_search_relevant_with_frequency;
		global $pmb_search_fixed_and_operator;
		global $pmb_synonym_search;

		$this->default_operator = $pmb_default_operator;
    	$this->allow_term_troncat_search = $pmb_allow_term_troncat_search;
    	$this->exclude_fields = $pmb_search_exclude_fields;
    	$this->search_relevant_with_frequency = $pmb_search_relevant_with_frequency;
    	$this->search_fixed_and_operator = $pmb_search_fixed_and_operator;

		$input=clean_nbsp($input);
    	$this->parenthesis=$parenthesis;
		$this->current_car=$debut;
		$this->input=$input;
		$this->keep_empty=$keep_empty;
		$this->stemming = $stemming;

		if ($pmb_synonym_search) {
			$this->search_linked_words = $search_linked_words;
		} else {
			$this->search_linked_words = 0;
		}

		$this->recurse_analyse();
		if($this->parenthesis==0){
			$this->tree=$this->nettoyage_etoile($this->tree,false);
		}
		if($this->stemming){
			$this->add_stemming();
		}
		// pour remonter les termes exacts, mais ne marche pas pour les autorités. A revoir
		if(!$parenthesis && ((!empty($this->tree)) && (!$this->tree[0]->not))) {
    		$empty_word = get_empty_words();
			if((!$this->keep_empty && in_array($this->input,$empty_word)===false) || $this->keep_empty) {
				$t=new term(trim($this->input,"_~\""),2,0,1,"or",null,0.2);
				$this->store_in_tree($t,0);
	    	}
    	}
    }

    public function nettoyage_etoile($tree_array,$is_sub){
    	//Pour chaque terme du tableau
    	foreach($tree_array as $key=>$value){
    		//Si le mot est *
    	    if( pmb_preg_match("/^\*+$/", $value->word) ) {
    			//On efface si on est dans une sub ou s'il y a d'autres termes
    			if($is_sub || (count($tree_array)>1)){
    				unset($tree_array[$key]);
    			}
    		}elseif(is_array($value->sub) && count($value->sub)){
    			//Le terme est une sub : on nettoie le tableau de termes de la sub
    			$tree_array[$key]->sub = $this->nettoyage_etoile($tree_array[$key]->sub,true);
    			//Si la sub est vide, on l'efface
    			if(!count($tree_array[$key]->sub)){
    				unset($tree_array[$key]);
    			}
    		}
    	}
    	//Recalcul des index
    	$tree_array = array_values($tree_array);
    	return $tree_array;
    }

	// Recherche les synonymes d'un mot
	public function get_synonymes($mot) {
    	if(!isset(self::$synonymes[$mot])) {
    		$synonymes = array();
			$mot= addslashes($mot);
			$rqt="select id_mot from mots where mot='".$mot."'";
			$execute_query=pmb_mysql_query($rqt);
			if (pmb_mysql_num_rows($execute_query)) {
				//constitution d'un tableau avec le mot et ses synonymes
				$r=pmb_mysql_fetch_object($execute_query);
				$rqt1="select mot,ponderation from mots,linked_mots where type_lien=1 and num_mot=".$r->id_mot." and mots.id_mot=linked_mots.num_linked_mot";
				$execute_query1=pmb_mysql_query($rqt1);
		 		if (pmb_mysql_num_rows($execute_query1)) {
					while (($r1=pmb_mysql_fetch_object($execute_query1))) {
						$synonymes[$r1->mot]=$r1->ponderation;
					}
		 		}
			}
			self::$synonymes[$mot] = $synonymes;
    	}
		return self::$synonymes[$mot];
	 }

	public function nettoyage_mot_vide($string) {
 		//Récupération des mots vides
 		$empty_word = get_empty_words();
		//Supression des espaces avant et après le terme
		$string = trim($string);
		//Décomposition en mots du mot nettoyé (ex : l'arbre devient l arbre qui donne deux mots : l et arbre)
		$words=array();
		if ($string) {
			$words=explode(" ",$string);
		}
		//Variable de stockage des mots restants après supression des mots vides
		$words_empty_free=array();
		//Pour chaque mot
		for ($i=0; $i<count($words); $i++) {
			$words[$i]=trim($words[$i]);
			//Vérification que ce n'est pas un mot vide
			if (($this->keep_empty)||(in_array($words[$i],$empty_word)===false)) {
				//Si ce n'est pas un mot vide, on stoque
				$words_empty_free[]=$words[$i];
			}
		}
		return $words_empty_free;
	}

	public function calcul_term(&$t, $mot, $litteral, $ponderation) {
		// Littéral ?
		if (!empty($litteral)) {
			// Oui c'est un mot littéral
			$t->word = $mot;
			$t->literal = 1;
			// fin
			return;
		} else {
			// Non ce n'est pas un mot littéral
			// Un espace dans le mot?
			if (strstr($mot, ' ')) {
				// Oui ula un espace
				$t->word = $mot;
				$t->literal = 1;
				// fin
				return;
			} else {
				// Non, pas d'espace dans le mot
				// Nettoyage des caractères
				$mot_clean = convert_diacrit($mot);

				$mot_clean = pmb_alphabetic('^a-z0-9\s\*', ' ',pmb_strtolower($mot_clean));
				// Nettoyage des mots vides
				$mot_clean_vide=$this->nettoyage_mot_vide($mot_clean);
				// Combien de mots reste-t-il ?
				if(count($mot_clean_vide) > 1) {
					// Plusieurs
					$terms=array();
					if (!is_array($t->sub) || !count($t->sub)) $op_sub=''; else $op_sub="or";
					foreach($mot_clean_vide as $key => $word) {
						if($key == 0){
							$terms[]=new term($word,0,0,0,"","",$ponderation);
						}else{
							$terms[]=new term($word,0,0,0,$op_sub,"",$ponderation);
						}
						$op_sub="or";
					}
					$t->sub=$terms;
				} elseif(count($mot_clean_vide) == 1)  {
					// Un seul
					$t->word=$mot_clean_vide[0];
					$t->literal=0;
					// fin
					return;
				}else return;
			}
		}
	}

    //Stockage d'un terme dans l'arbre de résultat
	public function store_in_tree($t,$search_linked_words) {
 		// Mot ou expression ?
 		if (!$t->sub && $t->word) {
 			//C'est un mot
 			// Synonyme activé && ce n'est pas une expression commence par '_xx*' ?
 			if ($search_linked_words && !$this->start_with) {
 				// Oui, Synonyme activé
 				// C'est un littéral ?
 				if ($t->literal) {
 					// Oui, c'est un littéral
 					// Recherche de synonymes
 					$synonymes=$this->get_synonymes($t->word);
					$mots=$t->word;

 					// Y-a-t'il des synonymes ?
 					if($synonymes) {
 						// Oui il y a des synonymes
	 					// Pour chaque synonyme et le terme ajout à $t->sub
	 					$op_sub="";
						foreach($synonymes as $synonyme => $ponderation) {

							$t->sub[]=new term($synonyme,0,0,0,$op_sub,"",$ponderation);
							$this->calcul_term($t->sub[count($t->sub)-1],$synonyme,0,$ponderation);
							$op_sub="or";
						}
						// Ajout du term force litéral à 1
						$t->word="";
						$t->sub[]=new term($mots,1,0,0,$op_sub,"",$t->pound);
						$this->calcul_term($t->sub[count($t->sub)-1],$mots,1,$t->pound);
						$op_sub="or";

 					}
 				} else {
 					$liste_mots = array();
 					// Non, ce n'est pas un littéral
 					// Recherche de synonymes
  					$synonymes=$this->get_synonymes($t->word);
					$mots=$t->word;
					$t->word="";

 					// Y-a-t'il des synonymes ?
 					if($synonymes) {
 						// Oui il y a des synonymes
						foreach($synonymes as $synonyme => $ponderation) {
							$liste_mots[$synonyme]=$ponderation;
						}
 					}
 					// Suite et, Non, il n'y a pas de synonyme
	 				// Nettoyage des caractères
					$mot_clean = convert_diacrit($mots);
					$mot_clean = pmb_alphabetic('^a-z0-9\s\*', ' ',pmb_strtolower($mot_clean));
					// Nettoyage des mots vides
					$mot_clean_vide=$this->nettoyage_mot_vide($mot_clean);

					// TODO : Prendre en compte les mots-composés comme en 301

					// Pour chaque mot nettoyer
					if(count($mot_clean_vide)) foreach($mot_clean_vide as $word) {
		 				// Recherche de synonymes
		 				$synonymes_clean=$this->get_synonymes($word);
		 				// Pour chaque synonyme et le terme ajout à $t->sub
						if(count($synonymes_clean))foreach($synonymes_clean as $synonyme => $ponderation) {
							$liste_mots[$synonyme]=$ponderation;
						}
					}

					// ajout des mots nettoyés
					if(count($mot_clean_vide))foreach($mot_clean_vide as $word) {
						$liste_mots[$word]=$t->pound;
					}

					if (!is_array($t->sub) || !count($t->sub)) $op_sub=''; else $op_sub="or";
					if(count($liste_mots) > 1) {
						$t->word="";
						// Plusieurs mots
						foreach($liste_mots as $word => $ponderation) {
							$t->sub[]=new term($word,0,0,0,$op_sub,"",$ponderation);
							$this->calcul_term($t->sub[count($t->sub)-1],$word,0,$ponderation);
							$op_sub="or";
						}
						//$t->sub=$terms;
					} elseif(count($liste_mots) == 1)  {
						// Un seul mot
						foreach($liste_mots as $word=> $ponderation) {
							$t->word=$word;
						}
					} else return;
 				}
 			} else {
 				// Non, Synonyme désactivé
 				// C'est un littéral ?
 				if ($t->literal) {
 					// Oui, c'est un littéral
 					// plus rien à faire
 				} else {
 					// Non, ce n'est pas un littéral
 					// Nettoyage des caractères
					$mot_clean = convert_diacrit($t->word);

					$mot_clean = pmb_alphabetic('^a-z0-9\s\*', ' ',pmb_strtolower($mot_clean));
 					// Nettoyage des mots vides
					$mot_clean_vide=$this->nettoyage_mot_vide($mot_clean);
					// Combien de mots reste-t-il ?
					if(count($mot_clean_vide) > 1) {
						$t->word="";
						// Plusieurs mots
						if (!is_array($t->sub) || !count($t->sub)) $op_sub=''; else $op_sub="and";
						$terms = array();
						foreach($mot_clean_vide as $word) {
						    $terms[]=new term($word,0,0,0,$op_sub,"",$t->pound);
							$op_sub="and";
						}
						$t->sub=$terms;
					} elseif(count($mot_clean_vide) == 1)  {
						// Un seul mot
						$t->word=$mot_clean_vide[0];
					} else return;
 				}
 			}
 		} elseif ($t->sub && !$t->word) {
 			// C'est une expression :
 			// Vider opérateur
 			if (!count($this->tree)) $t->operator="";
 		} else {
 			//	Ce n'est ni un mot, ni une exrssion: c'est rien
 			return;
 		}
 		// Inscription dans l'arbre
 		if(count($this->tree) < 50) { // Limitons à 50 mots
 			$this->tree[]=$t;
 		}
		//print "<pre>";print_r($this->tree);print"</pre>";
 	}

	//Affichage sous forme RPN du résultat de l'analyse
	public function show_analyse_rpn($tree = array()) {
		//Si tree vide alors on prend l'arbre de la classe
		if (empty($tree)) $tree = $this->tree;
		$r="";
		//Pour chaque branche ou feuille de l'arbre
		for ($i=0; $i<count($tree); $i++) {
			//Si le terme est un mot
			if ($tree[$i]->sub==null) {
				//Affichage du mot avec le préfixe N pour terme Normal et L pour terme litéral, C pour Commence par
				if ($tree[$i]->start_with) $r.="C ";
				if ($tree[$i]->literal) $r.="L "; else $r.="N ";
				$r.=$tree[$i]->word."\n";
			} else
				//Sinon on analyse l'expression
				$r.=$this->show_analyse_rpn($tree[$i]->sub);
			//Affichage négation et opérateur si nécessaire
			if ($tree[$i]->not) $r.="not\n";
			if ($tree[$i]->operator) $r.=$tree[$i]->operator."\n";
		}
		return $r;
	}

	//Affichage sous forme mathématique logique du résultat de l'analyse
	public function show_analyse($tree = array()) {
		if (empty($tree)) $tree = $this->tree;
		$r="";
		for ($i=0; $i<count($tree); $i++) {
			if ($tree[$i]->operator) $r.=$tree[$i]->operator." ";
			if ($tree[$i]->not) $r.="not";
			if ($tree[$i]->sub==null) {
				if ($tree[$i]->start_with) $start_w="start with "; else $start_w="";
				if ($tree[$i]->not) $r.="(";
				$r.=$start_w;
				if ($tree[$i]->literal) $r.="\"";
				$r.=$tree[$i]->word;
				if ($tree[$i]->literal) $r.="\"";
				if ($tree[$i]->not) $r.=")";
				$r.=" ";
			} else { $r.="( ".$this->show_analyse($tree[$i]->sub).") "; }
		}
		return $r;
	}

	//Construction récursive de la requête SQL
	public function get_query_r($tree,&$select,&$pcount,$table,$field_l,$field_i,$id_field,$neg_parent=0,$main=1) {

		// Variable permettant de choisir si l'on utilise ou non la troncature à droite du terme recherché
		$empty_word = get_empty_words();
		$troncat = "";
		if ($this->allow_term_troncat_search) {
			 $troncat = "%";
		}

		$where="";
		for ($i=0; $i<count($tree); $i++) {

			if (($tree[$i]->operator)&&($tree[$i]->literal!=2)) $where.=$tree[$i]->operator." ";
			if ($tree[$i]->sub==null) {
				if ($tree[$i]->literal) $clause="trim(".$field_l.") "; else $clause=$field_i." ";
				if ($tree[$i]->not) $clause.="not ";
				$clause.="like '";
				if (!$tree[$i]->start_with) $clause.="%";
				if (!$tree[$i]->literal) $clause.=" ";

				// Condition permettant de détecter si on a déjà une étoile dans le terme
				// Si la recherche avec troncature à droite est activee dans l'administration
				// et qu'il n'y a pas d'étoiles ajout du '%' à droite du terme
				if(strpos($tree[$i]->word,"*") === false) {
					//Si c'est un mot vide, on ne troncature pas
					if (in_array($tree[$i]->word,$empty_word)===false) {
						$clause.=addslashes($tree[$i]->word.$troncat);
					} else {
						$clause.=addslashes($tree[$i]->word);
					}
				} else {
					$clause.=addslashes(str_replace("*","%",$tree[$i]->word));
				}

				if (!$tree[$i]->literal) $clause.=" ";
				$clause.="%'";
				if($tree[$i]->literal!=2) $where.=$clause." ";
				//if ((!$tree[$i]->not)&&(!$neg_parent)) {
					if ($select) $select.="+";
					$select.="(".$clause.")";
					if ($tree[$i]->pound && ($tree[$i]->pound!=1)) $select.="*".$tree[$i]->pound;
					$pcount++;
				//}
			} else {
				if ($tree[$i]->not) $where.="not ";
				//$tree[$i]->not
				$where.="( ".$this->get_query_r($tree[$i]->sub,$select,$pcount,$table,$field_l,$field_i,$id_field,$tree[$i]->not,0).") ";
			}
		}
		if ($main) {
			if ($select=="") $select="1";
			if ($where=="") $where="0";
			$q=array();
			$q["select"]="(".$select.")";
			$q["where"]="(".$where.")";
			$q["post"]=" group by ".$id_field." order by pert desc,".$field_i." asc";
			return $q;
		}
		else
			return $where;
	}

	//Fonction d'appel de la construction récursive de la requête SQL
	public function get_query($table,$field_l,$field_i,$id_field,$restrict="",$offset=0,$n=0) {
		$select="";
		$pcount=0;
		$q=$this->get_query_r($this->tree,$select,$pcount,$table,$field_l,$field_i,$id_field,0,1);
		$res="select ".$id_field.",".$q["select"]." as pert from ".$table." where (".$q["where"].")";
		if ($restrict!="") $res.=" and ".$restrict;
		$res.=$q["post"];
		if ($n!=0) $res.=" limit ".$offset.",".$n;
		return $res;
	}

	public function get_query_members($table,$field_l,$field_i,$id_field,$restrict="",$offset=0,$n=0,$is_fulltext=false) {
		global $pmb_search_full_text;
		if (($is_fulltext)&&($pmb_search_full_text)) $q=$this->get_query_full_text($table,$field_l,$field_i,$id_field); else {
			$select="";
			$pcount=0;
			$q=$this->get_query_r($this->tree,$select,$pcount,$table,$field_l,$field_i,$id_field,0,1);
		}
		if ($restrict) $q["restrict"]=$restrict;
		return $q;
	}

	//Adaptation de la recherche pour notices_mots_global_index
	public function get_query_mot($field_id,$table_mot,$field_mot,$table_term,$field_term,$restrict=array(),$neg_restrict=false,$all_fields=false) {
		if(count($this->tree)){
			//return $this->get_query_r_mot($this->tree,$field_id,$table_mot,$field_mot,$table_term,$field_term,$restrict,$neg_restrict);
			return $this->get_query_r_mot_with_table_tempo_all($this->tree,$field_id,$table_mot,$field_mot,$table_term,$field_term,$restrict,$neg_restrict,$all_fields);
		}else{
			return  "select $field_id from $table_mot where $field_id = 0";
		}
	}

	public function get_query_r_mot_with_table_tempo_all($tree,$field_id,$table_mot,$field_mot,$table_term,$field_term,$restrict,$neg_restrict=false,$all_fields=false) {
		// Variable permettant de choisir si l'on utilise ou non la troncature à droite du terme recherché
		$empty_word = get_empty_words();

		$temporary_table = $last_table = "";
		$troncat = "";
		if ($this->allow_term_troncat_search) {
			$troncat = "%";
		}
		$lang_restrict = $field_restrict = array();
		for($i=0 ; $i<count($restrict) ; $i++){
			if(isset($restrict[$i]['field']) && $restrict[$i]['field'] == "lang"){
				$lang_restrict[] = $restrict[$i];
			}else{
				$field_restrict[] = $restrict[$i];
			}
		}
		$restrict = $field_restrict;
		for ($i=0; $i<count($tree); $i++) {
			$elem_query ="";
			if($tree[$i]->sub){
				$elem_query = $this->get_query_r_mot_with_table_tempo_all($tree[$i]->sub,$field_id,$table_mot,$field_mot,$table_term,$field_term,$restrict,$neg_restrict,$all_fields);
			}else{
				if($tree[$i]->word !==  "*") {
					if($tree[$i]->literal == 0){
						// on commence...
						$qw = "select distinct id_word from words where ".(count($lang_restrict)>0 ? $this->get_field_restrict($lang_restrict,$neg_restrict)." and ": "");

						$table= $table_mot;
						//on ajoute le terme
						$qw.= " words.".$field_mot." ";
						if(strpos($tree[$i]->word, "*") !== false || ($this->allow_term_troncat_search && (strlen($tree[$i]->word)>2))){
							$qw.="like '";
							if (strpos($tree[$i]->word,"*") === false) {
								//Si c'est un mot vide, on ne troncature pas
								if (in_array($tree[$i]->word,$empty_word)===false) {
									$qw.=addslashes($tree[$i]->word.$troncat);
								} else {
									$qw.=addslashes($tree[$i]->word);
								}
							} else {
								$qw.=addslashes(str_replace("*","%",$tree[$i]->word));
							}
							$qw.="'";
						}else{
							$qw.="='".addslashes($tree[$i]->word)."'";
						}
						$tw = array();
						$rw = pmb_mysql_query($qw);
						if(pmb_mysql_num_rows($rw)) {
							while($o = pmb_mysql_fetch_object($rw) ) {
								$tw[]=$o->id_word;
							}
						}
						$elem_query = "select distinct $field_id from $table_mot ".gen_where_in($table_mot.'.num_word', $tw)." and pond>0 ";
						if($restrict){
							$elem_query.= ' and '.$this->get_field_restrict($restrict,$neg_restrict);
						}
						if($tree[$i]->start_with){
						    $elem_query.=" and field_position ='1'";
						}
					}else if ($tree[$i]->literal == 1){
						// on commence...
						$elem_query = "select distinct $field_id from $table_term where pond>0 and ";
						//on applique la restriction si besoin
						if($restrict){
							$elem_query.= $this->get_field_restrict($restrict,$neg_restrict)." and ";
						}
						$table= $table_term;
						//on ajoute le terme
						$elem_query.= " ".$table.".".$field_term." ";
						$elem_query.= "like '";
						if(!$tree[$i]->start_with){
							$elem_query.="%";
						}
						if (strpos($tree[$i]->word,"*") === false) {
							//Si c'est un mot vide, on ne troncature pas
							if (in_array($tree[$i]->word,$empty_word)===false) {
								$elem_query.=addslashes($tree[$i]->word.$troncat);
							} else {
								$elem_query.=addslashes($tree[$i]->word);
							}
						} else {
							$elem_query.=addslashes(str_replace("*","%",$tree[$i]->word));
						}
						$elem_query.="%'";
					}
					if($table_mot == "notices_mots_global_index" && $all_fields && $this->exclude_fields!=""){
						$elem_query.= " and code_champ not in (".$this->exclude_fields.")";
					}
				}else if(count($tree)==1){
					$elem_query = "select distinct $field_id from $table_mot where 1";
					if($restrict){
						$elem_query.= " and ".$this->get_field_restrict($restrict,$neg_restrict)." ";
					}
					if($table_mot == "notices_mots_global_index" && $all_fields && $this->exclude_fields!=""){
						$elem_query.= " and code_champ not in (".$this->exclude_fields.")";
					}
				}
			}
			if($tree[$i]->literal!=2 && $elem_query!= ""){
				$last_table = $temporary_table;
				$temporary_table = $this->tmp_prefix.md5(microtime(true)."#".$this->keep_empty.$i);
				switch($tree[$i]->operator){
					case "and" :
						//on créé la table tempo avec les résultats du critère...
						$rqt = "create temporary table ".$temporary_table." ($field_id int, index using btree($field_id)) engine=memory $elem_query";
						pmb_mysql_query($rqt);
						if($tree[$i]->not){
							$rqt = "delete from ".$last_table." where ".$field_id." in (select ".$field_id." from ".$temporary_table.")";
							pmb_mysql_query($rqt);
							pmb_mysql_query("drop table if exists ".$temporary_table);
							$temporary_table = $last_table;
							$last_table = "";
						}else{
							$new_temporary_table =$this->tmp_prefix.md5(microtime(true)."new_temporary_table".$this->keep_empty.$i);
							$rqt = "create temporary table ".$new_temporary_table." ($field_id int, index using btree($field_id)) engine=memory select $last_table.$field_id from $last_table join $temporary_table on $last_table.$field_id = $temporary_table.$field_id";
							pmb_mysql_query($rqt);
							pmb_mysql_query("drop table if exists ".$temporary_table);
							pmb_mysql_query("drop table if exists ".$last_table);
							$temporary_table=$new_temporary_table;
						}
						break;
					case "or" :
						$temporary_table = $last_table;
						if($tree[$i]->not){
							$rqt = "create temporary table tmp_".$temporary_table." ($field_id int, index using btree($field_id)) engine=memory $elem_query";
							pmb_mysql_query($rqt);
							$rqt = "insert ignore into ".$temporary_table." select distinct $field_id from $table_term where ".$field_id." not in(select ".$field_id." from tmp_".$temporary_table.") and code_champ = 1";
							pmb_mysql_query($rqt);
							pmb_mysql_query("drop table if exists tmp_".$temporary_table);
							$last_table= "";
						}else{
							$rqt = "insert ignore into ".$temporary_table." ".$elem_query;
							$last_table= "";
							pmb_mysql_query($rqt);
						}
						break;
					default :
						if($tree[$i]->not){
							$rqt = "create temporary table tmp_".$temporary_table." ($field_id int, index using btree($field_id)) engine=memory $elem_query";
							pmb_mysql_query($rqt);
							$rqt = "create temporary table ".$temporary_table." ($field_id int, index using btree($field_id)) engine=memory select distinct $field_id from $table_term where ".$field_id." not in(select ".$field_id." from tmp_".$temporary_table.") and code_champ = 1";
							pmb_mysql_query($rqt);
							pmb_mysql_query("drop table if exists tmp_".$temporary_table);
						}else{
							$rqt = "create temporary table ".$temporary_table." ($field_id int, index using btree($field_id)) engine=memory $elem_query";
							pmb_mysql_query($rqt);
						}
						break;
				}
				$query = "select distinct $field_id from ".$temporary_table;
				if($last_table != ""){
					pmb_mysql_query("drop table if exists ".$last_table);
				}
			}
		}
		return $query;
	}

	public function get_field_restrict($restrict,$neg=false){
		$return = "";
		foreach($restrict as $infos){
			if(isset($infos['values'])){
				if ($return != "") $return.=" ".$infos['op']." ";
				$return .= ($infos['not'] ? "not ":"")."(".$infos["field"];
				if(is_array($infos['values'])){
					$return .= " in ('".implode("','",$infos['values'])."')";
				}else{
					$return .= "='".$infos['values']."'";
				}

				if(isset($infos['sub']) && is_array($infos['sub']) && count($infos['sub'])){
					$sub ="";

					foreach($infos['sub'] as $subinfos){
						if ($sub != "") $sub .= " ".$subinfos['op'];
						$sub .= " ".($subinfos['not'] ? "not ":"")."(".$subinfos["sub_field"];
						if(is_array($subinfos['values'])){
							$sub .= " in ('".implode("','",$subinfos['values'])."')";
						}else{
							$sub .= "='".$subinfos['values']."'";
						}
						$sub .= ")";
					}
					if ($return != '') {
						$return.= " and";
					}
					$return.= " (".$sub.")";
				}
				$return.= ")";
			}else{
				if ($return != '') {
					$return.= " and";
				}
				$return.= ' '.$this->get_field_restrict($infos['sub'],$neg);
			}
		}
		if($neg){
			$return = "not (".$return.")";
		}else{
			$return = "(".$return.")";
		}
		return $return;
	}

	public function get_query_full_text($table,$field_l,$field_i,$id_field) {
		global $pmb_default_operator;

		if($pmb_default_operator == 1) {
			$against_value = ' +'.str_replace(' ', ' +', $this->input);
		} else {
			$against_value = $this->input;
		}
		$q=array();
		$q["select"]="(match($field_l) against ('".addslashes($against_value)."' in boolean mode))";
		$q["where"]="(match($field_l) against ('".addslashes($against_value)."' in boolean mode))";
		$q["post"]=" group by ".$id_field." order by pert desc,".$field_i." asc";
		return $q;
	}

	//Requête de comptage des résultats
	public function get_query_count($table,$field_l,$field_i,$id_field,$restrict="") {
		$select="";
		$pcount=0;
		$q=$this->get_query_r($this->tree,$select,$pcount,$table,$field_l,$field_i,$id_field,0,1);
		$res="select count(distinct ".$id_field.") from ".$table." where (".$q["where"].")";
		if ($restrict!="") $res.=" and ".$restrict;
		return $res;
	}

	//Analyse de la requête saisie (machine d'état)
	public function recurse_analyse() {
		global $msg;
		global $charset;

		$s="new_word";
		$end=false;

		while (!$end) {
			switch ($s) {
				//Début d'un nouveau terme
				case "new_word":
					if ($this->current_car>(pmb_strlen($this->input)-1)) {
						$end=true;
						if ($this->parenthesis) {
							$this->error=1;
							$this->error_message=$msg["aq_missing_term_and_p"];
							break;
						}
						if ($this->guillemet) {
							$this->error=1;
							$this->error_message=$msg["aq_missing_term_and_g"];
							break;
						}
						break;
					}
					$cprec=pmb_getcar($this->current_car - 1,$this->input);
					$c=pmb_getcar($this->current_car,$this->input);
					$this->current_car++;
					//Si terme précédé par un opérateur (+, -, ~) et pas d'opérateur et pas de guillemet ouvert et pas de commence par :
					//affectation opérateur. Néanmoins, si c'est le premier terme on n'en tient pas compte
					if ((($c=="|")||($c=="+")||($c=="-" && $cprec == " ")||($c=="~"))&&($this->operator=="")&&(!$this->guillemet)&&(!$this->neg)&&(!$this->start_with)) {
					    //opérateur ou ajouté
					    if (($c=="|")&&(count($this->tree))) {
								$this->operator="or";
					    }
						if ( ($c=="+")&& (count ($this->tree) ) ) {
								$this->operator="and";
							//peut être un OU si opérateur par défaut ET
							if ($this->default_operator == 1 && $this->search_fixed_and_operator == 0) {
								$this->operator="or";
							}
						} else if (($c=="-" && $cprec == " ")&&(count($this->tree))) {
							$this->operator="and";
							$this->neg=1;
						} else if ((($c=="-" && $cprec == " ")&&(!count($this->tree)))||($c=="~")) $this->neg=1;
						//Après l'opérateur, on continue à chercher le début du terme
						$s="new_word";
						break;
					}
					//Si terme précédé par un opérateur et qu'il y a déjà un opérateur ou un commence par et qu'on est pas
					//dans des guillemets alors erreur !
					if ((($c=="+")||($c=="-" && $cprec == " ")||($c=="~"))&&(!$this->guillemet)&&(($this->operator!="")||($this->neg)||($this->start_with))) {
						if (!$this->start_with) {
							if (($c=="~")&&($this->operator=="and")) {
								if (!$this->neg)
									$message_op=$msg["aq_and_not_error"];
								else
									$message_op=$msg["aq_minus_error"];
							} else if ((($c=="+")||($c=="-" && $cprec == " "))&&($this->neg)&&(!$this->operator)) {
								$message_op=sprintf($msg["aq_neg_error"],$c);
							}  else {
								$message_op=$msg["aq_only_one"];
							}
						} else $message_op=$msg["aq_start_with_error"];
						$end=true; $this->error_message=$message_op; $this->error=1; break;
					}
					//Si terme précédé par "commence par" et qu'on est pas dans les guillemets alors opérateur commence par activé
					if (($c=="_")&&(!$this->guillemet)) {
						$this->start_with=1;
						break;
					}

					//Si premier guillemet => terme litéral
					if (($c=="\"")&&($this->guillemet==0))	{
						$this->guillemet=1;
						$this->literal=1;
						//Après le guillemets, on continue à chercher le début du terme
						break;
					}
					//Si guillement et guillemet déjà ouvert => annulation du terme litéral
					if (($c=="\"")&&($this->guillemet==1)) {
						$this->guillemet=0;
						$this->literal=0;
						//Après le guillemets, on continue à chercher le début du terme
						break;
					}
					//Si il y a un espace et pas dans les guillemets, on en tient pas compte
					if (($c==" ")&&(!$this->guillemet)) break;
					//Si une parentèse ouverte, alors analyse de la sous expression
					if (($c=="(")&&(!$this->guillemet)) {
						$sub_a=new analyse_query($this->input,$this->current_car,1,$this->search_linked_words,$this->keep_empty,$this->stemming);
						//Si erreur dans sous expression, erreur !
						if ($sub_a->error) {
							$this->error=1;
							//Mise à jour du caractère courant où s'est produit l'erreur
							$this->current_car=$sub_a->current_car;
							$this->error_message=$sub_a->error_message;
							$end=true;
							break;
						} else {
							//Si pas d'erreur, stockage du résultat dans terme
							$this->term=$sub_a->tree;
							//Si il n'y a pas d'opérateur et que ce n'est pas le premier terme,
							//opérateur par défaut
							//if ((!$this->operator)&&(count($this->tree))) $this->operator="or";
							if ((!$this->operator)&&(count($this->tree))){
      							if ($this->default_operator == 1) {
      								$this->operator="and";
      							} else {
      								$this->operator="or";
      							}
      						}
							$this->current_car=$sub_a->current_car;
							//Début Attente du prochain terme
							$s="space_first";
							break;
						}
					}
					//Si parentèse fermante et parentèse déjà ouverte alors on s'en va
					if (($c==")")&&($this->parenthesis)&&(!$this->guillemet)) {
						$end=true;
						break;
					}
					//Si aucun des cas précédents, c'est le début du terme
					$this->term.=$c;
					//Si il n'y a pas d'opérateur et que ce n'est pas le premier terme,
					//opérateur par défaut
					//if ((!$this->operator)&&(count($this->tree))) $this->operator="or";
					if ((!$this->operator)&&(count($this->tree))){
						if ($this->default_operator == 1) {
							$this->operator="and";
						} else {
							$this->operator="or";
						}
					}
					//Lecture du terme
					$s="stockage_car";
					break;
				//Lecture d'un terme
				case "stockage_car":
					if ($this->current_car>(pmb_strlen($this->input)-1)) {
						//Si on lit une sous expression et qu'on arrive à la fin avant la parentèse fermante
						//alors erreur
						//sinon, passage à l'état attente du prochain terme (pourquoi me direz-vous alors qu'on arrive à la fin ? parceque ce cas est géré en space_first)
						if ($this->guillemet) { $this->error_message=$msg["aq_missing_g"]; $end=true; $this->error=1; break; }
						if ($this->parenthesis) { $this->error_message=$msg["aq_missing_p"]; $end=true; $this->error=1; break; }
						$s="space_first";
					}
					//Lecture caractère
					$cprec=pmb_getcar($this->current_car - 1,$this->input);
					$c=pmb_getcar($this->current_car,$this->input);
					$this->current_car++;
					//Si espace et terme litéral : l'espace fait partie du terme
					if ((($c=="|")||($c==" ")||($c=="+")||($c=="-" && $cprec == " "))&&($this->guillemet==1)) { $this->term.=$c; break; }
					//Si espace et terme non litéral : espace = séparateur de terme => passage à Début Attente du prochain terme
					if ((($c=="|")||($c==" ")||($c=="+")||($c=="-" && $cprec == " "))&&($this->guillemet==0)) { $s="space_first"; $this->current_car--; break; }
					//Si guillemet et terme litéral : guillemet = fin du terme => passage à Début Attente du prochain terme
					if (($c=="\"")&&($this->guillemet==1)) { $s="space_first"; $this->guillemet=0; break; }
					//Si parentèse fermante et sous-expression et que l'on est pas dans un terme litéral,
					//alors fin de sous expression à analyser => passage à Début Attente du prochain terme
					if (($c==")")&&($this->parenthesis==1)&&($this->guillemet==0)) { $s="space_first"; $this->current_car--; break; }
					//Si aucun des cas précédent, ajout du caractère au terme... et on recommence
					$this->term.=$c;
					break;
				//Début Attente du prochain terme après la fin d'un terme
				//A ce niveau, on s'attend à un caractère séparateur et si on le trouve, on enregistre le terme dans l'arbre
				//Ensuite on passe à l'état attente du prochain terme ("space_wait") qui saute tous les caractères vides avant de renvoyer à new_word
				case "space_first":
					if ($this->current_car>(pmb_strlen($this->input)-1)) {
						//Si fin de chaine et parentèse ouverte => erreur
						if ($this->parenthesis) { $end=true; $this->error_message=$msg["aq_missing_p"]; $this->error=1; break; }
						//Sinon c'est la fin de l'analyse : on enregistre le dernier terme et on s'arrête
						$end=true;
						if (is_array($this->term))
							$t=new term("",$this->literal,$this->neg,$this->start_with,$this->operator,$this->term);
						else
							$t=new term($this->term,$this->literal,$this->neg,$this->start_with,$this->operator,null);
						$this->store_in_tree($t,$this->search_linked_words);
						break;
					}
					//Lecture du prochain caractère
					$cprec=pmb_getcar($this->current_car - 1,$this->input);
					$c=pmb_getcar($this->current_car,$this->input);
					$this->current_car++;
					//Si parentèse fermante et sous expression en cours d'analyse => fin d'analyse de la sous expression
					if (($c==")")&&($this->parenthesis)) {
						$end=true;
						//Enregistrement du dernier terme
						if (is_array($this->term))
							$t=new term("",$this->literal,$this->neg,$this->start_with,$this->operator,$this->term);
						else
							$t=new term($this->term,$this->literal,$this->neg,$this->start_with,$this->operator,null);
						$this->store_in_tree($t,$this->search_linked_words);
						break;
					}
					//Sinon, si ce n'est pas un espace, alors erreur (ce n'est pas le séparateur attendu)
					if (($c!=" ")&&($c!="+")&&($c!="-" && $cprec!=" ")) {
						$end=true; $this->error_message=$msg["aq_missing_space"]; $this->error=1; break;
					}
					//Si tout va bien, on attend le prochain terme
					if ($c!=" ")
						$this->current_car--;
					$s="space_wait";
					break;
				//Attente du prochain terme : on saute tous les espaces avant de renvoyer à la lecture du nouveau terme !
				case "space_wait":
					if ($this->current_car>(pmb_strlen($this->input)-1)) {
						//Si prentèse ouverte et fin de la chaine => erreur
						if ($this->parenthesis) { $end=true; $this->error_message=$msg["aq_missing_p"]; $this->error=1; break; }
						//Sinon, si fin de la chaine, enregistrement du terme précédent et fin d'analyse
						if (is_array($this->term))
							$t=new term("",$this->literal,$this->neg,$this->start_with,$this->operator,$this->term);
						else
							$t=new term($this->term,$this->literal,$this->neg,$this->start_with,$this->operator,null);
						$this->store_in_tree($t,$this->search_linked_words);
						$end=true;
						break;
					}
					//Lecture du caractère suivant
					$c=pmb_getcar($this->current_car,$this->input);
					$this->current_car++;
					//Si ) et sous expression en cours d'analyse, fin de l'analyse de la sous expression
					if (($c==")")&&($this->parenthesis==1))	{
						//Enregistrement du terme et fin d'analyse
						if (is_array($this->term))
							$t=new term("",$this->literal,$this->neg,$this->start_with,$this->operator,$this->term);
						else
							$t=new term($this->term,$this->literal,$this->neg,$this->start_with,$this->operator,null);
						$this->store_in_tree($t,$this->search_linked_words);
						$end=true;
						break;
					}
					//Si le caractère n'est pas un espace, alors c'est le début du prochain terme
					if ($c!=" ") {
						$this->current_car--;
						//Enregistrement du dernier terme
						if (is_array($this->term))
							$t=new term("",$this->literal,$this->neg,$this->start_with,$this->operator,$this->term);
						else
							$t=new term($this->term,$this->literal,$this->neg,$this->start_with,$this->operator,null);
						$this->store_in_tree($t,$this->search_linked_words);
						//Remise à zéro des indicateurs
						$this->operator="";
						$this->term="";
						$this->neg=0;
						$this->literal=0;
						$this->start_with=0;
						//Passage à nouveau terme
						$s="new_word";
						break;
					}
					//Sinon on reste en attente
					break;
			}
		}
		if ($this->error) {
			$this->input_html=pmb_substr($this->input,0,$this->current_car-1)."!!red!!".pmb_substr($this->input,$this->current_car-1,1)."!!s_red!!".pmb_substr($this->input,$this->current_car);
		} else $this->input_html=$this->input;
		if ((!$this->error)&&(!count($this->tree))) {
			$this->error=1;
			$this->error_message=$msg["aq_no_term"];
		}
		$this->input_html=htmlentities($this->input_html,ENT_QUOTES,$charset);
		$this->input_html=str_replace("!!red!!","<span style='color:#DD0000'><b><u>",$this->input_html);
		$this->input_html=str_replace("!!s_red!!","</u></b></span>",$this->input_html);
	}

	public function get_positive_terms($tree,$father_sign=0){
		for($i=0;$i<(count($tree));$i++){
			$term_obj = $tree[$i];
			if($term_obj->word !== "*"){
				if($term_obj->sub){
					$this->get_positive_terms($term_obj->sub,$term_obj->not);
				} elseif($term_obj->word && ($term_obj->not == $father_sign)){
					$this->positive_terms[] = $term_obj->word;
				}
			}
		}
		return $this->positive_terms;
	}

	public function get_positive_terms_obj($tree,$father_sign=0){
		for($i=0;$i<(count($tree));$i++){
			$term_obj = $tree[$i];
			if($term_obj->word !== "*"){
				if($term_obj->sub){
					$this->get_positive_terms_obj($term_obj->sub,$term_obj->not);
				} elseif($term_obj->word && ($term_obj->not == $father_sign)) {
					$this->positive_terms[] = $term_obj;
				}
			}
		}
		return $this->positive_terms;
	}

	//récupère la pertinence d'une liste de notices sur la recherche instanciée!
	public function get_pert($notices_ids,$restrict=array(),$neg_restrict=false,$with_explnum=false,$return_query = false,$all_fields=false){
		return $this->get_objects_pert($notices_ids, "id_notice", "notices_mots_global_index", "word", "notices_fields_global_index", "value", "notice_id",$restrict,$neg_restrict,$with_explnum,$return_query,$all_fields);
	}

	//généralisation du calcul de la pertinence
	public function get_objects_pert($objects_ids,$field_id,$table_mot,$field_mot,$table_term,$field_term,$final_id,$restrict=array(),$neg_restrict=false,$with_explnum=false,$return_query = false,$all_fields=false){
		$empty_word = get_empty_words();
		$query = '';
		$troncat = "";
		if ($this->allow_term_troncat_search) {
			$troncat = "%";
		}
		$terms = $this->get_positive_terms_obj($this->tree);
		$words = array();
		$literals = array();
		$queries = array();
		if($objects_ids){
			if(is_array($terms) && count($terms)){
				foreach($terms as $term){
					if(!$term->literal){
						if(!in_array($term,$words)) {
							$words[]=$term;
						}
					}else {
						$literals[] = $term;
					}
				}
			}
			if($this->input !== "*"){
				//pertinence sur documents numériques valable uniquement sur les notices
 				if($table_mot == "notices_mots_global_index" && $with_explnum && $this->input !== "*"){
					$noti_members = $this->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_notice","",0,0,true);
 					$bull_members = $this->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_bulletin","",0,0,true);
 					$noti = "select distinct explnum_notice as notice_id, ".$noti_members['select']." as pert from explnum
 							".gen_where_in('explnum_notice', $objects_ids);
 					$bull = "select distinct num_notice as notice_id, ".$noti_members['select']." as pert from explnum join bulletins on explnum_bulletin = bulletin_id
 							".gen_where_in('num_notice', $objects_ids);
 					$queries[] = "select distinct notice_id, pert as pert from (($noti) union all ($bull)) as q1";
 				}
				if(count($words)){
					//si on veut nettoyer par rapport à la fréquence d'apparition, on compte le nombre total de lignes.
					if($this->search_relevant_with_frequency){
						$nb_lignes = 0;
						//pour les grosses volumétrie, on essaye d'etre plus efficace
						switch($table_mot){
							case "notices_mots_global_index" :
								$query =" select count(notice_id) from notices";
								break;
							default :
								$query = "select count(distinct ".$field_id.") from ".$table_mot;
								break;
						}
						$result = pmb_mysql_query($query);
						if(pmb_mysql_num_rows($result)){
							$nb_lignes = pmb_mysql_result($result,0,0);
						}
					}
					$pert_query_words = "select ".$field_id." as ".$final_id.", max(pond * (!!pert!!)) as pert from ".$table_mot." ";
					$where = "";
					foreach($words as $term){
						if($term->word !== "*"){
							if($where !="") $where.= " or ";
							$crit="word ";
							if (strpos($term->word,"*")!==false || ($this->allow_term_troncat_search && (strlen($term->word)>2))){
								if (strpos($term->word,"*") === false) {
									//Si c'est un mot vide, on ne troncature pas
									if (in_array($term->word,$empty_word)===false) {
										if($term->not) $crit.= "not ";
										$crit.= "like '".addslashes($term->word.$troncat)."'";
									} else {
										if($term->not) $crit.= "! ";
										$crit.="= '".addslashes($term->word)."'";
									}
								} else {
									if($term->not) $crit.= "not ";
									$crit.= "like '".addslashes(str_replace("*","%",$term->word))."'";
								}
							}else{
								if($term->not) $crit.= "!";
								$crit.= "= '".addslashes($term->word)."'";
							}
							$where.= " ".$crit;
							if (in_array($term->word,$empty_word)) $tpound=$term->pound/10; else $tpound=$term->pound;

							$query = "select id_word ";
							if($this->search_relevant_with_frequency){
								$query.=",count(distinct ".$field_id.") as freq, abs(length(word)-length('".str_replace("*","",$term->word)."')) as dist ";
								$dist = $freq = "case ";
								$max_dist = 0;
								$query.=" from words straight_join ".$table_mot." on id_word=num_word where $crit group by id_word";
							} else {
								$query = "select distinct id_word from words straight_join ".$table_mot." on id_word=num_word where $crit ";
							}
							$result = pmb_mysql_query($query);
							$ids_words = $subqueries = array();
							$subwhere = "";
							if(pmb_mysql_num_rows($result)){
								while($row = pmb_mysql_fetch_object($result)){
									if($this->search_relevant_with_frequency){
										$freq.= " when num_word=".$row->id_word." then ".$row->freq;
										$dist.= " when num_word=".$row->id_word." then ".$row->dist;
										if($row->dist > $max_dist) $max_dist = $row->dist;
									}
									$ids_words[]= $row->id_word;
								}

								if($this->search_relevant_with_frequency){
								    // AR - visiblement, mysql ne supporte pas des fractions trop petites..., on injecte un traitement pour éviter que la division retourne NULL
								    // Sinon on peurd des resultats en cherchant la pertinance...
 									$ln_freq = "ln(1/(if((".$freq." end /".$nb_lignes.") is null , (".$freq." end /".$nb_lignes.") , 0.0001)))";
 									$ln_dist = "ln(1/(".$dist." end /".$max_dist."))";

									$coeff = "if(".$dist." end != 0, ((".$ln_dist." + ".$ln_freq.")/2 ), ".$ln_freq.")";
									$query_words = str_replace("!!pert!!",$coeff." * ".$tpound,$pert_query_words);
								}else{
									$query_words = str_replace("!!pert!!",$tpound,$pert_query_words);
								}
								$subwhere = gen_where_in('num_word', $ids_words);
								$subwhere.= (count($restrict) > 0? " and ".$this->get_field_restrict($restrict,$neg_restrict) : "");
								if($table_mot == "notices_mots_global_index" && $all_fields && $this->exclude_fields!=""){
									$subwhere.=" and code_champ not in (".$this->exclude_fields.")";
								}
								$queries[]= $query_words.$subwhere." group by ".$field_id." having pert > 0 ";
							}
						}else{
							$query = "select distinct id_notice as notice_id, 100 from notices_mots_global_index where 1 ";
							$subwhere.= (count($restrict) > 0? " and ".$this->get_field_restrict($restrict,$neg_restrict) : "");
							if($all_fields && $opac_exclude_fields!= ""){
								$subwhere.=" and code_champ not in (".$opac_exclude_fields.")";
							}
							$queries[] = $query;
						}
					}
				}
				if(count($literals)){
					$pert_query_literals = "select distinct ".$field_id." as ".$final_id.", sum(!!pert!!) as pert from ".$table_term." where ";
					$where = "";
					foreach($literals as $term){
						//on n'ajoute pas une clause dans le where qui parcours toute la base...
						if($where !="") $where.= " or ";
						$crit = "value ";
						if($term->not) $crit.= "not ";
						$crit.= "like '".($term->start_with == 0 ? "%":"").addslashes(str_replace("*","%",$term->word))."%'";
						$where.= " ".$crit;
						$crit = str_replace("%%","%",$crit);
						$pert_query_literals = str_replace("!!pert!!","((".$crit.") * pond *".$term->pound.")+!!pert!!",$pert_query_literals);
					}
					$where.= (count($restrict) > 0? " and ".$this->get_field_restrict($restrict,$neg_restrict) : "");
					$pert_query_literals = str_replace("!!pert!!",0,$pert_query_literals);
					if($table_mot == "notices_mots_global_index" && $all_fields && $this->exclude_fields!=""){
						$where.=" and code_champ not in (".$this->exclude_fields.")";
					}
					$queries[]= $pert_query_literals.$where." group by ".$field_id." having pert > 0 ";
				}
				//aucun terme positif
				if(!count($words) && !count($literals)){
					$where= (count($restrict) > 0? $this->get_field_restrict($restrict,$neg_restrict) : "");
					if($table_mot == "notices_mots_global_index" && $all_fields && $this->exclude_fields!=""){
						if($where !="") $where.= " and";
						$where.=" code_champ not in (".$this->exclude_fields.")";
					}
					$queries[]= "select distinct ".$field_id." as ".$final_id.",max(pond) as pert from ".$table_term." ".($where ? " where ".$where : "")." group by ".$field_id." ";
				}
				$query = "select distinct ".$final_id.", sum(pert) as pert from ((".implode(") union all (",$queries).")) as uni
					".gen_where_in($final_id, $objects_ids)." group by ".$final_id."";

			}else{
				//Si recherche * alors la pondération est la même pour toutes les notices
				$query = "SELECT distinct(".$field_id.") as ".$final_id.", 100 as pert FROM ".$table_term." WHERE ".$field_id." in (".$objects_ids.")";

			}
		}
		if($return_query){
			return $query;
		}else{
			$table = "search_result".md5(microtime(true));
			$rqt = "create temporary table ".$table." $query";
			pmb_mysql_query($rqt);
			pmb_mysql_query("alter table ".$table." add index i_id(".$final_id.")");
			return $table;
		}
	}

	public function add_stemming(){
		for($i=0 ; $i<count($this->tree) ; $i++){
			$this->tree[$i] = $this->_add_stemming($this->tree[$i]);
		}
	}

	protected function _add_stemming($term){
		global $lang;
		if(!$term->literal && !$term->sub && !$term->not){
			$sub = array();
			//on perd pas le terme d'origine quand même...
			$sub[]= new term($term->word,$term->literal,$term->not,$term->start_with,"",$term->sub,$term->pound);
			//on cherche les mots de la base avec la même racine !
			$stemming = new stemming($term->word);
			$query = "select distinct word from words where stem like '".$stemming->stem."' and lang in ('','".$lang."') and word != '".addslashes($term->word)."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				if(pmb_mysql_num_rows($result)>1){
					$sub_stem = array();
					$op = "";
					while ($row = pmb_mysql_fetch_object($result)){
						if(count($sub_stem)) $op = "or";
						$sub_stem[] = new term($row->word,0,0,0,$op,null,$term->pound);
					}
					$sub[] = new term("",0,0,0,"or",$sub_stem,0.4,"stemming");
				}else{
					$row = pmb_mysql_fetch_object($result);
					$sub[] = new term($row->word,0,0,0,"or",null,$term->pound*0.4,"stemming");
				}
			}
			$term->word = "";
			$term->sub = $sub;
		}else if(!$term->literal && !$term->not){
			for ($i=0 ; $i<count($term->sub) ; $i++){
				$term->sub[$i] = $this->_add_stemming($term->sub[$i]);
			}
		}
		return $term;
	}

	public static function get_sphinx_query($input, $keep_empty_words = 0) {
	    $aq = new analyse_query($input, 0, 0, 1, $keep_empty_words);

		return $aq->build_sphinx_query($aq->tree);
	}

	protected function build_sphinx_query($tree) {
	    global $pmb_allow_term_troncat_search, $sphinx_troncat_min_length;

		$query = '';
		for ($i = 0; $i < count($tree); $i++) {
			if ($tree[$i]->literal != 2 && (!empty($tree[$i]->word) || !empty($tree[$i]->sub))) {
				if ($query != '') {
					$query .= ' ';
				}
				if ($tree[$i]->operator == "or") {
					$query .= '| ';
				}
				if ($tree[$i]->not) {
					$query .= '!';
				}
				if ($tree[$i]->sub) {
					$query .=' ('.$this->build_sphinx_query($tree[$i]->sub).')';
				} else {
					if ($tree[$i]->literal) {
						$tree[$i]->word = preg_replace( "/[\?\!]/", "", $tree[$i]->word);
						$query .= '"'.encoding_normalize::utf8_normalize($tree[$i]->word).'"';
					} else {
						if ($tree[$i]->start_with == 1) {
							$query .= '^';
						}
						$query .= encoding_normalize::utf8_normalize($tree[$i]->word);
						if($pmb_allow_term_troncat_search && $tree[$i]->word[-1] != "*" && strlen($tree[$i]->word) >= $sphinx_troncat_min_length){
						    $query .= "*";
						}
					}
				}
			}
		}

		return $query;
	}
}
?>