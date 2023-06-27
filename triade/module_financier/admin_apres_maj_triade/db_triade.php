<!--/****** APRES_MAJ_TRIADE_AUTO - 20110610163605 - IGONE : CODE AJOUTE AUTOMATIQUEMENT PAR SCRIPT 'admin_apres_maj_triade' ****** -->
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
?>