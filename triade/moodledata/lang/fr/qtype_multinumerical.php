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
 * Strings for component 'qtype_multinumerical', language 'fr', version '4.1'.
 *
 * @package     qtype_multinumerical
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['answer'] = 'Votre réponse : {$a}';
$string['badfeedbackperconditionsyntax'] = 'Chaque ligne doit être de la forme : &quot;Commentaire si la condition est vraie | Commentaire si la condition est fausse&quot;';
$string['badnumfeedbackperconditions'] = 'Le nombre de commentaires par contrainte ne peut pas être supérieur au nombre de contraintes';
$string['binarygrade'] = 'Calcul de la note';
$string['conditionnotverified'] = 'Contrainte non vérifiée';
$string['conditions'] = 'Contraintes';
$string['conditionverified'] = 'Contrainte vérifiée';
$string['displaycalc'] = 'Afficher le résultat du calcul';
$string['feedbackperconditions'] = 'Commentaire par contrainte';
$string['gradebinary'] = 'Tout ou rien';
$string['gradefractional'] = 'Fractionnaire';
$string['helponquestionoptions'] = 'Pour plus d\'informations sur ce type de question et le comportement des options suivantes, veuillez cliquer sur le bouton d\'aide en haut de ce formulaire.';
$string['noncomputable'] = '(réponses correctes non calculables automatiquement)';
$string['onlyforcalculations'] = 'Uniquement pour les calculs';
$string['parameters'] = 'Paramètres';
$string['pleaseenterananswer'] = 'Veuillez saisir une réponse';
$string['pluginname'] = 'Multinumérique';
$string['pluginname_help'] = '<h2>Comment cela fonctionne</h2>
<p>Une question multinumérique permet de demander aux étudiants une réponse composée de plusieurs paramètres (numériques).</p>
<p><strong>Exemple :</strong> trouvez <span style="font-family:monospace">X</span> et <span style="font-family:monospace">Y</span> tels que </p>
<ul><li>X + Y &lt; 20</li><li>X * Y &gt; 35</li></ul>
<p>Il y a <em>potentiellement</em> plusieurs réponses correctes à cette question, et toute réponse satisfaisant à ces conditions doit être considérée comme correcte.</p>
<p>Ce type de question permet ensuite de définir les paramètres que l\'on recherche (ici, <span style="font-family:monospace">X</span> et <span style="font-family:monospace"> Y</span>) et les contraintes données.</p>
<h2>Utilisation</h2>
<ul>
	<li>Entrez une liste de paramètres séparés par des virgules (dans notre exemple &quot;<span style="font-family:monospace">X,Y</span>&quot;).<br />
<strong>Remarque :</strong> des unités peuvent être saisies après chaque paramètre :
&quot;<span style="font-family:monospace">X [m],Y [h]</span>&quot; (un espace doit exister entre le paramètre et son unité).</li>
<li>Entrez les contraintes, une par ligne ; dans notre exemple : <pre>X + Y &lt; 20
X * Y &gt; 35</pre>(les lignes vides seront ignorées)
    <p>Les opérateurs disponibles sont : <ul>
        <li>&quot;<span style="font-family:monospace">=</span>&quot; (égalité)</li>
        <li>&quot;<span style="font-family:monospace">&lt;</span>&quot; (moins de)</li>
        <li>&quot;<span style="font-family:monospace">&lt;=</span>&quot; (inférieur ou égal)</li>
        <li>&quot;<span style="font-family:monospace">&gt;</span>&quot; (supérieur à)</li>
        <li>&quot;<span style="font-family:monospace">&gt;=</span>&quot; (supérieur ou égal)</li>
        <li>intervalles :
            <pre><span style="font-family:monospace">X = [1;5]</span></pre> (fermé)
            <pre><span style="font-family:monospace">X = ]1;5[</span></pre> (ouvert)
        </li>
    </ul></p></li>
<li>Entrez si vous le souhaitez un retour pour chaque contrainte. Dans notre exemple, on pourrait saisir :
    <pré>OK : X + Y &lt; 20 | Non, X + Y &gt;= 20 !
OK : X * Y &gt; 35 | Non, X + Y &lt;= 35 !</pre>
    </li>
    <li>Si &quot;Afficher le résultat du calcul&quot; est cochée, le retour affichera une évaluation numérique de chacune des contraintes.
    Ceci n\'est affiché que si le retour pour cette contrainte n\'est pas vide.<br />
    Si &quot;Uniquement pour les calculs&quot; est cochée, cela ne sera pas affiché pour les contraintes non calculées (telles que <span style="font-family:monospace">X&nbsp;>&nbsp;5</span>), afin de ne pas donner la réponse à la apprenant.</li>
    <li>Le &quot;Calcul des notes&quot; L\'option définit si une réponse partiellement correcte donne une fraction de la note ou la note nulle.</li>
</ul>';
$string['pluginname_link'] = 'question/type/multinumerical';
$string['pluginnameadding'] = 'Ajouter une question multinumérique';
$string['pluginnameediting'] = 'Modification d\'une question multinumérique';
$string['pluginnamesummary'] = 'Permet de créer une question dont les bonnes réponses peuvent être nombreuses, régies par des équations ou des inéquations.';
$string['privacy:metadata'] = 'Le plugin Type de question Multinumérique ne stocke aucune donnée personnelle.';
$string['qtypeoptions'] = 'Options spécifiques au type de question multinumérique';
$string['usecolorforfeedback'] = 'Utiliser de la couleur pour les commentaires par contrainte';
