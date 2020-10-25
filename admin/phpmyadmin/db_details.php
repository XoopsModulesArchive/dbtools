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
// ------------------------------------------------------------------------- //d: db_details.php,v 1.129 2002/02/16 16:48:29 lem9 Exp $ */

/**
 * Gets some core libraries
 */
require __DIR__ . '/grab_globals.lib.php';
require __DIR__ . '/common.lib.php';
require __DIR__ . '/bookmark.lib.php';

/**
 * Defines the urls to return to in case of error in a sql statement
 */
$err_url_0 = 'main.php' . '?lang=' . $lang . '&amp;server=' . $server;
$err_url = 'db_details.php' . '?lang=' . $lang . '&amp;server=' . $server . '&amp;db=' . urlencode($db);

/**
 * Ensures the database exists (else move to the "parent" script) and displays
 * headers
 */
if (!isset($is_db) || !$is_db) {
    // Not a valid db name -> back to the welcome page

    if (!empty($db)) {
        $is_db = @mysqli_select_db($GLOBALS['xoopsDB']->conn, $db);
    }

    if (empty($db) || !$is_db) {
        header('Location: ' . $cfgPmaAbsoluteUri . 'main.php?lang=' . $lang . '&server=' . $server . (isset($message) ? '&message=' . urlencode($message) : '') . '&reload=1');

        exit();
    }
} // end if (ensures db exists)

// Displays headers
if (!isset($message)) {
    $js_to_run = 'functions.js';

    require __DIR__ . '/header.inc.php';

    // Reloads the navigation frame via JavaScript if required

    if (isset($reload) && $reload) {
        echo "\n"; ?>
        <script type="text/javascript" language="javascript1.2">
            <!--
            window.parent.frames['nav'].location.replace('./left.php?lang=<?php echo $lang; ?>&server=<?php echo $server; ?>&db=<?php echo urlencode($db); ?>');
            //-->
        </script>
        <?php
    }

    echo "\n";
} else {
    PMA_showMessage($message);
}

/**
 * Drop/delete multiple tables if required
 */
if ((!empty($submit_mult) && isset($selected_tbl))
    || isset($mult_btn)) {
    $action = 'db_details.php';

    require __DIR__ . '/mult_submits.inc.php';
}

/**
 * Gets the list of the table in the current db and informations about these
 * tables if possible
 */
// staybyte: speedup view on locked tables - 11 June 2001
if (PMA_MYSQL_INT_VERSION >= 32303) {
    // Special speedup for newer MySQL Versions (in 4.0 format changed)

    if (true === $cfgSkipLockedTables && PMA_MYSQL_INT_VERSION >= 32330) {
        $local_query = 'SHOW OPEN TABLES FROM ' . PMA_backquote($db);

        $result = $GLOBALS['xoopsDB']->queryF($local_query) or PMA_mysqlDie('', $local_query, '', $err_url_0);

        // Blending out tables in use

        if (false !== $result && $GLOBALS['xoopsDB']->getRowsNum($result) > 0) {
            while (false !== ($tmp = $GLOBALS['xoopsDB']->fetchRow($result))) {
                // if in use memorize tablename

                if (eregi('in_use=[1-9]+', $tmp[1])) {
                    $sot_cache[$tmp[0]] = true;
                }
            }

            $GLOBALS['xoopsDB']->freeRecordSet($result);

            if (isset($sot_cache)) {
                $local_query = 'SHOW TABLES FROM ' . PMA_backquote($db);

                $result = $GLOBALS['xoopsDB']->queryF($local_query) or PMA_mysqlDie('', $local_query, '', $err_url_0);

                if (false !== $result && $GLOBALS['xoopsDB']->getRowsNum($result) > 0) {
                    while (false !== ($tmp = $GLOBALS['xoopsDB']->fetchRow($result))) {
                        if (!isset($sot_cache[$tmp[0]])) {
                            $local_query = 'SHOW TABLE STATUS FROM ' . PMA_backquote($db) . ' LIKE \'' . addslashes($tmp[0]) . '\'';

                            $sts_result = $GLOBALS['xoopsDB']->queryF($local_query) or PMA_mysqlDie('', $local_query, '', $err_url_0);

                            $sts_tmp = $GLOBALS['xoopsDB']->fetchBoth($sts_result);

                            $tables[] = $sts_tmp;
                        } else { // table in use
                            $tables[] = ['Name' => $tmp[0]];
                        }
                    }

                    $GLOBALS['xoopsDB']->freeRecordSet($result);

                    $sot_ready = true;
                }
            }
        }
    }

    if (!isset($sot_ready)) {
        $local_query = 'SHOW TABLE STATUS FROM ' . PMA_backquote($db);

        $result = $GLOBALS['xoopsDB']->queryF($local_query) or PMA_mysqlDie('', $local_query, '', $err_url_0);

        if (false !== $result && $GLOBALS['xoopsDB']->getRowsNum($result) > 0) {
            while (false !== ($sts_tmp = $GLOBALS['xoopsDB']->fetchBoth($result))) {
                $tables[] = $sts_tmp;
            }

            $GLOBALS['xoopsDB']->freeRecordSet($result);
        }
    }

    $num_tables = (isset($tables) ? count($tables) : 0);
} // end if (PMA_MYSQL_INT_VERSION >= 32303)
else {
    $result = mysql_list_tables($db);

    $num_tables = @mysql_numrows($result);

    for ($i = 0; $i < $num_tables; $i++) {
        $tables[] = mysql_tablename($result, $i);
    }

    $GLOBALS['xoopsDB']->freeRecordSet($result);
}

/**
 * Displays an html table with all the tables contained into the current
 * database
 */
?>

    <!-- TABLE LIST -->

<?php
// 1. No tables
if (0 == $num_tables) {
    echo $strNoTablesFound . "\n";
} // 2. Shows table informations on mysql >= 3.23 - staybyte - 11 June 2001
elseif (PMA_MYSQL_INT_VERSION >= 32300) {
    ?>
    <form method="post" action="db_details.php" name="tablesForm">
        <input type="hidden" name="lang" value="<?php echo $lang; ?>">
        <input type="hidden" name="server" value="<?php echo $server; ?>">
        <input type="hidden" name="db" value="<?php echo $db; ?>">

        <table border="<?php echo $cfgBorder; ?>">
            <tr>
                <td></td>
                <th>&nbsp;<?php echo ucfirst($strTable); ?>&nbsp;</th>
                <th colspan="6"><?php echo ucfirst($strAction); ?></th>
                <th><?php echo ucfirst($strRecords); ?></th>
                <th><?php echo ucfirst($strType); ?></th>
                <?php
                if ($cfgShowStats) {
                    echo '<th>' . ucfirst($strSize) . '</th>';
                }

    echo "\n"; ?>
            </tr>
            <?php
            $i = $sum_entries = $sum_size = 0;

    $checked = (!empty($checkall) ? ' checked' : '');

    while (list($keyname, $sts_data) = each($tables)) {
        $table = $sts_data['Name'];

        // Sets parameters for links

        $url_query = 'lang=' . $lang . '&amp;server=' . $server . '&amp;db=' . urlencode($db) . '&amp;table=' . urlencode($table) . '&amp;goto=db_details.php';

        $bgcolor = ($i++ % 2) ? $cfgBgcolorOne : $cfgBgcolorTwo;

        echo "\n"; ?>
                <tr>
                    <td align="center" bgcolor="<?php echo $bgcolor; ?>">
                        <input type="checkbox" name="selected_tbl[]" value="<?php echo urlencode($table); ?>"<?php echo $checked; ?>>
                    </td>
                    <td bgcolor="<?php echo $bgcolor; ?>" nowrap="nowrap">
                        &nbsp;<b><?php echo htmlspecialchars($table, ENT_QUOTES | ENT_HTML5); ?>&nbsp;</b>&nbsp;
                    </td>
                    <td bgcolor="<?php echo $bgcolor; ?>">
                        <?php
                        if ($sts_data['Rows'] > 0) {
                            echo '<a href="sql.php?' . $url_query . '&amp;sql_query=' . urlencode('SELECT * FROM ' . PMA_backquote($table)) . '&amp;pos=0">' . $strBrowse . '</a>';
                        } else {
                            echo $strBrowse;
                        } ?>
                    </td>
                    <td bgcolor="<?php echo $bgcolor; ?>">
                        <?php
                        if ($sts_data['Rows'] > 0) {
                            echo '<a href="tbl_select.php?' . $url_query . '">' . $strSelect . '</a>';
                        } else {
                            echo $strSelect;
                        } ?>
                    </td>
                    <td bgcolor="<?php echo $bgcolor; ?>">
                        <a href="tbl_change.php?<?php echo $url_query; ?>">
                            <?php echo $strInsert; ?></a>
                    </td>
                    <td bgcolor="<?php echo $bgcolor; ?>">
                        <a href="tbl_properties.php?<?php echo $url_query; ?>">
                            <?php echo $strProperties; ?></a>
                    </td>
                    <td bgcolor="<?php echo $bgcolor; ?>">
                        <a href="sql.php?<?php echo $url_query; ?>&amp;reload=1&amp;sql_query=<?php echo urlencode('DROP TABLE ' . PMA_backquote($table)); ?>&amp;zero_rows=<?php echo urlencode(sprintf($strTableHasBeenDropped, htmlspecialchars($table, ENT_QUOTES | ENT_HTML5))); ?>"
                           onclick="return confirmLink(this, 'DROP TABLE <?php echo PMA_jsFormat($table); ?>')">
                            <?php echo $strDrop; ?></a>
                    </td>
                    <td bgcolor="<?php echo $bgcolor; ?>">
                        <?php
                        if ($sts_data['Rows'] > 0) {
                            echo '<a href="sql.php?' . $url_query . '&amp;sql_query=' . urlencode('DELETE FROM ' . PMA_backquote($table)) . '&amp;zero_rows=' . urlencode(
                                sprintf(
                                        $strTableHasBeenEmptied,
                                        htmlspecialchars($table, ENT_QUOTES | ENT_HTML5)
                                    )
                            ) . '" onclick="return confirmLink(this, \'DELETE FROM ' . PMA_jsFormat($table) . '\')">' . $strEmpty . '</a>';
                        } else {
                            echo $strEmpty;
                        } ?>
                    </td>
                    <?php
                    echo "\n";

        $mergetable = false;

        $nonisam = false;

        if (isset($sts_data['Type'])) {
            if ('MRG_MyISAM' == $sts_data['Type']) {
                $mergetable = true;
            } elseif (!eregi('ISAM|HEAP', $sts_data['Type'])) {
                $nonisam = true;
            }
        }

        if (isset($sts_data['Rows'])) {
            if (false === $mergetable) {
                if ($cfgShowStats && false === $nonisam) {
                    $tblsize = $sts_data['Data_length'] + $sts_data['Index_length'];

                    $sum_size += $tblsize;

                    if ($tblsize > 0) {
                        [$formated_size, $unit] = PMA_formatByteDown($tblsize, 3, 1);
                    } else {
                        [$formated_size, $unit] = PMA_formatByteDown($tblsize, 3, 0);
                    }
                } elseif ($cfgShowStats) {
                    $formated_size = '&nbsp;-&nbsp;';

                    $unit = '';
                }

                $sum_entries += $sts_data['Rows'];
            } // MyISAM MERGE Table

            elseif ($cfgShowStats && true === $mergetable) {
                $formated_size = '&nbsp;-&nbsp;';

                $unit = '';
            } elseif ($cfgShowStats) {
                $formated_size = 'unknown';

                $unit = '';
            } ?>
                        <td align="right" bgcolor="<?php echo $bgcolor; ?>">
                            <?php
                            echo "\n" . '        ';

            if (true === $mergetable) {
                echo '<i>' . number_format($sts_data['Rows'], 0, $number_decimal_separator, $number_thousands_separator) . '</i>' . "\n";
            } else {
                echo number_format($sts_data['Rows'], 0, $number_decimal_separator, $number_thousands_separator) . "\n";
            } ?>
                        </td>
                        <td bgcolor="<?php echo $bgcolor; ?>" nowrap="nowrap">
                            &nbsp;<?php echo($sts_data['Type'] ?? '&nbsp;'); ?>&nbsp;
                        </td>
                        <?php
                        if ($cfgShowStats) {
                            echo "\n"; ?>
                            <td align="right" bgcolor="<?php echo $bgcolor; ?>" nowrap="nowrap">
                                &nbsp;&nbsp;
                                <a href="tbl_properties.php?<?php echo $url_query; ?>#showusage"><?php echo $formated_size . ' ' . $unit; ?></a>
                            </td>
                            <?php
                            echo "\n";
                        } // end if
        } else {
            ?>
                        <td colspan="3" align="center" bgcolor="<?php echo $bgcolor; ?>">
                            <?php echo $strInUse . "\n"; ?>
                        </td>
                        <?php
        }

        echo "\n"; ?>
                </tr>
                <?php
    }

    // Show Summary

    if ($cfgShowStats) {
        [$sum_formated, $unit] = PMA_formatByteDown($sum_size, 3, 1);
    }

    echo "\n"; ?>
            <tr>
                <td></td>
                <th align="center" nowrap="nowrap">
                    &nbsp;<b><?php echo sprintf($strTables, number_format($num_tables, 0, $number_decimal_separator, $number_thousands_separator)); ?></b>&nbsp;
                </th>
                <th colspan="6" align="center">
                    <b><?php echo $strSum; ?></b>
                </th>
                <th align="right" nowrap="nowrap">
                    <b><?php echo number_format($sum_entries, 0, $number_decimal_separator, $number_thousands_separator); ?></b>
                </th>
                <th align="center">
                    <b>--</b>
                </th>
                <?php
                if ($cfgShowStats) {
                    echo "\n"; ?>
                    <th align="right" nowrap="nowrap">
                        &nbsp;
                        <b><?php echo $sum_formated . ' ' . $unit; ?></b>
                    </th>
                    <?php
                }

    echo "\n"; ?>
            </tr>

            <?php
            // Check all tables url
            $checkall_url = 'db_details.php' . '?lang=' . $lang . '&amp;server=' . $server . '&amp;db=' . urlencode($db);

    echo "\n"; ?>
            <tr>
                <td colspan="<?php echo(($cfgShowStats) ? '11' : '10'); ?>" valign="bottom">
                    <img src="./images/arrow_<?php echo $text_dir; ?>.gif" border="0" width="38" height="22" alt="<?php echo $strWithChecked; ?>">
                    <a href="<?php echo $checkall_url; ?>&amp;checkall=1" onclick="setCheckboxes('tablesForm', true); return false;">
                        <?php echo $strCheckAll; ?></a>
                    &nbsp;/&nbsp;
                    <a href="<?php echo $checkall_url; ?>" onclick="setCheckboxes('tablesForm', false); return false;">
                        <?php echo $strUncheckAll; ?></a>
                    &nbsp;&nbsp;&nbsp;
                    <img src="./images/spacer.gif" border="0" width="38" height="1" alt="">
                    <select name="submit_mult" dir="ltr" onchange="this.form.submit();">
                        <?php
                        echo "\n";

    echo '            <option value="' . $strWithChecked . '" selected="selected">' . $strWithChecked . '</option>' . "\n";

    echo '            <option value="' . $strDrop . '" >' . $strDrop . '</option>' . "\n";

    echo '            <option value="' . $strEmpty . '" >' . $strEmpty . '</option>' . "\n";

    echo '            <option value="' . $strPrintView . '" >' . $strPrintView . '</option>' . "\n";

    echo '            <option value="' . $strOptimizeTable . '" >' . $strOptimizeTable . '</option>' . "\n"; ?>
                    </select>
                    <input type="submit" value="<?php echo $strGo; ?>">
                </td>
            </tr>
        </table>

    </form>
    <?php
} // end case mysql >= 3.23

// 3. Shows tables list mysql < 3.23
else {
    $i = 0;

    echo "\n"; ?>
    <form action="db_details.php">
        <input type="hidden" name="lang" value="<?php echo $lang; ?>">
        <input type="hidden" name="server" value="<?php echo $server; ?>">
        <input type="hidden" name="db" value="<?php echo $db; ?>">

        <table border="<?php echo $cfgBorder; ?>">
            <tr>
                <td></td>
                <th>&nbsp;<?php echo ucfirst($strTable); ?>&nbsp;</th>
                <th colspan="6"><?php echo ucfirst($strAction); ?></th>
                <th><?php echo ucfirst($strRecords); ?></th>
            </tr>
            <?php
            $checked = (!empty($checkall) ? ' checked' : '');

    while ($i < $num_tables) {
        // Sets parameters for links

        $url_query = 'lang=' . $lang . '&amp;server=' . $server . '&amp;db=' . urlencode($db) . '&amp;table=' . urlencode($tables[$i]) . '&amp;goto=db_details.php';

        $bgcolor = ($i % 2) ? $cfgBgcolorOne : $cfgBgcolorTwo;

        echo "\n"; ?>
                <tr>
                    <td align="center" bgcolor="<?php echo $bgcolor; ?>">
                        <input type="checkbox" name="selected_tbl[]" value="<?php echo urlencode($tables[$i]); ?>"<?php echo $checked; ?>>
                    </td>
                    <td bgcolor="<?php echo $bgcolor; ?>" class="data">
                        <b>&nbsp;<?php echo $tables[$i]; ?>&nbsp;</b>
                    </td>
                    <td bgcolor="<?php echo $bgcolor; ?>">
                        <a href="sql.php?<?php echo $url_query; ?>&amp;sql_query=<?php echo urlencode('SELECT * FROM ' . PMA_backquote($tables[$i])); ?>&amp;pos=0"><?php echo $strBrowse; ?></a>
                    </td>
                    <td bgcolor="<?php echo $bgcolor; ?>">
                        <a href="tbl_select.php?<?php echo $url_query; ?>"><?php echo $strSelect; ?></a>
                    </td>
                    <td bgcolor="<?php echo $bgcolor; ?>">
                        <a href="tbl_change.php?<?php echo $url_query; ?>"><?php echo $strInsert; ?></a>
                    </td>
                    <td bgcolor="<?php echo $bgcolor; ?>">
                        <a href="tbl_properties.php?<?php echo $url_query; ?>"><?php echo $strProperties; ?></a>
                    </td>
                    <td bgcolor="<?php echo $bgcolor; ?>">
                        <a href="sql.php?<?php echo $url_query; ?>&amp;reload=1&amp;sql_query=<?php echo urlencode('DROP TABLE ' . PMA_backquote($tables[$i])); ?>&amp;zero_rows=<?php echo urlencode(sprintf($strTableHasBeenDropped, htmlspecialchars($tables[$i], ENT_QUOTES | ENT_HTML5))); ?>"><?php echo $strDrop; ?></a>
                    </td>
                    <td bgcolor="<?php echo $bgcolor; ?>">
                        <a href="sql.php?<?php echo $url_query; ?>&amp;sql_query=<?php echo urlencode('DELETE FROM ' . PMA_backquote($tables[$i])); ?>&amp;zero_rows=<?php echo urlencode(sprintf($strTableHasBeenEmptied, htmlspecialchars($tables[$i], ENT_QUOTES | ENT_HTML5))); ?>"><?php echo $strEmpty; ?></a>
                    </td>
                    <td align="right" bgcolor="<?php echo $bgcolor; ?>">
                        <?php PMA_countRecords($db, $tables[$i]);

        echo "\n"; ?>
                    </td>
                </tr>
                <?php
                $i++;
    } // end while

    echo "\n";

    // Check all tables url
            $checkall_url = 'db_details.php' . '?lang=' . $lang . '&amp;server=' . $server . '&amp;db=' . urlencode($db); ?>
            <tr>
                <td colspan="9">
                    <img src="./images/arrow_<?php echo $text_dir; ?>.gif" border="0" width="38" height="22" alt="<?php echo $strWithChecked; ?>">
                    <a href="<?php echo $checkall_url; ?>&amp;checkall=1" onclick="setCheckboxes('tablesForm', true); return false;">
                        <?php echo $strCheckAll; ?></a>
                    &nbsp;/&nbsp;
                    <a href="<?php echo $checkall_url; ?>" onclick="setCheckboxes('tablesForm', false); return false;">
                        <?php echo $strUncheckAll; ?></a>
                </td>
            </tr>

            <tr>
                <td colspan="9">
                    <img src="./images/spacer.gif" border="0" width="38" height="1" alt="">
                    <i><?php echo $strWithChecked; ?></i>&nbsp;&nbsp;
                    <input type="submit" name="submit_mult" value="<?php echo $strDrop; ?>">
                    &nbsp;<?php $strOr . "\n"; ?>&nbsp;
                    <input type="submit" name="submit_mult" value="<?php echo $strEmpty; ?>">
                </td>
            </tr>
        </table>

    </form>
    <?php
} // end case mysql < 3.23

echo "\n";
?>
    <hr>


<?php
/**
 * Database work
 */
$url_query = 'lang=' . $lang . '&amp;server=' . $server . '&amp;db=' . urlencode($db) . '&amp;goto=db_details.php';
if (isset($show_query) && 'y' == $show_query) {
    // This script has been called by read_dump.php

    if (isset($sql_query_cpy)) {
        $query_to_display = $sql_query_cpy;
    } // Other cases

    elseif (get_magic_quotes_gpc()) {
        $query_to_display = stripslashes($sql_query);
    } else {
        $query_to_display = $sql_query;
    }
} else {
    $query_to_display = '';
}
?>
    <!-- DATABASE WORK -->
    <ul>
        <?php
        if ($num_tables > 0) {
            ?>
            <!-- Printable view of a table -->
            <li>
                <div style="margin-bottom: 10px"><a href="db_printview.php?<?php echo $url_query; ?>"><?php echo $strPrintView; ?></a></div>
            </li>
            <?php
        }

        // loic1: defines wether file upload is available or not
        $is_upload = (PMA_PHP_INT_VERSION >= 40000 && function_exists('ini_get')) ? (('on' == mb_strtolower(ini_get('file_uploads')) || 1 == ini_get('file_uploads')) && (int)ini_get('upload_max_filesize')) : ((int)@get_cfg_var('upload_max_filesize'));
        ?>

        <!-- Query box, sql file loader and bookmark support -->
        <li>
            <a name="querybox"></a>
            <form method="post" action="read_dump.php"<?php if ($is_upload) {
            echo ' enctype="multipart/form-data"';
        } ?>
                  onsubmit="return checkSqlQuery(this)">
                <input type="hidden" name="is_js_confirmed" value="0">
                <input type="hidden" name="lang" value="<?php echo $lang; ?>">
                <input type="hidden" name="server" value="<?php echo $server; ?>">
                <input type="hidden" name="db" value="<?php echo $db; ?>">
                <input type="hidden" name="pos" value="0">
                <input type="hidden" name="goto" value="db_details.php">
                <input type="hidden" name="zero_rows" value="<?php echo htmlspecialchars($strSuccess, ENT_QUOTES | ENT_HTML5); ?>">
                <input type="hidden" name="prev_sql_query" value="<?php echo((!empty($query_to_display)) ? urlencode($query_to_display) : ''); ?>">
                <?php echo sprintf($strRunSQLQuery, $db) . ' ' . PMA_showDocuShort('S/E/SELECT.html'); ?>&nbsp;:<br>
                <div style="margin-bottom: 5px">
<textarea name="sql_query" cols="<?php echo $cfgTextareaCols; ?>" rows="<?php echo $cfgTextareaRows; ?>" wrap="virtual" onfocus="this.select()">
<?php echo((!empty($query_to_display)) ? htmlspecialchars($query_to_display, ENT_QUOTES | ENT_HTML5) : ''); ?>
</textarea><br>
                    <input type="checkbox" name="show_query" value="y" checked>&nbsp;
                    <?php echo $strShowThisQuery; ?><br>
                </div>
                <?php
                // loic1: displays import dump feature only if file upload available
                if ($is_upload) {
                    echo '            <i>' . $strOr . '</i> ' . $strLocationTextfile . '&nbsp;:<br>' . "\n"; ?>
                    <div style="margin-bottom: 5px">
                        <input type="file" name="sql_file"><br>
                    </div>
                    <?php
                } // end if
                echo "\n";

                // Bookmark Support
                if ($cfgBookmark['db'] && $cfgBookmark['table']) {
                    if (($bookmark_list = PMA_listBookmarks($db, $cfgBookmark)) && count($bookmark_list) > 0) {
                        echo "            <i>$strOr</i> $strBookmarkQuery&nbsp;:<br>\n";

                        echo '            <div style="margin-bottom: 5px">' . "\n";

                        echo '            <select name="id_bookmark">' . "\n";

                        echo '                <option value=""></option>' . "\n";

                        while (list($key, $value) = each($bookmark_list)) {
                            echo '                <option value="' . $value . '">' . htmlentities($key, ENT_QUOTES | ENT_HTML5) . '</option>' . "\n";
                        }

                        echo '            </select>' . "\n";

                        echo '            <input type="radio" name="action_bookmark" value="0" checked style="vertical-align: middle">' . $strSubmit . "\n";

                        echo '            &nbsp;<input type="radio" name="action_bookmark" value="1" style="vertical-align: middle">' . $strBookmarkView . "\n";

                        echo '            &nbsp;<input type="radio" name="action_bookmark" value="2" style="vertical-align: middle">' . $strDelete . "\n";

                        echo '            <br>' . "\n";

                        echo '            </div>' . "\n";
                    }
                }
                ?>
                <input type="submit" name="SQL" value="<?php echo $strGo; ?>">
            </form>
        </li>


        <?php
        /**
         * Query by example and dump of the db
         * Only displayed if there is at least one table in the db
         */
        if ($num_tables > 0) {
            ?>
            <!-- Query by an example -->
            <li>
                <div style="margin-bottom: 10px"><a href="tbl_qbe.php?<?php echo $url_query; ?>"><?php echo $strQBE; ?></a></div>
            </li>

            <!-- Dump of a database -->
            <li>
                <a name="dumpdb"></a>
                <form method="post" action="tbl_dump.php" name="db_dump">
                    <?php echo $strViewDumpDB; ?><br>
                    <table>
                        <tr>
                            <?php
                            $colspan = '';

            // loic1: already defined at the top of the script!

            // $tables     = mysql_list_tables($db);

            // $num_tables = @mysql_numrows($tables);

            if ($num_tables > 1) {
                $colspan = ' colspan="2"';

                echo "\n"; ?>
                                <td>
                                    <select name="table_select[]" size="5" multiple="multiple">
                                        <?php
                                        $i = 0;

                echo "\n";

                $is_selected = (!empty($selectall) ? ' selected="selected"' : '');

                while ($i < $num_tables) {
                    $table = ((PMA_MYSQL_INT_VERSION >= 32300) ? $tables[$i]['Name'] : $tables[$i]);

                    echo '                    <option value="' . $table . '"' . $is_selected . '>' . $table . '</option>' . "\n";

                    $i++;
                } ?>
                                    </select>
                                </td>
                                <?php
            } // end if

            echo "\n"; ?>
                            <td valign="middle">
                                <input type="radio" name="what" value="structure" checked>
                                <?php echo $strStrucOnly; ?><br>
                                <input type="radio" name="what" value="data">
                                <?php echo $strStrucData; ?><br>
                                <input type="radio" name="what" value="dataonly">
                                <?php echo $strDataOnly; ?>
                                <?php
                                if ($num_tables > 1) {
                                    echo "\n"; ?>
                                    <br>
                                    <a href="<?php echo $checkall_url; ?>&amp;selectall=1#dumpdb" onclick="setSelectOptions('db_dump', 'table_select[]', true); return false;"><?php echo $strSelectAll; ?></a>
                                    &nbsp;/&nbsp;
                                    <a href="<?php echo $checkall_url; ?>#dumpdb" onclick="setSelectOptions('db_dump', 'table_select[]', false); return false;"><?php echo $strUnselectAll; ?></a>
                                    <?php
                                }  // end if
                                echo "\n"; ?>
                            </td>
                        </tr>
                        <tr>
                            <td<?php echo $colspan; ?>>
                                <input type="checkbox" name="drop" value="1">
                                <?php echo $strStrucDrop . "\n"; ?>
                            </td>
                        </tr>
                        <tr>
                            <td<?php echo $colspan; ?>>
                                <input type="checkbox" name="showcolumns" value="yes">
                                <?php echo $strCompleteInserts . "\n"; ?>
                            </td>
                        </tr>
                        <tr>
                            <td<?php echo $colspan; ?>>
                                <input type="checkbox" name="extended_ins" value="yes">
                                <?php echo $strExtendedInserts . "\n"; ?>
                            </td>
                        </tr>
                        <?php
                        // Add backquotes checkbox
                        if (PMA_MYSQL_INT_VERSION >= 32306) {
                            ?>
                            <tr>
                                <td<?php echo $colspan; ?>>
                                    <input type="checkbox" name="use_backquotes" value="1">
                                    <?php echo $strUseBackquotes . "\n"; ?>
                                </td>
                            </tr>
                            <?php
                        } // end backquotes feature
                        echo "\n"; ?>
                        <tr>
                            <td<?php echo $colspan; ?>>
                                <input type="checkbox" name="asfile" value="sendit" onclick="return checkTransmitDump(this.form, 'transmit')">
                                <?php echo $strSend . "\n"; ?>
                                <?php
                                // gzip and bzip2 encode features
                                if (PMA_PHP_INT_VERSION >= 40004) {
                                    $is_zip = (isset($cfgZipDump) && $cfgZipDump && @function_exists('gzcompress'));

                                    $is_gzip = (isset($cfgGZipDump) && $cfgGZipDump && @function_exists('gzencode'));

                                    $is_bzip = (isset($cfgBZipDump) && $cfgBZipDump && @function_exists('bzcompress'));

                                    if ($is_zip || $is_gzip || $is_bzip) {
                                        echo "\n" . '                (' . "\n";

                                        if ($is_zip) {
                                            ?>
                                            <input type="checkbox" name="zip" value="zip" onclick="return checkTransmitDump(this.form, 'zip')"><?php echo $strZip . (($is_gzip || $is_bzip) ? '&nbsp;' : '') . "\n"; ?>
                                            <?php
                                        }

                                        if ($is_gzip) {
                                            echo "\n"
                                            ?>
                                            <input type="checkbox" name="gzip" value="gzip" onclick="return checkTransmitDump(this.form, 'gzip')"><?php echo $strGzip . (($is_bzip) ? '&nbsp;' : '') . "\n"; ?>
                                            <?php
                                        }

                                        if ($is_bzip) {
                                            echo "\n"
                                            ?>
                                            <input type="checkbox" name="bzip" value="bzip" onclick="return checkTransmitDump(this.form, 'bzip')"><?php echo $strBzip . "\n"; ?>
                                            <?php
                                        }

                                        echo "\n" . '                )';
                                    }
                                }

            echo "\n"; ?>
                            </td>
                        </tr>
                        <tr>
                            <td<?php echo $colspan; ?>>
                                <input type="submit" value="<?php echo $strGo; ?>">
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" name="server" value="<?php echo $server; ?>">
                    <input type="hidden" name="lang" value="<?php echo $lang; ?>">
                    <input type="hidden" name="db" value="<?php echo $db; ?>">
                </form>
            </li>
            <?php
        } // end of create dump if there is at least one table in the db
        ?>

        <!-- Create a new table -->
        <li>
            <form method="post" action="tbl_create.php"
                  onsubmit="return (emptyFormElements(this, 'table') && checkFormElementInRange(this, 'num_fields', 1))">
                <input type="hidden" name="server" value="<?php echo $server; ?>">
                <input type="hidden" name="lang" value="<?php echo $lang; ?>">
                <input type="hidden" name="db" value="<?php echo $db; ?>">
                <?php
                echo '        ' . $strCreateNewTable . htmlspecialchars($db, ENT_QUOTES | ENT_HTML5) . '&nbsp;:<br>' . "\n";
                echo '        ' . $strName . '&nbsp;:&nbsp;' . "\n";
                echo '        ' . '<input type="text" name="table" maxlength="64">' . "\n";
                echo '        ' . '<br>' . "\n";
                echo '        ' . $strFields . '&nbsp;:&nbsp;' . "\n";
                echo '        ' . '<input type="text" name="num_fields" size="2">' . "\n";
                echo '        ' . '&nbsp;<input type="submit" value="' . $strGo . '">' . "\n";
                ?>
            </form>
        </li>

        <?php
        // Check if the user is a Superuser
        // TODO: set a global variable with this information
        // loic1: optimized query
        $result = @$GLOBALS['xoopsDB']->queryF('USE mysql');
        $is_superuser = (!$GLOBALS['xoopsDB']->error());

        // Display the DROP DATABASE link only if allowed to do so
        if ($cfgAllowUserDropDatabase || $is_superuser) {
            ?>
            <!-- Drop database -->
            <li>
                <a href="sql.php?server=<?php echo $server; ?>&amp;lang=<?php echo $lang; ?>&amp;db=<?php echo urlencode($db); ?>&amp;sql_query=<?php echo urlencode('DROP DATABASE ' . PMA_backquote($db)); ?>&amp;zero_rows=<?php echo urlencode(
                sprintf($strDatabaseHasBeenDropped, htmlspecialchars(PMA_backquote($db), ENT_QUOTES | ENT_HTML5))
            ); ?>&amp;goto=main.php&amp;back=db_details.php&amp;reload=1"
                   onclick="return confirmLink(this, 'DROP DATABASE <?php echo PMA_jsFormat($db); ?>')">
                    <?php echo $strDropDB . ' ' . htmlspecialchars($db, ENT_QUOTES | ENT_HTML5); ?></a>
                <?php echo PMA_showDocuShort('D/R/DROP_DATABASE.html') . "\n"; ?>
            </li>
            <?php
        }
        echo "\n";
        ?>

    </ul>


<?php
/**
 * Displays the footer
 */
echo "\n";
require __DIR__ . '/footer.inc.php';
?>
