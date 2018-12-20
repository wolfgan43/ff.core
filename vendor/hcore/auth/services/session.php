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

if(!defined("SESSION_NAME"))                                define("SESSION_NAME", false);
if(!defined("SESSION_SAVE_PATH"))                           define("SESSION_SAVE_PATH", false);

if(!defined("MOD_AUTH_SESSION_PERMANENT"))                  define("MOD_AUTH_SESSION_PERMANENT", true); //Cms::env("MOD_AUTH_SESSION_PERMANENT"));
if(!defined("MOD_AUTH_COOKIE_SHARE"))                       define("MOD_AUTH_COOKIE_SHARE", true); //Cms::env("MOD_AUTH_COOKIE_SHARE"));


class authSession
{
    const SESSION_NAME                                          = SESSION_NAME;
    const SESSION_PATH                                          = SESSION_SAVE_PATH;
    const COOKIE_PERMANENT                                      = MOD_AUTH_SESSION_PERMANENT;
    const COOKIE_SHARE                                          = MOD_AUTH_COOKIE_SHARE;
//todo: da implementare CSRF_PROTECTION
    const APPID                                                 = APPID;

    const DATA_USER                                             = "auth";
    const DATA_PERMISSIONS                                      = "permissions";

    const DATA_CSRF                                             = "__CSRF__";



    private $auth                                               = null;
    private $domain                                             = null;

    /**
     * authSession constructor.
     * @param $auth
     */
    public function __construct($auth)
    {
        $this->auth                                             = $auth;
    }

    /**
     * @param null $opt
     * @return array
     */
    public function check($opt = null) {
        $session_valid                                          = false;
        if(Auth::isLogged()) {
            $session_valid                                      = true;
        } else {
            if($this->checkSession()) {
                if(@session_start()) {
                    $session_valid                              = true;
                }
            } else {
                $session_valid                                  = null;
            }
        }

       /* $csrf                                                   = $this->env(authSession::DATA_CSRF);

        if($session_valid && $csrf) {
            if(!Auth::password(null, $csrf)) { //troppo lento il metodo
                $session_valid                                  = false;
            }
        }*/

        if($session_valid) {
            $user                                               = $this->env("user");
            /**
             * Set islogged
             */
            if(Auth::isLogged($user["acl"])) {
                if($opt["user"])                                { $res["user"] = $user; }

                $res["status"]                                  = "0";
                $res["error"]                                   = "";
            } else {
                //$this->destroy();

                $res["status"]                                  = "401";
                $res["error"]                                   = "Check: Insufficent Permission";
            }
        } elseif($session_valid === false) {
            $this->destroy();

            $res["status"]                                      = "404";
            $res["error"]                                       = "Invalid Session";

            if($opt["redirect"]) {
               // Cms::doRedirect("login");
            }

        }

        return $res;
    }

    /**
     * @param $ID
     * @param null $fields
     * @return string
     */
    public function create($ID_user, $domain = null, $opt = null) {
        $this->sessionPath();
        $this->sessionName();
//print_r($opt);

        if($opt["csrf"]) {
            if(!Auth::password(null, $opt["csrf"])) {
                return "Invalid Session: csrf";
            }
        }

        $invalid_session                                        = false;
        $permanent                                              = ($opt["refresh"] === null
                                                                    ? authSession::COOKIE_PERMANENT
                                                                    : $opt["refresh"]
                                                                );

        Auth::doEvent("on_create_session", $invalid_session, array("ID_user" => $ID_user, "domain" => $domain, "permanent" => $permanent, "opt" => $opt));
        if($invalid_session) {
            return $invalid_session;
        }

        /**
         * Purge header and remove old cookie
         */
        $this->destroy();
        session_regenerate_id(true);
        session_start();
        $session_id                                             = session_id();

        if(Auth::SECURITY_LEVEL == 3 || Auth::SECURITY_LEVEL == 7) {
            $select                                             = array(
                                                                    "anagraph.*"
                                                                    , "access.users.*"
                                                                    , "access.groups.*"
                                                                    , "access.tokens.token" => "name"
                                                                    , "access.tokens.expire"
                                                                    , "access.tokens.type"
                                                                );

            $anagraph                                           = Auth::getAnagraphByUser($ID_user, $opt["model"], $select);
        } else {
            $anagraph                                           = Auth::getUser($ID_user);
        }

        if(is_array($anagraph)) {
                $anagraph["domain"]                             = ($domain
                                                                    ? $domain
                                                                    : Auth::getDomain($anagraph["ID_domain"])
                                                                );
        }

        /**
         * Set islogged
         */
        if(Auth::isLogged($anagraph["user"]["acl"])) {
            if($anagraph["user"]["acl_profile"]) {
                $profiles                                       = Auth::getProfiles();

                $arrProfile                                     = explode(",", $anagraph["user"]["acl_profile"]);
                rsort($arrProfile);
                foreach($arrProfile AS $profile_name) {
                    if($profiles[$profile_name]) {
                        if($profiles[$profile_name]["rules"]) {
                            foreach($profiles[$profile_name]["rules"] AS $path => $rule) {
                                $anagraph["permissions"]["rules"][$path]["own"]         = (int) $anagraph["permissions"]["rules"][$path]["own"] | $rule["own"];
                                $anagraph["permissions"]["rules"][$path]["others"]      = (int) $anagraph["permissions"]["rules"][$path]["others"] | $rule["others"];
                            }
                        }
                        if($profiles[$profile_name]["env"]) {
                            foreach ($profiles[$profile_name]["env"] AS $key => $env) {
                                $anagraph["permissions"]["env"][$key]                   = $env["value"];
                            }
                        }
                    }
                }
                $anagraph["permissions"]["profiles"]            = $arrProfile;
            }

            Auth::doEvent("on_created_session", $anagraph);

            /*
             * Set Session Data
             */

            $this->env(null, $anagraph);
            /*if($opt["csrf"]) {
                $this->env(authSession::DATA_CSRF, $opt["csrf"]);
            }*/


            /*
             * Set Cookie
             */
            $this->cookie_create($this->sessionName(), $session_id, $permanent);
            if($domain["name"]) {
                $this->cookie_create("Domain", $domain["name"], $permanent);
            }
            $this->cookie_create("group", $anagraph["user"]["acl_primary"], $permanent);


            $res = array(
                "session"   => array(
                    "name"  => $this->sessionName()
                    , "id"  => $this->sessionId()
                )
                , "status"  => "0"
                , "error"   => ""
            );
        } else {
            $this->destroy();
            $res = array(
                 "status"  => "401"
                , "error"   => "Anagraph Missing"
            );
        }
        return $res;
    }
    public function start($user) {
        $this->sessionPath();
        $this->sessionName();

        $this->destroy();
        session_regenerate_id(true);
        session_start();
        $session_id                                             = session_id();

        if(Auth::isLogged($user["acl"])) {
            $this->cookie_create($this->sessionName(), $session_id);
            $this->cookie_create("group", $user["acl_primary"]);

            $this->env(null, array("user" => $user));

            $res = array(
                "session"   => array(
                    "name"  => $this->sessionName()
                    , "id"  => $this->sessionId()
                )
            , "status"  => "0"
            , "error"   => ""
            );
        } else {
            $this->destroy();
            $res = array(
                "status"  => "401"
                , "error"   => "Sudo: Insufficent Permission"
            );
        }

        return $res;
    }

    public function env($name = null, $value = null) {
        if($name) {
            $ref = &$_SESSION[authSession::DATA_USER][$name];
        } else {
            $ref = &$_SESSION[authSession::DATA_USER];
        }

        if($value) {
            $ref = $value;
        }

        return $ref;
    }
    public function envIsset($name) {
        return isset($_SESSION[$name]);
    }
    public function envUnset($name) {
        $res = $_SESSION[$name];
        unset($_SESSION[$name]);

        return $res;
    }
    public function userInfo($set = null) {
        $anagraph = $this->env();
        if(is_array($set) && count($set)) {
            $anagraph = array_replace_recursive($anagraph, $set);
            $this->env(null, $anagraph);
        }

        return $anagraph;
    }
    public function userPermissions($key = null) {
        $permissions = $this->env(authSession::DATA_PERMISSIONS);

        return ($key
            ? $permissions["env"][$key]
            : $permissions
        );
    }
    /**
     * @param bool $cookie
     */
    public function destroy($cookie = true) {
        $res = null;
        @session_unset();
        @session_destroy();

        $session_name = $this->sessionName();
        if($cookie) {
            header_remove("Set-Cookie");
            $this->cookie_destroy($session_name);
            $this->cookie_destroy("Domain");
            $this->cookie_destroy("group");

        }
        unset($_GET[$session_name], $_POST[$session_name], $_COOKIE[$session_name], $_REQUEST[$session_name]);
        unset($_GET["Domain"], $_POST["Domain"], $_COOKIE["Domain"], $_REQUEST["Domain"]);
        unset($_GET["group"], $_POST["group"], $_COOKIE["group"], $_REQUEST["group"]);


        Auth::doEvent("on_destroyed_session", $res);

        return $res;
    }

    /**
     * @param null $name
     * @return null|string
     */
    private function sessionName($name = null) {
        static $isset                                           = null;

        if(!$name)                                              $name = (authSession::SESSION_NAME
                                                                    ? authSession::SESSION_NAME
                                                                    : session_name()
                                                                );
        if($isset != $name) {
            session_name($name);
            $isset                                              = $name;
        }

        return $name;
    }

    /**
     * @param null $path
     * @return array|false|null|string
     */
    private function sessionPath($path = null) {
        static $isset                                           = null;

        if(!$path)                                              $path = (authSession::SESSION_PATH
                                                                    ? authSession::SESSION_PATH
                                                                    : (session_save_path()
                                                                        ? session_save_path()
                                                                        : sys_get_temp_dir()
                                                                    )
                                                                );
        if($isset != $path) {
            session_save_path($path);
            $isset                                              = $path;
        }

        return $path;
    }

    /**
     * @param null $id
     * @param null $path
     * @return bool
     */
    private function checkSession($id = null, $path = null) {
        if(!$id)                                                { $id     = $this->sessionId(); }
        if(!$path)                                              { $path   = $this->sessionPath(); }

        $valid_session                                          = file_exists(rtrim($path, "/") . "/sess_" . $id);

        Auth::doEvent("on_check_session", $valid_session, array("id" => $id, "path" => $path));

        return $valid_session;
    }

    /**
     * @return mixed
     */
    private function sessionId() {
        $session_name                                           = $this->sessionName();
        return ($_REQUEST[$session_name]
            ? $_REQUEST[$session_name]
            : $_COOKIE[$session_name]
        );
    }

    /**
     * @return mixed|null
     */
    private function getPrimaryDomain() {
        if(!$this->domain) {
            $regs                                               = array();
            if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $_SERVER["HTTP_HOST"], $regs)) {
                $this->domain                                  = $regs['domain'];
            } else {
                $this->domain                                  = $_SERVER["HTTP_HOST"];
            }
        }

        return $this->domain;
    }

    /**
     * @param string $name
     */
    private function cookie_create($name, $value, $permanent = null) { //_ut
        if(!$permanent)                                         $permanent = authSession::COOKIE_PERMANENT;
        $sessionCookie                                          = session_get_cookie_params();
        $lifetime                                               = ($permanent
                                                                    ? time() + (60 * 60 * 24 * 365)
                                                                    : $sessionCookie["lifetime"]
                                                                );

        //setcookie($name, $value, $lifetime, $sessionCookie['path'], $_SERVER["HTTP_HOST"], $sessionCookie['secure'], $sessionCookie["httponly"]);

        $sessionCookie                                          = $this->cookie_share_in_subdomains();
        setcookie($name, $value, $lifetime, $sessionCookie['path'], $sessionCookie['domain'], $sessionCookie['secure'], $sessionCookie["httponly"]);
        $_COOKIE[$name] = $value;
    }

    /**
     * @param string $name
     */
    private function cookie_destroy($name) { //_ut
        $sessionCookie                                          = session_get_cookie_params();
        setcookie($name, false, $sessionCookie["lifetime"], $sessionCookie['path'], $_SERVER["HTTP_HOST"], $sessionCookie['secure'], $sessionCookie["httponly"]);

        $sessionCookie                                          = $this->cookie_share_in_subdomains();
        setcookie($name, false, $sessionCookie["lifetime"], $sessionCookie['path'], $sessionCookie['domain'], $sessionCookie['secure'], $sessionCookie["httponly"]);

        unset($_COOKIE[$name]);
    }

    /**
     * @return bool
     */
    private function cookie_share_in_subdomains($share = authSession::COOKIE_SHARE) {
        if($share)                                              session_set_cookie_params(0, '/', '.' . $this->getPrimaryDomain());

        return session_get_cookie_params();
    }

}