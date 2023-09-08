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
 * Strings for component 'qtype_recordrtc', language 'fr', version '4.1'.
 *
 * @package     qtype_recordrtc
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['audio'] = 'Audio seul';
$string['audiobitrate'] = 'Débit audio';
$string['audiobitrate_desc'] = 'Qualité de l\'enregistrement audio en bit/seconde (un nombre plus grand signifie une meilleure qualité)';
$string['audiotimelimit'] = 'Durée maximale de l\'enregistrement audio';
$string['audiotimelimit_desc'] = 'Durée maximale que l\'auteur de la question peut définir pour la durée d’enregistrement audio.';
$string['avplaceholder'] = 'Emplacement de l\'audio/vidéo';
$string['customav'] = 'Audio/vidéo personnalisé';
$string['downloadrecording'] = 'Télécharger {$a}';
$string['err_audiotimelimit'] = 'La durée maximale de l\'enregistrement ne peut pas être supérieure à {$a} secondes (paramètre « Durée maximale d’enregistrement audio »).';
$string['err_closesquarebrackets'] = 'Il manque un ou plusieurs crochets fermant. {$a->format}';
$string['err_opensquarebrackets'] = 'Il manque un ou plusieurs crochets ouvrant. {$a->format}';
$string['err_placeholderformat'] = 'Le format de la chaîne de remplacement est [[title:audio]], où \'title\' est une chaîne de caractères (qui peut contenir des lettres, des traits d\'union et des caractères de soulignement).';
$string['err_placeholderincorrectformat'] = 'Le format de la chaîne de remplacement du média n\'est pas correct. {$a->format}';
$string['err_placeholdermediatype'] = '"{$a->text}" n\'est pas un type de média valide. Pour le type de média, utiliser la chaîne "audio". {$a->format}';
$string['err_placeholdermissingduration'] = '{$a} durée manquante. Saisir la durée requise dans le format correct ou supprimez le dernier « : » pour tenir compte de la durée par défaut du système pour cette question.';
$string['err_placeholderneeded'] = 'Vous devez ajouter au moins un emplacement au texte de la question.';
$string['err_placeholdertitle'] = '"{$a->text}" n\'est pas un titre valide. Utiliser une chaîne de caractères appropriée. {$a->format}';
$string['err_placeholdertitlecase'] = '"{$a->text}" n\'est pas un titre valide. Les titres ne peuvent contenir que des lettres minuscules. {$a->format}';
$string['err_placeholdertitleduplicate'] = '"{$a->text}" a déjà été utilisé. Chaque titre doit être unique.';
$string['err_placeholdertitlelength'] = 'Le titre "{$a->text}" dépasse la longueur maximale de {$a->maxlength} caractères. {$a->format}';
$string['err_timelimit'] = 'La durée maximale d\'enregistrement ne peut être supérieure à {$a}.';
$string['err_timelimitpositive'] = 'La durée maximale d\'enregistrement doit être supérieure à 0.';
$string['err_videotimelimit'] = 'La durée maximale de l\'enregistrement ne peut pas être supérieure à {$a} secondes (paramètre « Durée maximale d’enregistrement vidéo »).';
$string['err_zeroornegativetimelimit'] = '« {$a} » n’est pas valide. La durée maximale de l\'enregistrement doit être supérieure à 0.';
$string['filex'] = 'Fichier {$a}';
$string['gumabort'] = 'Il s\'est passé quelque chose d\'étrange qui a empêché l\'utilisation de la webcam et/ou du microphone.';
$string['gumabort_title'] = 'Il s\'est passé quelque chose...';
$string['gumnotallowed'] = 'L\'utilisateur doit autoriser le navigateur à accéder à la webcam/microphone';
$string['gumnotallowed_title'] = 'Autorisations erronées';
$string['gumnotfound'] = 'Il n\'y a pas de dispositif d\'entrée connecté ou activé';
$string['gumnotfound_title'] = 'Périphérique manquant';
$string['gumnotreadable'] = 'Quelque chose empêche le navigateur d\'accéder à la webcam et/ou au microphone';
$string['gumnotreadable_title'] = 'Erreur matérielle';
$string['gumnotsupported'] = 'Votre navigateur ne prend pas en charge l\'enregistrement sur une connexion non sécurisée et doit fermer le greffon';
$string['gumnotsupported_title'] = 'Pas de prise en charge des connexions non sécurisées';
$string['gumoverconstrained'] = 'La webcam et le microphone ne peuvent pas produire un flux d\'enregistrement avec les paramètres actuels';
$string['gumoverconstrained_title'] = 'Problème avec les paramètres';
$string['gumsecurity'] = 'Votre navigateur ne prend pas en charge l\'enregistrement sur une connexion non sécurisée et doit fermer le greffon';
$string['gumsecurity_title'] = 'Pas de prise en charge des connexions non sécurisées';
$string['gumtype'] = 'Impossible d\'obtenir le flux de la webcam/microphone, car aucun paramètre n\'a été spécifié';
$string['gumtype_title'] = 'Aucun paramètre spécifié';
$string['insecurewarning'] = 'Votre navigateur ne permettra pas à ce greffon de fonctionner s\'il n\'est pas utilisé avec une connexion sécurisée (HTTPS).';
$string['insecurewarningtitle'] = 'Connexion non sécurisée';
$string['mediatype'] = 'Type d\'enregistrement';
$string['mediatype_help'] = 'L\'élève est-il invité à enregistrer de l\'audio ou de la vidéo ?';
$string['nearingmaxsize'] = 'Vous avez atteint la limite de taille maximale pour le téléversement de fichiers';
$string['nearingmaxsize_title'] = 'Enregistrement stoppé';
$string['norecording'] = 'Pas d\'enregistrement';
$string['nowebrtc'] = 'Votre navigateur ne prend pas en charge les technologies WebRTC, ou alors de manière limitée, et ne peut pas être utilisé pour ce type de questions. Veuillez changer de navigateur ou le mettre à jour.';
$string['nowebrtctitle'] = 'WebRTC n\'est pas supporté';
$string['optionsforaudio'] = 'Options audio';
$string['optionsforaudioandvideo'] = 'Options audio et vidéo';
$string['optionsforvideo'] = 'Options vidéo';
$string['pause'] = 'Pause';
$string['pleaserecordsomethingineachpart'] = 'Veuillez compléter votre réponse';
$string['pluginname'] = 'Enregistrement audio/vidéo';
$string['pluginname_help'] = 'Les élèves répondent aux questions en enregistrant leur voix directement dans le navigateur web, et peuvent ensuite être notés manuellement.';
$string['pluginname_link'] = 'question/type/recordrtc';
$string['pluginnameadding'] = 'Ajouter une question audio/vidéo';
$string['pluginnameediting'] = 'Éditer une question audio/vidéo';
$string['pluginnamesummary'] = 'Les élèves répondent aux questions en enregistrant leur voix directement dans le navigateur web, et peuvent ensuite être notés manuellement.';
$string['privacy:metadata'] = 'Le plugin type de question audio n\'enregistre aucune donnée personnelle.';
$string['privacy:preference:timelimitinseconds'] = 'La « durée maximale de l\'enregistrement » définie pour une question donnée.';
$string['recordagain'] = 'Ré-enregistrer';
$string['recordagainx'] = 'Ré-enregistrer {$a}';
$string['recordingfailed'] = 'L\'enregistrement a échoué';
$string['resume'] = 'Reprendre';
$string['startcamera'] = 'Démarrer la caméra';
$string['startcamerax'] = 'Démarrer la caméra {$a}';
$string['startrecording'] = 'Démarrer l\'enregistrement';
$string['startrecordingx'] = 'Démarrer l\'enregistrement {$a}';
$string['stoprecording'] = 'Arrêter l\'enregistrement';
$string['timelimit'] = 'Durée d\'enregistrement maximum';
$string['timelimit_help'] = 'Durée maximale d\'un enregistrement que l\'étudiant est autorisé à faire. S\'il atteint cette durée, l\'enregistrement s\'arrête automatiquement. La limite peut être supérieure à cette valeur, vous devez demander à un administrateur de l\'augmenter.';
$string['uploadaborted'] = 'Sauvegarde annulée';
$string['uploadcomplete'] = 'Enregistrement sauvegardé';
$string['uploadfailed'] = 'Sauvegarde échouée';
$string['uploadfailed404'] = 'La sauvegarde de l\'enregistrement a échoué : le fichier est probablement trop volumineux';
$string['uploadpreparing'] = 'Préparation de la sauvegarde...';
$string['uploadpreparingpercent'] = 'Préparation du téléchargement ({$a} %)';
$string['uploadprogress'] = 'Sauvegarde de l\'enregistrement ({$a})...';
$string['video'] = 'Vidéo seule';
$string['videobitrate'] = 'Débit vidéo';
$string['videobitrate_desc'] = 'Qualité de l\'enregistrement vidéo en bit/seconde (un nombre plus grand signifie une meilleure qualité)';
$string['videosize'] = 'Taille de la vidéo';
$string['videosize_desc'] = 'La taille de la vidéo';
$string['videotimelimit'] = 'Durée maximale de l\'enregistrement vidéo.';
$string['videotimelimit_desc'] = 'Durée maximale que l\'auteur de question peut définir pour la durée de l’enregistrement vidéo.';
