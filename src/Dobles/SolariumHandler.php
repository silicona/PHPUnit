<?php


namespace MonologExtended\Handler;
use MonologExtended\Formatter\SolrFormatter;

use Monolog\Logger;
use Solarium\Client;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Formatter\FormatterInterface;

use Solarium\QueryType\Update\Query\Document\DocumentInterface;
use Solarium\Core\Query\QueryInterface;
use Solarium\QueryType\Update\Query\Document\Document;

/**
 * SolrHandler.
*/
class SolrHandler extends AbstractProcessingHandler
{
    /**
     * Client.
     *
     * @var client.
     */
    protected $client;

    /**
     * Options.
     *
     * @var options.
     */
    protected $options;


    /**
     * Constructor
     *
     * @param array   $options Options for configure the Solr connection.
     * @param integer $level   Level of logging.
     * @param boolean $bubble  Bubble option.
     *
     * @return void
    */
    public function __construct(array $options, $level = Logger::DEBUG, $bubble = false)
    {
        $this->client = new \Solarium\Client($options);

        parent::__construct($level, $bubble);
        $this->options = $options;
    }

    /**
     * Setter for Client property.
     *
     * @param Client $client Client to make the request.
     *
     * @return void
    */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Set formmater.
     *
     * @param FormatterInterface $formatter Formater object.
     *
     * @return FormatterInterface
     * @throws \InvalidArgumentException Throws the exception 
     *         if the SolrHandler is not compatible with SolrFormatter.
    */
    public function setFormatter(FormatterInterface $formatter)
    {
        if ($formatter instanceof SolrFormatter) {
            return parent::setFormatter($formatter);
        }
        $msg = 'SolrHandler is only compatible with SolrFormatter';
        throw new \InvalidArgumentException($msg);
    }

    /**
     * Getter options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Getter for default formatter.
     *
     * @return FormatterInterface
    */
    protected function getDefaultFormatter()
    {
        return new SolrFormatter($this->client);
    }

    /**
     * Handle a collection of records.
     *
     * @param array $records Array of records.
     *
     * @return void
    */
    public function handleBatch(array $records)
    {
        $documents = $this->getFormatter()->formatBatch($records);
        $this->bulkSend($documents);
    }

    /**
     * Write record
     *
     * @param array $record Record.
     *
     * @return void
    */
    protected function write(array $record)
    {
        $this->bulkSend(array($record));
    }

    /**
     * Send a bulk of documents to Solr.
     *
     * @param array $documents Array of documents.
     *
     * @return void
     * @throws \RuntimeException Something wrong happen sending data to solr.
    */
    protected function bulkSend(array $documents)
    {
        try {
            $update = $this->client->createUpdate();
            foreach ($documents as $document) {
                $update->addDocument($document['formatted']);
                $update->addCommit();
            }
            $result = $this->client->update($update);
        } catch (\Exception $e) {
            if (empty($this->options['ignore_errors'])) {
                throw new \RuntimeException('Error sending messages to Solr', 0, $e);
            }
        }
    }
}

?>