<?php

namespace TomKriek\CopernicaAPI\Endpoints;

use TomKriek\CopernicaAPI\CopernicaAPI;
use TomKriek\CopernicaAPI\Traits\Arguments;
use TomKriek\CopernicaAPI\Traits\Methods;
use TomKriek\CopernicaAPI\Traits\Parameters;

class AbstractEndpoint
{
    use Methods;
    use Parameters;
    use Arguments;

    /* @var CopernicaAPI $api */
    protected $api;

    public function __construct(CopernicaAPI $api)
    {
        $this->api = $api;
    }
}
