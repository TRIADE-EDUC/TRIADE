<?php
/***************************** MODULE FINANCIER ****************************

/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET 
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

//------------------------------
// GENERAL
//-------------------------------
define("LANG_FIN_GENE_001","Les données on été enregistrées");
define("LANG_FIN_GENE_002","Information");
define("LANG_FIN_GENE_003","Annuler/Retour");
define("LANG_FIN_GENE_004","Enregistrer");
define("LANG_FIN_GENE_005","Modifier");
define("LANG_FIN_GENE_006","Element introuvable");
define("LANG_FIN_GENE_007","Suppression effectuée");
define("LANG_FIN_GENE_008","Etes vous sûr de vouloir effectuer la suppression ?");
define("LANG_FIN_GENE_009","Accéder");
define("LANG_FIN_GENE_010","Libellé");
define("LANG_FIN_GENE_011","Année scolaire");
define("LANG_FIN_GENE_012","Optionnel");
define("LANG_FIN_GENE_013","Montant");
define("LANG_FIN_GENE_014","Ajouter");
define("LANG_FIN_GENE_015","Supprimer");
define("LANG_FIN_GENE_016","Intitulé");
define("LANG_FIN_GENE_017","Oui");
define("LANG_FIN_GENE_018","Non");
define("LANG_FIN_GENE_019","");
define("LANG_FIN_GENE_020","Rechercher");
define("LANG_FIN_GENE_021","Rechercher par");
define("LANG_FIN_GENE_022","Résultat");
define("LANG_FIN_GENE_023","Recherche de");
define("LANG_FIN_GENE_024","total");
define("LANG_FIN_GENE_025","tous");
define("LANG_FIN_GENE_026","Sélectionner un critère");
define("LANG_FIN_GENE_027","Chargement en cours. Veuillez patienter ...");
define("LANG_FIN_GENE_028","Inclus");
define("LANG_FIN_GENE_029","Calculer");
define("LANG_FIN_GENE_030","Date");
define("LANG_FIN_GENE_031","Payé");
define("LANG_FIN_GENE_032","Paiement");
define("LANG_FIN_GENE_033","Voir");
define("LANG_FIN_GENE_034","Reste à payer");
define("LANG_FIN_GENE_035","total prévisionel");
define("LANG_FIN_GENE_036","total à payer");
define("LANG_FIN_GENE_037","total échéances");
define("LANG_FIN_GENE_038","Réalisé");
define("LANG_FIN_GENE_039","Commentaire");
define("LANG_FIN_GENE_040","Annuler l'ajout");
define("LANG_FIN_GENE_041","Fermer la fenêtre");
define("LANG_FIN_GENE_042","Actualiser");
define("LANG_FIN_GENE_043","Totaux");
define("LANG_FIN_GENE_044","Voir plus d'infos");
define("LANG_FIN_GENE_045","Cacher les infos");
define("LANG_FIN_GENE_046","Légende");
define("LANG_FIN_GENE_047","Copier");
define("LANG_FIN_GENE_048","Element non modifiable");
define("LANG_FIN_GENE_049","Aucun");
define("LANG_FIN_GENE_050","Sélectionner");
define("LANG_FIN_GENE_051","Commentaires");
define("LANG_FIN_GENE_052","Optionnels");
define("LANG_FIN_GENE_053","Non-optionnels");
define("LANG_FIN_GENE_054","Copie");
define("LANG_FIN_GENE_055","Editer");
define("LANG_FIN_GENE_056","Type");
define("LANG_FIN_GENE_057","Copie");
define("LANG_FIN_GENE_058","Inscrits");
define("LANG_FIN_GENE_059","Pas inscrits");
define("LANG_FIN_GENE_060","Imprimer");
define("LANG_FIN_GENE_061","Exporter vers Excel");
define("LANG_FIN_GENE_062","toutes");

//------------------------------
// VALIDATION FORMULAIRE
//-------------------------------
define("LANG_FIN_VALI_001","Veuillez corriger les erreurs suivantes");
define("LANG_FIN_VALI_002","Le champ '%s' ne doit contenir que des chiffres");
define("LANG_FIN_VALI_003","Le champ '%s' doit être alphanumérique");
define("LANG_FIN_VALI_004","Le champ '%s' ne peut pas être vide");
define("LANG_FIN_VALI_005","Le champ '%s' doit être un nombre décimal.\\n            Ex : 54,00  (caractères autorisés : 0123456789,)");
define("LANG_FIN_VALI_006","Le champ '%s' doit être une date valide (jj/mm/aaa)");
define("LANG_FIN_VALI_007","Le champ '%s' doit être supérieur à 0");

//------------------------------
// VALIDATION AJAX
//-------------------------------
define("LANG_CHA_AJAX_001","Votre session a expiré. Veuillez vous authentifier à nouveau.");
define("LANG_CHA_AJAX_002","Le script appelé a généré une erreur inconnue. Veuillez essayer ultérieurement.");
define("LANG_CHA_AJAX_003","Erreur lors de la communication avec le serveur. Veuillez essayer ultérieurement.");

//------------------------------
// CLASSE
//-------------------------------
define("LANG_FIN_CLAS_001","Classes"); // Pluriel
define("LANG_FIN_CLAS_002","Pas de classe disponible");
define("LANG_FIN_CLAS_003","Classe"); // Singulier
define("LANG_FIN_CLAS_004","Pas de classe selectionnée");

//------------------------------
// ELEVE
//-------------------------------
define("LANG_FIN_ELEV_001","Elèves"); // Pluriel
define("LANG_FIN_ELEV_002","Elève"); // Singulier
define("LANG_FIN_ELEV_003","Aucun élève trouvé");
define("LANG_FIN_ELEV_004","Prénom");
define("LANG_FIN_ELEV_005","Nom");
define("LANG_FIN_ELEV_006","Inscrits/Pas inscrits");


//------------------------------
// RIB
//-------------------------------
define("LANG_FIN_RIB_001","Editer les RIB");
define("LANG_FIN_RIB_002","Impossible de générer l'enregistrement pour le RIB");
define("LANG_FIN_RIB_003","Code banque");
define("LANG_FIN_RIB_004","Code guichet");
define("LANG_FIN_RIB_005","N° de compte");
define("LANG_FIN_RIB_006","Clé RIB");
define("LANG_FIN_RIB_007","IBAN");
define("LANG_FIN_RIB_008","BIC");
define("LANG_FIN_RIB_009","SWIFT");
define("LANG_FIN_RIB_010","RIB");
define("LANG_FIN_RIB_011","Vous devez saisir le 'Code banque', et le 'Code guichet' et le 'N° de compte' et la 'Clé RIB'");
define("LANG_FIN_RIB_012","Titulaire");
define("LANG_FIN_RIB_013","Banque");
define("LANG_FIN_RIB_014","N° de RIB");
define("LANG_FIN_RIB_015","Description");
define("LANG_FIN_RIB_016","Pas de RIB");
define("LANG_FIN_RIB_017","RIB échéanche");
define("LANG_FIN_RIB_018","La clé RIB n'est pas valide (elle ne correpond pas au calcul basé sur le 'Code banque',\\n        le 'Code guichet' et le 'N° de compte'.");

//------------------------------
// PARAMETRAGE
//-------------------------------
define("LANG_FIN_PARA_001","Paramètrage du Module Financier");

//------------------------------
// TYPE DE FRAIS
//-------------------------------
define("LANG_FIN_TFRA_001","Gestion des types de frais");
define("LANG_FIN_TFRA_002","Ajouter, modifier ou supprimer un type de frais");
define("LANG_FIN_TFRA_003","Création d'un type de frais");
define("LANG_FIN_TFRA_004","Liste / modifier type de frais");
define("LANG_FIN_TFRA_005","Supprimer un type de frais");
define("LANG_FIN_TFRA_006","Modifier un type de frais");
define("LANG_FIN_TFRA_007","Intitulé du type de frais");
define("LANG_FIN_TFRA_008","Suppression d'un type de frais");
define("LANG_FIN_TFRA_009","Créer le type de frais");
define("LANG_FIN_TFRA_010","Modification d'un type de frais");
define("LANG_FIN_TFRA_011","Pas de type de frais disponible");
define("LANG_FIN_TFRA_012","Les types de frais grisés ne peuvent pas être supprimés car ils sont utilisés dans un ou plusieurs barèmes ou inscriptions");
define("LANG_FIN_TFRA_013","Ce type de frais ne peut pas être supprimé car il est utilisé dans un ou plusieurs barèmes");
define("LANG_FIN_TFRA_014","Lissé");
define("LANG_FIN_TFRA_015","Si le type de frais est lissé, ses paiements seront étalés sur plusieurs échéances. Sinon, il n\'y a qu\'une seule échéance au départ");
define("LANG_FIN_TFRA_016","Caution");
define("LANG_FIN_TFRA_017","Indique si le type de frais est considéré comme une caution. Devra être validé dans l\'inscription une fois payé");
define("LANG_FIN_TFRA_018","Non lissé");
define("LANG_FIN_TFRA_019","Caution remboursée");

//------------------------------
// TYPE DE REGLEMENT
//-------------------------------
define("LANG_FIN_TREG_001","Gestion des types de règlements");
define("LANG_FIN_TREG_002","Ajouter, modifier ou supprimer un type de règlement");
define("LANG_FIN_TREG_003","Création d'un type de règlement");
define("LANG_FIN_TREG_004","Liste / modifier type de règlement");
define("LANG_FIN_TREG_005","Supprimer un type de règlement");
define("LANG_FIN_TREG_006","Modifier un type de règlement");
define("LANG_FIN_TREG_007","Intitulé du type de règlement");
define("LANG_FIN_TREG_008","Suppression d'un type de règlement");
define("LANG_FIN_TREG_009","Créer le type de règlement");
define("LANG_FIN_TREG_010","Modification d'un type de règlement");
define("LANG_FIN_TREG_011","Pas de type de règlement disponible");
define("LANG_FIN_TREG_012","Les types de règlement grisés ne peuvent pas être supprimés  soit car ils sont utilisés dans un ou plusieurs règlements ou échéancier, soit car ils sont indispensables au bon fonctionement de l\'application");
define("LANG_FIN_TREG_013","Ce type de règlement ne peut pas être supprimé car il est utilisé dans un ou plusieurs règlements");
define("LANG_FIN_TREG_014","Types de règlement"); // Pluriel
define("LANG_FIN_TREG_015","Type de règlement"); // Singulier
define("LANG_FIN_TREG_016","Règlements"); // Pluriel
define("LANG_FIN_TREG_017","Règlement"); // Singulier

//------------------------------
// BAREME
//-------------------------------
define("LANG_FIN_BARE_001","Gestion des barèmes");
define("LANG_FIN_BARE_002","Ajouter, modifier ou supprimer un barème et ses frais pour une classe");
define("LANG_FIN_BARE_003","Barèmes"); // Pluriel
define("LANG_FIN_BARE_004","Barème"); // Singulier
define("LANG_FIN_BARE_005","Pas de barème pour cette classe et cette année scolaire");
define("LANG_FIN_BARE_006","Ajouter un barème");
define("LANG_FIN_BARE_007","Modifier un barème");
define("LANG_FIN_BARE_008","Supprimer un barème");
define("LANG_FIN_BARE_009","Etes-vous sûr de vouloir supprimer ce barème ?\\n(les frais associés seront aussi supprimés)");
define("LANG_FIN_BARE_010","Copier un barème");
define("LANG_FIN_BARE_011","Si vous continuez, le barème '#s1#' de la classe '#s2#' sera copié ici");
define("LANG_FIN_BARE_012","Barème initial");

//------------------------------
// FRAIS DE BAREME
//-------------------------------
define("LANG_FIN_FBAR_001","Gestion des frais pour un barème");
define("LANG_FIN_FBAR_002","Ajouter, modifier ou supprimer un frais pour un barème");
define("LANG_FIN_FBAR_003","Frais"); // Pluriel
define("LANG_FIN_FBAR_004","Frais"); // Singulier
define("LANG_FIN_FBAR_005","Pas de frais pour ce barème");
define("LANG_FIN_FBAR_006","Pas de classe, ni de barème selectionnés");
define("LANG_FIN_FBAR_007","Ajouter un frais de barème");
define("LANG_FIN_FBAR_008","Modifier un frais de barème");
define("LANG_FIN_FBAR_009","Supprimer un frais de barème");
define("LANG_FIN_FBAR_010","Etes-vous sûr de vouloir supprimer ce frais ?");
define("LANG_FIN_FBAR_011","Supprimer le frais");
define("LANG_FIN_FBAR_012","Pas de frais disponible (ils ont tous été utilisés)");

//------------------------------
// INSCRIPTIONS
//-------------------------------
define("LANG_FIN_INSC_001","Rechercher une inscription");
define("LANG_FIN_INSC_002","Inscrit");
define("LANG_FIN_INSC_003","Voir inscription");
define("LANG_FIN_INSC_004","Inscrire");
define("LANG_FIN_INSC_005","La classe \'%s\' n\'a pas de barème pour l\'année scolaire \'%s\'.<br>Vous devez créer au moins un barème avant de pouvoir inscrire l\'élève.<br>Pour cela cliquez sur le lien \'Paramètrage\' du menu \'Module financier\'.");
define("LANG_FIN_INSC_006","Inscrire un élève");
define("LANG_FIN_INSC_007","Vous devez saisir une date avant de pouvoir calculer les échéances");
define("LANG_FIN_INSC_008","Choisissez une date de début et cliquez sur 'Calculer'");
define("LANG_FIN_INSC_009","Vous avez ajouté et/ou enlevé un des frais optionnels ou bien vous avez changé le type d'échéancier.\\nL'échéancier doit être calculé à nouveau avant d'enregistrer l'inscription.");
define("LANG_FIN_INSC_010","Vous devez saisir une date de début et calculer les échéances avant de pouvoir enregistrer l'inscription.");
define("LANG_FIN_INSC_011","Si vous continuez, l'échéancier actuel sera effacé.\\nVoulez-vous continuer ?");
define("LANG_FIN_INSC_012","Date de l'échéance n°#i#");
define("LANG_FIN_INSC_013","Montant de l'échéance n°#i#");
define("LANG_FIN_INSC_014","La date de l'échéance n°#i# doit être supérieure ou égale à la précédente");
define("LANG_FIN_INSC_015","ATTENTION : Le total des montants des échéanches (#s1# ) n'est pas égal au montant total des frais (#s2# ).\\n\\nAprès avoir enregistré, veuillez vérifier que ces deux totaux seront égaux. Si nécéssaire, ajustez les montants des échéances ou créez une nouvelle échéance.");
define("LANG_FIN_INSC_016","L'élève a été inscrit");
define("LANG_FIN_INSC_017","L'élève est déjà inscrit");
define("LANG_FIN_INSC_018","Données de l'inscription de l'élève");
define("LANG_FIN_INSC_019","Inscriptions"); // Pluriel
define("LANG_FIN_INSC_020","Inscription"); // Singulier
define("LANG_FIN_INSC_021","Au moins une échéance a été modifiée.\\nSi vous continuez sans enregistrer, les modifications seront perdues.\\n\\nVoulez-vous enregistrer ?");
define("LANG_FIN_INSC_022","Recalculer le montant des échéances");
define("LANG_FIN_INSC_023","Si vous continuez, les montants de %s échéance(s) lissée(s) sans paiement seront recalculé en fonction des frais sélectionnés (optionnels et lissés)\\nVous devrez ensuite vous assurer que le montant total des échéances normales correspond au total des frais. Si ce n'est pas le cas, vous devrez ajuster les montants (et eventuellement ajouter des échéances).\\n\\nVoulez-vous continuer ?");
define("LANG_FIN_INSC_024","Date de départ");
define("LANG_FIN_INSC_025","Date de départ de l\'élève si il est partit avant la fin de l\'année scolaire.<br>(évite que les échéances postérieures soient considérées comme impayées)<br>Ce champ reste vide si l\'élève est resté jusqu\'à la fin de l\'année.");
define("LANG_FIN_INSC_026","Voir les frais");
define("LANG_FIN_INSC_027","Cacher les frais");
define("LANG_FIN_INSC_028","Classe et année scolaire");
define("LANG_FIN_INSC_029","L'élève est déjà inscrit dans cette classe pour toutes les années disponibles");
define("LANG_FIN_INSC_030","Supprimer cette inscription (pas de règlements)");
define("LANG_FIN_INSC_031","Êtes-vous sûr de vouloir supprimer l'inscription de cet élève ?");
define("LANG_FIN_INSC_032","L'inscription a été supprimée.");
define("LANG_FIN_INSC_033","ATTENTION : Le total des montants des échéanches (#s1# ) n'est pas égal au montant total des frais (#s2# ).\\n\\nVous devez ajustez les montants des échéances et/ou créez une nouvelle échéance.");

//------------------------------
// DUPLICATION ECHEANCIER
//-------------------------------
define("LANG_FIN_DUPL_01","Options de création d'une inscription");
define("LANG_FIN_DUPL_02","Nouvelle inscription (à partir de 0)");
define("LANG_FIN_DUPL_03","Copier l'inscription et l'échéancier d'un autre élève");

//------------------------------
// ECHEANCIER
//-------------------------------
define("LANG_FIN_ECHE_001","Echéanciers"); // Pluriel
define("LANG_FIN_ECHE_002","Echéancier"); // Singulier
define("LANG_FIN_ECHE_003","Echéances"); // Pluriel
define("LANG_FIN_ECHE_004","Echéance"); // Singulier
define("LANG_FIN_ECHE_005","Ajouter une échéance"); // Singulier
define("LANG_FIN_ECHE_006","Date de la nouvelle échéance");
define("LANG_FIN_ECHE_007","´Correspond à´ de la nouvelle échéance");
define("LANG_FIN_ECHE_008","Montant de la nouvelle échéance");
define("LANG_FIN_ECHE_009","Informations sur l\'échéance additionelle");
define("LANG_FIN_ECHE_010","Correspond à");
define("LANG_FIN_ECHE_011","Le champ \'Correspond à\' de l\'échéance n°#i# ne doit pas être vide. (si vous ne le voyez pas, cliquez sur le  [+])");
define("LANG_FIN_ECHE_012","Couleurs des lignes des échéances");
define("LANG_FIN_ECHE_013","Echéance normale, lissée et non expirée");
define("LANG_FIN_ECHE_014","Echéance normale, lissée, mais expirée");
define("LANG_FIN_ECHE_015","Echéance additionnelle et non expirée");
define("LANG_FIN_ECHE_016","Echéance additionnelle, mais expirée");
define("LANG_FIN_ECHE_017","Echéance normale, non-lissée et non expirée");
define("LANG_FIN_ECHE_018","Echéance normale, non-lissée, mais expirée");
define("LANG_FIN_ECHE_019","Supprimer les échéances");
define("LANG_FIN_ECHE_020","Diviser les échéances");
define("LANG_FIN_ECHE_021","Fusioner les échéances");
define("LANG_FIN_ECHE_022","Vous devez sélectionner une échéance (et une seule)");
define("LANG_FIN_ECHE_023","Etes-vous sûr de vouloir supprimer ces échéances ?");
define("LANG_FIN_ECHE_024","Si vous continuez :\\n      1 - chaque échéance sera dupliquée\\n      2 - à chaque fois, les deux échéances auront la même date\\n      3 - à chaque fois, le montant sera répartit entre les deux échéances\\n\\nEtes-vous sûr de vouloir diviser ces échéances ?");
define("LANG_FIN_ECHE_025","Vous devez sélectionner deux échéances");
define("LANG_FIN_ECHE_026","Si vous continuez :\\n      1 - les deux échéances seront fusionnées\\n      2 - les données iront dans la première échéance\\n      3 -les montants seront cumulés dans la premiere échéance\\n\\nEtes-vous sûr de vouloir fusionner ces échéances ?");
define("LANG_FIN_ECHE_027","Remise exceptionnelle et non expirée");
define("LANG_FIN_ECHE_028","Remise exceptionnelle, mais expirée");
define("LANG_FIN_ECHE_029","Ligne avec texte et champs grisés => échéance postérieure à la date de départ");

//------------------------------
// TYPE ECHEANCIER
//-------------------------------
define("LANG_FIN_TECHE_001","Types echéancier"); // Pluriel
define("LANG_FIN_TECHE_002","Type echéancier"); // Singulier
define("LANG_FIN_TECHE_005","Pas de type echéancier disponible");
define("LANG_FIN_TECHE_006","Date début");

//------------------------------
// REGLEMENT
//-------------------------------
define("LANG_FIN_REGL_001","Règlements"); // Pluriel
define("LANG_FIN_REGL_002","Règlement"); // Singulier
define("LANG_FIN_REGL_003","Règlements pour une échéance");
define("LANG_FIN_REGL_004","En rouge car l\'échéance est expirée et son montant total n\'a pas été payé");
define("LANG_FIN_REGL_005","En vert car le total des paiement est supérieur au montant de l\'échéance (solde créditeur pour cette échéance)");
define("LANG_FIN_REGL_006","Solde incluant les sommes payées pour les échéances expirées et les montants des échéances à venir");
define("LANG_FIN_REGL_007","Total des montants des échéances normales (celles générées lors de l\'inscription) + le montants des échéances exceptionelles<br>*** doit correspondre au total des frais ***");
define("LANG_FIN_REGL_008","Pas de règlement trouvé");
define("LANG_FIN_REGL_009","Ajouter un règlement");
define("LANG_FIN_REGL_010","Date du nouveau règlement");
define("LANG_FIN_REGL_011","Libellé du nouveau règlement");
define("LANG_FIN_REGL_012","Montant du nouveau règlement");
define("LANG_FIN_REGL_013","En orange car  seulement une partie de l\'échéance a été payée");
define("LANG_FIN_REGL_014","Date du règlement n°#i#");
define("LANG_FIN_REGL_015","Libellé du règlement n°#i#");
define("LANG_FIN_REGL_016","Montant du règlement n°#i#");
define("LANG_FIN_REGL_017","Total échéances normales");
define("LANG_FIN_REGL_018","Total échéances additionnelles");
define("LANG_FIN_REGL_019","Total des montants des échéances additionelles (celles ajoutées manuellement)");
define("LANG_FIN_REGL_020","N° de chèque");
define("LANG_FIN_REGL_021","N° de bordereau");
define("LANG_FIN_REGL_022","Total remises exceptionelles");
define("LANG_FIN_REGL_023","Total normales + remises");

define("LANG_FIN_REGL_024","Total des montants des échéances normales et aditionnelles<br>(échéances antérieures à la date de départ)");

//------------------------------
// PAIEMENTS
//-------------------------------
define("LANG_FIN_PAIE_001","Paiements"); // Pluriel
define("LANG_FIN_PAIE_002","Paiement"); // Singulier
define("LANG_FIN_PAIE_003","Génération fichier prélèvements");
define("LANG_FIN_PAIE_004","Génération du fichier envoyé à la banque pour les prélèvements automatiques");
define("LANG_FIN_PAIE_005","Génération des bordereaux");
define("LANG_FIN_PAIE_006","Génération des bordereaux pour déposer les chèques et les espèces à la banque");
define("LANG_FIN_PAIE_007","Cautions");
define("LANG_FIN_PAIE_008","Voir la liste des cautions non-remboursées");
define("LANG_FIN_PAIE_009","Rechercher un bordereau");
define("LANG_FIN_PAIE_010","Rechercher les bordereaux existants de remise de chèques et espèces");

//------------------------------
// IMPAYES
//-------------------------------
define("LANG_FIN_IMPA_001","Impayés"); // Pluriel
define("LANG_FIN_IMPA_002","Impayé"); // Singulier
define("LANG_FIN_IMPA_003","Liste des impayés à ce jour");
define("LANG_FIN_IMPA_004","Pas d'impayés à ce jour pour ce type de règlement");

//------------------------------
// GENERATION FICHIER PRELEVEMENT
//-------------------------------
define("LANG_FIN_GPRE_001","Génération du fichier de prélevement");
define("LANG_FIN_GPRE_002","Date limite échéances");
define("LANG_FIN_GPRE_003","Pour rechercher les échéances qui expirent avant cette date");
define("LANG_FIN_GPRE_004","Pas d'échéance à payer par prélèvement pour cette date limite");
define("LANG_FIN_GPRE_005","Générer le fichier de prélevement");
define("LANG_FIN_GPRE_006","Vous devez sélectionner un RIB pour l'élève n°#i#.\\n          Si il n'en n'a pas, cliquez sur 'Editer le RIB' pour en ajouter un");
define("LANG_FIN_GPRE_007","Si vous continuez, le fichier sera généré et un règlement sera créé pour chaque échéance");
define("LANG_FIN_GPRE_008","Date du règlement");
define("LANG_FIN_GPRE_009","Date avec laquelle le règlement de chaque échéance sera créé");
define("LANG_FIN_GPRE_010","Paiement échéance du");
define("LANG_FIN_GPRE_011","Ordre de tri");
define("LANG_FIN_GPRE_012","Nom de l'élève");
define("LANG_FIN_GPRE_013","Date d'échéance");

//------------------------------
// TYPE D'ECHEANCE
//-------------------------------
define("LANG_FIN_TECH2_001","Types echéance"); // Pluriel
define("LANG_FIN_TECH2_002","Type echéance"); // Singulier
define("LANG_FIN_TECH2_003","Normale - lissée");
define("LANG_FIN_TECH2_004","Normale - non lissée");
define("LANG_FIN_TECH2_005","Additionnelle");
define("LANG_FIN_TECH2_006","<b>Normale - lissée</b> : les échéances lissées sont celles sur lesquelles le total des frais est répartit (et leur montant est pris en compte dans \'Total échéances normales\')<br><b>Normale - non lissée</b> : Leur montant est fixe et il n\'y a pas de répartition (et leur montant est pris en compte dans \'Total échéances normales\')<br><b>Additionnelle</b> : sont indépendantes des frais<br><br>On a :<br>&nbsp;&nbsp;&nbsp;- Total échéances normales = échéances normales lissées + non lissées<br>&nbsp;&nbsp;&nbsp;- Total à payer = Total échéances normales + Total échéances additionnelle");
define("LANG_FIN_TECH2_007","Remise Exceptionelle");

//------------------------------
// CAUTIONS NON REMBOURSEES
//-------------------------------
define("LANG_FIN_CANR_001","Liste des cautions non remboursées");
define("LANG_FIN_CANR_002","Pas de caution non remboursée");

//------------------------------
// GENERATION DES BORDEREAUX
//-------------------------------
define("LANG_FIN_GBOR_001","Bordereaux"); // Pluriel
define("LANG_FIN_GBOR_002","Bordereau"); // Singulier
define("LANG_FIN_GBOR_003","N° du bordereau");
define("LANG_FIN_GBOR_004","Générer le bordereau");
define("LANG_FIN_GBOR_005","Le règlement n'est pas réalisé");
define("LANG_FIN_GBOR_006","Le type de règlement n'est pas celui recherché");
define("LANG_FIN_GBOR_007","Le numéro de bordereau n'est pas vide (le règlement fait partie d'un autre bordereau)");
define("LANG_FIN_GBOR_008","N° du bordereau qui sera généré (le n° de bordereau des règlements sélectionnés sera initialisé)");
define("LANG_FIN_GBOR_009","Vous devez sélectionner au moins un règlement qui fera partie du bordereau");
define("LANG_FIN_GBOR_010","Ce n° de bordereau est déjà utilisé. Veuillez en saisir un autre");
define("LANG_FIN_GBOR_011","Si vous continuez, le bordereau sera généré et le n° de bordereau de chaque règlement sera initialisé.\\nUne fois le fichier téléchargé, cliquez ensuite sur le bouton 'Rechercher' pour voir les modifications apportées aux règlements");
define("LANG_FIN_GBOR_012","Pas d'échéances et de règlements pour cette date limite et ce type de règlement");
define("LANG_FIN_GBOR_013","Date de remise");
define("LANG_FIN_GBOR_014","Pour rechercher les échéances non payées qui expirent avant cette date (inclue)");
define("LANG_FIN_GBOR_015","Bordereau de remise de chèque");
define("LANG_FIN_GBOR_016","Bordereau de remise d'espèce");
define("LANG_FIN_GBOR_017","bordereau_remise_cheque");
define("LANG_FIN_GBOR_018","bordereau_remise_espece");

//------------------------------
// RECHERCHE DES BORDEREAUX
//-------------------------------
define("LANG_FIN_RBOR_001","Pas de règlement trouvé pour ce numéro de bordereau");

//------------------------------
// EDITIONS
//-------------------------------
define("LANG_FIN_EDIT_001","Editions du module financier");
define("LANG_FIN_EDIT_002","Editions des barèmes");
define("LANG_FIN_EDIT_003","Editions des inscriptions");
define("LANG_FIN_EDIT_004","Tableau de bord");
define("LANG_FIN_EDIT_005","Editions des montants de scolarité par élève");
define("LANG_FIN_EDIT_006","Editions des montants encaissés et impayés");

//------------------------------
// EDITIONS DES BAREMES
//-------------------------------
define("LANG_FIN_EBAR_001","Pas de barèmes correspondant aux critères");

//------------------------------
// EDITIONS DES INSCRIPTIONS
//-------------------------------
define("LANG_FIN_EINS_001","Pas d'inscriptions correspondant aux critères");
define("LANG_FIN_EINS_002","Sélectionnez un critère puis cliquez sur 'Rechercher'");
define("LANG_FIN_EINS_003","inscription(s) trouvée(s)");

//------------------------------
// EDITION - TABLEAU DE BORD
//-------------------------------
define("LANG_FIN_EBOR_001","inscriptions");
define("LANG_FIN_EBOR_002","encaissé'");
define("LANG_FIN_EBOR_003","A encaisser");
define("LANG_FIN_EBOR_004","Détail par type de frais");
define("LANG_FIN_EBOR_005","Nombre d'inscriptions");
define("LANG_FIN_EBOR_006","Montant encaissé");
define("LANG_FIN_EBOR_007","Montant à encaisser");
define("LANG_FIN_EBOR_008","Nombre");

//------------------------------
// EDITION - SCOLARITE PAR ELEVE
//-------------------------------
define("LANG_FIN_ESCO_001","Classe/élève");
define("LANG_FIN_ESCO_002","Élève/clase");
define("LANG_FIN_ESCO_003","Nombre d'élèves");
define("LANG_FIN_ESCO_004","Total pour cette classe");
define("LANG_FIN_ESCO_005","Nombre total d'élèves");
define("LANG_FIN_ESCO_006","Total scolarité général");
define("LANG_FIN_ESCO_007","Total reste à payer général");
define("LANG_FIN_ESCO_008","Nombre de classes");

//------------------------------
// EDITION - ENCAISSES ET IMPAYES
//-------------------------------
define("LANG_FIN_EENC_001","Date début");
define("LANG_FIN_EENC_002","Date fin");
define("LANG_FIN_EENC_003","Date de début de la recherche");
define("LANG_FIN_EENC_004","Date de fin de la recherche");
define("LANG_FIN_EENC_005","Date échéance");
define("LANG_FIN_EENC_006","Total échéance");
define("LANG_FIN_EENC_007","Reste à payer");
define("LANG_FIN_EENC_008","Encaissé");
define("LANG_FIN_EENC_009","Impayé");
?>