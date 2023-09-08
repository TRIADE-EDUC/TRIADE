<!--
  /**************************************************************************\
  * Phenix Agenda                                                            *
  * http://phenix.gapi.fr                                                    *
  * Written by    Stephane TEIL            <phenix-agenda@laposte.net>       *
  * Contributors  Christian AUDEON (Omega) <christian.audeon@gmail.com>      *
  *               Maxime CORMAU (MaxWho17) <maxwho17@free.fr>                *
  *               Mathieu RUE (Frognico)   <matt_rue@yahoo.fr>               *
  *               Bernard CHAIX (Berni69)  <ber123456@free.fr>               *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
 -->
<?php
  require("html.inc.php");
  $_GET['msg']=6;
  include("conf.inc.php");
  if (isset($lg) && !empty($lg)) {
    $APPLI_LANGUE = $lg;
  }

  include("../lang/$APPLI_LANGUE.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
  <TITLE><?php echo trad("NOPSCRIPT_ACCES"); ?></TITLE>
  <STYLE type="text/css">
  <!--
    BODY {
      font-family: Verdana, Arial, Tahoma;
      font-size: 12px;
      font-weight: normal;
      color: #000000;
      background-color: #FFFFFF;
    }

    TABLE.message {
      font-family: Helvetica, Verdana, Arial;
      color: #FFFFFF;
      font-size: 30px;
      font-weight: bold;
      background-color: #D50000;
      text-align: center;
    }
  -->
  </STYLE>
</HEAD>

<BODY>
<TABLE width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
<TR>
  <TD width="100%" height="100%" align="center" valign="middle"><TABLE border="0" cellspacing="0" cellpadding="7" align="center" class="message"><TR><TD><?php echo trad("NOPSCRIPT_MSG"); ?></TD></TR></TABLE>
  <BR><BR><BR><BR><BR><A href="../"><B><?php echo trad("NOPSCRIPT_RET"); ?></B></A></TD>
</TR>
</TABLE>
</BODY>
</HTML>
