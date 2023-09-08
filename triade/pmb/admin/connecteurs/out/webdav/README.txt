Information concernant de module SabreDAV :
 -> http://code.google.com/p/sabredav/

Pour le mettre à jour dans PMB :
  - Télécharger la dernière version
  - Copier les dossiers lib et vendor dans ce dossier (s'assurer que le dossier lib/Sabre/PMB n'est pas écrasé)
  - Dans le dossier vendor/sabre/vobject ne garder que le dossier lib
  - Modifier le fichier autoload_namespaces.php du dossier vendor/composer pour ajouter " 'Sabre\\PMB' => $baseDir . '/lib/', " à la fin du tableau

Et normalement tout devrait fonctionner...

