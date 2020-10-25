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

if (!defined('PMA_BUILD_DUMP_LIB_INCLUDED')) {
    define('PMA_BUILD_DUMP_LIB_INCLUDED', 1);

    /**
     * Uses the 'htmlspecialchars()' php function on databases, tables and fields
     * name if the dump has to be displayed on screen.
     *
     * @param mixed $a_string
     *
     * @return  string   the formatted string
     */

    function PMA_htmlFormat($a_string = '')
    {
        return (empty($GLOBALS['asfile']) ? htmlspecialchars($a_string, ENT_QUOTES | ENT_HTML5) : $a_string);
    } // end of the 'PMA_htmlFormat()' function

    /**
     * Returns $table's CREATE definition
     *
     * Uses the 'PMA_htmlFormat()' function defined in 'tbl_dump.php'
     *
     * @param mixed $db
     * @param mixed $table
     * @param mixed $crlf
     * @param mixed $error_url
     *
     * @return  string   the CREATE statement on success
     *
     * @global  bool  whether to add 'drop' statements or not
     * @global  bool  whether to use backquotes to allow the use of special
     *                   characters in database, table and fields names or not
     *
     * @see     PMA_htmlFormat()
     */

    function PMA_getTableDef($db, $table, $crlf, $error_url)
    {
        global $drop;

        global $use_backquotes;

        $schema_create = '';

        if (!empty($drop)) {
            $schema_create .= 'DROP TABLE IF EXISTS ' . PMA_backquote(PMA_htmlFormat($table), $use_backquotes) . ';' . $crlf;
        }

        // Steve Alberty's patch for complete table dump,

        // modified by Lem9 to allow older MySQL versions to continue to work

        if (PMA_MYSQL_INT_VERSION >= 32321) {
            // Whether to quote table and fields names or not

            if ($use_backquotes) {
                $GLOBALS['xoopsDB']->queryF('SET SQL_QUOTE_SHOW_CREATE = 1');
            } else {
                $GLOBALS['xoopsDB']->queryF('SET SQL_QUOTE_SHOW_CREATE = 0');
            }

            $result = $GLOBALS['xoopsDB']->queryF('SHOW CREATE TABLE ' . PMA_backquote($db) . '.' . PMA_backquote($table));

            if (false !== $result && $GLOBALS['xoopsDB']->getRowsNum($result) > 0) {
                $tmpres = $GLOBALS['xoopsDB']->fetchBoth($result);

                $schema_create .= str_replace("\n", $crlf, PMA_htmlFormat($tmpres[1]));
            }

            $GLOBALS['xoopsDB']->freeRecordSet($result);

            return $schema_create;
        } // end if MySQL >= 3.23.20

        // For MySQL < 3.23.20

        $schema_create .= 'CREATE TABLE ' . PMA_htmlFormat(PMA_backquote($table), $use_backquotes) . ' (' . $crlf;

        $local_query = 'SHOW FIELDS FROM ' . PMA_backquote($db) . '.' . PMA_backquote($table);

        $result = $GLOBALS['xoopsDB']->queryF($local_query) or PMA_mysqlDie('', $local_query, '', $error_url);

        while (false !== ($row = $GLOBALS['xoopsDB']->fetchBoth($result))) {
            $schema_create .= '   ' . PMA_htmlFormat(PMA_backquote($row['Field'], $use_backquotes)) . ' ' . $row['Type'];

            if (isset($row['Default']) && '' != $row['Default']) {
                $schema_create .= ' DEFAULT \'' . PMA_htmlFormat(PMA_sqlAddslashes($row['Default'])) . '\'';
            }

            if ('YES' != $row['Null']) {
                $schema_create .= ' NOT NULL';
            }

            if ('' != $row['Extra']) {
                $schema_create .= ' ' . $row['Extra'];
            }

            $schema_create .= ',' . $crlf;
        } // end while

        $GLOBALS['xoopsDB']->freeRecordSet($result);

        $schema_create = preg_replace(',' . $crlf . '$', '', $schema_create);

        $local_query = 'SHOW KEYS FROM ' . PMA_backquote($db) . '.' . PMA_backquote($table);

        $result = $GLOBALS['xoopsDB']->queryF($local_query) or PMA_mysqlDie('', $local_query, '', $error_url);

        while (false !== ($row = $GLOBALS['xoopsDB']->fetchBoth($result))) {
            $kname = $row['Key_name'];

            $comment = $row['Comment'] ?? '';

            $sub_part = $row['Sub_part'] ?? '';

            if ('PRIMARY' != $kname && 0 == $row['Non_unique']) {
                $kname = "UNIQUE|$kname";
            }

            if ('FULLTEXT' == $comment) {
                $kname = 'FULLTEXT|$kname';
            }

            if (!isset($index[$kname])) {
                $index[$kname] = [];
            }

            if ($sub_part > 1) {
                $index[$kname][] = PMA_htmlFormat(PMA_backquote($row['Column_name'], $use_backquotes)) . '(' . $sub_part . ')';
            } else {
                $index[$kname][] = PMA_htmlFormat(PMA_backquote($row['Column_name'], $use_backquotes));
            }
        } // end while

        $GLOBALS['xoopsDB']->freeRecordSet($result);

        while (list($x, $columns) = @each($index)) {
            $schema_create .= ',' . $crlf;

            if ('PRIMARY' == $x) {
                $schema_create .= '   PRIMARY KEY (';
            } elseif ('UNIQUE' == mb_substr($x, 0, 6)) {
                $schema_create .= '   UNIQUE ' . mb_substr($x, 7) . ' (';
            } elseif ('FULLTEXT' == mb_substr($x, 0, 8)) {
                $schema_create .= '   FULLTEXT ' . mb_substr($x, 9) . ' (';
            } else {
                $schema_create .= '   KEY ' . $x . ' (';
            }

            $schema_create .= implode(', ', $columns) . ')';
        } // end while

        $schema_create .= $crlf . ')';

        return $schema_create;
    } // end of the 'PMA_getTableDef()' function

    /**
     * php >= 4.0.5 only : get the content of $table as a series of INSERT
     * statements.
     * After every row, a custom callback function $handler gets called.
     *
     * Last revision 13 July 2001: Patch for limiting dump size from
     * vinay@sanisoft.com & girish@sanisoft.com
     *
     * @param mixed $db
     * @param mixed $table
     * @param mixed $add_query
     * @param mixed $handler
     * @param mixed $error_url
     *
     * @return  bool  always true
     *
     * @global  bool  whether to use backquotes to allow the use of special
     *                   characters in database, table and fields names or not
     * @global  int  the number of records
     * @global  int  the current record position
     *
     * @see     PMA_getTableContent()
     *
     * @author  staybyte
     */

    function PMA_getTableContentFast($db, $table, $add_query, $handler, $error_url)
    {
        global $use_backquotes;

        global $rows_cnt;

        global $current_row;

        $local_query = 'SELECT * FROM ' . PMA_backquote($db) . '.' . PMA_backquote($table) . $add_query;

        $result = $GLOBALS['xoopsDB']->queryF($local_query) or PMA_mysqlDie('', $local_query, '', $error_url);

        if (false !== $result) {
            $fields_cnt = mysqli_num_fields($result);

            $rows_cnt = $GLOBALS['xoopsDB']->getRowsNum($result);

            // Checks whether the field is an integer or not

            for ($j = 0; $j < $fields_cnt; $j++) {
                $field_set[$j] = PMA_backquote(mysql_field_name($result, $j), $use_backquotes);

                $type = mysql_field_type($result, $j);

                if ('tinyint' == $type || 'smallint' == $type || 'mediumint' == $type || 'int' == $type
                    || 'bigint' == $type
                    || 'timestamp' == $type) {
                    $field_num[$j] = true;
                } else {
                    $field_num[$j] = false;
                }
            } // end for

            // Sets the scheme

            if (isset($GLOBALS['showcolumns'])) {
                $fields = implode(', ', $field_set);

                $schema_insert = 'INSERT INTO ' . PMA_backquote(PMA_htmlFormat($table), $use_backquotes) . ' (' . PMA_htmlFormat($fields) . ') VALUES (';
            } else {
                $schema_insert = 'INSERT INTO ' . PMA_backquote(PMA_htmlFormat($table), $use_backquotes) . ' VALUES (';
            }

            $search = ["\x00", "\x0a", "\x0d", "\x1a"]; //\x08\\x09, not required

            $replace = ['\0', '\n', '\r', '\Z'];

            $current_row = 0;

            @set_time_limit($GLOBALS['cfgExecTimeLimit']);

            while (false !== ($row = $GLOBALS['xoopsDB']->fetchRow($result))) {
                $current_row++;

                for ($j = 0; $j < $fields_cnt; $j++) {
                    if (!isset($row[$j])) {
                        $values[] = 'NULL';
                    } elseif ('0' == $row[$j] || '' != $row[$j]) {
                        // a number

                        if ($field_num[$j]) {
                            $values[] = $row[$j];
                        } // a string

                        else {
                            $values[] = "'" . str_replace($search, $replace, PMA_sqlAddslashes($row[$j])) . "'";
                        }
                    } else {
                        $values[] = "''";
                    } // end if
                } // end for

                // Extended inserts case

                if (isset($GLOBALS['extended_ins'])) {
                    if (1 == $current_row) {
                        $insert_line = $schema_insert . implode(', ', $values) . ')';
                    } else {
                        $insert_line = '(' . implode(', ', $values) . ')';
                    }
                } // Other inserts case

                else {
                    $insert_line = $schema_insert . implode(', ', $values) . ')';
                }

                unset($values);

                // Call the handler

                $handler($insert_line);

                // loic1: send a fake header to bypass browser timeout if data

                //        are bufferized

                if (!empty($GLOBALS['ob_mode'])
                    || (isset($GLOBALS['zip']) || isset($GLOBALS['bzip']) || isset($GLOBALS['gzip']))) {
                    header('Expires: 0');
                }
            } // end while
        } // end if ($result !== false)
        $GLOBALS['xoopsDB']->freeRecordSet($result);

        return true;
    } // end of the 'PMA_getTableContentFast()' function

    /**
     * php < 4.0.5 only: get the content of $table as a series of INSERT
     * statements.
     * After every row, a custom callback function $handler gets called.
     *
     * Last revision 13 July 2001: Patch for limiting dump size from
     * vinay@sanisoft.com & girish@sanisoft.com
     *
     * @param mixed $db
     * @param mixed $table
     * @param mixed $add_query
     * @param mixed $handler
     * @param mixed $error_url
     *
     * @return  bool  always true
     *
     * @global  bool  whether to use backquotes to allow the use of special
     *                   characters in database, table and fields names or not
     * @global  int  the number of records
     * @global  int  the current record position
     *
     * @see     PMA_getTableContent()
     */

    function PMA_getTableContentOld($db, $table, $add_query, $handler, $error_url)
    {
        global $use_backquotes;

        global $rows_cnt;

        global $current_row;

        $local_query = 'SELECT * FROM ' . PMA_backquote($db) . '.' . PMA_backquote($table) . $add_query;

        $result = $GLOBALS['xoopsDB']->queryF($local_query) or PMA_mysqlDie('', $local_query, '', $error_url);

        $current_row = 0;

        $fields_cnt = mysqli_num_fields($result);

        $rows_cnt = $GLOBALS['xoopsDB']->getRowsNum($result);

        @set_time_limit($GLOBALS['cfgExecTimeLimit']); // HaRa

        while (false !== ($row = $GLOBALS['xoopsDB']->fetchRow($result))) {
            $current_row++;

            $table_list = '(';

            for ($j = 0; $j < $fields_cnt; $j++) {
                $table_list .= PMA_backquote(mysql_field_name($result, $j), $use_backquotes) . ', ';
            }

            $table_list = mb_substr($table_list, 0, -2);

            $table_list .= ')';

            if (isset($GLOBALS['extended_ins']) && $current_row > 1) {
                $schema_insert = '(';
            } else {
                if (isset($GLOBALS['showcolumns'])) {
                    $schema_insert = 'INSERT INTO ' . PMA_backquote(PMA_htmlFormat($table), $use_backquotes) . ' ' . PMA_htmlFormat($table_list) . ' VALUES (';
                } else {
                    $schema_insert = 'INSERT INTO ' . PMA_backquote(PMA_htmlFormat($table), $use_backquotes) . ' VALUES (';
                }

                $is_first_row = false;
            }

            for ($j = 0; $j < $fields_cnt; $j++) {
                if (!isset($row[$j])) {
                    $schema_insert .= ' NULL, ';
                } elseif ('0' == $row[$j] || '' != $row[$j]) {
                    $type = mysql_field_type($result, $j);

                    // a number

                    if ('tinyint' == $type || 'smallint' == $type || 'mediumint' == $type || 'int' == $type
                        || 'bigint' == $type
                        || 'timestamp' == $type) {
                        $schema_insert .= $row[$j] . ', ';
                    } // a string

                    else {
                        $dummy = '';

                        $srcstr = $row[$j];

                        for ($xx = 0, $xxMax = mb_strlen($srcstr); $xx < $xxMax; $xx++) {
                            $yy = mb_strlen($dummy);

                            if ('\\' == $srcstr[$xx]) {
                                $dummy .= '\\\\';
                            }

                            if ('\'' == $srcstr[$xx]) {
                                $dummy .= '\\\'';
                            }

                            //                            if ($srcstr[$xx] == '"')    $dummy .= '\\"';

                            if ("\x00" == $srcstr[$xx]) {
                                $dummy .= '\0';
                            }

                            if ("\x0a" == $srcstr[$xx]) {
                                $dummy .= '\n';
                            }

                            if ("\x0d" == $srcstr[$xx]) {
                                $dummy .= '\r';
                            }

                            //                            if ($srcstr[$xx] == "\x08") $dummy .= '\b';

                            //                            if ($srcstr[$xx] == "\t")   $dummy .= '\t';

                            if ("\x1a" == $srcstr[$xx]) {
                                $dummy .= '\Z';
                            }

                            if (mb_strlen($dummy) == $yy) {
                                $dummy .= $srcstr[$xx];
                            }
                        }

                        $schema_insert .= "'" . $dummy . "', ";
                    }
                } else {
                    $schema_insert .= "'', ";
                } // end if
            } // end for
            $schema_insert = preg_replace(', $', '', $schema_insert);

            $schema_insert .= ')';

            $handler(trim($schema_insert));

            // loic1: send a fake header to bypass browser timeout if data are

            //        bufferized

            if (!empty($GLOBALS['ob_mode'])
                && (isset($GLOBALS['zip']) || isset($GLOBALS['bzip']) || isset($GLOBALS['gzip']))) {
                header('Expires: 0');
            }
        } // end while

        $GLOBALS['xoopsDB']->freeRecordSet($result);

        return true;
    } // end of the 'PMA_getTableContentOld()' function

    /**
     * Dispatches between the versions of 'getTableContent' to use depending
     * on the php version
     *
     * Last revision 13 July 2001: Patch for limiting dump size from
     * vinay@sanisoft.com & girish@sanisoft.com
     *
     * @param mixed $db
     * @param mixed $table
     * @param mixed $limit_from
     * @param mixed $limit_to
     * @param mixed $handler
     * @param mixed $error_url
     *
     * @see     PMA_getTableContentFast(), PMA_getTableContentOld()
     *
     * @author  staybyte
     */

    function PMA_getTableContent($db, $table, $limit_from, $limit_to, $handler, $error_url)
    {
        // Defines the offsets to use

        if ($limit_from > 0) {
            $limit_from--;
        } else {
            $limit_from = 0;
        }

        if ($limit_to > 0 && $limit_from >= 0) {
            $add_query = " LIMIT $limit_from, $limit_to";
        } else {
            $add_query = '';
        }

        // Call the working function depending on the php version

        if (PMA_PHP_INT_VERSION >= 40005) {
            PMA_getTableContentFast($db, $table, $add_query, $handler, $error_url);
        } else {
            PMA_getTableContentOld($db, $table, $add_query, $handler, $error_url);
        }
    } // end of the 'PMA_getTableContent()' function

    /**
     * Outputs the content of a table in CSV format
     *
     * Last revision 14 July 2001: Patch for limiting dump size from
     * vinay@sanisoft.com & girish@sanisoft.com
     *
     * @param mixed $db
     * @param mixed $table
     * @param mixed $limit_from
     * @param mixed $limit_to
     * @param mixed $sep
     * @param mixed $enc_by
     * @param mixed $esc_by
     * @param mixed $handler
     * @param mixed $error_url
     *
     * @return  bool always true
     *
     * @global  string   whether to obtain an excel compatible csv format or a
     *                   simple csv one
     */

    function PMA_getTableCsv($db, $table, $limit_from, $limit_to, $sep, $enc_by, $esc_by, $handler, $error_url)
    {
        global $what;

        // Handles the "separator" and the optionnal "enclosed by" characters

        if ('excel' == $what) {
            $sep = ',';
        } elseif (!isset($sep)) {
            $sep = '';
        } else {
            if (function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc()) {
                $sep = stripslashes($sep);
            }

            $sep = str_replace('\\t', "\011", $sep);
        }

        if ('excel' == $what) {
            $enc_by = '"';
        } elseif (!isset($enc_by)) {
            $enc_by = '';
        } elseif (get_magic_quotes_gpc()) {
            $enc_by = stripslashes($enc_by);
        }

        if ('excel' == $what
            || (empty($esc_by) && '' != $enc_by)) {
            // double the "enclosed by" character

            $esc_by = $enc_by;
        } elseif (!isset($esc_by)) {
            $esc_by = '';
        } elseif (get_magic_quotes_gpc()) {
            $esc_by = stripslashes($esc_by);
        }

        // Defines the offsets to use

        if ($limit_from > 0) {
            $limit_from--;
        } else {
            $limit_from = 0;
        }

        if ($limit_to > 0 && $limit_from >= 0) {
            $add_query = " LIMIT $limit_from, $limit_to";
        } else {
            $add_query = '';
        }

        // Gets the data from the database

        $local_query = 'SELECT * FROM ' . PMA_backquote($db) . '.' . PMA_backquote($table) . $add_query;

        $result = $GLOBALS['xoopsDB']->queryF($local_query) or PMA_mysqlDie('', $local_query, '', $error_url);

        $fields_cnt = mysqli_num_fields($result);

        @set_time_limit($GLOBALS['cfgExecTimeLimit']);

        // Format the data

        $i = 0;

        while (false !== ($row = $GLOBALS['xoopsDB']->fetchRow($result))) {
            $schema_insert = '';

            for ($j = 0; $j < $fields_cnt; $j++) {
                if (!isset($row[$j])) {
                    $schema_insert .= 'NULL';
                } elseif ('0' == $row[$j] || '' != $row[$j]) {
                    // loic1 : always enclose fields

                    if ('excel' == $what) {
                        $row[$j] = preg_replace("\015(\012)?", "\012", $row[$j]);
                    }

                    if ('' == $enc_by) {
                        $schema_insert .= $row[$j];
                    } else {
                        $schema_insert .= $enc_by . str_replace($enc_by, $esc_by . $enc_by, $row[$j]) . $enc_by;
                    }
                } else {
                    $schema_insert .= '';
                }

                if ($j < $fields_cnt - 1) {
                    $schema_insert .= $sep;
                }
            } // end for

            $handler(trim($schema_insert));

            ++$i;

            // loic1: send a fake header to bypass browser timeout if data are

            //        bufferized

            if (!empty($GLOBALS['ob_mode'])
                && (isset($GLOBALS['zip']) || isset($GLOBALS['bzip']) || isset($GLOBALS['gzip']))) {
                header('Expires: 0');
            }
        } // end while

        $GLOBALS['xoopsDB']->freeRecordSet($result);

        return true;
    } // end of the 'PMA_getTableCsv()' function
} // $__PMA_BUILD_DUMP_LIB__
