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
if (!isset($_SESSION["membre"])) { exit; }
include_once("./common/config.inc.php");
if (file_exists("./common/lib_triade_interne.php")) {
        if (file_exists("../../../common/config-all-site.php")) {
                include_once("../../../common/config-all-site.php");
        }
}
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
$cr=verifEmailEnregistrer($_SESSION["id_pers"],$_SESSION["membre"],$_SESSION["idparent"]);
if ($cr == 0) {
	header("Location:./gescompte.php?alerte");
	exit;
}
Pgclose();
?>
