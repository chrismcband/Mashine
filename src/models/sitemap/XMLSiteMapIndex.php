<?php
/**
 * src/models/sitemap/XMLSiteMapIndex.php
 *
 * PHP version 5
 *
 * @category  PHPFrame_Applications
 * @package   Mashine
 * @author    Lupo Montero <lupo@e-noise.com>
 * @copyright 2010 E-NOISE.COM LIMITED
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://github.com/E-NOISE/Mashine
 */

/**
 * XMLSiteMapIndex class
 *
 * @category PHPFrame_Applications
 * @package  Mashine
 * @author   Lupo Montero <lupo@e-noise.com>
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link     http://github.com/E-NOISE/Mashine
 * @since    1.0
 */
class XMLSiteMapIndex implements IteratorAggregate
{
    private $_sitemaps;

    /**
     * Constructor.
     *
     * @return void
     * @since  1.0
     */
    public function __construct()
    {
        $this->_sitemaps = new SplObjectStorage();
    }

    public function getIterator()
    {
        return $this->_sitemaps;
    }
}
