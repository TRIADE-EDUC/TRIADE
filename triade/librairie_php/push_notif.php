<?php
function sendExpoPush($token, $title, $body) {
    if (empty($token)) return;
    $payload = json_encode([
        'to'        => $token,
        'title'     => $title,
        'body'      => $body,
        'sound'     => 'default',
        'channelId' => 'messages',
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

function notifyEleve($elevId, $title, $body) {
    global $prefixe;
    $id = intval($elevId);
    if (!$id) return;
    $rows = chargeMat(execSql("SELECT token FROM {$prefixe}push_tokens WHERE (user_type='ELE' OR user_type='PAR') AND user_id='$id'"));
    if (!$rows) return;
    foreach ($rows as $row) {
        sendExpoPush($row[0] ?? '', $title, $body);
    }
}
?>
