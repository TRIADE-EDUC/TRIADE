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

Last updated: 28.06.2002    par Taesch  Eric
*************************************************************/  -->
        <HTML>
        <HEAD>
        <META http-equiv="CacheControl" content = "no-cache">
        <META http-equiv="pragma" content = "no-cache">
        <META http-equiv="expires" content = -1>
        <meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
        <title>Triade</title>
        </head>
        <body bgcolor="#FAEBD7" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
        <?php include("./librairie_php/lib_licence.php"); ?>
        <SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>

             <!-- // texte du menu qui defile   -->
               <?php include("./librairie_php/lib_defilement.php"); ?>
             <!-- // fin du texte   -->

             </TD><td width="472" valign="middle" rowspan="3" align="center">

             <!--   -->
             <div align='center'><?php top_h(); ?>
             <!--  -->

             <SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

              <?php
              $today= date ("j M, Y");
              $fichier=fopen("./data/fic_news_defil.txt","w");
	      $saisietitre=strip_tags($_POST[saisietitre]);
	      $saisienews=strip_tags($_POST[saisienews]);
              $donnee=fwrite($fichier,"$saisietitre#||#$today#||#$saisienews");
              fclose($fichier);
              ?>


                    <!-- // debut de la saisie -->

              <table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
                <tr bgcolor="#666666">
                  <td height="2"> <b><font  color="#FFFFFF">News Défilement </font><font color="#FFFFFF">du <?php print $today   ?> </font></b></td>
                    </tr>
                <tr bgcolor="#CCCCCC">
                  <td >
                    <blockquote>
                      <p align="center"><font color="#000000"> News envoyé.
                        </font></p>
                    </blockquote>

                  </td>
                </tr></table>

<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
