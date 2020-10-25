//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
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
// Project:  The XOOPS Project (http://www.xoops.org/)                       //
// Based on  optimise from http://web-lien.net & 			     //
//	     phpmyadmin http://www.phpwizard.net                             //
// ------------------------------------------------------------------------- //
French 
Ci joint un petit fichier utile : 
il exécute quand il est appelé une sauvegarde de la base
les parametres par défault dont les suivant 
- créée un backup unique daté en .sql
- le place par défautl dans le dossier /cache/backup/

Install : 
-placez le fichier et les dossiers à la racine de votre xoops
-vous pouvez le renommer si vous le souhaitez
-effectuez un chmod777 sur le dossier backup et chomod66 sur ce qu'il contient


L'utilité ? sauvegarder votre base bien sur ;-)
mais l'intéret réel est de l'effectuer automatiquement
pour ce faire aller chez webcrong.org, ouvrez vous un compte 
et planifiez les exécutions de ce script en entrant l'url à la fréquence de votre choix.
http://voutre url/savedb.php (ou le nom que vous avez donné au fichier )
si vous voulez déplacer ce fichier pour des raaison de sécurité, n'oubliez pas de modifier 
le path relatif pour l'include du mainfile.

pour des soucis de sécurité, placez un fichier .htaccess dans le dossier backup. 
celui ci devra comprendre les lignes suivantes :

<Directory>
order allow,deny
deny from all
</Directory> 

verifiez auprès de votre hébergeur la syntaxe exacte concernant le htaccess, celle-ci peut être différente 
ou nécéssiter des commandes particulières (faq,forum et en cas d'absence mail)

// ajout du fichier opt.php : il permet selon le meme principe de réaliser des optimisations à date fixe toujours grâce à webcron.org.
il suffit d'ajouter la tache et de l'activer. 
j'ai établi qu'il serai placé à la racine du site, mais rien ne vous empèche de faire autrement... il vous faudra modifier le path du include('mainfile');
c'est tout


English
here is join a small utility file : 
it make automatiquely a backup from your xoops database when you point on it
and save it in a forder /cache/backup/
For install it :
place the files and the folder a the of your site, 
chmod 777 de directory /backup and 666 the files he content
it work nice with www.webcron.org ;-) 
i don't test it with crontabs

Deutsch
Hier ist ein kleines Utility:
Es erzeugt automatisch ein Backup der Xoops-Datenbank wenn man auf die Dateien ansurft und sichert die Datei in den Ordner /cache/backup/.
Die Dateien und den Ordner in den root Eures Webservers kopieren, chmod 777 von Verzeichnis /backup und chmod 666 der enthaltenen Dateien.
Es funtioniert gut mit www.webcron.org ;-)
Es ist nicht mit crontabs getestet worden.

Enjoy :-D 