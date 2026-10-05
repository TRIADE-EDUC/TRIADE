<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: import_bretagne_2.inc.php,v 1.17.6.1.2.1 2025/05/20 06:43:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
global $action, $imp_elv, $imp_prof, $Sep_Champs, $type_import;

require_once $class_path."/emprunteur.class.php";
require_once $class_path."/import/import_empr.class.php";

function show_import_choix_fichier() {
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
        <input type=radio name='type_import' value='nouveau_lect' checked>
        <label class='etiquette' for='form_import_lec'>Nouveaux lecteurs</label>
        (ajoute ou modifie les lecteurs présents dans le fichier)
        <br />
        <input type=radio name='type_import' value='maj_complete'>
        <label class='etiquette' for='form_import_lec'>Mise à jour complète</label>
        (supprime les lecteurs non présents dans le fichier et qui n'ont pas de prêt en cours)
    </div>
    <div class='row'></div>

	</div>
<div class='row'>
	<input name='imp_elv' type='submit' class='bouton' value='Import des élèves'/>
	<input name='imp_prof' value='Import des professeurs' type='submit' class='bouton'/>
</div>
</form>";
}

function import_eleves($separateur, $type_import){

    //La structure du fichier texte doit être la suivante :
    //Numéro identifiant/Nom/Prénom/Rue/Complément de rue/Code postal/Commune/Téléphone/Date de naissance/Classe/Sexe

    global $lang;
    $cpt_insert = 0;
    $cpt_maj = 0;

    $eleve_abrege = array("Numéro identifiant","Nom","Prénom");
    $date_auj = date("Y-m-d", time());
    $date_an_proch = date("Y-m-d", time()+3600*24*30.42*12);

    //Upload du fichier
    if (!($_FILES['import_lec']['tmp_name'])) {
        print "Cliquez sur Pr&eacute;c&eacute;dent et choisissez un fichier";
    } elseif (!(move_uploaded_file($_FILES['import_lec']['tmp_name'], "./temp/" . basename($_FILES['import_lec']['tmp_name'])))) {
        print "Le fichier n'a pas pu être téléchargé. Voici plus d'informations :<br />";
        print_r($_FILES)."<p>";
    }
    $fichier = @fopen( "./temp/".basename($_FILES['import_lec']['tmp_name']), "r" );

    if ($fichier) {

        if ($type_import == 'maj_complete') {
            //Vide la table empr_groupe
            pmb_mysql_query("DELETE FROM empr_groupe");
            //Supprime les élèves qui n'ont pas de prêts en cours
            $req_select_verif_pret = "SELECT id_empr FROM empr left join pret on id_empr=pret_idempr WHERE pret_idempr is null and empr_cb NOT LIKE 'E%'";
            $select_verif_pret = pmb_mysql_query($req_select_verif_pret);
            while (($verif_pret = pmb_mysql_fetch_array($select_verif_pret))) {
            	//pour tous les emprunteurs qui n'ont pas de pret en cours
                emprunteur::del_empr($verif_pret["id_empr"]);
            }
		}

        //Récupération dans la table 'empr' du 'empr_cb' maximum
		$req=pmb_mysql_query("SELECT MAX(empr_cb) AS cbmax FROM empr WHERE empr_categ=1 and empr_codestat=1");
		$cb=pmb_mysql_result($req,0,"cbmax");
		if (!$cb) {
		    $numeroE="0000";
		} else {
		  $numeroE= substr($cb,1,4);
		}

        while (!feof($fichier)) {
            $buffer = fgets($fichier, 4096);
            if (empty($buffer)) {
                continue;
            }
            $buffer = import_empr::get_encoded_buffer($buffer);
            $buffer = pmb_mysql_escape_string($buffer);
            $tab = explode($separateur, $buffer);

			//Génération du code-barre

			$numeroE = intval($numeroE)+1;
			if ($numeroE < 10) {
			    $eleve_cb = "E000".$numeroE;
			} elseif ($numeroE < 100) {
				$eleve_cb = "E00".$numeroE;
			} elseif ($numeroE < 1000) {
				$eleve_cb = "E0".$numeroE;
			} elseif ($numeroE < 10000) {
				$eleve_cb = "E".$numeroE;
			}

            //Gestion du sexe
            switch ($tab[9][0]) {
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

            // Traitement de l'élève
            $id_empr = 0;
            $select = pmb_mysql_query("SELECT id_empr FROM empr WHERE empr_nom = '".$tab[0]."' AND empr_prenom= '".$tab[1]."' AND empr_year='".$tab[7]."'");
            $nb_enreg = pmb_mysql_num_rows($select);

            //Test si un numéro id est fourni
            if (!$tab[0] || $tab[0] == "") {
                print("<b> Elève non pris en compte car \"Nom\" non renseigné : </b><br />");
                for ($i=0;$i<3;$i++) {
                    print($eleve_abrege[$i]." : ".$tab[$i].", ");
                }
                print("<br />");
                $nb_enreg = 2;
            }
            if($nb_enreg == 1) {
                $row = pmb_mysql_fetch_assoc($select);
                $id_empr = $row['id_empr'];
            }

            $login = import_empr::cre_login($tab[0],$tab[1]);

            switch ($nb_enreg) {

                case 0:
                	//Cet élève n'est pas enregistré
                    $req_insert = "INSERT INTO empr(empr_cb, empr_nom, empr_prenom, empr_adr1, empr_adr2, empr_cp, empr_ville, empr_mail,";
                    $req_insert .= "empr_tel1, empr_year, empr_categ, empr_codestat, empr_creation, empr_sexe,  ";
                    $req_insert .= "empr_login, empr_password, empr_date_adhesion, empr_date_expiration) ";
                    $req_insert .= "VALUES ('$eleve_cb','$tab[0]','$tab[1]','$tab[2]', '$tab[3]', '$tab[4]', ";
                    $req_insert .= "'$tab[5]', '$tab[10]', '$tab[6]', '$tab[7]', 1, 1, '$date_auj', '$sexe', ";
                    $req_insert .= "'$login', replace(replace('".$tab[7]."','\n',''),'\r',''), '$date_auj', '$date_an_proch')";
                    $insert = pmb_mysql_query($req_insert);

                    if (!$insert) {

                        print("<b>Echec de la création de l'élève suivant (Erreur : ".pmb_mysql_error().") : </b><br />");
                        for ($i=1;$i<3;$i++) {
                            print($eleve_abrege[$i]." : ".$tab[$i-1].", ");
                        }
                        print("<br />");

                    } else {

                        $id_empr = pmb_mysql_insert_id();
                        $empr_password = str_replace(array("\\n","\\r","\n","\r"), "", $tab[7]);

                        //Chiffrement du mot de passe
                        //On verifie que le mot de passe lecteur correspond aux regles de saisie definies
                        //Si non, encodage dans l'ancien format
                        $old_hash = false;
                        $check_password_rules = emprunteur::check_password_rules((int) $id_empr, $empr_password, [], $lang);
                        if( !$check_password_rules['result'] ) {
                            $old_hash = true;
                        }
                        emprunteur::update_digest($login, $empr_password);
                        emprunteur::hash_password($login, $empr_password, $old_hash);

                        $cpt_insert ++;
                    }
					//Inscription dans une classe
                    import_empr::gestion_groupe($tab[8], $eleve_cb);
                    break;

                case 1:
                	//Cet élève est déja enregistré
                    $req_update = "UPDATE empr SET empr_nom = '$tab[0]', empr_prenom = '$tab[1]', empr_adr1 = '$tab[2]', ";
                    $req_update .= "empr_adr2 = '$tab[3]', empr_cp = '$tab[4]', empr_ville = '$tab[5]', empr_mail = '$tab[10]', ";
                    $req_update .= "empr_tel1 = '$tab[6]', empr_year = '$tab[7]', empr_categ = '1', empr_codestat = '1', empr_modif = '$date_auj', empr_sexe = '$sexe', ";
                    $req_update .= "empr_login = '$login', empr_password= replace(replace('".$tab[7]."','\n',''),'\r',''), ";
                    $req_update .= "empr_date_adhesion = '$date_auj', empr_date_expiration = '$date_an_proch' ";
                    $req_update .= "WHERE empr_nom = '$tab[0]' AND empr_prenom = '$tab[1]'";
                    $update = pmb_mysql_query($req_update);

                    if (!$update) {

                        print("<b>Echec de la modification de l'élève suivant (Erreur : ".pmb_mysql_error().") : </b><br />");
                        for ($i=1;$i<3;$i++) {
                            print($eleve_abrege[$i]." : ".$tab[$i-1].", ");
                        }
                        print("<br />");

                    } else {

                        $empr_password = str_replace(array("\\n","\\r","\n","\r"), "", $tab[7]);

                        //Chiffrement du mot de passe
                        //On verifie que le mot de passe lecteur correspond aux regles de saisie definies
                        //Si non, encodage dans l'ancien format
                        $old_hash = false;
                        $check_password_rules = emprunteur::check_password_rules((int) $id_empr, $empr_password, [], $lang);
                        if( !$check_password_rules['result'] ) {
                            $old_hash = true;
                        }

                        emprunteur::update_digest($login, $empr_password);
                        emprunteur::hash_password($login, $empr_password, $old_hash);

                        $cpt_maj ++;
                    }
					//Inscription dans une classe
                    import_empr::gestion_groupe($tab[8], $eleve_cb);
                    break;

                case 2:
                    break;

                default:
                    print("<b>Echec pour l'élève suivant (Erreur : ".pmb_mysql_error().") : </b><br />");
                    for ($i=1;$i<3;$i++) {
                        print($eleve_abrege[$i]." : ".$tab[$i-1].", ");
                    }
                    print("<br />");
                    break;
            }
        }

        //Affichage des insert et update
        print("<br />_____________________<br />");
        if ($cpt_insert) print($cpt_insert." Elèves créés. <br />");
        if ($cpt_maj) print($cpt_maj." Elèves modifiés. <br />");
        fclose($fichier);
    }

}

function import_profs($separateur, $type_import){

    //La structure du fichier texte doit être la suivante :
    //nom, prénom (le cb est généré automatiquement)

    global $lang;
    $cpt_insert = 0;
    $cpt_maj = 0;

    $prof = array("Numéro auto","Nom","Prénom");
    $date_auj = date("Y-m-d", time());
    $date_an_proch = date("Y-m-d", time()+3600*24*30.42*12);

    //Upload du fichier
    if (!($_FILES['import_lec']['tmp_name'])) {
        print "Cliquez sur Pr&eacute;c&eacute;dent et choisissez un fichier";
    } elseif (!(move_uploaded_file($_FILES['import_lec']['tmp_name'], "./temp/".basename($_FILES['import_lec']['tmp_name'])))) {
        print "Le fichier n'a pas pu être téléchargé. Voici plus d'informations :<br />";
        print_r($_FILES)."<p>";
    }
    $fichier = @fopen( "./temp/".basename($_FILES['import_lec']['tmp_name']), "r" );

    if ($fichier) {

        if ($type_import == 'maj_complete') {
            //Vide la table empr_groupe
            pmb_mysql_query("DELETE FROM empr_groupe");
             echo $type_import;
            //Supprime les profs qui n'ont pas de prêts en cours
            $req_select_verif_pret = "SELECT id_empr FROM empr left join pret on id_empr=pret_idempr WHERE pret_idempr is null and empr_cb NOT LIKE 'P%'";
            $select_verif_pret = pmb_mysql_query($req_select_verif_pret);
            while (($verif_pret = pmb_mysql_fetch_array($select_verif_pret))) {
            	//pour tous les emprunteurs qui n'ont pas de pret en cours
                emprunteur::del_empr($verif_pret["id_empr"]);
            }
        }

		//Récupération dans la table 'empr' du 'empr_cb' maximum
		$req=pmb_mysql_query("SELECT MAX(empr_cb) AS cbmax FROM empr WHERE empr_categ=2 and empr_codestat=1");
		$cb=pmb_mysql_result($req,0,"cbmax");
		if (!$cb) {
		    $numeroP="000";
		} else {
		  $numeroP= substr($cb,1,3);
		}

        while (!feof($fichier)) {
            $buffer = fgets($fichier, 4096);
            $buffer = import_empr::get_encoded_buffer($buffer);
            $buffer = pmb_mysql_escape_string($buffer);
            $tab = explode($separateur, $buffer);
            $buf_prenom = explode("\\",$tab[1]);
            $prenom = $buf_prenom[0];

            // Traitement du prof
            $id_empr = 0;
            $select = pmb_mysql_query("SELECT id_empr FROM empr WHERE empr_nom = '".$tab[0]."' AND empr_prenom = '".$prenom."'");
            $nb_enreg = pmb_mysql_num_rows($select);

            if (!$tab[0] || $tab[0] == "") {
                print("<b> Professeur non pris en compte car \"Nom\" non renseigné : </b><br />");
                for ($i=1;$i<3;$i++) {
                    print($prof[$i]." : ".$tab[$i-1].", ");
                }
                print("<br />");
                $nb_enreg = 2;
            }
            if($nb_enreg == 1) {
                $row = pmb_mysql_fetch_assoc($select);
                $id_empr = $row['id_empr'];
            }

			//Gestion du sexe
            switch ($tab[8][0]) {
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

            //Génération du code-barre

			$numeroP = intval($numeroP)+1;
			if ($numeroP < 10) {
			    $prof_cb = "P00".$numeroP;
			} elseif ($numeroP < 100) {
				$prof_cb = "P0".$numeroP;
			} elseif ($numeroP < 1000) {
				$prof_cb = "P".$numeroP;
			}


            //Génération du login
            $login = import_empr::cre_login($tab[0],$prenom);

            //Pour l'instant login = mdp car lors de l'import des profs, aucune date de naissance n'est fournie

            switch ($nb_enreg) {

                case 0:
                	//Ce prof n'est pas enregistré
                    $req_insert = "INSERT INTO empr(empr_cb, empr_nom, empr_prenom, empr_adr1, empr_adr2, empr_cp, empr_ville, ";
                    $req_insert .= "empr_mail, empr_tel1, empr_year, empr_categ, empr_codestat, empr_creation, empr_sexe,  ";
                    $req_insert .= "empr_login, empr_password, empr_date_adhesion, empr_date_expiration) ";
                    $req_insert .= "VALUES ('$prof_cb','$tab[0]','$prenom', '$tab[2]', '$tab[3]', '$tab[4]', '$tab[5]', '$tab[9]', '$tab[6]', '$tab[7]', ";
                    $req_insert .= "2, 1, '$date_auj', $sexe, '$login', replace(replace('".$tab[7]."','\n',''),'\r',''), '$date_auj', '$date_an_proch' )";
                    $insert = pmb_mysql_query($req_insert);

                    if (!$insert) {

                        print("<b>Echec de la création du professeur suivant (Erreur : ".pmb_mysql_error().") : </b><br />");
                        for ($i=1;$i<3;$i++) {
                            print($prof[$i]." : ".$tab[$i-1].", ");
                        }
                        print("<br />");

                    } else {

                        $id_empr = pmb_mysql_insert_id();
                        $empr_password = str_replace(array("\\n","\\r","\n","\r"), "", $tab[7]);

                        //Chiffrement du mot de passe
                        //On verifie que le mot de passe lecteur correspond aux regles de saisie definies
                        //Si non, encodage dans l'ancien format
                        $old_hash = false;
                        $check_password_rules = emprunteur::check_password_rules((int) $id_empr, $empr_password, [], $lang);
                        if( !$check_password_rules['result'] ) {
                            $old_hash = true;
                        }
                        emprunteur::update_digest($login, $empr_password);
                        emprunteur::hash_password($login, $empr_password, $old_hash);

                        $cpt_insert ++;
                    }
                    break;

                case 1:
                	//Ce prof est déja enregistré
                    $req_update = "UPDATE empr SET empr_nom = '$tab[0]', empr_prenom = '$tab[1]', empr_adr1 = '$tab[2]', ";
                    $req_update .= "empr_adr2 = '$tab[3]', empr_cp = '$tab[4]', empr_ville = '$tab[5]', empr_mail = '$tab[9]', ";
                    $req_update .= "empr_tel1 = '$tab[6]', empr_year = '$tab[7]', empr_categ = '2', empr_codestat = '1', empr_modif = '$date_auj', empr_sexe = '$sexe', ";
                    $req_update .= "empr_login = '$login', empr_password= replace(replace('".$tab[7]."','\n',''),'\r',''), ";
                    $req_update .= "empr_date_adhesion = '$date_auj', empr_date_expiration = '$date_an_proch' ";
                    $req_update .= "WHERE empr_nom = '$tab[0]' AND empr_prenom = '$prenom'";
                    $update = pmb_mysql_query($req_update);

                    if (!$update) {

                        print("<b>Echec de la modification du professeur suivant (Erreur : ".pmb_mysql_error().") : </b><br />");
                        for ($i=1;$i<3;$i++) {
                            print($prof[$i]." : ".$tab[$i-1].", ");
                        }
                        print("<br />");

                    } else {

                        $empr_password = str_replace(array("\\n","\\r","\n","\r"), "", $tab[7]);

                        //Chiffrement du mot de passe
                        //On verifie que le mot de passe lecteur correspond aux regles de saisie definies
                        //Si non, encodage dans l'ancien format
                        $old_hash = false;
                        $check_password_rules = emprunteur::check_password_rules((int) $id_empr, $empr_password, [], $lang);
                        if( !$check_password_rules['result'] ) {
                            $old_hash = true;
                        }

                        emprunteur::update_digest($login, $empr_password);
                        emprunteur::hash_password($login, $empr_password, $old_hash);

                        $cpt_maj ++;
                    }
                    break;

                case 2:
                    break;

                default:
                    print("<b>Echec pour le professeur suivant (Erreur : ".pmb_mysql_error().") : </b><br />");
                    for ($i=0;$i<3;$i++) {
                        print($prof[$i]." : ".$tab[$i].", ");
                    }
                    print("<br />");
                    break;
            }
        }


        //Affichage des insert et update
        print("<br />_____________________<br />");
        if ($cpt_insert) print($cpt_insert." Professeurs créés. <br />");
        if ($cpt_maj) print($cpt_maj." Professeurs modifiés. <br />");
        fclose($fichier);
    }

}



switch($action) {
    case 1:
        if ($imp_elv){
            import_eleves($Sep_Champs, $type_import);
        }
        elseif ($imp_prof) {
            import_profs($Sep_Champs, $type_import);
        }
        else {
            show_import_choix_fichier();
        }
        break;
    case 2:
        break;
    default:
        show_import_choix_fichier();
        break;
}
