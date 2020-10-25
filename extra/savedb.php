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
include 'mainfile.php';

$db = XOOPS_DB_NAME;
$host = XOOPS_DB_HOST;
$login = XOOPS_DB_USER;
$password = XOOPS_DB_PASS;
//autres variables
$time = time();
$date_jour = date('d-m-Y');
$filename = 'backup_' . $db . '_' . $date_jour . '_' . $time . '.sql';
$path = XOOPS_ROOT_PATH . '/cache/backup/';
$newname = 'backup_' . $db . '_' . $date_jour . '_' . $time;
$dir = $path . $filename;

backup(XOOPS_DB_HOST, XOOPS_DB_USER, XOOPS_DB_PASS, $path, $filename, XOOPS_DB_NAME);

function backup($host, $login, $password, $chemin, $nom_fichier, $db)
{
    function get_list_tables($db)
    {
        $i = 0;

        $nbtab = mysql_list_tables($db);

        while ($i < $GLOBALS['xoopsDB']->getRowsNum($nbtab)) {
            $tb_names[$i] = mysql_tablename($nbtab, $i);

            $i++;
        }

        return $tb_names;
    }

    function get_table_data($table, $fd)
    {
        $tableau = [];

        $j = 0;

        $resultat = $GLOBALS['xoopsDB']->queryF("select * from $table");

        if (false === $resultat) {
            printf(_MA_DBTOOLS_QUERRY_ERROR, $table);

            break;
        }

        while (false !== ($valeurs = $GLOBALS['xoopsDB']->fetchRow($resultat))) {
            ecrire_ligne($valeurs, $table, $fd);
        }

        return $valeurs;
    }

    function get_table_structure($struct, $fd)
    {
        $requete = $GLOBALS['xoopsDB']->queryF("show create table $struct");

        if (false === $requete) {
            printf(_MA_DBTOOLS_STRUCTUREERROR, $struct);

            break;
        }

        $structure = $GLOBALS['xoopsDB']->fetchRow($requete);

        $ligne = 0;

        return $structure[1];
    }

    function put_struct_into_file($structure, $nom_table, $fd)
    {
        $struct = "\n#\n#" . _MA_DBTOOLS_TABLE . ' ' . $nom_table . "\n#\n\n";

        $struct .= $structure;

        $struct .= ';';

        $struct .= "\n\n";

        $ecriture = fwrite($fd, $struct, mb_strlen($struct));

        if (0 == $ecriture) {
            return _MA_DBTOOLS_FWRITE_ERROR;
        }

        return $fd;
    }

    function put_data_into_file($donnees, $nom_table, $fd)
    {
        $lignes = 0;

        print($donnees[$lignes]);

        while (isset($donnees[$lignes])) {
            $appel_fonction = ecrire_ligne($donnees[$lignes], $nom_table, $fd);

            $lignes++;
        }

        return $fd;
    }

    function ecrire_ligne($donnees, $nom_table, $fd)
    {
        $case = 1;

        $debut = 'INSERT INTO `' . $nom_table . "` VALUES ('" . $donnees[0] . "'";

        fwrite($fd, $debut, mb_strlen($debut));

        while (isset($donnees[$case])) {
            fwrite($fd, ", '" . $donnees[$case] . "'", mb_strlen(", '" . $donnees[$case] . "'"));

            $case++;
        }

        $fin = ");\n";

        fwrite($fd, $fin, mb_strlen($fin));
    }

    include 'include/version.php';

    $emplacement = $chemin . '/' . $nom_fichier;

    if (!isset($chemin) || !is_dir($chemin)) {
        return $chemin . _MA_DBTOOLS_WRONGDIRECTORY;
    } elseif (file_exists($nom_fichier)) {
        printf(_MA_DBTOOLS_FILEEXIST, $nom_fichier);

        break;
    }

    $connec = mysql_connect($host, $login, $password);

    mysqli_select_db($GLOBALS['xoopsDB']->conn, $db, $connec);

    $list = get_list_tables($db);

    $tab = 0;

    $fd = fopen($emplacement, 'ab');

    $mysqlversion = $GLOBALS['xoopsDB']->getServerVersion();

    $mysqlserver = mysql_get_host_info();

    $dump_buffer .= '# DBTOOLS - Backup for ' . XOOPS_VERSION . "\n";

    $dump_buffer .= '# Version      : ' . $modversion['version'] . "\n";

    $dump_buffer .= '# Credits      : ' . $modversion['author'] . "\n";

    $dump_buffer .= '# Générated    : ' . date('d-m-Y H:i') . "\n";

    $dump_buffer .= '# PHP version  : ' . phpversion() . "\n";

    $dump_buffer .= '# Mysql        : ' . $GLOBALS['xoopsDB']->getServerVersion() . '  on ' . mysql_get_host_info() . "\n#\n\n";

    fwrite($fd, $dump_buffer, mb_strlen($dump_buffer));

    while (isset($list[$tab])) {
        $structure = get_table_structure($list[$tab], $fd);

        $backup = put_struct_into_file($structure, $list[$tab], $fd);

        $query = get_table_data($list[$tab], $fd);

        $backup_suite = put_data_into_file($query, $list[$tab], $fd);

        $tab++;
    }

    $end = " ########################\n";

    $end .= '####';

    $end .= 'fin du backup';

    $end .= "####\n";

    $end .= "########################\n";

    fwrite($fd, $end, mb_strlen($end));

    fclose($fd);

    $GLOBALS['xoopsDB']->close($connec);

    print 'Backup réalisé avec succès<br><br>';
}
