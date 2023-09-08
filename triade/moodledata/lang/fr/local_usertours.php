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
 * Strings for component 'local_usertours', language 'fr', version '4.1'.
 *
 * @package     local_usertours
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actions'] = 'Actions';
$string['appliesto'] = 'Appliquer à';
$string['backdrop'] = 'Afficher avec l\'arrière plan';
$string['backdrop_help'] = 'Vous pouvez utiliser l\'arrière plan pour mettre en évidence la portion de page ciblée.

Note : L\'arrière plan n\'est pas compatible avec certaines portions de page telles que la barre de navigation.';
$string['block'] = 'Bloc';
$string['block_named'] = 'Bloc nommé « {$a} »';
$string['bottom'] = 'En bas';
$string['confirmstepremovalquestion'] = 'Voulez-vous vraiment supprimer cette étape ?';
$string['confirmstepremovaltitle'] = 'Confirmer la suppression de l\'étape';
$string['confirmtourremovalquestion'] = 'Voulez-vous vraiment supprimer cette visite ?';
$string['confirmtourremovaltitle'] = 'Confirmer la suppression de la visite';
$string['content'] = 'Contenu';
$string['cssselector'] = 'Sélecteur CSS';
$string['defaultvalue'] = 'Par défaut ({$a})';
$string['delay'] = 'Délais avant d\'afficher cette étape';
$string['delay_help'] = 'Vous pouvez choisir d\'ajouter un délais avant que cette étape ne soit affichée.

Le délais est indiqué en millisecondes.';
$string['done'] = 'Effectué';
$string['editstep'] = 'Modification de « {$a} »';
$string['enabled'] = 'Activé';
$string['endtour'] = 'Fin du tour';
$string['exporttour'] = 'Exporter la visite';
$string['importtour'] = 'Importer une visite';
$string['left'] = 'À gauche';
$string['movestepdown'] = 'Déplacer l\'étape en bas';
$string['movestepup'] = 'Déplacer l\'étape en haut';
$string['movetourdown'] = 'Déplacer la visite en bas';
$string['movetourup'] = 'Déplacer la visite en haut';
$string['name'] = 'Nom';
$string['newstep'] = 'Nouvelle étape';
$string['newtour'] = 'Créer une nouvelle visite';
$string['next'] = 'Suivant';
$string['orphan'] = 'Afficher si la cible n\'est pas trouvée';
$string['orphan_help'] = 'Afficher l\'étape si la cible ne peut pas être trouvée sur la page.';
$string['pathmatch'] = 'Appliquer à la correspondance d\'URL';
$string['pathmatch_help'] = 'La visite sera affichée sur chaque page dont l\'URL correspond à cette valeur.

Vous pouvez utiliser le caractère % comme joker pour signifier « n\'importe quoi ».
Quelques exemples de valeurs :
* /my/% - correspond au Tableau de bord
* /course/view.php?id=2 - correspond à un cours spécifique
* /mod/forum/view.php% - correspond à la liste du forum de discussion
* /user/profile.php% - correspond à la page de profil de l\'utilisateur';
$string['pausetour'] = 'Pause';
$string['placement'] = 'Emplacement';
$string['placement_help'] = 'Vous pouvez placer une étape soit au-dessus, au-dessous, à gauche ou à droite de la cible.

Les meilleures options sont en haut ou en bas, pour permettre une meilleur affichage sur mobile.';
$string['pluginname'] = 'User Tours';
$string['reflex'] = 'Déplacer au clic';
$string['reflex_help'] = 'Passer à l\'étape suivante quand la cible est cliquée';
$string['resettouronpage'] = 'Réinitialiser la visite sur cette page';
$string['resumetour'] = 'Reprendre';
$string['right'] = 'À droite';
$string['select_block'] = 'Choisir un bloc';
$string['select_targettype'] = 'Chaque étape est associée à une partie de la page que vous devez choisir. Pour rendre cela plus facile, il existe plusieurs types de cibles pour différents types de contenu de la page.
<dl>
<dt>Bloc</dt>
<dd>Affiche l\'étape à coté de la première correspondance du bloc ciblé de la page</dd>
<dt>Selecteur</dt>
<dd>Le sélecteur CSS est un moyen puissant pour vous permettre de sélectionner une portion de page basée sur les meta-données de la page.</dd>
<dt>Afficher au milieu de la page</dt>
<dd>Au lieu d\'associer une étape à une portion spécifique de la page, vous pouvez l\'afficher au milieu de la page.</dd>
</dl>';
$string['selector_defaulttitle'] = 'Entrez un titre descriptif';
$string['selectordisplayname'] = 'Le sélecteur CSS correspondant à « {$a} »';
$string['selecttype'] = 'Sélectionnez un type d\'étape';
$string['sharedtourslink'] = 'Répertoire de visites';
$string['skip'] = 'Passer';
$string['target'] = 'Cible';
$string['target_block'] = 'Bloc';
$string['target_selector'] = 'Sélecteur';
$string['target_selector_targetvalue'] = 'Sélecteur CSS';
$string['target_selector_targetvalue_help'] = 'Vous pouvez utiliser le sélecteur CSS pour cibler preques chaque élément de la page.

Le sélecteur CSS est puissant et vous pouvez facilement déterminer des portions de pages en construisant le sélecteur graduellement.

Mozilla fourni une [très bonne documentation](https://developer.mozilla.org/en/docs/Web/Guide/CSS/Getting_started/Selectors) pour les sélecteurs qui vous aidera à créer vos propre sélecteurs.

Vous trouverez aussi les outils développeur des navigateurs très utiles pour vous aider à créer vos sélecteurs :
* [Google Chrome](https://developer.chrome.com/devtools#dom-and-styles)
* [Mozilla Firefox](https://developer.mozilla.org/en-US/docs/Tools/DOM_Property_Viewer)
* [Microsoft Edge](https://developer.microsoft.com/en-us/microsoft-edge/platform/documentation/f12-devtools-guide/)
* [Apple Safari](https://developer.apple.com/library/iad/documentation/AppleApplications/Conceptual/Safari_Developer_Guide/ResourcesandtheDOM/ResourcesandtheDOM.html#//apple_ref/doc/uid/TP40007874-CH3-SW1)';
$string['target_unattached'] = 'Afficher au milieu de la page';
$string['targettype'] = 'Type de cible';
$string['title'] = 'Titre';
$string['top'] = 'En haut';
$string['tourconfig'] = 'Fichier de configuration de visite à importer';
$string['tourlist_explanation'] = 'Vous pouvez créer autant de visites que vous le souhaitez et les activer pour différentes parties de Moodle. Une seule visite ne peut être activée par page.';
$string['tours'] = 'Visites';
$string['usertours'] = 'User tours';
$string['viewtour_info'] = 'Ceci est la visite « {$a->tourname} ». Elle s\'applique au chemin « {$a->path} ».';
