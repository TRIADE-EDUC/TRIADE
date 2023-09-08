<?php
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
//
// fonctions métiers de Triade
//
// Fred Ory Dern. modif :
// Eric Taesch Dern. modif: 01/12/2005
//
// HOST, USER, DB, PWD définies dans ../common/config.inc.php
//


include_once 'lib_param.php';
include_once 'DB.php';
include_once 'timezone.php';
include_once 'conf_error.php';
include_once 'lib_prefixe.php';

include_once("./magpierss/rss_fetch.inc");

global $prefixe;
global $cnx;

//-------------------------------------------------------------------------//

function verifRss($idpers,$membre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id FROM ${prefixe}rssgen WHERE idpers='$idpers' AND membre='$membre' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return count($data);
}

?>
