<script type="text/javascript" src="./librairie_js/allo.js"></script>
<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
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

function popupfen($idpers,$membre,$nom,$prenom,$ip,$os,$nav,$idsession) {
	$okaff=0;
	$texte="";

	include_once("./common/config2.inc.php");
	include_once("./common/config-module.php");

	if ($membre == "menuadmin")     {$type_personne="ADM";}
	if ($membre == "menuprof")      {$type_personne="ENS";}
	if ($membre == "menuscolaire")  {$type_personne="MVS";}
	if ($membre == "menuparent")    {$type_personne="PAR";}
	if ($membre == "menueleve")     {$type_personne="ELE";}
	if ($membre == "menupersonnel") {$type_personne="PER";}

	$compteur_mess=0;
	$son=0;
	$data=affichage_messagerie($type_personne,$idpers,"");
	for($i=0;$i<count($data);$i++) {
		if ($data[$i][10] != "1") {
			$okaff=1;
			$son=1;
			$compteur_mess++;
		}
	}

	if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
		$datasanc=recherche_sanction_du_jour();
		$nbsanc=count($datasanc);
		if ($nbsanc > 0) { $son=1; }
	}

	$dernierconnex=affichage_derniereconx($type_personne,$idpers);

	$largeurFen=375;
	$hauteurFen=130;

	$nom_prenom=$nom." ".ucwords($prenom);
	$titre=trunchaine($nom_prenom,23)." ".$dernierconnex;

	$alertPaiement=0;

	$colortext2="#000000";
	if ((GRAPH == 25) || (GRAPH == 26) || (GRAPH == 27) || (GRAPH == 28)) $colortext2="#FFFFFF";
	if (GRAPH == 28) $colortext2="#000000";

	/* ── Styles inline réutilisables ── */
	$cellStyle  = 'padding:8px 10px;text-align:center;vertical-align:middle';
	$numStyle   = 'font-size:22px;font-weight:700;color:#080A66;display:block;line-height:1.1';
	$lblStyle   = 'font-size:9px;color:#666;display:block;margin-top:2px;text-transform:uppercase;letter-spacing:.4px';
	$sepStyle   = 'border-left:1px solid #c5cae9;height:50px;padding:0';
	$numAlerte  = 'font-size:22px;font-weight:700;color:#c62828;display:block;line-height:1.1';

	if (($membre == "menuparent") || ($membre == "menueleve")) {
		$ideleve=$idpers;
		$idclasse=chercheClasseEleve($ideleve);
		$dataV=recupConfigVersement($idclasse);
		if ($dataV == "") { $dataV=array(); }
		$dataVE=recupConfigVersementEleve($ideleve);
		if ($dataVE == "") { $dataVE=array(); }
		$dataV=array_merge($dataV,$dataVE);
		for($j=0;$j<count($dataV);$j++) {
			$id=$dataV[$j][0];
			if (verifcomptaExclu($id,$ideleve)) { continue; }
			$data=recupInfoVersement($ideleve,$id);
			$dateVersement=$data[0][3];
			$idvers=$data[0][1];
			if ($dateVersement != "") { $dateVersement=dateForm($dateVersement); }
			$montantVers=number_format($data[0][2],2,'.','');
			$modepaiement=nl2br($data[0][4]);
			$dateVersOr=$dataV[$j][4];
			$montantavers=$dataV[$j][3];

			$dateduJour=date("Ymd");
			$dateVersOr=preg_replace('/-/',"",$dateVersOr);
			$dateVersOr=$dateVersOr+4;

			if (($montantVers == "0.00") && ($dateduJour > $dateVersOr)) {
				$alertPaiement=1;
			}

			if (($montantVers < $dataV[$j][3] ) && ($dateduJour > $dateVersOr) && ($montantVers != "0.00") ) {
				$alertPaiement=1;
			}
			$dateVersement=preg_replace('/-/',"",dateFormBase($dateVersement));
			$nbj=$dateVersement-$dateduJour;
			if  (($nbj > 0) && ($nbj <= 7)) {
				$alertPaiement=1;
			}
		}
	}

	$messagerieVisu=0;
	if (($_SESSION["membre"] == "menuprof")      && (MODULEMESSAGERIEPROF == "oui"))      $messagerieVisu=1;
	if (($_SESSION["membre"] == "menuadmin")     && (MODULEMESSAGERIEADMIN == "oui"))     $messagerieVisu=1;
	if (($_SESSION["membre"] == "menueleve")     && (MODULEMESSAGERIEELEVE == "oui"))     $messagerieVisu=1;
	if (($_SESSION["membre"] == "menuparent")    && (MODULEMESSAGERIEPARENT == "oui"))    $messagerieVisu=1;
	if (($_SESSION["membre"] == "menututeur")    && (MODULEMESSAGERIETUTEUR == "oui"))    $messagerieVisu=1;
	if (($_SESSION["membre"] == "menuscolaire")  && (MODULEMESSAGERIESCOLAIRE == "oui"))  $messagerieVisu=1;
	if (($_SESSION["membre"] == "menupersonnel") && (MODULEMESSAGERIESCOLAIRE == "oui"))  $messagerieVisu=1;

	if ((LAN == "oui") && (AGENTWEB == "oui") && ($messagerieVisu)) {
		if ($compteur_mess == 1) { $M="M7"; }
		if ($compteur_mess > 1)  { $M="M8"; }
	}

	/* ── Construction du contenu ── */
	$texte  = '<table role="presentation" border=0 cellpadding=0 cellspacing=0 style="font-family:Arial,sans-serif;border-collapse:collapse"><tr>';

	if ($messagerieVisu) {
		$texte .= '<td style="'.$cellStyle.'">'.
			'<span style="'.($compteur_mess > 0 ? $numStyle : $numStyle.';opacity:.4').'">'.$compteur_mess.'</span>'.
			'<img src="./image/commun/email.gif" alt="" aria-hidden="true" border="0" style="margin:3px 0 1px"><br>'.
			'<span style="'.$lblStyle.'">Message(s)</span>'.
			'</td>';
		$texte .= '<td style="'.$sepStyle.'"></td>';
	}

	// flux rss
	include_once("./librairie_php/verifRss.php");
	$nb=0;
	$nb=verifRss($idpers,$membre);
	$texte .= '<td style="'.$cellStyle.'">'.
		'<span style="'.($nb > 0 ? $numStyle : $numStyle.';opacity:.4').'">'.$nb.'</span>'.
		'<img src="./image/commun/rss-icon.gif" alt="" aria-hidden="true" border="0" style="margin:3px 0 1px"><br>'.
		'<span style="'.$lblStyle.'">RSS</span>'.
		'</td>';

	if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
		$texte .= '<td style="'.$sepStyle.'"></td>';
		$texte .= '<td style="'.$cellStyle.'">'.
			'<span style="'.($nbsanc > 0 ? $numAlerte : $numStyle.';opacity:.4').'">'.$nbsanc.'</span>'.
			'<img src="./image/commun/enable.png" alt="" aria-hidden="true" border="0" style="margin:3px 0 1px"><br>'.
			'<span style="'.$lblStyle.'">'.LANGNEW100.'</span>'.
			'</td>';
	}

	if (($membre == "menuparent") || ($membre == "menueleve")) {
		$ideleve=$idpers;
		$idclasse=chercheClasseEleve($ideleve);
		$dataV=recupConfigVersement($idclasse);
		if ($dataV == "") { $dataV=array(); }
		$dataVE=recupConfigVersementEleve($ideleve);
		if ($dataVE == "") { $dataVE=array(); }
		$dataV=array_merge($dataV,$dataVE);
		for($j=0;$j<count($dataV);$j++) {
			$id=$dataV[$j][0];
			if (verifcomptaExclu($id,$ideleve)) { continue; }
			$data=recupInfoVersement($ideleve,$id);
			$dateVersement=$data[0][3];
			$idvers=$data[0][1];
			if ($dateVersement != "") { $dateVersement=dateForm($dateVersement); }
			$montantVers=number_format($data[0][2],2,'.','');
			$modepaiement=nl2br($data[0][4]);
			$dateVersOr=$dataV[$j][4];
			$montantavers=$dataV[$j][3];

			$dateduJour=date("Ymd");
			$dateVersOr=preg_replace('/-/',"",$dateVersOr);
			if (($montantVers == "0.00") && ($dateduJour > $dateVersOr)) {
				$okaff="1";
				$paiement1 = '<td style="'.$sepStyle.'"></td>'.
					'<td style="'.$cellStyle.'">'.
					'<span style="'.$numAlerte.'">!</span>'.
					'<img src="./image/commun/important.png" border="0" style="margin:3px 0 1px"><br>'.
					'<span style="'.$lblStyle.'">Paiement(s) en retard</span>'.
					'</td>';
				$largeurFen=$largeurFen+90;
			}

			if (($montantVers < $dataV[$j][3]) && ($dateduJour > $dateVersOr) && ($montantVers != "0.00")) {
				$okaff="1";
				$paiement = '<td style="'.$sepStyle.'"></td>'.
					'<td style="'.$cellStyle.'">'.
					'<span style="'.$numAlerte.'">!</span>'.
					'<img src="./image/commun/warning.gif" border="0" style="margin:3px 0 1px"><br>'.
					'<span style="'.$lblStyle.'">Paiement(s) incomplet</span>'.
					'</td>';
				$largeurFen=$largeurFen+90;
			}

			$dateVersement=preg_replace('/-/',"",dateFormBase($dateVersement));
			$nbj=$dateVersement-$dateduJour;
			if (($nbj > 0) && ($nbj <= 7)) {
				$okaff="1";
				$paiement2 = '<td style="'.$sepStyle.'"></td>'.
					'<td style="'.$cellStyle.'">'.
					'<span style="'.$numStyle.'">'.$nbj.'</span>'.
					'<img src="./image/commun/antenne.gif" border="0" style="margin:3px 0 1px"><br>'.
					'<span style="'.$lblStyle.'">J. avant encaissement</span>'.
					'</td>';
				$largeurFen=$largeurFen+90;
			}
		}
		$texte .= isset($paiement1) ? $paiement1 : '';
		$texte .= isset($paiement)  ? $paiement  : '';
		$texte .= isset($paiement2) ? $paiement2 : '';
	}

	if ($membre == "menuadmin") {
		$dateJ=date("d/m/Y");
		$data=chercheVersement($dateJ);
		if (count($data) > 0) {
			$okaff="1";
			$texte .= '<td style="'.$sepStyle.'"></td>'.
				'<td style="'.$cellStyle.'">'.
				'<span style="'.$numStyle.'">'.count($data).'</span>'.
				'<img src="./image/commun/antenne.gif" border="0" style="margin:3px 0 1px"><br>'.
				'<span style="'.$lblStyle.'">Encaissement(s)</span>'.
				'</td>';
			$largeurFen=$largeurFen+100;
		}
	}

	$texte .= '</tr></table>';

	if (LAN == "oui") {
		$hauteurFen=$hauteurFen-27;
	}

	if (isset($_GET["id"])) {
		$okaff=1;
	}

 if ($okaff == 1) {
?>

	 <script type="text/javascript">
// id, title, width, height, x , y , isdraggable, boxcolor, barcolor, shadowcolor, text, textcolor, textptsize, textfamily, titlecolor
<?php  if (GRAPH == 0) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#B2CADE' , '#506E87', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 1) {?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#FCE4BA' , '#666666', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 2) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#CC9999' , '#CC6666', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 3) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#99FF66' , '#66CC66', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 4) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#FFE664' , '#F1B100', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 5) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#E3F75E' , '#99CC66', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 6) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#FF9797' , '#7A0000', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 7) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#F6F5F4' , '#52251C', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 8) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#cba3c6' , '#91127d', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 15) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#cba3c6' , '#91127d', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 9) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#EAE8E3' , '#8CC0D8', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 10) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#94C11F' , '#739618', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 11) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#FFFFFB' , '#CCCCCC', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#000000');
<?php }elseif(GRAPH == 13) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#E3F75E' , '#F98F3C', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 14) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#506E87' , '#3576B4', 'white' ,  '<?php print $texte?>' , '#FFFFFF',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 16) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#cbcbca' , '#bd2d24', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 17) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#9d9d98' , '#C63f36', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 18) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#9d9d98' , '#c90128', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 19) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#b9b9b9' , '#000000', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 20) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#E1E2E2' , '#FF7F00', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 21) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#E1E2E2' , '#E20879', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 22) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#cba3c6' , '#91127d', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 23) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#BAD064' , '#9DB539', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 24) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#C5A8C5' , '#660066', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 25) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#0075b5' , '#414042', 'black' ,  '<?php print $texte?>' , '#FFFFFF',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 26) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#D9EDF7' , '#031259', 'black' ,  '<?php print $texte?>' , '#FFFFFF',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 27) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#F6F5F4' , '#9A5361', 'black' ,  '<?php print $texte?>' , '#FFFFFF',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 28) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#F3F4F4' , '#C5E875', 'black' ,  '<?php print $texte?>' , '#FFFFFF',8,'Arial','#000000');
<?php }elseif(GRAPH == 29) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#F6F5F4' , '#2D466D', 'black' ,  '<?php print $texte?>' , '#FFFFFF',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 30) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#F6F5F4' , '#e95f1e', 'black' ,  '<?php print $texte?>' , '#FFFFFF',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 31) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#EEEEEE' , '#67546e', 'black' ,  '<?php print $texte?>' , '#FFFFFF',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 32) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#F6F5F4' , '#642424', 'black' ,  '<?php print $texte?>' , '#FFFFFF',8,'Arial','#FFFFFF');
<?php }elseif(GRAPH == 33) { ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#f8f9ff' , '#080A66', 'rgba(8,10,102,.07)' ,  '<?php print $texte?>' , '#080A66',8,'Arial','#fff','#000000');
<?php }else{ ?>
	createPopup( 'b997', '<?php print $titre?>',<?php print $largeurFen ?>, <?php print $hauteurFen ?>, 300, 300, true,  '#cba3c6' , '#91127d', 'black' ,  '<?php print $texte?>' , '#000000',8,'Arial','#FCE4BA');
<?php } ?>
</script>
<?php
	mise_statUtilisateur($nom,$prenom,$idpers,$membre,$idsession);
	if (($son) && ($messagerieVisu)) {
		if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
			if ( ($nbsanc == 1) && ($compteur_mess == 0)) {	sonore_action("../audio/message4.mp3"); }
			if ( ($nbsanc > 1)  && ($compteur_mess == 1)) {	sonore_action("../audio/message8.mp3"); }
			if ( ($nbsanc > 1)  && ($compteur_mess > 1))  {	sonore_action("../audio/message7.mp3"); }
			if ( ($nbsanc > 1)  && ($compteur_mess == 0)) {	sonore_action("../audio/message9.mp3"); }
			if ( ($nbsanc == 1) && ($compteur_mess == 1)) {	sonore_action("../audio/message5.mp3"); }
			if ( ($nbsanc == 1) && ($compteur_mess > 1))  {	sonore_action("../audio/message6.mp3"); }
			if ( ($nbsanc == 0) && ($compteur_mess > 1))  {	sonore_action("../audio/message2.mp3"); }
			if ( ($nbsanc == 0) && ($compteur_mess == 1)) {	sonore_action("../audio/message.mp3"); }
		}else{
			if ($compteur_mess == 1) { sonore_action("../audio/message.mp3"); }
			if ($compteur_mess > 1)  { sonore_action("../audio/message2.mp3"); }
		}
	}
} // fin du if de okaff



} // fin de la fonction



?>
