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
<title>TRIADE - Attente - Messagerie</title>
</head>

<body bgcolor="#FCE4BA">
                <?php include("./librairie_php/lib_licence.php"); ?>
                <table width="100%" height=100% border="1" bordercolor="#000000">
  <tr valign="top" bgcolor="#FFFFFF">
    <td height="117" >
      <table width="100%" border="1" bgcolor="#CCCCCC">
        <tr>
          <td  bgcolor="#FFFFFF" height="16">
            <div align="left">De : </div>
          </td>
          <td  bgcolor="#FFFFFF" height="16">
            <div align="center">10/10 - 13:33</div>
          </td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td height="19" valign=top>
            <div align="left" >Objet :</div>
          </td>
          <td height="19" width="18%">
            <div align="center">&nbsp;&nbsp;
                                <a href='#' onclick="imprimer();"><img src="./image/print.gif" align="absmiddle" alt="Imprimer" border=0></A>
            </div>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
