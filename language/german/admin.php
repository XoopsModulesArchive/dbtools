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
define('_MA_DBTOOLS_NOTABLE', 'In der Datenbank liegen keine Tabellen vor.');
define('_MA_DBTOOLS_HOST', 'Host');
define('_MA_DBTOOLS_DB', 'Datenbank');
define('_MA_DBTOOLS_STR', 'Struktur der Tabelle');
define('_MA_DBTOOLS_DUMP', 'Datensicherung der Tabelle');
define('_MA_DBTOOLS_ERROR', 'Fehler');
define('_MA_DBTOOLS_QUERY', 'SQL-Abfrage');
define('_MA_DBTOOLS_MYSAID', 'MySQL meldet:');
define('_MA_DBTOOLS_BACK', 'Zur&uuml;ck');
define('_MA_DBTOOLS_FILENAME', 'Datenbank sichern');
define('_MA_DBTOOLS_NAME', 'Datenbank gesichert');
define('_MA_DBTOOLS_DONE', 'Die');
define('_MA_DBTOOLS_AT', 'am');
define('_MA_DBTOOLS_DATE', date('m-d-Y'));
define('_MA_DBTOOLS_PATH', 'Die Datei wurde ins Verzeichnis<b> %s </b>gesichert.<br><br>');
define('_MA_DBTOOLS_BACKUPNAME', 'Dateiname: <b> %s </b><br><br>');
define('_MA_DBTOOLS_DOWNLOAD', 'Download');
define('_MA_DBTOOLS_WRONGDIRECTORY', 'ist kein Verzeichnis oder ist nicht vorhanden.');
define('_MA_DBTOOLS_FWRITE_ERROR', 'Fehler beim Schreiben der Datenbank-Strukturinformationen.');
define('_MA_DBTOOLS_BACKUPOK', '<b>Backup wurde erfolgreich ausgef&uuml;hrt.</b>');
define('_MA_DBTOOLS_QUERRY_ERROR', 'Die Abfrage der Tabelle %s ist fehlgeschlagen.<br>');
define('_MA_DBTOOLS_ENDBACKUP', '   BACKUP BEENDEN   ');
define('_MA_DBTOOLS_FILEEXIST', 'Backup nicht m&ouml;glich, da die Datei %s bereits existiert.');
define('_MA_DBTOOLS_STRUCTUREERROR', 'Es ist nicht m&ouml;glich die Struktur der Tabelle %s zu bekommen.<br> ');

define('_MA_DBTOOLS_TABLE', 'Tabelle');
define('_MA_DBTOOLS_SIZE', 'Gr&ouml;&szlig;e');
define('_MA_DBTOOLS_STATUS', 'Status');
define('_MA_DBTOOLS_SPACE', 'Freigegebener Speicherplatz');
define('_MA_DBTOOLS_OPTITLE', 'Optimiere die Datenbank ');
define('_MA_DBTOOLS_RES', 'Ergebnisse');
define('_MA_DBTOOLS_SAVED', 'Insgesamt freigegebener Speicherplatz: ');
define('_MA_DBTOOLS_TOTAL', 'gesichert seit der ersten Ausf&uuml;hrung.');
define('_MA_DBTOOLS_NBREXE', 'Optimierung  %s mal');
define('_MA_DBTOOLS_SOPTIMISED', 'Bereits optimiert');
define('_MA_DBTOOLS_OPTIMISED', 'Optimiert');
define('_MA_DBTOOLS_BACKUP', 'Backup der Datenbank');

//mysql_dump.php
define('_MA_DBTOOLS_SQLDUMT', 'Backup Datenbank');
define('_MA_DBTOOLS_SQLSTRUC', 'Nur Struktur');
define('_MA_DBTOOLS_SQLSTDAT', 'Struktur und Daten');
define('_MA_DBTOOLS_SQLDATA', 'Nur Daten');
define('_MA_DBTOOLS_DROP', "'drop table' hinzuf&uuml;gen");
define('_MA_DBTOOLS_SQLCOMP', 'Komplette inserts  ');
define('_MA_DBTOOLS_SQLEXT', 'Erweiterte inserts');
define('_MA_DBTOOLS_SQLBACKQ', 'backquotes bei Tabellen und Feldnamen benutzen');
define('_MA_DBTOOLS_SQLSAF', 'Sichern als Datei  ');
define('_MA_DBTOOLS_SQLZIP', 'zipped');
define('_MA_DBTOOLS_SQLGZ', 'gzipped');
define('_MA_DBTOOLS_SQLFILELOC', 'Ablageort der SQL-Datei&nbsp;');
