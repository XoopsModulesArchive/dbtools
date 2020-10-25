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
define('_MA_DBTOOLS_NOTABLE', "Aucune table n'a été trouvée dans cette base.");
define('_MA_DBTOOLS_HOST', 'Serveur');
define('_MA_DBTOOLS_DB', 'Base de données');
define('_MA_DBTOOLS_STR', 'Structure de la table');
define('_MA_DBTOOLS_DUMP', 'Contenu de la table');
define('_MA_DBTOOLS_ERROR', 'Erreur');
define('_MA_DBTOOLS_QUERY', 'requête SQL');
define('_MA_DBTOOLS_MYSAID', 'MySQL a répondu:');
define('_MA_DBTOOLS_BACK', 'Retour');
define('_MA_DBTOOLS_FILENAME', 'Sauvegarde BD');
define('_MA_DBTOOLS_NAME', 'Sauvegarde de la base de données');
define('_MA_DBTOOLS_DONE', 'effectuée le');
define('_MA_DBTOOLS_AT', 'à');
define('_MA_DBTOOLS_DATE', date('d-m-Y'));
define('_MA_DBTOOLS_PATH', 'le fichier à été sauvegardé dans le dossier :<b> %s </b><br><br>');
define('_MA_DBTOOLS_BACKUPNAME', 'sous le nom : <b> %s </b><br><br>');
define('_MA_DBTOOLS_DOWNLOAD', 'Télécharger le fichier');
define('_MA_DBTOOLS_WRONGDIRECTORY', "n'est pas un repertoire ou n'existe pas");
define('_MA_DBTOOLS_FWRITE_ERROR', "erreur lors de l'ecriture de la structure dans le fichier de sauvegarde.");
define('_MA_DBTOOLS_BACKUPOK', '<b>Sauvegarde reussie</b>');
define('_MA_DBTOOLS_QUERRY_ERROR', 'La requete dans la table %s a echoue.<br>');
define('_MA_DBTOOLS_ENDBACKUP', ' backup terminé ');
define('_MA_DBTOOLS_FILEEXIST', 'Impossible de faire la sauvegarde, le fichier %s existe deja.');
define('_MA_DBTOOLS_STRUCTUREERROR', 'la recuperation de la structure %s a echoue.<br> ');

define('_MA_DBTOOLS_TABLE', 'Table');
define('_MA_DBTOOLS_SIZE', 'Taille');
define('_MA_DBTOOLS_STATUS', 'Status');
define('_MA_DBTOOLS_SPACE', 'Espace sauvé');
define('_MA_DBTOOLS_OPTITLE', 'Optimisation de la base ');
define('_MA_DBTOOLS_RES', "Résultat de l'optimisation");
define('_MA_DBTOOLS_SAVED', 'Espace total sauvé : ');
define('_MA_DBTOOLS_TOTAL', 'sauvés depuis sa première éxécution');
define('_MA_DBTOOLS_NBREXE', 'Optimisation effectuée  %s fois');
define('_MA_DBTOOLS_SOPTIMISED', 'Déjà Optimisée');
define('_MA_DBTOOLS_OPTIMISED', 'Optimisée');
define('_MA_DBTOOLS_BACKUP', 'Sauvegarder la base');

//mysql_dump.php
define('_MA_DBTOOLS_SQLDUMT', 'Sauvegarde de la base de Données');
define('_MA_DBTOOLS_SQLSTRUC', 'Structure seule');
define('_MA_DBTOOLS_SQLSTDAT', 'Structure et données');
define('_MA_DBTOOLS_SQLDATA', 'Données seulement');
define('_MA_DBTOOLS_DROP', "Ajouter des énoncés drop table'");
define('_MA_DBTOOLS_SQLCOMP', 'Insertions complètes ');
define('_MA_DBTOOLS_SQLEXT', 'Insertions étendues ');
define('_MA_DBTOOLS_SQLBACKQ', ' Protéger les noms des tables et des champs par des ` `');
define('_MA_DBTOOLS_SQLSAF', 'Transmettre (<small>fichier .sql</small>) ');
define('_MA_DBTOOLS_SQLZIP', 'zippé');
define('_MA_DBTOOLS_SQLGZ', 'gzippé');
define('_MA_DBTOOLS_SQLFILELOC', 'Emplacement du fichier .sql');
