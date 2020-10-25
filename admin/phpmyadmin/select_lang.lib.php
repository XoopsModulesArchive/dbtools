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
 * phpMyAdmin Language Loading File
 */

/**
 * Define the path to the translations directory and get some variables
 * from system arrays if 'register_globals' is set to 'off'
 */
$lang_path = '';

/**
 * All the supported languages have to be listed in the array below.
 * 1. The key must be the "official" ISO 639 language code and, if required,
 *    the dialect code. It can also contains some informations about the
 *    charset (see the Russian case).
 *    These code are displayed at the starting page of phpMyAdmin.
 * 2. The first of the values associated to the key is used in a regular
 *    expression to find some keywords corresponding to the language inside two
 *    environment variables.
 *    These values contains:
 *    - the "official" ISO language code and, if required, the dialect code
 *      also ('bu' for Bulgarian, 'fr([-_][[:alpha:]]{2})?' for all French
 *      dialects, 'zh[-_]tw' for Chinese traditional...);
 *    - the '|' character (it means 'OR');
 *    - the full language name.
 * 3. The second values associated to the key is the name of the file to load
 *    without the 'inc.php' extension.
 * 4. The last values associated to the key is the language code as defined by
 *    the RFC1766.
 *
 * Beware that the sorting order (first values associated to keys by
 * alphabetical reverse order in the array) is important: 'zh-tw' (chinese
 * traditional) must be detected before 'zh' (chinese simplified) for
 * example.
 */
$available_languages = [
    'ar' => ['ar([-_][[:alpha:]]{2})?|arabic', 'arabic', 'ar'],
'bg-koi8r' => ['bg|bulgarian', 'bulgarian-koi8', 'bg'],
'bg-win1251' => ['bg|bulgarian', 'bulgarian-win1251', 'bg'],
'ca' => ['ca|catalan', 'catala', 'ca'],
'cs-iso' => ['cs|czech', 'czech-iso', 'cs'],
'cs-win1250' => ['cs|czech', 'czech-win1250', 'cs'],
'da' => ['da|danish', 'danish', 'da'],
'de' => ['de([-_][[:alpha:]]{2})?|german', 'german', 'de'],
'el' => ['el|greek', 'greek', 'el'],
'en' => ['en([-_][[:alpha:]]{2})?|english', 'english', 'en'],
'es' => ['es([-_][[:alpha:]]{2})?|spanish', 'spanish', 'es'],
'fi' => ['fi|finnish', 'finnish', 'fi'],
'fr' => ['fr([-_][[:alpha:]]{2})?|french', 'french', 'fr'],
'gl' => ['gl|galician', 'galician', 'gl'],
'it' => ['it|italian', 'italian', 'it'],
'ja' => ['ja|japanese', 'japanese', 'ja'],
'ko' => ['ko|korean', 'korean', 'ko'],
'nl' => ['nl([-_][[:alpha:]]{2})?|dutch', 'dutch', 'nl'],
'no' => ['no|norwegian', 'norwegian', 'no'],
'pl' => ['pl|polish', 'polish', 'pl'],
'pt-br' => ['pt[-_]br|brazilian portuguese', 'brazilian_portuguese', 'pt-BR'],
'pt' => ['pt([-_][[:alpha:]]{2})?|portuguese', 'portuguese', 'pt'],
'ro' => ['ro|romanian', 'romanian', 'ro'],
'ru-koi8r' => ['ru|russian', 'russian-koi8', 'ru'],
'ru-win1251' => ['ru|russian', 'russian-win1251', 'ru'],
'se' => ['se|swedish', 'swedish', 'se'],
'sk' => ['sk|slovak', 'slovak-iso', 'sk'],
'th' => ['th|thai', 'thai', 'th'],
'tr' => ['tr|turkish', 'turkish', 'tr'],
'zh-tw' => ['zh[-_]tw|chinese traditional', 'chinese_big5', 'zh-TW'],
'zh' => ['zh|chinese simplified', 'chinese_gb', 'zh'],
];

if (!defined('PMA_IS_LANG_DETECT_FUNCTION')) {
    define('PMA_IS_LANG_DETECT_FUNCTION', 1);

    /**
     * Analyzes some PHP environment variables to find the most probable language
     * that should be used
     *
     * @param string $str
     * @param mixed  $envType
     *
     * @global  array    the list of available translations
     * @global  string   the retained translation keyword
     */

    function PMA_langDetect($str = '', $envType = '')
    {
        global $available_languages;

        global $lang;

        reset($available_languages);

        while (list($key, $value) = each($available_languages)) {
            // $envType =  1 for the 'HTTP_ACCEPT_LANGUAGE' environment variable,

            //             2 for the 'HTTP_USER_AGENT' one

            if ((1 == $envType && eregi('^(' . $value[0] . ')(;q=[0-9]\\.[0-9])?$', $str))
                || (2 == $envType && eregi('(\(|\[|;[[:space:]])(' . $value[0] . ')(;|\]|\))', $str))) {
                $lang = $key;

                break;
            }
        }
    } // end of the 'PMA_langDetect()' function
} // end if

/**
 * Get some global variables if 'register_globals' is set to 'off'
 * loic1 - 2001/25/11: use the new globals arrays defined with php 4.1+
 */
if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $HTTP_ACCEPT_LANGUAGE = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
} elseif (!empty($HTTP_SERVER_VARS['HTTP_ACCEPT_LANGUAGE'])) {
    $HTTP_ACCEPT_LANGUAGE = $HTTP_SERVER_VARS['HTTP_ACCEPT_LANGUAGE'];
}

if (!empty($_SERVER['HTTP_USER_AGENT'])) {
    $HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
} elseif (!empty($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {
    $HTTP_USER_AGENT = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
}

if (!isset($lang)) {
    if (isset($_GET) && !empty($_GET['lang'])) {
        $lang = $_GET['lang'];
    } elseif (isset($_GET) && !empty($_GET['lang'])) {
        $lang = $_GET['lang'];
    } elseif (isset($_POST) && !empty($_POST['lang'])) {
        $lang = $_POST['lang'];
    } elseif (isset($_POST) && !empty($_POST['lang'])) {
        $lang = $_POST['lang'];
    } elseif (isset($_COOKIE) && !empty($_COOKIE['lang'])) {
        $lang = $_COOKIE['lang'];
    } elseif (isset($HTTP_COOKIE_VARS) && !empty($HTTP_COOKIE_VARS['lang'])) {
        $lang = $HTTP_COOKIE_VARS['lang'];
    }
}

/**
 * Do the work!
 */
// Lang forced
if (!empty($cfgLang)) {
    $lang = $cfgLang;
}

// If '$lang' is defined, ensure this is a valid translation
if (!empty($lang) && empty($available_languages[$lang])) {
    $lang = '';
}

// Language is not defined yet :
// 1. try to findout users language by checking it's HTTP_ACCEPT_LANGUAGE
//    variable
if (empty($lang) && !empty($HTTP_ACCEPT_LANGUAGE)) {
    $accepted = explode(',', $HTTP_ACCEPT_LANGUAGE);

    $acceptedCnt = count($accepted);

    reset($accepted);

    for ($i = 0; $i < $acceptedCnt && empty($lang); $i++) {
        PMA_langDetect($accepted[$i], 1);
    }
}
// 2. try to findout users language by checking it's HTTP_USER_AGENT variable
if (empty($lang) && !empty($HTTP_USER_AGENT)) {
    PMA_langDetect($HTTP_USER_AGENT, 2);
}

// 3. Didn't catch any valid lang : we use the default settings
if (empty($lang)) {
    $lang = $cfgDefaultLang;
}

// 4. Defines the associated filename and load the translation
$lang_file = $lang_path . $available_languages[$lang][1] . '.inc.php';
require __DIR__ . '/' . $lang_file;

// $__PMA_SELECT_LANG_LIB__
