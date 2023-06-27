<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggest.class.php,v 1.16 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
	die("no access");

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

// ---------------------------------------------------------------------------------------------------
//  suggest($string) : constructeur
// ---------------------------------------------------------------------------------------------------
	public function __construct($string,$searchTable='notices',$maxSuggest=10,$maxResultsByPermutation=500,$excludeNotes=true) {
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
	
		if(!count($champ_base)) {
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
		if(!count($this->tbChampBase)) {
			foreach($champ_base["FIELD"] as $k=>$v){
				if(isset($v["TABLE"][0]["TABLEFIELD"])){
					foreach($v["TABLE"][0]["TABLEFIELD"] as $k2=>$v2){
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
		global $dbh,$lang;
		
		if(count($this->arrayWords)){
			foreach($this->arrayWords as $key=>$word){
				$dmeta = new DoubleMetaPhone($word);
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
				$query = "SELECT DISTINCT id_word, word
								FROM words
								WHERE word LIKE '".addslashes($word)."%'
								AND lang IN ('','".$lang."')
								AND id_word IN (
									SELECT DISTINCT num_word FROM ".$this->searchIndex."
								)
						UNION
							SELECT * FROM
							(
							SELECT DISTINCT id_word, word
								FROM words
								WHERE levenshtein('".$dmeta->primary." ".$dmeta->secondary."',double_metaphone) < ".$distMax."
								AND lang IN ('','".$lang."')
								AND id_word IN (
									SELECT DISTINCT num_word FROM ".$this->searchIndex."
								)
								ORDER BY levenshtein('".addslashes($word)."',word) ASC
							) as R1
						LIMIT ".$maxSimilars;
				$res=pmb_mysql_query($query,$dbh);
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
		if (count($this->arraySimilars)) {
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
				$tarr = array_shift($tmpArray);
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
		for ($i = 0; $i < sizeof($this->arrayResults); $i++) {
			$sort_values[$i] = $this->arrayResults[$i][$sort];
		}
		asort ($sort_values);
		reset ($sort_values);

		foreach ($sort_values as $arr_key => $arr_val) {
			$sorted_arr[] = $this->arrayResults[$arr_key];
		}
		$this->arrayResults = $sorted_arr;
	}
	
// ---------------------------------------------------------------------------------------------------
//  findAndOrderPermutationInDatabase() : trouve les champs de notice où les permutations apparaissent
//	classés par distance max des deux termes les plus éloignés (ou position si un seul terme) pondérée
//	par nombre d'occurrences en regroupement
// ---------------------------------------------------------------------------------------------------
	public function findAndOrderPermutationInDatabase() {
		global $dbh;
		
		if(count($this->arrayPermutations)){
			$arrayResults=array();
			foreach($this->arrayPermutations as $permutation){
				$itemPermutation=explode(",",$permutation);
				//Cas particulier si un seul mot
				if(count($itemPermutation)==1){
					$query="SELECT DISTINCT id_notice, code_champ, code_ss_champ, field_position 
							FROM ".$this->searchIndex." 
							WHERE num_word=".$itemPermutation[0];
					if($this->excludeNotes){
						$query.=" AND code_champ NOT IN (12,13,14)";
					}
					$query.=" ORDER BY 4 LIMIT 0,".$this->maxResultsByPermutation;
					$res=pmb_mysql_query($query,$dbh) or die();
					if(pmb_mysql_num_rows($res)){				
						while($row=pmb_mysql_fetch_object($res)){
							$key=$row->id_notice."_".$row->code_champ."_".$row->code_ss_champ."_".$row->field_position."_".$itemPermutation[0];
							$arrayResults[$key]=$row->field_position*$this->arraySimilars[$itemPermutation[0]]["pond"];
						}
					}
				}else{
					$ponderation=0;
					foreach($itemPermutation as $keyItem=>$idWord){
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
							$from=$this->searchIndex." n";
							$where="n.num_word=".$idWord;
							if($this->excludeNotes){
								$where.=" AND n.code_champ NOT IN (12,13,14)";
							}
						}else{
							$from.=" JOIN ".$this->searchIndex." n".$keyItem." 
									ON n.id_notice=n".$keyItem.".id_notice 
									AND n.code_champ=n".$keyItem.".code_champ 
									AND n.code_ss_champ=n".$keyItem.".code_ss_champ";
							$where.=" AND n".$keyItem.".num_word=".$idWord;
						}
					}
					$query="SELECT ".$select." FROM ".$from." WHERE ".$where;
					$res=pmb_mysql_query($query,$dbh) or die();
					if(pmb_mysql_num_rows($res)){
						while($row=pmb_mysql_fetch_object($res)){
							$key=$row->id_notice."_".$row->code_champ."_".$row->code_ss_champ."_".$row->field_position."_".implode("_",$itemPermutation);
							$arrayResults[$key]=$row->distance*$ponderation;
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
				$res=pmb_mysql_query($query,$dbh) or die();
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
		global $msg;
		global $opac_show_suggest;
		global $opac_resa_popup;
		$add_link = '';
		if ($opac_show_suggest) {
			$add_link .= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;&nbsp;</span><span class=\"search_bt_sugg\"><a href=# ";
			if ($opac_resa_popup) $add_link .= " onClick=\"w=window.open('./do_resa.php?lvl=make_sugg&oresa=popup','doresa','scrollbars=yes,width=600,height=600,menubar=0,resizable=yes'); w.focus(); return false;\"";
			else $add_link .= "onClick=\"document.location='./do_resa.php?lvl=make_sugg&oresa=popup' \" ";
			$add_link .= " title='".$msg["empr_bt_make_sugg"]."' >".$msg['empr_bt_make_sugg']."</a></span>";
		}
		return $add_link;
	}
} # fin de définition de la classe