<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list.inc.php,v 1.5 2020/11/05 12:48:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $objects_type, $class_path, $id;

$type = $objects_type;
$directory = '';

if (!isset($id)) $id = 0;

if (strpos($objects_type, 'accounting') !== false) $directory = 'accounting';
if (strpos($objects_type, 'bannettes') !== false) $directory = 'bannettes';
if (strpos($objects_type, 'configuration') !== false) $directory = 'configuration';
if (strpos($objects_type, 'custom_fields') !== false) $directory = 'custom_fields';
if (strpos($objects_type, 'demandes') !== false) $directory = 'demandes';
if (strpos($objects_type, 'fiches') !== false) $directory = 'fiches';
if (strpos($objects_type, 'frbr') !== false) $directory = 'frbr';
if (strpos($objects_type, 'loans') !== false) $directory = 'loans';
if (strpos($objects_type, 'readers') !== false) $directory = 'readers';
if (strpos($objects_type, 'records') !== false) $directory = 'records';
if (strpos($objects_type, 'resa_planning') !== false) $directory = 'resa_planning';
if (strpos($objects_type, 'reservations') !== false) $directory = 'reservations';
if (strpos($objects_type, 'scan_requests') !== false) $directory = 'scan_requests';
if (strpos($objects_type, 'suggestions') !== false) $directory = 'suggestions';
if (strpos($objects_type, 'transferts') !== false) $directory = 'transferts';
if (strpos($objects_type, 'users') !== false) $directory = 'users';

if (strpos($objects_type, 'caddie_content_ui') !== false) $type = 'caddie_content_ui';

switch ($type) {
    case 'caddie_content_ui':
        require_once "$class_path/caddie/caddie_root_lists_controller.class.php";
        caddie_root_lists_controller::proceed_manage_ajax($id, $objects_type);
        break;
    default:
        lists_controller::proceed_manage_ajax($id, $objects_type, $directory);
        break;
}