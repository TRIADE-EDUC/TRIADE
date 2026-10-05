<?php
if (!function_exists('sendExpoPush')) {
function sendExpoPush($token, $title, $body, $data = []) {
    if (empty($token)) return;
    $payload = json_encode([
        'to'        => $token,
        'title'     => $title,
        'body'      => $body,
        'sound'     => 'default',
        'channelId' => 'messages',
        'data'      => $data,
    ]);
    $ch = curl_init('https://exp.host/--/api/v2/push/send');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 5,
    ]);
    curl_exec($ch);
    curl_close($ch);
}
} // end if !function_exists

// Envoie une notification à l'élève (ELE) ET au parent (PAR) d'un même elev_id.
if (!function_exists('notifyEleve')) {
function notifyEleve($prefixe, $elevId, $title, $body) {
    $id = intval($elevId);
    if (!$id) return;
    $rows = chargeMat(execSql(
        "SELECT token FROM {$prefixe}push_tokens"
        . " WHERE (user_type='ELE' OR user_type='PAR') AND user_id='$id'"
    ));
    if (!$rows) return;
    foreach ($rows as $row) {
        sendExpoPush($row[0] ?? '', $title, $body, ['type' => 'viesco']);
    }
}
} // end if !function_exists notifyEleve

// Envoie une notification push à un utilisateur MSN (par son unique_id).
// L'email MSN {id_pers}_{membre}@msn.triade sert de pont vers tria_push_tokens.
if (!function_exists('notifyMsnUser')) {
function notifyMsnUser($prefixe, $recipientUniqueId, $senderName, $messageText, $senderUniqueId, $senderFname = '', $senderLname = '') {
    $uid = intval($recipientUniqueId);
    if (!$uid) return;

    $rows = chargeMat(execSql("SELECT email FROM {$prefixe}users WHERE unique_id='$uid'"));
    if (!$rows || empty($rows[0][0])) return;
    $email = $rows[0][0];

    if (!preg_match('/^(\d+)_([^@]+)@msn\.triade$/', $email, $m)) return;
    $idPers = intval($m[1]);
    $membre = $m[2];

    $typeMap = [
        'menueleve'    => 'ELE',
        'menuparent'   => 'PAR',
        'menuprof'     => 'ENS',
        'menuadmin'    => 'ADM',
        'menuscolaire' => 'MVS',
    ];
    $userType = $typeMap[$membre] ?? null;
    if (!$userType) return;

    $trows = chargeMat(execSql(
        "SELECT token FROM {$prefixe}push_tokens WHERE user_type='$userType' AND user_id='$idPers'"
    ));
    if (!$trows || empty($trows[0][0])) return;

    $body = mb_substr($messageText, 0, 80);
    sendExpoPush($trows[0][0], $senderName, $body, [
        'type'      => 'msn',
        'contactId' => $senderUniqueId,
        'fname'     => $senderFname,
        'lname'     => $senderLname,
    ]);
}
} // end if !function_exists notifyMsnUser
?>
