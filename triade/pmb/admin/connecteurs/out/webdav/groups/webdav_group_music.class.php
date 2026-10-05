<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: webdav_group_music.class.php,v 1.5 2016/03/30 13:13:27 apetithomme Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path.'/admin/connecteurs/out/webdav/groups/webdav_group.class.php');

class webdav_group_music extends webdav_group {
}