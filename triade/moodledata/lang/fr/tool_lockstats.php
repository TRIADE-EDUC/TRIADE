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
 * Strings for component 'tool_lockstats', language 'fr', version '4.1'.
 *
 * @package     tool_lockstats
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['blacklist'] = 'Historique de la liste noire';
$string['blacklistdesc'] = 'Ceux-ci sont identifiés par leur classpath, par exemple \\tool_crawler\\task\\crawl_task';
$string['cleanup'] = 'Nettoyer l\'historique';
$string['cleanupdesc'] = 'Élaguer automatiquement la table d\'historique après cette valeur.';
$string['debug'] = 'Débogage';
$string['debugdesc'] = 'Imprimer une sortie de débogage utile supplémentaire dans le cron.log';
$string['enable'] = 'Activer';
$string['enabledesc'] = 'Activer les statistiques de verrouillage';
$string['errornolockfactory'] = 'Erreur : La lock_factory n\'est pas configurée correctement dans config.php :<br>$CFG->lock_factory = \'\\tool_lockstats\\proxy_lock_factory\' ;';
$string['errornotenabled'] = 'Attention : le plugin de verrouillage des statistiques n\'est pas activé. Veuillez l\'activer sur la page des paramètres du plugin <a href="{$a}"></a>';
$string['form_reset_button'] = 'Réinitialiser l\'historique de verrouillage';
$string['form_reset_warning'] = 'Avertissement. Vous êtes sur le point de réinitialiser l\'historique des statistiques de verrouillage. Voulez-vous vraiment faire cela ?';
$string['h1_adhoctask'] = 'Résumé des tâches ad hoc';
$string['h1_current'] = 'Verrouillages actuels';
$string['h1_detail'] = 'Détails de la tâche';
$string['h1_nexttask'] = 'Prochaines tâches en cours d\'exécution';
$string['h1_slowest'] = 'Tâches les plus lentes cette semaine avec une durée supérieure à {$a} secondes';
$string['lock_in_use'] = 'Verrouillage encore utilisé par un autre processus';
$string['pluginname'] = 'Cron des statistiques du verrouillage';
$string['privacy:metadata'] = 'Le cron du plugin de statistique de verrouillage ne stocke aucune donnée personnelle.';
$string['release_all_locks'] = 'Supprimer tous les verrouillages périmés';
$string['release_lock'] = 'Supprimer le verrouillage';
$string['reset_header'] = 'Réinitialiser l\'historique des statistiques de verrouillage';
$string['reset_text'] = 'Réinitialiser l\'historique des statistiques de verrouillage';
$string['table_classname'] = 'Nom du cours';
$string['table_customdata'] = 'Données personnalisées';
$string['table_duration'] = 'Durée moyenne';
$string['table_failed'] = 'Échoué';
$string['table_gained'] = 'Temps gagné';
$string['table_host'] = 'Dernier hôte';
$string['table_latency'] = 'Latence';
$string['table_latencyavg'] = 'Latence moyenne';
$string['table_latencymax'] = 'Latence maximale';
$string['table_lock_key'] = 'Clef de verouillage';
$string['table_lockcount'] = 'Nombre';
$string['table_missinglock'] = 'Impossible de trouver l\'enregistrement de verrouillage correspondant.';
$string['table_pid'] = 'PID';
$string['table_processed'] = 'Traité';
$string['table_queuedup'] = 'En file d\'attente';
$string['table_released'] = 'Temps libéré';
$string['table_running'] = 'En cours';
$string['table_task'] = 'Tâche';
$string['task_cleanup'] = 'Nettoyer l\'historique des statistiques de verrouillage';
$string['threshold'] = 'Seuil de l\'historique';
$string['thresholddesc'] = 'Enregistrer les nouvelles entrées d\'historique uniquement lorsque le temps de la tâche périodique dépasse cette valeur.';
