<?php

    namespace Test;

    use App\MasTest\Comando;
    use App\MasTest\Logger;
    use App\MasTest\Emailer;
    // use Src\CommandClass;
    // use Src\Logger;
    // use Src\Emailer;

    /**
     * Test for CommandClass
    */
    // class CommandClassTest extends \PHPUnit_Framework_TestCase
    class CommandoTest extends \PHPUnit\Framework\TestCase {

        /**
         * Test for a command class. A simple mail sender command.
         *
         * @return void
        */
        public function testCommandSend()
        {
            $messages = [
                ["email" => "email@example.com", "content" => "Good example of mail"],
                ["email" => "emailexample.com", "content" => "Bad example of mail"],
            ];

            $command = new Comando( new Logger(), new Emailer() );
            $command->execute($messages);

            $logger = $command->getLogger();
            $logs = $logger->getLogs();
            $this->assertEquals($logs[0]['message'], 'Start command');
            $this->assertEquals($logs[1]['message'], 'Validating email email@example.com');
            $this->assertEquals($logs[2]['message'], 'Email email@example.com is valid');
            $this->assertEquals($logs[3]['message'], 'Email email@example.com sended');

            $this->assertEquals($logs[4]['message'], 'Validating email emailexample.com');
            $this->assertEquals($logs[5]['message'], 'Email emailexample.com is not valid');
            $this->assertEquals($logs[6]['message'], 'End command');

        }
    }

?>