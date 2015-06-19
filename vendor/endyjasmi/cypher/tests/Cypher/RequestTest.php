<?php namespace EndyJasmi\Cypher;

use Mockery;
use PHPUnit_Framework_TestCase as TestCase;

class RequestTest extends TestCase
{
    protected $cypher;

    protected $request;

    public function setUp()
    {
        $this->cypher = Mockery::mock('EndyJasmi\Cypher');

        $this->request = new Request($this->cypher, 'query', array());
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCountable()
    {
        $count = count($this->request);

        $this->assertEquals(1, $count);
    }

    public function testStatementMethod()
    {
        $returnRequest = $this->request->statement('query');
        $count = count($returnRequest);

        $this->assertInstanceOf('EndyJasmi\Cypher\Request', $returnRequest);
        $this->assertEquals(2, $count);
    }

    public function testArrayAccess()
    {
        $statement = $this->request[0];

        $this->assertInstanceOf('EndyJasmi\Cypher\Request\Statement', $statement);
    }

    public function testIterator()
    {
        $statements = array();

        foreach ($this->request as $statement) {
            $statements[] = $statement;
        }

        $this->assertCount(1, $statements);
    }

    public function testRequestToArray()
    {
        $this->request->statement('query');

        $array = $this->request->toArray();

        $this->assertArrayHasKey('statements', $array);
        $this->assertCount(2, $array['statements']);
        $this->assertArrayHasKey('statement', $array['statements'][0]);
    }

    public function testExecuteMethod()
    {
        $response = Mockery::mock('EndyJasmi\Cypher\Response');

        $this->cypher->shouldReceive('execute')
            ->with($this->request)
            ->once()
            ->andReturn($response);

        $response = $this->request->execute();

        $this->assertInstanceOf('EndyJasmi\Cypher\Response', $response);
    }

    public function testBeginTransactionMethod()
    {
        $response = Mockery::mock('EndyJasmi\Cypher\Response');

        $this->cypher->shouldReceive('beginTransaction')
            ->with($this->request)
            ->once()
            ->andReturn($response);

        $response = $this->request->beginTransaction();

        $this->assertInstanceOf('EndyJasmi\Cypher\Response', $response);
    }

    public function testCommitMethod()
    {
        $this->cypher->shouldReceive('commit')
            ->once();

        $this->request->commit();
    }
    
    public function testRollbackMethod()
    {
        $this->cypher->shouldReceive('rollback')
            ->once();

        $this->request->rollback();
    }
}
