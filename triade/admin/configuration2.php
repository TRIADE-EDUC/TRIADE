<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET -
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
error_reporting(0);
//include_once("./librairie_php/lib_licence.php");
include_once("../common/config.inc.php");
$repecole = REPECOLE;
$repadmin = REPADMIN;

include_once("./librairie_php/edit_fichier.php");
$forward=$_POST["forward"];
$timezone=$_POST["timezone"];
$minute=$_POST["minute"];
$messdefil=$_POST["messdefil"];
$pubhaut=$_POST["pubhaut"];
$uploadimg=$_POST["uploadimg"];
$grpmailparent=$_POST["grpmailparent"];
$mailcontact=$_POST["mailcontact"];
$meteovalide=$_POST["meteo"];
$meteoregion=$_POST["meteoregion"];
$lan=$_POST["lan"];
$proxy=$_POST["proxy"];
$dstprof=$_POST["dstprof"];
$calprof=$_POST["calprof"];
$ferie=$_POST["ferie"];
if (trim($ferie) != "") {
	$ferie="'";
	$ferie.=$_POST["ferie"];
	$ferie=preg_replace('/,/',"','",$ferie);
	$ferie.="'";
}

$cnilprotecteur=$_POST["cnilprotecteur"];
$forum=$_POST["forum"];
$noteusa=$_POST["noteusa"];
$mailreply=$_POST["mailreply"];
$trace=$_POST["trace"];
$resvprof=$_POST["resvprof"];
$retenuprof=$_POST["retenuprof"];
$graph=$_POST["graph"];
$absprof=$_POST["absprof"];
$noteprof=$_POST["noteprof"];
$mailadmin=$_POST["mailadmin"];
$mailadmin2=$_POST["mailadmin2"];
$pwd=$_POST["pwd"];
$audio=$_POST["audio"];
$fete=$_POST["fete"];
$infomedic=$_POST["infomedic"];
$infomedic2=$_POST["infomedic2"];
$accesstockage=$_POST["accesstockage"];
$accesstockageparent=$_POST["accesstockageparent"];
$accesstockageeleve=$_POST["accesstockageeleve"];
$accesstockageprof=$_POST["accesstockageprof"];
$accesstockagecpe=$_POST["accesstockagecpe"];
$taillestockage=$_POST["taillestockage"];
$inodestockage=$_POST["inodestockage"];
$securite=$_POST["securite"];
$tailleupload=$_POST["tailleupload"];
$charset=$_POST["charset"];
$mailmess=$_POST["mailmess"];
$verifpass=$_POST["verifpass"];
$supppassmail=$_POST["supppassmail"];
$mailexterne=$_POST["mailexterne"];
$mailnomreply=$_POST["mailnomreply"];
$infonoteeleve=$_POST["infonoteeleve"];
$nomdulien=preg_replace("/ /","&nbsp;",$_POST["nomdulien"]);
$adressedulien=$_POST["adressedulien"];
$nomdulien2=preg_replace("/ /","&nbsp;",$_POST["nomdulien2"]);
$adressedulien2=$_POST["adressedulien2"];
$nomdulien3=preg_replace("/ /","&nbsp;",$_POST["nomdulien3"]);
$adressedulien3=$_POST["adressedulien3"];
$nomdulien4=preg_replace("/ /","&nbsp;",$_POST["nomdulien4"]);
$adressedulien4=$_POST["adressedulien4"];
$accesmessparent=$_POST["accesmessparent"];
$accesmesseleve=$_POST["accesmesseleve"];
$accesnoteparent=$_POST["accesnoteparent"];
$accesnoteeleve=$_POST["accesnoteeleve"];
$noteexamen=$_POST["noteexamen"];
$cmpsocial=$_POST["cmpsocial"];
$aniversaire=$_POST["aniversaire"];
$dstvisuaccueil=$_POST["dstvisuaccueil"];
$accesforumparent=$_POST["accesforumparent"];
$accesforumprof=$_POST["accesforumprof"];
$accesforumeleve=$_POST["accesforumeleve"];
$pwdeleve=$_POST["pwdeleve"];
$pwdprof=$_POST["pwdprof"];
$pwdparent=$_POST["pwdparent"];
$accesmessenvoiparent=$_POST["accesmessenvoiparent"];
$accesmessenvoieleve=$_POST["accesmessenvoieleve"];
$calsamediap=$_POST["calsamediap"];
$calsamedimatin=$_POST["calsamedimatin"];
$urlsite=preg_replace('/https:\/\//','',$_POST["urlsite"]);
$urlsite=preg_replace('/http:\/\//','',$urlsite);
$cnil=$_POST["cnil"];
$trombinoEleve=$_POST["trombinoEleve"];
$trombinoParent=$_POST["trombinoParent"];
$messagedelegueparent=$_POST["messagedelegueparent"];
$messagedelegueeleve=$_POST["messagedelegueeleve"];
$calmercrediap=$_POST["calmercrediap"];
$calmercredimatin=$_POST["calmercredimatin"];
$https=$_POST["https"];
$profpbulletin=$_POST["profpbulletin"];
$profpbulletinverif=$_POST["profpbulletinverif"];
$profpreleve=$_POST["profpreleve"];
$hd_edt=$_POST["hd_edt"];
$hf_edt=$_POST["hf_edt"];
$mf_edt=$_POST["mf_edt"];
$accesabsrtdprof=$_POST["accesabsrtdprof"];
$nbcarbull=$_POST["nbcarbull"];
$absvisuprof=$_POST["absvisuprof"];
$visutriauto=$_POST["visutriauto"];
$emailchangeeleve=$_POST["emailchangeeleve"];
$agendadirect=$_POST["agendadirect"];
$groupeclasseprof=$_POST["groupeclasseprofp"];
$agentweb=$_POST["agentweb"];
$agentweb="non";
$bit=$_POST["bit"];
$eleveenvoieleve=$_POST["eleveenvoieleve"];
$eleveenvoiprof=$_POST["eleveenvoiprof"];
$eleveenvoiparent=$_POST["eleveenvoiparent"];
$eleveenvoituteur=$_POST["eleveenvoituteur"];
$eleveenvoidirec=$_POST["eleveenvoidirec"];
$eleveenvoiscolaire=$_POST["eleveenvoiscolaire"];
$eleveenvoiext=$_POST["eleveenvoiext"];
$profenvoiprof=$_POST["profenvoiprof"];
$profenvoituteur=$_POST["profenvoituteur"];
$profenvoigroupe=$_POST["profenvoigroupe"];
$profenvoiparent=$_POST["profenvoiparent"];
$profenvoieleve=$_POST["profenvoieleve"];
$profenvoiscolaire=$_POST["profenvoiscolaire"];
$profenvoidirec=$_POST["profenvoidirec"];
$profenvoiext=$_POST["profenvoiext"];
$parentenvoiprof=$_POST["parentenvoiprof"];
$parentenvoituteur=$_POST["parentenvoituteur"];
$parentenvoigroupe=$_POST["parentenvoigroupe"];
$parentenvoiparent=$_POST["parentenvoiparent"];
$parentenvoieleve=$_POST["parentenvoieleve"];
$parentenvoiscolaire=$_POST["parentenvoiscolaire"];
$parentenvoidirec=$_POST["parentenvoidirec"];
$parentenvoiext=$_POST["parentenvoiext"];
$emargement=$_POST["emargement"];
$agendapda=$_POST["agendapda"];
$passmoduleindividuel=$_POST["passmoduleindividuel"];
$passmodulemedical=$_POST["passmodulemedical"];
$eleveenvoipersonnel=$_POST["eleveenvoipersonnel"];
$profenvoipersonnel=$_POST["profenvoipersonnel"];
$parentenvoipersonnel=$_POST["parentenvoipersonnel"];
$gestionentrepriseprofp=$_POST["gestionentrepriseprofp"];
$choixmatiereprof=$_POST["choixmatiereprof"];
$carnetsuiviprof=$_POST["carnetsuiviprof"];
$tombiallclasseprof=$_POST["tombiallclasseprof"];
$intitule_direction=$_POST["intitule_direction"];
$intitule_eleve=$_POST["intitule_eleve"];
$passoublie=$_POST["passoublie"];
$banniere_dispo=$_POST["banniere_dispo"];
$dircahiertexte=$_POST["dircahiertexte"];
$profpVideoProjo=$_POST["profpVideoProjo"];
$titrebanniere=$_POST["titrebanniere"];
$examenipac=$_POST["examenipac"];
$profpaccesnote=$_POST["profpaccesnote"];
$autoINE=$_POST["autoINE"];
$examenjtc=$_POST["examenjtc"];
$profpaccesabsrtd=$_POST["profpaccesabsrtd"];
$affichageVatel=$_POST["affichageVatel"];
$modiftrombieleve=$_POST["modiftrombieleve"];
$intitule_classe=$_POST["intitule_classe"];
$intitule_enseignant=$_POST["intitule_enseignant"];


if ($_POST["intramessenger"] == "oui") {
	$intramessengerpers=$_POST["intramessengerpers"];
	$intramessengereleve=$_POST["intramessengereleve"];
	$text2 = '<?php'."\n";
	$text2.= 'define("MESSENGERPERS","'.$intramessengerpers.'");'."\n";
	$text2.= 'define("MESSENGERELEV","'.$intramessengereleve.'");'."\n";
	$text2.= '?>'."\n";
	$fp=fopen("../common/config-messenger.php","w");
	fwrite($fp,"$text2");
	fclose($fp);	
}else{
	@unlink("../common/config-messenger.php");
}

$verifsujetnote=$_POST["verifsujetnote"];


$edtvisu=$_POST["edtvisu"];

$fichier=$_FILES['fichier']['name'];
$type=$_FILES['fichier']['type'];
$tmp_name=$_FILES['fichier']['tmp_name'];
$size=$_FILES['fichier']['size'];
$erreur="";

$examenblanc=$_POST["examenblanc"];
$examends=$_POST["examends"];
$examennamur=$_POST["examennamur"];
$examenkinshasa=$_POST["examenkinshasa"];
$examenismap=$_POST["examenismap"];
$examen=$_POST["examen"];
$examencieformation=$_POST["examencieformation"];
$exameneepp=$_POST["exameneepp"];
$examenbrevetcollege=$_POST["examenbrevetcollege"];



$nbdecimalmonnaie=$_POST["nbdecimalmonnaie"];
$monnaie=$_POST["monnaie"];
$nomaniversaire=$_POST["nomaniversaire"];

$nomdulien=preg_replace('/ /','&nbsp;',$nomdulien);
$nomdulien2=preg_replace('/ /','&nbsp;',$nomdulien2);
$nomdulien3=preg_replace('/ /','&nbsp;',$nomdulien3);
$nomdulien4=preg_replace('/ /','&nbsp;',$nomdulien4);

if (!is_dir('../data/image_banniere')) { @mkdir('../data/image_banniere');  }


$profenvoigrpelev=$_POST["profenvoigrpelev"];
$profenvoidelegue=$_POST["profenvoidelegue"];
$parentenvoigrpelev=$_POST["parentenvoigrpelev"];
$parentenvoidelegue=$_POST["parentenvoidelegue"];
$eleveenvoigrpelev=$_POST["eleveenvoigrpelev"];
$eleveenvoidelegue=$_POST["eleveenvoidelegue"];	
$phpconfigmagic=$_POST["phpconfigmagic"];	
$combulltype=$_POST["combulltype"];
$nbcarbullprofp=$_POST["nbcarbullprofp"];
$semainedimanche=$_POST["semainedimanche"];
$gestmdp=$_POST["gestmdp"];
$civarmee=$_POST["civarmee"];
$timesession=$_POST["timesession"];
$timesessionprof=$_POST["timesessionprof"];
$edtdirect=$_POST["edtdirect"];
$viescolairehistocmd=$_POST["viescolairehistocmd"];


$personnelenvoiprof=$_POST["personnelenvoiprof"];
$personnelenvoigrpelev=$_POST["personnelenvoigrpelev"];
$personnelenvoiprof=$_POST["personnelenvoiprof"];
$personnelenvoiparent=$_POST["personnelenvoiparent"];
$personnelenvoiext=$_POST["personnelenvoiext"];

$planclasseParent=$_POST["planclasseParent"];

$viescolairestageetudiant=$_POST["viescolairestageetudiant"];
$viescolairestageent=$_POST["viescolairestageent"];
$viescolairestagedate=$_POST["viescolairestagedate"];
$profentr=$_POST["profentr"];
$profstageetudiant=$_POST["profstageetudiant"];
$createntreleve=$_POST["createntreleve"];
$createntrparent=$_POST["createntrparent"];
$viescolairenoteenseignant=$_POST["viescolairenoteenseignant"];
$examenpigiernimes=$_POST["examenpigiernimes"];
$examenispacademies=$_POST["examenispacademies"];
$maxnoteviescolaire=$_POST["maxnoteviescolaire"];
$modulefinanciervatel=$_POST["modulefinanciervatel"];
$profentrconvention=$_POST["profentrconvention"];
$profpentrconvention=$_POST["profpentrconvention"];
$profpcreatetuteur=$_POST["profpcreatetuteur"];
$presentprof=$_POST["presentprof"];
$modulenote40=$_POST["modulenote40"];
$modulenote30=$_POST["modulenote30"];
$modulenote20=$_POST["modulenote20"];
$modulenote15=$_POST["modulenote15"];
$modulenote10=$_POST["modulenote10"];
$modulenote5=$_POST["modulenote5"];
$modulenote6=$_POST["modulenote6"];

if ($modulenote6 == "oui") {
	$modulenote40="non";
	$modulenote30="non";
	$modulenote20="non";
	$modulenote15="non";
	$modulenote10="non";
	$modulenote5="non";
}

$semainevendredi=$_POST["semainevendredi"];
$examenpigieraix=$_POST["examenpigieraix"];
$examenisp=$_POST["examenisp"];
$absprofmotif=$_POST["absprofmotif"];

$accesmesstuteur=$_POST["accesmesstuteur"];
$accesmessenvoituteur=$_POST["accesmessenvoituteur"];
$tuteurenvoiprof=$_POST["tuteurenvoiprof"];
$tuteurenvoituteur=$_POST["tuteurenvoituteur"];
$tuteurenvoiparent=$_POST["tuteurenvoiparent"];
$tuteurenvoieleve=$_POST["tuteurenvoieleve"];
$tuteurenvoidirec=$_POST["tuteurenvoidirec"];
$tuteurenvoiscolaire=$_POST["tuteurenvoiscolaire"];
$tuteurenvoiext=$_POST["tuteurenvoiext"];
$tuteurenvoigrpelev=$_POST["tuteurenvoigrpelev"];
$tuteurenvoidelegue=$_POST["tuteurenvoidelegue"];
$tuteurenvoipersonnel=$_POST["tuteurenvoipersonnel"];
$stageetudiantadminprof=$_POST["stageetudiantadminprof"];
$affichageia=$_POST["affichageia"];
$affichagesign=$_POST["affichagesign"];

$profpmodifaffect=$_POST["profpmodifaffect"];

$messdefilhori=$_POST["messdefilhori"];

$passmodulebilanfinancier=$_POST["passmodulebilanfinancier"];

$moduleelearning=$_POST["moduleelearning"];
$viescolairemodifetudiant=$_POST["viescolairemodifetudiant"];

$modifnoteapresarret=$_POST["modifnoteapresarret"];

$examenvatelreunion=$_POST["examenvatelreunion"];

$entretienprof=$_POST["entretienprof"];
$profpaccesvisadirection=$_POST["profpaccesvisadirection"];

$bannierehauteur=55;


if ($banniere_dispo == 1) { $hauteur="62"; }
if ($banniere_dispo == 2) { $hauteur="132"; }
if ($banniere_dispo == 3) { $hauteur="132"; }
if ($banniere_dispo == 7) { $hauteur="121"; }
if ($banniere_dispo == 8) { $hauteur="158"; }
if ($banniere_dispo == 9) { $hauteur="150"; }
if ($banniere_dispo == 10) { $hauteur="155"; }
if ($banniere_dispo == 11) { $hauteur="155"; }
if ($banniere_dispo == 12) { $hauteur="190"; }
if ($banniere_dispo == 13) { $hauteur="180"; }
if ($banniere_dispo == 14) { $hauteur="200"; }
if ($banniere_dispo == 15) { $hauteur="132"; }
if ($banniere_dispo == 16) { $hauteur="190"; }
if ($banniere_dispo == 17) { $hauteur="277"; }
if ($banniere_dispo == 18) { $hauteur="206"; }
if ($banniere_dispo == 19) { $hauteur="190"; }
if ($banniere_dispo == 20) { $hauteur="200"; }
if ($banniere_dispo == 21) { $hauteur="158"; }
if ($banniere_dispo == 22) { $hauteur="138"; }
if ($banniere_dispo == 23) { $hauteur="200"; }
if ($banniere_dispo == 24) { $hauteur="244"; }
if ($banniere_dispo == 25) { $hauteur="115"; }
if ($banniere_dispo == 26) { $hauteur="150"; }
if ($banniere_dispo == 27) { $hauteur="220"; }
if ($banniere_dispo == 28) { $hauteur="150"; }
if ($banniere_dispo == 29) { $hauteur="250"; }
if ($hauteur > 0) $bannierehauteur=$hauteur;

if (($banniere_dispo != "") && ($banniere_dispo != "supprimer")) {
	@copy("../image/banniere_triade/banniere-$banniere_dispo.jpg","../data/image_banniere/banniere000.jpg");
	$pubhaut="oui";
	$nomlogo="../data/image_banniere/banniere000.jpg";
	banniere_edit("./librairie_js/menudepart.js",$nomlogo,$hauteur);
	$nomlogo2="./data/image_banniere/banniere000.jpg";
	banniere_edit("../librairie_js/menudepart.js",$nomlogo2,$hauteur);
	banniere_edit("../librairie_js/menuadmin.js",$nomlogo2,$hauteur);
	banniere_edit("../librairie_js/menuparent.js",$nomlogo2,$hauteur);
	banniere_edit("../librairie_js/menuprof.js",$nomlogo2,$hauteur);
	banniere_edit("../librairie_js/menuscolaire.js",$nomlogo2,$hauteur);
	banniere_edit("../librairie_js/menueleve.js",$nomlogo2,$hauteur);
	banniere_edit("../librairie_js/menututeur.js",$nomlogo2,$hauteur);
	banniere_edit("../librairie_js/menupersonnel.js",$nomlogo2,$hauteur);
}

if (trim($_POST["hauteurbanniere"]) != "") { $hauteur=$bannierehauteur=$_POST["hauteurbanniere"]; } 

if ($banniere_dispo == "supprimer") { @unlink("../data/image_banniere/banniere000.jpg"); }

if (!empty($fichier) && ($_POST["chg_ban"] == '1' ) ) {
        // image/pjpeg ;  image/x-png ; image/gif
	if (($type == "image/pjpeg" ) || 
		($type == "image/x-png") || 
		($type == "image/png") || 
		(preg_match('/\.jpg$/',$fichier)) ||
		(preg_match('/\.png$/',$fichier)) || 
		(preg_match('/\.jpeg$/',$fichier)) 
	)  {
		$nomlogo="../data/image_banniere/banniere000.jpg";
        	move_uploaded_file($tmp_name,$nomlogo);
		$messdefil="oui";
		$pubhaut="oui";
		banniere_edit("./librairie_js/menudepart.js",$nomlogo,$hauteur);
		$nomlogo2="./data/image_banniere/banniere000.jpg";
		banniere_edit("../librairie_js/menudepart.js",$nomlogo2,$hauteur);
		banniere_edit("../librairie_js/menuadmin.js",$nomlogo2,$hauteur);
		banniere_edit("../librairie_js/menuparent.js",$nomlogo2,$hauteur);
		banniere_edit("../librairie_js/menuprof.js",$nomlogo2,$hauteur);
		banniere_edit("../librairie_js/menuscolaire.js",$nomlogo2,$hauteur);
		banniere_edit("../librairie_js/menueleve.js",$nomlogo2,$hauteur);
		banniere_edit("../librairie_js/menututeur.js",$nomlogo2,$hauteur);
		banniere_edit("../librairie_js/menupersonnel.js",$nomlogo2,$hauteur);

	 }else{
                $erreur="<br><br><font class=T1 color=red>Attention format du fichier non conforme</font>";
         }
}






/*
if ($lan == "non") {
	$forward="non";
	$pubhaut="oui";
	$meteovalide="non";
}
*/
list($meteoregion,$ville)=preg_split('/:/', $meteoregion);

$ferie=stripslashes($ferie);

$tvavatation=$_POST["tvavatation"];
$tvavatationtaux=preg_replace("/,/",'.',$_POST["tvavatationtaux"]);
$autocompletionlogin=$_POST["autocompletionlogin"];
$autodateabsrtd=$_POST["autodateabsrtd"];
$messdefilhoriY=$_POST["messdefilhoriY"];
$messdefilhoriX=$_POST["messdefilhoriX"];

$texte="<?php\n";
$texte.="define(\"LAN\",\"$lan\");\n";
$texte.="define(\"PROXY\",\"$proxy\");\n";
$texte.="define(\"FORWARDMAIL\",\"$forward\");\n";
$texte.="define(\"TIMEZONE\",\"$timezone\");\n";
$texte.="define(\"TIMEZONEMINUTE\",\"$minute\");\n";
$texte.="define(\"MESSDEFIL\",\"$messdefil\");\n";
$texte.="define(\"PUBHAUT\",\"$pubhaut\");\n";
$texte.="define(\"UPLOADIMG\",\"$uploadimg\");\n";
$texte.="define(\"GRPMAILPARENT\",\"$grpmailparent\");\n";
$texte.="define(\"MAILCONTACT\",\"$mailcontact\");\n";
$texte.="define(\"METEOVALIDE\",\"$meteovalide\");\n";
$texte.="define(\"METEOID\",\"$meteoregion\");\n";
$texte.="define(\"METEOVILLE\",\"$ville\");\n";
$texte.="define(\"DSTPROF\",\"$dstprof\");\n";
$texte.="define(\"CALPROF\",\"$calprof\");\n";
$texte.="define(\"FERIE\",\"$ferie\");\n";
$texte.="define(\"FORUM\",\"$forum\");\n";
$texte.="define(\"NOTEUSA\",\"$noteusa\");\n";
$texte.="define(\"MAILREPLY\",\"$mailreply\");\n";
$texte.="define(\"TRACE\",\"$trace\");\n";
$texte.="define(\"RESERV\",\"$resvprof\");\n";
$texte.="define(\"RETENUPROF\",\"$retenuprof\");\n";
$texte.="define(\"GRAPH\",\"$graph\");\n";
$texte.="define(\"ABSPROF\",\"$absprof\");\n";
$texte.="define(\"NOTEPROF\",\"$noteprof\");\n";
$texte.="define(\"MAILADMIN\",\"$mailadmin\");\n";
$texte.="define(\"VALIDPWD\",\"$pwd\");\n";
$texte.="define(\"AUDIO\",\"$audio\");\n";
$texte.="define(\"FETE\",\"$fete\");\n";
$texte.="define(\"INFOMEDIC\",\"$infomedic\");\n";
$texte.="define(\"INFOMEDIC2\",\"$infomedic2\");\n";
$texte.="define(\"ACCESSTOCKAGE\",\"$accesstockage\");\n";
$texte.="define(\"ACCESSTOCKAGEPROF\",\"$accesstockageprof\");\n";
$texte.="define(\"ACCESSTOCKAGEPARENT\",\"$accesstockageparent\");\n";
$texte.="define(\"ACCESSTOCKAGECPE\",\"$accesstockagecpe\");\n";
$texte.="define(\"ACCESSTOCKAGEELEVE\",\"$accesstockageeleve\");\n";
$texte.="define(\"TAILLESTOCKAGE\",\"$taillestockage\");\n";
$texte.="define(\"INODESTOCKAGE\",\"$inodestockage\");\n";
$texte.="define(\"SECURITE\",\"$securite\");\n";
$texte.="define(\"MAILMESS\",\"$mailmess\");\n";
$texte.="define(\"VERIFPASS\",\"$verifpass\");\n";
$texte.="define(\"SUPPPASSMAIL\",\"$supppassmail\");\n";
$texte.="define(\"MAILEXTERNE\",\"$mailexterne\");\n";
$texte.="define(\"MAILNOMREPLY\",\"$mailnomreply\");\n";
$texte.="define(\"NOTEELEVEVISU\",\"$infonoteeleve\");\n";
$texte.="define(\"URLNOMCONTACT\",\"$nomdulien\");\n";
$texte.="define(\"URLCONTACT\",\"$adressedulien\");\n";
$texte.="define(\"ACCESMESSELEVE\",\"$accesmesseleve\");\n";
$texte.="define(\"ACCESMESSPARENT\",\"$accesmessparent\");\n";
$texte.="define(\"ACCESNOTEELEVE\",\"$accesnoteeleve\");\n";
$texte.="define(\"ACCESNOTEPARENT\",\"$accesnoteparent\");\n";
$texte.="define(\"MAILADMIN2\",\"$mailadmin2\");\n";
$texte.="define(\"NOTEEXAMEN\",\"$noteexamen\");\n";
$texte.="define(\"MODNAMUR0\",\"$cmpsocial\");\n";
$texte.="define(\"ANI\",\"$aniversaire\");\n";
$texte.="define(\"DSTVISUACCUEIL\",\"$dstvisuaccueil\");\n";
$texte.="define(\"ACCESFORUMPARENT\",\"$accesforumparent\");\n";
$texte.="define(\"ACCESFORUMPROF\",\"$accesforumprof\");\n";
$texte.="define(\"ACCESFORUMELEVE\",\"$accesforumeleve\");\n";
$texte.="define(\"PWDELEVE\",\"$pwdeleve\");\n";
$texte.="define(\"PWDPROF\",\"$pwdprof\");\n";
$texte.="define(\"PWDPARENT\",\"$pwdparent\");\n";
$texte.="define(\"ACCESMESSENVOIELEVE\",\"$accesmessenvoieleve\");\n";
$texte.="define(\"ACCESMESSENVOIPARENT\",\"$accesmessenvoiparent\");\n";
$texte.="define(\"CALMERCREDIAP\",\"$calmercrediap\");\n";
$texte.="define(\"CALMERCREDIMATIN\",\"$calmercredimatin\");\n";
$texte.="define(\"CALSAMEDIAP\",\"$calsamediap\");\n";
$texte.="define(\"CALSAMEDIMATIN\",\"$calsamedimatin\");\n";
$texte.="define(\"CNILNUM\",\"$cnil\");\n";
$texte.="define(\"URLSITE\",\"$urlsite\");\n";
$texte.="define(\"TROMBIELEVE\",\"$trombinoEleve\");\n";
$texte.="define(\"TROMBIPARENT\",\"$trombinoParent\");\n";
$texte.="define(\"MESSDELEGUEPARENT\",\"$messagedelegueparent\");\n";
$texte.="define(\"MESSDELEGUEELEVE\",\"$messagedelegueeleve\");\n";
$texte.="define(\"HTTPS\",\"$https\");\n";
$texte.="define(\"PROFPBULLETIN\",\"$profpbulletin\");\n";
$texte.="define(\"PROFPBULLETINVERIF\",\"$profpbulletinverif\");\n";
$texte.="define(\"PROFPRELEVE\",\"$profpreleve\");\n";
$texte.="define(\"HD_EDT\",\"$hd_edt\");\n";
$texte.="define(\"HF_EDT\",\"$hf_edt\");\n";
$texte.="define(\"MF_EDT\",\"$mf_edt\");\n";
$texte.="define(\"ACCESPROFABSRTD\",\"$accesabsrtdprof\");\n";
$texte.="define(\"NBCARBULL\",\"$nbcarbull\");\n";
$texte.="define(\"ACCESPROFVISUABSRTD\",\"$absvisuprof\");\n";
$texte.="define(\"VISUTRIAUTO\",\"$visutriauto\");\n";
$texte.="define(\"EMAILCHANGEELEVE\",\"$emailchangeeleve\");\n";
$texte.="define(\"AGENDADIRECT\",\"$agendadirect\");\n";
$texte.="define(\"GROUPEGESTIONPROF\",\"$groupeclasseprof\");\n";
$texte.="define(\"AGENTWEB\",\"$agentweb\");\n";
$texte.="define(\"ARCHBIT\",\"$bit\");\n";
$texte.="define(\"ELEVEENVOIELEVE\",\"$eleveenvoieleve\");\n";
$texte.="define(\"ELEVEENVOIPROF\",\"$eleveenvoiprof\");\n";
$texte.="define(\"ELEVEENVOIPARENT\",\"$eleveenvoiparent\");\n";
$texte.="define(\"ELEVEENVOITUTEUR\",\"$eleveenvoituteur\");\n";
$texte.="define(\"ELEVEENVOIDIREC\",\"$eleveenvoidirec\");\n";
$texte.="define(\"ELEVEENVOISCOLAIRE\",\"$eleveenvoiscolaire\");\n";
$texte.="define(\"ELEVEENVOIEXT\",\"$eleveenvoiext\");\n";
$texte.="define(\"PROFENVOIPROF\",\"$profenvoiprof\");\n";
$texte.="define(\"PROFENVOITUTEUR\",\"$profenvoituteur\");\n";
$texte.="define(\"PROFENVOIGROUPE\",\"$profenvoigroupe\");\n";
$texte.="define(\"PROFENVOIPARENT\",\"$profenvoiparent\");\n";
$texte.="define(\"PROFENVOIELEVE\",\"$profenvoieleve\");\n";
$texte.="define(\"PROFENVOIEXT\",\"$profenvoiext\");\n";
$texte.="define(\"PROFENVOIDIREC\",\"$profenvoidirec\");\n";
$texte.="define(\"PROFENVOISCOLAIRE\",\"$profenvoiscolaire\");\n";
$texte.="define(\"PARENTENVOIPROF\",\"$parentenvoiprof\");\n";
$texte.="define(\"PARENTENVOITUTEUR\",\"$parentenvoituteur\");\n";
$texte.="define(\"PARENTENVOIGROUPE\",\"$parentenvoigroupe\");\n";
$texte.="define(\"PARENTENVOIPARENT\",\"$parentenvoiparent\");\n";
$texte.="define(\"PARENTENVOIELEVE\",\"$parentenvoieleve\");\n";
$texte.="define(\"PARENTENVOIDIREC\",\"$parentenvoidirec\");\n";
$texte.="define(\"PARENTENVOIEXT\",\"$parentenvoiext\");\n";
$texte.="define(\"EDTVISUPROF\",\"$edtvisu\");\n";
$texte.="define(\"EXAMENBLANC\",\"$examenblanc\");\n";
$texte.="define(\"EXAMENDS\",\"$examends\");\n";
$texte.="define(\"EXAMENNAMUR\",\"$examennamur\");\n";
$texte.="define(\"EXAMENKINSHASA\",\"$examenkinshasa\");\n";
$texte.="define(\"EXAMENISMAP\",\"$examenismap\");\n";
$texte.="define(\"EXAMEN\",\"$examen\");\n";
$texte.="define(\"VERIFSUJETNOTE\",\"$verifsujetnote\");\n";
$texte.="define(\"MONNAIE\",\"$monnaie\");\n";
$texte.="define(\"NBDECIMALMONNAIE\",\"$nbdecimalmonnaie\");\n";
$texte.="define(\"EXAMENCIEFORMATION\",\"$examencieformation\");\n";
$texte.="define(\"EXAMENEEPP\",\"$exameneepp\");\n";
$texte.="define(\"NOMANI\",\"$nomaniversaire\");\n";
$texte.="define(\"PROFENVOIGRPELE\",\"$profenvoigrpelev\");\n";
$texte.="define(\"PROFENVOIDELEGUE\",\"$profenvoidelegue\");\n";
$texte.="define(\"PARENTENVOIGRPELE\",\"$parentenvoigrpelev\");\n";
$texte.="define(\"PARENTENVOIDELEGUE\",\"$parentenvoidelegue\");\n";
$texte.="define(\"ELEVEENVOIGRPELE\",\"$eleveenvoigrpelev\");\n";
$texte.="define(\"ELEVEENVOIDELEGUE\",\"$eleveenvoidelegue\");\n";
$texte.="define(\"PARENTENVOISCOLAIRE\",\"$parentenvoiscolaire\");\n";
$texte.="define(\"COMBULTINTYPE\",\"$combulltype\");\n";
$texte.="define(\"NBCARBULLPROFP\",\"$nbcarbullprofp\");\n";
$texte.="define(\"SEMAINEDIMANCHE\",\"$semainedimanche\");\n";
$texte.="define(\"TVAVACATION\",\"$tvavatation\");\n";
$texte.="define(\"TVAVACATIONTAUX\",\"$tvavatationtaux\");\n";
$texte.="define(\"AUTOCOMPLETIONLOGIN\",\"$autocompletionlogin\");\n";
$texte.="define(\"CIVARMEE\",\"$civarmee\");\n";
$texte.="define(\"TIMESESSION\",\"$timesession\");\n";
$texte.="define(\"EDTDIRECT\",\"$edtdirect\");\n";
$texte.="define(\"VIESCOLAIREHISTORYCMD\",\"$viescolairehistocmd\");\n";
$texte.="define(\"EMARGEMENT\",\"$emargement\");\n";
$texte.="define(\"AGENDAPDA\",\"$agendapda\");\n";
$texte.="define(\"PASSMODULEINDIVIDUEL\",\"$passmoduleindividuel\");\n";
$texte.="define(\"PASSMODULEMEDICAL\",\"$passmodulemedical\");\n";
$texte.="define(\"ELEVEENVOIPERSONNEL\",\"$eleveenvoipersonnel\");\n";
$texte.="define(\"PARENTENVOIPERSONNEL\",\"$parentenvoipersonnel\");\n";
$texte.="define(\"PROFENVOIPERSONNEL\",\"$profenvoipersonnel\");\n";
$texte.="define(\"PERSONNELENVOIPROF\",\"$personnelenvoiprof\");\n";
$texte.="define(\"PERSONNELENVOIGRPELE\",\"$personnelenvoigrpelev\");\n";
$texte.="define(\"PERSONNELENVOIPARENT\",\"$personnelenvoiprof\");\n";
$texte.="define(\"PERSONNELENVOIELEVE\",\"$personnelenvoiparent\");\n";
$texte.="define(\"PERSONNELENVOIEXT\",\"$personnelenvoiext\");\n";
$texte.="define(\"PLANCLASSEPARENT\",\"$planclasseParent\");\n";
$texte.="define(\"URLNOMCONTACT2\",\"$nomdulien2\");\n";
$texte.="define(\"URLCONTACT2\",\"$adressedulien2\");\n";
$texte.="define(\"URLNOMCONTACT3\",\"$nomdulien3\");\n";
$texte.="define(\"URLCONTACT3\",\"$adressedulien3\");\n";
$texte.="define(\"URLNOMCONTACT4\",\"$nomdulien4\");\n";
$texte.="define(\"URLCONTACT4\",\"$adressedulien4\");\n";
$texte.="define(\"PROFPGESTIONENTREPRISE\",\"$gestionentrepriseprofp\");\n";
$texte.="define(\"VIESCOLAIRESTAGEDATE\",\"$viescolairestagedate\");\n";
$texte.="define(\"VIESCOLAIRESTAGEENT\",\"$viescolairestageent\");\n";
$texte.="define(\"VIESCOLAIRESTAGEETUDIANT\",\"$viescolairestageetudiant\");\n";
$texte.="define(\"PROFSTAGEENTR\",\"$profentr\");\n";
$texte.="define(\"PROFSTAGEETUDIANT\",\"$profstageetudiant\");\n";
$texte.="define(\"CREATENTRPARENT\",\"$createntrparent\");\n";
$texte.="define(\"CREATENTRELEVE\",\"$createntreleve\");\n";
$texte.="define(\"VIESCOLAIRENOTEENSEIGNANT\",\"$viescolairenoteenseignant\");\n";
$texte.="define(\"EXAMENPIGIERNIMES\",\"$examenpigiernimes\");\n";
$texte.="define(\"EXAMENISPACADEMIES\",\"$examenispacademies\");\n";
$texte.="define(\"MAXNOTEVIESCOLAIRE\",\"$maxnoteviescolaire\");\n";
$texte.="define(\"FINANCIERVATEL\",\"$modulefinanciervatel\");\n";
$texte.="define(\"PROFENTRCONVENTION\",\"$profentrconvention\");\n";
$texte.="define(\"PROFPENTRCONVENTION\",\"$profpentrconvention\");\n";
$texte.="define(\"PROFPCREATETUTEUR\",\"$profpcreatetuteur\");\n";
$texte.="define(\"PRESENTPROF\",\"$presentprof\");\n";
$texte.="define(\"NOTATION40\",\"$modulenote40\");\n";
$texte.="define(\"NOTATION30\",\"$modulenote30\");\n";
$texte.="define(\"NOTATION20\",\"$modulenote20\");\n";
$texte.="define(\"NOTATION15\",\"$modulenote15\");\n";
$texte.="define(\"NOTATION10\",\"$modulenote10\");\n";
$texte.="define(\"NOTATION5\",\"$modulenote5\");\n";
$texte.="define(\"NOTATION6\",\"$modulenote6\");\n";
$texte.="define(\"SEMAINEVENDREDI\",\"$semainevendredi\");\n";
$texte.="define(\"PROFMOTIFABSRTD\",\"$absprofmotif\");\n";
$texte.="define(\"ACCESMESSTUTEUR\",\"$accesmesstuteur\");\n";
$texte.="define(\"ACCESMESSENVOITUTEUR\",\"$accesmessenvoituteur\");\n";
$texte.="define(\"TUTEURENVOIPROF\",\"$tuteurenvoiprof\");\n";
$texte.="define(\"TUTEURENVOITUTEUR\",\"$tuteurenvoituteur\");\n";
$texte.="define(\"TUTEURENVOIPARENT\",\"$tuteurenvoiparent\");\n";
$texte.="define(\"TUTEURENVOIELEVE\",\"$tuteurenvoieleve\");\n";
$texte.="define(\"TUTEURENVOIDIREC\",\"$tuteurenvoidirec\");\n";
$texte.="define(\"TUTEURENVOISCOLAIRE\",\"$tuteurenvoiscolaire\");\n";
$texte.="define(\"TUTEURENVOIEXT\",\"$tuteurenvoiext\");\n";
$texte.="define(\"TUTEURENVOIGRPELEV\",\"$tuteurenvoigrpelev\");\n";
$texte.="define(\"TUTEURENVOIDELEGUE\",\"$tuteurenvoidelegue\");\n";
$texte.="define(\"TUTEURENVOIPERSONNEL\",\"$tuteurenvoipersonnel\");\n";
$texte.="define(\"CHOIXMATIEREPROF\",\"$choixmatiereprof\");\n";
$texte.="define(\"CARNETSUIVIPROF\",\"$carnetsuiviprof\");\n";
$texte.="define(\"TROMBIALLCLASSEPROF\",\"$tombiallclasseprof\");\n";
$texte.="define(\"PROFSTAGEETUDIANTADMIN\",\"$stageetudiantadminprof\");\n";
$texte.="define(\"TIMESESSIONPROF\",\"$timesessionprof\");\n";
$texte.="define(\"PASSMODULEBILANFINANCIER\",\"$passmodulebilanfinancier\");\n";
$texte.="define(\"MODULEELEARNING\",\"$moduleelearning\");\n";
$texte.="define(\"INTITULEDIRECTION\",\"$intitule_direction\");\n";
$texte.="define(\"INTITULEELEVE\",\"$intitule_eleve\");\n";
$texte.="define(\"INTITULEELEVES\",\"${intitule_eleve}s\");\n";
$texte.="define(\"PASSOUBLIE\",\"$passoublie\");\n";
$texte.="define(\"BANNIEREDISPO\",\"$banniere_dispo\");\n";
$texte.="define(\"EXAMENBREVETCOLLEGE\",\"$examenbrevetcollege\");\n";
$texte.="define(\"DIRCAHIERTEXTE\",\"$dircahiertexte\");\n";
$texte.="define(\"VIESCOLAIREMODIFETUDIANT\",\"$viescolairemodifetudiant\");\n";
$texte.="define(\"MODIFNOTEAPRESARRET\",\"$modifnoteapresarret\");\n";
$texte.="define(\"PROFPVIDEOPROJO\",\"$profpVideoProjo\");\n";
$texte.="define(\"TITREBANNIERE\",\"$titrebanniere\");\n";
$texte.="define(\"EXAMENIPAC\",\"$examenipac\");\n";
$texte.="define(\"PROFPMODIFAFFECT\",\"$profpmodifaffect\");\n";
$texte.="define(\"PROFPACCESNOTE\",\"$profpaccesnote\");\n";
$texte.="define(\"AUTOINE\",\"$autoINE\");\n";
$texte.="define(\"ENTRETIENPROF\",\"$entretienprof\");\n";
$texte.="define(\"EXAMENJTC\",\"$examenjtc\");\n";
$texte.="define(\"AUTODATEABSRTD\",\"$autodateabsrtd\");\n";
$texte.="define(\"DEFILMESSAGEHORI\",\"$messdefilhori\");\n";
$texte.="define(\"DEFILMESSAGEHORIX\",\"$messdefilhoriX\");\n";
$texte.="define(\"DEFILMESSAGEHORIY\",\"$messdefilhoriY\");\n";
$texte.="define(\"PROFPACCESVISADIRECTION\",\"$profpaccesvisadirection\");\n";
$texte.="define(\"BANNIEREHAUTEUR\",\"$bannierehauteur\");\n";
$texte.="define(\"PROFPACCESABSRTD\",\"$profpaccesabsrtd\");\n";
$texte.="define(\"AFFICHAGEVATEL\",\"$affichageVatel\");\n";
$texte.="define(\"EXAMENVATELREUNION\",\"$examenvatelreunion\");\n";
$texte.="define(\"MODIFTROMBIELEVE\",\"$modiftrombieleve\");\n";
$texte.="define(\"INTITULEENSEIGNANT\",\"$intitule_enseignant\");\n";
$texte.="define(\"INTITULECLASSE\",\"$intitule_classe\");\n";
$texte.="define(\"CNILPROTECTEUR\",\"$cnilprotecteur\");\n";
$texte.="define(\"AFFICHAGEIA\",\"$affichageia\");\n";
$texte.="define(\"AFFICHAGESIGN\",\"$affichagesign\");\n";
$texte.="?>\n";


$fp=fopen("../common/config2.inc.php","w");
fwrite($fp,"$texte");
fclose($fp);


$ficsource="../librairie_css/css.css-".$graph;
$ficdest="../librairie_css/css.css";
if (file_exists($ficsource)) {
	@unlink("../librairie_css/css.css");
	copy($ficsource,$ficdest);
}

//------------------------------------------------------------------
$text2 = '<?php'."\n";
$text2.= 'define("MAXUPLOAD","'.$tailleupload.'");'."\n";
$text2.= '?>'."\n";
$fp=fopen("../common/config6.inc.php","w");
fwrite($fp,"$text2");
fclose($fp);
//-------------------------------------------------------------------

//------------------------------------------------------------------
$fp=fopen("../common/config5.inc.php","w");
$text3 = '<?php'."\n";
$text3.= 'define("CHARSET","'.$charset.'");'."\n";
$text3.= '?>'."\n";
fwrite($fp,"$text3");
fclose($fp);
//-------------------------------------------------------------------
$prenomagentweb=trim(ucfirst($_POST["prenomagentweb"]));
if ($prenomagentweb == "") { $prenomagentweb="Lise"; }
$fp=fopen("../common/config8.inc.php","w");
$text3 = '<?php'."\n";
$text3.= 'define("AGENTWEBPRENOM","'.$prenomagentweb.'");'."\n";
$text3.= 'print "<script language=\"javascript\">";'."\n";
$text3.= 'print "var agentweb=\"'.$prenomagentweb.'\";";'."\n";
$text3.= 'print "</script>";'."\n";
$text3.= '?>'."\n";
fwrite($fp,"$text3");
fclose($fp);
//-------------------------------------------------------------------

if ((defined("LOG")) && (LOG == "oui")) {
    $fichier="../data/install_log/access.log";
    $date=dateDMY();
    $heure=dateHIS();
    $message="Configuration Triade modifié ($forward,$timezone,$minute,$messdefil,$pubhaut,$uploadimg,$grpmailparent,$mailcontact,$meteovalide,$meteoregion,$lan,$proxy,$dstprof,$calprof,$popup,$ferie,$forum,$noteusa,$mailreply,$trace,$resvprof,$retenuprof,$graph,$absprof,$noteprof,,$mailadmin,$pwd,$audio,$fete,$infomedic,$infomedic2,$accesstockage,$taillestockage,$inodestockage,$securite)";
    $texte="$date|$heure|$message\r\n";
    $fic=fopen($fichier,"a+");
    fwrite($fic,$texte);
    fclose($fic);
}


if (($graph == "11") || ($graph == "27")) {
	@unlink("../image/cube.gif");
	@copy("../image/cube1.gif","../image/cube.gif");
	@unlink("./image/cube.gif");
	@copy("./image/cube1.gif","./image/cube.gif");
}elseif ($graph == "28") {
	@unlink("../image/cube.gif");
        @copy("../image/cube3.gif","../image/cube.gif");
        @unlink("./image/cube.gif");
        @copy("../image/cube3.gif","./image/cube.gif");
}elseif ($graph == "29") {
	@unlink("../image/cube.gif");
        @copy("../image/cube4.gif","../image/cube.gif");
        @unlink("./image/cube.gif");
        @copy("../image/cube4.gif","./image/cube.gif");
}else{
	@unlink("../image/cube.gif");
	@copy("../image/cube0.gif","../image/cube.gif");
	@unlink("./image/cube.gif");
	@copy("./image/cube0.gif","./image/cube.gif");
}

header("Location:configuration3.php");

?>
