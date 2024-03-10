<?php
session_start();
echo '<?xml version="1.0" encoding="UTF-8" ?>';

/* Input to this file:
$_GET['year'];
$_GET['month'];
$_GET['day'];
$_GET['idclasse'];
$_GET['idprof'];
$_GET['idRessource'];
*/


include_once("common/config.inc.php");
include_once("common/config2.inc.php");
include_once("./librairie_php/langue.php");
include_once("librairie_php/db_triade.php");
include_once("librairie_php/timezone.php");

$cnx=cnx();

if ($_GET['month'] != "") {
	$startOfWeek = date("Y-m-d H:i:s",mktime(0,0,0,$_GET['month'],$_GET['day'],$_GET['year']));
}else{
	$startOfWeek = dateYMDHMS2_duServeur() ;
}

$dateencours=$_GET['day']."/".$_GET['month']."/".$_GET['year'];
$elements=preg_split('/\//',$dateencours);
$nb=6; // 6 jours
$annee=$elements[2];
$mois=$elements[1];
$jour=$elements[0];
$resultat=mktime(0,0,0,$mois,$jour,$annee);
$resultat=$resultat + $nb * 86400 ;
list($annee,$mois,$jours)=preg_split("/-/",strftime("%Y-%m-%d",$resultat));

$endOfWeek = date("Y-m-d H:i:s",mktime(0,0,0,$mois,$jours,$annee));

$idclasse=$_GET['idclasse'];
$idprof=$_GET['idprof'];
$idRessource=$_GET['idRessource'];

$data=listEdt($startOfWeek,$endOfWeek,$idclasse,$idprof,$idRessource,$_SESSION["membre"]);
// code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,docdst,reportle,reporta,idressource,id_resa_liste,idgroupe

@unlink("./data/essai.txt");

$fichiercsv="./data/edt/edtgoogle_".$_SESSION["id_pers"].".csv";
unlink($fichiercsv);
$fp=fopen("$fichiercsv", "w");
fwrite($fp,"Subject,StartDate,StartTime,EndDate,EndTime,Alldayevent,Reminderonoff,ReminderDate,Reminder­Time,Description,Priority\n");
for($i=0;$i<count($data);$i++) {
	$inf["ID"]=$data[$i][0];
	$inf["description"]=$data[$i][1];
	$heuredep=date($data[$i][2]." ".$data[$i][3]);
	list($year,$month,$day)=preg_split("/-/",$data[$i][2]);
	list($hour,$minute,$second)=preg_split("/:/",$data[$i][3]);
	$timestamp=mktime ($hour,$minute,$second,$month,$day,$year) ;
	list($hour,$minute,$second)=preg_split("/:/",$data[$i][4]);
	$timestamp=$timestamp + ($hour*3600) + ($minute*60);
	$nomMatiere=chercheMatiereNom($data[$i][9]);
	$nomMatiere2=$nomMatiere;
	$coursannule=$data[$i][10];
	$reportle=$data[$i][12];
	$reporta=$data[$i][13];
	$docdst=$data[$i][11];
	$idgroupe=$data[$i][16];

	$Nomclasse=chercheClasse_nom($data[$i][6]);

	if ($idgroupe == 0) { 
		$groupelibelle=""; 
		$grpinfo="";
	}else{ 
		$groupelibelle=chercheGroupeNom($idgroupe); 
		$grpinfo=" <u>grp</u> : ".$groupelibelle."<br />";
		
	}

	if ($data[$i][15] > 0) { $valideresa=verifResaValider($data[$i][15]); }else{ $valideresa="aucun"; }
	$ressource=recherche_ressource($data[$i][14]);
	$idprof=$data[$i][7];  $nomProf=recherche_personne($idprof);
	if ($nomMatiere != "") 	{ $nomMatiere="<u>Mat</u> : <a title=\"$nomMatiere\" >".trunchaine($nomMatiere,15)."</a>"; }else{ $nomMatiere=""; }
	if ($nomProf != "") 	{ $nomProf2=$nomProf ; $nomProf="<u>Ens</u> : ".$nomProf; }else{ $nomProf="";$nomProf2=""; }
	if ($data[$i][8] > 0) { 
		$tabpretation=affEvalHoraireMotif($data[$i][8]);
		$pretation=" <i>(".$tabpretation[0][1].")</i>"; 
	}else{
		$pretation="";
	}

	$imgreportele="";
	if ($coursannule == 1) {
		if ($reportle != 0000-00-00) {
			$reportepourleV="Ce cours est report&eacute; pour le ".dateForm($reportle)." &agrave; $reporta";
			$reportepourleT=" - <font color=blue>Report&eacute; le ".dateForm($reportle)." &agrave; $reporta </font>";
		}else{
			$reportepourleT=" - <font color=blue>Cours pas encore report&eacute;</font> ";
			$imgreportele="<img src='image/commun/important.png' title='Cours pas encore report&eacute;'/>";
		}
	}

	$heurefin=strftime("%Y-%m-%d %H:%M:%S",$timestamp);
	$bgcolor=$data[$i][5];
	if ($bgcolor == "") { $bgcolor="#FFFFFF"; }

	$inf["eventStartDate"]=$heuredep;
	$inf["eventEndDate"]=$heurefin;
	$inf["bgColorCode"]=$bgcolor;

	$description=$inf["description"];
	$information=$inf["description"]."&nbsp;";
	$description=preg_replace("/\n/","<br />",$description);
	$information=preg_replace("/\n/","",$information);
	unset($detail);
	unset($texteV);
	unset($Utxt);

	if ($coursannule == "1") {
		$u="<s>"; $uf="</s>"; $Utxt="- <font color=red><b>Annul&eacute;</b> </font> $reportepourleT "; 
	}else{
		$u="";$uf="";$Utxt="";
	}


	$dateDebut=$data[$i][2];
	$dateFin=$dateDebut;
	$heureDebut=$data[$i][3];
	list($tp1,$tp2)=preg_split("/ /",$heurefin);
	$heureFin=$tp2;
	$description="$description";
	$objet="$nomMatiere2 ($Nomclasse)";
	if ($nomProf2 != "") { $objet.=" par $nomProf2"; }

//	if ($information != "") { $texteV.="$information, "; }
	if ($nomMatiere2 != "") { $detail="$u <u>Mati&egrave;re</u> : $nomMatiere2 <br> "; $texteV.="Votre $pretation, $nomMatiere2, "; }
	if ($ressource != "") 	{ $detail.="<i>R&eacute;servation</i> : $ressource <br />"; }
	if ($nomProf2 != "") 	{ $detail.="$nomProf<br>"; $texteV.="est enseign&eacute; par $nomProf2 ";  }
	if ($groupelibelle != "") { $detail.="<u>Groupe</u> : $groupelibelle<br>"; }
	if ($pretation != "") 	{ $detail.="<i>$pretation</i>$uf $Utxt"; }
	if ($coursannule == "1") { $texteV.=", est annulé. $reportepourle "; }
	if ($docdst == "1") { $texteV.=". Le document pour ce devoir, a bien été reçu."; $detail.="<br>Document D.S.T : re&ccedil;u."; }
	$detail=addslashes($detail);
	$detail=preg_replace("/'/","&acute;",$detail);
	$detail=preg_replace('/"/',"&quot;",$detail);
	$information=preg_replace("/'/","&acute;",$information);	
	$information=preg_replace('/"/',"&quot;",$information);

	
	$mess=$detail;
	include_once("common/config2.inc.php");
	if ((LAN == "oui") && (AGENTWEB == "oui")) {
		$texteV=preg_replace("/&nbsp;/","",$texteV);
		$texteV=preg_replace("/&/"," et ",$texteV);
		$texteV=preg_replace("/'/","&acute;",$texteV);
		$texteV=preg_replace("/\(/"," ",$texteV);
		$texteV=preg_replace("/\)/"," ",$texteV);
		$texteV=preg_replace("/M. /","monsieur ",$texteV);
		$texteV=preg_replace("/Mme /","madame ",$texteV);
		$texteV=preg_replace("/Mlle /","mademoisel ",$texteV);
		$texteV=preg_replace("/P. /","professeur ",$texteV);
		$vocal=urlencode(stripHTMLtags($texteV));
		$information="Agent Web";
		$detail="<iframe width=100 height=100 src=\'./agentweb/agentmel.php?inc=5&mess=$vocal\'  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no align=left ></iframe>".$detail."</font>" ;
	}
	$imgresa="";	
	if ((trim($ressource) != "") && ($valideresa != "aucun")){ 
		$ressource="<br />Réserv. : $ressource"; 	
		if ($valideresa == 1) {
			$imgresa="<img src='image/commun/valid.gif' align='center' title='R&eacute;servation confirmer' />";
		}else{
			$imgresa="<img src='image/commun/important.png' align='center' title='R&eacute;servation en attente de validation'/>";
			$detail.="<br><img src=\'image/commun/important.png\' > <font color=red>R&eacute;servation en attente de confirmation.</font>";
		}

	}

	list($t11,$t22)=preg_split('/ /',$inf["eventEndDate"]); //2013-02-05 10:37:00
	list($annee,$mois,$jour)=preg_split('/-/',$t11);
	list($heure,$minute,$seconde)=preg_split('/:/',$t22);
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$eventEndDate=strftime("%Y-%m-%d %H:%M:%S",$resultat);

	$derniercours="";
	if ($idprof > 0) {
		$derniercours=verifSiDernierCours($idprof,"$jour/$mois/$annee");
		if ($derniercours == 0) {
			$derniercours="<img src='image/commun/alerte.png' />";
			$detail.="<img src=\'image/commun/alerte.png\' /> <b>Dernier cours pour l\'enseignant</b><br/>";
		}else{
			$derniercours="";
		}
	}


	list($t1,$t2)=preg_split('/ /',$inf["eventStartDate"]); //2013-02-05 10:37:00
	list($annee,$mois,$jour)=preg_split('/-/',$t1);
	list($heure,$minute,$seconde)=preg_split('/:/',$t2);
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$eventStartDate=strftime("%Y-%m-%d %H:%M:%S",$resultat);

//	$timezone=TIMEZONE;
//	if ($timezone == 0) $timezone="";

	$rc=verifSiAutreGroupeEDT($inf["eventStartDate"],$idclasse);
	if ($rc > 1){
		$idgroupeE=$rc;
		$tabE[$inf["eventStartDate"]]++;
	}else{
		$idgroupeE=0;	
	}


	if ($tabE[$inf["eventStartDate"]] == 2) {
		$position=2;
	}elseif($tabE[$inf["eventStartDate"]] == 3) {
		$position=3;
	}else{
		$position=1;
	}	

	$affiche="
<item>
	<id>".$inf["ID"]."</id>
	<description><a href=\"javascript:AffBulleEDT('$information','./image/commun/info.jpg','$detail');\"; ><img src='image/commun/affichage.gif' border='0' align=left /></a>&nbsp;$derniercours$imgresa $imgreportele $u".$description." $ressource<br />".$nomMatiere."<br />".$grpinfo.$nomProf." ".$pretation."</u></description>
	<eventStartDate>".gmdate('D, d M Y H:i:s',strtotime($eventStartDate)) . ' GMT'.$timezone." </eventStartDate>
	<eventEndDate>".gmdate('D, d M Y H:i:s',strtotime($eventEndDate)) . ' GMT'.$timezone."</eventEndDate>
	<bgColorCode>".$inf["bgColorCode"]."</bgColorCode>
	<idgroupe>".$idgroupeE."</idgroupe>
	<position>".$position."</position>
</item>

";


	$description=preg_replace('/,/',' ',$description);
	$objet=preg_replace('/,/',' ',$objet);
	fwrite($fp,"$objet,$dateDebut,$heureDebut,$dateFin,$heureFin,FALSE,FALSE,,,$description,Normal\n");
	print $affiche;
/*
	$f_pass=fopen("./data/essai2.txt","a+");
	fwrite($f_pass,$affiche);
	fclose($f_pass);  
 */
}
fclose($fp);


Pgclose();

/*
<item>
	<id>1</id>
	<description>Lunch</description>
	<eventStartDate>Mon, 13 Feb 2006 11:30 GMT</eventStartDate>
	<eventEndDate>Mon, 13 Feb 2006 12:00 GMT</eventEndDate>
	<bgColorCode>#FFFFFF</bgColorCode>
</item>
 */
?>
