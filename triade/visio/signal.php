<?php
session_start();
if (empty($_SESSION['membre']) && empty($_SESSION['admin1'])) { http_response_code(403); exit; }

header('Content-Type: application/json');

$room   = preg_replace('/[^a-zA-Z0-9]/', '', $_POST['room']  ?? '');
$action = $_POST['action'] ?? '';
$from   = $_POST['from']   ?? '';
$to     = $_POST['to']     ?? 'all';
$type   = $_POST['type']   ?? '';
$data   = $_POST['data']   ?? '';
$nomSession = !empty($_SESSION['nom']) ? $_SESSION['nom'] : (!empty($_SESSION['admin1']) ? 'Admin' : 'Participant');
$nom    = $nomSession;
$since  = intval($_POST['since'] ?? 0);
$my_id  = $_POST['my_id'] ?? '';

if ($room === '' && $action !== 'list') { echo json_encode(['error' => 'room vide']); exit; }

$dir  = __DIR__ . '/rooms/';
$file = $dir . $room . '.json';
if (!is_dir($dir)) mkdir($dir, 0755, true);

function lireRoom($file) {
    $raw = @file_get_contents($file);
    if (!$raw) return ['peers' => [], 'msgs' => []];
    $d = json_decode($raw, true);
    return is_array($d) ? $d : ['peers' => [], 'msgs' => []];
}

function ecrireRoom($file, $data) {
    file_put_contents($file, json_encode($data), LOCK_EX);
}

$now = time();

// Nettoyage des peers inactifs (> 30s sans ping)
function nettoyerPeers(&$room) {
    $now = time();
    foreach ($room['peers'] as $id => $p) {
        if (($now - ($p['ping'] ?? 0)) > 30) {
            unset($room['peers'][$id]);
        }
    }
}

switch ($action) {

    case 'join':
        $room_data = lireRoom($file);
        // Vérification des bannis (par nom de session — non falsifiable côté client)
        $banned = isset($room_data['banned']) ? $room_data['banned'] : array();
        if (!empty($banned) && in_array($nomSession, $banned)) {
            echo json_encode(array('error' => 'banni', 'msg' => 'Vous avez été exclu de cette salle par l\'animateur.'));
            exit;
        }
        // Vérification des classes autorisées
        $classes_auto = isset($room_data['classes_autorisees']) ? $room_data['classes_autorisees'] : array();
        if (!empty($classes_auto)) {
            $isProf = !empty($_SESSION['admin1']) ||
                      in_array(isset($_SESSION['membre']) ? $_SESSION['membre'] : '', array('menuprof','menuadmin','menuscolaire','menupersonnel'));
            if (!$isProf) {
                $idClasse = isset($_SESSION['idClasse']) ? $_SESSION['idClasse'] : '';
                if (!in_array($idClasse, $classes_auto)) {
                    echo json_encode(array('error' => 'acces_refuse', 'msg' => 'Votre classe n\'est pas autorisée dans cette salle.'));
                    exit;
                }
            }
        }
        nettoyerPeers($room_data);
        // Vérifier la limite de participants du plan
        $max_p = intval($room_data['max_participants'] ?? 6);
        $nb_actifs = count($room_data['peers']); // avant d'ajouter le nouveau
        if ($nb_actifs >= $max_p) {
            echo json_encode(array('error' => 'salle_pleine', 'msg' => "La salle est complète ({$max_p} participants maximum selon votre plan)."));
            exit;
        }
        $room_data['peers'][$my_id] = ['nom' => $nom, 'ts' => $now, 'ping' => $now];
        // garder 200 msgs max
        if (count($room_data['msgs'] ?? []) > 200) {
            $room_data['msgs'] = array_slice($room_data['msgs'], -200);
        }
        ecrireRoom($file, $room_data);
        // retourner la liste des autres peers
        $autres = [];
        foreach ($room_data['peers'] as $id => $p) {
            if ($id !== $my_id) $autres[$id] = $p['nom'];
        }
        echo json_encode(['ok' => true, 'peers' => $autres]);
        break;

    case 'ping':
        $room_data = lireRoom($file);
        if (isset($room_data['peers'][$my_id])) {
            $room_data['peers'][$my_id]['ping'] = $now;
            ecrireRoom($file, $room_data);
        }
        echo json_encode(['ok' => true]);
        break;

    case 'leave':
        $room_data = lireRoom($file);
        unset($room_data['peers'][$my_id]);
        // notifier les autres
        $room_data['msgs'][] = ['from' => $my_id, 'to' => 'all', 'type' => 'bye', 'data' => '{}', 'ts' => $now];
        ecrireRoom($file, $room_data);
        echo json_encode(['ok' => true]);
        break;

    case 'push':
        $room_data = lireRoom($file);
        $room_data['msgs'][] = ['from' => $from, 'to' => $to, 'type' => $type, 'data' => $data, 'ts' => $now];
        if (count($room_data['msgs']) > 200) {
            $room_data['msgs'] = array_slice($room_data['msgs'], -200);
        }
        ecrireRoom($file, $room_data);
        echo json_encode(['ok' => true]);
        break;

    case 'pull':
        $room_data = lireRoom($file);
        nettoyerPeers($room_data);
        // mettre à jour ping
        if ($my_id && isset($room_data['peers'][$my_id])) {
            $room_data['peers'][$my_id]['ping'] = $now;
            ecrireRoom($file, $room_data);
        }
        $msgs = array_values(array_filter($room_data['msgs'] ?? [], function($m) use ($since, $my_id) {
            if ($m['ts'] <= $since) return false;
            return ($m['to'] === 'all' || $m['to'] === $my_id);
        }));
        $peers = [];
        foreach ($room_data['peers'] as $id => $p) {
            $peers[$id] = $p['nom'];
        }
        echo json_encode(['msgs' => $msgs, 'peers' => $peers]);
        break;

    case 'list':
        // liste toutes les salles actives (pour rooms.php)
        $salles = [];
        if (is_dir($dir)) {
            foreach (glob($dir . '*.json') as $f) {
                $d = lireRoom($f);
                nettoyerPeers($d);
                $code  = basename($f, '.json');
                $peers = [];
                foreach ($d['peers'] as $id => $p) $peers[] = $p['nom'];
                $nb = count($peers);
                // Supprimer les salles vides depuis plus d'1 heure
                if ($nb === 0 && filemtime($f) < time() - 120) {
                    @unlink($f);
                    continue;
                }
                if ($nb > 0 || filemtime($f) > time() - 180) {
                    $salles[] = array(
                        'code'               => $code,
                        'participants'        => $peers,
                        'nb'                 => $nb,
                        'nom'                => isset($d['nom']) ? $d['nom'] : '',
                        'classes_autorisees' => isset($d['classes_autorisees']) ? $d['classes_autorisees'] : array(),
                        'classes_libelles'   => isset($d['classes_libelles'])   ? $d['classes_libelles']   : array(),
                    );
                }
            }
        }
        echo json_encode($salles);
        break;

    case 'ban':
        // Seuls les profs, admins et vie scolaire peuvent bannir
        $_isProf = !empty($_SESSION['admin1']) || in_array($_SESSION['membre'] ?? '', ['menuprof','menuadmin','menuscolaire']);
        if (!$_isProf) { echo json_encode(['error' => 'Action réservée aux animateurs de salle']); break; }
        $ban_id  = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['ban_id']  ?? '');
        $ban_nom = trim($_POST['ban_nom'] ?? '');
        if (!$ban_id && !$ban_nom) { echo json_encode(['error' => 'Cible manquante']); break; }
        $room_data = lireRoom($file);
        if (!isset($room_data['banned'])) $room_data['banned'] = array();
        // Bannir par nom de session (persistant même après refresh)
        if ($ban_nom && !in_array($ban_nom, $room_data['banned'])) {
            $room_data['banned'][] = $ban_nom;
        }
        // Notifier le peer via message de signaling
        if ($ban_id) {
            $room_data['msgs'][] = array('from' => $my_id, 'to' => $ban_id, 'type' => 'ban', 'data' => '{}', 'ts' => $now);
        }
        // Retirer le peer des participants actifs
        if ($ban_id && isset($room_data['peers'][$ban_id])) {
            unset($room_data['peers'][$ban_id]);
        }
        ecrireRoom($file, $room_data);
        echo json_encode(array('ok' => true));
        break;

    default:
        echo json_encode(['error' => 'action inconnue']);
}
