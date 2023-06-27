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
if (isset($_POST["edt"])) {
	if ($_POST["edt"] == "VT") {
		header('Location: edt_import_vt.php');
	}
	if ($_POST["edt"] == "EX") {
		header('Location: edt_export_vt.php');
	}
}else{
	exit;
}


?>
