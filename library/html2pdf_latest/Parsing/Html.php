<?php
/**
 * Html2Pdf Library - parsing Html class
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Parsing;

use Spipu\Html2Pdf\Exception\HtmlParsingException;

class Html
{
    protected $lexer;
    protected $_html     = '';        // HTML code to parse
    protected $_num      = 0;         // table number
    protected $_level    = 0;         // table level
    protected $_encoding = '';        // encoding
    public $code      = array();   // parsed HTML code

    const HTML_TAB = '        ';

    /**
     * main constructor
     *
     * @param   string $encoding
     * @access  public
     */
    public function __construct($encoding = 'UTF-8')
    {
        $this->lexer = new HtmlLexer();
        $this->_num   = 0;
        $this->_level = array($this->_num);
        $this->_html  = '';
        $this->code  = array();
        $this->setEncoding($encoding);
    }

    /**
     * change the encoding
     *
     * @param   string $encoding
     * @access  public
     */
    public function setEncoding($encoding)
    {
        $this->_encoding = $encoding;
    }

    /**
     * Define the HTML code to parse
     *
     * @param   string $html code
     * @access  public
     */
    public function setHTML($html)
    {
        // remove the HTML in comment
        $html = preg_replace('/<!--(.*)-->/isU', '', $html);

        // save the HTML code
        $this->_html = $html;
    }

    /**
     * parse the HTML code
     *
     * @access public
     */
    public function parse()
    {
        $parents = array();

        // flag : are we in a <pre> Tag ?
        $tagPreIn = false;

        // action to use for each line of the content of a <pre> Tag
        $tagPreBr = array(
                    'name' => 'br',
                    'close' => false,
                    'param' => array(
                        'style' => array(),
                        'num'    => 0
                    )
                );

        // tag that can be not closed
        $tagsNotClosed = array(
            'br', 'hr', 'img', 'col',
            'input', 'link', 'option',
            'circle', 'ellipse', 'path', 'rect', 'line', 'polygon', 'polyline'
        );

        // search the HTML tags
        $tokens = $this->lexer->tokenize($this->_html);

        // all the actions to do
        $actions = array();

        // foreach part of the HTML code
        foreach ($tokens as $part) {
            // if it is a tag code
            if ($part[0] == 'code') {
                // analyze the HTML code
                $res = $this->_analyzeTag($part[1]);

                // save the current position in the HTML code
                $res['html_pos'] = $part[2];

                // if the tag must be closed
                if (!in_array($res['name'], $tagsNotClosed)) {
                    // if it is a closure tag
                    if ($res['close']) {
                        // HTML validation
                        if (count($parents) < 1) {
                            $e = new HtmlParsingException('Too many tag closures found for ['.$res['name'].']');
                            $e->setInvalidTag($res['name']);
                            $e->setHtmlPart($this->getHtmlErrorCode($res['html_pos']));
                            throw $e;
                        } elseif (end($parents) != $res['name']) {
                            $e = new HtmlParsingException('Tags are closed in a wrong order for ['.$res['name'].']');
                            $e->setInvalidTag($res['name']);
                            $e->setHtmlPart($this->getHtmlErrorCode($res['html_pos']));
                            throw $e;
                        } else {
                            array_pop($parents);
                        }
                    } else {
                        // if it is an auto-closed tag
                        if ($res['autoclose']) {
                            // save the opened tag
                            $actions[] = $res;

                            // prepare the closed tag
                            $res['params'] = array();
                            $res['close'] = true;
                        } else {
                            // else: add a child for validation
                            array_push($parents, $res['name']);
                        }
                    }

                    // if it is a <pre> tag (or <code> tag) not auto-closed => update the flag
                    if (($res['name'] == 'pre' || $res['name'] == 'code') && !$res['autoclose']) {
                        $tagPreIn = !$res['close'];
                    }
                }

                // save the actions to convert
                $actions[] = $res;
            } else if ($part[0] == 'txt') {
                // if we are not in a <pre> tag
                if (!$tagPreIn) {
                    // save the action
                    $actions[] = array(
                        'name'  => 'write',
                        'close' => false,
                        'param' => array('txt' => $this->_prepareTxt($part[1])),
                    );
                } else { // else (if we are in a <pre> tag)
                    // prepare the text
                    $part[1] = str_replace("\r", '', $part[1]);
                    $part[1] = explode("\n", $part[1]);

                    // foreach line of the text
                    foreach ($part[1] as $k => $txt) {
                        // transform the line
                        $txt = str_replace("\t", self::HTML_TAB, $txt);
                        $txt = str_replace(' ', '&nbsp;', $txt);

                        // add a break line
                        if ($k > 0) {
                            $actions[] = $tagPreBr;
                        }

                        // save the action
                        $actions[] = array(
                            'name'  => 'write',
                            'close' => false,
                            'param' => array('txt' => $this->_prepareTxt($txt, false)),
                        );
                    }
                }
            }
        }

        // for each identified action, we have to clean up the begin and the end of the texte
        // based on tags that surround it

        // list of the tags to clean
        $tagsToClean = array(
            'page', 'page_header', 'page_footer', 'form',
            'table', 'thead', 'tfoot', 'tr', 'td', 'th', 'br',
            'div', 'hr', 'p', 'ul', 'ol', 'li',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'bookmark', 'fieldset', 'legend',
            'draw', 'circle', 'ellipse', 'path', 'rect', 'line', 'g', 'polygon', 'polyline',
            'option'
        );

        // foreach action
        $nb = count($actions);
        for ($k = 0; $k < $nb; $k++) {
            // if it is a Text
            if ($actions[$k]['name']=='write') {
                // if the tag before the text is a tag to clean => ltrim on the text
                if ($k>0 && in_array($actions[$k - 1]['name'], $tagsToClean)) {
                    $actions[$k]['param']['txt'] = ltrim($actions[$k]['param']['txt']);
                }

                // if the tag after the text is a tag to clean => rtrim on the text
                if ($k < $nb - 1 && in_array($actions[$k + 1]['name'], $tagsToClean)) {
                    $actions[$k]['param']['txt'] = rtrim($actions[$k]['param']['txt']);
                }

                // if the text is empty => remove the action
                if (!strlen($actions[$k]['param']['txt'])) {
                    unset($actions[$k]);
                }
            }
        }

        // if we are not on the level 0 => HTML validator ERROR
        if (count($parents)) {
            if (count($parents)>1) {
                $errorMsg = 'The following tags have not been closed:';
            } else {
                $errorMsg = 'The following tag has not been closed:';
            }

            $e = new HtmlParsingException($errorMsg.' '.implode(', ', $parents));
            $e->setInvalidTag($parents[0]);
            throw $e;
        }

        // save the actions to do
        $this->code = array_values($actions);
    }

    /**
     * prepare the text
     *
     * @param   string $txt
     * @param   boolean $spaces true => replace multiple space+\t+\r+\n by a single space
     * @return  string txt
     * @access  protected
     */
    protected function _prepareTxt($txt, $spaces = true)
    {
        if ($spaces) {
            $txt = preg_replace('/\s+/isu', ' ', $txt);
        }
        $txt = str_replace('&euro;', '€', $txt);
        $txt = html_entity_decode($txt, ENT_QUOTES, $this->_encoding);
        return $txt;
    }

    /**
     * analyze a HTML tag
     *
     * @param   string   $code HTML code to analise
     * @return  array    corresponding action
     */
    protected function _analyzeTag($code)
    {
        // name of the tag, opening, closure, autoclosure
        $tag = '<([\/]{0,1})([_a-z0-9]+)([\/>\s]+)';
        if (!preg_match('/'.$tag.'/isU', $code, $match)) {
            $e = new HtmlParsingException('The HTML tag ['.$code.'] provided is invalid');
            $e->setInvalidTag($code);
            throw $e;
        }
        $close     = ($match[1] == '/' ? true : false);
        $autoclose = preg_match('/\/>$/isU', $code);
        $name      = strtolower($match[2]);

        // required parameters (depends on the tag name)
        $param    = array();
        $param['style'] = '';
        if ($name == 'img') {
            $param['alt'] = '';
            $param['src'] = '';
        }
        if ($name == 'a') {
            $param['href'] = '';
        }

        // read the parameters : name=value
        $prop = '([a-zA-Z0-9_]+)=([^"\'\s>]+)';
        preg_match_all('/'.$prop.'/is', $code, $match);
        for ($k = 0; $k < count($match[0]); $k++) {
            $param[trim(strtolower($match[1][$k]))] = trim($match[2][$k]);
        }

        // read the parameters : name="value"
        $prop = '([a-zA-Z0-9_]+)=["]([^"]*)["]';
        preg_match_all('/'.$prop.'/is', $code, $match);
        for ($k = 0; $k < count($match[0]); $k++) {
            $param[trim(strtolower($match[1][$k]))] = trim($match[2][$k]);
        }

        // read the parameters : name='value'
        $prop = "([a-zA-Z0-9_]+)=[']([^']*)[']";
        preg_match_all('/'.$prop.'/is', $code, $match);
        for ($k = 0; $k < count($match[0]); $k++) {
            $param[trim(strtolower($match[1][$k]))] = trim($match[2][$k]);
        }

        // compliance of each parameter
        $color  = "#000000";
        $border = null;
        foreach ($param as $key => $val) {
            $key = strtolower($key);
            switch ($key) {
                case 'width':
                    unset($param[$key]);
                    $param['style'] .= 'width: '.$val.'px; ';
                    break;

                case 'align':
                    if ($name === 'img') {
                        unset($param[$key]);
                        $param['style'] .= 'float: '.$val.'; ';
                    } elseif ($name !== 'table') {
                        unset($param[$key]);
                        $param['style'] .= 'text-align: '.$val.'; ';
                    }
                    break;

                case 'valign':
                    unset($param[$key]);
                    $param['style'] .= 'vertical-align: '.$val.'; ';
                    break;

                case 'height':
                    unset($param[$key]);
                    $param['style'] .= 'height: '.$val.'px; ';
                    break;

                case 'bgcolor':
                    unset($param[$key]);
                    $param['style'] .= 'background: '.$val.'; ';
                    break;

                case 'bordercolor':
                    unset($param[$key]);
                    $color = $val;
                    break;

                case 'border':
                    unset($param[$key]);
                    if (preg_match('/^[0-9]+$/isU', $val)) {
                        $val = $val.'px';
                    }
                    $border = $val;
                    break;

                case 'cellpadding':
                case 'cellspacing':
                    if (preg_match('/^([0-9]+)$/isU', $val)) {
                        $param[$key] = $val.'px';
                    }
                    break;

                case 'colspan':
                case 'rowspan':
                    $val = preg_replace('/[^0-9]/isU', '', $val);
                    if (!$val) {
                        $val = 1;
                    }
                    $param[$key] = $val;
                    break;
            }
        }

        // compliance of the border
        if ($border !== null) {
            if ($border) {
                $border = 'border: solid '.$border.' '.$color;
            } else {
                $border = 'border: none';
            }

            $param['style'] .= $border.'; ';
            $param['border'] = $border;
        }

        // reading styles: decomposition and standardization
        $styles = explode(';', $param['style']);
        $param['style'] = array();
        foreach ($styles as $style) {
            $tmp = explode(':', $style);
            if (count($tmp) > 1) {
                $cod = $tmp[0];
                unset($tmp[0]);
                $tmp = implode(':', $tmp);
                $param['style'][trim(strtolower($cod))] = preg_replace('/[\s]+/isU', ' ', trim($tmp));
            }
        }

        // determining the level of table opening, with an added level
        if (in_array($name, array('ul', 'ol', 'table')) && !$close) {
            $this->_num++;
            $this->_level[count($this->_level)] = $this->_num;
        }

        // get the level of the table containing the element
        if (!isset($param['num'])) {
            $param['num'] = $this->_level[count($this->_level) - 1];
        }

        // for closures table: remove a level
        if (in_array($name, array('ul', 'ol', 'table')) && $close) {
            unset($this->_level[count($this->_level) - 1]);
        }

        // prepare the parameters
        if (isset($param['value'])) {
            $param['value']  = $this->_prepareTxt($param['value']);
        }
        if (isset($param['alt'])) {
            $param['alt']    = $this->_prepareTxt($param['alt']);
        }
        if (isset($param['title'])) {
            $param['title']  = $this->_prepareTxt($param['title']);
        }
        if (isset($param['class'])) {
            $param['class']  = $this->_prepareTxt($param['class']);
        }

        // return the new action to do
        return array('name' => $name, 'close' => $close ? 1 : 0, 'autoclose' => $autoclose, 'param' => $param);
    }

    /**
     * get a full level of HTML, between an opening and closing corresponding
     *
     * @param   integer $k
     * @return  array   actions
     */
    public function getLevel($k)
    {
        // if the code does not exist => return empty
        if (!isset($this->code[$k])) {
            return array();
        }

        // the tag to detect
        $detect = $this->code[$k]['name'];

        // if it is a text => return
        if ($detect == 'write') {
            return array($this->code[$k]);
        }

        //
        $level = 0;      // depth level
        $end = false;    // end of the search
        $code = array(); // extract code

        // while it's not ended
        while (!$end) {
            // current action
            $row = $this->code[$k];

            // if 'write' => we add the text
            if ($row['name']=='write') {
                $code[] = $row;
            } else { // else, it is a html tag
                $not = false; // flag for not taking into account the current tag

                // if it is the searched tag
                if ($row['name'] == $detect) {
                    // if we are just at the root level => dont take it
                    if ($level == 0) {
                        $not = true;
                    }

                    // update the level
                    $level+= ($row['close'] ? -1 : 1);

                    // if we are now at the root level => it is the end, and dont take it
                    if ($level == 0) {
                        $not = true;
                        $end = true;
                    }
                }

                // if we can take into account the current tag => save it
                if (!$not) {
                    if (isset($row['style']['text-align'])) {
                        unset($row['style']['text-align']);
                    }
                    $code[] = $row;
                }
            }

            // it continues as long as there has code to analyze
            if (isset($this->code[$k + 1])) {
                $k++;
            } else {
                $end = true;
            }
        }

        // return the extract
        return $code;
    }

    /**
     * return a part of the HTML code, for error message
     *
     * @param   integer $pos
     * @param   integer $before take before
     * @param   integer $after  take after
     * @return  string  part of the html code
     */
    public function getHtmlErrorCode($pos, $before = 30, $after = 40)
    {
        return substr($this->_html, $pos-$before, $before+$after);
    }
}
