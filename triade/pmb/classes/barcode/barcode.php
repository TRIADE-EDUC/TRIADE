<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: barcode.php,v 1.4.14.1 2025/03/27 13:29:26 dbellamy Exp $

/*
 * Barcode Render Class for PHP using the GD graphics library
 * Copyright (C) 2001 Karim Mribti
 *
 * Version 0.0.7a 2001-04-01
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * Copy of GNU Lesser General Public License at: http://www.gnu.org/copyleft/lesser.txt
 *
 * Source code home page: http://www.mribti.com/barcode/
 * Contact author at: barcode@mribti.com
 */
if (! defined("BARCODE_CLASS")) {
    define("BARCODE_CLASS", 1);

    /**
     * *************************** base class *******************************************
     */
    /**
     * NB: all GD call's is here *
     */

    /* Styles */

    /* Global */
    define("BCS_BORDER", 1);
    define("BCS_TRANSPARENT", 2);
    define("BCS_ALIGN_CENTER", 4);
    define("BCS_ALIGN_LEFT", 8);
    define("BCS_ALIGN_RIGHT", 16);
    define("BCS_IMAGE_JPEG", 32);
    define("BCS_IMAGE_PNG", 64);
    define("BCS_DRAW_TEXT", 0); // modif cerovetti mettre 0 pour invalider impression du texte du code, 128 pour valider
    define("BCS_STRETCH_TEXT", 256);
    define("BCS_REVERSE_COLOR", 512);
    /* For the I25 Only */
    define("BCS_I25_DRAW_CHECK", 2048);

    /* Default values */

    /* Global */
    define("BCD_DEFAULT_BACKGROUND_COLOR", 0xFFFFFF);
    define("BCD_DEFAULT_FOREGROUND_COLOR", 0x000000);
    define("BCD_DEFAULT_STYLE", BCS_BORDER | BCS_ALIGN_CENTER | BCS_IMAGE_PNG);
    define("BCD_DEFAULT_WIDTH", 460);
    define("BCD_DEFAULT_HEIGHT", 120);
    define("BCD_DEFAULT_FONT", 5);
    define("BCD_DEFAULT_XRES", 2);
    /* Margins */
    define("BCD_DEFAULT_MAR_Y1", 0);
    define("BCD_DEFAULT_MAR_Y2", 0);
    define("BCD_DEFAULT_TEXT_OFFSET", 2);
    /* For the I25 Only */
    define("BCD_I25_NARROW_BAR", 1);
    define("BCD_I25_WIDE_BAR", 2);

    /* For the C39 Only */
    define("BCD_C39_NARROW_BAR", 1);
    define("BCD_C39_WIDE_BAR", 2);

    /* For Code 128 */
    define("BCD_C128_BAR_1", 1);
    define("BCD_C128_BAR_2", 2);
    define("BCD_C128_BAR_3", 3);
    define("BCD_C128_BAR_4", 4);

    class BarcodeObject
    {

        public $mWidth, $mHeight, $mStyle, $mBgcolor, $mBrush;

        public $mImg, $mFont;

        public $mError;

        public function __construct($Width = BCD_DEFAULT_WIDTH, $Height = BCD_DEFAULT_HEIGHT, $Style = BCD_DEFAULT_STYLE)
        {
            $Width = intval($Width);
            $Height = intval($Height);

            $this->mWidth = ($Width > 0) ? $Width : BCD_DEFAULT_WIDTH;
            $this->mHeight = ($Height > 0) ? $Height : BCD_DEFAULT_HEIGHT;
            $this->mStyle = $Style;
            $this->mFont = BCD_DEFAULT_FONT;
            $this->mImg = ImageCreate($this->mWidth, $this->mHeight);
            $dbColor = $this->mStyle & BCS_REVERSE_COLOR ? BCD_DEFAULT_FOREGROUND_COLOR : BCD_DEFAULT_BACKGROUND_COLOR;
            $dfColor = $this->mStyle & BCS_REVERSE_COLOR ? BCD_DEFAULT_BACKGROUND_COLOR : BCD_DEFAULT_FOREGROUND_COLOR;
            $this->mBgcolor = ImageColorAllocate($this->mImg, ($dbColor & 0xFF0000) >> 16, ($dbColor & 0x00FF00) >> 8, $dbColor & 0x0000FF);
            $this->mBrush = ImageColorAllocate($this->mImg, ($dfColor & 0xFF0000) >> 16, ($dfColor & 0x00FF00) >> 8, $dfColor & 0x0000FF);
            if (! ($this->mStyle & BCS_TRANSPARENT)) {
                ImageFill($this->mImg, $this->mWidth, $this->mHeight, $this->mBgcolor);
            }
        }

        public function DrawObject($xres)
        {
            /* there is not implementation neded, is simply the asbsract function. */
            return false;
        }

        public function DrawBorder()
        {
            ImageRectangle($this->mImg, 0, 0, $this->mWidth - 1, $this->mHeight - 1, $this->mBrush);
        }

        public function DrawChar($Font, $xPos, $yPos, $Char)
        {
            ImageString($this->mImg, $Font, $xPos, $yPos, $Char, $this->mBrush);
        }

        public function DrawText($Font, $xPos, $yPos, $Char)
        {
            ImageString($this->mImg, $Font, $xPos, $yPos, $Char, $this->mBrush);
        }

        public function DrawSingleBar($xPos, $yPos, $xSize, $ySize)
        {
            if ($xPos >= 0 && $xPos <= $this->mWidth && ($xPos + $xSize) <= $this->mWidth && $yPos >= 0 && $yPos <= $this->mHeight && ($yPos + $ySize) <= $this->mHeight) {
                for ($i = 0; $i < $xSize; $i ++) {
                    ImageLine($this->mImg, $xPos + $i, $yPos, $xPos + $i, $yPos + $ySize, $this->mBrush);
                }
                return true;
            }
            return false;
        }

        public function GetError()
        {
            return $this->mError;
        }

        public function GetFontHeight($font)
        {
            return ImageFontHeight($font);
        }

        public function GetFontWidth($font)
        {
            return ImageFontWidth($font);
        }

        public function SetFont($font)
        {
            $this->mFont = $font;
        }

        public function GetStyle()
        {
            return $this->mStyle;
        }

        public function SaveTo($file)
        {
            return ImagePNG($this->mImg, $file);
        }

        public function SetStyle($Style)
        {
            $this->mStyle = $Style;
        }

        public function FlushObject()
        {
            if (($this->mStyle & BCS_BORDER)) {
                $this->DrawBorder();
            }
            if ($this->mStyle & BCS_IMAGE_PNG) {
                Header("Content-Type: image/png");
                ImagePng($this->mImg);
            } else if ($this->mStyle & BCS_IMAGE_JPEG) {
                Header("Content-Type: image/jpeg");
                ImageJpeg($this->mImg);
            }
        }

        public function DestroyObject()
        {
            ImageDestroy($this->mImg);
        }
    }
}
