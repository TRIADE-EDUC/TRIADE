<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: emergency_upload.php,v 1.8 2023/03/02 08:45:38 dbellamy Exp $

//Restauration d'urgence
@set_time_limit(1200);

require "./messages_env_ract.inc.php";
require "../lib/api.inc.php";

//Verification du fichier
$filename = $_FILES['archive_file']['tmp_name'];
$infos = read_infos($filename);

if ( !empty($infos['error']) ) {
    @unlink ($filename);
    abort_critical($msg['sauv_misc_ract_no_sauv']);
    exit();
}
$critical_upload_filename = '<?php die("no access");'.PHP_EOL.'//'.basename($_FILES['archive_file']['tmp_name']);
$f = file_put_contents("../../backup/backups/critical_upload.php", $critical_upload_filename);
if( false === $f) {
    @unlink ($filename);
    abort_critical($msg['sauv_misc_ract_backups_dir_not_writeable']);
    exit();
}
move_uploaded_file($_FILES['archive_file']['tmp_name'], "../../backup/backups/".basename($_FILES['archive_file']['tmp_name']));

?>
<script>document.location="../restaure.php?filename=<?php echo rawurlencode(basename($_FILES['archive_file']['name'])); ?>&critical=1";</script>