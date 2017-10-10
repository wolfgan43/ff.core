<?php
/**
 * Html2Pdf Library - Exception
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Exception;

/**
 * Html2Pdf Library - Html2PdfException
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */
class Html2PdfException extends \Exception
{
    /**
     * ERROR CODE 0
     * @var int
     */
    const ERROR_CODE = 0;

    /**
     * Construct the exception.
     *
     * @param string $message The Exception message to throw.
     *
     * @return Html2PdfException
     */
    public function __construct($message)
    {
        $className = get_class($this);
        return parent::__construct($message, $className::ERROR_CODE);
    }
}
