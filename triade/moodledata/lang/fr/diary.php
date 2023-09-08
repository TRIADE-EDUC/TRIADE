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
 * Strings for component 'diary', language 'fr', version '4.1'.
 *
 * @package     diary
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['accessdenied'] = 'Accès refusé';
$string['addtofeedback'] = 'Ajouter aux commentaires';
$string['alias'] = 'Mot-clef';
$string['aliases'] = 'Mot(s)-clef(s)';
$string['aliases_help'] = 'Chaque entrée du journal peut être associée à une liste de mots-clefs (ou d\'alias).

Saisir chaque mot-clef sur une nouvelle ligne (non séparée par une virgule).';
$string['alwaysopen'] = 'Toujours ouvert';
$string['alwaysshowdescription'] = 'Toujours afficher la description';
$string['alwaysshowdescription_help'] = 'Si ce réglage est désactivé, la description du journal ci-dessus ne sera visible aux étudiants qu\'à la date d\'ouverture.';
$string['and'] = 'et';
$string['attachment'] = 'Pièce jointe';
$string['attachment_help'] = 'Vous pouvez optionnellement joindre un ou plusieurs fichiers à une entrée de journal.';
$string['autorating'] = 'Évaluation automatique';
$string['autorating_descr'] = 'Si cette option est activée, l\'évaluation d\'une entrée sera automatiquement calculée en fonction des paramètres de comptage Min/Max.';
$string['autorating_help'] = 'Ce paramètre, ainsi que les comptages Min/Max, définissent les valeurs par défaut de l\'évaluation automatique dans tous les nouveaux journaux.';
$string['autorating_title'] = 'Activer l\'évaluation automatique';
$string['autoratingbelowmaxitemdetails'] = 'L\'évaluation automatique requiert {$a->one} ou plus {$a->two} avec une pénalité possible de {$a->three}% pour chaque élément manquant. <br>Vous en avez {$a->four}. Vous devez en trouver {$a->cinq}. La pénalité possible est de {$a->six} points.';
$string['autoratingitempenaltymath'] = 'Le calcul automatique de la pénalité de l\'évaluation de l\'élément est (max({$a->one} - {$a->two}, 0)) * {$a->three} = {$a->four}.<br>Remarque : le Max empêche les nombres négatifs causés par le fait d\'en avoir plus que ce qui est nécessaire.';
$string['autoratingitempercentset'] = 'Paramètres de pourcentage de l\'évaluation automatique  : {$a}%';
$string['autoratingovermaxitemdetails'] = 'Le nombre maximal d\'évaluations automatiques est {$a->one} {$a->two} avec une pénalité possible de {$a->three}% pour chaque évaluation supplémentaire. Vous avez {$a->four} évaluations, ce qui est {$a->five} de trop. La pénalité possible est de {$a->six} points.';
$string['availabilityhdr'] = 'Disponibilité';
$string['avgsylperword'] = 'Nombre moyen de syllables par mot {$a}';
$string['avgwordlenchar'] = 'Longueur moyenne des mots : {$a} caractères';
$string['avgwordpara'] = 'Nombre moyen de mots par paragraphe : {$a}';
$string['blankentry'] = 'Entrée vide';
$string['calendarend'] = '{$a} ferme';
$string['calendarstart'] = '{$a} ouvre';
$string['cancel'] = 'Annuler le transfert';
$string['chars'] = 'Caractères';
$string['charspersentence'] = 'Caractères par phrase';
$string['clearfeedback'] = 'Effacer le commentaire';
$string['commonerrorpercentset'] = 'Réglage d\'erreurs fréquentes en pourcentage : {$a}%.';
$string['commonerrors'] = 'Erreurs fréquentes';
$string['commonerrors_help'] = 'Les erreurs fréquentes sont définies dans le "Glossaire des erreurs" associé à cette question.';
$string['configdateformat'] = 'Cette option définit la façon dont les dates s\'affichent dans les rapports de journal. La valeur par défaut, « M d, Y G:i » , correspond au mois, au jour, à l\'année et à l\'heure (format 24 heures). Référez-vous à la section Date du manuel PHP pour plus d\'exemples et de constantes de date prédéfinies.';
$string['created'] = 'Créé il y a {$a->one} jours et {$a->two} heures.';
$string['crontask'] = 'Traitement de fond pour le module Journal';
$string['csvexport'] = 'Exporter en format .csv';
$string['currententry'] = 'Entrées actuelles du journal :';
$string['currpotrating'] = 'Votre évaluation potentielle actuelle est de : {$a->one} points, ou {$a->two}%.';
$string['dateformat'] = 'Format de date par défaut';
$string['daysavailable'] = 'Journées disponibles';
$string['daysavailable_help'] = 'Si vous utilisez le format hebdomadaire, vous pouvez définir le nombre de jours pendant lesquels le Journal peut être utilisé.';
$string['deadline'] = 'Journées ouvertes';
$string['details'] = 'Détails :';
$string['detectcommonerror'] = 'Au moins {$a->one}, {$a->two} détectés.  Ils sont {$a->three}.<br>Si permis, vous devriez corriger et soumettre à nouveau.';
$string['diary:addentries'] = 'Ajouter des entrées au Journal';
$string['diary:addinstance'] = 'Ajouter des instances de Journal';
$string['diary:manageentries'] = 'Gérer les entrées du Journal';
$string['diary:rate'] = 'Évaluer les entrées du Journal';
$string['diaryclosetime'] = 'Heure de fermeture';
$string['diaryclosetime_help'] = 'Si cette option est activée, vous pouvez définir une date à laquelle le Journal sera fermé et ne pourra plus être modifié.';
$string['diarydescription'] = 'Description du Journal';
$string['diaryentrydate'] = 'Définir une date pour cette entrée';
$string['diaryid'] = 'diaryid à transférer vers';
$string['diarymail'] = 'Bonjour {$a->user},
{$a->teacher} a publié un commentaire associé à votre entrée de Journal pour « {$a->diary} ».

Vous pouvez le consulter dans votre journal :

    {$a->url}';
$string['diarymailhtml'] = 'Bonjour {$a->user},
{$a->teacher} a publié un commentaire associé à votre entrée de Journal pour « <i>{$a->diary}</i> ».<br /><br />Vous pouvez le consulter dans votre journal : <a href="{$a->url}">entrée du Journal</a>.';
$string['diaryname'] = 'Nom du Journal';
$string['diaryopentime'] = 'Heure d\'ouverture';
$string['diaryopentime_help'] = 'Si cette option est activée, vous pouvez déterminer une date à partir de laquelle le Journal doit être ouvert pour être utilisé.';
$string['editall'] = 'Modifier toutes les entrées';
$string['editall_help'] = 'Si l\'option est activée, les utilisateurs peuvent modifier n\'importe quelle entrée.';
$string['editdates'] = 'Modifier les dates des entrées';
$string['editdates_help'] = 'Si l\'option est activée, les utilisateurs peuvent modifier la date de n\'importe quelle entrée.';
$string['editingended'] = 'La période d\'édition est terminée';
$string['editingends'] = 'Fin de la période d\'édition';
$string['editthisentry'] = 'Modifier cette entrée';
$string['edittopoflist'] = 'Modifier le haut de la liste';
$string['enableautorating'] = 'Activer l\'évaluation automatique';
$string['enableautorating_help'] = 'Activer ou désactiver les évaluations automatiques';
$string['enablestats'] = 'Activer les statistiques';
$string['enablestats_descr'] = 'Si l\'option est activée, les statistiques pour chacune des entrées seront affichées.';
$string['enablestats_help'] = 'Activer ou désactiver l\'affichage des statistiques pour chacune des entrées.';
$string['enablestats_title'] = 'Activer les statistiques';
$string['entries'] = 'Entrées';
$string['entry'] = 'Entrée';
$string['entrybgc'] = 'Couleur d\'arrière-plan des entrées et des commentaires dans le Journal';
$string['entrybgc_descr'] = 'Cette option définit la couleur d\'arrière-plan d\'une entrée ou d\'un commentaire dans le Journal.';
$string['entrybgc_help'] = 'Cette option permet de définir la couleur d\'arrière-plan pour chaque entrée du Journal ainsi que pour les commentaires.';
$string['entrybgc_title'] = 'Couleur d\'arrière-plan des entrées et des commentaires dans le Journal';
$string['entrycomment'] = 'Commentaires sur l\'entrée';
$string['entrytextbgc'] = 'Couleur d\'arrière-plan du texte du Journal';
$string['entrytextbgc_descr'] = 'Cette option définit la couleur d\'arrière-plan du texte dans une entrée de Journal.';
$string['entrytextbgc_help'] = 'Cette option définit la couleur de fond du texte dans une entrée de journal.';
$string['entrytextbgc_title'] = 'Couleur d\'arrière-plan du texte du Journal';
$string['errorbehavior'] = 'Comportement en cas d\'erreur';
$string['errorbehavior_help'] = 'Ces options affinent le comportement de correspondance pour les entrées du glossaire des erreurs fréquentes.';
$string['errorcmid'] = 'Glossaire des erreurs';
$string['errorcmid_help'] = 'Choisir le glossaire qui contient une liste des erreurs fréquentes. Chaque fois que l\'une de ces erreurs est trouvée dans la réponse soumise, la pénalité spécifiée sera déduite de l\'évaluation de l\'étudiant pour cette entrée.';
$string['errorpercent'] = 'Pénalité pour chaque erreur';
$string['errorpercent_help'] = 'Sélectionner le pourcentage de la note totale à déduire pour chaque erreur trouvée dans la réponse.';
$string['eventdiarycreated'] = 'Journal créé';
$string['eventdiarydeleted'] = 'Journal supprimé';
$string['eventdiaryviewed'] = 'Journal consulté';
$string['eventdownloadentriess'] = 'Télécharger les entrées';
$string['evententriesviewed'] = 'Consultation des entrées du Journal';
$string['evententrycreated'] = 'Création d\'une entrée de Journal';
$string['evententryupdated'] = 'Mise à jour de l\'entrée du Journal';
$string['eventfeedbackupdated'] = 'Mise à jour des commentaires du Journal';
$string['eventinvalidentryattempt'] = 'Tentative de saisie invalide dans le Journal';
$string['eventxfrentries'] = 'Transfert d\'une entrée de Journal à un Journal de bord';
$string['feedbackupdated'] = 'Commentaires mis à jour pour les entrées {$a}';
$string['files'] = 'Fichiers';
$string['firstentry'] = 'Premières entrées du Journal :';
$string['fkgrade'] = 'Note FK';
$string['fkgrade_help'] = 'Le niveau de lisibilité Flesch-Kincaid indique le nombre d\'années d\'études généralement nécessaires pour comprendre ce texte. Essayez de viser un niveau inférieur à 10.';
$string['fogindex'] = 'Indice de lisibilité';
$string['fogindex_help'] = 'L\'indice de Gunning est une mesure de la lisibilité. Elle est calculée à l\'aide de la formule suivante.

((mots par phrase) + (mots longs par phrase)) x 0,4

Essayez de viser un niveau inférieur à 10. Pour plus d\'informations, voir : <https://fr.wikipedia.org/wiki/Indice_de_lisibilit%C3%A9_de_Gunning>';
$string['freadingease'] = 'Facilité de lecture de Flesch';
$string['freadingease_help'] = 'Facilité de lecture de Flesch : un score élevé indique que votre texte est plus facile à lire, tandis qu\'un score plus faible indique que votre texte est plus difficile à lire. Essayez de viser une facilité de lecture supérieure à 60.';
$string['generalerror'] = 'Il y a eu une erreur.';
$string['generalerrorinsert'] = 'Impossible d\'insérer une nouvelle entrée dans le Journal.';
$string['generalerrorupdate'] = 'Impossible de mettre à jour votre Journal.';
$string['gradeingradebook'] = 'Évaluation actuelle dans le tableau des notes.';
$string['highestgradeentry'] = 'Les entrées les mieux notées :';
$string['incorrectcourseid'] = 'L\'ID du cours est incorrect';
$string['incorrectmodule'] = 'L\'ID du module de cours est incorrect';
$string['invalidaccess'] = 'Accès non valide';
$string['invalidaccessexp'] = 'Vous n\'avez pas la permission de voir la page à laquelle vous avez tenté d\'accéder. La tentative d\'accès a été enregistrée.';
$string['invalidtimechange'] = 'Une tentative non valide de modifier l\'heure de cette entrée a été détectée.';
$string['invalidtimechangenewtime'] = 'L\'heure modifiée était : {$a->one}.';
$string['invalidtimechangeoriginal'] = 'L\'heure originale était : {$a->one}.';
$string['invalidtimeresettime'] = 'L\'heure a été réinitialisée à l\'heure originale de : {$a->one}.';
$string['journalid'] = 'L\'ID du Journal à transférer';
$string['journalmissing'] = 'Actuellement, il n\'y a pas d\'activité Journal dans ce cours.';
$string['journaltodiaryxfrdid'] = '<br>Voici une liste de toutes les activités Journal dans ce cours.<br><b> ID</b> | Cours | Nom du Journal<br>';
$string['journaltodiaryxfrjid'] = 'Voici une liste de toutes les activités Journal dans ce cours.<br><b>   ID</b> | Cours | Nom du Journal<br>';
$string['journaltodiaryxfrp1'] = 'Il s\'agit d\'une fonction réservée aux administrateurs permettant de transférer les entrées du Journal vers les entrées du Journal de bord. Les entrées de plusieurs journaux peuvent être transférées dans un seul Journal ou dans plusieurs journaux distincts. Il s\'agit d\'une nouvelle fonctionnalité encore en cours de développement.<br><br>';
$string['journaltodiaryxfrp2'] = 'Si vous cochez la case <b>Transférer et envoyer un courriel</b>, toute entrée de Journal transférée vers une activité de Journal de bord marquera la nouvelle entrée comme nécessitant l\'envoi d\'un courriel à l\'utilisateur afin qu\'il sache que l\'entrée a été transférée.<br><br>';
$string['journaltodiaryxfrp3'] = 'Si vous utilisez le bouton <b>Transférer sans courriel</b>, il n\'y aura PAS de courriel envoyé à chaque utilisateur, même si le processus ajoute automatiquement le retour d\'information dans la nouvelle entrée du Journal et que l\'entrée originale du Journal ne comportait pas de retour d\'information.<br><br>';
$string['journaltodiaryxfrp4'] = 'Le titre du cours dans lequel vous travaillez est : <b> {$a->one}</b>, avec un ID de cours de : <b> {$a->two}</b><br><br>';
$string['journaltodiaryxfrp5'] = 'Si vous choisissez d\'inclure un retour d\'information concernant le transfert et que l\'écriture de Journal n\'a pas déjà de retour d\'information, vous serez automatiquement ajouté comme enseignant pour l\'écriture afin d\'éviter une erreur.<br><br>.';
$string['journaltodiaryxfrtitle'] = 'Journal à Journal de bord xfr';
$string['lastnameasc'] = 'Nom de famille en ordre croissant :';
$string['lastnamedesc'] = 'Nom de famille en ordre décroissant :';
$string['latestmodifiedentry'] = 'Les entrées récemment modifiées :';
$string['lexicaldensity'] = 'Densité lexicale';
$string['lexicaldensity_help'] = 'La densité lexicale est un pourcentage calculé avec la formule suivante.

 100 x (nombre de mots uniques) / (nombre total de mots)

Ainsi, une rédaction dans laquelle de nombreux mots sont répétés a une faible densité lexicale, tandis qu\'une rédaction comportant de nombreux mots uniques a une densité lexicale élevée.';
$string['longwords'] = 'Mots longs uniques';
$string['longwords_help'] = 'Les mots longs sont des mots qui ont trois syllabes ou plus. Notez que l\'algorithme permettant de déterminer le nombre de syllabes ne donne que des résultats approximatifs.';
$string['longwordspersentence'] = 'Mots longs par phrase';
$string['lowestgradeentry'] = 'Les entrées les moins bien notées :';
$string['mailed'] = 'Envoyés';
$string['mailsubject'] = 'Commentaire du Journal';
$string['maxcharacterlimit'] = 'Nombre maximal de caractères';
$string['maxcharacterlimit_desc'] = 'Remarque : cette entrée peut accepter un <strong>maximum de {$a} caractères.</strong>';
$string['maxcharacterlimit_help'] = 'Si un nombre est saisi, l\'utilisateur doit utiliser moins de caractères que le nombre maximal indiqué.';
$string['maxparagraphlimit'] = 'Nombre maximum de paragraphes';
$string['maxparagraphlimit_desc'] = 'Remarque : cette entrée peut comporter un <strong>maximum de {$a} paragraphes.</strong>';
$string['maxparagraphlimit_help'] = 'Si un nombre est indiqué, l\'utilisateur doit utiliser moins de paragraphes que le nombre maximum indiqué.';
$string['maxpossrating'] = 'La note maximale possible pour cette entrée est de {$a} points.';
$string['maxsentencelimit'] = 'Nombre maximum de phrases';
$string['maxsentencelimit_desc'] = 'Remarque : cette entrée peut comporter un <strong>maximum de {$a} phrases.</strong>';
$string['maxsentencelimit_help'] = 'Si un nombre est indiqué, l\'utilisateur doit utiliser moins de phrases que le nombre maximum indiqué.';
$string['maxwordlimit'] = 'Nombre maximum de mots';
$string['maxwordlimit_desc'] = 'Remarque : cette entrée peut comporter un <strong>maximum de {$a} mots.</strong>';
$string['maxwordlimit_help'] = 'Si un nombre est indiqué, l\'utilisateur doit utiliser moins de mots que le nombre maximum indiqué.';
$string['mediumwords'] = 'Mots moyens uniques';
$string['mediumwords_help'] = 'Les mots moyens sont des mots qui comportent deux syllabes. Notez que l\'algorithme permettant de déterminer le nombre de syllabes ne donne que des résultats approximatifs.';
$string['mincharacterlimit'] = 'Nombre minimum de caractères';
$string['mincharacterlimit_desc'] = 'Remarque : cette entrée doit comporter un <strong>minimum de {$a} caractères.</strong>';
$string['mincharacterlimit_help'] = 'Si un nombre est indiqué, l\'utilisateur doit utiliser moins de caractères que le nombre minimum indiqué.';
$string['minmaxcharpercent'] = 'Pénalité en caractères par erreur de comptage Min/Max';
$string['minmaxcharpercent_help'] = 'Sélectionnez le pourcentage de la note totale qui doit être déduit pour chaque erreur de comptage de caractères Min/Max.';
$string['minmaxhdr'] = 'Comptage Min/Max';
$string['minmaxhdr_help'] = 'Ces options définissent les valeurs par défaut pour les nombres minimum et maximum de caractères et de mots dans tous les nouveaux Journaux personnels.';
$string['minmaxparapercent'] = 'Pénalité de paragraphe par erreur de comptage Min/Max';
$string['minmaxparapercent_help'] = 'Sélectionnez le pourcentage de la note totale qui doit être déduit pour chaque erreur de comptage de paragraphes Min/Max.';
$string['minmaxpercent'] = 'Pénalité par erreur de comptage Min/Max';
$string['minmaxpercent_help'] = 'Sélectionnez le pourcentage de la note totale qui doit être déduit pour chaque erreur de comptage Min/Max.';
$string['minmaxsentpercent'] = 'Pénalité par erreur de comptage des phrases Min/Max';
$string['minmaxsentpercent_help'] = 'Sélectionnez le pourcentage de la note totale qui doit être déduit pour chaque erreur de comptage de phrases Min/Max.';
$string['minmaxwordpercent'] = 'Pénalité par erreur de comptage des mots Min/Max';
$string['minmaxwordpercent_help'] = 'Sélectionnez le pourcentage de la note totale qui doit être déduit pour chaque erreur de comptage de mots Min/Max.';
$string['minparagraphlimit'] = 'Nombre minimum de paragraphes';
$string['minparagraphlimit_desc'] = 'Remarque : cette entrée doit comporter un <strong>minimum de {$a} paragraphes.</strong>';
$string['minparagraphlimit_help'] = 'Si un nombre est indiqué, l\'utilisateur doit ajouter plus de paragraphes que le nombre minimum indiqué.';
$string['minsentencelimit'] = 'Nombre minimum de phrases';
$string['minsentencelimit_desc'] = 'Remarque : cette entrée doit comporter un <strong>minimum de {$a} phrases.</strong>';
$string['minsentencelimit_help'] = 'Si un nombre est indiqué, l\'utilisateur doit ajouter plus de phrases que le nombre minimum indiqué.';
$string['minwordlimit'] = 'Nombre minimum de mots';
$string['minwordlimit_desc'] = 'Remarque : cette entrée doit comporter un <strong>minimum de {$a} mots.</strong>';
$string['minwordlimit_help'] = 'Si un nombre est indiqué, l\'utilisateur doit ajouter plus de mots que le nombre minimum indiqué.';
$string['missing'] = 'Manquant';
$string['modulename'] = 'Journal';
$string['modulename_help'] = 'L\'activité Journal de bord permet aux enseignants d\'obtenir les commentaires des étudiants sur une période donnée.';
$string['modulenameplural'] = 'Journaux';
$string['needsgrading'] = 'Cette entrée n\'a pas encore été commentée ou évaluée.';
$string['needsregrade'] = 'Cette entrée a été modifiée depuis que des commentaires ou une évaluation ont été donnés.';
$string['newdiaryentries'] = 'Nouvelles entrées dans le Journal';
$string['nextentry'] = 'Entrée suivante';
$string['nodeadline'] = 'Toujours ouvert';
$string['noentriesmanagers'] = 'Il n\'y a pas d\'enseignant';
$string['noentry'] = 'Pas d\'entrée';
$string['noratinggiven'] = 'Aucune note donnée';
$string['notextdetected'] = '<b>Aucun texte détecté!</b>';
$string['notopenuntil'] = 'Ce Journal ne sera pas disponible avant';
$string['notstarted'] = 'Vous n\'avez pas encore démarré ce Journal';
$string['numwordscln'] = '{$a->one} mots de texte propre utilisant {$a->two} caractères, SANS inclure {$a->three} espaces.';
$string['numwordsnew'] = 'Nouveau calcul : {$a->one} mots de texte brut utilisant {$a->two} caractères, dans {$a->three} phrases, dans {$a->four} paragraphes.';
$string['numwordsraw'] = '{$a->one} mots de texte brut utilisant {$a->two} caractères, dont {$a->three} espaces.';
$string['numwordsstd'] = '{$a->one} mots normalisés utilisant {$a->two} caractères, dont {$a->three} espaces.';
$string['outof'] = 'sur {$a} entrées.';
$string['overallrating'] = 'Évaluation globale';
$string['pagesize'] = 'Entrées par page';
$string['paragraphs'] = 'Paragraphes';
$string['percentofentryrating'] = '{$a}% de l\'évaluation de l\'entrée.';
$string['phrasecasesensitiveno'] = 'La correspondance est insensible à la casse.';
$string['phrasecasesensitiveyes'] = 'La correspondance est sensible à la casse.';
$string['phrasefullmatchno'] = 'Faites correspondre des mots entiers ou partiels.';
$string['phrasefullmatchyes'] = 'Faites correspondre des mots entiers seulement.';
$string['phraseignorebreaksno'] = 'Reconnaître les sauts de ligne.';
$string['phraseignorebreaksyes'] = 'Ignorer les sauts de ligne.';
$string['pluginadministration'] = 'Administration du module Journal';
$string['pluginname'] = 'Journal de bord';
$string['popoverhelp'] = 'cliquez pour plus d\'informations';
$string['potautoratingerrpen'] = 'Pénalité potentielle pour erreur d\'autoévaluation : {$a->one}% ou {$a->two} points en moins.';
$string['potcommerrpen'] = 'Pénalité pour erreur fréquente potentielle : {$a->one} * {$a->two} = {$a->three}% ou {$a->four} points de moins.';
$string['present'] = 'Présent';
$string['previousentry'] = 'Entrée précédente';
$string['rate'] = 'Évaluer';
$string['rating'] = 'Évaluation pour cette entrée';
$string['reload'] = 'Recharger et afficher de l\'entrée la plus récente à l\'entrée la plus ancienne du Journal.';
$string['removeentries'] = 'Supprimer toutes les entrées';
$string['removemessages'] = 'Supprimer toutes les entrées du Journal';
$string['reportsingle'] = 'Obtenir toutes les entrées du Journal de bord de cet utilisateur.';
$string['reportsingleallentries'] = 'Toutes les entrées du Journal pour cet utilisateur.';
$string['returnto'] = 'Retourner à {$a}';
$string['returntoreport'] = 'Retour à la page du rapport pour - {$a}';
$string['saveallfeedback'] = 'Sauvegarder tous mes commentaires';
$string['savesettings'] = 'Sauvegarder paramètres';
$string['search'] = 'Chercher';
$string['search:activity'] = 'Journal de bord - information sur l\'activité';
$string['search:entry'] = 'Journal - entrées';
$string['search:entrycomment'] = 'Journal - commentaire sur l\'entrée';
$string['selectentry'] = 'Sélectionner l\'entrée à noter';
$string['sentences'] = 'Phrases';
$string['sentencesperparagraph'] = 'Phrases par paragraphe';
$string['shortwords'] = 'Mots courts uniques';
$string['shortwords_help'] = 'Les mots courts sont des mots qui n\'ont qu\'une seule syllabe. Notez que l\'algorithme permettant de déterminer le nombre de syllabes ne donne que des résultats approximatifs.';
$string['shownone'] = 'Ne pas afficher';
$string['showoverview'] = 'Afficher l\'aperçu des Journaux dans mon site Moodle';
$string['showrecentactivity'] = 'Afficher l\'activité récente';
$string['showstudentsonly'] = 'Afficher les étudiants seulement';
$string['showteacherandstudents'] = 'Afficher l\'enseignant et les étudiants';
$string['showteachersonly'] = 'Afficher les enseignants seulement';
$string['showtextstats'] = 'Afficher les statistiques à propos du texte ?';
$string['showtextstats_help'] = 'Si cette option est activée, des statistiques sur le texte seront affichées.';
$string['showtostudentsonly'] = 'Oui, afficher aux étudiants seulement';
$string['showtoteachersandstudents'] = 'Oui, afficher aux enseignants et aux étudiants';
$string['showtoteachersonly'] = 'Oui, afficher aux enseignants seulement';
$string['sortcurrententry'] = 'De l\'entrée actuelle du Journal à la première entrée.';
$string['sortfirstentry'] = 'De la première à la dernière entrée du Journal.';
$string['sorthighestentry'] = 'De l\'entrée du Journal ayant la meilleure note à l\'entrée à celle ayant la note la plus faible.';
$string['sortlastentry'] = 'De la dernière entrée modifiée du Journal à la plus ancienne.';
$string['sortlowestentry'] = 'De l\'entrée ayant la note la plus faible à l\'entrée ayant la meilleure note.';
$string['sortoptions'] = 'Options de tri :';
$string['sortorder'] = 'L\'ordre de tri est :';
$string['startnewentry'] = 'Ajouter une nouvelle entrée';
$string['startoredit'] = 'Recommencer ou modifier l\'entrée d\'aujourd\'hui';
$string['statshdr'] = 'Statistiques sur le texte';
$string['statshdr_help'] = 'Ces paramètres définissent les valeurs par défaut des statistiques dans tous les nouveaux Journaux.';
$string['teacher'] = 'Enseignante ou enseignant';
$string['text'] = 'Texte';
$string['textstatitems'] = 'Éléments statistiques';
$string['textstatitems_help'] = 'Sélectionner les éléments que vous souhaitez voir apparaître dans les statistiques textuelles qui sont affichées sur une page de vue, une page de rapport et une page de rapport unique.';
$string['timecreated'] = 'Heure créée';
$string['timemarked'] = 'Heure indiquée';
$string['timemodified'] = 'Heure modifiée';
$string['toolbar'] = 'Barre d\'outils :';
$string['totalsyllables'] = 'Total des syllabes: {$a}';
$string['transfer'] = 'Transférer les entrées';
$string['transferwemail'] = 'Transférer et envoyer un courriel. <b>Par défaut : Ne pas envoyer de courriel</b>';
$string['transferwfb'] = 'Transférer et inclure un commentaire à propos du transfert. <b>Par défaut : Ne pas inclure de commentaire</b>';
$string['transferwfbmsg'] = '<br>Cette entrée a été transférée du Journal nommé : {$a}';
$string['transferwoe'] = 'Transférer sans courriel';
$string['uniquewords'] = 'Mots uniques';
$string['userid'] = 'ID d\'utilisateur';
$string['usertoolbar'] = 'Barre d\'outils de l\'utilisateur :';
$string['viewalldiaries'] = 'Afficher tous les Journaux du cours';
$string['viewallentries'] = 'Voir les entrées du Journal de {$a}';
$string['viewentries'] = 'Afficher toutes les entrées';
$string['words'] = 'Mots';
$string['wordspersentence'] = 'Mots par phrase';
$string['xfrresults'] = 'Il y a eu {$a->one} entrées traitées, et {$a->two} d\'entre elles ont été transférées.';
