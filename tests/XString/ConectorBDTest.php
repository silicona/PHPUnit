<?php

//require_once '/var/www/html/phpunit_josegitbook/src/XString/ConectorBD.php';
use App\XString\ConectorBD;

class ConectorBDTest extends \PHPUnit\Framework\TestCase{

		// Variables para setUp NO son static
    protected static $conectorBD;

    protected static $host_test = '127.0.0.1';
    protected static $user_test = 'root';
    protected static $pass_test = '';
    protected static $db_test 	 = 'videoclub';

    public static function setUpBeforeClass(){

        self::$conectorBD = new ConectorBD(  self::$host_test, 
        																		self::$user_test,
        																		self::$pass_test,
        																		self::$db_test );
    }

    public static function tearDownAfterClass(){

        self::$conectorBD = NULL;
    }

    /*
    public function setUp(){
        $this -> conectorBD = new ConectorBD( $this -> host_test, 
	        																		$this -> user_test,
	        																		$this -> pass_test,
	        																		$this -> db_test );
    }

    public function tearDown(){
        $this -> conectorBD = NULL;
    }
    */

    /*
    	* ALERTA: MALA PRACTICA - Si se utiliza BBDD de produccion, se corre riesgo
    	* 	de borrado accidental
    	*
    	* @depends testAnadirRegistro
    	*
    	*/
    // public function tearDown( $id_pel_test ){

    // 	$this -> assertEquals( 'integer', $id_pel_test );

    // 	// Eliminar el registro con el titulo 'Pelucula de Prueba'
    // 	// Devuelve Array con status y mensaje
    // 	$resultado = $this::$conectorBD -> borrarRegistro( $id_pel_test );
    // 	$this -> assertArraySubset( ['status' => 'ok', 'mensaje' => 'Registro eliminado'] );
    // }


    public function testObtenerCantidad(){

    	$cantidad = $this::$conectorBD -> obtenerCantidad();
    	// $cantidad = $this -> conectorBD -> obtenerCantidad(); // Para setUp()

     	$this -> assertEquals( 'integer', gettype( $cantidad ) );
     	$this -> assertFalse( $cantidad == 0 );

     	return $cantidad;
    
    }


    /**
    	* @depends testObtenerCantidad
    */
    public function testObtenerTodo( int $cantidad ){

    	$todo = $this::$conectorBD -> obtenerTodo();
    	// $todo = $this -> conectorBD -> obtenerTodo(); // Para setUp()

    	$this -> assertNotEmpty( $todo );
    	$this -> assertEquals( $cantidad, count($todo) - 1 ); // status es un elemento de $todo

    	$primero = $todo[0];
    	$this -> assertArrayHasKey( 'codigo', $primero );
    	$this -> assertArrayHasKey( 'titulo', $primero );

    }


    /**
      * @depends testObtenerCantidad
    */
    public function testObtenerUno( $cantidad ){

        $uno = $this::$conectorBD -> obtenerUno();

        $this -> assertTrue( $cantidad > 0 );
    }

    
    public function devuelveRegistroTest(){

      $salida = array( 
        'pel_test' => [
          'titulo' => 'Prueba de pelicula', 
          'direccion' => 'Shilumita', 
          'actor' => 'Zakiak', 
          'estreno' => 1950, 
          'sinopsis' => 'Esta es una sinopsis de prueba'
          ],

      );
      
      return array( $salida );

    }

    /**
    	* @dataProvider devuelveRegistroTest
    	*/
    public function testAnadirRegistro( $obj_registro ){
    // public function testAnadirRegistro( $titulo, $direccion, $actor, $estreno, $sinopsis ){

    	$this -> assertEquals( 'array', gettype( $obj_registro ) );
    	$this -> assertEquals( 'Prueba de pelicula', $obj_registro['titulo'] );

        $tabla = 'peliculas';
        $campos = join( ',', array_keys($obj_registro) );
        $values = join( ',', array_values($obj_registro) );
        $sql = 'INSERT INTO ' . $tabla . '(' . $campos . ') VALUES ('. $values . ');';

        $id_registro = 4;
    	//$id_pel_test = $this::$conectorBD -> anadirRegistro( $obj_pel_test );

        //$this -> assertTrue( $id_pel_test );
    	//$this -> assertEquals( $id_pel_test );
        //print( gettype($id_registro));

    	return $id_registro;
    }

      // @depends testAnadirRegistro
    //public function testBorrarRegistro( int $id_registro ){

    /**
      * @depends testObtenerCantidad
    */
    public function testBorrarRegistro( int $cantidad ){

        $id_registro = rand(1, $cantidad);
        $tabla = 'peliculas';
        $this -> assertEquals( 'integer', gettype($id_registro) );

        $sql = 'DELETE FROM ' . $tabla . ' WHERE codigo=' . $id_registro . ';';
        //print $sql;

    }


}

?>