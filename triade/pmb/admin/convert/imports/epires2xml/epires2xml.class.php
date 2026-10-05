<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: epires2xml.class.php,v 1.4 2023/03/22 09:16:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once("$class_path/marc_table.class.php");
require_once($base_path."/admin/convert/convert.class.php");

class epires2xml extends convert {

	protected static function make_index($descr,$tete) {
		global $charset;
		$data="";
		if ($descr) {
			$d=explode(",",$descr);
			for ($i=0; $i<count($d); $i++) {
				if (trim($d[$i])) {
					$data.="  <f c='606' ind='  '>\n";
					$data.="    <s c='a'>".htmlspecialchars(trim($tete),ENT_QUOTES,$charset)."</s>\n";
					$data.="    <s c='x'>".htmlspecialchars(trim($d[$i]),ENT_QUOTES,$charset)."</s>\n";
					$data.="  </f>\n";
				}
			}
		}
		return $data;
	}
	
	public static function convert_data($notice, $s, $islast, $isfirst, $param_path) {
		global $cols;
		global $ty;
		global $intitules;
		global $base_path,$origine;
		global $tab_functions;
		global $charset;
		
		if (!$tab_functions) $tab_functions=new marc_list('function');
		
		if (!$cols) {
			//On lit les intitulés dans le fichier temporaire
			$fcols=fopen("$base_path/temp/".$origine."_cols.txt","r");
			if ($fcols) {
				$cols=fread($fcols,filesize("$base_path/temp/".$origine."_cols.txt"));
				fclose($fcols);
				$cols=unserialize($cols);
			}
		}
		
		if (!$ty) {
			$ty=array("ARTICLE"=>"v","REVUE"=>"v","LIVRE"=>"a","MEMOIRE"=>"b","DOCUMENT AUDIOVISUEL"=>"g","CDROM"=>"m","CD-ROM"=>"m","DOCUMENT EN LIGNE"=>"l");
		}
		
		//if (!$cols) {
		//	for ($i=0; $i<count($s["FIELDS"][0]["FIELD"]); $i++) {
		//		$cols[$s["FIELDS"][0]["FIELD"][$i]["ID"]]=$s["FIELDS"][0]["FIELD"][$i]["value"];
		//	}
		//}
		
		$ntable=array();
		$fields=explode(";;",$notice);
		for ($i=0; $i<count($fields); $i++) {
			$ntable[$cols[$i]]=$fields[$i];
		}
		
		if (!$ntable["TI"]) {
			$data=""; 
			$error="Titre vide<br />".$notice;
		} else {
			$error="";
			$data="<notice>\n";
			
			//Entête
			$data.="  <rs>n</rs>\n";
			$type = strtoupper($ntable["TY"]);
			if ($ty[$type]) $dt=$ty[$type]; else $dt="a";
			$bl="m";
			$data.="  <dt>".$dt."</dt>\n";
			$data.="<bl>".$bl."</bl>\n";
			$data.="<hl>*</hl>\n<el>1</el>\n<ru>i</ru>\n";
			//Numéro d'enregistrement
			$data.="  <f c='001' ind='  '>".$ntable["REF"]."</f>\n";
			
			//Titre
			$titres=explode(":",$ntable["TI"]);
			$data.="  <f c='200' ind='  '>\n";
			//Titre principal
			$data.="    <s c='a'>".htmlspecialchars(trim($titres[0]),ENT_QUOTES,$charset)."</s>\n";
			//Titre parallèle
			if ($ntable["COL"]) {
				$data.="    <s c='d'>".htmlspecialchars(trim($ntable["COL"]),ENT_QUOTES,$charset)."</s>\n";
			} else if ($ntable["ND"]) {
				$diplome=explode(":",$ntable["ND"]);
				if ($diplome[0]) {
					$data.="    <s c='d'>".htmlspecialchars(trim($diplome[0]),ENT_QUOTES,$charset)."</s>\n";
				}
			}
			//Titre complémentaire
			if ($titres[1])
				$data.="    <s c='e'>".htmlspecialchars(trim($titres[1]),ENT_QUOTES,$charset)."</s>\n";
			$data.="  </f>\n";
			
			//Traitement des Auteurs principaux
			if ($ntable["AU"]) {
				$auteurs=explode(",",$ntable["AU"]);
				if (count($auteurs)>1) {
					$f_a="701";
				} else {
					$f_a="700";
				}
				
				$data_auteurs="";
				for ($i=0; $i<count($auteurs); $i++) {
					$matches = array();
					preg_match_all("/([^\(]*)(\((.*)\))*( (.*))?/",trim($auteurs[$i]),$matches);
					$entree=$matches[1][0];
					$rejete=$matches[3][0];
					$fonction=$matches[5][0];
					if ($entree) {
						$data_auteurs.="    <s c='a'>".htmlspecialchars(trim($entree),ENT_QUOTES,$charset)."</s>\n";
						if ($rejete) {
							$data_auteurs.="    <s c='b'>".htmlspecialchars(trim($rejete),ENT_QUOTES,$charset)."</s>\n";
						}
						$as=array_search($fonction,$tab_functions->table);
						if (($as!==false)&&($as!==null)) $fonction=$as; else $fonction="070";
						$data_auteurs.="    <s c='4'>".$fonction."</s>\n";
					}
				}
				if ($data_auteurs) {
					$data.="  <f c='".$f_a."' ind='  '>\n";
					$data.=$data_auteurs;
					$data.="  </f>\n";
				}
			}
			
			//Traitement des auteurs secondaires
			if ($ntable["AS"]) {
				$auteurs=explode(",",$ntable["AS"]);
				$f_a="702";
	
				$data_auteurs="";
				for ($i=0; $i<count($auteurs); $i++) {
					preg_match_all("/([^\(]*)(\((.*)\))*( (.*))?/",trim($auteurs[$i]),$matches);
					$entree=$matches[1][0];
					$rejete=$matches[3][0];
					$fonction=$matches[5][0];
					if ($entree) {
						$data_auteurs.="    <s c='a'>".htmlspecialchars(trim($entree),ENT_QUOTES,$charset)."</s>\n";
						if ($rejete) {
							$data_auteurs.="    <s c='b'>".htmlspecialchars(trim($rejete),ENT_QUOTES,$charset)."</s>\n";
						}
						//Recherche de la fonction
						$as=array_search($fonction,$tab_functions->table);
						if (($as!==false)&&($as!==null)) $fonction=$as; else $fonction="070";
						$data_auteurs.="    <s c='4'>".$fonction."</s>\n";
					}
				}
				if ($data_auteurs) {
					$data.="  <f c='".$f_a."' ind='  '>\n";
					$data.=$data_auteurs;
					$data.="  </f>\n";
				}
			}
			
			//Traitement des Auteurs collectif
			if ($ntable["AUCO"]) {
				$auteurs=explode(",",$ntable["AUCO"]);
				$f_a="702";
				
				$data_auteurs="";
				for ($i=0; $i<count($auteurs); $i++) {
					$matches = array();
					preg_match_all("/([^\(]*)(\((.*)\))*( (.*))?/",trim($auteurs[$i]),$matches);
					$entree=$matches[1][0];
					$rejete=$matches[3][0];
					$fonction=$matches[5][0];
					if ($entree) {
						$data_auteurs.="    <s c='a'>".htmlspecialchars(trim($entree),ENT_QUOTES,$charset)."</s>\n";
						if ($rejete) {
							$data_auteurs.="    <s c='b'>".htmlspecialchars(trim($rejete),ENT_QUOTES,$charset)."</s>\n";
						}
						$as=array_search($fonction,$tab_functions->table);
						if (($as!==false)&&($as!==null)) $fonction=$as; else $fonction="070";
						$data_auteurs.="    <s c='4'>".$fonction."</s>\n";
					}
				}
				if ($data_auteurs) {
					$data.="  <f c='".$f_a."' ind='  '>\n";
					$data.=$data_auteurs;
					$data.="  </f>\n";
				}
			}
			
			//Editeurs / collection
			if ($ntable["ED"]) {
				$editeur=explode(":",$ntable["ED"]);
				$lieu=$editeur[0];
				$nom=$editeur[1];
				$matches = array();
				preg_match_all("/([^\(]*)(\((.*)\))*?/",trim($editeur[2]),$matches);
				$annee=$matches[1][0];
				$collection=$matches[3][2];
				$collection=str_replace("COLL.","",$collection);
			} else if (!empty($diplome[2])) {
				$lieu=$diplome[1];
				$nom=$diplome[2];
				$annee=$diplome[3];
				$collection='';
			} else {
				$lieu='';
				$nom='';
				$annee='';
				$collection='';
			}
			$data_editeur="";
			if ($nom) {
				$data_editeur.="    <s c='c'>".htmlspecialchars(trim($nom),ENT_QUOTES,$charset)."</s>\n";
				if ($lieu) $data_editeur.="    <s c='a'>".htmlspecialchars(trim($lieu),ENT_QUOTES,$charset)."</s>\n";
				if ($annee) $ann=$annee; else $ann=$ntable["DP"];
				$data_editeur.="    <s c='d'>".htmlspecialchars(trim($ann),ENT_QUOTES,$charset)."</s>\n";
			}
			$editeur_present=false;
			if ($data_editeur) {
				$data.="  <f c='210' ind='  '>\n";
				$data.=$data_editeur;
				$data.="  </f>\n";
				$editeur_present=true;
			}
			
			//Date de publication
			$dp=false;
			if ($ntable["DP"]) {
				if (!$editeur_present) {
					$data.=static::get_converted_field_uni('210', 'd', $ntable["DP"]);
					$dp=true;
				}
			}
			
			//Distributeur
			if ($ntable["DIST"]) {
				if ((!$dp)&&(!$editeur_present)) {
					$data.="  <f c='210' ind=' '>\n";
					$data.="    <s c='a'> </s>\n";
					$data.="  </f>\n";
				}
				$distributeur=explode(":",$ntable["DIST"]);
				if ($distributeur[1]) {
					$nom=$distributeur[1];
					$lieu=$distributeur[0];
				} else {
					$nom=$ntable["DIST"];
					$lieu="";
				}
				$data_editeur="    <s c='c'>".htmlspecialchars(trim($nom),ENT_QUOTES,$charset)."</s>\n";
				if ($lieu) $data_editeur.="    <s c='a'>".htmlspecialchars(trim($lieu),ENT_QUOTES,$charset)."</s>\n";
				$data.="  <f c='210'>\n";
				$data.=$data_editeur;
				$data.="  </f>\n";
			}	
			
			$data.=static::get_converted_field_uni('225', 'a', $collection);
			
			//Notes
			if (($ntable["NO"])&&($ntable["TY"]!="REVUE")) {
				$data.=static::get_converted_field_uni('327', 'a', $ntable["NO"]);
			}
			
			//Résumé
			$data.=static::get_converted_field_uni('330', 'a', $ntable["RESU"]);
			
			//Périodiques
			if ($ntable["TP"]) {
				$data.="  <f c='464' ind='  '>\n";
				$data.="    <s c='t'>".htmlspecialchars(trim($ntable["TP"]),ENT_QUOTES,$charset)."</s>\n";
				$data.="    <s c='u'>".htmlspecialchars(trim($ntable["TN"]),ENT_QUOTES,$charset)."</s>\n";
				$so=explode(",",$ntable["SO"]);
				$data.="    <s c='d'>".htmlspecialchars(trim($so[count($so)-1]),ENT_QUOTES,$charset)."</s>\n";
				unset($so[count($so)-1]);
				$data.="    <s c='v'>".htmlspecialchars(trim(implode(",",$so)),ENT_QUOTES,$charset)."</s>\n";
				$data.="    <s c='p'>".htmlspecialchars(trim($ntable["NO"]),ENT_QUOTES,$charset)."</s>\n";
				$data.="  </f>";
			}
			
			//Indexations
			if ($ntable["GO"]||$ntable["HI"]||$ntable["DENP"]||$ntable["DE"]||$ntable["CD"]) {
				$data.=static::make_index($ntable["GO"],"Géo");
				$data.=static::make_index($ntable["HI"],"Hist");
				$data.=static::make_index($ntable["DENP"],"DENP");
				$data.=static::make_index($ntable["DE"],"Mots clés");
				$data.=static::make_index($ntable["CD"],"CD");
			}
			
			//Champs spéciaux
			$data.=static::get_converted_field_uni('900', 'a', $ntable["OP"]);
			$data.=static::get_converted_field_uni('901', 'a', $ntable["GEN"]);
			$data.=static::get_converted_field_uni('902', 'a', $ntable["DS"]);
			$data.="</notice>\n";
		}
		
		$r = array();
		if (!$error) $r['VALID'] = true; else $r['VALID']=false;
		$r['ERROR'] = $error;
		$r['DATA'] = $data;
		return $r;
	}
}
