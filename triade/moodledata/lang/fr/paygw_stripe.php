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
 * Strings for component 'paygw_stripe', language 'fr', version '4.1'.
 *
 * @package     paygw_stripe
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['allowpromotioncodes'] = 'Autoriser les codes de promotion';
$string['apikey'] = 'Clé d\'API';
$string['apikey_help'] = 'La clé d\'API qui sert à nous identifier chez Stripe';
$string['customerdescription'] = 'ID d\'utilisateur Moodle : {$a}';
$string['defaulttaxbehavior'] = 'Comportement par défaut pour les taxes';
$string['defaulttaxbehavior_help'] = 'Comportement par défaut pour les taxes (comprises ou non). Modifiable dans le tableau de bord Stripe.';
$string['enableautomatictax'] = 'Active les taxes automatiques';
$string['enableautomatictax_desc'] = 'Les taxes automatiques doivent être activées et configurées dans le tableau de bord Stripe.';
$string['gatewaydescription'] = 'Stripe est une passerelle de paiement autorisée pour les transactions par carte (crédit et débit).';
$string['paymentcancelled'] = 'Le paiement a été annulé';
$string['paymentmethod:card'] = 'Carte de crédit';
$string['paymentmethods'] = 'Méthodes de paiement';
$string['paymentsuccessful'] = 'Le paiement a réussi';
$string['pluginname_desc'] = 'Le plugin Stripe permet la réception de paiements via Stripe.';
$string['privacy:metadata:stripe_customers'] = 'Enregistre le lien entre les utilisateurs Moodle et les objets clients Stripe';
$string['privacy:metadata:stripe_customers:customerid'] = 'ID de client Stripe';
$string['privacy:metadata:stripe_customers:userid'] = 'ID d\'utilisateur Moodle';
$string['secretkey'] = 'Clef secrète';
$string['secretkey_help'] = 'Clef secrète d\'identification Stripe';
$string['stripeaccount'] = 'ID de compte Stripe';
$string['stripeaccount_help'] = 'Pour la création de la page d\'entreprise de paiement direct';
$string['taxbehavior:exclusive'] = 'Non comprises';
$string['taxbehavior:inclusive'] = 'Comprises';
