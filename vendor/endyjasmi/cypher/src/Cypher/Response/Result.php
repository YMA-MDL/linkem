<?php namespace EndyJasmi\Cypher\Response;

use ArrayAccess;
use Countable;
use Exception;
use Iterator;
use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Contracts\JsonableInterface;

class Result implements ArrayAccess, Countable, Iterator, ArrayableInterface, JsonableInterface
{
    protected $result;

    public function __construct(array $result)
    {
        $this->result = $result;
    }

    public function containsUpdates()
    {
        return $this->result['stats']['contains_updates'];
    }

    public function info()
    {
        return $this->result['stats'];
    }

    public function toArray()
    {
        $columns = $this->result['columns'];

        return array_map(
            function ($row) use ($columns) {
                return array_combine($columns, $row['row']);
            },
            $this->result['data']
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
        return isset($this->result['data'][$offset]);
    }

    public function offsetGet($offset)
    {
        if (!isset($this->result['data'][$offset])) {
            return null;
        }

        return array_combine(
            $this->result['columns'],
            $this->result['data'][$offset]['row']
        );
    }

    public function offsetSet($offset, $value)
    {
        throw new Exception('Invalid method');
    }

    public function offsetUnset($offset)
    {
        throw new Exception('Invalid method');
    }

    /**
     * Implement countable interface
     */
    public function count()
    {
        return count($this->result['data']);
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
