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

include_once("./common/config2.inc.php");
function verifDroitEnvoiMessage($membre,$type_dest,$envoiext) {
//	print "$membre,$type_dest,$envoiext";
	if ($envoiext == "mailexterne") { $type_dest="MAILEXT"; }
	if ($membre == "menuprof") {
		if (($type_dest == "GRPMAIL") && (PROFENVOIGROUPE == "oui")) 	{return 1;}
		if (($type_dest == "ENS") && (PROFENVOIPROF == "oui")) 		{return 1;}
		if (($type_dest == "TUT") && (PROFENVOITUTEUR == "oui")) 	{return 1;}
		if (($type_dest == "PAR") && (PROFENVOIPARENT == "oui")) 	{return 1;}
		if (($type_dest == "ELE") && (PROFENVOIELEVE == "oui")) 	{return 1;}
		if (($type_dest == "MAILEXT") && (PROFENVOIEXT == "oui")) 	{return 1;}
		if (($type_dest == "ADM") && (PROFENVOIDIREC == "oui")) 	{return 1;}
		if (($type_dest == "MVS") && (PROFENVOISCOLAIRE == "oui")) 	{return 1;}
		if (($type_dest == "GRPMAILELEV") && (PROFENVOIGRPELE == "oui")) {return 1;}
		if (($type_dest == "PER") && (PROFENVOIPERSONNEL == "oui")) {return 1;}
	}elseif($membre == "menuadmin") {
		return 1;
	}elseif($membre == "menuscolaire") {
		return 1;
	}elseif($membre == "menueleve") {
	//	if (($type_dest == "GRPMAIL") && (ELEVEENVOIGROUPE == "oui")) 	{return 1;}
		if (($type_dest == "ENS") && (ELEVEENVOIPROF == "oui")) 	{return 1;}
		if (($type_dest == "PAR") && (ELEVEENVOIPARENT == "oui")) 	{return 1;}
		if (($type_dest == "ELE") && (ELEVEENVOIELEVE == "oui")) 	{return 1;}
		if (($type_dest == "MAILEXT") && (ELEVEENVOIEXT == "oui")) 	{return 1;}
		if (($type_dest == "ADM") && (ELEVEENVOIDIREC == "oui")) 	{return 1;}
		if (($type_dest == "MVS") && (ELEVEENVOISCOLAIRE == "oui")) 	{return 1;}
		if (($type_dest == "TUT") && (ELEVEENVOITUTEUR == "oui")) 	{return 1;}
		if (($type_dest == "DELEGUE") && (ELEVEENVOIDELEGUE == "oui")) 	{return 1;}
		if (($type_dest == "PER") && (ELEVEENVOIPERSONNEL == "oui")) 	{return 1;}
	}elseif($membre == "menuparent") {
		if (($type_dest == "GRPMAIL") && (PARENTENVOIGROUPE == "oui")) 	{return 1;}
		if (($type_dest == "ENS") && (PARENTENVOIPROF == "oui")) 	{return 1;}
		if (($type_dest == "TUT") && (PARENTENVOITUTEUR == "oui")) 	{return 1;}
		if (($type_dest == "PAR") && (PARENTENVOIPARENT == "oui")) 	{return 1;}
		if (($type_dest == "ELE") && (PARENTENVOIELEVE == "oui")) 	{return 1;}
		if (($type_dest == "MAILEXT") && (PARENTENVOIEXT == "oui")) 	{return 1;}
		if (($type_dest == "ADM") && (PARENTENVOIDIREC == "oui")) 	{return 1;}
		if (($type_dest == "PER") && (PARENTENVOIPERSONNEL == "oui")) 	{return 1;}
		if (($type_dest == "GRPMAILELEV") && (PARENTENVOIGRPELE == "oui")) {return 1;}
		if ($type_dest == "MVS") {return 1;}
	}elseif($membre == "menupersonnel") {
		return 1;
	}elseif($membre == "menututeur") {
		return 1;
	}else{
		return 0;
	}
	return 0;
}

?>
