<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET 
 *   Site                 : http://www.triade-educ.org
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

// fichier pour langue cote admin.
// POUR TOUS -------------------
// brmozilla($_SESSION[navigateur]);
//

function TextNoAccentLicence2($str){
	$str = htmlentities($str, ENT_NOQUOTES, 'utf-8');
        $str = preg_replace('#&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        $str = preg_replace('#&[^;]+;#', '', $str);
        return($str);
}

if (!defined(INTITULEDIRECTION)) { define("INTITULEDIRECTION","direction"); }
if (!defined(INTITULEELEVE)) { define("INTITULEELEVE","élève"); }
if (!defined(INTITULEELEVES)) { define("INTITULEELEVES","élèves"); }
if (!defined(INTITULECLASSE)) { define("INTITULECLASSE","classe"); }
if (!defined(INTITULEENSEIGNANT)) { define("INTITULEENSEIGNANT","enseignant"); }



define("CLICKICI","Cliquez ici");
define("VALIDER","Valider");
define("LANGTP22","INFORMATION - Demande de D.S.T. à confimer !");
define("LANGTP3"," calendrier DST ");
define("LANGCHOIX","-- Sélectionnez --");
define("LANGCHOIX2","aucune ".INTITULECLASSE);
define("LANGCHOIX3","-- Sélectionnez --");
define("LANGOUI","oui");
define("LANGNON","non");
define("LANGFERMERFEN","Fermer la fenêtre");
define("LANGATT","ATTENTION !");
define("LANGDONENR","Donnée(s) enregistrée(s)");
define("LANGPATIENT","Merci de bien vouloir patienter");
define("LANGSTAGE1",'Gestion des stages professionnels');
define("LANGINCONNU",'inconnu'); // doit être identique que langinconnu cote javascript
define("LANGABS",'abs');
define("LANGRTD",'rtd');
define("LANGRIEN",'rien');
define("LANGENR",'Enregistrer');
define("LANGRAS1",'Aujourd\'hui, le ');
define("LANGDATEFORMAT",'jj/mm/aaaa');

//------------------------------
// titre
//-------------------------------

define("LANGTITRE3","Message défilant dans le haut de la page");
define("LANGTITRE4","Message défilant dans le bandeau ");
define("LANGTITRE5","Réception message");
define("LANGTITRE6","Création d'un compte ".INTITULEDIRECTION);
define("LANGTITRE7","Création d'un compte vie scolaire");
define("LANGTITRE8","Création d'un compte ".INTITULEENSEIGNANT."");
define("LANGTITRE9","Création d'un compte suppléant");
define("LANGTITRE10","Création d'un compte ".INTITULEELEVE);
define("LANGTITRE11","Création d'un groupe"); //
define("LANGTITRE12","Création d'une ".INTITULECLASSE); //
define("LANGTITRE13","Création d'une matière"); //
define("LANGTITRE14","Création d'une sous-matière"); //
define("LANGTITRE16","Création d'affectation");
define("LANGTITRE17","Création d'affectation pour la ".INTITULECLASSE);
define("LANGTITRE18","Visualisation d'affectation");
define("LANGTITRE19","Modification d'affectation");
define("LANGTITRE20","Modification de l'affectation pour la ".INTITULECLASSE);
define("LANGTITRE21","Suppression d'affectation");
define("LANGTITRE22","Importation d'un fichier ASCII (txt,csv) ");
define("LANGTITRE23","Liste des retards non justifiés ");
define("LANGTITRE24","Ajouter une dispense");
define("LANGTITRE25","Lister / Modifier les  dispenses");
define("LANGTITRE26","Supprimer une dispense");
define("LANGTITRE27","Gestion dispenses -  Planification");
define("LANGTITRE28","Affichage / Modification des dispenses");
define("LANGTITRE29","Consultation des ".INTITULECLASSE."s" );
define("LANGTITRE30","Recherche d'un ".INTITULEELEVE);
define("LANGTITRE31","Importation du fichier GEP");
define("LANGTITRE32","Trombinoscope des ".INTITULEELEVE."s");
define("LANGTITRE33","Certificat de scolarité");

//------------------------------
define("LANGTE1","Titre");
define("LANGTE2","du");
define("LANGTE3","de");
define("LANGTE4","Nombre de caractères");
define("LANGTE5","Objet");
define("LANGTE6","A");
define("LANGTE6bis","Aux parents de ");
define("LANGTE7","Date");
define("LANGTE8","Suppression messages");
define("LANGTE9","lu");
define("LANGTE10","jusqu'au :");
define("LANGTE11","au ");
define("LANGTE12","le ");
define("LANGTE13","à");
define("LANGTE14","Au groupe ");

//------------------------------
define("LANGFETE","Bonne Fête aux ");
define("LANGFEN1","Evénement(s) du jour");
define("LANGFEN2","D.S.T. du jour");
//------------------------------
define("LANGLUNDI","Lundi");
define("LANGMARDI","Mardi");
define("LANGMERCREDI","Mercredi");
define("LANGJEUDI","Jeudi");
define("LANGVENDREDI","Vendredi");
define("LANGSAMEDI","Samedi");
define("LANGDIMANCHE","Dimanche");
// ------------------------------
define("LANGMESS1","Envoi d'un message - le ");
define("LANGMESS3","Message à la vie scolaire : ");
define("LANGMESS4","Message à un ".INTITULEENSEIGNANT." : ");
define("LANGMESS6","Message(s) envoyé(s)");
define("LANGMESS7","Actualité enregistrée");
define("LANGMESS8","Message(s) envoyé(s)");
define("LANGMESS9","Répondre au message - le ");
define("LANGMESS10",'Les dates trimestrielles ne sont pas enregistrées.');
define("LANGMESS11",'Veuillez prévenir la '.INTITULEDIRECTION.'.');
define("LANGMESS12",'afin de valider les dates trimestrielles.');
define("LANGMESS13",'Veuillez cliquer <a href="definir_trimestre.php">ici</a>');
define("LANGMESS14",'Les affectations de cette '.INTITULECLASSE.'  ne sont pas enregistrées.');
define("LANGMESS15",'Veuillez cliquer <a href="affectation_creation_key.php">ici</a>');
define("LANGMESS16",'afin de valider les affectations de cette '.INTITULECLASSE);
define("LANGMESS17","Configuration");
define("LANGMESS18","S");     // première lettre de la phrase suivante !!!
define("LANGMESS18bis","i plusieurs emails à déclarer,<br> séparer les emails par une virgule.");
define("LANGMESS19","Activé");
define("LANGMESS20","Configuration mise à jour");
define("LANGMESS21","Etre averti d'un message reçu sur votre messagerie ");
define("LANGMESS22","Envoyer message à un groupe <font class=T1>(Ens,Vs,Dir,Tuteur Stage)</font> : ");
define("LANGMESS23","Création d'un groupe mail ");
define("LANGMESS24","Indiquer les personnes du groupe ");
define("LANGMESS25","Sélectionner les différentes personnes en maintenant la touche"); //
define("LANGMESS26","Valider la création");
define("LANGMESS27","Groupe de mail créé");
define("LANGMESS28","Liste de vos groupes mail ");
define("LANGMESS29","Groupe ");
define("LANGMESS30","Liste des personnes ");
define("LANGMESS31","Message de ");
define("LANGMESS32","Vous avez actuellement ");
define("LANGMESS33","message(s) en attente ");

// -----------------------------
// bouton
// PAS DE -->' (cote) !!!!
define("LANGBTS","Suivant >");
define("LANGBT1","Enregistrer le message défilant");
define("LANGBT2","Enregistrer information");
define("LANGBT3","Quitter sans envoyer");
define("LANGBT4","Envoyer message");
define("LANGBT5","Patientez, S.V.P.");
define("LANGBT6","Supprimer les messages cochés");
define("LANGBT7","Enregistrer le compte");
define("LANGBT11","Liste des suppléants");
define("LANGBT12","Liste des groupes");
define("LANGBT13","Valider la ou les ".INTITULECLASSE."(s)");
define("LANGBT14","Enregistrer la création");
define("LANGBT15","Liste des ".INTITULECLASSE."s");
define("LANGBT16","Liste des matières");
define("LANGBT17","Enregistrer la sous-matière");
define("LANGBT18","Enregistrer le statut"); //
define("LANGBT19","Valider"); //
define("LANGBT20","Quitter sans enregistrer"); //
define("LANGBT21","Enregistrer affectation"); //
define("LANGBT22","Supprimer affectation"); //
define("LANGBT23","Envoyer le fichier"); //
define("LANGBT24","Recommencer"); //
define("LANGBT25","Réactualiser la page"); //
define("LANGBT26","Créer une ".INTITULECLASSE); //
define("LANGBT27","Planifier abs ou retard"); //
define("LANGBT28","Consulter"); //
define("LANGBT29","Supprimer abs ou retard"); //
define("LANGBT30","Valider la mise à jour"); //
define("LANGBT31","Valider");
define("LANGBT32","Supprimer dispenses");
define("LANGBT33","Modifier dispenses");
define("LANGBT34","Ajouter dispenses");
define("LANGBT35","Enregistrer la donnée de ");
define("LANGBT36","Dispense  modifiée --  L'équipe TRIADE");
define("LANGBT37","Transmettre information");
define("LANGBT38","Envoyer");
define("LANGBT39","Lancer la recherche");
define("LANGBT40","Récupération");
define("LANGBT41","Terminé");
define("LANGBT42","Valider les ".INTITULEELEVE."s non enregistrés");
define("LANGBT43","Imprimer le bulletin");
define("LANGBT44","Historique");
define("LANGBT45","Consulter la documentation");
define("LANGBT46","Enregistrer la photo");
define("LANGBT47","Autre changement");
define("LANGBT48","Quitter ce module");
define("LANGBT49","Editer toute la ".INTITULECLASSE);
define("LANGBT50","Supprimer");
define("LANGBT51","Valider demande D.S.T");
// -----------------------------
define("LANGCA1","M"); //
define("LANGCA1bis","essage pas encore lu"); // sans la première lettre
define("LANGCA2","M"); //
define("LANGCA2bis","essage déjà lu"); // sans la premiere lettre
define("LANGCA3","I"); //
define("LANGCA3bis","ndiquez le JJ/MM/AAAA  <BR> Dans le cas d\'une date non <BR>convenue, précisez la mention <br>"); // sans la premiere lettre
// -----------------------------
define("LANGNA1","Nom"); //
define("LANGNA2","Pr&eacute;nom"); //
define("LANGNA3","Mot&nbsp;de&nbsp;passe"); //
define("LANGNA4","Nouveau compte créé \\n\\n L'équipe TRIADE "); //
define("LANGNA5","Remplacement&nbsp;de&nbsp;"); //
// -----------------------------
define("LANGELE1","Renseignements sur l'".INTITULEELEVE); //
define("LANGELE2","Nom"); //
define("LANGELE3","Prénom"); //
define("LANGELE4","Classe"); //
define("LANGELE5","Option"); //
define("LANGELE6","Régime"); //
define("LANGELE7","Interne"); //
define("LANGELE8","Demi-pensionnaire"); //
define("LANGELE9","Externe"); //
define("LANGELE10","Date de naissance"); //
define("LANGELE11","Nationalité"); //
define("LANGELE12","Numéro étudiant"); //
// define("LANGELE12","Numéro national"); //
define("LANGELE13","Renseignements sur la famille"); //
define("LANGELE14","Adresse 1"); //
define("LANGELE15","Code postal"); //
define("LANGELE16","Commune"); //
define("LANGELE17","Adresse 2"); //
define("LANGELE18",""); //
define("LANGELE19",""); //
define("LANGELE20","Numéro de téléphone"); //
define("LANGELE21","Profession du père"); //
define("LANGELE22","Téléphone du père"); //
define("LANGELE23","Profession de la mère"); //
define("LANGELE24","Téléphone de la mère"); //
define("LANGELE25","Ecole antérieure"); //
define("LANGELE26","Nom de l'établissement"); //
define("LANGELE27","Numéro établissement"); //
define("LANGELE28","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." créé -- L'équipe TRIADE"); //
define("LANGELE29","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." déjà existant  -- L'équipe TRIADE"); //
//------------------------------------------------------------
define("LANGGRP1","Intitulé du groupe"); //
define("LANGGRP2","Indiquez les ".INTITULECLASSE."s pour la création du groupe"); //
define("LANGGRP3","Sélectionnez les différentes ".INTITULECLASSE."s en maintenant la touche"); //
define("LANGGRP4","Ctrl"); //
define("LANGGRP5","et en appuyant sur le bouton gauche de la souris."); //
define("LANGGRP6","Intitulé de la section"); //
define("LANGGRP7","Nouvelle ".INTITULECLASSE." créée -- L'équipe TRIADE"); //
define("LANGGRP8","Nouvelle matière créée -- L'équipe TRIADE"); //
define("LANGGRP9","Intitulé de la matière"); //
define("LANGGRP10","Nom de la sous-matière"); //
//------------------------------------------------------------
//------------------------------------------------------------
define("LANGAFF1","Affectation pour la ".INTITULECLASSE); //
define("LANGAFF2","!! La création d'affectation <u>supprime</u> toutes les notes de la ".INTITULECLASSE." !!</u>"); //
define("LANGAFF3","Affectation des ".INTITULECLASSE."s"); //
//------------------------------------------------------------
define("LANGPER1","Impression de période"); //
define("LANGPER2","Début de la période"); //
define("LANGPER3","Fin de la période"); //
define("LANGPER4","Section"); //
define("LANGPER5","Récupérer le fichier PDF"); //
define("LANGPER6",ucfirst(INTITULEENSEIGNANT)." "); //
define("LANGPER8","en ".INTITULECLASSE." de "); //
define("LANGPER9","Module d'affectation des ".INTITULECLASSE."s."); //
define("LANGPER10","ATTENTION ce module est à utiliser lors d'une nouvelle affectation,<br> il détruit toutes les notes des ".INTITULEELEVE."s  des ".INTITULECLASSE."s affectées."); //
define("LANGPER11","ATTENTION, les notes des ".INTITULECLASSE."s sélectionnées  seront supprimées. \\n Souhaitez-vous continuer ? \\n\\n Equipe TRIADE"); //
define("LANGPER12","Indiquez le code d'accès.");
define("LANGPER13","Vérification du code");
define("LANGPER14","Nombre de matières");
define("LANGPER15","Création d'affectation pour la ".INTITULECLASSE);
define("LANGPER16","Nb");
define("LANGPER17","Mati&egrave;re");
define("LANGPER18",ucfirst(INTITULEENSEIGNANT));
define("LANGPER19","Coef");
define("LANGPER20","Groupe");
define("LANGPER21","Langue");
define("LANGPER22","Imprimer cette page");
define("LANGPER23","affectation");
define("LANGPER23bis","réussie");  // affectation xxxx réussie
define("LANGPER24","interrompue"); // affectation xxxx interrompue
define("LANGPER25","Classe");
define("LANGPER26","Visualisation");
define("LANGPER27","Visualiser");
define("LANGPER28","Visualisation d'affectation pour la ".INTITULECLASSE);
define("LANGPER29","!! La modification d'affectation <u>supprime</u> toutes les notes de la ".INTITULECLASSE." !!");
define("LANGPER30","Modifier");
define("LANGPER31","Modifier l'affectation");
define("LANGPER32","Modification d'affectation");
define("LANGPER32bis","interrompue"); // Modification d'affectation xxxx interrompue
define("LANGPER33","Suppression de l'affectation  pour la ");
define("LANGPER34","!! La suppression d'affectation <u>supprime</u> toutes les notes de la ".INTITULECLASSE." !!</u>");
define("LANGPER35","Affectation de la ".INTITULECLASSE);
define("LANGPER35bis","supprimée"); // Affectation de la classe  xxxx supprimée
//------------------------------------------------------------------------------
define("LANGIMP1","Importation d'une base existante ");
define("LANGIMP2","Indiquer le type du fichier à importer ");
define("LANGIMP3","Fichier ASCII ");
define("LANGIMP4","Fichier GEP ");
define("LANGIMP5","Module d'importation de fichier ASCII.");
define("LANGIMP6","Le fichier à transmettre <FONT color=RED><B>DOIT</B></FONT> contenir <FONT COLOR=red><B>45</B></FONT> champs <I>(vides ou non vides)</I> séparés par un même séparateur le \"<FONT color=red><B>;</B></font>\" <I>Soit la présence de 44 fois le caractère \"<FONT color=red><B>;</B></font>\"</I>");
define("LANGIMP7","Voici l'ordre des champs à indiquer : ");
define("LANGIMP8","nom");
define("LANGIMP9","prénom");
define("LANGIMP10",INTITULECLASSE);
define("LANGIMP11","régime");
define("LANGIMP12","date naissance");
define("LANGIMP13","nationalité");
define("LANGIMP14","nom tuteur");
define("LANGIMP15","prénom tuteur");

define("LANGIMP16","adresse&nbsp;1");
define("LANGIMP18","code postal&nbsp;1");
define("LANGIMP19","commune&nbsp;1");

define("LANGIMP17","adresse&nbsp;2");
define("LANGIMP18_2","code postal&nbsp;2");
define("LANGIMP19_2","commune&nbsp;2");


define("LANGIMP20","téléphone");
define("LANGIMP21","profession père");
define("LANGIMP22","téléphone profession père");
define("LANGIMP23","profession mère");
define("LANGIMP24","téléphone profession mère");
define("LANGIMP25","numéro établissement");

define("LANGIMP26","lv1");
define("LANGIMP27","lv2");
define("LANGIMP28","option");
define("LANGIMP29","Numéro ".INTITULEELEVE);
define("LANGIMP30","ATTENTION, la destruction de la base sera automatique. \\n Souhaitez-vous continuer ? \\n\\n L\'Equipe TRIADE");
define("LANGIMP31","ATTENTION : ce module est à utiliser lors de la première utilisation,<br> il détruit toutes les informations des ".INTITULEELEVE."s (notes, bulletins, vie scolaire).<br /> * champ obligatoire");
define("LANGIMP39","Indiquer le fichier à transmettre ");
define("LANGIMP40","Fichier transmis -- L'équipe TRIADE ");
define("LANGIMP41","Le nombre de champs n'est pas respecté ");
define("LANGIMP42","Indiquer pour chaque référence la ".INTITULECLASSE." correspondante ");
define("LANGIMP43","Fichier non enregistré ");
// ------------------------------------------------------------------------------
define("LANGABS1","Gestion absences - retards du jour");
define("LANGABS2","Planifier une absence ou retard");
define("LANGABS3","Indiquer le nom de l'".INTITULEELEVE);
define("LANGABS4","Lister les absences ou retards non justifiés");
define("LANGABS5","Absences non justifiées");
define("LANGABS6","Retards non justifiés");
define("LANGABS7","Visualiser et/ou modifier une absence ou retard");
define("LANGABS8","Indiquer le nom de l'".INTITULEELEVE);
define("LANGABS9","Afficher et/ou supprimer une absence ou retard");
define("LANGABS10","aucun éléve dans la base de données");
define("LANGABS11","Abs/Rtd");
define("LANGABS12","Motif");
define("LANGABS13","En retard le");
define("LANGABS14","Rtd");
define("LANGABS15","Abs");
define("LANGABS16","Annuler");
define("LANGABS17","Modifier abs ou retard");
define("LANGABS18","Absent&nbsp;du&nbsp;");
define("LANGABS19","au&nbsp;");
define("LANGABS20","Abs/Rtd");
define("LANGABS21","Durée");
define("LANGABS22","Motif");
define("LANGABS23","Heure / Date");
define("LANGABS24","Mise en place des absences ou retards en  Classe de ");
define("LANGABS25","Gestion Absence - Retard");
define("LANGABS26","Gestion Absence - Retard  Planification");
define("LANGABS27","Enregistrer la donnée de ");
define("LANGABS28","Donnée(s) Enregistrée(s) ");
define("LANGABS29","D"); //premiere lettre
define("LANGABS29bis","ispensé(e) de :"); //suite
define("LANGABS30","Disp");
define("LANGABS31",INTITULECLASSE." de ");
define("LANGABS32","R"); //premiere lettre
define("LANGABS32bis","etard "); //suite
define("LANGABS33","en");
define("LANGABS34","de");
define("LANGABS35","Absence - Retard - dispense  du ");
define("LANGABS36","Mise à jour");
define("LANGABS37","Imprimer les absences, dispenses, retards, du jour ");
define("LANGABS38","T&eacute;l.");
define("LANGABS39","Tél. Prof Père ");
define("LANGABS40","Tél. Prof Mère");
define("LANGABS41","Tél. Dom ");
define("LANGABS42","Absent(e)  du ");
define("LANGABS43","pendant ");
define("LANGABS44","Jour(s) ");
define("LANGABS45","Enregistrer la mise à jour ");
define("LANGABS46","à partir du ");

define("LANGDISP8","Suppression dispense");
//----------------------------------------------------------------------------
define("LANGPROJ1","Choix de la ".INTITULECLASSE);
define("LANGPROJ2","Choix du trimestre");
define("LANGPROJ3","Trimestre 1");
define("LANGPROJ4","Trimestre 2");
define("LANGPROJ5","Trimestre 3");
define("LANGPROJ6","<font class=T2>Aucun ".INTITULEELEVE." dans cette ".INTITULECLASSE."</font>");
define("LANGPROJ7","Nombre de retards");
define("LANGPROJ8"," Cumul");
define("LANGPROJ9","Discipline");
define("LANGPROJ10","minutes");
define("LANGPROJ11","Nbr de retenues");
define("LANGPROJ12","attr.&nbsp;par&nbsp;");
define("LANGPROJ13","Liste");
define("LANGPROJ14","Moy ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
define("LANGPROJ15","Moy Classe");
define("LANGPROJ16","Moyenne ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
// ----------------------------------------------------------------------------
define("LANGDISP1","<font class=T2>aucun ".INTITULEELEVE." à ce nom</font>");
define("LANGDISP2","Motif");
define("LANGDISP3","Certificat médical");
define("LANGDISP4","Période&nbsp;du&nbsp;");
define("LANGDISP5","en matière ");
define("LANGDISP6","Heure de dispense ");
define("LANGDISP7","<B><font color=red>I</font></B>ndiquez le JJ/MM/AAAA  <BR> dans les 2 champs");
define("LANGDISP9","Affichage <b>complet</B> des dispenses");
define("LANGDISP10","En");
// ----------------------------------------------------------------------------
define("LANGASS1","TRIADE assistance");
define("LANGASS2","Vous propose un  service pour vous dépanner, vous aider dans votre utilisation  de TRIADE.<br /><br />Vous avez un problème sur un des services de TRIADE, n'hésitez pas à nous transmettre par le formulaire qui suit, les informations sur le service en question. Nos ingénieurs se chargeront de vérifier ce service.");
define("LANGASS3","Membre concerné");
define("LANGASS4","Administration");
define("LANGASS5",ucfirst(INTITULEENSEIGNANT));
define("LANGASS6","Vie Scolaire");
define("LANGASS6bis","Parent");
define("LANGASS7","Action");
define("LANGASS8","Création");
define("LANGASS9","Visualisation");
define("LANGASS10","Suppression");
define("LANGASS11","Autre");
define("LANGASS12","Service");
define("LANGASS13","Compte utilisateur");
define("LANGASS14","Messagerie");
define("LANGASS15","Affectation");
define("LANGASS16","Base de données");
define("LANGASS17","Classe");
define("LANGASS18","Matière");
define("LANGASS19","Recherche");
define("LANGASS20","D.S.T.");
define("LANGASS21","Planning");
define("LANGASS22","Dispense");
define("LANGASS23","Discipline");
define("LANGASS24","Circulaire");
define("LANGASS25","Bulletin");
define("LANGASS26","Période");
define("LANGASS27","Commentaire");
define("LANGASS28","TRIADE assistance vous remercie pour votre aide.");
define("LANGASS29","Equipe TRIADE.");
define("LANGASS30","L'équipe TRIADE à votre service");
define("LANGASS31","TRIADE est un produit unique et inédit, aussi, n'hésitez pas à nous transmettre vos conseils et suggestions afin que le site répondre aux attentes réelles des utilisateurs ! Merci à vous :-)");
define("LANGASS32","Livre d'or");
define("LANGASS33","Votre témoignage en direct : inscrivez vos remarques sur notre livre d'or.");
define("LANGASS34","Votre message nous a été envoyé, nous ne manquerons pas de vous répondre.<br> <BR>Merci d'utiliser TRIADE et à bientôt.<BR><BR><BR><UL><UL>L'équipe TRIADE.<BR>");
define("LANGASS35","Autre");
define("LANGASS36","SMS");
define("LANGASS37","WAP");
define("LANGASS38","Trombinoscope");
define("LANGASS39","Code barre");
define("LANGASS40","Stage Pro.");
// -----------------------------------------------------------------------------
define("LANGRECH1","<font class=T2>aucun ".INTITULEELEVE." dans la ".INTITULECLASSE."</font>");
define("LANGRECH2","Recherche de ");
define("LANGRECH3","<font class=T2>aucun ".INTITULEELEVE." pour cette recherche</font>");
define("LANGRECH4","Information / Modification");
// ---------------------------------------------------------------------------------
define("LANGBASE1","ATTENTION : ce module est à utiliser lors de la première utilisation,<br> il détruit toutes les informations des ".INTITULEELEVE."s  (notes, bulletins, vie scolaire).");
define("LANGBASE2"," Les fichiers à importer DOIVENT être au format dbf ");
define("LANGBASE3","Voici la liste des fichiers ");
define("LANGBASE4","Module d'importation des fichiers GEP ");
define("LANGBASE5","Importation d'une base GEP ");
define("LANGBASE6","Total d'".INTITULEELEVE."s dans le fichier DBF ");
define("LANGBASE7","Total d'".INTITULEELEVE."s en  ".INTITULECLASSE);
define("LANGBASE8","Total d'".INTITULEELEVE."s sans  ".INTITULECLASSE);
define("LANGBASE9","Récupération des mots de passe  ");
define("LANGBASE10","Impossible d'ouvrir le fichier F_ele.dbf");
define("LANGBASE11","Base de données traitée -- L'équipe TRIADE");
define("LANGBASE12","Le fichier sélectionné n'est pas valide !");
define("LANGBASE13","Voici la liste des mots de passe");
define("LANGBASE14","Récupérer la liste  en sélectionnant l'ensemble des lignes et effectuez un copier/coller dans un fichier \"txt\".");
define("LANGBASE15","Puis via excel ou OpenOffice, récupérer le fichier \"txt\"  en précisant le point virgule comme séparateur de champs.");
define("LANGBASE17"," Attention : les mots de passe ne sont accessibles que sur <br />cette page !! Pensez à récuperer la liste <b>AVANT</b> de Terminer ");
define("LANGBASE18","INFORMATION NON DISPONIBLE");
// -----------------------------------------------------------------------------------------------------------------------
define("LANGBULL1","Impression  bulletin trimestriel");
define("LANGBULL2","Indiquez la ".INTITULECLASSE);
define("LANGBULL3","Année scolaire");
define("LANGBULL4","<a href=\"#\" onclick=\"open('https://www.adobe.com/fr/','_blank','')\"><b><FONT COLOR=red>ATTENTION</FONT></B> Besoin de l'outil <B>Adobe Acrobat Reader</B>.  Logiciel et téléchargement gratuits  cliquez <B>ICI</B></A>");
// -----------------------------------------------------------------------------------------------------------------------
define("LANGPARENT1","aucun message");
define("LANGPARENT2","Aucun délégué affecté pour le moment");
define("LANGPARENT3","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."(s) délégué(s)");
define("LANGPARENT4","Parent(s) délégué(s)");
define("LANGPARENT5","Liste des délégués");
//----------------------------------------------------------------------//
define("LANGPUR3","ATTENTION: ce module est à utiliser <br>lorsque vous souhaitez effacer des données TRIADE.");
define("LANGPUR4","ATTENTION, Vous rentrez dans un module qui par la suite supprimera des données que vous aurez choisi. \\n Souhaitez-vous continuer ? \\n\\n L\'équipe TRIADE");
define("LANGPUR5","Les données sont supprimées");
define("LANGPUR6","Information : La sélection \"".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."\" implique automatiquement la suppression des notes, absences, disciplines, dispenses, retards, entretiens");
define("LANGPUR7","Indiquer l'élément ou les éléments  à  détruire : ");
define("LANGPUR8","A conserver");
define("LANGPUR9","A Supprimer");
//----------------------------------------------------------------------//
define("LANGCHAN0","Module pour le changement de ".INTITULECLASSE." d'un ou de plusieurs ".INTITULEELEVE."s");
define("LANGCHAN1","ATTENTION: ce module est à utiliser <br>lorsque vous souhaitez effectuer <br> un changement de ".INTITULECLASSE." pour les ".INTITULEELEVE."s");
define("LANGCHAN3","ATTENTION, des données de l\'".INTITULEELEVE." \\n ou des ".INTITULEELEVE."s concerné(s) par le changement de ".INTITULECLASSE." seront supprimées");
//----------------------------------------------------------------------//
define("LANGGEP1",'Importation du fichier GEP');
define("LANGGEP2",'Indiquez le fichier');
//----------------------------------------------------------------------//
define("LANGCERT1"," télécharger ce certificat ");
//----------------------------------------------------------------------//
define("LANGPROFR1",'Indiquez des '.INTITULEELEVE.'s en retard');
define("LANGPROFR2",'Mise en place des retards  ');
define("LANGKEY1",'<font class=T1>Pas de clef d\'enregistrement </font>');
define("LANGDISP20",'Ajouter dispenses');
define("LANGPROFA",'<br><center><font size=2>Pas de clef d\'enregistrement </font><br><br>Veuillez contacter votre administrateur TRIADE, <br>afin de valider la demande d\'enregistrement de TRIADE. </center><br><br>');
define("LANGPROFB",'Ajout d\'une note en ');
define("LANGPROFC",'Confirmez l\'enregistrement des notes ');
define("LANGPROFD",'Validez l\'enregistrement des notes');
define("LANGPROFE",'&nbsp;&nbsp;<i><u>Info</u>: La touche Entrée vous permet de passer automatiquement à la note suivante.</i>');
define("LANGPROFF",'Ajout d\'une note');
define("LANGPROFG",'Indiquer la '.INTITULECLASSE);
//----------------------------------------------------------------------//
define("LANGMETEO1",'JOUR');
define("LANGMETEO2",'NUIT');
//----------------------------------------------------------------------//
define("LANGPROFP1","Message pour la ".INTITULECLASSE);
define("LANGPROFP2","Enregistrer le message");
define("LANGPROFP3","Message du Professeur Principal");
//----------------------------------------------------------------------//
// Module Stage Pro
define("LANGSTAGE1","Planification des stages ");
define("LANGSTAGE2","Visualiser les dates des stages ");
define("LANGSTAGE3","Ajouter ");
define("LANGSTAGE4","Affecter ");
define("LANGSTAGE5","Insertion d'une date de stage ");
define("LANGSTAGE6","Modification  d'une date de stage ");
define("LANGSTAGE7","Supprimer une date de stage ");
define("LANGSTAGE8","Gestion des entreprises ");
define("LANGSTAGE9","Visualiser les différentes entreprises ");
define("LANGSTAGE10","Ajouter une entreprise ");
define("LANGSTAGE11","Modifier une entreprise ");
define("LANGSTAGE12","Supprimer une entreprise ");
define("LANGSTAGE13","Gestion des ".INTITULEELEVE."s ");
define("LANGSTAGE14","Visualiser les ".INTITULEELEVE."s en entreprise ");
define("LANGSTAGE15","Affecter un ".INTITULEELEVE." à une entreprise ");
define("LANGSTAGE16","Modifier les caractéristiques d'un ".INTITULEELEVE." ");
define("LANGSTAGE17","Supprimer l'attribution d'un ".INTITULEELEVE." ");
define("LANGSTAGE18","Visualisation des dates de stage");
define("LANGSTAGE19","Stage");
define("LANGSTAGE20","Recherche d'entreprises");
define("LANGSTAGE21","Consulter les entreprises par activité");
define("LANGSTAGE22","Consultation des entreprises");
//----------------------------------------------------------------------//
define("LANGGEN1","Administration");
define("LANGGEN2","Vie Scolaire");
define("LANGGEN3",ucfirst(INTITULEENSEIGNANT)."s");
//----------------------------------------------------------------------//
define("LANGDST1","Demande de D.S.T");
define("LANGDST2","Bonjour, <br> <br> Votre demande de Devoir sur Table pour le ");
define("LANGDST3","<br><br><b>n\'est pas possible</b>, veuillez choisir une autre date ou nous contacter directement. <br><br> Merci");
define("LANGDST4","<br><br><b>est enregistrée</b> pour toute information supplémentaire, nous contacter. <br><br> Merci");
define("LANGDST5","pour le ");
define("LANGDST6","Sujet / Matière");
define("LANGDST7","Demande refusée");
define("LANGDST8","Demande accordée");
//----------------------------------------------------------------------//
define("LANGCALEN1","Evénement");
define("LANGCALEN2","Planning du ");
define("LANGCALEN3","Ajouter une entrée");
define("LANGCALEN4","Supprimer une entrée");
define("LANGCALEN5","Réactualiser la page");
define("LANGCALEN6","Calendrier des événements");
define("LANGCALEN7","En ".INTITULECLASSE." de ");
define("LANGCALEN8","Devoir de ");
define("LANGCALEN9","Devoir(s) Sur Table du jour");
//----------------------------------------------------------------------//
//module reservation
define("LANGRESA1","Gestion de l'équipement");
define("LANGRESA2","Gestion des salles");
define("LANGRESA3","Liste de l'équipement");
define("LANGRESA4","Liste des salles");
define("LANGRESA5","Ajouter un équipement");
define("LANGRESA6","Modifier un équipement");
define("LANGRESA7","Supprimer un équipement");
define("LANGRESA8","Ajouter salle");
define("LANGRESA9","Supprimer salle");
define("LANGRESA10","Supprimer une salle");
define("LANGRESA11","Réservation équipement / salle");
define("LANGRESA12","Réservation équipement");
define("LANGRESA13","Réservation salle");
define("LANGRESA14","Réserver");
define("LANGRESA15","Création d'un équipement");
define("LANGRESA16","Intitulé de l'équipement");
define("LANGRESA17","Enregistrer la création");
define("LANGRESA18","Informations complémentaires");
define("LANGRESA19","Equipement enregistré");
define("LANGRESA20","Création d'une salle");
define("LANGRESA21","Intitulé de la salle");
define("LANGRESA22","Salle enregistrée");
define("LANGRESA23","Supprimer salle");
define("LANGRESA24","Salle");
define("LANGRESA25","Supprimer la salle");
define("LANGRESA26","Salle supprimée");
define("LANGRESA27","une salle");
define("LANGRESA28","Impossible de supprimer cette salle. \\n\\n Salle affectée.  ");
define("LANGRESA29","Equipement supprimé");
define("LANGRESA30","Impossible de supprimer cet équipement. \\n\\n Equipement affecté.  ");
define("LANGRESA31","un équipement");
define("LANGRESA32","Supprimer équipement");
define("LANGRESA33","Equipement");
define("LANGRESA34","Supprimer un équipement");
define("LANGRESA35","Liste des équipements");
define("LANGRESA36","DATE");
define("LANGRESA37","De");
define("LANGRESA38","A");
define("LANGRESA39","Par qui");
define("LANGRESA40","Information");
define("LANGRESA41","Confirmer");
define("LANGRESA42","Confirmé");
define("LANGRESA43","Non&nbsp;Confirmé");
define("LANGRESA44","Planning Equipement");
define("LANGRESA45","Equipement");
define("LANGRESA46","Equipement déjà réservé à cette date");
define("LANGRESA47","Consulter le planning de réservation de cet équipement");
define("LANGRESA48","Réservation à partir du ");
define("LANGRESA49","En date du ");
define("LANGRESA50","Equipement réservé en attente de confirmation");
define("LANGRESA51","Planning Salle");
define("LANGRESA52","Salle");
define("LANGRESA53","Salle déjà réservée à cette date");
define("LANGRESA54","Salle réservée en attente de confirmation");
define("LANGRESA55","Consulter le planning de réservation pour cette salle");
define("LANGRESA56","Confirmer Réservation");
define("LANGRESA57","Planning");
define("LANGRESA58","Confirmer");
//----------------------------------------------------------------------//
define("LANGTTITRE1","Acc&egrave;s Membre");
define("LANGTTITRE2","Membre");
define("LANGTTITRE3","Activation du compte");
define("LANGTTITRE4","Merci de bien vouloir patienter");
//--------------
define("LANGTP1","Nom");
define("LANGTP2","Prénom");
define("LANGTP3","Mot de passe");
define("LANGTCONNEXION","Connexion");
define("LANGTERREURCONNECT","Erreur de connexion");
define("LANGTCONNECCOURS","Connexion en cours ");
define("LANGTFERMCONNEC","Cliquez ici pour la fermeture de votre compte");
define("LANGTDECONNEC","Déconnexion en cours");

define("LANGTBLAKLIST0",'<b><font color=red  class=T2>Votre compte est désactivé !!</b><br> Pour revalider votre compte, contacter votre établissement scolaire.</font>');

define("LANGMOIS1","Janvier");
define("LANGMOIS2","Février");
define("LANGMOIS3","Mars");
define("LANGMOIS4","Avril");
define("LANGMOIS5","Mai");
define("LANGMOIS6","Juin");
define("LANGMOIS7","Juillet");
define("LANGMOIS8","Août");
define("LANGMOIS9","Septembre");
define("LANGMOIS10","Octobre");
define("LANGMOIS11","Novembre");
define("LANGMOIS12","Décembre");

define("LANGDEPART1","de l'".INTITULEELEVE);

define("LANGVALIDE","Valider");
define("LANGIMP45","Editer");

define("LANGMESS34","Message plus disponible.");
define("LANGMESS35","Rendre public ce groupe.");
define("LANGMESS36","Message supprimé");


define("LANGRESA59","Nom de la salle");
define("LANGRESA60","Information");

define("LANGMAINT0","Une intervention est prévue sur le logiciel");
define("LANGMAINT1","Le service TRIADE sera inaccessible le ");
define("LANGMAINT2","entre");
define("LANGMAINT3","et");

define("LANCALED1","Année Précédente");
define("LANCALED2","Année Suivante");


define("LANGTTITRE5","Problème d'accès");
define("LANGTTITRE6","Questions");
define("LANGTPROBL1","Actuellement, le service TRIADE  est en  service.");
define("LANGTPROBL2","J'ai une Question");
define("LANGTPROBL3","Enregistrer la question");
define("LANGTPROBL4","Quitter sans enregistrer");
define("LANGTPROBL5","Expliquez-nous votre problème");
define("LANGTPROBL6","Etablissement scolaire*: ");
define("LANGTPROBL7","Email : ");
define("LANGTPROBL8","Message : ");
define("LANGTPROBL9","(* champ obligatoire)");
define("LANGTPROBL10","Enregistrer le problème");
define("LANGTPROBL12","Nous nous chargeons de régler votre problème dans les plus brefs délais. \\n\\n  L'Equipe TRIADE ");

define("LANGELEV1","Notes scolaires de");

define("LANGFORUM1","- Liste des messages");
define("LANGFORUM2","Aucun message n'a été posté dans ce forum de discussion");
define("LANGFORUM3","Vous pouvez ");
define("LANGFORUM3bis"," poster ");
define("LANGFORUM3ter"," un premier message si vous le souhaitez ");
define("LANGFORUM4","Poster un nouveau message");
define("LANGFORUM5","Forum - Poster un message");
define("LANGFORUM6","Charte à respecter");
define("LANGFORUM7","Erreur : le message référant n'existe pas.");
define("LANGFORUM8","Retour à la liste des messages postés");
define("LANGFORUM9","--- Message d'origine ---");
define("LANGFORUM10","Votre nom ");
define("LANGFORUM11","Votre email ");
define("LANGFORUM12","Sujet ");
define("LANGFORUM13","Envoyer"); // --> bouton envoyer
define("LANGFORUM14","Retour à la liste des messages postés");
define("LANGFORUM15","Forum - envoi d'un message");
define("LANGFORUM16","<b>Erreur</b> : cette page ne peut être appelée<br> que si un message a été préalablement ");
define("LANGFORUM16bis"," posté ");
define("LANGFORUM17","<b>Erreur</b> : votre message ne comporte aucun texte.<br>");
define("LANGFORUM18","<b>Erreur</b> : vous avez oublié d'indiquer votre nom.<br>");
define("LANGFORUM19","Erreur ! Votre message n'a pas pu être posté. ");
define("LANGFORUM20","<b>Erreur</b> : impossible de mettre à jour le fichier index. <br>");
define("LANGFORUM21","Votre message n'a pas pu être posté.");
define("LANGFORUM22","Votre message a été posté correctement.<br>Merci de votre contribution.");
define("LANGFORUM23","Retour à la liste des messages postés");
define("LANGFORUM24","Forum - lecture d'un message");
define("LANGFORUM25","Aucun message n'a été posté dans ce forum de discussion.");
define("LANGFORUM26","Vous pouvez ");
define("LANGFORUM26bis","poster");
define("LANGFORUM26ter","un premier message si vous le souhaitez.");
define("LANGFORUM27","Ce message n'existe pas ou a été supprimé par l'administrateur du forum de discussion.<br>");
define("LANGFORUM28","Retour à la liste des messages postés");
define("LANGFORUM30","Auteur");
define("LANGFORUM31","Date");
define("LANGFORUM32","Poster une réponse");
define("LANGFORUM33","Message précédent (dans le fil de discussion)");
define("LANGFORUM34","Messages suivants (dans le fil de discussion)");

define("LANGPROFH","Devoir Scolaire  à  faire en ");
define("LANGPROFI","Enregistrer le devoir à faire ");
define("LANGPROFJ","Devoir à faire ");
define("LANGPROFK","saisie&nbsp;le&nbsp;");
define("LANGPROFL","Confirmer la date");
define("LANGPROFM","Pour le ");
define("LANGPROFN","Devoir du ");
define("LANGPROFO","Devoir Scolaire ");
define("LANGPROFP","Mise en place des professeurs principaux");
define("LANGPROFQ","Pour demain");
define("LANGPROFR","Pour hier");
define("LANGPROFS","Matière ou sujet");
define("LANGPROFT","Valider la demande de D.S.T");
define("LANGPROFU","Demande Envoyée -- L'équipe TRIADE");


define("LANGPROJ17","Nombre d'absences");
define("LANGPROJ18","jours");

define("LANGCALEN10","Calendrier des devoirs sur table");

define("LANGPARENT6","Liste des Retards");
define("LANGPARENT7","Liste des Absences");
define("LANGPARENT8","Absent le ");
define("LANGPARENT9","Liste des dispenses");
define("LANGPARENT10","Période&nbsp;du&nbsp;");
define("LANGPARENT11","A"); // indique une date (heure)
define("LANGPARENT12","Le"); // indique une date jour
define("LANGPARENT13","Certificat");
define("LANGPARENT14","Sanction disciplinaire");
define("LANGPARENT15","Sanction");
define("LANGPARENT16","En&nbsp;retenue");
define("LANGPARENT17","à");  // indique une heure
define("LANGPARENT18","Retenue effectuée");
define("LANGPARENT19","Liste des circulaires administratives");
define("LANGPARENT20","Accès Fichier");
define("LANGPARENT21","Visible par ");
define("LANGPARENT22","Calendrier des événements ");
define("LANGPARENT23","Calendrier des devoirs sur table ");
define("LANGPARENT24","Demande de D.S.T ");


define("LANGAUDIO1","Communiqué Audio");
define("LANGAUDIO2","Le "); // indique une date
define("LANGAUDIO3","C"); // première lettre
define("LANGAUDIO3bis","ommuniqué audio au format <b>mp3</b><br>Taille maximum du fichier : ");
define("LANGAUDIO4","Enregistrez le communiqué");
define("LANGAUDIO5","Veuillez patienter 2 à 3 minutes après l'envoi du fichier audio.");
define("LANGAUDIO6","Supprimer le communiqué audio");


define("LANGOK","Ok");
define("LANGCLICK","Cliquez-ici");
define("LANGPRECE","Précédent");
define("LANGERROR1","Données introuvables");
define("LANGERROR2","aucune donnée");


define("LANGPROF1","Indiquer la matière");
define("LANGPROF2","Nombre de notes");
define("LANGPROF3","Visualisation des notes");
define("LANGPROF4","groupe");
define("LANGPROF5","Choix du Trimestre");
define("LANGPROF6","Sujet "); // sujet du devoir
define("LANGPROF7","Intitulé du sujet "); // sujet du devoir
define("LANGPROF8","Note"); //note d'un devoir
define("LANGPROF9","Devoir Scolaire  à  faire à la maison");
define("LANGPROF10","Modification d'une note");
define("LANGPROF11","Suppression d'un devoir"); // devoir --> interrogation
define("LANGPROF12","Professeur Principal");
define("LANGPROF13","Fiche ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
define("LANGPROF14","Ajout de Note en ");
define("LANGPROF15","Modifier une note en");
define("LANGPROF16","Nom du devoir");
define("LANGPROF17","Date&nbsp;du&nbsp;devoir"); // &nbsp; --> égal un blanc
define("LANGPROF18","Patientez");
define("LANGPROF19","Confirmer la modification des notes");
define("LANGPROF20","Valider la modification  des notes");
define("LANGPROF21","Modification de Notes en");
define("LANGPROF22","Visualisation des notes en");
define("LANGPROF23","Suppression d'un devoir en");
define("LANGPROF24","Devoir de "); // interrogation du
define("LANGPROF25","est supprimé");
define("LANGPROF26","Informations sur l'".INTITULEELEVE);
define("LANGPROF27","Renseignements administratifs");
define("LANGPROF28","Informations sur la vie scolaire");
define("LANGPROF29","Informations médicales");
define("LANGPROF30","Information du");
define("LANGPROF31","De"); // indiquant une personne


define("LANGEL1","Nom");
define("LANGEL2","Prénom");
define("LANGEL3","Classe ");
define("LANGEL4","Lv1");
define("LANGEL5","Lv2");
define("LANGEL6","Option");
define("LANGEL7","Régime");
define("LANGEL8","Date de naissance");
define("LANGEL9","Nationalité");
define("LANGEL10","Mot de passe");
define("LANGEL11","Nom de Famille");
define("LANGEL12","Prénom");
define("LANGEL13","rue");
define("LANGEL14","Adresse 1");
define("LANGEL15","Code postal");
define("LANGEL16","Commune");
define("LANGEL17","rue");
define("LANGEL18","Adresse 2");
define("LANGEL19","Code Postal");
define("LANGEL20","Commune");
define("LANGEL21","Téléphone");
define("LANGEL22","Profession du père");
define("LANGEL23","Téléphone du père");
define("LANGEL24","Profession de la mère");
define("LANGEL25","Téléphone de la mère");
define("LANGEL26","Etablissement");
define("LANGEL27","Code établissement");
define("LANGEL28","Code postal");
define("LANGEL29","Commune");
define("LANGEL30","Numéro Etudiant");
// define("LANGEL30","Numéro National");


define("LANGPROF32","Informations scolaires");
define("LANGPROF33","Devoir à la maison");
define("LANGPROF34","Consultation en semaine");
define("LANGPROF35","Semaine dernière");
define("LANGPROF36","Semaine prochaine");
define("LANGTP23"," INFORMATION - Demande de réservation !");
define("LANGRESA61","Nom de l'équipement");


define("LANGIMP46","Prénom");
define("LANGIMP47","Intitulé (M. ou Mme ou Mlle) ");
define("LANGIMP48","Nom");
define("LANGIMP49","* champ obligatoire");
define("LANGIMP50","Le fichier à transmettre <FONT color=RED><B>DOIT</B></FONT> contenir <FONT COLOR=red><B>9</B></FONT> champs <I>(non vides)</I> séparés par un même séparateur le \"<FONT color=red><B>;</B></font>\" <I>Soit la présence de 8 fois le caractère \"<FONT color=red><B>;</B></font>\"</I>");
define("LANGIMP51","mot de passe parent");
define("LANGIMP52","mot de passe ".INTITULEELEVE);



define("LANGacce_dep1","Erreur de connexion");
define("LANGacce_dep2","Vérifier vos identifiants de connexion, si le problème persiste, <br />  avertissez votre administrateur TRIADE via le lien <br /> 'Problème d'accès' dans le menu de gauche");

define("LANGacce_ref1","Erreur Type :Accès non autorisé");
define("LANGacce_ref11","Visité le ");
define("LANGacce_ref12","par ");
define("LANGacce_ref13","avec  ");
define("LANGacce_ref2","ACCES NON AUTORISE");
define("LANGacce_ref3","Pour accéder à votre compte, vous devez vous connecter.");
define("LANGacce1","L'".INTITULEELEVE." ");
define("LANGacce12","a une punition à rendre, <br> suite à la catégorie : ");
define("LANGacce13","pour le motif ");
define("LANGacce14","Le devoir à faire est le suivant : ");
define("LANGacce2","Supprimer ce message : ");
define("LANGacce21","Supprimer");
define("LANGacce3","L'".INTITULEELEVE." ");
define("LANacce31","ne s'est pas présenté</b></font> à la vie scolaire (CPE), <b>pour la retenue</b>,  suite à la catégorie :");
define("LANacce32","pour le motif : ");
define("LANGacce4","Le devoir à faire est le suivant :");
define("LANGacce5","Supprimer");
define("LANGacce6","Gestion disciplinaire");
define("LANGaccrob11","Téléchargement du Logiciel Adobe Acrobat Reader 8.1.0 fr");
define("LANGaccrob2","23,4 Mo  pour Windows 2000/XP/2003/Vista");
define("LANGaccrob3","Temps du téléchargement :");
define("LANGaccrob4","en 56 K : 57 min et 3 s");
define("LANGaccrob5","en 512 K : 6 min et 14 s");
define("LANGaccrob6","en 5 M : 37 secondes");
define("LANGaccrob7","Téléchargement du Logiciel Adobe Acrobat Reader 6.O.1 fr");
define("LANGaccrob8","Taille : ");
define("LANGaccrob9","0.40916 Mo pour NT/95/98/2000/ME/XP");
define("LANGaccrob10","en 56 K : 0 min et 58.2 s");
define("LANGaccrob11bis","en 512 K : 0 min et 6.6 s ");
define("LANGaffec_cre21","Création d'affectation pour la  ".INTITULECLASSE);
define("LANGaffec_cre22","Mise en place d'affectation en cours ");
define("LANGaffec_cre23","Le lancement du logiciel d'affectation va se faire automatiquement<br>Si la nouvelle page n'apparait pas, cliquez ");
define("LANGaffec_cre24","TRIADE - Compte de ");
define("LANGaffec_cre31","CREATION - AFFECTATION");
define("LANGaffec_cre41","Imprimer");
define("LANGaffec_mod_key1","Affectation des ".INTITULECLASSE."s");
define("LANGaffec_mod_key2","Module de modification d'affectation des ".INTITULECLASSE."s.");
define("LANGaffec_mod_key3","ATTENTION ce module est à utiliser lors de modification  d'affectation,<br> il détruit toutes les notes des ".INTITULEELEVE."s  des ".INTITULECLASSE."s modifiées. ");
define("LANGaffec_mod_key4","ATTENTION, la destruction des notes des ".INTITULECLASSE."s sélectionnées  seront supprimées. \\n Souhaitez-vous continuer ? \\n\\n L\'équipe TRIADE");
define("LANGattente1","Attente - TRIADE");
define("LANGattente2","Veuillez patienter, S.V.P.");
define("LANGattente3","L'Equipe TRIADE.");
define("LANGatte_mess1","TRIADE - Attente - Messagerie");
define("LANGatte_mess2","Veuillez patienter, S.V.P.");
define("LANGatte_mess3","service TRIADE");
define("LANGbasededon20","Envoyer le fichier");
define("LANGbasededon201","rien");
define("LANGbasededon2011","Importation de fichier GEP");
define("LANGbasededon202","Fichier Transmis -- L'équipe TRIADE");
define("LANGbasededon203","Fichier non enregistré");
define("LANGbasededon31","Indiquer pour chaque référence la ".INTITULECLASSE." correspondante");
define("LANGbasededon32","Choix ...");
define("LANGbasededon33","aucun");
define("LANGbasededon34","L'envoi du fichier peut durer de <b>2 à 4 minutes</b> en fonction du nombre d'".INTITULEELEVE."s.");
define("LANGbasededon35","Le fichier doit être au format <b>dbf</b> et doit être <b>F_ele.dbf</b>");
define("LANGbasededon41","Erreur sur le nombre de ".INTITULECLASSE."s !!! - Contacter l'équipe TRIADE <br /><br /> support@triade-educ.org</center>");
define("LANGbasededon42","Erreur sur la saisie des ".INTITULECLASSE."s, une ".INTITULECLASSE." est répétée plusieurs fois -- L'équipe TRIADE");
define("LANGbasededon43","Message du : ");
define("LANGbasededon44","De");
define("LANGbasededon45","Membre :");
define("LANGbasededon46","Message :");
define("LANGbasededon47","NOUVELLE BASE:");
define("LANGbasededon48","- avec GEP");
define("LANGbasededon49"," Etablissement :");
define("LANGbasededoni11","'Attention','./image/commun/warning.jpg','<font face=Verdana size=1><font color=red>L</font>e module <b>dbase</b> n\'est pas <br> chargé !! <i>Nécessaire pour importer <br> une base GEP.");
define("LANGbasededoni21","ATTENTION, la destruction de l\'ancienne base sera automatique. \\n Souhaitez-vous continuer ? \\n\\n L\'Equipe TRIADE");
define("LANGbasededoni31","Indiquer pour quelle catégorie le fichier est attribué ");
define("LANGbasededoni32","L'import du fichier concerne : ");
define("LANGbasededoni33","Import des ".INTITULEELEVE."s : ");
define("LANGbasededoni34","Import des ".INTITULEENSEIGNANT."s :");
define("LANGbasededoni35","Import du personnel vie scolaire : ");
define("LANGbasededoni36","Import du personnel administratif : ");
define("LANGbasededoni41","Classe antérieure");
define("LANGbasededoni42","Année antérieure");
define("LANGbasededoni51","Pour l'intitulé");

define("LANGbasededoni61","erreur");
define("LANGbasededoni71","Importation du fichier ASCII");
define("LANGbasededoni72","Message du : ");
define("LANGbasededoni721","De");
define("LANGbasededoni722","Membre :");
define("LANGbasededoni723","Message :");
define("LANGbasededoni724","NOUVELLE BASE:");
define("LANGbasededoni725","- avec ASCII");
define("LANGbasededoni726"," Etablissement :");
define("LANGbasededoni73","Total d'enregistrements dans la base ");
define("LANGbasededoni91","Importation du fichier ASCII");
define("LANGbasededoni92","Erreur sur le nombre de ".INTITULECLASSE."s !!! - Contacter le l'équipe TRIADE <br />");
define("LANGbasededoni93","Erreur sur la saisie des ".INTITULECLASSE."s, une ".INTITULECLASSE." est répétée plusieurs fois -- L'équipe TRIADE");
define("LANGbasededoni94","Donnée de la base traitée -- L'équipe TRIADE<br />");
define("LANGbasededoni95","Total d'".INTITULEELEVE."  enregistré dans la base : ");
define("LANGPIEDPAGE","<p> La <b>T</b>ransparence et la <b>R</b>apidité de l'<b>I</b>nformatique <b>A</b>u service <b>D</b>e l'<b>E</b>nseignement<br>Pour visualiser ce site de façon optimale :  résolution minimale : 800x600 <br>  © 2000 - ".date("Y")." TRIADE - Tous droits réservés");

define("LANGAPROPOS1","Version");
define("LANGAPROPOS2","Tous droits r&eacute;serv&eacute;s");
define("LANGAPROPOS3","Licence d'utilisation");
define("LANGAPROPOS4","Product ID");

define("LANGTELECHARGER","Télécharger");
define("LANGAJOUT1","Pour le Régime : choix possible (<b>INT</b> (Interne),<b>EXT</b> (Externe), <b>DP</b> (Demi Pension)<br><br>");
define("LANGIMP44","Le fichier n'est pas conforme.");
define("LANGBASE16"," Les colonnes sont représentées sous la forme : <b>nom de login ; prénom de login ; mot de passe Parent ; mot de passe ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." en clair ; ".INTITULECLASSE." de l'".INTITULEELEVE." </b>");


define("LANGSUPP0","Suppression d'un compte Suppléant");
define("LANGSUPP1","Module Suppression");
define("LANGSUPP2","Supprimer le compte");
define("LANGSUPP3","Voulez-vous supprimer de la liste des suppléants");
define("LANGSUPP3bis","remplaçant de");
define("LANGSUPP4","Confirmer la suppression");
define("LANGSUPP5","Impossible de supprimer ce compte. \\n\\n Compte affecté à une ".INTITULECLASSE.".  \\n\\n  L'équipe TRIADE");
define("LANGSUPP6","Compte supprimé - L'équipe TRIADE");
define("LANGSUPP7","Suppression d'un groupe");
define("LANGSUPP8","Supprimer le groupe");
define("LANGSUPP9","Suppression d'un compte ");
define("LANGSUPP10","Supprimer le compte");
define("LANGSUPP11","un membre de la vie scolaire");
define("LANGSUPP12","un administrateur");
define("LANGSUPP13","un ".INTITULEENSEIGNANT."");
define("LANGSUPP14","Suppression d'un ".INTITULEELEVE." dans la  ".INTITULECLASSE);
define("LANGSUPP15","Cliquer sur l'".INTITULEELEVE." à supprimer");
define("LANGSUPP16","Suppression d'un ".INTITULEELEVE."");
define("LANGSUPP17","va être supprimé de la base");
define("LANGSUPP18","Toutes les informations sur cet ".INTITULEELEVE." vont être supprimées, à savoir : <br> (notes, absences, retards, dispences, sanctions, informations, messageries, ...)");
define("LANGSUPP19","Annuler la suppression");
define("LANGSUPP20","est supprimé de la base");
define("LANGSUPP21","Supprimer une ".INTITULECLASSE);
define("LANGSUPP22","Suppression d'une ".INTITULECLASSE);
define("LANGSUPP23","Suppression d'une matière ou sous-matière");
define("LANGSUPP24","Supprimer la matière");
define("LANGSUPP25","Classe supprimée --  Service TRIADE");
define("LANGSUPP26","Matière supprimée --  Service TRIADE");
define("LANGSUPP27","Création de la matière");
define("LANGSUPP28","Sous-matière enregistrée");

define("LANGADMIN","Administration");
define("LANGPROF",ucfirst(INTITULEENSEIGNANT));
define("LANGSCOLAIRE","de la Vie Scolaire");
define("LANGCLASSE","une ".INTITULECLASSE);


define("LANGGRP11","Nom du Groupe");
define("LANGGRP12","Classe(s) concernée(s)");
define("LANGGRP13","Liste ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."s");
define("LANGGRP14","Liste des groupes");
define("LANGGRP15","Création d'un groupe");
define("LANGGRP16","Indiquez les ".INTITULEELEVE."s dans le groupe");
define("LANGGRP17","Sélectionner");
define("LANGGRP18","Enregistrer le groupe");
define("LANGGRP19","Création du groupe effectuée");
define("LANGGRP20","Autre groupe");
define("LANGGRP21","Liste des groupes");
define("LANGGRP22","Indiquer une ".INTITULECLASSE." pour la création du groupe S.V.P. \\n\\n L'équipe TRIADE");
define("LANGGRP23","Liste des ".INTITULEELEVE."s du groupe");
define("LANGGRP24","Liste des ".INTITULECLASSE."s");
define("LANGGRP25","Liste des matières");



//----------------//
define("LANGDONNEENR","<font class=T2>Donnée(s) Enregistrée(s).</font>");

define("LANGABS47","Ajout d'une sanction disciplinaire");
define("LANGABS48"," a atteint ");
define("LANGABS48bis","fois la catégorie");
define("LANGABS49","durée");
define("LANGABS50"," Retenue  du ");
define("LANGABS51","Tél. Prof Père ");
define("LANGABS52","Tél. Prof Mère ");
define("LANGABS53","Aucun retard ou absence signalé");

define("LANGCALRET1","Calendrier &nbsp; des &nbsp; Retenues");

define("LANGHISTO1","Historique des opérations");

define("LANGDST9","Ajouter une entrée");
define("LANGDST10","Supprimer une entrée");
define("LANGDST11","en ".INTITULECLASSE." de");

define("LANGDISP11","Affichage <b>complet</B> des dispenses");

define("LANGEN","En");

define("LANGAFF4","Edition d'une ".INTITULECLASSE);
define("LANGAFF5","Toutes les ".INTITULECLASSE."s");
define("LANGAFF6","Consulter cette ".INTITULECLASSE);

define("LANGCHER1","Recherche Complexe");
define("LANGCHER2","Indiquer le format de fichier à générer");
define("LANGCHER3","Indiquer le séparateur de champs");
define("LANGCHER4","Effectuer la recherche d'un ".INTITULEELEVE." à partir du nom : <b>cliquez ici</b>");
define("LANGCHER5","Ajouter");
define("LANGCHER6","Enlever");
define("LANGCHER7","Monter");
define("LANGCHER8","Descendre");
define("LANGCHER9","Suivant");
define("LANGCHER10","Elément recherché");
define("LANGCHER11","Nombre de critères de recherche");
define("LANGCHER12","A partir de");

define("LANGCHER13","avec la valeur");
define("LANGCHER14","Recherche approximative");
define("LANGCHER15","Recherche précise");
define("LANGCHER16","Lancer la recherche");
define("LANGCHER17","Attention: reste un élément non choisi !! -- L'équipe TRIADE ");

define("LANGCHER18","avec comme valeur");

define("LANGTITRE34","Configuration du courrier retard");
define("LANGTITRE35","Configuration du courrier absence");

define("LANGCONFIG1","Configuration enregistrée.");
define("LANGCONFIG2","Voici votre texte ");

define("LANGCONFIG3","Indiquer la liste des parents d'".INTITULEELEVE."s qui recevront un courrier");

define("LANGERROR01","Erreur d'accès à la base");
define("LANGERROR02","ATTENTION Impossible <br><br>Le problème peut venir des informations saisies <br>(Vérifiez les différents champs avant de valider).<BR>  <BR>Ou l'information est déjà enregistrée OU non accessible.");
define("LANGERROR03","Accès impossible à la base pour cette action . <BR>");

define("LANGABS54","est déjà noté absent.");
define("LANGABS55","est déjà noté en retard.");


define("LANGPARAM4","Le certificat est bien enregistré.");
define("LANGPARAM5","Le certificat de scolarité des ".INTITULEELEVE."s de la ".INTITULECLASSE);
define("LANGPARAM5bis","est disponible, au format PDF");
define("LANGPARAM6","Paramétrage pour le contenu des bulletins et périodes");

define("LANGPARAM7","Nom  du directeur");
define("LANGPARAM8","Nom  de l'établissement");
define("LANGPARAM9","adresse");
define("LANGPARAM10","Code Postal");
define("LANGPARAM11","Ville");
define("LANGPARAM12","Téléphone");
define("LANGPARAM13","E-mail");
define("LANGPARAM14","Logo établissement");
define("LANGPARAM15","Enregistrer les paramètres");
define("LANGPARAM16","Enregistrement effectué. -- L'Equipe TRIADE");

define("LANGCERTIF1","Le certificat de scolarité de ");
define("LANGCERTIF1bis","est disponible, au format PDF");


define("LANGRECHE1","Informations sur l'".INTITULEELEVE."");

define("LANGBT52","Modifier les données");

define("LANGEDIT1","Données introuvables");

define("LANGMODIF1","Mise à jour d'un compte ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
define("LANGMODIF2","Renseignements sur l'".INTITULEELEVE);
define("LANGMODIF3","Renseignements sur la famille");

define("LANGALERT1","Données mises à jour -- Equipe TRIADE");
define("LANGALERT2","Attention format du fichier non conforme ou taille non respectée");
define("LANGALERT3","Attention format du fichier non conforme ou taille non respectée");

define("LANGLOGO1","Logo à transmettre");
define("LANGLOGO2","Enregistrer le logo");
define("LANGLOGO3","Le logo <b>doit être au format jpg</b> et de taille 96px sur 96px.");

define("LANGPARAM17","Définition des périodes trimestrielles ou semestrielles");
define("LANGPARAM18","Trimestre ou Semestre");
define("LANGPARAM19","Date de début");
define("LANGPARAM20","Date de fin");
define("LANGPARAM21","Premier");
define("LANGPARAM22","Deuxième");
define("LANGPARAM23","Troisième");
define("LANGPARAM24","Enregistrer les dates trimestrielles");
define("LANGPARAM25","Donnée prise en compte, si l'enregistrement est au format Trimestriel");
define("LANGPARAM26","Date non valide -- Equipe TRIADE");
define("LANGPARAM27","Informations Enregistrées -- Equipe TRIADE");
define("LANGPARAM28","trimestre");
define("LANGPARAM29","semestre");
define("LANGPARAM30","Bulletin");


define("LANGBULL5","Impression de bulletin");
define("LANGBULL6","Continuer le traitement");
define("LANGBULL7","Impression de période");
define("LANGBULL8","Indiquez le début de la période");
define("LANGBULL9","Indiquez la fin de la période");
define("LANGBULL10","Indiquez la période");
define("LANGBULL11","Indiquez la section");
define("LANGBULL12","Imprimer la période");
define("LANGBULL13","Historique");
define("LANGBULL14","<FONT COLOR='red'>ATTENTION</FONT></B> Besoin de l'outil <B>Adobe Acrobat Reader</B>.  Logiciel et téléchargement Gratuits ");
define("LANGBULL14bis","Téléchargement");
define("LANGBULL15","Visualiser / Supprimer");
define("LANGBULL16","Nom de l'".INTITULEELEVE);
define("LANGBULL17","Professeur");
define("LANGBULL18","Détail des notes");
define("LANGBULL19","Appréciation du Professeur Principal");
define("LANGBULL20","RELEVE DE NOTES");
define("LANGBULL21","période");

define("LANGBULL22","premier trimestre");
define("LANGBULL23","deuxième trimestre");
define("LANGBULL24","troisième trimestre");

define("LANGBULL25","premier semestre");
define("LANGBULL26","deuxième semestre");

define("LANGBULL27","Bulletin du ");
define("LANGBULL28","Section");
define("LANGBULL29","Année Scolaire");

define("LANGBULL30","BULLETIN");

define("LANGBULL31","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
define("LANGBULL32","Matières");
define("LANGBULL33","Classe");
define("LANGBULL34","Appréciations, progrès, conseils pour progresser");

define("LANGBULL35","Coef");
define("LANGBULL36","Moy");
define("LANGBULL37","Mini");
define("LANGBULL38","Maxi");
define("LANGBULL39","Assiduité et comportement au sein de l'établissement : ");
define("LANGBULL40","Appréciation globale de l'équipe pédagogique : ");
define("LANGBULL41","Bulletin à conserver précieusement");
define("LANGBULL42","Visa du chef d'établissement ou de son délégué");
define("LANGBULL43","ANNEE SCOLAIRE");
define("LANGBULL44","M. & Mme");
define("LANGOU","ou"); // le ou de ou bien


define("LANGPROJ19","Semestre 1");
define("LANGPROJ20","Semestre 2");

define("LANGDISC1","Retenue  du ");
define("LANGDISC2","Imprimer les retenues du jour");


define("LANGDISC3","Tél. Dom. ");
define("LANGDISC4","Tél. Prof. Pére ");
define("LANGDISC5","Tél. Prof. Mère ");
define("LANGDISC6","Mise en place d'une sanction en  Classe de ");
define("LANGDISC7","Intitulé de la catégorie ");
define("LANGDISC8","Intitulé de la sanction ");
define("LANGDISC9","Attribué par ");
define("LANGDISC10","Motif, informations, devoir à faire ");
define("LANGDISC11","Retenue");
define("LANGDISC11bis","Le");  // Le pour indiquer une date
define("LANGDISC11Ter","A");  // A pour indiquer une heure
define("LANGDISC12","durée");
define("LANGDISC13","<font color=red>C</font></B>ochez la case si l\'".INTITULEELEVE." est soit en retenue soit sanctionné.");
define("LANGDISC14","Ajout d'une sanction disciplinaire");
define("LANGDISC15","<B>*<I> D</B>: Téléphone Domicile, <B>P</B>: Téléphone Profession Père, <B>M</B>: Téléphone Profession Mère</I>");
define("LANGDISC16","Effectuer");
define("LANGDISC17","Tél.");
define("LANGDISC18","Affichage  des Sanctions");
define("LANGDISC19","Affichage des <b>5</B> dernières sanctions");
define("LANGDISC20","Catégorie");
define("LANGDISC21","Liste complète de ");
define("LANGDISC22","Visualiser les retenues de ");
define("LANGDISC23","Affichage des retenues");
define("LANGDISC24","Affichage  <b>complet</B> des retenues");
define("LANGDISC25","En&nbsp;retenue");
define("LANGDISC26","Retenue non effectuée");
define("LANGDISC27","Lister les sanctions de ");
define("LANGDISC28","Affichage   des Sanctions");
define("LANGDISC29","Affichage  <b>complet</B> des sanctions");
define("LANGDISC30","Saisie&nbsp;le");
define("LANGDISC31","Lister les sanctions de ");
define("LANGDISC32","Retenue non affectée à un éléve ");
define("LANGDISC33","ATTENTION l'".INTITULEELEVE." ");
define("LANGDISC33bis"," est déjà en retenue pour la date et l'heure indiquée. ");
define("LANGDISC34","a atteint");
define("LANGDISC34bis","fois la catégorie");
define("LANGDISC35","Suppression Sanction");
define("LANGDISC36","Suppression Retenue");

define("LANGattente222","Patientez");



define("LANGSUPP","Supp"); // abréviation de Supprimer



define("LANGCIRCU1","Gestion des Circulaires administratives");
define("LANGCIRCU2","Ajouter une circulaire");
define("LANGCIRCU3","Lister des circulaires");
define("LANGCIRCU4","Supprimer une circulaire");
define("LANGCIRCU5","Ajout de circulaires administratives");
define("LANGCIRCU6","Sujet");
define("LANGCIRCU7","Référence");
define("LANGCIRCU8","Circulaire");
define("LANGCIRCU9","Corps ".ucfirst(INTITULEENSEIGNANT));
define("LANGCIRCU10","Dans la ou les ".INTITULECLASSE."(s)");
define("LANGCIRCU11","<font face=Verdana size=1><B><font color=red>C</font></B>irculaire au format : <b>doc</b>, <b>pdf</b>, <b>txt</b>, <b>Office</b>.</FONT>");
define("LANGCIRCU12","<font face=Verdana size=1><B><font color=red>C</font></B>irculaire visible par les ".INTITULEENSEIGNANT."s.</FONT>");
define("LANGCIRCU13","Toutes les ".INTITULECLASSE."s");
define("LANGCIRCU14","Retour au Menu");
define("LANGCIRCU15","Enregistrer la circulaire");
define("LANGCIRCU16","Circulaire non enregistrée");
define("LANGCIRCU17","Le fichier doit être au format <b>txt ou doc ou pdf</b> et inférieur à 2Mo ");
define("LANGCIRCU18","<font class=T2>Circulaire enregistrée</font>");
define("LANGCIRCU19","Supprimer des Circulaires administratives");
define("LANGCIRCU20","Accès Fichier");
define("LANGCIRCU21","<font color=red>R</b></font><font color=#000000>éférence");

define("LANGCODEBAR1","Gestion des codes barres");
define("LANGCODEBAR2","Ce module ne fonctionne pas avec votre serveur. <br> Vous devez avoir PHP 5 ou supp pour utiliser ce module.");
define("LANGCODEBAR3","Voici la liste des codes barres accessible par TRIADE");
define("LANGCODEBAR4","Le code barre utilisé par défaut est le ");
define("LANGCODEBAR5","Liste");


define("LANGPUB1","Ajout d'une bannière de publicité");
define("LANGPUB2","Vous désirez publier sur le site de TRIADE");
define("LANGPUB3","Effectuer une campagne publicitaire");
define("LANGPUB4","Pour cela  ");
define("LANGPUB5","Vous êtes déjà annonceur sur TRIADE ");

define("LANGPROFB1","Appréciation pour les bulletins trimestriels");
define("LANGPROFB2","Paramétrage de vos commentaires automatisés");
define("LANGPROFB3","Paramétrage");
define("LANGPROFB4","Configuration Commentaires Bulletins");
define("LANGPROFB5","Enregistrement des commentaires");
define("LANGPROFB6","Commentaire");
define("LANGPROFB7","Liste");


define("LANGPROFC1","Calendrier du planning d'équipement");
define("LANGPROFC2","Calendrier du planning des salles");


define("LANGPARAM31","Visualisation en mode U.S.A.");
define("LANGPARAM32","Assiduité et comportement au sein de l'établissement : ");
define("LANGPARAM33","Récupérer le fichier PDF");

define("LANGDISC37","Ajout d'une sanction disciplinaire");

define("LANGPROFP4","<b>Professeur Principal</b> en ");
define("LANGPROFP5","Informations sur l'".INTITULEELEVE);
define("LANGPROFP6","Informations du ");
define("LANGPROFP7","jusqu'au ");

define("LANGPROFP8","Nombre total de retards");
define("LANGPROFP9","Nombre de retards ce trimestre");
define("LANGPROFP10","Nombre total d'absences");
define("LANGPROFP11","Nombre d'absences ce trimestre");

define("LANGPROFP12","Gestion des délégués");
define("LANGPROFP13"," en ".INTITULECLASSE." de ");
define("LANGPROFP14","Parent délégué");
define("LANGPROFP15","Coordonnées");
define("LANGPROFP16","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." délégué");
define("LANGPROFP17","Parent(s) délégué(s)");
define("LANGPROFP18","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."(s) délégué(s)");
define("LANGPROFP19","Tél."); // pour téléphone
define("LANGPROFP20","Mail");
define("LANGPROFP21","Complément d'informations médicales sur l'".INTITULEELEVE);

define("LANGETUDE1","Gestion des études");
define("LANGETUDE2","Affectation des ".INTITULEELEVE."s à l'étude");
define("LANGETUDE3","Consulter la liste des études affectées");
define("LANGETUDE4","Ajouter une étude");
define("LANGETUDE5","Modifier une étude");
define("LANGETUDE6","Supprimer une étude");
define("LANGETUDE7","Consultation d'une étude");
define("LANGETUDE8","Affecter un ".INTITULEELEVE." à une étude");
define("LANGETUDE9","Modifier un ".INTITULEELEVE." à une étude");
define("LANGETUDE10","Supprimer un ".INTITULEELEVE." d'une étude");
define("LANGETUDE11","Liste des études");

define("LANGETUDE12","Surveillant");
define("LANGETUDE13","Etude");
define("LANGETUDE14","En salle");
define("LANGETUDE15","Semaine");
define("LANGETUDE16","Le");  		// Le indique une date
define("LANGETUDE17","à");  		// à indique une heure
define("LANGETUDE18","pendant");  	//indique une durée
define("LANGETUDE19","Création d'une étude");
define("LANGETUDE20","Nom de l'étude");
define("LANGETUDE21","Jour de la semaine");
define("LANGETUDE22","L'heure d'étude");
define("LANGETUDE23","Durée de l'étude");
define("LANGETUDE24","hh:mm");
define("LANGETUDE25","Salle d'étude");
define("LANGETUDE26","Surveillant de cette étude");
define("LANGETUDE27","L'étude est enregistrée");
define("LANGETUDE28","Liste des études");
define("LANGETUDE29","Modification d'une étude");
define("LANGETUDE30","L'étude posséde des ".INTITULEELEVE."s. Supprimer la liste des ".INTITULEELEVE."s de l'étude avant de supprimer l'étude");
define("LANGETUDE31","Liste ".INTITULEELEVE);
define("LANGETUDE32","Liste des ".INTITULEELEVE."s");
define("LANGETUDE33","Affectation d'un ".INTITULEELEVE." à une étude");
define("LANGETUDE34","Choix de l'étude");
define("LANGETUDE35","Indiquer les ".INTITULECLASSE."s pour l'affectation des ".INTITULEELEVE."s à cette étude");
define("LANGETUDE36","Intitulé de l'étude");
define("LANGETUDE37","Indiquez les ".INTITULEELEVE."s dans cette étude");
define("LANGETUDE38","autorisé à sortir");
define("LANGETUDE39","Enregistrer l'étude");
define("LANGETUDE40","Autre étude");
define("LANGETUDE41","Modifier l'étude d'un ".INTITULEELEVE);
define("LANGETUDE42","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." en étude");
define("LANGETUDE43","Enregister les modifications");
define("LANGETUDE44","Sortie autorisée");
define("LANGETUDE45","Supprimer l'étude d'un ".INTITULEELEVE);

define("LANGLIST1","Edition d'une ".INTITULECLASSE);
define("LANGLIST2","Liste des ".INTITULEENSEIGNANT."s de la ".INTITULECLASSE);
define("LANGLIST3","Professeur Principal");
define("LANGLIST4","Date");
define("LANGLIST5","Liste complète au format PDF");
define("LANGLIST6","Professeur Principal");


define("LANGPASS1","Nouveau mot de passe");

define("LANGTRONBI1","Visualisation Trombinoscope des ".INTITULEELEVES);
define("LANGTRONBI2","Modifier Trombinoscope des ".INTITULEELEVES);
define("LANGTRONBI3","Attention format du fichier non conforme");
define("LANGTRONBI4","Impossible photo de taille non conforme");
define("LANGTRONBI5","Nom ".INTITULEELEVE);
define("LANGTRONBI6","Prénom ".INTITULEELEVE);
define("LANGTRONBI7","la photo");
define("LANGTRONBI8","ajouter photo");


define("LANGBASE19","Le fichier sélectionné n'est pas valide");
define("LANGBASE20","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." sans ".INTITULECLASSE);
define("LANGBASE21","Nombre d'".INTITULEELEVE."s sans ".INTITULECLASSE);
define("LANGBASE22","Affichage des 30 premiers");
define("LANGBASE23","Changement de ".INTITULECLASSE." pour les ".INTITULEELEVE."s");
define("LANGBASE24","Changement Terminé");
define("LANGBASE25","AVANT TOUTES MODIFICATIONS CONSULTER NOTRE AIDE");
define("LANGBASE26","Changement de ".INTITULECLASSE." pour les ".INTITULEELEVE."s de la ".INTITULECLASSE);
define("LANGBASE27","Information sur le changement de ".INTITULECLASSE." d'un ".INTITULEELEVE);
define("LANGBASE28","<b>Pas de changement.</b> <i>(Avec l'option 'choix ...')</i>");
define("LANGBASE29","Aucune suppression d'information de l'".INTITULEELEVE." n'est réalisée.");
define("LANGBASE30","<b>Le changement de ".INTITULECLASSE.".</b> <i>(Avec indication d'une ".INTITULECLASSE.")</i>");
define("LANGBASE31","Suppression notes, abs, retards, disciplines, dispenses  de l'".INTITULEELEVE.".");
define("LANGBASE32","<b>Quitte l'école.</b>  <i>(Avec l'option 'Quitte l'école')</i>");
define("LANGBASE33","Suppression de l'".INTITULEELEVE." dans la base.");
define("LANGBASE34","Suppression notes, abs, retards, disciplines, dispenses de l'".INTITULEELEVE.".");
define("LANGBASE35","Suppression messages internes de la famille.");
define("LANGBASE36","Va en ".INTITULECLASSE." de");
define("LANGBASE37","Quitte l'école");
define("LANGBASE38","Valider le(s) changement(s)");
define("LANGBASE39","Choisissez un élément");


define("LANGBASE40","Choix du ");


// MODULE AGENDA 
define("LANGAGENDA1","Attention!!!\nLa note que vous venez de créer ou de modifier se superpose\navec une autre note pour les utilisateurs suivants");
define("LANGAGENDA2","Voulez-vous supprimer cette note qui vous a été affectée ?");
define("LANGAGENDA3","Suppression d'une note, rappel :\\n\\n - Toutes les occurences découlant de cette note seront également effacées\\n - Pour supprimer juste une occurence, cliquez sur l'image correspondante à droite de la note dans les planning\\n\\nVoulez vous supprimer cette note ?");
define("LANGAGENDA4","Suppression d'une occurence, rappel :\\n\\n - Seule cette occurence sera supprimée\\n - Pour supprimer une note récurrente et toutes ses occurences, cliquez sur la croix à droite de la note dans les plannings ou éditez la note et cliquez sur le bouton [Supprimer]\\n\\nVoulez-vous supprimer cette occurence ?");
define("LANGAGENDA5","Note avec rappel");
define("LANGAGENDA6","Supprimer une occurence");
define("LANGAGENDA7","Supprimer une note");
define("LANGAGENDA8","S'approprier une note");
define("LANGAGENDA9","Afficher le détail");
define("LANGAGENDA10","Note personnelle");
define("LANGAGENDA11","Note affectée");
define("LANGAGENDA12","Note Active");
define("LANGAGENDA13","Note Terminée");
define("LANGAGENDA14","Jour courant");
define("LANGAGENDA15","Jour férié");
define("LANGAGENDA16","Créer une note");
define("LANGAGENDA17","cliquer pour changer");
define("LANGAGENDA18","Enregistrer une date d'anniversaire");
define("LANGAGENDA19","Modification d'une date d'anniversaire");
define("LANGAGENDA20","Veuillez saisir le nom de la personne");
define("LANGAGENDA21","Veuillez saisir la date de naissance de la personne");
define("LANGAGENDA22","Anniversaire de");
define("LANGAGENDA23","Date de naissance");
define("LANGAGENDA24","Format jj/mm/aaaa");
define("LANGAGENDA25","Supprimer cet anniversaire ?");
define("LANGAGENDA26","Supprimer");
define("LANGAGENDA27","Annuler");
define("LANGAGENDA28","Enregistrer");
define("LANGAGENDA29","Etes-vous sûr de vouloir effacer cet anniversaire ?");
define("LANGAGENDA30","Modifier");
define("LANGAGENDA31","Année préc.");
define("LANGAGENDA32","Mois préc.");
define("LANGAGENDA33","Atteindre la date du jour");
define("LANGAGENDA34","maintenir pour menu");
define("LANGAGENDA35","Mois suiv.");
define("LANGAGENDA36","Année suiv.");
define("LANGAGENDA37","Sélectionner une date");
define("LANGAGENDA38","Déplacer");
define("LANGAGENDA39","Aujourd'hui");
define("LANGAGENDA40","A propos du calendrier");
define("LANGAGENDA41","Afficher %s en premier");
define("LANGAGENDA42","Fermer");
define("LANGAGENDA43","Cliquer ou glisser pour modifier la valeur");
define("LANGAGENDA44","Utilisateur inconnu");
define("LANGAGENDA45","Votre session a expiré !");
define("LANGAGENDA46","Ce login est déjà utilisé");
define("LANGAGENDA47","Ancien mot de passe erroné");
define("LANGAGENDA48","Veuillez vous identifier pour utiliser Phenix");
define("LANGAGENDA49","La connexion au serveur SQL a échoué");
define("LANGAGENDA50","Profil modifié");
define("LANGAGENDA51","Note enregistrée");
define("LANGAGENDA52","Note mise à jour");
define("LANGAGENDA53","Note supprimée");
define("LANGAGENDA54","Occurence de la note supprimée");
define("LANGAGENDA55","Anniversaire enregistré");
define("LANGAGENDA56","Anniversaire mis à jour");
define("LANGAGENDA57","Anniversaire supprimé");
define("LANGAGENDA58","Compte créé, vous pouvez vous connecter");
define("LANGAGENDA59","L'enregistrement a échoué");
define("LANGAGENDA60","Tous les champs");
define("LANGAGENDA61","Société");
define("LANGAGENDA62","Nom + Prénom");
define("LANGAGENDA63","Adresse");
define("LANGAGENDA64","Numéro de téléphone");
define("LANGAGENDA65","Adresse Email");
define("LANGAGENDA66","Commentaires");
define("LANGAGENDA67","Lancer la recherche");
define("LANGAGENDA68","Société");
define("LANGAGENDA69","Nom");
define("LANGAGENDA70","Prénom");
define("LANGAGENDA71","Adresse");
define("LANGAGENDA72","Ville");
define("LANGAGENDA73","Pays");
define("LANGAGENDA74","Tél. Domicile");
define("LANGAGENDA75","Tél. Travail");
define("LANGAGENDA76","T&eacute;l.&nbsp;Portable");
define("LANGAGENDA77","Fax");
define("LANGAGENDA78","Email");
define("LANGAGENDA79","Email Pro");
define("LANGAGENDA80","Note / Divers");
define("LANGAGENDA81","Groupe");
define("LANGAGENDA82","Partage");
define("LANGAGENDA83","CP");
define("LANGAGENDA84","Date de naissance");
define("LANGAGENDA85","Recommencer");
define("LANGAGENDA86","Importer");
define("LANGAGENDA87","Import terminé");
define("LANGAGENDA88","contact(s) ajouté(s)");
define("LANGAGENDA89","Pas de contact disponible !");
define("LANGAGENDA90","<LI>Dans Outlook, faire <I>Fichier</I>-&gt;<I>Exporter</I>-&gt;<I>Autre carnet d'adresses...</I></LI>");
define("LANGAGENDA91","<LI>Choisir <I>Fichier texte (valeurs séparées par des virgules)</I> puis <I>Exporter</I></LI>");
define("LANGAGENDA92","<LI>Choisir l'endroit o&ugrave; le fichier sera sauvegardé puis <I>Suivant</I></LI>");
define("LANGAGENDA93","<LI>Dans la liste des champs à exporter, sélectionner :<BR>");
define("LANGAGENDA94","<I>Prénom, Nom, Adresse de messagerie, Rue (domicile), Ville (domicile), Code Postal (domicile), Pays/région (domicile), Téléphone personnel, Téléphone mobile, Téléphone professionnel, Télécopie professionnelle, Société</I> puis cliquer sur <I>Terminer</I></LI>");
define("LANGAGENDA95","<LI>Récupérer le fichier ainsi créé dans le formulaire ci-dessous et cliquer sur <I>Importer</I></LI>");
define("LANGAGENDA96","Veuillez entrer une société pour la recherche");
define("LANGAGENDA97","Veuillez entrer un nom ou un prénom pour la recherche");
define("LANGAGENDA98","Veuillez entrer une adresse pour la recherche");
define("LANGAGENDA99","Veuillez entrer un numéro de téléphone pour la recherche");
define("LANGAGENDA100","Veuillez entrer une adresse Email pour la recherche");
define("LANGAGENDA101","Veuillez saisir une bribe de commentaire pour la recherche");
define("LANGAGENDA102","Veuillez entrer au moins un critère pour la recherche");
define("LANGAGENDA103","Etes-vous sûr de vouloir effacer ce contact ?");
define("LANGAGENDA104","Année");
define("LANGAGENDA105","Pas de père");
define("LANGAGENDA106","Liste des personnes<BR>à qui vous pouvez<BR>affecter une note");
define("LANGAGENDA107","Personne(s) possible(s)");
define("LANGAGENDA108","Personne(s) sélectionnée(s)");
define("LANGAGENDA109","Précision d'affichage");
define("LANGAGENDA110","Tranche de 30mn");
define("LANGAGENDA111","Tranche de 15mn");
define("LANGAGENDA112","Heure de début");
define("LANGAGENDA113","Heure de fin");
define("LANGAGENDA114","Occupé");
define("LANGAGENDA115","Partiel");
define("LANGAGENDA116","Libre");
define("LANGAGENDA117","Créer une note entre ");
define("LANGAGENDA118","Détail par utilisateur de cette journée");
define("LANGAGENDA119","Afficher");
define("LANGAGENDA120","Veuillez sélectionner une personne");
define("LANGAGENDA121","Veuillez sélectionner une heure de fin postérieure à l'heure de début");
define("LANGAGENDA122","Semaine du ");
define("LANGAGENDA123","au");
define("LANGAGENDA124","Semaine suivante");
define("LANGAGENDA125","Enlever");
define("LANGAGENDA126","Disponibilités de vos contacts pour le ");
define("LANGAGENDA127","Ajouter");
define("LANGAGENDA128","Hors Profil");
define("LANGAGENDA129","Veuillez sélectionner une heure de fin postérieure à l'heure de début");
define("LANGAGENDA130","Précision d'affichage");
define("LANGAGENDA131","Veuillez saisir un nom");
define("LANGAGENDA132","Veuillez saisir une URL");
define("LANGAGENDA133","Ajouter un favori");
define("LANGAGENDA134","Impression en mode paysage conseillée");
define("LANGAGENDA135","Semaine précédente ");
define("LANGAGENDA136","Semaine ");
define("LANGAGENDA137","du");
define("LANGAGENDA138","Anniversaire");
define("LANGAGENDA139","Rappel par défaut à la création d'une note");
define("LANGAGENDA140","Pas de rappel");
define("LANGAGENDA141","Rappel");
define("LANGAGENDA142","copie par mail");
define("LANGAGENDA143","minute(s)");
define("LANGAGENDA144","heure(s)");
define("LANGAGENDA145","jour(s)");
define("LANGAGENDA146","Journée type");
define("LANGAGENDA147","Termine à");
define("LANGAGENDA148","Téléphone VF");
define("LANGAGENDA149","Interface");
define("LANGAGENDA150","Planning par défaut");
define("LANGAGENDA151","Quotidien");
define("LANGAGENDA152","Hebdomadaire");
define("LANGAGENDA153","Mensuel");
define("LANGAGENDA154","30 minutes");
define("LANGAGENDA155","15 minutes");
define("LANGAGENDA156","45 minutes");
define("LANGAGENDA157","1 heure");
define("LANGAGENDA158","Sélection automatique de l'heure de fin d'une note");
define("LANGAGENDA159","Partage du planning<BR>en consultation");
define("LANGAGENDA160","Personnes autorisées à consulter mon planning");
define("LANGAGENDA161","Non partagé");
define("LANGAGENDA162","Au choix");
define("LANGAGENDA163","Tout le monde");
define("LANGAGENDA164","Partage du planning<BR>en modification");
define("LANGAGENDA165","Personne(s) pouvant m'affecter une note");
define("LANGAGENDA166","M'informer par mail lorsqu'une note m'est affectée");
define("LANGAGENDA167","Supprimer cette note que j'ai créée");
define("LANGAGENDA168","Supprimer cette note que l'on m'a affectée");
define("LANGAGENDA169","M'approprier cette note qui m'a été affectée");
define("LANGAGENDA170","Toute la journée");
define("LANGAGENDA171","Choix du libellé");
define("LANGAGENDA172","Nouveau libellé");
define("LANGAGENDA173","Intitulé");
define("LANGAGENDA174","Durée moyenne");
define("LANGAGENDA175","Couleur");
define("LANGAGENDA176","Apparence de la note");
define("LANGAGENDA177","Supprimer ce libellé ?");
define("LANGAGENDA178","Enregistrer un mémo");
define("LANGAGENDA179","Veuillez saisir un titre");
define("LANGAGENDA180","Titre");
define("LANGAGENDA181","Contenu");
define("LANGAGENDA182","Etes-vous sûr de vouloir effacer ce mémo ?");
define("LANGAGENDA183","Enregistrer une note");
define("LANGAGENDA184","La note que vous souhaitez modifier appartient à une série récurrente");
define("LANGAGENDA185","Souhaitez-vous modifier toute la série ou uniquement cette occurence ?");
define("LANGAGENDA186","Toute la série");
define("LANGAGENDA187","Uniquement cette occurence");
define("LANGAGENDA188","Note couvrant toute la journée");
define("LANGAGENDA189","Afficher le calendrier");
define("LANGAGENDA190","Toute la journée");
define("LANGAGENDA191","Débute à");  // Début à
define("LANGAGENDA192","Personne<BR>concernée");
define("LANGAGENDA193","Apparence de la note");
define("LANGAGENDA194","Note publique");
define("LANGAGENDA195","note détaillée dans le partage de planning");
define("LANGAGENDA196","mention \"Occupé\" dans le partage de planning");
define("LANGAGENDA197","Note privée");
define("LANGAGENDA198","Occupé(e)");
define("LANGAGENDA199","considérer comme <B>non disponible</B> dans le module des disponibilités");
define("LANGAGENDA200","Libre");
define("LANGAGENDA201","considérer comme <B>libre</B> dans le module des disponibilités");
define("LANGAGENDA202","Couleur");
define("LANGAGENDA203","Partage");
define("LANGAGENDA204","Disponibilité");
define("LANGAGENDA205","Rappel");
define("LANGAGENDA206","Pas de rappel");
define("LANGAGENDA207","copie par mail");
define("LANGAGENDA208","à l'avance");  // à l'avance
define("LANGAGENDA209","Périodicité");
define("LANGAGENDA210","Aucune");
define("LANGAGENDA211","Quotidienne");
define("LANGAGENDA212","Hebdomadaire");
define("LANGAGENDA213","Mensuelle");
define("LANGAGENDA214","Annuelle");
define("LANGAGENDA215","Tous les ");
define("LANGAGENDA215bis","jours");
define("LANGAGENDA216","Tous les jours ouvrables (Lundi au Vendredi)");
define("LANGAGENDA217","Tous les jours de ma semaine type");
define("LANGAGENDA218","Les informations saisies ou modifiées ne seront pas enregistrées\\nEtes-vous sûr de vouloir continuer ?");
define("LANGAGENDA219","profil");
define("LANGAGENDA220","Tous les ");
define("LANGAGENDA221","Toutes les ");
define("LANGAGENDA221bis","semaines");
define("LANGAGENDA222","de chaque mois");
define("LANGAGENDA223","premier");
define("LANGAGENDA224","deuxième");
define("LANGAGENDA225","troisième");
define("LANGAGENDA226","quatrième");
define("LANGAGENDA227","dernier");
define("LANGAGENDA228","du mois");
define("LANGAGENDA229","Le ");
define("LANGAGENDA230","Définir la date de fin");
define("LANGAGENDA231","Fin après"); // Fin après
define("LANGAGENDA232","Fin le");
define("LANGAGENDA233","occurence(s)");
define("LANGAGENDA234","Veuillez saisir un libellé");
define("LANGAGENDA235","Veuillez saisir une date");
define("LANGAGENDA236","Veuillez sélectionner une heure de fin\\npostérieure à l'heure de début");  // \\n signifie un retour chariot
define("LANGAGENDA237","Veuillez sélectionner une personne");
define("LANGAGENDA238","Veuillez saisir un nombre de jours\\nsupérieur ou égal à 1");
define("LANGAGENDA239","Veuillez saisir un nombre d'occurences\\nsupérieur ou égal à 1");
define("LANGAGENDA240","Répétition"); // répétition
define("LANGAGENDA241","Veuillez saisir votre nom et votre prénom au préalable");
define("LANGAGENDA242","Veuillez saisir votre Prénom");
define("LANGAGENDA243","Vous devez saisir votre login");
define("LANGAGENDA244","Veuillez saisir votre ancien mot de passe");
define("LANGAGENDA245","Mots de passe différents");
define("LANGAGENDA246","Un mot de passe est obligatoire");
define("LANGAGENDA247","Veuillez sélectionner une heure de fin\\npostérieure à l'heure de début");
define("LANGAGENDA248","Supprimer cette occurence");
define("LANGAGENDA249","Note récurente");
define("LANGAGENDA250","Supprimer cette note que j'ai créée");
define("LANGAGENDA251","M'approprier cette note qui m'a été affectée");
define("LANGAGENDA252","Filtrer");
define("LANGAGENDA253","Imprimer ce planning");
define("LANGAGENDA254","Impression en mode paysage conseillée");
define("LANGAGENDA255","Note créée par ");
define("LANGAGENDA256","Changer le statut");
define("LANGAGENDA257","Supprimer cette occurence");
define("LANGAGENDA258","Supprimer cette note que j'ai créée");
define("LANGAGENDA259","Supprimer cette note que l'on m'a affectée");
define("LANGAGENDA260","une note");
define("LANGAGENDA261","un anniversaire");
define("LANGAGENDA262","un contact");
define("LANGAGENDA263","A l'utilisateur sélectionné ci-dessous");
define("LANGAGENDA264","Ajouter une note");
define("LANGAGENDA265","Recherche");
define("LANGAGENDA266","Disponibilités");
define("LANGAGENDA267","Contacts");
define("LANGAGENDA268","Mémo");
define("LANGAGENDA269","Libellés");
define("LANGAGENDA270","Favoris");
define("LANGAGENDA271","Profil");
define("LANGAGENDA272","Echec création export");
define("LANGAGENDA273","Agenda de ");
// FIN AGENDA

define("LANGL","L");  // L de lundi
define("LANGM","M");  // M de mardi
define("LANGME","M");  // M de mercredi
define("LANGJ","J");  // J de jeudi
define("LANGV","V");  // V de vendredi
define("LANGS","S");  // S de samedi
define("LANGD","D");  // D de dimanche

define("LANGL1","Lun"); // Jours sur 3 lettres
define("LANGM1","Mar");	// Jours sur 3 lettres
define("LANGME1","Mer"); // Jours sur 3 lettres
define("LANGJ1","Jeu");	// Jours sur 3 lettres
define("LANGV1","Ven");	// Jours sur 3 lettres
define("LANGS1","Sam");	// Jours sur 3 lettres
define("LANGD1","Dim");	// Jours sur 3 lettres

define("LANGMOIS21","Janv");			// mois abregé
define("LANGMOIS22","Fév"); 		// mois abregé
define("LANGMOIS23","Mars");			// mois abregé
define("LANGMOIS24","Avr");				// mois abregé
define("LANGMOIS25","Mai");				// mois abregé
define("LANGMOIS26","Juin");			// mois abregé
define("LANGMOIS27","Juil");			// mois abregé
define("LANGMOIS28","Ao&ucirc;t");		// mois abregé
define("LANGMOIS29","Sept");			// mois abregé
define("LANGMOIS210","Oct");			// mois abregé
define("LANGMOIS211","Nov"); 			// mois abregé
define("LANGMOIS212","Déc"); 	// mois abregé



define("LANGPROFP22","Cet ".INTITULEENSEIGNANT." est déjà assigné comme professeur principal. \\n\\n L'Equipe TRIADE");



define("LANGSTAGE23","Nom de l'activité");
define("LANGSTAGE24","Enregistrer une nouvelle entreprise");
define("LANGSTAGE25","Le nom de cette entreprise est déjà enregistré");
define("LANGSTAGE26","Nom de l'entreprise");
define("LANGSTAGE27","Contact");
define("LANGSTAGE28","Adresse");
define("LANGSTAGE29","Code Postal");
define("LANGSTAGE30","Ville");
define("LANGSTAGE31","Secteur Activité");
define("LANGSTAGE32","ajouter activité");
define("LANGSTAGE33","Activité principale");
define("LANGSTAGE34","Téléphone");
define("LANGSTAGE35","Fax");
define("LANGSTAGE36","Email");
define("LANGSTAGE37","Informations");
define("LANGSTAGE38","Consultation des entreprises");
define("LANGSTAGE39","Société");
define("LANGSTAGE40","Activité principale");
define("LANGSTAGE41","Autre recherche");
define("LANGSTAGE42","Tél. / Fax");
define("LANGSTAGE43","Aucune entreprise pour ce nom");
define("LANGSTAGE44","Planification des stages");
define("LANGSTAGE45","Date de début de stage");
define("LANGSTAGE46","Date de fin de stage");
define("LANGSTAGE47","Enregistrer le stage");
define("LANGSTAGE48","Numéro du stage");
define("LANGSTAGE49","Modification des dates de stage");
define("LANGSTAGE50","Stage");
define("LANGSTAGE51","Date du stage");
define("LANGSTAGE52","Erreur de saisie");
define("LANGSTAGE53","Stage mise à jour");
define("LANGSTAGE54","Le stage du ");
define("LANGSTAGE55","pour la ".INTITULECLASSE." de");
define("LANGSTAGE56","est enregistré");
define("LANGSTAGE57","Date de stage, supprimée \\n\\n L'Equipe TRIADE");
define("LANGSTAGE58","Entreprise enregistrée \\n\\n L'Equipe TRIADE");
define("LANGSTAGE59","Modification d'entreprise");
define("LANGSTAGE60","Entreprises par activité");
define("LANGSTAGE61","Recherche d'entreprises");
define("LANGSTAGE62","Info");
define("LANGSTAGE63","Liste complète");
define("LANGSTAGE64","Visualisation des dates de stage");
define("LANGSTAGE65","Suppression d'entreprise");
define("LANGSTAGE66","Entreprise Supprimée \\n\\n L'Equipe TRIADE");
define("LANGSTAGE67","Consulter les entreprises par activité");
define("LANGSTAGE68","Aucune entreprise pour ce nom");
define("LANGSTAGE69","Visualisation d'un ".INTITULEELEVE." à un stage");
define("LANGSTAGE70","Imprimer le stage numéro");
define("LANGSTAGE71","Visualisation d'un ".INTITULEELEVE." aux stages");
define("LANGSTAGE72","&nbsp;Date&nbsp;du&nbsp;Stage&nbsp;"); // respecter les &nbsp;
define("LANGSTAGE73","Retour");
define("LANGSTAGE74","Entreprise");
define("LANGSTAGE75","Affectation d'un ".INTITULEELEVE." à un stage");
define("LANGSTAGE76","Lieu du stage");
define("LANGSTAGE77","Responsable");
define("LANGSTAGE78",ucfirst(INTITULEENSEIGNANT)." Visiteur");
define("LANGSTAGE79","Logé");
define("LANGSTAGE80","Nourri");
define("LANGSTAGE81","Passage dans n services");
define("LANGSTAGE82","Raison chgment de service");
define("LANGSTAGE83","Info. complémentaires");
define("LANGSTAGE84","Création enregistrée \\n \\n L'Equipe TRIADE");
define("LANGSTAGE85","Date de la visite");
define("LANGSTAGE86","Modification d'un ".INTITULEELEVE." à un stage");
define("LANGSTAGE87","Informations enregistrées");
define("LANGSTAGE88","Suppression d'un ".INTITULEELEVE." à un stage");


define("LANGRESA62","Libellé");
define("LANGRESA63","Refuser");
define("LANGRESA64","Ajouter une demande");
define("LANGRESA65","&nbsp;De&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;à");
define("LANGRESA66","Réservé");
define("LANGRESA66bis","par");  // suite réservé par
define("LANGRESA67","Non confirmé");
define("LANGRESA68","Confirmé");
define("LANGRESA69","Enregistrement terminé");
define("LANGRESA70","réservation pour le ");






define("LANGNOTEUSA1","Configuration des attributions des notes pour le mode USA");
define("LANGNOTEUSA2","Ce module vous permet de positionner les lettres en fonction du pourcentage à attribuer à chaque note (lettre).");
define("LANGNOTEUSA3","Exemple : de 95 à 100 --> A+ , de 87 à 94  --> A, etc...");
define("LANGNOTEUSA4","De");
define("LANGNOTEUSA4bis","à");
define("LANGNOTEUSA4ter","équivaut à");   //  ex : De  10 à 20 équivaut à B
define("LANGNOTEUSA5","Entre la note");
define("LANGNOTEUSA5bis","et la note");
define("LANGNOTEUSA5ter","cela équivaut à");



define("LANGABS56","Liste des absences non justifiées");
define("LANGABS57","Mise à jour réalisée pour cette liste d'".INTITULEELEVE."s");




define("LANGSANC1","Sanction créée -- L'Equipe TRIADE");
define("LANGSANC2","Catégorie non supprimée. Cette catégorie est déjà affectée à une sanction ou un ".INTITULEELEVE." -- Equipe TRIADE");
define("LANGSANC3","Configuration Discipline");
define("LANGSANC4","Enregistrement des catégories.");
define("LANGSANC5","Intitulé de la catégorie");
define("LANGSANC6","Enregistrement des noms des sanctions par catégorie.");
define("LANGSANC7","Intitulé de la sanction");
define("LANGSANC8","Configuration retenue");
define("LANGSANC9","Avertissement d'un message  lorsque l'".INTITULEELEVE."  a atteint la limite autorisée.");
define("LANGSANC10","Pour  la catégorie");
define("LANGSANC11","Avertissement d'un message au bout de");
define("LANGSANC12","Nb de fois");
define("LANGSANC13","Créé par");
define("LANGSANC14","Date de saisie");

// Modification de ces 2 phrases à traduire
// define("LANGPARAM1","<font class=T1>Composez votre texte pour le contenu du message de l'absence pour l'envoi du courrier aux parents d'".INTITULEELEVE.". Pour une prise en compte du nom et du prénom de l'".INTITULEELEVE." automatiquement dans chaque document, veuillez présiser la chaîne <b>NomEleve</b> et <b>PrenomEleve</b> à l'emplacement désiré. De même possibilité d'indiquer la classe avec le mot clef <b>ClasseEleve</b>, ou la date de l'absence ABSDEBUT ou ABSFIN ainsi que la durée ABSDUREE </font><br><br>");
// define("LANGPARAM2","<font class=T1>Composez votre texte pour le contenu du message de retard pour l'envoi du courrier aux parents. Pour une prise en compte du nom et du prénom de l'".INTITULEELEVE." automatiquement dans chaque document, veuillez présiser la chaîne <b>NomEleve</b> et <b>PrenomEleve</b> à l'emplacement désiré. De même possibilité d'indiquer la classe avec le mot clef <b>ClasseEleve</b>, ou la date du retard RTDDATE , l'heure RTDHEURE ainsi que le durée RTDDUREE </font><br><br>");


define("LANGMODIF4","Modification d'un compte");
define("LANGMODIF5","Informations de connexion");
define("LANGMODIF6","Photo d'identité");
define("LANGMODIF7","Cordonn&eacute;es du compte");
define("LANGMODIF8","Adresse");
define("LANGMODIF9","Code Postal");
define("LANGMODIF10","Commune");
define("LANGMODIF11","T&eacute;l.");
define("LANGMODIF12","Email");
define("LANGMODIF13","Modifier le compte");
define("LANGMODIF14","Compte modifié -- Equipe TRIADE");
define("LANGMODIF15","Le mot de passe de ");
define("LANGMODIF15bis"," a été modifié.");
define("LANGMODIF16","Modification du mot de passe");
define("LANGMODIF17","Impossible photo de taille non conforme");
define("LANGMODIF18","Réactualiser cette photo");
define("LANGMODIF19","Ajouter la photo");
define("LANGMODIF20","Modifier la photo");

define("LANGGRP25bis","Gestion des groupes");
define("LANGGRP26","Liste des groupes");
define("LANGGRP27","Ajouter un ".INTITULEELEVE." dans un groupe");
define("LANGGRP28","Supprimer un ".INTITULEELEVE." d'un groupe");
define("LANGGRP29","Nom du Groupe");
define("LANGGRP30","Classe(s) concernée(s)");
define("LANGGRP31","Modifier liste");
define("LANGGRP32","Ajouter des ".INTITULEELEVE."s dans le groupe");
define("LANGGRP33","Ajouter un ".INTITULEELEVE." dans ce groupe");
define("LANGGRP34","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." en ".INTITULECLASSE." de ");
define("LANGGRP35","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." dans le groupe");
define("LANGGRP36","Valider le groupe");
define("LANGGRP37","Groupe modifié -- Equipe TRIADE ");
define("LANGGRP38","Liste des ".INTITULEELEVE."s du groupe ");
define("LANGGRP39","Aucun ".INTITULEELEVE." dans ce groupe");

define("LANGCARNET1","Carnet de notes");
define("LANGCARNET2","Classe de l'".INTITULEELEVE);
define("LANGCARNET3","Cliquez sur le <b>nom</b> de l'".INTITULEELEVE);

define("LANGPASSG1","Le mot de passe doit être de <b>8 caractères</b> minimum,<br /> <b>alphanumérique</b> et utilisant <b>majuscule et minuscule</b>.");
define("LANGPASSG2","Le mot de passe n'est pas correct. \\n Le mot de passe doit comporter : \\n\\n -> 8 caractères minimum, \\n -> alphanumérique, \\n -> majuscule et minuscule \\n\\n L\\'Equipe TRIADE");
define("LANGPASSG3","Echec de la création");



define("LANGDISC38","Ajouter Sanction");
define("LANGDISC39","Gestion des disciplines");
define("LANGDISC40","Retenue non effectuée.");
define("LANGDISC41","Planning Retenue.");
define("LANGDISC42","Retenue non affectée à un éléve.");
define("LANGDISC43","Configuration.");
define("LANGDISC44","Supprimer Retenues et sanctions");
define("LANGDISC45","Supprimer Retenues et sanctions");
define("LANGDISC46","Liste des absences et des retards d'une ".INTITULECLASSE);
define("LANGDISC47","Indiquez le début de la période");
define("LANGDISC48","Indiquez la fin de la période");
define("LANGDISC49","Indiquez la section");
define("LANGDISC50","<br><ul>Suppression des retenues et des sanctions en <br>fonction de l'intervalle de date.</ul>");
define("LANGDISC51","Toutes les ".INTITULECLASSE."s");
define("LANGDISC52","Retenues et sanctions supprimées");
define("LANGDISC53","Erreur ! Retenues et sanctions non supprimées");

define("LANGIMP53","Fichier ASCII via SQL ");


// autre new

define("LANGSTAGE31bis","2ème Secteur Activité");
define("LANGSTAGE31ter","3ème Secteur Activité");
define("LANGMEDIC1","Dossier médical d'un ".INTITULEELEVE);
define("LANGMEDIC2","Envoyer la recherche");
define("LANGMEDIC3","Information / Modification");


define("LANGDISC54","Visualiser les disciplines d'un éléve");
define("LANGDISC55","Supprimer une Sanction");
define("LANGDISC56","Supprimer Sanction");

define("LANGBASE6bis","Total d'".INTITULEELEVE."s dans le fichier ");

define("LANGMODIF21","Le mot de passe doit avoir : \\n\\n - 8 caracteres minimum \\n - Alphanumerique \\n - MAJUSCULE et minuscule.\\n\\n Equipe TRIADE");

define("LANGMODIF22","Mot de passe : 8 caractères - Alphanumérique - Majuscules et minuscules");
define("LANGPASS1bis","Confirmer mot de passe");

define("LANGMODIF23","Vous pouvez changer votre mot de passe pour votre compte TRIADE");
define("LANGMODIF24","Le compte ");
define("LANGMODIF24bis","est en cours de validation..");
define("LANGMODIF24ter","est maintenant opérationnel");
define("LANGMODIF25","Mot de passe non identique. \\n\\n Equipe TRIADE");

define("LANGABS58","Visualisation / Suppression  Absence - Retard");
define("LANGABS59","Affichage complet des retards");
define("LANGABS60","Pendant");  	// une durée pendant tant de temps
define("LANGABS61","Visualisation / Modification d'une  Absence - Retard");
define("LANGABS62","Affichage <b>complet</B> des rtds et abs");
define("LANGABS63","Saisie le");
define("LANGABS64","Affichage des <b>5</B> derniers rtd et abs");
define("LANGABS65","Affichage complet des absences");
define("LANGABS66","Mise à jour effectuée pour cette liste d'".INTITULEELEVE."s");
define("LANGABS6bis","Liste des retards non justifiés");
define("LANGABS4bis","Lister les absences ou retards");
define("LANGABS67","<font class=T2>Aucun éléve dans cette ".INTITULECLASSE."</font>");
define("LANGABS68","Abs / Rtds d'une ".INTITULECLASSE);
define("LANGABS69","Cumul abs/rtds des ".INTITULEELEVE."s");
define("LANGABS70","Configuration des motifs");
define("LANGABS71","Nombre d'absences / Cumul");
define("LANGABS72","Nombre de Retards / Cumul");
define("LANGABS73","Absences - Retards -  de la  ".INTITULECLASSE);
define("LANGABS74","Effectuer la mise à jour");
define("LANGABS75","Aucun absent ou retard");
define("LANGABS76","relevé à ");

define("LANGDEPART3","Suite à un problème technique,");
define("LANGDEPART4","l'accès au serveur est indisponible. L'équipe TRIADE intervient actuellement sur le serveur.");

define("LANGBASE3_2","Voici la liste des fichiers pouvant être importés.");
define("LANGbasededoni21_2","Souhaitez-vous continuer ? \\n\\n L\'Equipe TRIADE");
define("LANGbasededon21","L'envoi du fichier peut durer de <b>2 à 4 mn</b> en fonction du nombre d'éléments.");
define("LANGbasededon31_2","Indiquez les matières que vous souhaitez importer.");
define("LANGBASE10_2","Indiquez les ".INTITULEENSEIGNANT."s à ajouter.");

define("LANGBASE16_2"," Les colonnes sont représentées sous la forme : <b>nom de login ; prénom de login ; mot de passe en clair</b>");
define("LANGIMP25_2","nom établissement");
// ----------------------------- //
define("LANGABS77","Signalé le");
define("LANGSTAGE89","Etablir la convention de stage");
define("LANGSTAGE90","Sortir les conventions de stage");
define("LANGSTAFE91","Liste des ".INTITULEELEVE."s en entreprise actuellement");
define("LANGSTAGE92","Liste des ".INTITULEELEVE."s en entreprise actuellement");
define("LANGPASSG4","Le mot de passe doit être de <b>8 caractères</b> minimum <br /><b>alphanumérique</b>.");
define("LANGPASSG5","Le mot de passe doit être de <b>4 caractères</b> minimum.");
define("LANGPASSG6","Le mot de passe n'est pas correct. \\n Le mot de passe doit comporter : \\n\\n -> 8 caractères minimum, \\n -> alphanumérique \\n\\n L\\'Equipe TRIADE");
define("LANGPASSG7","Le mot de passe n'est pas correct. \\n Le mot de passe doit comporter : \\n\\n -> 4 caractères minimum. \\n\\n L\\'Equipe TRIADE");

define("LANGMODIF22_1","Mot de passe : 4 caractères");
define("LANGMODIF22_2","Mot de passe : 8 caractères - Alphanumérique ");
define("LANGMODIF22_3","Mot de passe : 8 caractères - Alphanumérique - Majuscules et minuscules");
define("LANGDEPART2","<font color=red  class=T2>ATTENTION, pour utiliser TRIADE, la variable php '<strong>register_globals</strong>' doit être sur <u>Off</u>.</font><br />");


define("LANGacce15","Devoir à remettre pour le ");
define("LANGacce16","Devoir à rendre aujourd'hui !");
define("LANGacce17","Ajout d'une sanction disciplinaire");

define("LANGBASE41","Supprimer tous les ".INTITULEELEVE."s avant l'import");
define("LANGBASE7bis","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." déjà affecté");
define("LANGBASE8bis","pour les ".INTITULEELEVE."s <u>affectés</u> et <u>sans ".INTITULECLASSE."</u>");

define("LANGPER21bis","Langue&nbsp;/&nbsp;option");

define("LANGASS6ter","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
define("LANGASS41","Stockage");
define("LANGASS42","Paramétrage");

define("LANGIMP46bis","Mot de passe");

define("LANGIMP54","N° rue");
define("LANGIMP55","adresse");
define("LANGIMP56","code postal");
define("LANGIMP57","téléphone");
define("LANGIMP58","email");
define("LANGIMP59","commune");

define("LANGBULL1pp","Impression bulletin trimestriel ou semestriel");
define("LANGBT43pp","Imprimer Tableau");


define("LANGMESS38","Message lu.");
define("LANGMESS39","Message non lu.");


define("LANGDISC57","Motif&nbsp;/&nbsp;Sanction");

define("CUMUL01","Cumul des absences et retards d'une ".INTITULECLASSE." par ".INTITULEELEVE);
define("CUMUL02","Cumul des sanctions d'une ".INTITULECLASSE." par ".INTITULEELEVE);
define("CUMUL03","Cumul des sanctions d'un ".INTITULEELEVE);
define("LANGPROJ18bis","heure(s)");
define("LANGCREAT1","Compte déjà existant.");
define("ERREUR1","Réseau Internet non disponible pour ce module.");
define("ERREUR2","Consulter le module Configuration pour activer le réseau.");


define("PASSG8","Modification du mot de passe");
define("PASSG9","Le mot de passe de l'".INTITULEELEVE." ");
define("PASSG9bis"," a été modifié.");


define("LANGPARAM34","Site Web de l'établissement");
define("LANGLOGO3bis","Le logo <b>doit être au format jpg</b>");


define("LANGMAT1","Enregistrer matière");
define("LANGMAT2","Liste / Modification d'une matière");
define("LANGMAT3","Supprimer matière");
define("LANGMAT4","Valider la modification");
define("LANGMAT5","Matière modifiée");
define("LANGMAT6","Matière déjà affectée");
define("LANGCLAS1","Liste / Modifier ".INTITULECLASSE);
define("LANGCLAS2","Classe modifiée");
define("LANGCLAS3","Classe déjà affectée");

define("LANGDEVOIR1","pour le  groupe");
define("LANGDEVOIR2","pour la  ".INTITULECLASSE);
define("LANGDEVOIR3","Enregistrer un devoir scolaire");
define("LANGCIRCU111","<font face=Verdana size=1><B><font color=red>D</font></B>ocument au format : <b> doc</b>, <b>pdf</b>, <b>txt</b>.</FONT>");

define("LANGAFF7","Module de suppression d'affectation des ".INTITULECLASSE."s.");
define("LANGAFF8","ATTENTION ce module est à utiliser lors de la suppression d'affectation,<br> il détruit toutes les notes des ".INTITULEELEVE."s  des ".INTITULECLASSE."s supprimées.");
define("LANGAFF9","ATTENTION, les notes des ".INTITULECLASSE."s sélectionnées  seront supprimées. \\n Souhaitez vous continuer ? \\n\\n Equipe TRIADE");
define("LANGCREAT2","Supprimer compte");


define("LANGPROF37","Cahier de textes.");

// news

define("LANGPARAM35","Choix du bulletin");
define("LANGPROBLE1","réponse par email");
define("LANGPROBLE2","Tous les champs doivent être renseignés");
define("LANGMESS37","Ce module n'a pas été validé par l'administrateur TRIADE.<br><br> L'Equipe TRIADE");

define("LANGPROFP23","Notes scolaires de ");
define("LANGPROFP24","du  mois de");
define("LANGPROFP25","Trombinoscope");
define("LANGPROFP26","Suivi d'un ".INTITULEELEVE);
define("LANGPROFP27","Informations sur les délégués");
define("LANGPROFP28","Message pour la ".INTITULECLASSE);
define("LANGPROFP29","Circulaire pour la ".INTITULECLASSE);
define("LANGPROFP30","Gestion de stage professionnel");
define("LANGPROFP31","Tableau des moyennes des ".INTITULEELEVE."s");
define("LANGPROFP32","Bulletins graphiques des ".INTITULEELEVE."s");


define("LANGLETTRELUNDI","L");	  // Lundi
define("LANGLETTREMARDI","M");    // Mardi
define("LANGLETTREMERCREDI","M"); // Mercredi
define("LANGLETTREJEUDI","J");    // Jeudi
define("LANGLETTREVENDREDI","V"); // Vendredi
define("LANGLETTRESAMEDI","S");   // Samedi
define("LANGLETTREDIMANCHE","D"); // Dimanche



define("LANGRESA71","réservation pour le");
define("LANGRESA72","de");
define("LANGRESA73","à");
define("LANGRESA74","Informations complémentaires");

define("LANGbasededoni52","valeur acceptée : <b>0</b> ou M.<br>");
define("LANGbasededoni53","valeur acceptée : <b>1</b> ou Mme.<br>");
define("LANGbasededoni54","valeur acceptée : <b>2</b> ou Mlle.<br>");
define("LANGbasededoni54_2","valeur acceptée : <b>3</b> ou Ms <br>");
define("LANGbasededoni54_3","valeur acceptée : <b>4</b> ou Mr <br>");
define("LANGbasededoni54_4","valeur acceptée : <b>5</b> ou Mrs <br>");


define("LANGacce_dep2bis","<br><b>ATTENTION !!  Vérifiez bien votre mode d'accès,<br> choisissez votre compte correspondant.</b>");

define("LANGNA3bis","Mot de passe parent "); //
define("LANGNA3ter","Mot de passe ".INTITULEELEVE." "); //

define("LANGELE244","Email");

define("LANGTP12","Veuillez valider votre compte");

define("LANGMESS40","Vous avez <strong> ");
define("LANGMESS40bis"," </strong> flux RSS enregistré(s).");  // ajouter "\" devant les quotes
define("LANGMESS41","Compte ");  // Compte comme "compte utilisateur".
define("LANGMESS42","Deuxième connexion");
define("LANGMESS43","Dernière connexion le");

define("LANGALERT4","ATTENTION, choisissez des noms de sujet différent.");

define("LANGMODIF26","Modifier sous-matière");
define("LANGPROF38","Notes Trimestrielles");
define("LANGPROF39","Complément d'information");

define("LANGCIRCU21","Disp. pour"); // abréviation de "Disponible pour" 

define("LANGTELECHARGE","Télécharger"); //  downloader

define("LANGPARENT15bis","Sanction du");
define("LANGDISC2bis","Imprimer les sanctions du jour");

define("LANGRECH5","Indiquer l'élément ou les éléments  à  rechercher");
define("LANGRECH6","Trier par ordre");

define("LANGPROFP33","Remplir les bulletins");
define("LANGPROFP34","Vérifier bulletin");
define("LANGPROFP35","Consulter ou modifier les commentaires des bulletins");


define("LANGPROFP36","Aucune date trimestrielle <br /><br /> affectée pour <u>cette année scolaire</u>");
define("LANGPROFP37","Enregistrer les commentaires");

define("LANGGRP40","Groupe créé");
define("LANGGRP41","Voici la liste des ".INTITULEELEVE."s non enregistrés");
define("LANGGRP42","Ce groupe existe déjà");
define("LANGGRP43","Erreur de fichier");
define("LANGGRP44","Supprimer un groupe");
define("LANGGRP45","Importer fichier");
define("LANGGRP46","Nom de groupe existant -- Service TRIADE");

define("LANGPARAM37","Académie");
define("LANGAGENDA274","F&ecirc;te du jour ");
define("LANGPARAM38","Joyeux Anniversaire à ");
define("LANGEDT1","F"); // première lettre
define("LANGEDT1bis","ichier au format <b>xml</b> ou <b>zip</b> <br>Taille maximum du fichier : ");
define("ERREUR3","Contacter l'administrateur TRIADE pour activer le réseau.");
define("LANGELE30","Changer le mot de passe");
define("LANGMESS44","Message à un ".INTITULEELEVE." en ");
define("LANGMESS5","Message à un parent en : ");
define("LANGMESS45","Message vers un email : ");
define("LANGMESS2","Message pour ".INTITULEDIRECTION." : ");
define("LANGTRONBI9","des ".INTITULEELEVE."s");
define("LANGTRONBI10","du personnel");
define("LANGTRONBI11","Trombinoscope du personnel");

define("LANGTITRE15","Mise en place des professeurs principaux ou des instituteurs");
define("LANGPER7","affecté en ".INTITULECLASSE); 
define("LANGPROF40","Renseignements complémentaires");
define("LANGPROFP38","Remplir ou consulter le Carnet de Suivi");
define("LANGEDIT2","Tél. Portable 1");
define("LANGEDIT3","Civilité ");
define("LANGEDIT4","Nom Resp. 2");
define("LANGEDIT5","Prénom Resp. 2");
define("LANGEDIT6","Lieu de naissance");
define("LANGEDIT7","Civilité ");
define("LANGEDIT8","Nom Resp. 1");
define("LANGEDIT9","Tél. Portable 2");
define("LANGEDIT10"," Parent");
define("LANGEDIT11","E-mail ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
define("LANGEDIT12","Tél. ".INTITULEELEVE);
define("LANGEDIT13","E-mail Tuteur 2");
define("LANGEDIT14","d'aujourd'hui");
define("LANGEDIT15","Depuis 1 jour");
define("LANGEDIT16","Depuis 2 jours");
define("LANGEDIT17","Depuis 3 jours");
define("LANGEDIT18","Depuis 4 jours");
define("LANGEDIT19","Retard(s) non justifié(s)");
define("LANGEDIT20","Tél. Portable ");
define("LANGSMS1","Envoi SMS pour les retards depuis ");
define("LANGSMS2","Non indiqué");
define("LANGSUPPLE","Liste des suppléants");
define("LANGSUPPLE1","En remplacement de ");
define("LANGTITRE2","Actualités de l'établissement");
define("LANGTITRE1","Evénements");

define("LANGDISC58","Ajouter une discipline à un ".INTITULEELEVE);
define("LANGDISC59","Saisie en mode U.S.A.");
define("LANGDISC60","Examen ");

define("LANGBT8","Lister / Modification");
define("LANGBT9","Lister / Modification");
define("LANGBT10","Lister / Modification");
define("LANGDIRECTION","Administration");

define("LANGTITRE36","Gestion des membres ".INTITULEDIRECTION);
define("LANGTITRE37","Gestion des membres Vie Scolaire");
define("LANGTITRE38","Gestion ".INTITULEENSEIGNANT);
define("LANGTITRE39","Gestion Suppléants");
define("LANGTITRE40",INTITULEELEVE);
define("LANGTITRE41","resp."); // pour l'abréviation de "responsable"
define("LANGTITRE42","tuteur"); // dans le cadre familial
define("LANGTITRE43","Gestion d'un ".INTITULEELEVE);
define("LANGTITRE44","Importer une liste d'".INTITULEELEVE."s");
define("LANGTITRE45","Recherche ".INTITULEELEVE);
define("LANGCHERCH1","En fonction du critère de recherche");
define("LANGCHERCH2","Fin de la recherche");
define("LANGCHERCH3","Nombre d'éléments trouvés");
define("LANGPROF3bis","Visualiser les devoirs, interrogations et contrôles");
define("LANGTROMBI","Exporter les listes d'".INTITULEELEVE."s vers WellPhoto");
define("LANGPURG1","Suppression des informations");
define("LANGPUR2","Suppression des informations");
define("LANGPROFP39","Tableau des moyennes annuelles :");
define("LANGBLK1","Votre compte est désactivé.<br /><br />Vous avez tenté un accès sur une page non autorisée.<br /><br />Pour réactiver votre compte, veuillez contacter votre établissement scolaire.<br /><br />L'Equipe TRIADE.");
define("LANGCARNET4","accéder");
define("LANGFORUM10bis","Votre prénom ");
define("LANGTPROBL11","Nous nous chargeons de vous répondre dans les plus brefs délais. \\n\\n  L'Equipe TRIADE ");
define("LANGTRAD1","Liste des opérations effectuées");
define("LANGPARAM39","Certificat enregistré");
define("LANGPARAM40","Certificat non enregistré");
define("LANGPARAM41","Le fichier doit être au format <b>rtf</b> et inférieur à 2Mo");
define("LANGBASE42","Importation du fichier");
define("ACCEPTER","Accepter");
define("LANGCONDITION","J'accepte les Conditions");
define("LANGPARAM42","Liste des retards ou absences");
define("LANGCARNET5","Consulter le Carnet de Suivi");
define("LANGCARNET6","Remplir le Carnet de Suivi");
define("LANGCARNET7","Remplir");
define("LANGCARNET8","Carnet de Suivi");
define("LANGCARNET9","Créer un Carnet de Suivi");
define("LANGCARNET10","Modifier un Carnet de Suivi");
define("LANGCARNET11","Supprimer un Carnet de Suivi");
define("LANGCARNET12","Consulter un Carnet de Suivi");
define("LANGCARNET13","Exporter un Carnet de Suivi");
define("LANGCARNET14","Importer un Carnet de Suivi");
define("LANGCARNET15","Importer");
define("LANGCARNET16","Exporter");
define("LANGCARNET17","Menu Carnet de Suivi");
define("LANGCARNET18","Nom du Carnet de Suivi");
define("LANGCONTINUER","Continuer --->");
define("LANGCARNET19","Création d'un Carnet de Suivi");
define("LANGCARNET20","Codes d'appréciation pouvant être choisis par les ".INTITULEENSEIGNANT."s");
define("LANGCARNET21","Lettres");
define("LANGCARNET22","Chiffres");
define("LANGCARNET23","Couleurs");
define("LANGCARNET24","Notes");
define("LANGCARNET25","(0 à 10 ou 0 à 20)");
define("LANGCARNET26","Correspondance");
define("LANGCARNET27","acquis");
define("LANGCARNET28","à&nbsp;confirmer");
define("LANGCARNET29","non&nbsp;acquis");
define("LANGCARNET30","en&nbsp;cours&nbsp;d'acquisition");
define("LANGCARNET31","non&nbsp;évalué");
define("LANGCARNET32","Vert");
define("LANGCARNET33","Bleu");
define("LANGCARNET34","Orange");
define("LANGCARNET35","Rouge");
define("LANGCARNET36","période");
define("LANGCARNET37","périodes");
define("LANGCARNET38","Gestion du Carnet de Suivi");
define("LANGCARNET39","Nombre(s) de période(s) imposant la signature des parents, de l'".INTITULEENSEIGNANT." et de la direction ");
define("LANGCARNET40","Nombre(s) ");
define("LANGCARNET41","Sections associées à ce Carnet de Suivi");
define("LANGCARNET42","Sections");
define("LANGCARNET43","Maximum 4 choix possibles (les 4 premiers seront conservés)");
define("LANGCARNET44","Carnet de Suivi créé. Vous pouvez maintenant ajouter les compétences associées à ce Carnet.");
define("LANGCARNET45","Ajout d'un domaine de compétences ");
define("LANGCARNET46","Intitulé du domaine de compétences ");
define("LANGCARNET47","Cet intitulé correspond-il à une rubrique de compétences ?  ");
define("LANGCARNET48","Intitulé");
define("LANGCARNET49","Ajout d'une compétence ");
define("LANGCARNET50","Modifier les caractéristiques générales du Carnet ");
define("LANGCARNET51","Ajouter un domaine de compétences ");
define("LANGCARNET52","Modifier un domaine de compétences ");
define("LANGCARNET53","Indiquez le Carnet de Suivi");
define("LANGCARNET54","Carnet de Suivi non existant ");
define("LANGCARNET55","Consultation d'un Carnet de Suivi");
define("LANGCARNET56","Un Carnet de Suivi");
define("LANGCARNET57","Récupération du Carnet de Suivi au format PDF");
define("LANGCARNET58","Exportation d'un Carnet de Suivi");
define("LANGCARNET59","Pour récupérer ce Carnet de Suivi");
define("LANGCARNET60","Modification d'un Carnet de Suivi");
define("LANGCARNET61","Suppression d'un Carnet de Suivi");
define("LANGCARNET63","Importation d'un Carnet de Suivi");
define("LANGCARNET64","Fichier à importer");
define("LANGCARNET65","Supprimer tout l'emploi du temps avant l'import ?");
define("LANGCARNET66","Importation annulée. <br><br>Ce nom de Carnet existe déjà ! <br />Veuillez supprimer ce Carnet avant d'effectuer l'importation.");
define("LANGCARNET62","ATTENTION !!! Toutes les notes assujetties au Carnet de Suivi seront effacées!");
define("LANGEDT2","Import Emploi du temps Visual Timetabling");
define("LANGEDT3","Import Visual Timetabling terminé");
define("LANGEDT4","Affichage / Gestion de l'Emploi du Temps");
define("LANGEDT5","Importer Emploi du temps Visual Timetabling");
define("LANGEDT6","Exporter Triade vers Visual Timetabling");
define("LANGEDT7","Affichage / Gestion de l'Emploi du Temps");
define("LANGEDT8","Administrer");
define("LANGEDT9","Mise en place de l'Emploi du Temps");
define("LANGEDT10","Module SQLite non supporté. Veuillez valider votre serveur pour la prise en charge du support SQLite.");
define("LANGGRP47","Rechercher les groupes");
define("LANGGRP48","Liste des groupes d'un ".INTITULEELEVE);
define("LANGGRP49","Liste des groupes");
define("LANGDISP21","Configuration Motif abs / rtds");
define("LANGDISP22","Enregistrement des motifs ");
define("LANGDISP23","Intitulé du motif ");
define("LANGDISP24","Liste des motifs ");
define("LANGDISP25","Nombre d'".INTITULEELEVE."s mis à jour");
define("LANGDISP26","Le fichier doit être au format xls");
define("LANGCARNET63","Import Carnet de Suivi terminé");
define("LANGCARNET64","Liste des sanctions");
// News 2
define("LANGCARNET67","Ajout d'une sanction disciplinaire");
define("LANGCARNET68","Horaire");
define("LANGVIES1","Nom de la personne rattachée au bulletin");
define("LANGVIES2","Coefficient de la note Vie Scolaire sur le bulletin");
define("LANGVIES3","Coefficient ".ucfirst(INTITULEENSEIGNANT));
define("LANGVIES4","Coefficient Vie scolaire");
define("LANGVIES5","Liste des ".ucfirst(INTITULEENSEIGNANT)."s");
define("LANGVIES6","Informations Scolaires Complémentaires");


define("LANGVIES7","Enregistrer les notes et commentaires");
define("LANGVIES8","Impression des absences d'une ".INTITULECLASSE);
define("LANGVIES9","Indiquez le mois");
define("LANGVIES10","Indiquez une ".INTITULECLASSE);
define("LANGPDF1","Un fichier PDF pour l'ensemble");
define("LANGPDF2","Un fichier PDF par ".INTITULEELEVE);
define("LANGEDIT5bis","Prénom Resp. 1");
define("LANGGRP50","Modifier le nom d'un groupe");
define("LANGGRP51","Nom du groupe");
define("LANGGRP52","Module Modification");
define("LANGGRP53","Nouveau nom de groupe");
define("LANGGRP54","ou relevé de notes");
define("LANGGRP55","examen");
define("LANG1ER","1ère");
define("LANG2EME","2ème");
define("LANG3EME","3ème");
define("LANG4EME","4ème");
define("LANG5EME","5ème");
define("LANG6EME","6ème");
define("LANG7EME","7ème");
define("LANG8EME","8ème");
define("LANG9EME","9ème");
define("LANGGRP56","Notation sur");
define("LANGGRP57","Garder");
define("LANGGRP58","Attention, les notes des ".INTITULEELEVE."s sélectionnés à la suppression <br /> seront supprimées dans toutes les ".INTITULECLASSE."s utilisant ce groupe !!!");
define("LANGGRP59","Décocher le(s) ".INTITULEELEVE."(s) n'appartenant plus au groupe");
define("LANGGRP60","Modifier la liste");
define("LANGPARAM3","<font class=T1>Composez votre texte pour le contenu du certificat de scolarité.  Pour une prise en compte du nom, du prénom et de l'adresse de l'".INTITULEELEVE." automatiquement dans chaque document, veuillez présiser la chaîne <b>NomEleve</b>, <b>PrenomEleve</b>, <b>AdresseEleve</b>, <b>CodePostalEleve</b> et <b>VilleEleve</b> à l'emplacement désiré. De même, possibilité d'indiquer la ".INTITULECLASSE." avec le mot clef <b>ClasseEleve</b> ou <b>ClasseEleveLong</b>, la date de naissance avec <b>DateNaissanceEleve</b>, lieu de naissance via <b>LieuDeNaissance</b>, la date du jour via <b>DateDuJour</b>, l'année scolaire via <b>AnneeScolaire</b>, nationalité via <b>Nationalite</b>.</font><br><br>");
define("LANGEDIT20bis","Supp");  // abréviation de Supprimer  sur 3 lettres seulement
define("LANGGRP61","Retour à la mise à jour");
define("LANGRTDJUS","Justifi&eacute;"); // pour un retard
define("LANGABSJUS","Justifi&eacute;e"); // pour une abs
define("LANGPARAM2","<font class=T1>Composez votre texte pour le contenu du message de retard à envoyer aux parents. Vous pouvez préciser les informations suivantes : Nom de l'".INTITULEELEVE." : <b>NomEleve</b> - Prénom de l'".INTITULEELEVE." : <b>PrenomEleve</b> - Adresse : <b>AdresseEleve</b> - Code postal : <b>CodePostalEleve</b> - Ville : <b>VilleEleve</b> - Classe de l'".INTITULEELEVE." : <b>ClasseEleve</b> - Date du retard : <b>RTDDATE</b> - Heure du retard : <b>RTDHEURE</b> - Durée : <b>RTDDUREE</b>  - Cumul absence : <b>CumulABS</b> </font><br><br>");
define("LANGPARAM1","<font class=T1>Composez votre texte pour le contenu du message de l'absence à envoyer aux parents. Vous pouvez préciser les informations suivantes : Nom de l'".INTITULEELEVE." : <b>NomEleve</b> - Prénom de l'".INTITULEELEVE." : <b>PrenomEleve</b> - Adresse : <b>AdresseEleve</b> - Code postal : <b>CodePostalEleve</b> - Ville : <b>VilleEleve</b> - Classe de l'".INTITULEELEVE." : <b>ClasseEleve</b> - Date de début d'absence :  <b>ABSDEBUT</b> - Date de fin d'absence : <b>ABSFIN</b> - Durée : <b>ABSDUREE</b> - Nom du responsable 1 : <b>NomResponsable1</b> - Adresse responsable 1 : <b>AdresseResponsable1</b> - Ville responsable 1 : <b>VilleResponsable1</b> - Cumul absence : <b>CumulABS</b> - Date du jour : <b>DATEDUJOUR</b> </font><br><br>");
define("LANGGRP62","étude");
define("LANGGRP63","Courrier");
define("LANGDELEGUE1","délégué");
define("LANGEDT10bis","Module SimpleXML non supporté. Veuillez valider votre serveur pour la prise en charge de l'extension SimpleXML.");
define("LANGBULL45","Envoyer un message à tous les ".INTITULEENSEIGNANT."s cochés pour les prévenir de remplir leurs bulletins.");
define("LANGBULL46","Nombre de bulletins remplis dans la ".INTITULECLASSE);
define("LANGMESS46","Visualiser dans");
define("LANGMESS47","Supprimer une retenue ou une sanction");
define("LANGCOUR","Courrier terminé");
define("LANGCOUR1","Liste des retenues non effectuées");
define("LANGCOUR2","Configuration du courrier de retenues");
define("LANGPARAM43","<font class=T1>Composez votre texte pour le contenu du message de retenue à envoyer aux parents. Vous pouvez préciser les informations suivantes : Nom de l'".INTITULEELEVE." : <b>NomEleve</b> - Prénom de l'".INTITULEELEVE." : <b>PrenomEleve</b> - Adresse : <b>AdresseEleve</b> - Code postal : <b>CodePostalEleve</b> - Ville : <b>VilleEleve</b> - Classe de l'".INTITULEELEVE." : <b>ClasseEleve</b> - Date de la retenue : <b>DATERETENU</b> - Heure de la retenue : <b>HEURERETENU</b> - Durée : <b>RETENUDUREE</b> - Motif : <b>RETENUMOTIF</b> -  Catégorie : <b>RETENUCATEGORY</b> - Attribuée par : <b>ATTRIBUEPAR</b> - Devoir à faire : <b>DEVOIRAFAIRE</b> - Les faits : <b>FAITS</b> - Civilité tuteur 1 : <b>CIVILITETUTEUR1</b> - Nom du responsable 1 : <b>NOMRESP1</b> Prénom du responsable 1 : <b>PRENOMRESP1</b> - Date du jour : <b>DATEDUJOUR</b> </font><br><br>");
define("RESA75","Informations complémentaires");
define("LANGCOM","Enregistrer tous vos commentaires dans votre bibliothèque.");
define("LANGCOM1","La valeur max doit être plus grande que la valeur min.");
define("LANGCOM2","Tous les champs doivent être indiqués correctement.");
define("LANGCOM3","Nombre d'".INTITULEELEVE."s : ");
define("LANGSTAGE91","Nom du responsable");
define("LANGSTAGE93","Fonction du resp.");
define("LANGSTAGE94","de l'entreprise");
define("LANGSTAGE95","Entreprise");
define("LANGSTAGE96","Nombre d'élèments trouvés");
define("LANGSTAGE97","Indiquer une valeur numérique, svp");
define("LANGSTAGE98","Indiquez la date du début de stage, svp");
define("LANGSTAGE99","Indiquez la date de fin de stage, svp");
define("LANGPATIENTE","Veuillez patienter");
define("LANGSMS3","Numéro de téléphone portable");
define("LANGSMS4","150 caractères maximum");
define("LANGSMS5","Message");
define("LANGSMS6","L'envoi du message SMS est conservé et accessible par ".INTITULEDIRECTION);
define("LANGSMS7","Envoi message SMS");
define("LANGSMS8","Envoyer un message SMS");
define("LANGSMS9","Liste des numéros de téléphones des parents <br> de ");
define("LANGSMS10","Envoyer un sms à toute une ".INTITULECLASSE);
define("LANGSMS11","Envoyer un sms à un parent d'".INTITULEELEVE." via son nom");
define("LANGSMS12","Envoyer un sms à une personne via son nom");
define("LANGSMS13","Envoyer un sms à une personne via son numéro");
define("LANGSMS14","Numéro");
define("LANGbasededoni54_5","valeur acceptée : <b>7</b> ou P <br>");
define("LANGbasededoni54_6","valeur acceptée : <b>8</b> ou Sr <br>");
define("LANGGRP27bis","Ajouter un ".INTITULEELEVE." dans plusieurs groupes");
define("LANGGRP28bis","Ajout ".INTITULEELEVE." dans groupe");
define("LANGGRP29bis","Saisie&nbsp;/&nbsp;Modif");
define("LANGNOTEUSA6","Correspondance des notes pour la notation en mode USA");
define("LANGNOTE1","Intitulé de l'examen");
define("LANGPARAM44","Recevoir un message lorsque vous recevez une information de type");
define("LANGMESS17bis","Config.");
define("LANGNNOTE2","Trier par ".INTITULECLASSE);
define("LANGNNOTE3","Trier par nom");
define("LANGNNOTE4","Indiquer le titre du document");
define("LANGBULL47","Bulletin sans sous-matières");
define("LANGBULL48","Bulletin avec sous-matières");
define("LANGBULL49","Bulletin examen blanc");
define("LANGMESS48","Boite de suppression");
define("LANGMESS49","Aucun ".INTITULEELEVE." n'a d'entreprise affectée.");
define("LANGMESS50","Plan de la ".INTITULECLASSE);
define("LANGMESS51","Indiquer les matières facultatives");
define("LANGMESS52","(Notes comptabilisées dans la moyenne générale, si elles sont supérieures à 10/20)");
define("LANGMESS53","Semaine précédente");
define("LANGMESS54","Semaine suivante");
define("LANGMESS55","Emploi du temps de la ".INTITULECLASSE);
define("LANGMESS56","Aucun ".INTITULEELEVE."");
define("LANGMESS57","Identifiant");
define("LANGMESS58","Ce compte ne possède aucun numéro.");
define("LANGMESS59","Modifier aussi les abs/rtd justifiés");
define("LANGMESS60","A");
define("LANGMESS60bis","bsent");
define("LANGMESS61","des ".INTITULEENSEIGNANT."s");
define("LANGMESS62","Parent de ");
define("LANGMESS63","aujourd\'hui");  // mettre une ' 
define("LANGBT27bis","Enregistrer abs/rtd"); //
define("LANGDEPART3bis","Accès interrompu ! ");
define("LANGDEPART4bis","L'accès à votre TRIADE est actuellement interrompu, merci de contacter votre établissement scolaire pour de plus amples informations.");
define("LANGAIDE","Aide en ligne");
define("LANGAIDE1","Indiquer les correspondances entre vos matières enregistrées dans TRIADE et les matières enseignées pour le brevet des collèges. Pour cela, effectuer un drag&drop (glisser&relacher) entre les matières de gauche à droite.");
define("LANGAIDE2","Composer votre texte pour le contenu de la convention de stage. Pour une prise en compte d'éléments tels que le nom, prénom, adresse, etc..., veuillez présiser la chaîne suivante en fonction de vos besoins :");
define("LANGBREVET1","Accéder");
define("LANGCONFIG4","Etre averti d'un message lorsque");
define("LANGCONFIG5","Nbr d'absences non justifiées d'un ".INTITULEELEVE." a dépassé ");
define("LANGCONFIG6","Nbr de retards non justifiés d'un ".INTITULEELEVE." a dépassé ");
define("LANGCONFIG7","fois");
define("LANGCONFIG8","Liste des utilisateurs avertis");

define("LANGMESS64","Personnes ayant reçu ce message");
define("LANGMESS65","Liste des règlements intérieurs");
define("LANGMESS66","Le Directeur");
define("LANGMESS67","J'ai pris connaissance des différents documents ci-dessus");
define("LANGMESS68","J'accepte le ou les règlement(s) intérieur(s)");
define("LANGMESS69","J'accepte les conditions générales d'enseignement");
define("LANGMESS70","Règlement accessible par les ".INTITULEENSEIGNANT."s");
define("LANGMESS71","Consulter Fiche d'état des règlements");
define("LANGMESS72","Imprimer Fiche d'état des règlements");
define("LANGMESS73","Liste des impayés ou paiement(s) incomplet(s)");
define("LANGMESS74","Fiche d'état des règlements");
define("LANGacce_dep2ter","<br><b>ATTENTION !  Vérifiez bien votre mode d'accès, choisissez votre compte correspondant.</b>");
//NEW NON CORRIGE

define("LANGMESS75","Retour menu principal");
define("LANGMESS76","Correspondance");
define("LANGMESS77","(devoir, contrôle, examen)");
define("LANGMESS78","Trier par ");
define("LANGMESS79","Notes visibles aux ".INTITULEELEVE."s le ");
define("LANGMESS80","vie scolaire");
define("LANGMESS81","Connexion en cours");
define("LANGMESS82","Moyenne");
define("LANGMESS83","Moyenne de ".INTITULECLASSE);
define("LANGMESS84","Max");
define("LANGMESS85","Min");
define("LANGMESS86","Aucune date trimestrielle affectée");
define("LANGMESS86bis","pour");
define("LANGMESS86ter","cette année scolaire");
define("LANGMESS87","Note des devoirs de");




define("LANGMESS88","Cahier de texte enregistré  -- Service Triade");
define("LANGMESS89","Cahier de texte en ");
define("LANGMESS90","Penser à enregistrer votre contenu avant de changer d'onglet.");
define("LANGMESS91","Consultation de la semaine");
define("LANGMESS92","Contenu du cours");
define("LANGMESS93","Fichier joint");
define("LANGMESS94","Piece Jointe");
define("LANGMESS95","Objectif du cours");
define("LANGMESS96","Devoir à faire pour le ");
define("LANGMESS97","non indiqué");
define("LANGMESS98","Devoir à faire");
define("LANGMESS99","Bloc-Notes");
define("LANGMESS100","Consultation compléte");
define("LANGMESS101","Validation");
define("LANGMESS102","Consultation");
define("LANGMESS103","Temps estimé pour ce travail ");
define("LANGMESS104","Temps de travail estimé à ");
define("LANGMESS105","Fichier ");
define("LANGMESS106","Modification ");
define("LANGMESS107","Supprimer cette fiche ");
define("LANGMESS108","Temps de travail total estimé ");
define("LANGMESS109","du"); // notion de date du xxxx au xxxx
define("LANGMESS110","au"); // notion de date du xxxx au xxxx
define("LANGMESS111","Format PDF"); 
define("LANGBT288","Consulter / Modifier"); //
define("LANGSITU1","Marié(e)"); //
define("LANGSITU2","Divorcé(e)"); //
define("LANGSITU3","Veuf"); //
define("LANGSITU4","Veuve"); //
define("LANGSITU5","Concubin"); //
define("LANGSITU6","PACS"); //
define("LANGSITU7","Célibataire"); //
define("LANGFIN002","Echéancier");//
define("LANGFIN003","Echéancier");//
define("LANGFIN004","Aucune date de configurée");//
define("LANGCONFIG","Configurer");//

define("LANGMESS112","Commentaire bulletin trimestre/semestre");
define("LANGMESS113","Choix du commentaire");
define("LANGMESS114","Commentaire brevet des collèges");
define("LANGMESS115","Visualisation du bulletin de ".INTITULECLASSE);
define("LANGMESS116","Accèder");
define("LANGMESS117","Série");
define("LANGMESS118","Passer en mode étendu");
define("LANGMESS119","Appréciations, Conseils pour progresser");
define("LANGMESS120","Points d'appui. Progrès. Efforts");
define("LANGMESS121","Ecarts par rapport aux objectifs attendu");
define("LANGMESS122","Conseils pour progresser");
define("LANGMESS123","Moyenne de la ".INTITULECLASSE);
define("LANGMESS124","Commentaire précédent");
define("LANGMESS125","Ajout dans liste"); // vérif. pas de quote (') 
define("LANGMESS126","Enregistrer le commentaire"); // vérif. pas de quote (') 
define("LANGMESS127","Revenir et cliquer sur"); // vérif. pas de quote (') 
define("LANGMESS128","Enregistrement");  // vérif. pas de quote (') 
define("LANGMESS129","Consulter");
define("LANGMESS130","Moy. Précédente");
define("LANGMESS131","Enregistrer les commentaires");
define("LANGMESS132","Patientez S.V.P.");
define("LANGMESS133","Commentaire vide");
define("LANGMESS134","commentaire non enregistré");
define("LANGMESS135","Appréciation pour le bulletin trimestriel ".INTITULECLASSE);
define("LANGMESS136","cliquez-ici");
define("LANGMESS137","Information Scolaire Complémentaire");
define("LANGMESS138","Saisir autres commentaires pour les bulletins");

//-----------------Traduction Sam le 06/06/2014
//-----------------messagerie_brouillon.php
define("LANGMESS139","Messagerie brouillon");
define("LANGMESS140","Préparer un brouillon ");
define("LANGMESS141","Accès");
define("LANGMESS142","Valider un brouillon");
define("LANGMESS143","Les messages brouillons sont visibles par tous les membres de la direction");

//------------------param.php
define("LANGMESS144","Signature du directeur");
define("LANGMESS145","Année scolaire");
define("LANGMESS156","Pays");
define("LANGMESS159","Choix du site");
define("LANGMESS160","Nouveau site");
define("LANGMESS177","Département ");
//------------------definir_trimestre.php
define("LANGMESS146","Enregistrement au format semestriel.");
define("LANGMESS147","Toutes les ".INTITULECLASSE."s");
define("LANGMESS148","Liste des périodes trimestrielles ou semestrielles ");
define("LANGMESS149","Modifier");
define("LANGMESS150","Supprimer");
define("LANGMESS157","Trimestre");
define("LANGMESS158","Classe");
//-----------------probleme_acces_2.php
define("LANGMESS151","Identifiez votre compte");
define("LANGMESS152","Veuillez d'abord identifier votre compte pour réinitialiser votre mot de passe.");
define("LANGMESS153","Demande de mot de passe");
//-----------------geston_groupe.php
define("LANGMESS154","Création de groupe");
define("LANGMESS155","Liste des groupes des ".INTITULEENSEIGNANT."s");
//-----------------gestcompte.php
define("LANGMESS161","Gestion de votre compte");
//-----------------messagerie_reception.php
define("LANGMESS162","Gestion de votre compte");
//------------------gestion_groupe.php
define("LANGBT53","Entrée"); // traduit par sam le 09/06/2014
define("LANGMESS163","Vérification des groupes");
//-------------------messagerie_suppression.php
define("LANGMESS164","Boite de suppression");
define("LANGMESS165","Archiver dans");
//-------------------messagerie_reception.php
define("LANGMESS166","Boite de reception");
//-------------------parametrage.php
define("LANGMESS167","Paramétrage de votre compte");
define("LANGMESS168","Actualités");
define("LANGMESS169","Réservation Salle / Equipement");
define("LANGMESS170","Messagerie Triade");
define("LANGMESS171","(Indiquer votre  email)");
define("LANGMESS172","(Numéro de portable)");
//-------------------messagerie_envoi.php
define("LANGMESS173","Message à un groupe ");
define("LANGMESS174","Message aux délégués :");
define("LANGMESS175","Message à un membre du personnel : ");
define("LANGMESS176","Message à un tuteur de stage : ");
//-------------------creat_admin.php
define("LANGMESS178","Civ.");
define("LANGMESS179","Indice&nbsp;salaire");
//-------------------creat_tuteur.php
define("LANGMESS180","Création d'un compte tuteur de stage");
define("LANGMESS181","Liste / Modification d'un tuteur de stage");
define("LANGMESS182","Gestion des membres Tuteur de stage");
define("LANGMESS183","Entreprise liée");
define("LANGMESS184","En qualité de ");
//--------------------creat_personnel.php
define("LANGMESS185","Gestion des membres du Personnel");
define("LANGMESS186","Création d'un compte personnel"); // "Cr&eacute;ation d'un compte personnel"
//--------------------creat_eleve.php
define("LANGMESS187","Rechercher");
define("LANGMESS188","Importer");
define("LANGMESS189","Supprimer");
define("LANGMESS190","Lv1/Spé :");
define("LANGMESS191","Lv2/Spé :");
define("LANGMESS192","Boursier");
define("LANGMESS193","Inscription au BDE");
define("LANGMESS194","Inscription à la bibliothèque");
define("LANGMESS195","Montant Bourse");
define("LANGMESS196","Indemnité Stage");
define("LANGMESS197","Code comptabilité ");
define("LANGMESS198","Adresse");
define("LANGMESS199","Téléphone");
define("LANGMESS200","Tél. Portable");
define("LANGMESS201","E-mail Etudiant");
define("LANGMESS202","E-mail universitaire");
define("LANGMESS203","Situation Familiale");
define("LANGMESS204","Copier adresse");
define("LANGMESS205","Classe antérieure");
//--------------------creat_class.php
define("LANGMESS206","Intitulé de la ".INTITULECLASSE);
define("LANGMESS207","Ecole");
//--------------------creat_matiere.php
define("LANGMESS208","Format court");
define("LANGMESS209","Format long");
define("LANGMESS210","Code matière");
//--------------------reglement.php
define("LANGMESS211","Réglement intérieur");
define("LANGMESS212","Ajouter un règlement");
define("LANGMESS213","lister le/les règlements");
define("LANGMESS214","Supprimer un règlement");
//--------------------sms.php
define("LANGMESS215","Gestion des SMS");
define("LANGMESS216","Membre");
define("LANGMESS217",ucfirst(INTITULEDIRECTION));
define("LANGMESS218",ucfirst(INTITULEENSEIGNANT));
define("LANGMESS219","Vie Scolaire");
define("LANGMESS220","Personnel");
//--------------------Codebar0.php
define("LANGMESS221","Code barre :");
//--------------------vatel_gestion_ue.php
define("LANGMESS222","Gestion des Unités d'enseignements");
define("LANGMESS223","Création d'une unité d'enseignement");
define("LANGMESS224","Lister/Modifier");
//--------------------base_de_donne_importation.php
define("LANGMESS225","Fichier Excel");
define("LANGMESS226","Fichier XML");
define("LANGMESS227","Code barre");
//--------------------edt.php
define("LANGMESS228","Suppression d'une période ");
define("LANGMESS229","Ajustement des horaires ");
define("LANGMESS230","Période visible sur l'EDT");
define("LANGMESS231","Importer image ou pdf : ");
define("LANGMESS232","(format  de l'image : jpg et moins de 2Mo )");
define("LANGMESS233","EDT de la ".INTITULECLASSE." : ");
//--------------------export.php
define("LANGMESS234","Exportation des données");
define("LANGMESS235","Informations à exporter");
define("LANGMESS236","Personnel");
define("LANGMESS237","Choix de l'extraction : ");
//--------------------export.php
define("LANGMESS238","Nom de l'".INTITULEENSEIGNANT." ");
define("LANGMESS239","Exportation au format PDF : ");
define("LANGMESS240","Exporter");
//--------------------commaudio.php
define("LANGMESS241","Sujet : ");
define("LANGMESS242","Fichier audio : ");
//--------------------consult_classe.php
define("LANGMESS243","Impression ");
define("LANGMESS365","&nbsp;Demi&nbsp;Pension&nbsp;");
define("LANGMESS366","&nbsp;Interne&nbsp;");
define("LANGMESS367","&nbsp;Externe&nbsp;");
define("LANGMESS368","&nbsp;Inconnu&nbsp;");
//--------------------resr_admin.php
define("LANGMESS244","Réserver via E.D.T.");
//--------------------carnetnote.php
//------------modif nom de l'enseignant---LANGMESS238
//--------------------publipostage.php
define("LANGMESS245","Type membre : ");
define("LANGMESS246","Parents");
define("LANGMESS247","Etudiants");
define("LANGMESS248","Type adresse :");
define("LANGMESS249","Tuteur");
define("LANGMESS327","Publipostage");
define("LANGMESS328","Afficher la civilit&eacute; des &eacute;tudiants : ");
define("LANGMESS329","Afficher matricule : ");
define("LANGMESS330","Afficher Classe : ");
define("LANGMESS331","Afficher Adresse : ");
//--------------------ficheeleve3.php
define("LANGMESS250","Listing Classe");
define("LANGMESS251","Envoyer un SMS");
define("LANGMESS252","Modifier Fiche");
define("LANGMESS253","Affecter &agrave; un stage");
define("LANGMESS254","Bloquer ce compte");
define("LANGMESS255","Débloquer ce compte");
define("LANGMESS259","Renseignements");
define("LANGMESS260","Carnet de notes");
define("LANGMESS261","Vie Scolaire");
define("LANGMESS262","Disciplines");
define("LANGMESS263","Opérations effectuées");
define("LANGMESS264","Info. Tuteur 1");
define("LANGMESS265","Info. Tuteur 2");
define("LANGMESS266","Info. Etudiant");
define("LANGMESS267","Archives");
define("LANGMESS268","Info. médicales");
define("LANGMESS269","info. compl.");
define("LANGMESS270","Nom :");
define("LANGMESS271","Prénom :");
define("LANGMESS272","Classe :");
define("LANGMESS273","Date&nbsp;de&nbsp;nais.&nbsp;:");
define("LANGMESS274","Nationalité&nbsp;:");
define("LANGMESS275","Lieu&nbsp;naissance&nbsp;:");
define("LANGMESS276","Boursier :");
define("LANGMESS277","Numéro&nbsp;Etudiant&nbsp;:");
define("LANGMESS278","Lv1/Spé :");
define("LANGMESS279","Lv2/Spé :");
define("LANGMESS280","Option :");
define("LANGMESS281","Régime :");
define("LANGMESS282","N°&nbsp;Rangement&nbsp;:");
define("LANGMESS283","Contact&nbsp;:");
define("LANGMESS284","Situation&nbsp;familiale&nbsp;:");
define("LANGMESS285","Adresse&nbsp;:");
define("LANGMESS287","Code&nbsp;Postal&nbsp;:");
define("LANGMESS288","Ville&nbsp;:");
define("LANGMESS289","Email&nbsp;:");
define("LANGMESS290","Téléphone&nbsp;:");
define("LANGMESS291","Profession&nbsp;:");
define("LANGMESS292","Tél.&nbsp;Prof.&nbsp;:");
define("LANGMESS293","Sexe&nbsp;:");
define("LANGMESS294","Classe&nbsp;ant.&nbsp;:");
define("LANGMESS295","Année&nbsp;Scolaire");
define("LANGMESS296","Trim&nbsp;/&nbsp;Sem");
define("LANGMESS297","Bulletin");
define("LANGMESS298","Effectué&nbsp;le");
define("LANGMESS308","Permission non accordées");
define("LANGMESS309","Ajouter une information");
define("LANGMESS310","Entretien individuel");
define("LANGMESS311","Planifier abs/rtd");
define("LANGMESS312","Modifier abs/rtd");
define("LANGMESS313","Supprimer abs/rtd");
define("LANGMESS320","$email_eleve / $emailpro_eleve");
define("LANGMESS321","$tel_eleve / $tel_fixe_eleve");

//--------------------elevesansclasse.php
define("LANGMESS256","Save");
//--------------------consult_classe.php
define("LANGMESS257","All ".INTITULECLASSE."s.");
//--------------------ficheeleve.php
define("LANGMESS258","Search");
//--------------------newsactualite.php
define("LANGMESS299","    Titre : ");
define("LANGMESS300","Votre TRIADE n'est pas configuré en accès Internet, veuillez consulter votre compte administrateur Triade pour valider l'option de la connexion Internet.");
define("LANGMESS365","Actualités  de la 1er page");
//--------------------actualiteetablissement.php
//--------------------newsdefil.php
//--------------------commaudio.php // Bouton Parcourir
//--------------------commvideo.php
define("LANGMESS301","Lien de la video : ");
define("LANGMESS302","ou Lien Youtube : ");
//--------------------emmargement.php
define("LANGMESS303","Gestion des émargements ");
define("LANGMESS304","Au niveau de la ".INTITULECLASSE);
define("LANGMESS305","Emargement vierge");
define("LANGMESS306","Emargement vierge d'examen");
define("LANGMESS307","Au niveau du groupe");
define("LANGMESS314","Emargement du jour ");
define("LANGMESS315","Emargement&nbsp;du&nbsp;");
define("LANGMESS316","Pour la ".INTITULECLASSE." : ");
define("LANGMESS317",ucfirst(INTITULEENSEIGNANT)." : ");
define("LANGMESS318","Tous les ".INTITULEENSEIGNANT."s : ");
define("LANGMESS319","Hauteur des cellules des ".INTITULEELEVE."s");
//--------------------trombinoscope0.php
define("LANGMESS322","Imprimer au format PDF des ".INTITULEELEVE);
define("LANGMESS323","Importer les photos au format ZIP");
//--------------------chgmentclas.php
define("LANGMESS324",": notes, absences, retards, dispences, sanctions, retenues, Brevets, Commentaires bulletin de l'".INTITULEELEVE.", droits de scolarité, plan de ".INTITULECLASSE.", Brevets, Affectation stage");
//------LANGASS10-- Variable pour suppression
//--------------------certificat.php
define("LANGMESS325","Paramétrage  manuel : ");
define("LANGMESS326","Paramétrage  import : ");
//define("LANGMESS331","Publipostage");
//--------------------visa_direction.php
define("LANGMESS332","Type du bulletin : ");
define("LANGMESS333","Valider");
define("LANGMESS334","Annuel"); /// voir si posible de mettre une variable
///////////////////////
//--------------------list_classe.php----- Voir comment changer le bouton Modifier
//--------------------list_matiere.php---- Voir comment changer le bouton Modifier
//--------------------listepreinscription.php
define("LANGMESS335","Liste des pré-inscriptions");
//--------------------reglement_ajout.php
define("LANGMESS336","Règlement intérieur");
define("LANGMESS337","règlement");
define("LANGMESS338","la ou les ".INTITULECLASSE."(s)");
define("LANGMESS339","la ou les ".INTITULECLASSE."(s)");
//--------------------affectation_visu.php
define("LANGMESS340","Année/Trimestre/Semestre");
define("LANGMESS341","Toute l'année");
define("LANGMESS342","Trimestre 1 / Semestre 1");
define("LANGMESS343","Trimestre 2 / Semestre 2");
define("LANGMESS344","Trimestre 3");
//--------------------affectation_modif_key.php
//----Modidifier le bouton suivant par next
//--------------------reglement_ajout.php
//--------------------reglement_liste.php
// comment modifier le lien Reglement interieur
//----------------/reglement_supp.php
define("LANGMESS345","Visualiser");
//-----------------vatel_list_ue.php
define("LANGMESS346","Gestion des Unités d'Enseignements");
define("LANGMESS347","Filtre : ");
define("LANGMESS348","Modifier");
define("LANGMESS349","Supprimer");
define("LANGMESS350","Nom UE");
define("LANGMESS351","Sem.");
define("LANGMESS352","Création d'une UE");

//----------------creat_groupe.php
define("LANGMESS353","Fichier excel");
define("LANGMESS354","Contenu du fichier excel");
//----------------visa_direction2.php
define("LANGMESS355","Commentaire des ".INTITULEENSEIGNANT."s");
define("LANGMESS356","Visa direction");
//----------------imprimer_tableaupp.php
define("LANGMESS357","Impression tableau de notes trimestriel ou semestriel");
define("LANGMESS358","Afficher le classement ");
define("LANGMESS359","Afficher les colonnes vides ");
define("LANGMESS360","Regroupement par module ");
define("LANGMESS361","Afficher les matières ");
define("LANGMESS362","Tableau des différentes moyennes au format excel");
define("LANGMESS374","Jusqu'au :");
define("LANGMESS375","Fichier Excel");
//------------------affectation_creation_key.php
//------------------affectation_visu2.php
define("LANGMESS363","Visu");
define("LANGMESS364","Unité Ens.");
//------------------entretien.php
define("LANGMESS369","Journal d'entretiens individuels");
define("LANGMESS370","Journal d'entretiens groupés ");
define("LANGMESS371","Tableau récapitulatif");
define("LANGMESS372","&nbsp;".ucfirst(INTITULEENSEIGNANT)."&nbsp;");
define("LANGMESS373","&nbsp;Nombre&nbsp;d'heures&nbsp;");
//------------------base_de_donne_key.php
define("LANGMESS376","Pour modifier / changer votre code d'accès, merci de consulter votre compte ");
define("LANGMESS377","administrateur Triade");
define("LANGMESS378","puis le module \"code d'accès\"");
//------------------chgmentClas0.php
// année = Year
define("LANGMESS379","pas d'année");
define("LANGMESS380","Choix de la ".INTITULECLASSE."");
//------------------chgmentClas00.php
// année et pas d'année 
define("LANGMESS381","Choix des ".INTITULECLASSE." :");
define("LANGMESS383","Changement de ".INTITULECLASSE." pour les ".INTITULEELEVE."s en ");
define("LANGMESS384","Passage pour l'année scolaire");
define("LANGMESS385","Sans ".INTITULECLASSE."");
//------------------bro3uillon_reception.php
define("LANGMESS382","Liste des messages brouillons");
//------------------imprimer_trimestre.php
define("LANGMESS386","Bulletin&nbsp;personnalisé");
define("LANGMESS387","Bulletin définit pour les ".INTITULEENSEIGNANT."s (et parents  prochainement)");
define("LANGMESS388","Visible pour la ".INTITULECLASSE."");
define("LANGMESS389","Autoriser l'accès aux bulletins pour les ".INTITULEENSEIGNANT."s");




// --- NEW ERIC --- // 
define("LANGMESST390","Merci de renseigner les informations nécessaires à Triade pour le site numéro 1 !!<br>Merci de confirmer en validant ou revalidant le formulaire suivant.");
define("LANGMESST391","Supprimer site");
define("LANGMESST392","Carnet de suivi");
define("LANGMESST393","COMPTE BLOQUE");
define("LANGMESST394","COMPTE EN PERIODE PROBATOIRE");
define("LANGMESST395","Supprimer la période probatoire");
define("LANGMESST396","Mise en période probatoire");
define("LANGMESST397","Saisie&nbsp;par");
define("LANGMESST398","Enregistrer cette liste");
define("LANGMESST399","Effectuer une recherche complexe");
define("LANGMESST700","Supprimer message en cours");
define("LANGMESST701","Actualités  de la 1er page");
define("LANGMESST702","Titre de la vidéo");
define("LANGMESST703","Copier/coller le lien ");
define("LANGMESST704","Indiquer le destinateur du message à transmettre.");
define("LANGMESST705","Message non envoyé ! \\n \\n Vous n'avez pas l'autorisation d'envoyer un message à cette personne.\\n\\n L'Equipe TRIADE. ");
define("LANGTMESS400","Votre demande a bien été pris en compte,");
define("LANGTMESS401","Veuillez consulter votre adresse email");
define("LANGTMESS402","Aucun compte pour cet email !!");
define("LANGTMESS403","merci de contacter votre administrateur en cliquant ");
define("LANGTMESS404","sur ce lien ");
define("LANGTMESS405","Contacter l'administrateur TRIADE ");
define("LANGTMESS406","Vérifier");
define("LANGTMESS407","Vérification / Check groupes");
define("LANGTMESS408","Email non valide !!");
define("LANGTMESS409","Merci d'indiquer un email valide.");
define("LANGTMESS410","Les emails <b>hotmail</b> ne sont pas reconnues par nos serveurs.");
define("LANGTMESS411","Merci d'indiquer une autre adresse email.");
define("LANGTMESS412","Nouveau Répertoire");
define("LANGTMESS413","Message déjà imprimé");
define("LANGTMESS414","Pièce jointe");
define("LANGTMESS415","Archiver dans");
define("LANGTMESS416","Boite de ");
define("LANGTMESS417","Boite de Réception");
define("LANGTMESS418","Mode Classique");
define("LANGTMESS419","Messages envoyées ");
define("LANGTMESS420","Vos répertoires ");
define("LANGTMESS421","via le mail ");
define("LANGTMESS422","via SMS ");
define("LANGTMESS423","via RSS ");
define("LANGTMESS424","Module lors de votre connexion");
define("LANGTMESS425","Module d'absenteisme");
define("LANGTMESS426","Liste d'une UE ( Modif / Suppr )");
define("LANGTMESS427","PDF EDT Enregistré");
define("LANGTMESS428","L'Equipe Triade");
define("LANGTMESS429","Image EDT Enregistrée");
define("LANGTMESS430","EDT Supprimé");
define("LANGTMESS431","Nom de structure déjà utilisé");
define("LANGTMESS432","Exportation format");
define("LANGTMESS433","&nbsp;Total&nbsp;");
define("LANGTMESS434","colonnes");
define("LANGTMESS435","Tuteur de stage");
define("LANGTMESS436","Afficher Adresse");
define("LANGTMESS437","Tous les parents");
define("LANGTMESS438","Tous les ");
define("LANGTMESS439","Lister / Modification");
define("LANGTMESS440","ajouter");
define("LANGTMESS441","Rangement / Info.");
define("LANGTMESS442","par mois");
define("LANGTMESS443","Nb mois");
define("LANGTMESS444","Code comptabilité");
define("LANGTMESS445","Universitaire");
define("LANGTMESS446","Editer le RIB");
define("LANGTMESS447","Donnée déjà enregistrée");
define("LANGTMESS448","Site rattaché");
define("LANGTMESS449","Définition compléte");
define("LANGCIV0","M.");
define("LANGCIV1","Mme");
define("LANGCIV2","Mlle");
define("LANGCIV3","Ms");
define("LANGCIV4","Mr");
define("LANGCIV5","Mrs");
define("LANGCIV6","M. ou Mme");
define("LANGCIV7","Sr");
define("LANGCIV8","Général");
define("LANGCIV9","Colonel");
define("LANGCIV10","Lieutenant-Colonel");
define("LANGCIV11","Commandant");
define("LANGCIV12","Capitaine");
define("LANGCIV13","Lieutenant");
define("LANGCIV14","Sous-Lieutenant");
define("LANGCIV15","Aspirant");
define("LANGCIV16","Major");
define("LANGCIV17","Adjudant-Chef");
define("LANGCIV18","Adjudant");
define("LANGCIV19","Sergent-Chef");
define("LANGCIV20","Sergent");
define("LANGCIV21","Caporal-Chef");
define("LANGCIV22","Caporal");
define("LANGCIV23","Aviateur");
define("LANGCIV24","Dr");

define("LANGMESS391","Mode Classique");
define("LANGMESS392","Liste des destinataires");
define("LANGMESS393","Effacer liste"); // lg 262
define("LANGMESS394","Sélectionnez un fichier");
define("LANGMESS395","Liste des membres de la direction");
define("LANGMESS396","Visualiser / Modifier");
define("LANGMESS397","Liste de la Vie Scolaire");
define("LANGMESS398","Désactiver compte");
define("LANGMESS399","Activer compte");
define("LANGMESS400","Permission");
define("LANGMESS401","Liste des comptes personnels ");
define("LANGMESS403","Liste Tuteur de stage");
define("LANGMESS404","Liste / Modifier");
define("LANGMESS405","M.");
define("LANGMESS406","Mme");
//--------------------list_classe.php
//--------------------modif_classe.php
define("LANGMESS407","Modification d'une ".INTITULECLASSE."");
define("LANGMESS408","Activer la ".INTITULECLASSE."");
define("LANGMESS409","Désactiver la ".INTITULECLASSE."");
define("LANGMESS410","Définition complète");
define("LANGMESS411","Site rattaché");
//--------------------affectation_creation.php
//-------------------publipostage.php
define("LANGMESS412","Type de vignette");
define("LANGMESS413","Type de membre");
//-------------------list_matiere.php
//-------------------modif_matiere.php
define("LANGMESS414","Type de membre");
define("LANGMESS415","Code matière");
define("LANGMESS416","Nom de la sous-matière");
define("LANGMESS417","Supprimer sous matière");
define("LANGMESS418","Désactiver matière");
define("LANGMESS419","Activer matière");
//-------------------triadev1/circulaire_liste.php
define("LANGMESS420","Référence");
//-------------------visu_retard_parent.php
//-------------------messagerie_envoi.php
define("LANGMESS421","Vous n'avez pas l'autorisation d'envoyer un message à cette personne.");
//-------------------information.php
define("LANGMESS422","Informations scolaires");
//-------------------parametrage.php
define("LANGMESS423","Module lors de votre connexion ");
define("LANGMESS424","Actualités");
define("LANGMESS425","Module d'absenteisme");
//-------------------retardprof.php
define("LANGMESS426","Indiquez des ".INTITULEELEVE."s en retard ou absent");
//-------------------retardprof2.php
define("LANGMESS427","Indiquer heure d'abs/rtd");
define("LANGMESS428","En ");
define("LANGMESS429","Horaire : ");



define("LANGTMESS450","Traduction autre langue");
define("LANGTMESS451","Actuellement le fichier import sert de référence à la création du certificat.");
define("LANGTMESS452","Récupérer");
define("LANGTMESS453","Certificat numéro :");
define("LANGTMESS454","Ajouter une inscription :");
define("LANGTMESS455","Nouveau");
define("LANGTOUS","Tous");
define("LANGTMESS456","En attente");
define("LANGTMESS457","Accepté");
define("LANGTMESS458","Réfusé");
define("LANGTMESS459","Décision");
define("LANGTMESS460","Transferer liste en ".INTITULECLASSE."");
define("LANGTMESS461","Destruction fiche(s)");
define("LANGTMESS462","Attention !, le règlement doit être au format pdf et ne pas dépasser deux méga octé.");
define("LANGTMESS463","Cette option permet aux ".INTITULEENSEIGNANT."s, de valider le réglement au moment de leur premiere connexion.");
define("LANGTMESS464",ucfirst(INTITULEELEVE)."(s) au total.");
define("LANGTMESS465","Commentaire pour le");
define("LANGTMESS466","Afficher les sous-matières");
define("LANGTMESS467","Prise en compte note examen");
define("LANGTMESS468","Prise en compte coef à zéro");
define("LANGTMESS469","Si le coefficient est à zéro, les points supérieurs à 10 seront pris en compte.");
define("LANGTMESS470","Spécif");
define("LANGTMESS471","Etude de cas");
define("LANGTMESS472","Visu : Visualisation dans le bulletin");
define("LANGTMESS473","pour l'année :");
define("LANGTMESS474","changer");
define("LANGTMESS475","Fichier Taille Max");
define("LANGTMESS476","Liste / Modifier un compte personnel");
define("LANGTMESS477","Liste / Modifier un tuteur de stage");
define("LANGTMESS478","Via code barre");
define("LANGTMESS479","Valider les présents");
define("LANGTMESS480","Visa direction");
define("LANGTMESS481","Commentaires pour les ".INTITULEELEVES);


define("LANGTMESS482","ACTUALITES - TRIADE");
define("LANGTMESS483","non disponible");
define("LANGTMESS484","Vos répertoires");
define("LANGTMESS485","Messages aux délégués");
define("LANGTMESS486","Modifier des circulaires");


define("LANGMESS430","L'année complète");
define("LANGMESS431","Avec notes partiel Vatel ");
define("LANGMESS432","Type du bulletin");
define("LANGMESS433","Enregistrement par code barre");
define("LANGMESS434","Valider les présents");
define("LANGMESS435","Courrier");
define("LANGMESS436","Relevés sans abs, ni rtd");
define("LANGMESS437","Listing des absences");
define("LANGMESS438","Absences par semaine");
define("LANGMESS439","Imprimer absences / retards");
define("LANGMESS440","Liste des présents");
define("LANGMESS441","Gestion abs/rtd via sconet");
define("LANGMESS442","Statistiques Abs / Rtd ");
define("LANGMESS443","Gestion des absences et retards d'un ".INTITULEELEVE);
define("LANGMESS444","Planifier&nbsp;");
define("LANGMESS445","&nbsp;Consulter&nbsp;/&nbsp;Modifier&nbsp;");
define("LANGMESS446","&nbsp;Supprimer&nbsp;");
define("LANGMESS447","Accéder");
define("LANGMESS448","&nbsp;Convertir&nbsp;abs.&nbsp;");
define("LANGMESS449","Configuration");
define("LANGMESS450","Gestion alertes");
define("LANGMESS451","Configuration créneau horaire ");
define("LANGMESS452","Configuration  SMS ");
define("LANGMESS453","Créditer des SMS");

define("LANGTMESS487","Avec notes vie scolaire");
define("LANGTMESS488","Rattrapage non validés");

define("LANGTRONBI30","Visualisation Trombinoscope du personnel");
define("LANGTRONBI20","Modifier Trombinoscope du personnel");

define("LANGSEXEF","F");
define("LANGSEXEH","H");
define("LANGHOM","Homme");
define("LANGFEM","Femme");

define("LANGTMESS489","Dupliquer l'EDT");
define("LANGTMESS490","Dupliquer l'EDT d'une ".INTITULECLASSE." vers une autre");
define("LANGTMESS491","Période à copier");
define("LANGTMESS492","Import du personnel de direction : ");
define("LANGTMESS493","Import des comptes du personnel : ");
define("LANGTMESS494","Import des entreprises : ");
define("LANGTMESS495","Import Spécif. IPAC : ");
define("LANGTMESS496","Import des matières : ");
define("LANGTMESS497","Module d'importation de fichier : ");
define("LANGTMESS498","Module d'importation de fichier Excel ");
define("LANGTMESS499","Le fichier excel à transmettre DOIT contenir 4 champs");
define("LANGTMESS500","Exemple fichier xls");
define("LANGTMESS501","Nombre de matière ajoutée : ");
define("LANGTMESS502","Dates Trimestrielles");
define("LANGTMESS503","Votre accès est actuellement désactivé.");



define("LANGTMESS504","Envoyer mot de passe par mail");
define("TITREACC1","parents");      // Info au niveau de la page d'accueil "Accès Parents"  
define("TITREACC2",ucfirst(INTITULEENSEIGNANT)."s");  // Info au niveau de la page d'accueil "Accès Enseignants"  
define("TITREACC3","Vie scolaire"); // Info au niveau de la page d'accueil "Accès Vie scolaire"  
define("TITREACC4","Tuteur Stage"); // Info au niveau de la page d'accueil "Accès Tuteur Stage"  
define("TITREACC5","Personnels");   // Info au niveau de la page d'accueil "Accès Personnels"  
define("LANGTMESS505","Classe antérieures");
define("LANGTMESS506","Spécialisation");

define("LANGTMESS507","Sortie supplément au titre");
define("LANGTMESS508","Configuration supplément au titre");
define("LANGTMESS509","Gestion d'examen");
define("LANGTMESS510","Choix du document :");
define("LANGTMESS511","Récupérer le fichier ZIP Suppléments Titre");
define("LANGTMESS512","Niveau scolaire");
define("LANGTMESS513","Publipostage des sociétés ");
define("LANGTMESS514","Import des entreprises");
define("LANGTMESS515","Indemnité de stage");
define("LANGTMESS516","Suivi des demandes de convention");
define("LANGTMESS517","Gestion supplément au titre");
define("LANGTMESS518","Libellé :");
define("LANGTMESS519","Fichier");


define("LANGTMESS520","Nom du stage");
define("LANGTMESS521","En Entreprise le : ");
define("LANGTMESS522","Pays");
define("LANGTMESS523","Groupe hôtelier");
define("LANGTMESS524","Nombre d'étoiles");
define("LANGTMESS525","Nombre de chambres");
define("LANGTMESS526","Site web");
define("LANGTMESS527","Affectation de plusieurs étudiants à un stage");
define("LANGSTAGE100","Nom");
define("LANGSTAGE101","N° Stage");
define("LANGSTAGE102","Entreprise");
define("LANGSTAGE103","Service");
define("LANGSTAGE104","Indemnité");
define("LANGSTAGE105","Logé");
define("LANGSTAGE106","Nourri");
define("LANGSTAGE107","Valider");
define("LANGSTAGE108","Stage personnalisé");
define("LANGSTAGE109","Pays");
define("LANGSTAGE110","Tuteur de stage");
define("LANGSTAGE111","Langue parlé durant le stage");
define("LANGSTAGE112","Intitulé du service");
define("LANGSTAGE113","Indemnités de stage");
define("LANGSTAGE114","Horaires journaliers");
define("LANGSTAGE115","Les conventions de stage");
define("LANGSTAGE116","Sortie des conventions groupées");

define("LANGTMESS528","Langue de la ".INTITULECLASSE."");
define("LANGTMESS529","Retour ".INTITULECLASSE."");
define("LANGTMESS530","Récuperation des conventions de stage");


define("LANGVATEL1","D&eacute;connexion");
define("LANGVATEL2","Me connecter");
define("LANGVATEL3","Mot de passe oubli&eacute;");
define("LANGVATEL4","Ecris ton email");
define("LANGVATEL5","Ecris ton mot de passe");
define("LANGVATEL6","Semestre");
define("LANGVATEL7","Abs/Rtd/Sanction");
define("LANGVATEL8","Absences / Retards / Sanctions");
define("LANGVATEL9","Absences");
define("LANGVATEL10","Retards");
define("LANGVATEL11","Sanctions");
define("LANGVATEL12","Description des faits");
define("LANGVATEL13","<");
define("LANGVATEL14",">");
define("LANGVATEL15","Mois");
define("LANGVATEL16","R&eacute;initialiser votre mot de passe");
define("LANGVATEL17","Mot de passe oubli&eacute; ?");

define("LANGVATEL18","Acc&egrave;s ".ucfirst(INTITULEELEVE));
define("LANGVATEL19","Acc&egrave;s ".ucfirst(INTITULEENSEIGNANT));
define("LANGVATEL20","Acc&egrave;s ".ucfirst(INTITULEDIRECTION));

define("LANGVATEL21","Ajouter");
define("LANGVATEL22","Modifier");
define("LANGVATEL23","Supprimer");
define("LANGVATEL24","Visualiser");
define("LANGVATEL25","Quoi de neuf ?");
define("LANGVATEL26","Notes");
define("LANGVATEL27","Statistiques de ce devoir");
define("LANGVATEL28","IMPOSSIBLE");
define("LANGVATEL29","Semestre déjà passé.");
define("LANGVATEL30","Ajouter ".INTITULEELEVE);
define("LANGVATEL31","Ajouter une note à un ".INTITULEELEVE." pour ce devoir.");
define("LANGVATEL32","Retour sur la liste des devoirs");
define("LANGVATEL33","Emploi du temps");
define("LANGVATEL34","Absentéisme");
define("LANGVATEL35","absent(s) signé(s)");
define("LANGVATEL36","Calendrier");
define("LANGVATEL37","Problème d'enregistrement");
define("LANGVATEL38","Indiquer la date");


define("LANGVATEL39","Accueil");
define("LANGVATEL40","Choix de l'".INTITULEENSEIGNANT."");
define("LANGVATEL41","pour l'".INTITULEENSEIGNANT."");
define("LANGVATEL42",ucfirst(INTITULEENSEIGNANT)." affecté à ce devoir");
define("LANGVATEL43","Absences ou retards en ".INTITULECLASSE." de");
define("LANGVATEL44","Autres absences");
define("LANGVATEL45","Autres absences pour la même ".INTITULECLASSE."");
define("LANGVATEL46","Signalé le ");
define("LANGVATEL47","Gestion Absences / Retards");
define("LANGVATEL48","Avertir par messagerie ");
define("LANGVATEL49","Mise à jour des tables");
define("LANGVATEL50","Impossible de supprimer cette ".INTITULECLASSE."");
define("LANGVATEL51","Classe non supprimable");
define("LANGVATEL52","Classe affectée");
define("LANGVATEL53","Supprimer cette ".INTITULECLASSE."");
define("LANGVATEL54","Supprimer cette matière");
define("LANGVATEL55","Impossible de supprimer cette matière");
define("LANGVATEL56","Matière affectée à une ".INTITULECLASSE."");
define("LANGVATEL57","Si pas de prénom, indiquer 'inconnu' ");
define("LANGVATEL58","Création d'un compte administratif");

define("LANGVATEL59","Liste des Tuteurs de stage");
define("LANGVATEL60","Liste des ".INTITULEENSEIGNANT."s");
define("LANGVATEL61","Liste du personnel administratif");
define("LANGVATEL62","Liste des membres de la vie scolaire");
define("LANGVATEL63","Règlement intérieur");
define("LANGVATEL64","Classes");
define("LANGVATEL65","Personnel administratif");
define("LANGVATEL66","Tuteur de stage");
define("LANGVATEL67","Règlement interieur non enregistré");
define("LANGVATEL68","Le fichier doit être au format pdf et inférieur à 2Mo");
define("LANGVATEL69","Menu");
define("LANGVATEL70","Accès au PDF de la ".INTITULECLASSE."");
define("LANGVATEL71","Accès au PDF du régime");
define("LANGVATEL72","Imprimer au format PDF");
define("LANGVATEL73","Modifier la photo de ");
define("LANGVATEL74","Groupes");
define("LANGVATEL75","Création d'un groupe");
define("LANGVATEL76","Voir cette liste");
define("LANGVATEL77","aucun étudiant");
define("LANGVATEL78","Modifier ce groupe");
define("LANGVATEL79","Gestion des groupes");
define("LANGVATEL80","Groupe NON supprimé");
define("LANGVATEL81","Groupe supprimé");
define("LANGVATEL82","Le groupe est actuellement affecté.\\n\\n Impossible de le supprimer.\\n\\n Modifier l\\'affectation avant de supprimer ce groupe.");
define("LANGVATEL83","Groupe déjà créé.");


define("LANGVATEL84","Paramétrage Bulletin");
define("LANGVATEL85","Paramétrage Ecole");
define("LANGVATEL86","Mise en place des affectations");
define("LANGVATEL87","Modification des affectations");
define("LANGVATEL88","Suppression des affectations");
define("LANGVATEL89","Unité d'enseignement");
define("LANGVATEL90","Paramétrage des absences");
define("LANGVATEL91","Paramétrage des certificats de scolarité");
define("LANGVATEL92","Paramétrage du supplément");
define("LANGVATEL93","Indiquer le jour et le mois du début de votre année scolaire : ");
define("LANGVATEL94","Indiquer le jour et le mois de la fin de votre année scolaire : ");
define("LANGVATEL95","Erreur de saisie sur vos jours ou mois indiqués");
define("LANGVATEL96","Indiquer l'ann&eacute;e scolaire");
define("LANGVATEL97","IMPORTANT, LA CREATION D'AFFECTATION SUPPRIME TOUTES LES INFORMATIONS DE NOTATION DE LA NOUVELLE CLASSE CONCERNEE !!");
define("LANGVATEL98","Copier Affectation");
define("LANGVATEL99","ERREUR DE COPIE");
define("LANGVATEL100","de l'année scolaire");
define("LANGVATEL101","IMPORTANT, LA COPIE D'AFFECTATION SUPPRIME TOUTES LES INFORMATIONS DE NOTATION DE LA NOUVELLE CLASSE CONCERNEE !!");
define("LANGVATEL102","Copier l'affectation de la ".INTITULECLASSE." ");
define("LANGVATEL103","Etude de cas");
define("LANGVATEL104","Supprimer les notes scolaires de cette ".INTITULECLASSE.".");
define("LANGVATEL105","* Visu. : Visualiser au sein du bulletin / ** Nombre d'heure annuelle / *** Visu. : Visualiser au sein du bulletin AFTEC BTS BLANC");
define("LANGVATEL106","Indiquer un ".INTITULEENSEIGNANT."");
define("LANGVATEL107","Indiquer le coef de la matière");
define("LANGVATEL108","Indiquer une valeur Numérique");
define("LANGVATEL109","Déplacer la ligne en effectuant un drag&drop");
define("LANGVATEL110","cliquer/deplacer");
define("LANGVATEL111","sur le N° correspondant");
define("LANGVATEL112","Copier unité enseignement");
define("LANGVATEL113","Liste des unités d'enseignements");
define("LANGVATEL114","ATTENTION !! METTRE A JOUR LES AFFECTATIONS DE LA CLASSE ");
define("LANGVATEL115"," SUR LES DONNEES UNITE D'ENSEIGNEMENT ");
define("LANGVATEL116"," au sein du bulletin de zéro à n ");
define("LANGVATEL117","Valider la suppression");
define("LANGVATEL118","Etes vous sur de vouloir supprimer l'unité d'enseignement suivante ?");
define("LANGVATEL119","Suppresion effectuée");
define("LANGVATEL120","Config. créneaux horaires");
define("LANGVATEL121","Config. des motifs");
define("LANGVATEL122","Nom du créneau");
define("LANGVATEL123","Heure de départ");
define("LANGVATEL124","Heure de fin");
define("LANGVATEL125","Intitulé du créneau");
define("LANGVATEL126","Enregistrer les créneaux horaires");
define("LANGVATEL127","Créneaux par défaut");
define("LANGVATEL128","Certificat numéro");
define("LANGVATEL129","Paramétrage certificats");
define("LANGVATEL130","Importer un certificat");
define("LANGVATEL131","Erreur d'enregistrement");
define("LANGVATEL132","Certificat en cours");
define("LANGVATEL133","Configuration des mots clefs");

define("LANGVATEL134","Erreur d'enregistrement");
define("LANGVATEL135","Erreur : Fichier non reconnu ");
define("LANGVATEL136","Erreur : Fichier suppérieur à 8 MO");
define("LANGVATEL137","Fichier NON enregistré");
define("LANGVATEL138","Editions / Listes");
define("LANGVATEL139","Editions par ".INTITULECLASSE."");
define("LANGVATEL140","Liste des étudiants");
define("LANGVATEL150","Tableau de bord de toutes les ".INTITULECLASSE."s.");
define("LANGVATEL151"," ".ucfirst(INTITULEELEVE)."(s) au total. Année Scolaire : ");
define("LANGVATEL152"," ".ucfirst(INTITULEELEVE)."(s) au total ");
define("LANGVATEL153","Liste d'émargements");
define("LANGVATEL154","Aucun cours définis sur l'emploi du temps.");
define("LANGVATEL155","Horaire début");
define("LANGVATEL156","Horaire fin");
define("LANGVATEL157","Intitulé du cours");
define("LANGVATEL158","Liste des ".INTITULEENSEIGNANT."s");
define("LANGVATEL159","Liste des matières");
define("LANGVATEL160","Editions des certificats de scolarité");
define("LANGVATEL161","Documents de certificats de scolarité");
define("LANGVATEL162","Récupération des certificats au format ZIP");
define("LANGVATEL163","Liste des entretiens");
define("LANGVATEL164","Edition étiquettes Etudiants");
define("LANGVATEL165","Edition étiquettes Parents");
define("LANGVATEL166","Récupération du document Publipostage");
define("LANGVATEL167","Import / Export");
define("LANGVATEL168","Importer des étudiants");
define("LANGVATEL169","Importer des ".INTITULEENSEIGNANT."s");
define("LANGVATEL170","Importer du personnel direction");
define("LANGVATEL171","Importer des entreprises");
define("LANGVATEL172","Exporter des étudiants");
define("LANGVATEL173","Exporter des ".INTITULEENSEIGNANT."s");
define("LANGVATEL174","Exporter du personnel direction");

define("LANGVATEL175","Adresse ".INTITULEELEVE);
define("LANGVATEL176","Commune ".INTITULEELEVE."");
define("LANGVATEL177","CCP ".INTITULEELEVE."");
define("LANGVATEL178","Tél. fixe ".INTITULEELEVE."");
define("LANGVATEL179","Boursier");
define("LANGVATEL180","Email Universitaire");
define("LANGVATEL181","Sexe ".INTITULEELEVE."");
define("LANGVATEL182","Mot de passe tuteur 2");
define("LANGVATEL183","Régime possible");
define("LANGVATEL184","Civilité possible");
define("LANGVATEL185","Le fichier à transmettre DOIT contenir 47 champs");
define("LANGVATEL186","Exemple fichier xls");
define("LANGVATEL187","Prendre la première ligne du fichier ");
define("LANGVATEL188","Effectuer une mise à jour ");
define("LANGVATEL189","Prendre en compte les champs vides du fichier");
define("LANGVATEL190","Affecter un nouveau mot de passe pour les ".INTITULEELEVE."s déjà inscrits");
define("LANGVATEL191","Pas d'archivage possible");
define("LANGVATEL192","Attention la suppression des l'".INTITULEELEVE."s, supprimera toutes les archives !!");
define("LANGVATEL193","Import pour l'année scolaire suivante : ");
define("LANGVATEL194","ERREUR CLASSE NON CREEE -- Service Triade");
define("LANGVATEL195","Le fichier à transmettre DOIT contenir 9 champs");
define("LANGVATEL196","ERREUR sur le mot de passe de la personne");
define("LANGVATEL197","Ajouter d'autres colonnes");
define("LANGVATEL198","nbr de colonne(s) supplémentaire(s)");
define("LANGVATEL199","Indiquer les données à exporter");
define("LANGVATEL200","Sauvegarder la structure");
define("LANGVATEL201","Si vous souhaitez sauvegarder la structure de l'exporation, récupérez d'abord votre fichier excel, puis cliquez sur le bouton \"Sauvegarder la structure\"");
define("LANGVATEL202","Nom de la structure");
define("LANGVATEL203","Récupération de l'exportation");
define("LANGVATEL204","Indiquer l'ordre des colonnes dans votre fichier excel");
define("LANGVATEL205","Bulletin scolaire");

define("LANGVATEL206","Bulletin / Supplément au diplôme");
define("LANGVATEL207","Appréciations de la direction");
define("LANGVATEL208","Appréciations de la ".INTITULECLASSE."");
define("LANGVATEL209","Editions des notes");
define("LANGVATEL210","Edition des bulletins scolaires");
define("LANGVATEL211","Edition supplément Bachelor / Master");
define("LANGVATEL212","Commentaires enregistrés");
define("LANGVATEL213","Vérifier vos affectations pour cette ".INTITULECLASSE."");
define("LANGVATEL214","Gestion des dates de stage");
define("LANGVATEL215","Gestion des entreprises");
define("LANGVATEL216","Affectation des étudiants aux stages");
define("LANGVATEL217","Liste des étudiants en entreprise actuellement");
define("LANGVATEL218","Edition des conventions");
define("LANGVATEL219","Ajouter une période");
define("LANGVATEL220","Liste des périodes");
define("LANGVATEL221","La date de fin de stage ne peut être avant la date de début");
define("LANGVATEL222","Modifier une période");
define("LANGVATEL223","Supprimer une période");
define("LANGVATEL224","Suppression de toutes les dates non affectées à un étudiant");
define("LANGVATEL225","Lister");
define("LANGVATEL226","Gestion Stage");
define("LANGVATEL227","Imprimer la liste des entreprises");
define("LANGVATEL228","Nbre d'".INTITULEELEVE."s ayant effectué un stage");
define("LANGVATEL229","Plan");
define("LANGVATEL230","Historique des ".INTITULEELEVE."s");
define("LANGVATEL231","Récupération du fichier PDF");
define("LANGVATEL232","Adresse / CCP / Ville");
define("LANGVATEL233","Listing des entreprises en date du ");
define("LANGVATEL234","Affectation de plusieurs étudiants à un stage");
define("LANGVATEL235","Affectation d'un étudiant à un stage");
define("LANGVATEL236","Début");
define("LANGVATEL237","Fin");
define("LANGVATEL238","Pour la période : Semestre / Trimestre");
define("LANGVATEL239","Période désirée");
define("LANGVATEL240","Indiquer le numéro de stage ou du stage personnalisé");
define("LANGVATEL241","Imprimer la liste complète");

define("LANGVATEL242","Edition des affectations");
define("LANGVATEL243","Autre ".INTITULECLASSE."");
define("LANGVATEL244","Mise en place de l'EDT");


define("LANGVATEL245","Veuillez choisir le type de compte souhait&eacute;");
define("LANGVATEL246","Mise &agrave; jour des tables");
define("LANGVATEL247","Gestion Absences / Retards");
define("LANGVATEL248","Ajouter une absence ou un retard");
define("LANGVATEL249","Pour le personnel");
define("LANGVATEL250","Pour la vie scolaire");
define("LANGVATEL251","Pour tuteurs de stage");
define("LANGVATEL252","Pour la direction");
define("LANGVATEL253","la ou les ".INTITULECLASSE."(s)");
define("LANGVATEL254","Param&eacute;trage");
define("LANGVATEL255","Mise en place de l'EDT");

define("LANGVATEL256","Indiquer la liste des parents d'étudiants qui recevront un email");
define("LANGVATEL257","Aucun numéro");
define("LANGVATEL258","Confirmer envoi SMS");
define("LANGVATEL259","Port. ".ucfirst(INTITULEELEVE)." ");
define("LANGVATEL260","Portable ");
define("LANGVATEL261","Tel Prof. Mère ");
define("LANGVATEL262","Tel Prof. Père ");
define("LANGVATEL263","Vidéo Projecteur ");
define("LANGVATEL264","Détail ABS/Rtd ");
define("LANGVATEL265","Créneaux ");
define("LANGVATEL266","non précisé ");
define("LANGVATEL267","Lister / Modifier des ".INTITULEENSEIGNANT."s ");
define("LANGVATEL268","Supprimer un compte ");
define("LANGVATEL269","Abs/Rtd Etudiant ");
define("LANGVATEL270","Absences et Retards d'un étudiant");
define("LANGVATEL271","Création impossible, année scolaire non indiquée.");
define("LANGVATEL272","Nouvelle unité d'enseignement créée.");
define("LANGVATEL273","Trombinoscope");


define("LANGVATEL274","Envoi SMS pour les absences depuis ");

define("LANGVATEL275","NBETUDIANTS => Nombre d'etudiants<br />
 HISTOETUDIANT => Parcours de l'etudiant<br />
 NOMETUDIANT => Nom de l'etudiant<br>
 PREETUDIANT => Prénom de l'etudiant<br>
 DATENAISETUDIANT => Date de naissance de l'etudiant<br>
 IDENTETUDIANT => Code d'identification de l'etudiant<br>
 NOMETABLISSEMENT => Nom de l'etablissement de l'etudiant<br>
 DATEDUJOUR => Date du jour<br>
 LANGUEETUDIANT => La langue d'enseignement<br>
 NBRETUDIANTPA1 => Le nombre d'etudiants M4 et PREPA pour le titre 1 <br>
 NBRETUDIANTPA2 => Le nombre d'etudiants en première année pour le titre 2 <br>
 NBRETUDIANTPREPA => Le nombre d'etudiants en prepa  <br>
 NBRETUDIANTM4 => Le nombre d'etudiants en M4 pour le titre 1 <br>
 SPECIALISATION => Specialisation de la ".INTITULECLASSE." <br>
 NOMDIRECTEUR => Nom du Directeur de l'etablissement <br>
 NOMCLASSELONG => Nom de la ".INTITULECLASSE." au format long <br>");

define("LANGVATEL276","Listes des commentaires des bulletins effectués.");
define("LANGVATEL277","Consulter les commentaires de la ".INTITULECLASSE." par matière.");
define("LANGVATEL278","Nombre effectué");
define("LANGVATEL279","Taux effectué");
define("LANGVATEL280","Signalé");
define("LANGVATEL281","Envoi message");
define("LANGVATEL282","commentaire(s) enregistré(s) sur ");
define("LANGVATEL283","Age");
define("LANGVATEL284","ans");
define("LANGVATEL285","Listing des absences de cette période.");
define("LANGVATEL286","Nbr&nbsp;d'absences&nbsp;");
define("LANGVATEL287","Listing des retards de cette période.");
define("LANGVATEL288","Graphiques");
define("LANGVATEL289","Commentaire de la direction.");
define("LANGVATEL290","Commentaire du professeur principal.");
define("LANGVATEL291","Les commentaires des ".INTITULEENSEIGNANT."s sont disponibles sur la moyenne de l'".INTITULEELEVE.".");
define("LANGVATEL292","Archive bulletin");

define("LANGVATEL293","Control d'accès");
define("LANGVATEL294","Changement de ".INTITULECLASSE."");
define("LANGVATEL295","Indiquer l'année scolaire en cours des étudiants");
define("LANGVATEL296","Indiquer l'année scolaire future pour les étudiants ");
define("LANGVATEL297","Indiquer l'année scolaire de l'étudiant en cours ");
define("LANGVATEL298","Indiquer la nouvelle année scolaire ");
define("LANGVATEL299","Né(e) le ");
define("LANGVATEL300","Fiche Etudiante");
define("LANGVATEL301","Alerte Absences");
define("LANGVATEL302","Alerte SMS");
define("LANGVATEL303","Alerte SMS");


define("LANGNEW100","Sanction(s)");
define("LANGNEW101","Prévision sur ");

?>
