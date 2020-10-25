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
// ------------------------------------------------------------------------- // */

include '../../../../mainfile.php';

/**
 * phpMyAdmin Configuration File
 *
 * All directives are explained in Documentation.html
 */

/**
 * Bookmark Table Structure
 *
 * CREATE TABLE bookmark (
 *  id int(11) DEFAULT '0' NOT NULL auto_increment,
 *  dbase varchar(255) NOT NULL,
 *  user varchar(255) NOT NULL,
 *  label varchar(255) NOT NULL,
 *  query text NOT NULL,
 *  PRIMARY KEY (id)
 * );
 */

/**
 * Your phpMyAdmin url
 *
 * Complete the variable below with the full url ie
 *    http://www.your_web.net/path_to_your_phpMyAdmin_directory/
 */
$cfgPmaAbsoluteUri = '';

/**
 * Server(s) configuration
 */
// The $cfgServers array starts with $cfgServers[1].  Do not use $cfgServers[0].
// You can disable a server config entry by setting host to ''.
$cfgServers[1]['host'] = XOOPS_DB_HOST; // MySQL hostname
$cfgServers[1]['port'] = '';          // MySQL port - leave blank for default port
$cfgServers[1]['socket'] = '';          // Path to the socket - leave blank for default socket
$cfgServers[1]['connect_type'] = 'tcp';       // How to connect to MySQL server ('tcp' or 'socket')
$cfgServers[1]['stduser'] = '';          // MySQL standard user settings (this user must have read-only
$cfgServers[1]['stdpass'] = '';          //   access to the "mysql/user" and "mysql/db" tables)
$cfgServers[1]['adv_auth'] = false;       // Use advanced authentication?
$cfgServers[1]['user'] = XOOPS_DB_USER;      // MySQL user
$cfgServers[1]['password'] = XOOPS_DB_PASS;          // MySQL password (only needed with basic auth)
$cfgServers[1]['only_db'] = XOOPS_DB_NAME;          // If set to a db-name, only this db is displayed at left frame
// It may also be an array of db-names
$cfgServers[1]['verbose'] = '';          // Verbose name for this host - leave blank to show the hostname
$cfgServers[1]['bookmarkdb'] = '';          // Bookmark db - leave blank for no bookmark support
$cfgServers[1]['bookmarktable'] = '';          // Bookmark table - leave blank for no bookmark support

// If you have more than one server configured, you can set $cfgServerDefault
// to any one of them to autoconnect to that server when phpMyAdmin is started,
// or set it to 0 to be given a list of servers without logging in
// If you have only one server configured, $cfgServerDefault *MUST* be
// set to that server.
$cfgServerDefault = 1;                         // Default server (0 = no default server)
$cfgServer = '';
unset($cfgServers[0]);

/**
 * Other core phpMyAdmin settings
 */
$cfgOBGzip = true;   // use GZIP output buffering if possible
$cfgPersistentConnections = false;  // use persistent connections to MySQL database
$cfgExecTimeLimit = 300;    // maximum execution time in seconds (0 for no limit)
$cfgSkipLockedTables = false;  // mark used tables, make possible to show
// locked tables (since MySQL 3.23.30)
$cfgShowSQL = true;   // show SQL queries as run
$cfgAllowUserDropDatabase = false;  // show a 'Drop database' link to normal users
$cfgConfirm = true;   // confirm 'DROP TABLE' & 'DROP DATABASE'

$cfgShowStats = true;   // allow to display statistics and space usage in
// the pages about database details and table
// properties
$cfgShowTooltip = true;   // display table comment as tooltip in left frame

// In the main frame, at startup...
$cfgShowMysqlInfo = false;  // whether to display the "MySQL runtime
$cfgShowMysqlVars = false;  // information", "MySQL system variables" and "PHP
$cfgShowPhpInfo = false;  // information" links for simple users or not

// In browse mode...
$cfgShowBlob = false;  // display blob field contents
$cfgNavigationBarIconic = true;   // do not display text inside navigation bar buttons
$cfgShowAll = false;  // allows to display all the rows
$cfgMaxRows = 30;     // maximum number of rows to display
$cfgOrder = 'ASC';  // default for 'ORDER BY' clause (valid
// values are 'ASC', 'DESC' or 'SMART' -ie
// descending order for fields of type
// TIME, DATE, DATETIME & TIMESTAMP,
// ascending order else-)

// In edit mode...
$cfgProtectBinary = 'blob'; // disallow editing of binary fields
// valid values are:
//   FALSE  allow editing
//   'blob' allow editing except for BLOB fields
//   'all'  disallow editing

$cfgZipDump = true;   // Allow the use of zip/gzip/bzip
$cfgGZipDump = true;   // compression for
$cfgBZipDump = true;   // dump files

/**
 * Link to the official MySQL documentation
 * Be sure to include no trailing slash on the path
 */
$cfgManualBase = 'http://www.mysql.com/documentation/mysql/bychapter';

/**
 * Language settings
 */
// Default language to use, if not browser-defined or user-defined
$cfgDefaultLang = 'en';
// Force: always use this language - must be defined in
//        libraries/select_lang.lib.php
// $cfgLang     = 'en';
// Loads language file
require __DIR__ . '/select_lang.lib.php';

/**
 * Customization & design
 */
$cfgLeftBgColor = '#D0DCE0';    // background color for the left frame
$cfgRightBgColor = '#F5F5F5';    // background color for the right frame
$cfgBorder = 0;            // border width on tables
$cfgThBgcolor = '#D3DCE3';    // table header row colour
$cfgBgcolorOne = '#CCCCCC';    // table data row colour
$cfgBgcolorTwo = '#DDDDDD';    // table data row colour, alternate
$cfgPointerColor = '#CCFFCC';    // color of the pointer in browse mode
$cfgTextareaCols = 40;           // textarea size (columns) in edit mode
$cfgTextareaRows = 7;            // textarea size (rows) in edit mode
$cfgLimitChars = 50;           // max field data length in browse mode
$cfgModifyDeleteAtLeft = true;         // show edit/delete links on left side of browse
$cfgModifyDeleteAtRight = false;        // show edit/delete links on right side of browse
$cfgLeftWidth = 150;          // left frame width

/**
 * MySQL settings
 */
// Column types
$cfgColumnTypes = [
    'TINYINT',
    'SMALLINT',
    'MEDIUMINT',
    'INT',
    'BIGINT',
    'FLOAT',
    'DOUBLE',
    'DECIMAL',
    'DATE',
    'DATETIME',
    'TIMESTAMP',
    'TIME',
    'YEAR',
    'CHAR',
    'VARCHAR',
    'TINYBLOB',
    'TINYTEXT',
    'TEXT',
    'BLOB',
    'MEDIUMBLOB',
    'MEDIUMTEXT',
    'LONGBLOB',
    'LONGTEXT',
    'ENUM',
    'SET',
];

// Atributes
$cfgAttributeTypes = [
    '',
    'BINARY',
    'UNSIGNED',
    'UNSIGNED ZEROFILL',
];

// Available functions
$cfgFunctions = [
    'ASCII',
    'CHAR',
    'SOUNDEX',
    'LCASE',
    'UCASE',
    'NOW',
    'PASSWORD',
    'MD5',
    'ENCRYPT',
    'RAND',
    'LAST_INSERT_ID',
    'COUNT',
    'AVG',
    'SUM',
    'CURDATE',
    'CURTIME',
    'FROM_DAYS',
    'FROM_UNIXTIME',
    'PERIOD_ADD',
    'PERIOD_DIFF',
    'TO_DAYS',
    'UNIX_TIMESTAMP',
    'USER',
    'WEEKDAY',
];

/**
 * Unset magic_quotes_runtime - do not change!
 */
set_magic_quotes_runtime(0);
