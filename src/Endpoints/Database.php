<?php

namespace TomKriek\CopernicaAPI\Endpoints;

use TomKriek\CopernicaAPI\CopernicaAPI;
use TomKriek\CopernicaAPI\Traits\Methods;
use TomKriek\CopernicaAPI\Traits\Parameters;

/**
 * Class Database
 * @package TomKriek\CopernicaAPI\Endpoints
 *
 * @method CopernicaAPI unsubscribe(array $parameters = [])
 * @method CopernicaAPI fields(array $parameters = [])
 * @method CopernicaAPI field($id, array $parameters = [])
 * @method CopernicaAPI interests(array $parameters = [])
 * @method CopernicaAPI collections(array $parameters = [])
 * @method CopernicaAPI profiles(array $parameters = [])
 * @method CopernicaAPI profileids(array $parameters = [])
 * @method CopernicaAPI views(array $parameters = [])
 */
class Database
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
        if ($name === 'field') {
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
