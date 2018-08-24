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

if(!defined("MOD_SECURITY_SESSION_PERMANENT"))              define("MOD_SECURITY_SESSION_PERMANENT", false);
if (!defined("MOD_SEC_ENABLE_GEOLOCALIZATION")) 	        define ("MOD_SEC_ENABLE_GEOLOCALIZATION", false);

if (!defined("MOD_SEC_MULTIDOMAIN"))                    define ("MOD_SEC_MULTIDOMAIN", false);
if (!defined("MOD_SEC_USERNAME_RECOVER_USERNAME"))    define ("MOD_SEC_USERNAME_RECOVER_USERNAME", false);   //se definito non funziona piu
if (!defined("MOD_SEC_PASSWORD_RECOVER"))				define ("MOD_SEC_PASSWORD_RECOVER", false);   //se definito non funziona piu

if (!defined("MOD_SEC_SOCIAL_FACEBOOK"))            define ("MOD_SEC_SOCIAL_FACEBOOK",    false);
if (!defined("MOD_SEC_SOCIAL_GPLUS"))               define ("MOD_SEC_SOCIAL_GPLUS",    false);
if (!defined("MOD_SEC_SOCIAL_TWITTER"))             define ("MOD_SEC_SOCIAL_TWITTER",    false);
if (!defined("MOD_SEC_SOCIAL_LINKEDIN"))            define ("MOD_SEC_SOCIAL_LINKEDIN",    false);
if (!defined("MOD_SEC_SOCIAL_DRIBBLE"))             define ("MOD_SEC_SOCIAL_DRIBBLE",    false);

if (!defined("MOD_SEC_SOCIAL_FF"))                  define ("MOD_SEC_SOCIAL_FF",    false);

if(!defined("MOD_SECURITY_SESSION_PERMANENT")) define("MOD_SECURITY_SESSION_PERMANENT", false);
if (!defined("MOD_SEC_REGISTRATION")) define ("MOD_SEC_REGISTRATION", false);


