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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<script language="JavaScript" src="librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<?php //<script language="JavaScript" src="librairie_js/function.js"></script> ?>
<title>Triade Vidéo-Projecteur</title>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" bgcolor="#FFFFFF" >
<?php include("./librairie_php/lib_licence.php"); ?>
<div style="height: 90%; width: 97%; padding: 5px; border: 1px solid rgba(0,0,0,0.5); border-radius: 10px; background: rgba(0,0,0,0.25); box-shadow: 0 2px 6px rgba(0,0,0,0.5), inset 0 1px rgba(255,255,255,0.3), inset 0 10px rgba(255,255,255,0.1), inset 0 10px 20px rgba(255,255,255,0.3), inset 0 -15px 30px rgba(0,0,0,0.3); -o-box-shadow: 0 2px 6px rgba(0,0,0,0.5), inset 0 1px rgba(255,255,255,0.3), inset 0 10px rgba(255,255,255,0.2), inset 0 10px 20px rgba(255,255,255,0.25), inset 0 -15px 30px rgba(0,0,0,0.3); -webkit-box-shadow: 0 2px 6px rgba(0,0,0,0.5), inset 0 1px rgba(255,255,255,0.3), inset 0 10px rgba(255,255,255,0.2), inset 0 10px 20px rgba(255,255,255,0.25), inset 0 -15px 30px rgba(0,0,0,0.3); -moz-box-shadow: 0 2px 6px rgba(0,0,0,0.5), inset 0 1px rgba(255,255,255,0.3), inset 0 10px rgba(255,255,255,0.2), inset 0 10px 20px rgba(255,255,255,0.25), inset 0 -15px 30px rgba(0,0,0,0.3);">
<table border='0' width=100%  cellspacing=2>
<?php
include_once('librairie_php/db_triade.php');
validerequete("7");
$cnx=cnx();
$ideleve=$_GET["saisie_eleve"];
$idclasse=$_GET["saisie_classe"];

if ($ideleve == "") {
	print "<tr><td align=center><br><br><b><font size=3>Aucun élève dans cette classe</font></b></td></tr>";
}else {
	$sql="SELECT  elev_id,nom,prenom,c.libelle,lv1,lv2,`option`,regime,date_naissance,numero_eleve,boursier,cdi,bde  FROM ${prefixe}eleves, ${prefixe}classes c WHERE elev_id='$ideleve' AND c.code_class='$idclasse'";
	$res=execSql($sql);
	$data=chargeMat($res);
if( count($data)  <= 0 ) {
	print("<tr><td align=center valign=center>Données introuvables</td></tr>");
}else { //debut else
	$boursier=($data[0][10]) ? LANGOUI : LANGNON ;
	$cdi=($data[0][11]) ? LANGOUI : LANGNON ;
	$bde=($data[0][12]) ? LANGOUI : LANGNON ;

	?>
	<tr>
	<td width=5 valign=top rowspan=3>
	<div>
<!--	<div style="position:absolute;top:0px;left:0px;z-index:1000000" ><img src='image/commun/paperclip.png'></div> -->
	<img src="image_trombi.php?idE=<?php print $ideleve ?>" height='100' style="position:relative;top:19;left:15px;z-index:1;border:2px solid #fff;background: url(img/tiger.png) no-repeat;-moz-box-shadow: 5px 5px 5px grey;-webkit-box-shadow: 5px 5px 5px grey ;box-shadow: 5px 5px 5px grey;-moz-border-radius:20px;-webkit-border-radius:25px;border-radius:25px;"   >
	</div>
	</td>
	<td valign='top' >
	<div  style="position:relative;top:19;left:50px;z-index:1" >
	&nbsp; <font size=3 color="#000000" >Nom : <b><?php print strtoupper(trim($data[0][1]))?></b></font>
	<br>&nbsp; <font size=3 color="#000000" >Prénom : <b><?php print ucwords(trim($data[0][2]))?></b></font>
	<br>&nbsp; <font size=3 color="#000000" >Age : <?php
	                $dateage=dateForm($data[0][8]);
	                if ($dateage == "00/00/0000") {
	                        print "??/??/????";
	                        $age="??";
	                }else{
	                        print $dateage;
	                        $age=calculAge(dateForm($data[0][8]));
	                }
		        ?>&nbsp;&nbsp;(<?php print $age ?> ans)
	<br>&nbsp; <font class='T1'>Boursier (<?php print $boursier?>)&nbsp;&nbsp;/&nbsp;&nbsp;CDI (<?php print $cdi?>)&nbsp;&nbsp;/&nbsp;&nbsp;BDE (<?php print $bde?>) </font>
	</div>
	</td>
	</tr>
	<?php
	}
}
?>
</table>
</div>
<?php Pgclose(); ?>
</body>
</html>
