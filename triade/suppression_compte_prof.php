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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
if(isset($_POST["supp"])):
	$personne=recherche_personne($_POST["saisie_pers_supp"]);
	$cr=@verif_utiliser($_POST["saisie_pers_supp"]);
	if ($cr) {
		$cr=suppression_personnel($_POST["saisie_pers_supp"]) ;
        	if($cr){
			@delete_comptaVacation($_POST["saisie_pers_supp"]);
			@delete_profp($_POST["saisie_pers_supp"],"ALL");
			@delete_prof_com($_POST["saisie_pers_supp"]);
			@suppEntretienEnseignentPourEtudiant($_POST["saisie_pers_supp"]);
			@unlink("data/image_pers/$_POST[saisie_pers_supp].gif");
		        @unlink("data/image_pers/$_POST[saisie_pers_supp].jpg");
			$fichier="./data/pdf_bull/edition_".$_SESSION["saisie_pers_supp"].".pdf";
			@unlink($fichier);
			$image="./data/pdf_bull/graph_".$_SESSION["id_pers"].".jpg";
			@unlink($image);
                	alertJs("Compte supprimé --  Service Triade");
			history_cmd($_SESSION["nom"],"SUPPRESSION","$personne");
			reload_page('suppression_compte_prof.php');
		}
	}else {
		$listematiere=listingMatiereProf($_POST["saisie_pers_supp"],'tous');
		for($i=0;$i<count($listematiere);$i++) {
			$classe=chercheClasse_nom($listematiere[$i][1]);
			$matiere=chercheMatiereNom($listematiere[$i][0]);
			$info.="- En $classe matière $matiere\\n";
		}
		alertJs("Impossible de supprimer ce compte. \\n\\n Compte affecté à une classe. \\n\\n$info \\n\\n  Service Triade");
	}
endif;
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>    <?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return valide_supp_choix('saisie_pers_supp','<?php print LANGSUPP13?>')" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGSUPP9." ".LANGPROF?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<blockquote><BR>
<fieldset><legend><?php print LANGSUPP1?></legend>
&nbsp;&nbsp;
<font class="T2"><?php print LANGNA1." ".LANGNA2?>  :</font> <select name="saisie_pers_supp">
             <option  id='select0'><?php print LANGCHOIX?></option>
<?php
select_personne('ENS'); // creation des options
Pgclose();
?>
</select> <BR><br>
<UL><UL><UL><script language=JavaScript>buttonMagicSubmit("<?php print LANGSUPP10?>","supp"); //text,nomInput</script></UL></UL></UL><br>
<?php brmozilla($_SESSION["navigateur"]); ?>
</fieldset>
</blockquote>
<!-- // fin  -->
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
