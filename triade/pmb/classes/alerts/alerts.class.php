<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alerts.class.php,v 1.2.4.1 2025/03/13 16:27:12 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class alerts {

	protected $data;

	protected function fetch_data() {

	}

	protected function is_count_from_query($query) {
		$result = pmb_mysql_query($query);
		if ($result && pmb_mysql_result($result, 0, 0)) {
			return true;
		}
		return false;
	}

	protected function is_num_rows_from_query($query) {
		global $msg;

		$result = pmb_mysql_query($query) or die ($msg["err_sql"]."<br />".$query."<br />".pmb_mysql_error());
		return pmb_mysql_num_rows($result) ;
	}

	protected function add_data($categ, $label_code, $sub='', $url_extra='', $number=0) {
		$this->data[] = array(
				'module' => method_exists($this, 'get_module') ? $this->get_module() : null,
				'section' => method_exists($this, 'get_section') ? $this->get_section() : null,
				'categ' => $categ,
				'label_code' => $label_code,
				'sub' => $sub,
				'url_extra' => $url_extra,
				'number' => $number
		);
	}

	public function get_data() {
		if(!isset($this->data)) {
			$this->fetch_data();
		}
		return $this->data;
	}

	public static function get_module_list()
	{
		$modules = [];

        $files = glob(__DIR__ . DIRECTORY_SEPARATOR . "alerts_*");
        foreach ($files as $file) {
            $filename = basename($file);

            if(is_file($file)) {
				$pattern = '/^alerts_([a-zA-Z_]+)\.class\.php$/';
				preg_match($pattern, $filename, $matches);

                $modules[] = $matches[1];
            }
        }

        return $modules;
	}

}