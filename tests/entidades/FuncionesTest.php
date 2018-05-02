<?php

	//use App\Funciones.php
	require_once 'src/entidades/Funciones.php';

	class FuncionesTest extends \PHPUnit\Framework\TestCase {

		public function test_array_a_option(){

			$array_ejemplos = array(
				array( 1, 'Torrevieja'),
				array( 2, 'Albacete')
			);

			$array_ejemplos_assoc = array(
				'nombre'    => 'Torrevieja',
				'provincia' => 'Albacete',
			);

			//foreach( $arr_ejemplos as $ejemplo ){
			function arr_a_opt($array, $valor_sele, $assoc = ''){
				
				$i=0;
		    $option = '';
		    foreach ($array as $key => $value) {
		       
		    	if($assoc == ''){
		    		($array[$i][0] == $valor_sele) ? $selected = ' selected ' : $selected = '';
			        $option .= '<option '.$selected.' value="'.$array[$i][0].'" >'.$array[$i][1].'</option>'.PHP_EOL;
			        $i++;
		    	}else{
		    		($key == $valor_sele) ? $selected = ' selected ' : $selected = '';
			        $option .= '<option '.$selected.' value="'.$key.'" >'.$value.'</option>'.PHP_EOL;
		    	}
			        
		    }

		    return $option;
			}

			$this -> assertEquals( "<option  selected  value=\"1\" >Torrevieja</option>\n<option  value=\"2\" >Albacete</option>\n", arr_a_opt($array_ejemplos, 1) );

			//$this -> assertEquals( "<option  value=\"1\" >Torrevieja</option>\n", arr_a_opt($array_ejemplos, 2, '') );

			$this -> assertRegExp( "/<option  selected  value=\"nombre\" >Torrevieja<\/option>\n/", arr_a_opt($array_ejemplos_assoc, 'nombre', true) );
			//}

			$this -> assertEquals( "<option  selected  value=\"1\" >Torrevieja</option>\n<option  value=\"2\" >Albacete</option>\n", array_a_option($array_ejemplos, 1), '$option no está definido en la función' );

		}

		public function test_cambiaf_a_normal(){

			$fechas = ['2018-12-24', '0000-00-00'];
			$devuelve_blanco = true;

			foreach($fechas as $fecha){
				// list($dia,$mes,$ano) = explode("-",$fecha);
				// $lafecha             = $ano.'/'.$mes.'/'.$dia;
				list($ano,$mes,$dia) = explode("-",$fecha);
				$lafecha             = $dia.'/'.$mes.'/'.$ano;
			        
				if(($lafecha == '00/00/0000') && ($devuelve_blanco != '')){

					$this -> assertEquals( '', cambiaf_a_normal($fecha, $devuelve_blanco), 'La funcion debería devolver ""' );

				}else{
					$this -> assertEquals('24/12/2018', $lafecha);
					$this -> assertEquals('24/12/2018', cambiaf_a_normal($fecha, $devuelve_blanco), 'La funcion debe devolver la fecha');
					//return $lafecha;
				}
			}
		}

		public function test_cambiaf_a_mysql(){

			$fechas = ['23/12/2012', '00/00/0000'];

			foreach( $fechas as $fecha ){

				$lafecha = substr( $fecha, 0, 10 );
				list($dia, $mes, $ano) = explode('/', $lafecha);
				$fecha_mysql = $ano . '-' . $mes . '-' . $dia;

				$this -> assertEquals( $fecha_mysql, cambiaf_a_mysql($fecha) );
			}
		}

		public function test_cambiaf_larga_a_mysql(){

			$fecha = '12 de Enero de 1988';

			$this -> assertEquals( '1988-01-12', cambiaf_larga_a_mysql($fecha) );
		}

		public function test_cambiaf_larga_a_normal(){

			$fecha = "15 de Julio de 2015";

			$this -> assertEquals( '15/07/2015', cambiaf_a_normal( cambiaf_larga_a_mysql($fecha) ) );
		}

		public function test_comprueba_si_fecha_mysql(){

			$fechas = array(
				'0000-00-00',	'2018-12-24',
				'2018.12.24',	'2018/12/24',
				'18-12-24',	'24-12-2012',	'24-12-12',	'',
			);

			foreach($fechas as $fecha){

				if(preg_match('/(\d{4})\-(\d{2})\-(\d{2})/', $fecha, $arr_fecha)){

					$this -> assertEquals(strlen($fecha), 10, 'La fecha deberia ser válida');

					if($fecha != '0000-00-00'){

						$this -> assertTrue( checkdate($arr_fecha[2], $arr_fecha[3], $arr_fecha[1]));

					}

					$this -> fechas[] = $fecha;

					$this -> assertTrue( comprueba_si_fecha_mysql($fecha) );

				} else {

					// La funcion hace explode sobre '-', con lo que la separacion de . o / entra
					//$this -> assertFalse( comprueba_si_fecha_mysql($fecha), 'La funcion falla con ' . $fecha . ' por el explode');
					$this -> fail( 'La funcion falla con ' . $fecha . ' por el explode' );

				}

			}

			$this -> assertArraySubset($this -> fechas, ['0000-00-00',	'2018-12-24']);
		}

		public function test_devuelve_num_mes_ceros(){

			$mes = 'Septiembre';

			$this -> assertEquals( '09', devuelve_num_mes_ceros($mes), 'La función falla con ' . $mes );
		}

		public function test_devuelve_j(){

			$array = array(
				array(0, 'Albacete'),
				array(1, 'Toledo')
			);

			$this -> assertEquals( 'Albacete', devuelve_j($array, 0) );
		}

		public function test_devuelve_i(){

			$array = array(
				array(0, 'Albacete'),
				array(1, 'Toledo')
			);

			$this -> assertEquals( 1, devuelve_i($array, 'Toledo') );
		}

		public function test_echo_json(){

			$array = array(
				'status' => 'ok',
				'mensaje' => 'Todo correcto'
			);

			$json = json_encode($array);

			$this -> assertEquals( '{"status":"ok","mensaje":"Todo correcto"}', $json, 'La función falla con ' . implode(', ', $array) );
			// $this -> assertEquals( 1, echo_json($array), 'La función falla con ' . implode(', ', $array) );
		}

		public function test_eur_to_double(){

			$cantidades = array(
				'12.345.123,24 Euros', '12345,56 €', '123.456 &euro;'
			);

			foreach ($cantidades as $cantidad) {

				$euros = $cantidad;
						
				$euros = quitar_espacios($euros);
				$euros = str_replace('€', '', $euros);
				$euros = str_replace('&eur;', '', $euros);
				$euros = str_replace('&euro;', '', $euros);
				$euros = str_replace('Euros', '', $euros);
				$euros = str_replace('euros', '', $euros);
				$euros = str_replace('Eur.;', '', $euros);
				$euros = str_replace('Eur;', '', $euros);

				$euros = str_replace('Año', '', $euros);
				$euros = str_replace('/', '', $euros);

				$euros = str_replace('.', '', $euros);
				$euros = str_replace(',', '.', $euros);

				$this -> assertRegExp('/[\d\.]+/', $euros);

				$this -> assertNotRegExp('/[a-zA-z\&\€\;,]/', $euros);

				$this -> assertEquals( $euros, eur_to_double($cantidad), 'La función falla con ' . $cantidad );
			}

		}

		public function test_limpiar_comillas(){

			$arr_comillas = array(
				"una'comilla",
				'comilla"doble',
				'caracter' . chr(39) . '39', // 39 - Comilla simple // 34 - Comilla doble
				"barra|vertical"
			);
			
			//print_r('caracter' . chr(34) . '34');

			foreach( $arr_comillas as $cadena_test ){

				$cadena = $cadena_test;

				$cadena = str_replace("'", "", $cadena);
				$cadena = str_replace('"', '', $cadena);
				$cadena = str_replace(chr(39), chr(34), $cadena);
				$cadena = str_replace('|', '', $cadena);

				$resultado = addslashes( $cadena );
				
				$this -> assertNotRegExp('/\"\'\|/', $resultado, $resultado . ' no deberia tener comillas.');
				
				$this -> assertEquals( limpiar_comillas( $cadena_test ), $resultado, 'La función falla con ' . $cadena_test);
				//return addslashes( $cadena );
			}
		}

		public function test_listar_directorio(){
			//recorre un directorio y devuelve un array con el contenido
			//subdirectorios puede ser: '' = sin subdirectorios, '1' = listar subdirectorios, 'all' = listar todo
			
			$directorio = __DIR__; // Directorio actual de los test.
			$subdirectorios = '';

			$results = array();
				
			$dir_ok = is_dir($directorio);
			$this -> assertTrue( $dir_ok );

			if ( $dir_ok==true){
				
				$handler = opendir($directorio);
				$this -> assertNotFalse( $handler );

				while ($file = readdir($handler)) {

					$this -> assertNotNull( $file );
			    
			    if($subdirectorios==''){
						//no listar subdirectorios
						if ($file != '.' && $file != '..' && $file != 'Thumbs.db' && !is_dir($directorio.'/'.$file)){
						    $results[] = $file;
						}
			    }
			    
			    if($subdirectorios=='1'){
						if ($file != '.' && $file != '..' && is_dir($directorio.'/'.$file)){
							$results[] = $file;
						}
			    }
			    
			    if($subdirectorios=='all'){
						if ($file != '.' && $file != '..' && is_dir($directorio.'/'.$file)){
							$results[] = $file;
						}
			    }
				    
				}
			
				closedir($handler);
			}

			$this -> assertTrue( in_array('FuncionesTest.php', $results) );
			$this -> assertTrue( in_array('FuncionesTest.php', listar_directorio(__DIR__)) );
		}

		public function test_mayusculas(){

			$string = 'mistérpotátó';

			$cadena = strtoupper($string);
			$cadena = str_replace("á", "Á", $cadena);
			$cadena = str_replace("é", "É", $cadena);
			$cadena = str_replace("í", "Í", $cadena);
			$cadena = str_replace("ó", "Ó", $cadena);
			$cadena = str_replace("ú", "Ú", $cadena);
			
			$this -> assertEquals( 'MISTÉRPOTÁTÓ', $cadena );

			$this -> assertEquals( $cadena, mayusculas($string), 'La funcion falla con ' . $string );
			//return $cadena;
		}


		public function test_quitar_acentos(){
			
			$arr_texto = array(
				'Paláda',	'Ávila', 'fèrmér',
				'impÌo', // Fallo por acentos agudos en zona de graves
				//'Âvila', // Falla por no haber circunflejo
			);

			foreach($arr_texto as $texto){

		    $miTexto=$texto;
		    //$this -> assertRegExp( '[^a]', $miTexto, $miTexto . ' no debería tener acentos');
		    
		    $miTexto = str_replace('Á','A',$miTexto);
		    $miTexto = str_replace('É','E',$miTexto);
		    $miTexto = str_replace('Í','I',$miTexto);
		    $miTexto = str_replace('Ó','O',$miTexto);
		    $miTexto = str_replace('Ú','U',$miTexto);
		    $miTexto = str_replace('á','a',$miTexto);
		    $miTexto = str_replace('é','e',$miTexto);
		    $miTexto = str_replace('í','i',$miTexto);
		    $miTexto = str_replace('ó','o',$miTexto);
		    $miTexto = str_replace('ú','u',$miTexto);

		    $miTexto = str_replace('À','A',$miTexto);
		    $miTexto = str_replace('È','E',$miTexto);
		    $miTexto = str_replace('Í','I',$miTexto);
		    $miTexto = str_replace('Ó','O',$miTexto);
		    $miTexto = str_replace('Ú','U',$miTexto);
		    $miTexto = str_replace(['à', '&agrave;'],'a',$miTexto);
		    $miTexto = str_replace('è','e',$miTexto);
		    $miTexto = str_replace('ì','i',$miTexto);
		    $miTexto = str_replace('ò','o',$miTexto);
		    $miTexto = str_replace('ù','u',$miTexto);
		    
		    //$this -> assertRegExp( '/\A[\w]+\z/i', $miTexto, $miTexto . ' no debería tener acentos');

		    $resultado = quitar_acentos($texto);
		    $this -> assertRegExp( '/\A[\w]+\z/i', $resultado, 'La función falla con ' . $resultado);
	    	//return $miTexto;

	    }
		}


		public function test_quitar_espacios(){

			$string = "A mi mama  le gust a\xc2\xa0 los mac  arr&nbsp;ones";

			$cadena = str_replace('&nbsp;', '', $string);
			$cadena = preg_replace('/\s+/', '', $cadena);
			$this -> assertEquals( 'Amimamalegustalosmacarrones', quitar_espacios_unix($cadena) );
			//$this -> assertEquals( 'Amimamalegustalosmacarrones', $cadena );
			$this -> assertEquals( 'Amimamalegustalosmacarrones', quitar_espacios($string), 'La funcion falla con Espacios Unix' );
			// Amimamalegusta\xc2\xa0losmacarrones

		}

		public function test_quitar_no_alfanumerico(){

			$string = "strin^g|\"#con!\\si'm*}bo\"lo\¿s";

			$this -> assertEquals( 'stringconsimbolos', quitar_no_alfanumerico($string), 'La función falla con ' . $string );
			
			$string = "error¡";
			$string = "errorº";

			$this -> assertEquals( 'error', quitar_no_alfanumerico($string), 'La función falla con ' . $string );
		}

		public function test_quitar_no_numerico(){

			$string = '50l0num3r05!º - está_claro?';

			$this -> assertEquals( '500305', quitar_no_numerico($string), 'La función falla con ' . $string );
		}

		public function test_quitar_numerico(){

			$string = 'N1ngunNum350??';

			$this -> assertEquals( 'NngunNum??', quitar_numerico($string), 'La función falla con ' . $string );
		}

		public function test_quitar_ultimo_caracter(){

			$string = 'camión';
			$largo = strlen($string);

			if($largo){ $string = substr($string, 0, $largo -1); }

			$this -> assertEquals( 'camió', $string );

			//$this -> assertEquals( 'camió', quitar_ultimo_caracter($string) );
			$this -> fail( 'La función falla porque $largo no esta definido' );
		}

		public function test_number_format_idioma(){

			$arr_ejemplos = array( // numero, decimales, idioma
				[1234567, 2, 'eng'],
				[1234567, 1, 'es']
			);

			foreach( $arr_ejemplos as $ejemplo){

				$numero = $ejemplo[0];
				$decimales = $ejemplo[1];
				$idioma = $ejemplo[2];

				if(($idioma=='eng') || ($idioma=='en')){

					$mi_numero = number_format($numero,$decimales,'.',',');
					//print_r('Eng: ' . $mi_numero);
					$this -> assertEquals( $mi_numero, number_format_idioma($numero, $decimales, $idioma), 'La función falla con ' . $numero );

				}else{

					$mi_numero = number_format($numero,$decimales,',','.');
					//print_r('Es: ' . $mi_numero);
					$this -> assertEquals( $mi_numero, number_format_idioma($numero, $decimales, $idioma), 'La función falla con ' . $numero );

				}
				
				//return $mi_numero;
			}
		}

		public function test_shorten_string(){
			
		}

		public function test_si_no(){

			$this -> assertEquals( 'Sí', si_no(1) );
			$this -> assertEquals( 'Sí', si_no('1') );
			$this -> assertEquals( 'Sí', si_no(true) );
			
			$this -> assertEquals( 'No', si_no(2) );
			$this -> assertEquals( 'No', si_no('string') );
			$this -> assertEquals( 'No', si_no(['1']) );
			$this -> assertEquals( 'No', si_no(false) );
			 
		}


		public function test_str_check_oblig(){

			$string = 'Minombre';
			$minimo = 10;
			$etiqueta = 'nombre';

			if(strlen($string) < $minimo){
				$resultado = '(*) Debe escribir un ' . $etiqueta . ' válido.<br>';
			} else {
				$resultado = '';
			}

			$this -> assertEquals( $resultado, str_check_oblig($string, $minimo, $etiqueta), 'La función falla con ' . $string  );

		}

		

	}

?>