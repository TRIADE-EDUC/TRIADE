<?php
session_start();
//echo"<html>";

// On commence par vérifier si les champs sont vides 
///                        if(empty($nom_agent) OR empty($prenom_agent) OR empty($login) OR empty($pwd))
                            { 
                            //echo "<font color=red>Attention le champs ne peut rester vide !</font>"; 
                            } 

                        // Aucun champ n'est vide, on peut enregistrer dans la table 
//                        else      
//                            { 
			    include ("./librairie_php/fonction.inc.php");
			    $id=db_connect() or die ("<br>Acces a la base de donne cagt impossible");
error_reporting(0);
$nom = trim($_POST['saisie_nom']);
$prenom = trim($_POST['saisie_prenom']);
$classe = trim($_POST['saisie_classe']);
$lv1 = trim($_POST['saisie_lv1']);
$lv2 = trim($_POST['saisie_lv2']);
$regime = trim($_POST['saisie_regime']);
$annee_naissance = trim($_POST['saisie_annee_naissance']);
$mois_naissance = trim($_POST['saisie_mois_naissance']);
$jour_naissance = trim($_POST['saisie_jour_naissance']);
$lieu_naissance = trim($_POST['saisie_lieu_naissance']);
$nationalite = trim($_POST['saisie_nationalite']);
$passwd = trim($_POST['saisie_passwd']);
$passwd_eleve = trim($_POST['saisie_passwd_eleve']);
$civ_1 = trim($_POST['saisie_civ_1']);
$nomtuteur = trim($_POST['saisie_nomtuteur']);
$prenomtuteur = trim($_POST['saisie_prenomtuteur']);
$adr1 = trim($_POST['saisie_adr1']);
$code_post_adr1 = trim($_POST['saisie_code_post_adr1']);
$commune_adr1 = trim($_POST['saisie_commune_adr1']);
$tel_port_1 = trim($_POST['saisie_tel_port_1']);
$civ_2 = trim($_POST['saisie_civ_2']);
$nom_resp_2 = trim($_POST['saisie_nom_resp_2']);
$prenom_resp_2 = trim($_POST['saisie_prenom_resp_2']);
$adr2 = trim($_POST['saisie_adr2']);
$code_post_adr2 = trim($_POST['saisie_code_post_adr2']);
$commune_adr2 = trim($_POST['saisie_commune_adr2']);
$tel_port_2 = trim($_POST['saisie_tel_port_2']);
$telephone = trim($_POST['saisie_telephone']);
$profession_pere = trim($_POST['saisie_profession_pere']);
$tel_prof_pere = trim($_POST['saisie_tel_prof_pere']);
$profession_mere = trim($_POST['saisie_profession_mere']);
$tel_prof_mere = trim($_POST['saisie_tel_prof_mere']);
$nom_etablissement = trim($_POST['saisie_nom_etablissement']);
$numero_etablissement = trim($_POST['saisie_numero_etablissement']);
$code_postal_etablissement = trim($_POST['saisie_code_postal_etablissement']);
$commune_etablissement = trim($_POST['saisie_commune_etablissement']);
$numero_eleve = trim($_POST['saisie_numero_eleve']);
//$photo = trim($_POST['saisie_photo']);
$email = trim($_POST['saisie_email']);
$email_eleve = trim($_POST['saisie_email_eleve']);
$email_resp_2 = trim($_POST['saisie_email_resp_2']);
$class_ant = trim($_POST['saisie_classe_ant']);
$annee_ant = trim($_POST['saisie_date_ant']);
$valid_forward_mail_eleve = trim($_POST['saisie_valid_forward_mail_eleve']);
$valid_forward_mail_parent = trim($_POST['saisie_valid_forward_mail_parent']);
$tel_eleve = trim($_POST['saisie_tel_eleve']);
$sexe = trim($_POST['saisie_sexe']);
$option2 = trim($_POST['saisie_option2']);
$elev_id = trim($_POST['elev_id']);

$_SESSION['email_login'] = $email_eleve;
$_SESSION['passwd_eleve'] = $passwd_eleve;

if ($classe == 'G1')
 $classe = 'Graduat 1ère année';
if ($classe == 'G2')
 $classe = 'Graduat 2ème année';
if ($classe == 'G3')
 $classe = 'Graduat 3ème année';
if ($classe == 'L1')
 $classe = 'Licence 1ère année';
if ($classe == 'L2')
 $classe = 'Licence 2ème année';		
// on ecris la requete sql 


$sql = "UPDATE preinscription_eleves SET nom = '$nom', prenom = '$prenom', classe = '$classe', lv1 = '$lv1', lv2 = '$lv2', regime = '$regime', date_naissance = '$annee_naissance-$mois_naissance-$jour_naissance', lieu_naissance = '$lieu_naissance', nationalite = '$nationalite', passwd = '$passwd', passwd_eleve = '$passwd_eleve', civ_1 = '$civ_1', nomtuteur = '$nomtuteur', prenomtuteur = '$prenomtuteur', adr1 = '$adr1', code_post_adr1 = '$code_post_adr1', commune_adr1 = '$commune_adr1', tel_port_1 = '$tel_port_1', civ_2 = '$civ_2', nom_resp_2 = '$nom_resp_2', prenom_resp_2 = '$prenom_resp_2', adr2 = '$adr2', code_post_adr2 = '$code_post_adr2', commune_adr2 = '$commune_adr2', tel_port_2 = '$tel_port_2', telephone = '$telephone', profession_pere = '$profession_pere', tel_prof_pere = '$tel_prof_pere', profession_mere = '$profession_mere', tel_prof_mere = '$tel_prof_mere', nom_etablissement = '$nom_etablissement', numero_etablissement = '$numero_etablissement', code_postal_etablissement = '$code_postal_etablissement', commune_etablissement = '$commune_etablissement', numero_eleve = '$numero_eleve', photo = '$photo', email = '$email', email_eleve = '$email_eleve', email_resp_2 = '$email_resp_2', class_ant = '$class_ant', annee_ant = '$annee_ant', tel_eleve = '$tel_eleve', sexe = '$sexe', option2 = '$option2' WHERE elev_id= '$elev_id';";
                             
                            // on insère les informations du formulaire dans la table 
                            mysql_query($sql) or die('Erreur SQL !'.$sql.'<br>'.mysql_error()); 

                            // on affiche le résultat pour le visiteur 
                            echo "Vos infos ont étés modifiées $annee_naissance-$mois_naissance-$jour_naissance"; 
			    
                            mysql_close();  // on ferme la connexion 
                         //   header('location: http://iroise/shtml/liste_agent.php');
//			    }  
//}
//else
//{
//echo"Vous devez ouvrir une session pour accéder à cette page";
//}
                        
?>  

<script type="text/javascript">
//######### click droit ###########//

if (document.all) {        document.onmousedown=clicie;}
if (document.layers) {document.captureEvents(Event.MOUSEDOWN); document.onmousedown = clicns;}

//################################//
function ouvert() {
        location.href="affiche_preinscription.php";
}

</script>
</head>
<body  background="./image/attente.jpg" OnLoad="ouvert();">
</body>
</html>