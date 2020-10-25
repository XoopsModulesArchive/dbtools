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
// +--------------------------------------------------------------------------+
// | Set of functions used to run config authentication (ie no                 |
// | authentication).                                                         |
// +--------------------------------------------------------------------------+

if (!defined('PMA_CONFIG_AUTH_INCLUDED')) {
    define('PMA_CONFIG_AUTH_INCLUDED', 1);

    /**
     * Displays authentication form
     *
     * @return  bool   always true
     */

    function PMA_auth()
    {
        return true;
    } // end of the 'PMA_auth()' function

    /**
     * Gets advanced authentication settings
     *
     * @return  bool   always true
     */

    function PMA_auth_check()
    {
        return true;
    } // end of the 'PMA_auth_check()' function

    /**
     * Set the user and password after last checkings if required
     *
     * @return  bool   always true
     */

    function PMA_auth_set_user()
    {
        return true;
    } // end of the 'PMA_auth_set_user()' function

    /**
     * User is not allowed to login to MySQL -> authentication failed
     *
     * @return  bool   always true (no return indeed)
     *
     * @global  string    the connection type (persitent or not)
     * @global  string    the MySQL server port to use
     * @global  string    the MySQL socket port to use
     * @global  array     the current server settings
     * @global  string    the font face to use in case of failure
     * @global  string    the default font size to use in case of failure
     * @global  string    the big font size to use in case of failure
     *
     * @global  string    the MySQL error message PHP returns
     */

    function PMA_auth_fails()
    {
        global $php_errormsg;

        global $connect_func, $server_port, $server_socket, $cfgServer;

        global $right_font_family, $font_size, $font_bigger;

        if ($GLOBALS['xoopsDB']->error()) {
            $conn_error = $GLOBALS['xoopsDB']->error();
        } elseif (isset($php_errormsg)) {
            $conn_error = $php_errormsg;
        } else {
            $conn_error = 'Cannot connect: invalid settings.';
        }

        $local_query = $connect_func . '(' . $cfgServer['host'] . $server_port . $server_socket . ', ' . $cfgServer['user'] . ', ' . $cfgServer['password'] . ')'; ?>
        <!DOCTYPE html
                PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
                "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $GLOBALS['available_languages'][$GLOBALS['lang']][2]; ?>" lang="<?php echo $GLOBALS['available_languages'][$GLOBALS['lang']][2]; ?>" dir="<?php echo $GLOBALS['text_dir']; ?>">

        <head>
            <title><?php echo $GLOBALS['strAccessDenied']; ?></title>
            <style type="text/css">
                <!--
                body {
                    font-family: <?php echo $right_font_family; ?>;
                    font-size: <?php echo $font_size; ?>;
                    color: #000000
                }

                h1 {
                    font-family: <?php echo $right_font_family; ?>;
                    font-size: <?php echo $font_bigger; ?>;
                    font-weight: bold
                }

                /
                /
                -->
            </style>
        </head>

    <body bgcolor="<?php echo $GLOBALS['cfgRightBgColor']; ?>">
    <br><br>
    <center>
        <h1><?php echo sprintf($GLOBALS['strWelcome'], ' phpMyAdmin ' . PMA_VERSION); ?></h1>
    </center>
    <br>
        <?php
        echo "\n";

        PMA_mysqlDie($conn_error, $local_query, false);

        return true;
    } // end of the 'PMA_auth()' function
} // $__PMA_CONFIG_AUTH_LIB__
?>
