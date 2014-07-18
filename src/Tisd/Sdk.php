<?php

namespace Tisd;

use Tisd\Sdk\Cache as TisdSdkCache;


class Sdk
{
    const HOSTNAME_DEVELOPMENT = 'dl.theimagingsource.com.dev';
    const HOSTNAME_PRODUCTION  = 'dl.theimagingsource.com';

    const LOCALE  = 'en_US';

    const VERSION = '2.0';

    const TIMEOUT = 10;

    protected $cache;
    protected $locale;
    protected $hostname;
    protected $version;
    protected $timeout;

    public function __construct($options = array())
    {
        $locale = self::LOCALE;

        $this->setCache(new TisdSdkCache());


        if (isset($options['locale'])) {
            $locale = $options['locale'];
        }

        $this->setLocale($locale);


        $hostname = $this->getDefaultHostname();

        if (isset($options['hostname'])) {
            $hostname = $options['hostname'];
        }

        $this->setHostname($hostname);


        $version = self::VERSION;

        if (isset($options['version'])) {
            $version = $options['version'];
        }

        $this->setVersion($version);


        $timeout = self::TIMEOUT;

        if (isset($options['timeout'])) {
            $version = $options['timeout'];
        }

        $this->setTimeout($timeout);
    }

    // --------------------------------------------------------------------------------

    protected function queryUrl($fragment)
    {
        $ret = false;

        $url = $this->buildUrl($fragment);

        if ($this->getCache()->getTtl() > 0) {

            $cacheId = $this->getCache()->getId($url);

            $ret = $this->getCache()->read($cacheId);

            if (false === $ret) {

                $ret = $this->requestUrl($url);

                $this->getCache()->write($cacheId, $ret);
            }

        } else {

            $ret = $this->requestUrl($url);
        }

        return $ret;
    }

    protected function requestUrl($url)
    {
        $context = stream_context_create(
                array(
                    'http' => array(
                        'timeout' => $this->getTimeout(),
                    )
                )
        );

        $ret = file_get_contents($url, false, $context);
        $ret = json_decode($ret, true);

        return $ret;
    }

    protected function buildUrl($fragment)
    {
        $ret = sprintf('http://%s/api/%s%s', $this->getHostname(), $this->getVersion(), $fragment);

        return $ret;
    }

    // --------------------------------------------------------------------------------

    public function getLocales()
    {
        return $this->queryUrl('/locales.json');
    }

    public function getPackages($categoryId = null, $sectionId = null, $packageId = null)
    {
        if (isset($categoryId) && isset($sectionId) && isset($packageId)) {

            $fragment = sprintf('/packages/%s/%s/%s/%s.json'
                    , $categoryId
                    , $sectionId
                    , $packageId
                    , $this->getLocale());
        } elseif (isset($categoryId) && isset($sectionId)) {

            $fragment = sprintf('/packages/%s/%s/%s.json'
                    , $categoryId
                    , $sectionId
                    , $this->getLocale());
        } elseif (isset($categoryId)) {

            $fragment = sprintf('/packages/%s/%s.json'
                    , $categoryId
                    , $this->getLocale());
        } else {

            $fragment = sprintf('/packages/%s.json'
                    , $this->getLocale());
        }

        return $this->queryUrl($fragment);
    }

    public function getPackageByUniqueId($uniqueId)
    {
        $fragment = sprintf('/get-package-by-unique-id/%s.json'
                , $uniqueId);

        return $this->queryUrl($fragment);
    }

    public function getPackageByProductCodeId($productCodeId)
    {
        $fragment = sprintf('/get-package-by-product-code-id/%s/%s.json'
                , $productCodeId
                , $this->getLocale());

        return $this->queryUrl($fragment);
    }

    public function getPackageByPackageId($packageId)
    {
        $fragment = sprintf('/get-package-by-package-id/%s/%s.json'
                , $packageId
                , $this->getLocale());

        return $this->queryUrl($fragment);
    }

    // --------------------------------------------------------------------------------

    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    public function getHostname()
    {
        return $this->hostname;
    }

    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function setCache($cache)
    {
        $this->cache = $cache;

        return $this;
    }

    public function getCache()
    {
        return $this->cache;
    }

    // --------------------------------------------------------------------------------

    protected function getDefaultHostname()
    {
        $hostname = gethostbyname(trim(`hostname`));

        if ('192.168' === substr($hostname, 0, 7)) {
            $ret = self::HOSTNAME_DEVELOPMENT;
        } else {
            $ret = self::HOSTNAME_PRODUCTION;
        }

        return $ret;
    }

    // --------------------------------------------------------------------------------
}