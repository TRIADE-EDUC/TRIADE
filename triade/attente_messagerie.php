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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<?php include("./librairie_php/lib_licence.php"); ?>
<title><?php print LANGatte_mess1?></title>
</head>
<body id='bodyfond2' border=1 marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<table width=102% height=100%>
  <tr>
    <td align=center> <font class='T2'><?php print LANGatte_mess2 ?></font>
       <center>
        <br>
        <table border=0 >
          <tr><td><img src="./image/temps1.gif" align=center></td></tr>
        </table>
        <br>
        <font class='T2'><?php print LANGatte_mess3 ?></font>
      </center>
    </td>
  </tr>
</table>
</body>
</html>
