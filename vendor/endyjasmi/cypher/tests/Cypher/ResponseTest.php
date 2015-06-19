<?php namespace EndyJasmi\Cypher;

use Mockery;
use PHPUnit_Framework_TestCase as TestCase;

class ResponseTest extends TestCase
{
    protected $guzzleResponse;

    protected $mockResponse = array(
        'results' => array(
            array(
                'columns' => array('name', 'born'),
                'data' => array(
                    array('row' => array('Jeffrey Jasmi', 1987)),
                    array('row' => array('Endy Jasmi', 1990)),
                    array('row' => array('Donney Jasmi', 1995))
                    ),
                'stats' => array(
                    'constraints_added' => 0,
                    'constraints_removed' => 0,
                    'contains_updates' => false,
                    'indexes_added' => 0,
                    'indexes_removed' => 0,
                    'labels_added' => 0,
                    'labels_removed' => 0,
                    'nodes_created' => 0,
                    'nodes_deleted' => 0,
                    'properties_set' => 0,
                    'relationship_created' => 0,
                    'relationship_deleted' => 0
                    )
                ),
            ),
        'errors' => array()
        );

    protected $response;

    public function setUp()
    {
        $this->guzzleResponse = Mockery::mock('GuzzleHttp\Message\Response');
        $this->guzzleResponse->shouldReceive('json')
            ->once()
            ->andReturn($this->mockResponse);

        $this->response = new Response($this->guzzleResponse);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testResponseToArray()
    {
        $array = $this->response->toArray();

        $this->assertCount(1, $array);
        $this->assertInternalType('array', $array[0]);
    }

    public function testResponseToJson()
    {
        $json = $this->response->toJson();

        $this->assertEquals(
            '[[{"name":"Jeffrey Jasmi","born":1987},{"name":"Endy Jasmi","born":1990},'.
            '{"name":"Donney Jasmi","born":1995}]]',
            $json
        );
    }

    public function testCountable()
    {
        $count = count($this->response);

        $this->assertEquals(1, $count);
    }

    public function testArrayAccess()
    {
        $result = $this->response[0];

        $this->assertInstanceOf('EndyJasmi\Cypher\Response\Result', $result);
    }

    public function testIterator()
    {
        $results = array();

        foreach ($this->response as $result) {
            $results[] = $result;
        }

        $this->assertCount(1, $results);
        $this->assertInstanceOf('EndyJasmi\Cypher\Response\Result', $results[0]);
    }
}
