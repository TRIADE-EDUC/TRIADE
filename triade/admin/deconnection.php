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
session_set_cookie_params(0);
$_SESSION['nom_admin_triade1'] = array();
$_SESSION['membre'] = array();
session_unset();
$ok=session_destroy();
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<title>Deconnection - Triade </title>
</head>
<body bgcolor="#666666" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<TABLE border=O width=100% height=100%>
<TR><TD align=center   bgcolor="#FFFFFF">
Deconnexion en cours <img src="./image/cubemv2.gif" align=center> <img src="./image/cubemv1.gif" align=center> <img src="./image/cubemv.gif" align=center>
<BR><bR> Veuillez patientez S.V.P</TD><TR>
</table>
          <?php
           if ( $ok == true )  :
              sleep(3);
              print "<script language=JavaScript>this.close();\n";
              print "</script>";
            endif;
           ?>
</BODY></HTML>
