<?php
/**
 * Html2Pdf Library - Tag S
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Tag;

class S extends AbstractDefaultTag
{
    /**
     * get the name of the tag
     *
     * @return string
     */
    public function getName()
    {
        return 's';
    }


    /**
     * override some styles
     *
     * @return Span
     */
    protected function _overrideStyles()
    {
        $this->_parsingCss->value['font-linethrough'] = true;

        return $this;
    }
}
