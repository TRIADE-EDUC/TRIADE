<?php
/**
 * ----------------------------------------------------------------------
 *  
 * Copyright (C) 2009 by Khaled Al-Shamaa.
 *  
 * http://www.ar-php.org
 *  
 * ----------------------------------------------------------------------
 *  
 * LICENSE
 *
 * This program is open source product; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License (LGPL)
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 *  
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *  
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/lgpl.txt>.
 *  
 * ----------------------------------------------------------------------
 *  
 * Class Name: Muslim Prayer Times
 *  
 * Filename:   Salat.class.php
 *  
 * Original    Author(s): Khaled Al-Sham'aa <khaled.alshamaa@gmail.com>
 *  
 * Purpose:    The five Islamic prayers are named Fajr, Zuhr, Asr, Maghrib
 *             and Isha. The timing of these five prayers varies from place
 *             to place and from day to day. It is obligatory for Muslims
 *             to perform these prayers at the correct time.
 *              
 * ----------------------------------------------------------------------
 *  
 * Source: http://qasweb.org/qasforum/index.php?showtopic=177&st=0
 * By: Mohamad Magdy <mohamad_magdy_egy@hotmail.com>
 *  
 * ----------------------------------------------------------------------
 *  
 * Muslim Prayer Times
 *
 * Using this PHP Class you can calculate the time of Muslim prayer
 * according to the geographic location.
 * 
 * The five Islamic prayers are named Fajr, Zuhr, Asr, Maghrib and Isha. The timing 
 * of these five prayers varies from place to place and from day to day. It is 
 * obligatory for Muslims to perform these prayers at the correct time.
 * 
 * The prayer times for any given location on earth may be determined mathematically 
 * if the latitude and longitude of the location are known. However, the theoretical 
 * determination of prayer times is a lengthy process. Much of this tedium may be 
 * alleviated by using computer programs.
 * 
 * Definition of prayer times
 * 
 * - FAJR starts with the dawn or morning twilight. Fajr ends just before sunrise.
 * - ZUHR begins after midday when the trailing limb of the sun has passed the 
 *   meridian. For convenience, many published prayer timetables add five minutes to 
 *   mid-day (zawal) to obtain the start of Zuhr. Zuhr ends at the start of Asr time.
 * - The timing of ASR depends on the length of the shadow cast by an object. 
 *   According to the Shafi school of jurisprudence, Asr begins when the length of 
 *   the shadow of an object exceeds the length of the object. According to the 
 *   Hanafi school of jurisprudence, Asr begins when the length of the shadow exceeds 
 *   TWICE the length of the object. In both cases, the minimum length of shadow 
 *   (which occurs when the sun passes the meridian) is subtracted from the length 
 *   of the shadow before comparing it with the length of the object.
 * - MAGHRIB begins at sunset and ends at the start of isha.
 * - ISHA starts after dusk when the evening twilight disappears.      
 *
 * Example:
 * <code>
 *     date_default_timezone_set('UTC');
 *     
 *     include('./Arabic.php');
 *     $Ar = new Arabic('Salat');
 * 
 *     $Ar->Salat->setLocation(33.513,36.292,2);
 *     $Ar->Salat->setDate(date('j'), date('n'), date('Y'));
 * 
 *     $times = $Ar->Salat->getPrayTime();
 * 
 *     echo '<b>Damascus, Syria</b><br />';
 *     echo date('l F j, Y').'<br /><br />';
 *        
 *     echo "<b class=hilight>Fajr:</b> {$times[0]}<br />";
 *     echo "<b class=hilight>Sunrise:</b> {$times[1]}<br />";
 *     echo "<b class=hilight>Zuhr:</b> {$times[2]}<br />";
 *     echo "<b class=hilight>Asr:</b> {$times[3]}<br />";
 *     echo "<b class=hilight>Maghrib:</b> {$times[4]}<br />";
 *     echo "<b class=hilight>Isha:</b> {$times[5]}<br />";    
 * </code>
 * 
 * Qibla Determination Methods - Basic Spherical Trigonometric Formula
 * 
 * The problem of qibla determination has a simple formulation in spherical 
 * trigonometry. A is a given location, K is the KaÃ¢ÂÂba, and N is the North Pole. 
 * The great circle arcs AN and KN are along the meridians through A and K, 
 * respectively, and both point to the north. The qibla is along the great 
 * circle arc AK. The spherical angle q = NAK is the angle at A from the north 
 * direction AN to the direction AK towards the KaÃ¢ÂÂba, and so q is the qibla 
 * bearing to be computed. Let ÃÂ¦ and ÃÂ» be the latitude and longitude of A, and ÃÂ¦K 
 * and ÃÂ»K be the latitude and longitude ofK (the KaÃ¢ÂÂba). If all angles and arc 
 * lengths are measured in degrees, then it is seen that the arcs AN and KN are 
 * of measure 90 - ÃÂ¦ and 90 - ÃÂ¦K, respectively. Also, the angle ANK between the 
 * meridians of K and A equals the difference between the longitudes of A and K, 
 * that is, ÃÂ»K - ÃÂ», no matter what the prime meridian is. Here we are given two 
 * sides and the included angle of a spherical triangle, and it is required to 
 * determine one other angle. One of the simplest solutions is given by the 
 * formula: 
 *                       -1              sin(ÃÂ»K - ÃÂ»)
 *                q = tan   ------------------------------------------
 *                              cos ÃÂ¦ tan ÃÂ¦K - sin ÃÂ¦ cos(ÃÂ»K - ÃÂ») 
 * 
 * In this Equation, the sign of the input quantities are assumed as follows: 
 * latitudes are positive if north, negative if south; longitudes are positive 
 * if east, negative if west. The quadrant of q is assumed to be so selected 
 * that sin q and cos q have the same sign as the numerator and denominator of 
 * this Equation. With these conventions, q will be positive for bearings east 
 * of north, negative for bearings west of north.
 * 
 * Reference:
 * The Correct Qibla, S. Kamal Abdali <k.abdali@acm.org>
 * PDF version in http://www.patriot.net/users/abdali/ftp/qibla.pdf    
 *
 * Example:
 * <code>
 *     date_default_timezone_set('UTC');
 *     
 *     include('./Arabic.php');
 *     $Ar = new Arabic('Salat');
 * 
 *     $Ar->Salat->setLocation(33.513,36.292,2);
 *
 *     $direction = $Arabic->getQibla();
 *     echo "<b>Qibla Direction (from the north direction):</b> $direction<br />";
 * </code>
 *  
 * @category  Text 
 * @package   Arabic
 * @author    Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
 * @copyright 2009 Khaled Al-Shamaa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */

// New in PHP V5.3: Namespaces
// namespace Arabic/Salat;

/**
 * This PHP class calculate the time of Muslim prayer according to the geographic 
 * location.
 *  
 * @category  Text 
 * @package   Arabic
 * @author    Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
 * @copyright 2009 Khaled Al-Shamaa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */ 
class Salat
{
    // ÃÂ§ÃÂÃÂ³ÃÂÃÂ©
    protected $_year = 1975;
    
    // ÃÂ§ÃÂÃÂ´ÃÂÃÂ±
    protected $_month = 8;
    
    // ÃÂ§ÃÂÃÂÃÂÃÂ
    protected $_day = 2;
    
    // ÃÂÃÂ±ÃÂ ÃÂ§ÃÂÃÂªÃÂÃÂÃÂÃÂª ÃÂ§ÃÂÃÂ¹ÃÂ§ÃÂÃÂÃÂ
    protected $_zone = 2;
    
    // ÃÂ®ÃÂ· ÃÂ§ÃÂÃÂ·ÃÂÃÂ ÃÂ§ÃÂÃÂ¬ÃÂºÃÂ±ÃÂ§ÃÂÃÂ ÃÂÃÂÃÂÃÂÃÂ§ÃÂ
    protected $_long = 37.15861;
    
    // ÃÂ®ÃÂ· ÃÂ§ÃÂÃÂ¹ÃÂ±ÃÂ¶ ÃÂ§ÃÂÃÂ¬ÃÂºÃÂ±ÃÂ§ÃÂÃÂ
    protected $_lat = 36.20278;
    
    // ÃÂ²ÃÂ§ÃÂÃÂÃÂ© ÃÂ§ÃÂÃÂ´ÃÂ±ÃÂÃÂ ÃÂÃÂ§ÃÂÃÂºÃÂ±ÃÂÃÂ¨
    protected $_AB2 = -0.833333;
    
    // ÃÂ²ÃÂ§ÃÂÃÂÃÂ© ÃÂ§ÃÂÃÂ¹ÃÂ´ÃÂ§ÃÂ¡
    protected $_AG2 = -18;
    
    // ÃÂ²ÃÂ§ÃÂÃÂÃÂ© ÃÂ§ÃÂÃÂÃÂ¬ÃÂ±
    protected $_AJ2 = -18;
    
    // ÃÂ§ÃÂÃÂÃÂ°ÃÂÃÂ¨
    protected $_school = 'Shafi';
    
    /**
     * Setting date of day for Salat calculation
     *      
     * @param integer $d Day of date you want to calculate Salat in
     * @param integer $m Month of date you want to calculate Salat in
     * @param integer $y Year (four digits) of date you want to calculate Salat in
     *      
     * @return boolean TRUE if success, or FALSE if fail
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function setDate($d = 2, $m = 8, $y = 1975)
    {
        $flag = true;
        
        if (is_numeric($y) && $y > 0 && $y < 3000) {
            $this->_year = floor($y);
        } else {
            $flag = false;
        }
        
        if (is_numeric($m) && $m >= 1 && $m <= 12) {
            $this->_month = floor($m);
        } else {
            $flag = false;
        }
        
        if (is_numeric($d) && $d >= 1 && $d <= 31) {
            $this->_day = floor($d);
        } else {
            $flag = false;
        }
        
        return $flag;
    }
    
    /**
     * Setting location information for Salat calculation
     *      
     * @param decimal $l1 Longitude of location you want to calculate Salat time in
     * @param decimal $l2 Latitude of location you want to calculate Salat time in
     * @param integer $z  Time Zone, offset from UTC (see also Greenwich Mean Time)
     *      
     * @return boolean TRUE if success, or FALSE if fail
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function setLocation($l1 = 37.15861, $l2 = 36.20278, $z = 2)
    {
        $flag = true;
        
        if (is_numeric($l1) && $l1 >= -180 && $l1 <= 180) {
            $this->_long = $l1;
        } else {
            $flag = false;
        }
        
        if (is_numeric($l2) && $l2 >= -180 && $l2 <= 180) {
            $this->_lat = $l2;
        } else {
            $flag = false;
        }
        
        if (is_numeric($z) && $z >= -12 && $z <= 12) {
            $this->_zone = floor($z);
        } else {
            $flag = false;
        }
        
        return $flag;
    }
    
    /**
     * Setting rest of Salat calculation configuration
     *      
     * @param string  $sch        [Shafi|Hanafi] to define Muslims Salat 
     *                            calculation method (affect Asr time)
     * @param decimal $sunriseArc Sun rise arc (default value is -0.833333)
     * @param decimal $ishaArc    Isha arc (default value is -18)
     * @param decimal $fajrArc    Fajr arc (default value is -18)
     *      
     * @return boolean TRUE if success, or FALSE if fail
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function setConf($sch = 'Shafi', $sunriseArc = -0.833333, $ishaArc = -18, $fajrArc = -18)
    {
        $flag = true;
        
        $sch = ucfirst($sch);
        
        if ($sch == 'Shafi' || $sch == 'Hanafi') {
            $this->_school = $sch;
        } else {
            $flag = false;
        }
        
        if (is_numeric($sunriseArc) && $sunriseArc >= -180 && $sunriseArc <= 180) {
            $this->_AB2 = $sunriseArc;
        } else {
            $flag = false;
        }
        
        if (is_numeric($ishaArc) && $ishaArc >= -180 && $ishaArc <= 180) {
            $this->_AG2 = $ishaArc;
        } else {
            $flag = false;
        }
        
        if (is_numeric($fajrArc) && $fajrArc >= -180 && $fajrArc <= 180) {
            $this->_AJ2 = $fajrArc;
        } else {
            $flag = false;
        }
        
        return $flag;
    }
    
    /**
     * Calculate Salat times for the date set in setSalatDate methode, and 
     * location set in setSalatLocation.
     *                        
     * @return array of Salat times + sun rise in the following format
     *               hh:mm where hh is the hour in local format and 24 mode
     *               mm is minutes with leading zero to be 2 digits always
     *               array items is [Fajr, Sunrise, Zuhr, Asr, Maghrib, Isha]
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     * @author Mohamad Magdy <mohamad_magdy_egy@hotmail.com>
     * @source http://qasweb.org/qasforum/index.php?showtopic=177&st=0
     */
    public function getPrayTime()
    {
        $prayTime = array();
        
        // ÃÂÃÂ­ÃÂ³ÃÂ¨ ÃÂ§ÃÂÃÂÃÂÃÂ ÃÂ§ÃÂÃÂ¬ÃÂÃÂÃÂÃÂ§ÃÂÃÂ
        $d = ((367 * $this->_year) - (floor((7 / 4) * ($this->_year + floor(($this->_month + 9) / 12)))) + floor(275 * ($this->_month / 9)) + $this->_day - 730531.5);
        
        // ÃÂÃÂ­ÃÂ³ÃÂ¨ ÃÂ·ÃÂÃÂ ÃÂ§ÃÂÃÂ´ÃÂÃÂ³ ÃÂ§ÃÂÃÂÃÂ³ÃÂ·ÃÂ
        $L = fmod(280.461 + 0.9856474 * $d, 360);
        
        // ÃÂ«ÃÂ ÃÂÃÂ­ÃÂ³ÃÂ¨ ÃÂ­ÃÂµÃÂ© ÃÂ§ÃÂÃÂ´ÃÂÃÂ³ ÃÂ§ÃÂÃÂÃÂ³ÃÂ·ÃÂ
        $M = fmod(357.528 + 0.9856003 * $d, 360);
        
        // ÃÂ«ÃÂ ÃÂÃÂ­ÃÂ³ÃÂ¨ ÃÂ·ÃÂÃÂ ÃÂ§ÃÂÃÂ´ÃÂÃÂ³ ÃÂ§ÃÂÃÂ¨ÃÂ±ÃÂÃÂ¬ÃÂ
        $lambda = $L + 1.915 * sin($M * pi() / 180) + 0.02 * sin(2 * $M * pi() / 180);
        
        // ÃÂ«ÃÂ ÃÂÃÂ­ÃÂ³ÃÂ¨ ÃÂÃÂÃÂ ÃÂ¯ÃÂ§ÃÂ¦ÃÂ±ÃÂ© ÃÂ§ÃÂÃÂ¨ÃÂ±ÃÂÃÂ¬
        $obl = 23.439 - 0.0000004 * $d;
        
        // ÃÂ«ÃÂ ÃÂÃÂ­ÃÂ³ÃÂ¨ ÃÂ§ÃÂÃÂÃÂ·ÃÂÃÂ¹ ÃÂ§ÃÂÃÂÃÂ³ÃÂªÃÂÃÂÃÂ
        $alpha = atan(cos($obl * pi() / 180) * tan($lambda * pi() / 180)) * 180 / pi();
        $alpha = $alpha - (360 * floor($alpha / 360));
        
        // ÃÂ«ÃÂ ÃÂÃÂ¹ÃÂ¯ÃÂ ÃÂ§ÃÂÃÂÃÂ·ÃÂÃÂ¹ ÃÂ§ÃÂÃÂÃÂ³ÃÂªÃÂÃÂÃÂ
        $alpha = $alpha + 90 * ((int)($lambda / 90) - (int)($alpha / 90));
        
        // ÃÂÃÂ­ÃÂ³ÃÂ¨ ÃÂ§ÃÂÃÂ²ÃÂÃÂ ÃÂ§ÃÂÃÂÃÂ¬ÃÂÃÂ ÃÂ¨ÃÂ§ÃÂÃÂ¯ÃÂ±ÃÂ¬ÃÂ§ÃÂª ÃÂ§ÃÂÃÂ²ÃÂ§ÃÂÃÂÃÂ©
        $ST = fmod(100.46 + 0.985647352 * $d, 360);
        
        // ÃÂ«ÃÂ ÃÂÃÂ­ÃÂ³ÃÂ¨ ÃÂÃÂÃÂ ÃÂ§ÃÂÃÂ´ÃÂÃÂ³ ÃÂ§ÃÂÃÂ²ÃÂ§ÃÂÃÂ
        $Dec = asin(sin($obl * pi() / 180) * sin($lambda * pi() / 180)) * 180 / pi();
        
        // ÃÂÃÂ­ÃÂ³ÃÂ¨ ÃÂ²ÃÂÃÂ§ÃÂ ÃÂ§ÃÂÃÂ´ÃÂÃÂ³ ÃÂ§ÃÂÃÂÃÂ³ÃÂ·ÃÂ
        $noon = fmod(abs($alpha - $ST), 360);
        
        // ÃÂ«ÃÂ ÃÂ§ÃÂÃÂ²ÃÂÃÂ§ÃÂÃÂ ÃÂ§ÃÂÃÂ¹ÃÂ§ÃÂÃÂÃÂ
        $un_noon = $noon - $this->_long;
        
        // ÃÂ«ÃÂ ÃÂ§ÃÂÃÂ²ÃÂÃÂ§ÃÂ ÃÂ§ÃÂÃÂÃÂ­ÃÂÃÂ
        $local_noon = fmod(($un_noon/15) + $this->_zone, 24);
        
        // ÃÂÃÂÃÂª ÃÂµÃÂÃÂ§ÃÂ© ÃÂ§ÃÂÃÂ¸ÃÂÃÂ±
        $Dhuhr = $local_noon / 24;
        $Dhuhr_h = (int)($Dhuhr * 24 * 60 / 60);
        $Dhuhr_m = sprintf('%02d', ($Dhuhr * 24 * 60) % 60);
        $prayTime[2] = $Dhuhr_h.':'.$Dhuhr_m;
        
        if ($this->_school == 'Shafi') {
            // ÃÂÃÂ­ÃÂ³ÃÂ¨ ÃÂ¥ÃÂ±ÃÂªÃÂÃÂ§ÃÂ¹ ÃÂ§ÃÂÃÂ´ÃÂÃÂ³ ÃÂÃÂÃÂÃÂª ÃÂµÃÂÃÂ§ÃÂ© ÃÂ§ÃÂÃÂ¹ÃÂµÃÂ± ÃÂ­ÃÂ³ÃÂ¨ ÃÂ§ÃÂÃÂÃÂ°ÃÂÃÂ¨ ÃÂ§ÃÂÃÂ´ÃÂ§ÃÂÃÂ¹ÃÂ
            $T = atan(1 + tan(abs($this->_lat - $Dec) * pi() / 180)) * 180 / pi();
            
            // ÃÂ«ÃÂ ÃÂÃÂ­ÃÂ³ÃÂ¨ ÃÂÃÂÃÂ³ ÃÂ§ÃÂÃÂ¯ÃÂ§ÃÂ¦ÃÂ± ÃÂ£ÃÂ ÃÂ§ÃÂÃÂÃÂÃÂª ÃÂ§ÃÂÃÂÃÂªÃÂ¨ÃÂÃÂ ÃÂÃÂ ÃÂÃÂÃÂª ÃÂ§ÃÂÃÂ¸ÃÂÃÂ± ÃÂ­ÃÂªÃÂ ÃÂµÃÂÃÂ§ÃÂ© ÃÂ§ÃÂÃÂ¹ÃÂµÃÂ± ÃÂ­ÃÂ³ÃÂ¨ ÃÂ§ÃÂÃÂÃÂ°ÃÂÃÂ¨ ÃÂ§ÃÂÃÂ´ÃÂ§ÃÂÃÂ¹ÃÂ
            $V = acos((sin((90 - $T) * pi() / 180) - sin($Dec * pi() / 180) * sin($this->_lat * pi() / 180)) / (cos($Dec * pi() / 180) * cos($this->_lat * pi() / 180))) * 180 / pi() / 15;
            
            // ÃÂÃÂÃÂª ÃÂµÃÂÃÂ§ÃÂ© ÃÂ§ÃÂÃÂ¹ÃÂµÃÂ± ÃÂ­ÃÂ³ÃÂ¨ ÃÂ§ÃÂÃÂÃÂ°ÃÂÃÂ¨ ÃÂ§ÃÂÃÂ´ÃÂ§ÃÂÃÂ¹ÃÂ
            $X = $local_noon + $V;
            $SAsr = $Dhuhr + $V / 24;
            $SAsr_h = (int)($SAsr * 24 * 60 / 60);
            $SAsr_m = sprintf('%02d', ($SAsr * 24 * 60) % 60);
            $prayTime[3] = $SAsr_h.':'.$SAsr_m;
        } else {
            // ÃÂÃÂ­ÃÂ³ÃÂ¨ ÃÂ¥ÃÂ±ÃÂªÃÂÃÂ§ÃÂ¹ ÃÂ§ÃÂÃÂ´ÃÂÃÂ³ ÃÂÃÂÃÂÃÂª ÃÂµÃÂÃÂ§ÃÂ© ÃÂ§ÃÂÃÂ¹ÃÂµÃÂ± ÃÂ­ÃÂ³ÃÂ¨ ÃÂ§ÃÂÃÂÃÂ°ÃÂÃÂ¨ ÃÂ§ÃÂÃÂ­ÃÂÃÂÃÂ
            $U = atan(2 + tan(abs($this->_lat - $Dec) * pi() / 180)) * 180 / pi();
            
            // ÃÂ«ÃÂ ÃÂÃÂ­ÃÂ³ÃÂ¨ ÃÂÃÂÃÂ³ ÃÂ§ÃÂÃÂ¯ÃÂ§ÃÂ¦ÃÂ± ÃÂ£ÃÂ ÃÂ§ÃÂÃÂÃÂÃÂª ÃÂ§ÃÂÃÂÃÂªÃÂ¨ÃÂÃÂ ÃÂÃÂ ÃÂÃÂÃÂª ÃÂ§ÃÂÃÂ¸ÃÂÃÂ± ÃÂ­ÃÂªÃÂ ÃÂµÃÂÃÂ§ÃÂ© ÃÂ§ÃÂÃÂ¹ÃÂµÃÂ± ÃÂ­ÃÂ³ÃÂ¨ ÃÂ§ÃÂÃÂÃÂ°ÃÂÃÂ¨ ÃÂ§ÃÂÃÂ­ÃÂÃÂÃÂ
            $W = acos((sin((90 - $U) * pi() / 180) - sin($Dec * pi() / 180) * sin($this->_lat * pi() / 180)) / (cos($Dec * pi() / 180) * cos($this->_lat * pi() / 180))) * 180 / pi() / 15;
            
            // ÃÂÃÂÃÂª ÃÂµÃÂÃÂ§ÃÂ© ÃÂ§ÃÂÃÂ¹ÃÂµÃÂ± ÃÂ­ÃÂ³ÃÂ¨ ÃÂ§ÃÂÃÂÃÂ°ÃÂÃÂ¨ ÃÂ§ÃÂÃÂ­ÃÂÃÂÃÂ
            $Z = $local_noon + $W;
            $HAsr = $Z / 24;
            $HAsr_h = (int)($HAsr * 24 * 60 / 60);
            $HAsr_m = sprintf('%02d', ($HAsr * 24 * 60) % 60);
            $prayTime[3] = $HAsr_h.':'.$HAsr_m;
        }
        
        // ÃÂÃÂ­ÃÂ³ÃÂ¨ ÃÂÃÂµÃÂ ÃÂÃÂÃÂ³ ÃÂ§ÃÂÃÂÃÂÃÂ§ÃÂ±
        $AB = acos((SIN($this->_AB2 * pi() / 180) - sin($Dec * pi() / 180) * sin($this->_lat * pi() / 180)) / (cos($Dec * pi() / 180) * cos($this->_lat * pi() / 180))) * 180 / pi();
        
        // ÃÂÃÂÃÂª ÃÂ§ÃÂÃÂ´ÃÂ±ÃÂÃÂ
        $AC = $local_noon - $AB / 15;
        $Sunrise = $AC / 24;
        $Sunrise_h = (int)($Sunrise * 24 * 60 / 60);
        $Sunrise_m = sprintf('%02d', ($Sunrise * 24 * 60) % 60);
        $prayTime[1] = $Sunrise_h.':'.$Sunrise_m;
        
        // ÃÂÃÂÃÂª ÃÂ§ÃÂÃÂºÃÂ±ÃÂÃÂ¨
        $AE = $local_noon + $AB / 15;
        $Sunset = $AE / 24;
        $Sunset_h = (int)($Sunset * 24 * 60 / 60);
        $Sunset_m = sprintf('%02d', ($Sunset * 24 * 60) % 60);
        $prayTime[4] = $Sunset_h.':'.$Sunset_m;
        
        // ÃÂÃÂ­ÃÂ³ÃÂ¨ ÃÂÃÂ¶ÃÂ ÃÂ§ÃÂÃÂ¯ÃÂ§ÃÂ¦ÃÂ± ÃÂÃÂÃÂ ÃÂ§ÃÂÃÂÃÂÃÂª ÃÂ§ÃÂÃÂÃÂªÃÂ¨ÃÂÃÂ ÃÂÃÂ ÃÂÃÂÃÂª ÃÂµÃÂÃÂ§ÃÂ© ÃÂ§ÃÂÃÂ¸ÃÂÃÂ± ÃÂ¥ÃÂÃÂ ÃÂÃÂÃÂª ÃÂ§ÃÂÃÂ¹ÃÂ´ÃÂ§ÃÂ¡
        $AG = acos((sin($this->_AG2 * pi() / 180) - sin($Dec * pi() / 180) * sin($this->_lat * pi() / 180)) / (cos($Dec * pi() / 180) * cos($this->_lat * pi() / 180))) * 180 / pi();
        
        // ÃÂÃÂÃÂª ÃÂµÃÂÃÂ§ÃÂ© ÃÂ§ÃÂÃÂ¹ÃÂ´ÃÂ§ÃÂ¡
        $AH = $local_noon + ($AG / 15);
        $Isha = $AH / 24;
        $Isha_h = (int)($Isha * 24 * 60 / 60);
        $Isha_m = sprintf('%02d', ($Isha * 24 * 60) % 60);
        $prayTime[5] = $Isha_h.':'.$Isha_m;
        
        // ÃÂÃÂ­ÃÂ³ÃÂ¨ ÃÂÃÂ¶ÃÂ ÃÂ¯ÃÂ§ÃÂ¦ÃÂ± ÃÂ§ÃÂÃÂÃÂ¬ÃÂ± ÃÂÃÂÃÂ ÃÂ§ÃÂÃÂÃÂÃÂª ÃÂ§ÃÂÃÂÃÂªÃÂ¨ÃÂÃÂ ÃÂÃÂ ÃÂÃÂÃÂª ÃÂµÃÂÃÂ§ÃÂ© ÃÂ§ÃÂÃÂÃÂ¬ÃÂ± ÃÂ­ÃÂªÃÂ ÃÂÃÂÃÂª ÃÂµÃÂÃÂ§ÃÂ© ÃÂ§ÃÂÃÂ¸ÃÂÃÂ±
        $AJ = acos((sin($this->_AJ2 * pi() / 180) - sin($Dec * pi() / 180) * sin($this->_lat * pi() / 180)) / (cos($Dec * pi() / 180) * cos($this->_lat * pi() / 180))) * 180 / pi();
        
        // ÃÂÃÂÃÂª ÃÂµÃÂÃÂ§ÃÂ© ÃÂ§ÃÂÃÂÃÂ¬ÃÂ±
        $AK = $local_noon - $AJ / 15;
        $Fajr = $AK / 24;
        $Fajr_h = (int)($Fajr * 24 * 60 / 60);
        $Fajr_m = sprintf('%02d', ($Fajr * 24 * 60) % 60);
        $prayTime[0] = $Fajr_h.':'.$Fajr_m;
        
        return $prayTime;
    }
    
    /**
     * Determine Qibla direction using basic spherical trigonometric formula 
     *                        
     * @return float Qibla Direction (from the north direction) in degrees
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     * @author S. Kamal Abdali <k.abdali@acm.org>
     * @source http://www.patriot.net/users/abdali/ftp/qibla.pdf
     */
    public function getQibla () {
        // The geographical coordinates of the Ka'ba
        $K_latitude = deg2rad(21.423333);
        $K_longitude = deg2rad(39.823333);
        
        $latitude = deg2rad($this->_lat);
        $longitude = deg2rad($this->_long);
        
        $numerator = sin($K_longitude - $longitude);
        $denominator = (cos($latitude) * tan($K_latitude)) -
                       (sin($latitude) * cos($K_longitude - $longitude));
        $q = atan($numerator / $denominator);
        
        $q = rad2deg($q);
        
        return $q;
    }
    
    /**
     * Convert coordinates presented in degrees, minutes and seconds 
     * (i.e. 12ÃÂ°34'56"S formula) into usual float number in degree unit scale 
     * (i.e. -12.5822 value)
     *      
     * @param string $value Coordinate presented in degrees, minutes and seconds
     *                      (i.e. 12ÃÂ°34'56"S formula)     
     *      
     * @return float Equivalent float number in degree unit scale
     *               (i.e. -12.5822 value)     
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function coordinate2deg ($value) {
        $pattern = "/(\d{1,2})ÃÂ°((\d{1,2})')?((\d{1,2})\")?([NSEW])/i";
        
        preg_match($pattern, $value, $matches);
        
        $degree = $matches[1] + ($matches[3] / 60) + ($matches[5] /3600);
        
        $direction = strtoupper($matches[6]);
        
        if($direction == 'S' || $direction == 'W') {
            $degree = -1 * $degree;
        }
        
        return $degree;
    }
}
?>