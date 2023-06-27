<?php
//error_reporting(0);
include_once("./librairie_php/fonction.inc.php");
include_once("./common/config.inc.php");
$prefixe=PREFIXE;
$id=db_connect();

if (!isset($_GET["ok"])) {
	$email=$_POST["mail"];
	$passwd=$_POST["password"];
	$sql = "SELECT elev_id,nom,prenom,classe,lv1,lv2,regime,date_naissance,lieu_naissance,nationalite,passwd,passwd_eleve,civ_1,nomtuteur,prenomtuteur,adr1,code_post_adr1,commune_adr1,tel_port_1,civ_2,nom_resp_2,prenom_resp_2,adr2,code_post_adr2,commune_adr2,tel_port_2,telephone,profession_pere,tel_prof_pere,profession_mere,tel_prof_mere,nom_etablissement,numero_etablissement,code_postal_etablissement,commune_etablissement,numero_eleve,photo,email,email_eleve,email_resp_2,class_ant,annee_ant,tel_eleve,sexe,option2,decision,adr_eleve,ccp_eleve,commune_eleve,tel_fixe_eleve,pays_eleve,boursier FROM ${prefixe}preinscription_eleves WHERE email_eleve = '$email'  AND passwd_eleve='$passwd'; ";
	$res = mysqli_query($id,$sql); 
	$data = mysqli_fetch_assoc($res);
}



$nomClasse="";
if ($data['classe'] > 0) {
	$sql="SELECT code_class,libelle FROM ${prefixe}classes WHERE code_class='".$data['classe']."'";
	$res = mysqli_query($id,$sql); 
	$data2 = mysqli_fetch_assoc($res);
	$nomClasse=$data2['libelle'];
}


mysqli_close($id);
if (trim($data['nom'])!= "") {

?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<html>
<head>
   <meta http-equiv="Content-type" content = "text/html; charset=iso-8859-1" />
   <meta http-equiv="CacheControl" content = "no-cache" />
   <meta http-equiv="pragma" content = "no-cache" />
   <meta http-equiv="expires" content = -1 />
   <meta name="Copyright" content="Triade©, 2001" />
   <meta http-equiv="imagetoolbar" content="no" />
     <link rel="stylesheet" type="text/CSS" href="./librairie_css/css.css" media="screen" />
     <link rel="shortcut icon" href="./favicon.ico" type="image/icon" />
   <title>Envoi des candidatures</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
	<noscript><meta http-equiv="Refresh" content="0; URL=noscript.php"></noscript>
	<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
	<script type="text/javascript" src="./librairie_js/function.js"></script>
	<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
  	<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
  	<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
	<?php
	include_once("./librairie_php/lib_netscape.php");
	include_once("./librairie_php/lib_licence2.php");
	include_once("./librairie_php/db_triade.php");
	include_once("./common/lib_ecole.php");
	include_once("./common/version.php");
	if ($_COOKIE["langue-triade"] == "fr") {
        	include_once("./librairie_php/langue-text-fr.php");
	        print "<script type=text/javascript src='librairie_js/languefrmenu-depart.js'></script>\n";
        	print "<script type=text/javascript src='librairie_js/languefrfunction-depart.js'></script>\n";
	}elseif ($_COOKIE["langue-triade"] == "en") {
        	print "<script type=text/javascript src='librairie_js/langueenmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/langueenfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-en.php");
	}elseif ($_COOKIE["langue-triade"] == "es") {
        	print "<script type=text/javascript src='librairie_js/langueesmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/langueesfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-es.php");
	}elseif ($_COOKIE["langue-triade"] == "bret") {
        	print "<script type=text/javascript src='librairie_js/languebretmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languebretfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-bret.php");
	}elseif ($_COOKIE["langue-triade"] == "arabe") {
        	print "<script type=text/javascript src='librairie_js/languearabemenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languearabefunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-arabe.php");
	}else {
        	print "<script type=text/javascript src='librairie_js/languefrmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languefrfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-fr.php");
	}
	if (POPUP == "non") {
		print "<script type='text/javascript'>var popup='non';</script>\n";
	}else {
		print "<script type='text/javascript'>var popup='oui';</script>\n";
	}
	if (HTTPS == "non") {
		print "<script type='text/javascript'>var http='http://';</script>\n";
	}else{
		print "<script type='text/javascript'>var http='https://';</script>\n";
	}
	print "<script type='text/javascript'>var vocalmess='offline';</script>\n";
	print "<script type='text/javascript'>var inc='".GRAPH."';</script>\n";

	?>
	<script type="text/javascript" >var mailcontact="<?php 
		if ((MAILCONTACT != "") && (defined("MAILCONTACT")) ) { 
			print MAILCONTACT; 
		}else{ 
			print ""; 
		} ?>";</script>
	<script type="text/javascript" >var urlcontact="<?php 
		if ((URLCONTACT != "") && (defined("URLCONTACT"))) { 
			print URLCONTACT; 
		}else{ 
			print ""; 
		}  ?>"; </script>
	<script type="text/javascript" >var urlnomcontact="<?php 
		if ((URLNOMCONTACT != "") && (defined("URLNOMCONTACT"))) { 
			$urlnomcontact=preg_replace('/ /',"&nbsp;",URLNOMCONTACT);
			print URLNOMCONTACT; 
		}else{ 
			print ""; 
		} ?>"; </script>
<script type="text/javascript" >var urlcontact2="<?php if (URLCONTACT2 != "") { print URLCONTACT2; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact2="<?php if (URLNOMCONTACT2 != "") { print URLNOMCONTACT2; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact3="<?php if (URLCONTACT3 != "") { print URLCONTACT3; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact3="<?php if (URLNOMCONTACT3 != "") { print URLNOMCONTACT3; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact4="<?php if (URLCONTACT4 != "") { print URLCONTACT4; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact4="<?php if (URLNOMCONTACT4 != "") { print URLNOMCONTACT4; }else{ print ""; } ?>"; </script>
	<script type="text/javascript" src="./librairie_js/menudepart.js"></script>
	<?php include("./librairie_php/lib_defilement.php"); ?>
	</TD><td width="472" valign="middle" rowspan="3" align="center" >
	<div align='center'><?php top_h(); ?>
	<script type="text/javascript" src="./librairie_js/menudepart1.js"></script>



<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2" colspan=2 ><b><font   id='menumodule1' >Informations sur l'élève</B></font></td></tr>
<td id='cadreCentral0'  colspan=2>




<table width=100%>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Décision actuelle :</B> </td>
		    <td bgcolor="#FFFFFF"><font color=blue>
				<?php
				echo $data['decision'];
				?>
				</font></td></tr>
		<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Nom :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				echo $data['nom'];
				?>
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Prénom :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				echo $data['prenom'];
				?>
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Classe :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				echo $nomClasse;
				?>				
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Lv1 :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				echo $data['lv1'];
				?>		
		     </td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Lv2 :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				echo $data['lv2'];
				?>		
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Option :</B> </td>
		     <td bgcolor="#FFFFFF">
				<?php
				echo $data['option2'];
				?>				 
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>R&eacute;gime :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				echo $data['regime'];
				?>				
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Boursier :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				echo ($data['boursier'] == '1') ? "oui" : "non" ;
				?>				
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Date de naissance :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				echo dateForm($data['date_naissance']);
				?>				
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Lieu de naissance :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				echo $data['lieu_naissance'];
				?>				
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Nationalité :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				echo $data['nationalite'];
				?>				
				</td></tr>

				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Adresse élève :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php 
				echo $data['adr_eleve'];
				?>				
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Code postale élève :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				echo $data['ccp_eleve'];
				?>				
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Ville élève :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				echo $data['commune_eleve'];
				?>				
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Pays élève :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				echo $data['tel_fixe_eleve'];
				?>				
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Téléphone fixe élève :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				echo $data['pays_eleve'];
				?>				
				</td></tr>


				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>mot de passe parent :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo "********************" //$data['passwd'];
				?>				
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>mot de passe élève :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				echo "********************" //$data['passwd_eleve'];
				?>				
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Civilité  :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				if ($data['civ_1']=='1')
				  $choix="Mme";
				if ($data['civ_1']=='2')
				  $choix="Mlle";
				if ($data['civ_1']=='3')
					$choix="Ms";
				if ($data['civ_1']=='4')
				  $choix="Mr";
				if ($data['civ_1']=='5')
				  $choix="Mrs";
				echo $choix;
				?>
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Nom Resp. 1 :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				echo $data['nomtuteur'];
				?>				
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Prénom :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				echo $data['prenomtuteur'];
				?>				
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Adresse 1 :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['adr1'];
				?>				
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Code postal :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['code_post_adr1'];
				?>						
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Commune :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['commune_adr1'];
				?>						
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Tél. Portable 1 :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['tel_port_1'];
				?>						
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Civilité  :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				if ($data['civ_2']=='1')
				  $choix="Mme";
				if ($data['civ_2']=='2')
				  $choix="Mlle";
				if ($data['civ_2']=='3')
					$choix="Ms";
				if ($data['civ_2']=='4')
				  $choix="Mr";
				if ($data['civ_2']=='5')
				  $choix="Mrs";
				echo $choix;
				?>				
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Nom Resp. 2 :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['nom_resp_2'];
				?>					
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Prénom Resp. 2 :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['prenom_resp_2'];
				?>					
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Adresse 2 :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['adr2'];
				?>					
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Code Postal :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['code_post_adr2'];
				?>					
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Commune :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['commune_adr2'];
				?>					
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Tél. Portable 2 :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['tel_port_2'];
				?>					
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Téléphone :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['telephone'];
				?>					
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Profession du père :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['profession_pere'];
				?>			
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Téléphone du père :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['tel_prof_pere'];
				?>			
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Profession de la mère :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['profession_mere'];
				?>			
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Téléphone de la mère :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['tel_prof_mere'];
				?>			
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Etablissement :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['nom_etablissement'];
				?>					
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Code établissement :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['code_etablissement'];
				?>					
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Code postal :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['code_postal_etablissement'];
				?>					
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Commune :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['commune_etablissement'];
				?>					
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Numéro Etudiant :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				echo $data['numero_eleve'];
				?>					
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Email Parent :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['email'];
				?>					
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>E-mail Elève :</B> </td>
		    <td bgcolor="#FFFFFF">
				<?php
				echo $data['email_eleve'];
				?>					
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Classe antérieure :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['class_ant'];
				?>					
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Année antérieure :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['annee_ant'];
				?>					
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>Tél. élève :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['tel_eleve'];
				?>					
				</td></tr>
				<tr><td bgcolor="#FFFFFF" width=40% align=right><B>E-mail Tuteur 2 :</B> </td>
				<td bgcolor="#FFFFFF">
				<?php
				echo $data['email_resp_2'];
				?>					
				</td></tr>
		</table>
</form>
<?php 
}else{
	header("Location:ouverture_session_preinscription_eleve.php?error");
}
?>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
