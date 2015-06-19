<?php namespace EndyJasmi;

use EndyJasmi\Cypher\Request;
use EndyJasmi\Cypher\Response;
use GuzzleHttp\Client;

class Cypher
{
    protected $config = array(
        'scheme' => 'http',
        'host' => 'localhost',
        'port' => 7474,
        'user' => null,
        'pass' => null
        );

    protected $guzzle;

    protected $operationUrls = array(
        'beginTransaction' => null,
        'commitTransaction' => null,
        'transaction' => null
        );

    protected $options = array(
        'config' => array(
            'curl' => array(
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
                )
            ),
        'headers' => array(
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Stream' => 'true'
            )
        );

    public function __construct($host = null)
    {
        if (isset($host)) {
            $config = parse_url($host);
            $this->config = array_merge($this->config, $config);

            if (isset($this->config['user']) && isset($this->config['pass'])) {
                $this->options['auth'] = array($this->config['user'], $this->config['pass']);
            }
        }

        $host = $this->host();
        $this->operationUrls['beginTransaction'] = "$host/db/data/transaction";
        $this->operationUrls['transaction'] = "$host/db/data/transaction/commit";
    }

    public function beginTransaction(Request $request = null)
    {
        $options = $this->options;

        if (isset($request)) {
            $options['json'] = $request->toArray();
        }

        $result = $this->operation('post', 'beginTransaction', $options);

        $location = $result['guzzleResponse']->getHeader('Location');
        $commit = $result['guzzleResponse']->json();
        $commit = $commit['commit'];

        $this->operationUrls['commitTransaction'] = $commit;
        $this->operationUrls['transaction'] = $location;

        return $result['response'];
    }

    public function commit()
    {
        $result = $this->operation('post', 'commitTransaction');

        $host = $this->host();
        $this->operationUrls['commitTransaction'] = null;
        $this->operationUrls['transaction'] = "$host/db/data/transaction/commit";
    }

    public function execute(Request $request)
    {
        $options = $this->options;
        $options['json'] = $request->toArray();

        $result = $this->operation('post', 'transaction', $options);

        return $result['response'];
    }

    public function guzzle()
    {
        if (is_null($this->guzzle)) {
            $this->guzzle = new Client;
        }

        return $this->guzzle;
    }

    public function host()
    {
        $scheme = $this->config['scheme'];
        $host = $this->config['host'];
        $port = $this->config['port'];

        return "$scheme://$host:$port";
    }

    public function operation($method, $operation, array $options = array())
    {
        if (empty($options)) {
            $options = $this->options;
        }

        $guzzle = $this->guzzle();
        $guzzleRequest = $guzzle->createRequest(
            $method,
            $this->operationUrls[$operation],
            $options
        );
        $guzzleResponse = $guzzle->send($guzzleRequest);
        $response = new Response($guzzleResponse);

        return array(
            'guzzleRequest' => $guzzleRequest,
            'guzzleResponse' => $guzzleResponse,
            'response' => $response
            );
    }

    public function rollback()
    {
        $result = $this->operation('delete', 'transaction');

        $host = $this->host();
        $this->operationUrls['commitTransaction'] = null;
        $this->operationUrls['transaction'] = "$host/db/data/transaction/commit";
    }

    public function statement($query, array $parameters = array())
    {
        return new Request($this, $query, $parameters);
    }
}
