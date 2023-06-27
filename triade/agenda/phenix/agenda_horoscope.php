<?php
if (!isset($sid)) {
  Header("location: deconnexion.php?msg=5");
  exit;
}
  // MOD horoscope
  $err = 0;
  $signe = "";
  $DB_CX->DbQuery("SELECT util_horo FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$USER_SUBSTITUE);
  $signe = $DB_CX->DbResult(0,0);
  if ($signe) {
    $DB_CX->DbQuery("SELECT horo_detail FROM ${PREFIX_TABLE}horoscope WHERE horo_signe='".$signe."'");
  if ($DB_CX->DbResult(0,0)!=NULL) {
    $signe_detail = $DB_CX->DbResult(0,0);
    $signe_detail = str_replace("\r\n","",$signe_detail);
    //$signe_detail = str_replace("'"," ",$signe_detail);
    $signe_detail = str_replace('"','&quot;',$signe_detail);
    $signe_detail = str_replace('</center><br>','</center>',$signe_detail);
    $signe_detail = str_replace('<description>','',$signe_detail);
    $signe_detail = str_replace('<br><center>','',$signe_detail);
  }
  else $err = "Pas de donnée disponible pour ce signe";
  }
  function aff_horoscope($sel_jour) {  
  global $AgendaPopupBordure, $bgColor, $signe, $signe_detail, $err, $DB_CX;
    if (($signe) && (!$err) && (trim(date("M j")) == trim($sel_jour))) {
      $ico_horo = $signe;
        return "<img width=\"15\" height=\"15\" align=\"absmiddle\" src=\"image/horoscope/".$ico_horo.".gif\" onclick=\"javascript: stc('Horoscope du jour pour le signe : ','".$signe."','".addslashes($signe_detail)."','".trad("POPUP_FERMER")."'); sw=2; return false;\" onmouseover=\"javascript: dtc('Horoscope du jour pour le signe : ','".$signe."','".addslashes($signe_detail)."'); return false;\" onmouseout=\"javascript: nd(); return true;\">";
  }
    if (($signe) && ($err) && (trim(date("M j")) == trim($sel_jour)))
    return "&nbsp;&nbsp;<img width=\"11\" title=\"".$err."\" height=\"11\" align=\"absmiddle\" src=\"image/horoscope/pb_connexion_s.png\">";  
  }
  // fin mod horoscope
?>