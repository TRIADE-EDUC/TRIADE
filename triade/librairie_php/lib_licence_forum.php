<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH 
 *   Site                 : http://www.triade-educ.org
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

//------------------------------------------------------------------------------
// section pour un acces non autorise



//error_reporting(0);
include_once("../librairie_php/lib_emul_register.php");


if (empty($_SESSION["nom"]))  {
    print "<script type=\"text/javascript\">";
    print "location.href='./acces_refuse.php'";
    print "</script>";
    exit;
}

//------------------------------------------------------------------------------
// declaration de variables
include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../common/config3.inc.php");
include_once("../common/config4.inc.php");
include_once("../common/config8.inc.php");
include_once("../common/version.php");
include_once("../common/lib_admin.php");
include_once("../common/lib_ecole.php");
include_once("../common/productId.php");
//------------------------------------------------------------------------------
include_once("../librairie_php/langue_forum.php");
include_once("../librairie_php/lib_error_forum.php");






//----------------------------------------------------------------------------

function droit() {
$annee=date("Y");
print <<<EOF
Copyright (C) 2000-$annee - T.R.I.A.D.E

Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes de la Licence Publique Générale GNU publiée par la Free Software Foundation.

Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE, ni explicite ni implicite, y compris les garanties de commercialisation ou d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU pour plus de détails.

Vous devez avoir reçu une copie de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.

EOF;
}
 


//----------------------------------------------------------------
?>
