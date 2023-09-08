<?php
      session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
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
<!-- /************************************************************

Last updated: 08.07.2002    par Taesch  Eric
*************************************************************/  -->
        <?php
	$fichier="data/fic_news_defil.txt";
	if (file_exists($fichier))  {
       	 	$fichier=fopen($fichier,"r");
        	$donnee=fread($fichier,10000);
		fclose($fichier);
	}
        $tab=explode("#||#",$donnee);
        $text=stripslashes($tab[2]);
        $text=nl2br($text);
        $titre=stripslashes($tab[0]);

        ?>
        <HTML>
        <HEAD>
        <META http-equiv="CacheControl" content = "no-cache">
        <META http-equiv="pragma" content = "no-cache">
        <META http-equiv="expires" content = -1>
        <meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_compte_caractere.js"></script>
        <title>Triade</title>
        </head>
        <body bgcolor="#FAEBD7" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();setTimeout('timer()',100)" >
        <?php include("./librairie_php/lib_licence.php"); ?>
        <SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>

             <!--  texte du menu qui defile   -->
               <?php include("librairie_php/lib_defilement.php"); ?>
             <!--  fin du texte   -->

             </TD><td width="472" valign="middle" rowspan="3" align="center">

<!--   -->
<div align='center'><?php top_h(); ?>
<!--  -->


             <SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

              <?php  $today= date ("j M, Y");  ?>

                    <!--  debut de la saisie -->

              <FORM method=POST action="newsdefil1.php" name="formulaire">
              <table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
                <tr bgcolor="#666666"><td height="2"> <b><font  color="#FFFFFF">News Défilement</font><font color="#FFFFFF"> du <?php print $today   ?> </font></b></td></tr>
                <tr bgcolor="#CCCCCC"><td > <p align="left"><font color="#000000" class=T1>


              Message du Titre : <input type="text" name="saisietitre" value="<?php print "$titre"  ?>" maxlength=30  size=35><BR>
              <BR><textarea name="saisienews"  onkeypress="compter(this.form)"  rows="6" cols="55"  wrap="VIRTUAL"><?php print "$text"  ?></textarea>

             <BR>Nombre de caractères : <INPUT type="text" name="nbcar" size=3>
	     <br><table border=0 align=center>
	     <tr><td>
	     <script language=JavaScript>buttonMagicSubmit("Enregistrer","Submit"); //text,nomInput</script>
	     </td></tr></table>



                        </font></p>

                   <!-- // fin de la saisie -->
              </blockquote> </td></tr></table>

          </FORM>

<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
