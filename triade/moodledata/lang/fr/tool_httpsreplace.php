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
 * Strings for component 'tool_httpsreplace', language 'fr', version '4.1'.
 *
 * @package     tool_httpsreplace
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['complete'] = 'Terminé';
$string['count'] = 'Nombre de contenus intégrés';
$string['disclaimer'] = 'Je comprends les risques de cette opération';
$string['doclink'] = 'Outil de conversion HTTPS';
$string['doit'] = 'Effectuer la conversion';
$string['domain'] = 'Domaine problématique';
$string['domainexplain'] = 'Lorsqu\'un site passe de HTTP à HTTPS, tous les contenus HTTP intégrés au site ne fonctionnent plus. Cet outil vous permet de convertir les contenus intégrés de HTTP à HTTPS.

Avant d\'effectuer la conversion, les contenus seront analysés pour découvrir toutes les URLs qui ne fonctionneraient plus après la conversion. Il est recommandé de vérifier si chacune d\'entre elles possède une version HTTPS ou de la remplacer par une autre ressource.';
$string['domainexplainhelp'] = 'Ces domaines sont présents dans vos contenus et ne supportent pas HTTPS. Après le passage en HTTPS, les contenus intégrés de ces sites ne s\'afficheront plus dans Moodle pour les utilisateurs qui emploient des navigateurs modernes et sûrs. Il est possible que ces sites soit temporairement indisponibles (ou de façon permanente), et qu\'ils ne fonctionnent plus quel que soit le niveau de sécurité. Ne continuez la conversion qu\'après avoir contrôlé ces résultats et déterminé si ces contenus externes sont indispensables ou non. Dans tous les cas, ces contenus ne pourraient plus être atteints après le passage à HTTPS.';
$string['httpwarning'] = 'Ce site fonctionne encore avec HTTP. Vous pouvez malgré tout lancer cet outil et modifier les contenus externes à HTTPS, mais les ressources internes resteront en HTTP. Vous devrez relancer cet outil après avoir passé en HTTPS, afin de convertir les contenus internes.';
$string['notimplemented'] = 'Cette fonctionnalité n\'est pas implémentée par le pilote de votre base de données.';
$string['oktoprocede'] = 'L\'analyse n\'a trouvé aucun problème dans vos contenus. Vous pouvez continuer la conversion des contenus HTTP vers HTTPS.';
$string['pageheader'] = 'Modifier les URLs des contenus externes en HTTPS';
$string['pluginname'] = 'Outil de conversion HTTPS';
$string['privacy:metadata'] = 'Le plugin Outil de conversion HTTPS n\'enregistre aucune donnée personnelle.';
$string['replacing'] = 'Remplacement du contenu HTTP par HTTPS…';
$string['searching'] = 'Recherche de {$a}';
$string['takeabackupwarning'] = 'Attention ! Les modifications effectuées par cet outil ne sont pas réversibles. Il est vivement recommander d\'effectuer une sauvegarde de la plateforme avant de le lancer, un risque minime subsiste que des contenus soit modifiés de manière erronée.';
$string['toolintro'] = 'Si vous planifiez de passer votre site au protocole HTTPS, vous pouvez utiliser l\'<a href="{$a}">outil de conversion HTTPS</a> pour convertir votre contenu intégré vers HTTPS.';
