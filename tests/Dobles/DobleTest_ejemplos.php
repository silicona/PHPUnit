<?php


	class DobleTest extends \PHPUnit\Framework\TestCase {

		// Dummy - para src/Dobles/Miclase.php
		public function testWithDummy(){

			$dummy = $this -> prophesize('Dummyclass');

			$clase_a_probar = new MiClase( $dummy -> reveal() );

		}


		// Fake
    public function testWithFakeObject() {

    	// En este ejemplo también hemos usado Prophecy. La instancia fakeLogger es un objeto que en la aplicación en sí no devuelve resultado a cada llamada a los métodos log o info, pero sabemos que nuestra clase Controller ejecuta en determinadas circunstancias. Al querer romper la dependencia con la clase Logger real, hemos creado un objeto Fake el cual no hará nada, pero permitirá que la ejecución del SUT (en este caso Controller) sea posible.

        $fakeLogger = $this->prophesize("Logger");
        $fakeLogger->log(Argument::any());
        $fakeLogger->info(Argument::any());
        $controler = new Controller($fakeLogger->reveal());

    }


    // Stubs - test doble que devuelve una salida configurada
    public function testWithStub() {

    	// La dependencia del método "getHttpStatus" del objeto this->response la configuramos y dejamos bajo control con nuestro objeto stub, al cual le hemos indicado que devuelva el código de estado 200, con la configuración willReturn(200)

        $stub = $this->prophesize("\SolrResponse");
        $stub->getHttpStatus()->willReturn(200);

        $controller = new Controller($stub->reveal());
        $this->assertTrue($controller->doController());
    }

    // Mocks - 
    // Test doble que verifica expectativas, por ejemplo, que un método concreto del objeto colaborador ha sido llamado
			// Test para la clase Subject
    public function testObserversAreUpdated() {

        $observer = $this->prophesize('Observer');
        $observer->update("somethig")->shouldBeCalledTimes(1);

        // Create a Subject object and attach the mocked
        // Observer object to it.
        $subject = new Subject('My subject');
        $subject->attach($observer->reveal());

        $subject->doSomething();
    }

    // Mocks Parciales -mockery
    // Estos objetos, como comentábamos anteriormente, heredan directamente de la clase mockeada, pero lo que el comportamiento de todos los métodos públicos pueden ser configurados después de la creación. 
    public function testNose(){

    		// Mock parcial normal - creará un objeto que hereda directamente de DoSearch solo con el método "fetch" con el comportamiento establecido.
    	$mock = Mockery::mock('DoSearch[fetch]', ['text']);
    	$mock->shouldReceive('fetch')->once()->andReturn('stub');

    			// mock parcial pasivo -  creando un mock llamando al método makePartial el objeto se comporta como lo haría mediante la sentencia new DoSearch, sin embargo, después de configurarlo el comportamiento quedaría sobreescrito.
    	$mock = Mockery::mock('DoSearch')->makePartial();
	    $mock->fetch(); // llama al método real

	    $mock = Mockery::mock('TwitterSearch')->makePartial();
	    $mock->shouldReceive('fetch')->once()->andReturn('foo');
	    $mock->fetch(); // devuelve foo.

	    // Para resolver el problema de mockear el método fetch en los tests de la clase DoSearch sólo bastaría con crear un objeto mockeado parcialmente en el método setUp de nuestro tests, para PHPUnit.

    }
	
	}

?>