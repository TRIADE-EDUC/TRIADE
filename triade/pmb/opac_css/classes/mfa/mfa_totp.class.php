<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mfa_totp.class.php,v 1.1 2023/07/06 14:57:03 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

class mfa_totp {
    public static $hash_methods = [
        "sha1"
    ];
    protected $life_time = 30;
    protected $length_code = 6;
    protected $hash_method = 'sha1';
    protected $time_offset = 0;

    public function get_totp($secret_code, $time = null) {
        if (is_null($time)) {
            $time = $this->get_time();
        }

        $hash = hash_hmac($this->hash_method, $this->get_binary_counter($time), $secret_code, true);

        return str_pad($this->truncate($hash), $this->length_code, '0', STR_PAD_LEFT);
    
    }

    public function check_totp($secret_code, $code, $time_drift = 1) {
        $time_drift = intval($time_drift);

        $time = $this->get_time();
    
        $start = $time - ($time_drift);
        $end = $time + ($time_drift);
    
        if (hash_equals($this->get_totp($secret_code, $time), $code)) {
            return true;
        } elseif ($time_drift == 0) {
            return false;
        }
    
        for ($t = $start; $t <= $end; $t = $t + 1) {
            if ($t == $time) {
                continue;
            }
                
            if (hash_equals($this->get_totp($secret_code, $t), $code)) {
                return true;
            }
        }
    
        return false;
    }

    private function get_time() {
        return floor((time() + $this->time_offset) / $this->life_time);
    }

    private function get_binary_counter($counter) {
        // 64 bit && PHP >= 5.6.3
        if (8 === PHP_INT_SIZE && PHP_VERSION_ID >= 50603) {
            return pack('J', $counter);
        }

        // 32 bit or PHP < 5.6.3
        return pack('N*', 0) . pack('N*', $counter);
    }

    private function truncate($hash) {
        $offset = ord($hash[strlen($hash)-1]) & 0xf;
        
        return (
            ((ord($hash[$offset+0]) & 0x7f) << 24 ) |
            ((ord($hash[$offset+1]) & 0xff) << 16 ) |
            ((ord($hash[$offset+2]) & 0xff) << 8 ) |
            (ord($hash[$offset+3]) & 0xff)
            ) % pow(10, $this->length_code);
    }

    public function set_life_time($life_time) {
        $this->life_time = $life_time;
    }

    public function set_length_code($length_code) {
        $this->length_code = $length_code;
    }

    public function set_hash_method($hash_method) {
        $this->hash_method = $hash_method;
    }
}