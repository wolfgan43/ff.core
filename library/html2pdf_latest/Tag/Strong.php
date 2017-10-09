<?php
/**
 * Html2Pdf Library - Tag Strong
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
namespace Spipu\Html2Pdf\Tag;

class Strong extends B
{
    /**
     * get the name of the tag
     *
     * @return string
     */
    public function getName()
    {
        return 'strong';
    }
}
