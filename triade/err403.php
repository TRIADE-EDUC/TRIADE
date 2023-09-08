<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<?php
error_reporting(0);
include_once("./common/lib_admin.php");
include_once("./common/lib_ecole.php");
?>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="/<?php print REPECOLE?>/librairie_css/css.css">
<script language="JavaScript" src="/<?php print REPECOLE ?>/librairie_js/clickdroit2.js"></script>
<title>Accès IMPOSSIBLE</title>
</head>
<body id='bodyfond2'>
<BR><BR><BR><BR>
<center>
<table width="57%" border="0" align="center" >
<tr><td align=center id='bordure' >
<font color="red" class="T2"><br />ERREUR 403<br />

Acces Impossible.<br /><br /></font>
Un message a été envoyé à l'auto-support de TRIADE.
<br /><br />
<input type=button  value="Retour Page Précédente" onClick="open('Javascript:history.go(-1)','_top','');" class='bouton2' ><BR><BR>
</td>
</tr>
</table>
<BR><BR>
Notre site web : <a href="http://www.triade-educ.com">www.triade-educ.com</A> pour toutes informations.
<br />
<p> La <b>T</b>ransparence et la <b>R</b>apidité de l'<b>I</b>nformatique <b>A</b>u service <b>D</b>e l'<b>E</b>nseignement<br>Pour visualiser ce site de façon optimale : résolution minimale : 800x600 <br>  © 2000/2006 Triade - Tous droits réservés
</center>
<BR><BR>
</BODY></HTML>
<?php
$date = date("d/m/Y \à G:i");
$fp=fopen("./data/error.log","a+");
fwrite($fp, "<font color=red>Erreur Type : 403 acces Interdit </font>- <br> Visité le $date par ".$_SERVER["REMOTE_ADDR"]." <BR>avec ".$_SERVER["HTTP_USER_AGENT"]." <BR> ".$_SERVER["REDIRECT_URL"]."</font><BR><hr><br>\n");
fclose($fp);
?>
