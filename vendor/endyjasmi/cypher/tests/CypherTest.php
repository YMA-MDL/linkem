<?php namespace EndyJasmi;

use Mockery;
use PHPUnit_Framework_TestCase as TestCase;

class CypherTest extends TestCase
{
    protected $guzzle;

    public function setUp()
    {
        $this->guzzle = Mockery::mock('GuzzleHttp\Client');
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testGuzzleMethod()
    {
        $cypher = Mockery::mock('EndyJasmi\Cypher')
            ->makePartial();
        $cypher->shouldReceive('guzzle')
            ->once()
            ->andReturn($this->guzzle);

        $guzzle = $cypher->guzzle();

        $this->assertInstanceOf('GuzzleHttp\Client', $guzzle);
    }

    public function testHostMethod()
    {
        $cypher = new Cypher('https://user:pass@localhost:7473');

        $host = $cypher->host();

        $this->assertEquals('https://localhost:7473', $host);
    }

    public function testOperationMethod()
    {
        $guzzleRequest = Mockery::mock('GuzzleHttp\Message\Request');

        $guzzleResponse = Mockery::mock('GuzzleHttp\Message\Response');
        $guzzleResponse->shouldReceive('json')
            ->once()
            ->andReturn(array(
                'results' => array(),
                'errors' => array()
                ));

        $this->guzzle->shouldReceive('createRequest')
            ->once()
            ->andReturn($guzzleRequest);

        $this->guzzle->shouldReceive('send')
            ->with($guzzleRequest)
            ->once()
            ->andReturn($guzzleResponse);

        $cypher = Mockery::mock('EndyJasmi\Cypher')
            ->makePartial();
        $cypher->shouldReceive('guzzle')
            ->once()
            ->andReturn($this->guzzle);

        $result = $cypher->operation('post', 'transaction');

        $this->assertInstanceOf('GuzzleHttp\Message\Request', $result['guzzleRequest']);
        $this->assertInstanceOf('GuzzleHttp\Message\Response', $result['guzzleResponse']);
        $this->assertInstanceOf('EndyJasmi\Cypher\Response', $result['response']);
    }

    public function testExecute()
    {
        $request = Mockery::mock('EndyJasmi\Cypher\Request');
        $request->shouldReceive('toArray')
            ->once()
            ->andReturn(array(
                'statements' => array()
                ));

        $guzzleRequest = Mockery::mock('GuzzleHttp\Message\Request');
        $guzzleResponse = Mockery::mock('GuzzleHttp\Message\Response');
        $response = Mockery::mock('EndyJasmi\Cypher\Response');

        $cypher = Mockery::mock('EndyJasmi\Cypher')
            ->makePartial();
        $cypher->shouldReceive('operation')
            ->once()
            ->andReturn(array(
                'guzzleRequest' => $guzzleRequest,
                'guzzleResponse' => $guzzleResponse,
                'response' => $response
                ));

        $result = $cypher->execute($request);

        $this->assertInstanceOf('EndyJasmi\Cypher\Response', $result);
    }

    public function testStatement()
    {
        $cypher = new Cypher;

        $statement = $cypher->statement('test');

        $this->assertInstanceOf('EndyJasmi\Cypher\Request', $statement);
    }
}
