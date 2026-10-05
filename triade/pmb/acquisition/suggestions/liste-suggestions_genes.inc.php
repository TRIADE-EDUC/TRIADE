<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: liste-suggestions_genes.inc.php,v 1.8.4.1 2025/03/05 10:21:40 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// popup d'impression PDF pour liste de suggestions
// reçoit : user_input, statut

global $class_path, $base_path, $acquisition_pdfsug_text_size, $acquisition_pdfsug_format_page, $acquisition_pdfsug_orient_page, $acquisition_pdfsug_marges_page, $msg;
global $acquisition_pdfsug_pos_titre, $acquisition_pdfsug_pos_date, $acquisition_pdfsug_tab_sug, $acquisition_pdfsug_pos_footer, $fpdf, $statut, $user_input, $num_categ, $pmb_pdf_font;

	require_once($class_path.'/suggestions.class.php');
	require_once($class_path.'/suggestions_origine.class.php');
	require_once($class_path.'/suggestions_map.class.php');
	require_once($class_path.'/suggestions_categ.class.php');
	require_once($class_path.'/analyse_query.class.php');
	require_once($base_path.'/includes/misc.inc.php');

	//paramètres modifiables-----------------------------------------------------------------------------

	if (!$acquisition_pdfsug_text_size) $fs = '8';	//Taille de la police
		else $fs = $acquisition_pdfsug_text_size;

	$format_page = explode('x',$acquisition_pdfsug_format_page);
	$largeur_page = !empty($format_page[0]) ? intval($format_page[0]) : 210;//largeur de page
	$hauteur_page = !empty($format_page[1]) ? intval($format_page[1]) : 297;//hauteur de page

	if(!$acquisition_pdfsug_orient_page) $orient_page = 'P';		//orientation page (P=portrait, L=paysage)
		else $orient_page = $acquisition_pdfsug_orient_page;

	$marges_page = explode(',', $acquisition_pdfsug_marges_page);
	$marge_haut = !empty($marges_page[0]) ? intval($marges_page[0]) : 10;//marge haut
	$marge_bas = !empty($marges_page[1]) ? intval($marges_page[1]) : 20;//marge bas
	$marge_droite = !empty($marges_page[2]) ? intval($marges_page[2]) : 10;//marge droite
	$marge_gauche = !empty($marges_page[3]) ? intval($marges_page[3]) : 10;//marge gauche

	$pos_titre = explode(',', $acquisition_pdfsug_pos_titre);
	$x_titre = !empty($pos_titre[0]) ? intval($pos_titre[0]) : 10;//Distance titre / bord gauche de page
	$y_titre = !empty($pos_titre[1]) ? intval($pos_titre[1]) : 10;//Distance titre / bord haut de page
	$l_titre = !empty($pos_titre[2]) ? intval($pos_titre[2]) : 100;//Largeur titre
	$h_titre = !empty($pos_titre[3]) ? intval($pos_titre[3]) : 10;//Hauteur titre
	$fs_titre = !empty($pos_titre[4]) ? intval($pos_titre[4]) : 16;//Police titre

	$pos_date = explode(',', $acquisition_pdfsug_pos_date);
	$x_date = !empty($pos_date[0]) ? intval($pos_date[0]) : 170;//Distance date / bord gauche de page
	$y_date = !empty($pos_date[1]) ? intval($pos_date[1]) : 10;//Distance date / bord haut de page
	$l_date = !empty($pos_date[2]) ? intval($pos_date[2]) : 0;//Largeur date
	$h_date = !empty($pos_date[3]) ? intval($pos_date[3]) : 6;//Hauteur date
	$fs_date = !empty($pos_date[4]) ? intval($pos_date[4]) : 8;//Police date

	$pos_tab = explode(',', $acquisition_pdfsug_tab_sug);
	$h_tab = !empty($pos_tab[0]) ? intval($pos_tab[0]) : 5;//Hauteur de ligne table suggestions
	$fs_tab = !empty($pos_tab[1]) ? intval($pos_tab[1]) : 6;//taile de la police table suggestions
	$x_tab = $marge_gauche;						//position table suggestions / bord droit page
	$y_tab = $marge_haut;						//position table suggestions / haut page sur pages 2 et +

	$pos_footer = explode(',', $acquisition_pdfsug_pos_footer);
	$y_footer = !empty($pos_footer[0]) ? intval($pos_footer[0]) : 15;//Distance footer / bas de page
	$fs_footer = !empty($pos_footer[1]) ? intval($pos_footer[1]) : 8;//Police footer

	$taille_doc=array($largeur_page,$hauteur_page);
	$w = $largeur_page-$marge_gauche-$marge_droite;
	$ourPDF = new $fpdf($orient_page, 'mm', $taille_doc);
	$ourPDF->Open();
	$ourPDF->SetMargins($marge_gauche, $marge_haut, $marge_droite);


	$sug_map = new suggestions_map();

	//On récupère les infos de la liste de suggestions
	if(!$statut) $statut= -1;

	$us=stripslashes($user_input);

	$mask=$sug_map->getMask_FILED();


	if(!$user_input) {
		$q = suggestions::listSuggestions(0, $statut, $num_categ, $mask);
	} else {
		$aq=new analyse_query(stripslashes($user_input),0,0,0,0);
		$q = suggestions::listSuggestions(0, $statut, $num_categ, $mask, 0, 0, $aq, $user_input);
	}
	$res = pmb_mysql_query($q);


	$ourPDF->addPage();
	$ourPDF->setFont($pmb_pdf_font);

	//Affichage date
	$date =  formatdate(today());
	$ourPDF->setFontSize($fs_date);
	$ourPDF->SetXY($x_date, $y_date);
	$ourPDF->Cell($l_date, $h_date, $date, 0, 0, 'L', 0);

	//Affichage titre
	$titre =  $msg['acquisition_sug_list'];
	$ourPDF->setFontSize($fs_titre);
	$ourPDF->SetXY($x_titre, $y_titre);
	$ourPDF->Cell($l_titre, $h_titre, $titre, 0, 0, 'L', 0);
	if ($us!='') {
		$ourPDF->Ln();
		$ourPDF->Cell($l_titre, $h_titre, "Critères de recherche: ".$us, 0, 0, 'L', 0);
	}
	if ($num_categ != '-1') {
		$ourPDF->Ln();
		$sug_categ= new suggestions_categ($num_categ);
		$ourPDF->Cell($l_titre, $h_titre, "Catégories de suggestions: ".$sug_categ->libelle_categ, 0, 0, 'L', 0);
	}
	if ($statut != '-1') {
		$ourPDF->Ln();
		$ourPDF->Cell($l_titre, $h_titre, "Etat des suggestions: ".$sug_map->getPdfComment($statut), 0, 0, 'L', 0);
	}

	//Affichage lignes suggestions
	$ourPDF->SetAutoPageBreak(false);
	$ourPDF->AliasNbPages();

	$ourPDF->SetFontSize($fs_tab);
	$ourPDF->SetFillColor(230);
	$ourPDF->Ln();
	$y = $ourPDF->GetY();
	$ourPDF->SetXY($x_tab,$y);

	$x_dat =  $x_tab;
	$w_dat = round($w*10/100);
	$x_tit = $x_dat + $w_dat;
	$w_tit = round($w*30/100);
	$x_edi = $x_tit + $w_tit;
	$w_edi = round($w*20/100);
	$x_qte = $x_edi + $w_edi;
	$w_qte = round($w*7/100);
	$x_com = $x_qte + $w_qte;
	$w_com = round($w*33/100);

	printEntete_genes();

	while ($row = pmb_mysql_fetch_object($res)){

		//recuperation origine
		$lib_orig = "";
		$typ_orig = "0";

		$sug = new suggestions($row->id_suggestion);
		$q = suggestions_origine::listOccurences($row->id_suggestion, '1');
		$list_orig = pmb_mysql_query($q);

		if (pmb_mysql_num_rows($list_orig)) {
			$row_orig = pmb_mysql_fetch_object($list_orig);
			$orig = $row_orig->origine;
			$typ_orig = $row_orig->type_origine;
		}

		//Récupération du nom du créateur de la suggestion
		switch($typ_orig){
			default:
			case '0' :
			 	$requete_user = "SELECT userid, nom, prenom FROM users where userid = '".$orig."' limit 1 ";
				$res_user = pmb_mysql_query($requete_user);
				$row_user=pmb_mysql_fetch_row($res_user);
				$lib_orig = $row_user[1];
				if ($row_user[2]) $lib_orig.= ", ".$row_user[2];
				break;
			case '1' :
			 	$requete_empr = "SELECT id_empr, empr_nom, empr_prenom, empr_adr1 FROM empr where id_empr = '".$orig."' limit 1 ";
				$res_empr = pmb_mysql_query($requete_empr);
				$row_empr=pmb_mysql_fetch_row($res_empr);
				$lib_orig = $row_empr[1];
				if ($row_empr[2]) $lib_orig.= ", ".$row_empr[2];
				break;
			case '2' :
				$lib_orig = $orig;
				break;
		}
		$lib_orig=trim($lib_orig);

		$col1="";
		$col1= formatdate($row->date_creation)."\n".$lib_orig;

		$col2="";
		if ($row->code !="") $col2=$row->code;
		$col2.= "\n".$row->titre;

		$col3="";
		$col30="";
		$col31="";
		if (trim($row->auteur)!="") $col30=trim($row->auteur);
		if (trim($row->editeur)!="") $col31 ="[".trim($row->editeur)."]";
		$col3=$col30;
		if ($col30!="" && $col31!="") $col3.="\n";
		$col3.=$col31;

		$col4=trim($row->nb);
		$col5=trim($row->commentaires);

		//Calcul hauteur ligne
		$h = $h_tab * max( 	$ourPDF->NbLines($w_dat, $col1),
							$ourPDF->NbLines($w_tit, $col2),
							$ourPDF->NbLines($w_edi, $col3),
							$ourPDF->NbLines($w_qte, $col4),
							$ourPDF->NbLines($w_com, $col5)
						 );
		$s = $y+$h;

		//Changement de page si on depasse
		if ($s > ($hauteur_page-$marge_bas)){

			$ourPDF->AddPage();
			$ourPDF->SetXY($x_tab, $y_tab);
			$y = $ourPDF->GetY();
			printEntete_genes();

		}

		//Affichage ligne
		$ourPDF->SetXY($x_dat, $y);
		$ourPDF->Rect($x_dat, $y, $w_dat, $h);
		$ourPDF->MultiCell($w_dat, $h_tab, $col1, 0, 'L');
		$ourPDF->SetXY($x_tit, $y);
		$ourPDF->Rect($x_tit, $y, $w_tit, $h);
		$ourPDF->MultiCell($w_tit, $h_tab, $col2, 0, 'L');
		$ourPDF->SetXY($x_edi, $y);
		$ourPDF->Rect($x_edi, $y, $w_edi, $h);
		$ourPDF->MultiCell($w_edi, $h_tab, $col3, 0, 'L');
		$ourPDF->SetXY($x_qte, $y);
		$ourPDF->Rect($x_qte, $y, $w_qte, $h);
		$ourPDF->MultiCell($w_qte, $h_tab, $col4, 0, 'L');
		$ourPDF->SetXY($x_com, $y);
		$ourPDF->Rect($x_com, $y, $w_com, $h);
		$ourPDF->MultiCell($w_com, $h_tab, $col5, 0, 'L');

		$y = $y+$h;

	}

	$y = $ourPDF->SetY($y);

	$ourPDF->SetAutoPageBreak(true, $marge_bas);
	$ourPDF->SetX($marge_gauche);
	$ourPDF->Ln();

	//Impression
	$ourPDF->OutPut();

	exit();

//Entete de tableau
function printEntete_genes() {

	global $msg;
	global $ourPDF, $y;
	global $x_tab,$y_tab,$h_tab;
	global $x_dat,$x_tit,$x_edi,$x_qte,$x_com ;
	global $w_dat,$w_tit,$w_edi,$w_qte,$w_com;
	global $hauteur_page, $marge_bas;

	//Calcul hauteur ligne
	$h = $h_tab * max( 	$ourPDF->NbLines($w_dat, $msg['acquisition_sug_cre']."\n".$msg['acquisition_sug_dat_orig']),
			$ourPDF->NbLines($w_tit,$msg['acquisition_sug_code']."\n".$msg['acquisition_sug_tit']),
			$ourPDF->NbLines($w_edi, $msg['acquisition_sug_aut']."\n[".$msg['acquisition_sug_edi']."]"),
			$ourPDF->NbLines($w_qte,$msg['acquisition_sug_qte']),
			$ourPDF->NbLines($w_com,$msg['acquisition_sug_com'])
			 );
	$s = $y+$h;

	//Changement de page si on dépasse
	if ($s > ($hauteur_page-$marge_bas)){

		$ourPDF->AddPage();
		$ourPDF->SetXY($x_tab, $y_tab);
		$y = $ourPDF->GetY();

	}

	//Affichage ligne
	$ourPDF->SetXY($x_dat, $y);
	$ourPDF->Rect($x_dat, $y, $w_dat, $h, 'FD');
	$ourPDF->MultiCell($w_dat, $h_tab, $msg['acquisition_sug_dat_cre']."\n".$msg['acquisition_sug_orig'], 0, 'L');
	$ourPDF->SetXY($x_tit, $y);
	$ourPDF->Rect($x_tit, $y, $w_tit, $h, 'FD');
	$ourPDF->MultiCell($w_tit, $h_tab, "Isbn\n".$msg['acquisition_sug_tit'], 0, 'L');
	$ourPDF->SetXY($x_edi, $y);
	$ourPDF->Rect($x_edi, $y, $w_edi, $h, 'FD');
	$ourPDF->MultiCell($w_edi, $h_tab, $msg['acquisition_sug_aut']."\n[".$msg['acquisition_sug_edi']."]", 0, 'L');
	$ourPDF->SetXY($x_qte, $y);
	$ourPDF->Rect($x_qte, $y, $w_qte, $h, 'FD');
	$ourPDF->MultiCell($w_qte, $h_tab, $msg['acquisition_sug_qte'], 0, 'L');
	$ourPDF->SetXY($x_com, $y);
	$ourPDF->Rect($x_com, $y, $w_com, $h, 'FD');
	$ourPDF->MultiCell($w_com, $h_tab, $msg['acquisition_sug_com'], 0, 'L');

	$y = $y+$h;
}
