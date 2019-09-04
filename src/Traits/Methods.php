<?php

namespace TomKriek\CopernicaAPI\Traits;

use TomKriek\CopernicaAPI\CopernicaAPI;
use TomKriek\CopernicaAPI\Exceptions\BadCopernicaRequest;
use UnexpectedValueException;

trait Methods
{

    private $allow_delete_endpoints = [
        'view',
        'rule',
        'profile',
        'subprofile',
        'collection',
        'miniview',
        'minirule',
    ];

    /**
     * @return int|mixed
     * @throws BadCopernicaRequest
     */
    public function get()
    {
        /* @var CopernicaAPI $api */
        $api = $this->api;

        return $api->get();
    }

    /**
     * @param array $data
     * @return int|mixed
     * @throws BadCopernicaRequest
     */
    public function post(array $data)
    {
        /* @var CopernicaAPI $api */
        $api = $this->api;

        return $api->post($data);
    }

    /**
     * @param array $data
     * @return int|mixed
     * @throws BadCopernicaRequest
     */
    public function put(array $data)
    {
        /* @var CopernicaAPI $api */
        $api = $this->api;

        return $api->put($data);
    }

    /**
     * @return int|mixed
     * @throws BadCopernicaRequest
     * @throws UnexpectedValueException
     */
    public function delete()
    {
        /* @var CopernicaAPI $api */
        $api = $this->api;

        if (!in_array($api->getEndpoint(), $this->allow_delete_endpoints, true)) {
            throw new UnexpectedValueException('Cannot delete this resource');
        }

        return $api->delete();
    }
}
