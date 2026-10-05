<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: folders_selector.inc.php,v 1.2 2022/09/14 14:38:51 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $charset;
global $folder_id;

require_once $class_path.'/upload_folder.class.php';
require_once $class_path.'/encoding_normalize.class.php';

if ( !isset($folder_id) ) {
    $folder_id = '';
} 

$up_list = upload_folder::getFolders();

// folder_id = '' > On renvoie les répertoires racines
if( $folder_id == '' ) {
    
    $data[] = [
        'id'    => 0,
        'type'  => 'root',
        'name'  => 'root',
    ];
    
    foreach($up_list as $up) {
        $data[] = [
            'id'            => $up['repertoire_id']."_",
            'type'          => 'folder',
            'name'          => $up['repertoire_nom'],
            'navigation'    => $up['repertoire_navigation'],
            'parent'        => 0,
        ];
    }
    header("Content-Type: application/json; charset=utf-8");
    print encoding_normalize::json_encode($data);
    return;
}

// folder_id != ''  On renvoie les sous répertoires
$folder_id = rawurldecode($folder_id);
$folder_path = '';
$pos = strpos($folder_id, '_');
if(false !== $pos) {
    $folder_path = substr($folder_id, $pos+1);
    $folder_id = substr($folder_id, 0, $pos);
}

$data = [];
$up = new upload_folder($folder_id);
$folder_path = $up->convertToFileSystemCharset($folder_path, $charset);

$sub_folders = $up->getSubFolders($folder_path, true);

if ( !empty($sub_folders) ) {
    for($i=0; $i < count($sub_folders); $i++ ) {
        $data[] = [
            'id'            => rawurlencode($sub_folders[$i]['id']."_".$sub_folders[$i]['path']),
            'type'          => 'folder',
            'name'          => $sub_folders[$i]['name'],
            'navigation'    => $up_list[$folder_id]['repertoire_navigation'],
            'parent'        => rawurlencode($folder_id."_".$folder_path),
        ];
    }
}
header("Content-Type: application/json; charset=utf-8");
print json_encode($data);
return;

