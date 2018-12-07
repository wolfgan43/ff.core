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

class authApiUser {


    public function __construct()
    {

    }

    /**
     * @param null $opt
     * @return array
     */
    public function login($request = null) {
        $opt["method"]                      = "token";

        return Auth::login($opt);
    }

    public function logout($request = null) {
        $opt["method"]                      = "token";

        return Auth::logout($opt);
    }

    public function registration($request = null) {
        $opt                                = null;

        return Auth::registration($opt);
    }

    public function check($request = null) {
        $opt["method"]                      = "token";

        return Auth::check($opt);
    }
    public function code($request = null) {
        $opt                                = null;

        return Auth::code($opt);
    }
    public function refresh($request = null) {
        $opt["method"]                      = "refresh";

        return Auth::check($opt);
    }
    public function recover($request = null) {
        $opt["scopes"]                      = "password";

        return Auth::write(null, null, $opt);
    }
    public function activation($request = null) {
        $opt["scopes"]                      = "activation";

        return Auth::write(null, null, $opt);
    }

    /**
     * @api /api/user/key
     * @header hash client-id
     * @header hash client-secret
     *
     * @post array $scope
     * @post hash $token
     *
     * @example client-id Auth::env("CLIENT_ID")
     * @example client-secret Auth::env("CLIENT_SECRET")
     * @example $scope
     * @example $token Auth::login
     *
     * @assert status != 0
     *
     *
     * @param null $scope
     * @param null $token
     * @param null $opt
     * @return mixed
     */
    public function key($request = null) {
        $opt                                = null;

        return Auth::key($request["rawdata"]["scopes"], $request["auth"]["t"], $opt);
    }
    public function share($request = null) {
        $opt                                = null;

        return Auth::share($opt);
    }
    public function join($request = null) {
        $opt                                = null;

        return Auth::join($opt);
    }
    public function lists($request = null) {
        $opt                                = null;

        return Auth::users($opt);
    }
    public function certificate($request = null) {
        $opt                                = null;

        return Auth::createCertificate($opt);
    }
}
