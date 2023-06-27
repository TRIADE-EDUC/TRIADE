<?php
session_start();
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(0);
}
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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_attente.php"); ?>
<script>AfficheAttente()</script>
<?php
if (empty($_SESSION["adminplus"])) {
	print "<script>";
	print "location.href='./base_de_donne_importation.php'";
	print "</script>";
	exit;
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript"<?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Archivage des données"?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<br /><br />
<center>
<table border="0" align="center">  

<?php
	include_once("./librairie_php/db_triade.php");
	if (!is_dir("./data/archive")) { 
		mkdir("./data/archive") ; 
		$text="<Files \"*\">\n";
		$text.="Order Deny,Allow\n";
		$text.="Deny from all\n";
		$text.="</Files>";
		@unlink("./data/archive/.htaccess");
		$fp = fopen("./data/archive/.htaccess", "w");
		fwrite($fp,$text);
		fclose($fp);	
	}
	$cnx=cnx();
	$fichier="./data/archive/".$_POST["annee"].".sqlite";
	if (isset($_POST["etape1"])) { if (file_exists($fichier)) {  @unlink($fichier); }  }
	$db=new SQLiteDatabase("$fichier");
	$nbetape=9;

	if (isset($_POST["etape1"])) {
		//------------------------------------------------------------------------------------------------
		// GESTION DES NOTES
		$db->queryExec("CREATE TABLE notes (
				note_id INT NOT NULL PRIMARY KEY ,
				elev_id integer NOT NULL,
				prof_id integer NOT NULL,
				code_mat integer NOT NULL,
				coef numeric(30,2) NOT NULL,
				date date NOT NULL,
				sujet text,
				note numeric(30,6) NOT NULL,
				id_classe integer NOT NULL,
				id_groupe integer,
				typenote varchar(5),
				noteexam varchar(30),
				notationsur integer,
			        notevisiblele date )");
		$data=SQLite_notes();  
		// note_id,elev_id,prof_id,code_mat,coef,date,sujet,TRUNCATE(note,2),id_classe,id_groupe,typenote,noteexam,notationsur,notevisiblele
		$db->queryExec('BEGIN TRANSACTION');
		for($i=0;$i<count($data);$i++) {
			$val1=$data[$i][0];
			$val2=$data[$i][1];
			$val3=$data[$i][2];
			$val4=$data[$i][3];
			$val5=$data[$i][4];
			$val6=$data[$i][5];
			$val7=$data[$i][6];
			$val8=$data[$i][7];
			$val9=$data[$i][8];
			$val10=$data[$i][9];
			$val11=$data[$i][10];
			$val12=$data[$i][11];
			$val13=$data[$i][12];
			$val14=$data[$i][13];
			$sql="INSERT INTO notes (note_id,elev_id,prof_id,code_mat,coef,date,sujet,note,id_classe,id_groupe,typenote,noteexam,notationsur,notevisiblele) VALUES ('$val1','$val2','$val3','$val4','$val5','$val6','$val7','$val8','$val9','$val10','$val11','$val12','$val13','$val14');";	
			$db->queryExec($sql);
		}
		$db->queryExec('COMMIT TRANSACTION;'); 
		print "<tr><td align='right'><font class='T2'>Archivage Notes :</font></td><td><img src='./image/commun/stat1.gif' align=center></td></tr>";
		$etape="2/$nbetape";$nometape="etape2";
	}

	
	//------------------------------------------------------------------------------------------------
	// GESTION DES abs
	//
	
	if (isset($_POST["etape2"])) {
		$db->queryExec("CREATE TABLE absences (
				elev_id	integer NOT NULL,
				date_ab date NOT NULL,
				date_saisie date NOT NULL,
				duree_ab real NOT NULL,
				origin_saisie varchar(30),
				date_fin date,
				motif text,
				duree_heure integer,
				id_matiere integer,
				time  time,
				justifier integer default NULL,
			  	heure_saisie time default NULL,
  				heuredabsence time default NULL,
  				idprof integer default NULL,
  				creneaux varchar(45) )");
		$data=SQLite_abs();  
		$db->queryExec('BEGIN TRANSACTION');
		//elev_id, date_ab, date_saisie,origin_saisie,duree_ab ,date_fin, motif,  duree_heure, id_matiere, time
		for($i=0;$i<count($data);$i++) {
			$val1=$data[$i][0];
			$val2=$data[$i][1];
			$val3=$data[$i][2];
			$val4=$data[$i][3];
			$val5=$data[$i][4];
			$val6=$data[$i][5];
			$val7=$data[$i][6];
			$val8=$data[$i][7];
			$val9=$data[$i][8];
			$val10=$data[$i][9];
			$val11=$data[$i][10];
			$val12=$data[$i][11];
			$val13=$data[$i][12];
			$val14=$data[$i][13];
			$val15=$data[$i][14];
			$sql="INSERT INTO absences (elev_id,date_ab,date_saisie,origin_saisie,duree_ab,date_fin,motif,duree_heure,id_matiere,time,justifier,heure_saisie,heuredabsence,idprof,creneaux) VALUES ('$val1','$val2','$val3','$val4','$val5','$val6','$val7','$val8','$val9','$val10','$val11','$val12','$val13','$val14','$val15');";
			$db->queryExec($sql);

		}
		$db->queryExec('COMMIT TRANSACTION;'); 
		print "<tr><td align='right'><font class='T2'>Archivage Absences :</font></td><td><img src='./image/commun/stat1.gif' align=center></td></tr>";

		$etape="3/$nbetape";$nometape="etape3";
	}

	// --------------------------------------------------------------------------------------------------------------------


	//------------------------------------------------------------------------------------------------
	// GESTION DES rtd

	if (isset($_POST["etape3"])) {
		$db->queryExec("CREATE TABLE retards (
				elev_id integer NOT NULL,
				heure_ret time NOT NULL,
				date_ret date NOT NULL,
				date_saisie date NOT NULL,
				origin_saisie varchar(30),
				duree_ret varchar(10),
				motif text,
				idmatiere integer,
				justifier integer default NULL,
			 	heure_saisie time default NULL,
				idprof integer default NULL,
				creneaux varchar(45) default ''  ,
				primary key(date_ret,elev_id,heure_ret))");
		$data=SQLite_rtd();  
		//elev_id,heure_ret,date_ret,date_saisie,origin_saisie,duree_ret,motif,idmatiere,justifier,heure_saisie,idprof,creneaux
		$db->queryExec('BEGIN TRANSACTION');
		for($i=0;$i<count($data);$i++) {
			$val1=$data[$i][0];
			$val2=$data[$i][1];
			$val3=$data[$i][2];
			$val4=$data[$i][3];
			$val5=$data[$i][4];
			$val6=$data[$i][5];
			$val7=$data[$i][6];
			$val8=$data[$i][7];
			$val9=$data[$i][8];
			$val10=$data[$i][9];
			$val11=$data[$i][10];
			$val12=$data[$i][11];
		
			$sql="INSERT INTO retards (elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere,justifier,heure_saisie,idprof,creneaux) VALUES ('$val1','$val2','$val3','$val4','$val5','$val6','$val7','$val8','$val9','$val10','$val11','$val12');";
			$db->queryExec($sql);

		}
		$db->queryExec('COMMIT TRANSACTION;'); 
		print "<tr><td align='right'><font class='T2'>Archivage Retards :</font></td><td><img src='./image/commun/stat1.gif' align=center></td></tr>";
		$etape="4/$nbetape";$nometape="etape4";
	}

	// --------------------------------------------------------------------------------------------------------------------

	//------------------------------------------------------------------------------------------------
	// GESTION DES disciplines
	
	if (isset($_POST["etape4"])) {

		$db->queryExec("CREATE TABLE discipline_sanction (
  				id int(11),
				id_eleve int(11),
				motif text,
				id_category int(11),	
			      	date_saisie date,
			      	origin_saisie varchar(30),
			      	enr_en_retenue tinyint(1),
			      	signature_parent tinyint(1),
			      	attribuer_par varchar(30),
				devoir_a_faire text,
				description_fait text,
			  	PRIMARY KEY  (id)
  				)");	
		$data=SQLite_sanction();  
		//id,id_eleve,motif,id_category,date_saisie,origin_saisie,enr_en_retenue,signature_parent,attribuer_par,devoir_a_faire
		$db->queryExec('BEGIN TRANSACTION');
		for($i=0;$i<count($data);$i++) {
			$val1=$data[$i][0];
			$val2=$data[$i][1];
			$val3=$data[$i][2];
			$val4=$data[$i][3];
			$val5=$data[$i][4];
			$val6=$data[$i][5];
			$val7=$data[$i][6];
			$val8=$data[$i][7];
			$val9=$data[$i][8];
			$val10=$data[$i][9];
			$val11=$data[$i][10];
		
			$sql="INSERT INTO discipline_sanction (id,id_eleve,motif,id_category,date_saisie,origin_saisie,enr_en_retenue,signature_parent,attribuer_par,devoir_a_faire,description_fait) VALUES ('$val1','$val2','$val3','$val4','$val5','$val6','$val7','$val8','$val9','$val10','$val11');";
			$db->queryExec($sql);

		}
		$db->queryExec('COMMIT TRANSACTION;'); 
		print "<tr><td align='right'><font class='T2'>Archivage Disciplines (Sanctions)  :</font></td><td><img src='./image/commun/stat1.gif' align=center></td></tr>";
		
		$etape="5/$nbetape";$nometape="etape5";
	}
	// --------------------------------------------------------------------------------------------------------------------

	//------------------------------------------------------------------------------------------------
	// GESTION DES disciplines
	
	if (isset($_POST["etape5"])) {

		$db->queryExec("CREATE TABLE entretieneleve (
  				ideleve int(11) NOT NULL,
  				date date NOT NULL,
  				heuredebut time NOT NULL,
  				heurefin time NOT NULL,
  				nomclasse varchar(40) NOT NULL,
  				objet text NOT NULL,
  				recupar varchar(100) NOT NULL,
  				id int(11) NOT NULL,
				preparation tinyint(4),
  				PRIMARY KEY  (id)
  				)");	
		$data=SQLite_entretienEleve();  
		// ideleve,date,heuredebut,heurefin,nomclasse,objet,recupar,id
		$db->queryExec('BEGIN TRANSACTION');
		for($i=0;$i<count($data);$i++) {
			$val1=$data[$i][0];
			$val2=$data[$i][1];
			$val3=$data[$i][2];
			$val4=$data[$i][3];
			$val5=$data[$i][4];
			$val6=$data[$i][5];
			$val7=$data[$i][6];
			$val8=$data[$i][7];
			$val9=$data[$i][8];
			$sql="INSERT INTO entretieneleve (ideleve,date,heuredebut,heurefin,nomclasse,objet,recupar,id,preparation) VALUES ('$val1','$val2','$val3','$val4','$val5','$val6','$val7','$val8','$val9');";
			$db->queryExec($sql);

		}
		$db->queryExec('COMMIT TRANSACTION;'); 
		print "<tr><td align='right'><font class='T2'>Archivage des entretiens individuels  :</font></td><td><img src='./image/commun/stat1.gif' align=center></td></tr>";
		
		$etape="6/$nbetape";$nometape="etape6";
	}
	// --------------------------------------------------------------------------------------------------------------------

	//------------------------------------------------------------------------------------------------
	// GESTION DES disciplines retenu
	if (isset($_POST["etape6"])) {
		$db->queryExec("CREATE TABLE discipline_retenue (
			  id_elev int(11),
			  date_de_la_retenue date,
			  heure_de_la_retenue time,
			  date_de_saisie date,
			  origi_saisie varchar(30),
			  id_category int(11),
			  retenue_effectuer tinyint(1),
			  motif text,
			  attribuer_par varchar(30),
			  signature_parent tinyint(1),
			  duree_retenu time,
			  devoir_a_faire text,
			  description_fait text,
			  PRIMARY KEY  (date_de_la_retenue,heure_de_la_retenue,id_elev))");
		$data=SQLite_retenu();  
		// id_elev,date_de_la_retenue,heure_de_la_retenue,date_de_saisie,origi_saisie,id_category,retenue_effectuer,motif,attribuer_par,signature_parent,duree_retenu,devoir_a_faire
		$db->queryExec('BEGIN TRANSACTION');
		for($i=0;$i<count($data);$i++) {
			$val1=$data[$i][0];
			$val2=$data[$i][1];
			$val3=$data[$i][2];
			$val4=$data[$i][3];
			$val5=$data[$i][4];
			$val6=$data[$i][5];
			$val7=$data[$i][6];
			$val8=$data[$i][7];
			$val9=$data[$i][8];
			$val10=$data[$i][9];
			$val11=$data[$i][10];
			$val12=$data[$i][11];
			$val13=$data[$i][12];
			$sql="INSERT INTO discipline_retenue (id_elev,date_de_la_retenue,heure_de_la_retenue,date_de_saisie,origi_saisie,id_category,retenue_effectuer,motif,attribuer_par,signature_parent,duree_retenu,devoir_a_faire,description_fait) VALUES ('$val1','$val2','$val3','$val4','$val5','$val6','$val7','$val8','$val9','$val10','$val11','$val12','$val13');";
			$db->queryExec($sql);

		}
		$db->queryExec('COMMIT TRANSACTION;'); 
		print "<tr><td align='right'><font class='T2'>Archivage Disciplines (Retenues)  :</font></td><td><img src='./image/commun/stat1.gif' align=center></td></tr>";
		$etape="7/$nbetape";$nometape="etape7";
	
	}


//------------------------------------------------------------------------------------------------
	// GESTION DES Affectations
	if (isset($_POST["etape7"])) {
		$db->queryExec("CREATE TABLE affectations (
				  ordre_affichage int(6) NOT NULL,
				  code_matiere int(6) NOT NULL,
				  code_prof int(11) NOT NULL,
				  code_classe int(6) NOT NULL,
				  coef decimal(30,2) NOT NULL,
				  groupe varchar(30) DEFAULT NULL,
				  langue varchar(20) DEFAULT NULL,
				  avec_sous_matiere integer NOT NULL,
				  visubull integer  NOT NULL DEFAULT '1',
				  nb_heure varchar(30) NOT NULL DEFAULT ' ',
				  trim varchar(30) NOT NULL DEFAULT 'tous',
				  ects varchar(10) NOT NULL DEFAULT '0',
				  PRIMARY KEY (code_classe,code_matiere,code_prof,ordre_affichage,trim))
				");
		$data=SQLite_affectation();  
		// ordre_affichage, code_matiere, code_prof, code_classe, coef, code_groupe, langue, avec_sous_matiere, visubull, nb_heure, trim, ects
		$db->queryExec('BEGIN TRANSACTION');
		for($i=0;$i<count($data);$i++) {
			$val1=$data[$i][0];
			$val2=$data[$i][1];
			$val3=$data[$i][2];
			$val4=$data[$i][3];
			$val5=$data[$i][4];
			$val6=chercheGroupeNom($data[$i][5]);
			$val7=$data[$i][6];
			$val8=$data[$i][7];
			$val9=$data[$i][8];
			$val10=$data[$i][9];
			$val11=$data[$i][10];
			$val12=$data[$i][11];
			$sql="INSERT INTO affectations (ordre_affichage, code_matiere, code_prof, code_classe, coef, groupe, langue, avec_sous_matiere, visubull, nb_heure, trim, ects) VALUES ('$val1','$val2','$val3','$val4','$val5','$val6','$val7','$val8','$val9','$val10','$val11','$val12');";
			$db->queryExec($sql);

		}
		$db->queryExec('COMMIT TRANSACTION;'); 
		print "<tr><td align='right'><font class='T2'>Archivage des affectations  :</font></td><td><img src='./image/commun/stat1.gif' align=center></td></tr>";
		$etape="8/$nbetape";$nometape="etape8";
	}

//------------------------------------------------------------------------------------------------
	// GESTION DU CAHIER DE TEXTES 
	if (isset($_POST["etape8"])) {
		$db->queryExec("CREATE TABLE devoir_scolaire (
  				id int(11) NOT NULL,
				id_class_or_grp int(11) NOT NULL,
				matiere_id int(11) NOT NULL,
				date_saisie date NOT NULL,
				heure_saisie time NOT NULL,
				date_devoir date NOT NULL,
				texte text NOT NULL,
			      	classorgrp bool NOT NULL,
				number varchar(50) default NULL,
				fichier varchar(255) default NULL,
				idprof int(11) NOT NULL,
				tempsestimedevoir time NOT NULL,
				visadirecteur bool NOT NULL default '0',
				PRIMARY KEY  (id))");
		print 
		$data=SQLite_devoirScolaire();  
		// ordre_affichage, code_matiere, code_prof, code_classe, coef, code_groupe, langue, avec_sous_matiere, visubull, nb_heure, trim, ects
		$db->queryExec('BEGIN TRANSACTION');
		for($i=0;$i<count($data);$i++) {
			$val1=$data[$i][0];
			$val2=$data[$i][1];
			$val3=$data[$i][2];
			$val4=$data[$i][3];
			$val5=$data[$i][4];
			$val6=chercheGroupeNom($data[$i][5]);
			$val7=html_quotes($data[$i][6]);
			$val8=$data[$i][7];
			$val9=$data[$i][8];
			$val10=$data[$i][9];
			$val11=$data[$i][10];
			$val12=$data[$i][11];
			$val13=$data[$i][12];
			$sql="INSERT INTO devoir_scolaire (id,id_class_or_grp,  matiere_id,  date_saisie,  heure_saisie,  date_devoir,  texte,  classorgrp,  number,  fichier , idprof , tempsestimedevoir,  visadirecteur) VALUES ('$val1','$val2','$val3','$val4','$val5','$val6','$val7','$val8','$val9','$val10','$val11','$val12','$val13');";
			$db->queryExec($sql);

		}
		$db->queryExec('COMMIT TRANSACTION;'); 
		print "<tr><td align='right'><font class='T2'>Archivage du cahier de textes (1)  :</font></td><td><img src='./image/commun/stat1.gif' align=center></td></tr>";
		$etape="9/$nbetape";$nometape="etape9";
	}


	//------------------------------------------------------------------------------------------------
	// GESTION DU cahier de texte
	if (isset($_POST["etape9"])) {
		$db->queryExec("CREATE TABLE cahiertexte (
			  id int(11) NOT NULL,
			  id_class_or_grp int(11) NOT NULL,
			  matiere_id int(11) NOT NULL,
			  date_saisie date NOT NULL,
			  heure_saisie time NOT NULL,
			  classorgrp integer  NOT NULL,
			  number varchar(50) DEFAULT NULL,
			  fichier varchar(255) DEFAULT NULL,
			  idprof int(11) NOT NULL,
			  objectif text NOT NULL,
			  contenu text NOT NULL,
			  date_contenu date NOT NULL,
			  number_obj varchar(50) NOT NULL,
			  fichier_obj varchar(250) NOT NULL,
			  blocnote text NOT NULL,
			  visadirecteur int(6) NOT NULL DEFAULT '0',
			  PRIMARY KEY (id))
			");
		$data=SQLite_cahierDeTextes();  
		// id,id_class_or_grp,matiere_id,date_saisie,heure_saisie,classorgrp,number,fichier,idprof,objectif,contenu,date_contenu,number_obj,fichier_obj,blocnote,visadirecteur
		$db->queryExec('BEGIN TRANSACTION');
		for($i=0;$i<count($data);$i++) {
			$val1=$data[$i][0];
			$val2=$data[$i][1];
			$val3=$data[$i][2];
			$val4=$data[$i][3];
			$val5=$data[$i][4];
			$val6=$data[$i][5];
			$val7=$data[$i][6];
			$val8=$data[$i][7];
			$val9=$data[$i][8];
			$val10=$data[$i][9];
			$val11=html_quotes($data[$i][10]);
			$val12=html_quotes($data[$i][11]);
			$val13=$data[$i][12];
			$val14=$data[$i][13];
			$val15=html_quotes($data[$i][14]);
			$val16=$data[$i][15];
			$sql="INSERT INTO cahiertexte (id,id_class_or_grp,matiere_id,date_saisie,heure_saisie,classorgrp,number,fichier,idprof,objectif,contenu,date_contenu,number_obj,fichier_obj,blocnote,visadirecteur) VALUES ('$val1','$val2','$val3','$val4','$val5','$val6','$val7','$val8','$val9','$val10','$val11','$val12','$val13','$val14','$val15','$val16');";
			$db->queryExec($sql);

		}
		$db->queryExec('COMMIT TRANSACTION;'); 
		print "<tr><td align='right'><font class='T2'>Archivage du cahier de textes (2) :</font></td><td><img src='./image/commun/stat1.gif' align=center></td></tr>";
		print "<tr><td align='right' colspan='2'><br /><br /><font class='T2'>Archivage Terminé </font></td></tr>";
		$nometape="fin";
	}


	// --------------------------------------------------------------------------------------------------------------------
	if ($nometape != "fin") {
		print "<tr><td><br /></td></tr>";
		print "<tr><td><br /></td></tr>";
		print "<tr><td><form method='post'>";
		print "<input type='hidden' name='annee' value='".$_POST["annee"]."' >";
		?>
		<script language=JavaScript>buttonMagicSubmit3('Continuer étape <?php print $etape ?>','<?php print $nometape ?>',"onclick='this.value=\"<?php print LANGBT5 ?>\";AfficheAttente()'");</script>
		<?php
		print "</form>";
		print "</tr></td>";
	}
	// FERMETURE BASE
	Pgclose();
?>
</table>
</font>
<br /><br />
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
<?php attente(); ?>
</BODY></HTML>
