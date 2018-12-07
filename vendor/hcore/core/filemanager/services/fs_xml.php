<?php
/**
 *   VGallery: CMS based on FormsFramework
Copyright (C) 2004-2015 Alessandro Stucchi <wolfgan@gmail.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

 * @package VGallery
 * @subpackage core
 * @author Alessandro Stucchi <wolfgan@gmail.com>
 * @copyright Copyright (c) 2004, Alessandro Stucchi
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link https://github.com/wolfgan43/vgallery
 */

class filemanagerXml
{
    const EXT                                                   = "xml";

    private $device                                             = null;
    private $config                                             = null;
    private $filemanager                                        = null;

    /**
     * filemanagerPhp constructor.
     * @param $filemanager
     * @param null $data
     * @param null $config
     */
    public function __construct($filemanager, $data = null, $config = null)
    {
        $this->filemanager                                      = $filemanager;
        $this->setConfig($config);
    }

    /**
     * @return null
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @return null
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param null $config
     */
    private function setConfig($config = null)
    {
        $this->config                                           = $config;
    }

    /**
     * @param null $keys
     * @param null $flags
     * @return array|mixed
     */
    public function read($keys = null, $flags = null)
    {
        $res                                                    = array();
        $path                                                   = $this->filemanager->getPath($this::EXT);
        $var                                                    = $this->filemanager->getParam("var");
        //$keys                                                   = $this->filemanager->getParam("keys");
        if(!$flags)
            $flags                                              = Filemanager::SEARCH_DEFAULT;


        if(is_file($path)) {
            $xmlstring = file_get_contents($path);
            if($xmlstring) {
                $return = Array2XML::XML_TO_ARR($xmlstring);

                if($return) {
                    if($keys) {
                        if(!is_array($keys)) {
                            $keys = array($keys);
                        }
                        foreach($keys AS $key)
                        {
                            if($flags == Filemanager::SEARCH_IN_KEY || $flags == Filemanager::SEARCH_IN_BOTH && isset($return[$key])) {
                                $res[$key] = $return[$key];
                            }
                            if($flags == Filemanager::SEARCH_IN_VALUE || $flags == Filemanager::SEARCH_IN_BOTH) {
                                $arrToAdd                       = array_flip(array_keys($return, $key));
                                $res                            = array_replace($res, array_intersect_key($return, $arrToAdd));
                            }
                        }
                    } else {
                        $res                                    = $return;
                    }
                } elseif($return === false) {
                    $this->filemanager->isError("syntax errors into file" . (Filemanager::DEBUG ? ": " . $path : ""));
                } else {
                    $res                                        = null;
                    //$this->filemanager->isError("Return Empty");
                }
            } else {
                @unlink($this->filemanager->getParam("path"));
                $this->filemanager->isError("syntax errors into file" . (Filemanager::DEBUG ? ": " . $path : ""));
            }
        }

        return $res;
    }

    /**
     * @param $data
     * @param null $var
     * @return mixed
     */
    public function write($data, $var = null)
    {
        $path 													= $this->filemanager->getPath($this::EXT);
        if(!$var)                                               { $var = $this->filemanager->getParam("var"); }
        $expires                                                = $this->filemanager->getParam("expires");

        $root_node = ($var
            ? $var
            : "root"
        );

        return $this->filemanager->save(Array2XML::createXML($root_node, $data), $expires, $path);
    }

    /**
     * @param $data
     * @param null $var
     * @return mixed
     */
    public function update($data, $var = null)
    {
        //$data                                                   = $this->filemanager->getParam("data");
        //$expires                                                = $this->filemanager->getParam("expires");

        if(is_array($data)) {
            $res                                                = array_replace($this->read(), $data);
        } else {
            $res = $data;
        }
        return $this->write($res, $var);
    }

    /**
     * @param $keys
     * @param null $flags
     * @return mixed
     */
    public function delete($keys, $flags = null)
    {
        if(!$flags)
            $flags                                              = Filemanager::SEARCH_DEFAULT;

        //$var                                                    = $this->filemanager->getParam("var");
        //$keys                                                   = $this->filemanager->getParam("keys");

        $res                                                    = $this->read();
        if($keys)
        {
            if(!is_array($keys))
                $keys = array($keys);

            foreach($keys AS $key)
            {
                if($flags == Filemanager::SEARCH_IN_KEY || $flags == Filemanager::SEARCH_IN_BOTH)
                    unset($res[$key]);

                if($flags == Filemanager::SEARCH_IN_VALUE || $flags == Filemanager::SEARCH_IN_BOTH) {
                    $arrToDel                                   = array_flip(array_keys($res, $key));
                    $res                                        = array_diff_key($res, $arrToDel);
                }
            }
        }

        return $this->write($res);
    }
}

class Array2XML {

    private static $xml = null;
    private static $encoding = 'UTF-8';

    /**
     * Initialize the root XML node [optional]
     * @param $version
     * @param $encoding
     * @param $format_output
     */
    public static function init($version = '1.0', $encoding = 'UTF-8', $format_output = true) {
        self::$xml = new DomDocument($version, $encoding);
        self::$xml->formatOutput = $format_output;
        self::$encoding = $encoding;
    }

    /**
     * Convert an Array to XML
     * @param string $node_name - name of the root node to be converted
     * @param array $arr - aray to be converterd
     * @return DomDocument
     */
    public static function &createXML($node_name, $arr=array()) {
        $xml = self::getXMLRoot();
        $xml->appendChild(self::convert($node_name, $arr));

        self::$xml = null;    // clear the xml node in the class for 2nd time use.
        return $xml;
    }

    /**
     * Convert an Array to XML
     * @param string $node_name - name of the root node to be converted
     * @param array $arr - aray to be converterd
     * @return DOMNode
     */
    private static function &convert($node_name, $arr=array()) {

        //print_arr($node_name);
        $xml = self::getXMLRoot();
        $node = $xml->createElement($node_name);

        if(is_array($arr)){
            // get the attributes first.;
            if(isset($arr['@attributes'])) {
                foreach($arr['@attributes'] as $key => $value) {
                    if(!self::isValidTagName($key)) {
                        throw new Exception('[Array2XML] Illegal character in attribute name. attribute: '.$key.' in node: '.$node_name);
                    }
                    $node->setAttribute($key, self::bool2str($value));
                }
                unset($arr['@attributes']); //remove the key from the array once done.
            }

            // check if it has a value stored in @value, if yes store the value and return
            // else check if its directly stored as string
            if(isset($arr['@value'])) {
                $node->appendChild($xml->createTextNode(self::bool2str($arr['@value'])));
                unset($arr['@value']);    //remove the key from the array once done.
                //return from recursion, as a note with value cannot have child nodes.
                return $node;
            } else if(isset($arr['@cdata'])) {
                $node->appendChild($xml->createCDATASection(self::bool2str($arr['@cdata'])));
                unset($arr['@cdata']);    //remove the key from the array once done.
                //return from recursion, as a note with cdata cannot have child nodes.
                return $node;
            }
        }

        //create subnodes using recursion
        if(is_array($arr)){
            // recurse to get the node for that key
            foreach($arr as $key=>$value){
                if(!self::isValidTagName($key)) {
                    throw new Exception('[Array2XML] Illegal character in tag name. tag: '.$key.' in node: '.$node_name);
                }
                if(is_array($value) && is_numeric(key($value))) {
                    // MORE THAN ONE NODE OF ITS KIND;
                    // if the new array is numeric index, means it is array of nodes of the same kind
                    // it should follow the parent key name
                    foreach($value as $k=>$v){
                        $node->appendChild(self::convert($key, $v));
                    }
                } else {
                    // ONLY ONE NODE OF ITS KIND
                    $node->appendChild(self::convert($key, $value));
                }
                unset($arr[$key]); //remove the key from the array once done.
            }
        }

        // after we are done with all the keys in the array (if it is one)
        // we check if it has any text value, if yes, append it.
        if(!is_array($arr)) {
            $node->appendChild($xml->createTextNode(self::bool2str($arr)));
        }

        return $node;
    }

    /*
     * Get the root XML node, if there isn't one, create it.
     */
    private static function getXMLRoot(){
        if(empty(self::$xml)) {
            self::init();
        }
        return self::$xml;
    }

    /*
     * Get string representation of boolean value
     */
    private static function bool2str($v){
        //convert boolean to text value.
        $v = $v === true ? 'true' : $v;
        $v = $v === false ? 'false' : $v;
        return $v;
    }

    /*
     * Check if the tag name or attribute name contains illegal characters
     * Ref: http://www.w3.org/TR/xml/#sec-common-syn
     */
    private static function isValidTagName($tag){
        $pattern = '/^[a-z_]+[a-z0-9\:\-\.\_]*[^:]*$/i';
        return preg_match($pattern, $tag, $matches) && $matches[0] == $tag;
    }

    /*
     * Convert xml string into array.
     */
    public static function XML_TO_ARR($xmlstring)
    {
        $array = false;
        $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
        if(array_key_exists("0", $xml))
            return null;

        if($xml) {
            $json = json_encode($xml);
            $json_normalized = str_replace(
                array('"comment":{},', '"true"', '"false"', '"null"', '[{}]')
                , array('', 'true', 'false', 'null', '[]')
                , $json
            );

            // $json_no_attrib = preg_replace("/{\"@attributes\":(.*)}/i", "$1", $json_normalized);
            // $json_no_comment = preg_replace("/\"comment\":\[{.*)\]/i", "$1", $json_no_attrib);

            $json_no_attrib = str_replace(
                array('{"@attributes":{', '}},', '}}]', '}}}}')
                , array('{', '},', '}]', '}}}')
                , $json_normalized
            );
            // $array = json_decode($json_no_attrib, TRUE);
            // if(!$array) {
            $array = json_decode($json_normalized, TRUE);
            //}


            /* print_r($json);
             echo "<br><br><br><br><br><br>";
             print_r($json_no_attrib);
             echo "<br><br><br><br><br><br>";
             print_r($array);
             echo "<br><br><br><br><br><br>";

             $p = xml_parser_create();
             xml_parse_into_struct($p, $xmlstring, $vals, $index);
             xml_parser_free($p);
             print_r($index);
             print_r($vals);
            */
        } else {
            $array = $xml;
        }
        return $array;
    }
}