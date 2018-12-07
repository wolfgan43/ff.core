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

use OAuth2;

class authOAuth2
{
    private $oAuth                                     = null;

    public function __construct($auth) {

    }
    public function grantAccess($path_info) {
        $request_method = $_SERVER["REQUEST_METHOD"];
        $oAuth = $this->oAuth();

        return true;

        if($oAuth) {
            $settings_path = $path_info;
            do {

                if (isset($oAuth[$settings_path])) {
                    $server["rules"] = $oAuth[$settings_path];
                    break;
                }
            } while ($settings_path != DIRECTORY_SEPARATOR && ($settings_path = dirname($settings_path)));

            if ($server["rules"]["scopes"][$request_method]) {
                if ($server["rules"]["scopes"][$request_method]) {
                    if (!$_REQUEST["access_token"]) {
                        switch ($request_method) {
                            case "get":
                                $_GET["access_token"] = Auth::getBearerToken();
                                break;
                            case "post":
                            default:
                                $_POST["access_token"] = Auth::getBearerToken();
                        }
                    }
                    $server["scopes"]["available"] = $server["rules"]["scopes"][$request_method];

                    $server["oAuth2"] = $this->server();
                    $server["request"] = OAuth2\Request::createFromGlobals();
                    $server["response"] = new OAuth2\Response();

                    if (is_array($server["scopes"]["available"])) {
                        foreach ($server["scopes"]["available"] AS $scope) {
                            //$server["oAuth2"]->verifyResourceRequest($server["request"], $server["response"], $scope);
                            if ($server["oAuth2"]->verifyResourceRequest($server["request"], $server["response"], $scope))
                                $server["scopes"]["valid"][] = $scope;
                        }
                    } else {
                        if ($server["oAuth2"]->verifyResourceRequest($server["request"], $server["response"], $server["scopes"]["available"]))
                            $server["scopes"]["valid"][] = $server["scopes"]["available"];

                    }

                    if (!$server["scopes"]["valid"]) {
                        $server["oAuth2"]->getResponse()->send();
                        exit;
                    }
                }
            }
        }
    }

    private function server() {
        if ($_REQUEST["__OAUTH2DEBUG__"])
        {
            $parts = explode("/", $_SERVER["REQUEST_URI"]);
            @mkdir(CM_CACHE_DISK_PATH . "/oauth2", 0777, true);
            $fp = fopen(CM_CACHE_DISK_PATH . "/oauth2/" . end($parts) . "_" . uniqid(), "w+");
            fwrite($fp, print_r($_REQUEST, true));
            fclose($fp);
        }

        static $server = null;

        if ($server !== null) {
            return $server;
        }

        $storage = new OAuth2\Storage\FF();

        $server = new OAuth2\Server($storage);

        $server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
        $server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
        $server->addGrantType(new OAuth2\GrantType\RefreshToken($storage));
        $server->addGrantType(new OAuth2\GrantType\UserCredentials($storage));

        return $server;
    }

    public function token() {
        $server = modsec_getOauth2Server();

        $_REQUEST["grant_type"] = $_GET["grant_type"] = $_POST["grant_type"] = "client_credentials";

        $response = new OAuth2\Response();
        $server->handleTokenRequest(OAuth2\Request::createFromGlobals(), $response);

        $response->send();
        exit;
    }

    private function oAuth() {
        if(!$this->oAuth) {
            Auth::getSchema(function(&$config) {
                /**
                 * oAuth
                 */

                if(is_array($config["oAuth"]["rule"]) && count($config["oAuth"]["rule"])) {
                    foreach ($config["oAuth"]["rule"] AS $oAuth) {
                        $attr                                           = Filemanager::getAttr($oAuth);
                        $this->oAuth[$attr["path"]]                     = array(
                            "scopes" => array(
                                "get" => $attr["get"]
                            , "post" => $attr["post"]
                            )
                        );
                    }
                }
                unset($config["oAuth"]);
            });
        }
        return $this->oAuth;
    }


}
