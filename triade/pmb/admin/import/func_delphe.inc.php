<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_delphe.inc.php,v 1.12.4.1 2025/03/04 15:50:02 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function recup_noticeunimarc_suite($notice) {
	global $hl,$bl;
	global $info_461,$info_463;
	global $info_606;
	global $serie;


	$info_461 = array();
	$info_463 = array();
	$info_606 = array();
	$serie = array();

	$record = new iso2709_record($notice, AUTO_UPDATE);

	$bl=$record->inner_guide['bl'];
	$hl=$record->inner_guide['hl'];

	$info_461=$record->get_subfield("461","t");
	$info_463=$record->get_subfield("463","v","d");
	$info_606=$record->get_subfield("606","a","2");
} // fin recup_noticeunimarc_suite

function import_new_notice_suite() {
	global $dbh ;
	global $notice_id ;

	global $hl,$bl;
	global $info_461,$info_463;
	global $info_606;
	global $id_unimarc;
	global $delphe_convert;

	//Récupération des id de thésaurus
	$thesEntreprise=identifiants_thesaurus("Entreprise");
	$thesDelphes=identifiants_thesaurus("Mot clé Delphes");
	$thesAciege=identifiants_thesaurus("Aciege");

	//les notices ne sont que des articles...
	if($hl==2 && $bl=="a"){
		$bulletin = array(
			'date' => decoupe_date($info_463[0]['d']),
			'num' => clean_string($info_463[0]['v'])
		);
		$perio = array(
			'titre' =>  clean_string($info_461[0]),
		);
		notice_to_article($perio,$bulletin);
	}

	//les descripteurs

	if(count($delphe_convert)==0){
		init_delphe_term_convert();
	}
	$lang="fr_FR";
	$ordre_categ = 0 ;
	foreach ($info_606 as $terms){
		$categ_id=0;
		$term = $terms['a'];
		switch($terms['2']){
			case "local" :
				$id_thesaurus=$thesEntreprise['TOP'];
				$non_classes =$thesEntreprise['NONCLASSES'];
				$categ_id = find_categ($term,$id_thesaurus,$lang);
				if($categ_id == 0){
					$categ_id = add_categ($term,$id_thesaurus,$non_classes,$lang);
				}
				break;
			default :
				//on regarde par défault dans Aciège...
				$id_thesaurus=$thesAciege['TOP'];
				$non_classes =$thesAciege['NONCLASSES'];
				$categ_id = find_categ($term,$id_thesaurus,$lang);
				if($categ_id == 0){
					//pas trouvé dans aciège, on regarde dans delphes
					$id_thesaurus=$thesDelphes['TOP'];
					$non_classes =$thesDelphes['NONCLASSES'];
					$categ_id = find_categ($term,$id_thesaurus,$lang);
					if($categ_id == 0){
						//pas trouvé dans delphe, on regarde la table de correspondance
						if(isset($delphe_convert[$term]['aciege']) && $delphe_convert[$term]['aciege']!= ""){
							//on reprend la correspondance dans Aciège
							//on peut avoir plusieurs termes séparés par un +...
							$terms_to_keep = explode("+",$delphe_convert[$term]['aciege']);
							if(count($terms_to_keep)>1){
								foreach($terms_to_keep as $term_to_keep){
									$term_to_keep = trim($term_to_keep);
									$categ_id = find_categ($term_to_keep,1,$lang);
									if($categ_id){
										save_categ($categ_id,$ordre_categ);
										$ordre_categ++;
									}
									$categ_id=0;
								}
							}else{
								$categ_id = find_categ($delphe_convert[$term]['aciege'],1,$lang);
							}
						}else if (isset($delphe_convert[$term]['aciege']) && $delphe_convert[$term]['delphes']!=""){
							//ou dans delphes
							$categ_id = find_categ($delphe_convert[$term]['delphes'],$id_thesaurus,$lang);
						}else if (!isset($delphe_convert[$term])){
							//si le terme est présent dans le fichier sans aucunes correspondances,on veut juste pas le traiter du tout, sinon reprise en non classé...
							$categ_id = add_categ($term,$id_thesaurus,$non_classes,$lang);
						}
					}
				}
				break;
		}
		if($categ_id){
			save_categ($categ_id,$ordre_categ);
			$ordre_categ++;
		}
	}

	//on renseigne le champ perso indexpresse avec le 001...
	//on récup l'id du champ
	$rqt = "select idchamp, datatype from notices_custom where name ='cp_index' ";
	$res = pmb_mysql_query($rqt);
	if(pmb_mysql_num_rows($res)){
		$cp_indexpresse = pmb_mysql_fetch_object($res);
		$insert = "insert into notices_custom_values set notices_custom_champ=".$cp_indexpresse->idchamp.", notices_custom_origine=".$notice_id.", notices_custom_".$cp_indexpresse->datatype." = '".$id_unimarc."'";
		pmb_mysql_query($insert) or die(pmb_mysql_error());
	}

} // fin import_new_notice_suite

// TRAITEMENT DES EXEMPLAIRES ICI
function traite_exemplaires () {}
// fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI

// fonction spécifique d'export de la zone 995
function export_traite_exemplaires ($ex=array()) {}




//catégories
function find_categ($term,$id_thesaurus,$lang){
	$categ_id = categories::searchLibelle(addslashes($term),$id_thesaurus,$lang);
	if($categ_id){
		//le terme existe
		$noeud = new noeuds($categ_id);
		if($noeud->num_renvoi_voir){
			$categ_to_index = $noeud->num_renvoi_voir;
		}else{
			$categ_to_index = $categ_id;
		}
	}else{
		$categ_to_index=0;
	}
	return $categ_to_index;
}

function add_categ($term,$id_thesaurus,$non_classes,$lang){
	$n = new noeuds();
	$n->num_thesaurus = $id_thesaurus;
	$n->num_parent = $non_classes;
	$n->save();
	$c = new categories($n->id_noeud, $lang);
	$c->libelle_categorie = $term;
	$c->index_categorie = ' '.strip_empty_words($term).' ';
	$c->save();
	return $n->id_noeud;
}

function save_categ($categ_to_index,$ordre_categ){
	global $notice_id;
	$requete = "INSERT INTO notices_categories (notcateg_notice,num_noeud,ordre_categorie) VALUES(".$notice_id.",".$categ_to_index.",".$ordre_categ.")";
	pmb_mysql_query($requete);
}

function init_delphe_term_convert(){
	global $base_path;

	global $delphe_convert;
	$delphe_convert = array();
	$fp = fopen("$base_path/admin/convert/imports/delphe2unimarciso/TabCorrespDelphes.txt","r");
	while (!feof($fp)) {
		$data = fgetcsv($fp,4096,"\t");
		$delphe_convert[$data[0]] = array('aciege' => $data[2], 'delphes' => $data[3]);
	}
	//highlight_string(print_r($delphe_convert,true));//die;
	fclose($fp);
}

//Pour le formatage de la date
if(!function_exists('decoupe_date')) {
function decoupe_date($date_nom_formate,$annee_seule=false){
	$date="";
	$tab=preg_split("/\D/",$date_nom_formate);

	switch(count($tab)){
		case 3 :
			if(strlen($tab[0]) == 4){
				$date=$tab[0]."-".$tab[1]."-".$tab[2];
			}elseif(strlen($tab[2]) == 4){
				$date=$tab[2]."-".$tab[1]."-".$tab[0];
			}elseif($tab[0] > 31){
				$date="19".$tab[0]."-".$tab[1]."-".$tab[2];
			}elseif($tab[2] > 31){
				$date="19".$tab[2]."-".$tab[1]."-".$tab[0];
			}
			break;
		case 2 :
			if(strlen($tab[0]) == 4){
				$date=$tab[0]."-".$tab[1]."-01";
			}elseif(strlen($tab[1]) == 4){
				$date=$tab[1]."-".$tab[0]."-01";
			}elseif($tab[0] > 31){
				$date="19".$tab[0]."-".$tab[1]."-01";
			}elseif($tab[1] > 31){
				$date="19".$tab[1]."-".$tab[0]."-01";
			}
			break;
		case 1 :
			if(strlen($tab[0]) == 8){
				$date=substr($tab[0],0,4)."-".substr($tab[0],4,2)."-".substr($tab[0],6,2);
			}elseif(strlen($tab[0]) == 6){
				$date=substr($tab[0],0,4)."-".substr($tab[0],4,2)."-01";
			}elseif(strlen($tab[0]) == 4){
					$date=substr($tab[0],0,4)."-01-01";
				}
		}

		if($annee_seule){
			return substr($date,0,4);
		}else{
			return $date;
		}

	}
}

//ca reste pratique...
function update_notice($bl,$hl,$typdoc){
	global $notice_id;
	$update =" update notices set niveau_biblio = '$bl', niveau_hierar ='$hl', typdoc='$typdoc' where notice_id = $notice_id";
	pmb_mysql_query($update);
}
function notice_to_article($perio_info,$bull_info){
	global $notice_id;
	$bull_id = genere_bulletin($perio_info,$bull_info);
	update_notice("a","2","q");
	$insert = "insert into analysis set analysis_bulletin = $bull_id, analysis_notice = $notice_id";
	pmb_mysql_query($insert);
}

function genere_perio($perio_info){
	global $statutnot;
	$search = "select notice_id from notices where tit1 LIKE '".addslashes($perio_info['titre'])."' and niveau_biblio = 's' and niveau_hierar = '1'";
	$res = pmb_mysql_query($search);
	if(pmb_mysql_num_rows($res) == 0){
		//il existe pas, faut le créer
		//le type de document par défaut est révue
		$insert = "insert into notices set tit1 = '".addslashes($perio_info['titre'])."', typdoc = 'n', niveau_biblio = 's', niveau_hierar = '1', statut = '".$statutnot."', create_date = '".date("Y-m-d H:i:s")."'";
		$result = pmb_mysql_query($insert);
		$perio_id = pmb_mysql_insert_id();
	}else $perio_id = pmb_mysql_result($res,0,0);
	return $perio_id;
}

function genere_bulletin($perio_info,$bull_info,$isbull=true){
	global $bl,$hl,$notice_id;
	//on récup et/ou génère le pério
	$perio_id = genere_perio($perio_info);
	//on s'occupe du cas ou on a pas de titre pour le bulletin
	$search = "select bulletin_id from bulletins where date_date  = '".$bull_info['date']."' and bulletin_numero LIKE '".$bull_info['num']."' and bulletin_notice = $perio_id";
	$res = pmb_mysql_query($search);
	if(pmb_mysql_num_rows($res) == 0){
		if($bull_info['mention'] ==""){
			$bull_info['mention'] = substr($bull_info['date'],8,2)."/".substr($bull_info['date'],5,2)."/".substr($bull_info['date'],0,4);
		}
		$insert = "insert into bulletins set date_date  = '".$bull_info['date']."', mention_date = '".$bull_info['mention']."', bulletin_numero = '".$bull_info['num']."', bulletin_notice = $perio_id";
		if($bl == "s" && $hl == "2") {
			$insert .=", num_notice = $notice_id";
			update_notice("b","2","a");
		}
		$result = pmb_mysql_query($insert);
		$bull_id = pmb_mysql_insert_id();
	}else {
		$bull_id = pmb_mysql_result($res,0,0);
	}
	return $bull_id;
}

function identifiants_thesaurus ($thesaurus_name,$langues_thesaurus='fr_FR') {
	global $charset;

	$q = "select id_thesaurus from thesaurus where libelle_thesaurus='".addslashes($thesaurus_name)."'";
	$r = pmb_mysql_query($q);
	if ($o=pmb_mysql_fetch_object($r)) {
		$res['NUMTHESAURUS']=$o->id_thesaurus;
		$q="select id_noeud, autorite from noeuds where num_thesaurus=".$o->id_thesaurus." and autorite in ('TOP','NONCLASSES','ORPHELINS') ";
		$r = pmb_mysql_query($q) or die(pmb_mysql_error()."<br><br>$q<br><br>");
		while ($o=pmb_mysql_fetch_object($r)) {
			$res[$o->autorite]=$o->id_noeud ;
		}
		return $res ;
	} else {
		$q = "INSERT INTO thesaurus (id_thesaurus, libelle_thesaurus, langue_defaut, active, opac_active, num_noeud_racine) VALUES (0, '".addslashes($thesaurus_name)."', '$langues_thesaurus', '1', '1', 0)";
		$r = pmb_mysql_query($q) or die(pmb_mysql_error()."<br><br>$q<br><br>");
		$res['NUMTHESAURUS']=pmb_mysql_insert_id();

		$q = "INSERT INTO noeuds (id_noeud, autorite, num_parent, num_renvoi_voir, visible, num_thesaurus) VALUES (0, 'TOP', 0, 0, '0', ".$res['NUMTHESAURUS'].")";
		$r = pmb_mysql_query($q) or die(pmb_mysql_error()."<br><br>$q<br><br>");
		$res['TOP']=pmb_mysql_insert_id();
		$q = "update thesaurus set num_noeud_racine=".$res['TOP']." where id_thesaurus=".$res['NUMTHESAURUS']." ";
		$r = pmb_mysql_query($q) or die(pmb_mysql_error()."<br><br>$q<br><br>");

		$q = "INSERT INTO noeuds (id_noeud, autorite, num_parent, num_renvoi_voir, visible, num_thesaurus) VALUES (0, 'NONCLASSES', ".$res['TOP'].", 0, '0', ".$res['NUMTHESAURUS'].")";
		$r = pmb_mysql_query($q) or die(pmb_mysql_error()."<br><br>$q<br><br>");
		$res['NONCLASSES']=pmb_mysql_insert_id();

		$q = "INSERT INTO noeuds (id_noeud, autorite, num_parent, num_renvoi_voir, visible, num_thesaurus) VALUES (0, 'ORPHELINS', ".$res['TOP'].", 0, '0', ".$res['NUMTHESAURUS'].")";
		$r = pmb_mysql_query($q) or die(pmb_mysql_error()."<br><br>$q<br><br>");
		$res['ORPHELINS']=pmb_mysql_insert_id();

		$tmp='~termes non classés';
		if($charset=='utf-8'){
			$tmp=encoding_normalize::utf8_normalize($tmp);
		}
		$q = "INSERT INTO categories (num_thesaurus,num_noeud, langue, libelle_categorie, note_application, comment_public, comment_voir, index_categorie) VALUES (".$res['NUMTHESAURUS'].", ".$res['NONCLASSES'].", 'fr_FR', '".$tmp."', '', '', '', ' termes non classes ')";
		$r = pmb_mysql_query($q) or die(pmb_mysql_error()."<br><br>$q<br><br>");

		$q = "INSERT INTO categories (num_thesaurus,num_noeud, langue, libelle_categorie, note_application, comment_public, comment_voir, index_categorie) VALUES (".$res['NUMTHESAURUS'].", ".$res['ORPHELINS'].", 'fr_FR', '~termes orphelins', '', '', '', ' termes orphelins ')";
		$r = pmb_mysql_query($q) or die(pmb_mysql_error()."<br><br>$q<br><br>");

		return $res ;
	}
}