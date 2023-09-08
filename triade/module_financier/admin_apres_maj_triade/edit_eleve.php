		<!--/****** APRES_MAJ_TRIADE_AUTO - 20110610163605 - IGONE : CODE AJOUTE AUTOMATIQUEMENT PAR SCRIPT 'admin_apres_maj_triade' ****** -->
<div align=center>	
	<table border="0" cellpadding="0" cellspacing="5" align="center">
     	<tr>
        	<td align="center">
				<input type=button value="<?php print LANGBT52?>" onClick="open('modif_eleve.php?eid=<?php print $data[0][0]?>','_parent','')"  class="bouton2"  >
        	</td>
        </tr>

    	<tr>
        	<td align="center">
				<input type=button onClick="open('module_financier/rib_editer.php?elev_id=<?php print $eid;?>','pass','width=550,height=320')" value='<?php print "Editer le RIB" ?>' class="bouton2" >
        	</td>
        </tr>

    	<tr>
        	<td align="center">
                <table border="0" cellpadding="0" cellspacing="0" align=center">
                	<?php
					$sql_insc ="SELECT i.inscription_id, i.annee_scolaire, c.code_class, c.libelle ";
					$sql_insc.="FROM " . PREFIXE . "fin_inscriptions i INNER JOIN " . PREFIXE . "classes c ON i.code_class = c.code_class ";
					$sql_insc.="WHERE i.elev_id = " . $eid . " ";
					$sql_insc.="ORDER BY i.annee_scolaire ASC, c.libelle ASC";
					//echo $sql_insc;
					$res_insc=execSql($sql_insc);
					

					?>
                
                	<?php
					if($res_insc->numRows() > 0) {
					?>
                    <tr>
                        <td>
                        	<select name="inscription_id_insc" id="inscription_id_insc">
                            <?php
							for($i=0; $i<$res_insc->numRows(); $i++) {
								$ligne_insc = &$res_insc->fetchRow();
								$selected = '';
								if($i == 0) {
									$selected = 'selected';
								}
							?>
                            	<option value="<?php echo $ligne_insc[0]; ?>" <?php echo $selected; ?>><?php echo $ligne_insc[1]; ?> - <?php echo $ligne_insc[3]; ?></option>
                            <?php
							}
							?>
                            </select>
                        </td>
                        <td>&nbsp;&nbsp;&nbsp;</td>
                        <td>
							<input type=button onClick="aller_a_inscription_editer()" value='<?php print "Editer inscription" ?>' class="bouton2" >
                		</td>
                   </tr>
                	<?php
					} else {
					?>
                    <tr>
                        <td>
							<input type=button onClick="eleve_pas_inscrit()" value='<?php print "Nouvelle inscription" ?>' class="bouton2" >
                		</td>
                   </tr>
                	<?php
					}
				
				   ?>
				   
            	</table>
        	</td>
        </tr>
			
		<script language="javascript">
			function aller_a_inscription_editer() {
				document.for_inscription_editer.inscription_id.value = document.getElementById('inscription_id_insc').options[document.getElementById('inscription_id_insc').selectedIndex].value;
				//alert(document.getElementById('inscription_id_insc').options[document.getElementById('inscription_id_insc').selectedIndex].value);
				document.for_inscription_editer.submit();
			}
			function eleve_pas_inscrit() {
				document.inscription_dupliquer_echeancier.submit();
			}
			
			function getInactifEleve($eid) {
			global $cnx;
			global $prefixe;
			$sql="SELECT compte_inactif FROM ${prefixe}eleves WHERE elev_id='$eid'";
			$res=execSql($sql);
			$data=ChargeMat($res);
			return $data[0][0];
			}

			function inactifEleve($eid,$inactif) {
			global $cnx;
			global $prefixe;
			$sql="UPDATE ${prefixe}eleves SET compte_inactif='$inactif'  WHERE elev_id='$eid'";
			return(execSql($sql));
			}
		</script>
        <form name="for_inscription_editer" id="for_inscription_editer" method="post" action="module_financier/inscription_editer.php">
        	<input type="hidden" name="inscription_id" id="inscription_id" value="0">
         	<input type="hidden" name="elev_id" id="elev_id" value="<?php echo $eid; ?>">
        	<input type="hidden" name="appelant" id="appelant" value="edit_eleve">
       </form>
	   
	   <form name="inscription_dupliquer_echeancier" id="inscription_dupliquer_echeancier" method="post" action="module_financier/inscription_dupliquer_echeancier.php">
         	<input type="hidden" name="elev_id" id="elev_id" value="<?php echo $eid; ?>">
       </form>

    	<tr>
        	<td align="center">
                <input type=button value="<?php print "Réserver une chambre"?>" onClick="open('module_chambres/reservation_liste.php?eleve_id_forcer=<?php print $eid;?>&batiment_id_forcer=0&chambre_id_forcer=0&date_debut_forcer=null&date_fin_forcer=null','_parent','')"  class="bouton2"  >
        	</td>
        </tr>
    	<tr>
        	<td>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        	</td>
 </tr>
	<!--***************************************************************************-->
<?php
if (isset($_GET["val"])) { inactifEleve($_GET["eid"],$_GET["val"]); }
$inactif=getInactifEleve($data[0][0]);

if ($inactif == "1") {
	$bouton="Débloquer ce compte";
	$inactifval="0";
	$img="<font id='color2'><img src='image/commun/warning2.gif' align='center' /><b>COMPTE BLOQUE</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font>";
}else{
	$bouton="Bloquer ce compte";
	$inactifval="1";
}


print $img; ?>
<tr><td align="center"><input type=button value="<?php print $bouton ?>" onclick="open('edit_eleve.php?eid=<?php print $data[0][0]?>&val=<?php print $inactifval?>','_parent','')"  class="bouton2"  ></td></tr>
</table>
</div>
</td></tr>	

