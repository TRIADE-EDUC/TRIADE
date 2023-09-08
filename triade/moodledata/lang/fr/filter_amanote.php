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
 * Strings for component 'filter_amanote', language 'fr', version '4.1'.
 *
 * @package     filter_amanote
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['anonymous'] = 'Anonyme';
$string['anonymous_help'] = 'Avec cette option, les utilisateurs sont anonymes dans Amanote et aucune authentification n\'est requise. Cependant, les fonctionnalités de collaboration et de podcast ne seront plus disponibles. Enfin, pour activer cette fonctionnalité vous devez activer la sauvegarde dans les fichiers privés et ajouter une clé d\'activation.';
$string['autosaveperiod'] = 'Intervalle d\'enregistrement automatique';
$string['autosaveperiod_help'] = 'Configurez le délai en minutes entre les sauvegardes automatiques (min. : 1, max. : 30). Régler la période sur 0 signifie qu\'il n\'y a pas d\'enregistrement automatique.';
$string['downloadnotes'] = 'Télécharger le fichier annoté';
$string['filtername'] = 'Amanote';
$string['importantinformationdescription'] = 'Pour que le module fonctionne correctement, veuillez vérifier que les conditions suivantes sont remplies sur votre site Moodle :

1. Le filtre Amanote est activé (Administration du site > Plugins > Filtres > Gérer les filtres)

2. Les services Web sont activés (Administration du site > Fonctionnalité avancée)

3. *Le service Web mobile Moodle* est activé (Administration du site > Plugins > Services Web > Services externes)

4. Le protocole REST est activé (Administration du site > Plugins > Services Web > Gérer les protocoles)

5. La capacité *webservice/rest:use* est autorisée pour les *utilisateurs authentifiés* (Administration du site > Utilisateurs > Autorisations > Définir les rôles > Utilisateurs authentifiés > Gérer les rôles)';
$string['importantinformationheading'] = 'Information importante concernant l\'installation';
$string['key'] = 'Clé d\'activation';
$string['key_help'] = 'Cette clé est requise pour les fonctionnalités avancées telles que Podcast Creator.';
$string['openanalytics'] = 'Ouvrir Learning Analytics';
$string['openinamanote'] = 'Ouvrir dans Amanote';
$string['openpodcast'] = 'Ouvrir Podcast Creator';
$string['openstudentsworks'] = 'Ouvrir les travaux des étudiants';
$string['pluginadministration'] = 'Administration du module Amanote';
$string['privacy:metadata'] = 'Afin de s\'intégrer à Amanote, certaines données utilisateur doivent être envoyées à l\'application cliente Amanote (système distant).';
$string['privacy:metadata:access_token'] = 'Le jeton d\'accès de l\'utilisateur est requis pour enregistrer les notes dans l\'espace des fichiers privés de Moodle.';
$string['privacy:metadata:access_token_expiration'] = 'L\'expiration du jeton d\'accès est envoyée pour empêcher l\'utilisateur d\'utiliser l\'application avec un jeton expiré.';
$string['privacy:metadata:email'] = 'L\'adresse mail de l\'utilisateur est envoyée au système distant pour permettre une meilleure expérience utilisateur (partage de notes, etc.).';
$string['privacy:metadata:fullname'] = 'Le nom complet de l\'utilisateur est envoyé au système distant pour permettre une meilleure expérience utilisateur.';
$string['privacy:metadata:subsystem:corefiles'] = 'Les fichiers (PDF, AMA) sont stockés à l\'aide du système de fichiers de Moodle.';
$string['privacy:metadata:userid'] = 'L\'identifiant utilisateur est envoyé de Moodle à Amanote afin d\'accélérer le processus d\'authentification.';
$string['saveinprivate'] = 'Enregistrer des notes dans des fichiers privés';
$string['saveinprivate_help'] = 'Enregistrez le fichier annoté dans les fichiers privés de l\'utilisateur. Cela permettra à l\'utilisateur de récupérer ses notes la prochaine fois qu\'il ouvrira le fichier annotable dans Amanote.';
$string['target'] = 'Cible';
$string['target_help'] = 'Sélectionnez l\'emplacement où l\'utilisateur sera redirigé pour annoter la ressource.';
$string['target_inamanote'] = 'Site Amanote (meilleure expérience utilisateur)';
$string['target_inmoodle'] = 'Intégré dans Moodle';
$string['target_inmoodlefullscreen'] = 'Moodle en plein écran';
$string['teacher'] = 'Enseignant';
$string['worksheet'] = 'Envoyer à l\'enseignant';
$string['worksheet_help'] = 'Cette option permet aux étudiant d\'envoyer leurs remarques ou devoirs à l\'enseignant. L\'enseignant peut alors corriger les remarques et les renvoyer à l\'étudiant.';
