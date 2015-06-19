<?php namespace EndyJasmi\Cypher\Response;

use PHPUnit_Framework_TestCase as TestCase;

class ResultTest extends TestCase
{
    protected $mockResult = array(
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
        );

    protected $result;

    public function setUp()
    {
        $this->result = new Result($this->mockResult);
    }

    public function testResultToArray()
    {
        $array = $this->result->toArray();

        $this->assertCount(3, $array);
        $this->assertArrayHasKey('name', $array[0]);
        $this->assertEquals('Endy Jasmi', $array[1]['name']);
        $this->assertEquals(1995, $array[2]['born']);
    }

    public function testResultToJson()
    {
        $json = $this->result->toJson();

        $this->assertEquals(
            '[{"name":"Jeffrey Jasmi","born":1987},{"name":"Endy Jasmi","born":1990},'.
            '{"name":"Donney Jasmi","born":1995}]',
            $json
        );
    }

    public function testCountable()
    {
        $count = count($this->result);

        $this->assertEquals(3, $count);
    }

    public function testArrayAccess()
    {
        $endy = $this->result[1];

        $this->assertEquals('Endy Jasmi', $endy['name']);
        $this->assertEquals(1990, $endy['born']);
    }

    public function testIterator()
    {
        $rows = array();

        foreach ($this->result as $row) {
            $rows[] = $row;
        }

        $this->assertCount(3, $rows);
        $this->assertEquals('Endy Jasmi', $rows[1]['name']);
    }

    public function testContainsUpdates()
    {
        $updates = $this->result->containsUpdates();

        $this->assertFalse($updates);
    }

    public function testInfo()
    {
        $info = $this->result->info();

        $this->assertArrayHasKey('nodes_created', $info);
    }
}
