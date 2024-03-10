<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2024
 *   copyright            : (C) 2000 E. TAESCH - 
 *   Site                 : http://www.triade-educ.org
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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<script language="JavaScript" src="./librairie_js/ajaxIA.js"></script>
<script language="JavaScript" src="./librairie_js/docopy.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<?php include("./librairie_php/lib_licence.php");?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<?php
if (($_SESSION['membre'] == "menuadmin") || ($_SESSION['membre'] == "menuprof") || ($_SESSION['membre'] == "menuscolaire") || ($_SESSION['membre'] == "menupersonnel") ) {
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="750">
<tr id='coulBar0' >
<td height="2"> <b><font  id='menumodule1' ><?php print "TRIADE-COPILOT"?></font></b></td>
</tr>
<tr  id='cadreCentral0'>
<td valign='top' >

<?php
if(file_exists("./common/config-ia.php")) {
	include_once("common/productId.php");
        include_once("common/config-ia.php");
        $productID=PRODUCTID;
        $iakey=IAKEY;
	$lienIA="ajaxCopilot(document.getElementById('question').value,'$productID','$iakey','afficheretour')";
}else{
        $lienIA="alert('Votre Triade n\'est pas configur&eacute; pour utiliser l\'IA. Contacter votre administrateur Triade')";
}
?>

<div style="border-radius:30px;background-color:#F3F6FC;border:solid;border-width:1px;height:92%;width:94%;margin:5px;padding:13px;overflow-x:hidden;overflow-y:auto;" >
<input placeholder="Poser votre question." style="border-radius:30px;height:40px;padding:20px;font-size:14px;"  type='text' name='question' size='70' maxlength='300' id='question' /> 
<input type='button' id='question' value='Envoyer' class="button" onClick="<?php print $lienIA ?>" /><br>&nbsp;&nbsp;&nbsp;&nbsp;<font size='1'><i>TRIADE-COPILOT peut afficher des informations inexactes ou choquantes qui ne rep&eacute;rsentent pas l'opinion de Triade.</i></font>
<br /><br />
<div id='afficheretour' ></div>
<div id='afficheToken' ></div>


</div>
<br><br><br>

<!-- // fin  -->
</td></tr></table>
<br />
<?php
}
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' >
<td height="2"> <b><font  id='menumodule1' ><?php print "Online Assistance"?></font></b></td>
</tr>
<tr  id='cadreCentral0'>
<td valign='top' >
<!-- // fin  -->
<br>
<table><tr><td><img src="image/commun/assisante.gif" /></td><td><font class=T2><?php print "Disposer d'un service d'assistance en ligne." ?></font></td></tr></table>
<br><br>
<table align='center' ><tr><td align='center'>
<script language=JavaScript>buttonMagic2("TRIADE-CLIENT",'http://www.triade-educ.org/accueil/acces_client.php','_blank','','0')</script>
<script language=JavaScript>buttonMagic2("TRIADE-FORUM",'http://forum.triade-educ.org','_blank','','0')</script>
<script language=JavaScript>buttonMagic2("TRIADE-DOC",'http://doc.triade-educ.org','_blank','','0')</script>
<script language=JavaScript>buttonMagic2("TRIADE-DISCORD",'https://www.triade-educ.org/accueil/discord.php','_blank','','0')</script>&nbsp;&nbsp;</td></tr></table>
&nbsp;&nbsp;
<br><br><br>
    
     <!-- // fin  -->
     </td></tr></table>

     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
// Test du membre pour savoir quel fichier JS je dois executer
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
     print "<SCRIPT type='text/javascript' ";
     print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
     print "</SCRIPT>";
else :
     print "<SCRIPT type='text/javascript' ";
     print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
     print "</SCRIPT>";
     top_d();
     print "<SCRIPT type='text/javascript' ";
     print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
     print "</SCRIPT>";
endif ;
     ?>

<!-- Brevo Conversations {literal} -->
<script>
    (function(d, w, c) {
        w.BrevoConversationsID = '64baa5aa2041cf06f4299bfc';
        w[c] = w[c] || function() {
            (w[c].q = w[c].q || []).push(arguments);
        };
        var s = d.createElement('script');
        s.async = true;
        s.src = 'https://conversations-widget.brevo.com/brevo-conversations.js';
        if (d.head) d.head.appendChild(s);
    })(document, window, 'BrevoConversations');
</script>
<!-- /Brevo Conversations {/literal} -->

</BODY></HTML>
