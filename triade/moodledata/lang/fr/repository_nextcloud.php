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
 * Strings for component 'repository_nextcloud', language 'fr', version '4.1'.
 *
 * @package     repository_nextcloud
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['both'] = 'Internes et externes';
$string['cannotconnect'] = 'L\'utilisateur n\'a pas pu être authentifié. Veuillez vous connecter et déposer ensuite le fichier.';
$string['chooseissuer'] = 'Fournisseur';
$string['chooseissuer_help'] = 'Pour ajouter un nouveau fournisseur, aller à Administration du site / Serveur / Services OAuth 2.';
$string['configplugin'] = 'Configuration dépôt Nextcloud';
$string['configuration_exception'] = 'Une erreur est survenue dans la configuration OAuth 2 du client : {$a}';
$string['contactadminwith'] = 'L\'action requise n\'a pas pu être effectuée. Si cela se reproduit, veuillez contacter l\'administrateur du site en indiquant les informations suivantes : <br />« <em>{$a}</em> »';
$string['couldnotmove'] = 'Le fichier demandé n\'a pas pu être déplacé dans le dossier {$a}';
$string['defaultreturntype'] = 'Type retourné par défaut';
$string['endpointnotdefined'] = 'Point de terminaison {$a} non défini.';
$string['external'] = 'Externes (seuls des liens sont enregistrés dans Moodle)';
$string['externalpubliclinkwarning'] = 'Attention ! Ce fichier deviendra public.';
$string['filenotaccessed'] = 'Il n\'a pas été possible d\'accéder au fichier demandé. Veuillez vérifier si vous avez choisi un fichier valide et si vous êtes authentifié avec le compte adéquat.';
$string['fileoptions'] = 'Les types et réglages par défaut des fichiers retournés peuvent être configurés ici. Tous les fichiers liés vers des serveurs externes seront mis à jour de sorte que leur possesseur soit le compte système Moodle.';
$string['foldername'] = 'Nom du dossier créé dans la zone privée des utilisateurs Nextcloud pour contenir les liens à accès contrôlés.';
$string['foldername_help'] = 'Afin de permettre aux utilisateurs de retrouver les fichiers partagés avec eux, les partages sont enregistrés dans un dossier spécifique.

Ce réglage détermine le nom de ce dossier. Il est recommandé de choisir un nom en lien avec votre instance Moodle.';
$string['internal'] = 'Internes (fichiers enregistrés dans Moodle)';
$string['invalidresponse'] = 'Réponse du serveur non valide.';
$string['issuervalidation_invalid'] = 'Le fournisseur {$a} est actuellement actif, mais il n\'implémente pas tous les points de terminaison nécessaires. Ce dépôt ne peut pas fonctionner.';
$string['issuervalidation_valid'] = 'Le fournisseur {$a} est actuellement actif.';
$string['issuervalidation_without'] = 'Vous n\'avez pas encore sélectionné de serveur Nextcloud comme fournisseur OAuth 2.';
$string['nextcloud'] = 'Nextcloud';
$string['nextcloud:view'] = 'Consulter un dépôt Nextcloud';
$string['no_right_issuers'] = 'Aucun des fournisseurs existants n\'implémente tous les points de terminaison requis. Veuillez enregistrer un fournisseur adéquat.';
$string['noclientconnection'] = 'Les clients OAuth n\'ont pas pu être connectés.';
$string['notauthorized'] = 'Vous n\'avez pas les permissions requises pour exécuter cette requête. Veuillez vous assurer que vous êtes connecté avec le compte adéquat.';
$string['oauth2serviceslink'] = '<a href="{$a}" title="Lien vers la configuration des services OAuth 2">Configuration des services OAuth 2</a>';
$string['pathnotcreated'] = 'Le dossier {$a} n\'a pas pu être créé dans le compte système.';
$string['pluginname'] = 'Nextcloud';
$string['pluginname_help'] = 'Dépot Nextcloud';
$string['privacy:metadata'] = 'Le plugin de dépôt Nextcloud n\'enregistre et ne transmet aucune donnée personnelle vers le système distant.';
$string['request_exception'] = 'Une requête à {$a->instance} a échoué. {$a->errormessage}';
$string['requestnotexecuted'] = 'La requête n\'a pas pu être exécutée. Si cela se reproduit, veuillez contacter l\'administrateur du site.';
$string['right_issuers'] = 'Les fournisseurs suivantes implémentent les points de terminaison requis :<br />{$a}';
$string['supportedreturntypes'] = 'Fichier supportés';
