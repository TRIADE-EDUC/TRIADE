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
// <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
?>

<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
   <meta http-equiv="Content-type" content = "text/html; charset=iso-8859-1" />
   <meta name="MSSmartTagsPreventParsing" content="TRUE" />
   <meta http-equiv="CacheControl" content = "no-cache" />
   <meta http-equiv="pragma" content = "no-cache" />
   <meta http-equiv="expires" content = -1 />
   <meta name="Copyright" content="Triade©, 2001" />
   <link rel="shortcut icon" href="./favicon.ico" type="image/icon" />
   <LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
   <script type="text/javascript" src="./librairie_js/lib_defil.js"></script>
   <script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
   <script type="text/javascript" src="./librairie_js/function.js"></script>
   <script type="text/javascript" src="./librairie_js/lib_css.js"></script>
   <script type="text/javascript" src="./ckeditor/ckeditor.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include_once("./librairie_php/lib_licence.php"); ?>
<?php verifplus("menudeux",$_SESSION["id_pers"],$_SESSION["membre"]); ?>
<SCRIPT type="text/javascript" <?php print "src='librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include_once("./librairie_php/lib_defilement.php"); ?>
<?php  $today= dateDMY();  ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h();?>
<SCRIPT type="text/javascript" <?php print "src='librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<FORM method=POST action="newsactualite2.php">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESST701 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<?php
	if (file_exists("./data/fic_news_page_contenu.txt")) {	
		$fic=fopen("./data/fic_news_page_contenu.txt","r");
    		$textA=fread($fic,filesize("./data/fic_news_page_contenu.txt"));
		fclose($fic);
		if (file_exists("./data/fic_news_page_titre.txt")) {
			$fic=fopen("./data/fic_news_page_titre.txt","r");
    			$titreA=fread($fic,filesize("./data/fic_news_page_titre.txt"));
			fclose($fic);
			$titreA=stripslashes($titreA);
			$titreA=preg_replace('/"/','&quot;',$titreA);
		}
		if (trim($text) != "") {
			if (file_exists("./data/fic_news_page_date.txt")) {
				$fic=fopen("./data/fic_news_page_date.txt","r");
    				$dateA=fread($fic,filesize("./data/fic_news_page_date.txt"));
				fclose($fic);
			}
		}
	}
?>


<?php brmozilla($_SESSION["navigateur"]); ?>
<p align="left"><font color="#000000">
	&nbsp;&nbsp;<?php print LANGAGENDA180 ?> : <input type="text"  name="saisie_titre_news" maxlength=30  size=35 value="<?php print $titreA ?>" ><br />
<center>
<br />
<?php
$textA=preg_replace('#(\\\\r|\\\\r\\\\n|\\\\n)#', ' ',$textA);
$textA=stripslashes($textA);
?>
<textarea id="editor" name="resultat" ><?php print stripslashes($textA) ?></textarea>
<script type="text/javascript">
var colorGRAPH='<?php print GRAPH ?>';
//<![CDATA[
CKEDITOR.replace( 'editor', {
	height: '300px' , language:'<?php print ($_SESSION["langue"] == "fr") ? "fr" : "en";  ?>'
	} );
//]]>

</script>
</center><br>
<br />
<center>
<?php	if (LAN == "oui") { ?>

<script type="text/javascript" >buttonMagicSubmit("<?php print LANGBT2?>","Submit"); //text,nomInput</script>
<script type='text/javascript' >buttonMagicSubmit("<?php print LANGMESST700 ?>","Supp");</script>

<?php }else{ ?>
	<font color='red' class='T2'><?php print LANGMESS300 ?></font>
<?php } ?>
</center>
<br><br>
</font>
</form><br /></font></p>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
<!-- // fin  -->
</td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT type='text/javascript' ";
            print "src='librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT type='text/javascript' ";
            print "src='librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";
	    top_d();
            print "<SCRIPT type='text/javascript' ";
            print "src='librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
     ?>
   </BODY></HTML>
