<?php

namespace TomKriek\CopernicaAPI\Endpoints;

use TomKriek\CopernicaAPI\CopernicaAPI;
use TomKriek\CopernicaAPI\Traits\Methods;

/**
 * Class Database
 * @package TomKriek\CopernicaAPI\Endpoints
 *
 * @method CopernicaAPI fields(array $parameters = [])
 * @method CopernicaAPI interests(array $parameters = [])
 * @method CopernicaAPI subprofiles($collection_id)
 * @method CopernicaAPI events(array $parameters = [])
 */
class Profile
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

        switch(strtolower($name)){
            case 'subprofiles':
                $this->api->setExtra($name . '/' . array_shift($arguments));
                break;
            default:
                $this->api->setExtra($name);
        }

        if (count($arguments) === 1) {
            $this->api->setParams(array_shift($arguments));
        }

        return $this->api;
    }
}
