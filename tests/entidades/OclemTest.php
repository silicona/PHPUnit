<?php

	require_once 'src/entidades/Oclem.php';

	class OclemTest extends \PHPUnit\Framework\TestCase {

		public function test_comprueba_cods_clasificaciones(){

			$arr_cods = array(
				'A1D,A2C,B2F,B3C,D4', 'A1,A2,B2C,D4',
				'A1D,,B2F,B3C',	'A1D,B2FA,B3C',
			);

			//$this -> assertFalse( comprueba_cods_clasificaciones('A1D,B2FA,B3C') );


			foreach($arr_cods as $indice => $cod){

				$arr = explode(',', $cod);

				foreach( $arr as $codigo ){
					$codigo = str_replace(' ', '', $codigo);

					if( (strlen($codigo) > 3) || ( !ctype_alnum($codigo) ) ){
						$this -> cods_ko = $cod;
						unset($arr_cods[$indice]);
						break;
						//return false;
					}

				}

				if( isset($this -> cods_ko) ){
					$this -> assertFalse( comprueba_cods_clasificaciones($this -> cods_ko), 'Estos codigos deberian ser no validos' );
					unset($this -> cods_ko); 
				}
				//$this -> cods_ok[] = $cod;
				//return true;
			}

			$this -> assertEquals( 2, count($arr_cods) );
			foreach($arr_cods as $cod){
				$this -> assertTrue( comprueba_cods_clasificaciones($cod) );
				$this -> assertRegExp( '(A1D,A2C,B2F,B3C,D4|A1,A2,B2C,D4)', $cod);
			}
			//$this -> assertEquals( 2, count($this -> cods_ko) );

		}
	}


?>