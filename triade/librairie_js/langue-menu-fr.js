/***************************************************************************
 *                              T.R.I.A.D.E.
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) S.A.R.L. T.R.I.A.D.E. 
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
// Ne pas d&eacute;passer 24 caract&egrave;res

function ucfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}


langtitre0="TRIADE-COPILOT";
langtitre1="Actualit&eacute;";
langtitre2="Service Triade";
langtitre3="Forum";
langtitre4="Rechercher";
langtitre5="Quitter";
langtitre6="Assistance";
langtitre7="Administrateur&nbsp;Triade";
//-------
langmenuparent1="Messagerie";
langmenuparent11="Ecrire";
langmenuparent12="R&eacute;ception";
langmenuparent13="Messages du P. Principal";
//-------
langmenuparent2="Vie Scolaire";
langmenuparent21="Notes";
langmenuparent22="Retards";
langmenuparent23="Absences";
langmenuparent24="Dispenses";
langmenuparent25="Discipline";
langmenuparent26="Devoirs maison";
langmenuparent27="Cahier de textes";
//-------
langmenuparent3=INTITULEDIRECTION;
langmenuparent31="Emploi du temps";
langmenuparent32="Circulaires adm.";
langmenuparent33="D&eacute;l&eacute;gu&eacute;s";
langmenuparent34="Calendrier";
langmenuparent35="D.S.T.";
//------
// #########################
langmenuprof1="Messagerie";
langmenuprof11="Ecrire";
langmenuprof12="R&eacute;ception";
langmenuprof13="Message envoy&eacute;s";
//------
langmenuprof2="Notes";
langmenuprof21="Ajouter devoirs";
langmenuprof22="Visualiser notes";
langmenuprof23="Modifier devoirs";
langmenuprof24="Visualiser devoirs";
langmenuprof25="Supprimer devoirs";
langmenuprof26="Devoirs scolaires";
langmenuprof27="Cahier de textes";
//------
langmenuprof3=""+INTITULEELEVE+"s";
langmenuprof30="Rtd / Abs "+intituleeleve+"s";
langmenuprof32="Fiches "+intituleeleve+"s";
//------
langmenuprof4=INTITULEDIRECTION;
langmenuprof40="R&eacute;servation salle";
langmenuprof41="Emploi du Temps";
langmenuprof42="Circulaires adm.";
langmenuprof43="Calendrier";
langmenuprof44="D.S.T.";
//------
// #########################
langmenuadmin00="Agenda";
langmenuadmin0="Messagerie";
langmenuadmin01="News - Actualit&eacute;s";
langmenuadmin02="Ecrire";
langmenuadmin023="Corbeille";
langmenuadmin03="R&eacute;ception";
langmenuadmin04="Messages envoy&eacute;s";
langmenuadmin05="News Audio";
//------
langmenuadmin1="Gestion";
langmenuadmin11=INTITULEDIRECTION;
langmenuadmin12="Vie Scolaire ";
langmenuadmin13=ucfirst(intituleenseignant)+"s";
langmenuadmin14="Suppl&eacute;ants";
langmenuadmin15=INTITULEELEVE+"s";
langmenuadmin16="Groupes";
langmenuadmin17=ucfirst(intituleclasse);
langmenuadmin18="Mati&egrave;res";
langmenuadmin19="Sous-mati&egrave;res";
//------
langmenuadmin2="Suppression";
//------
langmenuadmin3="Affectation";
langmenuadmin31="Emploi du temps";
langmenuadmin33="Mise en place";
langmenuadmin34="Visualisation";
langmenuadmin35="Modification";
langmenuadmin36="Suppression";
langmenuadmin37="Edition Enseignant";
//------
langmenuadmin4="Etablissement";
langmenuadmin40="R&eacute;servation salle";
langmenuadmin41="Restauration";
langmenuadmin42="Internat";
langmenuadmin43="C.D.I.";
//------
langmenuadmin5=""+INTITULEELEVE+"s";
langmenuadmin51="Recherche";
langmenuadmin52="Listing";
langmenuadmin53="Gestion certificats";
langmenuadmin54="Gestion groupes";
langmenuadmin55="Carnet de notes";
langmenuadmin56="Dossier m&eacute;dical";
langmenuadmin57="Gestion D.S.T.";
langmenuadmin58="Gestion planning";
langmenuadmin59="Abs,retards du jour";
langmenuadmin510="Gestion abs,retards";
langmenuadmin511="Gestion dispenses";
langmenuadmin512="Retenues du jour";
langmenuadmin513="Gestion discipline";
langmenuadmin514="Gestion circulaires";
langmenuadmin515="S.M.S.";
langmenuadmin516="Gestion W.A.P.";
langmenuadmin517="Gestion Stage Pro"; 
langmenuadmin518="Trombinoscopes";
langmenuadmin519="Code barre";
// --------------
langmenuadmin6="Bulletins";
langmenuadmin61="Param&eacute;trage";
langmenuadmin62="D&eacute;finir p&eacute;riodes T.";
langmenuadmin63="Imprimer bulletins";
langmenuadmin64="Imprimer p&eacute;riodes";
langmenuadmin65="Vid&eacute;o-projecteur";
langmenuadmin69="Visa vie scolaire";
// --------------
langmenuadmin7="Gestion banni&egrave;res";
langmenuadmin71="Ajout banni&egrave;re";
langmenuadmin72="Visualisation";
// --------------
langmenuadmin8="Etablissement";
langmenuadmin81="Importer";
langmenuadmin82="Mise &agrave; jour";
langmenuadmin83=""+INTITULEELEVE+" sans cls";
langmenuadmin84="Archivage";
langmenuadmin85="V&eacute;rification";
// --------------
langmenuadmin9="Nouvelle ann&eacute;e";
langmenuadmin91="Chgment de "+intituleclasse;
langmenuadmin92="Purger infos";
// --------------
// #########################
langmenuscolaire0="Vie Scolaire";
langmenuscolaire01="Abs,retards du jour";
langmenuscolaire02="Gestion abs,retards";
langmenuscolaire03="Gestion dispenses";
langmenuscolaire04="Retenues du jour";
langmenuscolaire05="Gestion discipline";
// --------------
langmenuscolaire1=INTITULEDIRECTION;
langmenuscolaire11="Liste "+intituleeleve+"s";
langmenuscolaire12="Emploi du temps";
langmenuscolaire13="Recherche "+intituleeleve+"";
langmenuscolaire14="Gestion calendrier";
langmenuscolaire15="Gestion circulaires";
langmenuscolaire16="Gestion D.S.T.";
// #########################
// pied de page
if (footer != "") { 
	if (footerlien != "") {
		langmenupied="<a href='"+footerlien+"' target='_blank'>"+footer+"</a>"; 
	}else{
		langmenupied=footer; 
	}
	langmenupied+="<br>Pour visualiser ce site de façon optimale : r&eacute;solution minimale : 800x600 <br>"; 
}else{
	langmenupied="<p> La <b>T</b>ransparence et la <b>R</b>apidit&eacute; de l'<b>I</b>nformatique <b>A</b>u service <b>D</b>e l'<b>E</b>nseignement<br>Pour visualiser ce site de façon optimale : r&eacute;solution minimale : 800x600 <br> T.R.I.A.D.E. © 2024 - Tous droits r&eacute;serv&eacute;s";
}
img_logo_pied="<img src='./image/commun/triade-xhtml.jpg' alt='XHTML'>  <img src='./image/commun/triade-w3C.jpg' alt='w3C'> <img src='./image/commun/triade-css.png' alt='css' > <a href='http://www.triade-educ.com/accueil/don-triade.php' target='_blank' ><img border='0' src='./image/commun/triade_paypal.png' alt='Paypal' ></a><br /><br />";
 
// --------------
// #########################


langmenuadmin38="Resp. Niveau";
langmenuprof28="Comm. Bulletins";
langmenuadmin520="Gestion &eacute;tude";
langmenuprof45="Sanctions "+intituleeleve+"s";
langmenuadmin38="Config Note USA";
langmenuadmin44="Historique cmd";
langmenuadmin45="Diaporama";
langmenuadmin46="Param&eacute;trage";
langmenueleve1="P&eacute;dagogie";
langmenueleve2="Cours / Leçon";
langmenueleve3="Exercices";

langmenuadmin06="Stockage";
langmenuadmin07="Espace Priv&eacute;";


langmenuadmin66="Imprimer tableau";

img_logo_pied="<img src='./image/commun/triade-xhtml.jpg' alt='XHTML'>  <img src='./image/commun/triade-w3C.jpg' alt='w3C'> <img src='./image/commun/triade-css.png' alt='css' > <a href='http://www.triade-educ.com/accueil/don-triade.php' target='_blank' ><img border='0' src='./image/commun/triade_paypal.png' alt='Paypal' ></a><br /><br />";


langmenuadmin521="Mes Flux RSS";
langmenuadmin522="Envoyer un SMS";

langmenuadmin67="V&eacute;rifier bulletins";
langmenuadmin68="Visa Direction";

langmenuadmin523="Sanctions du jour";

if (VATEL == "1") {
	langmenuadmin70="Suppl.	au diplôme";
}else{
	langmenuadmin70="Examens / Brevets";
}

langtitre2bis="Nouvelle&nbsp;fen&ecirc;tre";
langmenuprof29="Informations";
langmenuadmin73="Note Vie Scolaire";

langmenuadmin47="Modules annexes";
langmenuadmin48="Carnet de Suivi";
langmenuprof31="Prof P. / Instituteur";
langmenuadmin32="Prof P. / Instituteur";
langmenuadmin20="Tuteurs de stage";

// non corrig&eacute;

langmenuadmin40bis="R&eacute;servation &eacute;quip.";
langmenuadmin41bis="D&eacute;l&eacute;gu&eacute;s";
langmenuparent36="D&eacute;l&eacute;gu&eacute;s";
langmenuprof46="Plan de "+intituleclasse;
langmenuadmin524="Notanet";
langmenuadmin39="Entretien individuel";
langmenuadmin525="Vacation Ens.";

// --------------
langmenuadmin102="Pr&eacute;-inscriptions";
//-------


langmenuadmin101="R&eacute;glement interne";
langmenuprof47="Stage Pro.";
langmenuadmin90="Comptabilit&eacute;";
langmenuadmin91bis="Fiche d'&eacute;tat";
langmenuadmin92bis="Encaissement";
langmenuadmin93="Rappel";
langmenuadmin94="Config. Ech&eacute;ancier";
langmenuadmin95="Quantification";
langmenuadmin96="Exporter";
langmenuadmin97="Emargement";
langmenuadmin98="Unit&eacute;s enseignmts";
langmenuadmin99="Nouvelle ann&eacute;e";
langmenuadmin100="Intra-MSN";
langmenuadmin103="E-Learning";
langmenuadmin525bis="Etat Versement";
langmenuadmin104="Personnels";
langmenueleve517="Stage Pro."; 
langmenueleve518="Publipostage"; 

langmenuadmin01A="News 1er Page";
langmenuadmin01B="News actualit&eacute;";
langmenuadmin01C="News d&eacute;filant";


langmenupersonnel1="Acc&egrave;s Modules";
langmenupersonnel2="Cantine";
langmenuadmin399="Entretien Ens.";
langmenuadmin531="Centrale Stages";

langmenuadmin9000="Module financier";
langmenuadmin9001="Inscription";
langmenuadmin9002="Param&egrave;trage";
langmenuadmin9003="Paiements";
langmenuadmin9004="Editions";

langmenuadmin9100="Module chambres";
langmenuadmin9101="Planning";
langmenuadmin9102="Reservations";
langmenuadmin9103="Param&egrave;trage";

langmenuadmin022="Brouillon";
langmenuadmin055="News Vid&eacute;o";
langmenuadmin1011="Boursier";
langmenuadmin526="Fourniture scolaire";
langmenuadmin527="Gestion ressources";

langmenugeneral01="Votre compte";
langmenuparent34bis="Planning";
langmenuadmin528="Organigramme";
langmenuadmin529="Année Scolaire";
langmenuadmin911=intituleclasse+" antérieure";

langmenuadmin912="Savoir-être";
langmenuadmin913="Eval. Enseignant";

langmenugeneral01a="M&eacute;mo";

