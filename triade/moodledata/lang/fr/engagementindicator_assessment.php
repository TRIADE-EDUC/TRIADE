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
 * Strings for component 'engagementindicator_assessment', language 'fr', version '4.1'.
 *
 * @package     engagementindicator_assessment
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['dayslate'] = 'Jours de retard';
$string['dayslate_help'] = 'Nombre de jours de retard du devoir. Cette valeur prend en compte toutes les exceptions qui affecterait la date de rendu.';
$string['localrisk'] = 'Risque propre à cette activité';
$string['localrisk_help'] = 'Le pourcentage de risque pour ce devoir. Le risque ponctuel est modifié par le facteur de pondération avant de contribuer au risque total.';
$string['logic'] = 'Logique';
$string['logic_help'] = 'Ce champs fournit des informations sur le calcul du risque propre à cette activité.';
$string['overduegracedays'] = 'Jours de retard tolérés';
$string['overduemaximumdays'] = 'Jours de retard maximum';
$string['overduenotsubmittedweighting'] = 'Facteur de pondération si le devoir n\'est pas remis (dans le délai de retard toléré)';
$string['overduesubmittedweighting'] = 'Facteur de pondération si le devoir est remis en retard';
$string['override'] = 'Exceptions';
$string['override_help'] = 'Certaines activités évaluées (ex. quiz) permettent de configurer des dates de remises par individus ou groupes. Ce champs indique si l\'utilisateur a bénéficié d\'une telle exception.';
$string['pluginname'] = 'Activité dans les devoirs';
$string['riskcontribution'] = 'Contribution au risque';
$string['riskcontribution_help'] = 'La contribution de cette activité au risque global de l\'indicateur lié à l\'indicateur d\'activité dans les devoirs. Il est calculé en multipliant le risque propre de l\'activité par le facteur de pondération. Les contributions de chaque activités sont additionnées pour calculer l\'indicateur.';
$string['status'] = 'Statut';
$string['status_help'] = 'Ce statut indique si l\'utilisateur a complété cette activité ou non.';
$string['weighting'] = 'Facteur de pondération';
$string['weighting_help'] = 'Représente la note maximale de cette activité en pourcentage de la note totale de toutes les activités suivies par l\'indicateur.
Cette valeur est multipliée par le "risque local" de l\'activité pour calculer la contribution au risque global.';
