<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggest.class.php,v 1.25.4.3 2025/06/05 12:03:04 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
	die("no access");

global $class_path, $include_path;
require_once ($include_path."/misc.inc.php");
require_once($class_path."/double_metaphone.class.php");
require_once($include_path."/parser.inc.php");

/**
 * Classe de suggestions depuis saisie en opac
 */
class suggest {

	// ---------------------------------------------------------------------------------------------------
	//  propriétés de la classe
	// ---------------------------------------------------------------------------------------------------

	public $inputString;       		// chaine en entrée
	public $cleanInputString;       	// chaine en entrée nettoyée
	public $searchIndex;       		// table d'indexation
	public $searchValueFields;       	// table des contenus de champs
	public $excludeNotes;       		// exclure les notes des recherches
	public $arrayWords;				// liste des mots en entrée
	public $arraySimilars;				// liste de tous les approchants pondérés
	public $arraySimilarsByWord;		// liste de tous les approchants par mot
	public $arrayPermutations;			// liste des différentes permutations d'approchants
	public $permutationCode;			// tableau utilisé pour les permutations
	public $permutationPos;			// position utilisée pour les permutations
	public $maxSuggestions;			// nombre de suggestions maximum
	public $maxResultsByPermutation;	// nombre de résultats maximum par permutation
	public $tbChampBase;				// correspondance champs en base/libellé
	public $arrayResults;				// tableau des résultats classés

	public $temporary_tables_words;

	public $access;
	public $domaine;

// ---------------------------------------------------------------------------------------------------
//  suggest($string) : constructeur
// ---------------------------------------------------------------------------------------------------
	public function __construct($string,$searchTable='notices',$maxSuggest=10,$maxResultsByPermutation=500,$excludeNotes=true) {
	    global $gestion_acces_active, $gestion_acces_empr_notice;

	    if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1){
	        $this->access = new acces();
	        $this->domaine = $this->access->setDomain(2);
	    }

		$this->loadTbChampBase();
		$this->maxSuggestions = $maxSuggest;
		$this->arrayResults = array();
		if ($searchTable == 'notices') {
			$this->searchIndex = 'notices_mots_global_index';
			$this->searchValueFields = 'notices_fields_global_index';
		}
		$this->excludeNotes = $excludeNotes;
		$this->maxResultsByPermutation = $maxResultsByPermutation;
		if($tmp = trim($string)){
			$this->inputString = $tmp;
			$this->findWords();
		}
	}

// ---------------------------------------------------------------------------------------------------
//  loadTbChampBase() : correspondance champs base / libellés OPAC
// ---------------------------------------------------------------------------------------------------
	public function loadTbChampBase() {
		global $champ_base,$include_path,$msg;

		if(empty($champ_base) || !count($champ_base)) {
			$file = $include_path."/indexation/notices/champs_base_subst.xml";
			if(!file_exists($file)){
				$file = $include_path."/indexation/notices/champs_base.xml";
			}
			$fp=fopen($file,"r");
	    	if ($fp) {
				$xml=fread($fp,filesize($file));
			}
			fclose($fp);
			$champ_base=_parser_text_no_function_($xml,"INDEXATION",$file);
		}
		if(empty($this->tbChampBase) || !count($this->tbChampBase)) {
			foreach($champ_base["FIELD"] as $v){
				if(isset($v["TABLE"][0]["TABLEFIELD"])){
					foreach($v["TABLE"][0]["TABLEFIELD"] as $v2){
						if(isset($v2["NAME"])){
							if(isset($v2["ID"])){
								$this->tbChampBase[(int)$v["ID"]."_".(int)$v2["ID"]]=$msg[$v2["NAME"]];
							}else{
								$this->tbChampBase[(int)$v["ID"]."_0"]=$msg[$v2["NAME"]];
							}
						}else{
							if(isset($v2["ID"])){
								$this->tbChampBase[(int)$v["ID"]."_".(int)$v2["ID"]]=$msg[$v["NAME"]];
							}else{
								$this->tbChampBase[(int)$v["ID"]."_0"]=$msg[$v["NAME"]];
							}
						}
						if(isset($v2["ID"])){
							if(!trim($this->tbChampBase[(int)$v["ID"]."_".(int)$v2["ID"]])){
								$this->tbChampBase[(int)$v["ID"]."_".(int)$v2["ID"]]="___champ sans libellé___";
							}
						}else{
							if(!trim($this->tbChampBase[(int)$v["ID"]."_0"])){
								$this->tbChampBase[(int)$v["ID"]."_0"]="___champ sans libellé___";
							}
						}
					}
				}
				if(isset($v["ISBD"])){
					$tmp=$v["ISBD"][0];
					$this->tbChampBase[(int)$v["ID"]."_".(int)$tmp["ID"]]=$msg[$tmp["NAME"]."_".$tmp["CLASS_NAME"]];
					if(!trim($this->tbChampBase[(int)$v["ID"]."_".(int)$tmp["ID"]])){
						$this->tbChampBase[(int)$v["ID"]."_".(int)$tmp["ID"]]="___champ sans libellé___";
					}
				}
			}
		}
	}

// ---------------------------------------------------------------------------------------------------
//  findWords() : nettoie et trouve tous les mots de la chaine saisie
// ---------------------------------------------------------------------------------------------------
	public function findWords() {
		$this->cleanInputString = $this->cleanString($this->inputString);
		$this->arrayWords = str_word_count($this->cleanInputString,1,"0123456789");
		//on nettoie les doublons éventuels
		$this->arrayWords = array_values(array_unique($this->arrayWords));
		//on limite le nombre de mots cherchés
		$this->arrayWords = array_slice($this->arrayWords,0,5);
		if(count($this->arrayWords)){
			$this->findAndPermuteSimilars();
		}
	}

// ---------------------------------------------------------------------------------------------------
//  findSimilars() : trouve les approchants pondérés depuis le tableau de mots
//	la pondération est inversée : plus "pond" est faible, plus le mot est pertinent
// ---------------------------------------------------------------------------------------------------
	public function findAndPermuteSimilars() {
		global $lang;

		if(count($this->arrayWords)){
			foreach($this->arrayWords as $key=>$word){
				$distMax=2;
				switch(count($this->arrayWords)){
					case 1 : $maxSimilars=10;
						break;
					case 2 : $maxSimilars=5;
						break;
					case 3 : $maxSimilars=3;
						break;
					case 4 : $maxSimilars=2;
						break;
					default : $maxSimilars=1;
						break;
				}

				$temporary_table = 'suggest_'.hrtime(true);

				$qd = "drop table if exists $temporary_table";
				pmb_mysql_query($qd);

				$qc = "create temporary table $temporary_table  (id_word int, word varchar(255), dist int, index using btree(id_word), key i_word(word)) engine memory";
				pmb_mysql_query($qc);

				$qi_1 = "insert ignore into $temporary_table
					(SELECT id_word, word, 0 as dist FROM words
					WHERE word LIKE '".addslashes($word)."%'
					AND lang IN ('','".$lang."') )";
				pmb_mysql_query($qi_1);

				$dmeta = new DoubleMetaPhone($word);
				if($dmeta->primary || $dmeta->secondary){
					$qi_2 = "insert ignore into $temporary_table
						(SELECT id_word, word, levenshtein('".addslashes($word)."',word) as dist FROM words
						WHERE levenshtein('".$dmeta->primary." ".$dmeta->secondary."',double_metaphone) < ".$distMax."
						AND lang IN ('','".$lang."')
					)";
					pmb_mysql_query($qi_2);
				}

				$qr = "select distinct id_word, word, dist from $temporary_table join {$this->searchIndex} on num_word=id_word order by dist, word limit $maxSimilars";
				$res=pmb_mysql_query($qr);

				$count=1;
				if($res && pmb_mysql_num_rows($res)){
					$nbRows=pmb_mysql_num_rows($res);
					while($row=pmb_mysql_fetch_object($res)){
						$this->arraySimilarsByWord[$key][] = $row->id_word;
						$this->arraySimilars[$row->id_word]["word"] = $row->word;
						$this->arraySimilars[$row->id_word]["pond"] = $count/$nbRows;
						$count++;
					}
				}
			}
			if(count($this->arraySimilarsByWord)){
				$this->permutationCode=array();
				$this->permutationPos=0;
				$this->findPermutations($this->arraySimilarsByWord);
			}
			if(count($this->arrayPermutations)){
				$this->findAndOrderPermutationInDatabase();
			}
		}
	}

// ---------------------------------------------------------------------------------------------------
//  listUniqueSimilars() : renvoie un tableau des suggestions uniques
// ---------------------------------------------------------------------------------------------------
	public function listUniqueSimilars(){
		$arrayReturn = array();
		if (is_countable($this->arraySimilars) && count($this->arraySimilars)) {
			foreach ($this->arraySimilars as $value) {
				$arrayReturn[] = $value["word"];
			}
		}
		$arrayReturn = array_unique($arrayReturn);

		return $arrayReturn;
	}

// ---------------------------------------------------------------------------------------------------
//  findPermutations() : trouve les permutations du tableau en entrée
//	attention : fonction récursive (d'où le paramètre en entrée, et les deux propriétés de classe)
// ---------------------------------------------------------------------------------------------------
	public function findPermutations($array) {
		if(count($array)) {
			for($i=0; $i<count($array[0]); $i++) {
				$tmpArray = $array;
				$this->permutationCode[$this->permutationPos] = $array[0][$i];
				array_shift($tmpArray);
				$this->permutationPos++;
				$this->findPermutations($tmpArray);
			}
		} else {
			asort($this->permutationCode);
			$tmpValeur=implode(",",$this->permutationCode);
			if(!is_array($this->arrayPermutations) || !in_array($tmpValeur,$this->arrayPermutations)){
				$this->arrayPermutations[]=$tmpValeur;
			}
		}
		$this->permutationPos--;
	}

// ---------------------------------------------------------------------------------------------------
//  arrayResultsSort($sort) : trie la propriété arrayResultFinal selon la clé donnée
// ---------------------------------------------------------------------------------------------------
	public function arrayResultsSort($sort){
		$sort_values=array();
		if (is_countable($this->arrayResults)) {
			for ($i = 0; $i < sizeof($this->arrayResults); $i++) {
				$sort_values[$i] = $this->arrayResults[$i][$sort];
			}
		}
		asort ($sort_values);
		reset ($sort_values);

		$sorted_arr = array();
		foreach ($sort_values as $arr_key => $arr_val) {
			$sorted_arr[] = $this->arrayResults[$arr_key];
		}
		$this->arrayResults = $sorted_arr;
	}

	protected function gen_temporary_table_word($idWord) {
		if(empty($this->temporary_tables_words[$idWord])) {
			$temporary_table = 'suggest_'.hrtime(true);
			pmb_mysql_query("DROP TABLE IF EXISTS $temporary_table");
			pmb_mysql_query("CREATE TEMPORARY TABLE $temporary_table  (id_notice mediumint(8), code_champ int(3), code_ss_champ int(3), field_position int(11), index using btree(id_notice), key i_id_notice(id_notice)) engine memory");

			$where="n.num_word=".$idWord;
			if($this->excludeNotes){
				$where.=" AND n.code_champ NOT IN (12,13,14)";
			}
			$query = "
			INSERT IGNORE INTO  $temporary_table (
				SELECT DISTINCT n.id_notice, n.code_champ, n.code_ss_champ, n.field_position
				FROM ".$this->searchIndex." n
				WHERE ".$where."
			)";
			pmb_mysql_query($query);
			$this->temporary_tables_words[$idWord] = $temporary_table;
		}
		return $this->temporary_tables_words[$idWord];
	}

// ---------------------------------------------------------------------------------------------------
//  findAndOrderPermutationInDatabase() : trouve les champs de notice où les permutations apparaissent
//	classés par distance max des deux termes les plus éloignés (ou position si un seul terme) pondérée
//	par nombre d'occurrences en regroupement
// ---------------------------------------------------------------------------------------------------
	public function findAndOrderPermutationInDatabase() {
		if(count($this->arrayPermutations)){
			$arrayResults=array();
			foreach($this->arrayPermutations as $permutation){
				$itemPermutation=explode(",",$permutation);
				//Cas particulier si un seul mot
				if(count($itemPermutation)==1){
					$temporary_tablename = $this->gen_temporary_table_word($itemPermutation[0]);
					$query="SELECT DISTINCT id_notice, code_champ, code_ss_champ, field_position
							FROM ".$temporary_tablename."
 							ORDER BY 4 LIMIT 0,".$this->maxResultsByPermutation;
					$res=pmb_mysql_query($query) or die();
					if(pmb_mysql_num_rows($res)){
						while($row=pmb_mysql_fetch_object($res)){
							if ($this->has_rights($row->id_notice)) {
    							$key=$row->id_notice."_".$row->code_champ."_".$row->code_ss_champ."_".$row->field_position."_".$itemPermutation[0];
    							$arrayResults[$key]=$row->field_position*$this->arraySimilars[$itemPermutation[0]]["pond"];
							}
						}
					}
				}else{
					$ponderation=0;
					foreach($itemPermutation as $keyItem=>$idWord){
						$temporary_tablename = $this->gen_temporary_table_word($idWord);
						$ponderation+=$this->arraySimilars[$idWord]["pond"];
						if(!$keyItem){
							$select="DISTINCT n.id_notice, n.code_champ, n.code_ss_champ, n.field_position,
								(
									SELECT MAX(field_position)-MIN(field_position)
									FROM ".$this->searchIndex."
									WHERE id_notice=n.id_notice
									AND code_champ=n.code_champ
									AND code_ss_champ=n.code_ss_champ
									AND num_word IN (".$permutation.")";
							if($this->excludeNotes){
								$select.=" AND code_champ NOT IN (12,13,14)";
							}
							$select.=") as distance";
							$from=$temporary_tablename." n";
						}else{
							$from.=" JOIN ".$temporary_tablename." n".$keyItem."
									ON n.id_notice=n".$keyItem.".id_notice
									AND n.code_champ=n".$keyItem.".code_champ
									AND n.code_ss_champ=n".$keyItem.".code_ss_champ";
						}
					}
					$query="SELECT ".$select." FROM ".$from;
					$res=pmb_mysql_query($query) or die();
					if(pmb_mysql_num_rows($res)){
						while($row=pmb_mysql_fetch_object($res)){
						    if ($this->has_rights($row->id_notice)) {
    							$key=$row->id_notice."_".$row->code_champ."_".$row->code_ss_champ."_".$row->field_position."_".implode("_",$itemPermutation);
    							$arrayResults[$key]=$row->distance*$ponderation;
						    }
						}
					}
				}
			}
			asort($arrayResults);
			//Regroupement par valeur/champ
			foreach($arrayResults as $key=>$value){
				$tmpArray=explode("_",$key);
				$query="SELECT value
						FROM ".$this->searchValueFields."
						WHERE id_notice=".$tmpArray[0]."
						AND code_champ=".$tmpArray[1]."
						AND code_ss_champ=".$tmpArray[2];
				$res=pmb_mysql_query($query) or die();
				$row=pmb_mysql_fetch_object($res);
				$creeElement=true;
				if(count($this->arrayResults)){
					foreach($this->arrayResults as $key2=>$value2){
						if(($value2["field_content"]==$row->value) && ($value2["field_subfield"]==$tmpArray[1]."_".$tmpArray[2])){
							$this->arrayResults[$key2]["occurrences"]++;
							$creeElement=false;
							break;
						}
					}
				}
				if($creeElement){
					$tmpArrayTmp=array();
					$tmpArrayTmp["field_content"]=$row->value;
					$tmpArrayTmp["field_clean_content"]=$row->value;
					$tmpArrayTmp["field_subfield"]=$tmpArray[1]."_".$tmpArray[2];
					$tmpArrayTmp["ratio"]=$value;
					$tmpArrayTmp["occurrences"]=1;
					$this->arrayResults[]=$tmpArrayTmp;
				}
			}
			//Calcul des scores
			foreach($this->arrayResults as $key=>$value){
				$this->arrayResults[$key]["score"]=$value["ratio"]/$value["occurrences"];
			}
			//Classement des résultats
			$this->arrayResultsSort('score');
			//On limite et on gère l'affichage
			$search=array();
			foreach($this->arraySimilars as $similar){
				$search[]=$similar["word"];
			}
			arsort($search);
			if(is_array($this->arrayResults)){
				//Les résultats qui commencent par la saisie sont placés en premiers
				$tmpArray=array();
				foreach($this->arrayResults as $key=>$value){
					if(preg_match('`^'.$this->cleanInputString.'`',$this->cleanString($value['field_content']))){
						$tmpArray[]=$value;
						unset($this->arrayResults[$key]);
					}
				}
				$this->arrayResults=array_merge($tmpArray,$this->arrayResults);
				//limitation des résultats et gestion de l'affichage
				$arrayUniqueAffiche=array();
				foreach($this->arrayResults as $key=>$value){
					if (count($arrayUniqueAffiche)<$this->maxSuggestions && !in_array($value["field_content"],$arrayUniqueAffiche)) {
						//champ dans lequel la valeur a été trouvée : 27/08/2015 on n'affiche plus le champs
						//$this->arrayResults[$key]["field_name"]=$this->tbChampBase[$value["field_subfield"]];
						//passage en gras des mots
						$this->arrayResults[$key]["field_content"]=$this->markBold($value["field_content"],implode("|",$search));
						//pour les occurences trop longues, juste les mots en gras
						$this->arrayResults[$key]["field_content_search"]=$this->listFoundWords($this->arrayResults[$key]["field_content"]);
						$arrayUniqueAffiche[]=$value["field_content"];
					} else {
						unset($this->arrayResults[$key]);
					}
				}
			}
		}
	}

// ---------------------------------------------------------------------------------------------------
//  markBold($string,$wordToFind) : met un mot en gras dans une chaîne
// ---------------------------------------------------------------------------------------------------
	public function markBold($string,$wordsToFind){
		$specialChars = array("a","e","i","o","u","y","c","n" );
		$specialCharsReplacement = array("[a|à|á|â|ã|ä|å]{1}","[e|è|é|ê|ë]{1}","[i|ì|í|î|ï]{1}","[o|ò|ó|ô|õ|ö|ø]{1}","[u|ù|ú|û|ü]{1}","[y|y]{1}","[c|ç]{1}","[n|ñ]{1}" );
		$wordsToFind = str_replace($specialChars, $specialCharsReplacement, $wordsToFind);

		$tmpArray=preg_split("/([\s,\'\"\.\-\(\) ]+)/",trim($string),-1,PREG_SPLIT_DELIM_CAPTURE);
		foreach($tmpArray as $key=>$value){
			$tmpArray[$key]=preg_replace("/^($wordsToFind)$/i", "<b>\\1</b>",$value);
		}
		return implode("",$tmpArray);
	}

// ---------------------------------------------------------------------------------------------------
//  listFoundWords($string) : renvoie un tableau des mots uniques trouvés en gras
// ---------------------------------------------------------------------------------------------------
	public function listFoundWords($string){
		$arrayReturn = array();
		preg_match_all("`<b>(.*?)<\/b>`",$string,$arrayReturn);
		$arrayReturn = array_unique($arrayReturn[1]);
		return $arrayReturn;
	}

// ---------------------------------------------------------------------------------------------------
//  cleanString($string) : renvoie une chaine nettoyée
// ---------------------------------------------------------------------------------------------------
	public function cleanString($string){
		$string = str_replace("%","",$string);
		$string = convert_diacrit($string);
		$string = strip_empty_words($string);
		return $string;
	}

	public static function get_add_link() {
	    global $msg, $charset;
	    global $opac_show_suggest;
	    global $opac_resa_popup;
	    global $opac_rgaa_active;

	    $add_link = '';
	    if ($opac_show_suggest) {
	        $add_link .= "<span class='espaceResultSearch'>&nbsp;&nbsp;&nbsp;</span><span class='search_bt_sugg'>";
			if ($opac_resa_popup) {
				if ($opac_rgaa_active) {
					$add_link .= "<a
						href=\"./do_resa.php?lvl=make_sugg&oresa=popup\"
						target=\"_blank\"
						onClick=\"w=window.open('./do_resa.php?lvl=make_sugg&oresa=popup','doresa','scrollbars=yes,width=600,height=600,menubar=0,resizable=yes'); w.focus(); return false;\" ";
				} else {
					$add_link .= "<a href=\"#\" onClick=\"w=window.open('./do_resa.php?lvl=make_sugg&oresa=popup','doresa','scrollbars=yes,width=600,height=600,menubar=0,resizable=yes'); w.focus(); return false;\" ";
				}
			} else {
				$add_link .= "<a href=\"./do_resa.php?lvl=make_sugg&oresa=popup\" ";
			}
	        $add_link .= " title='".htmlentities($msg["empr_bt_make_sugg"], ENT_QUOTES, $charset)."' >".$msg['empr_bt_make_sugg']."</a></span>";
	    }
	    return $add_link;
	}

// ---------------------------------------------------------------------------------------------------
//  has_rights($id: int) : renvoie un boolean si l'empr a les droits sur une ressource
// ---------------------------------------------------------------------------------------------------
	public function has_rights($id) {
	    global $gestion_acces_active, $gestion_acces_empr_notice;
	    $has_access = true;
	    if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1){
	        if(isset($this->access) && isset($this->domaine)) {
	            $has_access = $this->domaine->getRights($_SESSION['id_empr_session'], $id, 4);
	        }
	    }
	    return $has_access;
	}
} # fin de définition de la classe