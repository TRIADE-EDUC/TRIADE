<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Max-Age: 1000');
if("OPTIONS" == $_SERVER['REQUEST_METHOD']) { exit(0); }
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

//print "ok";
// p=696186d1596721cb6e79013d4655c5d9&productid=169e261092efbd2c36aba846140124a1&identreprise=CS:495&info=lieu

$rawData = file_get_contents("php://input");
$HTTP_RAW_POST_DATA=$rawData;

if($HTTP_RAW_POST_DATA != "") {
        list($a,$b,$c,$d)=preg_split('/\&/',$HTTP_RAW_POST_DATA); // here you will get variable $foo
        list($null,$valeur)=preg_split('/=/',$a);
        $p=$valeur;
        list($null,$valeur)=preg_split('/=/',$b);
        $productid=$valeur;
        list($null,$valeur)=preg_split('/=/',$c);
        $identreprise=$valeur;
        list($null,$valeur)=preg_split('/=/',$d);
        $info=$valeur;

        include_once("./common/config.inc.php");
        include_once("./librairie_php/db_triade.php");
        if (file_exists("./common/config.centralStage.php")) {
                $cnx=cnx();
                verifAccesCentrale("$productid","$p");
                PgClose($cnx);
        }


	$cnx=cnx();
	$liste=$info;
	$identreprise=preg_replace('/CS:/','',$identreprise);
	$data=recupInfoEntreprise($identreprise);
	// id_serial, 0
	// nom,1
	// contact,2
	// adresse,3
	// code_p,4
	// ville,5
	// secteur_ac,6
	// activite_prin,7
	// tel,8
	// fax,9
	// email,10
	// info_plus,11
	// bonus,12
	// contact_fonction,13
	// pays_ent 14
	// secteur_ac2 ,  15
	// secteur_ac3 ,16
	// contact_fonction 17 
	// ,nbchambre , 18
	// siteweb , 19 
	// grphotelier , 20 
	// nbetoile , 21 
	// registrecommerce , 22 
	// siren , 23 
	// siret , 24 
	// formejuridique , 25 
	// secteureconomique , 26
	// INSEE , 27 
	// NAFAPE , 28
	// NACE , 29
	// typeorganisation , 30
	//
        if ($info == "nom_entreprise_via_central") { if (VATEL == 1) { $liste=utf8_encode($data[0][1]); }else{ $liste=$data[0][1]; } }
        if ($info == "registrecommerce") { if (VATEL == 1) { $liste=utf8_encode($data[0][22]); }else{ $liste=$data[0][22];  } }
        if ($info == "siren") { if (VATEL == 1) { $liste=utf8_encode($data[0][23]); }else{ $liste=$data[0][23];  } }
        if ($info == "siret") { if (VATEL == 1) { $liste=utf8_encode($data[0][24]); }else{ $liste=$data[0][24];  } }
        if ($info == "formejuridique") { if (VATEL == 1) { $liste=utf8_encode($data[0][25]); }else{ $liste=$data[0][25];  } }
        if ($info == "secteureconomique") { if (VATEL == 1) { $liste=utf8_encode($data[0][26]); }else{ $liste=$data[0][26];  } }
        if ($info == "INSEE") { if (VATEL == 1) { $liste=utf8_encode($data[0][27]); }else{ $liste=$data[0][27];  } }
        if ($info == "NAFAPE") { if (VATEL == 1) { $liste=utf8_encode($data[0][28]); }else{ $liste=$data[0][28];  } }
        if ($info == "NACE") { if (VATEL == 1) { $liste=utf8_encode($data[0][29]); }else{ $liste=$data[0][29];  } }
        if ($info == "typeorganisation") { if (VATEL == 1) { $liste=utf8_encode($data[0][30]); }else{ $liste=$data[0][30];  } }
        if ($info == "contact") { if (VATEL == 1) { $liste=utf8_encode($data[0][2]); }else{ $liste=$data[0][2];  } }
        if ($info == "fonction") { if (VATEL == 1) { $liste=utf8_encode($data[0][13]); }else{ $liste=$data[0][13];  } }
        if ($info == "activite") { if (VATEL == 1) { $liste=utf8_encode($data[0][6]); }else{ $liste=$data[0][6];  } }
        if ($info == "activite2") { if (VATEL == 1) { $liste=utf8_encode($data[0][15]); }else{ $liste=$data[0][15];  } }
        if ($info == "activite3") { if (VATEL == 1) { $liste=utf8_encode($data[0][16]); }else{ $liste=$data[0][16];  } }
        if ($info == "activiteprin") { if (VATEL == 1) { $liste=utf8_encode($data[0][7]); }else{ $liste=$data[0][7];  } }
        if ($info == "grphotelier") { if (VATEL == 1) { $liste=utf8_encode($data[0][20]); }else{ $liste=$data[0][20];  } }
        if ($info == "nbetoile") { if (VATEL == 1) { $liste=utf8_encode($data[0][21]); }else{ $liste=$data[0][21];  } }
        if ($info == "nbchambre") { if (VATEL == 1) { $liste=utf8_encode($data[0][1]); }else{ $liste=$data[0][1];  } }
        if ($info == "email") { if (VATEL == 1) { $liste=utf8_encode($data[0][10]); }else{ $liste=$data[0][10];  } }
        if ($info == "siteweb") { if (VATEL == 1) { $liste=utf8_encode($data[0][19]); }else{ $liste=$data[0][19];  } }
        if ($info == "information") { if (VATEL == 1) { $liste=utf8_encode($data[0][11]); }else{ $liste=$data[0][11];  } }


	
	if ($info == "lieu") {
		if (VATEL == 1) { 
			$liste=utf8_encode($data[0][3]);
		}else{
		       $liste=$data[0][3];
		}
	}
	if ($info == "ville") {
		if (VATEL == 1) {
			$liste=utf8_encode($data[0][5]);
		}else{
			$liste=$data[0][5];
		}
	}
	if ($info == "postal") { $liste=$data[0][4]; }
	if ($info == "pays") {
		if (VATEL == 1) {
	               $liste=utf8_encode($data[0][14]);
		}else{
		       $liste=$data[0][14];
		}
	}
	if ($info == "responsable") {
		if (VATEL == 1) {
	                $liste=utf8_encode($data[0][2]);
		}else{
	                $liste=$data[0][2];
		}
	}
	if ($info == "tel") { $liste=$data[0][8]; }
	if ($info == "fax") { $liste=$data[0][9]; }
		
	PgClose($cnx);
	print $liste;
	sleep(1);
}


?>
