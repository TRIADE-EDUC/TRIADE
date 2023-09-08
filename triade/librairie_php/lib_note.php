<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET 
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
if (file_exists("./common/config3.inc.php"))  include_once("./common/config3.inc.php");
if (file_exists("../common/config3.inc.php")) include_once("../common/config3.inc.php");
$message=LANGPROFA;
function vsuite() {
	if (!defined("PASS1")) {
		return 1;
	}else {
		return 0;
	}
}
?>
