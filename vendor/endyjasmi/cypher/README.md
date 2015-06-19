#Neo4j Cypher PHP Adapter
[![Build Status](https://travis-ci.org/endyjasmi/cypher.svg?branch=1.2.6)](https://travis-ci.org/endyjasmi/cypher) [![Latest Stable Version](https://poser.pugx.org/endyjasmi/cypher/v/stable.svg)](https://packagist.org/packages/endyjasmi/cypher) [![Total Downloads](https://poser.pugx.org/endyjasmi/cypher/downloads.svg)](https://packagist.org/packages/endyjasmi/cypher) [![Latest Unstable Version](https://poser.pugx.org/endyjasmi/cypher/v/unstable.svg)](https://packagist.org/packages/endyjasmi/cypher) [![License](https://poser.pugx.org/endyjasmi/cypher/license.svg)](https://packagist.org/packages/endyjasmi/cypher)

Cypher is a PHP adapter for Neo4j ReST API cypher endpoint. Cypher aims to take the pain out of sending cypher query to Neo4j server. I believe that cypher will be a major part of Neo4j in near future. In short, this library focus solely on sending query to Neo4j database. For those looking for adapter on all the Neo4j ReST API can try this [great library](https://github.com/jadell/neo4jphp).

For those that dont know, Neo4j is a graph database. More information about Neo4j can be found [here](http://neo4j.com/) and information for cypher can be found [here](http://neo4j.com/docs/2.1.1/cypher-query-lang/). It also serve as a [laravel 4](http://laravel.com/) package and it is licensed under MIT so you can do whatever you want with it.

For more in-depth documentation and example, do visit [project wiki](https://github.com/endyjasmi/cypher/wiki).

##Requirement
1. PHP 5.4 and above
2. Neo4j 2.0 and above

##Features
1. Send cypher to the server
2. Send multiple cypher in single request
3. Support cypher transaction
4. Support native status code
5. Double as Laravel 4 package

##Installation
This library is available through [composer](https://packagist.org/packages/endyjasmi/cypher). If you dont know how to use composer, a tutorial can be found [here](http://code.tutsplus.com/tutorials/easy-package-management-with-composer--net-25530).

##Quickstart
```
$cypher = new Cypher;

$result = $cypher->statement(
	'CREATE (jeffrey:Person {information}) RETURN jeffrey.name AS name',
	array('information' => array(
		'name' => 'Jeffrey Jasmi',
		'born' => 1987
	))
)
->execute();

echo $result[0][0]['name']; // Jeffrey Jasmi
```

##Documentation
For more in-depth documentation and example, do visit [project wiki](https://github.com/endyjasmi/cypher/wiki).

##Feedback
If you have any feature request, bug report, proposal, comment, or anything related to this library. Do not hesitate to [open a new issues](https://github.com/endyjasmi/cypher/issues/new).