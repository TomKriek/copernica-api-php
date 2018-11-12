<?php

namespace TomKriek\CopernicaAPI;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Exception\GuzzleException;
use TomKriek\Exceptions\BadCopernicaRequest;

/**
 * Class CopernicaAPI
 * @package TomKriek\CopernicaAPI
 * @see https://www.copernica.com/nl/documentation/rest-api for complete list of functionalities
 *
 * @method \TomKriek\CopernicaAPI\Endpoints\Collection collection(int $id)
 * @method \TomKriek\CopernicaAPI\Endpoints\Database database(int $id)
 * @method \TomKriek\CopernicaAPI\Endpoints\Datarequest datarequest(int $id)
 * @method \TomKriek\CopernicaAPI\Endpoints\Email email(int $id)
 * @method \TomKriek\CopernicaAPI\Endpoints\Emailingdocument emailingdocument(int $id)
 * @method \TomKriek\CopernicaAPI\Endpoints\Identity identity(int $id)
 * @method \TomKriek\CopernicaAPI\Endpoints\Logfiles logfiles(string $name)
 * @method \TomKriek\CopernicaAPI\Endpoints\Message message(int $id)
 * @method \TomKriek\CopernicaAPI\Endpoints\Minirule minirule(int $id)
 * @method \TomKriek\CopernicaAPI\Endpoints\Miniview miniview(int $id)
 * @method \TomKriek\CopernicaAPI\Endpoints\Profile profile(int $id)
 * @method \TomKriek\CopernicaAPI\Endpoints\Rule rule(int $id)
 * @method \TomKriek\CopernicaAPI\Endpoints\Subprofile subprofile(int $id)
 * @method \TomKriek\CopernicaAPI\Endpoints\Tags tags(string $tag)
 * @method \TomKriek\CopernicaAPI\Endpoints\Template template(int $id)
 * @method \TomKriek\CopernicaAPI\Endpoints\View view(int $id)
 */
class CopernicaAPI
{

    /* @var string API_GATEWAY */
    const API_GATEWAY = 'https://api.copernica.com';

    /* @var string VERSION */
    const VERSION = 'v1';

    /* @var string $token */
    private $token;

    /* @var string $method */
    private $method = 'GET';

    /* @var Client $http_client */
    private $http_client;

    /* @var string $resource */
    private $resource = '';

    /* @var string $extra */
    private $extra = '';

    /* @var array $params */
    private $params;

    /* @var array $data */
    private $data;

    public function __construct($token)
    {
        if (null === $this->http_client) {
            $this->http_client = new Client([
                'base_uri' => self::API_GATEWAY . '/' . self::VERSION . '/'
            ]);
        }

        $this->token = $token;
    }

    /**
     * @return int|mixed
     * @throws BadCopernicaRequest
     */
    public function get()
    {
        $this->method = 'GET';

        return $this->doRequest();
    }

    /**
     * @return int|mixed
     * @throws BadCopernicaRequest
     */
    public function post()
    {
        $this->method = 'POST';

        return $this->doRequest();
    }

    /**
     * @return int|mixed
     * @throws BadCopernicaRequest
     */
    public function put()
    {
        $this->method = 'PUT';

        return $this->doRequest();
    }

    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    public function setParams($params = null)
    {
        $this->params = $params;
    }

    public function getParams()
    {
        if (null === $this->params) {
            return [];
        }

        return $this->params;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function setExtra($extra)
    {
        $this->extra = $extra;
    }

    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @return int|mixed
     * @throws BadCopernicaRequest
     */
    public function delete()
    {
        $this->method = 'DELETE';

        return $this->doRequest();
    }

    private function buildURI()
    {
        $parts = [
            self::API_GATEWAY,
            self::VERSION,
            $this->resource,
        ];

        if ($this->extra !== '') {
            $parts[] = $this->extra;
        }

        $url = implode('/', $parts);

        $query = http_build_query(array('access_token' => $this->token) + $this->getParams());

        return new Uri($url . '?' . $query);
    }

    /**
     * @return int|mixed
     * @throws BadCopernicaRequest
     */
    private function doRequest()
    {
        $headers = [];

        $data = null;

        if (null !== $this->getData()) {
            $data = json_encode($this->getData());
        }

        if ($this->method === 'POST' || $this->method === 'PUT') {
            $headers['Content-Type'] = 'application/json';
        }

        if ($this->method === 'PUT') {
            $headers['Content-Length'] = strlen($data);
        }

        try {
            $uri = $this->buildURI();

            $request = new Request($this->method, $uri, $headers, $data);

            $response = $this->http_client->send($request);

            $created = $response->getHeader('X-Created');

            if (count($created) !== 0) {
                // Creation was succesful return id
                return 0;
            }

            $decoded = json_decode($response->getBody()->getContents());

            if (json_last_error() !== 0) {
                throw new \UnexpectedValueException("Json Error", json_last_error());
            }

            return $decoded;
        } catch (GuzzleException $exception) {
            throw new BadCopernicaRequest("Something went wrong.", 0, $exception);
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        $fqcn = '\TomKriek\CopernicaAPI\Endpoints\\'. ucfirst($name);

        $exists = class_exists($fqcn);

        if (!$exists) {
            throw new \Exception("Endpoint '". ucfirst($name) ."' does not exist.");
        }

        // Different behaviour for some endpoints
        switch ($name) {
            case 'database':
                if ($arguments[0] === 0) {
                    $this->resource = 'databases';
                } else {
                    $this->resource = 'database/' . $arguments[0];
                }
                break;
            default:
                $this->resource = $name;
        }

        return new $fqcn($this);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (null === $this->data) {
            return [];
        }

        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
