<?php
declare(strict_types=1);

namespace TomKriek\CopernicaAPI;

use BadMethodCallException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use TomKriek\CopernicaAPI\Exceptions\BadCopernicaRequest;
use UnexpectedValueException;

/**
 * Class CopernicaAPI
 * @package TomKriek\CopernicaAPI
 * @see https://www.copernica.com/nl/documentation/rest-api for complete list of functionalities
 *
 * @method Endpoints\Collection collection(int $id)
 * @method Endpoints\Condition condition($type, $id)
 * @method Endpoints\Database database(int $id)
 * @method Endpoints\Databases databases()
 * @method Endpoints\Datarequest datarequest(int $id)
 * @method Endpoints\Email email(int $id)
 * @method Endpoints\Emailingdocument emailingdocument(int $id)
 * @method Endpoints\Identity identity()
 * @method Endpoints\Interest interest()
 * @method Endpoints\Logfile logfile(string $name)
 * @method Endpoints\Logfiles logfiles()
 * @method Endpoints\Message message(int $id)
 * @method Endpoints\Minicondition minicondition($type, int $id)
 * @method Endpoints\Minirule minirule(int $id)
 * @method Endpoints\Miniview miniview(int $id)
 * @method Endpoints\Ms ms()
 * @method Endpoints\Profile profile(int $id)
 * @method Endpoints\Publisher publisher()
 * @method Endpoints\Rule rule(int $id)
 * @method Endpoints\Subprofile subprofile(int $id)
 * @method Endpoints\Tags tags(string $tag)
 * @method Endpoints\Template template(int $id)
 * @method Endpoints\View view(int $id)
 */
class CopernicaAPI
{

    /* @var string API_GATEWAY */
    private const API_GATEWAY = 'https://api.copernica.com';

    /* @var string $token */
    private $token;

    /* @var string $method */
    private $method = 'GET';

    /* @var string $version */
    private $version;

    /* @var Client $http_client */
    private $http_client;

    /* @var string $endpoint */
    private $endpoint = '';

    /* @var string $extra */
    private $extra = '';

    /* @var array $params */
    private $params;

    /* @var array $data */
    private $data;

    /* @var int $limit */
    private $limit;

    /* @var boolean $total */
    private $total;

    /* @var int $start */
    private $start;

    public function __construct(string $token, string $version = 'v1', bool $debug = false)
    {
        $this->version = $version;

        if (null === $this->http_client) {
            $this->http_client = new Client([
                'base_uri' => self::API_GATEWAY . '/' . $this->version . '/',
                'debug'    => $debug,
                'timeout'  => 30
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
     * @param array $data
     * @return int|mixed
     * @throws BadCopernicaRequest
     */
    public function post(array $data)
    {
        $this->method = 'POST';

        $this->setData($data);

        return $this->doRequest();
    }

    /**
     * @param array $data
     * @return int|mixed
     * @throws BadCopernicaRequest
     */
    public function put(array $data)
    {
        $this->method = 'PUT';

        $this->setData($data);

        return $this->doRequest();
    }

    /**
     * @param string $name
     */
    public function setEndpoint($name): void
    {
        $this->endpoint = $name;
    }

    /**
     * Setter for the parameters that will be used to build an query
     *
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * @param int $limit
     * @return CopernicaAPI
     */
    public function limit($limit = null): CopernicaAPI
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param int $start
     * @return CopernicaAPI
     */
    public function start(int $start): CopernicaAPI
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @param bool $total
     * @return CopernicaAPI
     */
    public function total(bool $total = true): CopernicaAPI
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Build the query parameters to be appended to the request URL towards Copernica
     *
     * @return string
     */
    public function buildQuery(): string
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

    /**
     * @return array
     */
    public function getParams(): array
    {
        if (null === $this->params) {
            return [];
        }

        return $this->params;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @param string $extra
     */
    public function setExtra($extra): void
    {
        $this->extra = $extra;
    }

    /**
     * @return string
     */
    public function getExtra(): string
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

    /**
     * Build complete URL to be used in the Request object
     *
     * @return Uri
     */
    private function buildURI(): Uri
    {
        $parts = [
            self::API_GATEWAY,
            $this->version,
            $this->getEndpoint(),
        ];

        if ($this->getExtra() !== '') {
            $parts[] = $this->getExtra();
        }

        $url = implode('/', $parts);

        $query = $this->buildQuery();

        return new Uri($url . '?' . $query);
    }

    /**
     * Returns an integer on succesful creation for post methods and the response of the call on other methods
     *
     * @return int|mixed
     *
     * @throws BadCopernicaRequest
     */
    private function doRequest()
    {
        $data = null;

        if (null !== $this->getData()) {
            $data = json_encode($this->getData());
        }

        $headers = [];
        if ($this->method === 'POST' || $this->method === 'PUT') {
            $headers['Content-Type'] = 'application/json';
        }

        try {
            $uri = $this->buildURI();

            $request = new Request($this->method, $uri, $headers, $data);

            $response = $this->http_client->send($request);

            $createdHeader = $response->getHeader('X-Created');

            if (count($createdHeader) !== 0) {
                return (int)array_shift($createdHeader);
            }

            $decoded = json_decode($response->getBody()->getContents(), false);

            if (json_last_error() !== 0) {
                throw new UnexpectedValueException('Json Error', json_last_error());
            }

            return $decoded;
        } catch (ClientException $exception) {
            throw new BadCopernicaRequest('Client Exception', $exception->getCode(), $exception);
        } catch (GuzzleException $exception) {
            throw new BadCopernicaRequest('Something went wrong.', 0, $exception);
        }
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return object
     *
     * @throws BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        $fqcn = '\TomKriek\CopernicaAPI\Endpoints\\' . ucfirst($name);

        $exists = class_exists($fqcn);

        if (!$exists) {
            throw new BadMethodCallException("Endpoint '" . ucfirst($name) . "' does not exist.");
        }

        $this->setEndpoint($name . (isset($arguments[0]) ? '/' . $arguments[0] : ''));

        return new $fqcn($this);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        if (null === $this->data) {
            return [];
        }

        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }
}
