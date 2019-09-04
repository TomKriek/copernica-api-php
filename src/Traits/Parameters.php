<?php

namespace TomKriek\CopernicaAPI\Traits;

use TomKriek\CopernicaAPI\CopernicaAPI;

trait Parameters
{

    /**
     * @param int $limit
     * @return CopernicaAPI
     */
    public function limit($limit = null): CopernicaAPI
    {
        /* @var CopernicaAPI $api */
        $api = $this->api;

        $api->limit($limit);

        return $this->api;
    }

    /**
     * @param int $start
     * @return CopernicaAPI
     */
    public function start($start = null): CopernicaAPI
    {
        /* @var CopernicaAPI $api */
        $api = $this->api;

        $api->start($start);

        return $this->api;
    }

    /**
     * @param bool $total
     * @return CopernicaAPI
     */
    public function total($total = true): CopernicaAPI
    {
        /* @var CopernicaAPI $api */
        $api = $this->api;

        $api->total($total);

        return $this->api;
    }
}
