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
require __DIR__ . '/admin_header.php';

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

xoops_cp_header();

echo "<h3 align='center'>" . _MA_DBTOOLS_SQLDUMT . ' : ' . XOOPS_DB_NAME . '</h3>';
echo "<table align='center'><tr class='odd'><td>
<script src='phpmyadmin/functions.js' type='text/javascript' language='javascript'></script>
       <form method='post' action='phpmyadmin/tbl_dump.php' name='db_dump'>
        <table>
        <tr>
            <td>
                <select name='table_select[]' size='5' multiple='multiple'>";

$_tables = get_list_tables(XOOPS_DB_NAME);

sort($_tables);
while (list($key, $val) = each($_tables)) {
    echo "<option value='$val' selected>$val</option>";
}

echo "</select>
            </td>

            <td valign='middle'>
                <input type='radio' name='what' value='structure'>" . _MA_DBTOOLS_SQLSTRUC . "   <br>
                <input type='radio' name='what' value='data' checked> " . _MA_DBTOOLS_SQLSTDAT . "<br>
                <input type='radio' name='what' value='dataonly'>" . _MA_DBTOOLS_SQLDATA . "
            </td>
        </tr>
        <tr>
            <td colspan='2'>
                <input type='checkbox' name='drop' value='1' checked>" . _MA_DBTOOLS_DROP . "</td>
        </tr>
        <tr>
            <td colspan='2'>
                <input type='checkbox' name='showcolumns' value='yes' checked>" . _MA_DBTOOLS_SQLCOMP . "
            </td>
        </tr>
        <tr>
            <td colspan='2'>
                <input type='checkbox' name='extended_ins' value='yes'>" . _MA_DBTOOLS_SQLEXT . "
            </td>
        </tr>
            <tr>
            <td colspan='2'>
                <input type='checkbox' name='use_backquotes' value='1' checked>" . _MA_DBTOOLS_SQLBACKQ . "
            </td>
        </tr>
        <tr>
            <td colspan='2'>
                <input type='checkbox' name='asfile' value='sendit' onclick='return checkTransmitDump(this.form, 'transmit')' checked>" . _MA_DBTOOLS_SQLSAF . "


                (
                <input type='checkbox' name='zip' value='zip' onclick='return checkTransmitDump(this.form, 'zip')'>" . _MA_DBTOOLS_SQLZIP . "&nbsp;

                <input type='checkbox' name='gzip' value='gzip' onclick='return checkTransmitDump(this.form, 'gzip')'>" . _MA_DBTOOLS_SQLGZ . "

                )
            </td>
        </tr>
        <tr>
            <td colspan='2'>
                <input type='submit' value='" . _GO . "'>
            </td>
        </tr>
        </table>
        <input type='hidden' name='server' value='1'>
        <input type='hidden' name='lang' value='en'>
        <input type='hidden' name='db' value='" . XOOPS_DB_NAME . "'>
        </form><div align='center'>powered by <a href='http://phpwizard.net/projects/phpMyAdmin' target='_blank' title='powered by phpMyAdmin'>phpMyAdmin</a></div>
</td></tr></table>";

xoops_cp_footer();
