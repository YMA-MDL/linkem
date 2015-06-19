<?php namespace EndyJasmi\Cypher;

use ArrayAccess;
use Countable;
use Exception;
use Iterator;
use EndyJasmi\Cypher\Response\Result;
use GuzzleHttp\Message\Response as GuzzleResponse;
use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Contracts\JsonableInterface;

class Response implements ArrayAccess, Countable, Iterator, ArrayableInterface, JsonableInterface
{
    protected $guzzleResponse;

    protected $response;

    public function __construct(GuzzleResponse $guzzleResponse)
    {
        $this->guzzleResponse = $guzzleResponse;

        $json = $this->guzzleResponse->json();

        $this->response = array(
            'results' => array_map(
                function ($result) {
                    return new Result($result);
                },
                $json['results']
            ),
            'errors' => $json['errors']
            );

        if (!empty($this->response['errors'])) {
            $error = $this->response['errors'][0]['code'];
            $error = "EndyJasmi\\Cypher\\StatusCodes\\" . str_replace('.', '\\', $error);

            throw new $error($this->response['errors'][0]['message']);
        }
    }

    public function toArray()
    {
        return array_map(
            function ($result) {
                return $result->toArray();
            },
            $this->response['results']
        );
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray());
    }

    /**
     * Implement array access interface
     */
    public function offsetExists($offset)
    {
        return isset($this->response['results'][$offset]);
    }

    public function offsetGet($offset)
    {
        if (!isset($this->response['results'][$offset])) {
            return null;
        }

        return $this->response['results'][$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new Exception('Invalid method');
    }

    public function offsetUnset($offset)
    {
        throw new InvalidMethod('Invalid method');
    }

    /**
     * Implement countable interface
     */
    public function count()
    {
        return count($this->response['results']);
    }

    /**
     * Implement iterator interface
     */
    protected $cursor = 0;

    public function current()
    {
        return $this[$this->cursor];
    }

    public function key()
    {
        return $this->cursor;
    }

    public function next()
    {
        $this->cursor++;
    }

    public function rewind()
    {
        $this->cursor = 0;
    }

    public function valid()
    {
        return isset($this[$this->cursor]);
    }
}
