<?php
include_once "../../../config/config.php";
include_once ROOT_PATH.'lib/ajax.class.php';

$user = new GCUser();
if (!$user->isAuthenticated()) {
    header("HTTP/1.1 401 Unauthorized");
    die();
}

$ajax = new GCAjax();
$check_command = ROOT_PATH . '/scripts/maintenance/geoweb_check_mapset';

if(empty($_REQUEST['mapfiles'])) $ajax->error('Lista mapset non specificata, impossibile avviare controllo mappe');
if (!file_exists($check_command)) $ajax->error('Eseguibile per controllo mappe non trovato, impossibile avviare controllo mappe');
$check_command .= ' "' . $_REQUEST['mapfiles'] . '"';
if (!empty($_REQUEST['check_single_layers'])) {
    $check_command .= ' ' . $_REQUEST['check_single_layers'];
}
else {
    $check_command .= ' ""' ;
}
if (!empty($_REQUEST['mail_address'])) {
    $check_command .= ' "' . $_REQUEST['mail_address'] . '"';
}
$check_command .= ' > /dev/null 2>&1 &';
exec($check_command);
$ajax->success();
?>
