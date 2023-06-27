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
        <title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
        <body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
        <?php include("./librairie_php/lib_licence.php"); ?>
        <SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'.js'?>"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
<?php  $today= date ("j M, Y");  ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'1.js'?>"></SCRIPT>
<FORM method=POST action="">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE21?></font></b></td></tr>
<tr id='cadreCentral0'>
                  <td valign=top>
			<br><br>
			<ul><font class='T2'><?php print LANGAFF7 ?></font></ul><br>
		    <center>
		    <b><font color=red><?php print LANGAFF8 ?> </font></b>
</center>
        <br>
      <BR>
      <ul>
      </ul>
      <script language=JavaScript>
      function suite() {
	      var confirmation=confirm('<?php print LANGAFF9 ?>','')
               if (confirmation) {
                   location.href="./base_de_donne_key.php?base=suppression";
               }
      }
      </script>
	      <BR><div align="center"> <input type=button  class="BUTTON" value='<?php print LANGBTS ?>' onclick='suite();'> </div><br />
<br>

     <!-- // fin  -->
     </td></tr></table> </form>

               <BR>



     <SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'2.js'?>"> </SCRIPT>
   </BODY></HTML>
