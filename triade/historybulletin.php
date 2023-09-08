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
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Liste des Périodes </title>
</head>
<body id='coulfond1' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onScroll="openPopup()">
<?php include("./librairie_php/lib_licence.php"); ?>
<BR><center><font size=3><U>Liste des bulletins déjà effectués</U></font></center>
<BR><BR>
<table border="1" align=center width=95% bordercolor="#000000">
<tr>
<td align=center bgcolor='yellow' width=20%>Classe</td>
<td align=center bgcolor='yellow'>Trimestre</td>
<td align=center bgcolor='yellow'>Date</td>
<td align=center bgcolor='yellow'>Visualiser / Supprimer</td>
</tr>
<?php
include_once('librairie_php/db_triade.php');
validerequete("2");
$cnx=cnx();

if(isset($_POST["supp"])):
	$fic=trim($_POST["nomfic"]);
	@unlink("$fic");
        $cr=supp_history_bulletin($_POST["idsupp"]) ;
        if($cr):
                //alertJs("Compte supprimé --  Service Triade");
                history_cmd($_SESSION["nom"],"SUPPRESSION BULLETIN","fichier : $fic");
                reload_page('historybulletin.php');
        else:
              //  error(0);
        endif;
endif;



$data=historyBulletinAff();
// fichier,classe,periode,datedebut,datefin
for($i=0;$i<count($data);$i++)
	{
	print "<tr>\n";
	print "<td bgcolor='#FFFFFF' align='center' valign=top>".$data[$i][1]."</td>\n";
	print "<td bgcolor='#FFFFFF' align='center' valign=top>".$data[$i][2]."</td>\n";
	print "<td bgcolor='#FFFFFF' align='center' valign=top>".dateForm($data[$i][3])."-".dateForm($data[$i][4])."</td>\n";
	print "<td align=center bgcolor='#FFFFFF' valign=top><form method=POST><input type=button onclick=\"open('visu_pdf_admin.php?id=".$data[$i][0]."','_blank','');\" value='PDF'  STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'>";
	print " <input type=hidden value='".$data[$i][0]."' name='nomfic'> <input type=hidden value='".$data[$i][5]."' name='idsupp'><input name=supp type=submit value='Supprimer'  STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'></form></td>\n";
	print "</tr>\n";
	}
?>
</table>
<BR><BR>
<table align=center><tr><td><script language=JavaScript>buttonMagicFermeture(); //bouton de fermeture</script></td></tr></table>
<br /><br />
<?php
// deconnexion en fin de fichier
Pgclose();
?>
        </BODY></HTML>



