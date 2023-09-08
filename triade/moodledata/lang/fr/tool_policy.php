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
 * Strings for component 'tool_policy', language 'fr', version '4.1'.
 *
 * @package     tool_policy
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['acceptanceacknowledgement'] = 'Je confirme que j\'ai reçu une demande pour donner mon consentement au nom de l\'utilisateur ou des utilisateurs ci-dessus.';
$string['acceptancenote'] = 'Remarques';
$string['acceptancepolicies'] = 'Politiques';
$string['acceptancessavedsucessfully'] = 'Les accords ont été enregistrés.';
$string['acceptancestatusaccepted'] = 'Accepté';
$string['acceptancestatusacceptedbehalf'] = 'Accepté au nom de l\'utilisateur';
$string['acceptancestatusdeclined'] = 'Refusé';
$string['acceptancestatusdeclinedbehalf'] = 'Refusé au nom de l\'utilisateur';
$string['acceptancestatusoverall'] = 'Globalement';
$string['acceptancestatuspartial'] = 'Accepté partiellement';
$string['acceptancestatuspending'] = 'En attente';
$string['acceptanceusers'] = 'Utilisateurs';
$string['actions'] = 'Actions';
$string['activate'] = 'Définir le statut comme « Actif »';
$string['activateconfirm'] = '<p>Vous êtes sur le point d\'activer la politique « <em>{$a->name}</em> » et de faire de la version <em>{$a->revision}</em> la version actuelle.</p><p>Tous les utilisateurs devront accepter cette nouvelle version pour utiliser le site.</p>';
$string['activateconfirmyes'] = 'Activer';
$string['activating'] = 'Activation d\'une politique';
$string['agreepolicies'] = 'Veuillez accepter les politiques suivantes';
$string['backtoprevious'] = 'Revenir à la page précédente';
$string['backtotop'] = 'Retour en haut';
$string['cachedef_policy_optional'] = 'Cache des états optionnel/obligatoire des versions des politiques';
$string['consentbulk'] = 'Consentement';
$string['consentpagetitle'] = 'Consentement';
$string['contactdpo'] = 'Pour toute question en lien avec les politiques, veuillez contacter le délégué à la protection des données.';
$string['dataproc'] = 'Traitement des données personnelles';
$string['declineacknowledgement'] = 'Je confirme que j\'ai reçu une demande de refuser le consentement au nom des utilisateurs ci-dessus.';
$string['declinethepolicy'] = 'Refuser le consentement de l\'utilisateur';
$string['deleteconfirm'] = '<p>Voulez-vous vraiment supprimer la politique « <em>{$a->name}</em> » ?</p><p>Cette opération ne peut pas être annulée.</p>';
$string['deleting'] = 'Suppression d\'une version';
$string['editingpolicydocument'] = 'Modification de politique';
$string['errorpolicyversioncompulsory'] = 'Les politiques obligatoires ne peuvent pas être refusées !';
$string['errorpolicyversionnotfound'] = 'Il n\'y a pas de version de politique avec cet identifiant.';
$string['errorsaveasdraft'] = 'Une modification mineure ne peut pas être enregistrée comme brouillon';
$string['errorusercantviewpolicyversion'] = 'L\'utilisateur n\'a pas accès à cette version de la politique.';
$string['event_acceptance_created'] = 'Accord de politique créé';
$string['event_acceptance_updated'] = 'Accord de politique modifié';
$string['filtercapabilityno'] = 'Permission : ne peut pas donner son accord';
$string['filtercapabilityyes'] = 'Permission : peut donner son accord';
$string['filterplaceholder'] = 'Rechercher un terme ou sélectionner un filtre';
$string['filterpolicy'] = 'Politique : {$a}';
$string['filterrevision'] = 'Version : {$a}';
$string['filterrevisionstatus'] = 'Version : {$a->name} ({$a->status})';
$string['filterrole'] = 'Rôle : {$a}';
$string['filters'] = 'Filtres';
$string['filterstatusdeclined'] = 'État : refusé';
$string['filterstatuspending'] = 'État : en attente';
$string['filterstatusyes'] = 'Statut : accepté';
$string['guestconsent:continue'] = 'Continuer';
$string['guestconsentmessage'] = 'Si vous continuez à consulter ce site web, vous acceptez nos politiques :';
$string['iagree'] = 'J\'accepte la {$a}';
$string['iagreetothepolicy'] = 'Donner son consentement';
$string['idontagree'] = 'Non merci, je refuse la {$a}';
$string['inactivate'] = 'Définir le statut comme « Inactif »';
$string['inactivating'] = 'Désactivation d\'une politique';
$string['inactivatingconfirm'] = '<p>Vous êtes sur le point de désactiver la politique « <em>{$a->name}</em> », version <em>{$a->revision}</em>.</p>';
$string['inactivatingconfirmyes'] = 'Désactiver';
$string['invalidversionid'] = 'Il n\'y a pas de politique avec cet identifiant !';
$string['irevokethepolicy'] = 'Révoquer le consentement';
$string['listactivepolicies'] = 'Liste des politiques actives';
$string['managepolicies'] = 'Gérer les politiques';
$string['minorchange'] = 'Modification mineure';
$string['minorchangeinfo'] = 'Une modification mineure ne change pas le sens de la politique. Les utilisateurs ne doivent pas accepter à nouveau la politique si la modification est marquée comme mineure.';
$string['movedown'] = 'Déplacer vers le bas';
$string['moveup'] = 'Déplacer vers le haut';
$string['mustagreetocontinue'] = 'Avant de continuer, vous devez vous positionner sur toutes ces politiques.';
$string['newpolicy'] = 'Nouvelle politique';
$string['newversion'] = 'Nouvelle version';
$string['noactivepolicies'] = 'Il n\'y a pas de politique ayant une version active.';
$string['nofiltersapplied'] = 'Aucun filtre appliqué';
$string['nopermissiontoagreedocs'] = 'Pas les permissions requises pour accepter les politiques';
$string['nopermissiontoagreedocs_desc'] = 'Désolé, vous n\'avez pas les permissions requises pour accepter les politiques.<br />Vous ne pourrez pas utiliser ce site tant que les politiques suivantes ne sont pas acceptées :';
$string['nopermissiontoagreedocsbehalf'] = 'Pas les permissions requises pour accepter les politiques au nom de cet utilisateur';
$string['nopermissiontoagreedocsbehalf_desc'] = 'Désolé, vous n\'avez pas les permissions requises pour accepter les politiques suivantes au nom de {$a} :';
$string['nopermissiontoagreedocscontact'] = 'Pour plus d\'aide :';
$string['nopermissiontoviewpolicyversion'] = 'Vous n\'avez pas les permissions requises pour consulter cette version de la politique.';
$string['nopolicies'] = 'Il n\'y a pas de politique avec version active pour les utilisateurs enregistrés.';
$string['pluginname'] = 'Politiques';
$string['policiesagreements'] = 'Politiques et accords';
$string['policy:accept'] = 'Accepter les politiques';
$string['policy:acceptbehalf'] = 'Donner le consentement aux politiques au nom de quelqu\'un d\'autre';
$string['policy:managedocs'] = 'Gérer les politiques';
$string['policy:viewacceptances'] = 'Consulter les rapports des accords des utilisateurs';
$string['policydocaudience'] = 'Consentement utilisateur';
$string['policydocaudience0'] = 'Tous les utilisateurs';
$string['policydocaudience1'] = 'Utilisateurs authentifiés';
$string['policydocaudience2'] = 'Utilisateurs anonymes';
$string['policydoccontent'] = 'Politique complète';
$string['policydochdrpolicy'] = 'Politique';
$string['policydochdrversion'] = 'Version du document';
$string['policydocname'] = 'Nom';
$string['policydocoptional'] = 'Accord optionnel';
$string['policydocoptionalno'] = 'Obligatoire';
$string['policydocoptionalyes'] = 'Optionnel';
$string['policydocrevision'] = 'Version';
$string['policydocsummary'] = 'Résumé';
$string['policydocsummary_help'] = 'Ce texte fournit un résumé de la politique, de préférence sous une forme simplifiée et facilement compréhensible, dans un langage simple et accessible.';
$string['policydoctype'] = 'Type';
$string['policydoctype0'] = 'Politique de site';
$string['policydoctype1'] = 'Politique de confidentialité';
$string['policydoctype2'] = 'Politique de tiers';
$string['policydoctype99'] = 'Autre politique';
$string['policydocuments'] = 'Documents de politique';
$string['policynamedversion'] = 'Politique {$a->name} (version {$a->revision} - {$a->id})';
$string['policypriorityagreement'] = 'Afficher cette politique avant d\'afficher d\'autres politiques';
$string['policyversionacceptedinbehalf'] = 'Le consentement à cette politique a été donné en votre nom.';
$string['policyversionacceptedinotherlang'] = 'Le consentement à cette version de la politique a été donné dans une langue différente.';
$string['previousversions'] = '{$a} versions antérieures';
$string['privacy:metadata:acceptances'] = 'Informations sur les accords des utilisateurs aux politiques du site.';
$string['privacy:metadata:acceptances:lang'] = 'La langue utilisée pour afficher la politique lorsque le consentement a été donné.';
$string['privacy:metadata:acceptances:note'] = 'Tous les commentaires ajoutés par un utilisateur lorsqu\'il a donné son consentement au nom d\'un autre utilisateur.';
$string['privacy:metadata:acceptances:policyversionid'] = 'La version de la politique pour laquelle le consentement a été donné.';
$string['privacy:metadata:acceptances:status'] = 'Le statut de l\'accord.';
$string['privacy:metadata:acceptances:timecreated'] = 'La date et l\'heure à laquelle l\'utilisateur a accepté la politique.';
$string['privacy:metadata:acceptances:timemodified'] = 'La date et l\'heure à laquelle l\'utilisateur a modifié son consentement.';
$string['privacy:metadata:acceptances:userid'] = 'L\'utilisateur auquel l\'accord de cette politique correspond.';
$string['privacy:metadata:acceptances:usermodified'] = 'L\'utilisateur qui a donné son accord à la politique, s\'il a été fait au nom d\'un autre utilisateur.';
$string['privacy:metadata:subsystem:corefiles'] = 'L\'outil des politiques enregistre les fichiers inclus dans la politique intégrale et dans son résumé.';
$string['privacy:metadata:versions'] = 'Information de version de la politique.';
$string['privacy:metadata:versions:archived'] = 'Le statut de la politique (active ou inactive).';
$string['privacy:metadata:versions:audience'] = 'Le type des utilisateurs dont le consentement est requis.';
$string['privacy:metadata:versions:content'] = 'Le contenu de cette version de la politique.';
$string['privacy:metadata:versions:contentformat'] = 'Le format du champ de contenu.';
$string['privacy:metadata:versions:name'] = 'Le nom de la politique.';
$string['privacy:metadata:versions:policyid'] = 'La politique à laquelle cette version est associée.';
$string['privacy:metadata:versions:revision'] = 'Le nom de révision de cette version de la politique.';
$string['privacy:metadata:versions:summary'] = 'Le résumé de cette version de la politique.';
$string['privacy:metadata:versions:summaryformat'] = 'Le format du champ de résumé.';
$string['privacy:metadata:versions:timecreated'] = 'La date et l\'heure de la création de cette version de la politique.';
$string['privacy:metadata:versions:timemodified'] = 'La date et l\'heure de la modification de cette version de la politique.';
$string['privacy:metadata:versions:type'] = 'Type de politique';
$string['privacy:metadata:versions:usermodified'] = 'L\'utilisateur qui a modifié la politique.';
$string['privacysettings'] = 'Réglages de confidentialité';
$string['readpolicy'] = 'Veuillez consulter notre {$a}';
$string['refertofullpolicytext'] = 'Veuillez vous référer à la {$a} complète pour consulter le texte.';
$string['response'] = 'Réponse';
$string['responseby'] = 'Répondant';
$string['responseon'] = 'Date';
$string['revokeacknowledgement'] = 'Je confirme que j\'ai reçu une demande de révoquer mon consentement au nom de l\'utilisateur ou des utilisateurs ci-dessus.';
$string['save'] = 'Enregistrer';
$string['saveasdraft'] = 'Enregistrer comme brouillon';
$string['selectpolicyandversion'] = 'Utiliser le filtre ci-dessus pour sélectionner la politique et/ou la version';
$string['selectuser'] = 'Sélection l\'utilisateur {$a}';
$string['selectusersforconsent'] = 'Sélectionner des utilisateurs au nom desquels donner le consentement.';
$string['settodraft'] = 'Créer un brouillon';
$string['status'] = 'Statut de la politique';
$string['status0'] = 'Brouillon';
$string['status1'] = 'Active';
$string['status2'] = 'Inactive';
$string['statusformtitleaccept'] = 'Acceptation de la politique';
$string['statusformtitledecline'] = 'Refus de la politique';
$string['statusformtitlerevoke'] = 'Consentement retiré pour la politique';
$string['statusinfo'] = 'Une politique ayant le statut « Active » doit être acceptée par les utilisateurs, soit lors de leur première connexion, soit lors de leur prochaine connexion si ce sont des utilisateurs existants.';
$string['steppolicies'] = 'Politique {$a->numpolicy} sur {$a->totalpolicies}';
$string['useracceptanceactionaccept'] = 'Accepter';
$string['useracceptanceactionacceptone'] = 'Accepter {$a}';
$string['useracceptanceactionacceptpending'] = 'Accepter les politiques en attente';
$string['useracceptanceactiondecline'] = 'Refuser';
$string['useracceptanceactiondeclineone'] = 'Refuser {$a}';
$string['useracceptanceactiondeclinepending'] = 'Refuser les politiques en attente';
$string['useracceptanceactiondetails'] = 'Détails';
$string['useracceptanceactionrevoke'] = 'Retirer le consentement';
$string['useracceptanceactionrevokeall'] = 'Retirer le consentement pour les politiques acceptées';
$string['useracceptanceactionrevokeone'] = 'Retirer l\'acceptation de {$a}';
$string['useracceptancecount'] = '{$a->agreedcount} sur {$a->userscount} ({$a->percent}%)';
$string['useracceptancecountna'] = 'Non disponible';
$string['useracceptances'] = 'Accords des utilisateurs';
$string['userpolicysettings'] = 'Politiques';
$string['usersaccepted'] = 'Accords';
$string['viewarchived'] = 'Consulter les versions antérieures';
$string['viewconsentpageforuser'] = 'Consultation de cette page au nom de {$a}';
