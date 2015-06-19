<?php namespace EndyJasmi\Cypher\Request;

use PHPUnit_Framework_TestCase as TestCase;

class StatementTest extends TestCase
{
    protected $statement;

    public function setUp()
    {
        $this->statement = new Statement('query', array());
    }

    public function testQueryMethod()
    {
        $getQueryBeforeSet = $this->statement->getQuery();
        $returnStatement = $this->statement->setQuery('test');
        $getQueryAfterSet = $this->statement->getQuery();

        $this->assertEquals('query', $getQueryBeforeSet);
        $this->assertInstanceOf('EndyJasmi\Cypher\Request\Statement', $returnStatement);
        $this->assertEquals('test', $getQueryAfterSet);
    }

    public function testParametersMethod()
    {
        $parameters = array(
            'parameter' => 'value'
            );

        $getParametersBeforeSet = $this->statement->getParameters();
        $returnStatement = $this->statement->setParameters($parameters);
        $getParametersAfterSet = $this->statement->getParameters();

        $this->assertEmpty($getParametersBeforeSet);
        $this->assertInstanceOf('EndyJasmi\Cypher\Request\Statement', $returnStatement);
        $this->assertNotEmpty($getParametersAfterSet);
    }

    public function testParameterToArray()
    {
        $query = "CREATE (n {properties}) RETURN n";
        $parameters = array(
            'properties' => array(
                'name' => 'Endy Jasmi',
                'born' => 1990
                )
            );

        $statement = new Statement($query, $parameters);

        $array = $statement->toArray();

        $this->assertInternalType('array', $array);
        $this->assertArrayHasKey('statement', $array);
        $this->assertArrayHasKey('parameters', $array);
    }
}
