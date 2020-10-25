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

include 'admin_header.php';
$xoopsDB = XoopsDatabaseFactory::getDatabaseConnection();
global $xoopsConfig, $xoopsDB, $xoopsModule;
xoops_cp_header();
echo "\n\n";
echo "<div align='center'><img src='../images/logo.png'></div><br><br>";
echo "\n\n";

echo '<center><font class="title"><b>'
     . _MA_DBTOOLS_OPTITLE
     . ' '
     . XOOPS_DB_NAME
     . '</b></font></center><br><br>'
     . "<table border=0 cellspacing=2 cellpadding=2 align=\"center\" bgcolor='#000000'><tr class='head'><td>"
     . _MA_DBTOOLS_TABLE
     . '</td><td>'
     . _MA_DBTOOLS_SIZE
     . '</td><td>'
     . _MA_DBTOOLS_STATUS
     . '</td><td>'
     . _MA_DBTOOLS_SPACE
     . '</td></tr>';
$db_clean = XOOPS_DB_NAME;
$tot_data = 0;
$tot_idx = 0;
$tot_all = 0;
$local_query = 'SHOW TABLE STATUS FROM ' . XOOPS_DB_NAME;
$result = @$GLOBALS['xoopsDB']->queryF($local_query);
if (@$GLOBALS['xoopsDB']->getRowsNum($result)) {
    while (false !== ($row = $GLOBALS['xoopsDB']->fetchBoth($result))) {
        $tot_data = $row['Data_length'];

        $tot_idx = $row['Index_length'];

        $total = $tot_data + $tot_idx;

        $total /= 1024;

        $total = round($total, 3);

        $gain = $row['Data_free'];

        $gain /= 1024;

        $total_gain += $gain;

        $gain = round($gain, 3);

        $local_query = 'OPTIMIZE TABLE ' . $row[0];

        $resultat = $GLOBALS['xoopsDB']->queryF($local_query);

        if (0 == $gain) {
            echo "<tr><td  bgcolor='#EEEEEE'>" . (string)$row[0] . "</td><td bgcolor='#DDDDDD'>" . (string)$total . ' Kb' . "</td><td bgcolor='#EEEEEE'>" . _MA_DBTOOLS_SOPTIMISED . "</td><td  bgcolor='#DDDDDD'>0 Kb</td></tr>";
        } else {
            echo "<tr><td bgcolor='#EEEEEE'><b>" . (string)$row[0] . "</b></td><td bgcolor='#DDDDDD'><b>" . (string)$total . ' Kb' . "</b></td><td bgcolor='#EEEEEE'><b>" . _MA_DBTOOLS_OPTIMISED . "</b></td><td  bgcolor='#DDDDDD'><b>" . (string)$gain . ' Kb</b></td></tr>';
        }
    }
}
echo '</table>';
echo '</center>';

echo '<br>';

$sql_query = 'INSERT INTO ' . $xoopsDB->prefix('optimise_gain') . " (gain) VALUES ('$total_gain')";
$result = @$GLOBALS['xoopsDB']->queryF($sql_query);

$sql_query = 'SELECT * FROM ' . $xoopsDB->prefix('optimise_gain');
$result = @$GLOBALS['xoopsDB']->queryF($sql_query);
while (false !== ($row = $GLOBALS['xoopsDB']->fetchRow($result))) {
    $histo += $row[0];

    $cpt += 1;
}
$total_gain = round($total_gain, 3);
echo "<center><table border=0 cellspacing=2 cellpadding=2 align=\"center\" bgcolor='#000000'><tr><td bgcolor='#EEEEEE'><b>" . _MA_DBTOOLS_RES . '</b><br><br>' . _MA_DBTOOLS_SAVED . (string)$total_gain . ' Kb<br>';

printf(_MA_DBTOOLS_NBREXE, $cpt);
echo '<br>' . $histo . ' Kb ' . _MA_DBTOOLS_TOTAL . '';
echo '<br><br><a href=mysql_dump.php>' . _MA_DBTOOLS_BACKUP . ' ' . XOOPS_DB_NAME . '</a></td></tr></table></center>';

xoops_cp_footer();
