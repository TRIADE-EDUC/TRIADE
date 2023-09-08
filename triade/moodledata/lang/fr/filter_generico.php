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
 * Strings for component 'filter_generico', language 'fr', version '4.1'.
 *
 * @package     filter_generico
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['appauthorised'] = 'Poodll Cloud est autorisé sur ce site.';
$string['appnotauthorised'] = 'Poodll Cloud n\'est pas autorisé sur ce site.';
$string['bundle'] = 'Paquet';
$string['cleartemplate'] = 'Effacer le modèle';
$string['commonpageheading'] = 'Réglages généraux';
$string['cpapi_heading'] = 'Réglages de l\'API Poodll Cloud';
$string['cpapi_heading_desc'] = 'Poodll Cloud vous permet d\'intégrer des enregistreurs provenant directement de cloud.poodll.com dans des widgets. Cette option est facultative et vous n\'avez pas besoin de la remplir.';
$string['cpapisecret'] = 'Clef secrète API Poodll Cloud';
$string['cpapisecret_details'] = 'Il s\'agit d\'une clef secrète spéciale qui peut être générée à partir de l\'onglet <a href=\'https://support.poodll.com/support/solutions/articles/19000083076-cloud-poodll-api-secret\'>API</a> dans votre espace membre sur Poodll.com.';
$string['cpapiuser'] = 'Utilisateur API Cloud Poodll';
$string['cpapiuser_details'] = 'C\'est le même que votre nom d\'utilisateur sur Poodll.com.';
$string['credentialsinvalid'] = 'Le nom d\'utilisateur et le secret de l\'API saisis n\'ont pas pu être utilisés pour obtenir l\'accès. Veuillez les vérifier.';
$string['dataset'] = 'Jeu de données';
$string['dataset_desc'] = 'Generico vous permet d\'extraire un jeu de données de la base de données pour l\'utiliser dans votre modèle. Il s\'agit d\'une fonctionnalité avancée. Entrez la partie sql d\'un appel $DB->get_records_sql ici.';
$string['datasetvars'] = 'Variables du jeu de données';
$string['datasetvars_desc'] = 'Mettez une liste de variables séparées par des virgules qui constituent les variables pour SQL. Vous pouvez et vous voudrez probablement utiliser des variables ici.';
$string['displaysubs'] = '{$a->subscriptionname} : expire le {$a->expiredate}';
$string['filterdescription'] = 'Convertir les chaînes de filtres en modèles fusionnés avec les données';
$string['filtername'] = 'Generico';
$string['generico:managetemplates'] = 'Gérer les modèles Generico';
$string['genericotemplatesadmin'] = 'Admin des modèles Generico';
$string['jumpcat_explanation'] = 'L\'ensemble complet des paramètres du filtre Generico se trouve <a href="{$a}">ici</a>.';
$string['jumpcat_heading'] = 'Réglages des filtres Generico';
$string['noapisecret'] = 'Aucun mot secret d\'API saisi.';
$string['noapiuser'] = 'Aucun nom d\'utilisateur d\'API saisi.';
$string['notokenincache'] = 'Actualisez les informations de licence Cloud Poodll pour voir les détails.';
$string['pluginname'] = 'Generico';
$string['presets'] = 'Modèle de remplissage automatique avec un préréglage';
$string['presets_desc'] = 'Generico est livré avec des préréglages par défaut que vous pouvez utiliser directement ou pour vous aider à démarrer avec votre propre modèle. Choisissez-en un ici, ou créez simplement votre propre modèle à partir de zéro. Vous pouvez exporter un modèle sous forme de paquet en cliquant sur la case verte ci-dessus. Vous pouvez importer un paquet en le faisant glisser sur la case verte.';
$string['privacy:metadata'] = 'Le plugin filtre Generico n\'enregistre aucune donnée personnelle.';
$string['refreshtoken'] = 'Actualiser les informations de licence Cloud Poodll.';
$string['template'] = 'Le corps du modèle {$a}';
$string['template_desc'] = 'Placer le modèle ici ; définir les variables en les entourant de marques @@ par exemple @@variable@@';
$string['templatealternate'] = 'Contenu alternatif';
$string['templatealternate_desc'] = 'Contenu pouvant être utilisé lorsque le contenu CSS et javascript personnalisé et téléchargé n\'est pas disponible. Actuellement, cela est utilisé lorsque le modèle est traité par un service Web, probablement pour le contenu de l\'application mobile';
$string['templatealternate_end'] = 'Fin de contenu alternative (modèle {$a})';
$string['templatealternate_end_desc'] = 'Fermeture des balises de contenu alternatives pour les modèles qui incluent le contenu utilisateur avec des balises génériques de début et de fin';
$string['templatecount'] = 'Nombre de modèles';
$string['templatecount_desc'] = 'Le nombre de modèles que vous pouvez avoir. La valeur par défaut est 20.';
$string['templatedefaults'] = 'valeurs par défaut des variables (modèle {$a})';
$string['templatedefaults_desc'] = 'Définir les valeurs par défaut dans des ensembles de paires nom=valeur délimités par des virgules. Par ex. largeur=800,hauteur=900,sentiment=joie';
$string['templateend'] = 'Balises de fin (modèle {$a})';
$string['templateend_desc'] = 'Si votre modèle contient du contenu utilisateur, par exemple une boîte d\'informations, placez les balises de fermeture ici. L\'utilisateur saisira quelque chose comme {GENERICO:mytag_end} pour fermer le filtre.';
$string['templateheading'] = 'Paramètres du modèle Generico {$a}';
$string['templateheadingcss'] = 'Paramètres CSS/styles.';
$string['templateheadingjs'] = 'Paramètres Javascript.';
$string['templateinstructions'] = 'Instructions (modèle {$a})';
$string['templateinstructions_desc'] = 'Toutes les instructions saisies ici seront affichées sur le formulaire Atto Generico pour ce modèle. Elles doivent être courtes ou l\'affichage sera mauvais.';
$string['templatekey'] = 'La clef qui identifie le modèle {$a}';
$string['templatekey_desc'] = 'La clef doit être un mot et ne contenir que des chiffres et des lettres, des traits de soulignement, des traits d\'union et des points .';
$string['templatename'] = 'Nom du modèle';
$string['templatename_desc'] = 'Le nom de ce modèle';
$string['templatepageheading'] = 'Modèle : {$a}';
$string['templaterequire_amd'] = 'Charger via AMD';
$string['templaterequire_amd_desc'] = 'AMD est un mécanisme de chargement javascript. Si vous téléchargez ou créez un lien vers des bibliothèques javascript dans votre modèle, vous devrez peut-être décocher cette case. Cela ne s\'applique que pour Moodle 2.9 ou supérieur';
$string['templaterequire_css'] = 'Nécessite CSS (modèle {$a})';
$string['templaterequire_css_desc'] = 'Un lien (1 uniquement) vers un fichier CSS externe requis par ce modèle. Optionnel.';
$string['templaterequire_js'] = 'Nécessite JS (modèle {$a})';
$string['templaterequire_js_desc'] = 'Un lien (1 uniquement) vers un fichier JS externe requis par ce modèle. Optionnel.';
$string['templaterequirejsshim'] = 'Exiger l\'export Shim';
$string['templaterequirejsshim_desc'] = 'Laisser vide sauf si vous savez ce qu\'est le calage (Shim)';
$string['templates'] = 'Modèles';
$string['templatescript'] = 'JS personnalisé (modèle {$a})';
$string['templatescript_desc'] = 'Si votre modèle doit exécuter un javascript personnalisé, saisissez-le ici. Il sera exécuté une fois tous les éléments chargés sur la page.';
$string['templatestyle'] = 'CSS personnalisé (modèle {$a})';
$string['templatestyle_desc'] = 'Saisir ici n\'importe quel CSS personnalisé utilisé par votre modèle. Les variables de modèle ne fonctionneront pas ici ; juste du CSS de base.';
$string['templateupdated'] = '{$a} modèles Poodll mis à jour.';
$string['templateuploadjsshim'] = 'Déposer un export de calage (Shim)';
$string['templateuploadjsshim_desc'] = 'Laisser vide sauf si vous savez ce qu\'est le calage (Shim)';
$string['templateversion'] = 'La version du modèle {$a}';
$string['templateversion_desc'] = 'Utiliser la version sémantique, par exemple Generico 1.0.0. affichera un bouton de mise à jour lorsque la version prédéfinie est supérieure à la version du modèle.';
$string['updateall'] = 'Tout mettre à jour';
$string['updatetoversion'] = 'Mise à jour vers la version : {$a}';
$string['uploadcss'] = 'Déposer un CSS (modèle {$a})';
$string['uploadcss_desc'] = 'Vous pouvez déposer un fichier CSS qui sera chargé pour votre modèle. Seulement un.';
$string['uploadjs'] = 'Déposer un JS (modèle {$a})';
$string['uploadjs_desc'] = 'Vous pouvez déposer un fichier de bibliothèque js qui sera chargé pour votre modèle. Un seul.';
