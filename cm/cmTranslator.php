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

if(!defined("FF_PREFIX"))                       define("FF_PREFIX", "ff_");
if(!defined("FF_LOCALE"))                       define("FF_LOCALE", "ITA");

class cmTranslator
{
    const DB_TABLE_LANG                     = FF_PREFIX . "languages";
    const DB_TABLE_INTERNATIONAL            = FF_PREFIX . "international";
    const LANG                              = FF_LOCALE;

    private static $instances   = array();

    private function __construct($name = null)
    {
    }

    public static function getInstance($name = "router")
    {
        if (!isset(cmTranslator::$instances[$name]))
            cmTranslator::$instances[$name] = new cmTranslator($name);

        return cmTranslator::$instances[$name];
    }

    public static function get_word_by_code($code, $language = self::LANG) {
        static $loaded_i18n = array();

        $language = strtolower($language);

        if(!$loaded_i18n[$language][$code]) {
            if(ffTemplate::$_MultiLang_cache) {
                $cache = ffCache::getInstance();
                $loaded_i18n[$language][$code] = $cache->get($code, "ffcms/translate/" . $language);
                if(!$loaded_i18n[$language][$code]) {
                    $loaded_i18n[$language][$code] = self::getWordByCodeFromDB($code, $language);

                    $cache->set($code, $loaded_i18n[$language][$code], "ffcms/translate/" . $language);
                }
            }
        }

        return $loaded_i18n[$language][$code]["word"];
    }

    private static function getWordByCodeFromDB($code, $language = self::LANG) {
        self::initEvents();
        $res = self::$_events->doEvent("on_get_word_by_code", array($code, $language));
        $rc = end($res);
        if ($rc !== null)
            return $rc;

        $db = ffDb_Sql::factory();
        $i18n                           = array(
            "code"      => $code
        , "lang"    => strtoupper($language)
        );

        $db->query("SELECT
                        " . ffTemplate::DB_TABLE_INTERNATIONAL . ".*
                    FROM
                        " . ffTemplate::DB_TABLE_INTERNATIONAL . "
                        INNER JOIN " . ffTemplate::DB_TABLE_LANG . " ON
                            " . ffTemplate::DB_TABLE_INTERNATIONAL . ".`ID_lang` = " . ffTemplate::DB_TABLE_LANG . ".ID
                    WHERE
                        " . ffTemplate::DB_TABLE_LANG . ".`code` = " . $db->toSql($i18n["lang"]) . "
                        AND " . ffTemplate::DB_TABLE_INTERNATIONAL . ".`word_code` =" . $db->toSql($i18n["code"])
        );
        if($db->nextRecord())
        {
            if($db->record["is_new"])
            {
                $i18n["word"]           = (ffTemplate::$_MultiLang_Hide_code
                    ? $i18n["code"]
                    : "{" . $i18n["code"] . "}"
                );

                $i18n["cache"]          = false;
            }
            else
            {
                $i18n["word"]           = $db->getField("description", "Text", true);
                $i18n["cache"]          = true;
            }
        }
        else
        {
            if(ffTemplate::$_MultiLang_Insert_code_empty)
            {
                $sSQL = "INSERT INTO " . ffTemplate::DB_TABLE_INTERNATIONAL . "
                        (
                            `ID`
                            , `ID_lang`
                            , `word_code`
                            , `is_new`
                        )
                        VALUES
                        (
                            null
                            , IFNULL(
                                (SELECT " . ffTemplate::DB_TABLE_LANG . ".`ID` 
                                    FROM " . ffTemplate::DB_TABLE_LANG . " 
                                    WHERE " . ffTemplate::DB_TABLE_LANG . ".`code` = " . $db->toSql($i18n["lang"]) . " 
                                    LIMIT 1
                                )
                                , 0
                            )
                            , " . $db->toSql($i18n["code"]) . "
                            , " . $db->toSql("1", "Number") . "
                        )";
                $db->execute($sSQL);
            }
            $i18n["word"]               = (ffTemplate::$_MultiLang_Hide_code
                ? $i18n["code"]
                : "{" . $i18n["code"] . "}"
            );
            $i18n["cache"]              = false;
        }
        return $i18n;
    }
}
