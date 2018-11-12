<?php

namespace TomKriek\CopernicaAPI\Traits;

use TomKriek\CopernicaAPI\CopernicaAPI;

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
     * @throws \TomKriek\Exceptions\BadCopernicaRequest
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
     * @throws \TomKriek\Exceptions\BadCopernicaRequest
     */
    public function post(array $data)
    {
        /* @var CopernicaAPI $api */
        $api = $this->api;

        $api->setData($data);

        return $api->post();
    }

    /**
     * @param array $data
     * @return int|mixed
     * @throws \TomKriek\Exceptions\BadCopernicaRequest
     */
    public function put(array $data)
    {
        /* @var CopernicaAPI $api */
        $api = $this->api;

        $api->setData($data);

        return $api->put();
    }

    /**
     * @return int|mixed
     * @throws \TomKriek\Exceptions\BadCopernicaRequest
     * @throws \UnexpectedValueException
     */
    public function delete()
    {
        /* @var CopernicaAPI $api */
        $api = $this->api;

        if (!in_array($api->getResource(), $this->allow_delete_endpoints, true)) {
            throw new \UnexpectedValueException('Cannot delete this resource: ' . $api->getResource());
        }

        return $api->delete();
    }
}
