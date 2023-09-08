<?php
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


include_once("../common/productId.php");
include_once("../librairie_php/lib_emul_register.php");
include_once("../common/lib_admin.php");
include_once("../common/lib_ecole.php");
include_once("./librairie_php/langue.php");
include_once("./librairie_php/lib_verif.php");
include_once("./librairie_php/lib_licence_text.php");
include_once("./librairie_php/lib_error.php");
include_once("./librairie_php/mactu.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/timezone.php");
include_once("../common/config.inc.php");
if (file_exists("../common/config-fen.php")) include_once("../common/config-fen.php");
if (!defined('LARGEURFEN')) { define("LARGEURFEN","780"); }
print "<script>";
print "var largeurfen='".LARGEURFEN."';";
print "if (screen.width >= 800) { largeurfen='780'; }";
print "if (screen.width >= 1024) { largeurfen='1020'; }";
print "</script>";
?>
