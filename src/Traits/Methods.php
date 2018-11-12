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
     */
    public function get()
    {
        /* @var CopernicaAPI $api */
        $api = $this->api;

        return $api->get();
    }

    /**
     * @return int|mixed
     */
    public function post(array $data)
    {
        /* @var CopernicaAPI $api */
        $api = $this->api;

        $api->setData($data);

        return $api->post();
    }

    /**
     * @return int|mixed
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
     */
    public function delete()
    {
        /* @var CopernicaAPI $api */
        $api = $this->api;

        if (!in_array($api->getResource(), $this->allow_delete_endpoints)) {
            throw new \UnexpectedValueException("Cannot delete this resource: ". $api->getResource());
        }

        return $api->delete();
    }
}
