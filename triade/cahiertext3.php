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
include_once("./librairie_php/lib_error.php");
include("./common/config.inc.php"); // futur : auto_prepend_file
include("./librairie_php/db_triade.php");
validerequete("profadmin");
$cnx=cnx();
error($cnx);

?>
<HTML>
<HEAD>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
</head>

<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<center>
<table border="1" align=center width=99% bordercolor="#000000">
<?php
if(isset($_POST["supp"])):
        $cr=suppression_devoir_scolaire($_POST["saisie_id"]) ;
        if($cr):
//                alertJs("Compte supprimé --  Service Triade");
//                reload_page('suppression_compte_prof.php');
        else:
                error(0);
        endif;
endif;
?>
<?php
$clsorgrp=0;
if ($_GET["clsorgrp"] != 0 ) { $clsorgrp=1 ; }
$data=affdevoirScolaire($clsorgrp,$_GET["sMat"],$_GET["idclsorgrp"]);
for($i=0;$i<count($data);$i++)
        {
	if (dateFormSimple(dateForm($data[$i][4])) >= date("Ymd") ) {
	print "<form method=POST>";
?>
        <tr class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
<?php
        print "<td width=40% valign=top > Pour le <b>".dateForm($data[$i][4])."</b>";
	print "<BR> <i>Saisie le ".dateForm($data[$i][2]);
	print " à ".timeForm($data[$i][3])."</i>";
	print "</td>";
        print "<td  valign=top>&nbsp;".$data[$i][5];
	print "<div align=right><input type=submit name=supp value='Supprimer' STYLE='font-family: Arial;font-size:9px;color:#CC0000;background-color:#CCCCFF'  ></div>";
	print "<input type=hidden name=saisie_id value='".$data[$i][7]."' >";
	print "</td>";
        print "</tr>\n";
	print "</form>";
	}
        }
?>
</table>
</center>
   </BODY>
   </HTML>
   <?php @Pgclose() ?>
