<?php
/**
 * VGallery: CMS based on FormsFramework
 * Copyright (C) 2004-2015 Alessandro Stucchi <wolfgan@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  @package VGallery
 *  @subpackage core
 *  @author Alessandro Stucchi <wolfgan@gmail.com>
 *  @copyright Copyright (c) 2004, Alessandro Stucchi
 *  @license http://opensource.org/licenses/gpl-3.0.html
 *  @link https://github.com/wolfgan43/vgallery
 */

// *****************
//  GLOBAL SETTINGS
// *****************


// unique application id
define("APPID", "[APPID]");

// session name
session_name(APPID);
//session_save_path("/tmp");
//define("FF_THEME_ADMIN", "admin");

//define("FF_THEME_ADMIN", "hcore");



// ****************
//  ERROR HANDLING
// ****************

/* used to define errors handled by PHP.
   NB:
   This will be bit-masquered with FF_ERRORS_HANDLED by the framework.
 */
error_reporting((E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED) | E_STRICT);


// ***************
//  FILE HANDLING
// ***************

// disable file umasking
@umask(0);

// **********************
//  INTERNATIONALIZATION
// **********************

// default data type conversion
define("FF_LOCALE", "ITA");
define("FF_SYSTEM_LOCALE", "ISO9075"); /* this is the locale setting used to convert system data, like url parameters.
											 this not affect the user directly. */
											 
date_default_timezone_set("Europe/Rome");

define("FF_DEFAULT_CHARSET", "UTF-8");

session_save_path("[SESSION_SAVE_PATH]");

define("DEBUG_MODE", true);
define("SUPERADMIN_USERNAME", "[USERNAME]");
define("SUPERADMIN_PASSWORD", "[PASSWORD]");

define("FF_DISK_PATH", "[DISK_PATH]");
define("FF_SITE_PATH", "[SITE_PATH");

// DEFAULT DB CONNECTION
define("FF_DATABASE_HOST", "[DB_HOST]");
define("FF_DATABASE_NAME", "[DB_NAME]");
define("FF_DATABASE_USER", "[DB_USER]");
define("FF_DATABASE_PASSWORD", "[DB_PASSWORD]");