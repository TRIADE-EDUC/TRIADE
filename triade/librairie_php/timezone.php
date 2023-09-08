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

function dateDMY() {
	$timezone=TIMEZONE;
	$timezoneminute=TIMEZONEMINUTE;
	$heure=date("H");
	$minute=date("i");
	$seconde=date("s");
	$jour=date("d");
	$mois=date("m");
	$annee=date("Y");
	$heure=$heure+$timezone;
	$minute=$minute+$timezoneminute;
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$resultat2=strftime("%d/%m/%Y",$resultat);
	return $resultat2;
}

function dateDMY2() {
	$timezone=TIMEZONE;
	$timezoneminute=TIMEZONEMINUTE;
	$heure=date("H");
	$minute=date("i");
	$seconde=date("s");
	$jour=date("d");
	$mois=date("m");
	$annee=date("Y");
	$heure=$heure+$timezone;
	$minute=$minute+$timezoneminute;
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$resultat2=strftime("%Y-%m-%d",$resultat);
	return $resultat2;
}

function dateYMD() {
	$timezone=TIMEZONE;
        $timezoneminute=TIMEZONEMINUTE;
        $heure=date("H");
        $minute=date("i");
        $seconde=date("s");
        $jour=date("d");
        $mois=date("m");
        $annee=date("Y");
        $heure=$heure+$timezone;
        $minute=$minute+$timezoneminute;
        $resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
        $resultat2=strftime("%Y%m%d",$resultat);
        return $resultat2;
}

function dateMY() {
	$timezone=TIMEZONE;
	$timezoneminute=TIMEZONEMINUTE;
	$heure=date("H");
	$minute=date("i");
	$seconde=date("s");
	$jour=date("d");
	$mois=date("m");
	$annee=date("Y");
	$heure=$heure+$timezone;
	$minute=$minute+$timezoneminute;
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$resultat2=strftime("%m/%Y",$resultat);
	return $resultat2;
}


function dateMY2() {
	$timezone=TIMEZONE;
	$timezoneminute=TIMEZONEMINUTE;
	$heure=date("H");
	$minute=date("i");
	$seconde=date("s");
	$jour=date("d");
	$mois=date("m");
	$annee=date("Y");
	$heure=$heure+$timezone;
	$minute=$minute+$timezoneminute;
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$resultat2=strftime("%m-%d",$resultat);
	return $resultat2;
}

function dateY() {
	$timezone=TIMEZONE;
	$timezoneminute=TIMEZONEMINUTE;
	$heure=date("H");
	$minute=date("i");
	$seconde=date("s");
	$jour=date("d");
	$mois=date("m");
	$annee=date("Y");
	$heure=$heure+$timezone;
	$minute=$minute+$timezoneminute;
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$resultat2=strftime("%Y",$resultat);
	return $resultat2;
}

function dateY_duServeur() {
        $heure=date("H");
        $minute=date("i");
        $seconde=date("s");
        $jour=date("d");
        $mois=date("m");
        $annee=date("Y");
        $resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
        $resultat2=strftime("%Y",$resultat);
        return $resultat2;
}



function dateD_duServeur() {
        $heure=date("H");
        $minute=date("i");
        $seconde=date("s");
        $jour=date("d");
        $mois=date("m");
        $annee=date("Y");
        $resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
        $resultat2=strftime("%d",$resultat);
        return $resultat2;
}


function dateD() {
	$timezone=TIMEZONE;
	$timezoneminute=TIMEZONEMINUTE;
	$heure=date("H");
	$minute=date("i");
	$seconde=date("s");
	$jour=date("d");
	$mois=date("m");
	$annee=date("Y");
	$heure=$heure+$timezone;
	$minute=$minute+$timezoneminute;
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$resultat2=strftime("%d",$resultat);
	return $resultat2;
}

function datej() {
	$timezone=TIMEZONE;
	$timezoneminute=TIMEZONEMINUTE;
	$heure=date("H");
	$minute=date("i");
	$seconde=date("s");
	$jour=date("d");
	$mois=date("m");
	$annee=date("Y");
	$heure=$heure+$timezone;
	$minute=$minute+$timezoneminute;
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$resultat2=strftime("%d",$resultat);
	if (preg_match("/^0/",$resultat2)) {
		$resultat2=trim(strtr($resultat2, "0", " "));
	}
	return $resultat2;
}

function dateM() {
	$timezone=TIMEZONE;
	$timezoneminute=TIMEZONEMINUTE;
	$heure=date("H");
	$minute=date("i");
	$seconde=date("s");
	$jour=date("d");
	$mois=date("m");
	$annee=date("Y");
	$heure=$heure+$timezone;
	$minute=$minute+$timezoneminute;
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$resultat2=strftime("%m",$resultat);
	return $resultat2;
}


function dateM_duServeur() {
        $heure=date("H");
        $minute=date("i");
        $seconde=date("s");
        $jour=date("d");
        $mois=date("m");
        $annee=date("Y");
        $resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
        $resultat2=strftime("%m",$resultat);
        return $resultat2;
}

function dateHIS() {
	$timezone=TIMEZONE;
	$timezoneminute=TIMEZONEMINUTE;
	$heure=date("H");
	$minute=date("i");
	$seconde=date("s");
	$jour=date("d");
	$mois=date("m");
	$annee=date("Y");
	$heure=$heure+$timezone;
	$minute=$minute+$timezoneminute;
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$resultat2=strftime("%H:%M:%S",$resultat);
	return $resultat2;
}

function dateHI() {
	$timezone=TIMEZONE;
	$timezoneminute=TIMEZONEMINUTE;
	$heure=date("H");
	$minute=date("i");
	$seconde=date("s");
	$jour=date("d");
	$mois=date("m");
	$annee=date("Y");
	$heure=$heure+$timezone;
	$minute=$minute+$timezoneminute;
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$resultat2=strftime("%H:%M",$resultat);
	return $resultat2;
}

function dateI() {
	$timezone=TIMEZONE;
	$timezoneminute=TIMEZONEMINUTE;
	$heure=date("H");
	$minute=date("i");
	$seconde=date("s");
	$jour=date("d");
	$mois=date("m");
	$annee=date("Y");
	$heure=$heure+$timezone;
	$minute=$minute+$timezoneminute;
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$resultat2=strftime("%M",$resultat);
	return $resultat2;
}


function dateJourSemaine() {
	$timezone=TIMEZONE;
	$timezoneminute=TIMEZONEMINUTE;
	$heure=date("H");
	$minute=date("i");
	$seconde=date("s");
	$jour=date("d");
	$mois=date("m");
	$annee=date("Y");
	$heure=$heure+$timezone;
	$minute=$minute+$timezoneminute;
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$resultat2=strftime("%w",$resultat);
	return $resultat2;
}




function dateH() {
	$timezone=TIMEZONE;
	$timezoneminute=TIMEZONEMINUTE;
	$heure=date("H");
	$minute=date("i");
	$seconde=date("s");
	$jour=date("d");
	$mois=date("m");
	$annee=date("Y");
	$heure=$heure+$timezone;
	$minute=$minute+$timezoneminute;
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$resultat2=strftime("%H",$resultat);
	return $resultat2;
}

function datecalendrier() {
	$timezone=TIMEZONE;
	$timezoneminute=TIMEZONEMINUTE;
	$heure=date("H");
	$minute=date("i");
	$seconde=date("s");
	$jour=date("d");
	$mois=date("m");
	$annee=date("Y");
	$heure=$heure+$timezone;
	$minute=$minute+$timezoneminute;
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$resultat2=strftime("%Y,%m,%d,%H,%M,%S",$resultat);
	$objet="new Date($resultat2);";
	return $objet;
}

function dateYMDHMS2() {
	$timezone=TIMEZONE;
	$timezoneminute=TIMEZONEMINUTE;
	$heure=date("H");
	$minute=date("i");
	$seconde=date("s");
	$jour=date("d");
	$mois=date("m")-1;
	$annee=date("Y");
	$heure=$heure+$timezone;
	$minute=$minute+$timezoneminute;
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$resultat2=strftime("%Y-%m-%d %H:%M:%S",$resultat);
	return $resultat2;
}

function dateYMDHMS2_duServeur() {
        $heure=date("H");
        $minute=date("i");
        $seconde=date("s");
        $jour=date("d");
        $mois=date("m")-1;
        $annee=date("Y");
        $resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
        $resultat2=strftime("%Y-%m-%d %H:%M:%S",$resultat);
        return $resultat2;
}



function dateYMDHMS() {
	$timezone=TIMEZONE;
	$timezoneminute=TIMEZONEMINUTE;
	$heure=date("H");
	$minute=date("i");
	$seconde=date("s");
	$jour=date("d");
	$mois=date("m")-1;
	$annee=date("Y");
	$heure=$heure+$timezone;
	$minute=$minute+$timezoneminute;
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$resultat2=strftime("%Y,%m,%d,%H,%M,%S",$resultat);
	return $resultat2;
}

function dateprecedent($date) {
	// au format dd/mm/yyyy
	$elements=preg_split('/\//',$date);
        $annee=$elements[2];
	$mois=$elements[1];
	$jour=$elements[0];
	$resultat=mktime(0,0,0,$mois,$jour,$annee);
	$resultat=$resultat - 86400 ;
	$resultat2=strftime("%Y-%m-%d",$resultat);
	return $resultat2;
}


function dateLettre($date) {
	// au format dd/mm/yyyy
	$elements=preg_split('/\//',$date);
        $annee=$elements[2];
	$mois=$elements[1];
	$jour=$elements[0];
	if ($mois == 1) { $mois=LANGMOIS1; }
	if ($mois == 2) { $mois=LANGMOIS2; }
	if ($mois == 3) { $mois=LANGMOIS3; }
	if ($mois == 4) { $mois=LANGMOIS4; }
	if ($mois == 5) { $mois=LANGMOIS5; }
	if ($mois == 6) { $mois=LANGMOIS6; }
	if ($mois == 7) { $mois=LANGMOIS7; }
	if ($mois == 8) { $mois=LANGMOIS8; }
	if ($mois == 9) { $mois=LANGMOIS9; }
	if ($mois == 10) { $mois=LANGMOIS10; }
	if ($mois == 11) { $mois=LANGMOIS11; }
	if ($mois == 12) { $mois=LANGMOIS12; }
	$resultat2="$jour $mois $annee";
	return $resultat2;
}

function recupdateFin($mois) {
	$annee=date("Y");
	if ($mois == "01") { return "31/01/$annee"; }
	if ($mois == "02") { return "29/02/$annee"; }
	if ($mois == "03") { return "31/03/$annee"; }
	if ($mois == "04") { return "30/04/$annee"; }
	if ($mois == "05") { return "31/05/$annee"; }
	if ($mois == "06") { return "30/06/$annee"; }
	if ($mois == "07") { return "31/07/$annee"; }
	if ($mois == "08") { return "31/08/$annee"; }
	if ($mois == "09") { return "30/09/$annee"; }
	if ($mois == "10") { return "31/10/$annee"; }
	if ($mois == "11") { return "30/11/$annee"; }
	if ($mois == "12") { return "31/12/$annee"; }
}

function recupdateFin2($mois,$annee) {
	if ($mois == "01") { return "31/01/$annee"; }
	if ($mois == "02") { return "29/02/$annee"; }
	if ($mois == "03") { return "31/03/$annee"; }
	if ($mois == "04") { return "30/04/$annee"; }
	if ($mois == "05") { return "31/05/$annee"; }
	if ($mois == "06") { return "30/06/$annee"; }
	if ($mois == "07") { return "31/07/$annee"; }
	if ($mois == "08") { return "31/08/$annee"; }
	if ($mois == "09") { return "30/09/$annee"; }
	if ($mois == "10") { return "31/10/$annee"; }
	if ($mois == "11") { return "30/11/$annee"; }
	if ($mois == "12") { return "31/12/$annee"; }
}

function nbjourdumois($mois) {
	$annee=date("Y");
	if ($mois == "01") { return "31"; }
	if ($mois == "02") { return "29"; }
	if ($mois == "03") { return "31"; }
	if ($mois == "04") { return "30"; }
	if ($mois == "05") { return "31"; }
	if ($mois == "06") { return "30"; }
	if ($mois == "07") { return "31"; }
	if ($mois == "08") { return "31"; }
	if ($mois == "09") { return "30"; }
	if ($mois == "10") { return "31"; }
	if ($mois == "11") { return "30"; }
	if ($mois == "12") { return "31"; }
}

function nbjourdumois2($mois,$annee) {
	if ($mois == "01") { return "31"; }
	if ($mois == "02") { return "29"; }
	if ($mois == "03") { return "31"; }
	if ($mois == "04") { return "30"; }
	if ($mois == "05") { return "31"; }
	if ($mois == "06") { return "30"; }
	if ($mois == "07") { return "31"; }
	if ($mois == "08") { return "31"; }
	if ($mois == "09") { return "30"; }
	if ($mois == "10") { return "31"; }
	if ($mois == "11") { return "30"; }
	if ($mois == "12") { return "31"; }
}


function recupdateDebut($mois) {
	$annee=date("Y");
	if ($mois == "01") { return "01/01/$annee"; }
	if ($mois == "02") { return "01/02/$annee"; }
	if ($mois == "03") { return "01/03/$annee"; }
	if ($mois == "04") { return "01/04/$annee"; }
	if ($mois == "05") { return "01/05/$annee"; }
	if ($mois == "06") { return "01/06/$annee"; }
	if ($mois == "07") { return "01/07/$annee"; }
	if ($mois == "08") { return "01/08/$annee"; }
	if ($mois == "09") { return "01/09/$annee"; }
	if ($mois == "10") { return "01/10/$annee"; }
	if ($mois == "11") { return "01/11/$annee"; }
	if ($mois == "12") { return "01/12/$annee"; }
}


function recupdateDebut2($mois,$annee) {
	if ($mois == "01") { return "01/01/$annee"; }
	if ($mois == "02") { return "01/02/$annee"; }
	if ($mois == "03") { return "01/03/$annee"; }
	if ($mois == "04") { return "01/04/$annee"; }
	if ($mois == "05") { return "01/05/$annee"; }
	if ($mois == "06") { return "01/06/$annee"; }
	if ($mois == "07") { return "01/07/$annee"; }
	if ($mois == "08") { return "01/08/$annee"; }
	if ($mois == "09") { return "01/09/$annee"; }
	if ($mois == "10") { return "01/10/$annee"; }
	if ($mois == "11") { return "01/11/$annee"; }
	if ($mois == "12") { return "01/12/$annee"; }
}

function datesuivante($date) {
	// $dateencours au format dd/mm/yyyy
	$elements=preg_split('/\//',$date);
        $annee=$elements[2];
	$mois=$elements[1];
	$jour=$elements[0];
	$resultat=mktime(0,0,0,$mois,$jour,$annee);
	$resultat=$resultat + 86400 ;
	$resultat2=strftime("%Y-%m-%d",$resultat);
	return $resultat2;
}

function datesuivante_nb($date,$nb) {
	// $dateencours au format dd/mm/yyyy
	$elements=preg_split('/\//',$date);
        $annee=$elements[2];
	$mois=$elements[1];
	$jour=$elements[0];
	$resultat=mktime(0,0,0,$mois,$jour,$annee);
	$resultat=$resultat + $nb *86400 ;
	$resultat2=strftime("%Y-%m-%d",$resultat);
	return $resultat2;
}

function dateprecedent_nb($date,$nb) {
	// $dateencours au format dd/mm/yyyy
	$elements=preg_split('/\//',$date);
        $annee=$elements[2];
	$mois=$elements[1];
	$jour=$elements[0];
	$resultat=mktime(0,0,0,$mois,$jour,$annee);
	$resultat=$resultat - $nb * 86400 ;
	$resultat2=strftime("%Y-%m-%d",$resultat);
	return $resultat2;
}

function dateplusn($date,$nj) {
        // $date au format dd/mm/yyyy
        $elements=preg_split('/\//',$date);
        $annee=$elements[2];
        $mois=$elements[1];
        $jour=$elements[0];
        $resultat=mktime(0,0,0,$mois,$jour,$annee);
        $resultat=$resultat + $nj * 86400 ;
        $resultat2=strftime("%Y-%m-%d",$resultat);
        return $resultat2;
}


function dateplusnh($date,$heure,$nh) {
	// $date au format dd/mm/yyyy
	// alertJs("$date,$heure,$nh");
        $elements=preg_split('/\//',$date);
        $annee=$elements[2];
        $mois=$elements[1];
	$jour=$elements[0];
	list($h,$m,$s)=preg_split('/:/',$heure);
        $resultat=mktime($h,$m,$s,$mois,$jour,$annee);
        $resultat=$resultat + $nh * 60 * 60 ;
        $resultat2=strftime("%H:%M:%S",$resultat);
        return $resultat2;
}


function datemoinsn($date,$nj) {
        // $date au format dd/mm/yyyy
        $elements=preg_split('/\//',$date);
        $annee=$elements[2];
        $mois=$elements[1];
        $jour=$elements[0];
        $resultat=mktime(0,0,0,$mois,$jour,$annee);
        $resultat=$resultat - $nj * 86400 ;
        $resultat2=strftime("%Y-%m-%d",$resultat);
        return $resultat2;
}

function date_jour($frdate) {
	$timestamp=conv_datetimestamp($frdate);
        $lstjour = array(LANGDIMANCHE,LANGLUNDI,LANGMARDI,LANGMERCREDI,LANGJEUDI,LANGVENDREDI,LANGSAMEDI);
        return $lstjour[(int)date("w", $timestamp)];
}

function date_jour2($frdate) {
	$timestamp=conv_datetimestamp($frdate);
        $lstjour = array("di","lu","ma","me","je","ve","sa");
        return $lstjour[(int)date("w", $timestamp)];
}


// Conversion date JJ/MM/AAAA en TimeStamp
function conv_datetimestamp( $frdate ) {
    if( preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/([0-9]{2}|[0-9]{4})/", $frdate) )
        $tab = explode( "/", $frdate );
    else
        if( preg_match("/^[0-9]{1,2}-[0-9]{1,2}-([0-9]{2}|[0-9]{4})/", $frdate) )
            $tab = explode( "-", $frdate );
        else
            return false;
    return mktime(0,0,0,$tab[1],$tab[0],$tab[2]);
}



// Nombre de semaine écoulé entre la date A (JJ/MM/AAAA) debut et la date B (JJ/MM/AAAA) fin
function nbsemaine_beetween_date( $frdatea, $frdateb) {
    if( preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/([0-9]{2}|[0-9]{4})/", $frdatea) && preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/([0-9]{2}|[0-9]{4})/", $frdateb)) {
        $arr_datea = explode("/", $frdatea);
        $datea = semaine_date(date_semaine($frdatea), $arr_datea[2]);
        $arr_dateb = explode("/", $frdateb);
        $dateb = semaine_date(date_semaine($frdateb), $arr_dateb[2]);;
        $arr_date = explode("/", $datea);
        $int_j = $arr_date[0];
        $int_m = $arr_date[1];
        $int_a = $arr_date[2];
        $int_nrsemaine = 0;
        for($i=0; ; $i++) {
            $date = date("d/m/Y", mktime(0,0,0,$int_m,($int_j + ($i*7)),$int_a));
            if(conv_datetimestamp($date)>conv_datetimestamp($dateb)) {
                break;
            }
            $int_nrsemaine++;
        }
        return $int_nrsemaine;
    } else {
        return false;
    }
}

// Numéro de la semaine d'une date JJ/MM/AAAA
function date_semaine( $frdate) {
    if( preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/([0-9]{2}|[0-9]{4})/", $frdate) ) {
        $timestamp = conv_datetimestamp($frdate);
        $int_js = (int)date("w", $timestamp);                    // jour courant dans la semaine courante
        if($int_js==0) $int_js = 7;                                // Mise en dernière position du Dimanche
        $int_ja = (int)date("z", $timestamp);                    // jour courant dans l'année courante
        $int_a = (int)date("Y", $timestamp);                    // année courante
        $int_fjs = (int)date("w", mktime(0,0,0,1,1,$int_a));    // 1er jour de l'année courante
        if($int_fjs==0) $int_fjs = 7;                            // Mise en dernière position du Dimanche
        //$int_nbja = (int)date("z", mktime(0,0,0,12,31,$int_a)); // nbr jours dans l'année courante
        $int_jdec = (7 - $int_fjs);                                // nbr de jours restant avant la fin de la 1ere semaine
        $int_fjds = (1 + $int_jdec);                            // 1er jour de la 2ème semaine
        for($i=0;$i<54;$i++) {
            if($int_ja < (($i * 7) + $int_fjds)) break;
        }
        $int_semaine = ($i + 1);
        return $int_semaine;
    } else {
        return false;
    }
}

// Date JJ/MM/AAAA du debut d'une semaine d'une année
function semaine_date( $int_semaine , $int_annee ) {
    if((int)$int_semaine>0 && $int_semaine<>"" && (int)$int_annee>0 && $int_annee<>"" ) {
        $int_ja = (($int_semaine - 1) * 7);                        // jour courant dans l'année courante
        if($int_ja==0)
            $int_ja = 1;                                        // Mise en dernière position du Dimanche
        else
            $int_ja = $int_ja - 1;                                // Mise en dernière position du Dimanche
        $str_date_semaine = date("d/m/Y", mktime(0,0,0,1,$int_ja,$int_annee));
        return $str_date_semaine;
    } else {
        return false;
    }
}

// Conversion date JJ/MM/AAAA en AAAA-MM-JJ
function conv_mysqldate( $frdate ) {
    if( preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/([0-9]{2}|[0-9]{4})/", $frdate) )
        $tab = explode( "/", $frdate );
    else
        if( preg_match("/^[0-9]{1,2}-[0-9]{1,2}-([0-9]{2}|[0-9]{4})/", $frdate) )
            $tab = explode( "-", $frdate );
        else
            return false;

    return $tab[2]."-".$tab[1]."-".$tab[0];
}

// Conversion date AAAA-MM-JJ en JJ/MM/AAAA
function conv_datemysql( $frdate ) {
    if( preg_match("/^([0-9]{2}|[0-9]{4})-[0-9]{1,2}-[0-9]{1,2}/", $frdate) )
        $tab = explode( "-", $frdate );
    else
        if( preg_match("^[0-9]{1,2}\/[0-9]{1,2}\/([0-9]{2}|[0-9]{4})/", $frdate) )
            $tab = explode( "/", $frdate );
        else
            return false;

    return $tab[2]."/".$tab[1]."/".$tab[0];
}


// convertie seconde en jour, heure, minute, seconde
function convert_sec($time) {
	$output = '';
	$tab = array ('jour(s)' => '86400', 'heure(s)' => '3600', 'minute(s)' => '60', 'seconde(s)' => '1');	
	foreach ($tab as $key => $value) {
		$compteur = 0;
		while ($time > ($value-1)) {
			$time = $time - $value;
			$compteur++;
		}
		if ($compteur != 0) {
			$output .= $compteur.' '.$key;
			if ($compteur > 1) $output .= '';
			if ($value != 1) $output .= ', ';
		}
	}
	return $output;
}

// convertie seconde en heure:minute:seconde
function calcul_hours($temps) { 
  	//combien d'heures ? 
	$hours = floor($temps / 3600); 
 	if ($hours < 10) 
    		$hours = "0".$hours;

	//combien de minutes ? 
  	$min = floor(($temps - ($hours * 3600)) / 60); 
  	if ($min < 10) 
    		$min = "0".$min; 

  	//combien de secondes 
  	$sec = $temps - ($hours * 3600) - ($min * 60); 
  	if ($sec < 10) 
    		$sec = "0".$sec; 
         
	return $hours.":".$min.":".$sec.""; 
} 

// convertie seconde en heure:minute:seconde --> affichage 12 heure(s) et 30 minute(s)
function calcul_hours2($temps) { 
  //combien d'heures ? 
  $hours = floor($temps / 3600); 
 if ($hours < 10) 
    $hours = "0".$hours;

  //combien de minutes ? 
  $min = floor(($temps - ($hours * 3600)) / 60); 
  if ($min < 10) 
    $min = "0".$min; 

  //combien de secondes 
  $sec = $temps - ($hours * 3600) - ($min * 60); 
  if ($sec < 10) 
    $sec = "0".$sec; 
         
  return $hours." heure(s) et ".$min." minute(s)"; 
} 

// converti HH:MM:SS  en seconde
function conv_en_seconde($hms) {
	list($h,$m,$s)=preg_split('/:/',$hms);
	$seconde=($h*60*60)+$m*60+$s;
	return($seconde);
}

function nbjours_entre_2_date($datedeb, $datefin) {  // attend yyyy-mm-dd  
     list ($yearF, $monthF, $dayF) = explode ('-', $datedeb);   
     list ($yearC, $monthC, $dayC) = explode ('-', $datefin);   
     if(strlen($yearF)===4){   
         if (false === @checkdate ($monthF, $dayF, $yearF) || false === @checkdate ($monthC, $dayC, $yearC)) {   
             return false;   
         } else {   
             $tFar = mktime (0,0,0,$monthF, $dayF, $yearF);   
             $tClose = mktime (0,0,0,$monthC, $dayC, $yearC);   
             $tDistance = $tFar - $tClose;   
             return round ($tDistance/(24*60*60));   
         }   
    }else{   
         return false;   
   }   
}   

// Permet d'avoir la date de debut de semaine
function debutsem($n) {
    $premier_jour = mktime(0,0,0,date("m"),date("d")-date("w")+1-$n*7,date("Y"));
    $datedeb = date("m-d-Y", $premier_jour);
    return $datedeb;
}

function age($date_naissance) {
	$arr1=explode('/',$date_naissance);
	$arr2=explode('/',dateDMY());
	if (($arr1[1] < $arr2[1]) || (($arr1[1] == $arr2[1]) && ($arr1[0] <= $arr2[0]))) {
		return $arr2[2] - $arr1[2];
	}else{
		return $arr2[2] - $arr1[2] - 1;
	}
}


function diffheure($heuredeb,$heurefin) {
	$hd=explode(":",$heuredeb);
   	$hf=explode(":",$heurefin);
   	$hd[0]=(int)($hd[0]);$hd[1]=(int)($hd[1]);$hd[2]=(int)($hd[2]);
   	$hf[0]=(int)($hf[0]);$hf[1]=(int)($hf[1]);$hf[2]=(int)($hf[2]);
   	if($hf[2]<$hd[2]){$hf[1]=$hf[1]-1;$hf[2]=$hf[2]+60;}
   	if($hf[1]<$hd[1]){$hf[0]=$hf[0]-1;$hf[1]=$hf[1]+60;}
   	if($hf[0]<$hd[0]){$hf[0]=$hf[0]+24;}
   	return (($hf[0]-$hd[0]).":".($hf[1]-$hd[1]).":".($hf[2]-$hd[2]));
}


?>
