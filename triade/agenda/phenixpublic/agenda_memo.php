<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5.5_Aide.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_MemoProgress_plus.txt ?>
<?php
  /**************************************************************************\
  * Phenix Agenda                                                            *
  * http://phenix.gapi.fr                                                    *
  * Written by    Stephane TEIL            <phenix-agenda@laposte.net>       *
  * Contributors  Christian AUDEON (Omega) <christian.audeon@gmail.com>      *
  *               Maxime CORMAU (MaxWho17) <maxwho17@free.fr>                *
  *               Mathieu RUE (Frognico)   <matt_rue@yahoo.fr>               *
  *               Bernard CHAIX (Berni69)  <ber123456@free.fr>               *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
  // Mod Aide
  // Fichier d'aide contextuel
  ?> <SCRIPT> HelpPhenixCtx="{B66BF351-A5A0-4634-A2B6-228C1885D372}.htm"; </SCRIPT> <?php
  // Mod Aide

  $id += 0;
  $ztAction = "INSERT";
  $titrePage = trad("MEMO_TITRE_ENREG");
  $createur = $idUser;
  if ($id) {
    // Edition d'un memo
	  //debut mod MemProg
    $DB_CX->DbQuery("SELECT mem_titre, mem_contenu, mem_partage, mem_progress, mem_util_id, mem_pcent FROM ${PREFIX_TABLE}memo WHERE mem_id=".$id." AND (mem_util_id=".$idUser." OR mem_partage='O')");
    //fin mod MemProg
    if ($enr = $DB_CX->DbNextRow()) {
      $titre = $enr['mem_titre'];
      $contenu = $enr['mem_contenu'];
      $ckPartage = $enr['mem_partage'];
      $createur = $enr['mem_util_id'];
	    //Mod MemoProgress
	    $percent = $enr['mem_pcent'];
      $ckProgress = $enr['mem_progress'];
	    //fin Mod MemoProgress
      $ztAction = "UPDATE";
      $titrePage = trad("MEMO_TITRE_MODIF");
      if ($createur!=$idUser) {
        $titrePage .= " ".trad("MEMO_TITRE_PARTAGE");
      }
    } else  {
      $id = 0;
    }
  }
  if ($ckProgress=="") $ckProgress="O";
?>
<!-- MODULE MEMO -->
  <SCRIPT language="JavaScript" type="text/javascript">
  <!--
    // MOD MemoProgress
	function MemoProgress(memid,val) {
	  if((memid != '') && (val != '')) {
		  texte = file('memo_progress.php?sid=<?php echo $sid;?>&memid='+memid+'&memval='+val);
		  writediv(memid,texte);
	  }
	}
	function writediv(id,texte) {
	  document.getElementById(id).innerHTML = texte;
	}
	function file(fichier) {
	  if (window.XMLHttpRequest) // FIREFOX
		xhr_object = new XMLHttpRequest();
	  else if (window.ActiveXObject) // IE
		xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
	  else
		return(false);
	  xhr_object.open("GET", fichier, false);
	  xhr_object.send(null);
	  if (xhr_object.readyState == 4) 
        return(xhr_object.responseText);
	  else 
        return(false);
	}	
    // Fin MOD MemoProgress
    //Saisie d'un memo
    function saisieOK(theForm) {
      if (trim(theForm.ztTitre.value) == "") {
        window.alert("<?php echo trad("MEMO_ALERTE_TITRE");?>");
        theForm.ztTitre.focus();
        return (false);
      }

      PrepareSave();
      theForm.submit();
      return (true);
    }
    //Active/Desactive le choix de partage si le memo est affecte a un autre utilisateur
    function autorisePartage(_val) {
      if (document.FormMemo.ckPartage != null) {
        if (_val!='<?php echo $idUser; ?>') {
          document.FormMemo.ckPartage.checked = false;
          document.FormMemo.ckPartage.disabled = true;
        } else {
          document.FormMemo.ckPartage.disabled = false;
        }
      }
    }
  //-->
  </SCRIPT>
  <TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
  <TR>
    <TD height="28" class="sousMenu"><?php echo $titrePage; ?></TD>
  </TR>
  </TABLE>
  <BR>
  <FORM action="agenda_traitement.php" method="post" name="FormMemo">
    <INPUT type="hidden" name="sid" value="<?php echo $sid; ?>">
    <INPUT type="hidden" name="sd" value="<?php echo date("Y-n-j", $sd); ?>">
    <INPUT type="hidden" name="id" value="<?php echo $id; ?>">
    <INPUT type="hidden" name="ztFrom" value="memo">
    <INPUT type="hidden" name="ztAction" value="<?php echo $ztAction; ?>">
    <INPUT type="hidden" name="tcMenu" value="<?php echo $tcMenu; ?>">
    <INPUT type="hidden" name="tcPlg" value="<?php echo $tcPlg; ?>">
<?php
  if ($createur!=$idUser) {
    echo ("    <INPUT type=\"hidden\" name=\"ckPartage\" value=\"O\">
    <INPUT type=\"hidden\" name=\"zlUtilisateur\" value=\"".$createur."\">
    <TABLE border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"600\">\n");
  } else {
    $DB_CX->DbQuery("SELECT DISTINCT util_id, CONCAT(".$FORMAT_NOM_UTIL.") AS nomUtil FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}planning_affecte ON paf_util_id=util_id WHERE util_id=".$idUser." OR (util_autorise_affect ='1') OR (util_autorise_affect IN ('2','3') AND paf_consultant_id=".$idUser.") ORDER BY nomUtil");
    if ($DB_CX->DbNumRows() == 1) {
      echo "    <INPUT type=\"hidden\" name=\"zlUtilisateur\" value=\"".$idUser."\">\n";
    }
    echo "    <TABLE border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"600\">\n";
    if ($DB_CX->DbNumRows()>1) {
      echo ("    <TR bgcolor=\"".$bgColor[1]."\">
      <TD class=\"tabIntitule\" nowrap>".trad("MEMO_PERSONNE_CONCERNEE")."&nbsp;</TD>
      <TD class=\"tabInput\"><SELECT name=\"zlUtilisateur\" id=\"zlUtilisateur\" size=\"1\" onchange=\"javascript: autorisePartage(this.value);\">\n");
      while ($rsUtil = $DB_CX->DbNextRow()) {
        $selected = ($idUser == $rsUtil['util_id']) ? " selected" : "";
        echo "        <OPTION value=\"".$rsUtil['util_id']."\"".$selected.">".$rsUtil['nomUtil']."</OPTION>\n";
      }
      echo ("      </SELECT></TD>
    </TR>\n");
    }
  }
?>
    <TR bgcolor="<?php echo $bgColor[0]; ?>">
      <TD class="tabIntitule"><?php echo trad("MEMO_TITRE");?></TD>
      <TD class="tabInput" nowrap height="21" width="471"><INPUT type="text" class="Texte" name="ztTitre" value="<?php echo htmlspecialchars(stripslashes($titre)); ?>" style="width:469px" maxlength="150"></TD>
    </TR>
    <TR bgcolor="<?php echo $bgColor[1]; ?>">
      <TD class="tabIntitule"><?php echo trad("MEMO_CONTENU");?>&nbsp;</TD>
      <TD class="tabInput" nowrap><?php genereTextArea("ztContenu",$contenu,469,7); ?></TD>
    </TR>
<?php if ($createur==$idUser) { ?>
    <TR bgcolor="<?php echo $bgColor[0]; ?>" height="21">
      <TD class="tabIntitule" nowrap><?php echo trad("MEMO_LIB_PARTAGE");?></TD>
      <TD class="tabInput" nowrap><LABEL for="partageMemo"><INPUT type="checkbox" name="ckPartage" id="partageMemo" value="O" class="Case"<?php if ($ckPartage=='O') {echo " checked";} ?>>&nbsp;<?php echo trad("MEMO_COCHER_PARTAGE");?></LABEL></TD>
    </TR>
<?php } ?>
    <TR bgcolor="<?php echo $bgColor[1]; ?>" height="21">
      <TD class="tabIntitule" nowrap><?php echo trad("MODMEMPROG_PROGRESSION");?></TD>
      <TD class="tabInput" nowrap><LABEL for="progressMemo"><INPUT type="checkbox" name="ckProgress" id="progressMemo" value="O" class="Case" <?php if ($ckProgress=='O') {echo " checked";} ?>>&nbsp;<?php echo trad("MODMEMPROG_TEXTE");?></LABEL></TD>
    </TR>
    </TABLE>
    <BR><INPUT type="button" name="btEnregistre" value="<?php echo trad("MEMO_BT_ENREGISTRER");?>" onClick="javascript: return saisieOK(document.FormMemo);" class="bouton">&nbsp;&nbsp;&nbsp;<INPUT type="button" name="btAnnule" value="<?php echo trad("MEMO_BT_ANNULER");?>" onclick="javascript: btAnnul();" class="bouton"><?php if ($ztAction == "UPDATE") { ?>&nbsp;&nbsp;&nbsp;<INPUT type="button" name="btSupprime" value="<?php echo trad("MEMO_BT_SUPPRIMER");?>" onclick="javascript: if (confirm('<?php echo trad("MEMO_ALERTE_SUP");?>')) { document.FormMemo.ztAction.value='DELETE'; document.FormMemo.submit(); }" class="Bouton"><?php } ?>
  </FORM>
<?php
  //Liste des differents memos
  // MOD MemoProgress
	$DB_CX->DbQuery("SELECT mem_id, mem_titre, mem_contenu, mem_progress, mem_util_id, mem_pcent, mem_date FROM ${PREFIX_TABLE}memo WHERE mem_util_id=".$idUser." OR mem_partage='O' ORDER BY mem_id ASC");
  // Fin MOD MemoProgress
  if ($DB_CX->DbNumRows()) {
    echo ("  <BR><TABLE width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n");
    $index = 0;
    while ($enr = $DB_CX->DbNextRow()) {
      $index = 1 - $index;
    // MOD MemoProgress
	$memid = $enr['mem_id'];
	$barre = nlTObr($enr['mem_pcent'])*1;
	$barre2 = 100-$barre;
	$valpercent = 20;
	if ( $enr['mem_pcent'] <= $valpercent ) {
		$mem_pcent_reduire = 0;
		$mem_pcent_augmenter = $enr['mem_pcent']+$valpercent;
		} elseif (( $enr['mem_pcent'] > $valpercent ) & ( $enr['mem_pcent'] < (100-$valpercent ) )) {
			$mem_pcent_reduire = $enr['mem_pcent']-$valpercent;
			$mem_pcent_augmenter = $enr['mem_pcent']+$valpercent;
			} elseif ( $enr['mem_pcent'] >= ( 100-$valpercent )) {
				$mem_pcent_reduire = $enr['mem_pcent']-$valpercent;
				$mem_pcent_augmenter = 100;
				}
    // Fin MOD MemoProgress
    // MOD MemoProgress
	if  ($enr['mem_progress']=="O") {
	if ($enr['mem_date']!="") $mem_date = "&nbsp;".trad("MODMEMPROG_DATE")."&nbsp;".$enr['mem_date'];
	echo ("    <TR bgcolor=\"".$bgColor[$index]."\" height=\"25\">");
	echo ("   <TD width=\"400\" valign=\"middle\" class=\"bordTLR\"><B>".$enr['mem_titre']."</B>".$mem_date."</TD>
	<td valign=\"middle\" class=\"bordTB\">
	<div id=".nlTObr($enr['mem_id']).">
	<input type=button onclick=\"MemoProgress('$memid','$mem_pcent_reduire');\" value='-' style='height: 15px; width: 15px; FONT-SIZE: 15px; FONT-WEIGHT: bold; BORDER: 0px; BACKGROUND-COLOR:transparent;'>	
	<img src='image/barre-vert.gif' width='$barre' height='7px'><img src='image/barre-rouge.gif' width='$barre2' height='7px'>
	<input type=button onclick=\"MemoProgress('$memid','$mem_pcent_augmenter');\" value='+' style='height: 15px; width: 15px; FONT-SIZE: 15px; FONT-WEIGHT: bold; BORDER: 0px; BACKGROUND-COLOR:transparent;'>
	</div>
	</td>");
	echo ("  <TD class=\"bordTRB\" valign=\"middle\" align=\"center\" nowrap>");
	echo "<form>&nbsp;";
      if ($enr['mem_util_id']==$idUser || $MODIF_PARTAGE) { // Modif du memo
        echo "<INPUT type=\"button\" class=\"bouton\" name=\"btModif\" value=\"".trad("MEMO_M")."\" title=\"".trad("MEMO_BT_MODIFIER")."\" style=\"width:16px\" onclick=\"javascript: window.location.href='?id=".$enr['mem_id']."&tcType="._TYPE_MEMO."&sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."';\">&nbsp;";
      }
      if ($enr['mem_util_id']==$idUser) { // Suppression du memo
        echo "<INPUT type=\"button\" class=\"bouton\" name=\"btSuppr\" value=\"".trad("MEMO_S")."\" title=\"".trad("MEMO_BT_SUPPRIMER")."\" style=\"width:16px\" onclick=\"javascript: if (confirm('".trad("MEMO_ALERTE_SUP")."')) window.location.href='agenda_traitement.php?ztFrom=memo&ztAction=DELETE&id=".$enr['mem_id']."&sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".date("Y-n-j", $sd)."';\">&nbsp;";
      }
      echo ("</form></TD>
    </TR>
    <TR bgcolor=\"".$bgColor[$index]."\">
	<TD colspan=\"5\" class=\"bordLRB\">".nlTObr($enr['mem_contenu'])."</TD>
    </TR>\n");
    } else {
      echo ("    <TR bgcolor=\"".$bgColor[$index]."\">
      <TD colspan=\"2\" width=\"420\" class=\"bordTL\"><B>".$enr['mem_titre']."</B></TD>
      <TD width=\"45\" class=\"bordTR\" nowrap>&nbsp;");
    // Fin MOD MemoProgress
      if ($enr['mem_util_id']==$idUser || $MODIF_PARTAGE) { // Modif du memo
        echo "<INPUT type=\"button\" class=\"bouton\" name=\"btModif\" value=\"".trad("MEMO_M")."\" title=\"".trad("MEMO_BT_MODIFIER")."\" style=\"width:16px\" onclick=\"javascript: window.location.href='?id=".$enr['mem_id']."&tcType="._TYPE_MEMO."&sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."';\">&nbsp;";
      }
      if ($enr['mem_util_id']==$idUser) { // Suppression du memo
        echo "<INPUT type=\"button\" class=\"bouton\" name=\"btSuppr\" value=\"".trad("MEMO_S")."\" title=\"".trad("MEMO_BT_SUPPRIMER")."\" style=\"width:16px\" onclick=\"javascript: if (confirm('".trad("MEMO_ALERTE_SUP")."')) window.location.href='agenda_traitement.php?ztFrom=memo&ztAction=DELETE&id=".$enr['mem_id']."&sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".date("Y-n-j", $sd)."';\">&nbsp;";
      }
      echo ("</form></TD>
    </TR>
    <TR bgcolor=\"".$bgColor[$index]."\">
	<TD colspan=\"5\" class=\"bordLRB\">".nlTObr($enr['mem_contenu'])."</TD>
    </TR>\n");
    }
    }
    echo ("</TABLE>");
  }

  if (!$id) {
    echo ("  <SCRIPT type=\"text/javascript\">
  <!--
    document.FormMemo.ztTitre.focus();
  //-->
  </SCRIPT>\n");
  }
?>
<!-- FIN MODULE MEMO -->
