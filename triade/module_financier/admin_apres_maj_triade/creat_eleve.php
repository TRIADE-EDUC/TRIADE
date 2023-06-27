				//****** APRES_MAJ_TRIADE_AUTO - [APRES_MAJ_TRIADE_AUTO_DATE_TRAITEMENT] - [APRES_MAJ_TRIADE_AUTO_ENTITE] : CODE AJOUTE AUTOMATIQUEMENT PAR SCRIPT 'admin_apres_maj_triade' ******
				// 20100512 - AP : on veut proposer la saisie du RIB une fois l'eleve cree 
				
				// Recherche de l'eleve a partir de ce qui a ete donne dans le formulaire
				$sql_eleve  = "SELECT elev_id ";
				$sql_eleve .= "FROM " . PREFIXE . "eleves ";
				$sql_eleve .= "WHERE nom = '" . $params[ne] . "' ";
				$sql_eleve .= "AND prenom = '" . $params[pe] . "' ";
				$sql_eleve .= "AND classe = " . $params[ce] . " ";
				$sql_eleve .= "AND date_naissance = '" . dateFormBase($params[naiss]) . "' ";
				//echo $sql_eleve;
				$res_eleve=execSql($sql_eleve);
				
				// on verifie si on a bien trouve le nouvel eleve
				if($res_eleve->numRows() > 0) {
					$ligne_eleve = &$res_eleve->fetchRow();
					// On demande a l'utilisateur si il veut gérer les RIB maintenant ou on
				?>
                	<script language="javascript">
						if(confirm("<?php echo LANGFIN005; ?>")) {
							open('module_financier/rib_editer.php?elev_id=<?php echo $ligne_eleve[0];?>','rib','width=550,height=320')
						}
					</script>
                <?php
				
				}
				//***************************************************************************
	