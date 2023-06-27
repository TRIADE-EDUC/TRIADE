<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<?php
session_start();	
$email = $_SESSION['email_login'];
if ($email == '')
  {
  header('Location: ./ouverture_session_preinscription_eleve.php');
  }
?>
<head>
   <meta http-equiv="Content-type" content = "text/html; charset=iso-8859-1" />
   <meta name="MSSmartTagsPreventParsing" content="TRUE" />
   <meta http-equiv="CacheControl" content = "no-cache" />
   <meta http-equiv="pragma" content = "no-cache" />
   <meta http-equiv="expires" content = -1 />
   <meta name="Copyright" content="Triade©, 2001" />
   <meta http-equiv="imagetoolbar" content="no" />
     <link rel="alternate" type="application/rss+xml" title="Actualité Triade" href="http://www.triade-educ.com/accueil/news/rss.xml" />
     <link rel="stylesheet" type="text/CSS" href="./librairie_css/css.css" media="screen" />
     <link rel="shortcut icon" href="./favicon.ico" type="image/icon" />
   <title>Modification des informations d'une candidature</title>
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
	include_once("./common/lib_ecole.php");
	include_once("./common/config2.inc.php");
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
	<script type="text/javascript" src="./librairie_js/menudepart.js"></script>
	<?php include("./librairie_php/lib_defilement.php"); ?>
	</TD><td width="472" valign="middle" rowspan="3" align="center" >
	<div align='center'><?php top_h(); ?>
	<script type="text/javascript" src="./librairie_js/menudepart1.js"></script>





<?php
include ("./librairie_php/fonction.inc.php");
$id=db_connect() or die ("<br>Acces a la base de donne cagt impossible");
//$email = trim($_POST['mail']);


$sql = "SELECT elev_id,nom,prenom,classe,lv1,lv2,regime,date_naissance,lieu_naissance,nationalite,passwd,passwd_eleve,civ_1,nomtuteur,prenomtuteur,adr1,code_post_adr1,commune_adr1,tel_port_1,civ_2,nom_resp_2,prenom_resp_2,adr2,code_post_adr2,commune_adr2,tel_port_2,telephone,profession_pere,tel_prof_pere,profession_mere,tel_prof_mere,nom_etablissement,numero_etablissement,code_postal_etablissement,commune_etablissement,numero_eleve,photo,email,email_eleve,email_resp_2,class_ant,annee_ant,tel_eleve,sexe,option2 FROM preinscription_eleves WHERE email_eleve = '$email';";
$res = mysql_query($sql); 
$data = mysql_fetch_assoc($res);
$classe = $data['classe']; 
$sql_classe = "SELECT libelle FROM tria_classes WHERE code_class = '$classe';";
// echo $sql_classe;
$res_classe = mysql_query($sql_classe); 
$data_classe = mysql_fetch_assoc($res_classe); 		
mysql_close(); 

?> 
                        

<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >


<table width="200" cellpadding="0" cellspacing="0" class="texte" style="display:inline;"> 
<br /><br />
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
Candidature élève</font></B></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<BR>

<form method=post action="modif_preinscription_eleve.php" >

<ul><font class=T1 color='#CC0000'><b>Renseignements sur l'élève</b></font></ul>
<?php
//echo $data['elev_id'];
echo '<input type=hidden name=elev_id value='.$data['elev_id'].'>';
?>
  <TABLE border="0"  width=100% align="center">
     <tr><td width="50%" ><div align="right"><font class="T2">Nom : </font></div></td>
        <td>
				<?php
				echo '<input type=text name=saisie_nom maxlength=30 value='.$data['nom'].'></td>';
				?>
    </tr>
    <tr><td><div align="right"><font class="T2">Prénom :  </font></div></td>
        <?php
				echo '<td><input type=text name=saisie_prenom  maxlength=50 value='.$data['prenom'].'></td>';
				?>
    </tr>
    <tr><td><div align="right"><font class="T2">Classe : </font></div></td>
        <td>
<?php
if ($data['classe'] == 'Graduat 1ère année')
 $classe = 'G1';
if ($data['classe'] == 'Graduat 2ème année')
 $classe = 'G2';
if ($data['classe'] == 'Graduat 3ème année')
 $classe = 'G3';
if ($data['classe'] == 'Licence 1ère année')
 $classe = 'L1';
if ($data['classe'] == 'Licence 2ème année')
 $classe = 'L2';
?>
				
				<select name=saisie_classe size=1>
				<?php
				echo '<option selected value='.$classe.'>'.$data['classe'].'</option>';
				?>
<option id='select1' value='G1'>Graduat 1ère année</option>
<option id='select1' value='G2'>Graduat 2ème année</option>
<option id='select1' value='G3'>Graduat 3ème année</option>
<option id='select1' value='L1'>licence 1ère année</option>
<option id='select1' value='L2'>Licence 2ème année</option>
            </select>
        </td>
    </tr>
    <tr><td><div align="right"><font class="T2">LV1 : </font></div></td>

				<?php
				//echo '<select name=saisie_classe size=1 value'.$data['classe'].'>';
				?>

        <td>
				<select name=saisie_lv1 size=1>
				<?php
				echo '<option selected value='.$data['lv1'].'>'.$data['lv1'].'</option>';
				?>
	
<option id='select1' value='anglais'>ANGLAIS</option>
<option id='select1' value='espagnol'>ESPAGNOL</option>
<option id='select1' value='allemand'>ALLEMAND</option>
<option id='select1' value='italien'>ITALIEN</option>
            </select>
	</td>
    </tr>
    <tr><td><div align="right"><font class="T2">LV2 : </font></div></td>
        <td><select name="saisie_lv2" size="1">
        <?php
				echo '<option selected value='.$data['lv2'].'>'.$data['lv2'].'</option>';
				?>
<option id='select1' value='latin'>LATIN</option>
<option id='select1' value='grec'>GREC</option>
<option id='select1' value='russe'>RUSSE</option>
<option id='select1' value='chinois'>CHINOIS</option>
	    </select>
	</td>
    </tr>
    <tr><td><div align="right"><font class="T2">Option : </font></div></td>
    <td>
<select name="saisie_option2" size="1">
        <?php
				echo '<option selected value='.$data['option2'].'>'.$data['option2'].'</option>';
				?>
<option id='select1' value='scientifique'>SCIENTIFIQUE</option>
<option id='select1' value='litteraire'>LITTERAIRE</option>
<option id='select1' value='commercial'>COMMERCIAL</option>
	    </select>
    </td>
    </tr>
    <tr><td><div align="right"><font class="T2">R&eacute;gime : </font></div></td>
    <td>
		<?php
		if ($data['regime']=='Interne')
		  {
		  echo "<input type=radio name=saisie_regime value=Interne id='.btradio1' checked=checked>Interne<br>";
		  }
		else
		  {
		  echo "<input type=radio name=saisie_regime value=Interne id='.btradio1'>Interne<br>";
		  }
		if ($data['regime']=='Demi-pension')
		  {
		  echo "<input type=radio name=saisie_regime value=Demi-pension id='.btradio1' checked=checked>Demi-pensionnaire<br>";
		  }
		else
		  {
		  echo "<input type=radio name=saisie_regime value=Demi-pensionnaire id='.btradio1'>Demi-pensionnaire<br>";
		  }
		if ($data['regime']=='Externe')
		  {
		  echo "<input type=radio name=saisie_regime value=Externe id='.btradio1' checked=checked>Externe<br>";
		  }
		else
		  {
		  echo "<input type=radio name=saisie_regime value=Externe id='.btradio1'>Externe<br>";
		  }	  
		?>
		

      </tr>
    <tr><td><div align="right"><font class="T2">Date de naissance : </font></div></td>
        <td>
<?php
$annee = substr($data['date_naissance'], 0, 4);  // abcd
$mois = substr($data['date_naissance'], 5, 2);  // abcd
$jour = substr($data['date_naissance'], 8, 2);  // abcd

echo '<SELECT name=saisie_jour_naissance Size=1>';
echo '<option selected value='.$jour.'>'.$jour.'</option>';
     for($i=1; $i<=31;$i++){	       //Lister les jours
     	       if ($i < 10){		       //Lister les jours pour pouvoir leur ajouter un 0 devant
	       	  echo "<OPTION>0$i<br></OPTION>";
		           }
               else {
	          echo "<OPTION>$i<br></OPTION>";
                    }
                          }
echo "</SELECT>";
echo '<SELECT name="saisie_mois_naissance" Size="1">';
echo '<option selected value='.$mois.'>'.$mois.'</option>';
     for($d=1; $d<=12;$d++){	       //Lister les mois
     	       if ($d < 10){		       //Lister les jours pour pouvoir leur ajouter un 0 devant
	       	  echo "<OPTION>0$d<br></OPTION>";
		           }
               else {
	          echo "<OPTION>$d<br></OPTION>";
                    }
                          }
echo "</SELECT>";
$date = date('Y');		 //On prend l'année en cours
	
echo '<SELECT name="saisie_annee_naissance" Size="1">';
echo '<option selected value='.$annee.'>'.$annee.'</option>';
     for ($y=1900; $y<=$date; $y++) {	       //De l'année 1900 à l'année actuelle
     	 echo "<OPTION><br>$y<br></OPTION>"; }
echo "</SELECT>";

?>
 </td>
    </tr>
    <tr><td><div align="right"><font class="T2">Sexe : </font></div></td>
<?php
		if ($data['sexe']=='m')
		  {
		  echo "<td><input type=radio name=saisie_sexe value=m id='.btradio1' checked=checked> M -";
		  }
		else
		  {
		  echo "<td><input type=radio name=saisie_sexe value=m id='.btradio1'> M -";
		  }	
		if ($data['sexe']=='f')
		  {
		  echo "<input type=radio name=saisie_sexe value=f id='.btradio1' checked=checked> F <br></td></tr>";
		  }
		else
		  {
		  echo "<input type=radio name=saisie_sexe value=f id='.btradio1'> F <br></td></tr>";
		  }	
?>

 				 
    <tr><td><div align="right"><font class="T2">Nationalité : </font></div></td>
        <td>
				<?php
				echo '<input type=text name=saisie_nationalite maxlength=20 value='.$data['nationalite'].'></td>';
				?>  
    </td></tr>
    <tr><td><div align="right"><font class="T2">Lieu de naissance : </font></div></td>
        <td>
				<?php
				echo '<input type=text name=saisie_lieu_naissance maxlength=25 value='.$data['lieu_naissance'].'></td>';
				?>  
    </td></tr>
    
    <tr><td><div align="right"><font class="T2">Mot de passe élève :</font></div></td>
	         <td>
				<?php
				echo '<input type=passwd name=saisie_passwd_eleve maxlength=50 value='.$data['passwd_eleve'].'></td>';
				?> 	 
    </td></tr>
    
    <tr><td><div align="right"><font class="T2">Numéro étudiant :</font></div></td>
         <td>
				<?php
				echo '<input type=text name=saisie_numero_eleve maxlength=30 value='.$data['numero_eleve'].'></td>';
				?> 				 

    </tr>
 <tr><td><div align="right"><font class="T2">Tél Portable élève :  </font></div></td>
      <td>
				<?php
				echo '<input type=text name=saisie_tel_eleve maxlength=18 value='.$data['tel_eleve'].'></td>';
				?> 				
    </tr>
 <tr><td><div align="right"><font class="T2">Email élève :  </font></div></td>
      <td>
				<?php
				echo '<input type=text name=saisie_email_eleve maxlength=48 size=30 value='.$data['email_eleve'].'></td>';
				?> 			
    </tr>
  </table>


<BR>
<ul><font class=T1 color='#CC0000'><b>Renseignements sur la famille</b></font></ul>
 <TABLE border="0"  width=100% align=center>
<tr><td align=right ><font class="T2">Civ 1 : </font></td><td>



<select name="saisie_civ_1" >
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
				  
				echo '<option selected value='.$data['civ_1'].'>'.$choix.'</option>';
				?>
<option value='0' id='select1' >M.</option>
<option value='1' id='select1'>Mme</option>
<option value='2' id='select1'>Mlle</option>
<option value='3' id='select1'>Ms</option>
<option value='4' id='select1'>Mr</option>
<option value='5' id='select1' >Mrs</option>
</select>
</td></tr>
<tr><td width="50%" ><div align="right"><font class="T2">Nom resp. 1 : </font></div></td>
       <td>
				<?php
				echo '<input type=text name=saisie_nomtuteur maxlength=30 size=30 value='.$data['nomtuteur'].'></td>';
				?> 				 
   </tr>
   
    <tr><td><div align="right"><font class="T2">Prénom resp. 1 :  </font></div></td>
        <td>
				<?php
				echo '<input type=text name=saisie_prenomtuteur maxlength=48 size=30 value='.$data['prenomtuteur'].'></td>';
				?> 	
    </tr>
    
    <tr><td><div align="right"><font class="T2">Adresse 1 : </font></div></td>
        <td>
				<?php
				echo '<input type=text name=saisie_adr1 maxlength=48 size=30 value='.$data['adr1'].'></td>';
				?> 	
    </tr>
    
    <tr><td><div align="right"><font class="T2">Code postal 1 : </font></div></td>
      <td>
				<?php
				echo '<input type=text name=saisie_code_post_adr1 maxlength=48 size=30 value='.$data['code_post_adr1'].'></td>';
				?> 				
    </tr>
    
    <tr><td><div align="right"><font class="T2">Commune 1 : </font></div></td>
      <td>
				<?php
				echo '<input type=text name=saisie_commune_adr1 maxlength=48 size=30 value='.$data['commune_adr1'].'></td>';
				?> 				
    </tr>
    
    <tr><td><div align="right"><font class="T2">Tél. portable  1 : </font></div></td>
      <td>
				<?php
				echo '<input type=text name=saisie_tel_port_1 maxlength=48 size=30 value='.$data['tel_port_1'].'></td>';
				?> 				
    </tr>
    
    <tr><td><div align="right"><font class="T2">Email tuteur 1 :  </font></div></td>
      <td>
				<?php
				echo '<input type=text name=saisie_email maxlength=48 size=30 value='.$data['email'].'></td>';
				?> 				
    </tr>

<tr><td align=right ><font class="T2">Civ 2 : </td><td>
<select name="saisie_civ_2" >

        <?php
				if ($data['civ_2']=='1')
				  $choix2="Mme";
				if ($data['civ_2']=='2')
				  $choix2="Mlle";
				if ($data['civ_2']=='3')
					$choix2="Ms";
				if ($data['civ_2']=='4')
				  $choix2="Mr";
				if ($data['civ_2']=='5')
				  $choix2="Mrs";
				  
				echo '<option selected value='.$data['civ_2'].'>'.$choix2.'</option>';
				?>
<option value='6' id='select1' >M. ou Mme</option>
<option value='0' id='select1' >M.</option>
<option value='1' id='select1'>Mme</option>
<option value='2' id='select1'>Mlle</option>
<option value='3' id='select1'>Ms</option>
<option value='4' id='select1'>Mr</option>
<option value='5' id='select1' >Mrs</option>
</select>
</td></tr>
   <tr><td width="50%" ><div align="right"><font class="T2">Nom resp. 2 : </font></div></td>
       <td>
				<?php
				echo '<input type=text name=saisie_nom_resp_2 maxlength=48 size=30 value='.$data['nom_resp_2'].'></td>';
				?> 			 
   </tr>
   
    <tr><td><div align="right"><font class="T2">Prénom resp. 2 :  </font></div></td>
        <td>
				<?php
				echo '<input type=text name=saisie_prenom_resp_2 maxlength=48 size=30 value='.$data['prenom_resp_2'].'></td>';
				?> 				
    </tr>
    
    <tr><td><div align="right"><font class="T2">Adresse 2 : </font></div></td>
        <td>
				<?php
				echo '<input type=text name=saisie_adr2 maxlength=48 size=30 value='.$data['adr2'].'></td>';
				?> 				
    </tr>
    
    <tr><td><div align="right"><font class="T2">Code postal 2 : </font></div></td>
      <td>
				<?php
				echo '<input type=text name=saisie_code_post_adr2 maxlength=48 size=30 value='.$data['code_post_adr2'].'></td>';
				?> 			
    </tr>
    
    <tr><td><div align="right"><font class="T2">Commune 2 : </font></div></td>
      <td>
				<?php
				echo '<input type=text name=saisie_commune_adr2 maxlength=48 size=30 value='.$data['commune_adr2'].'></td>';
				?> 			
</tr>

<tr><td><div align="right"><font class="T2">Tél. portable  2 : </font></div></td>
      <td>
				<?php
				echo '<input type=text name=saisie_tel_port_2 maxlength=48 size=30 value='.$data['tel_port_2'].'></td>';
				?> 			
    </tr>
    
<tr><td><div align="right"><font class="T2">Email tuteur 2 :  </font></div></td>
      <td>
				<?php
				echo '<input type=text name=saisie_email_resp_2 maxlength=48 size=30 value='.$data['email_resp_2'].'></td>';
				?> 			
    </tr>
    
    <tr><td><div align="right"><font class="T2">Numéro de téléphone : </font></div></td>
      <td>
				<?php
				echo '<input type=text name=saisie_telephone maxlength=48 size=30 value='.$data['telephone'].'></td>';
				?> 			
    </tr>
    
    <tr><td><div align="right"><font class="T2">Profession du père : </font></div></td>
      <td>
				<?php
				echo '<input type=text name=saisie_profession_pere maxlength=48 size=30 value='.$data['profession_pere'].'></td>';
				?> 			
    </tr>
    
    <tr><td><div align="right"><font class="T2">Téléphone du père :  </font></div></td>
      <td>
				<?php
				echo '<input type=text name=saisie_tel_prof_pere maxlength=48 size=30 value='.$data['tel_prof_pere'].'></td>';
				?> 			
    </tr>
    
    <tr><td><div align="right"><font class="T2">Profession de la mère : </font></div></td>
      <td>
				<?php
				echo '<input type=text name=saisie_profession_mere maxlength=48 size=30 value='.$data['profession_mere'].'></td>';
				?> 			
    </tr>
    
    <tr><td><div align="right"><font class="T2">Téléphone de la mère :  </font></div></td>
      <td>
				<?php
				echo '<input type=text name=saisie_tel_prof_mere maxlength=48 size=30 value='.$data['tel_prof_mere'].'></td>';
				?> 			
    </tr>
    
   <tr><td><div align="right"><font class="T2">Mot de passe parent  :</font></div></td>
         <td>
				<?php
				echo '<input type=passwd name=saisie_passwd maxlength=50 size=30 value='.$data['passwd'].'></td>';
				?> 				 
    </tr> 
  </table>

<BR>
<ul><font class=T1 color='#CC0000'><b>Ecole antérieure</b></font></ul>
  <TABLE border="0" width=100% align="center">
    <tr><td width="50%"><div align="right"><font class="T2">Nom de l'établissement :</font></div></td>
          <td>
				<?php
				echo '<input type=text name=saisie_nom_etablissement maxlength=48 size=30 value='.$data['nom_etablissement'].'></td>';
				?> 					
    </tr>
    
    <tr><td><div align="right"><font class="T2">Numéro établissement :</font></div></td>
      <td>
				<?php
				echo '<input type=text name=saisie_numero_etablissement maxlength=48 size=30 value='.$data['numero_etablissement'].'></td>';
				?> 			
    </tr>
      
      <tr><td><div align="right"><font class="T2">Classe antérieure :</font></div></td>
      <td>
				<?php
				echo '<input type=text name=saisie_classe_ant maxlength=48 size=30 value='.$data['class_ant'].'></td>';
				?> 			
</tr>

    <tr><td><div align="right"><font class="T2">Année antérieure : </font></div></td>
        <td>
				<?php
				echo '<input type=text name=saisie_date_ant maxlength=48 size=30 value='.$data['annee_ant'].'></td>';
				?> 				
    </tr>
    
<tr><td><div align="right"><font class="T2">Code postal : </font></div></td>
<td>
				<?php
				echo '<input type=text name=saisie_code_postal_etablissement maxlength=48 size=30 value='.$data['code_postal_etablissement'].'></td>';
				?> 
</tr>

<tr><td><div align="right"><font class="T2">Commune : </font></div></td>
<td>
				<?php
				echo '<input type=text name=saisie_commune_etablissement maxlength=48 size=30 value='.$data['commune_etablissement'].'></td>';
				?> 
</tr>

</table>

<br>
<table  border="0" align="center">
<tr><td height="53">
<div align="center">

<script language=JavaScript>buttonMagicSubmit('Modifier ma candidature','create'); //text,nomInput</script>
<br><br>
</div></td></tr>
</table>
</form>
<!-- // fin  -->

	
</td></tr>
</table>
</form>
<!-- // fin  -->
</td></tr></table>
