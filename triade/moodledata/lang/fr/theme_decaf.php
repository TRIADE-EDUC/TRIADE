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
 * Strings for component 'theme_decaf', language 'fr', version '4.1'.
 *
 * @package     theme_decaf
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['alwaysexpandsiteadmin'] = 'Toujours peupler le menu administration';
$string['alwaysexpandsiteadmindesc'] = 'Activer cette option aura pour effet de peupler le menu administration (si applicable), au détriment de la performance.';
$string['awesomebarsettings'] = 'Awesomebar / Paramètre de navigation';
$string['awesomebarsettingsdesc'] = 'La « Awesomebar » est la barre de navigation noir qui est fixé dans le haut de la fenêtre du navigateur quand le thème Decaf est utilisé.<br>Les paramètres suivants sont en lien avec la « Awesome bar » et la navigation.';
$string['backgroundcolor'] = 'Couleur de fond';
$string['backgroundcolordesc'] = 'Ceci définit la couleur de fond pour le thème.';
$string['choosereadme'] = '<div class="clearfix">
<div class="well">
<h2>Decaf</h2>
<p><img class=img-polaroid src="decaf/pix/screenshot.jpg" /></p>
</div>
<div class="well">
<h3>À propos</h3>
<p>Decaf est un thème de trois colonnes fluides pour Moodle qui a été créé par <a href="http://moodle.org/user/view.php?id=390408">Lei Zhang</a>.
Il améliore la facilité d\'utilisation en déplaçant des blocs de réglage en haut de la page, dans une section appelé la barre « awesome ».
Pou les développeurs , il transfère les informations de performance dans une barre flottante coloré au bas de la page , qui affiche des informations de la performance et qui améliore la facilité d\'utilisation.</p>
<h3>Parents</h3>
<p>Ce thème est basé sur le thème Bootstrap , qui a été créé pour Moodle 2.5 , avec l\'aide de:<br>
Stuart Lamour, Mark Aberdour, Paul Hibbitts, Mary Evans.</p>
<h3>Crédits</h3>
<p>Auteurs: Paul Nicholls, Lei Zhang</p>
<h3>Signaler un problème:</h3>
<p><a href="https://github.com/CTANZ/moodle-theme_decaf/issues">Github</a></p>
</div></div>';
$string['configtitle'] = 'Paramètres de thème Decaf';
$string['coursesleafonly'] = 'Remplir le menu « Cours »';
$string['coursesleafonlydesc'] = 'Sur les sites avec un grand nombre de cours , générer la liste complète des cours dans la AwesomeBar peut causer des problèmes de performance . Désactivez ce paramètre ( le régler sur « Non » ) pour éviter la liste d\'être produite - le lien « Cours " restera affiché et sera un moyen rapide d\' accéder à la liste de cours .';
$string['coursesloggedinonly'] = 'Masquer le menu « Cours » aux utilisateurs non-connectés';
$string['coursesloggedinonlydesc'] = 'Si vous ne voulez pas que les utilisateurs non-connectés voient le menu " Cours " dans la AwesomeBar, activez ce paramètre.';
$string['customcss'] = 'CSS personnalisé';
$string['customcssdesc'] = 'Tous les fichiers CSS que vous entrez ici seront ajoutés à chaque page, permettant de personnaliser facilement votre ce thème .';
$string['custommenuafterawesomebar'] = 'Placez les menus personnalisés après l\'AwesomeBar';
$string['custommenuafterawesomebardesc'] = 'Placer les menus personnalisés après le contenu de l\'AwesomeBar. Sinon, ils apparaîtront devant les menus Paramètres. (Applicable uniquement si l\'option précédente est activée. )';
$string['custommenuinawesomebar'] = 'Ajouter les menus personnalisés à l\'AwesomeBar';
$string['custommenuinawesomebardesc'] = 'Déplace les menus personnalisé dans l\'AwesomeBar . Sinon, ils apparaîtront sous l\'en-tête.';
$string['editingsettings'] = 'Paramètres du mode d\'édition';
$string['editingsettingsdesc'] = 'Les paramètres suivants se rapportent au mode d\'édition et visent à le rendre plus net et plus facile à utiliser.<br />Le paramètre « Utilisez les boutons Modifier » des versions précédentes de Decaf a été remplacé par « Activité les menu de modifications » sous Apparence / AJAX et Javascript ($CFG->modeditingmenu) .';
$string['expandtoactivities'] = 'Développer pour montrer les activités des sections';
$string['expandtoactivitiesdesc'] = 'L\'activation de cette option affiche activités des sections dans l\'AwesomeBar , mais dégrade les performances - en particulier dans les cours avec beaucoup d\'activités / ressources.';
$string['footnote'] = 'Note de pied de page';
$string['footnotedesc'] = 'Ce contenu sera affiché dans le pied de page de chaque page.';
$string['hidenavigationblock'] = 'Cacher le bloc Navigation';
$string['hidenavigationblockdesc'] = 'Ce paramètre retire le bloc de Navigation standard dans toutes les page.';
$string['hidesettingsblock'] = 'Cacher le bloc Paramètres';
$string['hidesettingsblockdesc'] = 'Ce paramètre retire le bloc de Paramètres standard dans toutes les page.';
$string['logo'] = 'Logo';
$string['logodesc'] = 'Télécharger votre logo personnalisé ici si vous voulez ajouter à l\'en-tête.<br>Si la hauteur de votre logo est plus que 75px ajouter la règle CSS suivant à la boîte CSS personnalisé ci-dessous.<br>
a.logo {height : 100px;} (Remplacer 100px par la hauteur de votre logo)';
$string['persistentedit'] = 'Activer le mode édition persistante';
$string['persistenteditdesc'] = 'Le mode d\'édition persistante maintient le mode d\'édition allumé en permanence pour les utilisateurs avec des privilèges d\'édition.<br />Le mode d\'édition peut encore être éteint et rallumé indépendamment via l\'option « Activer / Désactiver le mode édition » dans les paramètres du cours.';
$string['pluginname'] = 'Decaf';
$string['region-side-post'] = 'Droit';
$string['region-side-pre'] = 'Gauche';
$string['regionwidth'] = 'Largeur de colonnes';
$string['regionwidthdesc'] = 'Ceci définit la largeur des deux zones de blocs qui forment les colonnes de gauche et de droite.';
$string['showuserpicture'] = 'Montrez l\'image de l\'utilisateur';
$string['showuserpicturedesc'] = 'Ce paramètre ajoute l\'image de l\'utilisateur dans l\'en-tête de la page';
$string['usemodchoosertiles'] = 'Utiliser le sélecteur de tuiles';
$string['usemodchoosertilesdesc'] = 'Dans le sélecteur de tuiles, les modules seront affichés comme «tuiles» (semblables aux icônes sur le bureau de la plupart des systèmes d\'exploitation) .';
