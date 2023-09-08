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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Administration du calendrier D.S.T</title>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once('librairie_php/db_triade.php');
if (DSTPROF == "oui") {
        validerequete("3");
}else{
        validerequete("2");
}
$cnx=cnx();
if (isset($_POST["creat"])) {
	$cr=suppression_dst($_POST["saisie_id_supp"]) ;
        if($cr):
         //      alertJs("Entrée supprimée --  Service Triade");
        	history_cmd($_SESSION["nom"],"SUPPRESSION","DST");
        else:
                error(0);
        endif;
}

?>
<?php
$saisiejour=$_GET["saisiejour"];
$saisiemois=$_GET["saisiemois"];
$saisieannee=$_GET["saisieannee"];
?>
<br />
<center>
<a href="calendrier_config_dst2.php?saisiejour=<?php print $saisiejour?>&saisiemois=<?php print $saisiemois?>&saisieannee=<?php print $saisieannee?>"><?php print LANGDST9 ?></A>
-
<a href="calendrier_config_dst3.php?saisiejour=<?php print $saisiejour?>&saisiemois=<?php print $saisiemois?>&saisieannee=<?php print $saisieannee?>"><?php print LANGDST10 ?></A>
</center>

<BR><UL>
             <form method=post onsubmit="return valid_calendrier()" name=formulaire>
             <?php
                   $jour="$saisiejour";
                   if ($saisiejour < 10) :
                        $jour="0$saisiejour";
                   endif ;
             if ($saisiemois == LANGMOIS1) : $date="$jour/01/$saisieannee"; endif ;
             if (($saisiemois == "C hwevrer")  ||   ($saisiemois == LANGMOIS2 )) : $date="$jour/02/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS3) : $date="$jour/03/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS4) : $date="$jour/04/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS5) : $date="$jour/05/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS6) : $date="$jour/06/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS7) : $date="$jour/07/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS8) : $date="$jour/08/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS9) : $date="$jour/09/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS10) : $date="$jour/10/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS11) : $date="$jour/11/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS12 ) : $date="$jour/12/$saisieannee"; endif ;

             
             ?>
             <font class="T2"><?php print LANGCALEN2 ?></font> <input type=text name="saisiedate" value='<?php print "$date" ?>' size="10" onfocus="this.blur()"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"><BR><BR>

</UL>
<center>
<table width=90% border=1 bordercolor=#000000">
<tr>
<TD bgcolor=yellow align=center width=30%><?php print LANGASS18 ?></TD>
<TD bgcolor=yellow align=center width=30%><?php print LANGASS17 ?></TD>
<TD bgcolor=yellow align=center width='50%'><?php print LANGCARNET68  ?></TD>
<TD bgcolor=yellow align=center width='5%'><?php print LANGacce21  ?></TD>
</TR>

<?php
$data=affDst(); // id_dst,date,matiere,code_classe,heure,duree

$tab_j=array();
// $data : tab bidim - soustab 3 champs
for($i=0;$i<count($data);$i++)
{
        $date_recup_jma=dateFormBase($date);
	if ($date_recup_jma == $data[$i][1]) {

		print "<form method=POST>";
		print "<TR class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >";
		print "<TD>".trim(stripslashes($data[$i][2]))."</td>";
		print "<TD>".trim($data[$i][3])."</td>";
		print "<TD>&nbsp;".LANGTE13." ".trim(timeForm($data[$i][4]))." ".LANGABS43." ".trim($data[$i][5])." heure(s) </td>";
		print "<TD><input type=submit name=creat value='Supprimer' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'><input type=hidden name=saisie_id_supp value='".$data[$i][0]."'</td></TR>";
		print "</form>";
	}
}
?>
</table><BR><BR>
<table align=center><tr><td>
<script language=JavaScript>buttonMagicFermeture()</script>
</td></tr></table><br><br>
</center>
<?php
Pgclose();
?>
</BODY></HTML>
