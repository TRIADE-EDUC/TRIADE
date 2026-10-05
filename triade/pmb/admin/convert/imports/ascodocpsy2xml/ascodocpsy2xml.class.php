<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ascodocpsy2xml.class.php,v 1.6.4.1 2025/04/28 14:27:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once("$class_path/marc_table.class.php");
require_once($base_path."/admin/convert/convert.class.php");

class ascodocpsy2xml extends convert {

	public static function convert_data($notice, $s, $islast, $isfirst, $param_path) {
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
				if (!empty($ntable["VOL"]) && ($code == "461")) {
					$with_bull_info=true;
					$data.="    	<s c='v'>".htmlspecialchars($ntable["VOL"],ENT_QUOTES,$charset)."</s>\n";
				}
				$data.="  </f>\n";
			}elseif (!empty($ntable["VOL"])){
				$with_bull_info=true;
				$data.=static::get_converted_field_uni('461', 'v', $ntable["VOL"]);
			}
			
			//Reprise DATETEXT comme DATE si c'est un "Texte officiel"
			if( ($dt == "t") && (!trim($ntable["DATEPUB"])) && (!trim($ntable["DATE"])) && ($ntable["DATETEXT"]) ){
				$ntable["DATE"]=$ntable["DATETEXT"];
			}elseif($ntable["DATEPUB"]) { //Date de publication du texte -> Que pour les textes officiel
				$with_bull_info=true;
				$data.=static::get_converted_field_uni('210', 'd', $ntable["DATEPUB"]);
			}
			
			//Date de vie et de mort du périodique -> Que pour les périodiques
			if (!empty($ntable["VIEPERIO"])/* && ($ntable["VIEPERIO"] != "[s.d.]")*/) {
				$data.=static::get_converted_field_uni('210', 'd', $ntable["VIEPERIO"]);
			}
			
			//Editeurs -> Pas présent pour les textes officiel et les périodiques
			if (!empty($ntable["EDIT"])/* && ($ntable["EDIT"] != "[s.n.]")*/) {
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
				if (!empty($ntable["DATE"])) {
					$with_bull_info=true;
					$data.="    <s c='d'>".htmlspecialchars($ntable["DATE"],ENT_QUOTES,$charset)."</s>\n";
				}
				$data.="  </f>\n";
			} elseif (!empty($ntable["DATE"])) {
				$with_bull_info=true;
				$data.=static::get_converted_field_uni('210', 'd', $ntable["DATE"]);
			}
			
			//Traitement des Auteurs
			if (!empty($ntable["AUT"]) /* && ($ntable["AUT"] != "[s.n.]")*/) {
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
			if (!empty($ntable["NUM"])/* && ($ntable["NUM"] != "[s.n.]")*/) {
				//infos bulletin
				$with_bull_info=true;
				$data.=static::get_converted_field_uni('463', 'v', $ntable["NUM"]);
			}
			
			//TODO Modification liée à la demande #115316
			//ne plus prendre les 4 champs suivants en auteur de type congrès mais les concaténer dans le champ de note générale
			
			//Congrès
			if (!empty($ntable["CONGRTIT"]) || !empty($ntable["CONGRNUM"]) || !empty($ntable["CONGRLIE"]) || !empty($ntable["CONGRDAT"])) {
				$data.="  <f c='300' ind='  '>\n";
				$data.="    <s c='a'>";
				
				$val_congres_300 = "";
				
				//Intitulé du congrès
				if (!empty($ntable["CONGRTIT"])) {
					$val_congres_300 = htmlspecialchars($ntable["CONGRTIT"],ENT_QUOTES,$charset);
				}
				//Numéro du congrès
				if (!empty($ntable["CONGRNUM"])) {
					if($val_congres_300!="") $val_congres_300 .= ". ".htmlspecialchars($ntable["CONGRNUM"],ENT_QUOTES,$charset);
					else $val_congres_300 = htmlspecialchars($ntable["CONGRNUM"],ENT_QUOTES,$charset);
				}	
				//Lieu du congrès
				if (!empty($ntable["CONGRLIE"])) {
					if($val_congres_300!="") $val_congres_300 .= ", ".htmlspecialchars($ntable["CONGRLIE"],ENT_QUOTES,$charset);
					else $val_congres_300 = htmlspecialchars($ntable["CONGRLIE"],ENT_QUOTES,$charset);
				}
				//Date du congrès
				if (!empty($ntable["CONGRDAT"])) {
					if($val_congres_300!="") $val_congres_300 .= " (".htmlspecialchars($ntable["CONGRDAT"],ENT_QUOTES,$charset).")";
					else $val_congres_300 = "(".htmlspecialchars($ntable["CONGRDAT"],ENT_QUOTES,$charset).")";
				}
				
				$data.=$val_congres_300;
				
				$data.="</s>\n  </f>\n";
			}
			/*
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
			*/
			//Réédition
			if (!empty($ntable["REED"])) {
                $data.=static::get_converted_field_uni('205', 'a', $ntable["REED"]);
			}
			
			//Collection
			if (!empty($ntable["COL"])) {
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
			if (!empty($ntable["PAGE"]) && ($ntable["PAGE"] != "[s.p.]")) {
				$data.=static::get_converted_field_uni('215', 'a', $ntable["PAGE"]);
			}
			
			//PDPF
			if (!empty($ntable["PDPF"])) {
                $data.=static::get_converted_field_uni('215', 'a', $ntable["PDPF"]);
			}
			
			//Traitement des Mots-clés
			if (!empty($ntable["MOTCLE"])) {
				$motcles = explode("/",$ntable["MOTCLE"]);
				for ($i=0; $i<count($motcles); $i++) {
					$data.=static::get_converted_field_uni('606', 'a', $motcles[$i]);
				}
			}
	
			//Résumé
			$data.=static::get_converted_field_uni('330', 'a', $ntable["RESU"]);
			
			//Lien
			$data.=static::get_converted_field_uni('856', 'u', $ntable["LIEN"]);
			
			//Notes
			if (!empty($ntable["NOTES"])) {
			    $data.=static::get_converted_field_uni('300', 'a', $ntable["NOTES"]);
			}
			
			//ISBNISSN
			if (!empty($ntable["ISBNISSN"]) && ($ntable["ISBNISSN"] != "0000-0000")) {
				$isbnissn = explode("/",$ntable["ISBNISSN"]);
				$data.=static::get_converted_field_uni('010', 'a', $isbnissn[0]);
			}
			
			//Champs spéciaux
			//Candidat-descripteur
			if (!empty($ntable["CANDES"])) {
				$candes = explode("/", $ntable["CANDES"]);
				for ($i=0; $i < count($candes); $i++) {
					$data.=static::get_converted_field_uni('900', 'a', $candes[$i]);
				}
			}
			//Thème
			if (!empty($ntable["THEME"])) {
			    $candes = explode("/", $ntable["THEME"]);
			    for ($i=0; $i < count($candes); $i++) {
			    	$data.=static::get_converted_field_uni('901', 'a', $candes[$i]);
			    }
			}
			//Nom Propre
			if (!empty($ntable["NOMP"])) {
				$nomp = explode("/", $ntable["NOMP"]);
				for ($i=0; $i < count($nomp); $i++) {
					$data.=static::get_converted_field_uni('902', 'a', $nomp[$i]);
				}
			}
			//Producteur de la fiche
			if (!empty($ntable["PRODFICH"])) {
				$prodfich = explode("/", $ntable["PRODFICH"]);
				for ($i=0; $i < count($prodfich); $i++) {
					if($prodfich[$i] && ($prodfich[$i] != "[vide]")){
						$tmp_prod_array=explode("-",$prodfich[$i]);
						$match_prod=array();
						if(preg_match("/asco[0]*([0-9]+)/",mb_strtolower($tmp_prod_array[0]),$match_prod)){
							$tmp_prod_array[0]="asco".str_pad($match_prod[1],3,"0",STR_PAD_LEFT);
						}elseif(preg_match("/^criavs/",mb_strtolower(trim($tmp_prod_array[0])))){
							$tmp_prod_array[0]=mb_strtolower(trim($tmp_prod_array[0]));
						}else{
							$error.="PRODFICH incorrect: ".$prodfich[$i]."<br />\n";
						}
						$data.=static::get_converted_field_uni('903', 'a', trim($tmp_prod_array[0]));
					}
				}
			}
			//DIPSPE
			if (!empty($ntable["DIPSPE"])/* && ($ntable["DIPSPE"] != "[vide]")*/) {
				$data.=static::get_converted_field_uni('904', 'a', $ntable["DIPSPE"]);
			}
			//Annexe
			if (!empty($ntable["ANNEXE"])) {
				$annexe = explode("/", $ntable["ANNEXE"]);
				if(count($annexe) == 1){
					$annexe = explode(" ; ", $ntable["ANNEXE"]);
				}
				for ($i=0; $i < count($annexe); $i++) {
					$data.=static::get_converted_field_uni('905', 'a', $annexe[$i]);
				}
			}
			//Lien annexe
			if (!empty($ntable["LIENANNE"])) {
				$lienanne = explode(" ; ", $ntable["LIENANNE"]);
				for ($i=0; $i < count($lienanne); $i++) {
					$data.=static::get_converted_field_uni('906', 'a', $lienanne[$i]);
				}
			}
			
			//Localisation
			if (!empty($ntable["LOC"])) {
				$loc = explode("/", $ntable["LOC"]);
				for ($i=0; $i < count($loc); $i++) {
					if($loc[$i] && ($loc[$i] != "[vide]")){
						$tmp_loc_array=explode("-",$loc[$i]);
						
						$match_prod=array();
						if(preg_match("/asco[0]*([0-9]+)/",mb_strtolower($tmp_loc_array[0]),$match_prod)){
							$tmp_loc_array[0]="asco".$match_prod[1];
						}elseif(preg_match("/^criavs/",mb_strtolower(trim($tmp_loc_array[0])))){
							$tmp_loc_array[0]=mb_strtolower(trim($tmp_loc_array[0]));
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
			if (!empty($ntable["NATTEXT"]) && ($ntable["NATTEXT"] != "[vide]")) {
				$data.=static::get_converted_field_uni('908', 'a', $ntable["NATTEXT"]);
			}
			
			//Date du texte
			$data.=static::get_converted_field_uni('909', 'a', $ntable["DATETEXT"]);
			
			//Numéro du texte officiel
			$data.=static::get_converted_field_uni('910', 'a', $ntable["NUMTEXOF"]);
			
			//Date de fin de validité
			$data.=static::get_converted_field_uni('911', 'a', $ntable["DATEVALI"]);
			
	//		//Date de saisie
	//		$data.=static::get_converted_field_uni('912', 'a', $ntable["DATESAIS"]);
			
			//Etat des collections des centres
			if (!empty($ntable["ETATCOL"]) && ($ntable["ETATCOL"] != "[vide]")) {
				$data.="  <f c='913'>\n";
				$data.="    <s c='a'>".htmlspecialchars($ntable["ETATCOL"],ENT_QUOTES,$charset)."</s>\n";
				if ($ntable["SUPPORTPERIO"] && ($ntable["SUPPORTPERIO"] != "[vide]")) {
					$data.="    <s c='b'>".htmlspecialchars($ntable["SUPPORTPERIO"],ENT_QUOTES,$charset)."</s>\n";
				}
				$data.="  </f>\n";
			}
			
			//Support pour les documents multimédia
			if (!empty($ntable["SUPPORT"])) {
			    $data.=static::get_converted_field_uni('914', 'a', $ntable["SUPPORT"]);
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
}
