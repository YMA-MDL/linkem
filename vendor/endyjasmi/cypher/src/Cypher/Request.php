<?php namespace EndyJasmi\Cypher;

use ArrayAccess;
use Countable;
use Exception;
use Iterator;
use EndyJasmi\Cypher\Request\Statement;

class Request implements ArrayAccess, Countable, Iterator
{
    protected $cypher;

    protected $statements = array(
        'statements' => array()
        );

    public function __construct($cypher, $query, array $parameters = array())
    {
        $this->cypher = $cypher;

        $this->statement($query, $parameters);
    }

    public function beginTransaction()
    {
        return $this->cypher->beginTransaction($this);
    }

    public function commit()
    {
        $this->cypher->commit();
    }

    public function execute()
    {
        return $this->cypher->execute($this);
    }

    public function rollback()
    {
        $this->cypher->rollback();
    }

    public function statement($query, array $parameters = array())
    {
        $statement = new Statement($query, $parameters);

        $this->statements['statements'][] = $statement;

        return $this;
    }

    public function toArray()
    {
        return array(
            'statements' => array_map(
                function ($statement) {
                    return $statement->toArray();
                },
                $this->statements['statements']
            ));
    }

    /**
     * Implement array access interface
     */
    public function offsetExists($offset)
    {
        return isset($this->statements['statements'][$offset]);
    }

    public function offsetGet($offset)
    {
        if (!isset($this->statements['statements'][$offset])) {
            return null;
        }

        return $this->statements['statements'][$offset];
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
        return count($this->statements['statements']);
    }

    /**
     * Implement iterator interface
     */
    protected $cursor = 0;

    public function current()
    {
        return $this->statements['statements'][$this->cursor];
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
        return isset($this->statements['statements'][$this->cursor]);
    }
}
