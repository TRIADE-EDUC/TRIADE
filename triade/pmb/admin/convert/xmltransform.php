<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: xmltransform.php,v 1.36.2.5.2.1 2025/03/13 16:18:03 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], "xmltransform.php")) die("no access");

global $base_path, $include_path;

if (version_compare(PHP_VERSION,'5','>=') && extension_loaded('xsl')) {
	require_once($include_path.'/xslt-php4-to-php5.inc.php');
}

//Bibliothèque des transformations par défaut

require_once ("$base_path/admin/convert/xml_unimarc.class.php");

//Conversion par une feuille de style XSLT
function perform_xslt($xml, $s, $islast, $isfirst, $param_path) {
	global $base_path, $charset, $opac_url_base;
	$transform="$base_path/admin/convert/imports/".$param_path."/".$s['XSLFILE'][0]['value'];

	//Si c'est la première transformation, on rajoute les entêtes
	if ($isfirst) {
		if(isset($s['ENCODING']) && $s['ENCODING']){
			$xml1 = "<?xml version=\"1.0\" encoding=\"".$s['ENCODING']."\"?>\n<".$s['ROOTELEMENT'][0]["value"];
		}else{
			$xml1 = "<?xml version=\"1.0\" encoding=\"$charset\"?>\n<".$s['ROOTELEMENT'][0]["value"];
		}

		if (isset($s["NAMESPACE"]) && $s["NAMESPACE"]) {
			$xml1.=" xmlns:".$s["NAMESPACE"][0]["ID"]."='".$s["NAMESPACE"][0]["value"]."' ";
		}
		$xml1.=">\n".$xml."\n</".$s['ROOTELEMENT'][0]['value'].">";
		$xml=$xml1;
	}
	$f = fopen($transform, "r");
	$xsl = fread($f, filesize($transform));
	fclose($f);
	$xsl = str_replace('!!charset!!',$charset,$xsl);

	//Création du processeur
	$xh = xslt_create();

	//Encodage = $charset
	if (defined("ICONV_IMPL")) {
		xslt_set_encoding($xh, "$charset");
	}

	// Traite le document
	$r=array();
	if ($result = @xslt_process($xh, 'arg:/_xml', 'arg:/_xsl', NULL, array("/_xml" => $xml, "/_xsl" => $xsl), array("opac_url_base" => $opac_url_base))) {
		$r['VALID']=true;
		$r['DATA']=$result;
		$r['ERROR']="";
		//Si c'est la dernière transformation, on supprime les entêtes et l'élément root
		if ($islast) {
			$m = array();
			$p = preg_match("/<".$s['TNOTICEELEMENT'][0]['value']."(?:\ [^>]*|)>/", $r["DATA"], $m, PREG_OFFSET_CAPTURE);
			if ($p) {
				$r['DATA'] = "  ".substr($r['DATA'], $m[0][1]);
			}
			$p1 = 0;
			$p = 0;
			while ($p!==false) {
				$p1 = $p;
				$p = @strpos($r['DATA'], "</".$s['TNOTICEELEMENT'][0]['value'].">",$p1+strlen("</".$s['TNOTICEELEMENT'][0]['value'].">"));
			}
			if (($p1 !== false)&&($p1!=0)) {
				$r['DATA'] = substr($r['DATA'], 0, $p1+strlen($s['TNOTICEELEMENT'][0]['value'])+3)."\n";
			}
		}
	} else {
		$r['VALID']=false;
		$r['DATA']="";
		$r['ERROR']="Sorry, notice could not be transformed by $transform the reason is that ".xslt_error($xh)." and the error code is ".xslt_errno($xh);
	}

	xslt_free($xh);
	return $r;
}

//Conversion XML en iso2709
function toiso($notice, $s, $islast, $isfirst, $param_path) {
	$r = array();
	$x2i = new xml_unimarc();
	$x2i -> XMLtoiso2709_notice($notice,(isset($s['ENCODING']) ? $s['ENCODING'] : ''));
	if(!empty($x2i->warning_msg[0])){
		$r['WARNING']=$x2i->warning_msg[0];
	}
	if (!empty($x2i->n_valid==0)) {
		$r['VALID']=false;
		$r['DATA']="";
		$r['ERROR']=$x2i->error_msg[0];
	} else {
		$r['VALID']=true;
		$r['DATA']=$x2i->notices_[0];
		$r['ERROR']="";
	}
	return $r;
}

//Conversion iso2709 en XML
function isotoxml($notice, $s, $islast, $isfirst, $param_path) {
	global $charset;
	global $output_params;

	$r = array();
	$i2x = new xml_unimarc();
	$format = $s['FORMAT'] ?? 'unimarc';
	$i2x->iso2709toXML_notice($notice, $format);
	if ($i2x->n_valid == 0) {
		$r['VALID']=false;
		$r['DATA']="";
		$r['ERROR']=$i2x->error_msg[0];
	} else {
		$r['VALID']=true;
		$r['DATA']=$i2x->notices_xml_[0];
		$r['ERROR']="";
		//Si ce n'est pas la dernière transformation, on rajoute des tags root et l'entête
		if (!$islast) {
			$r['DATA'] = "<".$s['TROOTELEMENT'][0]['value'].">\n".$r['DATA'];
			$r['DATA'].= "</".$s['TROOTELEMENT'][0]['value'].">";
			$r['DATA'] = "<?xml version=\"1.0\" encoding=\"".$charset."\" ?>\n".$r['DATA'];
		}
	}

	return $r;
}

//Conversion texte en XML
function texttoxml($notice, $s, $islast, $isfirst, $param_path) {
	global $cols, $charset;

	//initialisations de tableaux utilisés plus loin
	$rep_field = array();
	$rep_subfield = array();
	$vput = array();
	$sep = array();
	$vpt = array();

	// Conversion dans le bon encodage si la chaîne en entrée n'est pas bonne
	$notice_encoding = mb_detect_encoding($notice, "UTF-8,ISO-8859-1");
	if ($notice_encoding) {
	    if ($notice_encoding == 'UTF-8' && $charset == "iso-8859-1") {
	        $notice = encoding_normalize::utf8_decode($notice);
	    } elseif ($notice_encoding == 'ISO-8859-1' && $charset == "utf-8") {
	    	$notice = encoding_normalize::utf8_encode($notice);
	    }
	}

	eval("\$spt=\"".$s["SEPARATOR"][0]["value"]."\";");
	$fields=explode($spt,$notice);

	//Recherche du type doc
	if ($s["COLS"][0]["DT"]) {
		if (!empty($s["COLS"][0]["DT"][0]["CORRESP"][0])) {
			$corresp=$s["COLS"][0]["DT"][0]["CORRESP"][0];
			$f_id=$fields[($corresp["ID"]-1)];
			if ($s["DELIMITEDBY"][0]["value"]) {
				$f_id=trim($f_id,$s["DELIMITEDBY"][0]["value"]);
			}
			for ($i=0; $i<count($corresp["FOR"]); $i++) {
				if ($corresp["FOR"][$i]["ID"]==$f_id) {
					$dt=$corresp["FOR"][$i]["value"];
					break;
				}
			}
		} else $dt=$s["COLS"][0]["DT"][0]["value"];
	}

	//Recherche du bl
	if ($s["COLS"][0]["BL"]) {
		if (!empty($s["COLS"][0]["BL"][0]["CORRESP"][0])) {
			$corresp=$s["COLS"][0]["BL"][0]["CORRESP"][0];
			$f_id=$fields[($corresp["ID"]-1)];
			if ($s["DELIMITEDBY"][0]["value"]) {
				$f_id=trim($f_id,$s["DELIMITEDBY"][0]["value"]);
			}
			if (!empty($corresp["FOR"])) {
    			for ($i=0; $i<count($corresp["FOR"]); $i++) {
    				if ($corresp["FOR"][$i]["ID"]==$f_id) {
    					$bl=$corresp["FOR"][$i]["value"];
    					break;
    				}
    			}
			}
		} else {
			$bl=$s["COLS"][0]["BL"][0]["value"];
		}
	}

	//Recherche du type hl
	if ($s["COLS"][0]["HL"]) {
		if (!empty($s["COLS"][0]["HL"][0]["CORRESP"][0])) {
			$corresp=$s["COLS"][0]["HL"][0]["CORRESP"][0];
			$f_id=$fields[($corresp["ID"]-1)];
			if ($s["DELIMITEDBY"][0]["value"]) {
				$f_id=trim($f_id,$s["DELIMITEDBY"][0]["value"]);
			}
			if (!empty($corresp["FOR"])) {
    			for ($i=0; $i<count($corresp["FOR"]); $i++) {
    				if ($corresp["FOR"][$i]["ID"]==$f_id) {
    					$hl=$corresp["FOR"][$i]["value"];
    					break;
    				}
    			}
			}
		} else {
			$hl=$s["COLS"][0]["HL"][0]["value"];
		}
	}

	if (!$cols) {
	    if (!empty($s["COLS"][0]["COL"])) {
    		for ($j=0; $j<count($s["COLS"][0]["COL"]); $j++) {
    			$cols[$j]=$s["COLS"][0]["COL"][$j];
    			//$cols[$s["COLS"][0]["COL"][$j]["ID"]]=$s["COLS"][0]["COL"][$j];
    		}
	    }
	}
	$param=array();
	$param["rs"][0]["value"]="n";
	$param["dt"][0]["value"]=($dt?$dt:"a");
	$param["bl"][0]["value"]=($bl?$bl:"m");
	$param["hl"][0]["value"]=($hl?$hl:"*");
	$param["el"][0]["value"]="1";
	$param["ru"][0]["value"]="i";

	//Pour chaque colonne
	for ($i=0; $i<count($cols); $i++) {
		//Récupération des id
		$ids=explode(",",$cols[$i]["ID"]);

		//Correspondances
		if (!empty($cols[$i]["CORRESP"])) {
    		for ($j=0; $j<count($cols[$i]["CORRESP"]); $j++) {
    			$corresp[$cols[$i]["CORRESP"][$j]["ID"]]=array();
    			$corresp_table=$cols[$i]["CORRESP"][$j]["FOR"];
    			for ($k=0; $k<count($corresp_table); $k++) {
    				$corresp[$cols[$i]["CORRESP"][$j]["ID"]][$corresp_table[$k]["ID"]]=$corresp_table[$k]["value"];
    			}
    		}
		}

		//print_r($corresp);

		//Séparateurs pour répétition
		if (!empty($cols[$i]["REP"])) {
    		for ($j=0; $j<count($cols[$i]["REP"]); $j++) {
    		    if ($cols[$i]["REP"][$j]["FOR"]=="field") {
    				$rep_field[$cols[$i]["REP"][$j]["ID"]]=$cols[$i]["REP"][$j]["value"];
    		    } else {
    				$rep_subfield[$cols[$i]["REP"][$j]["ID"]]=$cols[$i]["REP"][$j]["value"];
    		    }
    		}
		}
		$max=1;
		for ($j=0; $j<count($ids); $j++) {
		    if ($ids[$j][0]=="'") {
				$vpte=trim($ids[$j],"'");
		    } else {
				if ($s["DELIMITEDBY"][0]["value"]) {
					$fields[$ids[$j]-1]=trim($fields[$ids[$j]-1],$s["DELIMITEDBY"][0]["value"]);
				}
				if ($s["ESCAPED"][0]['value']=="yes") {
					$fields[$ids[$j]-1]=stripslashes($fields[$ids[$j]-1]);
				}
				$vpte=$fields[$ids[$j]-1];
			}
			if (!empty($rep_field[$ids[$j]])) {
				$vput[$ids[$j]]=explode($rep_field[$ids[$j]],$vpte);
				if ($max<count($vput[$ids[$j]])) {
				    $max=count($vput[$ids[$j]]);
				}
			} else $vpt[$j]=$vpte;
		}
		for ($j=0; $j<count($ids); $j++) {
			if (empty($rep_field[$ids[$j]])) {
				for ($k=0; $k<$max; $k++) {
					$vput[$ids[$j]][$k]=$vpt[$j];
				}
			}
		}
		for ($z=0; $z<$max; $z++) {
			$f=array();
			$f["c"]=$cols[$i]["FIELD"][0]["value"];
			$f["ind"]=$cols[$i]["IND"][0]["value"];
			if ($f["ind"]=="") $f["ind"]="  ";
			$subfields=explode(",",$cols[$i]["SUBFIELD"][0]["value"]);
			if (!empty($cols[$i]["SEP"])) {
    			for ($j=0; $j<count($cols[$i]["SEP"]) ;$j++) {
    				$sep[$cols[$i]["SEP"][$j]["ID"]]=$cols[$i]["SEP"][$j]["value"];
    			}
			}
			//$rep_sub=$cols[$i]["SUBFIELD"][0]["REP"];
			//$rep_field=$cols[$i]["FIELD"][0]["REP"];
			for ($j=0; $j<count($ids); $j++) {
				$vprsf=array();
				if ($cols[$i]["SUBFIELD"][0]["value"]) {
					if (!empty($sep[$ids[$j]])) {
						if ($rep_subfield[$ids[$j]]) {
							$vprsf=explode($rep_subfield[$ids[$j]],$vput[$ids[$j]][$z]);
						} else {
							$vprsf[0]=$vput[$ids[$j]][$z];
						}
						for ($x=0; $x<count($vprsf); $x++) {
							$sfv=explode($sep[$ids[$j]],$vprsf[$x]);
							$sf=explode(";",$subfields[$j]);
							for ($k=0; $k<count($sf); $k++) {
								if ($sf[$k]) {
									$nf=count($f["s"]);
									if ($sfv[$k]) {
										if ($corresp[$ids[$j]]) $sfv[$k]=$corresp[$ids[$j]][trim($sfv[$k])];
										$f["s"][$nf]["c"]=$sf[$k];
										$f["s"][$nf]["value"]=htmlspecialchars(trim($sfv[$k]),ENT_QUOTES,$charset);
									}
								}
							}
						}
					} else {
						if (!empty($rep_subfield[$ids[$j]])) {
							$vprsf=explode($rep_subfield[$ids[$j]],$vput[$ids[$j]][$z]);
						} else {
							$vprsf[0]=$vput[$ids[$j]][$z];
						}
						for ($x=0; $x<count($vprsf) ;$x++) {
							if ($vprsf[$x]) {

							    if (!empty($corresp[$ids[$j]])) {
							        $vprsf[$x]=$corresp[$ids[$j]][trim($vprsf[$x])];
							    }
							    $nf=(!empty($f["s"]) && is_countable($f["s"]) ? count($f["s"]) : 0);
								$f["s"][$nf]["c"]=$subfields[$j];
								$f["s"][$nf]["value"]=htmlspecialchars(trim($vprsf[$x]),ENT_QUOTES,$charset);
							}
						}
					}
				} else if ($vput[$ids[$j]][$z]) {
				    if ($corresp[$ids[$j]]) {
				        $vput[$ids[$j]][$z]=$corresp[$ids[$j]][trim($vput[$ids[$j]][$z])];
				    }
					$f["value"]=htmlspecialchars(trim($vput[$ids[$j]][$z]),ENT_QUOTES,$charset);
				}
			}
			if ((!empty($f["s"]))||(!empty($f["value"]))) {
				$param["f"][]=$f;
			}
		}
	}

	/*for ($i=0; $i<count($fields); $i++) {
		$f=array();
		if ($s["DELIMITEDBY"][0]["value"]) {
			$fields[$i]=trim($fields[$i],$s["DELIMITEDBY"][0]["value"]);
		}
		if ($s["ESCAPED"][0][value]=="yes") {
			$fields[$i]=stripslashes($fields[$i]);
		}
		$f["c"]=$cols[$i+1]["FIELD"][0]["value"];
		$f["ind"]=$cols[$i+1]["IND"][0]["value"];
		if ($f["ind"]=="") $f["ind"]="  ";
		if ($cols[$i+1]["SUBFIELD"][0]["value"]) {
			$f["s"][0]["c"]=$cols[$i+1]["SUBFIELD"][0]["value"];
			$f["s"][0]["value"]="<![CDATA[".$fields[$i]."]]>";
		} else {
			$f["value"]=$fields[$i];
		}
		$param["f"][]=$f;
	}*/
	$r=array();
	$r['DATA']=@array_to_xml($param,"notice");
	if ($r['DATA']) {
		//Si ce n'est pas la dernière transformation, on rajoute des tags root et l'entête
		if (!$islast) {
			$r['DATA'] = "<".$s['TROOTELEMENT'][0]['value'].">\n".$r['DATA'];
			$r['DATA'].= "</".$s['TROOTELEMENT'][0]['value'].">";
			$r['DATA'] = "<?xml version=\"1.0\" encoding=\"$charset\" ?>\n".$r['DATA'];
		}
		$r['VALID']=true;
		$r['ERROR']="";
	} else {
		$r['VALID']=false;
		$r['ERROR']="Can't convert to XML line ".$notice;
	}
	return $r;
}
