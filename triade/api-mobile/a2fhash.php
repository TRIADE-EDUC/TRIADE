<?php 
function genererHashA2F($n, $p){
    $nom = strtolower($n);
    $prenom = strtolower($p);
    $horodateur = time();
    $ech_txt = bin2hex(random_bytes(32));
    $ech_nb = random_int(1000, 9999);
    $hash_identite = hash('sha256', "{$nom}-{$prenom}");
    $hash_a2f = hash('sha256', "{$horodateur}; {$ech_txt}; {$ech_nb}; {$hash_identite}");
    return $hash_a2f;
};
//echo genererHashA2F("Jean", "Pierre");
?>
