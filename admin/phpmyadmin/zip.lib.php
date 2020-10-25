<?php

//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <https://www.xoops.org>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author:   melkahdor                                                       //
// URL:      http://81.91.66.251/~xoops-factory.com -http://www.melkahdor.com//
// Project:  The XOOPS Project (https://www.xoops.org/)                       //
// Based on  optimise from http://web-lien.net &                              //
//             phpmyadmin http://www.phpwizard.net                             //
// ------------------------------------------------------------------------- //

/**
 * Zip file creation class.
 * Makes zip files.
 *
 * Based on :
 *
 *  http://www.zend.com/codex.php?id=535&single=1
 *  By Eric Mueller (eric@themepark.com)
 *
 *  http://www.zend.com/codex.php?id=470&single=1
 *  by Denis125 (webmaster@atlant.ru)
 *
 * Official ZIP file format: http://www.pkware.com/appnote.txt
 */
class zipfile
{
    /**
     * Array to store compressed data
     *
     * @var  array
     */

    public $datasec = [];

    /**
     * Central directory
     *
     * @var  array
     */

    public $ctrl_dir = [];

    /**
     * End of central directory record
     *
     * @var  string
     */

    public $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";

    /**
     * Last offset position
     *
     * @var  int
     */

    public $old_offset = 0;

    /**
     * Adds "file" to archive
     *
     * @param mixed $data
     * @param mixed $name
     */
    public function addFile($data, $name)
    {
        $name = str_replace('\\', '/', $name);

        $fr = "\x50\x4b\x03\x04";

        $fr .= "\x14\x00";            // ver needed to extract
        $fr .= "\x00\x00";            // gen purpose bit flag
        $fr .= "\x08\x00";            // compression method
        $fr .= "\x00\x00\x00\x00";    // last mod time and date

        // "local file header" segment

        $unc_len = mb_strlen($data);

        $crc = crc32($data);

        $zdata = gzcompress($data);

        $zdata = mb_substr(mb_substr($zdata, 0, -4), 2); // fix crc bug

        $c_len = mb_strlen($zdata);

        $fr .= pack('V', $crc);             // crc32
        $fr .= pack('V', $c_len);           // compressed filesize
        $fr .= pack('V', $unc_len);         // uncompressed filesize
        $fr .= pack('v', mb_strlen($name));    // length of filename
        $fr .= pack('v', 0);                // extra field length
        $fr .= $name;

        // "file data" segment

        $fr .= $zdata;

        // "data descriptor" segment (optional but necessary if archive is not
        // served as file)
        $fr .= pack('V', $crc);                 // crc32
        $fr .= pack('V', $c_len);               // compressed filesize
        $fr .= pack('V', $unc_len);             // uncompressed filesize

        // add this entry to array

        $this->datasec[] = $fr;

        $new_offset = mb_strlen(implode('', $this->datasec));

        // now add to central directory record

        $cdrec = "\x50\x4b\x01\x02";

        $cdrec .= "\x00\x00";                // version made by
        $cdrec .= "\x14\x00";                // version needed to extract
        $cdrec .= "\x00\x00";                // gen purpose bit flag
        $cdrec .= "\x08\x00";                // compression method
        $cdrec .= "\x00\x00\x00\x00";        // last mod time & date
        $cdrec .= pack('V', $crc);           // crc32
        $cdrec .= pack('V', $c_len);         // compressed filesize
        $cdrec .= pack('V', $unc_len);       // uncompressed filesize
        $cdrec .= pack('v', mb_strlen($name)); // length of filename
        $cdrec .= pack('v', 0);             // extra field length
        $cdrec .= pack('v', 0);             // file comment length
        $cdrec .= pack('v', 0);             // disk number start
        $cdrec .= pack('v', 0);             // internal file attributes
        $cdrec .= pack('V', 32);            // external file attributes - 'archive' bit set

        $cdrec .= pack('V', $this->old_offset); // relative offset of local header

        $this->old_offset = $new_offset;

        $cdrec .= $name;

        // optional extra field, file comment goes here

        // save to central directory

        $this->ctrl_dir[] = $cdrec;
    }

    // end of the 'addFile()' method

    /**
     * Dumps out file
     *
     * @return  string  the zipped file
     */
    public function file()
    {
        $data = implode('', $this->datasec);

        $ctrldir = implode('', $this->ctrl_dir);

        return $data . $ctrldir . $this->eof_ctrl_dir . pack('v', count($this->ctrl_dir)) .  // total # of entries "on this disk"
               pack('v', count($this->ctrl_dir)) .  // total # of entries overall
               pack('V', mb_strlen($ctrldir)) .           // size of central dir
               pack('V', mb_strlen($data)) .              // offset to start of central dir
               "\x00\x00";                             // .zip file comment length
    }

    // end of the 'file()' method
} // end of the 'zipfile' class
