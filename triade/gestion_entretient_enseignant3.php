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
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx(); 
if (isset($_GET["eid"])) {
	$eid=$_GET["eid"];
	if($eid) {
		$sql="SELECT pers_id,nom,prenom,prenom2,type_pers,civ,photo,email FROM ${prefixe}personnel WHERE pers_id='$eid'";
		$res=execSql($sql);
		$data=chargeMat($res);
		$nomProf=ucwords($data[0][1]);
		$prenomProf=ucfirst($data[0][2]);
	}
}
if (isset($_POST["create"])) {
	$eid=$_POST["idpers"];
	if($eid) {
		$sql="SELECT pers_id,nom,prenom,prenom2,type_pers,civ,photo,email FROM ${prefixe}personnel WHERE pers_id='$eid'";
		$res=execSql($sql);
		$data=chargeMat($res);
		$nomProf=ucwords($data[0][1]);
		$prenomProf=ucfirst($data[0][2]);
	}
}
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="100%">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print "Prochain rendez vous pour <font id='color2'>$nomProf $prenomProf</font> "?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign="top">
<?php
if (isset($_POST["create"])) {
	
	$idpers=$_POST["idpers"];

	$heureR=$_POST["heure"];
	$dateR=$_POST["saisiedate"];
	$Qui=recherche_personne2($_SESSION["id_pers"]);
	$message1="Rendez vous pour entretien individuel le ".$dateR." à $heureR avec $Qui";

	$number=md5(uniqid(rand()));
	$emetteur=$_SESSION["id_pers"];
	$destinataire=$idpers;
	$objet="Entretien individuel le $dateR";
	$text=$message1;
	
	$date=dateDMY2();
	$heure=dateHIS();
	$type_personne="ADM";
	$type_personne_dest="ENS";
	$idpiecejointe='';
	
	$cr=envoi_messagerie($emetteur,$destinataire,$objet,Crypte($text,$number),$date,$heure,$type_personne,$type_personne_dest,$number,$idpiecejointe);
	if($cr == 1){
	     history_cmd($_SESSION["nom"],"MESSAGERIE","envoi à $nomProf");
	     if (FORWARDMAIL == "oui") {
		@ini_set("sendmail_from",MAILCONTACT);
		$nomemetteur=strtolower($_SESSION["nom"]);
		$prenomemetteur=strtolower($_SESSION["prenom"]);
		$membre_dest="menuprof";
		if (check_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest)) {
			$email=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest);
			$http=protohttps(); // return http:// ou https://
			$lien="$http".$_SERVER["SERVER_NAME"]."/";
			envoi_mail_forward($nomemetteur,$prenomemetteur,$text,$email,$lien,recherche_personne($emetteur),$number,$objet,$destinataire) ;
		}
	     }
	}

	     /*---------------------------------*/
	$emetteur="$idpers";
	$destinataire=$_SESSION["id_pers"];
	$type_personne="ENS";
	$type_personne_dest="ADM";
	$number=md5(uniqid(rand()));
	$message2="Rendez vous pour entretien individuel le $dateR à $heureR avec $nomProf $prenomProf";
	$text=$message2;
	$cr=envoi_messagerie($emetteur,$destinataire,$objet,Crypte($text,$number),$date,$heure,$type_personne,$type_personne_dest,$number,$idpiecejointe);
	if($cr == 1){
	     $personne_envoi=$_SESSION["nom"];
	     history_cmd($nomProf,"MESSAGERIE","envoi à $personne_envoi");
	     if (FORWARDMAIL == "oui") {
		@ini_set("sendmail_from",MAILCONTACT);
		$nomemetteur=strtolower($nomProf);
		$prenomemetteur=strtolower($prenomProf);
		$membre_dest=$_SESSION["membre"];
		if (check_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest)) {
			$email=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest);
			$http=protohttps(); // return http:// ou https://
			$lien="$http".$_SERVER["SERVER_NAME"]."/";
			envoi_mail_forward($nomemetteur,$prenomemetteur,$text,$email,$lien,recherche_personne($emetteur),$number,$objet,$destinataire) ;
		}
	     }
	}

	print "<br><br><center><font class='T2'>Rendez-vous enregistré.</font>";
	print "<br><br><br><table align=center><td><script language=JavaScript>buttonMagicFermeture();</script></td></tr></table>";
	print "</center>";
}else{
?>
	<form name="formulaire" method="post" action="gestion_entretient_enseignant3.php" >
	<font class="T2"><br />
	&nbsp;&nbsp;Rendez-vous le <input type="text" name="saisiedate" readonly="readonly" size=10 > 
	<?php include_once("librairie_php/calendar.php"); 
	calendarDim('id2','document.formulaire.saisiedate',$_SESSION["langue"],"1","0");
?>
	<br><br>
	&nbsp;&nbsp;à <input type="text" name="heure" onclick="this.value=''" size=5 value="hh:mm" onKeyPress="onlyChar2(event)" > 
	</font>

	<br><br><br>
	<input type="hidden" name="idpers" value="<?php print $eid ?>" />
	<ul><table><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGENR?>","create"); //text,nomInput</script></td></tr></table></ul>

	</form>
<?php } ?> 
</td></tr></table>
<?php 
@Pgclose();
?>
</BODY>
</HTML>
