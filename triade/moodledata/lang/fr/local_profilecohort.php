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
 * Strings for component 'local_profilecohort', language 'fr', version '4.1'.
 *
 * @package     local_profilecohort
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addintro'] = 'Dans cet onglet, vous pouvez ajouter une nouvelle règle pour faire correspondre les valeurs des champs de profil d’utilisateur personnalisés à une cohorte à laquelle l\'utilisateur sera ajouté.';
$string['addrule'] = 'Ajouter une règle pour les champs de profil d\'utilisateur personnalisés…';
$string['addrules'] = 'Ajouter une nouvelle règle';
$string['andnextrule'] = 'La prochaine règle doit aussi correspondre pour rejoindre la cohorte.';
$string['cohortsintro'] = 'Dans cet onglet, vous pouvez sélectionner les cohortes que le plugin doit gérer.<br />
Une fois les cohortes sélectionnées, vous ne pourrez plus y modifier les membres manuellement. De plus, tous les utilisateurs membres de ces cohortes seront retirés et ajoutés de nouveau selon la règle du plugin configuré. <br />
Si vous décidez d’arrêter de gérer une cohorte avec le plugin en le désactivant ici, tous les utilisateurs qui y sont actuellement membres le resteront. Par la suite, vous pourrez mettre à jour de nouveau les membres manuellement.';
$string['delete'] = 'Supprimer cette règle';
$string['iffield'] = 'Si {$a}';
$string['invisiblecohortsnote'] = 'Remarque : ce plugin traite les cohortes visibles et invisibles également. De plus, la liste des cohortes inclut également les cohortes qui sont créées comme invisibles.';
$string['match_contains'] = 'contient';
$string['match_defined'] = 'est défini';
$string['match_exact'] = 'correspond';
$string['match_notcontains'] = 'ne contient pas';
$string['match_notdefined'] = 'n\'est pas défini';
$string['match_notexact'] = 'ne correspond pas';
$string['matchtype'] = 'correspond au type';
$string['matchvalue'] = 'correspond à la valeur';
$string['members'] = 'Membres des cohortes';
$string['membersintro'] = 'Dans cet onglet, vous pouvez voir les utilisateurs qui sont actuellement membres de la cohorte gérée par ce plugin.';
$string['moveto'] = 'déplacer à la position';
$string['nocohorts'] = 'Il n\'y a pas de cohortes disponibles pour le plugin. Allez sur {$a} pour créer des cohortes.';
$string['nofields'] = 'Aucun champ de profil d\'utilisateur personnalisé n\'est défini.<br />
Vous devez définir les champs de profil d\'utilisateur personnalisés avant de pouvoir y ajouter des règles. Allez sur {$a} pour créer au moins un champ de profil d\'utilisateur personnalisé.';
$string['nousers'] = 'Cette cohorte ne contient actuellement aucun membre.';
$string['pluginname'] = 'Membres de cohorte selon les champs de profil';
$string['selectcohorts'] = 'Sélectionnez les cohortes à gérer';
$string['selectvalue'] = 'l\'utilisateur sera ajouté à la cohorte';
$string['updatecohorts'] = 'Mettre à jour les cohortes d\'après les champs de profil d\'utilisateur personnalisés.';
$string['viewintro'] = 'Dans cet onglet, vous pouvez définir les règles pour faire correspondre les champs de profil d’utilisateur personnalisés aux cohortes dans lesquelles l’utilisateur sera ajouté. Les règles définies sont traitées dans l’ordre d’affichage. Donc, un utilisateur qui suit plusieurs règles sera ajouté à toutes les cohortes pertinentes. Notez que si une règle est modifiée, une tâche d’arrière-plan sera programmée pour mettre à jour tous les utilisateurs affectés. Un délai de quelques minutes peut s’écouler avant que tous les utilisateurs membres de la cohorte soient mis à jour, cela dependra de la configuration de vos tâches programmées sur Moodle et des tâches cron sur le serveur Moodle. Quoi qu’il en soit, n’importe quel utilisateur qui se connecte avant que la tâche d’arrière-plan soit terminée sera mis à jour immédiatement.';
$string['viewrules'] = 'Afficher / Modifier une règle';
