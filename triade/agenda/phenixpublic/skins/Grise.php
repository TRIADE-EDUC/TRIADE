<?php
  $AppliStyleNom="Grise";             /*NOM DU STYLE AFFICHE DANS LE CHOIX DE L'INTERFACE*/
                                      /*           NE PAS DEPLACER NI SUPPRIMER          */

  $AppliFondEcran="#FFFFFF";                               /*FOND PAGE*/
  $AppliFondImage="url(\"../skins/Grise/bg.png\")";        /*IMAGE FOND PAGE*/

  $FormulaireFondInput="#FFFFF7";                          /*FOND FORMULAIRE*/
  $FormulaireTexteInput="#000000";                         /*TEXTE FORMULAIRE*/
  $FormulaireBordureInput="#666666 solid 1px";             /*BORDURE FORMULAIRE*/
  $FormulaireFondBouton="#B5BECE";                         /*FOND BOUTON*/
  $FormulaireTexteBouton="#336699";                        /*TEXTE BOUTON*/
  $FormulaireBordureBouton="#666666 solid 1px";            /*BORDURE BOUTON*/
  $FormulaireImageBouton="url(\"../skins/Grise/th.gif\")"; /*IMAGE FOND BOUTON*/

  $MenuFond="#404040";                                     /*AGENDA FOND MENU*/
  $MenuOnLien="#535D66";
  $MenuOnLienHover="#000000";
  $MenuOnFond="#CCD9E2;BORDER-LEFT:solid 1px #FFFFFF;BORDER-RIGHT:solid 1px #FFFFFF;";
  $MenuOffLien="#FFFFFF";
  $MenuOffLienHover="#BBBBBB";
  $MenuCopyright="#BBBBBB";                                /*TEXTE COPYRIGHT*/

  $AujourdhuiFond="#CCD9E2";
  $AujourdhuiLien="#535D66";
  $AujourdhuiLienHover="#000000";

  $PageTitre="#535D66";
  $PageNomUtilisateur=$PageTitre;
  $PageDate="#535D66";

  $CalNavigationFond="#83909D";                            /*AGENDA FOND SOUS MENU*/
  $CalNavigationTexte="#FFFFFF";                           /*AGENDA TEXTE SOUS MENU*/
  $CalNavigationLien=$CalNavigationTexte;                  /*AGENDA LIEN SOUS MENU*/
  $CalNavigationLienHover="#404040";                       /*AGENDA SURVOL LIEN SOUS MENU*/
  $CalTitreFond="#CCD9E2";
  $CalFond="#F1F1F1";                                      /*CALENDRIER FOND CALENDRIER*/
  $CalJour="#000000";                                      /*CALENDRIER JOUR DU MOIS*/
  $CalJourHover="#999999";                                 /*CALENDRIER JOUR DU MOIS SURVOL*/
  $CalJourWE="#CC0000";                                    /*CALENDRIER JOUR DU MOIS WEEK-END*/
  $CalJourWEHover="#FF0000";                               /*CALENDRIER JOUR DU MOIS WEEK-END SURVOL*/
  $CalJourMoisPrec="#535D66";                              /*CALENDRIER JOUR MOIS PRECEDENT*/
  $CalJourMoisPrecHover="#000000";                         /*CALENDRIER JOUR MOIS PRECEDENT SURVOL*/
  $CalJourMoisPrecWE="#EF5353";                            /*CALENDRIER JOUR MOIS PRECEDENT WEEK-END*/
  $CalJourMoisPrecWEHover="#FF0000";                       /*CALENDRIER JOUR MOIS PRECEDENT WEEK-END SURVOL*/
  $CalJourFerie="#FFCC88";                                 /*CALENDRIER JOUR FERIE*/
  $CalJourEvenement="#BBBBBB"; 											  		 /*CALENDRIER EVENEMENT*/
  $CalJourSelection="#E9EEF3";                             /*CALENDRIER FOND PLAGE SELECTION*/
  $CalJourCourant="#DDDDDD";                               /*CALENDRIER JOUR OU SEMAINE COURANTE*/
  $CalQuitterLien="#000000";
  $CalQuitterLienHover="#999999";
  $CalFondExport="#FFFFFF";                                /*FOND DES LIENS POUR L'IMPORT / EXPORT*/
  $CalFlechePopup="menuarrow.gif";                         /*FLECHE POUR LES MENUS DU CALENDRIER EN DHTML*/
                                                           /*A PLACER DANS "image/calendrier/"*/

  $AgendaLien="#000000";                                   /*AGENDA LIEN*/
  $AgendaLienHover="#999999";                              /*AGENDA LIEN SURVOL*/
  $AgendaTexte="#000000";                                  /*AGENDA TEXTE*/
  $AgendaBordureTableau="#666666";
  $AgendaBordureNote=$AgendaBordureTableau;                /*PLANNING BORDURE NOTE*/
  $AgendaFondNotePerso="#E9EEF3";                          /*PLANNING FOND NOTE PERSO*/
  $AgendaFondNote="#FFFFC0";                               /*PLANNING FOND NOTE*/
  $AgendaPopupBordure=$AgendaBordureTableau;               /*AGENDA BORDURE POPUP*/
  $AgendaFondTitrePopup="#404040";                         /*AGENDA FOND TITRE POPUP*/
  $AgendaTexteTitrePopup="#FFFFFF";                        /*AGENDA TEXTE TITRE POPUP*/
  $AgendaFondPopup="#FFFFE1";                              /*AGENDA FOND POPUP*/
  $AgendaTextePopup="#000000";                             /*AGENDA TEXTE POPUP*/
  $AgendaContactPopup="#DDDDDD";                           /*AGENDA CONTACT ASSOCIE POPUP*/
  $AgendaLegende="#000000";                                /*LEGENDE DES BOUTONS DE L'AGENDA*/
  $AgendaDateCreationNote="#666666";                       /*DATE DE CREATION D'UNE NOTE DANS LES POPUP*/
  $AgendaLigneHover="#83909D";                             /*SURVOL DES LIGNES DANS LES PLANNINGS*/

  $CalepinValide=$CalNavigationTexte;                      /*CALEPIN LETTRE VALIDE*/
  $CalepinNonValide="#404040";                             /*CALEPIN LETTRE NON VALIDE*/
  $CalepinSelection=$CalNavigationFond;                    /*CALEPIN LETTRE SELECTIONNEE*/
  $CalepinFondSelection=$CalNavigationTexte;               /*CALEPIN FOND LETTRE SELECTIONNEE*/
  $CalepinFondMessage=$CalFond;                            /*CALEPIN FOND MESSAGE INFORMATION*/

  $MessageErreurFond="#ED1015";                            /*MESSAGE ERREUR FOND*/
  $MessageErreurTexte="#FFFFFF";                           /*MESSAGE ERREUR TEXTE*/
  $MessageConfirmeFond="#008000";                          /*MESSAGE CONFIRME FOND*/
  $MessageConfirmeTexte="#FFFFFF";                         /*MESSAGE CONFIRME TEXTE*/

  $PlanningJour=$CalFond;                                  /*PLANNING DISPO JOUR*/
  $PlanningMois=$PlanningJour;                             /*PLANNING DISPO MOIS*/
  $PlanningNotePrivee="#ED1015";                           /*PLANNING DISPO NOTE*/
  $PlanningPartiel="#FFCC00";                              /*PLANNING DISPO PARTIEL*/
  $PlanningLibre=$CalJourSelection;                        /*PLANNING DISPO LIBRE*/
  $PlanningInvalideTexte="#FFFFFF";                        /*PLANNING DISPO JOUR INVALIDE TEXTE*/

  $bgColor[0]="#FFFFFF";                                   /*FOND LIGNE1*/
  $bgColor[1]="#DDDDDD";                                   /*FOND LIGNE2*/

// MAJ Phenix 5.0
  $MenuTitreFond=$MenuFond;                                /*COULEUR DE FOND DU TITRE PRINCIPAL (Phenix - forum)*/
  $CalLignesHMoisFond=$CalNavigationFond;                  /*CALENDRIER FOND BORDURES CALENDRIER MOIS EN COURS, LIGNES HOR.*/
  $CalMemoFavorisTitre=$CalNavigationLien;                 /*CALENDRIER TITRE MEMO FAVORIS*/
  $CalMemoFavorisTitreHover=$CalNavigationLienHover;       /*CALENDRIER TITRE MEMO FAVORIS HOVER*/
  $CalMemoFavoris=$CalNavigationFond;                      /*CALENDRIER TITRE MEMOS, FAVORIS*/
  $CalMemoFavorisFond=$CalTitreFond;                       /*CALENDRIER FOND MEMOS, FAVORIS*/
  $CalAgendaSeparation=$CalNavigationFond;                 /*CALENDRIER BORDURE SEPARATION CALENDRIER/AGENDA UTILISATEUR*/
  $CalClockFond=$CalNavigationFond;                        /*CALENDRIER FOND DE L'HORLOGE*/
  $CalClockTexte=$CalNavigationTexte;                      /*CALENDRIER COULEUR DE TEXTE DE L'HORLOGE*/
  $CalMoisNavigationFond=$CalNavigationFond;               /*CALENDRIER GAUCHE FOND MOIS EN COURS*/
  $CalMoisNavigationTexte=$CalNavigationTexte;             /*CALENDRIER GAUCHE TEXTE MOIS EN COURS*/
  $CalMoisNavigationTexteHover=$CalMoisNavigationTexte;    /*CALENDRIER GAUCHE TEXTE MOIS EN COURS SURVOL*/
  $CalGaucheTitreFond=$CalTitreFond;                       /*CALENDRIER GAUCHE FOND TITRE*/
  $CalGaucheFond=$CalFond;                                 /*CALENDRIER DE GAUCHE - COULEUR DE FOND*/
  $AgendaTitreFond=$CalTitreFond;                          /*AGENDA FOND TITRE 1*/
  $AgendaTitre2Fond=$CalTitreFond;                         /*AGENDA FOND TITRE 2*/
  $AgendaLegendeActionFond=$CalNavigationFond;             /*AGENDA FOND LEGENDE DES ACTIONS*/
  $AgendaLegendeActionTexte=$CalNavigationTexte;           /*AGENDA TEXTE LEGENDE DES ACTIONS*/
  $AgendaProfilActifLien=$CalNavigationLien;               /*AGENDA PROFIL COULEUR TEXTE MENU ACTIF*/
  $AgendaProfilActifLienHover=$CalNavigationLienHover;     /*AGENDA PROFIL COULEUR TEXTE MENU ACTIF PASSAGE SOURIS*/
  $AgendaProfilActifFond=$CalNavigationFond;               /*AGENDA FOND MENU PROFIL ACTIF BACKGROUND-COLOR*/
  $AgendaProfilActifColor=$CalNavigationTexte;             /*AGENDA FOND MENU PROFIL ACTIF COLOR*/
  $AgendaProfilInactifLien=$MenuOffLien;                   /*AGENDA PROFIL COULEUR TEXTE MENU INACTIF*/
  $AgendaProfilInactifLienHover=$MenuOffLienHover;         /*AGENDA PROFIL COULEUR TEXTE MENU INACTIF PASSAGE SOURIS*/
  $AgendaProfilInactifFond=$MenuFond;                      /*AGENDA FOND MENU PROFIL ACTIF BACKGROUND-COLOR*/
  $AgendaProfilInactifColor=$MenuOffLien;                  /*AGENDA FOND MENU PROFIL INACTIF TEXTE*/
  $AgendaPopUpBords=$AgendaBordureTableau;                 /*AGENDA POPUP BORDURES FOND avec transparence*/
  $AgendaPopUpFondImage="";                                /*AGENDA POPUP FOND IMAGE - REMPLACE L'IMAGE DE FOND GENERALE*/
  //$AgendaPopUpFondImage="BACKGROUND-IMAGE: url(\"./skins/XX/YYYY.jpg\");";            /*AGENDA POPUP FOND IMAGE - REMPLACE L'IMAGE DE FOND GENERALE*/
  $AgendaFavorisFond=$CalNavigationFond;                   /*AGENDA FAVORIS TITRE FOND*/
  $AgendaFavorisGroupesBords=$AgendaPopUpBords;            /*AGENDA FAVORIS BORDURES FOND avec transparence*/
  $AgendaFavorisGroupesFondImage=$AgendaPopUpFondImage;    /*AGENDA FAVORIS GROUPE FOND - REMPLACE L'IMAGE DE FOND GENERALE, idem popup*/
  $AgendaCalepinGroupesBords=$AgendaPopUpBords;            /*AGENDA CALEPIN TITRE FOND avec transparence*/
  $AgendaCalepinGroupesFondImage=$AgendaPopUpFondImage;    /*AGENDA FAVORIS GROUPE FOND - REMPLACE L'IMAGE DE FOND GENERALE, idem poup*/
  $AujourdhuiStyle="";                                     /*CALENDRIER AUJOURDHUI STYLE*/
  $CalFondBandeauGauche="";                                /*CALENDRIER COULEUR DE FOND DU BANDEAU GAUCHE*/
  $CalMoisNavigationCelFond=$CalMoisNavigationFond;        /*CALENDRIER GAUCHE FOND MOIS EN COURS td bgcolor*/
  $CalFondQuitter="";                                      /*CALENDRIER FOND QUITTER*/
  $MenuLienAppli=$MenuOffLien;                             /*COULEUR LIEN PHENIX EN HAUT A GAUCHE*/
  $MenuLienAppliHover=$MenuOffLienHover;                   /*COULEUR LIEN ACTIF PHENIX EN HAUT A GAUCHE*/
  $MenuLienForum=$MenuOffLien;                             /*COULEUR LIEN FORUM EN HAUT A GAUCHE*/
  $MenuLienForumHover=$MenuOffLienHover;                   /*COULEUR LIEN ACTIF FORUM EN HAUT A GAUCHE*/
  $AgendaFondEnteteTableau=$CalNavigationFond;             /*COULEUR DE FOND DES LIGNES D'ENTETE DE TABLEAUX*/
  $AgendaTexteEnteteTableau=$CalNavigationTexte;           /*COULEUR DU TEXTE DES LIGNES D'ENTETE DE TABLEAUX*/
  $AgendaTexteInfoTimezone="#000000";                      /*COULEUR DU TEXTE DU FUSEAU HORAIRE EN BAS DE PAGE*/
  $ListeChoixFond=$CalFond;                                /*LISTE DEROULANTE HTML COULEUR DE FOND*/
  $ListeChoixSelection=$CalJourSelection;                  /*LISTE DEROULANTE HTML COULEUR DE LA SELECTION*/
  $ListeChoixSurvol=$CalJourFerie;                         /*LISTE DEROULANTE HTML COULEUR DU SURVOL*/
  $ListeChoixDefaut=$bgColor[1];                           /*LISTE DEROULANTE HTML COULEUR DE LA VALEUR PAR DEFAUT*/
  $CalFondBordures=$CalAgendaSeparation;                   /*BORDURES DE LA SELECTION DANS LE CALENDRIER GAUCHE - SEMAINE/MOIS*/
  $CalTitreSemainesColor = $AgendaTexte;                   /*CALENDRIER COULEUR TEXTE "SEMAINES de xxxx" */
  $CalMemoFavorisTexte=$AgendaLien;                        /*CALENDRIER TITRE MEMO FAVORIS*/
  $CalMemoFavorisTexteHover=$AgendaLienHover;              /*CALENDRIER TITRE MEMO FAVORIS HOVER*/
  $CalFavorisGroupeColor=$AgendaTexte;						         /*CALENDRIER TEXTE FAVORIS NOM GROUPE*/
  $AgendaTitreJoursColor=$AgendaLien;                      /*AGENDA COULEUR DU TEXTE DES JOURS/SEMAINES PLANNINGS*/
  $AgendaTitreJoursColorHover=$AgendaLienHover;            /*AGENDA COULEUR DU TEXTE (HOVER) DES JOURS/SEMAINES PLANNINGS*/
  $AgendaFlecheColor=$CalNavigationLien;
  $AgendaFlecheColorHover=$CalNavigationLienHover;
  $AgendaBordLegende=$AgendaBordureTableau;                /*BORDURE DE LA LEGENDE DES BOUTONS DE L'AGENDA*/

// ------------------------------------------
// CSS de la page d'accueil à modifier !!!!
// ------------------------------------------
  $AgendaProfilActifFondIndex=$CalJourCourant.";BORDER-LEFT:solid 1px ".$AgendaBordureTableau.";BORDER-RIGHT:solid 1px ".$AgendaBordureTableau.";BORDER-TOP:solid 1px ".$AgendaBordureTableau.";BORDER-BOTTOM:solid 1px ".$AgendaBordureTableau; /*AGENDA FOND MENU PROFIL ACTIF BACKGROUND-COLOR*/
  $AgendaProfilActifColorIndex=$AgendaBordureTableau;                       /*AGENDA FOND MENU PROFIL ACTIF COLOR*/
  $bgColorIndex="#FFFFFF";                                      /*FOND LIGNE2*/
  $AgendaBordureTableauIndex=$AgendaBordureTableau;
  $MenuCopyrightIndex=$AujourdhuiFond."; } TD.Copyright { COLOR:".$AujourdhuiFond."; FONT-SIZE: 9px; FONT-FAMILY: Verdana, Arial, Tahoma";
//Exemple  $AppliFondEcranIndex="#1C2F74";                               /*FOND PAGE*/
//Exemple  $AppliFondImageIndex="url(\"../skins/xxx/agenda.jpg\"); background-repeat:no-repeat;"; /*IMAGE FOND PAGE*/
//Exemple  $AgendaTexteIndex="#080808";                                  /*AGENDA TEXTE*/
//Exemple  $FormulaireFondInputIndex="#FFFFFF";                          /*FOND FORMULAIRE*/
//Exemple  $FormulaireTexteInputIndex="#717171";                         /*TEXTE FORMULAIRE*/
//Exemple  $FormulaireBordureInputIndex="#828282 solid 1px";             /*BORDURE FORMULAIRE*/
//Exemple  $FormulaireFondBoutonIndex="#5798A6";                         /*FOND BOUTON*/
//Exemple  $FormulaireTexteBoutonIndex="#3B3B3C";                        /*TEXTE BOUTON*/
//Exemple  $FormulaireBordureBoutonIndex="#828282 solid 1px";            /*BORDURE BOUTON*/
//Exemple  $FormulaireImageBoutonIndex="url(\"../skins/PxBIG-MacOS/bt_fond1.jpg\")";                           /*IMAGE FOND BOUTON*/
?>
