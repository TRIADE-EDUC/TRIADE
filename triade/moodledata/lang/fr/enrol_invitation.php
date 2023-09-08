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
 * Strings for component 'enrol_invitation', language 'fr', version '4.1'.
 *
 * @package     enrol_invitation
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['assignrole'] = 'Attribuer le rôle';
$string['editenrolment'] = 'Modifier l\'inscription';
$string['emailaddressnumber'] = 'Adresse mail {$a}';
$string['emailmessageuserenrolled'] = '{$a->userfullname} a été inscrit au cours {$a->coursefullname}.

Cliquez sur le lien suivant pour vérifier les nouvelles inscriptions : {$a->courseenrolledusersurl}

{$a->sitename}
 -------------
{$a->siteurl}';
$string['emailtitleuserenrolled'] = '{$a->userfullname} s\'est inscrit dans le cours {$a->coursefullname}.';
$string['expiredtoken'] = 'Donnée invalide - le processus d\'inscription a été arrêté.';
$string['inviteusers'] = 'Inviter des utilisateurs';
$string['noinvitationinstanceset'] = 'Aucune instance d\'inscription par invitation n\'a été trouvée. Merci d\'ajouter d\'abord une instance d\'inscription par invitation à votre cours.';
$string['nopermissiontosendinvitation'] = 'Vous n\'êtes pas autorisé à envoyer des invitations';
$string['pluginname'] = 'Invitation';
$string['pluginname_desc'] = 'Le module d\'invitation permet d\'envoyer des invitations par courriel. Ces invitations ne peuvent être utilisées qu\'une seule fois. Les utilisateurs cliquent sur le lien transmis dans le courriel et sont automatiquement inscrits au cours.';
$string['status'] = 'Autoriser l\'inscription par invitation';
$string['status_desc'] = 'Autoriser les utilisateurs à inviter des gens à s\'inscrire au cours par défaut.';
$string['unenrol'] = 'Désinscrire l\'utilisateur';
$string['unenroluser'] = 'Voulez-vous vraiment désinscrire « {$a->user} » du cours « {$a->course} » ?';
