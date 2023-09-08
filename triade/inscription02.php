<?php
session_start();
if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) {
	header('Location: ./acces_refuse.php');
	exit;
}

include_once("common/config.inc.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
if (!verif_compte($_SESSION["nom"],$_SESSION["prenom"],$_SESSION["id_pers"],$_SESSION["membre"])) {
	header('Location: ./acces_depart.php');	
	PgClose();
	exit;
}
PgClose();

include_once("./librairie_php/lib_error.php");
include_once("./librairie_php/lib_licence2.php");
include_once("./librairie_php/langue.php");
include_once("./common/config2.inc.php");
$cnx=cnx();

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
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">	

<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<?php define("CHARSET","iso-8859-1"); ?>
		<meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
		<meta http-equiv="CacheControl" content="no-cache" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="expires" content="-1" />
		<meta name="Copyright" content="Triade©, 2001" />
		<link rel="SHORTCUT ICON" href="../favicon.ico" />
		<link title="style" type="text/css" rel="stylesheet" href="./librairie_css/css2.css" />
		<script type="text/javascript" src="./librairie_js/function.js"></script>
		<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
	<script language="JavaScript" src="./librairie_js/lib_type_navigateur.js"></script>
		<script language="JavaScript" src="./librairie_js/lib_type_debit.js"></script>

		<title>Triade Inscription</title>
	</head>

<body onload="Checkkos();">


<!-- "text-align: center" à cause du bug centrage d'IE :( -->
<div style="text-align: center;">
<div id="mainInst"><?php $photo=recup_photo_bulletin(); 
			if (count($photo) > 0) {
				if (file_exists("./data/image_pers/".$photo[0][0])) {
//					$logo="<img src='image.php?id=./data/image_pers/".$photo[0][0]."' >";
					$logo="<img src='./image/logo_triade_licence.gif' />";						
				}else{
					$logo="<img src='./image/logo_triade_licence.gif' />";						
				}
			}else{
				$logo="<img src='./image/logo_triade_licence.gif' />";
			}
			?>
<?php print $logo ?><br /><br />
<br />

<?php
$data2=visu_param();
for($i=0;$i<count($data2);$i++) {
       $nom_etablissement=trim($data2[$i][0]);
       $adresse=trim($data2[$i][1]);
       $postal=trim($data2[$i][2]);
       $ville=trim($data2[$i][3]);
       $tel=trim($data2[$i][4]);
       $mail=trim($data2[$i][5]);
       $directeur=trim($data2[$i][6]);
       $urlsite=trim($data2[$i][7]);
}
?>
<p>
<font class=T2>
<?php print LANGEL26 ?> : <b><?php print $nom_etablissement; ?></b><br />
<?php print LANGAGENDA63 ?>  : <?php print "$adresse, $ville "; ?> <br />
<?php print LANGMESS66 ?> : <b> <?php print $directeur ; ?> </b><br />
</font>
</p>

<img src="image/on1.gif" width="10" height="10" /> <font class=T2><?php print LANGMESS65 ?>.</font>
<br><br>
<table border="0" width="94%" align="center" >
<?php
$validation=1;
if ($_SESSION["membre"] == "menuprof") {
		$accepte=LANGMESS69;
		$data2=reglementAffProf(1);
		for($i=0;$i<count($data2);$i++) {
			$validation=0;
		?>
			<tr >
			<td valign=top align=right width="50%" >&nbsp;<?php print $data2[$i][1]?> : </td>
			<td valign=top> <script  type="text/javascript"  >buttonMagic("Lire","visu_document.php?fichier=./data/circulaire/<?php print $data2[$i][3]?>","_blank",'','')</script>	</td>
			</tr>
<?php
		}
	}

if (($_SESSION["membre"] == "menuparent") || ($_SESSION["membre"] == "menueleve")) {
		$accepte=LANGMESS68;
		$id_classe=chercheClasseEleve($_SESSION["id_pers"]);
		$data=reglementAffParent(); // id, sujet, refence, file, date, enseignant, classe
		for($i=0;$i<count($data);$i++) {
			$ok=0;
			$ligne=$data[$i][6];
		       	$ligne=substr("$ligne", 1); // retire le "{"
		       	$ligne=substr("$ligne", 0, -1); // retire le "}"
			$nbsep=substr_count("$ligne", ",");
			if ($nbsep == 0) {
				if (($id_classe == $ligne) && ($ligne != null )) { $ok=1; }
			}else {
				for ($j=0;$j<=$nbsep;$j++) {
					list ($valeur) = preg_split ('/,/', $ligne);
					if ($id_classe == $valeur) { $ok=1; }
					$ligne = stristr($ligne, ',');
					$ligne=substr("$ligne", 1);
				}
			}
			if ( $ok == 1 ) {
				$validation=0;
				print "<tr >";
				print "<td valign=top align=right width='50%' >&nbsp;".$data[$i][1]."</td>";
				print "<td valign=top><script type='text/javascript'>buttonMagic('Lire','visu_document.php?fichier=./data/circulaire/".$data[$i][3]."','_blank','','')</script></td>";
				print "</tr>";
			}
		}
	}
print "</table>";

?>
<br />
<?php print LANGMESS67 ?>. 
<form name="inscripform" method='POST' action="inscription2.php">
<input type=checkbox name="accord" value="1" onclick="document.inscripform.val.disabled=false; document.inscripform.accord.disabled=true;" /> 
<?php print LANGMESS68 ?> &nbsp;&nbsp;&nbsp;&nbsp;
<input type=submit value='<?php print ACCEPTER ?>' class=BUTTON disabled='disabled' name="val" >
<br /><br />
<!-- module stat -->
<input type=hidden name="statNomNavigateur">
<input type=hidden name="statVersion">
<input type=hidden name="statOs">
<input type=hidden name="statLangue">
<input type=hidden name="statEcran">
<input type=hidden name="statDebit" value="no test">
<script language=JavaScript>
document.inscripform.statNomNavigateur.value=nom;
document.inscripform.statVersion.value=version;
document.inscripform.statOs.value=os;
document.inscripform.statLangue.value=langue;
if (screen.width<800) {window.document.inscripform.statEcran.value="> 800";}
if (screen.width==800) {window.document.inscripform.statEcran.value="800";}
if (screen.width==1024) {window.document.inscripform.statEcran.value="> 1024";}
if (screen.width==1152) {window.document.inscripform.statEcran.value="1152";}
if (screen.width>1152) {window.document.inscripform.statEcran.value="> 1152";}
</script>
</form>
</div>	</div>	</div>

<?php
include_once("installation/librairie/pied_page.php");
if ($validation == 1) {
	print "<script>document.inscripform.submit();</script>";
}
?>

	</body>
</html>
