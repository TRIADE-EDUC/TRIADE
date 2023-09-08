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
 * Strings for component 'assignsubmission_maharaws', language 'fr', version '4.1'.
 *
 * @package     assignsubmission_maharaws
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['archiveonrelease'] = 'Archiver lors de la notation';
$string['archiveonrelease_help'] = 'Après l\'attribution d\'une note, un instantané du portfolio sera réalisé.';
$string['assign_submission_maharaws_description'] = 'Fonctions Mahara utilisées dans le plugin de remise de devoirs Mahara.<br />La publication de ce service sur un site Moodle n\'a aucun effet. Abonnez-vous à ce service si vous souhaitez pouvoir utiliser les devoirs avec {$a}.<br />';
$string['assign_submission_maharaws_name'] = 'Remise de devoir Mahara (services Web)';
$string['collectionsby'] = 'Collections par {$a}';
$string['debug'] = 'Déboguer OAuth';
$string['debug_help'] = 'Option de débogage pour interrompre le saut de connexion OAuth SSO afin que les paramètres puissent être inspectés';
$string['defaultlockpages'] = 'Défaut « {$a} »';
$string['defaultlockpages_help'] = 'Paramètre par défaut à utiliser pour le paramètre "{$a}" dans les nouveaux devoirs Mahara.';
$string['defaulton'] = 'Activé par défaut';
$string['defaulton_help'] = 'Si elle est définie, cette méthode de remise sera activée par défaut pour tous les nouveaux devoirs.';
$string['defaultsite'] = 'Défaut « {$a} »';
$string['defaultsite_help'] = 'Paramètre par défaut à utiliser pour le paramètre « {$a} » dans les nouveaux devoirs Mahara.';
$string['emptysubmission'] = 'Vous n\'avez pas choisi de portfolio à remettre.';
$string['enabled'] = 'Mahara';
$string['enabled_help'] = 'Si cette option est activée, les étudiants peuvent soumettre des pages et des collections Mahara pour évaluation sur ce site dans ce cours.';
$string['errorinvalidhost'] = 'ID hôte invalide sélectionné';
$string['errorinvalidstatus'] = 'Erreur du développeur : statut de remise non valide envoyé à assign_submission_mahara::set_mahara_submission_status()';
$string['errorinvalidurl'] = 'Erreur lors de la connexion aux services Web de Mahara. {$a}';
$string['errorrequest'] = 'La tentative d\'envoi de la requête OAuth a généré une erreur : {$a}';
$string['errorvieworcollectionalreadysubmitted'] = 'La page ou la collection sélectionnée n\'a pas pu être remise. Veuillez en sélectionner un autre.';
$string['errorwsrequest'] = 'La tentative d\'envoi de la requête Mahara a généré une erreur : {$a}';
$string['eventassessableuploaded'] = 'Une page ou une collection a été remise.';
$string['forceglobalcredentials'] = 'Forcer les informations d’identification globales';
$string['forceglobalcredentials_help'] = 'Toujours utiliser ces identifiants pour vous connecter à Mahara';
$string['gcdesc'] = 'Les informations d’identification de Mahara ont été définies au niveau mondial.';
$string['gclabel'] = 'Références globales';
$string['invalidurlhelp'] = 'Vérifier que l’URL et les informations d’identification OAuth sont correctes et qu’il existe un certificat SSL valide si HTTPS est utilisé. Vérifier également que les bonnes fonctions sont attribuées à l’accès OAuth.';
$string['key'] = 'Clé OAuth des services web de Mahara';
$string['key_help'] = 'Entrez la clé OAuth des services Web du site Mahara partenaire.';
$string['legacy_ext_username'] = 'Utiliser l’ancien format ext_user_username';
$string['legacy_ext_username_help'] = 'En activant cette option, le format du champ ext_usr_username suit la configuration suivante "Fieldname:value" Il n’est pas recommandé d’activer ce paramètre à moins d’avoir une raison spécifique de le faire.';
$string['lockpages'] = 'Verrouiller les portfolios remis';
$string['lockpages_help'] = 'Si l’option « Oui, mais déverrouiller après la notation » est sélectionnée, la page ou la collection sera déverrouillée après la notation du travail remis ou, si le flux d’évaluation a été utilisé, elle sera déverrouillée lorsque les notes seront transmises à l’étudiant.';
$string['mahara'] = 'Mahara';
$string['maharaws:configure'] = 'Configurer la remise de travaux Mahara';
$string['nomaharahostsfound'] = 'Aucun hôte Mahara trouvé.';
$string['noneselected'] = 'Aucune sélection';
$string['noviewscreated'] = 'Vous n’avez pas de pages ou de collections disponibles. Veuillez visiter « {$a->name} » et <a href="{$a->jumpurl}" target="_blank" rel="noopener noreferrer">créer un nouveau</a>.';
$string['option_collections'] = 'Collections';
$string['option_views'] = 'Pages';
$string['outputforlog'] = '{$a->remotehostname} : {$a->viewtitle} (view id: {$a->viewid})';
$string['outputforlognew'] = 'Nouveau travail remis {$a}.';
$string['pluginname'] = 'Travaux remis Mahara';
$string['previousattemptsnotvisible'] = 'Les tentatives précédentes avec le plug-in de remise de travaux Mahara ne sont pas visibles.';
$string['privacy:metadata:assignment'] = 'L’identifiant du devoir';
$string['privacy:metadata:assignmentsubmission_maharaws:coursefullname'] = 'Le nom complet du cours est envoyé pour permettre au système distant d’offrir une meilleure expérience utilisateur.';
$string['privacy:metadata:assignmentsubmission_maharaws:courseid'] = 'L’identifiant du cours est envoyé par Moodle pour permettre au système distant de soumettre votre portfolio au bon cours.';
$string['privacy:metadata:assignmentsubmission_maharaws:courseshortname'] = 'Le nom abrégé du cours est envoyé au système distant pour permettre une meilleure expérience utilisateur.';
$string['privacy:metadata:assignmentsubmission_maharaws:email'] = 'Votre courriel est envoyé au système distant pour permettre une meilleure expérience utilisateur et pour la gestion du compte.';
$string['privacy:metadata:assignmentsubmission_maharaws:firstname'] = 'Votre prénom est envoyé au système distant pour permettre une meilleure expérience utilisateur';
$string['privacy:metadata:assignmentsubmission_maharaws:fullname'] = 'Votre nom complet est envoyé au système distant pour permettre une meilleure expérience utilisateur.';
$string['privacy:metadata:assignmentsubmission_maharaws:idnumber'] = 'Votre numéro d’identification est envoyé par Moodle pour vous permettre d’accéder à vos données sur le système distant.';
$string['privacy:metadata:assignmentsubmission_maharaws:lastname'] = 'Votre nom de famille est envoyé au système distant pour permettre une meilleure expérience utilisateur.';
$string['privacy:metadata:assignmentsubmission_maharaws:userid'] = 'L’identifiant est envoyé par Moodle pour vous permettre d’accéder à vos données sur le système distant.';
$string['privacy:metadata:assignmentsubmission_maharaws:username'] = 'Votre nom d\'utilisateur est envoyé par Moodle pour vous permettre d\'accéder à vos données sur le système distant.';
$string['privacy:metadata:assignsubmission_maharaws'] = 'Stocke des informations sur les pages Mahara et les collections remises aux devoirs.';
$string['privacy:metadata:iscollection'] = 'Ce travail est-il une page ou une collection ?';
$string['privacy:metadata:submission'] = 'L’identifiant de la remise';
$string['privacy:metadata:viewid'] = 'Identifiant de la page ou de la collection Mahara';
$string['privacy:metadata:viewstatus'] = 'Le statut de la page ou de la collection Mahara';
$string['privacy:metadata:viewtitle'] = 'Le titre de la page ou de la collection Mahara';
$string['privacy:metadata:viewurl'] = 'L’URL de la page ou de la collection Mahara';
$string['privacy:path'] = 'Pages et collections Mahara';
$string['remoteuser'] = 'Utiliser un utilisateur distant';
$string['secret'] = 'Secret OAuth des services Web de Mahara';
$string['secret_help'] = 'Entrez le secret OAuth des services web du site partenaire de Mahara.';
$string['selectmaharaview'] = 'Sélectionnez l’une de vos pages ou collections de portfolio disponibles dans la liste ci-dessous ou visitez « {$a->name} » et <a href="{$a->jumpurl}" target="_blank" rel="noopener noreferrer"> créez-en un nouveau</a>.';
$string['token'] = 'Jeton de services Web Mahara';
$string['token_help'] = 'Entrez le jeton d’authentification des services Web du site Mahara partenaire.';
$string['url'] = 'URL pour le site de Mahara';
$string['url_help'] = 'Ce paramètre vous permet de définir à partir de quel site Mahara vos étudiants doivent soumettre leurs portfolios. Saisissez l’URL du site Mahara, par exemple https://mahara.some.edu/.';
$string['viewsby'] = 'Pages par {$a}';
$string['yeskeeplocked'] = 'Oui, garder verrouillé';
$string['yesunlock'] = 'Oui, mais déverrouiller après la notation';
