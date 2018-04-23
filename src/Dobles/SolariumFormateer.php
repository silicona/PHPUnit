<?php

namespace MonologExtended\Formatter;

use Monolog\Formatter\NormalizerFormatter;

/**
 * SolrFormatter. Extension of Monolog.
 */
class SolrFormatter extends NormalizerFormatter
{
    /**
     * Index for sending the messages.
     *
     * @var index
     */
    protected $index;

    /**
     * Type.
     *
     * @var type
     */
    protected $type;

    /**
     * Client.
     *
     * @var client
     */
    protected $client;

    /**
     * Constructor.
     *
     * @param mixed $client Client.
     */
    public function __construct($client)
    {
        parent::__construct(\DateTime::ISO8601);

        $this->client = $client;
    }

    /**
     * format function
     *
     * @param array $record Record to be formatted.
     *
     * @return Document
    */
    public function format(array $record)
    {
        $record = parent::format($record);

        return $this->getDocument($record);
    }

    /**
     * Format a collection of records
     *
     * @param array $records Records to be formatted.
     *
     * @return array
    */
    public function formatBatch(array $records)
    {
        $docs = array();
        foreach ($records as $key => $record) {
            $records[$key]['formatted'] = $this->getDocument($record);
        }

        return $records;
    }

    /**
     * Getter index.
     *
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Getter type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get document function
     *
     * @param mixed $record Record to make a SolrDocument.
     *
     * @return Document
     * @throws \RuntimeException The SolrDocument couldn't be created.
     **/
    protected function getDocument($record)
    {
        try {
            $update = $this->client->createUpdate();
            $doc = $update->createDocument();
            $doc->id = uniqid();

            $doc->description = $record['message'];
            $doc->title = $record['level_name'];

            return $doc;
        } catch (\Exception $e) {
            throw new \RuntimeException("Can't create an Solr document", 0, $e);
        }
    }
}

?>