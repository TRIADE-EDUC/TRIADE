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
<body  id='bodyfond2' >
<?php
include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuprof");
// connexion P
$cnx=cnx();
error($cnx);
?>

<form  method=post  name="formulaire">
<!-- // fin  -->
<blockquote>
<font class=T2>
<?php print LANGPROFM ?> : <input type=text name="saisie_date" size=12  maxlength=10  readonly >
<?php
include_once("librairie_php/calendar.php");
calendar("id1","document.formulaire.saisie_date",$_SESSION["langue"],"0");
?>

<BR><br>
<?php print LANGELE4 ?> : <select name="saisie_classe">
	  <option value=0  STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX?></option>
	  <?php select_classe_nom() ?>
	  </select><BR><br>
<?php print LANGPROFS ?> : <input type=text name="saisie_text" size=30  maxlength=30 ><br>
</font>
<BR><bR>
<font class=T2>Devoir à <input type=text name="heure" size=4 value="hh:mm"  onclick="this.value=''" onKeyPress="onlyChar2(event)" /> durant </font><select name="duree">
					<option value="choix" id="select0" ><?php print LANGCHOIX?></option>
					<option value=1 id="select1" >1 heure</option>
					<option value=2 id="select1" >2 heures</option>
					<option value=3 id="select1" >3 heures</option>
					<option value=4 id="select1" >4 heures</option>
					<option value=5 id="select1" >5 heures</option>
					<option value=6 id="select1" >6 heures</option>
					<option value=7 id="select1" >7 heures</option>
					<option value=8 id="select1" >8 heures</option>
				</select><BR><br /><br />
<script language=JavaScript>buttonMagicSubmit("<?php print LANGPROFT?>","create"); //text,nomInput</script>
<br><br>
</blockquote>
<!-- // fin  -->
</form>
<?php
if(isset($_POST["create"])):
        // creation
        $cr=demande_dst($_POST["saisie_date"],$_POST["saisie_classe"],$_POST["saisie_text"],$_SESSION["id_pers"],$_POST["heure"],$_POST["duree"]);
        if($cr) {
                alertJs(LANGPROFU);
		print "<script>parent.window.close();</script";
        }else {
		alertJs("ERREUR vérifier votre saisie -- Service Triade");
	}
endif;
Pgclose();
?>
   </BODY></HTML>
