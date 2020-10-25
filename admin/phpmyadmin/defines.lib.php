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
 * DEFINES VARIABLES & CONSTANTS
 * Overview:
 *    PMA_VERSION              (string) - phpMyAdmin version string
 *    PMA_PHP_INT_VERSION      (int)    - eg: 30017 instead of 3.0.17 or
 *                                        40006 instead of 4.0.6RC3
 *    PMA_IS_WINDOWS           (bool)   - mark if phpMyAdmin running on windows
 *                                        server
 *    PMA_MYSQL_INT_VERSION    (int)    - eg: 32339 instead of 3.23.39
 *    PMA_USR_OS               (string) - the plateform (os) of the user
 *    PMA_USR_BROWSER_AGENT    (string) - the browser of the user
 *    PMA_USR_BROWSER_VER      (double) - the version of this browser
 */
// phpMyAdmin release
if (!defined('PMA_VERSION')) {
    define('PMA_VERSION', '2.2.3');
}

// php version
if (!defined('PMA_PHP_INT_VERSION')) {
    if (!preg_match('([0-9]{1,2}).([0-9]{1,2}).([0-9]{1,2})', phpversion(), $match)) {
        $result = preg_match('([0-9]{1,2}).([0-9]{1,2})', phpversion(), $match);
    }

    if (isset($match) && !empty($match[1])) {
        if (!isset($match[2])) {
            $match[2] = 0;
        }

        if (!isset($match[3])) {
            $match[3] = 0;
        }

        define('PMA_PHP_INT_VERSION', (int)sprintf('%d%02d%02d', $match[1], $match[2], $match[3]));

        unset($match);
    } else {
        define('PMA_PHP_INT_VERSION', 0);
    }
}

// Whether the os php is running on is windows or not
if (!defined('PMA_IS_WINDOWS')) {
    if (defined('PHP_OS') && eregi('win', PHP_OS)) {
        define('PMA_IS_WINDOWS', 1);
    } else {
        define('PMA_IS_WINDOWS', 0);
    }
}

// MySQL Version
if (!defined('PMA_MYSQL_INT_VERSION') && isset($userlink)) {
    if (!empty($server)) {
        $result = $GLOBALS['xoopsDB']->queryF('SELECT VERSION() AS version');

        if (false !== $result && @$GLOBALS['xoopsDB']->getRowsNum($result) > 0) {
            $row = $GLOBALS['xoopsDB']->fetchBoth($result);

            $match = explode('.', $row['version']);
        } else {
            $result = @$GLOBALS['xoopsDB']->queryF('SHOW VARIABLES LIKE \'version\'');

            if (false !== $result && @$GLOBALS['xoopsDB']->getRowsNum($result) > 0) {
                $row = $GLOBALS['xoopsDB']->fetchRow($result);

                $match = explode('.', $row[1]);
            }
        }
    } // end server id is defined case

    if (!isset($match) || !isset($match[0])) {
        $match[0] = 3;
    }

    if (!isset($match[1])) {
        $match[1] = 21;
    }

    if (!isset($match[2])) {
        $match[2] = 0;
    }

    define('PMA_MYSQL_INT_VERSION', (int)sprintf('%d%02d%02d', $match[0], $match[1], (int)$match[2]));

    unset($match);
}

// Determines platform (OS), browser and version of the user
// Based on a phpBuilder article:
//   see http://www.phpbuilder.net/columns/tim20000821.php
if (!defined('PMA_USR_OS')) {
    // loic1 - 2001/25/11: use the new globals arrays defined with

    // php 4.1+

    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        $HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
    } elseif (!empty($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {
        $HTTP_USER_AGENT = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
    }

    // 1. Platform

    if (mb_strstr($HTTP_USER_AGENT, 'Win')) {
        define('PMA_USR_OS', 'Win');
    } elseif (mb_strstr($HTTP_USER_AGENT, 'Mac')) {
        define('PMA_USR_OS', 'Mac');
    } elseif (mb_strstr($HTTP_USER_AGENT, 'Linux')) {
        define('PMA_USR_OS', 'Linux');
    } elseif (mb_strstr($HTTP_USER_AGENT, 'Unix')) {
        define('PMA_USR_OS', 'Unix');
    } elseif (mb_strstr($HTTP_USER_AGENT, 'OS/2')) {
        define('PMA_USR_OS', 'OS/2');
    } else {
        define('PMA_USR_OS', 'Other');
    }

    // 2. browser and version

    if (preg_match('MSIE ([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
        define('PMA_USR_BROWSER_VER', $log_version[1]);

        define('PMA_USR_BROWSER_AGENT', 'IE');
    } elseif (preg_match('Opera(/| )([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
        define('PMA_USR_BROWSER_VER', $log_version[2]);

        define('PMA_USR_BROWSER_AGENT', 'OPERA');
    } elseif (preg_match('OmniWeb/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
        define('PMA_USR_BROWSER_VER', $log_version[1]);

        define('PMA_USR_BROWSER_AGENT', 'OMNIWEB');
    } elseif (preg_match('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
        define('PMA_USR_BROWSER_VER', $log_version[1]);

        define('PMA_USR_BROWSER_AGENT', 'MOZILLA');
    } elseif (preg_match('Konqueror/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
        define('PMA_USR_BROWSER_VER', $log_version[1]);

        define('PMA_USR_BROWSER_AGENT', 'KONQUEROR');
    } else {
        define('PMA_USR_BROWSER_VER', 0);

        define('PMA_USR_BROWSER_AGENT', 'OTHER');
    }
} // $__PMA_DEFINES_LIB__
