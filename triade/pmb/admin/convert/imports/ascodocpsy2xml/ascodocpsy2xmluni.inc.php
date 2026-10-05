<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ascodocpsy2xmluni.inc.php,v 1.12 2023/08/28 14:01:12 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once("$class_path/marc_table.class.php");

function convert_ascodocpsy($notice, $s, $islast, $isfirst, $param_path) {
	global $cols;
	global $ty;
	global $authors_function;
	global $base_path,$origine;
	global $tab_functions;
	global $charset;
	
	$error=$warning="";

	if (!$tab_functions) $tab_functions=new marc_list('function');
	
	if (!$authors_function) {
		$authors_function=array("Collab."=>"Collaborateur","Coord."=>"Coordinateur","Dir."=>"Directeur de publication","Ed."=>"Editeur scientifique",
				"Ill."=>"Illustrateur","Préf."=>"Préfacier","Trad."=>"Traducteur","Postf."=>"Postfacier");
	}
	
	if (!$cols) {
		//On lit les intitulés dans le fichier temporaire
		$fcols=fopen("$base_path/temp/".$origine."_cols.txt","r");
		if ($fcols) {
			$cols=fread($fcols,filesize("$base_path/temp/".$origine."_cols.txt"));
			fclose($fcols);
			$cols=unserialize($cols);
		}
	}
	
	if(!isset($cols) || !is_array($cols) || !count($cols)){
		$cols=array();
		$error.="Pas de description des champs de fourni<br />\n";
		$data="";
	}
	
	if (!$ty) {
		$tab_type=new marc_list('doctype');
		$ty=array_flip($tab_type->table);
		/*$ty=array("Livre"=>"a","Congrès"=>"h","Mémoire"=>"r",
				"Thèse"=>"o","Rapport"=>"q","Texte officiel"=>"t",
				"Périodique"=>"p","Article"=>"s","Document multimédia"=>"m");*/
	}
	
	$fields=explode("'^'",$notice);
	
	if(count($fields) != count($cols)){
		$error.="Pas le bon nombre de champs<br />\n";
		$data="";
	}
	$ntable=array();
	for ($i=0; $i<count($fields); $i++) {
		$ntable[$cols[$i]]=trim($fields[$i]);
	}
	
	$obligatoire=array();
	//Article
	$obligatoire["s"][]="TYPE";
	$obligatoire["s"][]="PRODFICH";
	$obligatoire["s"][]="AUT";
	$obligatoire["s"][]="TIT";
	$obligatoire["s"][]="DATE";
	$obligatoire["s"][]="MOTCLE";
	$obligatoire["s"][]="REV";
	$obligatoire["s"][]="NUM";
	$obligatoire["s"][]="PDPF";
	//Ouvrage
	$obligatoire["a"][]="TYPE";
	$obligatoire["a"][]="PRODFICH";
	$obligatoire["a"][]="AUT";
	$obligatoire["a"][]="TIT";
	$obligatoire["a"][]="EDIT";
	$obligatoire["a"][]="LIEU";
	$obligatoire["a"][]="PAGE";
	$obligatoire["a"][]="DATE";
	$obligatoire["a"][]="MOTCLE";
	$obligatoire["a"][]="LOC";
	$obligatoire["a"][]="ISBNISSN";
	//Congrès
	$obligatoire["h"]=$obligatoire["a"];
	//Périodique
	$obligatoire["p"][]="TYPE";
	$obligatoire["p"][]="PRODFICH";
	$obligatoire["p"][]="SUPPORTPERIO";
	$obligatoire["p"][]="ISBNISSN";
	$obligatoire["p"][]="REV";
	$obligatoire["p"][]="VIEPERIO";
	$obligatoire["p"][]="ETATCOL";
	//Thése
	$obligatoire["o"][]="TYPE";
	$obligatoire["o"][]="PRODFICH";
	$obligatoire["o"][]="AUT";
	$obligatoire["o"][]="TIT";
	$obligatoire["o"][]="EDIT";
	$obligatoire["o"][]="LIEU";
	$obligatoire["o"][]="PAGE";
	$obligatoire["o"][]="DATE";
	$obligatoire["o"][]="MOTCLE";
	$obligatoire["o"][]="LOC";
	$obligatoire["o"][]="DIPSPE";
	//Mémoire
	$obligatoire["r"]=$obligatoire["o"];
	//Texte officiel
	$obligatoire["t"][]="TYPE";
	$obligatoire["t"][]="PRODFICH";
	$obligatoire["t"][]="TIT";
	$obligatoire["t"][]="MOTCLE";
	$obligatoire["t"][]="THEME";
	$obligatoire["t"][]="LIEN";
	$obligatoire["t"][]="REV";
	$obligatoire["t"][]="NATTEXT";
	$obligatoire["t"][]="DATESAIS";
	//Rapport
	$obligatoire["q"][]="TYPE";
	$obligatoire["q"][]="PRODFICH";
	$obligatoire["q"][]="AUT";
	$obligatoire["q"][]="TIT";
	$obligatoire["q"][]="PAGE";
	$obligatoire["q"][]="DATE";
	$obligatoire["q"][]="MOTCLE";
	$obligatoire["q"][]="THEME";
	$obligatoire["q"][]="LIEN";
	$obligatoire["q"][]="DATESAIS";
	//Document multimédia
	$obligatoire["m"][]="TYPE";
	$obligatoire["m"][]="PRODFICH";
	$obligatoire["m"][]="AUT";
	$obligatoire["m"][]="TIT";
	$obligatoire["m"][]="EDIT";
	$obligatoire["m"][]="LIEU";
	$obligatoire["m"][]="DATE";
	$obligatoire["m"][]="MOTCLE";
	$obligatoire["m"][]="SUPPORT";
	
	if($ty[$ntable["TYPE"]]){
		foreach ($obligatoire[$ty[$ntable["TYPE"]]] as $value) {
			if(!$ntable[$value]){
				$warning.="Pas de ".$value."<br />\n";
			}
		}
	}else{
		$error.="TYPE inconnu<br />\n";
		$data="";
	}

	if ($error) {
		$data=""; 
	} else {
		$error="";
		$data="<notice>\n";
		
		//Entête
		$data.="  <rs>n</rs>\n";
		if ($ty[$ntable["TYPE"]]) $dt=$ty[$ntable["TYPE"]]; else $dt="a";
		
		switch ($dt) {
			case "p"://Périodique
				$bl = "s";
				$hl = "1";
				break;
			case "s"://Article
			case "t"://Texte officiel
				$bl = "a";
				$hl = "2";
				break;
			default :
				if(($dt == "q") && ($ntable["REV"])) {//Rapport
					$bl = "a";
					$hl = "2";
				} else {
					$bl = "m";
					$hl = "0";
				}
		}
		$data.="  <dt>".$dt."</dt>\n";
		$data.="<bl>".$bl."</bl>\n";
		$data.="<hl>".$hl."</hl>\n<el>1</el>\n<ru>i</ru>\n";
		
//		//Support du document
//		if ($ntable["SUPPORT"]) {
//			
//		}
		
		$with_titre=false;
		$with_titre_perio=false;
		$with_bull_info=false;
		//Traitement des titres
		if ($ntable["TIT"]) {
			$tmp_titre="";
			$tmp_titre=htmlspecialchars($ntable["TIT"],ENT_QUOTES,$charset);
			if($tmp_titre){
				$with_titre=true;
			}
			$data.="  <f c='200' ind='  '>\n";
			$data.="    <s c='a'>".$tmp_titre."</s>\n";
			$data.="  </f>\n";
		}

		//Titre de revue (périodique)
		if($ntable["REV"]){
			$tmp_titre="";
			if ($ntable["TYPE"] == (($charset == "utf-8")?encoding_normalize::utf8_normalize("Périodique"):"Périodique")) {
				$code = '200';
				$ss_code = 'a';
				$tmp_titre=htmlspecialchars($ntable["REV"],ENT_QUOTES,$charset);
				if($tmp_titre){
					$with_titre=true;
				}
			} else {
				$code = '461';
				$ss_code = 't';
				$tmp_titre=htmlspecialchars($ntable["REV"],ENT_QUOTES,$charset);
				if($tmp_titre){
					$with_titre_perio=true;
				}
			}
			$data .= "  <f c='".$code."' ind='  '>\n";
			$data .= "		<s c='".$ss_code."'>".$tmp_titre."</s>\n";
			//Volume ou tome
			if ($ntable["VOL"] && ($code == "461")) {
				$with_bull_info=true;
				$data.="    	<s c='v'>".htmlspecialchars($ntable["VOL"],ENT_QUOTES,$charset)."</s>\n";
			}
			$data.="  </f>\n";
		}elseif($ntable["VOL"]){
			$with_bull_info=true;
			$data.="  <f c='461' ind='  '>\n";
			$data.="    	<s c='v'>".htmlspecialchars($ntable["VOL"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Reprise DATETEXT comme DATE si c'est un "Texte officiel"
		if( ($dt == "t") && (!trim($ntable["DATEPUB"])) && (!trim($ntable["DATE"])) && ($ntable["DATETEXT"]) ){
			$ntable["DATE"]=$ntable["DATETEXT"];
		}elseif($ntable["DATEPUB"]) { //Date de publication du texte -> Que pour les textes officiel
			$with_bull_info=true;
			$data.="  <f c='210' ind='  '>\n";
			$data.="    <s c='d'>".htmlspecialchars($ntable["DATEPUB"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Date de vie et de mort du périodique -> Que pour les périodiques
		if (($ntable["VIEPERIO"])/* && ($ntable["VIEPERIO"] != "[s.d.]")*/) {
			$data.="  <f c='210' ind='  '>\n";
			$data.="    <s c='d'>".htmlspecialchars($ntable["VIEPERIO"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Editeurs -> Pas présent pour les textes officiel et les périodiques
		if (($ntable["EDIT"])/* && ($ntable["EDIT"] != "[s.n.]")*/) {
			$editeurs = explode("/", $ntable["EDIT"]);
			$data.="  <f c='210' ind='  '>\n";
			for ($i=0; $i<count($editeurs); $i++) {
				$data.="    <s c='c'>".htmlspecialchars($editeurs[$i],ENT_QUOTES,$charset)."</s>\n";
			}
			if (($ntable["LIEU"])/* && ($ntable["LIEU"] != "[s.l.]")*/) {
				$lieux = explode("/", $ntable["LIEU"]);
				for ($i=0; $i<count($lieux); $i++) {
					$data.="    <s c='a'>".htmlspecialchars($lieux[$i],ENT_QUOTES,$charset)."</s>\n";
				}
			}
			if ($ntable["DATE"]) {
				$with_bull_info=true;
				$data.="    <s c='d'>".htmlspecialchars($ntable["DATE"],ENT_QUOTES,$charset)."</s>\n";
			}
			$data.="  </f>\n";
		} elseif ($ntable["DATE"]) {
			$with_bull_info=true;
			$data.="  <f c='210' ind='  '>\n";
			$data.="    <s c='d'>".htmlspecialchars($ntable["DATE"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Traitement des Auteurs
		if ($ntable["AUT"]/* && ($ntable["AUT"] != "[s.n.]")*/) {
			$auteurs=explode("/",$ntable["AUT"]);
			for ($i=0; $i<count($auteurs); $i++) {
				//preg_match_all('~\b[[:upper:]]+\b~', trim($auteurs[$i]),$matches);
				$fonction = "";
				$func_author = "";
				if (pmb_substr($auteurs[$i], pmb_strlen($auteurs[$i])-1,pmb_strlen($auteurs[$i])) == ".") {
					$func_author = trim(pmb_substr($auteurs[$i], strrpos($auteurs[$i], " "),pmb_strlen($auteurs[$i])));
				}
				
				$entree=trim(str_replace($func_author, "", $auteurs[$i]));
				if ($entree) {
					if ($i == 0) $data.="  <f c='700' ind='  '>\n";
					else $data.="  <f c='701' ind='  '>\n";
					$data.="    <s c='a'>".htmlspecialchars($entree,ENT_QUOTES,$charset)."</s>\n";
//					if ($rejete) {
//						$data.="    <s c='b'>".htmlspecialchars($rejete,ENT_QUOTES,$charset)."</s>\n";
//					}
					$as=array_search($func_author,$tab_functions->table);
					if (($as!==false)&&($as!==null)){
						$fonction=$as;
					}else{
						if (array_key_exists($func_author, $authors_function)) {
							$fonction = $authors_function[$func_author];
						}
						$as=array_search($fonction,$tab_functions->table);
						if (($as!==false)&&($as!==null)){
							$fonction=$as;
						}else{
							$fonction="070";
						}
					}
					$data.="    <s c='4'>".$fonction."</s>\n";
					$data.="  </f>\n";
				}
			}
		}
		
		//Numéro - infos bulletin
		if (($ntable["NUM"])/* && ($ntable["NUM"] != "[s.n.]")*/) {
			//infos bulletin
			$with_bull_info=true;
			$data .= "<f c='463' ind='  '>";
			$data.="	<s c='v'>".htmlspecialchars($ntable["NUM"],ENT_QUOTES,$charset)."</s>";
			$data.="</f>\n";
		}
		
		//Congrès
		if (($ntable["CONGRTIT"]) || ($ntable["CONGRNUM"]) || ($ntable["CONGRLIE"]) || ($ntable["CONGRDAT"])) {
			$data.="  <f c='712' ind='1 '>\n";
			//Intitulé du congrès
			if ($ntable["CONGRTIT"]) {
				$data.="    <s c='a'>".htmlspecialchars($ntable["CONGRTIT"],ENT_QUOTES,$charset)."</s>\n";
			}
			//Numéro du congrès
			if ($ntable["CONGRNUM"]) {
				$data.="    <s c='d'>".htmlspecialchars($ntable["CONGRNUM"],ENT_QUOTES,$charset)."</s>\n";
			}	
			//Lieu du congrès
			if ($ntable["CONGRLIE"]) {
				$data.="    <s c='e'>".htmlspecialchars($ntable["CONGRLIE"],ENT_QUOTES,$charset)."</s>\n";
			}
			//Date du congrès
			if ($ntable["CONGRDAT"]) {
				$data.="    <s c='f'>".htmlspecialchars($ntable["CONGRDAT"],ENT_QUOTES,$charset)."</s>\n";
			}
			$data.="  </f>\n";
		}
		
		//Réédition
		if ($ntable["REED"]) {
			$data.="  <f c='205' ind='  '>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["REED"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Collection
		if ($ntable["COL"]) {
			//$pos_deb_subtitle=strpos($ntable["COL"],":");
			$pos_deb_num_col=mb_strpos($ntable["COL"],";",0,$charset);
			$data.="  <f c='225' ind='  '>\n";
			if ($pos_deb_num_col) {
				$data.="    <s c='v'>".htmlspecialchars(pmb_substr($ntable["COL"],$pos_deb_num_col+1),ENT_QUOTES,$charset)."</s>\n";
				$data.="    <s c='a'>".htmlspecialchars(trim(pmb_substr($ntable["COL"],0,($pos_deb_num_col-1))),ENT_QUOTES,$charset)."</s>\n";
			}else{
				$data.="    <s c='a'>".htmlspecialchars($ntable["COL"],ENT_QUOTES,$charset)."</s>\n";
			}
			$data.="  </f>\n";
		}
		
		//Nombre de pages
		if (($ntable["PAGE"]) && ($ntable["PAGE"] != "[s.p.]")) {
			$data.="  <f c='215' ind='  '>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["PAGE"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//PDPF
		if ($ntable["PDPF"]) {
			$data.="  <f c='215' ind='  '>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["PDPF"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Traitement des Mots-clés
		if ($ntable["MOTCLE"]) {
			$motcles = explode("/",$ntable["MOTCLE"]);
			for ($i=0; $i<count($motcles); $i++) {
				$data.="  <f c='606' ind='  '>\n";
				$data.="    <s c='a'>".htmlspecialchars($motcles[$i],ENT_QUOTES,$charset)."</s>\n";
				$data.="  </f>\n";
			}
		}

		//Résumé
		if ($ntable["RESU"]) {
			$data.="  <f c='330' ind='  '>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["RESU"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Lien
		if ($ntable["LIEN"]) {
			$data.="  <f c='856' ind='  '>\n";
			$data.="    <s c='u'>".htmlspecialchars($ntable["LIEN"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Notes
		if ($ntable["NOTES"]) {
			$data.="  <f c='300' ind='  '>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["NOTES"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//ISBNISSN
		if (($ntable["ISBNISSN"]) && ($ntable["ISBNISSN"] != "0000-0000")) {
			$isbnissn = explode("/",$ntable["ISBNISSN"]);
			$data.="  <f c='010' ind='  '>\n";
			$data.="    <s c='a'>".htmlspecialchars($isbnissn[0],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Champs spéciaux
		//Candidat-descripteur
		if ($ntable["CANDES"]) {
			$candes = explode("/", $ntable["CANDES"]);
			for ($i=0; $i < count($candes); $i++) {
				$data.="  <f c='900'>\n";
				$data.="    <s c='a'>".htmlspecialchars($candes[$i],ENT_QUOTES,$charset)."</s>\n";
				$data.="  </f>\n";
			}
		}
		//Thème
		if ($ntable["THEME"]) {
		    $candes = explode("/", $ntable["THEME"]);
		    for ($i=0; $i < count($candes); $i++) {
		        $data.="  <f c='901'>\n";
		        $data.="    <s c='a'>".htmlspecialchars($candes[$i],ENT_QUOTES,$charset)."</s>\n";
		        $data.="  </f>\n";
		    }
		}
		//Nom Propre
		if ($ntable["NOMP"]) {
			$nomp = explode("/", $ntable["NOMP"]);
			for ($i=0; $i < count($nomp); $i++) {
				$data.="  <f c='902'>\n";
				$data.="    <s c='a'>".htmlspecialchars($nomp[$i],ENT_QUOTES,$charset)."</s>\n";
				$data.="  </f>\n";
			}
		}
		//Producteur de la fiche
		if ($ntable["PRODFICH"]) {
			$prodfich = explode("/", $ntable["PRODFICH"]);
			for ($i=0; $i < count($prodfich); $i++) {
				if($prodfich[$i] && ($prodfich[$i] != "[vide]")){
					$tmp_prod_array=explode("-",$prodfich[$i]);
					$match_prod=array();
					if(preg_match("/asco[0]*([0-9]+)/",mb_strtolower($tmp_prod_array[0]),$match_prod)){
						$tmp_prod_array[0]="asco".str_pad($match_prod[1],3,"0",STR_PAD_LEFT);
					}else{
						$error.="PRODFICH incorrect: ".$prodfich[$i]."<br />\n";
					}
					$data.="  <f c='903'>\n";
					$data.="    <s c='a'>".htmlspecialchars(trim($tmp_prod_array[0]),ENT_QUOTES,$charset)."</s>\n";
					$data.="  </f>\n";
				}
			}
		}
		//DIPSPE
		if ($ntable["DIPSPE"]/* && ($ntable["DIPSPE"] != "[vide]")*/) {
			$data.="  <f c='904'>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["DIPSPE"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		//Annexe
		if ($ntable["ANNEXE"]) {
			$annexe = explode("/", $ntable["ANNEXE"]);
			if(count($annexe) == 1){
				$annexe = explode(" ; ", $ntable["ANNEXE"]);
			}
			for ($i=0; $i < count($annexe); $i++) {
				$data.="  <f c='905'>\n";
				$data.="    <s c='a'>".htmlspecialchars($annexe[$i],ENT_QUOTES,$charset)."</s>\n";
				$data.="  </f>\n";	
			}
		}
		//Lien annexe
		if ($ntable["LIENANNE"]) {
			$lienanne = explode(" ; ", $ntable["LIENANNE"]);
			for ($i=0; $i < count($lienanne); $i++) {
				$data.="  <f c='906'>\n";
				$data.="    <s c='a'>".htmlspecialchars($lienanne[$i],ENT_QUOTES,$charset)."</s>\n";
				$data.="  </f>\n";				
			}
		}
		
		//Localisation
		if ($ntable["LOC"]) {
			$loc = explode("/", $ntable["LOC"]);
			for ($i=0; $i < count($loc); $i++) {
				if($loc[$i] && ($loc[$i] != "[vide]")){
					$tmp_loc_array=explode("-",$loc[$i]);
					
					$match_prod=array();
					if(preg_match("/asco[0]*([0-9]+)/",mb_strtolower($tmp_loc_array[0]),$match_prod)){
						$tmp_loc_array[0]="asco".$match_prod[1];
					}else{
						$error.="LOC incorrect: ".$loc[$i]."<br />\n";
					}
					$data.="  <f c='907'>\n";
					$data.="    <s c='a'>".htmlspecialchars(trim($tmp_loc_array[0]),ENT_QUOTES,$charset)."</s>\n";
					$data.="  </f>\n";
					$data.="  <f c='995'>\n";
					$data.="    <s c='a'>".htmlspecialchars(trim($tmp_loc_array[0]),ENT_QUOTES,$charset)."</s>\n";
					if ($ntable["SUPPORT"]) {
						$data.="    <s c='r'>".htmlspecialchars($ntable["SUPPORT"],ENT_QUOTES,$charset)."</s>\n";
					}elseif($ntable["TYPE"]){
						$data.="    <s c='r'>".htmlspecialchars($ntable["TYPE"],ENT_QUOTES,$charset)."</s>\n";
					}
					$data.="  </f>\n";
				}
			}
		}
		
		//Nature du texte
		if ($ntable["NATTEXT"] && ($ntable["NATTEXT"] != "[vide]")) {
			$data.="  <f c='908'>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["NATTEXT"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Date du texte
		if ($ntable["DATETEXT"]) {
			$data.="  <f c='909'>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["DATETEXT"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Numéro du texte officiel
		if ($ntable["NUMTEXOF"]) {
			$data.="  <f c='910'>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["NUMTEXOF"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		//Date de fin de validité
		if ($ntable["DATEVALI"]) {
			$data.="  <f c='911'>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["DATEVALI"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
//		//Date de saisie
//		if ($ntable["DATESAIS"]) {
//			$data.="  <f c='912'>\n";
//			$data.="    <s c='a'>".htmlspecialchars($ntable["DATESAIS"],ENT_QUOTES,$charset)."</s>\n";
//			$data.="  </f>\n";
//		}	
		
		//Etat des collections des centres
		if ($ntable["ETATCOL"] && ($ntable["ETATCOL"] != "[vide]")) {
			$data.="  <f c='913'>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["ETATCOL"],ENT_QUOTES,$charset)."</s>\n";
			if ($ntable["SUPPORTPERIO"] && ($ntable["SUPPORTPERIO"] != "[vide]")) {
				$data.="    <s c='b'>".htmlspecialchars($ntable["SUPPORTPERIO"],ENT_QUOTES,$charset)."</s>\n";
			}
			$data.="  </f>\n";
		}
		
		//Support pour les documents multimédia
		if ($ntable["SUPPORT"]) {
			$data.="  <f c='914'>\n";
			$data.="    <s c='a'>".htmlspecialchars($ntable["SUPPORT"],ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
		}
		
		$data.="</notice>\n";
		
		if(!$with_titre){
			$error.="Pas de titre pour la notice<br />\n";
		}
		
		if(!$with_titre_perio && ($bl == "a")){
			$error.="Pas de titre de p&eacute;riodique pour l'article<br />\n";
		}
		
		if(!$with_bull_info && ($bl == "a")){
			$error.="Pas d'information de bulletin pour l'article (NUM, VOL, DATE et DATETEXT vide)<br />\n";
		}
		
	}
	
	$r = array();
	if(!$error) {
		$r['VALID'] = true; 
	}else {
		$error.=$notice."<br/>\n";
		$r['VALID']=false;
	}
	if($warning){
		//$r['WARNING']="Ne bloque pas la conversion: ".$warning.$notice."<br/>\n";
	}
	
	if($error){
		$r['ERROR'] = "<span style='color:red;'>".$error."</span>";
	}else{
		$r['ERROR'] = "";
	}
	$r['DATA'] = $data;
	return $r;
}
