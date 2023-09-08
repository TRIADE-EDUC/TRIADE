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
Last updated: 29.08.2004    par Taesch  Eric
*************************************************************/ -->
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Administration calend.</title>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
if (DSTPROF == "oui") {
        validerequete("3");
}else{
        validerequete("2");
}
$cnx=cnx();
$saisiejour=$_GET["saisiejour"];
$saisiemois=$_GET["saisiemois"];
$saisieannee=$_GET["saisieannee"];
?>
<BR><UL>
             <form method=post name=formulaire>
             <?php
             $jour="$saisiejour";
             if ($saisiejour < 10) { $jour="0$saisiejour"; }
             if ($saisiemois == LANGMOIS1 ) : $date="$jour/01/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS2 ) : $date="$jour/02/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS3 ) : $date="$jour/03/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS4 ) : $date="$jour/04/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS5 ) : $date="$jour/05/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS6 ) : $date="$jour/06/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS7 ) : $date="$jour/07/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS8 ) : $date="$jour/08/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS9 ) : $date="$jour/09/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS10 ) : $date="$jour/10/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS11 ) : $date="$jour/11/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS12 ) : $date="$jour/12/$saisieannee"; endif ;

             ?>
Planning du <input type=text name=saisiedate value='<?php print "$date" ?>' size="10" onfocus="this.blur()"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
</ul>
<?php

	if(isset($_GET["idsupp"])) {
		supp_resa($_GET["idsupp"]);
	}
	$data=affPlanEquip("salle",dateFormBase($date));
	// id.n,idmatos.n,idqui.n,quand.n,heure_depart.n,heure_fin.n,info.n,valider.n,type.m,id,m
	print "<table width=95% border=1 align=center>";
	print "<tr bgcolor='yellow'>";
	print "<td width=95 valign=top >".LANGRESA65."</td>";
	print "<td width=150 valign=top>&nbsp;".LANGRESA66."&nbsp;".LANGRESA66bis."</td>";
	print "<td valign=top >&nbsp;".LANGRESA24."&nbsp;</td>";
	print "<td  valign=top >&nbsp;".LANGBT50."&nbsp;</td>";
	print "</tr>";
	for($i=0;$i<count($data);$i++) {
		$res="";
        if (DBTYPE == "mysql") {
             if ($data[$i][7] == 0)  {
                  $res="(<b>".LANGRESA67."</b>)";
             }else {
                  $res="(<b>".LANGRESA68."</b>)";
             }
        }
        if (DBTYPE == "pgsql") {
             if ($data[$i][7] == 'f')  {
                  $res="(<b>".LANGRESA67."</b>)";
             }else {
                  $res="(<b>".LANGRESA68."</b>)";
             }
        }


		print "<tr bgcolor='#FFFFFF'>";
		print "<td width=95 valign=top >&nbsp;".timeForm($data[$i][4])."&nbsp;-&nbsp;".timeForm($data[$i][5])."&nbsp;</td>";
		print "<td width=150 valign=top> ".recherche_personne($data[$i][2])."</td>";
		print "<td valign=top > ".ucwords(recherche_equip($data[$i][1]))." ".$res."<br><i>".$data[$i][6]."</i></td>";
		print "<td  valign=top ><input type=button onclick=\"open('calendrier_config_salle.php?saisiejour=".$_GET["saisiejour"]."&saisiemois=".$_GET["saisiemois"]."&saisieannee=".$_GET["saisieannee"]."&idsupp=".$data[$i][0]."','_self','')\" value='".LANGBT50."' class='bouton2' ></td>";
		print "</tr>";

	}
	print "</table>";
Pgclose();
?><br><br>
<table align="center"><tr><td>
<script language=JavaScript>buttonMagicFermeture()</script>
</td></tr></table>
</BODY></HTML>
