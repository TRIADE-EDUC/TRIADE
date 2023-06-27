<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: import_bretagne_3.inc.php,v 1.20 2019-06-05 13:13:19 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/emprunteur.class.php");

function show_import_choix_fichier($dbh) {
	global $msg;
	global $current_module ;

print "
<form class='form-$current_module' name='form1' ENCTYPE=\"multipart/form-data\" method='post' action=\"./admin.php?categ=empr&sub=implec&action=1\">
<h3>Choix du fichier</h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='form_import_lec'>".$msg["import_lec_fichier"]."</label>
        <input name='import_lec' accept='text/plain' type='file' class='saisie-80em' size='40'>
		</div>
	<div class='row'>
        <label class='etiquette' for='form_import_lec'>". $msg["import_lec_separateur"]."</label>
        <select name='Sep_Champs' >
            <option value=';'>;</option>
            <option value='.'>.</option>
        </select>
    </div>
    <br />
	<div class='row'>
		<b>Structure du fichier pour l'import des &eacute;l&egrave;ves :</b>
	</div>
	<div class='row'>
		[Num&eacute;ro identifiant]/Nom/Pr&eacute;nom/Rue/Compl&eacute;ment de rue/Code postal/Commune/T&eacute;l&eacute;phone/Ann&eacute;e de naissance/Classe/Sexe/[Email]/[login/mdp]/[Prof Principal]
	</div>
	<br />
	<div class='row'>
		<b>Structure du fichier pour l'import des professeurs :</b>
	</div>
	<div class='row'>
		[Num&eacute;ro identifiant]/Nom/Pr&eacute;nom/Adresse 1/Adresse 2/Code postal/Commune/T&eacute;l&eacute;phone/Ann&eacute;e de naissance/Sexe/[Email]/[login/mdp]
	</div>
    <br />
	<div>
    	<b>Votre fichier contient :</b><br />
    	<input type=checkbox name='num_auto' value='num_auto'>
        <label class='etiquette' for='form_import_lec'>Les num&eacute;ros d'emprunteurs</label>
        <br />
		<input type=checkbox name='adr_mail' value='adr_mail'>
        <label class='etiquette' for='form_import_lec'>Les adresses e-mail</label>
        <br />
		<input type=checkbox name='mdp_auto' value='mdp_auto'>
        <label class='etiquette' for='form_import_lec'>Les logins et mots de passe</label>
        <br />
		<input type=checkbox name='prof_principal' value='prof_principal'>
        <label class='etiquette' for='form_import_lec'>Les professeurs principaux</label>
		(uniquement pour l'import des &eacute;l&egrave;ves)
    </div>
    <br />
	<div class='row'>
        <input type=radio name='type_import' value='nouveau_lect' checked>
        <label class='etiquette' for='form_import_lec'>Nouveaux lecteurs</label>
        (ajoute ou modifie les lecteurs pr&eacute;sents dans le fichier)
        <br />
        <input type=radio name='type_import' value='maj_complete'>
        <label class='etiquette' for='form_import_lec'>Mise &agrave; jour compl&egrave;te</label>
        (supprime les lecteurs non pr&eacute;sents dans le fichier et qui n'ont pas de pr&ecirc;t en cours)
    </div>
    <div class='row'></div>

	</div>
<div class='row'>
	<input name='imp_elv' type='submit' class='bouton' value='Import des &eacute;l&egrave;ves'/>
	<input name='imp_prof' value='Import des professeurs' type='submit' class='bouton'/>
</div>
</form>";
}

function cre_login($nom, $prenom, $dbh) {
    $empr_login = substr($prenom,0,1).$nom ;
    $empr_login = strtolower($empr_login);
    $empr_login = clean_string($empr_login) ;
    $empr_login = convert_diacrit(strtolower($empr_login)) ;
    $empr_login = preg_replace('/[^a-z0-9\.]/', '', $empr_login);
    $pb = 1 ;
    $num_login=1 ;
    $debut_log = $empr_login;
    while ($pb==1) {
        $requete = "SELECT empr_login FROM empr WHERE empr_login like '$empr_login' AND (empr_nom <> '$nom' OR empr_prenom <> '$prenom') LIMIT 1 ";
        $res = pmb_mysql_query($requete, $dbh);
        $nbr_lignes = pmb_mysql_num_rows($res);
        if ($nbr_lignes) {
            $empr_login = $debut_log.$num_login ;
            $num_login++;
        }
        else $pb = 0 ;
    }
    return $empr_login;
}

function import_eleves($separateur, $dbh, $type_import, $mdp_auto, $num_auto, $prof_principal, $adr_mail){

    //La structure du fichier texte doit être la suivante :
    //[Numéro identifiant]/Nom/Prénom/Rue/Complément de rue/Code postal/Commune/Téléphone/Année de naissance/Classe/Sexe/[Email]/[login/mdp]/[Prof Principal]

    $eleve_abrege = array("Num&eacute;ro identifiant","Nom","Pr&eacute;nom");
    $date_auj = date("Y-m-d", time());
    $date_an_proch = date("Y-m-d", time()+3600*24*30.42*12);

    //Upload du fichier
    if (!($_FILES['import_lec']['tmp_name']))
        print "Cliquez sur Pr&eacute;c&eacute;dent et choisissez un fichier";
    elseif (!(move_uploaded_file($_FILES['import_lec']['tmp_name'], "./temp/".basename($_FILES['import_lec']['tmp_name'])))) {
        print "Le fichier n'a pas pu &ecirc;tre t&eacute;l&eacute;charg&eacute;. Voici plus d'informations :<br />";
        print_r($_FILES)."<p>";
    }
    $fichier = @fopen( "./temp/".basename($_FILES['import_lec']['tmp_name']), "r" );

    if ($fichier) {
        if ($type_import == 'maj_complete') {
            //Vide la table empr_groupe
            //$delete_empr_groupe = pmb_mysql_query("DELETE FROM empr_groupe",$dbh);
            //Supprime les élèves qui n'ont pas de prêts en cours
            $req_select_verif_pret = "SELECT id_empr, empr_cb FROM groupe, empr_groupe, empr left join pret on id_empr=pret_idempr WHERE pret_idempr is null and empr_groupe.empr_id = empr.id_empr and empr_groupe.groupe_id = id_groupe and libelle_groupe not like 'Professeurs'";
            $select_verif_pret = pmb_mysql_query($req_select_verif_pret,$dbh);
            while (($verif_pret = pmb_mysql_fetch_array($select_verif_pret))) {
            	//pour tous les emprunteurs qui n'ont pas de pret en cours
                emprunteur::del_empr($verif_pret["id_empr"]);
            }
        	// On supprime les groupes qui ne sont plus utilisés.
        	$req_select_verif_groupe = "SELECT id_groupe FROM groupe left join empr_groupe on groupe_id=id_groupe WHERE empr_id is null";
            $select_verif_groupe = pmb_mysql_query($req_select_verif_groupe,$dbh);
            while (($verif_groupe = pmb_mysql_fetch_array($select_verif_groupe))) {
            	//pour tous les groupe qui n'ont plus d'emprunteurs :
                $req_delete = "DELETE FROM groupe WHERE id_groupe = '".$verif_groupe["id_groupe"]."'";
                pmb_mysql_query($req_delete);
            }
        }

        //Récupération dans la table 'empr' du 'empr_cb' maximum
		$req=pmb_mysql_query("SELECT MAX(empr_cb) AS cbmax FROM empr WHERE empr_categ=1 and empr_codestat=1",$dbh);
		$cb=pmb_mysql_result($req,0,"cbmax");
		if (!$cb) {
		    $numeroE="0000";
			}
		else {
			$numeroE= substr($cb,1,4);
		}

        $profDoublon = array();
        $profAbsent = array();

        while (!feof($fichier)) {
            $buffer = fgets($fichier, 4096);
            $buffer = pmb_mysql_escape_string($buffer);
            $tab = explode($separateur, $buffer);

			//Génération du code-barre si l'utilisateur souhaite que les numéros
			// emprunteur soient générer automatiquement
			if($num_auto != 'num_auto') {
				$numeroE=$numeroE+1;
				if ($numeroE < 10) {
				    $eleve_cb = "E000".$numeroE;
				}
				elseif ($numeroE < 100) {
					$eleve_cb = "E00".$numeroE;
				}
				elseif ($numeroE < 1000) {
					$eleve_cb = "E0".$numeroE;
				}
				elseif ($numeroE < 10000) {
					$eleve_cb = "E".$numeroE;
				}
			} else {
				$eleve_cb = $tab[0];
			}

            //Gestion du sexe
            if($num_auto != 'num_auto') {
            	switch ($tab[9]{0}) {
	                case 'M':
	                    $sexe = 1;
	                    break;
	                case 'F':
	                    $sexe = 2;
	                    break;
	                default:
	                    $sexe = 0;
	                    break;
	            }
            }else {
	            switch ($tab[10]{0}) {
	                case 'M':
	                    $sexe = 1;
	                    break;
	                case 'F':
	                    $sexe = 2;
	                    break;
	                default:
	                    $sexe = 0;
	                    break;
	            }
            }

            // Traitement de l'élève
            if($num_auto != 'num_auto') {
            	$select = pmb_mysql_query("SELECT id_empr, empr_cb FROM empr WHERE empr_nom = '".$tab[0]."' AND empr_prenom= '".$tab[1]."' AND empr_year='".$tab[7]."'",$dbh);
            	$nb_enreg = pmb_mysql_num_rows($select);
            	//Test si un numéro id ou nom est fourni
            	if (!$tab[0] || $tab[0] == "") {
	                if($tab[1] != "" || $tab[2] != "") {
		                print("<b> &Eacute;l&egrave;ve non pris en compte car \"Nom\" non renseign&eacute; : </b><br />");
		                for ($i=0;$i<3;$i++) {
		                    print($eleve_abrege[$i]." : ".$tab[$i].", ");
		                }
		                print("<br />");
	                }
	                $nb_enreg = 2;
            	}
            }else {
            	$select = pmb_mysql_query("SELECT id_empr, empr_cb FROM empr WHERE empr_cb = '".$tab[0]."'",$dbh);
            	$nb_enreg = pmb_mysql_num_rows($select);
            	//Test si un numéro id ou nom est fourni
            	if (!$tab[0] || $tab[0] == "") {
	                if ($tab[1] != "" || $tab[2] != "") {
		                print("<b> &Eacute;l&egrave;ve non pris en compte car \"Num&eacute;ro identifiant\" non renseign&eacute; : </b><br />");
		                for ($i=0;$i<3;$i++) {
		                    print($eleve_abrege[$i]." : ".$tab[$i].", ");
		                }
		                print("<br />");
	                }
	                $nb_enreg = 2;
	            }
            }
            if ($mdp_auto != 'mdp_auto') {
            	if($num_auto != 'num_auto') {
            		$login = cre_login($tab[0],$tab[1], $dbh);
            		$mdp = $tab[7];
            	}else {
            		$login = cre_login($tab[1],$tab[2], $dbh);
            		$mdp = $tab[8];
            	}
            } else {
            	if ($adr_mail == 'adr_mail') {
	            	if($num_auto != 'num_auto') {
	            		$login = $tab[11];
	            		$mdp = trim($tab[12]);
	            	}else {
	            		$login= $tab[12];
	            		$mdp = trim($tab[13]);
	            	}
            	} else {
            		if($num_auto != 'num_auto') {
	            		$login = $tab[10];
	            		$mdp = trim($tab[11]);
	            	}else {
	            		$login= $tab[11];
	            		$mdp = trim($tab[12]);
	            	}
            	}
            }
            if (!$mdp || $mdp == "") $mdp = $login;
            if($num_auto != 'num_auto') {
            	// On a pas de numéro identifiant dans le script
            	// on décale donc les indices du tableau à la hause :
            	if ($adr_mail == 'adr_mail') {
	            	if ($mdp_auto != 'mdp_auto') {
	            		$tab[12] = $tab[11];
	            	} else {
	            		$tab[12] = $tab[13];
	            	}
	            	$tab[11] = $tab[10];
            	} else {
            		if ($mdp_auto != 'mdp_auto') {
	            		$tab[12] = $tab[10];
	            	} else {
	            		$tab[12] = $tab[12];
	            	}
	            	$tab[11] = "";
            	}

            	$tab[10] = $tab[9];
            	$tab[9] = $tab[8];
            	$tab[8] = $tab[7];
            	$tab[7] = $tab[6];
            	$tab[6] = $tab[5];
            	$tab[5] = $tab[4];
            	$tab[4] = $tab[3];
            	$tab[3] = $tab[2];
            	$tab[2] = $tab[1];
            	$tab[1] = $tab[0];
            } else {
            	if ($adr_mail == 'adr_mail') {
	            	if ($mdp_auto == 'mdp_auto') {
	            		$tab[12] = $tab[14];
	            	}
            	} else {
	            	if ($mdp_auto == 'mdp_auto') {
	            		$tab[12] = $tab[13];
	            	}
	            	$tab[11] = "";
            	}
            }
        	// On verifie que le mail est bien de la forme chaine@chaine :
            if ($adr_mail == 'adr_mail') {
	            if(preg_match("#.*@.*#",$tab[11]) == false){
					$tab[11] = "";
				}
            }

            switch ($nb_enreg) {
                case 0:
                	//Cet élève n'est pas enregistré
                    $req_insert = "INSERT INTO empr(empr_cb, empr_nom, empr_prenom, empr_adr1, empr_adr2, empr_cp, empr_ville, ";
                    $req_insert .= "empr_mail, empr_tel1, empr_year, empr_categ, empr_codestat, empr_creation, empr_sexe,  ";
                    $req_insert .= "empr_login, empr_password, empr_date_adhesion, empr_date_expiration) ";
                    $req_insert .= "VALUES ('$eleve_cb','$tab[1]','$tab[2]','$tab[3]', '$tab[4]', '$tab[5]', ";
                    $req_insert .= "'$tab[6]', '$tab[11]', '$tab[7]', '$tab[8]', 1, 1, '$date_auj', '$sexe', ";
                    $req_insert .= "'$login', replace(replace('".$mdp."','\n',''),'\r',''), '$date_auj', '$date_an_proch')";
                    $insert = pmb_mysql_query($req_insert,$dbh);
                    if (!$insert) {
                        print("<b>&Eacute;chec de la cr&eacute;ation de l'&eacute;l&egrave;ve suivant (Erreur : ".pmb_mysql_error().") : </b><br />");
                        for ($i=0;$i<3;$i++) {
                            print($eleve_abrege[$i]." : ".$tab[$i].", ");
                        }
                        print("<br />");
                    }
                    else {
                    	emprunteur::update_digest($login,str_replace(array("\n","\r"), "", $mdp));
                    	emprunteur::hash_password($login,str_replace(array("\n","\r"), "", $mdp));
                        $cpt_insert ++;
                    }
                    if($prof_principal == 'prof_principal') {
	                    // On recupère le nom du prof principal :
	            		list ($Mr, $MrNom, $MrPrenom) = explode(' ', $tab[12]);
                    } else {
                    	$MrNom = "";
                    }
                    $resu = gestion_groupe($tab[9], $eleve_cb, $dbh,$MrNom);
                    if($prof_principal == 'prof_principal') {
	                    switch ($resu) {
	                    	case 0:
	                    		// Prof absent :
	                    		$profAbsent[$MrNom]++;
	                    		break;
	                    	case 1 :
	                    		// Prof en doublon :
	                    		$profDoublon[$MrNom]++;
	                    		break;
	                    	default :
	                    		// Pas de problème.
	                    		break;
	                    }
                    }
                    $j++;
                    break;

                case 1:
                	//Cet élève est déja enregistré
                    $req_update = "UPDATE empr SET empr_nom = '$tab[1]', empr_prenom = '$tab[2]', empr_adr1 = '$tab[3]', ";
                    $req_update .= "empr_adr2 = '$tab[4]', empr_cp = '$tab[5]', empr_ville = '$tab[6]', empr_mail = '$tab[11]', ";
                    $req_update .= "empr_tel1 = '$tab[7]', empr_year = '$tab[8]', empr_categ = '1', empr_codestat = '1', empr_modif = '$date_auj', empr_sexe = '$sexe', ";
                    $req_update .= "empr_login = '$login', empr_password=replace(replace('".$mdp."','\n',''),'\r',''), ";
                    $req_update .= "empr_date_adhesion = '$date_auj', empr_date_expiration = '$date_an_proch' ";
                    $req_update .= "WHERE empr_cb = '$eleve_cb'";
                    $update = pmb_mysql_query($req_update, $dbh);
                    if (!$update) {
                        print("<b>&Eacute;chec de la modification de l'&eacute;l&egrave;ve suivant (Erreur : ".pmb_mysql_error().") : </b><br />");
                        for ($i=0;$i<3;$i++) {
                            print($eleve_abrege[$i]." : ".$tab[$i].", ");
                        }
                        print("<br />");
                    }
                    else {
                    	emprunteur::update_digest($login,str_replace(array("\n","\r"), "", $mdp));
                    	emprunteur::hash_password($login,str_replace(array("\n","\r"), "", $mdp));
                        $cpt_maj ++;
                    }
                    if($prof_principal == 'prof_principal') {
	                    // On recupère le nom du prof principal :
	            		list ($Mr, $MrNom, $MrPrenom) = explode(' ', $tab[12]);
                    } else {
                    	$MrNom = "";
                    }
                    // On récupére le code-barres de l'eleve :
                    $selects = pmb_mysql_fetch_array($select);
                    $eleve_cb = $selects["empr_cb"];
                    $resu = gestion_groupe($tab[9], $eleve_cb, $dbh,$MrNom);
                    if($prof_principal == 'prof_principal') {
	                    switch ($resu) {
	                    	case 0:
	                    		// Prof absent :
	                    		$profAbsent[$MrNom]++;
	                    		break;
	                    	case 1 :
	                    		// Prof en doublon :
	                    		$profDoublon[$MrNom]++;
	                    		break;
	                    	default :
	                    		// Pas de problème.
	                    		break;
	                    }
                    }
                    $j++;
                    break;
                case 2:
                    break;
                default:
                    print("<b>&Eacute;chec pour l'&eacute;l&egrave;ve suivant (Erreur : ".pmb_mysql_error().") : </b><br />");
                    for ($i=0;$i<3;$i++) {
                        print($eleve_abrege[$i]." : ".$tab[$i].", ");
                    }
                    print("<br />");
                    break;
            }
        } // while

		if($prof_principal == 'prof_principal') {
		    // A t-on deja écrit un warning?
			$warningAbs = 0;
			$warningDoublon = 0;

			foreach ($profAbsent as $clef => $valeur) {
	            if($warningAbs == 0) print "<br />Les responsables trouv&eacute;s dans le fichier n'existent pas tous.<br />Veuillez ins&eacute;rer le(les) lecteur(s) manquant(s) si vous d&eacute;sirez le(s) nommer comme profresseur(s) principal(aux).<br />";
	            print "- Nom : ".$clef." Nombre d'apparitions  :<B> $valeur</B>.<br />";
	            $warningAbs = 1;
	        }
			if($warningAbs == 1) print "___________________<br />";
			foreach ($profDoublon as $clef => $valeur) {
	           	if($warningDoublon == 0) print "<br />Certains responsables trouv&eacute;s dans le fichier existent en doublon.<br />Veuillez v&eacute;rifier la validit&eacute; du(des) professeur(s) principal(aux) dans la gestion des groupes.<br />";
	           	print "- Nom : ".$clef." Nombre d'apparitions  :<B> $valeur</B>.<br />";
	           	$warningDoublon = 1;
	        }
        }


        //Affichage des insert et update
        print("<br />_____________________<br />");
        if ($cpt_insert) print($cpt_insert." &Eacute;l&egrave;ves cr&eacute;&eacute;s. <br />");
        if ($cpt_maj) print($cpt_maj." &Eacute;l&egrave;ves modifi&eacute;s. <br />");
        fclose($fichier);
    }

}


function gestion_groupe($lib_groupe, $empr_cb, $dbh,$ProfPrincipal = "") {
    $sel = pmb_mysql_query("SELECT id_groupe from groupe WHERE libelle_groupe = \"".$lib_groupe."\"",$dbh);
    $nb_enreg_grpe = pmb_mysql_num_rows($sel);

    if (!$nb_enreg_grpe) {
		//insertion dans la table groupe
		pmb_mysql_query("INSERT INTO groupe(libelle_groupe) VALUES(\"".$lib_groupe."\")");
		$groupe=pmb_mysql_insert_id();
    } else {
    	$grpobj = pmb_mysql_fetch_object($sel) ;
    	$groupe = $grpobj->id_groupe ;
    }

	//insertion dans la table empr_groupe
    $sel_empr = pmb_mysql_query("SELECT id_empr FROM empr WHERE empr_cb = \"".$empr_cb."\"",$dbh);
    $empr = pmb_mysql_fetch_array($sel_empr);
    @pmb_mysql_query("INSERT INTO empr_groupe(empr_id, groupe_id) VALUES ('$empr[id_empr]','$groupe')",$dbh);
	if ($ProfPrincipal != "") {
		// On recherche l'identifiant du responsable,
		// si il y a plusieur résultat on prend le premier réponsable.
		$resps = pmb_mysql_query("SELECT id_empr FROM empr, empr_groupe, groupe WHERE empr_nom like '".$ProfPrincipal."' and empr_groupe.empr_id = empr.id_empr and empr_groupe.groupe_id = id_groupe and libelle_groupe like 'Professeurs'",$dbh);
		$nb_enreg = pmb_mysql_num_rows($resps);
	    if (!$nb_enreg) {
	       	return 0;
	    } else {
	    	$resp = pmb_mysql_fetch_array($resps);
	    	$resp_id = $resp['id_empr'];
	    	pmb_mysql_query("UPDATE groupe SET resp_groupe = ".$resp_id." where id_groupe = ".$groupe,$dbh);
	    	if ($nb_enreg > 1) {
	       		return 1;
	    	} else {
	    		return 2;
	    	}
	    }
	}
}

function import_profs($separateur, $dbh, $type_import, $mdp_auto, $num_auto, $adr_mail){
    //La structure du fichier texte doit être la suivante :
    //[numéro],nom, prénom, adr1, adr2, code postal, commune, tel, année de naissance, sexe, e-mail,[login,mdp]
    $prof = array("Num&eacute;ro auto","Nom","Pr&eacute;nom");
    $date_auj = date("Y-m-d", time());
    $date_an_proch = date("Y-m-d", time()+3600*24*30.42*12);

    //Upload du fichier
    if (!($_FILES['import_lec']['tmp_name']))
        print "Cliquez sur Pr&eacute;c&eacute;dent et choisissez un fichier";
    elseif (!(move_uploaded_file($_FILES['import_lec']['tmp_name'], "./temp/".basename($_FILES['import_lec']['tmp_name'])))) {
        print "Le fichier n'a pas pu &ecirc;tre t&eacute;l&eacute;charg&eacute;. Voici plus d'informations :<br />";
        print_r($_FILES)."<p>";
    }
    $fichier = @fopen( "./temp/".basename($_FILES['import_lec']['tmp_name']), "r" );

    if ($fichier) {
        if ($type_import == 'maj_complete') {
            //Vide la table empr_groupe
            //$delete_empr_groupe = pmb_mysql_query("DELETE FROM empr_groupe",$dbh);
            //echo $type_import;
            //Supprime les profs qui n'ont pas de prêts en cours
            $req_select_verif_pret = "SELECT id_empr, empr_cb FROM groupe, empr_groupe, empr left join pret on id_empr=pret_idempr WHERE pret_idempr is null and empr_groupe.empr_id = empr.id_empr and empr_groupe.groupe_id = id_groupe and libelle_groupe like 'Professeurs'";
            $select_verif_pret = pmb_mysql_query($req_select_verif_pret,$dbh);
            while (($verif_pret = pmb_mysql_fetch_array($select_verif_pret))) {
            	//pour tous les emprunteurs qui n'ont pas de pret en cours
                emprunteur::del_empr($verif_pret["id_empr"]);
            }
        }

		//Récupération dans la table 'empr' du 'empr_cb' maximum
		$req=pmb_mysql_query("SELECT MAX(empr_cb) AS cbmax FROM empr WHERE empr_categ=2 and empr_codestat=1",$dbh);
		$cb=pmb_mysql_result($req,0,"cbmax");
		if (!$cb) {
		    $numeroP="0000";
		}
		else {
			$numeroP= substr($cb,1,4);
		}

        while (!feof($fichier)) {
            $buffer = fgets($fichier, 4096);
            $buffer = pmb_mysql_escape_string($buffer);
            $tab = explode($separateur, $buffer);
            if($num_auto != 'num_auto') {
	            $buf_prenom = explode("\\",$tab[1]);
	            $prenom = $buf_prenom[0];
            } else {
            	$buf_prenom = explode("\\",$tab[2]);
	            $prenom = $buf_prenom[1];
            }
            // Traitement du prof
            $select = pmb_mysql_query("SELECT id_empr, empr_cb FROM empr WHERE empr_nom = '".$tab[0]."' AND empr_prenom = '".$prenom."'",$dbh);
            $nb_enreg = pmb_mysql_num_rows($select);
            if (!$tab[0] || $tab[0] == "") {
                if ($tab[1] != "") {
	                print("<b> Professeur non pris en compte car \"Nom\" non renseign&eacute; : </b><br />");
	                for ($i=1;$i<3;$i++) {
	                    print($prof[$i]." : ".$tab[$i-1].", ");
	                }
	                print("<br />");
                }
                $nb_enreg = 2;
            }
            if($num_auto == 'num_auto') {
            	// Si il y a un numéro en debut de fichier,
            	// on decale les indices du tab à la baisse :
            	$prof_cb = $tab[0];
            	$tab[0] = $tab[1];
            	$tab[1] = $tab[2];
            	$tab[2] = $tab[3];
            	$tab[3] = $tab[4];
            	$tab[4] = $tab[5];
            	$tab[5] = $tab[6];
            	$tab[6] = $tab[7];
            	$tab[7] = $tab[8];
            	$tab[8] = $tab[9];
            	if ($adr_mail == 'adr_mail') {
					$tab[9] = $tab[10];
            		$tab[10] = $tab[11];
            		$tab[11] = $tab[12];
            	} else {
            		$tab[9] = "";
            	}
            } else {
            	//Génération du code-barre
				$numeroP=$numeroP+1;
				if ($numeroP < 10) {
				    $prof_cb = "P000".$numeroP;
				}
				elseif ($numeroP < 100) {
					$prof_cb = "P00".$numeroP;
				}
				elseif ($numeroP < 1000) {
					$prof_cb = "P0".$numeroP;
				}
				elseif ($numeroP < 10000) {
					$prof_cb = "P".$numeroP;
				}
            }
			// On verifie que le mail est bien de la forme chaine@chaine :
            if ($adr_mail == 'adr_mail') {
	            if(preg_match("#.*@.*#",$tab[9]) == false){
					$tab[9] = "";
				}
            }
			//Gestion du sexe
            switch ($tab[8]{0}) {
                case 'M':
                    $sexe = 1;
                    break;
                case 'F':
                    $sexe = 2;
                    break;
                default:
                    $sexe = 0;
                    break;
            }
            //Génération du login
            if ($mdp_auto != 'mdp_auto') {
        		$login = cre_login($tab[0],$prenom, $dbh);
        		$mdp = $tab[7];
            } else {
            	$login = $tab[10];
            	$mdp = $tab[11];
            }
            if (!$mdp || $mdp == "") $mdp = $login;
            switch ($nb_enreg) {
                case 0:
                	//Ce prof n'est pas enregistré
                    $req_insert = "INSERT INTO empr(empr_cb, empr_nom, empr_prenom, empr_adr1, empr_adr2, empr_cp, empr_ville, ";
                    $req_insert .= "empr_mail, empr_tel1, empr_year, empr_categ, empr_codestat, empr_creation, empr_sexe,  ";
                    $req_insert .= "empr_login, empr_password, empr_date_adhesion, empr_date_expiration) ";
                    $req_insert .= "VALUES ('$prof_cb','$tab[0]','$tab[1]', '$tab[2]', '$tab[3]', '$tab[4]', '$tab[5]', '$tab[9]', '$tab[6]', '$tab[7]', ";
                    $req_insert .= "2, 1, '$date_auj', $sexe, '$login', replace(replace('".$mdp."','\n',''),'\r',''), '$date_auj', '$date_an_proch' )";
                    $insert = pmb_mysql_query($req_insert,$dbh);
                    if (!$insert) {
                        print("<b>&Eacute;chec de la cr&eacute;ation du professeur suivant (Erreur : ".pmb_mysql_error().") : </b><br />");
                        for ($i=1;$i<3;$i++) {
                            print($prof[$i]." : ".$tab[$i-1].", ");
                        }
                        print("<br />");
                    }
                    else {
                    	emprunteur::update_digest($login,str_replace(array("\n","\r"), "", $mdp));
                    	emprunteur::hash_password($login,str_replace(array("\n","\r"), "", $mdp));
                        $cpt_insert ++;
                    }
                    $j++;
                    gestion_groupe("Professeurs", $prof_cb, $dbh);
                    break;

                case 1:
                   	//Ce prof est déja enregistré
                	$empr_cbs = pmb_mysql_fetch_array($select);
    				$prof_cb = $empr_cbs['empr_cb'];
                    $req_update = "UPDATE empr SET empr_nom = '$tab[0]', empr_prenom = '$tab[1]', empr_adr1 = '$tab[2]', ";
                    $req_update .= "empr_adr2 = '$tab[3]', empr_cp = '$tab[4]', empr_ville = '$tab[5]', empr_mail = '$tab[9]', ";
                    $req_update .= "empr_tel1 = '$tab[6]', empr_year = '$tab[7]', empr_categ = '2', empr_codestat = '1', empr_modif = '$date_auj', empr_sexe = '$sexe', ";
                    $req_update .= "empr_login = '$login', empr_password=replace(replace('".$mdp."','\n',''),'\r',''), ";
                    $req_update .= "empr_date_adhesion = '$date_auj', empr_date_expiration = '$date_an_proch' ";
                    $req_update .= "WHERE empr_nom = '$tb[0]' AND empr_prenom = '$prenom'";
                    $update = pmb_mysql_query($req_update, $dbh);
                    if (!$update) {
                        print("<b>&Eacute;chec de la modification du professeur suivant (Erreur : ".pmb_mysql_error().") : </b><br />");
                        for ($i=1;$i<3;$i++) {
                            print($prof[$i]." : ".$tab[$i-1].", ");
                        }
                        print("<br />");
                    }
                    else {
                    	emprunteur::update_digest($login,str_replace(array("\n","\r"), "", $mdp));
                    	emprunteur::hash_password($login,str_replace(array("\n","\r"), "", $mdp));
                        $cpt_maj ++;
                    }
                    $j++;
                    break;
                case 2:
                    break;
                default:
                    print("<b>&Eacute;chec pour le professeur suivant (Erreur : ".pmb_mysql_error().") : </b><br />");
                    for ($i=0;$i<3;$i++) {
                        print($prof[$i]." : ".$tab[$i].", ");
                    }
                    print("<br />");
                    break;
            }
        	//gestion_groupe("Professeurs", $prof_cb, $dbh);
        }

        //Affichage des insert et update
        print("<br />_____________________<br />");
        if ($cpt_insert) print($cpt_insert." Professeurs cr&eacute;&eacute;s. <br />");
        if ($cpt_maj) print($cpt_maj." Professeurs modifi&eacute;s. <br />");
        fclose($fichier);
    }

}



switch($action) {
    case 1:
        if ($imp_elv){
            import_eleves($Sep_Champs, $dbh, $type_import, $mdp_auto, $num_auto,$prof_principal,$adr_mail);
        }
        elseif ($imp_prof) {
            import_profs($Sep_Champs, $dbh, $type_import, $mdp_auto, $num_auto, $adr_mail);
        }
        else {
            show_import_choix_fichier($dbh);
        }
        break;
    case 2:
        break;
    default:
        show_import_choix_fichier($dbh);
        break;
}

?>



