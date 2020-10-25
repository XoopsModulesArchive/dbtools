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

if (!defined('__OB_LIB__')) {
    define('__OB_LIB__', 1);

    # Output buffer functions for phpMyAdmin

    # Copyright 2001 Jeremy Brand <jeremy@nirvani.net>

    # http://www.jeremybrand.com/Jeremy/Brand/Jeremy_Brand.html

    # Check for all the needed functions for output buffering

    # Make some wrappers for the top and bottoms of our files.

    /**
     * This function be used eventually to support more modes.  It is needed
     * because both header and footer functions must know what each other is
     * doing.
     *
     * @return  int  the output buffer mode
     */

    function out_buffer_mode_get()
    {
        if (PHP_INT_VERSION >= 40000 && @function_exists('ob_start')) {
            $mode = 1;
        } else {
            $mode = 0;
        }

        // If a user sets the outputHandler in php.ini to ob_gzhandler, then

        // any right frame file in phpMyAdmin will not be handled properly by

        // the browser. My fix was to check the ini file within the

        // out_buffer_mode_get() function.

        // (Patch by Garth Gillespie, modified by Marc Delisle)

        if (PHP_INT_VERSION >= 40000 && @ini_get('outputHandler')) {
            if ('ob_gzhandler' == @ini_get('outputHandler')) {
                $mode = 0;
            }
        } elseif (PHP_INT_VERSION >= 40000) {
            if ('ob_gzhandler' == @get_cfg_var('outputHandler')) {
                $mode = 0;
            }
        }

        // End patch

        // Zero (0) is no mode or in other words output buffering is OFF.

        // Follow 2^0, 2^1, 2^2, 2^3 type values for the modes.

        // Usefull if we ever decide to combine modes.  Then a bitmask field of

        // the sum of all modes will be the natural choice.

        header('X-ob_mode: ' . $mode);

        return $mode;
    } // end of the 'out_buffer_mode_get()' function

    /**
     * This function will need to run at the top of all pages if output
     * output buffering is turned on.  It also needs to be passed $mode from
     * the out_buffer_mode_get() function or it will be useless.
     *
     * @param mixed $mode
     *
     * @return  bool  whether output buffering is enabled or not
     */

    function out_buffer_pre($mode)
    {
        switch ($mode) {
            case 1:
                ob_start('ob_gzhandler');
                $retval = true;
                break;
            case 0:
                $retval = false;
                break;
            // loic1: php3 fix
            default:
                $retval = false;
                break;
        } // end switch

        return $retval;
    } // end of the 'out_buffer_pre()' function

    /**
     * This function will need to run at the bottom of all pages if output
     * buffering is turned on.  It also needs to be passed $mode from the
     * out_buffer_mode_get() function or it will be useless.
     *
     * @param mixed $mode
     *
     * @return  bool  whether data has been send from the buffer or not
     */

    function out_buffer_post($mode)
    {
        switch ($mode) {
            case 1:
                # This output buffer doesn't need a footer.
                $retval = true;
                break;
            case 0:
                $retval = false;
                break;
            // loic1: php3 fix
            default:
                $retval = false;
                break;
        } // end switch

        return $retval;
    } // end of the 'out_buffer_post()' function
} // $__OB_LIB__
