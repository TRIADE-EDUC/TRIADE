<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docbnf_zip.class.php,v 1.2.4.1 2025/03/17 08:15:42 qvarin Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

require_once ($class_path . "/docbnf.class.php");

class docbnf_zip extends docbnf
{

    public $tab_entries = array();

    public $sizes = array();

    public $tmp_path = array();

    public $zip;

    protected function parse()
    {
        $this->zip = zip_open($this->path);
        if ($this->zip) {
            while ($zip_entry = zip_read($this->zip)) {
                $zip_entry_name = strtolower(zip_entry_name($zip_entry));
                $this->tab_entries[$zip_entry_name] = $zip_entry;
            }
        }
        ksort($this->tab_entries);

        foreach ($this->tab_entries as $path => $ressource) {
            $this->ref = str_replace("/", "", $path);
            break;
        }
        $refnum_path = $this->ref . "/x" . $this->ref . ".xml";
        if ($this->file_exists($refnum_path)) {
            $this->refnum = new domDocument();
            $this->refnum->loadXML($this->get_file_content($refnum_path));
            return true;
        } else {
            return false;
        }
    }

    // retourne le path interne au fichier
    public function get_file_path($file)
    {
        return strtolower($this->ref . "/" . $file);
    }

    // retourne le contenu d'un fichier
    public function get_file_content($file_path)
    {
        if (! $this->tmp_path[$file_path]) {
            $this->tmp_path[$file_path] = array_search('uri', @array_flip(stream_get_meta_data($GLOBALS[mt_rand()] = tmpfile())));
            $fp = fopen($this->tmp_path[$file_path], "w+");
            $content = zip_entry_read($this->tab_entries[$file_path], zip_entry_filesize($this->tab_entries[$file_path]));
            fwrite($fp, $content);
            fclose($fp);
        } else {
            $content = file_get_contents($this->tmp_path[$file_path]);
        }
        return $content;
    }

    // retourne un chemin vers le fichier
    public function get_file($file_path)
    {
        if (! $this->tmp_path[$file_path]) {
            $this->get_file_content($file_path);
        }
        return $this->tmp_path[$file_path];
    }

    /**
     *
     * @param int $object_id
     * @return boolean
     * @see docbnf::file_exists()
     */
    public function file_exists($file)
    {
        if (isset($this->tab_entries[$file])) {
            return true;
        } else {
            return isset($this->tab_entries[$file . "/"]);
        }
    }
}