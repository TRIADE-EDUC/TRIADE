<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: crypto_plugin.tpl.php,v 1.3 2023/01/03 09:51:33 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) {
    die("no access");
}

global $base_path, $current_module, $msg;

$crypto_plugin_templates['no_access'] = "
<h1 class='section-title' >".plugins::get_message('crypto', "crypto_menu").plugins::get_message('crypto', "breadcrumb_separator").plugins::get_message('crypto', "crypto_sub_menu_key_generation") ."</h1>
<div class ='row'><h3 class='warning'>".plugins::get_message('crypto', "crypto_no_access")."</h3></div>";

$crypto_plugin_templates['key_generation'] = "
<h1 class='section-title' >".plugins::get_message('crypto', "crypto_menu").plugins::get_message('crypto', "breadcrumb_separator").plugins::get_message('crypto', "crypto_sub_menu_key_generation") ."</h1>
<form class='form-".$current_module."' id='crypto_key_generation_form' name='crypto_key_generation_form'  method='post' action='".$base_path."/admin.php?categ=plugin&plugin=crypto&sub=key_generation' >
    <div class='form-contenu'>

        <!-- crypto_keys_already_defined --> 

        <div class='row'>
            <label class='etiquette' for='crypto_public_key' >".plugins::get_message('crypto', "crypto_public_key")."</label>
        </div>
        <div class='row'>
            <textarea id='crypto_public_key' class='saisie-50emr' cols='40' rows='15' ><!-- crypto_public_key_value --></textarea>
        </div>

        <div class='row'>
            <label class='etiquette' for='crypto_private_key' >".plugins::get_message('crypto', "crypto_private_key")."</label>
        </div>
        <div class='row'>
            <textarea id='crypto_private_key' class='saisie-50emr' cols='40' rows='55' ><!-- crypto_private_key_value --></textarea>
        </div>

        <div class='row'></div>
        <div class='row'>
            <div class='left'>
                <input type='submit' class='bouton' value='".$msg['804']."'/>
            </div>
        </div>

    </div>
    <div class='row'></div>
</form>";


$crypto_plugin_templates['keys_already_defined'] = "
<h3 class='warning'>".plugins::get_message('crypto', 'crypto_keys_already_defined')."</h3>";


$crypto_plugin_templates['data_encryption'] = "
<h1 class='section-title' >".plugins::get_message('crypto', "crypto_menu").plugins::get_message('crypto', "breadcrumb_separator").plugins::get_message('crypto', "crypto_sub_menu_data_encryption") ."</h1>
<form class='form-".$current_module."' id='crypto_data_encryption_form' name='crypto_data_encryption_form'  method='post' action='".$base_path."/admin.php?categ=plugin&plugin=crypto&sub=data_encryption&action=encrypt' >
    <div class='form-contenu'>

        <!-- crypto_keys_not_defined --> 

        <!-- Données à chiffrer -->
        <div class='colonne2'>
            <div class='row'>
                <label class='etiquette' for='crypto_data_to_encrypt' >".plugins::get_message('crypto', "crypto_data_to_encrypt")."</label>
            </div>
            <div class='row'>
                <textarea id='crypto_data_to_encrypt' name='crypto_data_to_encrypt' class='saisie-80em' cols='80' rows='20' ><!-- crypto_data_to_encrypt --></textarea>
            </div>
        </div>

        <!-- Données chiffrées -->
        <div class='colonne_suite'>
            <div class='row'>
                <label class='etiquette' for='crypto_encrypted_data' >".plugins::get_message('crypto', "crypto_encrypted_data")."</label>
            </div>
            <div class='row'>
                <textarea id='crypto_encrypted_data' class='saisie-80em' cols='80' rows='20' readonly ><!-- crypto_encrypted_data --></textarea>
            </div>
        </div>

        <div class='row'>
            <div class='left'>
                <input type='submit' class='bouton' value='".plugins::get_message('crypto', 'crypto_encrypt')."' />
            </div>
        </div>

    </div>
    <div class='row'></div>
</form>

<form class='form-".$current_module."' id='crypto_data_decryption_form' name='crypto_data_decryption_form'  method='post' action='".$base_path."/admin.php?categ=plugin&plugin=crypto&sub=data_encryption&action=decrypt' >
    <div class='form-contenu'>

        <!-- Données à déchiffrer -->
        <div class='colonne2'>
            <div class='row'>
                <label class='etiquette' for='crypto_data_to_decrypt' >".plugins::get_message('crypto', "crypto_data_to_decrypt")."</label>
            </div>
            <div class='row'>
                <textarea id='crypto_data_to_decrypt' name='crypto_data_to_decrypt' class='saisie-80em' cols='80' rows='20' ><!-- crypto_data_to_decrypt --></textarea>
            </div>
        </div>

        <!-- Données déchiffrées -->
        <div class='colonne_suite'>
            <div class='row'>
                <label class='etiquette' for='crypto_decrypted_data' >".plugins::get_message('crypto', "crypto_decrypted_data")."</label>
            </div>
            <div class='row'>
                <textarea id='crypto_decrypted_data' class='saisie-80em' cols='80' rows='20' readonly ><!-- crypto_decrypted_data --></textarea>
            </div>
        </div>

        <div class='row'>
            <div class='left'>
                <input type='submit' class='bouton' value='".plugins::get_message('crypto', 'crypto_decrypt')."'/>
            </div>
        </div>

    </div>
    <div class='row'></div>
</form>";


$crypto_plugin_templates['keys_not_defined'] = "
<h3 class='warning'>".plugins::get_message('crypto', 'crypto_keys_not_defined')."</h3>";



