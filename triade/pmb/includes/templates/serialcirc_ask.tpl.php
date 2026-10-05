<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_ask.tpl.php,v 1.7 2020/05/11 12:01:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $biblio_name, $msg, $charset, $current_module, $serialcirc_inscription_accepted_mail, $serialcirc_inscription_no_mail, $serialcirc_inscription_end_mail;

if(!isset($biblio_name)) $biblio_name = '';

$serialcirc_inscription_accepted_mail="
<p>Bonjour,</p>
<p>La demande d'inscription concernant le périodique !!issue!! a été acceptée.
</p>
<p>Cordialement,<br />
$biblio_name</p>";


$serialcirc_inscription_no_mail="
<p>Bonjour,</p>
<p>La demande d'inscription concernant le périodique !!issue!! a été refusée.
</p>
<p>Cordialement,<br />
$biblio_name</p>";


$serialcirc_inscription_end_mail="
<p>Bonjour,</p>
<p>La désinscription concernant le périodique !!issue!! a été acceptée.
</p>
<p>Cordialement,<br />
$biblio_name</p>";
