<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: isbn.inc.php,v 1.27.2.3.2.2 2025/04/10 15:43:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path;
global $array_isbn_ranges;

require_once($class_path."/cache_factory.class.php");

if ((!isset($array_isbn_ranges))||(!is_array($array_isbn_ranges))||(!count($array_isbn_ranges))) {
	require_once $include_path."/parser.inc.php" ;
	$array_isbn_ranges = load_isbn_ranges();
}

// fonctions pour gérer les ISBN
function isISBN($isbn) {
    if (empty($isbn)) {
        return false;
    }

    // s'il y a des lettres, ce n'est pas un ISBN
    if (preg_match('/[A-WY-Z]/i', $isbn)) {
        return false;
    }

    $checksum = 0;
    $isbn = preg_replace('/-|\.| /', '', $isbn);

    $strlen_isbn = strlen($isbn);
    $key = $isbn[$strlen_isbn - 1];

    if ($strlen_isbn == 10) {
        if ($key == 'X') {
            $key = 10;
        }
        $isbn = substr($isbn, 0, $strlen_isbn - 1);

        // vérification de la clé
        for($i = 0; $i < strlen($isbn) ; $i++) {
            $checksum += (10 - $i) * intval($isbn[$i]);
        }
        $checksum += intval($key);

        if (($checksum % 11) == 0) {
            return true;
        }
    } elseif ($strlen_isbn == 13) {
		if (!is_numeric($isbn)) {
			return false;
		}

        if ((substr($isbn, 0, 3) == "978") || (substr($isbn, 0, 3) == "979")) {
            //Vérification de la clé
            $p = 1;
            for ($i = 0; $i < 13; $i++) {
				$checksum += $p * intval($isbn[$i]);
                $p = ($p == 1 ? 3 : 1);
            }
            if (($checksum % 10) == 0) {
                return true;
            }
        }
	}
	return false;
}


function isISBN10($isbn) {
	// return true si ISBN sur 10 caractères
    if(preg_match('/[A-WY-Z]/i', $isbn)) {
		return false;
    }
	$isbn = preg_replace('/-|\.| /', '', $isbn);
	if (strlen($isbn)==10) {
	    return true;
	}
	return false;
}

function isISBN13($isbn) {
	// return true si ISBN sur 10 caractères
    if(preg_match('/[A-WY-Z]/i', $isbn)) {
		return false;
    }
	$isbn = preg_replace('/-|\.| /', '', $isbn);
	if (strlen($isbn)==13) {
	    return true;
	}
	return false;
}

function key10($isbnwk) {
	$cksum=0;
	for ($i=0; $i<strlen($isbnwk); $i++) {
		$cksum+=(10-$i)*intval($isbnwk[$i]);
	}
	$key=$cksum%11;
	$key=11-$key;
	if ($key==1) {
	    $key="X"; 
	} else if ($key==11) {
	    $key=0;
	}
	return $key;
}

function key13($isbnwk) {
	$cksum = 0;
	$p = 1;
	for ($i = 0; $i < strlen($isbnwk); $i++) {
		$cksum += $p * intval($isbnwk[$i]);
		if ($p == 1) {
		    $p = 3;
		} else {
		    $p = 1;
		}
	}
	$key = 10 - $cksum % 10;
	if ($key==10) {
	    $key = 0;
	}
	return $key;
}

function formatISBN($isbn, $taille="") {
	global $array_isbn_ranges;

	$isbn = preg_replace('/-|\.| /', '', $isbn);

	if (strlen($isbn)==13) {
		$segg=substr($isbn,0,3);
		$seggBis=$segg;
		$isbn=substr($isbn,3,10);
	} else {
		$segg="";
		$seggBis="978";
	}

	// traitement du code géographique

	$sTmp1 = intval(substr($isbn, 0, 1));
	$sTmp2 = intval(substr($isbn, 0, 2));
	$sTmp3 = intval(substr($isbn, 0, 3));
	$sTmp4 = intval(substr($isbn, 0, 4));
	$sTmp5 = intval(substr($isbn, 0, 5));

	$seg1 = "";
	if (isset($array_isbn_ranges[$seggBis])) {
		foreach ($array_isbn_ranges[$seggBis] as $code=>$ranges) {
			$len_code = strlen($code);
			if ($code == ${"sTmp".$len_code}) {
				$seg1 = $code;
				break;
			}
		}
	}
	if ($seg1==="") {
		$seg1 = $sTmp5;
	}

	$isbn = preg_replace("/^$seg1/", '', $isbn);

	// calcul du segment de l'éditeur
	$seg2=$isbn;
	if (isset($array_isbn_ranges[$seggBis][$seg1])) {
		foreach($array_isbn_ranges[$seggBis][$seg1] as $motif){
			$tmpRange=explode("-",$motif);
			$strlen=strlen($tmpRange[0]);
			$sTmp = intval(substr($isbn, 0, $strlen));
			if(($sTmp>=(int)$tmpRange[0])&&($sTmp<=(int)$tmpRange[1])){
				$seg2 = substr($isbn, 0, $strlen);
				break;
			}
		}
	}

	$isbn = preg_replace("/^$seg2/", '', $isbn);

	$key = substr($isbn, strlen($isbn) - 1, 1);

	$seg3 = substr($isbn, 0, strlen($isbn) - 1);

	$isbn = ($segg?$segg."-":"")."$seg1-$seg2-$seg3-$key";

	if (!$taille)
		return $isbn;
	else {
		if ($taille==10) {
			//C'est un 13, on recalcule la clef pour le 10
			if ($segg) {
				$key=key10($seg1.$seg2.$seg3);
				return "$seg1-$seg2-$seg3-$key";
			} else return $isbn;
		} else if ($taille==13) {
			//C'est un 10, on recalcule la clef
			if (!$segg) {
				$segg="978";
				$key=key13($segg.$seg1.$seg2.$seg3);
				return "$segg-$seg1-$seg2-$seg3-$key";
			} else return $isbn;
		}
	}
}

function load_isbn_ranges() {
	global $include_path,$base_path,$charset, $KEY_CACHE_FILE_XML;

	$array_isbn_ranges = array();

	$xmlFile=$include_path."/notice/isbn_ranges.xml";
	// Gestion de fichier subst
	$xmlFile_subst=substr($xmlFile,0,-4)."_subst.xml";
	if (file_exists($xmlFile_subst)){
		$xmlFile=$xmlFile_subst;
	}
	$fileInfo = pathinfo($xmlFile);
	$tempFile = $base_path."/temp/XML".preg_replace("/[^a-z0-9]/i","",$fileInfo['dirname'].$fileInfo['filename'].$charset).".tmp";
	$dejaParse=false;

	$cache_php=cache_factory::getCache();
	$key_file="";
	if($cache_php){
		$key_file=getcwd().$xmlFile.filemtime($xmlFile);
		if($xmlFile_subst && file_exists($xmlFile_subst)){
			$key_file.=filemtime($xmlFile_subst);
		}
		$key_file=$KEY_CACHE_FILE_XML.md5($key_file);

		if($tmp_key = $cache_php->getFromCache($key_file)){
			if($array_isbn_ranges = $cache_php->getFromCache($tmp_key)){
				$dejaParse = true;
			}
		}
	}

	if (!$dejaParse &&  (!file_exists($tempFile) || (filemtime($xmlFile) > filemtime($tempFile)))) {
		//Le fichier XML original a-t-il été modifié ultérieurement ?
			//on va re-générer le pseudo-cache
			if(file_exists($tempFile)){
				unlink($tempFile);
			}
			//Parse le fichier dans un tableau
			$fp=fopen($xmlFile,"r") or die(htmlentities("Can't find XML file $xmlFile", ENT_QUOTES, $charset));
			$xml=fread($fp,filesize($xmlFile));
			fclose($fp);
			$param=_parser_text_no_function_($xml, "RANGES");
			//Récupération des éléments
			for ($i=0; $i<count($param["PREFIX"]); $i++) {
				for ($j=0; $j<count($param["PREFIX"][$i]["ZONE"]); $j++) {
					for ($k=0; $k<count($param["PREFIX"][$i]["ZONE"][$j]["RANGE"]); $k++) {
						$array_isbn_ranges[$param["PREFIX"][$i]["ID"]][$param["PREFIX"][$i]["ZONE"][$j]["ID"]][]=$param["PREFIX"][$i]["ZONE"][$j]["RANGE"][$k]["value"];
					}
				}
			}

			if($cache_php){
				$key_file_content=$KEY_CACHE_FILE_XML.md5(serialize($array_isbn_ranges));
				$cache_php->setInCache($key_file_content, $array_isbn_ranges);
				$cache_php->setInCache($key_file,$key_file_content);
			}else{
			    if (file_exists($tempFile)) {
    				$tmp = fopen($tempFile, "wb");
    				fwrite($tmp,serialize($array_isbn_ranges));
    				fclose($tmp);
			    }
			}
	} else if (file_exists($tempFile)){
		$tmp = fopen($tempFile, "r");
		$array_isbn_ranges = unserialize(fread($tmp,filesize($tempFile)));
		fclose($tmp);
	}
	return $array_isbn_ranges;
}

function z_formatISBN($isbn, $taille = "") {
	return formatISBN($isbn, $taille);
}

function isEAN($ean) {
    $ean = preg_replace('/-|\.| /', '', $ean);
    if (!is_numeric($ean)) {
    	return false;
    }

    if (!preg_match('/^978[0-9]|^979[0-9]/', $ean)) {
        return false;
    }

    if (strlen($ean) != 13) {
		return false;
    }

    $checksum = 0;
    for ($i = 0; $i < 13; $i = $i + 2) {
        $checksum += intval($ean[$i]);
    }

    for ($i = 1; $i < 13; $i = $i + 2) {
        $checksum += intval($ean[$i]) * 3;
    }

    if ($checksum % 10 == 0) {
        return true;
    }
    return false;
}


function EANtoISBN10($ean) {
	$checksum=0;
	// on contrôle si cela la conversion est applicable
	if (!isEAN($ean)) return '';
	if(!preg_match('/^978[0-9]/', $ean)) return '';

	$isbn = preg_replace('/^978|[0-9]$/', '', $ean);

	// calcul de la clé
	for ($i = 0; $i < strlen($isbn) ; $i++) {
		$checksum += (10 - $i) * intval(substr($isbn, $i, 1));
	}
	$key = 11 - $checksum % 11;

	if($key == 10) $key = 'X';

	if($key == 11) $key = '0';

	// traitement du code géographique
	$sTmp1 = intval(substr($isbn, 0, 1));
	$sTmp2 = intval(substr($isbn, 0, 2));
	$sTmp3 = intval(substr($isbn, 0, 3));
	$sTmp4 = intval(substr($isbn, 0, 4));
	$sTmp5 = intval(substr($isbn, 0, 5));

	if($sTmp1 <= 7) {
		$seg1 = $sTmp1;
	} else {
		if($sTmp2 <= 94) {
			$seg1 = $sTmp2;
		} else {
			if($sTmp3 <= 995) {
				$seg1 = $sTmp3;
			} else {
				if($sTmp4 <= 9989) {
					$seg1 = $sTmp4;
				} else {
					$seg1 = $sTmp5;
				}
			}
		}
	}

	$isbn = preg_replace("/^$seg1/", '', $isbn);

	// calcul du segment de l'éditeur
	$sTmp2 = intval(substr($isbn, 0, 2));
	$sTmp3 = intval(substr($isbn, 0, 3));
	$sTmp4 = intval(substr($isbn, 0, 4));
	$sTmp5 = intval(substr($isbn, 0, 5));
	$sTmp6 = intval(substr($isbn, 0, 6));
	$sTmp7 = intval(substr($isbn, 0, 7));

	if($sTmp2 <= 19) {
		$seg2 = substr($isbn, 0, 2);
	} else {
		if($sTmp3 <= 699) {
			$seg2 = substr($isbn, 0, 3);
		} else {
			if($sTmp4 <= 8399) {
				$seg2 = substr($isbn, 0, 4);
			} else {
				if($sTmp5 <= 89999) {
					$seg2 = substr($isbn, 0, 5);
				} else {
					if($sTmp6 <= 9499999) {
						$seg2 = substr($isbn, 0, 6);
					} else {
						$seg2 = substr($isbn, 0, 7);
					}
				}
			}
		}
	}

	$seg3 = preg_replace("/^$seg2/", '', $isbn);
	$isbn = "$seg1-$seg2-$seg3-$key";
	return $isbn;
}

function EANtoISBN($ean) {
	// on contrôle si cela la conversion est applicable
	if (!isEAN($ean))
		return '';
	$isbn = formatISBN($ean);
	// si échec de formatage (se termine par --), on prend l'EAN comme il vient
	if(strpos($isbn, '--') !== false) {
		return $ean;
	}
	return $isbn;
}


function z_EANtoISBN($ean) {
	// on contrôle si cela la conversion est applicable
	if (!isEAN($ean))
		return '';

	return z_formatISBN($ean);
}

function traite_code_isbn ($saisieISBN="") {
	if($saisieISBN) {
		if(isEAN($saisieISBN)) {
			// la saisie est un EAN -> on tente de le formater en ISBN
			$code = EANtoISBN($saisieISBN);
			// si échec, on prend l'EAN comme il vient
			if(!$code) $code = $saisieISBN;
		} else {
			if(isISBN($saisieISBN)) {
				// si la saisie est un ISBN
				$code = formatISBN($saisieISBN);
				// si échec, ISBN erroné on le prend sous cette forme
				if(!$code) $code = $saisieISBN;
			} else {
				// ce n'est rien de tout ça, on prend la saisie telle quelle
				$code = $saisieISBN;
						}
		}
		return $code ;
	}
	return "";
}

//Pour vérifier un ISSN
function isISSN($issn) {

	$checksum=0;

	// s'il y a des lettres, pas un ISSN
	if(preg_match('/[A-WY-Z]/i', $issn)) return FALSE;
	$issn = preg_replace('/-|\.| /', '', $issn);

	//Plus de 8 digits, pas un ISSN
	if (strlen($issn)!=8) return FALSE;

	$key = $issn[strlen($issn) - 1];

	if(strtoupper($key) == 'X') $key = 10;
	$issn = substr($issn, 0, strlen($issn) - 1);

	// vérification de la clé
	for($i = 0; $i < strlen($issn) ; $i++) {
		$checksum += (8 - $i) * $issn[$i];
	}

	$checksum += $key;

	if (($checksum%11) == 0) return TRUE ;
		else return FALSE ;

}

//retourne un code issn formate correctement ou le code saisi si ce n'est pas un issn
function traite_code_ISSN($issn) {
	if ($issn) {
		if (isISSN($issn)) {
			$issn = preg_replace("/[^0-9|X]/i", '', $issn);
			$issn = str_replace('x','X',$issn);
			$issn=substr($issn,0,4).'-'.substr($issn,4,4);
			return $issn;
		} else return $issn;
	}
	return '';
}
