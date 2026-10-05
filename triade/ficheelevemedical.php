<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
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
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/jquery-min.js"></script>
<style>
#userList li { cursor:pointer; padding:3px 6px; }
</style>
<title>Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMEDIC1 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<form method="post" onsubmit="return valide_recherche_eleve()" name="formulaire">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGABS3 ?> :</span>
    <div>
      <input type="text" name="saisie_nom_eleve" id="search" size="20" autocomplete="off" class="cc-select" style="width:15em;">
      <div id="userList" style="width:15em;border-style:none;background-color:#EEEEEE;"></div>
    </div>
  </div>
</div>
<br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGMEDIC2 ?>","create");</script>
<br><br>
</form>
</td></tr></table>
<br>
<?php
if (isset($_POST["saisie_nom_eleve"])) {
  $saisie_nom_eleve=$_POST["saisie_nom_eleve"];
  $motif=strtolower($saisie_nom_eleve);
  $sql=<<<EOF
SELECT c.libelle,e.nom,e.prenom,e.elev_id
FROM {$prefixe}eleves e, {$prefixe}classes c
WHERE lower(e.nom) LIKE '%$motif%'
AND c.code_class = e.classe
ORDER BY c.libelle, e.nom, e.prenom
EOF;
  $res=execSql($sql);
  $data=chargeMat($res);
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2" colspan="3"><b><font id='menumodule1'>
  <?php print LANGRECH2 ?> : <font id="color2"><b><?php print ucwords($motif) ?></b></font>
</font></b></td></tr>
<?php
if (countTriade($data) <= 0) {
  print "<tr id='cadreCentral0'><td align='center' valign='center'>".LANGRECH3."</td></tr>";
} else {
?>
<tr>
  <td style="background:#ffffd5;font-weight:700;padding:4px 6px;"><?php print LANGELE4 ?></td>
  <td style="background:#ffffd5;font-weight:700;padding:4px 6px;"><?php print LANGELE2 ?></td>
  <td style="background:#ffffd5;font-weight:700;padding:4px 6px;"><?php print LANGELE3 ?></td>
</tr>
<?php
for ($i=0; $i<countTriade($data); $i++) { ?>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
  <td><?php print $data[$i][0] ?></td>
  <td><a href="profpmedic.php?eid=<?php print $data[$i][3] ?>"><span class="htip-wrap"><?php print strtoupper($data[$i][1]) ?><span class="htip"><?php print LANGMEDIC3 ?></span></span></a></td>
  <td><?php print ucwords($data[$i][2]) ?></td>
</tr>
<?php
}
}
?>
</table>
<?php
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php Pgclose(); ?>
</BODY>
</HTML>
<script>
$(document).ready(function(){
  $('#search').keyup(function(){
    var query = $(this).val();
    if (query != '') {
      $.ajax({
        url: "librairie_php/search_eleve.php",
        method: "POST",
        data: {query: query},
        success: function(data){
          $('#userList').fadeIn();
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
