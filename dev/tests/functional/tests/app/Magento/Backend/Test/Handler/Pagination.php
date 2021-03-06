<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Backend\Test\Handler;

use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Pagination
 * Used to omit possible issue, when searched Id is not on the same page in cURL response
 */
class Pagination
{
    /**
     * Pattern for searching grid table in cURL response
     *
     * @var string
     */
    protected $regExpPattern;

    /**
     * Url of cURL request
     *
     * @var string
     */
    protected $url;

    /**
     * Setting all Pagination params for Pagination object.
     * Required url for cURL request and regexp pattern for searching in cURL response.
     *
     * @param $url
     * @param $regExpPattern
     */
    public function __construct($url, $regExpPattern)
    {
        $this->url = $url;
        $this->regExpPattern = $regExpPattern;
    }

    /**
     * Retrieves id from cURL response
     *
     * @throws \Exception
     * @return mixed
     */
    public function getId()
    {
        $url = $_ENV['app_backend_url'] . $this->url;
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0');
        $response = $curl->read();
        $curl->close();
        preg_match($this->regExpPattern, $response, $matches);
        if (empty($matches)) {
            throw new \Exception('Cannot find id');
        }
        return $matches[1];
    }
}
