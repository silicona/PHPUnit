<?php


	namespace App\MasTest;
	// namespace src;

	/** 
	 * Example of class that use generator.
	 */
	class Generador {

	    /**
	     * Count of words.
	     *
	     * @var [string => int] Number of times that appear a word hashed by word.
	     */
	    private $counts = [];

	    /**
	     * Get counts of words.
	     * 
	     * @param string $file Filename.
	     *
	     * @return Generador
	     * @throws \Exception File not found.
	    */
	    public function getCountOfWords($file){

        $f = fopen($file, 'r');

        if (!$f) {
            throw new \Exception('No se ha abierto el archivo!!');
        }

        while ($line = fgets($f)) {

            $parts = explode(' ', trim($line));

            foreach ($parts as $word) {

                if (!isset($this->counts[$word])) {

                    $this->counts[$word] = 1;

                } else {

                    $this->counts[$word]++;

                }
            }
        } 

        arsort($this->counts);
        foreach ($this->counts as $word => $count) {

            yield $word => $this->counts[$word];

        }

        fclose($f);
	    }
	}

?>