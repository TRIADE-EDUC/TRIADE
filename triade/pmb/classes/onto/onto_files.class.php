<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_files.class.php,v 1.1 2017/06/12 09:22:27 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/storages/storage_document.class.php");

class onto_files extends storage_document {

	protected static $table = 'onto_files';
	protected static $prefix = 'onto_file';
}