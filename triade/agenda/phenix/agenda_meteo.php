<?php
if (!isset($sid)) {
  Header("location: deconnexion.php?msg=5");
  exit;
}
// MOD meteo v5.0
  $err = 0;
  $DB_CX->DbQuery("SELECT util_meteo_code FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$USER_SUBSTITUE);
  $code_ville = $DB_CX->DbResult(0,0);
  $code_ville = explode(";",$code_ville);
  if ($code_ville[1]) {
  // L'utilisateur a activé l'affichage de la meteo
    $DB_CX->DbQuery("SELECT * FROM ${PREFIX_TABLE}meteo WHERE met_code_ville='".$code_ville[0]."'");
  if ($DB_CX->DbResult(0,0)!=NULL) {
    $met_nom_ville = $DB_CX->DbResult(0,1);
    $met_releve = $DB_CX->DbResult(0,2);
    $met_sync_px = $DB_CX->DbResult(0,3);
    $met_jour0 = explode(";",$DB_CX->DbResult(0,4));
    $met_jour1 = explode(";",$DB_CX->DbResult(0,5));
    $met_jour2 = explode(";",$DB_CX->DbResult(0,6));
    $met_jour3 = explode(";",$DB_CX->DbResult(0,7));
    $met_jour4 = explode(";",$DB_CX->DbResult(0,8));
    $met_jour5 = explode(";",$DB_CX->DbResult(0,9));
    $met_jour6 = explode(";",$DB_CX->DbResult(0,10));  
  }
  else $err = "Pas de donnée disponible pour ce code ville";
  }
  function aff_meteo_j($sel_jour) {  
  global $AgendaPopupBordure, $bgColor, $localTime, $code_ville, $err, $DB_CX, $met_nom_ville, $met_releve, $met_sync_px, $met_jour0, $met_jour1, $met_jour2, $met_jour3, $met_jour4, $met_jour5, $met_jour6;
    if (($code_ville[1]) && (!$err)) {
    for($j=0;$j<7;$j++) {
      if (${'met_jour'.$j}[0] == $sel_jour) {
      $jour_date_debug = ${'met_jour'.$j}[0];
      $low = ${'met_jour'.$j}[1];
      if ($low !="N/A") {
        if (trad("MODMET_TEMP") == "C") $low = round(($low-32)*5/9)."°C";
      elseif (trad("MODMET_TEMP") == "F") $low = $low."°F";
      }
      $hi = ${'met_jour'.$j}[2];
      if ($hi !="N/A") { 
        if (trad("MODMET_TEMP") == "C") $hi = round(($hi-32)*5/9)."°C";  
      elseif (trad("MODMET_TEMP") == "F") $hi = $hi."°F";  
      }
      $ico_mat = ${'met_jour'.$j}[3];
      $ico_soir = ${'met_jour'.$j}[4];
      $sunr = ${'met_jour'.$j}[5];
      $suns = ${'met_jour'.$j}[6];
      list($suns_h) = explode(":",$suns);
      for ($i=7;$i<=10;$i++) {
        if (trim(${'met_jour'.$j}[$i]) != "N/A")
        $aff_met[$i] = ${'met_jour'.$j}[$i]."%";
        else
        $aff_met[$i] = "N/A";
      }
      for ($i=11;$i<=12;$i++) {
        if (trim(${'met_jour'.$j}[$i]) != "N/A") {
        if (trad("MODMET_VIT") == "K") $aff_met[$i] = (round(${'met_jour'.$j}[$i]*1.609))." km/h";
        elseif (trad("MODMET_VIT") == "M") $aff_met[$i] = ${'met_jour'.$j}[$i]." miles/h";
        }
        else
        $aff_met[$i] = "N/A";
      }      
      $matin_p = $aff_met[7];
      $soir_p = $aff_met[8];       
      $matin_h = $aff_met[9];
      $soir_h = $aff_met[10];
      $matin_vent = $aff_met[11];
      $soir_vent = $aff_met[12];
      $ico_met = $ico_mat;
      if (trim(date("M j",$localTime)) == trim($sel_jour)) {
        $heure_meteo = date("H",$localTime);
        if ($heure_meteo>=$suns_h)
          $ico_met = $ico_soir;
      }
      return "<img width=\"27\" height=\"15\" align=\"absmiddle\" src=\"image/meteo/".$ico_met."s.png\"  onmouseover=\"javascript: dtc('&nbsp;".$met_nom_ville."','<i>(".trad("MODMET_REL")." : ".$met_releve.")</i>','".addslashes("<div align='left'>".$sel_jour."&nbsp;(<B>".trad("MODMET_MIN")."/".trad("MODMET_MAX")."</B> : ".$low."/".$hi.")<br><i>".trad("MODMET_LEVE")." : ".$sunr.", ".trad("MODMET_COUCHE")." : ".$suns."</i></div><br><table align='center' width='280' border='1' bgcolor='".$bgColor[0]."' style='border-color:".$AgendaPopupBordure."'><tr align='center'><td width='50%'><B>".trad("MODMET_JOUR")."</B><br><img align='absmiddle' src='image/meteo/".$ico_mat.".png'></td><td width='50%'><B>".trad("MODMET_SOIR")."</B><br><img align='absmiddle' src='image/meteo/".$ico_soir.".png'></td></tr><tr align='center'><td colspan='2'><B>".trad("MODMET_PREC")."</B></td></tr><tr align='center'><td width='50%'>".$matin_p."</td><td>".$soir_p."</td></tr><tr align='center'><td colspan='2'><B>".trad("MODMET_TAUXH")."</B></td></tr><tr align='center'><td width='50%'>".$matin_h."</td><td>".$soir_h."</td></tr><tr align='center'><td colspan='2'><B>".trad("MODMET_VENT")."</B></td></tr><tr align='center'><td width='50%'>".$matin_vent."</td><td>".$soir_vent."</td></tr></table><br>")."'); return false;\" onmouseout=\"javascript: nd(); return true;\">";
    }
    }  
  }  
    if (($code_ville[1]) && ($err !="")) return "&nbsp;&nbsp;<img width=\"11\" title=\"".$err."\" height=\"11\" align=\"absmiddle\" src=\"image/meteo/pb_connexion_s.png\">";  
  }
// fin mod meteo
?>
