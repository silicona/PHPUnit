<?php

	// Para Dummy
	class MiClase{

		protected $obj;

		public function __construct( ClaseDummy $dummy ){

			$this -> obj = $dummy;

		}
	}

	// Para Stubs
	class Controller {

	    protected $response;

	    public function __construct(\SolrResponse $response) {
	        $this->response = $response;
	    }

	    public function doController() {
	        if($this->response->getHttpStatus==200) {
	            // Do somehing to test

	            return true;
	        }

	        return false;
	    }
	}

	// Para Mocks
	class Subject	{

	    protected $observers = array();

	    protected $name;

	    public function __construct($name) {
	        $this->name = $name;
	    }

	    public function getName() {
	        return $this->name;
	    }

	    public function attach(Observer $observer) {
	        $this->observers[] = $observer;
	    }

	    public function doSomething() {
	        // Do something.
	        // Notify observers that we did something.
	        $this->notify('something');
	    }

	    public function doSomethingBad() {
	        foreach ($this->observers as $observer) {
	            $observer->reportError(42, 'Something bad happened', $this);
	        }
	    }

	    protected function notify($argument) {
	        foreach ($this->observers as $observer) {
	            $observer->update($argument);
	        }
	    }

	    // Other methods.
	}


	class Observer{

	    public function update($argument) {
	        // Do something.
	    }

	    public function reportError($errorCode, $errorMessage, Subject $subject) {
	        // Do something
	    }

	    // Other methods.
	}

	// Para Mockeo parcial con Mockery
	class DoSearch {

    protected static $url = 'http://exampledomain.com?q=';

    protected $searchTerm;

    public function __construct($searchTerm){

        $this-> searchTerm = $searchTerm;

    }

    public function compile() { /* ... */ }

    public function fetch(){

        $url = urlencode($this->url . $this->searchTerm);

        return file_get_contents($url);

    }
	}

?>