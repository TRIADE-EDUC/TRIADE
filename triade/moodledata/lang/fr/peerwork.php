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
 * Strings for component 'peerwork', language 'fr', version '4.1'.
 *
 * @package     peerwork
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activitydate:closed'] = 'Fermé :';
$string['activitydate:closes'] = 'Dû :';
$string['activitydate:opened'] = 'Ouvert :';
$string['activitydate:opens'] = 'Ouvre :';
$string['addmorecriteria'] = 'Ajouter {no} plus de critères';
$string['addmorecriteriastep'] = 'Ajouter plus d\'incréments de critères';
$string['addmorecriteriastep_help'] = 'Le nombre de critères d\'évaluation à ajouter au formulaire lorsqu\'un enseignant clique sur le bouton pour ajouter d\'autres critères.';
$string['addsubmission'] = 'Ajouter un travail';
$string['allmemberssubmitted'] = 'Tous les membres du groupe ont remis un travail';
$string['allowlatesubmissions'] = 'Autoriser les dépôts tardifs';
$string['allowlatesubmissions_help'] = 'Si cette option est activée, les dépôts seront toujours autorisés après la date d\'échéance.<br />
<strong>A noter :</strong> une fois que la note du groupe a été enregistrée et que les notes finales ont été calculées, les rendus de l\'étudiant deviendront non modifiables ou verrouillés. Il s\'agit d\'arrêter la falsification de la note finale par les étudiants modifiant leurs notes par les pairs.';
$string['assessment'] = 'Évaluation';
$string['assessmentalreadygraded'] = 'Évaluation déjà évaluée';
$string['assessmentclosedfor'] = 'Évaluation fermée à partir du : {$a}';
$string['assessmentcriteria:description'] = 'Critère {no} description';
$string['assessmentcriteria:description_help'] = 'Utilisez ceci pour décrire de manière précise le but de ce critère';
$string['assessmentcriteria:header'] = 'Paramètres des critères d\'évaluation';
$string['assessmentcriteria:modgradetypescale'] = 'Likert';
$string['assessmentcriteria:nocriteria'] = 'Aucun critère n\'a été défini pour cette évaluation.';
$string['assessmentcriteria:scoretype'] = 'Type {no} d\'évaluation des critères';
$string['assessmentcriteria:scoretype_help'] = 'Choisissez le barême selon laquelle ce critère doit être évalué';
$string['assessmentcriteria:weight'] = 'Poids';
$string['assessmentcriteria:weight_help'] = 'TODO pas encore utilisé';
$string['assessmentnotopenyet'] = 'L\'évaluation n\'est pas encore ouverte.';
$string['assessmentopen'] = 'Évaluation ouverte';
$string['assignment'] = 'Évaluation';
$string['availablescales'] = 'Barêmes disponibles.';
$string['availablescales_help'] = 'Barêmes que cette calculatrice peut utiliser.';
$string['base'] = 'Calculatrice de base';
$string['calcmissing'] = 'La calculatrice utilisée pour appliquer la pondération EPP n\'est pas disponible. {$a}';
$string['calcmissinggraded'] = 'Remarque : la modification des paramètres de la calculatrice entraînera des changements dans les notes finales des étudiants.';
$string['calculatedgrade'] = 'Note calculée';
$string['calculatedgrade_help'] = 'Note avant l\'application de la pondération et des pénalités.';
$string['calculatedgrades'] = 'Notes calculées';
$string['calculator'] = 'Calculatrice';
$string['calculator_help'] = 'Méthode de calcul à utiliser.';
$string['calculatorplugins'] = 'Plugins de calculatrice';
$string['calculatortypes'] = 'Paramètres de la calculatrice';
$string['calculatorupdate'] = 'Mettre à jour la calculatrice';
$string['charactersremaining'] = '{$a} Caractères restants';
$string['clearallsubmissionsforallgroups'] = 'Effacer toutes les dépôts';
$string['clearsubmission'] = 'Effacer le dépôt';
$string['comment'] = 'Commentaire :';
$string['comments'] = 'Commentaires';
$string['comments_help'] = 'Commentaire obligatoire sur la raison de la révision. Cela ne sera pas visible pour les étudiants. Il sera enregistré dans les journaux.';
$string['completiongradedpeers'] = 'Exiger que les pairs soient évalués';
$string['completiongradedpeers_desc'] = 'Les étudiants doivent évaluer tous leurs pairs';
$string['completiongradedpeers_help'] = 'Lorsque cette option est activée, un étudiant doit évaluer tous ses pairs pour que cette exigence soit satisfaite.';
$string['confimrclearsubmission'] = 'Voulez-vous vraiment effacer le dépôt de ce groupe ? Cela supprimera les informations fournies par tous les étudiants.';
$string['confimrclearsubmissions'] = 'Voulez-vous vraiment effacer le dépôt pour tous les groupes ? Cela supprimera les informations fournies par tous les étudiants.';
$string['confirmationmailbody'] = 'Vous avez rendu une évaluation par les pairs {$a->url} à {$a->time}.
Fichier(s) joint(s) :
{$a->files}

Notes que vous avez saisies :
{$a->grades}';
$string['confirmationmailsubject'] = 'Dépôt d\'évaluation par les pairs pour {$a}';
$string['confirmeditgrade'] = 'Évaluer avant la date limite';
$string['confirmeditgradetxt'] = 'La date d\'échéance n\'est pas dépassée. Si vous évaluez maintenant, les étudiants ne pourront plus modifier les dépôts. Souhaitez-vous continuer ?';
$string['confirmlockeditingaware'] = 'Vous ne serez plus autorisé à apporter des modifications à votre dépôt et aux notes de vos pairs une fois qu\'ils auront été enregistrés. Êtes-vous sûr de vouloir continuer ?';
$string['confirmunlockeditinggrader'] = 'Les notes données par {$a} sont actuellement verrouillées. Souhaitez-vous les déverrouiller et autoriser cet étudiant à modifier l\'une de ses notes ou justifications ? Cela prend effet immédiatement.';
$string['confirmunlockeditingsubmission'] = 'La modification du dépôt est actuellement verrouillée. Souhaitez-vous le déverrouiller et permettre aux étudiants de mettre à jour le dépôt ? Cela prend effet immédiatement.';
$string['contibutionscore'] = 'Contribution';
$string['contibutionscore_help'] = 'Il s\'agit du score EPP qui est la contribution relative apportée par les membres du groupe';
$string['criteria'] = 'Critère';
$string['criterianum'] = 'Critère {$a}';
$string['critscale'] = 'Type de notation du critère';
$string['critscale_help'] = 'Le barème selon lequel les critères doivent être notés.';
$string['defaultcrit'] = 'Paramètres des critères par défaut (facultatif)';
$string['defaultcrit0'] = 'Texte par défaut - Critère 1';
$string['defaultcrit0_help'] = 'Le texte par défaut à utiliser pour le premier critère';
$string['defaultcrit1'] = 'Texte par défaut - Critère 2';
$string['defaultcrit1_help'] = 'Le texte par défaut à utiliser pour le deuxième critère';
$string['defaultcrit2'] = 'Texte par défaut - Critère 3';
$string['defaultcrit2_help'] = 'Le texte par défaut à utiliser pour le troisième critère';
$string['defaultcrit3'] = 'Texte par défaut - Critère 4';
$string['defaultcrit3_help'] = 'Le texte par défaut à utiliser pour le quatrième critère';
$string['defaultcrit4'] = 'Texte par défaut - Critère 5';
$string['defaultcrit4_help'] = 'Le texte par défaut à utiliser pour le cinquième critère';
$string['defaultcrit_desc'] = 'Valeurs par défaut pour jusqu\'à 5 critères et leur barème correspondant';
$string['defaultscale'] = 'Barème par défaut';
$string['defaultscale0'] = 'Barème par défaut - Critère 1';
$string['defaultscale0_help'] = 'Le barème par défaut à utiliser pour le premier critère.';
$string['defaultscale1'] = 'Barème par défaut - Critère 2';
$string['defaultscale1_help'] = 'Le barème par défaut à utiliser pour le deuxième critère.';
$string['defaultscale2'] = 'Barème par défaut - Critère 3';
$string['defaultscale2_help'] = 'Le barème par défaut à utiliser pour le troisième critère.';
$string['defaultscale3'] = 'Barème par défaut - Critère 4';
$string['defaultscale3_help'] = 'Le barème par défaut à utiliser pour le quatrième critère.';
$string['defaultscale4'] = 'Barème par défaut - Critère 5';
$string['defaultscale4_help'] = 'Le barème par défaut à utiliser pour le cinquième critère.';
$string['defaultscale_help'] = 'Le barème par défaut à utiliser pour tous les autres critères.';
$string['defaultsettings'] = 'Paramètres par défaut';
$string['defaultsettings_desc'] = 'Les valeurs à utiliser par défaut lors de l\'ajout d\'une nouvelle instance de ce module à un cours.';
$string['displaypeergradestotals'] = 'Afficher les totaux des notes des pairs';
$string['displaypeergradestotals_help'] = 'Lorsque cette option est activée, les étudiants verront le total de leurs notes par les pairs sous forme de pourcentage pour chaque critère. Notez que pour que le total soit affiché, les notes des pairs doivent être visibles.';
$string['downloadallsubmissions'] = 'Télécharger tous les dépôts';
$string['draftnotsubmitted'] = 'Brouillon (non déposé)';
$string['duedate'] = 'Date limite';
$string['duedate_help'] = 'C\'est à ce moment que l\'évaluation par les pairs est due. Les dépôts seront toujours autorisés après cette date (si activée).<br />
<strong>A noter :</strong> tous les dépôts de fichiers d\'étudiants et la notation par les pairs deviendront non modifiables pour les étudiants après la notation.';
$string['duedateat'] = 'Date limite : {$a}';
$string['duedatenotpassed'] = 'La date limite n\'est pas dépassée. Si vous notez maintenant, les étudiants ne pourront plus modifier les soumissions.';
$string['duedatepassedago'] = 'La date limite est dépassée de {$a}.';
$string['editablebecause'] = 'Modifiable car : {$a}';
$string['editgrade'] = 'Modifier la note du groupe : {$a}';
$string['editinglocked'] = 'L\'édition est verrouillée';
$string['editsubmission'] = 'Modifier le dépôt';
$string['eventassessable_submitted'] = 'déposer le travail de pair';
$string['eventgradebookupdatefailed'] = 'mise à jour du carnet de notes de travail de pair';
$string['eventgradesreleased'] = 'Publication des notes';
$string['eventpeer_feedback_created'] = 'travail de pair commentaires d\'un pair';
$string['eventpeer_grade_created'] = 'travail de pair note d\'un pair';
$string['eventpeer_grade_overridden'] = 'travail de pair note d\'un pair révisée';
$string['eventsubmission_created'] = 'travail de pair dépôt créé';
$string['eventsubmission_exported'] = 'travail de pair export';
$string['eventsubmission_files_deleted'] = 'travail de pair suppression du fichier';
$string['eventsubmission_files_uploaded'] = 'travail de pair téléchargement de fichiers';
$string['eventsubmission_grade_form_viewed'] = 'travail de pair visualiser la grille d\'évaluation';
$string['eventsubmission_graded'] = 'travail de pair note';
$string['eventsubmission_updated'] = 'travail de pair mise à jour du dépôt';
$string['eventsubmissioncleared'] = 'Dépôt effacé';
$string['eventsubmissions_exported'] = 'travail de pair tout exporter';
$string['eventsubmissionsdownloaded'] = 'Dépôts téléchargés';
$string['export'] = 'Exporter';
$string['exportxls'] = 'Exporter toutes les notes de groupe';
$string['feedback'] = 'Commentaires au groupe';
$string['feedbackfiles'] = 'Fichiers de commentaires';
$string['finalgrades'] = 'Notes finales';
$string['finalgrades_help'] = 'La note finale est calculée en additionnant ou en soustrayant la différence de moyenne de l\'individu/du groupe, multipliée par cinq. Le résultat dépend du fait que la moyenne de l\'individu est supérieure ou inférieure à la moyenne du groupe.';
$string['finalweightedgrade'] = 'Note finale pondérée';
$string['firstsubmittedbyon'] = 'Soumis pour la première fois par {$a->name} le {$a->date}.';
$string['fromdate'] = 'Autoriser les dépôts de';
$string['fromdate_help'] = 'Si cette option est activée, les étudiants ne pourront pas déposer leurs travaux avant cette date. Si elle est désactivée, les étudiants pourront commencer à déposer leurs travaux immédiatement.';
$string['grade'] = 'Note';
$string['gradebefore'] = 'Note avant révision : {$a}';
$string['gradecannotberemoved'] = 'La note ne peut pas être supprimée.';
$string['gradedby'] = 'Noté par';
$string['gradedbyon'] = 'Noté par {$a->name} le {$a->date}.';
$string['gradedon'] = 'Noté le';
$string['gradegivento'] = '<strong>Note pour</strong>';
$string['gradeoverridden'] = 'Note de pair révisée : {$a}';
$string['gradeoverride'] = 'Note finale';
$string['gradesandfeedbacksaved'] = 'Les notes et les commentaires ont été enregistrés.';
$string['gradesexistmsg'] = 'Certaines notes ayant déjà été validées, le type de calcul ne peut pas être modifié. Si vous souhaitez changer de calcul, vous devez d\'abord choisir de recalculer ou non les notes existantes.';
$string['gradesgivenby'] = '<h2>Notes données par {$a}</h2>';
$string['groupaverage'] = 'Groupe Note moyenne';
$string['groupaverage_help'] = 'Il s\'agit de la moyenne générale des notes des pairs pour le groupe.';
$string['groupgrade'] = 'Note du groupe';
$string['groupgradeoutof100'] = 'Note du groupe sur 100';
$string['groupsubmissionsettings'] = 'Paramètres de dépôt de groupe';
$string['groupsubmittedon'] = 'Groupe déposé le';
$string['hideshow'] = 'Cacher/Afficher';
$string['invalidgrade'] = 'Note non valide';
$string['invalidpaweighting'] = 'Pondération non valide';
$string['invalidscale'] = 'Barème non valide. Veuillez sélectionner l\'une des options ci-dessus.';
$string['justification'] = 'Justification';
$string['justification_help'] = 'Activer/désactiver les commentaires de justification et sélectionner la visibilité.';
$string['justificationbyfor'] = 'Par {$a} pour';
$string['justificationdisabled'] = 'Désactivé';
$string['justificationhiddenfromstudents'] = 'Caché aux étudiants';
$string['justificationintro'] = 'Ajoutez ci-dessous des commentaires justifiant les notes que vous avez attribuées à chacun de vos pairs.';
$string['justificationmaxlength'] = 'Limite de caractères pour la justification';
$string['justificationmaxlength_help'] = 'Nombre maximal de caractères autorisés dans les champs de justification. Vous pouvez fixer cette valeur à 0 pour supprimer la limite.';
$string['justificationnoteshidden'] = 'A noter : vos commentaires seront cachés à vos camarades et ne seront visibles que par les enseignants.';
$string['justificationnotesvisibleanon'] = 'A noter : vos commentaires seront visibles par vos pairs mais anonymes, votre nom d\'utilisateur n\'apparaîtra pas à côté des commentaires que vous laisserez.';
$string['justificationnotesvisibleuser'] = 'A noter : vos commentaires et votre nom d\'utilisateur seront visibles par vos pairs.';
$string['justifications'] = 'Justifications';
$string['justificationtype'] = 'Type de justification';
$string['justificationtype0'] = 'Pair';
$string['justificationtype1'] = 'Critère';
$string['justificationtype_help'] = 'La justification par pair nécessite un commentaire pour chaque pair. La justification par critère nécessite un commentaire pour chaque note de critère.';
$string['justificationvisibleanon'] = 'Visible anonyme';
$string['justificationvisibleuser'] = 'Visible avec nom d\'utilisateur';
$string['lasteditedon'] = 'Dernière modification le {$a->date}.';
$string['latesubmissionsallowedafterduedate'] = 'La date limite est dépassée, mais les dépôts tardifs sont autorisées.';
$string['latesubmissionsnotallowedafterduedate'] = 'La date limite est dépassée et les dépôts tardifs ne sont pas autorisés.';
$string['latesubmissionsubject'] = 'Dépôt tardif';
$string['latesubmissiontext'] = 'Le dépôt tardif a été soumis dans {$a->name} par {$a->user}.';
$string['lockediting'] = 'Verrouiller la modification';
$string['lockediting_help'] = 'Lorsque cette option est activée, les dépôts et les notes des pairs ne peuvent pas être modifiés une fois qu\'ils ont été déposés par un étudiant. Les enseignants peuvent débloquer la modification pour les étudiants individuels lorsque les dépôts sont autorisés.';
$string['managepeerworkcalculatorplugins'] = 'Gérer les plugins de calcul de peerwork';
$string['messageprovider:grade_released'] = 'Note et commentaires publiés';
$string['modulename'] = 'Évaluation par les pairs';
$string['modulename_help'] = 'L\'activité d\'Évaluation par les pairs est un travail de groupe combiné à une notation par les pairs.
Pour cette activité, la notation par les pairs fait référence à la capacité des étudiants à évaluer la performance/contribution de leur groupe de pairs et, si cela est possible, d\'eux-mêmes, par rapport à un travail de groupe. Le travail du groupe est la composante de l\'activité relative à la soumission de fichier(s). La notation par les pairs consiste en un choix de barèmes de notes et de commentaires écrits sur les performances de chaque étudiant.
Les notes globales finales de chaque étudiant sont ensuite calculées à l\'aide de la méthode de calcul sélectionnée.';
$string['modulenameplural'] = 'Évaluations par les pairs';
$string['multiplegroups'] = 'Les personnes suivantes appartiennent à plusieurs groupes : {$a}. Leurs notes n\'ont pas été mises à jour.';
$string['myfinalgrade'] = 'Ma note finale';
$string['nocalculator'] = 'Aucune calculatrice n\'est installée. Les étudiants recevront tous la note du groupe, sous réserve d\'une pénalité en cas d\'échec.';
$string['nomembers'] = '# membres';
$string['noncompletionpenalty'] = 'Sanction en cas de non transmission des notes';
$string['noncompletionpenalty_help'] = 'Si un étudiant n\'a fourni aucune note pour l\'évaluation (il n\'a pas évalué ses camarades), dans quelle mesure doit-il être pénalisé ?';
$string['none'] = 'Aucune.';
$string['nonegiven'] = 'Aucune donnée';
$string['nonereceived'] = 'Aucune reçue';
$string['nopeergrades'] = '# notes des pairs';
$string['noteditablebecause'] = 'Non modifiable car : {$a}';
$string['noteoverdueby'] = '(en retard de {$a})';
$string['nothingsubmitted'] = 'Rien n\'a encore été déposé.';
$string['nothingsubmittedyet'] = 'Rien n\'a encore été déposé.';
$string['nothingsubmittedyetduedatepassednago'] = 'Rien n\'a encore été déposé, mais la date limite est dépassée depuis {$a}.';
$string['notifygradesreleasedhtml'] = 'La note et les commentaires pour votre dépôt dans \'<em>{$a->name}</em>\' ont été publiés. Vous pouvez les consulter <a href="{$a->url}">ici</a>.';
$string['notifygradesreleasedsmall'] = 'Votre note pour \'{$a}\' a été publiée.';
$string['notifygradesreleasedtext'] = 'La note et les commentaires pour votre dépôt dans \'{$a->name}\' ont été publiés. Vous pouvez les consulter ici : {$a->url}';
$string['notyetgraded'] = 'Pas encore noté';
$string['numcrit'] = 'Nombre de critères par défaut';
$string['numcrit_help'] = 'Le nombre de critères à inclure par défaut. Il existe 5 chaînes de caractères par défaut';
$string['overridden'] = 'Révisé';
$string['override'] = 'Réviser';
$string['overridepeergrades'] = 'Réviser les notes des pairs.';
$string['overridepeergrades_help'] = 'Si cette option est activée, les enseignants pourront réviser les notes attribuées par les étudiants à leurs pairs.';
$string['overridepeergradesby'] = 'Réviser les notes des pairs données par :';
$string['paweighting'] = 'Pondération de l\'évaluation par les pairs';
$string['paweighting_help'] = 'Quel pourcentage de la note totale du groupe doit venir des pairs ?';
$string['peergrades'] = 'Notes des pairs';
$string['peergradeshiddenfromstudents'] = 'Caché aux étudiants';
$string['peergradesvisibility'] = 'Visibilité des notes des pairs';
$string['peergradesvisibility_help'] = 'Ce paramètre détermine si les étudiants peuvent voir les notes qu\'ils ont reçues de leurs pairs.

- Caché aux étudiants : Les étudiants ne verront pas du tout les notes de leurs camarades.
- Visible anonyme : les étudiants verront les notes de leurs camarades, mais pas les noms d\'utilisateur de ceux qui les ont notées.
- Visible avec nom d\'utilisateur : Les étudiants verront les notes de leurs pairs et les noms de ceux qui les ont notées.';
$string['peergradesvisibleanon'] = 'Visible anonyme';
$string['peergradesvisibleuser'] = 'Visible avec nom d\'utilisateur';
$string['peergradetotal'] = 'Total : {$a}';
$string['peernameisyou'] = '{$a} (vous)';
$string['peerratedyou'] = '{$a->name} : {$a->grade}';
$string['peers'] = 'Notez vos pairs';
$string['peersaid'] = '{$a} :';
$string['peersubmissionandgrades'] = 'Dépôts et notes des pairs';
$string['peerwork'] = 'Évaluation par les pairs';
$string['peerwork:addinstance'] = 'Ajouter une activité de peerwork';
$string['peerwork:grade'] = 'Noter les travaux et les notes des pairs';
$string['peerwork:view'] = 'Voir le contenu de l\'évaluation par les pairs';
$string['peerworkcalculatorpluginname'] = 'Plugin de calculatrice';
$string['peerworkfieldset'] = 'Paramètres d\'évaluation par les pairs';
$string['peerworkname'] = 'Évaluation par les pairs';
$string['peerworkname_help'] = '<strong>Description</strong><br /> Dans le champ de description, vous pouvez ajouter vos consignes d\'évaluation par les pairs. Nous vous conseillons d\'inclure tous les détails du travail (nombre de mots, nombre de fichiers et types de fichiers acceptés) ainsi que des conseils sur les critères d\'évaluation par les pairs (expliquer la fourchette et ce qu\'il faut vérifier). Vous pouvez également ajouter des liens vers le syllabus du module en référant aux directives d\'évaluation. Nous recommandons également d\'inclure des informations sur l\'aide disponible pour les étudiants qui auraient des difficultés à soumettre leur travail de groupe.';
$string['penalty'] = 'Pénalité';
$string['pleaseexplainoverride'] = 'Veuillez indiquer la raison pour laquelle vous avez décidé de réviser cette note.';
$string['pleaseproviderating'] = 'Veuillez attribuer une note à chacun de vos pairs.';
$string['pluginadministration'] = 'Gestion de l\'évaluation par les pairs';
$string['pluginname'] = 'Travail des pairs';
$string['privacy:metadata:core_files'] = 'Le plugin stocke les fichiers de dépôt et de commentaires.';
$string['privacy:metadata:grades'] = 'Informations sur les notes calculées et attribuées par les éducateurs';
$string['privacy:metadata:grades:grade'] = 'La note attribuée à l\'étudiant';
$string['privacy:metadata:grades:prelimgrade'] = 'La note calculée par WebPA avant l\'application de la pondération et des pénalités';
$string['privacy:metadata:grades:revisedgrade'] = 'La note révisée qui prévaut sur la note éventuelle';
$string['privacy:metadata:grades:userid'] = 'Identité de l\'utilisateur qui a fourni la justification';
$string['privacy:metadata:justification'] = 'La justification fournie par les étudiants pour la note attribuée à un pair';
$string['privacy:metadata:justification:gradedby'] = 'Identité de l\'utilisateur qui a fourni la justification';
$string['privacy:metadata:justification:gradefor'] = 'Identité de l\'utilisateur qui a reçu la justification';
$string['privacy:metadata:justification:justification'] = 'La justification fournie';
$string['privacy:metadata:peers'] = 'Informations sur les notes attribuées par les pairs et le commentaire fourni';
$string['privacy:metadata:peers:comments'] = 'Les commentaires faits sur la note par l\'utilisateur qui l\'a annulée';
$string['privacy:metadata:peers:feedback'] = 'Le commentaire donné à un membre du groupe par un pair du groupe';
$string['privacy:metadata:peers:grade'] = 'La note finale attribuée à un membre du groupe par un pair du groupe.';
$string['privacy:metadata:peers:gradedby'] = 'Identité de l\'utilisateur qui a noté un pair';
$string['privacy:metadata:peers:gradefor'] = 'Identité de l\'utilisateur qui a été noté par un pair';
$string['privacy:metadata:peers:overriddenby'] = 'L\'utilisateur qui a révisé la note initiale attribuée par un pair';
$string['privacy:metadata:peers:peergrade'] = 'La note initiale attribuée à un membre du groupe par un pair du groupe.';
$string['privacy:metadata:peers:timecreated'] = 'L\'heure à laquelle la note a été transmise';
$string['privacy:metadata:peers:timemodified'] = 'L\'heure à laquelle la note a été mise à jour';
$string['privacy:metadata:peers:timeoverridden'] = 'Heure à laquelle la note du pair a été révisée';
$string['privacy:metadata:submission'] = 'Informations sur les dépôts de groupe effectués';
$string['privacy:metadata:submission:feedbacktext'] = 'Les commentaires donnés au groupe par l\'évaluateur';
$string['privacy:metadata:submission:grade'] = 'La note attribuée par l\'évaluateur au travail du groupe';
$string['privacy:metadata:submission:gradedby'] = 'Identité de l\'utilisateur qui a noté le travail';
$string['privacy:metadata:submission:groupid'] = 'Identité du groupe à l\'origine de ce travail';
$string['privacy:metadata:submission:paweighting'] = 'La pondération WebPA utilisée par l\'évaluateur pour ce travail';
$string['privacy:metadata:submission:released'] = 'L\'heure à laquelle les notes ont été publiées';
$string['privacy:metadata:submission:releasedby'] = 'Identité de l\'utilisateur qui a publié les notes';
$string['privacy:metadata:submission:timecreated'] = 'L\'heure à laquelle le travail a été déposé';
$string['privacy:metadata:submission:timegraded'] = 'L\'heure à laquelle le travail a été évalué';
$string['privacy:metadata:submission:timemodified'] = 'Si le dépôt a été modifié, l\'heure à laquelle le dépôt a été modifié';
$string['privacy:metadata:submission:userid'] = 'Identité de l\'utilisateur qui a créé le dépôt';
$string['privacy:path:grade'] = 'Note';
$string['privacy:path:peergrades'] = 'Notes des pairs';
$string['privacy:path:submission'] = 'Dépôt';
$string['provideajustification'] = 'Veuillez fournir une justification.';
$string['provideminimumonecriterion'] = 'Veuillez fournir au moins un critère.';
$string['ratingnforuser'] = 'Classement \'{$a->rating}\' pour {$a->user}';
$string['recalculategrades'] = 'Recalculer les notes';
$string['recalculategrades_help'] = 'Les notes ont été publiées. Vous ne pouvez changer de calculatrice que si vous acceptez que toutes les notes soient recalculées.';
$string['releaseallgradesforallgroups'] = 'Publier toutes les notes pour tous les groupes';
$string['releasedby'] = 'Publié par';
$string['releasedbyon'] = 'Notes publiées par {$a->name} le {$a->date}';
$string['releasedon'] = 'Publiée le';
$string['releasegrades'] = 'Publier les notes';
$string['requirejustification'] = 'Exiger une justification';
$string['requirejustification_help'] = '- Désactivé : Les étudiants ne seront pas tenus d\'ajouter des commentaires justifiant les notes attribuées à chacun de leurs pairs.
- Caché aux étudiants : Tous les commentaires laissés par les étudiants seront visibles uniquement par les enseignants et cachés à leurs camarades.
- Visible anonyme : Les commentaires laissés par les étudiants seront visibles par leurs pairs, mais l\'identité des personnes ayant laissé des commentaires sera cachée.
- Visible avec nom d\'utilisateur : Tous les commentaires laissés par les étudiants seront visibles par leurs pairs avec l\'identité de ceux qui les ont laissés.';
$string['revisedgrade'] = 'Note révisée';
$string['revisedgrade_help'] = 'Utilisez ce champ pour réviser la note finale pondérée, si nécessaire. Toutefois, si la note a été révisée ou verrouillée dans le carnet de notes, elle ne peut pas être modifiée.';
$string['search:activity'] = 'Travail des pairs - information sur l\'activité';
$string['selfgrading'] = 'Permettre aux étudiants de s\'auto-évaluer en même temps que leurs pairs';
$string['selfgrading_help'] = 'Si cette option est activée, les étudiants pourront s\'attribuer une note et un retour d\'information par leurs pairs, ainsi que par les autres membres de leur groupe. Ces notes seront alors prises en compte dans leur moyenne et dans celle de l\'ensemble du groupe.';
$string['setup.maxfiles'] = 'Nombre maximum de fichiers téléchargés';
$string['setup.maxfiles_help'] = 'Le nombre maximum de fichiers que le groupe pourra télécharger pour sa soumission. <br/>Si zéro, les étudiants n\'auront pas la possibilité de télécharger des fichiers.';
$string['studentcalculatedgrade'] = 'Note calculée par l\'étudiant';
$string['studentcontribution'] = 'Contribution de l\'étudiant';
$string['studentfinalgrade'] = 'Note finale de l\'étudiant';
$string['studentfinalweightedgrade'] = 'Note finale pondérée de l\'étudiant';
$string['studentondate'] = '{$a->fullname} le {$a->date}';
$string['studentrevisedgrade'] = 'Note révisée de l\'étudiant';
$string['submission'] = 'Dépôt(s)';
$string['submission_help'] = 'Fichier(s) déposé(s) par le groupe. <strong>Note :</strong> Le nombre maximum de fichiers peut être ajusté dans les paramètres de l\'évaluation par les pairs.';
$string['submissiongrading'] = 'Dépôt de fichier';
$string['submissiongrading_help'] = 'Fichier(s) déposé(s) par le groupe. <strong>Note :</strong> Le nombre maximum de fichiers peut être ajusté dans les paramètres de l\'évaluation par les pairs.';
$string['submissionstatus'] = 'Status de dépôt';
$string['subplugintype_peerworkcalculator'] = 'Méthode de la calculatrice de notation';
$string['subplugintype_peerworkcalculator_plural'] = 'Méthodes de la calculatrice de notation';
$string['tasknodifystudents'] = 'Informer les étudiants';
$string['teacherfeedback'] = 'Commentaires de l\'évaluateur';
$string['teacherfeedback_help'] = 'Il s\'agit du commentaire donné par l\'évaluateur.';
$string['thesestudentspastduedate'] = 'Ces étudiants ont remis leur travail après la date limite : {$a}.';
$string['timeremaining'] = 'Temps restant';
$string['timeremainingcolon'] = 'Temps restant : {$a}';
$string['tutorgrading'] = 'Notation par les tuteurs';
$string['userswhodidnotsubmitafter'] = 'Utilisateurs qui n\'ont rien déposé : {$a}';
$string['userswhodidnotsubmitbefore'] = 'Les utilisateurs qui doivent encore déposer : {$a}';
$string['youbelongtomorethanonegroup'] = 'Vous appartenez à plus d\'un groupe, ceci n\'est pas disponible pour l\'instant.';
$string['youdonotbelongtoanygroup'] = 'Vous n\'appartenez à aucun groupe.';
$string['youwereawardedthesepeergrades'] = 'Pour ce critère, vos pairs vous ont attribué les notes suivantes.';
