<?php

	class SolariumTest extends \PHPUnit\Framework\TestCase{

		/**
     * setUp compartido por todos los tests con un mensaje de error de ejemplo para registrar en Apache Solr
     *
     * @return void
    */
    public function setUp() {

        $this->msg = array(
            'level' => Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => ['foo' => 7, 'bar', 
                          'class' => new \stdClass()],
            'datetime' => new \DateTime('@0'),
            'extra' => array(),
            'message' => 'Logging an error',
        );
    }


    /**
     * test creating a Log using Mocks of PHPUnit
     *
     * @return void
    */
    public function testAddLogWithPHPUnitMock(){

    	// En PHPUnit, una vez creado un objeto colaborador es necesario indicarle los métodos que van a ser sobreescritos mediante el método setMethods. Además, es necesario indicar al framework que no utilice el constructor propio de la clase colaboradora.

       $client = $this->getMockBuilder('Solarium\Client')
            ->setMethods(array('addDocuments', 'createUpdate', 'update'))
            ->disableOriginalConstructor()
            ->getMock();

        $updateMock = $this->getMockBuilder("Solarium\QueryType\Update\Query\Query")
            ->setMethods(array('addDocument', 'addCommit', 'createDocument'))
            ->disableOriginalConstructor()
            ->getMock();

        $updateMock->expects($this->any())
                ->method('addDocument');

        $className = "Solarium\QueryType\Update\Query\Document\Document";
        $documentMock = $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();

        $updateMock->expects($this->any())
                ->method('createDocument')
                ->willReturn($documentMock);

        $client->expects($this->any())
            ->method('createUpdate')
            ->willReturn($updateMock);

        $solrHandler = new SolrHandler(array());
        $solrHandler->setClient($client);
        $solrHandler->handle($this->msg);
        $solrHandler->handleBatch(array($this->msg));
    }


    /**
     * test creating a Log using Mocks of Prophecy
     *
     * @return void
    */
    public function testAddLogWithProphecy(){

    	// La creación de los objetos colaboradores se realizan con el método prophesize, sin necesidad de especificarle ningún método ni otro tipo de configuraciones. El comportamiento viene definido por las expectativas que creamos con la llamada a cada método existente en la clase. Por ejemplo, la llamada al método shouldBeCalled indica que el método precedente debería ejecutarse para que ese test sea considerado como válido. De la misma forma, willReturn especificará la salida que tendrá el método que le precede.

        $documentProphecy = $this->prophesize(Document::class);

        $updateProphecy = $this->prophesize(Query::class);
        $updateProphecy->addDocument(Argument::any())->shouldBeCalled();
        $updateProphecy->addCommit()->shouldBeCalled();
        $updateProphecy->createDocument()->shouldBeCalled();
        $updateProphecy->createDocument()->willReturn($documentProphecy->reveal());

        $clientProphecy = $this->prophesize("Solarium\Client");
        $clientProphecy->createUpdate()->willReturn($updateProphecy->reveal());

        $clientProphecy->createUpdate()->shouldBeCalled();
        $clientProphecy->update($updateProphecy)->shouldBeCalled();

        $solrHandler = new SolrHandler(array());
        $solrHandler->setClient($clientProphecy->reveal());
        $solrHandler->handle($this->msg);
        $solrHandler->handleBatch(array($this->msg));
    }

     /**
     * test creating a Log using Mocks of Mockery
     *
     * @return void
    */
    public function testAddLogWithMockery() {


        $mockStrDocument = "Solarium\QueryType\Update\Query\Document\Document[addField]";
        $documentMockery = \Mockery::mock($mockStr);

        $methods = "addDocument,addCommit,createDocument";
        $mockStrUpdate = "Solarium\QueryType\Update\Query\Query[$methods]";
        $updateMockery = \Mockery::mock($mockStrUpdate );
        $updateMockery->shouldReceive('addDocument')->atLeast(1);
        $updateMockery->shouldReceive('addCommit')->atLeast(1);
        $updateMockery->shouldReceive('createDocument')
                        ->atLeast(1)
                        ->andReturn($documentMockery);

        $clientMockery = \Mockery::mock(Client::class);
        $clientMockery->shouldReceive('createUpdate')->andReturn($updateMockery);
        $clientMockery->shouldReceive('update')->with($updateMockery)->atLeast(1);

        $solrHandler = new SolrHandler(array());
        $solrHandler->setClient($clientMockery);
        $solrHandler->handle($this->msg);
        $solrHandler->handleBatch(array($this->msg));

    }


    /**
     * test creating a Log using Mocks of Phake
     *
     * @return void
    */
    public function testAddLogWithPhake() {

        $document = \Phake::mock(Document::class);
        $update = \Phake::mock(Query::class);

        \Phake::when($update)->createDocument()->thenReturn($document);
        $update->addDocument($document);
        $update->addCommit();

        \Phake::verify($update)->addDocument($document);
        \Phake::verify($update)->addCommit();

        $client = \Phake::mock(Client::class);
        \Phake::when($client)->createUpdate()->thenReturn($update);
        $client->update($update);

        $solrHandler = new SolrHandler(array());
        $solrHandler->setClient($client);
        $solrHandler->handle($this->msg);
        $solrHandler->handleBatch(array($this->msg));

    }


	}


?>