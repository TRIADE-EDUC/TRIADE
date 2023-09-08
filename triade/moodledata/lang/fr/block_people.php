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
 * Strings for component 'block_people', language 'fr', version '4.1'.
 *
 * @package     block_people
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['noparticipantslist'] = 'Vous n\'avez pas le droit de voir la liste des participants à ce cours';
$string['participantslist'] = 'Liste des participants';
$string['people:addinstance'] = 'Ajouter un bloc Participants';
$string['people:myaddinstance'] = 'Ajouter au tableau de bord un bloc Participants';
$string['pluginname'] = 'Participants';
$string['privacy:metadata'] = 'Le bloc Participant ajoute des fonctionnalités à Moodle, mais n\'enregistre aucune donnée personnelle.';
$string['resetvisibilityerror'] = 'Aie… quelque chose s\'est mal passé dans la mise à jour des tables de la base de données… <br/>Le paramètre "Réinitialiser la visibilité" a été réinitialisé.';
$string['resetvisibilitysuccess'] = 'Succès ! Toutes les instances de ce bloc sont visibles (à nouveau). <br/>Le paramètre "Réinitialiser la visibilité" a été réinitialisé.';
$string['setting_hideblock'] = 'Cacher le bloc';
$string['setting_hideblock_desc'] = 'En activant ce paramètre, le bloc peut être masqué par les utilisateurs.<br/>
Important :<br/>
La désactivation de ce paramètre supprimera entièrement l\'élément de menu permettant l\'affichage / masquage. Cela signifie que les utilisateurs ne peuvent plus masquer ce bloc, mais d\'un autre côté, les blocs déjà masqués ne peuvent plus être affichés. Si vous souhaitez désactiver cette fonctionnalité, utilisez la fonction suivante pour réinitialiser la visibilité de toutes les instances de "block_people".';
$string['setting_hideblockheading'] = 'Cacher le bloc';
$string['setting_linkavatar'] = 'Lien vers l\'avatar';
$string['setting_linkavatar_desc'] = 'En activant ce paramètre, un lien vers la page utilisateur de l\'enseignant sera ajouté sur l\'avatar de l\'enseignant. Le lien ne sera ajouté que si l\'utilisateur est autorisé à afficher les profils d\'utilisateurs.';
$string['setting_linkingheading'] = 'Mise en relation';
$string['setting_linkmessaging'] = 'Lien vers la messagerie';
$string['setting_linkmessaging_desc'] = 'En activant ce paramètre, un lien vers la page de messagerie de l\'enseignant sera ajouté sous le nom de l\'enseignant. Le lien ne sera ajouté que si l\'utilisateur est autorisé à envoyer un message à l\'enseignant.';
$string['setting_linkname'] = 'Nom du lien';
$string['setting_linkname_desc'] = 'En activant ce paramètre, un lien vers la page d\'utilisateur de l\'enseignant sera ajouté au nom de l\'enseignant. Le lien ne sera ajouté que si l\'utilisateur est autorisé à afficher les profils d\'utilisateurs.';
$string['setting_linkparticipantspage'] = 'Afficher le lien vers la page des participants';
$string['setting_linkparticipantspage_desc'] = 'En activant ce réglage, un lien vers la page des participants du cours sera affiché dans le bloc.';
$string['setting_multipleroles'] = 'Afficher des rôles multiples';
$string['setting_multipleroles_desc'] = 'Ce paramètre vous permet de contrôler si les utilisateurs possédant plusieurs des rôles indiqués sont affichés plusieurs fois dans ce bloc. Si le réglage est désactivé, ils n’apparaissent qu\'une seule fois avec le premier rôle qu\'ils possèdent, en suivant l\'ordre de tri des rôles. Si ce paramètre est activé, ils seront affichés pour chacun de leur rôles.';
$string['setting_participantspageheading'] = 'Page des participants';
$string['setting_resetvisibility'] = 'Reinitialiser la visibilité';
$string['setting_resetvisibility_desc'] = 'En cochant cette case, toutes les instances "block_people" existantes seront visibles (à nouveau).<br/>
Veuillez noter :<br/>
Après avoir sauvegardé cette option, les opérations de la base de données pour réinitialiser la visibilité seront déclenchées et cette case à cocher sera à nouveau décochée. L\'activation et la sauvegarde suivantes de cette fonctionnalité déclencheront les opérations de la base de données pour réinitialiser à nouveau la visibilité.';
$string['setting_roles'] = 'Afficher ces rôles';
$string['setting_roles_desc'] = 'Ce paramètre vous permet de contrôler quels utilisateurs apparaissent dans ce bloc. Les utilisateurs doivent posséder au moins un de ces rôles dans le cours pour être affichés dans le bloc.';
$string['setting_rolesheading'] = 'Rôles';
