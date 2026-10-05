<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ISBN.php,v 1.4.2.1 2024/12/20 10:18:35 qvarin Exp $

namespace Pmb\Common\Library\ISBN;

//Debut de remplacement de isbn.inc.php

class ISBN
{
    /**
     * Conversion chaine en EAN13
     *
     * @param string $str : chaine a traiter
     * @return string : EAN13 si $str est un EAN13|ISBN10|ISBN13 correct, vide sinon
     */
    public static function toEAN13(string $str)
    {
        if( false !== ISBN::isISBN13($str) ) {
            return ISBN::ISBN13ToEAN13($str);
        }
        if( false !== ISBN::isISBN10($str) ) {
            return ISBN::ISBN10ToEAN13($str);
        }
        return '';
    }


    /**
     * Conversion chaine en ISBN10
     *
     * @param string $str : chaine a traiter
     *
     * @return string : ISBN10  si $str est un EAN13|ISBN10|ISBN13 correct, vide sinon
     */
    public static function toISBN10(string $str)
    {
        $isbn10 = '';
        $str = preg_replace("/[^0-9|X]/i", '', $str );
        if( false !== ISBN::isISBN13($str) ) {
            $isbn10 = ISBN::ISBN13toISBN10($str);

        } elseif( false !== ISBN::isISBN10($str) ) {
            $isbn10 = $str;
        }
        return $isbn10;
    }


    /**
     * Conversion ISBN13/EAN13 correct en ISBN10
     *
     * @param string $isbn13
     *
     * @return string
     */
    public static function ISBN13toISBN10(string $isbn13)
    {
        $ean13 = preg_replace("/[^0-9]/", '', $isbn13);
        $isbn10_wo_key = substr($ean13, 3, 9);
        $isbn10 = $isbn10_wo_key . ISBN::key10($isbn10_wo_key);
        return $isbn10;
    }


    /**
     * Conversion ISBN13 correct en EAN13
     *
     * @param string $isbn13 : isbn13
     * @return string
     */
    public static function ISBN13ToEAN13(string $isbn13)
    {
        $ean13 = preg_replace("/[^0-9]/", '', $isbn13);
        return $ean13;
    }


    /**
     * Conversion ISBN10 correct en EAN13
     *
     * @param string $isbn10 : isbn10
     * @return string
     */
    public static function ISBN10ToEAN13(string $isbn10)
    {
        $ean13 = preg_replace("/[^0-9|X]/i", '', $isbn10 );
        $ean13_wo_key = '978'.substr($ean13, 0, 9);
        $ean13 = $ean13_wo_key . ISBN::key13($ean13_wo_key);
        return $ean13;
    }


    /**
     * Verification ISBN13
     *
     * @param string $str : chaine a traiter
     * @return boolean
     */
    public static function isISBN13(string $str)
    {
        $str = preg_replace("/[^0-9]/", '', $str);

        if(strlen($str) != 13) {
            return false;
        }
        $prefix = substr($str, 0, 3);
        if($prefix != '978' && $prefix != '979') {
            return false;
        }

        $checksum = 0;
        for($i = 0; $i < 13; $i = $i + 2) {
            $checksum += $str[$i];
        }
        for($i = 1; $i < 13; $i = $i + 2) {
            $checksum += $str[$i] * 3;
        }
        if($checksum % 10 != 0) {
            return false;
        }
        return true;
    }


    /**
     * Verification ISBN10
     *
     * @param string $str : chaine a traiter
     * @return boolean
     */
    public static function isISBN10(string $str)
    {
        $str = preg_replace("/[^0-9|X]/i", '', $str );

        if (strlen($str) != 10) {
            return false;
        }
        $str = str_replace('x', 'X', $str);
        if( !preg_match('/^[0-9]{9}([0-9]|X)$/', $str) ){
            return false;
        }

        $checksum = 0;
        for($i = 0; $i < 10; $i++) {
            $digit = $str[$i];
            if($i==9 && $digit == 'X') {
                $digit = 10;
            }
            $checksum += (10 - $i) * $digit;
        }
        if($checksum % 11 != 0) {
            return false;
        }
        return true;
    }


    /**
     * Calcul cle de controle ISBN10
     *
     * @param string $isbn_wo_key : ISBN10 : ISBN10 sans la cle de controle
     *
     * @return int|string : cle de controle
     */
    public static function key10(string $isbn_wo_key)
    {
        $cksum=0;
        for ($i=0; $i < 9; $i++) {
            $cksum+= (10 - $i) * $isbn_wo_key[$i];
        }

        $remainder = $cksum % 11;
        $key = 11 - $remainder;

        if ($key == 10) {
            $key="X";
        } elseif ($key == 11) {
            $key=0;
        }
        return $key;
    }


    /**
     * Calcul cle de controle ISBN13|EAN13
     *
     * @param int $isbn_wo_key : ISBN13|EAN13 sans la cle de controle
     * @return int : cle de controle
     */
    public static function key13(string $isbn_wo_key)
    {
        $cksum = 0;
        $p = 1;
        for ($i = 0; $i < 12; $i++) {
            $cksum += $p * $isbn_wo_key[$i];
            if ($p == 1) {
                $p = 3;
            } else {
                $p = 1;
            }
        }
        $key = 10 - $cksum % 10;
        if ($key == 10) {
            $key = 0;
        }
        return $key;
    }

    /**
     * Verification EAN
     * @param string $ean
     * @return boolean
     */
    public static function isEAN(string $ean)
    {
        $ean = preg_replace('/-|\.| /', '', $ean);
        if (!is_numeric($ean)) {
            return false;
        }

        if (!preg_match('/^978[0-9]|^979[0-9]/', $ean)) {
            return false;
        }

        if (strlen($ean) != 13) {
            return false;
        }

        $checksum = 0;
        for($i = 0; $i < 13; $i = $i + 2) {
            $checksum += $ean[$i];
        }

        for($i = 1; $i < 13; $i = $i + 2) {
            $checksum += $ean[$i] * 3;
        }

        if($checksum % 10 == 0) {
            return true;
        }
        return false;
    }

    /**
     * Verification ISBN
     * @param string $isbn
     * @return boolean
     */
    public static function isISBN(string $isbn)
    {
        if(empty($isbn)) {
            return false;
        }
        // s'il y a des lettres, ce n'est pas un ISBN
        if (preg_match('/[A-WY-Z]/i', $isbn)) {
            return false;
        }
        $checksum=0;
        $isbn = preg_replace('/-|\.| /', '', $isbn);

        $strlen_isbn = strlen($isbn);
        $key = $isbn[$strlen_isbn - 1];

        if ($strlen_isbn==10) {
            if($key == 'X')
                $key = 10;
                $isbn = substr($isbn, 0, $strlen_isbn - 1);

                // vérification de la clé
                for($i = 0; $i < strlen($isbn) ; $i++) {
                    $checksum += (10 - $i) * $isbn[$i];
                }
                $checksum += $key;

                if (($checksum%11) == 0) {
                    return true;
                }
        } else if ($strlen_isbn==13) {
            if (!is_numeric($isbn)) {
                return false;
            }

            if ((substr($isbn,0,3)=="978")||(substr($isbn,0,3)=="979")) {
                //Vérification de la clé
                $p=1;
                for ($i=0; $i<13; $i++) {
                    $checksum+=$p*$isbn[$i];
                    $p=($p==1?3:1);
                }
                if (($checksum%10) == 0) {
                    return true;
                }
            }
        }
        return false;
    }
}

