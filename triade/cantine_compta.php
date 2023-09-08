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
<?php  include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/jquery-min.js" ></script>
<style>
ul { style-type:none;list-style: none; cursor:pointer;margin-left: 3px;padding-left: 0; }
li { padding:7x; }
</style>



<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print "Gestionnaire de cantine"?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<?php
$idpers=$_SESSION["id_pers"];
if ( (verifDroit($idpers,"cantine")) || ($_SESSION["membre"] == "menuadmin" )) { 
?>

<blockquote><BR>
<form method=post onsubmit="return valide_recherche_eleve()" name="formulaire">
<table border=0 cellspacing=0><tr><td style="padding-top:0px;" nowrap>
<font class="T2"><?php print LANGABS3?> : </font></td><td> 

<input type="text" name="saisie_nom_eleve" size="20" id="search" autocomplete="off" style="width:15em;"  />
 
</td></tr>
<tr><td></td><td style="padding-top:0px;"><div id="userList" style="width:13.5em;border-style:none; background-color:#EEEEEE;"></div></td></tr>
</table><div style="position:relative"><UL><UL><UL><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT39?>","create"); //text,nomInput</script></UL></UL></UL></div>
</form>
<br><br>
<hr><br>
<form method="post" action='cantine_compta_2.php' name='form2' > 
<font class='T2'>Compte non élève : </font>
<select  name="saisie_pers" >
<option value='0' id='select0' ><?php print LANGCHOIX ?></option>
<?php
if ($etatmodif == 1) {
	$liste=preg_replace('/\{/',"",$liste);
	$liste=preg_replace('/\}/',"",$liste);
	$liste=explode(",",$liste);
	print "<optgroup label='".LANGGEN1."'>\n";
	select_personne_grpmail('ADM',$liste);
	print "<optgroup label='".LANGGEN2."'>\n";
	select_personne_grpmail('MVS',$liste);
	print "<optgroup label='".LANGGEN3."'>\n";
	select_personne_grpmail('ENS',$liste);
	print "<optgroup label='"."Personnels"."'>";
	select_personne_grpmail('PER',$liste);
}else{
	print "<optgroup label='".LANGGEN1."'>";
	select_personne('ADM');
	print "<optgroup label='".LANGGEN2."'>";
	select_personne('MVS');
	print "<optgroup label='".LANGGEN3."'>";
	select_personne('ENS');
	print "<optgroup label='"."Personnels"."'>";
	select_personne('PER');
}
?>
</select>
<br><br>
<ul><ul><script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","createnoneleve"); //text,nomInput</script>
<script language=JavaScript>buttonMagicRetour('cantine.php','_self')</script>
<br>
</ul></ul>
</blockquote>
<?php brmozilla($_SESSION["navigateur"]);?>
<?php brmozilla($_SESSION["navigateur"]);?>
</form>
<!-- // fin form -->
</td></tr></table>
<br /><br />
<?php
//alertJs(empty($create));
// affichage de la liste d élèves trouvés
if(isset($_POST["saisie_nom_eleve"])) {
$saisie_nom_eleve=trim($_POST["saisie_nom_eleve"]);
$motif=strtolower($saisie_nom_eleve);
$sql=<<<EOF

SELECT c.libelle,e.nom,e.prenom,e.elev_id,e.classe
FROM ${prefixe}eleves e, ${prefixe}classes c
WHERE lower(e.nom) LIKE '%$motif%'
AND c.code_class = e.classe
ORDER BY c.libelle, e.nom, e.prenom

EOF;
$res=execSql($sql);
$data=chargeMat($res);

?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#CCCCCC" >
<tr id='coulBar0' ><td height="2" colspan=3><b><font   id='menumodule1'>
		<?php print LANGRECH2?> : <font id="color2"><B><?php print ucwords(stripslashes($motif))?></font>
	</font></td>
</tr>
<?php

if( count($data) <= 0 )
	{
	print("<tr><td align=center valign=center>".LANGRECH3."</td></tr>");
	}
else {
?>
<tr bgcolor="#FFFFFF"><td><b><?php print ucwords(LANGIMP10)?></b></td><td><B><?php print LANGIMP8?> <?php print LANGIMP9?></B></td><td><B> </B></td></tr>
<?php
for($i=0;$i<count($data);$i++)
	{
	?>
	<tr>
	<td bgcolor="#FFFFFF"><?php print ucfirst($data[$i][0])?></td>
	<td bgcolor="#FFFFFF"><?php print strtoupper($data[$i][1])?> <?php print ucwords($data[$i][2])?> </td>
	<td bgcolor="#FFFFFF" width='5%'>&nbsp;[&nbsp;<a href="cantine_compta_2.php?eid=<?php print $data[$i][3]?>&idclasse=<?php print $data[$i][4] ?>" >Fiche&nbsp;compta</a>&nbsp;] </td>
	</tr>
	<?php
	}
}
print "</table>";

}

?>
<br><br>
<?php }else{ ?>
<br><font class="T2" id="color3"><center><img src="image/commun/img_ssl.gif" align='center' /> Accès réservé</center></font>
<br><br>
</td></tr></table>
<?php } ?>
<script type="text/JavaScript">InitBulle('#000000','#CCCCFF','red',1);</script>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
       print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
       print "</SCRIPT>";
   else :
      print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
      print "</SCRIPT>";

      top_d();

      print "<SCRIPT language='JavaScript' ";
     print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
     print "</SCRIPT>";

       endif ;
     ?>
</BODY>
</HTML>

<script>
    $(document).ready(function(){
        $('#search').keyup(function(){
            var query = $(this).val();
            if(query != '')
            {
                $.ajax({
                    url:"librairie_php/search_eleve.php",
                    method:"POST",
                    data:{query:query}, success:function(data)
                    {                        $('#userList').fadeIn();
                        $('#userList').html(data);
                    }
                });
            }
        });
        $(document).on('click', 'li', function(){
            $('#search').val($(this).text());
            $('#userList').fadeOut();
        });
    });
</script>

