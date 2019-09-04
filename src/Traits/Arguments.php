<?php

namespace TomKriek\CopernicaAPI\Traits;

use TomKriek\CopernicaAPI\CopernicaAPI;

trait Arguments
{
    public function __call($name, $arguments)
    {
        /* @var CopernicaAPI $api */
        $api = $this->api;

        switch (count($arguments)) {
            case 0:
                $api->setExtra($name);
                break;
            case 1:
                $api->setParams(array_shift($arguments));
                $api->setExtra($name);
                break;
            case 2:
                $id = array_shift($arguments);
                $api->setExtra($name . '/' . $id);
                $api->setParams(array_shift($arguments));
                break;
        }

        return $api;
    }
}
