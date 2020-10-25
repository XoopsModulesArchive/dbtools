<?php

#Application name: PhpCollab
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

if (!defined('PMA_BOOKMARK_LIB_INCLUDED')) {
    define('PMA_BOOKMARK_LIB_INCLUDED', 1);

    /**
     * Defines the bookmark parameters for the current user
     *
     * @return string the bookmark parameters for the current user
     *
     * @global  array    the list of settings for the current server
     * @global  int  the id of the current server
     */

    function PMA_getBookmarksParam()
    {
        global $cfgServer;

        global $server;

        $cfgBookmark = '';

        // No server selected -> no bookmark table

        if (0 == $server) {
            return '';
        }

        $cfgBookmark['user'] = $cfgServer['user'];

        $cfgBookmark['db'] = $cfgServer['bookmarkdb'];

        $cfgBookmark['table'] = $cfgServer['bookmarktable'];

        return $cfgBookmark;
    } // end of the 'PMA_getBookmarksParam()' function

    /**
     * Gets the list of bookmarks defined for the current database
     *
     * @param mixed $db
     * @param mixed $cfgBookmark
     *
     * @return  mixed    the bookmarks list if defined, false else
     */

    function PMA_listBookmarks($db, $cfgBookmark)
    {
        $query = 'SELECT label, id FROM ' . PMA_backquote($cfgBookmark['db']) . '.' . PMA_backquote($cfgBookmark['table']) . ' WHERE dbase = \'' . PMA_sqlAddslashes($db) . '\'' . ' AND user = \'' . PMA_sqlAddslashes($cfgBookmark['user']) . '\'';

        if (isset($GLOBALS['dbh'])) {
            $result = $GLOBALS['xoopsDB']->queryF($query, $GLOBALS['dbh']);
        } else {
            $result = $GLOBALS['xoopsDB']->queryF($query);
        }

        // There is some bookmarks -> store them

        if ($result > 0 && $GLOBALS['xoopsDB']->getRowsNum($result) > 0) {
            $flag = 1;

            while (false !== ($row = $GLOBALS['xoopsDB']->fetchRow($result))) {
                $bookmark_list[$flag . ' - ' . $row[0]] = $row[1];

                $flag++;
            } // end while

            return $bookmark_list;
        } // No bookmarks for the current database

        return false;
    } // end of the 'PMA_listBookmarks()' function

    /**
     * Gets the sql command from a bookmark
     *
     * @param mixed $db
     * @param mixed $cfgBookmark
     * @param mixed $id
     *
     * @return  string   the sql query
     */

    function PMA_queryBookmarks($db, $cfgBookmark, $id)
    {
        $query = 'SELECT query FROM ' . PMA_backquote($cfgBookmark['db']) . '.' . PMA_backquote($cfgBookmark['table']) . ' WHERE dbase = \'' . PMA_sqlAddslashes($db) . '\'' . ' AND user = \'' . PMA_sqlAddslashes($cfgBookmark['user']) . '\'' . ' AND id = ' . $id;

        if (isset($GLOBALS['dbh'])) {
            $result = $GLOBALS['xoopsDB']->queryF($query, $GLOBALS['dbh']);
        } else {
            $result = $GLOBALS['xoopsDB']->queryF($query);
        }

        $bookmark_query = mysql_result($result, 0, 'query');

        return $bookmark_query;
    } // end of the 'PMA_queryBookmarks()' function

    /**
     * Adds a bookmark
     *
     * @param mixed $fields
     * @param mixed $cfgBookmark
     */

    function PMA_addBookmarks($fields, $cfgBookmark)
    {
        $query = 'INSERT INTO ' . PMA_backquote($cfgBookmark['db']) . '.' . PMA_backquote($cfgBookmark['table']) . ' (id, dbase, user, query, label) VALUES (\'\', \'' . PMA_sqlAddslashes($fields['dbase']) . '\', \'' . PMA_sqlAddslashes($fields['user']) . '\', \'' . PMA_sqlAddslashes(
            urldecode($fields['query'])
        ) . '\', \'' . PMA_sqlAddslashes($fields['label']) . '\')';

        if (isset($GLOBALS['dbh'])) {
            $result = $GLOBALS['xoopsDB']->queryF($query, $GLOBALS['dbh']);
        } else {
            $result = $GLOBALS['xoopsDB']->queryF($query);
        }
    } // end of the 'PMA_addBookmarks()' function

    /**
     * Deletes a bookmark
     *
     * @param mixed $db
     * @param mixed $cfgBookmark
     * @param mixed $id
     */

    function PMA_deleteBookmarks($db, $cfgBookmark, $id)
    {
        $query = 'DELETE FROM ' . PMA_backquote($cfgBookmark['db']) . '.' . PMA_backquote($cfgBookmark['table']) . ' WHERE user = \'' . PMA_sqlAddslashes($cfgBookmark['user']) . '\'' . ' AND id = ' . $id;

        if (isset($GLOBALS['dbh'])) {
            $result = $GLOBALS['xoopsDB']->queryF($query, $GLOBALS['dbh']);
        } else {
            $result = $GLOBALS['xoopsDB']->queryF($query);
        }
    } // end of the 'PMA_deleteBookmarks()' function

    /**
     * Bookmark Support
     */

    $cfgBookmark = PMA_getBookmarksParam();
} // $__PMA_BOOKMARK_LIB__
