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

if(!defined("SUPERADMIN_USERNAME"))                         define("SUPERADMIN_USERNAME", null);
if(!defined("SUPERADMIN_PASSWORD"))                         define("SUPERADMIN_PASSWORD", null);
if(!defined("AUTH_USERNAME"))                               define("AUTH_USERNAME", null);
if(!defined("AUTH_PASSWORD"))                               define("AUTH_PASSWORD", null);

use OTPHP\HOTP;
use OTPHP\TOTP;

class Auth extends vgCommon
{
    const APPID                                                     = APPID;
    const API_PATH                                                  = "/api/auth/user";
    const PROFILE_PATH                                              = "/conf/profiling";
    const SECURITY_LEVEL                                            = "7"; //APP_SECURITY_LEVEL;
    const AUTHOR                                                    = "VGallery Auth";

    const CERTIFICATE_KL                                            = "002010008005";
    const CERTIFICATE_KD                                            = "846315270";
    const CERTIFICATE_KP                                            = "2";

    const TYPE                                                      = "auth";
    const REQUEST_METHOD                                            = "POST";
    const SA_ALG                                                    = "totp";
    const SA_SENDER                                                 = false;
    const SA_EXPIRE                                                 = 300;
    const SA_HUMAN                                                  = "question";

    const AVATAR_MODE                                               = "80x80";

    const GUEST_GROUP_ID                                            = "2";
    const GUEST_GROUP_NAME                                          = "guests";
    const SUPERADMIN_USERNAME                                       = SUPERADMIN_USERNAME;
    const SUPERADMIN_PASSWORD                                       = SUPERADMIN_PASSWORD;
    const HTTP_USERNAME                                             = AUTH_USERNAME;
    const _PASSWORD                                                 = AUTH_PASSWORD;

    static $singleton                                               = null;
    static $cache                                                   = null;

    public static $request                                          = array(//todo: da capire dove viene richiesto request public
                                                                        "token"             => "t"
                                                                        , "code"             => "code"
                                                                        , "username"        => "username"
                                                                        , "password"        => "password"
                                                                        , "scopes"          => "scopes"
                                                                        , "domain"          => "domain"
                                                                        , "refresh"         => "refresh"
                                                                        , "grantor"         => "g"
                                                                        , "key"             => "key"
                                                                    );
    private static $headers                                         = array(
                                                                        "client_id"         => "CLIENT_ID"
                                                                        , "client_secret"   => "CLIENT_SECRET"
                                                                        , "domain"          => "DOMAIN"
                                                                        , "model"           => "TYPE"               //person, company, custom | Default: null or from DB
                                                                        , "refresh"         => "REFRESH"
                                                                        , "csrf"            => "CSRF"
                                                                    );
    private static $opt                                             = array(
                                                                        "model"             => "person"
                                                                        , "method"          => "session"
                                                                        , "fields"          => null
                                                                        , "scopes"          => null
                                                                        , "redirect"        => null
                                                                        , "refresh"         => null
                                                                        , "token"           => true
                                                                        , "activation"      => true
                                                                        , "security"        => false
                                                                        , "user"            => false
                                                                        , "exit"            => true
                                                                    );
    private static $isLogged                                        = false;
    public static $profiles                                         = null; //todo: da rimettere private

    protected $service                                              = null;
    protected $controllers                                          = array(
                                                                    );
    protected $controllers_rev                                      = null;
    protected $connectors                                           = array(
                                                                    );
    protected $struct					                            = array();

    public $method                                                  = "token";

    public static function getInstance($service = null)
    {
        if(!self::$singleton[$service]) {
            $auth                                                   = (self::$singleton["auth"]
                                                                        ? self::$singleton["auth"]
                                                                        : new Auth($service)
                                                                    );
            self::$singleton[$service]                              = $auth->getService($service);
        }
        return self::$singleton[$service];
    }

    public function __construct($service = null)
    {
        $this->loadControllers(__DIR__);
        $this->service                                              = $service;

        require_once($this->getAbsPathPHP("/config"));

        //$this->setConfig($this->connectors, $this->services);
        //$this->loadSession();
    }

    /**
     * Autentica un utente passando le credenziali
     * questo metodo supporta le seguenti tipologie di autenticazione:
     *  - Sessione
     * @example Auth::login("[USERNAME]", "[PASSWORD]");
     * Se non viene specificato alcun parametro vengono recuperati username e password dalla $_REQUEST:
     * $_REQUEST["username"], $_REQUEST["password"]
     * @example Auth::login();

     *  - Token
     * @example Auth::login("[USERNAME]", "[PASSWORD]", array("type" => "token"));
     * Se non viene specificato alcun parametro vengono recuperati username e password dalla $_REQUEST:
     * $_REQUEST["username"], $_REQUEST["password"]
     * @example Auth::login(array("type" => "token"));
     *
     * Opt: il parametro ha i seguenti parametri
     * - type string: [session|token]
     *  Tipo di autenticazione
     *
     * - 2FA string: [sms|email]
     * Abilita la 2 factor Authentication via sms o email
     * NB: l'utente deve aver censito nella propria anagrafica tel o email
     *
     * - exit bool
     * Se true e il login non ha successo viene bloccata l'esecuzione dello script (exit)
     *
     * - domain string: [dominio specifico]
     * Specifica l'appartenenza o ambito dell'utente con il quale si vuole accedere
     *
     * - method string: [POST | PATCH | DELETE | GET | COOKIE | SESSION]
     * http request method usato per inviare le credenziali
     * NB: Per questo metodo l'unico metodo supportato è POST
     *
     * @api /api/user/login
     *
     * @param null $username
     * @param null $password
     * @param null $opt
     * @return array(status, error, token)
     *
     * @todo da finire e testare la 2FA
     *
     */
    public static function login($username = null, $password = null, $opt = null) { //aggiungere refresh token
        if(self::DEBUG)                                             { Debug::startWatch(); }

        if(is_array($username) && !$password && !$opt) {
            $opt                                                    = $username;
            $username                                               = null;
        }

        $opt                                                        = self::getOpt($opt);

        //$res                                                        = self::isInvalidReqMethod($opt["exit"]);
        //if(!$res) {
        $username                                                   = ($username
                                                                        ? $username
                                                                        : self::getReq("username")
                                                                    );
        $password                                                   = ($password
                                                                        ? $password
                                                                        : self::getReq("password")
                                                                    );

        if(!$opt["domain"])                                     $opt["domain"] = self::getReq("domain");

        $security                                               = self::security($opt); //todo: da verificare perche nn popola il dominio
        if(isset($security["status"]) && $security["status"] === "0") {
            if($username && $password) {
                if($opt["method"] == "session") {
                    $sudo                                           = Auth::sudo($username, $password, $security["domain"]);
                    if($sudo) { return $sudo; }
                }

                $ID_domain                                          = (int) $security["domain"]["ID"];
                $where                                              = array(
                                                                        "users.password"        => $password
                                                                        , "users.ID_domain"     => $ID_domain
                                                                    );

                if (Cms::getInstance("validator")->isEmail($username)) {
                    $where["users.email"]                           = $username;
                } elseif (Cms::getInstance("validator")->isTel($username)) {
                    $where["users.tel"]                             = $username;
                } else {
                    $where["users.username"]                        = $username;
                }

                if($where) {
                    $user                                           = Anagraph::getInstanceNoStrict("access")->read(
                                                                        array(
                                                                            "users.ID"
                                                                            , "users.tel"
                                                                            , "users.email"
                                                                            , "users.status"
                                                                            , "users.last_login"
                                                                            , "users.acl_primary"
                                                                        )
                                                                        , $where
                                                                    );
                }
                if(is_array($user) && $user["ID"]) {
                    switch ($opt["method"]) {
                        case "token":
                            $auth                                   = Auth::getInstance("token")->get(
                                                                        $user["ID"]
                                                                        , array(
                                                                            "limit"     => "1"
                                                                            , "token"    => ($security["domain"]["security"]["token_type"]
                                                                                            ? $security["domain"]["security"]["token_type"]
                                                                                            : null
                                                                                        )
                                                                            , "create"  => array(
                                                                                "key" => self::APPID . "-" . $ID_domain . "-" . $username . "-" . $password
                                                                                , "expire" => ($security["domain"]["security"]["token_expire"]
                                                                                    ? $security["domain"]["security"]["token_expire"]
                                                                                    : null
                                                                                )
                                                                            )
                                                                        )
                                                                    );
                            break;
                        case "session":
                            if($user["status"]) {
                                $auth                               = Auth::getInstance("session")->create($user["ID"], $security["domain"], $opt);
                            } else {
                                $auth                               = "User not Activated";
                            }
                            break;
                        default:
                            $auth                                   = "Authentication Method not Supported";
                    }

                    if(is_array($auth)) {
                        if(isset($auth["status"]) && $auth["status"] === "0") {
                            if($opt["2FA"]) {
                                switch ($opt["2FA"]) {
                                    case "sms":
                                        $to                         = $user["tel"];
                                        $service2FA                 = "sms";
                                        break;
                                    case "email":
                                        $to                         = $user["email"];
                                        $service2FA                 = "email";
                                        break;
                                    default:
                                }

                                if($to && $service2FA) {
                                    $code                           = self::createCode("activation", $security["domain"]["security"]["sa_alg"], $security["domain"]["security"]["sa_expire"]);

                                    $device = self::logDevice($user, $security);

                                    $res                            = Notifier::getInstance($service2FA)->send($code, $to);
                                } else {
                                    $res["status"]                  = "409";
                                    $res["error"]                   = "Email or Tel Empty for Sending AuthCode";
                                }
                            } else {
                                $res                                = $auth;
                                $anagraph                           = self::loginSuccess($user, $opt);
                                if($anagraph) {
                                    $res["user"]                    = $anagraph;
                                }
                            }
                        } else {
                            $res                                    = $auth;
                        }
                    } elseif($auth) {
                        $res["status"]                              = "500";
                        $res["error"]                               = $auth;
                    } else {
                        $res["status"]                              = "404";
                        $res["error"]                               = "User not Found";
                    }
                } else {
                    $res["status"]                                  = "401";
                    $res["error"]                                   = "Wrong Username or Password";
                }
            } else {
                $res["status"]                                      = "400";
                $res["error"]                                       = "Username or Password Empty";
            }
        } else {
            $res["status"]                                          = $security["status"];
            $res["error"]                                           = $security["error"];
        }

        if(is_array($res) && $res["status"] !== "0" && $opt["exit"])self::endScript($res);
        //}

        if(self::DEBUG && is_array($res))                           $res["exTime"] = Debug::stopWatch();

        return $res;
	}

    /**
     * Invalida l'autenticazione
     *
     * @param null $opt
     * @return mixed
     */
    public static function logout($token = null, $opt = null) {
        if(self::DEBUG)                                             { Debug::startWatch(); }

        if(is_array($token) && !$opt) {
            $opt                                                    = $token;
            $token                                                  = null;
        }

        $security                                                   = self::security($opt);
        if(isset($security["status"]) && $security["status"] === "0") {
            $res                                                    = Auth::getInstance("session")->destroy();

            $res["status"]                                          = "0"; //todo: da invalidare il token
            $res["error"]                                           = "";
        } else {
            $res["status"]                                          = $security["status"];
            $res["error"]                                           = $security["error"];
        }

        if(is_array($res) && $res["status"] !== "0" && $opt["exit"]){ self::endScript($res); }

        if(self::DEBUG && is_array($res))                           { $res["exTime"] = Debug::stopWatch(); }

        return $res;
    }

    /**
     * Verifica se le l'autenticazione è valita.
     * questo metodo valida le seguenti tipologie di autenticazione:
     * - Token
     *
     *
     * - Sessione
     *
     *
     * @param null $token
     * @param null $opt
     * @return mixed
     */
    public static function check($token = null, $opt = null) {
        //non torna i dati utente per scelta

        if(self::DEBUG)                                             { Debug::startWatch(); }
//da raffinare con il client id e secret e il domain name
        if(is_array($token) && !$opt) {
            $opt                                                    = $token;
            $token                                                  = null;
        }

        $opt                                                        = self::getOpt($opt);

        //$res                                                        = self::isInvalidReqMethod($opt["exit"]);
        //if(!$res) {
        $security                                                   = self::security($opt);

        if(isset($security["status"]) && $security["status"] === "0") {
            if($opt["method"] == "refresh") {
                $opt["refresh"]                                     = self::getReq("refresh");
                if($opt["refresh"] === null) {
                    $isInvalid                                      = "Refresh not Set";
                }
            }

            if(!$isInvalid) {
                $token                                              = ($token
                                                                        ? $token
                                                                        : self::getReq("token")
                                                                    );
                if(!self::getPathInfo(self::API_PATH))     { $opt["user"] = true; }
                $res                                                = ($token
                                                                        ? Auth::getInstance("token")->check($token, $opt)
                                                                        : Auth::getInstance("session")->check($opt)
                                                                    );

                if(isset($res["status"]) && $res["status"] === "0") {
                    if($opt["method"] == "session" && $token && $res["user"]) {
                        $auth                                       = Auth::getInstance("session")->create($res["user"]["ID"], $security["domain"], $opt);
                        if(isset($auth["status"]) && $auth["status"] === "0") {
                            $request                                = self::getRequest();
                            $query                              = (count($request["valid"])
                                                                    ? "?" . http_build_query($request["valid"])
                                                                    : ""
                                                                );

                            Cms::redirect($_SERVER["HTTP_HOST"] . $_SERVER["PATH_INFO"] . $query);
                        }
                    }

                    if($opt["security"]) {
                        $res                                        = array_replace($security, $res);
                        if(!$res["domain"] && $res["user"]["ID_domain"]) {
                            $res["domain"]                          = self::getDomain($res["user"]["ID_domain"]);
                        }
                    }
                } elseif($token) {
                }

            } else {
                $res["status"]                                      = "400";
                $res["error"]                                       = $isInvalid;
            }
        } else {
            $res["status"]                                          = $security["status"];
            $res["error"]                                           = $security["error"];
        }

        if(is_array($res) && $res["status"] !== "0" && $opt["exit"])self::endScript($res);

        //}

        if(self::DEBUG && is_array($res))                           $res["exTime"] = Debug::stopWatch();

        return ($opt["security"] || self::getPathInfo(self::API_PATH)
            ? $res
            : (is_array($res) && $res["status"] === "0"
                ? true
                : false
            )
        );
    }

    /**
     * @param null $key value to Set
     * @param null $token
     * @param null $opt
     * @return mixed
     */
    public static function write($value = null, $token = null, $opt = null) {


        if(self::DEBUG)                                             { Debug::startWatch(); }

        if(is_array($value) && !$token && !$opt) {
            $opt                                                    = $value;
            $value                                                  = null;
        }
        $opt                                                        = self::getOpt($opt);

        $token                                                      = ($token
                                                                        ? $token
                                                                        : self::getReq("code")
                                                                    );

        $value                                                      = ($value
                                                                        ? $value
                                                                        : self::getReq("key")
                                                                    );
        $scopes                                                     = ($opt["scopes"]
                                                                        ? $opt["scopes"]
                                                                        : self::getReq("scopes")
                                                                    );
        $username                                                   = ($opt["username"]
                                                                        ? $opt["username"]
                                                                        : self::getReq("username")
                                                                    );
        if(!$token && $username) {
            return self::request($username, $opt);
        }
        if($token) {
            $return                                                 = self::code($token, array("scopes" => $scopes));
            if(isset($return["status"]) && $return["status"] === "0") {
                $bearer                                             =  self::getBearerToken();

                $sa_human                                           = ($return["security"]["domain"]["security"]["sa_human"]
                                                                        ? $return["security"]["domain"]["security"]["sa_human"]
                                                                        : self::SA_HUMAN
                                                                    );
                if($bearer) {
                    $user                                           = self::getUserByBearer($sa_human
                                                                        ? $opt["grantor"]
                                                                        : false
                                                                    , $bearer);
                    if(isset($user["status"]) && $user["status"] === "0") {
                        //$device                                   = self::logDevice($user, $security);

                        if($device["ID"] == $user["SID_device"] && $user["SID_ip"] == $_SERVER["REMOTE_ADDR"]) {
                            //attenzione stiamo usando lo destto device per fare recover e activation
                            //da far partire la 2fa basata su domanda segreta
                        }


                        switch($scopes) {
                            case "email":
                                $field                              = "email";
                                $invalid                            = (Cms::getInstance("validator")->isEmail($value)
                                                                        ? false
                                                                        : "Invalid Email"
                                                                    );
                                break;
                            case "password":
                                $field                              = "password";
                                $invalid                            = Cms::getInstance("validator")->invalidPassword($value, $return["security"]["domain"]["security"]["pw_validator"]);
                                break;
                            case "activation":
                                $field                              = "status";
                                $value                              = "1";
                                $invalid                            = false;
                                break;
                            default:
                                $value                              = null;
                                $invalid                            = "Unknow Operation";
                        }

                        if(!$invalid) {
                            Auth::doEvent("on_do_write", $value, array("user" => $user, "scope" => $scopes));

                            if($field && $value) {
                                $update                             = Anagraph::getInstanceNoStrict("access")->update(
                                                                        array(
                                                                            $field                  => $value  //da sistemare e parametrizare il type
                                                                            , "users.SID"           => ""
                                                                            , "users.SID_expire"    => "0"
                                                                            , "users.SID_device"    => "0"
                                                                            , "users.SID_ip"        => ""
                                                                        )
                                                                        , array(
                                                                            "users.ID"              => $user["ID"]
                                                                        )
                                                                    );

                                //autologin
                                if($opt["method"] == "session") {
                                    $auth                           = Auth::getInstance("session")->create($user["ID"], $return["security"]["domain"], $opt);
                                }
                                if($auth) {
                                    $res                            = $auth;
                                } else {
                                    $res["status"]                  = "0";
                                    $res["error"]                   = "";
                                }
                            } else {
                                $res["status"]                      = "401";
                                $res["error"]                       = "Missing Params";
                            }
                        } else {
                            $res["status"]                          = "401";
                            $res["error"]                           = $invalid;
                        }
                    } else {
                        $res["status"]                              = $user["status"];
                        $res["error"]                               = $user["error"];
                    }
                } else {
                    $res["status"]                                  = "400";
                    $res["error"]                                   = "Bearer Auth missing";
                }
            } else {
                $res                                                = $return;
            }
        } else {
            $res["status"]                                          = "401";
            $res["error"]                                           = "Code Missing";
        }

        if(is_array($res) && $res["status"] !== "0" && $opt["exit"])self::endScript($res);

        if(self::DEBUG && is_array($res))                           $res["exTime"] = Debug::stopWatch();

        return $res;
    }

    public static function request($username = null, $opt = null) {
        if(self::DEBUG)                                             { Debug::startWatch(); }

        if(is_array($username) && !$opt) {
            $opt                                                    = $username;
            $username                                               = null;
        }
        $opt                                                        = self::getOpt($opt);

        $username                                                   = ($username
                                                                        ? $username
                                                                        : self::getReq("username")
                                                                    );
        $scopes                                                     = ($opt["scopes"]
                                                                        ? $opt["scopes"]
                                                                        : self::getReq("scopes")
                                                                    );

        if($username) {
            if (Cms::getInstance("validator")->isEmail($username)) {
                $where["users.email"]                               = $username;
                $sender_default                                     = "email";
            } elseif (Cms::getInstance("validator")->isTel($username)) {
                $where["users.tel"]                                 = $username;
                $sender_default                                     = "tel";
            } else {
                $where["users.username"]                            = $username;
                $sender_default                                     = self::SA_SENDER;
            }
        }

        if($where) {
            $security                                               = self::security($opt);
            if(isset($security["status"]) && $security["status"] === "0") {
                $sa_human                                           = ($security["domain"]["security"]["sa_human"]
                                                                        ? $security["domain"]["security"]["sa_human"]
                                                                        : self::SA_HUMAN
                                                                    );

                $where["domains.name"]                              = $security["domain"]["name"];
                $select                                             = array(
                                                                        "users.ID"
                                                                        , "users.tel"
                                                                        , "users.email"
                                                                        , "users.username"
                                                                    );
                if($sa_human)                                       { $select[] = "users.SID_" . $sa_human; }

                $user                                               = Anagraph::getInstanceNoStrict("access")->read($select, $where);
                if(is_array($user)) {
                    $code                                           = self::createCode($scopes, $security["domain"]["security"]["sa_alg"], $security["domain"]["security"]["sa_expire"]);
                    $sender                                         = ($security["domain"]["security"]["sa_sender"]
                                                                        ? $security["domain"]["security"]["sa_sender"]
                                                                        : $sender_default
                                                                    );
                    switch($sender) {
                        case "email":
                            switch ($scopes) {
                                case "email":
                                case "password":
                                    $endpoint                       = "recover";
                                    break;
                                case "activation":
                                    $endpoint                       = "activation";
                                    break;
                                default:
                                    $endpoint                       = "account";
                            }

                            $params                                 = array(
                                                                        "title"                 => "verify Code"
                                                                        , "template"            => "auth" . $opt["email_path"] . "::" . $endpoint . "_code.html"
                                                                        , "fields"              => array(
                                                                            "code"              => $code
                                                                            , "url"             => Cms::getUrl(self::API_PATH . "/" . $endpoint . "?code=" . $code)
                                                                            , "username"        => $user["username"]
                                                                            , "name"            => $user["name"]
                                                                            , "surname"         => $user["surname"]

                                                                        )
                                                                        , "email"               => $user["email"]
                                                                    );

                            Auth::doEvent("on_before_send_request", $params);

                            $return                                 = Notifier::getInstance("email")->send($params);
                            if($return["error"]) {
                                $res["status"]                      = "500";
                                $res["error"]                       = $return["error"];
                            }
                            break;
                        case "sms":
                            $return                                 = Notifier::getInstance("sms")->send(
                                                                        array(
                                                                            "message"               => "code : " . $code
                                                                        )
                                                                        , $user["tel"]
                                                                    );
                            if($return["error"]) {
                                $res["status"]                      = "500";
                                $res["error"]                       = $return["error"];
                            }
                            break;
                        case "google.authenticator":
                            break;
                        default:
                            $res["code"]                            = $code;
                    }

                    if(!$res["status"]) {
//                        $device                                     = self::logDevice($user, $security);
                        //if(!$res["code"]) {
                            $sa_expire                              = ($security["domain"]["security"]["sa_expire"]
                                                                        ? $security["domain"]["security"]["sa_expire"]
                                                                        : self::SA_EXPIRE
                                                                    );
                            $token                                  = Auth::getInstance("token")->create();
                            $update                                 = Anagraph::getInstanceNoStrict("access")->update(
                                                                        array(
                                                                            "users.SID"             => $token
                                                                            , "users.SID_expire"    => (time() + $sa_expire)
                                                                            , "users.SID_device"    => $device["ID"]
                                                                            , "users.SID_ip"        => $_SERVER["REMOTE_ADDR"]
                                                                        )
                                                                        , array(
                                                                            "users.ID"              => $user["ID"]
                                                                        )
                                                                    );
                        //}


                        if(0 && $update) { //todo: da verificare dove salva i dati
                            $res["status"]                          = "500";
                            $res["error"]                           = $update;
                        } else {
                            if($user["SID_" . $sa_human])           { $res["require"] = $user["SID_" . $sa_human]; }
                            if($token)                              { $res["t"] = $token; }
                            if($sender)                             { $res["sender"] = $sender; }
                            $res["status"]                          = "0";
                            $res["error"]                           = "";
                        }
                    }
                } else {
                    $res["status"]                                  = "404";
                    $res["error"]                                   = "User not Found";
                }
            } else {
                $res["status"]                                      = $security["status"];
                $res["error"]                                       = $security["error"];
            }
        } else {
            $res["status"]                                          = "400";
            $res["error"]                                           = "Identifier Empty or not valid";
        }

        if(is_array($res) && $res["status"] !== "0" && $opt["exit"])self::endScript($res);

        if(self::DEBUG && is_array($res))                           $res["exTime"] = Debug::stopWatch();

        return $res;
    }

    public static function code($key = null, $opt = null) {
        if(self::DEBUG)                                             { Debug::startWatch(); }

        if(is_array($key) && !$opt) {
            $opt                                                    = $key;
            $key                                                    = null;
        }
        $opt                                                        = self::getOpt($opt);

        $security                                                   = self::security($opt);
        if(isset($security["status"]) && $security["status"] === "0") {
            $key                                                    = ($key
                                                                        ? $key
                                                                        : self::getReq("token")
                                                                    );
            $return                                                 = self::createCode($opt["scopes"], $security["domain"]["security"]["sa_alg"], $security["domain"]["security"]["sa_expire"], $key);

            if($key) {
                //$res["code"]                                        = $secret;
                if($return) {
                    if($opt["security"])                            $res["security"] = $security;
                    $res["status"]                                  = "0";
                    $res["error"]                                   = "";
                } else {
                    $res["status"]                                  = "404";
                    $res["error"]                                   = "Code not Valid" . (self::DEBUG
                                                                        ? ": " . ($security["domain"]["security"]["sa_alg"] ? $security["domain"]["security"]["sa_alg"] : self::SA_ALG) . " - " . $key
                                                                        : ""
                                                                    );
                }
            } else {
                $res["code"]                                        = $return;
                $res["status"]                                      = "0";
                $res["error"]                                       = "";
            }
        } else {
            $res["status"]                                          = $security["status"];
            $res["error"]                                           = $security["error"];
        }

        if(is_array($res) && $res["status"] !== "0" && $opt["exit"])self::endScript($res);

        if(self::DEBUG && is_array($res))                           $res["exTime"] = Debug::stopWatch();

        return $res;
    }
    /**
     * @param null $request
     * @param null $opt
     * @return array|mixed|null
     */
    public static function registration($request = null, $opt = null) {
        if(self::DEBUG)                                             { Debug::startWatch(); }

        if(is_array($request) && !$opt) {
            $opt                                                    = $request;
            $request                                                = null;
        }
        $opt                                                        = self::getOpt($opt);

        //$res                                                        = self::isInvalidReqMethod($opt["exit"]);
        //if(!$res) {
        $use_activation                                             = true;
        $create_token                                               = false;

        $username                                                   = self::getReq("username");
        $password                                                   = self::getReq("password");

        if($username && $password) {
            if(!$opt["domain"])                                     $opt["domain"] = self::getReq("domain");

            $security                                               = self::security($opt);
            if(isset($security["status"]) && $security["status"] === "0") {
                $validator["username"]                              = self::validate($username, "username", array("security" => $security));
                $validator["password"]                              = self::validate($password, "password", array("security" => $security));
                if($validator["username"]["status"] === "0" && $validator["password"]["status"] === "0") {
                    $model                                          = self::getReqBySchema($opt["model"]);

                    $email                                          = $model["select"]["anagraph.email"];
                    $tel                                            = $model["select"]["anagraph.tel"];

                    $validator["email"]                             = ($email
                                                                        ? self::validate($email, "email", array("security" => $security))
                                                                        : array("status" => "0", "error" => "")
                                                                    );
                    $validator["tel"]                               = ($tel
                                                                        ? self::validate($tel, "tel", array("security" => $security))
                                                                        : array("status" => "0", "error" => "")
                                                                    );

                    if($validator["email"]["status"] === "0" && $validator["tel"]["status"] === "0") {
                        if($opt["model"]) {
                            $registration                           = Anagraph::getInstanceNoStrict("domain")->read(array(
                                                                        "registration.ID_group"
                                                                        , "registration.token"
                                                                        , "registration.activation"
                                                                    ), array(
                                                                        "registration.ID_domain"            => $security["domain"]["ID"]
                                                                        , "registration.anagraph_type"      => $opt["model"]
                                                                    ));
                            if($registration["ID_group"]) {
                                $groups                             = Anagraph::getInstanceNoStrict("access")->read(array(
                                                                        "groups.ID"
                                                                        , "groups.name"
                                                                    ), array(
                                                                        "groups.ID" => $registration["ID_group"]
                                                                    ));
                            }
                            if(is_array($registration)) {
                                $use_activation                     = $registration["activation"];
                                $create_token                       = $registration["token"];
                            } elseif($registration) {
                                $res["status"]                      = "500";
                                $res["error"]                       = $registration;
                            } else {
                                $res["status"]                      = "404";
                                $res["error"]                       = "model or group not found";
                            }
                        }
                    } else {
                        $res["status"]                              = "400";
                        $res["error"]                               = $validator["email"]["error"] . " " . $validator["tel"]["error"];
                    }
                } else {
                    $res["status"]                                  = "400";
                    $res["error"]                                   = $validator["username"]["error"] . " " . $validator["password"]["error"];
                }

                if(!$res && $security["domain"]["ID"]) {
                    $privacies                                      = Anagraph::getInstanceNoStrict("domain")->read(array(
                                                                        "privacy.*"
                                                                    ), array(
                                                                        "privacy.ID_domain" => $security["domain"]["ID"]
                                                                    ));

                    if(is_array($privacies) && count($privacies)) {
                        if(!isset($privacies[0])){
                            $privacies = array($privacies);
                        }

                        $privacy_req                                = (array) self::getReq("privacy", false);

                        foreach($privacies AS $privacy) {
                            if(!$privacy_req[$privacy["title"]]) {
                                $validator["privacy"][]             = $privacy["title"];
                            } else {
                                // $logs["privacy"][]                  = $privacy;
                            }
                        }
                        if($validator["privacy"]) {
                            $res["status"]                          = "400";
                            $res["error"]                           = "privacy required: " . implode(", ", $validator["privacy"]);
                        }
                    }
                }

                if(!$res) {
                    //create user basic
                    $req["access.users.username"]                   = $username;
                    $req["access.users.username_slug"]              = Util::url_rewrite($username);
                    $req["access.users.password"]                   = $password;
                    $req["access.users.email"]                      = $email;
                    $req["access.users.tel"]                        = $tel;
                    $req["access.users.ID_domain"]                  = $security["domain"]["ID"];
                    $req["access.users.expire"]                     = (int) $registration["expire"];

                    if($groups["ID"]) {
                        //create group
                        $req["access.users.acl"]                    = $groups["ID"];
                        $req["access.users.acl_primary"]            = $groups["name"];
                    }
                    if(!$use_activation) {
                        //skip activation
                        $req["access.users.status"]                 = true;
                    }
                    if($create_token) {
                        //add token to creation
                        $token                                      = Auth::getInstance("token");
                        $req["access.tokens.token"]                 = $token->create(self::APPID . "-" . $security["domain"]["name"] . "-" . $username . "-" . $password);
                        $req["access.tokens.type"]                  = ($security["domain"]["security"]["token_type"]
                                                                        ? $security["domain"]["security"]["token_type"]
                                                                        : $token::TYPE
                                                                    );
                        $req["access.tokens.expire"]                = (isset($security["domain"]["security"]["token_expire"])
                                                                        ? $security["domain"]["security"]["token_expire"]
                                                                        : time() + $token::EXPIRE
                                                                    );
                    }

                    self::doEvent("on_do_registration", $model, $req);

                    //create user, token
                    $anagraph                                       = Anagraph::getInstanceNoStrict();
                    $insert                                         = $anagraph->insert($req);

                    if($insert["user"]) {
                        //create relation user_groups
                        if($insert["user"] && $groups["ID"]) {
                            $anagraph->insert(
                                array(
                                    "access.user_groups.ID_user"    => $insert["user"]
                                    , "access.user_groups.ID_group" => 3
                                )
                            );
                        }

                        if(is_array($model["select"])) {
                            $data                                   = $model["select"];
                            $data["anagraph.ID_user"]               = $insert["user"];
                            $data["anagraph.ID_domain"]             = $security["domain"]["ID"];
                            $data["anagraph.valid_email"]           = 0;
                            $data["anagraph.valid_tel"]             = 0;
                            $data["anagraph.password_alg"]          = $security["domain"]["security"]["pw_hash"];


                            $insert                                 = $anagraph->insert($data);
                            if($insert["anagraph"]) {
                                $created                            = time();
                                $newsletter                         = self::getReq("newsletter", false);

                                if(is_array($newsletter) && count($newsletter)) {
                                    foreach ($newsletter AS $newsletter_key => $newsletter_subscription) {
                                        $anagraph->insert(
                                            array(
                                                "anagraph_newsletter.ID_anagraph"       => $insert["anagraph"]
                                                , "anagraph_newsletter.name"            => $newsletter_key
                                                , "anagraph_newsletter.created"         => $created
                                            )
                                        );
                                    }
                                }

                                if(is_array($logs["privacy"]) && count($logs["privacy"])) {

                                }
                            }
                        }

                        $res["status"]                              = "0";
                        $res["error"]                               = "";
                        if($create_token)                           $res["token"] = array(
                                                                        "name"                                      => $req["access.tokens.token"]
                                                                        , "expire"                                  => $req["access.tokens.expire"]
                                                                    );
                    } else {
                        $res["status"]                              = "410";
                        $res["error"]                               = "";
                    }
                }
            } else {
                $res["status"]                                      = $security["status"];
                $res["error"]                                       = $security["error"];
            }
        } else {
            $res["status"]                                          = "400";
            $res["error"]                                           = "username and password Required";
        }
        //}

        if(is_array($res) && $res["status"] !== "0" && $opt["exit"])self::endScript($res);

        if(self::DEBUG && is_array($res))                           $res["exTime"] = Debug::stopWatch();

        return $res;
	}

	public static function share($scopes = null, $token = null, $opt = null) {
        if(self::DEBUG)                                             { Debug::startWatch(); }

        $res = array();

        if(is_array($res) && $res["status"] !== "0" && $opt["exit"])self::endScript($res);

        if(self::DEBUG && is_array($res))                           $res["exTime"] = Debug::stopWatch();

        return $res;
    }

    public static function join($grantor = null, $scopes = null, $token = null, $opt = null) {
        if(self::DEBUG)                                             { Debug::startWatch(); }

        $opt                                                        = self::getOpt($opt);
        $security                                                   = self::check(
                                                                        $token
                                                                        , array(
                                                                            "exit"                              => $opt["exit"]
                                                                            , "fields" => array(
                                                                                "users.ID"
                                                                                , "users.acl"
                                                                            )
                                                                            , "security"                        => true
                                                                        )
                                                                    );
        if(isset($security["status"]) && $security["status"] === "0") {
            //$security                                               = self::security($opt);
            //if(isset($security["status"]) && $security["status"] === "0") {
            $policy                                                 = Anagraph::getInstanceNoStrict("domain")->read(array(
                                                                        "policy.groups"
                                                                        , "policy.scopes"
                                                                    ), array(
                                                                        "policy.ID_domain"                      => $security["domain"]["ID"]
                                                                        , "policy.ID_group"                     => $security["user"]["acl"]
                                                                    ));
            if(is_array($policy)) {
                $grantor                                            = ($grantor
                                                                        ? $grantor
                                                                        : self::getReq("grantor")
                                                                    );
                $user                                               = Anagraph::getInstanceNoStrict("access")->read(array(
                                                                        "users.ID"
                                                                        , "users.acl"
                                                                    ), array(
                                                                        "tokens.token"                          => $grantor
                                                                    ));
                if(is_array($user)) {
                    if(self::checkScopes($user["acl"], $policy["groups"])) {
                        if(!$scopes)                                $scopes = self::getReq("scopes");
                        if(!$policy["scopes"])                      $policy["scopes"] = $security["domain"]["scopes"];

                        $arrScopesValid                             = self::checkScopes($scopes, $policy["scopes"]);
                        if($arrScopesValid) {
                            $policy_granted                         = Anagraph::getInstanceNoStrict("domain")->read(array(
                                                                        "policy_granted.scope"
                                                                        , "policy_granted.expire"
                                                                    ), array(
                                                                        "policy_granted.ID_domain"              => $security["domain"]["ID"]
                                                                        , "policy_granted.ID_user_trusted"      => $security["user"]["ID"]
                                                                        , "policy_granted.ID_user_shared"       => $user["ID"]
                                                                        , "policy_granted.client_id"            => $security["client"]["ID"]
                                                                    ));
                            if(is_array($policy_granted) || $policy_granted === false) {
                                if(is_array($policy_granted) && count($policy_granted)) {
                                    foreach($policy_granted AS $granted) {

                                    }
                                }



                                $res["status"]                      = "0";
                                $res["error"]                       = "";
                            } else {
                                $res["status"]                      = "500";
                                $res["error"]                       = $policy_granted;
                            }
                        } else {
                            $res["status"]                          = "403";
                            $res["error"]                           = "Scope not Permitted" . (self::DEBUG
                                                                        ? ": " . implode(self::diffScopes($scopes, $policy["scopes"]), ", ")
                                                                        : ""
                                                                    );
                        }
                    } else {
                        $res["status"]                              = "403";
                        $res["error"]                               = "Policy Group not Permitted" . (self::DEBUG
                                                                        ? ": " . implode(self::diffScopes($user["acl"], $policy["groups"]), ", ")
                                                                        : ""
                                                                    );
                    }
                } else {
                    $res["status"]                                  = "410";
                    $res["error"]                                   = "Grantor not Found";
                }
            } elseif($policy === false) {
                $res["status"]                                      = "401";
                $res["eror"]                                        = "Policy not Found";
            } else {
                $res["status"]                                      = "500";
                $res["error"]                                       = $policy;
            }
            //}
        } else {
            $res["status"]                                          = $security["status"];
            $res["error"]                                           = $security["error"];
        }

        if(is_array($res) && $res["status"] !== "0" && $opt["exit"])self::endScript($res);

        if(self::DEBUG && is_array($res))                           $res["exTime"] = Debug::stopWatch();

        return $res;
    }

	public static function key($scopes = null, $token = null, $opt = null) {
        if(self::DEBUG)                                             { Debug::startWatch(); }

        $opt                                                        = self::getOpt($opt);
        if(!$scopes)                                                $scopes = self::getReq("scopes");

        if($scopes) {
            $security                                               = self::check(
                                                                        $token
                                                                        , array(
                                                                            "exit"                  => $opt["exit"]
                                                                            , "fields"              => array(
                                                                                "users.ID"
                                                                                , "users.acl_primary"
                                                                            )
                                                                            , "scopes"              => $scopes
                                                                            , "security"            => true
                                                                            , "method"              => "token"
                                                                        )
                                                                    );

            if(isset($security["status"]) && $security["status"] === "0") {
                //$security                                           = self::security($opt, $scopes);
                //if(isset($security["status"]) && $security["status"] === "0") {
                    /*$select                                         = ($opt["fields"]
                                                                        ? $opt["fields"]
                                                                        : self::getData()
                                                                    );*/
                $select[]                                           = "ID";
                $opt["model"]                                       = $security["user"]["acl_primary"];

                $anagraph                                           = self::getAnagraphByUser($security["user"]["ID"], $opt["model"], $select);

                //status 1 e 0 nella where se funziona correttamnte
                //fare un read partendo da anagraph e discendendo nei sotto elementi access e domain
                // verificare in insert expire che venga scritto correttamente

                //usare per il certificato questo openssl_csr_new
                //trovare sistema per la get con le chiavi con i punti per la registrazione
                if(is_array($anagraph)) {
                    if($anagraph["ID"]) {
                        $mc                                         = self::mergeKD(
                                                                        self::encipherKD(
                                                                            self::APPID
                                                                            , $anagraph["ID"]
                                                                            , $security["certificate"]
                                                                        )
                                                                        , $scopes
                                                                        , $security["certificate"]
                                                                    );

                        if($mc) {
                            $res                                    = $mc;

                            unset($anagraph["ID"]);
                            if(is_array($anagraph) && count($anagraph)) { $res["user"] = $anagraph; }
                            if($security["token"]["expire"] < 0)    { $res["token"] = $security["token"]; }

                            $res["status"]                          = "0";
                            $res["error"]                           = "";
                        } else {
                            $res["status"]                          = "410";
                            $res["error"]                           = "Unknow Error";
                        }
                    } else {
                        $res["status"]                              = "404";
                        $res["error"]                               = "Unknow User";
                    }
                } else {
                    $res["status"]                                  = "500";
                    $res["error"]                                   = "Unknow Anagraph";
                }
                //} else {
                //    $res                                            = $security;
                //}

            } else {
                $res["status"]                                      = $security["status"];
                $res["error"]                                       = $security["error"];
            }
        } else {
            $res["status"]                                          = "400";
            $res["error"]                                           = "Scope not Set";
        }

        if(is_array($res) && $res["status"] !== "0" && $opt["exit"])self::endScript($res);

        if(self::DEBUG && is_array($res))                           $res["exTime"] = Debug::stopWatch();

        return $res;
    }

    public static function verify($key = null, $opt = null) {
        if(self::DEBUG)                                             { Debug::startWatch(); }

        $opt                                                        = self::getOpt($opt);
        $security                                                   = self::security($opt);
        if(isset($security["status"]) && $security["status"] === "0") {
            $key                                                    = ($key
                                                                        ? $key
                                                                        : self::getReq("key")
                                                                    );

            $user                                                   = self::getUserByBearer($key);
            if(isset($user["status"]) && $user["status"] === "0") {
                $res["status"]                                      = "0";
                $res["error"]                                       = "";
            } else {
                $res["status"]                                      = $user["status"];
                $res["error"]                                       = $user["error"];
            }
        } else {
            $res["status"]                                          = $security["status"];
            $res["error"]                                           = $security["error"];
        }
        if(is_array($res) && $res["status"] !== "0" && $opt["exit"])self::endScript($res);

        if(self::DEBUG && is_array($res))                           $res["exTime"] = Debug::stopWatch();

        return $res;
    }


    public static function validate($value, $type, $opt = null) {
        if(self::DEBUG)                                             { Debug::startWatch(); }

        $opt                                                        = self::getOpt($opt);
        $security                                                   = self::security($opt);
        if(isset($security["status"]) && $security["status"] === "0") {
            if($value) {
                switch($type) {
                    case "username";
                        $invalid                                    = Cms::getInstance("validator")->invalidUsername($value);
                        if(!$invalid) {
                            $count                                  = Anagraph::getInstanceNoStrict("access")->cmd("count", array(
                                                                        "users.username" => $value
                                                                    ));
                            if($count["user"])                      { $error = "username already exist"; }
                        } else {
                            $error                                  = $invalid;
                        }
                        break;
                    case "password";
                        $invalid                                    = Cms::getInstance("validator")->invalidPassword($value);
                        if($invalid)                                { $error = $invalid; }
                        break;
                    case "email";
                        if(Cms::getInstance("validator")->isEmail($value)) {
                            $count                                  = Anagraph::getInstanceNoStrict("access")->cmd("count", array(
                                                                        "users.email" => $value
                                                                    ));
                            if($count["user"])                      { $error = "email already exist"; }
                        } else {
                            $error                                  = "incorrect email";
                        }
                        break;
                    case "tel";
                        if(Cms::getInstance("validator")->isTel($value)) {
                            $count                                  = Anagraph::getInstanceNoStrict("access")->cmd("count", array(
                                                                        "users.tel" => $value
                                                                    ));
                            if($count["user"])                      { $error = "tel already exist"; }
                        } else {
                            $error                                  = "incorrect tel";
                        }
                        break;
                    default:
                }
            } else {
                $error                                              = $type . " empty";
            }
            if($error) {
                $res["status"]                                      = "400";
                $res["error"]                                       = $error;
            } else {
                $res["status"]                                      = "0";
                $res["error"]                                       = "";
            }
        } else {
            $res["status"]                                          = $security["status"];
            $res["error"]                                           = $security["error"];
        }

        if(is_array($res) && $res["status"] !== "0" && $opt["exit"])self::endScript($res);

        if(self::DEBUG && is_array($res))                           $res["exTime"] = Debug::stopWatch();

        return $res;
    }

    public static function users($token = null, $opt = null) {
        if(self::DEBUG)                                             { Debug::startWatch(); }
//todo: da gestire lista utenti discriminati per token
        $opt                                                        = self::getOpt($opt);

        $security                                                   = self::security($opt);
        if(isset($security["status"]) && $security["status"] === "0") {
            $model                                                  = self::getReqBySchema($opt["model"]);

            $anagraph                                               = Anagraph::getInstanceNoStrict()->read(
                                                                        $model["select"]
                                                                        , $model["where"]
                                                                        , $model["order"]
                                                                        , $model["limit"]
                                                                    );

/*print_r($opt["fields"]);
print_r($anagraph);
die();*/
            if(is_array($anagraph)) {
                unset($anagraph["exTime"]);
                $res["users"]                                       = $anagraph;
                $res["status"]                                      = "0";
                $res["error"]                                       = "";
            } elseif($anagraph === false) {
                $res["users"]                                       = array();
                $res["status"]                                      = "0";
                $res["error"]                                       = "";
            } else {
                $res["status"]                                      = "500";
                $res["error"]                                       = $anagraph;
            }
        } else {
            $res["status"]                                          = $security["status"];
            $res["error"]                                           = $security["error"];
        }

        if(is_array($res) && $res["status"] !== "0" && $opt["exit"])self::endScript($res);
        //}

        if(self::DEBUG && is_array($res))                           $res["exTime"] = Debug::stopWatch();

        return $res;
    }

    public static function createCertificate($secret = null, $opt = null) {
        if(self::DEBUG)                                             { Debug::startWatch(); }

        $opt                                                        = self::getOpt($opt);

        $res                                                        = self::isInvalidReqMethod($opt["exit"]);
        if(isset($res["status"]) && $res["status"] === "0") {
            $secret                                                 = ($secret
                                                                        ? $secret
                                                                        : self::getReq("password")
                                                                    );

            $domain                                                 = self::getDomain(
                                                                        $opt["domain"]
                                                                        , $opt["client_id"]
                                                                        , $opt["client_secret"]
                                                                    );

            if(isset($domain["status"]) && $domain["status"] === "0") {
                require_once __DIR__ . "/AuthCertificate.php";

                $certificate                                        = new AuthCertificate(self::domain4certificate($domain));

                $res                                                = $certificate->createCertificate($secret);

                unset($secret);

            } else {
                $res                                                = $domain;
            }
        }

        if(is_array($res) && $res["status"] !== "0" && $opt["exit"]){ self::endScript($res); }

        if(self::DEBUG && is_array($res))                           { $res["exTime"] = Debug::stopWatch(); }

        return $res;
    }
    public static function password($password = null, $hash = null, $alg = null) {
        if($password == "random" && !$hash && !$alg) {
            return self::randomPassword();
        }
        if(!$password) {
            $password = Auth::APPID . "-" . Stats::getVisitor("unique"); //todo: da togliere
        }
        if(!$alg) {
            $alg = PASSWORD_DEFAULT;
        }

        return ($hash
            ? password_verify($_SERVER["SERVER_ADDR"] . $password, $hash)
            : password_hash($_SERVER["SERVER_ADDR"] . $password, $alg)
        );
    }
    private static function randomPassword($length = 5) {
        $result ="";
        $chars = 'bcdfghjklmnpqrstvwxyzaeiou';
        $delimeters  = '-_!@%-.#';

        for ($x = 0; $x < $length; $x++)
        {
            $result .= ($x%2) ? $chars[mt_rand(19, 23)] : $chars[mt_rand(0, 18)];
        }
        $result.=mt_rand(10,99);

        $result = substr_replace($result, substr($delimeters, mt_rand(0, 7), 1), mt_rand(1, $length), 0);

        return $result;
    }
    public static function env($name = null, $value = null)
    {
        if(!$value && self::isLogged()) {
            $res = Auth::getInstance("session")->userPermissions($name);
        }

        if(!$res) {
            $res = parent::env($name, $value);
        }
        return $res;
    }

    public static function isLogged($acl = null) {
        if($acl && $acl != Auth::GUEST_GROUP_ID) {
            self::$isLogged = $acl;
        }

        return self::$isLogged;
    }
    public static function isGuest($ID_user = null) {
        $user = Auth::get("user", array("ID_user" => $ID_user));

        return (!$user->acl || $user->acl_primary == Auth::GUEST_GROUP_NAME
            ? true
            : false
        );
    }

    public static function isAdmin($ID_user = null) {
        $user = Auth::get("user", array("ID_user" => $ID_user));

        return ($user->username == Auth::SUPERADMIN_USERNAME
            ? true
            : false
        );
    }

    public static function getProfiles($key = null) {
        if(!self::$profiles) {
            $fs                                                     = Filemanager::getInstance("xml");
            $profiling                                              = new DirectoryIterator(self::$disk_path . self::PROFILE_PATH);

            self::doEvent("on_get_profiles", self::$profiles);

            foreach ($profiling as $profile) {
                if ($profile->isDot()) {
                    continue;
                }
                $name                                               = $profile->getBasename(".xml");
                $xml                                                = $fs->read($profile->getPathname());

                self::loadProfile($xml, $name);
            }
        }

        return ($key
            ? self::$profiles[$key]
            : self::$profiles
        );
    }

    public static function loadProfile($data, $name) {
        $rules = ($data["rules"]["rule"][0]
            ? $data["rules"]["rule"]
            : $data["rules"]
        );

        if(is_array($rules) && count($rules)) {
            foreach ($rules AS $rule) {
                $rule_attr = $rule["@attributes"];
                $path = $rule_attr["path"];

                self::$profiles[$name]["rules"][$path] = array(
                    "own" => (
                        ($rule_attr["view_own"]          ? 1 : 0)
                        + ($rule_attr["modify_own"]      ? 2 : 0)
                        + ($rule_attr["insert_own"]      ? 4 : 0)
                        + ($rule_attr["delete_own"]      ? 8 : 0)
                    )
                , "others" => (
                        ($rule_attr["view_others"]       ? 1 : 0)
                        + ($rule_attr["modify_others"]   ? 2 : 0)
                        + ($rule_attr["insert_others"]   ? 4 : 0)
                        + ($rule_attr["delete_others"]   ? 8 : 0)
                    )
                );
            }
        }

        if(is_array($data["env"]) && count($data["env"])) {
            foreach ($data["env"] as $key => $value) {
                self::$profiles[$name]["env"][$key] = Filemanager::getAttr($value);
            }
        }
    }

    public static function set($value, $key = null) {
        if(self::isLogged()) {
            $set = ($key
                ? array($key => (array) $value)
                : (array) $value
            );

            Auth::getInstance("session")->userInfo($set);
        }
    }

    public static function get($key = null, $opt = null) {
        $opt                                                        = self::getOpt($opt);

        if($opt["ID_user"]) {
            $select                                                 = array(
                                                                        "anagraph.*"
                                                                        , "access.users.*"
                                                                        , "access.groups.*"
                                                                        , "access.tokens.token" => "name"
                                                                        , "access.tokens.expire"
                                                                        , "access.tokens.type"
                                                                    );
            $anagraph                                               = Auth::getAnagraphByUser($opt["ID_user"], $opt["model"], $select);
        } elseif(self::isLogged()) {
            $anagraph                                               = Auth::getInstance("session")->userInfo();
        } else {
            $anagraph                                               = self::getUserDefault();
        }

        $res                                                        = ($key && $key != "anagraph"
                                                                        ? $anagraph[$key]
                                                                        : $anagraph
                                                                    );

        switch($key) {
            case "user":
                if($anagraph["person"]) {
                    $res = array_replace($res, $anagraph["person"]);
                }
                if($anagraph["company"]) {
                    $res = array_replace($res, $anagraph["company"]);
                }
                break;
            default:
        }

        return (!$opt["toArray"] && $key && !isset($res[0])
            ? (object) (is_array($res)
                ? array_change_key_case($res)
                : $res
            )
            : $res
        );
    }

    public static function getAnagraphByUser($ID_user, $ext = null, $select = null) {
        $model                                                      = self::getReqBySchema($ext, $select);
        if($model["select"]) {
            $anagraph                                                   = Anagraph::getInstanceNoStrict()->read(
                                                                            $model["select"]
                                                                            , array(
                                                                                "ID_user"       => $ID_user
                                                                            )
                                                                        );

            if(is_array($anagraph["user"]) && !$anagraph["user"]["acl_primary"]) {
                $anagraph["user"]                                       = array_replace($anagraph["user"], self::getUserDefault("user"));
            }
        }
        return $anagraph;
    }
    public static function setUser($permission = null) {
        if(self::isLogged()) {
            $anagraph                                               = Auth::getInstance("session")->userInfo($permission);
        }

        return $anagraph;
    }

    public static function getUserAvatar($opt = null, $avatar = null) {
        if(!is_array($opt) && $opt) {
            $opt                                                    = array("mode" => $opt);
        }

        if(!$opt["mode"]) {
            $opt["mode"]                                            = Auth::AVATAR_MODE;
        }

        if($avatar === null) {
            $anagraph                                               = self::get();
            $avatar                                                 = ($anagraph["avatar"]
                                                                        ? $anagraph["avatar"]
                                                                        : $anagraph["user"]["avatar"]
                                                                    );
        }
        if(!$avatar) {
            $avatar                                                 = ($opt["noavatar"]
                                                                        ? $opt["noavatar"]
                                                                        : ffMedia::THEME_DIR . "/" . ffMedia::THEME_DEFAULT . "/images/noavatar.svg"
                                                                    );
            Auth::doEvent("on_no_avatar", $avatar);
        }

        return ffMedia::getUrl($avatar, $opt["mode"], "url");
    }


    public static function log($what, $user, $security) {
        if(self::DEBUG)                                             { Debug::startWatch(); }

        $res                                                        = null;
        switch($what) {
            case "device":
                $device = self::logDevice($user, $security);
            case "access":
                self::logAccess($user, $security, $device);
                break;
            default:
        }

        if(self::DEBUG && is_array($res))                           $res["exTime"] = Debug::stopWatch();

        return $res;
    }

    private static function logDevice($user, $security) {
//browser
//geolocalization

        $device                                                     = Anagraph::getInstanceNoStrict("access")->read(
                                                                        array(
                                                                            "devices.ID"
                                                                            , "devices.name"
                                                                            , "devices.type"
                                                                            , "devices.ips"
                                                                        )
                                                                        , array(
                                                                            "devices.client_id"     => $security["domain"]["client"]["client_id"]
                                                                            , "devices.name"        => ""
                                                                            , "devices.type"        => ""
                                                                            , "devices.ID_user"     => $user["ID"]
                                                                        )
                                                                    );
        if(is_array($device)) {
            $ips                                                    = array_fill_keys(explode(",", $device["ips"]), true);
            $ips[$_SERVER["REMOTE_ADDR"]]                           = true;
            $ips                                                    = implode(",", array_keys($ips));

            $device["new"]                                          = false;
            $update                                                 = Anagraph::getInstanceNoStrict("access")->update(
                                                                        array(
                                                                            "devices.last_update"   => time()
                                                                            , "devices.ips"         => $ips
                                                                            , "devices.hits"        => "++"
                                                                        )
                                                                        , array(
                                                                            "devices.ID"            => $device["ID"]
                                                                        )
                                                                    );

            $res["device"]                                          = $device;
        } elseif(!$device) {
            //$device                                               = Logs::getDevice();

            $device["client_id"]                                    = $security["domain"]["client"]["client_id"];
            $device["ID_user"]                                      = $user["ID"];
            $device["new"]                                          = true;

            $insert                                                 = Anagraph::getInstanceNoStrict("access")->insert(array(
                                                                        "devices.client_id"         => $device["client_id"]
                                                                        , "devices.ID_user"         => $device["ID_user"]
                                                                        , "devices.name"            => ""//$device["name"]
                                                                        , "devices.type"            => ""//$device["type"]
                                                                        , "devices.last_update"     => time()
                                                                        , "devices.hits"            => "1"
                                                                        , "devices.ips"             => $_SERVER["REMOTE_ADDR"]
                                                                    ));

            $res["device"]                                          = $device;
        } else {
            $res["status"]                                          = "500";
            $res["error"]                                           = $device;
        }

        return $res;
    }

    private static function logAccess($user, $security, $device) {

    }

    private static function getUserDefault($key = null) {
        $anagraph["user"]["acl"]                                = Auth::GUEST_GROUP_ID;
        $anagraph["user"]["acl_primary"]                        = Auth::GUEST_GROUP_NAME;

        return ($key
            ? $anagraph[$key]
            : $anagraph
        );
    }

    private static function security($opt) {
        switch(self::SECURITY_LEVEL) {
            case "0"; //no security
                break;
            case "1"; //no Client
                break;
            case "2"; //no Domain
                break;
            case "4"; //no Certificate
                break;
            case "7"; //max Security
            default:
        }
        $res                                                        = self::isInvalidReqMethod($opt["exit"]);
        if(isset($res["status"]) && $res["status"] === "0") {
            $schema                                                 = self::schema(null, "domains");
            $domain_name                                            = ($schema["alias"][$opt["domain"]]
                                                                        ? $schema["alias"][$opt["domain"]]
                                                                        : $opt["domain"]
                                                                    );
            $domain                                                 = self::getDomain(
                                                                        $domain_name
                                                                        , $opt["client_id"]
                                                                        , $opt["client_secret"]
                                                                        , $opt["scopes"]
                                                                    );

            if(self::getPathInfo(self::API_PATH)) {
                if(isset($domain["status"]) && $domain["status"] === "0") {
                    $res                                                = self::getCertificate(self::domain4certificate($domain), $domain["secret"]);
                    if(isset($res["status"]) && $res["status"] === "0") {
                        unset($domain["status"]);
                        unset($domain["error"]);
                        $res["domain"]                                  = $domain;
                    }
                } else {
                    $res                                                = $domain;
                }
                unset($domain);
            } else {
                unset($domain["status"]);
                unset($domain["error"]);
                $res["domain"]                                          = $domain;
            }
        }

        return $res;
    }

    private static function loginSuccess($user, $opt) {
        //check_function("analytics"); //todo: da far diventare oggetto

        switch($opt["method"]) {
            case "token":
                $anagraph                               = self::getAnagraphByUser($user["ID"], $opt["model"]);

                if($user["email"] && !$anagraph["email"])   { $anagraph["email"] = $user["email"]; }
                if($user["tel"] && !$anagraph["tel"])       { $anagraph["tel"] = $user["tel"]; }
                if($user["acl_primary"])                    { $anagraph["group"] = $user["acl_primary"]; }

          //      analytics_set_event('/login', "By Token");
                break;
            case "session":
            default:
            //    analytics_set_event('/login', "By Session");
        }



        if(!$user["last_login"]) { //todo:da eliminare analitics e inserire in cms::getInstance("analitics")
            analytics_set_event('/registrazione/first-login', "Step 3 - First login");
        }
        /**
         * todo: da aggiungere i log
         */
        Anagraph::getInstanceNoStrict("access")->update(
            array(
                "last_login" => time()
            )
            , array(
                "ID" => $user["ID"]
            )
        );

        Auth::doEvent("on_logged_in", $user, $opt);

        return $anagraph;
    }
    private static function domain4certificate($domain) {
        $cDomain                                                    = $domain;
        $cDomain["secret"]                                          = md5($domain["secret"]
            ? $domain["secret"]
            : self::APPID
        );

        return $cDomain;
    }
    protected static function schema($type = null, $key = null) {
        $schema                                                     = parent::schema();
        $def                                                        = array(
                                                                        "username"          => "access.users.username"
                                                                        , "password"        => "access.users.password"
                                                                        , "status"          => "access.users.status"
                                                                        , "email"           => array(
                                                                                                "access.users.email"
                                                                                                , "anagraph.email"
                                                                                            )
                                                                        , "tel"             => array(
                                                                                                "access.users.tel"
                                                                                                , "anagraph.tel"
                                                                                            )
                                                                        , "type"            => array(
                                                                                                "anagraph_type.name"
                                                                                            )
                                                                        , "group"           => array(
                                                                                                "access.groups.name"
                                                                                            )
                                                                        , "name"            => array(
                                                                                                "anagraph.name"
                                                                                            )
                                                                       /* , "custom1"         => "anagraph.custom1"
                                                                        , "custom2"         => "anagraph.custom2"
                                                                        , "custom3"         => "anagraph.custom3"
                                                                        , "custom4"         => "anagraph.custom4"
                                                                        , "custom5"         => "anagraph.custom5"
                                                                        , "custom6"         => "anagraph.custom6"
                                                                        , "custom7"         => "anagraph.custom7"
                                                                        , "custom8"         => "anagraph.custom8"
                                                                        , "custom9"         => "anagraph.custom9"*/
                                                                    );

        $model                                                      = $schema["models"][$type];
        $model["group"]                                             = (is_array($schema["models"][$type])
                                                                        ? $type
                                                                        : null
                                                                    );
        $model["fields"]                                            = (is_array($schema["models"][$type]["fields"])
                                                                        ? array_replace($def, $schema["models"][$type]["fields"])
                                                                        : $def
                                                                    );
        $model["domains"]                                           = $schema["domains"];

        return ($key
            ? $model[$key]
            : $model
        );
    }

    private static function getReqBySchema($ext = null, $select = null) {
        $rules                                                      = self::schema($ext);
        $rules["request_method"]                                    = self::REQUEST_METHOD;
        $rules["mapping"]                                           = array_fill_keys(self::$request, "security");

        $req                                                        = self::getRequest($rules, "query");
        $req["select"]                                              = array_replace($req["select"], (array) $select);
        $req["group"]                                               = $rules["group"];

        return $req;
    }
    private static function getCertificate($domain, $secret) {
        static $res                                                 = null;

        if($res === null) {
            if($domain) {
                if(!$domain["client"]["disable_csrf"]
                    && $domain["security"]["csr_url"]
                    && $domain["security"]["pkey_url"]
                ) {
                    require_once __DIR__ . "/AuthCertificate.php";

                    $certificate                                    = new AuthCertificate($domain);

                    $res                                            = $certificate->get($secret);

/*                    $return                                         = $certificate->get($secret);
                    if(isset($return["status"]) && $return["status"] === "0") {
                        $res["certificate"]                         = $certificate;
                        $res["status"]                              = "0";
                        $res["error"]                               = "";
                    } else {
                        $res                                        = $return;
                    }

                    unset($tmp);
*/
                } else {
                    $res["status"]                                  = "0";
                    $res["error"]                                   = "";
                }
            } else {
                $res["status"]                                      = "400";
                $res["error"]                                       = "Certicate Domain Missing";
            }
        }

        unset($secret);

        return $res;
    }
    private static function getOpt($opt = null) {
        $external                                                   = self::getPathInfo(self::API_PATH);
        if(!$opt)                                                   { $opt = array(); }
        self::$opt["exit"]                                          = $external;

        $opt                                                        = array_replace(self::$opt, $opt);

        foreach(self::$headers AS $key => $req) {
            if($_SERVER["HTTP_" . $req]) {
                $opt[$key]                                          = $_SERVER["HTTP_" . $req];
            }
        }

        if($external && !$opt["method"])                            { $opt["method"] = "token"; }
        if($external && $opt["csrf"])                               { $opt["method"] = "session"; }

        return $opt;
    }

    private static function combineKL($a /*unique id */, $b /* suffix */, $certificate) {
        $kl                                                         = ($certificate["kl"]
                                                                        ? $certificate["kl"]
                                                                        : self::CERTIFICATE_KL
                                                                    );
        if($kl) {
            $dsk                                                    = $a;
            $bn                                                     = crc32($b);

            $kln                                                    = chunk_split($kl, 3 * 2, "|");
            $arrKL                                                  = explode("|", $kln);
            foreach($arrKL AS $coord) {
                $arrCoord                                           = explode("|", chunk_split($coord, 3, "|"));

                $dsk                                                = substr_replace(
                                                                        $dsk
                                                                        , substr(
                                                                            $bn
                                                                            , ($arrCoord[1] >= strlen($bn)
                                                                                ? round($arrCoord[1] / strlen($bn), 0)
                                                                                : intval($arrCoord[1])
                                                                            )
                                                                            , 1
                                                                        )
                                                                        , $arrCoord[0]
                                                                        , 0
                                                                    );
            }

            return $dsk;
        }
    }
    private static function mergeKD($ask, $d, $certificate) {
        if($ask) {
            $arrD                                                   = explode(",", $d);
            $s                                                      = ($certificate["secret"]
                                                                        ? $certificate["secret"]
                                                                        : md5(self::APPID)
                                                                    );
            foreach($arrD AS $b) {
                $dask                                               = self::combineKL($ask, $b, $certificate);
                if($dask && $s) {
                    $res[$b]                                        = self::shuffle($dask, $s);
                }
            }
        }

        return $res;
    }
    private static function encipherKD($a /* prefix */, $b /*unique id*/, $certificate) {
        $kd                                                         = ($certificate["kd"]
                                                                        ? $certificate["kd"]
                                                                        : self::CERTIFICATE_KD
                                                                    );
        $p                                                          = ($certificate["kp"]
                                                                        ? $certificate["kp"]
                                                                        : self::CERTIFICATE_KP
                                                                    );
        if($kd) {
            $auk                                                    = array();
            $an                                                     = crc32($a);

            $arrAN                                                  = str_split($an);
            $arrKD                                                  = str_split($kd);
            $kdnP                                                   = strlen($kd) - $p;
            $bn                                                     = (strlen($b)> $kdnP
                                                                        ? chunk_split($b, $kdnP, "|")
                                                                        : $b
                                                                    );
            $arrBN                                                  = explode("|", $bn);
            foreach($arrBN AS $bnp) {
                $collision                                          = 0;
                $arrB                                               = str_split($bnp);
                foreach ($arrKD AS $index) {
                    if (isset($arrB[$index])) {
                        $auk[]                                      = $arrB[$index];
                    } elseif (isset($arrAN[$index])) {
                        $auk[]                                      = $arrAN[$index];
                    } else {
                        $auk[]                                      = $index;
                    }

                    if($arrB[$index] == $arrAN[$index] || $arrB[$index] == $index) {
                        $collision++;
                    }
                }
                if($collision) {
                    $arrC = array();
                    $n = 9;
                    for ($i = 1; $i <= $p; $i++) {
                        $arrC[]                                     = array_search($n, $arrKD);
                        $n--;
                    }
                    $arrC                                           = array_merge($arrC, $arrC, $arrC, $arrC, $arrC);
                    for ($i = 0; $i < $collision; $i++) {
                        $c                                          = $auk[$arrC[$i]] + strlen($bnp);
                        if($c > 9)                                  $c = $c - 10;
                        $auk[$arrC[$i]]                             = $c;
                    }
                }


            }

            return implode("", $auk);
        }
    }

    private static function shuffle($k /* base to shuffle */, $s /* scope */) {
        $i                                                          = 0;
        $sn                                                         = "";
        $arrS                                                       = str_split($s);
        foreach($arrS AS $char) {
            $sn                                                     .= ord($char);
        }

        $arrSN                                                      = str_split($sn);
        $arrK                                                       = str_split($k);
        foreach($arrSN AS $pos) {
            if($pos >= count($arrK)) {
                $pos                                                = $pos - count($arrK);
            }
            self::arrMove($arrK, $pos, $i);

            $i++;
            if(count($arrK) == $i)                                  $i = 0;
        }

        return implode("", $arrK);
    }

    private static function arrMove(&$array, $a, $b) {
        $out                                                        = array_splice($array, $a, 1);
        array_splice($array, $b, 0, $out);
    }

    private static function getClient($client_id = null, $client_secret = null) {
        if(self::SECURITY_LEVEL & 1 && self::getPathInfo(self::API_PATH)) {
            if(!$client_id && !$client_secret) {
                $opt                                                = self::getOpt();

                if($opt["csrf"] && Auth::password(null, $opt["csrf"])) {
                    $res["status"]                                  = "0";
                    $res["error"]                                   = "Internal Called";
                }

                $client_id                                          = $opt["client_id"];
                $client_secret                                      = $opt["client_secret"];
            }

            if(!$res) {
                if($client_id && $client_secret) {
                    if(self::$cache["clients"][$client_id] !== NULL) {
                        $client                                         = self::$cache["clients"][$client_id];
                    } else {
                        $client                                         = Anagraph::getInstanceNoStrict("domain")->read(array(
                                                                            "clients.client_id"
                                                                            , "clients.domains"
                                                                            , "clients.disable_csrf"
                                                                            , "clients.grant_types"
                                                                        ), array(
                                                                            "clients.client_id"         => $client_id
                                                                            , "clients.client_secret"   => $client_secret
                                                                        ));
                        self::$cache["clients"][$client_id]             = $client;
                    }
                    if(is_array($client)) {
                        $res                                            = $client;
                        $res["status"]                                  = "0";
                        $res["error"]                                   = "";
                    } else {
                        $res["status"]                                  = "410";
                        $res["error"]                                   = "Client not Found";
                    }
                } else {
                    $res["status"]                                      = "400";
                    $res["error"]                                       = "missing client_id or client_secret";
                }
            }
        } else {
            $res["status"]                                          = "0";
            $res["error"]                                           = "";
        }

        return $res;
    }

    public static function getDomain($domain_key = null, $client_id = null, $client_secret = null, $scopes = null) {
        $client                                                     = self::getClient($client_id, $client_secret);
        if(isset($client["status"]) && $client["status"] === "0") {
            if(self::SECURITY_LEVEL & 2) {
                if($domain_key) {
                    if(self::$cache["domains"][$domain_key] !== null) {
                        $domain                                         = self::$cache["domains"][$domain_key];
                    } else {
                        if(is_numeric($domain_key)) {
                            $domain_where                                   = array(
                                                                                "ID" => $domain_key
                                                                            );
                        } else {
                            $domain_where                                   = array(
                                                                                "name" => $domain_key
                                                                            );
                        }
                        /*$domain_select                                      = array(
                                                                                "ID"
                                                                                , "name"
                                                                                , "expire"
                                                                                , "status"
                                                                                , "scopes"
                                                                                , "secret"
                                                                                , "company_name"            //=> "company.name"
                                                                                , "company_description"     //=> "company.description"
                                                                                , "company_state"           //=> "company.state"
                                                                                , "company_province"        //=> "company.province"
                                                                                , "company_city"            //=> "company.city"
                                                                                , "company_email"           //=> "company.email"
                                                                            );

                        if(1 || self::getPathInfo(self::API_PATH)) {*/
                            $domain_select                                  =  array(
                                                                                "ID"
                                                                                , "name"
                                                                                , "expire"
                                                                                , "status"
                                                                                , "scopes"
                                                                                , "secret"
                                                                                , "company_name"            //=> "company.name"
                                                                                , "company_description"     //=> "company.description"
                                                                                , "company_state"           //=> "company.state"
                                                                                , "company_province"        //=> "company.province"
                                                                                , "company_city"            //=> "company.city"
                                                                                , "company_email"           //=> "company.email"
                                                                                , "security.csr_url"       // => "pem.url"
                                                                                , "security.csr_ip"         //=> "pem.ip"
                                                                                , "security.csr_protocol"   //=> "pem.protocol"

                                                                                , "security.pkey_url"       //=> "key.url"
                                                                                , "security.pkey_ip"        //=> "key.ip"
                                                                                , "security.pkey_protocol"  //=> "key.protocol"

                                                                                , "security.cert_expire"    //=> "cert.expire"
                                                                                , "security.cert_alg"       //=> "cert.alg"
                                                                                , "security.cert_id_length" //=> "cert.id_length"
                                                                                , "security.cert_key_length"//=> "cert.key_length"
                                                                                , "security.cert_precision" //=> "cert.precision"

                                                                                , "security.token_expire"   //=> "token.expire"
                                                                                , "security.token_type"     // => "token.type"
                                                                                , "security.sa_alg"         // => "fa.alg"
                                                                                , "security.sa_expire"      // => "fa.expire"
                                                                                , "security.sa_sender"      // => "fa.sender"
                                                                                , "security.sa_human"      // => "fa.human"
                                                                                , "security.pw_hash"        // => "pw.hash"
                                                                                , "security.pw_validator"   // => "pw.validator"
                                                                            );
                        //}
                        $domain                                         = Anagraph::getInstanceNoStrict("domain")->read(
                                                                            $domain_select
                                                                            , $domain_where
                                                                        );

                        self::$cache["domains"][$domain_key]            = $domain;
                    }
                    if(is_array($domain)) {
                       // $domain["security"]["pkey_url"] = $domain["security"]["csr_url"];
                       // $domain["security"]["pkey_ip"] = $domain["security"]["csr_ip"];
                      //  $domain["security"]["pkey_protocol"] = $domain["security"]["csr_protocol"];

                        $valid_domain                               = true;
                        if($client["domains"]) {
                            $arrDomains                             = explode(",", $client["domains"]);
                            if(array_search($domain["ID"], $arrDomains) === false) {
                                $valid_domain                       = false;
                            }
                        }

                        if($valid_domain) {
                            if($domain["status"]) {
                                if(!$domain["expiration"] || $domain["expiration"] < time()) {
                                    if(self::checkScopes($scopes, $domain["scopes"])) {
                                        unset($domain["expiration"]);
                                        unset($domain["status"]);

                                        $res                        = $domain;
                                        $res["client"]              = $client;
                                        $res["status"]              = "0";
                                        $res["error"]               = "";
                                    } else {
                                        $res["status"]              = "403";
                                        $res["error"]               = "Scope not Permitted" . (self::DEBUG
                                                                        ? ": " . implode(self::diffScopes($scopes, $domain["scopes"]), ", ")
                                                                        : ""
                                                                    );
                                    }
                                } else {
                                    $res["status"]                  = "401";
                                    $res["error"]                   = "Domain Expire Date";
                                }
                            } else {
                                $res["status"]                      = "401";
                                $res["error"]                       = "Domain not Active";
                            }
                        } else {
                            $res["status"]                          = "401";
                            $res["error"]                           = "Domain not Valid";
                        }
                    } else {
                        $res["status"]                              = "410";
                        $res["error"]                               = "Domain not Found";
                    }
                } else {
                    $res["status"]                                  = "400";
                    $res["error"]                                   = "Missing Domain Name";
                }
            } else {
                $res["status"]                                      = "0";
                $res["error"]                                       = "";
            }
        } else {
            $res                                                    = $client;
        }

        return $res;
    }

    private static function getUserByBearer($human_verify = false, $bearer = null) {
        if(!$bearer)                                                $bearer = self::getBearerToken();
        $select                                                     = array(
                                                                        "users.ID"
                                                                        , "users.SID_device"
                                                                        , "users.SID_ip"
                                                                        , "users.SID_expire"
                                                                    );
        $where                                                      = (is_array($bearer)
                                                                        ? $bearer
                                                                        : array(
                                                                            "users.SID"             => $bearer
                                                                        )
                                                                    );

        if($human_verify)                                           $where["users.SID_answer"]  = $human_verify;

        $user                                                       = Anagraph::getInstanceNoStrict("access")->read($select, $where);
        if(is_array($user)) {
            if ($user["SID_expire"] > time()) {
                $res                                                = $user;
                $res["status"]                                      = "0";
                $res["error"]                                       = "";

            } else {
                $res["status"]                                      = "400";
                $res["error"]                                       = "Session Expired";
            }
        } else {
            $res["status"]                                          = "403";
            $res["error"]                                           = "Bearer: not Permitted";
        }

        return $res;
    }

    private static function createCode($scope = null, $type = self::SA_ALG, $expire = null, $secret = null) {
        if(!$expire)                                                $expire = self::SA_EXPIRE;
        switch ($type) {
            case "hotp":
                $counter                                            = 1;
                $otp                                                = new OTPHP\HOTP("hotp", self::APPID . $scope, $counter);
                $res                                                = ($secret
                                                                        ? $otp->verify($secret)
                                                                        : $otp->at($counter)
                                                                    );
                break;
            case "totp":
            default:

                $totp                                               = new OTPHP\TOTP("totp", self::APPID . $scope, $expire);
                $res                                                = ($secret
                                                                        ? true //$totp->verify($secret) //todo: da trovare una classe che funzioni
                                                                        : $totp->now()
                                                                    );
        }

        return $res;
    }
    private static function checkScopes($set, $collection) {
        if($set && $collection) {
            $arrSet                                                 = explode("," , $set);
            $arrCollection                                          = explode(",", $collection);

            $arrIntersect                                           = array_intersect($arrSet, $arrCollection);

            return (count($arrIntersect) == count($arrSet)
                ? $arrIntersect
                : false
            );
        } else {
            return true;
        }
    }

    private static function diffScopes($set, $collection) {

        $arrSet                                                     = explode("," , $set);
        $arrCollection                                              = explode(",", $collection);

        return array_diff($arrSet, $arrCollection);
    }
    /**
     * @param null $service
     * @return mixed
     */
    private function getService($service = null) {
        $controller = $this->getControllerName($service ? $service : $this->service);

        return new $controller($this);
    }
    /*private static function getRequestAllowed($flip = false) {
        static $request                                             = null;

        if(!$request)                                               $request = self::$request;
        return ($flip
            ? array_fill_keys($request, true)
            : $request
        );
    }*/
    /**
     * @param null $request
     * @param string $method
     * @return array
     */
    /*private static function getData($request = null) {

        if(!$request)                                               $request = self::getReq();
        $return                                                     = array_diff_key($request, self::getRequestAllowed(true));

        foreach($return AS $key => $value) {
            $real_key                                               = str_replace("_", ".", $key);
            if($value)
                $res[$real_key]                                     = $value;
            else
                $res[]                                              = $real_key;

        }
        return $res;
    }*/

    /**
     * @param null $key
     * @param null $method
     * @return mixed
     */
    protected static function getReq($key = null, $strict = true) {
        $req                                                        = parent::getReq(self::REQUEST_METHOD);
        if(!count($req))                                            { $req = self::getRequest(null, "auth"); }

        return ($key
            ? ($strict
                ? $req[self::$request[$key]]
                : $req[$key]
            )
            : $req
        );
    }

   /* protected static function getRequest($rules = null, $key = null) {
        return parent::getRequest($rules, $key);
    }*/

    private static function isInvalidHTTPS($exit = false)
    {
        if(!$_SERVER["HTTPS"]) {
            $res["status"]                                          = "405";
            $res["error"]                                           = "Request Method Must Be In HTTPS";

            if($exit)                                               { self::endScript($res); }
        }

        return $res;
    }
    /**
     * @param $method
     * @param bool $exit
     * @return mixed
     */
    private static function isInvalidReqMethod($exit = false, $method = self::REQUEST_METHOD) {
        if(self::getPathInfo(self::API_PATH)) {
            $res                                                    = self::isInvalidHTTPS($exit);
            if(!$res) {
                if($_SERVER["REQUEST_METHOD"] != $method) {
                    $res["status"]                                  = "405";
                    $res["error"]                                   = "Request Method Must Be " . $method;

                    if($exit)                                       { self::endScript($res); }
                } else {
                    $res["status"]                                          = "0";
                    $res["error"]                                           = "";
                }
            } else {
                $res["status"]                                      = "405";
                $res["error"]                                       = "Https Required";
            }
        } else {
            $res["status"]                                          = "0";
            $res["error"]                                           = "Internal Called";
        }

        return $res;
    }

    /**
     * Get hearder Authorization
     * */
    private static function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    /**
     * get access token from header
     * */
    public static function getBearerToken() {
        $headers = self::getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers) && preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
        if($_SERVER["HTTP_BEARER"]) {
            return $_SERVER["HTTP_BEARER"];
        }
        return null;
    }


    private static function sudo($username, $password, $domain = null) {
        if(self::DEBUG
        && $username === self::SUPERADMIN_USERNAME
        && $password === self::SUPERADMIN_PASSWORD) {
            if (Cms::getInstance("validator")->isEmail($username)) {
                $where["users.email"]                           = $username;
            } elseif (Cms::getInstance("validator")->isTel($username)) {
                $where["users.tel"]                             = $username;
            } else {
                $where["users.username"]                        = $username;
            }

            if($domain) {
                $where["users.ID_domain"]                        = $domain["ID"];
            }

            if($where) {
                $user                                           = Anagraph::getInstanceNoStrict("access")->read(
                                                                    array(
                                                                        "users.ID"
                                                                        , "users.tel"
                                                                        , "users.email"
                                                                        , "users.status"
                                                                        , "users.last_login"
                                                                        , "users.acl"
                                                                        , "users.acl_primary"
                                                                    )
                                                                    , $where
                                                                );
            }

            if(!$user) {
                $user                                           = array(
                                                                    "ID"                => "0"
                                                                    , "ID_domain"       => "0"
                                                                    , "username"        => self::SUPERADMIN_USERNAME
                                                                    , "acl"             => -1
                                                                    , "acl_primary"     => "admin"
                                                                    , "acl_profile"     => "admin"
                                                                );
            }

            return Auth::getInstance("session")->start($user);
        }
    }

    /**
     * @param null $json
     */
    private static function endScript($json = null) {
        Api::send($json);
    }

    /**
     * @param $service
     * @return string
     */
    private function getControllerName($service) {
        return self::TYPE . ucfirst($service);
    }
}
