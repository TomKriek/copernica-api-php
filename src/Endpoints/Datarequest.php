<?php

namespace TomKriek\CopernicaAPI\Endpoints;

use TomKriek\CopernicaAPI\CopernicaAPI;
use TomKriek\CopernicaAPI\Traits\Methods;
use TomKriek\CopernicaAPI\Traits\Parameters;

/**
 * Class Database
 * @package TomKriek\CopernicaAPI\Endpoints
 *
 * @method CopernicaAPI data(array $parameters = [])
 * @method CopernicaAPI status(array $parameters = [])
 */
class Datarequest
{

    use Methods;
    use Parameters;

    /* @var CopernicaAPI $api */
    private $api;

    /**
     * Database constructor.
     * @param CopernicaAPI $api
     */
    public function __construct(CopernicaAPI $api)
    {
        $this->api = $api;
    }

    public function __call($name, $arguments)
    {
        $this->api->setExtra($name);

        if (count($arguments) === 1) {
            $this->api->setParams(array_shift($arguments));
        }

        return $this->api;
    }
}
