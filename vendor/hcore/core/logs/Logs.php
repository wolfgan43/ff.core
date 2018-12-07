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


class Logs extends vgCommon {

    public static function write($data, $filename = "log", $override = false) {
        $set_mod = false;
        $log_path = self::getDiskPath("cache") . "/logs";
        if(!is_dir($log_path))
            mkdir($log_path, 0777, true);

        $file = $log_path . '/' . date("Y-m-d") . "_" . $filename . '.txt';
        if(!is_file($file)) {
            $set_mod = true;
        } elseif($override) {
            unlink($file);
            $set_mod = true;
        }

        if($handle = @fopen($file, 'a'))
        {
            if(is_array($data)) {
                $string = print_r($data, true);
            } else {
                $string = $data;
            }

            if(@fwrite($handle, date("Y-m-d H:i:s", time()) . " " . $string . "\n") === FALSE)
            {
                $i18n_error = true;
            }
            @fclose($handle);

            if($set_mod && !$i18n_error)
                chmod($file, 0777);
        }
    }
}

