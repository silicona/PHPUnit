<?php

	namespace App\XString;

	class ConectorBD {

		private $link;

		public function __construct( $host, $user, $pass, $db ){

			$this -> link = new \mysqli( $host, $user, $pass, $db );

			//return $this;

		}

		public function obtenerCantidad( $tabla = 'peliculas' ){

			$sql = 'SELECT count(codigo) FROM ' . $tabla;

			$res = mysqli_query( $this -> link, $sql );

			return (int) mysqli_fetch_row( $res )[0];

		}

		public function obtenerTodo( $tabla = 'peliculas' ){

			$sql = 'SELECT * FROM ' . $tabla;

			$res = mysqli_query( $this -> link, $sql );

			$salida = array();

			while( $reg = mysqli_fetch_array($res) ){

				$salida[] = $reg;

			}

			if( $salida != array() ){

				$salida['status'] = 'ok';

			} else {

				$salida['status'] = 'ko';

			}

			return $salida;
		}

		public function obtenerUno( $tabla = 'peliculas' ){

		}

		public function anadirRegistro( $obj_registro, $tabla = 'peliculas' ){

			$campos = join(',', array_keys( $obj_registro ) );
			$values = join(',', array_values( $obj_registro ) );

			$sql = 'INSERT INTO ' . $tabla . '(' . $campos . ') VALUES ('. $values . ')'; 

			return $sql;  
		}
		
	}

?>