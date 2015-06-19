<?php namespace EndyJasmi;

use PHPUnit_Framework_TestCase as TestCase;
use EndyJasmi\Cypher;
use EndyJasmi\Cypher\StatusCodes\Neo\ClientError\Statement\EntityNotFound;

class IntegratedTest extends TestCase
{
    public function testMe()
    {
        $cypher = new Cypher;

        $result = $cypher->statement(
            'CREATE (person {information}) RETURN person.name',
            array('information' => array(
                'name' => 'Jeffrey Jasmi',
                'born' => 1987
            ))
        )
        ->execute();

        var_dump($result[0]->containsUpdates());
        var_dump($result[0]->info());
    }

    public function testBasicCase()
    {
        $cypher = new Cypher;

        $response = $cypher->statement('CREATE (n) RETURN id(n) AS id')
            ->execute();

        var_dump($response[0][0]['id']); // Return id
    }

    public function testBatchCase()
    {
        $cypher = new Cypher('http://localhost:7474');

        $response = $cypher->statement(
            'CREATE (table {table}) RETURN table.type',
            array('table' => array('type' => 'table'))
        )
        ->statement(
            'CREATE (chair {chair}) RETURN chair.type',
            array('chair' => array('type' => 'chair'))
        )
        ->execute();

        var_dump($response[0][0]['table.type']); // Return table
        var_dump($response[1][0]['chair.type']); // Return chair
    }

    public function testTransactionCommitCase()
    {
        $cypher = new Cypher('http://localhost:7474');

        $cypher->beginTransaction();

        $create = $cypher->statement('CREATE (n) RETURN id(n) AS id')
            ->execute();

        $cypher->commit();

        $fetch = $cypher->statement(
            'START n = node({id}) RETURN id(n) AS id',
            array('id' => $create[0][0]['id'])
        )
        ->execute();

        var_dump($fetch[0][0]['id']); // Changes persist after commit
    }

    public function testTransactionRollback()
    {
        $cypher = new Cypher('http://localhost:7474');

        $cypher->beginTransaction();

        $create = $cypher->statement('CREATE (n) RETURN id(n) AS id')
            ->execute();

        $cypher->rollback();

        try {
            $fetch = $cypher->statement(
                'START n = node({id}) RETURN id(n) AS id',
                array('id' => $create[0][0]['id'])
            )->execute();
        } catch (EntityNotFound $error) {
            // var_dump($error->code);
            var_dump($error->getMessage());
        }
    }
}
