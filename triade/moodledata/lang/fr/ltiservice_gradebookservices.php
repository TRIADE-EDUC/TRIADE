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
 * Strings for component 'ltiservice_gradebookservices', language 'fr', version '4.1'.
 *
 * @package     ltiservice_gradebookservices
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['alwaysgs'] = 'Utiliser ce service pour la synchronisation des notes et la gestion des colonnes';
$string['grade_synchronization'] = 'Services de devoirs et de notes IMS LTI';
$string['grade_synchronization_help'] = 'Ce réglage détermine si les Services de devoirs et de notes IMS LTI doivent être utilisés pour synchroniser les notes, en lieu et place du service Objectifs.

* **Ne pas utiliser ce service** – Les fonctionnalités de base et réglages des Objectifs seront utilisés
* **N\'utiliser ce service que pour la synchronisation des notes** – Le service insèrera les notes dans des colonnes existantes du carnet de notes, mais ne pourra pas y créer de nouvelles colonnes
* **Utiliser ce service pour la synchronisation des notes et la gestion des colonnes** – Le service pourra créer et modifier les colonnes des carnets de notes et gérer les notes.';
$string['ltiservice_gradebookservices'] = 'Services de devoir et de notes IMS LTI';
$string['modulename'] = 'Notes LTI';
$string['nevergs'] = 'Ne pas utiliser ce service';
$string['partialgs'] = 'N\'utiliser ce service que pour la synchronisation des notes';
$string['pluginname'] = 'Services de devoirs et de notes IMS LTI';
$string['privacy:metadata:externalpurpose'] = 'Ces informations sont envoyées à un fournisseur LTI externe.';
$string['privacy:metadata:feedback'] = 'Le feedback reçu par un utilisateur pour cette activité LTI.';
$string['privacy:metadata:grade'] = 'La note reçue par l\'utilisateur dans Moodle pour cette activité LTI.';
$string['privacy:metadata:maxgrade'] = 'La note maximale possible pour cette activité LTI.';
$string['privacy:metadata:timemodified'] = 'La date et l\'heure de la dernière modification de la note';
$string['privacy:metadata:userid'] = 'L\'identifiant de l\'utilisateur utilisant le client LTI.';
$string['taskcleanup'] = 'Nettoyage des tables des services de devoirs et de notes IMS LTI';
