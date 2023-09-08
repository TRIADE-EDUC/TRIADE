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
 * Strings for component 'tool_opencast', language 'fr', version '4.1'.
 *
 * @package     tool_opencast
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['apipassword'] = 'Mot de passe pour l\'utilisateur de l\'API';
$string['apipassworddesc'] = 'Configurer le mot de passe pour le super utilisateur, celui qui effectue les appels api.';
$string['apipasswordempty'] = 'Le mot de passe pour l\'utilisateur de l\'API n\'est pas correctement configuré, accédez aux réglages de l\'outil opencast pour corriger cela.';
$string['apiurl'] = 'Url de l\'API Opencast';
$string['apiurldesc'] = 'Configurer l\'URL de base du système Opencast, par exemple : opencast.example.com';
$string['apiurlempty'] = 'L\'URL de l\'API Opencast n\'est pas correctement configurée, accédez aux réglages de l\'outil opencast pour corriger cela.';
$string['apiusername'] = 'Nom d\'utilisateur utilisé pour les appels API';
$string['apiusernamedesc'] = 'Pour tous les appels API moodle utilise cet utilisateur. L\'autorisation est donnée en ajoutant les rôles nécessaires à ces appels';
$string['apiusernameempty'] = 'Le nom d\'utilisateur pour l\'utilisateur de l\'API Opencast n\'est pas correctement configuré, accédez aux réglages de l\'outil opencast pour corriger cela.';
$string['connecttimeout'] = 'Délai de connexion dépassé';
$string['connecttimeoutdesc'] = 'Configurer le temps en seconde pendant lequel moodle essaiera de se connecter à opencast avant la fin du délai';
$string['needphp55orhigher'] = 'Une version 5.5 ou supérieure de PHP est nécessaire';
$string['opencast:externalapi'] = 'Accès aux services webs tool_opencast';
$string['opencast:instructor'] = 'Donne le rôle d\'instructeur dans opencast';
$string['opencast:learner'] = 'Donne le rôle d\'apprenant dans opencast';
$string['pluginname'] = 'API Opencast';
$string['privacy:metadata'] = 'L\'outil d\'administration fournit uniquement les points finaux de l\'API et les paramètres généraux de l\'ensemble du plugin opencast.
Cela enregistre quelle série est reliée à quel cours, mais cela n\'enregistre pas de données personnelles.';
$string['serverconnectionerror'] = 'Un problème est survenu pendant la connexion au serveur opencast. Veuillez vérifier vos informations de connexion et votre réseau.';
$string['wrongmimetypedetected'] = 'Un type MIME incorrect a été détecté pendant le dépôt de {$a->filename} depuis le cours {$a->coursename},
    Vous ne pouvez déposer que des fichiers vidéos !';
