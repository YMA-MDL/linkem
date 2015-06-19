<?php namespace EndyJasmi\Cypher\Request;

class Statement
{
    protected $statement = array(
        'statement' => null,
        'parameters' => array(),
        'includeStats' => true
        );

    public function __construct($query, array $parameters = array())
    {
        $this->setQuery($query)
            ->setParameters($parameters);
    }

    public function getParameters()
    {
        return $this->statement['parameters'];
    }

    public function getQuery()
    {
        return $this->statement['statement'];
    }

    public function setParameters(array $parameters)
    {
        $this->statement['parameters'] = $parameters;

        return $this;
    }

    public function setQuery($query)
    {
        $this->statement['statement'] = $query;

        return $this;
    }

    public function toArray()
    {
        if (empty($this->statement['parameters'])) {
            unset($this->statement['parameters']);
        }

        return $this->statement;
    }
}
