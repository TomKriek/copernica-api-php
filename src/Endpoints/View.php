<?php

namespace TomKriek\CopernicaAPI\Endpoints;

use TomKriek\CopernicaAPI\CopernicaAPI;
use TomKriek\CopernicaAPI\Traits\Methods;

/**
 * Class Database
 * @package TomKriek\CopernicaAPI\Endpoints
 *
 * @method CopernicaAPI rules(array $parameters = [])
 * @method CopernicaAPI profiles(array $parameters = [])
 * @method CopernicaAPI views(array $parameters = [])
 * @method CopernicaAPI profileids(array $parameters = [])
 * @method CopernicaAPI rule($id, array $parameters = [])
 *
 */
class View
{

    use Methods;

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
        if ($name === 'rule') {
            $id = array_shift($arguments);
            $this->api->setExtra($name . '/' . $id);
        } else {
            $this->api->setExtra($name);
        }

        if (count($arguments) === 1) {
            $this->api->setParams(array_shift($arguments));
        }

        return $this->api;
    }
}
