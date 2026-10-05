<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list.inc.php,v 1.2 2023/11/17 09:34:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $objects_type, $id;

$directory = '';

if (!isset($id)) $id = 0;

if (strpos($objects_type, 'demandes') !== false) $directory = 'demandes';
if (strpos($objects_type, 'loans') !== false) $directory = 'loans';
if (strpos($objects_type, 'resa_planning') !== false) $directory = 'resa_planning';
if (strpos($objects_type, 'reservations') !== false) $directory = 'reservations';
if (strpos($objects_type, 'scan_requests') !== false) $directory = 'scan_requests';
if (strpos($objects_type, 'suggestions') !== false) $directory = 'suggestions';
if (strpos($objects_type, 'collstate') !== false) $directory = 'collstate';

lists_controller::proceed_manage_ajax($id, $objects_type, 'opac/'.$directory);