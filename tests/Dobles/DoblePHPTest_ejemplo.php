<?php


    /**
     * test creating a Log using Mocks of PHPUnit
     *
     * @return void
    */
    public function testAddLogWithPHPUnitMock(){

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

?>