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
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond2'>
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
validerequete("3");
if(isset($_POST["create"])):
        // creation
	for ($j=0;$j<$_POST["nb_demande"];$j++){
		$number=md5(uniqid(rand()));
		$destinataire=iddemandedst($_POST["saisie_id_$j"]);
		$datedemande=dateForm(datedemandedst($_POST["saisie_id_$j"]));
		$type_personne=recherche_type_personne($_SESSION["id_pers"]);
		$type_personne_dest=recherche_type_personne($destinataire);
		$objet="";
		$envoi=0;
		if ($_POST["saisie_ref_$j"] == 1){
			$objet=LANGDST1;
			$text=LANGDST2." ".$datedemande." ".LANGDST3;
			$date=dateDMY2();
			$heure=dateHIS();
			$cr=envoi_messagerie($_SESSION["id_pers"],$destinataire,$objet,Crypte($text,$number),$date,$heure,$type_personne,$type_personne_dest,$number);
	       		 if ($cr) { supp_dem_dst($_POST["saisie_id_$j"]); $envoi=1; }
		}
		if ($_POST["saisie_acc_$j"] == 1){
			$objet=LANGDST1;
			$text=LANGDST2." ".$datedemande." ".LANGDST4;
			$date=dateDMY2();
			$heure=dateHIS();
			$date_form=datedemandedst($_POST["saisie_id_$j"]);
			$valeur=chercheval($_POST["saisie_id_$j"]);
			$classe=chercheClasseDemandeDst($_POST["saisie_id_$j"]);
			$heure=$_POST["saisie_heure_$j"];
			$duree=$_POST["saisie_duree_$j"];

			if (get_magic_quotes_gpc()) { $valeur=preg_replace("/'/","\'", $valeur); } 

			$cr=calend_dst($date_form,$valeur,$classe,$heure,$duree);
	        	if ($cr) {
	        		envoi_messagerie($_SESSION["id_pers"],$destinataire,$objet,Crypte($text,$number),$date,$heure,$type_personne,$type_personne_dest,$number);
				supp_dem_dst($_POST["saisie_id_$j"]);
				$envoi=1;
	        	}
		}
	    if ((FORWARDMAIL == "oui")&&($envoi == 1)) {
		$emetteur=$_SESSION["id_pers"];
	        $nomemetteur=strtolower(recherche_personne_nom($destinataire,$type_personne_dest));
		$prenomemetteur=strtolower(recherche_personne_prenom($destinataire,$type_personne_dest));
            	if (check_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$type_personne_dest)) {
	              	$email=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$type_personne_dest);
			$http=protohttps(); // return http:// ou https://
                	$lien=$http.$_SERVER["SERVER_NAME"]."/";
                  	envoi_mail_forward($nomemetteur,$prenomemetteur,Crypte($text,$number),$email,$lien,recherche_personne($emetteur),$number,$objet) ;
            } 
       	}

	}
endif;
?>

<form method=post name=form1 >
<!-- // fin  -->
<table border=1 bordercolor="#000000" width="96%" align="center" style="border-collapse: collapse;"  >
<tr>
	<td align=center bgcolor="yellow">&nbsp;<?php print LANGPER6?>&nbsp;</td>
	<td align=center bgcolor="yellow">&nbsp;<?php print LANGDST5?>&nbsp;</td>
	<td align=center bgcolor="yellow">&nbsp;<?php print LANGELE4?>&nbsp;</td>
	<td align=center bgcolor="yellow">&nbsp;<?php print LANGDST6?>&nbsp;</td>
	<td align=center width=5% bgcolor="yellow">&nbsp;<?php print LANGDST7?>&nbsp;</td>
	<td align=center width=5% bgcolor="yellow">&nbsp;<?php print LANGDST8?>&nbsp;</td>
</tr>
<?php
$data=consult_demande_dst();
for($i=0;$i<count($data);$i++) {
?>
<tr  class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'" >
	<td align=center valign=top><?php print recherche_personne($data[$i][1])?></td>
	<td align=center valign=top><?php print dateForm($data[$i][2])?> à <?php print timeForm($data[$i][5]) ?> ( <?php print $data[$i][6] ?> heure(s)) </td>
	<td align=center valign=top><?php print $data[$i][3]?></td>
	<td align=left valign=top>&nbsp;<?php print stripslashes($data[$i][4]) ?></td>
	<td align=center valign=top><input type='checkbox' name="saisie_ref_<?php print $i ?>" value='1' ></td>
	<td align=center valign=top><input type='checkbox' name="saisie_acc_<?php print $i ?>" value='1' >
	<input type=hidden name="saisie_id_<?php print $i ?>" value="<?php print $data[$i][0]?>">
	<input type=hidden name="saisie_heure_<?php print $i ?>" value="<?php print $data[$i][5]?>">
	<input type=hidden name="saisie_duree_<?php print $i ?>" value="<?php print $data[$i][6]?>">
</td>
</tr>
<?php
}
?>
</table>
<br><center>
<input type=hidden name="nb_demande" value="<?php print count($data)?>">
<table align=center><tr><td><script language=JavaScript>buttonMagicFermeture(); //bouton de fermeture</script></td>
<td><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT51?>","create"); //text,nomInput</script></td>
</tr></table><br>

</center>
<!-- // fin  -->
</form>
<?php
Pgclose();
?>
   </BODY></HTML>
