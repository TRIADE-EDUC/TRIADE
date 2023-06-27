<?php

global $cnx;

function &imprimeNote($pdf,$idClasse,$id_eleve,$dateDebut,$anneeScolaire) {

	global $cnx;
	// recupe du nom de la classe
	$data=chercheClasse($idClasse);
	$classe_nom=preg_replace('/_/',' ',$data[0][1]);
	// recuperation des coordonnées
	// de l etablissement
	$data=visu_paramViaIdSite(chercheIdSite($idClasse));
	for($i=0;$i<count($data);$i++) {
	       	$nom_etablissement=trim($data[$i][0]);
	       	$adresse=trim($data[$i][1]);
	       	$postal=trim($data[$i][2]);
	       	$ville=trim($data[$i][3]);
       		$tel=trim($data[$i][4]);
	       	$mail=trim($data[$i][5]);
	}
	// fin de la recup

	$dateFin=date("d/m/Y");

	$ordre=ordre_matiere_visubull($idClasse,$anneeScolaire); // recup ordre matiere

	$eleveT=recupEleve($idClasse); // recup liste eleve
	for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
		// variable eleve
		if ($id_eleve != $eleveT[$j][4]) continue ;
	
		$nomEleve=trim(ucwords($eleveT[$j][0]));
		$prenomEleve=trim(ucfirst($eleveT[$j][1]));
		$lv1Eleve=$eleveT[$j][2];
		$lv2Eleve=$eleveT[$j][3];
		$idEleve=$eleveT[$j][4];
	
		$pdf->AddPage();
		$pdf->SetTitle("Période - $nomEleve $prenomEleve");
		$pdf->SetCreator("T.R.I.A.D.E.");
		$pdf->SetSubject("Relevé de notes"); 
		$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 

		// declaration variable
		$coordonne0=strtoupper($nom_etablissement);
		$coordonne1=$adresse;
		$coordonne2=$postal." - ".ucwords($ville);
		$coordonne3="Téléphone : ".$tel;
		$coordonne4="E-mail : ".$mail;

		$titre="<B><U>".LANGBULL20."</U>";

		$photo="data/image_eleve/".$idEleve.".jpg";
		$nbchaine=20;
		if (file_exists($photo)) {
			$nbchaine=40;
		}


		$nomEleve=strtoupper($nomEleve);
		$nomEleve=trunchaine("$nomEleve $prenomEleve",$nbchaine);

	
		$infoeleve=LANGBULL16." : <B>".$nomEleve."</B>";
		$infoeleve2=LANGELE4." : ";
		$infoeleveclasse=ucwords($classe_nom);

		$titrenote1=LANGPER17;
		$titrenote2=LANGBULL17;
		$titrenote3=LANGBULL18;

		$appreciation=LANGBULL19;
		$appreciation2="________________________________________________________________________________________________________________________";
		// FIN variables
	
		$xtitre=80;  // sans logo
		$xcoor0=3;   // sans logo
		$ycoor0=3;   // sans logo


		// mise en place du logo
		if (file_exists("./data/image_pers/logo_bull.jpg")) {
			$xlogo=3;
			$ylogo=3;
			$xcoor0=30;
			$ycoor0=3;
			$xtitre=90; // avec logo
			$logo="./data/image_pers/logo_bull.jpg";
			$pdf->Image($logo,$xlogo,$ylogo);
		}
		// fin du logo


		// Debut création PDF
		// mise en place des coordonnées
		$pdf->SetFont('Arial','',12);
		$pdf->SetXY($xcoor0,$ycoor0);
		$pdf->WriteHTML($coordonne0);
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($xcoor0,$ycoor0+5);
		$pdf->WriteHTML($coordonne1);
		$pdf->SetXY($xcoor0,$ycoor0+10);
		$pdf->WriteHTML($coordonne2);
		$pdf->SetXY($xcoor0,$ycoor0+15);
		$pdf->WriteHTML($coordonne3);
		$pdf->SetXY($xcoor0,$ycoor0+20);
		$pdf->WriteHTML($coordonne4);
		//fin coordonnees


		// insertion de la date
		$date=date("d/m/Y");
		$Pdate="Date: ".$date;
		$pdf->SetFont('Courier','',10);
		$pdf->SetXY(150,3);
		$pdf->WriteHTML($Pdate);
		// fin d'insertion

		// Titre
		$pdf->SetXY($xtitre,20);
		$pdf->SetFont('Courier','',18);
		$pdf->WriteHTML($titre);
		// fin titre

		// cadre du haut
		$pdf->SetFont('Arial','',11);
		$pdf->SetFillColor(220);
		$pdf->SetXY(15,35); // placement du cadre du nom de l eleve
		$pdf->MultiCell(184,20,'',1,'L',1);

		$photoeleve=image_bulletin($idEleve);
		$photo=$photoeleve;
		$xphoto=17;
		$yphoto=36;
		$photowidth=10.8;
		$photoheight=16.3;
		$Xv1=20;
		$Xv11=101;
		if (!empty($photo)) {
			$photo=$photoeleve;
			$pdf->Image($photo,$xphoto,$yphoto,$photowidth,$photoheight);
			$Xv1=20+9;
			$Xv11=110;
		}	

		$pdf->SetXY($Xv1,36); // placement du nom de l'eleve
		$pdf->WriteHTML($infoeleve);
		$pdf->SetXY($Xv1,48);
		$pdf->WriteHTML($infoeleve2);
		$pdf->SetX($Xv1+18);
		$pdf->WriteHTML($infoeleveclasse);


		// adresse de l'élève
		// elev_id, nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numeroEleve, class_ant, date_naissance, regime, civ_1, civ_2
		$dataadresse=chercheadresse($idEleve);
		for($ik=0;$ik<=count($dataadresse);$ik++) {
			$nomtuteur=$dataadresse[$ik][1];
			$prenomtuteur=$dataadresse[$ik][2];
			$adr1=$dataadresse[$ik][3];
			$code_post_adr1=$dataadresse[$ik][4];
			$commune_adr1=$dataadresse[$ik][5];
			$numero_eleve=$dataadresse[$ik][9];
			$datenaissance=$dataadresse[$ik][11];
			if ($datenaissance != "") {  $datenaissance=dateForm($datenaissance); }
			$regime=$dataadresse[$ik][12];
			$class_ant=trunchaine($dataadresse[$ik][10],20);
			
			$pdf->SetXY($Xv1,40); 
			$pdf->SetFont('Arial','',8);
		}


		// cadre des notes
		// ---------------
		// Barre des titres
		$pdf->SetFont('Arial','',9);
		$pdf->SetFillColor(220);
		$pdf->SetXY(15,60);
		$pdf->MultiCell(184,8,'',1,'C',1);
		$pdf->SetXY(19,62);
		$pdf->WriteHTML($titrenote1);
		$pdf->SetX(60);
		$pdf->WriteHTML($titrenote2);
		$pdf->SetX(125);
		$pdf->WriteHTML($titrenote3);
		// fin des titres

		// Mise en place des matieres et nom de prof
		$Xmat=15;
		$Ymat=68;
		$Xmatcont=15;
		$Ymatcont=$Ymat+1;

		$Xprof=55;
		$Yprof=$Ymat;
		$XnomProfcont=56;
		$YnomProfcont=$Ymatcont;
		$Xnote=$Xmat + 70;
		$Ynote=$Ymat;
		$YnotVal=$Ynote ;
		$YsujetNote=$YnotVal + 2;
		$hauteurMatiere=8;

	
	
		for($i=0;$i<count($ordre);$i++) {
			$matiere=chercheMatiereNom($ordre[$i][0]);
			$nomprof=recherche_personne2($ordre[$i][1]);
			$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);
			if ($verifGroupe) { continue; } // verif pour l eleve de l affichage de la matiere

			if ($Ynote >= 250) {
				$pdf->AddPage();
				$Xmat=15;
				$Ymat=20;
				$Xmatcont=16;
				$Ymatcont=20;
	
				$Xprof=55;
				$Yprof=$Ymat;
				$XnomProfcont=56;
				$YnomProfcont=$Ymatcont;
				$Xnote=$Xmat + 70;
				$Ynote=$Ymat;
				$YnotVal=$Ynote;
				$YsujetNote=$YnotVal + 2;
			}

			$XnotVal=$Xnote + 1 ;
			$XsujetNote=$XnotVal;
			// mise en place des notes
			$note=recupNote2($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$ordre[$i][1]);
			// note,elev_id,code_mat,date,sujet,typenote,notationsur	
			$aaa=0;
			for($b=0;$b<count($note);$b++) {
				$aaa++;
				$noteaff=$note[$b][0];
				$sujet=$note[$b][4];
				$typenote=$note[$b][5];
				$notationSur=$note[$b][6];
				if ($ordre[$i][0] == $note[$b][2]) {
					$note[$b][0]=trim(number_format($note[$b][0],2,'.',''));
					if ($note[$b][0] == "-1.00") { $noteaff="abs"; }
					if ($note[$b][0] == "-2.00") { $noteaff="disp"; }
					if ($note[$b][0] == "-3.00") { $noteaff="";$sujet=""; }
					if ($note[$b][0] == "-4.00") { $noteaff="DNN"; }
					if ($note[$b][0] == "-5.00") { $noteaff="DNR"; }
					if ($note[$b][0] == "-6.00") { $noteaff="VAL"; }
					if ($note[$b][0] == "-7.00") { $noteaff="NVAL"; }
					if (trim($typenote) == "en") {
						$noteaff=recherche_note_en($note[$b][0]);
					}else{
						$noteaff=preg_replace('/\.00/',"",$noteaff);
						$noteaff=preg_replace('/\.50/',".5",$noteaff);
						if ($notationSur == "") { $notationSur=20; }
						if (($noteaff != "") && ($noteaff != "abs") &&($noteaff != "disp") &&($noteaff != "DNN")&& ($noteaff != "DNR") && ($noteaff != "VAL") ){  
							$notationSur="$notationSur"; 
						}else{
							$notationSur="";
						}
					}
					$pdf->SetFont('Arial','',7);
					$pdf->SetXY($XnotVal,$YnotVal);
					if ($notationSur != "") {
						$moyP=$notationSur/2;
						if ($noteaff < $moyP) {
							$font="<FONT COLOR='RED'>";
							$fontF='</FONT>';
						}else{
							$font='';
							$fontF='';
						}
						$notationSur="/$notationSur";
					}
					$pdf->WriteHTML("$font$noteaff$fontF$notationSur");
					$font='';
					$fontF='';
					$pdf->SetXY($XsujetNote,$YsujetNote);
					$pdf->SetFont('Arial','',7);
					$sujet = strtolower(substr($sujet, 0, 6));  // decoupe la chaine du sujet
					$pdf->WriteHTML("<i>".$sujet."</i>");
					$pdf->SetXY($XnotVal,$YnotVal);
					$XnotVal=$XnotVal + 10;
					$XsujetNote=$XsujetNote + 10;
					if ($aaa > 10) {
						$aaa=0;
						break;
					}
					continue;
				}
			}
			$YnotVal=$YnotVal + $hauteurMatiere;
			$YsujetNote=$YsujetNote + $hauteurMatiere;

			// mise en place des matieres
			$pdf->SetFont('Arial','',8);
			$pdf->SetXY($Xmat,$Ymat);
			$pdf->MultiCell(40,$hauteurMatiere,'',1,'L',0);
			$pdf->SetXY($Xmatcont,$Ymatcont);
			$pdf->WriteHTML(trunchaine($matiere,20));
			$Ymat=$Ymat + $hauteurMatiere;
			$Ymatcont=$Ymatcont + $hauteurMatiere;
			// mise en place des noms professeurs
			$pdf->SetFont('Arial','',8);
			$pdf->SetXY($Xprof,$Yprof);
			$pdf->MultiCell(30,$hauteurMatiere,'',1,'L',0);
			$pdf->SetXY($XnomProfcont,$YnomProfcont);
			$pdf->WriteHTML(ucwords(trunchaine($nomprof,20)));
			$Yprof=$Yprof + $hauteurMatiere;
			$YnomProfcont=$YnomProfcont + $hauteurMatiere;
			// mise en place du cadre note
			$pdf->SetXY($Xnote,$Ynote);
			$pdf->MultiCell(114,$hauteurMatiere,'',1,'',0);
			$Ynote=$Ynote + $hauteurMatiere;
		}




		if ($Ynote >= 240) {
			$pdf->AddPage();
			$Ynote=15;
		}

		// fin de la mise en place des matiere
		$pos1=$Ynote + 5 ;
		// coordonée
		$pos2 = $pos1 + 5;
		$pos3 = $pos2 + 8;
		$pos4 = $pos3 + 5;
		
		// fin notes
		// --------
		//
		
		// Info abs, rtd et retenu
		$nb_retenue=0;
		$data_1=affRetenuTotal_par_eleve_trimestre($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin));
		if (count($data_1) > 0) { $nb_retenue=count($data_1); }
	
	
		$data_2=nombre_abs($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin));
		// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure
		$cumulabs=0;
		$cumulabsheure=0;
		$nbabs=count($data_2);
		for($ja=0;$ja<count($data_2);$ja++) {
			if ($data_2[$ja][4] > 0) {
				$cumulabs=$cumulabs + $data_2[$ja][4];
			}else {
				$cumulabsheure= $cumulabsheure + $data_2[$ja][7];
			}
		}
		
		$data_3=nombre_retard($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin));
		$cumulrtds=0;
		$nbrtd=count($data_3);
		for($ja=0;$ja<count($data_3);$ja++) {
			$nbminute=preg_replace('/mn/','',$data_3[$ja][5]);
			if (preg_match('/[0-9]h/',$data_3[$ja][5])) {
				$minute=0;
				list($heure,$minute) =preg_split('/h/', $data_3[$ja][5], 2 );
				$nbminute=$heure * 60 + $minute;
			}
			$cumulrtds=$cumulrtds + $nbminute ;
		}	
 
	$pdf->SetXY(15,$pos1-3); // +5 en bas
	$pdf->MultiCell(184,8,"Nombre d'absences : $nbabs (Cumul : $cumulabs jours / $cumulabsheure heure(s)) - Nombre de retards :  $nbrtd  (Cumul : $cumulrtds minutes) - Nombre de retenues : $nb_retenue ",0,'L',0);

	}

	return($pdf);
}


function &imprimeABSRts($pdf,$id_classe,$id_eleve,$dateDebut1) {
	global $cnx;

	$dateFin1=date("d/m/Y");

	$dateDebut=dateFormBase($dateDebut1);
	$dateFin=dateFormBase($dateFin1);
	$absrtd="tous";


	$listclasse=affClasse();
	
	for($c=0;$c<count($listclasse);$c++) {
		$idClasse=$listclasse[$c][0];

		if ($idClasse != $id_classe) continue ;

		$eleveT=recupEleve($idClasse);      // recup liste eleve
		$classe=chercheClasse_nom($idClasse);
		$nbeleve=count($eleveT);
	
		$idsite=chercheIdSite($idClasse);	
		$dataInfo=visu_paramViaIdSite($idsite);
		$nom_etablissement=trim($dataInfo[0][0]);
		$adresse=trim($dataInfo[0][1]);
		$postal=trim($dataInfo[0][2]);
		$ville=trim($dataInfo[0][3]);
		$tel=trim($dataInfo[0][4]);
		$mail=trim($dataInfo[0][5]);
		$directeur=trim($dataInfo[0][6]);
		$urlsite=trim($dataInfo[0][7]);

		for($i=0;$i<count($eleveT);$i++) {
			$idEleve=$eleveT[$i][4];
			if ($idEleve != $id_eleve) continue;
		
			$xcoor0="5";
			$ycoor0="5";
			$pdf->AddPage();
	
			$pdf->SetFont('Arial','',12);
			$pdf->SetXY($xcoor0,$ycoor0);
			$pdf->WriteHTML("$nom_etablissement");
		
			$pdf->SetXY(175,$ycoor0);
			$pdf->WriteHTML(dateDMY());
	
			$ycoor0+=10;
			$pdf->SetXY($xcoor0,$ycoor0);
	
			if ($absrtd == "tous") $tt="LISTE DES ABSENCES ET RETARDS";
			if ($absrtd == "abs") $tt="LISTE DES ABSENCES";
			if ($absrtd == "rtd") $tt="LISTE DES RETARDS";
	
			$pdf->WriteHTML("$tt");
			$ycoor0+=10;
			$pdf->SetXY($xcoor0,$ycoor0);
			$info="Période : ".$dateDebut1." au ".$dateFin1;
			$pdf->MultiCell(70,7,"$info",0,'L',0);
	
			$nomEleve=strtoupper($eleveT[$i][0]);
			$prenomEleve=ucfirst($eleveT[$i][1]);
			$nomprenomEleve=trunchaine("$nomEleve $prenomEleve",25);
	
			$absNonJustifie=nombre_absNonJustifie($idEleve,$dateDebut,$dateFin);
			$absJustifie=nombre_absJustifie($idEleve,$dateDebut,$dateFin);
			$rtdNonJustifie=nombre_retardNonJustifie($idEleve,$dateDebut,$dateFin);
			$rtdJustifie=nombre_retardJustifie($idEleve,$dateDebut,$dateFin);
			$absJustifieMaladie=nombre_absJustifieMaladie($idEleve,$dateDebut,$dateFin);
	
			$pdf->SetXY($xcoor0+80,$ycoor0);
			$pdf->MultiCell(100,7,"Nom élève : $nomprenomEleve",0,'L',0);
			$ycoor0+=10;
	
			if (($absrtd == "tous") || ($absrtd == "abs")) {
				// Absent le  Pendant   Créneaux  Motif
				$pdf->SetFillColor(230,230,255);
				$pdf->SetXY($xcoor0,$ycoor0);
				$pdf->MultiCell(30,7,"Absent le",1,'L',1);
				$pdf->SetXY($xcoor0+=30,$ycoor0);
				$pdf->MultiCell(30,7,"Durant",1,'L',1);
				$pdf->SetXY($xcoor0+=30,$ycoor0);
				$pdf->MultiCell(30,7,"Créneaux",1,'L',1);
				$pdf->SetXY($xcoor0+=30,$ycoor0);
				$pdf->MultiCell(70,7,"Matière",1,'L',1);
				$pdf->SetXY($xcoor0+=70,$ycoor0);
				$pdf->MultiCell(40,7,"Motif",1,'L',1);
	
				// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure, id_matiere, time, justifier,creneaux
				$listeabs=affAbsence2_via_date($idEleve,$dateDebut1,$dateFin1);
				$ycoor0+=7;
				for($j=0;$j<count($listeabs);$j++) {
					$pdf->SetFont('Arial','',7);
					$xcoor0="5";
					$matiere=chercheMatiereNom($listeabs[$j][8]);
					$dateabs=dateForm($listeabs[$j][1]);
					$pendant=$listeabs[$j][4];
					if ($pendant > 0) { $pendant.=" Jour(s)"; }
					if ($pendant == -1) { $pendant=$listeabs[$j][7]." Heure(s)"; }
					$creneaux=$listeabs[$j][11];
					list($null,$deb,$fin)=preg_split('/#/',$creneaux); 
					$creneaux="$deb - $fin";
					$motif=$listeabs[$j][6];
					if (trim($creneaux) == ": - :") { $creneaux="non précisé";}
		
		
					$dataRattrapage=recupRattrappage($listeabs[$j][12]); // date,heure_depart,duree,valider
			                $infoRattrapage="";
	        		        for($k=0;$k<count($dataRattrapage);$k++) {
		                	        $rattragefait=($dataRattrapage[$k][3] == 1) ? LANGOUI : LANGNON;
		                        	$infoRattrapage.="\n- Rattrapage le ".dateForm($dataRattrapage[$k][0])." à ".timeForm($dataRattrapage[$k][1])." durant ".timeForm($dataRattrapage[$k][2])." Effectuer : $rattragefait";
			                }
		
					$pdf->SetXY($xcoor0,$ycoor0);	
					$pdf->MultiCell(30,7,"$dateabs",1,'L',0);
					$pdf->SetXY($xcoor0+=30,$ycoor0);
					$pdf->MultiCell(30,7,"$pendant",1,'L',0);
					$pdf->SetXY($xcoor0+=30,$ycoor0);
					$pdf->MultiCell(30,7,"$creneaux",1,'L',0);
					$pdf->SetXY($xcoor0+=30,$ycoor0);
					$pdf->MultiCell(70,7,"",1,'L',0);
					$pdf->SetXY($xcoor0,$ycoor0+0.5);
					$pdf->MultiCell(70,3,"$matiere",0,'L',0);
					$pdf->SetXY($xcoor0+=70,$ycoor0);
					$pdf->MultiCell(40,7,trunchaine("$motif",80),1,'L',0);
					if ($infoRattrapage != "") {
						$pdf->SetXY($xcoor0,$ycoor0+=7);
						$pdf->MultiCell(80,4*$k,'',1,'L',0);
						$pdf->SetXY($xcoor0,$ycoor0-2);
						$pdf->MultiCell(80,3,"$infoRattrapage",0,'L',0);
						$ycoor0+=4*$k;
					}else{
						$ycoor0+=7;
					}
					if ($ycoor0 >= 250) { $pdf->AddPage(); $ycoor0=10; }
		        	        for($k=0;$k<count($dataRattrapage);$k++) {
	        	              		$date=$dataRattrapage[$k][0];
						$heure_depart=$dataRattrapage[$k][1];
						$duree=$dataRattrapage[$k][2];
						$valider=$dataRattrapage[$k][3];
	        	                	$date=dateForm($date);
	        	                	$heure_depart=timeForm($heure_depart);
	               		         	$duree=timeForm($duree);
		                        	$valider = ($valider == 1) ? LANGOUI : LANGNON;
	              			}
				}
			}


			if (($absrtd == "tous") || ($absrtd == "rtd")) {
	
				// Absent le  Pendant   Créneaux  Motif
				$ycoor0+=20;
				$xcoor0=5;
				$pdf->SetFont('Arial','',12);
				$pdf->SetFillColor(230,230,255);
				$pdf->SetXY($xcoor0,$ycoor0);
				$pdf->MultiCell(30,7,"Retard le",1,'L',1);
				$pdf->SetXY($xcoor0+=30,$ycoor0);
				$pdf->MultiCell(30,7,"Durant",1,'L',1);
				$pdf->SetXY($xcoor0+=30,$ycoor0);
				$pdf->MultiCell(30,7,"Créneaux",1,'L',1);
				$pdf->SetXY($xcoor0+=30,$ycoor0);
				$pdf->MultiCell(70,7,"Matière",1,'L',1);
				$pdf->SetXY($xcoor0+=70,$ycoor0);
				$pdf->MultiCell(40,7,"Motif",1,'L',1);
		
				$listeabs=affRetard_via_date($idEleve,$dateDebut1,$dateFin1); 
				//elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere,justifier,heure_saisie,creneaux, idrattrapage
				$ycoor0+=7;
				for($j=0;$j<count($listeabs);$j++) {
					$pdf->SetFont('Arial','',7);
					$xcoor0="5";
					$matiere=chercheMatiereNom($listeabs[$j][7]);		
					$dateabs=dateForm($listeabs[$j][2]);
					$pendant=$listeabs[$j][5];
					$creneaux=$listeabs[$j][10];
					list($null,$deb,$fin)=preg_split('/#/',$creneaux); 
					$creneaux="$deb - $fin";
					$motif=$listeabs[$j][6];
					if (trim($creneaux) == ": - :") { $creneaux="non précisé";}
					$dataRattrapage=recupRattrappage($listeabs[$j][11]); // date,heure_depart,duree,valider
			                $infoRattrapage="";
		        	        for($k=0;$k<count($dataRattrapage);$k++) {
		                	        $rattragefait=($dataRattrapage[$k][3] == 1) ? LANGOUI : LANGNON;
		                        	$infoRattrapage.="\n- Rattrapage le ".dateForm($dataRattrapage[$k][0])." à ".timeForm($dataRattrapage[$k][1])." durant ".timeForm($dataRattrapage[$k][2])." Effectuer : $rattragefait";
			                }
					$pdf->SetXY($xcoor0,$ycoor0);	
					$pdf->MultiCell(30,7,"$dateabs",1,'L',0);
					$pdf->SetXY($xcoor0+=30,$ycoor0);
					$pdf->MultiCell(30,7,"$pendant",1,'L',0);
					$pdf->SetXY($xcoor0+=30,$ycoor0);
					$pdf->MultiCell(30,7,"$creneaux",1,'L',0);
					$pdf->SetXY($xcoor0+=30,$ycoor0);
					$pdf->MultiCell(70,7,"",1,'L',0);
					$pdf->SetXY($xcoor0,$ycoor0);
		                        $pdf->SetFont('Arial','',6);
					$pdf->MultiCell(70,3,"$matiere",0,'L',0);
					$pdf->SetXY($xcoor0+=70,$ycoor0);
					$pdf->MultiCell(40,7,trunchaine("$motif",70),1,'L',0);
					$pdf->SetFont('Arial','',12);
		                     	if ($infoRattrapage != "") {
		                                $pdf->SetXY($xcoor0,$ycoor0+=7);
		                                $pdf->MultiCell(80,4*$k,'',1,'L',0);
		                                $pdf->SetXY($xcoor0,$ycoor0-2);
		                                $pdf->MultiCell(80,3,"$infoRattrapage",0,'L',0);
		                                $ycoor0+=4*$k;
		                        }else{
		                                $ycoor0+=7;
		                        }
		                        $pdf->SetFont('Arial','',12);
					if ($ycoor0 >= 250) { $pdf->AddPage(); $ycoor0=10; }
		        	        for($k=0;$k<count($dataRattrapage);$k++) {
		                      		$date=$dataRattrapage[$k][0];
						$heure_depart=$dataRattrapage[$k][1];
						$duree=$dataRattrapage[$k][2];
						$valider=$dataRattrapage[$k][3];
		                        	$date=dateForm($date);
		                        	$heure_depart=timeForm($heure_depart);
		                        	$duree=timeForm($duree);
		                        	$valider = ($valider == 1) ? LANGOUI : LANGNON;
		                	}
				}
			}
		}
	}
	return($pdf);
}



function &imprimeSavoirEtre($pdf,$id_classe,$id_eleve,$dateDebut1,$anneeScolaire) {

	global $cnx;
	$data=recupSavoirEtre($id_eleve,$id_classe,$anneeScolaire); 
        // ponctualite,motivation,dynamisme,id,date,idpers

	$dateFin1=date("d/m/Y");
        $dateDebut=dateFormBase($dateDebut1);
        $dateFin=dateFormBase($dateFin1);


	$xcoor0="5";
        $ycoor0="5";
        $pdf->AddPage();
        $pdf->SetFont('Arial','',12);
        $pdf->SetXY($xcoor0,$ycoor0);
	
	$pdf->WriteHTML("Savoir & Etre");
        $ycoor0+=10;
        $pdf->SetXY($xcoor0,$ycoor0);
        $info="Période : ".$dateDebut1." au ".$dateFin1;
        $pdf->MultiCell(70,7,"$info",0,'L',0);

        $nomEleve=strtoupper(recherche_personne_nom($id_eleve,'ELE'));
        $prenomEleve=ucfirst(recherche_personne_prenom($id_eleve,'ELE'));
        $nomprenomEleve=trunchaine("$nomEleve $prenomEleve",45);

        $pdf->SetXY($xcoor0+80,$ycoor0);
        $pdf->MultiCell(100,7,"Nom Etudiant : $nomprenomEleve",0,'L',0);
       
	$ycoor0+=10;
	$xcoor0=10;	

        $pdf->SetFont('Arial','',12);
	$pdf->SetFillColor(230,230,255);
        $pdf->SetXY($xcoor0,$ycoor0);
        $pdf->MultiCell(90,7,"Intérêt manifesté pour son travail",1,'L',1);
        $pdf->SetXY($xcoor0+=90,$ycoor0);
        $pdf->MultiCell(50,7,"Saisie Par ",1,'L',1);
        $pdf->SetXY($xcoor0+=50,$ycoor0);
        $pdf->MultiCell(30,7,"Date de saisie",1,'L',1);
	$ycoor0+=7;
	for($i=0;$i<count($data);$i++) {
        	$pdf->SetFont('Arial','',9);
		$ponc=$data[$i][0];
		$date=dateForm($data[$i][4]);
		$qui=recherche_personne2($data[$i][5]);
		if (trim($ponc) == "") continue;

		$xcoor0=10;	
		$pdf->SetFillColor(255);
		$pdf->SetXY($xcoor0,$ycoor0);
        	$pdf->MultiCell(90,15,"",1,'L',1);
		$pdf->SetXY($xcoor0,$ycoor0+1);
        	$pdf->MultiCell(90,3,"$ponc",0,'L',0);
	        $pdf->SetXY($xcoor0+=90,$ycoor0);
	        $pdf->MultiCell(50,15,"",1,'L',1);
	        $pdf->SetXY($xcoor0,$ycoor0-3);
	        $pdf->MultiCell(50,15,"$qui",0,'L',0);
        	$pdf->SetXY($xcoor0+=50,$ycoor0);
	        $pdf->MultiCell(30,15,"",1,'L',1);
        	$pdf->SetXY($xcoor0,$ycoor0-3);
	        $pdf->MultiCell(30,15,"$date",0,'L',0);
		$ycoor0+=15;
		if ($ycoor0 >= 250) {
			$pdf->AddPage(); 
			$ycoor0=15;
		}

	}	


	$xcoor0=10;
        $pdf->SetFillColor(230,230,255);
        $pdf->SetFont('Arial','',12);
        $pdf->SetXY($xcoor0,$ycoor0);
        $pdf->MultiCell(90,7,"Méthode et soin apportés",1,'L',1);
        $pdf->SetXY($xcoor0+=90,$ycoor0);
        $pdf->MultiCell(50,7,"Saisie Par ",1,'L',1);
        $pdf->SetXY($xcoor0+=50,$ycoor0);
        $pdf->MultiCell(30,7,"Date de saisie",1,'L',1);
        $ycoor0+=7;


        for($i=0;$i<count($data);$i++) {
        	$pdf->SetFont('Arial','',9);
                $ponc=$data[$i][1];
                $date=dateForm($data[$i][4]);
                $qui=recherche_personne2($data[$i][5]);
                if (trim($ponc) == "") continue;

                $xcoor0=10;
                $pdf->SetFillColor(255);
                $pdf->SetXY($xcoor0,$ycoor0);
                $pdf->MultiCell(90,15,"",1,'L',1);
                $pdf->SetXY($xcoor0,$ycoor0+1);
                $pdf->MultiCell(90,3,"$ponc",0,'L',0);
                $pdf->SetXY($xcoor0+=90,$ycoor0);
                $pdf->MultiCell(50,15,"",1,'L',1);
                $pdf->SetXY($xcoor0,$ycoor0-3);
                $pdf->MultiCell(50,15,"$qui",0,'L',0);
                $pdf->SetXY($xcoor0+=50,$ycoor0);
                $pdf->MultiCell(30,15,"",1,'L',1);
                $pdf->SetXY($xcoor0,$ycoor0-3);
                $pdf->MultiCell(30,15,"$date",0,'L',0);
                $ycoor0+=15;
		if ($ycoor0 >= 250) {
			$pdf->AddPage(); 
			$ycoor0=15;
		}

        }

        $pdf->SetFont('Arial','',12);
	$xcoor0=10;
        $pdf->SetFillColor(230,230,255);
        $pdf->SetXY($xcoor0,$ycoor0);
        $pdf->MultiCell(90,7,"Aptitude à écouter",1,'L',1);
        $pdf->SetXY($xcoor0+=90,$ycoor0);
        $pdf->MultiCell(50,7,"Saisie Par ",1,'L',1);
        $pdf->SetXY($xcoor0+=50,$ycoor0);
        $pdf->MultiCell(30,7,"Date de saisie",1,'L',1);
        $ycoor0+=7;

        for($i=0;$i<count($data);$i++) {
        	$pdf->SetFont('Arial','',9);
                $ponc=$data[$i][2];
                $date=dateForm($data[$i][4]);
                $qui=recherche_personne2($data[$i][5]);
                if (trim($ponc) == "") continue;

                $xcoor0=10;
                $pdf->SetFillColor(255);
                $pdf->SetXY($xcoor0,$ycoor0);
                $pdf->MultiCell(90,15,"",1,'L',1);
                $pdf->SetXY($xcoor0,$ycoor0+1);
                $pdf->MultiCell(90,3,"$ponc",0,'L',0);
                $pdf->SetXY($xcoor0+=90,$ycoor0);
                $pdf->MultiCell(50,15,"",1,'L',1);
                $pdf->SetXY($xcoor0,$ycoor0-3);
                $pdf->MultiCell(50,15,"$qui",0,'L',0);
                $pdf->SetXY($xcoor0+=50,$ycoor0);
                $pdf->MultiCell(30,15,"",1,'L',1);
                $pdf->SetXY($xcoor0,$ycoor0-3);
                $pdf->MultiCell(30,15,"$date",0,'L',0);
		
	        $ycoor0+=15;
		if ($ycoor0 >= 250) { 
			$pdf->AddPage(); 
	                $ycoor0=15;
		}

        }

	return($pdf);
}


?>
