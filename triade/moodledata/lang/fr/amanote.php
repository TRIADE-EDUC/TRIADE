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
 * Strings for component 'amanote', language 'fr', version '4.1'.
 *
 * @package     amanote
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['amanote:addinstance'] = 'Ajouter un nouveau fichier annotable';
$string['amanote:view'] = 'Ouvrir un fichier annotable';
$string['amanotecontent'] = 'Fichiers et sous-dossiers';
$string['amanotedetails_sizedate'] = '{$a->size} {$a->date}';
$string['amanotedetails_sizetype'] = '{$a->size} {$a->type}';
$string['amanotedetails_sizetypedate'] = '{$a->size} {$a->type} {$a->date}';
$string['amanotedetails_typedate'] = '{$a->type} {$a->date}';
$string['autosaveperiod'] = 'Période d\'enregistrement automatique';
$string['autosaveperiod_help'] = 'Configurez la période en minutes entre les sauvegardes automatiques (min .: 1, max .: 30). Régler la période sur 0 signifie qu\'il n\'y a pas de sauvegarde automatique.';
$string['cannotcreatetoken'] = 'Ouvrir dans Amanote';
$string['cannotcreatetoken_help'] = 'Vous n\'avez pas les permissions pour ouvrir ce document dans Amanote.';
$string['clicktoamanote'] = 'Ouvrir dans Amanote';
$string['clicktodownloadfile'] = 'Télécharger le fichier PDF';
$string['clicktodownloadnotes'] = 'Télécharger le fichier annoté';
$string['clicktoopen'] = 'Ouvrir dans un nouvel onglet';
$string['dnduploadamanote'] = 'Créer un fichier annotable';
$string['downloadfile'] = 'Télécharger';
$string['guestsarenotallowed'] = 'Ouvrir dans Amanote';
$string['guestsarenotallowed_help'] = 'Les utilisateurs invités ne sont pas autorisés à ouvrir une ressource dans Amanote. Veuillez vous connecter pour accéder à cette fonctionnalité.';
$string['importantinformationdescription'] = 'Pour que le module fonctionne correctement, vérifiez que les conditions suivantes sont remplies sur votre site Moodle :

1. Les services Web sont activés (Administration du site> Fonction avancée)

2. * Le service Web mobile Moodle * est activé (Administration du site> Plug-ins> Services Web> Services externes)

3. Le protocole REST est activé (Administration du site> Plug-ins> Services Web> Gérer les protocoles)

4. Fonctionnalité * webservice / rest: l\'utilisation * est autorisée pour * les utilisateurs authentifiés * (Administration du site> Utilisateurs> Autorisations> Définir les rôles> Utilisateurs authentifiés> Gérer les rôles)';
$string['importantinformationheading'] = 'Informations d\'installation importantes';
$string['key'] = 'Clé d\'activation';
$string['key_help'] = 'Cette clé est requise pour les fonctionnalités avancées telles que Podcast Creator.';
$string['modifieddate'] = 'Modifié {$a}';
$string['modulename'] = 'Fichier annotable';
$string['modulename_help'] = 'Un fichier annotable est une ressource de cours (PDF) que les étudiants peuvent ouvrir dans Amanote© afin de prendre des notes claires et structurées.

Les étudiants ont la possibilité :

* D\'ouvrir la ressource de cours dans un nouvel onglet de navigateur.
* De télécharger la ressource du cours sur leur ordinateur
* D\'ouvrir la ressource de cours dans Amanote© et de commencer à prendre des notes

Lorsque les étudiants ouvrent la ressource de cours dans Amanote©, ils ont la possibilité de commencer à prendre des notes intelligemment. Leurs notes sont liées aux différentes pages du document. De plus, ils peuvent enrichir leurs notes d’annotations, de dessins, de formules, d’images, de mises en évidence dans des diapositives, etc.

Lorsque les étudiants sauvegardent leurs notes, ils sont enregistrés dans la ressource, dans leur espace personnel. La prochaine fois qu\'ils ouvriront la ressource, ils récupéreront leurs notes.';
$string['modulenameplural'] = 'Fichiers annotable';
$string['nonotestodownload'] = 'Télécharger le fichier annotable';
$string['nonotestodownload_help'] = 'Vous n\'avez pas encore enregistrer les notes de ce document.';
$string['openinamanote'] = 'Ouvrir dans Amanote';
$string['openinamanote_help'] = 'Ouvrir le document dans Amanote vous permet de commencer ou de continuer à prendre des notes.';
$string['openpodcastcreator'] = 'Ouvrir Podcast Creator';
$string['openpodcastcreator_help'] = 'Le créateur de podcast vous permet d\'enregistrer un nouveau podcast ou d\'éditer un podcast existant.';
$string['openstatistics'] = 'Ouvrir les statistiques';
$string['openstatistics_help'] = 'Cela vous donne accès aux statistiques d\'utilisation de vos étudiants et à leurs commentaires sur cette ressource.';
$string['pluginadministration'] = 'Module d\'administration d\'Amanote';
$string['pluginname'] = 'Amanote';
$string['podcastcreatorbutton'] = 'Ouvrir Podcast Creator';
$string['preventdownload'] = 'Empêcher le téléchargement de PDF';
$string['preventdownload_desc'] = 'Empêcher les utilisateurs de télécharger le fichier source.';
$string['printintro'] = 'Montrer la description de la ressource';
$string['printintroexplain'] = 'Montrer la description de la ressource sous le contenu ?';
$string['privacy:metadata'] = 'Afin de s\'intégrer à Amanote, certaines données utilisateur doivent être envoyées à l\'application client Amanote (système distant).';
$string['privacy:metadata:email'] = 'Le courrier électronique de l\'utilisateur est envoyé au système distant pour améliorer les retours d\'expérience utilisateur (partage de notes, notification, etc.).';
$string['privacy:metadata:fullname'] = 'Le nom complet de l\'utilisateur est envoyé au système distant pour améliorer les retours d\'expérience utilisateur.';
$string['privacy:metadata:subsystem:corefiles'] = 'Les fichiers (PDF et AMA) sont stockés en utilisant le système de fichiers de Moodle.';
$string['privacy:metadata:userid'] = 'Le nom d\'utilisateur est envoyé de Moodle à Amanote afin d\'accélérer le processus d\'authentification.';
$string['saveinprivate'] = 'Enregistrer les notes dans les fichiers personnels';
$string['saveinprivate_help'] = 'Enregistrer le fichier annoté dans les fichiers privés de l\'utilisateur. Cela permettra à l\'utilisateur de récupérer ses notes la prochaine fois qu\'il ouvrira le fichier à annoter dans Amanote.';
$string['servicenotavailable'] = 'Ouvrir dans Amanote';
$string['servicenotavailable_help'] = 'Le service est indisponible. Veuillez contacter l\'administrateur du site.';
$string['showdate'] = 'Afficher la date de chargement/modification';
$string['showdate_desc'] = 'Afficher la date de chargement/modification sur la page du cours ?';
$string['showdate_help'] = 'Afficher la date de dépôt/modification à côté du lien vers la ressource.';
$string['showsize'] = 'Montrer la taille du fichier';
$string['showsize_desc'] = 'Montrer la taille du fichier sur la page de cours ?';
$string['showsize_help'] = 'Afficher la taille du fichier, par exemple « 3.1Mo » à côté du lien vers la ressource.';
$string['statisticsbutton'] = 'Ouvrir les learning Analytics';
$string['unexpectederror'] = 'Ouvrir dans Amanote';
$string['unexpectederror_help'] = 'Une erreur inattendue s\'est produite, la ressource ne peut pas être ouverte dans Amanote. Veuillez contacter l\'administrateur du site.';
$string['unsecureconnection'] = 'Attention ! Votre connexion n\'est pas sécurisée.';
$string['uploadeddate'] = '{$a} déposé';
