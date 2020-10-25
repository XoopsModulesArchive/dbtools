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
 * Removes comment lines and splits up large sql files into individual queries
 *
 * Last revision: September 23, 2001 - gandon
 *
 * @param mixed $ret
 * @param mixed $sql
 * @param mixed $release
 *
 * @return  bool  always true
 */
function PMA_splitSqlFile(&$ret, $sql, $release)
{
    $sql = trim($sql);

    $sql_len = mb_strlen($sql);

    $char = '';

    $string_start = '';

    $in_string = false;

    $time0 = time();

    for ($i = 0; $i < $sql_len; ++$i) {
        $char = $sql[$i];

        // We are in a string, check for not escaped end of strings except for

        // backquotes that can't be escaped

        if ($in_string) {
            for (; ;) {
                $i = mb_strpos($sql, $string_start, $i);

                // No end of string found -> add the current substring to the

                // returned array

                if (!$i) {
                    $ret[] = $sql;

                    return true;
                }

                // Backquotes or no backslashes before quotes: it's indeed the

                // end of the string -> exit the loop

                elseif ('`' == $string_start || '\\' != $sql[$i - 1]) {
                    $string_start = '';

                    $in_string = false;

                    break;
                } // one or more Backslashes before the presumed end of string...

                // ... first checks for escaped backslashes

                $j = 2;

                $escaped_backslash = false;

                while ($i - $j > 0 && '\\' == $sql[$i - $j]) {
                    $escaped_backslash = !$escaped_backslash;

                    $j++;
                }

                // ... if escaped backslashes: it's really the end of the

                // string -> exit the loop

                if ($escaped_backslash) {
                    $string_start = '';

                    $in_string = false;

                    break;
                } // ... else loop

                $i++;

                // end if...elseif...else
            } // end for
        } // end if (in string)

        // We are not in a string, first check for delimiter...

        elseif (';' == $char) {
            // if delimiter found, add the parsed part to the returned array

            $ret[] = mb_substr($sql, 0, $i);

            $sql = ltrim(mb_substr($sql, min($i + 1, $sql_len)));

            $sql_len = mb_strlen($sql);

            if ($sql_len) {
                $i = -1;
            } else {
                // The submited statement(s) end(s) here

                return true;
            }
        } // end else if (is delimiter)

        // ... then check for start of a string,...

        elseif (('"' == $char) || ('\'' == $char) || ('`' == $char)) {
            $in_string = true;

            $string_start = $char;
        } // end else if (is start of string)

        // ... for start of a comment (and remove this comment if found)...

        elseif ('#' == $char
                || (' ' == $char && $i > 1 && $sql[$i - 2] . $sql[$i - 1] == '--')) {
            // starting position of the comment depends on the comment type

            $start_of_comment = (('#' == $sql[$i]) ? $i : $i - 2);

            // if no "\n" exits in the remaining string, checks for "\r"

            // (Mac eol style)

            $end_of_comment = (mb_strpos(' ' . $sql, "\012", $i + 2)) ?: mb_strpos(' ' . $sql, "\015", $i + 2);

            if (!$end_of_comment) {
                // no eol found after '#', add the parsed part to the returned

                // array if required and exit

                if ($start_of_comment > 0) {
                    $ret[] = trim(mb_substr($sql, 0, $start_of_comment));
                }

                return true;
            }

            $sql = mb_substr($sql, 0, $start_of_comment) . ltrim(mb_substr($sql, $end_of_comment));

            $sql_len = mb_strlen($sql);

            $i--;

        // end if...else
        } // end else if (is comment)

        // ... and finally disactivate the "/*!...*/" syntax if MySQL < 3.22.07

        elseif ($release < 32270
                && ('!' == $char && $i > 1 && $sql[$i - 2] . $sql[$i - 1] == '/*')) {
            $sql[$i] = ' ';
        } // end else if

        // loic1: send a fake header each 30 sec. to bypass browser timeout

        $time1 = time();

        if ($time1 >= $time0 + 30) {
            $time0 = $time1;

            header('X-pmaPing: Pong');
        } // end if
    } // end for

    // add any rest to the returned array

    if (!empty($sql) && preg_match('[^[:space:]]+', $sql)) {
        $ret[] = $sql;
    }

    return true;
} // end of the 'PMA_splitSqlFile()' function

if (!function_exists('is_uploaded_file')) {
    /**
     * Emulates the 'is_uploaded_file()' function for old php versions.
     * Grabbed at the php manual:
     *     http://www.php.net/manual/en/features.file-upload.php
     *
     * @param mixed $filename
     *
     * @return  bool   wether the file has been uploaded or not
     */

    function is_uploaded_file($filename)
    {
        if (!$tmp_file = @get_cfg_var('upload_tmp_dir')) {
            $tmp_file = tempnam('', '');

            $deleted = @unlink($tmp_file);

            $tmp_file = dirname($tmp_file);
        }

        $tmp_file .= '/' . basename($filename);

        // User might have trailing slash in php.ini...

        return (preg_replace('/+', '/', $tmp_file) == $filename);
    } // end of the 'is_uploaded_file()' emulated function
} // end if

/**
 * Increases the max. allowed time to run a script
 */
@set_time_limit($cfgExecTimeLimit);

/**
 * Gets some core libraries
 */
require __DIR__ . '/grab_globals.lib.php';
require __DIR__ . '/common.lib.php';

/**
 * Defines the url to return to in case of error in a sql statement
 */
if (!isset($goto)
    || ('db_details.php' != $goto && 'tbl_properties.php' != $goto)) {
    $goto = 'db_details.php';
}
$err_url = $goto . '?lang=' . $lang . '&amp;server=' . $server . '&amp;db=' . urlencode($db) . (('tbl_properties.php' == $goto) ? '&amp;table=' . urlencode($table) : '');

/**
 * Set up default values for some variables
 */
$view_bookmark = 0;
$sql_bookmark = $sql_bookmark ?? '';
$sql_query = $sql_query ?? '';
$sql_file = !empty($sql_file) ? $sql_file : 'none';

/**
 * Bookmark Support: get a query back from bookmark if required
 */
if (!empty($id_bookmark)) {
    require __DIR__ . '/bookmark.lib.php';

    switch ($action_bookmark) {
        case 0: // bookmarked query that have to be run
            $sql_query = PMA_queryBookmarks($db, $cfgBookmark, $id_bookmark);
            break;
        case 1: // bookmarked query that have to be displayed
            $sql_query = PMA_queryBookmarks($db, $cfgBookmark, $id_bookmark);
            $view_bookmark = 1;
            break;
        case 2: // bookmarked query that have to be deleted
            $sql_query = PMA_deleteBookmarks($db, $cfgBookmark, $id_bookmark);
            break;
    }
} // end if

/**
 * Prepares the sql query
 */
// Gets the query from a file if required
if ('none' != $sql_file) {
    if (file_exists($sql_file) && is_uploaded_file($sql_file)) {
        $open_basedir = '';

        if (PMA_PHP_INT_VERSION >= 40000) {
            $open_basedir = @ini_get('open_basedir');
        }

        if (empty($open_basedir)) {
            $open_basedir = @get_cfg_var('open_basedir');
        }

        // If we are on a server with open_basedir, we must move the file

        // before opening it. The doc explains how to create the "./tmp"

        // directory

        if (!empty($open_basedir)) {
            // check if '.' is in open_basedir

            $pos = mb_strpos($open_basedir, '.');

            // from the PHP annotated manual

            if ((PMA_PHP_INT_VERSION < 40000 && is_int($pos) && !$pos)
                || (PMA_PHP_INT_VERSION >= 40000 && false === $pos)) {
                // if no '.' in openbasedir, do not move the file, force the

                // error and let PHP report it

                error_reporting(E_ALL);

                $sql_query = fread(fopen($sql_file, 'rb'), filesize($sql_file));
            } else {
                $sql_file_new = './tmp/' . basename($sql_file);

                if (PMA_PHP_INT_VERSION < 40003) {
                    copy($sql_file, $sql_file_new);
                } else {
                    move_uploaded_file($sql_file, $sql_file_new);
                }

                $sql_query = fread(fopen($sql_file_new, 'rb'), filesize($sql_file_new));

                unlink($sql_file_new);
            }
        } else {
            // read from the normal upload dir

            $sql_query = fread(fopen($sql_file, 'rb'), filesize($sql_file));
        }

        if (1 == get_magic_quotes_runtime()) {
            $sql_query = stripslashes($sql_query);
        }
    }
} elseif (empty($id_bookmark) && 1 == get_magic_quotes_gpc()) {
    $sql_query = stripslashes($sql_query);
}
$sql_query = trim($sql_query);
// $sql_query come from the query textarea, if it's a reposted query gets its
// 'true' value
if (!empty($prev_sql_query)) {
    $prev_sql_query = urldecode($prev_sql_query);

    if ($sql_query == trim(htmlspecialchars($prev_sql_query, ENT_QUOTES | ENT_HTML5))) {
        $sql_query = $prev_sql_query;
    }
}

// Drop database is not allowed -> ensure the query can be run
if (!$cfgAllowUserDropDatabase
    && eregi('DROP[[:space:]]+(IF EXISTS[[:space:]]+)?DATABASE ', $sql_query)) {
    // Checks if the user is a Superuser

    // TODO: set a global variable with this information

    // loic1: optimized query

    $result = @$GLOBALS['xoopsDB']->queryF('USE mysql');

    if ($GLOBALS['xoopsDB']->error()) {
        require __DIR__ . '/header.inc.php';

        PMA_mysqlDie($strNoDropDatabases, '', '', $err_url);
    }
}
define('PMA_CHK_DROP', 1);

/**
 * Executes the query
 */
if ('' != $sql_query) {
    $pieces = [];

    PMA_splitSqlFile($pieces, $sql_query, PMA_MYSQL_INT_VERSION);

    $pieces_count = count($pieces);

    // Copy of the cleaned sql statement for display purpose only (see near the

    // beginning of "db_details.php" & "tbl_properties.php")

    if ('none' != $sql_file && $pieces_count > 10) {
        // Be nice with bandwidth...

        $sql_query_cpy = $sql_query = '';
    } else {
        $sql_query_cpy = implode(";\n", $pieces) . ';';
    }

    // Only one query to run

    if (1 == $pieces_count && !empty($pieces[0]) && 0 == $view_bookmark) {
        // sql.php will stripslash the query if get_magic_quotes_gpc

        if (function_exists('get_magic_quotes_gpc') && 1 == @get_magic_quotes_gpc()) {
            $sql_query = addslashes($pieces[0]);
        } else {
            $sql_query = $pieces[0];
        }

        if (eregi('^(DROP|CREATE)[[:space:]]+(IF EXISTS[[:space:]]+)?(TABLE|DATABASE)[[:space:]]+(.+)', $sql_query)) {
            $reload = 1;
        }

        require __DIR__ . '/sql.php';

        exit();
    } // Runs multiple queries

    elseif (mysqli_select_db($GLOBALS['xoopsDB']->conn, $db)) {
        for ($i = 0; $i < $pieces_count; $i++) {
            $a_sql_query = $pieces[$i];

            $result = $GLOBALS['xoopsDB']->queryF($a_sql_query);

            if (false === $result) { // readdump failed
                $my_die = $a_sql_query;

                break;
            }

            if (!isset($reload) && eregi('^(DROP|CREATE)[[:space:]]+(IF EXISTS[[:space:]]+)?(TABLE|DATABASE)[[:space:]]+(.+)', $a_sql_query)) {
                $reload = 1;
            }
        } // end for
    } // end else if
    unset($pieces);
} // end if

/**
 * MySQL error
 */
if (isset($my_die)) {
    $js_to_run = 'functions.js';

    require __DIR__ . '/header.inc.php';

    PMA_mysqlDie('', $my_die, '', $err_url);
}

/**
 * Go back to the calling script
 */
// Checks for a valid target script
if (isset($table) && '' == $table) {
    unset($table);
}
if (isset($db) && '' == $db) {
    unset($db);
}
$is_db = $is_table = false;
if ('tbl_properties.php' == $goto) {
    if (!isset($table)) {
        $goto = 'db_details.php';
    } else {
        $is_table = @$GLOBALS['xoopsDB']->queryF('SHOW TABLES LIKE \'' . PMA_sqlAddslashes($table, true) . '\'');

        if (!@mysql_numrows($is_table)) {
            $goto = 'db_details.php';

            unset($table);
        }
    } // end if... else...
}
if ('db_details.php' == $goto) {
    if (isset($table)) {
        unset($table);
    }

    if (!isset($db)) {
        $goto = 'main.php';
    } else {
        $is_db = @mysqli_select_db($GLOBALS['xoopsDB']->conn, $db);

        if (!$is_db) {
            $goto = 'main.php';

            unset($db);
        }
    } // end if... else...
}
// Defines the message to be displayed
/*if (!empty($id_bookmark) && $action_bookmark == 2) {
    $message   = $strBookmarkDeleted;
} else if (!isset($sql_query_cpy)) {
    $message   = $strNoQuery;
} else if ($sql_query_cpy == '') {
    $message   = "$strSuccess&nbsp;:<br>$strTheContent ($pieces_count $strInstructions)&nbsp;";
} else {
    $message   = $strSuccess;
}
// Loads to target script
if ($goto == 'db_details.php' || $goto == 'tbl_properties.php') {
    $js_to_run = 'functions.js';
}
if ($goto != 'main.php') {
    require __DIR__ . '/header.inc.php';
}
require __DIR__ . '/' . $goto;*/
headerFunction('../index.php');
exit;
