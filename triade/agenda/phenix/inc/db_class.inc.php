<?php

$DB_CX = new Db();
if (!$DB_CX->DbConnect($cfgHote, $cfgUser, $cfgPass, $cfgBase)) {
    serveurDown();
}


class Db {
    // propriétés (compatibles avec ton code existant)
    var $ConnexionID = null;
    var $DatabaseName = null;
    var $Result = null;
    var $Row = null;
    var $cpt = 0;

    // CONSTRUCTEUR (compatible : on peut garder appel new Db();)
    function __construct($cxID = null) {
        $this->ConnexionID = $cxID;
        // ne RETURN pas dans le constructeur — on laisse l'objet se créer
    }

    // Connexion : retourne true/false
    function DbConnect($host, $user, $passwd, $database) {
        if (function_exists('mysqli_report')) {
            mysqli_report(MYSQLI_REPORT_OFF);
        }
        $this->ConnexionID = mysqli_connect($host, $user, $passwd, $database);

        if (!$this->ConnexionID) {
            // Erreur fatale de connexion : affiche et retourne false
            error_log("MySQL connect error: " . mysqli_connect_error());
            return false;
        }

        // Forcer charset moderne
        if (!mysqli_set_charset($this->ConnexionID, 'utf8mb4')) {
            // log mais ne tue pas la connexion
            error_log("Warning: impossible de définir le charset utf8mb4: " . mysqli_error($this->ConnexionID));
        }

        $this->DatabaseName = $database;
        return true;
    }

    function DbSelectDatabase($database) {
        $this->DatabaseName = $database;
        if ($this->ConnexionID) {
            return mysqli_select_db($this->ConnexionID, $database);
        } else {
            return false;
        }
    }

    /**
     * Exécute une requête.
     * - $start/$limit maintenus pour compatibilité (si fournis, concaténés en LIMIT)
     * - $debug : si true affiche la requête et l'erreur (désactiver en prod)
     */
    function DbQuery($query, $start = '', $limit = '', $debug = false) {
        if (!$this->ConnexionID) {
            if ($debug) echo "<pre>No mysqli connection</pre>";
            error_log("DbQuery called without active connection. Query: $query");
            return false;
        }


        // Si start/limit fournis (et numériques) : ajouter LIMIT proprement
        if ($start !== '' || $limit !== '') {
            // sécurité : forcer entiers
            $s = ($start === '') ? 0 : intval($start);
            $l = ($limit === '') ? 0 : intval($limit);
            // si $limit vaut 0 -> on évite LIMIT 0,0 (qui renverrait 0 lignes)
            if ($l > 0) {
                $query .= " LIMIT $s,$l";
            }
        }

        // Protection contre "IN ()" : remplace les IN() vides par IN (0)
        // (détecte "IN()", "IN ( )", "IN (   )" -- insensible à la casse)
        $query = preg_replace('/\bIN\s*\(\s*\)/i', 'IN (0)', $query);

        // debug optionnel
        if ($debug) {
            echo "<pre>SQL DEBUG:\n" . htmlspecialchars($query) . "\n</pre>";
        }


        $this->Result = mysqli_query($this->ConnexionID, $query);

        if ($this->Result === false) {
            $err = mysqli_error($this->ConnexionID);
            // log et (si debug) afficher l'erreur complète
            error_log("MySQL error: {$err} -- Query: {$query}");
            if ($debug) {
                echo "<pre>MySQL error: " . htmlspecialchars($err) . "\nQuery: " . htmlspecialchars($query) . "</pre>";
            }
            return false;
        }

        $this->cpt = $this->cpt + 1;
        return $this->Result;
    }

    function DbNextID($table, $champ) {
        $this->DbQuery("SELECT MAX(" . $champ . ") AS m FROM " . $table);
        $r = $this->DbNextRow();
        return (isset($r['m']) ? intval($r['m']) : 0) + 1;
    }

    function DbNumRows() {
        return ($this->Result instanceof mysqli_result) ? mysqli_num_rows($this->Result) : 0;
    }

    function DbAffectedRows() {
        return ($this->ConnexionID) ? mysqli_affected_rows($this->ConnexionID) : 0;
    }

    function DbNextRow() {
        if ($this->Result instanceof mysqli_result) {
            $this->Row = mysqli_fetch_assoc($this->Result);
            return $this->Row;
        }
        return false;
    }

    function DbResult($row, $field) {
        // version sécurisée : repositionne le curseur et lit
        if (!($this->Result instanceof mysqli_result)) return null;
        if (!is_int($row) || $row < 0) return null;

        if (mysqli_data_seek($this->Result, $row)) {
            $r = mysqli_fetch_array($this->Result, MYSQLI_BOTH);
            return ($r && array_key_exists($field, $r)) ? $r[$field] : null;
        }
        return null;
    }

    function DbNumFields() {
        return ($this->Result instanceof mysqli_result) ? mysqli_num_fields($this->Result) : 0;
    }

    function DbFieldName($field) {
        if ($this->Result instanceof mysqli_result) {
            $f = mysqli_fetch_field_direct($this->Result, $field);
            return $f ? $f->name : null;
        }
        return null;
    }

    // Remplacer mysqli_list_tables / mysqli_tablename (obsolètes)
    function DbListTable($base) {
        if (!$this->ConnexionID) return false;
        $res = mysqli_query($this->ConnexionID, "SHOW TABLES FROM " . mysqli_real_escape_string($this->ConnexionID, $base));
        return $res;
    }

    function DbTableName($tableIndex) {
        // nécessite que $this->Result contienne le résultat de SHOW TABLES
        if (!($this->Result instanceof mysqli_result)) return null;
        $row = mysqli_fetch_row($this->Result);
        return $row[$tableIndex] ?? null;
    }

    function DbError() {
        return ($this->ConnexionID) ? mysqli_error($this->ConnexionID) : null;
    }

    function DbErrorNo() {
        return ($this->ConnexionID) ? mysqli_errno($this->ConnexionID) : null;
    }

    function DbNbReq() {
        return $this->cpt;
    }

    function DbInsertID() {
    	if ($this->ConnexionID) {
        	return (int) mysqli_insert_id($this->ConnexionID);
    	}
    	return 0;
    }

    function DbDeconnect() {
        if ($this->ConnexionID) {
            $res = mysqli_close($this->ConnexionID);
            $this->ConnexionID = null;
            return $res;
        }
        return false;
    }
}

