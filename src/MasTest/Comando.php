<?php

    namespace App\MasTest;
    // namespace src;

    /**
     * CommandClass source code.
     * Simulate a email sender command.
    */
    class Comando {
    // class CommandClass {

        /**
         * Logger.
         *
         * @var $logger
         */
        private $logger = null;

        /**
         * Emailer.
         *
         * @var $emailer
         */
        private $emailer = null;

        /**
         * Construct.
         *
         * @param \stdClass $logger  Logger.
         * @param \stdClass $emailer Emailer.
         *
        */
        public function __construct(\stdClass $logger, \stdClass $emailer)
        {
            $this->logger = $logger;
            $this->emailer = $emailer;
        }

        /**
         * Messages.
         *
         * @param array $messages Array of messages hashed as 
         *  ["email"=> string, "content"=> string].
         *
         * @return void
        */
        public function execute(array $messages){

            $this->logger->log("Start command");

            foreach ($messages as $message) {

                $this->logger->log("Validating email ".$message['email']);

                if (filter_var($message['email'], FILTER_VALIDATE_EMAIL)) {

                    $this->logger->log("Email ".$message['email']. " is valid", Logger::DEBUG);

                    try {

                        $this->emailer->send($message['email'], $message['content']);
                        $this->logger->log("Email ".$message['email']. " sended", Logger::DEBUG);

                    } catch (\Exception $e) {

                        $this->logger->log($e->getMessage(), Logger::ERROR);
                    
                    }

                } else {

                    $this->logger->log("Email ".$message['email']." is not valid", Logger::DEBUG);
                
                }
            }
            
            $this->logger->log("End command");
        }

        /**
         * Return $this->logger.
         *
         * @return \stdClass
        */
        public function getLogger()
        {
            return $this->logger;
        }
    }

?>