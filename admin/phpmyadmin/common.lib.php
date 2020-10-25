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

if (!defined('PMA_COMMON_LIB_INCLUDED')) {
    define('PMA_COMMON_LIB_INCLUDED', 1);

    /**
     * Order of sections for common.lib.php:
     *
     * in PHP3, functions and constants must be physically defined
     * before they are referenced
     *
     * some functions need the constants of libraries/defines.lib.php
     *
     * the include of libraries/defines.lib.php must be after the connection
     * to db to get the MySql version
     *
     * the PMA_sqlAddslashes() function must be before the connection to db
     *
     * the authentication libraries must be before the connection to db but
     * after the PMA_isInto() function
     *
     * the PMA_mysqlDie() function must be before the connection to db but after
     * mysql extension has been loaded
     *
     * ... so the required order is:
     *
     * - parsing of the configuration file
     * - first load of the libraries/define.lib.php library (won't get the
     *   MySQL release number)
     * - load of mysql extension (if necessary)
     * - definition of PMA_sqlAddslashes()
     * - definition of PMA_mysqlDie()
     * - definition of PMA_isInto()
     * - loading of an authentication library
     * - db connection
     * - authentication work
     * - second load of the libraries/define.lib.php library to get the MySQL
     *   release number)
     * - other functions, respecting dependencies
     */

    /**
     * Avoids undefined variables in PHP3
     */

    if (!isset($use_backquotes)) {
        $use_backquotes = 0;
    }

    if (!isset($pos)) {
        $pos = 0;
    }

    /**
     * Parses the configuration file and gets some constants used to define
     * versions of phpMyAdmin/php/mysql...
     */

    require __DIR__ . '/config.inc.php';

    // For compatibility with old config.inc.php

    if (!isset($cfgExecTimeLimit)) {
        $cfgExecTimeLimit = 300; // 5 minuts
    }

    if (!isset($cfgShowStats)) {
        $cfgShowStats = true;
    }

    if (!isset($cfgShowTooltip)) {
        $cfgShowTooltip = true;
    }

    if (!isset($cfgShowMysqlInfo)) {
        $cfgShowMysqlInfo = false;
    }

    if (!isset($cfgShowMysqlVars)) {
        $cfgShowMysqlVars = false;
    }

    if (!isset($cfgShowPhpInfo)) {
        $cfgShowPhpInfo = false;
    }

    if (!isset($cfgShowAll)) {
        $cfgShowAll = false;
    }

    if (!isset($cfgNavigationBarIconic)) {
        $cfgNavigationBarIconic = true;
    }

    if (!isset($cfgProtectBinary)) {
        if (isset($cfgProtectBlob)) {
            $cfgProtectBinary = ($cfgProtectBlob ? 'blob' : false);

            unset($cfgProtectBlob);
        } else {
            $cfgProtectBinary = 'blob';
        }
    }

    if (!isset($cfgZipDump)) {
        $cfgZipDump = ($cfgGZipDump ?? true);
    }

    if (!isset($cfgLeftBgColor)) {
        $cfgLeftBgColor = '#D0DCE0';
    }

    if (!isset($cfgLeftPointerColor)) {
        $cfgLeftPointerColor = '';
    }

    if (!isset($cfgRightBgColor)) {
        $cfgRightBgColor = '#F5F5F5';
    }

    if (!isset($cfgBrowsePointerColor)) {
        $cfgBrowsePointerColor = '';
    }

    if (!isset($cfgTextareaCols)) {
        $cfgTextareaCols = 40;
    }

    if (!isset($cfgTextareaRows)) {
        $cfgTextareaRows = 7;
    }

    if (!isset($cfgDefaultDisplay)) {
        $cfgDefaultDisplay = 'horizontal';
    }

    if (!isset($cfgRepeatCells)) {
        $cfgRepeatCells = 100;
    }

    // Adds a trailing slash et the end of the phpMyAdmin uri if it does not

    // exist

    if ('' != $cfgPmaAbsoluteUri && '/' != mb_substr($cfgPmaAbsoluteUri, -1)) {
        $cfgPmaAbsoluteUri .= '/';
    }

    // Gets some constants

    require __DIR__ . '/defines.lib.php';

    // If zlib output compression is set in the php configuration file, no

    // output buffering should be run

    if (PMA_PHP_INT_VERSION < 40000
        || (PMA_PHP_INT_VERSION >= 40005 && @ini_get('zlib.output_compression'))) {
        $cfgOBGzip = false;
    }

    /**
     * Loads the mysql extensions if it is not loaded yet
     * staybyte - 26. June 2001
     */

    if (((PMA_PHP_INT_VERSION >= 40000 && !@ini_get('safe_mode') && @ini_get('enable_dl'))
         || (PMA_PHP_INT_VERSION > 30009 && !@get_cfg_var('safe_mode')))
        && @function_exists('dl')) {
        if (PMA_PHP_INT_VERSION < 40000) {
            $extension = 'MySQL';
        } else {
            $extension = 'mysql';
        }

        if (PMA_IS_WINDOWS) {
            $suffix = '.dll';
        } else {
            $suffix = '.so';
        }

        if (!@extension_loaded($extension)) {
            @dl($extension . $suffix);
        }

        if (!@extension_loaded($extension)) {
            echo $strCantLoadMySQL;

            exit();
        }
    } // end load mysql extension

    /**
     * Add slashes before "'" and "\" characters so a value containing them can
     * be used in a sql comparison.
     *
     * @param mixed $a_string
     * @param mixed $is_like
     *
     * @return  string   the slashed string
     */

    function PMA_sqlAddslashes($a_string = '', $is_like = false)
    {
        if ($is_like) {
            $a_string = str_replace('\\', '\\\\\\\\', $a_string);
        } else {
            $a_string = str_replace('\\', '\\\\', $a_string);
        }

        $a_string = str_replace('\'', '\\\'', $a_string);

        return $a_string;
    } // end of the 'PMA_sqlAddslashes()' function

    /**
     * Displays a MySQL error message in the right frame.
     *
     * @param mixed $error_message
     * @param mixed $the_query
     * @param mixed $is_modify_link
     * @param mixed $back_url
     */

    function PMA_mysqlDie(
        $error_message = '',
        $the_query = '',
        $is_modify_link = true,
        $back_url = ''
    ) {
        if (!$error_message) {
            $error_message = $GLOBALS['xoopsDB']->error();
        }

        if (!$the_query && !empty($GLOBALS['sql_query'])) {
            $the_query = $GLOBALS['sql_query'];
        }

        echo '<b>' . $GLOBALS['strError'] . '</b>' . "\n";

        // if the config password is wrong, or the MySQL server does not

        // respond, do not show the query that would reveal the

        // username/password

        if (!empty($the_query) && !mb_strstr($the_query, 'connect')) {
            $query_base = htmlspecialchars($the_query, ENT_QUOTES | ENT_HTML5);

            $query_base = preg_replace("((\015\012)|(\015)|(\012)){3,}", "\n\n", $query_base);

            echo '<p>' . "\n";

            echo '    ' . $GLOBALS['strSQLQuery'] . '&nbsp;:&nbsp;' . "\n";

            if ($is_modify_link) {
                echo '    [' . '<a href="db_details.php?lang=' . $GLOBALS['lang'] . '&amp;server=' . urlencode($GLOBALS['server']) . '&amp;db=' . urlencode($GLOBALS['db']) . '&amp;sql_query=' . urlencode($the_query) . '&amp;show_query=y">' . $GLOBALS['strEdit'] . '</a>' . ']' . "\n";
            } // end if

            echo '<pre>' . "\n" . $query_base . "\n" . '</pre>' . "\n";

            echo '</p>' . "\n";
        } // end if

        if (!empty($error_message)) {
            $error_message = htmlspecialchars($error_message, ENT_QUOTES | ENT_HTML5);

            $error_message = preg_replace("((\015\012)|(\015)|(\012)){3,}", "\n\n", $error_message);
        }

        echo '<p>' . "\n";

        echo '    ' . $GLOBALS['strMySQLSaid'] . '<br>' . "\n";

        echo '<pre>' . "\n" . $error_message . "\n" . '</pre>' . "\n";

        echo '</p>' . "\n";

        if (!empty($back_url)) {
            echo '<a href="' . $back_url . '">' . $GLOBALS['strBack'] . '</a>';
        }

        echo "\n";

        require __DIR__ . '/footer.inc.php';

        exit();
    } // end of the 'PMA_mysqlDie()' function

    /**
     * Defines whether a string exists inside an array or not
     *
     * @param mixed $toFind
     * @param mixed $in
     *
     * @return  int  the rank of the $toFind string in the array or '-1' if
     *                   it hasn't been found
     */

    function PMA_isInto($toFind, $in)
    {
        $max = count($in);

        for ($i = 0; $i < $max && ($toFind != $in[$i]); $i++) {
            // void();
        }

        return ($i < $max) ? $i : -1;
    }  // end of the 'PMA_isInto()' function

    /**
     * Use mysql_connect() or mysql_pconnect()?
     */

    $connect_func = ($cfgPersistentConnections) ? 'mysql_pconnect' : 'mysql_connect';

    $dblist = [];

    /**
     * Gets the valid servers list and parameters
     */

    reset($cfgServers);

    while (list($key, $val) = each($cfgServers)) {
        // Don't use servers with no hostname

        if (empty($val['host'])) {
            unset($cfgServers[$key]);
        }
    }

    if (empty($server) || !isset($cfgServers[$server]) || !is_array($cfgServers[$server])) {
        $server = $cfgServerDefault;
    }

    /**
     * If no server is selected, make sure that $cfgServer is empty (so that
     * nothing will work), and skip server authentication.
     * We do NOT exit here, but continue on without logging into any server.
     * This way, the welcome page will still come up (with no server info) and
     * present a choice of servers in the case that there are multiple servers
     * and '$cfgServerDefault = 0' is set.
     */

    if (0 == $server) {
        $cfgServer = [];
    } /**
     * Otherwise, set up $cfgServer and do the usual login stuff.
     */ elseif (isset($cfgServers[$server])) {
        $cfgServer = $cfgServers[$server];

        // Check how the config says to connect to the server

        $server_port = (empty($cfgServer['port'])) ? '' : ':' . $cfgServer['port'];

        if ('tcp' == mb_strtolower($cfgServer['connect_type'])) {
            $cfgServer['socket'] = '';
        }

        $server_socket = (empty($cfgServer['socket']) || PMA_PHP_INT_VERSION < 30010) ? '' : ':' . $cfgServer['socket'];

        // Ensures compatibility with old config files

        if (!isset($cfgServer['auth_type'])) {
            $cfgServer['auth_type'] = (isset($cfgServer['adv_auth']) && $cfgServer['adv_auth']) ? 'http' : 'config';
        }

        // Gets the authentication library that fits the cfgServer settings

        // and run authentication

        include $cfgServer['auth_type'] . '.auth.lib.php';

        if (!PMA_auth_check()) {
            PMA_auth();
        } else {
            PMA_auth_set_user();
        }

        // The user can work with only some databases

        if (isset($cfgServer['only_db']) && '' != $cfgServer['only_db']) {
            if (is_array($cfgServer['only_db'])) {
                $dblist = $cfgServer['only_db'];
            } else {
                $dblist[] = $cfgServer['only_db'];
            }
        } // end if

        if (PMA_PHP_INT_VERSION >= 40000) {
            $bkp_track_err = @ini_set('track_errors', 1);
        }

        // Try to connect MySQL with the standard user profile (will be used to

        // get the privileges list for the current user but the true user link

        // must be open after this one so it would be default one for all the

        // scripts)

        if ('' != $cfgServer['stduser']) {
            $dbh = @$connect_func(
                $cfgServer['host'] . $server_port . $server_socket,
                $cfgServer['stduser'],
                $cfgServer['stdpass']
            );

            if (false === $dbh) {
                if ($GLOBALS['xoopsDB']->error()) {
                    $conn_error = $GLOBALS['xoopsDB']->error();
                } elseif (isset($php_errormsg)) {
                    $conn_error = $php_errormsg;
                } else {
                    $conn_error = 'Cannot connect: invalid settings.';
                }

                $local_query = $connect_func . '(' . $cfgServer['host'] . $server_port . $server_socket . ', ' . $cfgServer['stduser'] . ', ' . $cfgServer['stdpass'] . ')';

                PMA_mysqlDie($conn_error, $local_query, false);
            } // end if
        } // end if

        // Connects to the server (validates user's login)

        $userlink = @$connect_func(
            $cfgServer['host'] . $server_port . $server_socket,
            $cfgServer['user'],
            $cfgServer['password']
        );

        if (false === $userlink) {
            PMA_auth_fails();
        } // end if

        if (PMA_PHP_INT_VERSION >= 40000) {
            @ini_set('track_errors', $bkp_track_err);
        }

        // If stduser isn't defined, use the current user settings to get his

        // rights

        if ('' == $cfgServer['stduser']) {
            $dbh = $userlink;
        }

        // if 'only_db' is set for the current user, there is no need to check for

        // available databases in the "mysql" db

        $dblist_cnt = count($dblist);

        if ($dblist_cnt) {
            $true_dblist = [];

            $is_show_dbs = true;

            for ($i = 0; $i < $dblist_cnt; $i++) {
                if ($is_show_dbs && preg_match('(^|[^\])(_|%)', $dblist[$i])) {
                    $local_query = 'SHOW DATABASES LIKE \'' . $dblist[$i] . '\'';

                    $rs = $GLOBALS['xoopsDB']->queryF($local_query, $dbh);

                    // "SHOW DATABASES" statement is disabled

                    if (0 == $i
                        && ($GLOBALS['xoopsDB']->error() && 1045 == $GLOBALS['xoopsDB']->errno())) {
                        $true_dblist[] = str_replace('\\_', '_', str_replace('\\%', '%', $dblist[$i]));

                        $is_show_dbs = false;
                    }

                    // Debug

                    // else if ($GLOBALS['xoopsDB']->error()) {

                    //    PMA_mysqlDie('', $local_query, FALSE);

                    // }

                    while (false !== ($row = @$GLOBALS['xoopsDB']->fetchRow($rs))) {
                        $true_dblist[] = $row[0];
                    } // end while

                    if ($rs) {
                        $GLOBALS['xoopsDB']->freeRecordSet($rs);
                    }
                } else {
                    $true_dblist[] = str_replace('\\_', '_', str_replace('\\%', '%', $dblist[$i]));
                } // end if... else...
            } // end for
            $dblist = $true_dblist;

            unset($true_dblist);
        } // end if

        // 'only_db' is empty for the current user -> checks for available

        // databases in the "mysql" db

        else {
            $auth_query = 'SELECT User, Select_priv ' . 'FROM mysql.user ' . 'WHERE User = \'' . PMA_sqlAddslashes($cfgServer['user']) . '\'';

            $rs = $GLOBALS['xoopsDB']->queryF($auth_query, $dbh); // Debug: or PMA_mysqlDie('', $auth_query, FALSE);
        } // end if

        // Access to "mysql" db allowed -> gets the usable db list

        if (!$dblist_cnt && @mysql_numrows($rs)) {
            $row = $GLOBALS['xoopsDB']->fetchBoth($rs);

            $GLOBALS['xoopsDB']->freeRecordSet($rs);

            // Correction uva 19991215

            // Previous code assumed database "mysql" admin table "db" column

            // "db" contains literal name of user database, and works if so.

            // Mysql usage generally (and uva usage specifically) allows this

            // column to contain regular expressions (we have all databases

            // owned by a given student/faculty/staff beginning with user i.d.

            // and governed by default by a single set of privileges with

            // regular expression as key). This breaks previous code.

            // This maintenance is to fix code to work correctly for regular

            // expressions.

            if ('Y' != $row['Select_priv']) {
                // 1. get allowed dbs from the "mysql.db" table

                // lem9: User can be blank (anonymous user)

                $local_query = 'SELECT DISTINCT Db FROM mysql.db WHERE Select_priv = \'Y\' AND (User = \'' . PMA_sqlAddslashes($cfgServer['user']) . '\' OR User = \'\')';

                $rs = $GLOBALS['xoopsDB']->queryF($local_query, $dbh); // Debug: or PMA_mysqlDie('', $local_query, FALSE);

                if (@mysql_numrows($rs)) {
                    // Will use as associative array of the following 2 code

                    // lines:

                    //   the 1st is the only line intact from before

                    //     correction,

                    //   the 2nd replaces $dblist[] = $row['Db'];

                    $uva_mydbs = [];

                    // Code following those 2 lines in correction continues

                    // populating $dblist[], as previous code did. But it is

                    // now populated with actual database names instead of

                    // with regular expressions.

                    while (false !== ($row = $GLOBALS['xoopsDB']->fetchBoth($rs))) {
                        // loic1: all databases cases - part 1

                        if (empty($row['Db']) || '%' == $row['Db']) {
                            $uva_mydbs['%'] = 1;

                            break;
                        }

                        // loic1: avoid multiple entries for dbs

                        if (!isset($uva_mydbs[$row['Db']])) {
                            $uva_mydbs[$row['Db']] = 1;
                        }
                    } // end while

                    $GLOBALS['xoopsDB']->freeRecordSet($rs);

                    $uva_alldbs = mysql_list_dbs($dbh);

                    // loic1: all databases cases - part 2

                    if (isset($uva_mydbs['%'])) {
                        while (false !== ($uva_row = $GLOBALS['xoopsDB']->fetchBoth($uva_alldbs))) {
                            $dblist[] = $uva_row[0];
                        } // end while
                    } // end if
                    else {
                        while (false !== ($uva_row = $GLOBALS['xoopsDB']->fetchBoth($uva_alldbs))) {
                            $uva_db = $uva_row[0];

                            if (isset($uva_mydbs[$uva_db]) && 1 == $uva_mydbs[$uva_db]) {
                                $dblist[] = $uva_db;

                                $uva_mydbs[$uva_db] = 0;
                            } elseif (!isset($dblist[$uva_db])) {
                                reset($uva_mydbs);

                                while (list($uva_matchpattern, $uva_value) = each($uva_mydbs)) {
                                    // loic1: fixed bad regexp

                                    // TODO: db names may contain characters

                                    //       that are regexp instructions

                                    $re = '(^|(\\\\\\\\)+|[^\])';

                                    $uva_regex = preg_replace($re . '%', '\\1.*', preg_replace($re . '_', '\\1.{1}', $uva_matchpattern));

                                    // Fixed db name matching

                                    // 2000-08-28 -- Benjamin Gandon

                                    if (preg_match('^' . $uva_regex . '$', $uva_db)) {
                                        $dblist[] = $uva_db;

                                        break;
                                    }
                                } // end while
                            } // end if ... else if....
                        } // end while
                    } // end else
                    $GLOBALS['xoopsDB']->freeRecordSet($uva_alldbs);

                    unset($uva_mydbs);
                } // end if

                // 2. get allowed dbs from the "mysql.tables_priv" table

                $local_query = 'SELECT DISTINCT Db FROM mysql.tables_priv WHERE Table_priv LIKE \'%Select%\' AND User = \'' . PMA_sqlAddslashes($cfgServer['user']) . '\'';

                $rs = $GLOBALS['xoopsDB']->queryF($local_query, $dbh); // Debug: or PMA_mysqlDie('', $local_query, FALSE);

                if (@mysql_numrows($rs)) {
                    while (false !== ($row = $GLOBALS['xoopsDB']->fetchBoth($rs))) {
                        if (-1 == PMA_isInto($row['Db'], $dblist)) {
                            $dblist[] = $row['Db'];
                        }
                    } // end while

                    $GLOBALS['xoopsDB']->freeRecordSet($rs);
                } // end if
            } // end if
        } // end building available dbs from the "mysql" db
    } // end server connecting

/**
 * Missing server hostname
 */ else {
    echo $strHostEmpty;
}

    /**
     * Get the list and number of available databases.
     *
     * @param mixed $error_url
     *
     * @return  bool  always true
     *
     * @global  array    the list of available databases
     * @global  int  the number of available databases
     */

    function PMA_availableDatabases($error_url = '')
    {
        global $dblist;

        global $num_dbs;

        $num_dbs = count($dblist);

        // 1. A list of allowed databases has already been defined by the

        //    authentification process -> gets the available databases list

        if ($num_dbs) {
            $true_dblist = [];

            for ($i = 0; $i < $num_dbs; $i++) {
                $dblink = @mysqli_select_db($GLOBALS['xoopsDB']->conn, $dblist[$i]);

                if ($dblink) {
                    $true_dblist[] = $dblist[$i];
                } // end if
            } // end for
            $dblist = [];

            $dblist = $true_dblist;

            unset($true_dblist);

            $num_dbs = count($dblist);
        } // end if

        // 2. Allowed database list is empty -> gets the list of all databases

        //    on the server

        else {
            $dbs = mysql_list_dbs() or PMA_mysqlDie('', 'mysql_list_dbs()', false, $error_url);

            $num_dbs = @$GLOBALS['xoopsDB']->getRowsNum($dbs);

            $real_num_dbs = 0;

            for ($i = 0; $i < $num_dbs; $i++) {
                $db_name_tmp = mysql_dbname($dbs, $i);

                $dblink = @mysqli_select_db($GLOBALS['xoopsDB']->conn, $db_name_tmp);

                if ($dblink) {
                    $dblist[] = $db_name_tmp;

                    $real_num_dbs++;
                }
            } // end for

            $GLOBALS['xoopsDB']->freeRecordSet($dbs);

            $num_dbs = $real_num_dbs;
        } // end else

        return true;
    } // end of the 'PMA_availableDatabases()' function

    /**
     * Gets constants that defines the PHP, MySQL... releases.
     * This include must be located physically before any code that needs to
     * reference the constants, else PHP 3.0.16 won't be happy; and must be
     * located after we are connected to db to get the MySql version.
     */

    require __DIR__ . '/defines.lib.php';

    /* ----------------------- Set of misc functions ----------------------- */

    /**
     * Determines the font sizes to use depending on the os and browser of the
     * user.
     *
     * This function is based on an article from phpBuilder (see
     * http://www.phpbuilder.net/columns/tim20000821.php).
     *
     * @return  bool    always true
     *
     * @global  string     the standard font size
     * @global  string     the font size for titles
     * @global  string     the small font size
     * @global  string     the smallest font size
     *
     * @version 1.1
     */

    function PMA_setFontSizes()
    {
        global $font_size, $font_bigger, $font_smaller, $font_smallest;

        // IE (<6)/Opera for win case: needs smaller fonts than anyone else

        if (PMA_USR_OS == 'Win'
            && ((PMA_USR_BROWSER_AGENT == 'IE' && PMA_USR_BROWSER_VER < 6) || PMA_USR_BROWSER_AGENT == 'OPERA')) {
            $font_size = 'x-small';

            $font_bigger = 'large';

            $font_smaller = '90%';

            $font_smallest = '7pt';
        } // IE6 and other browsers for win case

        elseif (PMA_USR_OS == 'Win') {
            $font_size = 'small';

            $font_bigger = 'large';

            $font_smaller = (PMA_USR_BROWSER_AGENT == 'IE') ? '90%' : 'x-small';

            $font_smallest = 'x-small';
        }

        // Some mac browsers need also smaller default fonts size (OmniWeb &

        // Opera)...

        elseif (PMA_USR_OS == 'Mac'
                && (PMA_USR_BROWSER_AGENT == 'OMNIWEB' || PMA_USR_BROWSER_AGENT == 'OPERA')) {
            $font_size = 'x-small';

            $font_bigger = 'large';

            $font_smaller = '90%';

            $font_smallest = '7pt';
        } // ... but most of them (except IE 5+ & NS 6+) need bigger fonts

        elseif (PMA_USR_OS == 'Mac'
                && ((PMA_USR_BROWSER_AGENT != 'IE' && PMA_USR_BROWSER_AGENT != 'MOZILLA')
                    || PMA_USR_BROWSER_VER < 5)) {
            $font_size = 'medium';

            $font_bigger = 'x-large';

            $font_smaller = 'small';

            $font_smallest = 'x-small';
        } // OS/2 browser

        elseif (PMA_USR_OS == 'OS/2'
                && PMA_USR_BROWSER_AGENT == 'OPERA') {
            $font_size = 'small';

            $font_bigger = 'medium';

            $font_smaller = 'x-small';

            $font_smallest = 'x-small';
        } else {
            $font_size = 'small';

            $font_bigger = 'large';

            $font_smaller = 'x-small';

            $font_smallest = 'x-small';
        }

        return true;
    } // end of the 'PMA_setFontSizes()' function

    /**
     * Adds backquotes on both sides of a database, table or field name.
     * Since MySQL 3.23.6 this allows to use non-alphanumeric characters in
     * these names.
     *
     * @param mixed $a_name
     * @param mixed $do_it
     *
     * @return  string   the "backquoted" database, table or field name if the
     *                   current MySQL release is >= 3.23.6, the original one
     *                   else
     */

    function PMA_backquote($a_name, $do_it = true)
    {
        if ($do_it
            && PMA_MYSQL_INT_VERSION >= 32306 && !empty($a_name)
            && '*' != $a_name) {
            return '`' . $a_name . '`';
        }

        return $a_name;
    } // end of the 'PMA_backquote()' function

    /**
     * Format a string so it can be passed to a javascript function.
     * This function is used to displays a javascript confirmation box for
     * "DROP/DELETE/ALTER" queries.
     *
     * @param mixed $a_string
     * @param mixed $add_backquotes
     *
     * @return  string   the formated string
     */

    function PMA_jsFormat($a_string = '', $add_backquotes = true)
    {
        if (is_string($a_string)) {
            $a_string = str_replace('"', '&quot;', $a_string);

            $a_string = str_replace('\\', '\\\\', $a_string);

            $a_string = str_replace('\'', '\\\'', $a_string);

            $a_string = str_replace('#', '\\#', $a_string);

            $a_string = str_replace("\012", '\\\\n', $a_string);

            $a_string = str_replace("\015", '\\\\r', $a_string);
        }

        return (($add_backquotes) ? PMA_backquote($a_string) : $a_string);
    } // end of the 'PMA_jsFormat()' function

    /**
     * Defines the <CR><LF> value depending on the user OS.
     *
     * @return  string   the <CR><LF> value to use
     */

    function PMA_whichCrlf()
    {
        $the_crlf = "\n";

        // The 'PMA_USR_OS' constant is defined in "./libraries/defines.lib.php"

        // Win case

        if (PMA_USR_OS == 'Win') {
            $the_crlf = "\r\n";
        } // Mac case

        elseif (PMA_USR_OS == 'Mac') {
            $the_crlf = "\r";
        } // Others

        else {
            $the_crlf = "\n";
        }

        return $the_crlf;
    } // end of the 'PMA_whichCrlf()' function

    /**
     * Counts and displays the number of records in a table
     *
     * Last revision 13 July 2001: Patch for limiting dump size from
     * vinay@sanisoft.com & girish@sanisoft.com
     *
     * @param mixed $db
     * @param mixed $table
     * @param mixed $ret
     *
     * @return  mixed    the number of records if retain is required, true else
     */

    function PMA_countRecords($db, $table, $ret = false)
    {
        $result = $GLOBALS['xoopsDB']->queryF('SELECT COUNT(*) AS num FROM ' . PMA_backquote($db) . '.' . PMA_backquote($table));

        $num = mysql_result($result, 0, 'num');

        $GLOBALS['xoopsDB']->freeRecordSet($result);

        if ($ret) {
            return $num;
        }

        echo number_format($num, 0, $GLOBALS['number_decimal_separator'], $GLOBALS['number_thousands_separator']);

        return true;
    } // end of the 'PMA_countRecords()' function

    /**
     * Displays a message at the top of the "main" (right) frame
     *
     * @param mixed $message
     */

    function PMA_showMessage($message)
    {
        // Reloads the navigation frame via JavaScript if required

        if (isset($GLOBALS['reload']) && $GLOBALS['reload']) {
            echo "\n";

            $reload_url = './left.php' . '?lang=' . $GLOBALS['lang'] . '&server=' . $GLOBALS['server'] . ((!empty($GLOBALS['db'])) ? '&db=' . urlencode($GLOBALS['db']) : ''); ?>
            <script type="text/javascript" language="javascript1.2">
                <!--
                window.parent.frames['nav'].location.replace('<?php echo $reload_url; ?>');
                //-->
            </script>
            <?php
        }

        echo "\n"; ?>
        <div align="<?php echo $GLOBALS['cell_align_left']; ?>">
            <table border="<?php echo $GLOBALS['cfgBorder']; ?>" cellpadding="5">
                <tr>
                    <td bgcolor="<?php echo $GLOBALS['cfgThBgcolor']; ?>">
                        <b><?php echo stripslashes($message); ?></b><br>
                    </td>
                </tr>
                <?php
                if (true === $GLOBALS['cfgShowSQL'] && !empty($GLOBALS['sql_query'])) {
                    echo "\n"; ?>
                    <tr>
                        <td bgcolor="<?php echo $GLOBALS['cfgBgcolorOne']; ?>">
                            <?php
                            echo "\n";

                    // The nl2br function isn't used because its result isn't a valid

                    // xhtml1.0 statement before php4.0.5 ("<br>" and not "<br>")

                    $new_line = '<br>' . "\n" . '            ';

                    $query_base = htmlspecialchars($GLOBALS['sql_query'], ENT_QUOTES | ENT_HTML5);

                    $query_base = preg_replace("((\015\012)|(\015)|(\012))+", $new_line, $query_base);

                    if (!isset($GLOBALS['show_query']) || 'y' != $GLOBALS['show_query']) {
                        if (!isset($GLOBALS['goto'])) {
                            $edit_target = (isset($GLOBALS['table'])) ? 'tbl_properties.php' : 'db_details.php';
                        } elseif ('main.php' != $GLOBALS['goto']) {
                            $edit_target = $GLOBALS['goto'];
                        } else {
                            $edit_target = '';
                        }

                        if ('tbl_properties.php' == $edit_target) {
                            $edit_link = '<a href="tbl_properties.php?lang='
                                                 . $GLOBALS['lang']
                                                 . '&amp;server='
                                                 . urlencode($GLOBALS['server'])
                                                 . '&amp;db='
                                                 . urlencode($GLOBALS['db'])
                                                 . '&amp;table='
                                                 . urlencode($GLOBALS['table'])
                                                 . '&amp;sql_query='
                                                 . urlencode($GLOBALS['sql_query'])
                                                 . '&amp;show_query=y#querybox">'
                                                 . $GLOBALS['strEdit']
                                                 . '</a>';
                        } elseif ('' != $edit_target) {
                            $edit_link = '<a href="db_details.php?lang='
                                                 . $GLOBALS['lang']
                                                 . '&amp;server='
                                                 . urlencode($GLOBALS['server'])
                                                 . '&amp;db='
                                                 . urlencode($GLOBALS['db'])
                                                 . '&amp;sql_query='
                                                 . urlencode($GLOBALS['sql_query'])
                                                 . '&amp;show_query=y#querybox">'
                                                 . $GLOBALS['strEdit']
                                                 . '</a>';
                        }
                    }

                    if (!empty($edit_target)) {
                        echo '            ' . $GLOBALS['strSQLQuery'] . '&nbsp;:&nbsp;[' . $edit_link . ']<br>' . "\n";
                    } else {
                        echo '            ' . $GLOBALS['strSQLQuery'] . '&nbsp;:<br>' . "\n";
                    }

                    echo '            ' . $query_base;

                    // If a 'LIMIT' clause has been programatically added to the query

                    // displays it

                    if (!empty($GLOBALS['sql_limit_to_append'])) {
                        echo $GLOBALS['sql_limit_to_append'];
                    }

                    echo "\n"; ?>
                        </td>
                    </tr>
                    <?php
                }

        echo "\n"; ?>
            </table>
        </div><br>
        <?php
    } // end of the 'PMA_showMessage()' function

    /**
     * Displays a link to the official MySQL documentation (short)
     *
     * @param mixed $link
     *
     * @return  string  the html link
     */

    function PMA_showDocuShort($link)
    {
        if (!empty($GLOBALS['cfgManualBaseShort'])) {
            return '[<a href="' . $GLOBALS['cfgManualBaseShort'] . '/' . $link . '" target="mysql_doc">' . $GLOBALS['strDocu'] . '</a>]';
        }
    } // end of the 'PMA_showDocuShort()' function

    /**
     * Formats $value to byte view
     *
     * @param mixed $value
     * @param mixed $limes
     * @param mixed $comma
     *
     * @return   array    the formatted value and its unit
     *
     * @author   staybyte
     * @version  1.1 - 07 July 2001
     */

    function PMA_formatByteDown($value, $limes = 6, $comma = 0)
    {
        $dh = pow(10, $comma);

        $li = pow(10, $limes);

        $return_value = $value;

        $unit = $GLOBALS['byteUnits'][0];

        if ($value >= $li * 1000000) {
            $value = round($value / (1073741824 / $dh)) / $dh;

            $unit = $GLOBALS['byteUnits'][3];
        } elseif ($value >= $li * 1000) {
            $value = round($value / (1048576 / $dh)) / $dh;

            $unit = $GLOBALS['byteUnits'][2];
        } elseif ($value >= $li) {
            $value = round($value / (1024 / $dh)) / $dh;

            $unit = $GLOBALS['byteUnits'][1];
        }

        if ($unit != $GLOBALS['byteUnits'][0]) {
            $return_value = number_format($value, $comma, $GLOBALS['number_decimal_separator'], $GLOBALS['number_thousands_separator']);
        } else {
            $return_value = number_format($value, 0, $GLOBALS['number_decimal_separator'], $GLOBALS['number_thousands_separator']);
        }

        return [$return_value, $unit];
    } // end of the 'PMA_formatByteDown' function

    /**
     * Ensures a database/table/field's name is not a reserved word (for MySQL
     * releases < 3.23.6)
     *
     * @param mixed $the_name
     * @param mixed $error_url
     *
     * @return   bool  true if the name is valid (no return else)
     *
     * @author   Dell'Aiera Pol; Olivier Blin
     */

    function PMA_checkReservedWords($the_name, $error_url)
    {
        // The name contains caracters <> a-z, A-Z and "_" -> not a reserved

        // word

        if (!preg_match('^[a-zA-Z_]+$', $the_name)) {
            return true;
        }

        // Else do the work

        $filename = 'badwords.txt';

        if (file_exists($filename)) {
            // Builds the reserved words array

            $fd = fopen($filename, 'rb');

            $contents = fread($fd, filesize($filename) - 1);

            fclose($fd);

            $word_list = explode("\n", $contents);

            // Do the checking

            $word_cnt = count($word_list);

            for ($i = 0; $i < $word_cnt; $i++) {
                if (mb_strtolower($the_name) == $word_list[$i]) {
                    PMA_mysqlDie(sprintf($GLOBALS['strInvalidName'], $the_name), '', false, $error_url);
                } // end if
            } // end for
        } // end if
    } // end of the 'PMA_checkReservedWords' function

    /**
     * Writes localised date
     *
     * @param mixed $timestamp
     *
     * @return  string   the formatted date
     */

    function PMA_localisedDate($timestamp = -1)
    {
        global $datefmt, $month, $day_of_week;

        if (-1 == $timestamp) {
            $timestamp = time();
        }

        $date = preg_replace('%[aA]', $day_of_week[(int)strftime('%w', $timestamp)], $datefmt);

        $date = preg_replace('%[bB]', $month[(int)strftime('%m', $timestamp) - 1], $date);

        return strftime($date, $timestamp);
    } // end of the 'PMA_localisedDate()' function
} // $__PMA_COMMON_LIB__
?>
