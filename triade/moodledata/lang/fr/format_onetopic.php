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
 * Strings for component 'format_onetopic', language 'fr', version '4.1'.
 *
 * @package     format_onetopic
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['asbrother'] = 'Même niveau que dans l\'onglet précédent';
$string['aschild'] = 'Descendant de l\'onglet précédent';
$string['asprincipal'] = 'Normal, comme l\'onglet de premier niveau';
$string['bgcolor'] = 'couleur d\'arrière plan';
$string['bgcolor_help'] = 'Utilisé pour changer la couleur de fond de l\'onglet. La valeur peut être une représentation de couleur en CSS valide, par exemple : <ul><li>Hexadecimale : #ffffff</li><li>RGB : rgb(0,255,0)</li><li>Nom : green</li></ul>';
$string['cantcreatesection'] = 'Erreur durant la création d\'un nouveau sujet';
$string['coursedisplay'] = 'Mode de visualisation de la section 0';
$string['coursedisplay_help'] = 'Ceci définit comment afficher la section 0 : comme un premier onglet ou comme une section avant la barre d\'onglets.';
$string['coursedisplay_multi'] = 'Avant les onglets';
$string['coursedisplay_single'] = 'Comme un onglet';
$string['creating_section'] = 'Création du nouveau sujet';
$string['cssstyles'] = 'Propriétés CSS';
$string['cssstyles_help'] = 'Utilisé pour changer les propriétés CSS de l\'onglet. Utilise une valeur standard de l\'attribut <em>style</em> dans une balise html. Exemple : <br /><strong>font-weight: bold; font-size: 16px;</strong>';
$string['currentsection'] = 'Cette section';
$string['disable'] = 'Désactiver';
$string['disableajax'] = 'Édition manuelle';
$string['disableajax_help'] = 'Activer cette option pour déplacer des ressources entre les onglets. Cette activation est temporaire et valable pour la durée de la session en cours.';
$string['duplicate'] = 'Dupliquer';
$string['duplicate_confirm'] = 'Voulez-vous vraiment dupliquer la section ?
La durée de cette opération dépend du nombre de ressources et peut être assez longue.';
$string['duplicatesection'] = 'Dupliquer la section actuelle';
$string['duplicatesection_help'] = 'Permet de dupliquer les ressources de la section en cours dans une nouvelle section.';
$string['duplicating'] = 'Duplication en cours…';
$string['enable'] = 'Activer';
$string['error_nosectioninfo'] = 'Le sujet indiqué n\'a pas d\'information';
$string['firsttabtext'] = 'Le texte du premier onglet dans le niveau du dessous';
$string['firsttabtext_help'] = 'Si cet onglet a des sous niveaux, ce sera le texte du premier onglet';
$string['fontcolor'] = 'Couleur de police';
$string['fontcolor_help'] = 'Utilisé pour changer la couleur de la police de l\'onglet. La valeur peut être une représentation de couleur en CSS valide, par exemple : <ul><li>Hexadecimale : #ffffff</li><li>RGB : rgb(0,255,0)</li><li>Nom : green</li></ul>';
$string['hidden_message'] = 'La section <em>{$a}</em> n\'est actuellement pas disponible.';
$string['hiddensectionshelp'] = 'Sections cachées affichées sous forme repliée avec message de disponibilité';
$string['hidefromothers'] = 'Cacher la section';
$string['hidetabsbar'] = 'Cacher la barre d\'onglets';
$string['hidetabsbar_help'] = 'Cache la barre d\'onglets pour tout le monde. La navigation se fait à travers le bloc navigation.';
$string['index'] = 'Index';
$string['level'] = 'Profondeur';
$string['level_help'] = 'Changer la profondeur de l\'onglet';
$string['movesectionto'] = 'Déplacer la section en cours';
$string['movesectionto_help'] = 'Déplacer la section en cours avant/après la section sélectionnée';
$string['page-course-view-topics'] = 'Toutes les pages principales de cours dans le format onetopic';
$string['page-course-view-topics-x'] = 'Toutes les pages de cours dans le format onetopic';
$string['pluginname'] = 'Une section par onglet';
$string['privacy:metadata'] = 'Le plugin format une section par onglet n\'enregistre aucune donnée personnelle.';
$string['progress_counter'] = 'Duplication des activités ({$a->current}/{$a->size})';
$string['progress_full'] = 'Duplication du sujet';
$string['rebuild_course_cache'] = 'Reconstruction du cache de cours';
$string['sectionname'] = 'Section';
$string['showfromothers'] = 'Afficher la section';
$string['subtopictoright'] = 'Déplacer à droite comme sous sujet';
$string['templatetopic'] = 'Utiliser le résumé de la section comme modèle';
$string['templatetopic_help'] = 'Cette option est utilisée afin de faire de la « section-résumé » un modèle. Si celui-ci est utilisé, il est possible d\'inclure une ressource dans le contenu, pas simplement en tant que liste traditionnelle de Moodle. <br/> Afin d\'inclure une ressource, écrire le nom de la ressource entre double crochets, par exemple : [[Forum des nouvelles]]. Cette fonctionnalité est similaire au filtre par nom de l\'activité, cependant, elle est différente dans le sens où l\'utilisateur peut choisir si il inclue l\'icône de la ressources et si les activités sont incluses';
$string['templatetopic_icons'] = 'Afficher les icônes dans les lien vers les ressources dans le résumé';
$string['templatetopic_icons_help'] = 'Cette option définie si les icônes sont affichées dans le résumé lorsque que celui-ci est un modèle.';
$string['templetetopic_list'] = 'Oui, utiliser le résumé comme modèle, lister les ressources qui ne sont pas référencées';
$string['templetetopic_not'] = 'Non, afficher par défaut';
$string['templetetopic_single'] = 'Oui, utiliser le résumé comme modèle';
$string['utilities'] = 'Options';
