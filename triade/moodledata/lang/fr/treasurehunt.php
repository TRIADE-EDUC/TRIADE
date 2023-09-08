<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'treasurehunt', language 'fr', version '4.1'.
 *
 * @package     treasurehunt
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activitytoend'] = 'Complétez d\'abord l\'activité sélectionnée';
$string['activitytoend_help'] = 'L\'activité sélectionnée doit-être complétée avant que l\'indice puisse être affiché.
Pour que les activités du cours soit affichées dans la liste cela doit-être activé dans le suivi d’achèvement dans la configuration de Moodle, dans le cours et dans l\'activité elle-même.';
$string['activitytoendovercome'] = 'Activité "<strong>{$a}</strong>" surmontée';
$string['activitytoendwarning'] = 'Vous devez d\'abord compléter l\'activité à résoudre';
$string['actnotavailableyet'] = 'L\'activité n\'est pas accessible pour l\'instant';
$string['add'] = 'Ajouter';
$string['addingroad'] = 'Ajouter un trajet';
$string['addingstage'] = 'Ajouter une étape';
$string['addroad_tour'] = 'Ajoutez une ou plusieurs route à suivre par vos étudiants';
$string['addsimplequestion'] = 'Ajouter une question simple';
$string['addsimplequestion_help'] = 'Ajouter une question simple avant d\'afficher l\'indice de cette étape';
$string['addstage_tour'] = 'Chaque route doit avoir deux étapes ou plus. Chaque étape donne un indice pour trouver la suivante';
$string['aerialmap'] = 'Vue aérienne';
$string['aerialview'] = 'Vue aérienne';
$string['allowattemptsfromdate'] = 'Autoriser les tentatives à partir de';
$string['allowattemptsfromdate_help'] = 'Si activé les étudiants ne pourront pas participer avant cette date.
Si désactivé, les étudiants pourront participer dès maintenant.';
$string['alwaysshowdescription'] = 'Toujours montrer la description';
$string['alwaysshowdescription_help'] = 'Si désactivé, la description ci-dessus deviendra visible aux étudiants seulement à la date « Autoriser les tentatives à partir de »';
$string['answerwarning'] = 'Vous devez d\'abord répondre à la question';
$string['areyousure'] = 'Voulez-vous vraiment continuer ?';
$string['attempt'] = 'Tentative';
$string['attemptsdeleted'] = 'Tentative de Chasse au Trésor effacée';
$string['autolocate_tour'] = 'En jouant, vous pouvez vous géolocaliser en utilisant le GPS de votre appareil avec ce bouton. Merci d\'autoriser l\'utilisation de votre localisation quand demandé.';
$string['availability'] = 'Disponibilité';
$string['back'] = 'Retour';
$string['backtocourse'] = 'Retour au cours';
$string['basemaps'] = 'Cartes de base';
$string['cancel'] = 'Annuler';
$string['changecamera'] = 'Changer la caméra';
$string['changetogroupmode'] = 'Le mode de jeu a été changé en jeu en groupe';
$string['changetoindividualmode'] = 'Le mode de jeu a été changé en jeu individuel';
$string['changetoplaywithmove'] = 'Le mode de jeu a été changé en jeu dynamique';
$string['changetoplaywithoutmoving'] = 'Le mode de jeu a été changé en jeu statique';
$string['cleartreasurehunt'] = 'Redémarrer la Chasse au Trésor';
$string['cleartreasurehunt_done'] = 'L\'activité a été réinitialisée. Toutes les activités des participants ont été supprimées.';
$string['cleartreasurehuntconfirm'] = 'Attention, toute activité enregistrée sera effacée si vous continuez. Normalement cette action n\'est nécessaire que si vous souhaitez changer le nombre de route ou d\'étapes mais que l\'activité est bloquée car quelqu\'un à commencé une partie.';
$string['completionfinish'] = 'Nécessaire pour finir la route';
$string['completionfinish_help'] = 'Complété quand l\'utilisateur passe toutes les étapes d\'une route.';
$string['configintro'] = 'Les valeurs que vous entrez ici définissent les valeurs par défaut qui sont utilisées dans le formulaire quand vous créez une nouvelle chasse au trésor.';
$string['configmaximumgrade'] = 'La note par défaut sur laquelle la chasse au trésor sera évaluées.';
$string['confirm'] = 'Confirmer';
$string['confirmdeletestage'] = 'L\'étape a été bien été supprimée';
$string['continue'] = 'Continuer';
$string['correctanswer'] = 'Réponse correcte.';
$string['customlayername'] = 'Titre de la couche';
$string['customlayername_help'] = 'Si vous utilisez une couche personnalisée, un titre est nécessaire pour l\'afficher sur la carte ou à vos utilisateurs. Si le titre est vide, la couche personnalisée sera désactivée.';
$string['customlayertype'] = 'Type de couche';
$string['customlayertype_help'] = 'La couche peut être la seule image visible en fond ou peut être positionnée par dessus la carte de base.';
$string['customlayerwms'] = 'Service WMS';
$string['customlayerwms_help'] = 'Utilisez une carte provenant d\'un service OGC WMS. (Par exemple EUNIS Forest Ecosystems WMS peut être configuré de cette manière : WMS:<code style="overflow-wrap: break-word;word-wrap: break-word;">http://bio.discomap.eea.europa.eu/arcgis/services/Ecosystem/Ecosystems/MapServer/WMSServer</code> PARAMS: <code>LAYERS=4</code>)';
$string['custommapbaselayer'] = 'Cette image est montrée comme une option de carte de fond ADDITIONNELLE';
$string['custommapimagefile'] = 'Image personnalisée pour la carte';
$string['custommapimagefile_help'] = 'Déposez une image de résolution suffisante et remplissez les 4 champs suivants avec les les coordonnées de projection au sol';
$string['custommapmaxlat'] = 'Latitude nord';
$string['custommapmaxlat_help'] = 'Latitude nord de l\'image. Utilisez « . » comme séparateur décimal. Inférieur à 85 degrés et supérieur à la latitude sud.';
$string['custommapmaxlon'] = 'Longitude est';
$string['custommapmaxlon_help'] = 'Longitude est de l\'image. Utilisez « . » comme séparateur décimal. Inférieur à 180 degrés et supérieur à la longitude ouest.';
$string['custommapminlat'] = 'Latitude sud';
$string['custommapminlat_help'] = 'Latitude sud de l\'image. Utilisez « . » comme séparateur décimal. Supérieur à -85 degrés et inférieur à la latitude nord.';
$string['custommapminlon'] = 'Longitude ouest';
$string['custommapminlon_help'] = 'Longitude ouest de l\'image. Utilisez « . » comme séparateur décimal. Supérieur à -180 degrés et inférieur à la longitude est.';
$string['custommapnongeographic'] = 'L\'image n\'est pas géographique';
$string['custommaponlybaselayer'] = 'L\'image est montrée comme la SEULE option de carte de fond';
$string['custommapoverlaylayer'] = 'L\'image est présentée par dessus la carte standard';
$string['custommapping'] = 'Carte personnalisée';
$string['customwmsparams'] = 'Paramètres WMS';
$string['customwmsparams_help'] = 'Ces paramètres définissent l\'apparence de la carte. Le format est défini comme ceci : "LAYERS=background,streets;STYLES=blue,default" (Par exemple EUNIS Forest Ecosystems WMS peut être configuré de cette manière : WMS: <code style="overflow-wrap: break-word;word-wrap: break-word;">http://bio.discomap.eea.europa.eu/arcgis/services/Ecosystem/Ecosystems/MapServer/WMSServer</code> PARAMS: <code>LAYERS=4</code>)';
$string['cutoffdate'] = 'Date limite';
$string['cutoffdate_help'] = 'Si défini, la chasse au trésor n\'acceptera plus de tentatives après cette date sans extension.';
$string['cutoffdatefromdatevalidation'] = 'La date limite doit être postérieure au champ autoriser la soumission à partir de.';
$string['discoveredlocation'] = 'Localisation découverte';
$string['donetutorial'] = 'Fin';
$string['editactivity_help'] = 'Vous pouvez trouver un tutoriel étape-par-étape sur la création d\'activités Chasse au Trésor sur <a href="http://juacas.github.io/moodle-mod_treasurehunt/create_activity.html">cette page.</a>';
$string['editend_tour'] = 'Amusez-vous en créant des jeux pour vos étudiants :';
$string['editingroad'] = 'Modification de la route';
$string['editingstage'] = 'Modification de l\'étape';
$string['editingtreasurehunt'] = 'Modification de la chasse au trésor';
$string['edition'] = 'Panneau de modification';
$string['edition_help'] = 'Pour activer la création de zone et les boutons dans le panneau de modification, l\'étape que vous voulez modifier doit être sélectionnée';
$string['editroad'] = 'Modifier la route';
$string['editstage'] = 'Modifier l\'étape';
$string['edittreasurehunt'] = 'Modifier les routes et les étapes';
$string['errcorrectanswers'] = 'Vous devez sélectionner une réponse correcte';
$string['errcorrectsetanswerblank'] = 'La réponse correcte est définie, mais la réponse est vide';
$string['erremptystage'] = 'Toutes les étapes doivent avoir au moins une zone pour que la route soit valide';
$string['errnocorrectanswers'] = 'Il ne doit y avoir qu\'une seule réponse correcte';
$string['errnumeric'] = 'Vous devez entrer un nombre décimal valide';
$string['error'] = 'Erreur';
$string['errpenalizationexceed'] = 'La pénélité ne peut pas être supérieure à 100';
$string['errpenalizationfall'] = 'La pénalité ne peut pas être inférieure à 0';
$string['errsendinganswer'] = 'La route a été mise à jour pendant que vous envoyiez la réponse, veuillez réessayer';
$string['errsendinglocation'] = 'La route a été mise à jour pendant que vous envoyiez la localisation, veuillez réessayer';
$string['errvalidroad'] = 'Il doit y avoir au moins deux étapes ayant chacune au moins une zone assignée pour que la route soit valide';
$string['eventattemptsubmitted'] = 'Tentative soumise';
$string['eventattemptsucceded'] = 'Étape passée';
$string['eventhuntsucceded'] = 'Chasse au trésor réussie';
$string['eventplayerentered'] = 'Le joueur a commencé';
$string['eventroadcreated'] = 'Route créée';
$string['eventroaddeleted'] = 'Route éffacée';
$string['eventroadupdated'] = 'Route mise à jour';
$string['eventstagecreated'] = 'Étape créée';
$string['eventstagedeleted'] = 'Étape effacée';
$string['eventstageupdated'] = 'Étape mise à jour';
$string['exit'] = 'Retour au cours';
$string['failedlocation'] = 'Erreur de localisation';
$string['faillocation'] = 'Ce n\'est pas le bon encdroit';
$string['gamemodeinfo'] = 'Mode de jeu : {$a}';
$string['gameupdatetime'] = 'Heure de mise à jour du jeu';
$string['gameupdatetime_help'] = 'Intervalle de temps en secondes entre deux mises à jour du jeu pour un utilisateur.
Plus celui-ci est élevé, le moins il y aura de requêtes de mises à jours, mais plus de temps passe avant le signalement d\'un possible changement.
Il doit être supérieur à 0 secondes, mais un temps est défini par défaut.';
$string['geolocation_needed'] = 'Pour participer à ce jeu votre géolocalisation est nécessaire.
<p>Pour l\'activer allez dans les paramétrages du navigateur->paramétrages du site->Localisation et retirez l\'interdiction pour ce site.
<p>Merci de recharger cette page et de répondre « Oui » quand votre navigateur demande si vous souhaitez partager votre localisation.
<p>Afin de pouvoir utiliser la localisation sur cet appareil durant le chasse au trésor, le serveur doit être contacté via HTTPS.
Autrement, utilisez uniquement le mode « Jouer sans bouger ». Les joueurs devront sélectionner manuellement chaque étape sur la carte.
Veuillez contacter l\'administrateur du site si des problèmes persistent.';
$string['geolocation_needed_title'] = 'Cette application à besoin de la géolocalisation';
$string['grade_explaination_fromabsolutetime'] = '{$a->rawscore}-{$a->penalization}% : Vous avez terminé la chasse en {$a->yourtime}. Le meilleur temps était de {$a->besttime}. Vous êtes pénalisé de {$a->penalization}% à cause de {$a->nolocationsfailed} mauvais emplacements, et {$a->noanswersfailed} mauvaises réponses.';
$string['grade_explaination_fromposition'] = '{$a->rawscore}-{$a->penalization}% : Vous avez découvert {$a->nosuccessfulstages} étapes en position {$a->position}. Vous êtes pénalisé de {$a->penalization}% à cause de {$a->nolocationsfailed} mauvais emplacements, et {$a->noanswersfailed} mauvaises réponses.';
$string['grade_explaination_fromstages'] = 'étapes{$a->rawscore}-{$a->penalization}% : Vous avez découvert {$a->nosuccessfulstages} sur {$a->nostages} stages. Vous êtes pénalisé de {$a->penalization}% à cause de {$a->nolocationsfailed} mauvais emplacements, et {$a->noanswersfailed} mauvaises réponses.';
$string['grade_explaination_fromtime'] = '{$a->rawscore}-{$a->penalization}% : Il vous fallait {$a->yourtime} pour compléter la chasse. Le meilleur temps était {$a->besttime}.Vous êtes pénalisé de {$a->penalization}% à cause de {$a->nolocationsfailed} mauvais emplacements, et {$a->noanswersfailed} mauvaises réponses.';
$string['grade_explaination_temporary'] = 'Chasse non finie, vous recevez 50% du score des étapes : {$a->rawscore}-{$a->penalization}% : Vous avez découvert {$a->nosuccessfulstages} sur {$a->nostages} étapes. Vous êtes pénalisé de {$a->penalization}% du fait de {$a->nolocationsfailed} mauvais positionnements, et {$a->noanswersfailed} mauvaises réponses.';
$string['gradefromabsolutetime'] = 'Note pour la durée de la chasse';
$string['gradefromposition'] = 'Note pour la position';
$string['gradefromstages'] = 'Note pour les étapes';
$string['gradefromtime'] = 'Note pour temps de parcours';
$string['grademethod'] = 'Méthode de notation';
$string['grademethodinfo'] = 'Méthode de notation : {$a->type}. Pénalisation de localisation : {$a->gradepenlocation}%. Pénalisation de réponse : {$a->gradepenanswer}%';
$string['gradepenanswer'] = 'Pénalité pour erreur dans la réponse';
$string['gradepenlocation'] = 'Pénalité pour erreur dans la localisation';
$string['gradepenlocation_help'] = 'La pénalisation est exprimée en % de la note.
Par exemple, si la pénalisation est 5.4, un joueur avec 3 erreurs aura une pénalité sur sa note de 16.2%, et donc recevra 83.8% de la note calculée sur le reste des critères.';
$string['gradesdeleted'] = 'Notes de la Chasse au Trésor effacées';
$string['gradingsummary'] = 'Résumé des notes';
$string['group'] = 'Groupe';
$string['groupactivityovercome'] = 'Activité à terminer complétée correctement par {$a->user} pour l\'étape {$a->position} à la date : {$a->date}';
$string['groupid'] = 'Groupe assigné à la route';
$string['groupid_help'] = 'Les utilisateurs de ce groupe sont assignés à cette route quand le jeu démarre.
Si il y a une seule route et l\'option sélectionnée est « aucun », tous les participants dans l\'activité participeront';
$string['groupingid'] = 'Groupement assigné à la route';
$string['groupingid_help'] = 'Groupes dans ce groupement assignés à cette route quand le jeu démarre';
$string['groupinvalidroad'] = '{$a} a assigné une route invalide.';
$string['grouplocationfailed'] = 'Mauvaise position de {$a->user} pour l\'étape {$a->position} à la date : {$a->date}';
$string['grouplocationovercome'] = 'Bonne position de {$a->user} pour l\'étape {$a->position} à la date : {$a->date}';
$string['groupmode'] = 'Les étudiants jouent en groupe';
$string['groupmode_help'] = 'Si activé les étudiants seront séparés en groupes d\'après la configuration des groupes du cours.
Un jeu en groupe sera partagés entre les membres des groupes et ils verront les changements dans le jeu.';
$string['groupmultipleroads'] = '{$a} a plus d\'une route assignée';
$string['groupquestionfailed'] = 'Mauvaise réponse de {$a->user} à la question de l\'étape {$a->position} à la date : {$a->date}';
$string['groupquestionovercome'] = 'Bonne réponse de {$a->user} à la question de l\'étape {$a->position} à la date : {$a->date}';
$string['groups'] = 'Groupes';
$string['groupstageovercome'] = 'Étape {$a->position} réussie par {$a->user} à la date : {$a->date}';
$string['hello'] = 'Bonjour';
$string['history'] = 'Historique';
$string['huntcompleted'] = 'Vous avez déjà complété cette chasse au trésor';
$string['incorrectanswer'] = 'Réponse incorrecte.';
$string['info'] = 'Information';
$string['infovalidatelocation'] = 'Validez la localisation de cette étape';
$string['invalidassignedroad'] = 'La route assignée n\'est pas validée';
$string['invalroadid'] = 'La route n\'est pas validée';
$string['lastsuccessfulstage_tour'] = 'Dans ce panneau vous pouvez voir votre dernière étape correcte. Il peut s\'agir de la votre ou de celle de votre groupe.';
$string['layers'] = 'Couches';
$string['loading'] = 'Chargement';
$string['lockedaclue'] = 'Vous devez faire l\'activité \'<strong>{$a}</strong>\' pour débloquer l\'indice';
$string['lockedaqclue'] = 'Vous devez faire l\'activité \'<strong>{$a}</strong>\' et répondre correctement à la question suivante pour débloquer l\'indice';
$string['lockedclue'] = 'Indice vérouillé';
$string['lockedqclue'] = 'Vous devez répondre correctement à la question suivante pour débloquer l\'indice';
$string['mapview'] = 'voir la carte';
$string['modify'] = 'Modifier';
$string['modulename'] = 'Chasse au Trésor';
$string['modulenameplural'] = 'Chasses au Trésor';
$string['multiplegroupingsplay'] = 'Votre groupe a plus d\'une route assignée, vous ne pouvez pas faire l\'activité.';
$string['multiplegroupsplay'] = 'Vous avez plus d\'une route assignée, impossible de faire l\'activité.';
$string['multiplegroupssameroadplay'] = 'Vous appartenez à plus d\'un groupe assigné à la même route, impossible de faire l\'activité.';
$string['multipleteamsplay'] = 'Membre de plus d\'un groupe, impossible de faire l\'activité.';
$string['mustanswerquestion'] = 'Vous devez répondre correctement à la question pour continuer';
$string['mustcompleteactivity'] = 'Vous devez compléter l\'activité à finir avant de continuer';
$string['mustcompleteboth'] = 'Vous devez répondre correctement à la question et compléter l\'activité à finir avant de continuer';
$string['nextstep'] = 'Suivant';
$string['noanswerselected'] = 'Vous devez sélectionner une réponse';
$string['noattempts'] = 'Vous n\'avez effectué aucune tentative';
$string['noexsitsstage'] = 'Il n\'y a pas de numéro d\'étape {$a} dans la base de donnée. Rechargez la page';
$string['nogroupassigned'] = 'Pas de groupe assigné à cette route';
$string['nogroupingplay'] = 'Vous n\'avez aucun groupe assigné à une route, vous ne pouvez donc pas participer à cette activité';
$string['nogroupplay'] = 'Vous n\'avez aucune route assignée, vous ne pouvez donc pas participer à cette activité';
$string['nogrouproad'] = '{$a} n\'a aucune route assignée.';
$string['nomarks'] = 'D';
$string['noresults'] = 'Aucun résultat trouvé';
$string['noroads'] = 'Aucune route n\'a été ajoutée pour l\'instant';
$string['notchangeorderstage'] = 'Vous ne pouvez pas changer l\'ordre des étapes une fois que des tentatives ont été effectuées pour la route';
$string['notcreatestage'] = 'Des tentatives ont été effectuées pour cette route, vous ne pouvez pas ajouter d\'étape.';
$string['notdeletestage'] = 'Des tentatives ont été effectuées pour cette route, vous ne pouvez pas supprimer d\'étape.';
$string['noteam'] = 'Membre d\'aucun groupe';
$string['notreasurehunts'] = 'Il n\'y a pas de chasse au trésor dans ce cours';
$string['nouserassigned'] = 'Aucun utilisateur assigné à cette route';
$string['nouserattempts'] = '{$a} n\'a fait aucune tentative';
$string['nouserroad'] = '{$a} n\'a aucune route assignée';
$string['nousersprogress'] = 'Aucun utilisateur / groupe n\'a de progression pour cette route';
$string['outoftime'] = 'Temps écoulé';
$string['overcomefirststage'] = 'Pour découvrir la première étape vous devez démarrer depuis la zone signalée sur la carte';
$string['play'] = 'Jouer';
$string['playstagewithoutmoving'] = 'Voir l\'étape sans bouger';
$string['playstagewithqr'] = 'Vois l\'étape en scannant ce texte QR';
$string['playwithoutmoving'] = 'Jouer sans bouger';
$string['pluginadministration'] = 'Administration de la chasse au trésor';
$string['pluginname'] = 'Chasse au Trésor';
$string['prevstep'] = 'Précédent';
$string['question'] = 'Question';
$string['remove'] = 'Supprimer';
$string['remove_tour'] = 'Vous pouvez effacer des parties des zones géométriques. Sélectionnez juste un polygone et cliquez sur ce bouton.';
$string['removealltreasurehuntattempts'] = 'Effacer toutes les tentatives de chasses au trésor';
$string['removedactivitytoend'] = 'L\'activité à compléter a été supprimée';
$string['removedquestion'] = 'La question a été supprimée';
$string['removeroadwarning'] = 'Si vous supprimez cette route, toutes les étapes associées seront supprimées sans possibilité de récupération';
$string['removewarning'] = 'Après suppression il n\'y aura pas de possibilité de récupération';
$string['restrictionsdiscoverstage'] = 'Restrictions pour découvrir l\'étape';
$string['reviewofplay'] = 'Revoir le jeu';
$string['road'] = 'Route';
$string['roadended'] = 'Cette route est finie. Félicitations ! Vous avez complété la chasse au trésor. Vous pouvez regarder votre historique sur la carte.';
$string['roadmap'] = 'Route';
$string['roadname'] = 'Nom de la route';
$string['roads_tour'] = 'Dans cette zone vous trouverez les différentes routes de votre jeu. Sélectionnez l\'une d\'entre elles pour modifier les étapes.';
$string['roadview'] = 'Route';
$string['save'] = 'Sauvegarder';
$string['save_tour'] = 'Après avoir dessiné vos localisations, n\'oubliez pas d\'enregistrer vos changements.';
$string['saveemptyridle'] = 'Toutes les étapes doivent avoir des zones indiquées avant d\'enregistrer';
$string['savewarning'] = 'Vous n\'avez pas enregistré les changements';
$string['scanQR_generatebutton'] = 'Générer un QRCode';
$string['scanQR_scanbutton'] = 'Scanner un QRCode';
$string['search'] = 'Rechercher';
$string['searching'] = 'Recherche';
$string['searchlocation'] = 'Rechercher la localisation';
$string['searchlocation_tour'] = 'Avec cette zone de recherche vous pouvez trouver votre chemin rapidement';
$string['send'] = 'Envoyer';
$string['skiptutorial'] = 'Quitter';
$string['stage'] = 'Étape';
$string['stageclue'] = 'Indice pour localiser la prochaine étape';
$string['stageclue_help'] = 'Décrivez ici l\'indice pour atteindre la prochaine destination.
Au cas où ce serait la dernière étape, ajoutez un message indiquant que la chasse au trésor est finie.';
$string['stagename'] = 'Nom de l\'étape';
$string['stageovercome'] = 'Étape accomplie';
$string['stages'] = 'Étapes';
$string['stages_tour'] = 'Dans cette zone vous trouverez les étapes de la route sélectionnée. Sélectionnez chaque étape pour zoomer sur les étapes sur la carte.';
$string['start'] = 'Commencer';
$string['startfromhere'] = 'Vous pouvez seulement commencer ici';
$string['state'] = 'État';
$string['successlocation'] = 'C\'est le bon endroit !';
$string['timeexceeded'] = 'Vous avez dépassé la limite de temps pour l\'activité. Cet écran sert uniquement à revoir le jeu';
$string['treasurehunt'] = 'Chasse au trésor';
$string['treasurehunt:addinstance'] = 'Ajouter une nouvelle chasse au trésor';
$string['treasurehunt:addroad'] = 'Ajouter une route';
$string['treasurehunt:addstage'] = 'Ajouter une étape';
$string['treasurehunt:editroad'] = 'Modifier la route';
$string['treasurehunt:editstage'] = 'Modifier l\'étape';
$string['treasurehunt:managetreasure'] = 'Gérer chasse au trésor';
$string['treasurehunt:managetreasurehunt'] = 'Gérer chasse au trésor';
$string['treasurehunt:play'] = 'Jouer';
$string['treasurehunt:view'] = 'Voir la chasse au trésir';
$string['treasurehunt:viewusershistoricalattempts'] = 'Voir l\'historique des tentatives utilisateurs';
$string['treasurehuntclosed'] = 'Cette chasse au trésor ferme à {$a}';
$string['treasurehuntcloses'] = 'La chasse au trésor se termine';
$string['treasurehuntcloseson'] = 'Cette chasse au trésor fermera à {$a}';
$string['treasurehuntislocked'] = '{$a} modifie cette chasse au trésor actuellement. Essayez de la modifier à nouveau dans quelques minutes';
$string['treasurehuntname'] = 'Nom de la chasse au trésor';
$string['treasurehuntnotavailable'] = 'La chasse au trésor ne sera pas disponible jusqu\'à {$a}';
$string['treasurehuntopenedon'] = 'Cette chasse au trésor a ouvert à {$a}';
$string['treasurehuntopens'] = 'La chasse au trésor ouvre';
$string['updates'] = 'Mises à jour';
$string['updatetimes'] = 'Temps de mise à jour';
$string['user'] = 'Utilisateur';
$string['useractivityovercome'] = 'Activité à finir complétée pour l\'étape {$a->position} à la date {$a->date}';
$string['userinvalidroad'] = '{$a} a assigné une route invalide';
$string['usermultipleroads'] = '{$a} a plus d\'une route assignée';
$string['usermultiplesameroad'] = '{$a} appartient à plusieurs groupes assignés à la même route.';
$string['userprogress'] = 'le progrès de l\'utilisateur a été mis à jour correctement';
$string['usersprogress'] = 'Progrès utilisateur';
$string['userstageovercome'] = 'Étape {$a->position} atteinte à la date du : {$a->date}';
$string['validatelocation'] = 'Valider la localisation';
$string['validatelocation_tour'] = 'Quand vous pensez avoir atteint la localisation d\'une étape, veuillez soumettre votre position pour vérifier si vous avez raison.';
$string['validateqr'] = 'Scanner le QR';
$string['warmatchanswer'] = 'La réponse ne correspond pas à la question';
$string['warnusersgroup'] = 'L\'utilisateur suivant appartient à plus d\'un groupe : {$a}, il ne peut donc pas participer à l\'activité';
$string['warnusersgrouping'] = 'Le groupe suivant appartient à plus d\'un groupement :{$a}, il ne peut donc pas participer à l\'activité';
