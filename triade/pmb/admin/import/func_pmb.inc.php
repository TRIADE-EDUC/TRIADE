<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_pmb.inc.php,v 1.12.4.1 2025/04/16 12:16:50 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path; //Nécessaire pour certaines inclusions
require_once("$class_path/thesaurus.class.php");
require_once("$class_path/noeuds.class.php");
require_once("$class_path/categories.class.php");
require_once($class_path."/serials.class.php");

global $action, $charset;

if($action == "beforeupload"){
	$var_lib="<h2 class='center'>Fonction destinée à l'échange de notices entre PMB et principalement en cas de suppression par erreur de notices</h2>
            	<div class='form-contenu'>
            		<div class='row'>
            			<h3>Prérequis en cas de suppression :</h3>
                        <h3 style='margin-left:2em;'>- Récupérer et installer sur un autre PMB une sauvegarde de la base datant d'avant la suppression et dans ce PMB mettre les notices à récupérer dans un panier.</h3>
                    </div><br/>
                    <div class='row'>
                        <h3>Procédure pour l'échange de notices entre PMB :</h3>
                        <h3 style='margin-left:2em;'>- Export des notices:</h3>
                        <h3 style='margin-left:4em;'>- Réaliser un panier avec toutes les notices que vous souhaitez échanger (Utiliser les transferts entre paniers en cas de besoin)</h3>
                        <h3 style='margin-left:6em;'>- Si vous souhaitez reprendre un périodique avec tous ses articles, il vous faut mettre toutes les notices d'article dans le panier</h3>
                        <h3 style='margin-left:6em;'>- Si vous souhaitez reprendre un périodique avec des notices de bulletins, il vous faut mettre toutes les notices de bulletin dans le panier</h3>
                        <h3 style='margin-left:6em;'>- Si vous avez des liens entre notices vous devez mettre dans le panier les notices liées</h3>
                        <h3 style='margin-left:4em;'>- Avant de faire l'export, assurez vous d'avoir coché l'option \"Exportable\" pour tous les champs personnalisés que vous souhaitez reprendre et qu'ils soient définis de la même façon entre les deux PMB</h3>
                        <h3 style='margin-left:4em;'>- Réaliser un export de type \"UNIMARC ISO2709\" du panier en cochant les options suivantes (laisser les autres décochées) : </h3>
                        <h3 style='margin-left:6em;'>- Conserver les informations des exemplaires dans la zone 995 (Si vous souhaitez reprendre les informations d'exemplaire)</h3>
                        <h3 style='margin-left:6em;'>- Générer les liens (Si vous avez des liens entre notices, des bulletins, des périodiques ou des articles dans votre panier)</h3>
                        <h3 style='margin-left:6em;'>- Liens vers les notices mères (Si vous avez des liens entre notices)</h3>
                        <h3 style='margin-left:6em;'>- Liens vers les notices filles (Si vous avez des liens entre notices)</h3>
                        <h3 style='margin-left:6em;'>- Liens vers les bulletins pour les notices d'article (Si vous avez des notices d'article)</h3>
                        <h3 style='margin-left:6em;'>- Liens vers les périodiques pour les notices d'article (Si vous avez des notices d'article)</h3>
                        <h3 style='margin-left:6em;'>- Générer le bulletinage pour les notices de périodique (Si vous avez des notices de périodique et que vous souhaitez reprendre leur bulletinage)</h3>
                        <h3 style='margin-left:2em;'>- Import des notices :</h3>
                        <!--<h3 style='margin-left:4em;'>- Si vous voyez ceci c'est que vous avez choisi la fonction func_pmb.inc.php</h3>-->
                        <h3 style='margin-left:4em;'>- Dans les options ci-dessous faites attention à celles-ci :</h3>
                        <h3 style='margin-left:6em;'>- Générer les liens entre notices ? : Choisir \"Oui\"</h3>
                        <h3 style='margin-left:6em;'>- Statut des notices importées : A utiliser pour retrouver facilement les notices que vous venez d'importer dans votre fonds</h3>
                        <h3 style='margin-left:6em;'>- Les informations de propriétaire, statut et localisation sont prises en compte si elles ne sont pas renseignées dans les exemplaires importés</h3>
                        <h3 style='margin-left:6em;'>- Les informations de codage sont prises en compte</h3>
                    	<br/>
                   </div>
				</div>";
	if($charset == "utf-8"){
		echo encoding_normalize::utf8_normalize($var_lib);
	}else{
		echo $var_lib;
	}
}

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

function renseigne_cp($val, $notice_id, $type="notices"){
	$nom=$val["n"];
	$valeur=$val["a"];
	if(!trim($nom) || !trim($valeur) || !$notice_id){
		return false;
	}

	//on va chercher les informations sur le champ
	$rqt = "SELECT idchamp, type, datatype FROM ".$type."_custom WHERE name='" . addslashes(trim($nom)) . "'";
	$res = pmb_mysql_query($rqt);
	if (!pmb_mysql_num_rows($res))
		return false;

	$cp=pmb_mysql_fetch_object($res);

	if($cp->type != $val["t"]){
		return false;
	}

	//On enregistre la valeur au bon endroit
	switch ($cp->type) {
		case "list":
			//On est sur une liste
			switch ($cp->datatype) {
				case "integer":
					$requete="select ".$type."_custom_list_value from ".$type."_custom_lists where ".$type."_custom_list_lib='".addslashes(trim($valeur))."' and ".$type."_custom_champ='".$cp->idchamp."' ";
					$resultat=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($resultat)) {
						$value2=pmb_mysql_result($resultat,0,0);
					} else {
						$requete="select max(".$type."_custom_list_value*1) from ".$type."_custom_lists where ".$type."_custom_champ='".$cp->idchamp."' ";
						$resultat=pmb_mysql_query($requete);
						$max=@pmb_mysql_result($resultat,0,0);
						$n=$max+1;
						$requete="insert into ".$type."_custom_lists (".$type."_custom_champ,".$type."_custom_list_value,".$type."_custom_list_lib) values('".$cp->idchamp."',$n,'".addslashes(trim($valeur))."')";
						if(!pmb_mysql_query($requete)) return false;
						$value2=$n;
					}
					$requete="insert into ".$type."_custom_values (".$type."_custom_champ,".$type."_custom_origine,".$type."_custom_integer) values('".$cp->idchamp."','".$notice_id."','".$value2."')";
					if(!pmb_mysql_query($requete)) return false;
					break;
				default:
					$requete="select ".$type."_custom_list_value from ".$type."_custom_lists where ".$type."_custom_list_lib='".addslashes(trim($valeur))."' and ".$type."_custom_champ='".$cp->idchamp."' ";
					$resultat=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($resultat)) {
						$value2=pmb_mysql_result($resultat,0,0);
					} else {
						$requete="insert into ".$type."_custom_lists (".$type."_custom_champ,".$type."_custom_list_value,".$type."_custom_list_lib) values('".$cp->idchamp."','".addslashes(trim($valeur))."','".addslashes($valeur)."')";
						if(!pmb_mysql_query($requete)) return false;
						$value2=trim($valeur);
					}
					$requete="insert into ".$type."_custom_values (".$type."_custom_champ,".$type."_custom_origine,".$type."_custom_".$cp->datatype.") values('".$cp->idchamp."','".$notice_id."','".$value2."')";
					if(!pmb_mysql_query($requete)) return false;
					break;
			}
			break;
		case "url":
			$requete="insert into ".$type."_custom_values (".$type."_custom_champ,".$type."_custom_origine,".$type."_custom_".$cp->datatype.") values('".$cp->idchamp."','".$notice_id."','".addslashes(trim($val["c"]))."')";
			if(!pmb_mysql_query($requete)) return false;
			break;
		case "resolve":
			$mes_pp= new parametres_perso($type);
			if($mes_pp->get_formatted_output(array($val["c"]),$cp->idchamp) == $val["b"]){
				$requete="insert into ".$type."_custom_values (".$type."_custom_champ,".$type."_custom_origine,".$type."_custom_".$cp->datatype.") values('".$cp->idchamp."','".$notice_id."','".addslashes($val["c"])."')";
				if(!pmb_mysql_query($requete)) return false;
			}else{
				return false;
			}
			break;
		case "query_list":
		case "query_auth":
			$mes_pp= new parametres_perso($type);
			if($mes_pp->get_formatted_output(array($val["c"]),$cp->idchamp) == $valeur){
				$requete="insert into ".$type."_custom_values (".$type."_custom_champ,".$type."_custom_origine,".$type."_custom_".$cp->datatype.") values('".$cp->idchamp."','".$notice_id."','".addslashes($val["c"])."')";
				if(!pmb_mysql_query($requete)) return false;
			}else{
				return false;
			}
			break;
		default:
			switch ($cp->datatype) {
				case "small_text":
					$requete="insert into ".$type."_custom_values (".$type."_custom_champ,".$type."_custom_origine,".$type."_custom_small_text) values('".$cp->idchamp."','".$notice_id."','".addslashes(trim($valeur))."')";
					if(!pmb_mysql_query($requete)) return false;
					break;
				case "int":
				case "integer":
					$requete="insert into ".$type."_custom_values (".$type."_custom_champ,".$type."_custom_origine,".$type."_custom_integer) values('".$cp->idchamp."','".$notice_id."','".addslashes(trim($valeur))."')";
					if(!pmb_mysql_query($requete)) return false;
					break;
				case "text":
					$requete="insert into ".$type."_custom_values (".$type."_custom_champ,".$type."_custom_origine,".$type."_custom_text) values('".$cp->idchamp."','".$notice_id."','".addslashes(trim($valeur))."')";
					if(!pmb_mysql_query($requete)) return false;
					break;
				case "date":
					$requete="insert into ".$type."_custom_values (".$type."_custom_champ,".$type."_custom_origine,".$type."_custom_date) values('".$cp->idchamp."','".$notice_id."','".addslashes(decoupe_date(trim($valeur)))."')";
					if(!pmb_mysql_query($requete)) return false;
					break;
			}
			break;
	}
	return true;
}

// UPDATE `notices_custom` SET export=1
function recup_noticeunimarc_suite($notice) {
	global $info_100,$info_606_a,$info_606_9,$info_900,$info_999,$info_950,$info_951,$info_996_9;
	$info_100=array();
	$info_606_a=array();
	$info_606_9=array();
	$info_900=array();
	$info_950=array();
	$info_951=array();
	$info_999=array();
	$info_996_9=array();
	$record = new iso2709_record($notice, AUTO_UPDATE);

	$info_100=$record->get_subfield("100","a");
	$info_606_a=$record->get_subfield_array_array("606","a");
	$info_606_9=$record->get_subfield_array_array("606","9");
	$info_900=$record->get_subfield("900","a","b","c","l","n","t");
	$info_950=$record->get_subfield("950","a","b","c","d","e","f","g","h","i","j","k");
	$info_951=$record->get_subfield("951","a","b","c","l","n","f","t");
	$info_996_9=$record->get_subfield_array_array("996","9");
	$info_999=$record->get_subfield("999","a","b","c","l","n","f","t");

} // fin recup_noticeunimarc_suite = fin récupération des variables propres BDP : rien de plus

function import_new_notice_suite() {
	global $id_unimarc,$info_100,$notice_id, $info_606_a, $info_606_9,$info_900,$info_950,$info_951;
	global $suffix,$isbn_OK,$from_file,$thesaurus_defaut;
	global $bibliographic_level, $hierarchic_level;
	if(trim($info_100[0])){
		$date=decoupe_date(substr($info_100[0], 0, 8));
		$requete="update notices set create_date = '".addslashes($date)."' where notice_id='".$notice_id."' ";
		pmb_mysql_query($requete);
		/*if(!pmb_mysql_query($requete)){
			echo "requete echoué : ".$requete."<br>";
		}*/
	}
	$incr_categ=0;
	if(count($info_606_a)){
		$thes = new thesaurus($thesaurus_defaut);
		for($i=0;$i<count($info_606_a);$i++){
			if($libelle=trim($info_606_a[$i][0])){
				//echo "ici : ".$info_606[$i]["a"]."<br>";
				$trouve=false;
				$id_noeud=0;
				foreach ( $info_606_9[$i] as $value ) {
		   			if(preg_match("/^id:([0-9]+)$/",$value,$matches)){
		   				$id_noeud=$matches[1];
		   				break;
		   			}
				}
				if($id_noeud){
					if(categories::exists($id_noeud,"fr_FR")){
						//echo "la : ".$info_606[$i]["a"]."<br>";
						$categ = new categories($id_noeud,"fr_FR");
						if($categ->libelle_categorie == $libelle){
							//echo "ou la : ".$info_606[$i]["a"]."<br>";
							// ajout de l'indexation à la notice dans la table notices_categories
							$rqt_ajout = "insert into notices_categories set notcateg_notice='".$notice_id."', num_noeud='".$categ->num_noeud."', ordre_categorie='".$incr_categ."' " ;
							$res_ajout = @pmb_mysql_query($rqt_ajout);
							$incr_categ++;
							$trouve=true;
						}
					}
				}

				if(!$trouve){
					//Je regarde si il y a une autre catégorie avec ce libellé dans les thésaurus
					$q="SELECT id_noeud from noeuds JOIN categories ON noeuds.id_noeud = categories.num_noeud WHERE categories.libelle_categorie = '".addslashes($libelle)."'";
					$res=pmb_mysql_query($q);
					if($res){
						if(pmb_mysql_num_rows($res) == 1){
							$rqt_ajout = "insert into notices_categories set notcateg_notice='".$notice_id."', num_noeud='".pmb_mysql_result($res,0,0)."', ordre_categorie='".$incr_categ."' " ;
							$res_ajout = @pmb_mysql_query($rqt_ajout);
							$incr_categ++;
						}elseif(pmb_mysql_num_rows($res) > 1){
							$mon_msg= "Catégorie non reprise car elle est présente plusieurs fois dans les thésaurus de PMB: ".$libelle;
							affiche_mes_erreurs($mon_msg);
						}else{
							$n=new noeuds();
							$n->num_parent=$thes->num_noeud_racine;
							$n->num_thesaurus=$thesaurus_defaut;
							$n->save();
							$resultat=$id_n=$n->id_noeud;
							$c=new categories($id_n, $thes->langue_defaut);
							$c->libelle_categorie=$libelle;
							$c->save();
							$rqt_ajout = "insert into notices_categories set notcateg_notice='".$notice_id."', num_noeud='".$id_n."', ordre_categorie='".$incr_categ."' " ;
							$res_ajout = @pmb_mysql_query($rqt_ajout);
							$incr_categ++;
							$mon_msg= "Catégorie créée à la racine du thésaurus par défaut: ".$libelle;
							affiche_mes_erreurs($mon_msg);
						}
					}
				}
			}
		}
	}

	if(($bibliographic_level== "s") && ($hierarchic_level == "1") && count($info_950)){
		foreach ( $info_950 as $value) {
       		//Emplacement
   			if(!$value["c"]){
   				$mon_msg= "Etat de collection non importé car pas d'emplacement pour le périodique: ".$id_unimarc;
				affiche_mes_erreurs($mon_msg);
				continue;
   			}
			$requete="SELECT archempla_id FROM arch_emplacement WHERE archempla_libelle='".addslashes($value["c"])."'";
			$res=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res)){
				$id_empl=pmb_mysql_result($res,0,0);
			}else{
				$requete="INSERT INTO arch_emplacement(archempla_libelle) VALUES('".addslashes($value["c"])."')";
				if(!pmb_mysql_query($requete)){
					$mon_msg= "Etat de collection non importé problème avec la requête: ".$requete;
					affiche_mes_erreurs($mon_msg);
					continue;
				}else{
					$id_empl=pmb_mysql_insert_id();
				}
			}

   			//Statut
   			if(!$value["h"] && !$value["k"]){
   				$mon_msg= "Etat de collection non importé car pas de statut pour le périodique: ".$id_unimarc;
				affiche_mes_erreurs($mon_msg);
				continue;
   			}
			$requete="SELECT archstatut_id FROM arch_statut WHERE archstatut_gestion_libelle='".addslashes($value["k"])."' AND  archstatut_opac_libelle='".addslashes($value["h"])."'";
			$res=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res)){
				$id_statut=pmb_mysql_result($res,0,0);
			}else{
				$requete="INSERT INTO arch_statut(archstatut_gestion_libelle,archstatut_opac_libelle) VALUES('".addslashes($value["k"])."','".addslashes($value["h"])."')";
				if(!pmb_mysql_query($requete)){
					$mon_msg= "Etat de collection non importé problème avec la requête: ".$requete;
					affiche_mes_erreurs($mon_msg);
					continue;
				}else{
					$id_statut=pmb_mysql_insert_id();
				}
			}

			//Support
   			if(!$value["d"]){
   				$mon_msg= "Etat de collection non importé car pas de support pour le périodique: ".$id_unimarc;
				affiche_mes_erreurs($mon_msg);
				continue;
   			}
			$requete="SELECT archtype_id FROM arch_type WHERE archtype_libelle='".addslashes($value["d"])."'";
			$res=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res)){
				$id_support=pmb_mysql_result($res,0,0);
			}else{
				$requete="INSERT INTO arch_type(archtype_libelle) VALUES('".addslashes($value["d"])."')";
				if(!pmb_mysql_query($requete)){
					$mon_msg= "Etat de collection non importé problème avec la requête: ".$requete;
					affiche_mes_erreurs($mon_msg);
					continue;
				}else{
					$id_support=pmb_mysql_insert_id();
				}
			}

			//Localisation
   			if(!$value["a"]){
   				$mon_msg= "Etat de collection non importé car pas de localisation pour le périodique: ".$id_unimarc;
				affiche_mes_erreurs($mon_msg);
				continue;
   			}
			$requete="SELECT idlocation FROM docs_location WHERE location_libelle='".addslashes($value["a"])."'";
			$res=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res)){
				$id_loc=pmb_mysql_result($res,0,0);
			}else{
				$requete="INSERT INTO docs_location(location_libelle) VALUES('".addslashes($value["a"])."')";
				if(!pmb_mysql_query($requete)){
					$mon_msg= "Etat de collection non importé problème avec la requête: ".$requete;
					affiche_mes_erreurs($mon_msg);
					continue;
				}else{
					$id_loc=pmb_mysql_insert_id();
				}
			}

			$stat=$value["b"];
			$cote=$value["f"];
			$archive=$value["g"];
			$origine=$value["e"];
			$note=$value["j"];
			$lacune=$value["i"];

   			$requete="insert into collections_state(id_serial,location_id,state_collections,collstate_emplacement,collstate_type,collstate_origine,collstate_cote,collstate_archive,collstate_statut,collstate_lacune,collstate_note) values (" .
				"'".$notice_id."','".$id_loc."','".addslashes($stat)."','".$id_empl."','".$id_support."','".addslashes($origine)."','".addslashes($cote)."','".addslashes($archive)."','".$id_statut."','".addslashes($lacune)."','".addslashes($note)."'" .
				")";
			if(!pmb_mysql_query($requete)){
				$mon_msg= "Etat de collection non importé problème avec la requete: ".$requete;
				affiche_mes_erreurs($mon_msg);
				continue;
			}else{
				$id_coll_stat=pmb_mysql_insert_id();
				if(count($info_951)){
					foreach ( $info_951 as $cle => $val ) {
		       			if($val["f"] == $id_coll_stat){
		       				//Je suis bien sur un cp de cet exemplaire
		       				if(!renseigne_cp($val,$id_coll_stat,"collstate")){
								$mon_msg= "La valeur  : ".$value["a"]." n'a pas été reprise dans le champ personnalisé : ".$value["n"]." car le champ n'existe pas";
								affiche_mes_erreurs($mon_msg);
							}else{
								unset($info_951[$cle]);
							}
		       			}
					}
				}
			}
		}
	}

	if(count($info_900)){
		for($i=0;$i<count($info_900);$i++){
			if(trim($info_900[$i]["a"])){
				if(!renseigne_cp($info_900[$i],$notice_id)){
					$mon_msg= "La valeur  : ".$info_900[$i]["a"]." n'a pas été reprise dans le champ personnalisé : ".$info_900[$i]["n"]." car le champ n'existe pas ou n'est pas défini de la même façon";
					affiche_mes_erreurs($mon_msg);
				}
			}
		}
	}


} // fin import_new_notice_suite

// TRAITEMENT DES EXEMPLAIRES ICI
function traite_exemplaires () {
	global $nb_expl_ignores,$bulletin_ex ;
	global $prix, $notice_id, $info_996_9,$info_999, $tdoc_codage, $book_lender_id,
		$sdoc_codage, $book_statut_id, $statisdoc_codage,
		$cote_mandatory, $book_location_id ;
	global $suffix;
	// lu en 010$d de la notice
	$price = $prix[0];

	$exemplaires=array();
	for($i=0;$i<count($info_996_9);$i++){
		foreach ( $info_996_9[$i] as $value ) {
   			if(preg_match("/^(.*?):(.*)$/",$value,$matches)){
   				$exemplaires[$i][$matches[1]]=$matches[2];
   			}
		}
	}

	$nb_exemplaires = count($exemplaires);
	// la zone 995 est répétable
	for ($nb_expl = 0; $nb_expl < $nb_exemplaires; $nb_expl++) {
		/* RAZ expl */
		$expl = array();

		$data=array();
		/*if(!$info_996[$nb_expl]['a'])$info_996[$nb_expl]['a'] ="Indéterminé";
		$data['lender_libelle']=$info_996[$nb_expl]['a'];
		$book_lender_id=lender::import($data);*/

		//Propriétaire
		if($tmp=trim($exemplaires[$nb_expl]["lender_libelle"])){
			$requete="SELECT idlender FROM lenders WHERE lender_libelle LIKE '".addslashes($tmp)."'";
			$res=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res) && $id=pmb_mysql_result($res,0,0)){
				$local_book_lender_id=$id;
			}else{
				$local_book_lender_id=$book_lender_id;
			}
		}else{
			$local_book_lender_id=$book_lender_id;
		}

		/* préparation du tableau à passer à la méthode */
		$cbarre = $exemplaires[$nb_expl]["expl_cb"];
		if(!$cbarre){
			$mon_msg= "ERREUR : J'ai un exemplaire sans code barres il ne sera donc pas créé";
			affiche_mes_erreurs($mon_msg);
			continue;
		}
		$pb = 1 ;
		$num_login=1 ;
		$expl['cb']=$cbarre;
		while ($pb==1) {
			$q = "SELECT expl_cb FROM exemplaires WHERE expl_cb='".addslashes($expl['cb'])."' LIMIT 1 ";
			$r = pmb_mysql_query($q);
			$nb = pmb_mysql_num_rows($r);
			if ($nb) {
				$expl['cb'] =$cbarre."-".$num_login ;
				$num_login++;
			} else $pb = 0 ;
		}

		if($cbarre != $expl['cb']){
			$mon_msg= "ERREUR : l'exemplaire avec le code barres : ".$cbarre." existe déjà donc il ne sera pas créé";
			affiche_mes_erreurs($mon_msg);
			continue;
		}

		if ($bulletin_ex) {
			$expl['bulletin']=$bulletin_ex;
			$expl['notice']=0;
		} else {
			$expl['notice']     = $notice_id ;
			$expl['bulletin']=0;
		}

		//Support exemplaire
		$data_doc=array();
		$data_doc['tdoc_libelle'] = $exemplaires[$nb_expl]["tdoc_libelle"];
		//if (!$data_doc['tdoc_libelle']) $data_doc['tdoc_libelle'] = "Indéterminé" ;

		$requete="SELECT idtyp_doc FROM docs_type WHERE tdoc_libelle LIKE '".addslashes($data_doc['tdoc_libelle'])."'";
		$res=pmb_mysql_query($requete);
		if(($data_doc['tdoc_libelle']) && pmb_mysql_num_rows($res) && ($id=pmb_mysql_result($res,0,0))){
			$expl['typdoc'] = $id;
		}else{
			$data_doc['duree_pret'] = 0 ; /* valeur par défaut */
			$data_doc['tdoc_codage_import'] = $exemplaires[$nb_expl]["tdoc_codage_import"];
			if ($tdoc_codage) $data_doc['tdoc_owner'] = $local_book_lender_id ;
				else $data_doc['tdoc_owner'] = 0 ;
			$expl['typdoc'] = docs_type::import($data_doc);
		}


		$expl['cote'] = $exemplaires[$nb_expl]["expl_cote"];

		//Section
		$data_doc=array();
		$data_doc['section_libelle'] = $exemplaires[$nb_expl]["section_libelle"];

		$requete="SELECT idsection FROM docs_section WHERE section_libelle LIKE '".addslashes($data_doc['section_libelle'])."'";
		$res=pmb_mysql_query($requete);
		if(($data_doc['section_libelle']) && pmb_mysql_num_rows($res) && ($id=pmb_mysql_result($res,0,0)) ){
			$expl['section'] = $id;
		}else{
			$data_doc['sdoc_codage_import'] = $exemplaires[$nb_expl]["sdoc_codage_import"];
			if ($sdoc_codage) $data_doc['sdoc_owner'] = $local_book_lender_id ;
				else $data_doc['sdoc_owner'] = 0 ;
			$expl['section'] = docs_section::import($data_doc);
		}

		//Statut
		$data_doc=array();
		$data_doc['statut_libelle'] = $exemplaires[$nb_expl]["statut_libelle"];

		$requete="SELECT  idstatut FROM docs_statut WHERE statut_libelle LIKE '".addslashes($data_doc['statut_libelle'])."'";
		$res=pmb_mysql_query($requete);
		if(($data_doc['statut_libelle']) && pmb_mysql_num_rows($res) && ($id=pmb_mysql_result($res,0,0)) ){
			$expl['statut'] = $id;
		}elseif($exemplaires[$nb_expl]["statusdoc_codage_import"]){
			$data_doc['pret_flag'] = 1 ;
			$data_doc['statusdoc_codage_import'] = $exemplaires[$nb_expl]["statusdoc_codage_import"];
			if ($sdoc_codage) $data_doc['statusdoc_owner'] = $local_book_lender_id ;
				else $data_doc['statusdoc_owner'] = 0 ;
			$expl['statut'] = docs_statut::import($data_doc);
		}else{
			$expl['statut'] = $book_statut_id;
		}

		//Localisation
		$requete="SELECT idlocation FROM docs_location WHERE location_libelle LIKE '".addslashes($exemplaires[$nb_expl]["location_libelle"])."'";
		$res=pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($res) && $id=pmb_mysql_result($res,0,0)){
			$expl['location'] = $id;
		}else{
			$expl['location'] = $book_location_id;
		}

		//Code stat
		$data_doc=array();
		$data_doc['codestat_libelle'] = $exemplaires[$nb_expl]["codestat_libelle"];

		$requete="SELECT idcode FROM docs_codestat WHERE codestat_libelle  LIKE '".addslashes($data_doc['codestat_libelle'])."'";
		$res=pmb_mysql_query($requete);
		if(($data_doc['codestat_libelle']) && pmb_mysql_num_rows($res) && ($id=pmb_mysql_result($res,0,0))){
			$expl['codestat'] = $id;
		}else{
			$data_doc['statisdoc_codage_import'] = $exemplaires[$nb_expl]["statisdoc_codage_import"];
			if ($statisdoc_codage) $data_doc['statisdoc_owner'] = $local_book_lender_id ;
				else $data_doc['statisdoc_owner'] = 0 ;
			$expl['codestat'] = docs_codestat::import($data_doc);
		}


        $expl['creation']   = $exemplaires[$nb_expl]["create_date"];
		$expl['note']       = $exemplaires[$nb_expl]["expl_note"];
		$expl['comment']       = $exemplaires[$nb_expl]["expl_comment"];
		$expl['prix']       = $exemplaires[$nb_expl]["expl_prix"];
		$expl['expl_owner'] = $local_book_lender_id ;
		$expl['cote_mandatory'] = $cote_mandatory ;

		$expl['date_depot'] = $exemplaires[$nb_expl]["date_depot"];
		$expl['date_retour'] = $exemplaires[$nb_expl]["date_retour"];

		// quoi_faire
		$expl['quoi_faire'] = 2 ;

		$expl_id = exemplaire::import($expl);
		if ($expl_id == 0) {
			$nb_expl_ignores++;
		}else{
			//Champ perso d'exemplaire
			//echo "Passe ici<br>";
			foreach ( $info_999 as $key => $value ) {
       			if($value["f"] == $cbarre){
       				//Je suis bien sur un cp de cet exemplaire
       				if(!renseigne_cp($value,$expl_id,"expl")){
						$mon_msg= "La valeur  : ".$value["a"]." n'a pas été reprise dans le champ personnalisé : ".$value["n"]." car le champ n'existe pas";
						affiche_mes_erreurs($mon_msg);
					}else{
						unset($info_999[$key]);
					}
       			}
			}
		}

		} // fin for
	} // fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI

// fonction spécifique d'export de la zone 995
function export_traite_exemplaires ($ex=array()) {

	$subfields=array();

	$subfields["a"] = $ex -> lender_libelle;
	$subfields["c"] = $ex -> lender_libelle;
	$subfields["f"] = $ex -> expl_cb;
	$subfields["k"] = $ex -> expl_cote;
	$subfields["u"] = $ex -> expl_note;

	if ($ex->statusdoc_codage_import) $subfields["o"] = $ex -> statusdoc_codage_import;
	if ($ex -> tdoc_codage_import) $subfields["r"] = $ex -> tdoc_codage_import;
		else $subfields["r"] = "uu";
	if ($ex -> sdoc_codage_import) $subfields["q"] = $ex -> sdoc_codage_import;
		else $subfields["q"] = "u";

	global $export996 ;
	global $export_traitement_exemplaires ;
	$export996['0'] = $export_traitement_exemplaires ;

	return 	$subfields ;

}

if(!function_exists('affiche_mes_erreurs')) {
    function affiche_mes_erreurs($mon_msg,$affiche=true,$log=true){
        global $charset;
        if($charset == "utf-8"){
            $mon_msg= encoding_normalize::utf8_normalize($mon_msg);
        }
        if($affiche){
            echo $mon_msg."<br>";
        }
        if($log){
            pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_".addslashes(SESSid).".inc', '".addslashes($mon_msg)."') ") ;
        }
    }
}
