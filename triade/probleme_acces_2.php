<?php
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
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php 
include_once("./librairie_php/lib_netscape.php"); 
include_once("./librairie_php/lib_licence2.php");
if ($_COOKIE["langue-triade"] == "fr") {
       	include_once("./librairie_php/langue-text-fr.php");
        print "<script type=text/javascript src='librairie_js/languefrmenu-depart.js'></script>\n";
       	print "<script type=text/javascript src='librairie_js/languefrfunction-depart.js'></script>\n";
}elseif ($_COOKIE["langue-triade"] == "en") {
       	print "<script type=text/javascript src='librairie_js/langueenmenu-depart.js'></script>\n";
        print "<script type=text/javascript src='librairie_js/langueenfunction-depart.js'></script>\n";
       	include_once("./librairie_php/langue-text-en.php");
}elseif ($_COOKIE["langue-triade"] == "es") {
       	print "<script type=text/javascript src='librairie_js/langueesmenu-depart.js'></script>\n";
        print "<script type=text/javascript src='librairie_js/langueesfunction-depart.js'></script>\n";
       	include_once("./librairie_php/langue-text-es.php");
}elseif ($_COOKIE["langue-triade"] == "bret") {
       	print "<script type=text/javascript src='librairie_js/languebretmenu-depart.js'></script>\n";
        print "<script type=text/javascript src='librairie_js/languebretfunction-depart.js'></script>\n";
	include_once("./librairie_php/langue-text-bret.php");
}elseif ($_COOKIE["langue-triade"] == "arabe") {
       	print "<script type=text/javascript src='librairie_js/languearabemenu-depart.js'></script>\n";
        print "<script type=text/javascript src='librairie_js/languearabefunction-depart.js'></script>\n";
       	include_once("./librairie_php/langue-text-arabe.php");
}else {
       	print "<script type=text/javascript src='librairie_js/languefrmenu-depart.js'></script>\n";
        print "<script type=text/javascript src='librairie_js/languefrfunction-depart.js'></script>\n";
       	include_once("./librairie_php/langue-text-fr.php");
}
include_once("./common/config2.inc.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");

if (HTTPS == "non") {
	print "<script type='text/javascript'>var http='http://';</script>\n";
}else{
	print "<script type='text/javascript'>var http='https://';</script>\n";
}
print "<script type='text/javascript'>var vocalmess='offline';</script>\n";
print "<script type='text/javascript'>var inc='".GRAPH."';</script>\n";
?>
<script type="text/javascript" >var mailcontact="<?php if (MAILCONTACT != "") { print MAILCONTACT; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact="<?php if (URLCONTACT != "") { print URLCONTACT; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact="<?php if (URLNOMCONTACT != "") { print URLNOMCONTACT; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact2="<?php if (URLCONTACT2 != "") { print URLCONTACT2; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact2="<?php if (URLNOMCONTACT2 != "") { print URLNOMCONTACT2; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact3="<?php if (URLCONTACT3 != "") { print URLCONTACT3; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact3="<?php if (URLNOMCONTACT3 != "") { print URLNOMCONTACT3; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact4="<?php if (URLCONTACT4 != "") { print URLCONTACT4; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact4="<?php if (URLNOMCONTACT4 != "") { print URLNOMCONTACT4; }else{ print ""; } ?>"; </script>

<SCRIPT language="JavaScript" src="./librairie_js/menudepart.js"></SCRIPT>
<?php include_once("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepartconnection1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTTITRE5 ?></FONT></td>
</tr>
<tr id='cadreCentral0'>
<td >

<?php
if (isset($_POST["mdp"])) {
	$cnx=cnx();
	$email=$_POST["email"];
	if (($_POST["membre"] == "ELE") || ($_POST["membre"] == "PAR")) {
		$info=rechercheCompteEmailMdp($email);
	}else{
		$info=rechercheCompteEmailMdpPersonnel($email,$_POST["membre"]);
	}
	if ( (trim($info) != "") && (trim($email) != "") ) { 
		$mdp=passwd_random2();
		list($membre,$idpers)=preg_split('/:/',$info);
		modifPassOublie($mdp,$idpers,$membre,$email);
		$message=LANGTMESS400."<br />".LANGTMESS401." : <br /> $email ";
	}else{
		$message="<font id='color2'>".LANGTMESS402."</font>  <br /><br />".LANGTMESS403." <br />".LANGTMESS404 .": <a href='probleme_acces.php?id'><b>".LANGTMESS405."</b></a> ";
	}	
	Pgclose();
?>
	<br><ul><font class='T2'> <?php print $message ?> <br />
	<br /><br /><?php print LANGattente3 ?></font></ul> 
	
<?php }else{ ?>
	<form name="formulaire" method="post" >
	<table border='0' width=100%>
	<tr><td>
	<ul>
	<br><B><font class=T2><?php print LANGMESS151 ?></B> : <BR><BR><br>
	<?php print LANGMESS152 ?><BR><BR><br />
	<?php print "Pour le mode d'accès : " ?>
	<select name="membre" >
	<option value="" id='select0'><?php print LANGCHOIX ?></option>
	<option value="ELE" id='select1' >Etudiant/Elève</option>
	<option value="PAR" id='select1' >Parent d'élève</option>
	<option value="ENS" id='select1' >Enseignant</option>
	<option value="MVS" id='select1' >Vie Scolaire</option>
	<option value="PER" id='select1' >Personnels</option>
	<option value="TUT" id='select1' >Tuteur de stage</option>
	<option value="ADM" id='select1' >Direction</option>
	</select> 
	<br><br><br>
	<?php print LANGELE244 ?> : <input type=text name="email" size=40 value="<?php print $_POST["email"] ?>" > <br><br>
	</ul></td></tr>
	</table>
	<BR><center><input type=submit name="mdp" value="<?php print LANGMESS153 ?>" class='bouton2'></center>
	</form>
<?php } ?>
<br /><br />
</tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
