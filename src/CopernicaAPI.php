<?php

namespace TomKriek\CopernicaAPI;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Exception\GuzzleException;
use TomKriek\CopernicaAPI\Exceptions\BadCopernicaRequest;

/**
 * Class CopernicaAPI
 * @package TomKriek\CopernicaAPI
 * @see https://www.copernica.com/nl/documentation/rest-api for complete list of functionalities
 *
 * @method Endpoints\Collection collection(int $id)
 * @method Endpoints\Database database(int $id)
 * @method Endpoints\Datarequest datarequest(int $id)
 * @method Endpoints\Email email(int $id)
 * @method Endpoints\Emailingdocument emailingdocument(int $id)
 * @method Endpoints\Identity identity(int $id)
 * @method Endpoints\Logfiles logfiles(string $name)
 * @method Endpoints\Message message(int $id)
 * @method Endpoints\Minirule minirule(int $id)
 * @method Endpoints\Miniview miniview(int $id)
 * @method Endpoints\Profile profile(int $id)
 * @method Endpoints\Rule rule(int $id)
 * @method Endpoints\Subprofile subprofile(int $id)
 * @method Endpoints\Tags tags(string $tag)
 * @method Endpoints\Template template(int $id)
 * @method Endpoints\View view(int $id)
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

    /* @var int $limit */
    private $limit;

    /* @var boolean $total  */
    private $total;

    /* @var int $start */
    private $start;

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

    /**
     * @param int $limit
     * @return CopernicaAPI
     */
    public function limit($limit = null)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param int $start
     * @return CopernicaAPI
     */
    public function start($start = null)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @param bool $total
     * @return CopernicaAPI
     */
    public function total($total = true)
    {
        $this->total = $total;

        return $this;
    }

    public function buildQuery()
    {
        $parts = [];

        $parts['access_token'] = $this->token;

        if (null !== $this->start) {
            $parts['start'] = $this->start;
        }

        if (null !== $this->limit) {
            $parts['limit'] = $this->limit;
        }

        if (null !== $this->total) {
            $parts['total'] = $this->total;
        }

        if (count($this->getParams()) > 0) {
            $parts = array_merge($parts, $this->getParams());
        }

        return http_build_query($parts);
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

        $query = $this->buildQuery();

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
                throw new \UnexpectedValueException('Json Error', json_last_error());
            }

            return $decoded;
        } catch (GuzzleException $exception) {
            throw new BadCopernicaRequest('Something went wrong.', 0, $exception);
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @throws \Exception
     * @return object
     */
    public function __call($name, $arguments)
    {
        $fqcn = '\TomKriek\CopernicaAPI\Endpoints\\' . ucfirst($name);

        $exists = class_exists($fqcn);

        if (!$exists) {
            throw new \BadMethodCallException("Endpoint '" . ucfirst($name) . "' does not exist.");
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
