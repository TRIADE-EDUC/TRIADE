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
 * Strings for component 'enrol_fee', language 'fr', version '4.1'.
 *
 * @package     enrol_fee
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['assignrole'] = 'Attribuer le rôle';
$string['cost'] = 'Montant de l\'inscription';
$string['costerror'] = 'Le montant de l\'inscription doit être un nombre.';
$string['currency'] = 'Devise';
$string['defaultrole'] = 'Attribution de rôle par défaut';
$string['defaultrole_desc'] = 'Sélectionner le rôle à attribuer aux utilisateurs ayant effectué leur paiement.';
$string['enrolenddate'] = 'Date de fin';
$string['enrolenddate_help'] = 'Si ce réglage est activé, les utilisateurs ne peuvent être inscrits que jusqu\'à cette date.';
$string['enrolenddaterror'] = 'La date de fin d\'inscription ne peut pas être antérieure à la date de début.';
$string['enrolperiod'] = 'Durée de l\'inscription';
$string['enrolperiod_desc'] = 'Durée par défaut de la validité de l\'inscription. Si égale à zéro, la durée par défaut est illimitée.';
$string['enrolperiod_help'] = 'Durée de la validité de l\'inscription, à partir du moment où l\'utilisateur est inscrit. Si ce réglage est désactivé, la durée de validité de l\'inscription est illimitée.';
$string['enrolstartdate'] = 'Date de début';
$string['enrolstartdate_help'] = 'Si ce réglage est activé, les utilisateurs ne peuvent être inscrits qu\'à partir de cette date.';
$string['expiredaction'] = 'Action à l\'échéance de la validité de l\'inscription';
$string['expiredaction_help'] = 'Sélectionner l\'action à effectuer lorsque la validité de l\'inscription arrive à échéance. Veuillez remarquer que certaines données et réglages utilisateur sont supprimés lors de la désinscription.';
$string['fee:config'] = 'Configurer les instances de la méthode Inscription après paiement';
$string['fee:manage'] = 'Gérer les utilisateurs inscrits';
$string['fee:unenrol'] = 'Désinscrire du cours les utilisateurs';
$string['fee:unenrolself'] = 'Se désinscrire soi-même du cours';
$string['nocost'] = 'Il n\'y a pas de montant d\'inscription pour ce cours !';
$string['paymentaccount'] = 'Compte de paiement';
$string['paymentaccount_help'] = 'Les montants d\'inscription seront payés sur ce compte,';
$string['pluginname'] = 'Inscription après paiement';
$string['pluginname_desc'] = 'La méthode d\'inscription Inscription après paiement permet de gérer des cours nécessitant paiement. Si le montant d\'un cours est zéro, il n\'est pas demandé aux étudiants de payer pour s\'inscrire. Un montant d\'inscription par défaut peut être fixé ici comme réglage par défaut pour tout le site. Ce réglage peut être modifié dans chaque cours, et le montant défini dans le cours a priorité sur celui du site.';
$string['privacy:metadata'] = 'Le plugin d\'inscription Inscription après paiement n\'enregistre aucune donnée personnelle.';
$string['purchasedescription'] = 'Inscription au cours {$a}';
$string['sendpaymentbutton'] = 'Sélectionner le type de paiement';
$string['status'] = 'Permettre l\'inscription après paiement';
$string['status_desc'] = 'Permettre par défaut aux utilisateurs d\'effectuer un paiement pour s\'inscrire à un cours.';
