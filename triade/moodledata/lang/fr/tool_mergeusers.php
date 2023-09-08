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
 * Strings for component 'tool_mergeusers', language 'fr', version '4.1'.
 *
 * @package     tool_mergeusers
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['choose_users'] = 'Choisir les utilisateurs à fusionner';
$string['clear_selection'] = 'Désélectionnez l\'utilisateur actuel';
$string['cligathering:description'] = 'Entrez les ID utilisateur à fusionner : le premier (fromid) vers le second (toid). Les données liées au premier utilisateur seront transférées vers le second, qui intégrera alors toutes les données.';
$string['cligathering:fromid'] = 'ID de l\'utilisateur d\'origine (fromid) :';
$string['cligathering:stopping'] = 'Pour interrompre, tapez Ctrl+C ou entrez -1 dans les deux champs (fromid et toid).';
$string['cligathering:toid'] = 'ID de l\'utilisateur de destination (toid) :';
$string['dbko_no_transactions'] = '<strong>La fusion a échoué !</strong> <br/> Votre moteur de base de données ne supporte pas les transactions. Par conséquent, votre base de données <strong>a été modifiée</strong> et a été laissée dans un état incohérent. <br/>Vérifiez le journal de la fusion et signalez les erreurs aux développeurs de plugin.<br/> Une fois le plugin corrigé par les développeurs et mis à jour, réitérez la fusion pour finaliser.';
$string['dbko_transactions'] = '<strong>La fusion a échoué !</strong> <br/>Votre moteur de base de données supporte les transactions. Par conséquent, la base de données <strong>n\'a pas été modifiée</strong>.';
$string['dbok'] = 'La fusion a réussi';
$string['deleted'] = 'L\'utilisateur d\'ID {$a} a été éliminé';
$string['error_return'] = 'Retourner au formulaire de recherche';
$string['errorsameuser'] = 'Impossible de fusionner le même utilisateur';
$string['errortransactionsonly'] = 'Erreur : le support des transactions est requis, et votre base de données {$a} ne les supporte pas. Si nécessaire, vous pouvez configurer ce module pour que les fusions soient faites sans utiliser les transactions. Ajustez les paramètres en fonction de vos besoins.';
$string['eventusermergedfailure'] = 'Fusion échouée';
$string['eventusermergedsuccess'] = 'Fusion réussie';
$string['excluded_exceptions'] = 'Exceptions à exclure';
$string['excluded_exceptions_desc'] = 'L\'expérience dans ce domaine suggère que ces tables de base de données doivent être exclues du processus de fusion. Voir le fichier README pour plus de détails.<br>Donc, si vous voulez appliquer le comportement par défaut, vous devez choisir « {$a} » afin d\'exclure ces tables du processus de fusion (recommandé).<br>Si vous préférez, vous pouvez choisir les tables que vous souhaitez inclure dans le processus de fusion (non recommandé).';
$string['finishtime'] = 'Fusion terminée à {$a}';
$string['form_description'] = '<p>Vous pouvez rechercher des utilisateurs ici si vous ne connaissez pas leur nom d\'utilisateur / numéro d\'identification. Sinon, vous pouvez développer le formulaire pour saisir ces informations directement. Veuillez consulter l\'aide sur les champs pour en savoir plus</p>';
$string['form_header'] = 'Trouver les utilisateurs à fusionner';
$string['header'] = 'Fusionner deux comptes utilisateur en un';
$string['header_help'] = '<p>Étant donné un utilisateur à supprimer et un utilisateur à conserver, ceci fusionnera toutes les données utilisateur vers le compte de l\'utilisateur à conserver. Les deux utilisateurs doivent exister dans la base d\'utilisateurs de Moodle, et aucun compte n\'est supprimé par cet utilitaire (ceci est laissé au loisir de l\'administrateur).</p><p><strong>N\'utilisez ceci que si vous en comprenez les implications, car les opérations réalisées ici ne sont pas réversibles !</strong></p>';
$string['into'] = 'vers';
$string['invalid_option'] = 'Option de formulaire invalide';
$string['invaliduser'] = 'Utilisateur non valide';
$string['logid'] = 'Pour référence ultérieure, ces données apparaissent dans le journal avec l\'id {$a}.';
$string['logko'] = 'Les erreurs suivantes se sont produites :';
$string['loglist'] = 'Il s\'agit de la liste des fusions, indiquant pour chacune si elle a été effectuée avec succès :';
$string['logok'] = 'Voici les requêtes qui ont été faites sur la base de données :';
$string['mergeusers'] = 'Fusionner des comptes utilisateur';
$string['mergeusers:mergeusers'] = 'Fusionner comptes utilisateur';
$string['mergeusers:view'] = 'Fusionner les comptes utilisateur';
$string['mergeusers_confirm'] = 'Après confirmation, le processus de fusion démarrera.
<br /><strong>Ceci est irréversible !</strong>
Voulez-vous vraiment continuer ?';
$string['mergeusersadvanced'] = '<strong>Saisie directe de l\'utilisateur</strong>';
$string['mergeusersadvanced_help'] = 'Ici, vous pouvez saisir les champs proposés ci-dessous si vous savez exactement quels utilisateurs vous souhaitez fusionner. <br /> <br />
Cliquez sur le bouton « rechercher » pour vérifier / confirmer l\'existence des utilisateurs.';
$string['merging'] = 'Fusion';
$string['newuser'] = 'Utilisateur à conserver';
$string['newuserid'] = 'ID de l\'utilisateur à conserver';
$string['newuseridonlog'] = 'ID de l\'utilisateur conservé';
$string['no_saveselection'] = 'Vous n\'avez pas sélectionné un ancien ou nouvel utilisateur.';
$string['nologs'] = 'Pas de journaux de fusion d\'utilisateurs. Bon pour vous !';
$string['olduser'] = 'Utilisateur à supprimer';
$string['olduserid'] = 'ID de l\'utilisateur à supprimer';
$string['olduseridonlog'] = 'Utilisateur supprimé';
$string['pluginname'] = 'Fusionner des comptes utilisateur';
$string['privacy:metadata'] = 'Le plugin Fusionner des comptes utilisateur n\'enregistre aucune donnée personnelle.';
$string['qa_action_delete_fromid'] = 'Conserver les tentatives du nouvel utilisateur';
$string['qa_action_delete_toid'] = 'Conserver les tentatives de l\'ancien utilisateur';
$string['qa_action_remain'] = 'Ne rien faire : ni fusion ni suppression';
$string['qa_action_remain_log'] = 'Les données utilisateur de la table <strong>{$a}</strong> ne sont pas modifiées.';
$string['qa_action_renumber'] = 'Fusionner les tentatives des utilisateurs et renuméroter';
$string['qa_chosen_action'] = 'Option active pour tentatives de test : {$a}.';
$string['qa_grades'] = 'Notes recalculées pour les tests : {$a}.';
$string['quizattemptsaction'] = 'Résoudre les tentatives d\'un test';
$string['quizattemptsaction_desc'] = 'La fusion de tentative de test peut produire trois situations :
    <ol>
    <li>Seul l\'ancien utilisateur a des tentatives de test. Toutes les tentatives apparaîtront comme si elles avaient été effectuées par le nouvel utilisateur.</li>
    <li>Seul le nouvel utilisateur a des tentatives de test. Tout est correct et il n\'y a rien à faire.</li>
    <li>Les deux utilisateurs ont fait des tentatives pour le même test. <strong>Vous devez choisir quoi faire dans ce cas de conflit</strong>. Les actions peuvent être :
        <ul>
        <li><strong>{$a->renumber}</strong>. Les tentatives de l\'ancien utilisateur sont ajoutées au nouvel utilisateur et renumérotées par l\'heure de début de chaque tentative.</li>
        <li><strong>{$a->delete_fromid}</strong>. Les tentatives de l\'ancien utilisateur sont supprimées. Celles du nouvel utilisateur sont conservées, car considérées comme plus importantes.</li>
        <li><strong>{$a->delete_toid}</strong>. Les tentatives du nouvel utilisateur sont supprimées. Celles de l\'ancien utilisateur sont conservées, car considérées comme plus importantes</li>
        <li><strong>{$a->remain}</strong> (par défaut). Les tentatives ne sont ni fusionnées ni supprimées, mais restent liées à l\'utilisateur qui les a faites. C\'est l\'option la plus sûre, mais elle peut générer des notes de test différentes selon la fusion utilisateur de A à B ou de B à A.</li>
        </ul>
    </li>
    </ol>';
$string['results'] = 'Résultat de la fusion';
$string['review_users'] = 'Confirmez utilisateurs à fusionner';
$string['saveselection_submit'] = 'Sauvegarder la sélection';
$string['searchuser'] = 'Chercher l\'utilisateur';
$string['searchuser_help'] = 'Entrez un nom d\'utilisateur, prénom / nom, adresse e-mail ou identifiant utilisateur pour rechercher des utilisateurs potentiels. Vous pouvez également préciser si vous souhaitez effectuer une recherche dans un champ particulier.';
$string['starttime'] = 'Fusion démarrée à {$a}';
$string['suspenduser_setting'] = 'Suspendre l\'ancien utilisateur';
$string['suspenduser_setting_desc'] = 'S\'il est activé, il suspend l\'ancien utilisateur automatiquement lors d\'un processus de fusion réussi, empêchant l\'utilisateur de se connecter à Moodle (recommandé). S\'il est désactivé, l\'ancien utilisateur reste actif.
    Dans les deux cas, l\'ancien utilisateur n\'aura pas ses données associées.';
$string['tableko'] = 'Table {$a} : mise à jour PAS OK !';
$string['tableok'] = 'Table {$a} : mise à jour OK';
$string['tableskipped'] = 'Pour des raisons de traçabilité ou de sécurité, la table <strong>{$a}</strong> n\'est pas traitée.<br />Pour supprimer ces entrées, supprimez l\'ancien compte utilisateur une fois la fusion réussie.';
$string['timetaken'] = 'La fusion a duré {$a} secondes';
$string['transactions_not_supported'] = 'Pour votre information, votre base de données <strong>ne prend pas en charge les transactions</strong>.';
$string['transactions_setting'] = 'Seules les transactions sont autorisées';
$string['transactions_setting_desc'] = 'Si cette option est activée, les comptes utilisateur ne peuvent être fusionnés que si votre base de données prend en charge les transactions (recommandé). Avec cette option activée, vous vous assurez que la base de données reste toujours dans un état cohérent, même si une fusion se termine avec des erreurs.<br /> Si cette option est désactivée, vous pourrez fusionner des comptes utilisateur sans utiliser de transactions.
En cas d\'erreur, le journal de la fusion montrera quel était le problème. Si vous signalez cette erreur aux développeurs de ce plugin, une solution devrait être trouvée rapidement.<br />Notez que ce plugin gère tous les composants standard de Moodle, et quelques plugins non standards. Par conséquent, si vous avez une installation de Moodle standard, vous pouvez exécuter ce plugin sans problème avec cette option activée ou désactivée.';
$string['transactions_supported'] = 'Pour votre information, votre base de données <strong>prend en charge les transactions</strong>.';
$string['uniquekeynewidtomaintain'] = 'Conserver les données du nouvel utilisateur';
$string['uniquekeynewidtomaintain_desc'] = 'En cas de conflit, comme lorsque la colonne associée à user.id est une clé unique, ce plugin conservera les données du nouvel utilisateur (par défaut). Cela signifie également que les données de l\'ancien utilisateur seront supprimées pour conserver la cohérence. Sinon, si vous décochez cette option, les données de l\'ancien utilisateur seront conservées.';
$string['usermergingheader'] = '&laquo;{$a->username}&raquo; (ID utilisateur = {$a->id})';
$string['userreviewtable_legend'] = '<b>Vérifiez les utilisateurs à fusionner</b>';
$string['userselecttable_legend'] = '<b>Sélectionnez les utilisateurs à fusionner</b>';
$string['viewlog'] = 'Voir le journal des fusions';
$string['wronglogid'] = 'Il n\'existe aucun enregistrement correspondant à votre choix.';
