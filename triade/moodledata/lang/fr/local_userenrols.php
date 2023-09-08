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
 * Strings for component 'local_userenrols', language 'fr', version '4.1'.
 *
 * @package     local_userenrols
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['ERR_CREATE_GROUP'] = 'Ligne %u : Impossible de créer le groupe « %s »\\n';
$string['ERR_ENROLL_FAILED'] = 'Ligne %u : Impossible d\'attribuer un rôle pour l\'ID utilisateur « %s »\\n';
$string['ERR_ENROLL_META'] = 'Ligne %u : Aucune inscription existante dans le méta-cours pour l\'ID utilisateur « %s »\\n';
$string['ERR_GROUP_MEMBER'] = 'Ligne %u : Impossible d\'ajouter l\'utilisateur « %s » au groupe « %s »\\n';
$string['ERR_INVALID_GROUP_ID'] = 'L\'identifiant de groupe %u n\'est pas valide pour ce cours.\\n';
$string['ERR_NO_MANUAL_ENROL'] = 'Le plugin d\'inscription manuelle doit être activé dans le cours.';
$string['ERR_NO_META_ENROL'] = 'Le plugin d\'inscription au cours doit être activé dans le cours.';
$string['ERR_PATTERN_MATCH'] = 'Ligne %u : impossible d\'analyser le contenu de la ligne « %s »\\n';
$string['ERR_USERID_INVALID'] = 'Ligne %u : valeur d\'identification d\'utilisateur non valide « %s » \\ n';
$string['ERR_USER_MULTIPLE_RECS'] = 'Ligne %u : L\'identité de l\'utilisateur « %s » n\'est pas unique. Plusieurs enregistrements trouvés\\n';
$string['HELP_PAGE_IMPORT'] = 'Importation des inscriptions et affectations dans les groupes';
$string['HELP_PAGE_IMPORT_help'] = '<p>
Utilisez ce plugin pour effectuer des inscriptions d\'utilisateurs à partir d\'un fichier texte délimité (CSV). Tous les utilisateurs inclus dans le fichier d\'importation doivent déjà posséder un compte configuré sur le site. Les nouveaux comptes d\\’utilisateur ne seront pas créés.<br />
<br />
Si un nom de groupe est indiqué sur la ligne de l\'utilisateur, cet utilisateur sera
ajouté à ce groupe si celui-ci existe. Vous pouvez éventuellement créer de nouveaux groupes si nécessaire.
</p>
<ul>
  <li>Chaque ligne du fichier d\'importation doit correspondre à un seul utilisateur.</li>
  <li>Chaque ligne doit contenir au moins un champ avec une valeur propre à l\'utilisateur, qu\'il s\'agisse du nom d\'utilisateur de l\'adresse courriel ou du l\'identifiant interne  </li>
  <li>Chaque enregistrement peut contenir un champ supplémentaire pour le nom de groupe, séparé par une virgule, un point-virgule ou une tabulation.</li>
  <li>Le rôle des utilisateurs peut être sélectionné, il correspond d\'emblée au rôle par défaut du cours.</li>
  <li>Les champs peuvent être mis entre guillemets notamment pour le champ du nom du groupe quand le nom contient un point-virgule ou une virgule. </li>
  <li>Les lignes vides du fichier d\'importation seront ignorées</li>
  <li>Remarque : si un utilisateur est déjà inscrit dans le cours, aucun changement ne sera effectué sur son inscription (notamment aucun changement de rôle).</li>
</ul>
<p>
Remarque à propos des méta-cours : ce plugin n\'importera pas les inscriptions utilisateurs dans un méta-cours car elles doivent être faites dans chacun des cours enfants. Toutefois le plugin effectuera des affectations de groupe et pourra aussi créer des groupes quand l\'ID utilisateur spécifié sera déjà associé au méta-cours via une inscription au cours enfant.
</p>

<h3>Exemples</h3>

Numéro d\'identification;Groupe
<pre>
2190101;TD001
2190103;TD001
2190110;TD002
2190121;TD004
</pre>

Adresses électroniques
<pre>
smith-john@university.edu
janedoe@university.edu, "Honors"
alan.jones@university.edu, "HonorsPlus"
</pre>

Nom d\'utilisateur qui doit être séparé du champ groupe par une tabulation
<pre>
johnsonf	"Presentation, Group One"
samsel	Ten O\'Clock Testing
</pre>';
$string['IMPORT_MENU_LONG'] = 'Importation des inscriptions';
$string['IMPORT_MENU_SHORT'] = 'Importation';
$string['INF_IMPORT_SUCCESS'] = 'Importation de l\'inscription utilisateur réussie';
$string['INF_METACOURSE_WARN'] = '<b>WARNING</b> : Vous ne pouvez pas importer des inscriptions directement dans un méta-cours. Faites plutôt des inscriptions dans l\'un des cours enfants.<br /><br />';
$string['LBL_ENROLL_OPTIONS'] = 'Options d\'inscription';
$string['LBL_FILE_OPTIONS'] = 'Importation du fichier';
$string['LBL_FILE_OPTIONS_help'] = 'Déposez un fichier .txt ou .csv. contenant les informations utilisateurs et leurs groupes optionnels';
$string['LBL_GROUP'] = 'Inscrire dans les groupes :';
$string['LBL_GROUP_CREATE'] = 'Créer les groupes :';
$string['LBL_GROUP_CREATE_help'] = 'Si les groupes indiqués dans le fichier d\'importation n\'existent pas et que vous en avez besoin, choisissez « Oui ».';
$string['LBL_GROUP_ID'] = 'Selectionner le groupe :';
$string['LBL_GROUP_ID_help'] = 'Utiliser les noms des groupes dans le fichier d\'importation ou sélectionnez un groupe existant en ignorant ceux du fichier.';
$string['LBL_GROUP_OPTIONS'] = 'Options des groupes';
$string['LBL_GROUP_help'] = 'Inscrire dans les groupes, à partir du fichier d\'importation ou dans le groupe existant selectionné.';
$string['LBL_IDENTITY_OPTIONS'] = 'Identité utilisateur';
$string['LBL_IMPORT'] = 'Importation';
$string['LBL_IMPORT_TITLE'] = 'Importation fichier CSV inscriptions';
$string['LBL_NO_GROUP_ID'] = 'Utiliser le fichier de données';
$string['LBL_NO_ROLE_ID'] = 'Pas d\'inscription';
$string['LBL_ROLE_ID'] = 'Rôle :';
$string['LBL_ROLE_ID_help'] = 'Rôle attribué aux utilisateurs dans le cours : si vous choisissez « Pas d\'inscription », seules les affectations de groupes seront effectuées.';
$string['LBL_USER_ID_FIELD'] = 'Champs utilisateur :';
$string['LBL_USER_ID_FIELD_help'] = 'Précisez le type de champ utilisateur de la première colonne du fichier d\'importation.';
$string['VAL_INVALID_FORM_DATA'] = 'Formulaire de données non valide.';
$string['VAL_INVALID_SELECTION'] = 'Sélection non valide';
$string['VAL_NO_FILES'] = 'Aucun fichier d\'importation sélectionné';
$string['pluginname'] = 'Importation inscriptions & groupes';
$string['privacy:no_data_reason'] = 'Le plugin Importation inscriptions & groupes n\'enregistre aucune donnée personnelle.';
