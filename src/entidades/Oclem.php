<?php
	
	
	function obtener_id_concurso($link, $arr_expediente){

		$id_concurso = 0;
		
		if( $arr_expediente['origen'] == 'CLM' ){

			// CASTILLA LA MANCHA TIENEN TODOS EL MISMO ENLACE
			$id_concurso = (int) coger_dato($link, 'id_concurso', '4887_concursos', 'expediente', $arr_expediente['expediente'] );

		} else {

			$id_concurso = (int) coger_dato($link, 'id_concurso', '4887_concursos', 'enlace', $arr_expediente['enlace'] );

		}


		if( $id_concurso == 0 ){

			if( $arr_expediente['origen'] == 'CLM' ){

				// CASTILLA LA MANCHA TIENEN TODOS EL MISMO ENLACE
				$id_concurso = (int) coger_dato($link, 'id_concurso', '4887_archivo_concursos', 'expediente', $arr_expediente['expediente'] );

			} else {

				$id_concurso = (int) coger_dato($link, 'id_concurso', '4887_archivo_concursos', 'enlace', $arr_expediente['enlace'] );

			}

		}
		
		return $id_concurso;

	}


	function obtener_array_resultado_select($e){
		
		$arr_aux = array();
		
		foreach ($e as $key => $value) {
			$arr_aux[ $key ] = $value;
		}

		return $arr_aux;
	}

	function devuelve_respondido($link, $id_soporte, $hash){

		$link = dblink();

		//SI ES ADMIN

		if($hash == HASH_ADMIN){

			$id_usuario_soporte = 1;
			$id_cliente = 0;
		}else{
			$id_usuario_soporte = coger_campo_misma_tabla($link, 'id_usuario', '4887_usuarios', 'hash', $hash);
			$id_cliente = coger_campo_misma_tabla($link, 'id_cliente', '4887_usuarios', 'hash', $hash);
		}
		//ES OCLEM


		$sql = 'SELECT id_usuario, id_soporte
					FROM 4887_soportes_detalles
					WHERE id_soporte = ' . $id_soporte . '
					ORDER BY id_soporte_detalle DESC
					LIMIT 1';

		$res = mysqli_query($link, $sql);
		while($e = mysqli_fetch_array($res)){
			
			$id_cliente_soporte = coger_campo_misma_tabla($link, 'id_cliente', '4887_usuarios', 'id_usuario',  $e['id_usuario']);
			if($id_cliente_soporte == null){
				$id_cliente_soporte = 0;
			}
			if( $id_cliente_soporte != $id_cliente){
				return true;
			}

		}

		return false;

	}

	function es_cliente($link, $hash_o_id_usuario){

		if($hash_o_id_usuario == HASH_ADMIN){return false;}

		if( is_integer($hash_o_id_usuario) ){
			$id_usuario = $hash_o_id_usuario;
		}else{
			$id_usuario = devuelve_id_usuario($link, $hash_o_id_usuario);
		}


		$sql = 'SELECT tecnico, comercial
				FROM 4887_usuarios
				WHERE id_usuario = ' . $id_usuario . '
					AND activo = 1';

		$res = mysqli_query($link, $sql);
		if(mysqli_num_rows($res) == 0){

			return false;

		}else{

			while($e = mysqli_fetch_array($res)){
				if( ($e['tecnico'] == 0) && ($e['comercial'] == 0) ){ 
					return true;
				}
			}

		}

		return false;

	}


	function devuelve_tipo_contrato($id_tipo_contrato){

		// 0 es contrato demo
		if( $id_tipo_contrato == 1 ){ return 'con30'  ;} // 3 meses
		if( $id_tipo_contrato == 2 ){ return 'con120' ;} // 1 año
		if( $id_tipo_contrato == 3 ){ return 'con2000';} // 1 año
		if( $id_tipo_contrato == 4 ){ return 'con60'  ;} // 6 meses
		if( $id_tipo_contrato == 5 ){ return 'con6'   ;} // 6 meses
		if( $id_tipo_contrato == 6 ){ return 'con3'   ;} // 3 meses
		if( $id_tipo_contrato == 7 ){ return 'clasif' ;} // 1 año
		if( $id_tipo_contrato == 8 ){ return 'con50'  ;} // 1 año

		return '';

	}

	function determina_id_estado_concurso($link, $id_concurso){

		// Determina si un concurso está abierto, en estudio, adjudicado o formalizado


		$timestring_hoy = strtotime('now');

		$sql = 'SELECT f_recepcion_ofertas, adjudicado, f_formalizacion
					FROM 4887_concursos
						WHERE id_concurso = ' . $id_concurso;
		$res = mysqli_query($link, $sql);
		while($e = mysqli_fetch_array($res)){

			$timestring_f_recepcion_ofertas = strtotime($e['f_recepcion_ofertas']);
			if( ($e['adjudicado'] == 0) && ( ($e['f_recepcion_ofertas'] == '0000-00-00') || ($timestring_f_recepcion_ofertas > $timestring_hoy) ) ){
				return 1;
			}

			if( ($e['adjudicado'] == 0) && ($timestring_f_recepcion_ofertas < $timestring_hoy) ){
				return 2;
			}

			if( ($e['f_adjudicacion'] != '0000-00-00') && ($e['f_formalizacion'] == '0000-00-00') ){
				return 3;
			}

			if( ($e['f_formalizacion'] != '0000-00-00') && ($e['adjudicado'] == 1) ){
				return 4;
			}

		}

		return 0;

	}

	function devuelve_texto_estado_concurso($id_estado_concurso){

		if($id_estado_concurso == 1){ return 'Abierto';     }
		if($id_estado_concurso == 2){ return 'En estudio';  }
		if($id_estado_concurso == 3){ return 'Adjudicado';  }
		if($id_estado_concurso == 4){ return 'Formalizado'; }

		return 'Indet.';

	}

	function comprueba_cpv_suministros($cods_cpv, $arr_cpvs_suministros){

		// devuelve true si todos los cpvs de cods_cpv son de suministros
		if( strlen($cods_cpv) < 8){
			return false;
		}

		$arr_cpvs_concurso = explode(',', $cods_cpv);

		for($i=0;$i<count($arr_cpvs_concurso);$i++){

			if( !in_array($arr_cpvs_concurso[$i], $arr_cpvs_suministros) ){
				return false;
			}

		}

		return true;

	}

	function devuelve_arr_cpvs_suministros($link){
		// devuelve un array con los cpvs de suministros

		$arr_cpvs_suministros = array();

		$sql = 'SELECT cod_cpv
					FROM 4887_cpvs
						WHERE grupo = "Z"';

		$res = mysqli_query($link, $sql);
		while($e = mysqli_fetch_array($res)){

			array_push($arr_cpvs_suministros, $e['cod_cpv'] );

		}

		return $arr_cpvs_suministros;

	}

	function devuelve_id_usuario($link, $hash){

		// devuelve el id de un usuario a partir de su hash
		if($hash == ''){
			return 0;
		}

		$id_usuario = coger_campo_misma_tabla( $link, 'id_usuario', '4887_usuarios', 'hash', $hash);

		return (int) $id_usuario;

	}

	function devuelve_id_cliente($link, $hash){

		// devuelve el id de un usuario a partir de su hash
		if($hash == ''){
			return 0;
		}

		$id_cliente = coger_campo_misma_tabla( $link, 'id_cliente', '4887_usuarios', 'hash', $hash);

		return (int) $id_cliente;

	}


	function comprueba_si_cliente($link, $hash){

		// comprueba si un usuario es cliente
		$id_usuario = devuelve_id_usuario($link, $hash);
		$id_cliente = coger_campo_misma_tabla($link, 'id_cliente', '4887_usuarios', 'id_usuario', $id_usuario);

		return $id_cliente > 0;

	}

	function comprueba_si_tecnico($link, $hash){

		// comprueba si un usuario es técnico
		$id_usuario = devuelve_id_usuario($link, $hash);
		$tecnico = coger_campo_misma_tabla($link, 'tecnico', '4887_usuarios', 'id_usuario', $id_usuario);

		return $tecnico == 1;

	}

	function comprueba_si_comercial($link, $hash){

		// comprueba si un usuario es comercial
		$id_usuario = devuelve_id_usuario($link, $hash);
		$comercial = coger_campo_misma_tabla($link, 'comercial', '4887_usuarios', 'id_usuario', $id_usuario);

		return $comercial == 1;

	}

	function comprueba_si_tarifa_mini($link, $hash){

		// comprueba si un usuario es cliente
		$id_usuario = devuelve_id_usuario($link, $hash);
		$id_cliente = coger_campo_misma_tabla($link, 'id_cliente', '4887_usuarios', 'id_usuario', $id_usuario);

		$id_tipo_contrato = coger_campo_misma_tabla($link, 'id_tipo_contrato', '4887_clientes', 'id_cliente', $id_cliente);

		if ( (int) $id_tipo_contrato === 8 ){
			return true;
		}

		return false;

	}


	function determinar_tipo_desde_cpv($link, $cpv){
		// Coge un cpv y devuelve "Obras", "Servicios", "Suministros" o "" según el grupo del CPV

		$arr_grupos_obras     = array('A','B','C','D','E','F','G','H','I','J','K');
		$arr_grupos_servicios = array('L','M','N','O','P','Q','R','S','T','U','V','W');

		$grupo_cpv = coger_campo_misma_tabla($link,'grupo','4887_cpvs','cod_cpv', $cpv);

		if(in_array($grupo_cpv, $arr_grupos_obras) ){ return 'Obras';}

		if(in_array($grupo_cpv, $arr_grupos_servicios) ){ return 'Servicios';}

		return '';

	}

	function devuelve_cods_clasificaciones($link, $cods_cpv){

		// coge los cpv separados por comas,
		// y devuelve las clasificaciones correspondientes, también separadas por comas

		$arr_cods_cpv = explode(',', $cods_cpv);
		$arr_cods_clasificaciones = array();

		for( $i=0; $i < count($arr_cods_cpv); $i++){

			$cods_cpv = $arr_cods_cpv[$i];
			$cods_cpv = trim( $cods_cpv );
			$cods_cpv = substr( $cods_cpv, 0, 8);

			$cod_clasificacion = coger_campo_misma_tabla( $link, 'cod_clasificacion', '4887_cpvs', 'cod_cpv', $cods_cpv );

			if($cod_clasificacion != ''){
				array_push($arr_cods_clasificaciones, $cod_clasificacion);
			}

		}

		$arr_cods_clasificaciones = array_unique($arr_cods_clasificaciones);
		$cods_clasificaciones     = implode(',', $arr_cods_clasificaciones);

		return $cods_clasificaciones;

	}



	function check_admin($hash){
		return $hash != HASH_ADMIN;
	}

	function salir_si_no_hash($link, $hash){

		if( ($hash == HASH_ADMIN) || ( devuelve_id_usuario($link, $hash) > 0) ){
			return true;
		}

		echo_json( array('error' => 'Validación no válida') );
		exit;

	}

	function salir_si_no_admin($hash){

		if( $hash != HASH_ADMIN ){
			i('Hash no válido');
			exit;
		}

	}


	/*
	function obtener_texto_clasificacion($grupo, $subgrupo){

		// Devuelve el texto de una clasificación según grupo y subgrupo

		$link = conectarse(DB_HOST, DB_NOMBRE, DB_USER, DB_PASS);

		$sql = 'SELECT clasificacion
				FROM 4887_clasificaciones
				WHERE grupo = "' . $grupo . '"
					AND num_clasificacion = "' . $subgrupo . '"
				LIMIT 1';

		$res = mysqli_query($link, $sql);

		while($e = mysqli_fetch_array($res)){
			return $e['clasificacion'];
		}

		return '';

	}
	*/


	// FUNCIONES PARA VOLCADOS AUTOMÁTICOS
	function obtener_cpvs($link, $arr_letras){

		// necesaria para cruce_concurso_cliente()

		// DETERMINAR LOS CÓDIGOS CPV CORRESPONDIENTES A UN ARRAY DE LETRAS
		$arr_cpvs_cliente = array();
		for($i = 0; $i < count($arr_letras) ; $i++ ){

			$letra_usuario = strtoupper( $arr_letras[$i] );

			$sql = 'SELECT cod_cpv
						FROM 4887_cpvs
						WHERE grupo = "' . $letra_usuario . '"';


			$res = mysqli_query($link, $sql);
			while($e = mysqli_fetch_array($res)){
				array_push( $arr_cpvs_cliente, substr($e['cod_cpv'], 0, 8 ) );
			}

		}

		return $arr_cpvs_cliente;

	}


	function check_lugares( $lugares_cliente, $provincia ){

		// necesaria para cruce de datos
		if( $lugares_cliente == '') {

			return true;

		}else{

			$provincia = strtolower( quitar_acentos( trim($provincia) ) );
			$arr_lugares_cliente = explode( ',', strtolower( quitar_acentos( $lugares_cliente ) ) );

			for($i = 0; $i < count($arr_lugares_cliente);$i++){

				$lugar_cliente = trim($arr_lugares_cliente[$i]);
				if( $lugar_cliente != ''){

					if( mb_strpos( $provincia, $lugar_cliente ) !== false){
						return true;
					}

				}

			}

		}

		return false;

	}

	function check_clasificado($cod_clasificacion, $arr_cods_clasificaciones){

		// sólo en check_todos_clasificados

		// Devuelve true si el código de clasificación está en el array
		// o si los dos primeros caracteres del código coinciden y la letra es igual o mayor

		if($cod_clasificacion == ''){ return false; }

		if( count( $arr_cods_clasificaciones) == 0 ){ return false; }

		if( strlen($cod_clasificacion) < 3){return false;}

		$arr_letras = array('A','B','C','D','E','F');


		$cod_clasificacion       = quitar_espacios($cod_clasificacion);
		$dos_primeros_caracteres = substr( $cod_clasificacion, 0, strlen($cod_clasificacion) - 1);
		$letra                   = strtoupper( substr( $cod_clasificacion, -1) );

		if( !in_array($letra, $arr_letras) ){ return false; }

		for($i=0; $i < count($arr_cods_clasificaciones); $i++){

			$dos_primeros_caracteres_cliente    = substr($arr_cods_clasificaciones[$i], 0, strlen($arr_cods_clasificaciones[$i]) - 1);
			$letra_cliente                      = substr($arr_cods_clasificaciones[$i], -1);

			if( !in_array($letra_cliente, $arr_letras) ){ return false;}
			if( $dos_primeros_caracteres_cliente === false) {return false;}

			if($dos_primeros_caracteres_cliente == $dos_primeros_caracteres){

				if( ord($letra_cliente) >= ord($letra) ){
					return true;
				}

			}

		}

		return false;
	}

	function check_todos_clasificados( $cods_clasificaciones, $arr_cods_clasificaciones ){

		// solo en 4887_emails_clientes_old

		// Devuelve true si todos los códigos de clasificación están en el array
		// cumplen con check_clasificado


		$arr_cods_clasificaciones_concurso = explode(',', $cods_clasificaciones);

		for($i = 0; $i < count($arr_cods_clasificaciones_concurso); $i++){

			if( check_clasificado( quitar_espacios($arr_cods_clasificaciones_concurso[$i]), $arr_cods_clasificaciones) == false){

				return false;
			}

		}

		return true;
	}


	/*
	function check_cpv($cod_cpv, $arr_cpvs){
		// Sólo en check_cpv
		// DEVUELVE SI UN CPV ESTÁ EN UN ARRAY DE CPVS
		return in_array($cod_cpv, $arr_cpvs);
	}

	function check_todos_cpvs( $cods_cpvs, $arr_cpvs){

		// Sólo en 4887_emails_clientes_old
		// Devuelve true si algún código de cpv está en el array de cpvs

		$arr_cods_cpvs   = explode(',', $cods_cpvs );
		for($i = 0; $i < count($arr_cods_cpvs); $i++){
			$arr_cods_cpvs[$i] = substr( $arr_cods_cpvs[$i], 0, 8);
		}

		// agregar los concursos que corresponden
		for($i = 0; $i < count($arr_cods_cpvs); $i++){

			if( check_cpv($arr_cods_cpvs[$i], $arr_cpvs) == true ){
				return true;
			}

		}

		return false;

	}
	*/

	/*
	function check_cpv($subgrupo_cpv, $arr_cods_grupos_cpv_interes){

		// DEVUELVE SI UN CPV ESTÁ EN EL ARRAY DE CPVS DEL CLIENTE
		return in_array( trim($subgrupo_cpv), $arr_cods_grupos_cpv_interes );

	}

	function check_todos_subgrupos_cpv( $subgrupos_cpv, $cods_grupos_cpv_interes ){

		// Devuelve true si todos los cpv de los subgrupos del concurso están
		// entre los códigos de grupos cpv del cliente


		// agregar los concursos que corresponden
		$arr_subgrupos_cpv           = explode( ',', $subgrupos_cpv );
		$arr_cods_grupos_cpv_interes = explode( ',', $cods_grupos_cpv_interes );

		for($i = 0; $i < count($arr_subgrupos_cpv); $i++){

			if( check_cpv($arr_subgrupos_cpv[$i], $arr_cods_grupos_cpv_interes) == true ){
				return true;
			}

		}

		return false;

	}
	*/

	function devuelve_codigo_clasificado($cod_clasificacion){
		// determina si un código de clasificación es válido o no

		// Si el código tiene menos de 3 letras o más de 5, no es un código de clasificación
		// La última letra debe ser A, B, C, D, E ó F
		// Si el código tiene 3 letras, la posición segunda es un entero > 0
		// Si el código tiene 4 letras, las posiciones 2 y 3 es un entero > 0


		// Si el código tiene menos de 3 letras o más de 4, no es un código de clasificación
		$cod_clasificacion = quitar_espacios($cod_clasificacion);

		$num_letras_cod_clasificacion = strlen($cod_clasificacion);

		if( ( $num_letras_cod_clasificacion < 3 ) || ( $num_letras_cod_clasificacion > 4 ) ){
			return false;
		}


		// La última letra debe ser A, B, C, D, E ó F
		$arr_letras_clasificaciones = array('A','B','C','D','E','F');
		$letra_clasificacion = substr($cod_clasificacion, -1);

		if( !in_array($letra_clasificacion, $arr_letras_clasificaciones) ){
			return false;
		}

		// Si el código tiene 3 letras, la posición segunda es un entero > 0
		if( $num_letras_cod_clasificacion == 3 ){

			$subgrupo_cod_clasificacion = substr( $cod_clasificacion, 1, 1 );

		}else{

			// Si el código tiene 4 letras, las posiciones 2 y 3 es un entero > 0
			$subgrupo_cod_clasificacion = substr( $cod_clasificacion, 1, 2 );

		}

		if( (int) $subgrupo_cod_clasificacion == 0){
			return false;
		}

		return true;
	}


	function devuelve_concurso_clasificado($arr_cods_clasificaciones_concurso){
		// coge un array con los códigos de clasificación y determina si es un código clasificado o no
		// Un código clasificado tiene la última letra A, B, C, D, E ó F y al menos 3 dígitos

		// SI NO, con que coincida alguno basta
		for($i=0; $i<count($arr_cods_clasificaciones_concurso); $i++){

			if( devuelve_codigo_clasificado($arr_cods_clasificaciones_concurso[$i]) == false){

				return false;
			
			};

		}

		return true;

	}


	function limpiar_codigos_comas($cadena){

		// coge una cadena de valores separados por comas
		//  aaF , kkasf, ED
		// y devuelve AAF,KKASF,ED

		if( ($cadena == '') || ($cadena == ',') ){
			return '';
		}

		$cadena = str_replace(' ',',', $cadena);
		$cadena = str_replace(',,',',', $cadena);

		$cadena  = strtoupper($cadena);
		$cadena  = quitar_espacios($cadena);

		if( substr($cadena, -1) == ',' ){
			$cadena = substr($cadena, 0, strlen($cadena) -1 );
		}

		return $cadena;

	}


	// function cruce_concurso_cliente($link, $id_concurso, $id_cliente ){
	function cruce_concurso_cliente($link, $id_concurso, $cods_clasificaciones_cliente, $lugares_cliente, $cods_cpv_cliente, $cods_cpv_ignorar_cliente, $importe_min, $importe_max ){

		// DEVUELVE TRUE SI EL CONCURSO CUMPLE CON LOS CRITERIOS DEL CLIENTE

		$arr_datos_concurso = coger_array_misma_tabla($link, array('importe', 'provincia', 'cods_clasificaciones', 'cods_cpv'), '4887_concursos', 'id_concurso', $id_concurso, 'assoc');

		if( $arr_datos_concurso['cods_clasificaciones'] == ''){ return false; }

		// chequear importes mínimo y máximo
		if($arr_datos_concurso['importe'] > 0){

			if( ( $importe_min > 0 ) && ( $arr_datos_concurso['importe'] < $importe_min ) ){
				 return false;
			}

			if( ($importe_max > 0) && ( $arr_datos_concurso['importe'] > $importe_max ) ){
				return false;
			}

		}

		// chequear que no haya ningún CPV del cliente en ignorar entre los del concurso
		$cods_cpv_concurso = $arr_datos_concurso['cods_cpv'];
		$cods_cpv_concurso = limpiar_codigos_comas( $cods_cpv_concurso );
		$arr_cods_cpv_concurso = explode(',', $cods_cpv_concurso);

		if($cods_cpv_ignorar_cliente != ''){

			$cods_cpv_ignorar_cliente      = limpiar_codigos_comas($cods_cpv_ignorar_cliente);
			$arr_cods_cpv_ignorar_cliente  = explode(',', $cods_cpv_ignorar_cliente);

			for ($j=0;$j<count($arr_cods_cpv_ignorar_cliente);$j++) {
				$arr_cods_cpv_ignorar_cliente[$j] = substr( trim( $arr_cods_cpv_ignorar_cliente[$j] ), 0, 8);
			}

			for($j=0;$j<count($arr_cods_cpv_concurso);$j++){

				if( in_array($arr_cods_cpv_concurso[$j], $arr_cods_cpv_ignorar_cliente) ){
					i($id_concurso, 'Concurso excluido por cpv en ignorar');
					return false;
				}

			}

		}


		$provincia = $arr_datos_concurso['provincia'];

		if( check_lugares( $lugares_cliente, $provincia ) == true){

			$cods_clasificaciones_cliente  = limpiar_codigos_comas($cods_clasificaciones_cliente);
			$cods_clasificaciones_concurso = limpiar_codigos_comas($arr_datos_concurso['cods_clasificaciones']);

			$arr_cods_clasificaciones_concurso = explode( ',', $cods_clasificaciones_concurso );
			$arr_cods_clasificaciones_cliente  = explode( ',', $cods_clasificaciones_cliente  );

			$concurso_clasificado = devuelve_concurso_clasificado( $arr_cods_clasificaciones_concurso);

			if($concurso_clasificado == true){

				// SI EL CONCURSO TIENE ALGÚN GRUPO DE 3 LETRAS, ES CLASIFICADO => DEBEN COINCIDIR TODOS
				for($i=0; $i<count($arr_cods_clasificaciones_concurso); $i++){

					$cod_concurso = $arr_cods_clasificaciones_concurso[$i];

					if( !check_clasificado($cod_concurso, $arr_cods_clasificaciones_cliente) ){
						return false;
					}

				}

			}else{


				$arr_letras = array('A','B','C','D','E','F');

				// i('concurso sin clasificar');

				// SI NO, con que coincida alguno basta.

				// Comprobar si coincide algún grupo
				for($i=0; $i < count($arr_cods_clasificaciones_concurso); $i++){

					$cod_concurso = strtoupper( trim($arr_cods_clasificaciones_concurso[$i]) );

					for($j=0;$j<count($arr_cods_clasificaciones_cliente);$j++){

						//if( $cod_concurso == substr( strtoupper( trim( $arr_cods_clasificaciones_cliente[$j] ) ) ) ){
						$letra_cliente = substr($arr_cods_clasificaciones_cliente[$j], -1);
						if( in_array($letra_cliente, $arr_letras) ){
							$dos_o_tres_primeros_caracteres_cliente = substr($arr_cods_clasificaciones_cliente[$j], 0, strlen($arr_cods_clasificaciones_cliente[$j]) - 1);
						}else{
							$dos_o_tres_primeros_caracteres_cliente = $arr_cods_clasificaciones_cliente[$j];
						}

						if($dos_o_tres_primeros_caracteres_cliente == $cod_concurso){
							return true;
						}

					}


					// Comprobar si conindide algún CPV
					if($cods_cpv_cliente != ''){

						$arr_cods_cpv_cliente  = explode(',', $cods_cpv_cliente);

						for ($j=0;$j<count($arr_cods_cpv_cliente);$j++) {
							$arr_cods_cpv_cliente[$j] = substr( trim( $arr_cods_cpv_cliente[$j] ), 0, 8);
						}

						for($j=0;$j<count($arr_cods_cpv_concurso);$j++){

							if( in_array($arr_cods_cpv_concurso[$j], $arr_cods_cpv_cliente) ){
								return true;
							}

						}

					}

				}

				return false;

			}

			return true;

		}

		return false;

	}


	function devuelve_id_tipo_notificacion($tipo_notificacion){

		/* TIPOS:
			1.- Anuncio Previo
			2.- Anuncio de Licitación
			3.- Anuncio de Adjudicación Provisional
			4.- Anuncio de Adjudicación Definitiva
			5.- Anuncio de Adjudicación
			6.- Anuncio de Formalización
			7.- Pliego
			8.- Otros
		*/

		switch ($tipo_notificacion) {
		    case 'Anuncio Previo':
		        return 1;
		        break;

		    case 'Anuncio de Licitación':
		        return 2;
		        break;

		    case 'Anuncio de Adjudicación Provisional':
		        return 3;
		        break;

		    case 'Anuncio de Adjudicación Definitiva':
		        return 4;
		        break;

		    case 'Anuncio de Adjudicación':
		        return 5;
		        break;

		    case 'Anuncio de Formalización':
		        return 6;
		        break;

		    case 'Pliego':
		        return 7;
		        break;

		 	case 'Otros':
		        return 8;
		        break;

		}


		return '';
	}

	function devuelve_tipo_notificacion($id_tipo_notificacion){

		/* TIPOS:
			1.- Anuncio Previo
			2.- Anuncio de Licitación
			3.- Anuncio de Adjudicación Provisional
			4.- Anuncio de Adjudicación Definitiva
			5.- Anuncio de Adjudicación
			6.- Anuncio de Formalización
			7.- Pliego
		*/


		switch ($id_tipo_notificacion) {
		    case 1:
		        return 'Anuncio Previo';
		        break;

		    case 2:
		        return 'Anuncio de Licitación';
		        break;

		    case 3:
		        return 'Anuncio de Adjudicación Provisional';
		        break;

		    case 4:
		        return 'Anuncio de Adjudicación Definitiva';
		        break;

		    case 5 :
		        return 'Anuncio de Adjudicación';
		        break;

		    case 6 :
		        return 'Anuncio de Formalización';
		        break;

		    case 7 :
		        return 'Pliego';
		        break;

		}

		return '';
	}


	function devuelve_id_provincia_old( $link, $lugar_ejecucion, $organo_contratacion, $titulo){

		// Devuelve el id de la provicia según el lugar de ejecución y el título.

		$arr_excepciones_municipios = array('Segura', 'Torre');

		$lugar_ejecucion_sin_acentos = $lugar_ejecucion;
		$lugar_ejecucion_sin_acentos = strtolower( quitar_acentos( $lugar_ejecucion_sin_acentos ) );
		$lugar_ejecucion_sin_acentos = quitar_espacios ( $lugar_ejecucion_sin_acentos );

		$organo_sin_acentos = $organo_contratacion;
		$organo_sin_acentos = strtolower( quitar_acentos( $organo_sin_acentos ) );
		$organo_sin_acentos = quitar_espacios ( $organo_sin_acentos );

		$titulo_sin_acentos = $titulo;
		$titulo_sin_acentos = strtolower( quitar_acentos( $titulo_sin_acentos ) );
		$titulo_sin_acentos = quitar_espacios ( $titulo_sin_acentos );

		$arr_provincias = array();
		$arr_municipios = array();

		// 0.- Comparar por provincia
		$sql = 'SELECT id_provincia, valores_provincia_limpios
				FROM 4887_provincias;';

		$res = mysqli_query($link, $sql);
		while($e = mysqli_fetch_array($res)){

			$arr_provincias = explode( ',', strtolower($e['valores_provincia_limpios']) );

			// 1.- Buscar la provincia en el lugar de ejecución. Si está, devolver el id;
			for($i=0;$i<count($arr_provincias);$i++){

				if( mb_strpos( $lugar_ejecucion_sin_acentos, $arr_provincias[$i]) !== FALSE ){
					return $e['id_provincia'];
				}

			}

			// 3.- Buscar la provincia en el titulo. Si está, devolver id
			for($i=0; $i < count($arr_provincias);$i++){

				if( mb_strpos( $titulo_sin_acentos, $arr_provincias[$i]) !== FALSE ){
					return $e['id_provincia'];
				}

			}

			// 3.- Buscar la provincia en el órgano de contratación. Si está, devolver id
			for($i=0;$i<count($arr_provincias);$i++){

				if( mb_strpos( $organo_sin_acentos, $arr_provincias[$i]) !== FALSE ){
					return $e['id_provincia'];
				}

			}


		}



		// 1.- Buscar el municipio dentro del título. Si no está, devolver id

		$sql = 'SELECT id_municipio, id_provincia, nombre_limpio
				FROM 4887_municipios;';

		$res = mysqli_query($link, $sql);
		while($e = mysqli_fetch_array($res)){

			$arr_municipios = array();

			$arr_municipios = explode( ',', trim( $e['nombre_limpio']) );


			// prevenir falsos positivos en cadenas pequeñas
			// por ejemplo, Ibi saldría en montones de concursos
			for( $i = 0; $i < count($arr_municipios); $i++){

				if( strlen($arr_municipios[$i]) < 5 ){

					array_push($arr_municipios, ' ' . $arr_municipios[$i] . ',');
					array_push($arr_municipios, ' ' . $arr_municipios[$i] . ',');
					array_push($arr_municipios, ' ' . $arr_municipios[$i] . '.');
					array_push($arr_municipios, ' ' . $arr_municipios[$i] . ';');
					array_push($arr_municipios, ' ' . $arr_municipios[$i] . ')');

					unset($arr_municipios[$i]);// No buscamos por ese municipio

					//$arr_municipios[$i] = '#'; // No buscamos por ese municipio

				}else{

					if( in_array($arr_municipios[$i], $arr_excepciones_municipios) ){
						//$arr_municipios[$i] = '#';
						unset($arr_municipios[$i]);
					}

				}

			}

			$arr_municipios = array_values($arr_municipios);

			// 1.- Buscar la provincia en el lugar de ejecución. Si está, devolver el id;
			for( $i = 0; $i < count($arr_municipios); $i++){

				if( strpos( $lugar_ejecucion, $arr_municipios[$i] ) !== FALSE ){
					return $e['id_provincia'];
				}

			}


			// 3.- Buscar la provincia en el titulo. Si está, devolver id
			for($i = 0; $i < count($arr_municipios); $i++){

				if( strpos( $titulo, $arr_municipios[$i] ) !== FALSE ){

					return $e['id_provincia'];
				}

			}

			// 3.- Buscar la provincia en el órgano de contratación. Si está, devolver id
			for($i = 0; $i < count($arr_municipios); $i++){

				if( strpos( $organo_contratacion, $arr_municipios[$i] ) !== FALSE ){
					return $e['id_provincia'];
				}

			}

		}

		return 0;

	}

	function limpiar_stop_words_array($array){

		$texto_final = '';
		foreach ($array as $texto_aux) {
			$texto_aux = limpiar_comillas($texto_aux);			
			$texto_aux = quitar_acentos($texto_aux);
			$texto_aux = limpiar_stop_words($texto_aux);

			$texto_final .= $texto_aux;
		}

		return $texto_final;

	}


	function devuelve_id_provincia( $link, $lugar_ejecucion, $organo_contratacion, $titulo){
		
		$palabras_buscar = '';
		$palabras_buscar = limpiar_stop_words_array(array($lugar_ejecucion, $organo_contratacion, $titulo));
		
		$arr_palabras_buscar = explode(',', $palabras_buscar);
		$provincia = 0;
		
		$sql = 'SELECT id_provincia, valores_provincia_limpios
				FROM 4887_provincias;';
		$res = mysqli_query($link, $sql);

		while($e = mysqli_fetch_array($res)){

			foreach ($arr_palabras_buscar as $value) {

				if( strlen( $value ) > 3 && stripos( $e['valores_provincia_limpios'], $value )!== false ){

					$provincia = $e['id_provincia'];
					//echo $value;
					//echo $e['valores_provincia_limpios'];

				}
			}
		}

		if($provincia == 0){

			$sql = 'SELECT id_municipio, id_provincia, nombre_limpio_sin_acentos
			FROM 4887_municipios;';

			$res = mysqli_query($link, $sql);

			while($e = mysqli_fetch_array($res)){

				foreach ($arr_palabras_buscar as $value) {

					if( strlen($value) > 5 && stripos($e['nombre_limpio_sin_acentos'], $value)!== false){

						$provincia = $e['id_provincia'];
						//echo $e['id_provincia'];
						//echo $e['nombre_limpio_sin_acentos'];

					}
				}
			}

		}

		return $provincia;

	}

	function determina_provincia( $localidad ){

		$link = dblink();

		$palabra_buscar = strtolower( $localidad );
		$palabra_buscar = quitar_acentos( $palabra_buscar );
		$palabra_buscar = quitar_espacios( $palabra_buscar );
		$palabra_buscar = html_entity_decode($palabra_buscar);

		$arr_provincias = array();
		
		$sql = 'SELECT id_provincia, provincia, valores_provincia_limpios
				FROM 4887_provincias;';

		$res = mysqli_query($link, $sql);

		if($res){

			while($e = mysqli_fetch_array($res)){


				$valores = explode(',', $e['valores_provincia_limpios']);

				//if( strlen( $palabra_buscar ) > 3 && stripos( $e['valores_provincia_limpios'], $palabra_buscar )!== false ){
				if( strlen( $palabra_buscar ) > 3 && ( in_array( $palabra_buscar, $valores ) !== false ) ){

					return $e['provincia'];

				} else{

					array_push( $arr_provincias, $e );

				}

			}

		} else {

			return mysqli_error($link);

		}

		$sql = 'SELECT id_municipio, id_provincia, nombre_limpio_sin_acentos
				FROM 4887_municipios;';

		$res = mysqli_query($link, $sql);

		while($e = mysqli_fetch_array($res)){

				// Correspondencia exacta de palabra y nombre_limpio_de_acentos
			$valores = explode(',', $e['nombre_limpio_sin_acentos']);

			if( strlen($palabra_buscar) > 2 && in_array( $palabra_buscar, $valores ) ){

			//if( strlen($palabra_buscar) > 2 && $palabra_buscar == $e['nombre_limpio_sin_acentos'] ){
			//if( strlen($palabra_buscar) > 5 && stripos($e['nombre_limpio_sin_acentos'], $palabra_buscar)!== false){

				return $arr_provincias[$e['id_provincia'] - 1]['provincia'];

			} 

		}

		return 'No se encuentra el municipio: ' . $palabra_buscar;

	}


	/*
	 * Funciones para guardar concursos y notificaciones
	 *
	 *
	*/

	function guardar_concurso_desde_array($arr_expediente){

		// Guarda un concurso a partir de un array
		// con los datos del expediente y los archivos
		// cuando viene desde una fuente externa

		$observaciones = '';

		$link = dblink();

		$arr_expediente['expediente'] = quitar_espacios( substr($arr_expediente['expediente'] , 0, 30) );

		// Corrige error extraño de expedientes todos a 0
		if( $arr_expediente['expediente'] == '0'){
			i('Expediente no válido');
			ver_en_pantalla('Sin Expediente', 'Error al guardar');
			return false;
		}

		// comprobar que el concurso tiene los datos mínimos necesarios
		$arr_campos_obligatorios = array('expediente', 'enlace', 'titulo' );
		for($i=0;$i<count($arr_campos_obligatorios); $i++){

			if($arr_expediente[ $arr_campos_obligatorios[$i] ] == ''){

				ver_en_pantalla( 'Faltan campos obligatorios', 'Error al guardar' );
				return false;

			}

		}


		// comprobar que la fecha de publicación no sea anterior a 3 meses
		if( (strtotime( $arr_expediente['f_publicacion'] ) < strtotime('-90 days') )
			&& comprueba_si_fecha_mysql($arr_expediente['f_publicacion'])
			&& $arr_expediente['f_publicacion'] != "0000-00-00" ){

		    i('Fecha no válida: '. $arr_expediente['f_publicacion']);
		    ver_en_pantalla( 'Fecha antigua o no válida: '. $arr_expediente['f_publicacion'], 'Error al guardar' );
		    return false;

		}

		// comprobar si el enlace del expediente existe ya
		$id_concurso_tabla = obtener_id_concurso($link, $arr_expediente);

		if( $id_concurso_tabla == 0 ){

		// if( $num_archivos == 0 ){

			// COMPROBAR SI EL CONCURSO NO ESTÁ DUPLICADO,
			// ES DECIR, SI HAY OTRO CONCURSO CON IGUAL EXPEDIENTE Y (TÍTULO O IMPORTE)
			// SI LO HAY Y LA JERARQUÍA DE EL EXPEDIENTE NO ES LA MÁXIMA, GRABAMOS EN LA TABLA DE ARCHIVOS
			$sufijo_tabla = '';

			/* 
			 * Concurso_duplicado:
			 *	Expediente y titulo
			 *	Expediente e importe
			 *	Expediente y f_recepcion_ofertas
			 *	Expediente y cods_cpv
			*/

			$str_campos_duplicados = concurso_duplicado( $arr_expediente );

			if( $str_campos_duplicados ){

				// Si el concurso es un posible duplicado, ver si es de mayor jerarquía
				// Si es de menor jerarquía que el máximo, guardar en tabla de archivo
				$num_orden_max = devuelve_jerarquia_max( $arr_expediente );
				$num_orden_max_expediente = coger_dato($link, 'num_orden', '4887_origenes', 'origen', $arr_expediente['origen'] );

				if( $num_orden_max > $num_orden_max_expediente ){
					
					$sufijo_tabla = 'archivo_';
					$id_concurso_archivo = microtime(true) . rand(0,10000);

				}

				$arr_expediente['posible_duplicado'] = 1;

				$arr_expediente['observaciones_admin'] .= 'Posible archivado duplicado: ';
				$arr_expediente['observaciones_admin'] .= $str_campos_duplicados;

			} else {

				$arr_expediente['posible_duplicado'] = 0;

			}

			/*
			if( comprueba_si_duplicado($arr_expediente) == true ){

				// Si el concurso es un posible duplicado, ver si es de mayor jerarquía
				// Si es de menor jerarquía que el máximo, guardar en tabla de archivo
				$num_orden_max = devuelve_jerarquia_max( $arr_expediente );
				$num_orden_max_expediente = coger_campo_misma_tabla($link, 'num_orden', '4887_origenes', 'origen', $arr_expediente['origen'] );

				if( $num_orden_max > $num_orden_max_expediente ){
					$sufijo_tabla = 'archivo_';

					$id_concurso_archivo = microtime(true) . rand(0,10000);
					$arr_expediente['observaciones'] .= 'Archivado por duplicado al guardar';

				}

				//SE MARCA COMO POSIBLE DUPLICADO
				if(comprueba_posible_duplicado($arr_expediente)){

					$arr_expediente['posible_duplicado'] = 1;

				}else{

					$arr_expediente['posible_duplicado'] = 0;

				}
			}
			*/

			$tabla  = '4887_' . $sufijo_tabla . 'concursos';

			// generar códigos de clasificaciones a partir de CPVs si no existen
			if( $arr_expediente['cods_clasificaciones'] == '' ){
				$arr_expediente['cods_clasificaciones'] = devuelve_cods_clasificaciones( $link, $arr_expediente['cods_cpv'] );
			}

			// Si no existe el tipo de concurso, tratar de determinarlo a partir del CPV
			if($arr_expediente['tipo'] == ''){
				$arr_expediente['tipo'] = determinar_tipo_desde_cpv( $link, substr($arr_expediente['cods_cpv'],0,8) );
			}

			// Lo ponemos aquí porque comprobamos codigos de clasificación y tipo.
			// $arr_expediente['revisado'] = devuelve_revisado($arr_expediente);

			// $revision = concurso_para_revisar( $link, $arr_expediente );
			// $arr_expediente['para_revisar'] = $revision ? $revision : 'No';

			
			$concurso_para_revisar = concurso_para_revisar($link, $arr_expediente);
			
			if( $concurso_para_revisar == false ){
				$arr_expediente['revisado'] = 1;
			}else{
				$arr_expediente['revisado'] = 0;
				$arr_expediente['observaciones_admin'] .= $concurso_para_revisar;
			}

			


			// ver_en_pantalla( $arr_expediente, 'Expediente antes de guardar:');

			$campos = array(
				'enlace',
				'descripcion',
				'f_publicacion',
				'expediente',
				'tipo',
				'subtipo',
				'titulo',
				'importe',
				'lugar_ejecucion',
				'organo_contratacion',
				'procedimiento',
				'tramite',
				'cpv',
				'clasificacion',
				'cods_cpv',
				'cods_clasificaciones',
				'f_recepcion_ofertas',
				'f_apertura_ofertas',
				'adjudicado',
				'f_adjudicacion',
				'f_licitacion',
				'f_formalizacion',
				'provincia',
				'nombre_adjudicatario',
				'cif_adjudicatario',
				'num_licitadores',
				'importe_adjudicacion',
				'porcentaje_adjudicatario',
				'observaciones',
				'observaciones_admin',
				'revisado',
				'origen',
				'f_entrada',
				'posible_duplicado'
			);

			$valores = array(
				$arr_expediente['enlace'],
				$arr_expediente['descripcion'],
				$arr_expediente['f_publicacion'], //la fecha del día en que aparecen
				$arr_expediente['expediente'],
				$arr_expediente['tipo'],
				$arr_expediente['subtipo'],
				$arr_expediente['titulo'],
				$arr_expediente['importe'],
				$arr_expediente['lugar_ejecucion'],
				$arr_expediente['organo_contratacion'],
				$arr_expediente['procedimiento'],
				$arr_expediente['tramite'],
				$arr_expediente['cpv'],
				$arr_expediente['clasificacion'],
				$arr_expediente['cods_cpv'],
				$arr_expediente['cods_clasificaciones'],
				$arr_expediente['f_recepcion_ofertas'],
				$arr_expediente['f_apertura_ofertas'],
				$arr_expediente['adjudicado'],
				$arr_expediente['f_adjudicacion'],
				$arr_expediente['f_licitacion'],
				$arr_expediente['f_formalizacion'],
				$arr_expediente['provincia'],
				$arr_expediente['nombre_adjudicatario'],
				$arr_expediente['cif_adjudicatario'],
				$arr_expediente['num_licitadores'],
				$arr_expediente['importe_adjudicacion'],
				$arr_expediente['porcentaje_adjudicatario'],
				$arr_expediente['observaciones'],
				$arr_expediente['observaciones_admin'],
				$arr_expediente['revisado'],
				$arr_expediente['origen'],
				$arr_expediente['f_entrada'],
				$arr_expediente['posible_duplicado']
			);

			//SI SE ARCHIVA HAY QUE INTRODUCIR EL ID_CONCURSO
			if($sufijo_tabla != ''){
				// unshift es como push pero pone el elemento al principio
				array_unshift($campos, 'id_concurso');
				array_unshift($valores, $id_concurso_archivo);
			}

			$id_concurso = sql_insert($link,$tabla,$campos,$valores);

		} else { // existe el concurso, modificar

			i('El concurso existe');

			$observaciones = coger_campo_misma_tabla($link, 'observaciones', '4887_concursos', 'id_concurso', $id_concurso);

			$campos = array(
				'adjudicado',
				'f_adjudicacion',
				'f_licitacion',
				'f_formalizacion',
				'observaciones',
				'f_modificacion'
			);

			$valores = array(
				$arr_expediente['adjudicado'],
				$arr_expediente['f_adjudicacion'],
				$arr_expediente['f_licitacion'],
				$arr_expediente['f_formalizacion'],
				$observaciones . '<br>Actualizado autom. el '. date('d/m/Y'),
				date('Y-m-d')

			);

			$id_concurso = $id_concurso_tabla;
			$where     = ' WHERE id_concurso = ' . $id_concurso;

			sql_update($link,$tabla,$campos,$valores, $where);

		}


		// Añadir los archivos que no estén
		$arr_archivos = $arr_expediente['archivos'];

		$tabla = '4887_' . $sufijo_tabla . 'concursos_docs';
		$campos = array(
			'id_concurso',
			'enlace',
			'tipo',
			'tipo_documento',
			'f_publicacion'
		);

		if(count($arr_archivos) > 0){

			foreach ($arr_archivos as $id_archivo => $arr_archivo) {

				if($arr_archivo['enlace'] != ''){

					// si no existe el archivo
					$id_concurso_doc = coger_campo_misma_tabla($link, 'id_concurso_doc', '4887_' . $sufijo_tabla . 'concursos_docs', 'enlace',  $arr_archivo['enlace'] );

					if($id_concurso_doc == 0){
						$valores = array(
							$id_concurso,
							$arr_archivo['enlace'],
							'pdf',
							$arr_archivo['tipo_documento'],
							$arr_archivo['f_publicacion']
						);

						$id_concurso_doc = sql_insert($link,$tabla,$campos,$valores);
					}
				}

			}
		}

		//PROVISIONAL
		/*
		if(comprueba_posible_duplicado($arr_expediente)){
			historificar_concurso($link, $id_concurso);
		}
		*/

		return $id_concurso;

	}


	function guardar_notificacion( $arr_notificaciones ){

		$link = conectarse(DB_HOST, DB_NOMBRE, DB_USER, DB_PASS);

		// comprobar que el concurso tiene los datos mínimos necesarios
		$arr_campos_obligatorios = array('expediente', 'enlace', 'titulo' );
		for($i=0;$i<count($arr_campos_obligatorios); $i++){

			if($arr_notificaciones[ $arr_campos_obligatorios[$i] ] == ''){
				return 0;
			}

		}

		$sql = 'SELECT id_notificacion
				FROM 4887_notificaciones
				WHERE enlace = "' .$arr_notificaciones['enlace'] . '"
					AND f_notificacion = CURDATE()
					LIMIT 1';

		$res = mysqli_query($link, $sql);
		if( mysqli_num_rows($res) == 0){

			// Comprobar que la notificación no está tampoco en el archivo
			$sql2 = 'SELECT id_notificacion
				FROM 4887_archivo_notificaciones
				WHERE enlace = "' .$arr_notificaciones['enlace'] . '"
					AND f_notificacion = CURDATE()
					LIMIT 1';

			$res2 = mysqli_query($link, $sql2);
			if( mysqli_num_rows($res2) > 0){
				return 0;
			}

			if($arr_notificaciones['f_notificacion'] == ''){ $arr_notificaciones['f_notificacion'] = date('Y-m-d'); }

			$tabla = '4887_notificaciones';
			$campos = array(
				'id_tipo_notificacion',
				'expediente',
				'enlace',
				'titulo',
				'categoria',
				'f_fin_presentacion',
				'organo_contratacion',
				'importe',
				'existen_modificaciones_en',
				'f_notificacion'
			);

			$valores = array(
				$arr_notificaciones['id_tipo_notificacion'],
				quitar_espacios($arr_notificaciones['expediente']),
				$arr_notificaciones['enlace'],
				$arr_notificaciones['titulo'],
				$arr_notificaciones['categoria'],
				$arr_notificaciones['f_recepcion_ofertas'],
				$arr_notificaciones['organo_contratacion'],
				$arr_notificaciones['importe'],
				$arr_notificaciones['existen_modificaciones_en'],
				$arr_notificaciones['f_notificacion']
			);

			$id_notificacion = sql_insert($link,$tabla,$campos,$valores);

			return $id_notificacion > 0;

		}

		return 0;
	}


	/*
	function devuelve_texto_cpv( $link, $cod_cpv ){

		// coge el código CPV y devuelve el texto correspondiente a ese cpv

		// evitar que se devuelva el primero si no hay código cpv
		if($cod_cpv == ''){ return '';}

		$sql = 'SELECT cpv
				FROM 4887_cpvs
				WHERE cod_cpv LIKE "' . $cod_cpv . '%"
				LIMIT 1';

		$res = mysqli_query($link, $sql);

		while($e = mysqli_fetch_array($res)){
			return substr( $e['cpv'], 11 );
		}

	}
	*/

	function anadir_arr_concursos($link, $e, $hash = '', $id_cliente){

		// Coge una fila de un resultado de una consulta ($e), y devuelve un array
		// sólo con los datos que interesan

		$arr_concursos_aux = array();
	    foreach ($e as $key => $value) {
			if( !is_long($key) ){
				$arr_concursos_aux[$key] = $value;
			}
		}

		// $cod_cpv = substr( $arr_concursos_aux['cods_cpv'] , 0, 8);
		// $arr_concursos_aux['texto_cpv'] = devuelve_texto_cpv( $link, $arr_concursos_aux['cods_cpv'] );

		if($hash != HASH_ADMIN){

			unset($arr_concursos_aux['baja_estadistica']);
			unset($arr_concursos_aux['porcentaje_adjudicatario']);
			unset($arr_concursos_aux['observaciones']);

		}

		$arr_concursos_aux['num_bajas'] = devuelve_num_bajas_concurso($link, $arr_concursos_aux['id_concurso'], $hash, $id_cliente);

		return $arr_concursos_aux;

	}

	function devuelve_num_bajas_concurso($link, $id_concurso, $hash, $id_cliente){

		// devuelve el número de bajas solicitadas de la tabla de soportes
		// Si no es admin, coge sólo las del cliente

		if($hash != HASH_ADMIN){
			$and = ' AND id_cliente = ' . $id_cliente;
		}

		$sql = 'SELECT id_concurso
					FROM 4887_soportes
				WHERE id_tipo_soporte = 0
					AND id_concurso = ' . $id_concurso .
				$and;

		$res = mysqli_query($link, $sql);

		return mysqli_num_rows($res);

	}

	function comprueba_cods_clasificaciones($cods_clasificaciones){

		$arr_cods_clasificaciones = explode(',', $cods_clasificaciones);

		for($i=0;$i<count($arr_cods_clasificaciones);$i++){

			$cod_clasificacion = str_replace(' ','',$arr_cods_clasificaciones[$i]);

			if( (strlen($cod_clasificacion) > 3) || ( !ctype_alnum($cod_clasificacion) ) ){
				return false;
			}

		}

		return true;

	}

	function devuelve_revisado( $arr_expediente ){

		// coge los datos de un expediente y
		// devuelve 1 si cumple con todos los criterios de revisión, 0 si no
		// si es una adjudicación, basta con tener cpvs, cod_clasificacion, f_formalizacion o adjudicación y nombre_adjudicatario o cif

		i($arr_expediente, 'arr_expediente');

		if( $arr_expediente['posible_duplicado'] == 1){
			i('posible duplicado');
			$error = 'posible dup';
			return 0;
		}


		// revisar que los cpv sean números y 8 dígitos
		$arr_cpvs = explode(',', $arr_expediente['cods_cpv']);
		for($i=0; $i < count($arr_cpvs); $i++){

			if( ( (int) $arr_cpvs[$i] == 0) || ( strlen($arr_cpvs[$i]) != 8 ) ){
				i('faltan cpvs');
				$error = 'faltan cpv';
				return 0;
			}

		}		

		if($arr_expediente['cods_cpv'] == ''){
			i('falta clasificación');
			$error = 'falta clas';
			return 0;
		}

		// No hay provincia
		if( $arr_expediente['provincia'] == '' ){
			i('falta provincia');
			$error = 'falta prov';
			return 0;
		}

		// No hay tipo tipo
		if( $arr_expediente['tipo'] == '' ){
			i('falta tipo');
			$error = 'no tipo';
			return 0;
		}

		

		// No hay órgano de contratación
		if( $arr_expediente['organo_contratacion'] == '' ){
			i('falta órgano de contratación');
			$error = 'no organo ';
			return 0;
		}


		/* SI EL CONCURSO ESTÁ ADJUDICADO, ESTÁ REVISADO SI
			- EXITE NOMBRE O NIF
			- EXISTE FECHA DE ADJUDICACIÓN O FORMALIZACIÓN
		*/

		if(
			( (int) substr(   $arr_expediente['f_formalizacion'], 0 ,4)  > 2015 || (int) substr( $arr_expediente['f_adjudicacion'], 0 ,4)  > 2015 )
			&&

			( strlen( $arr_expediente['nombre_adjudicatario']) > 2 ||
				strlen( $arr_expediente['cif_adjudicatario']) > 2 )
			&&($arr_expediente['adjudicado'] == 1)){


			i('Está formalizado');
			return 1;
		}




		// No revisado si no hay fecha límite
		$f_recepcion_ofertas = $arr_expediente['f_recepcion_ofertas'];
		if( comprueba_si_fecha_mysql($arr_expediente['f_recepcion_ofertas']) == false ){
			i('f recepcion no válida');
			$error = 'recepcion no valida';
			return 0;
		}


		// No revisado si no hay clasificación
		if( $arr_expediente['cods_clasificaciones'] == '' ) {
			i('cods clasificaciones no válidos (1)');
			$error = 'cods clas no valida';
			return 0;
		}

		// No revisado si los códigos de clasificación tienen algún símbolo
		if( !comprueba_cods_clasificaciones( $arr_expediente['cods_clasificaciones'] ) ){
			i('cods clasificaciones no válidos (2)');
			$error = 'cods clas no valida 2';
			return 0;
		}



		// revisar que las clasificaciones sean 3 dígitos
		$arr_cods_clasificaciones = explode(',', $arr_expediente['cods_clasificaciones']);

		for($i=0; $i < count($arr_cods_clasificaciones); $i++){

			if( (int) substr($arr_cods_clasificaciones[$i], 1 , 1) == 0 ){
				i('Número de la clasificación no válida');
				return 0;
			}

			

			if( strlen( $arr_cods_clasificaciones[$i] ) > 2){

				$ultima_letra = substr($arr_cods_clasificaciones[$i], -1);
				if( is_numeric( $ultima_letra ) ){

				/*	if( (substr($arr_cods_clasificaciones[$i], 0, 1) != 'Z') && ( $arr_cods_clasificaciones[$i] != 'R10') ){

						i('última letra de la clasificación no válida (1)');
						return 0;
					}*/
	

				}else{
					if( ( $ultima_letra != 'A' ) &&
						( $ultima_letra != 'B' ) &&
						( $ultima_letra != 'C' ) &&
						( $ultima_letra != 'D' ) &&
						( $ultima_letra != 'E' ) &&
						( $ultima_letra != 'F' ) ){
						i('última letra de la clasificación no válida (2)');
						return 0;

					}
				}

			}

		}

		// revisar que haya fecha límite
		if( ($arr_expediente['f_recepcion_ofertas'] == '') || ($arr_expediente['f_recepcion_ofertas'] == '0000-00-00') ){
			i('F. recepción no válida');
			return 0;
		}

		// revisar que haya al menos 1 f_licitacion, adjudicación, formalización
		if($arr_expediente['adjudicado'] == 1){

			if( 
				($arr_expediente['f_adjudicacion'] == '') 
				&& ( $arr_expediente['f_licitacion'] == '') 
				&& ( $arr_expediente['f_formalizacion'] == '')  		
			){

				i('Adjudicado, sin fechas');
				return 0;

			}

			if(
				( $arr_expediente['f_adjudicacion'] == '0000-00-00' ) 
						&& ( $arr_expediente['f_licitacion'] == '0000-00-00') 
						&& ( $arr_expediente['f_formalizacion'] == '0000-00-00') 
			){

				i('Adjudicado, sin fechas');
				return 0;
			} 
			

		}

		return 1;

	}

	function concurso_para_revisar($link, $arr_expediente){

		$campos_incorrectos = false;

		// Expediente
		if( $arr_expediente['expediente'] == ''){

			$campos_incorrectos .= 'Sin expediente. ';

		}

		// Organo de Contratación
		if( $arr_expediente['organo_contratacion'] == ''){

			$campos_incorrectos .= 'Sin órgano de contratacion. ';

		}

		// Titulo
		if( $arr_expediente['titulo'] == ''){

			$campos_incorrectos .= 'Sin título. ';

		}

		// Tipo
		if( $arr_expediente['tipo'] == ''){

			$campos_incorrectos .= 'Sin tipo de concurso. ';

		}

		// Provincia
		if( $arr_expediente['provincia'] == ''){

			$campos_incorrectos .= 'Sin provincia. ';

		}

		// Lugar ejecucion
		if( $arr_expediente['lugar_ejecucion'] == ''){

			$campos_incorrectos .= 'Sin lugar de ejecución. ';

		}

		// Fecha Límite
		if( !preg_match( '/\A20\d{2}-\d{2}-\d{2}\z/', $arr_expediente['f_recepcion_ofertas'] ) ){

			$campos_incorrectos .= 'Sin fecha de recepción de ofertas. ';

		}

		// CPV
		$arr_cods = explode( ',', $arr_expediente['cods_cpv']);
		$sql = "SELECT cod_cpv FROM 4887_cpvs";
		$res = mysqli_query( $link, $sql );
		while( $cpv = mysqli_fetch_row($res) ){

			$arr_cpvs[] = $cpv[0];

		}

		foreach( $arr_cods as $cpv ){

			if( !in_array($cpv, $arr_cpvs) ){

				$campos_incorrectos .= 'CPV ' . $cpv . ' desconocido. ';

			}

		}

		// Posible Concurso Duplicado
		if( $arr_expediente['posible_duplicado'] ){

			$campos_incorrectos .= 'Posible duplicado. ';
		}

		// concurso Adjudicado
		// revisar que haya al menos 1 f_licitacion, adjudicación, formalización
		if( $arr_expediente['adjudicado'] == 1 ){

			$fechas_esperadas = array(
				'adjudicacion' => $arr_expediente['f_adjudicacion'],
				'licitacion' => $arr_expediente['f_licitacion'],
				'formalizacion' => $arr_expediente['f_formalizacion'],
			);

			foreach( $fechas_esperadas as $nombre => $fecha_esp ){

				if( !preg_match( '/\A20\d{2}-\d{2}-\d{2}\z/', $fecha_esp ) ){

					$campos_incorrectos .= 'Fecha de ' . $nombre . ' incorrecta. ';

				}
				
			}	

		};

		return $campos_incorrectos;

	}


		//FUNCION NUEVA
	function concurso_duplicado($arr_expediente){

		// DEVUELVE false si no está duplicado
		// Devuelve un strin si está duplicado

		$campos_duplicados = false;

		$expediente = trim($arr_expediente['expediente']);

		$link = dblink();

		$sql = 'SELECT id_concurso 
				FROM 4887_concursos
				WHERE expediente = "' . $expediente . '"
					AND titulo = "' . $arr_expediente['titulo'] . '" 
				LIMIT 1';

		$res = mysqli_query($link, $sql);
		if( mysqli_num_rows($res) > 0){
			
			$campos_duplicados .= 'Expediente y titulo duplicados. ';

		}


		$sql = 'SELECT id_concurso
				FROM 4887_concursos
				WHERE expediente = "' . $expediente . '"
					AND importe = "' . (double) $arr_expediente['importe'] . '"
					AND importe > 0 
				LIMIT 1';

		$res = mysqli_query($link, $sql);
		if( mysqli_num_rows($res) > 0 ){

			$campos_duplicados .= 'Expediente e importe duplicados. ';

		}


		$sql = 'SELECT id_concurso 
				FROM 4887_concursos
				WHERE expediente = "' . $expediente. '"
					AND f_recepcion_ofertas = "' . $arr_expediente['f_recepcion_ofertas'] . '" 
					AND f_recepcion_ofertas != "0000-00-00" 
				LIMIT 1';

		$res = mysqli_query($link, $sql);
		if( mysqli_num_rows($res) > 0 ){

			$campos_duplicados .= 'Expediente y fecha de recepción de ofertas duplicadas. ';

		}


		$arr_cods = explode( ',', $arr_expediente['cods_cpv'] );
		foreach( $arr_cods as $cpv ){

			$sql = 'SELECT id_concurso
					FROM 4887_concursos
					WHERE expediente = "' . $expediente . '"
						AND cods_cpv LIKE "%' . $cpv . '%" 
					LIMIT 1';

			$res = mysqli_query( $link, $sql );

			if( mysqli_fetch_row($res) > 0 ){

				$campos_duplicados .= 'Expediente y cpv duplicados. ';

			}

		}

		return $campos_duplicados;

	}


	function comprueba_posible_duplicado($arr_expediente){

		// DEVUELVE TRUE SI ESTÁ DUPLICADO, FALSE EN OTRO CASO

		// CONSIDERAMOS QUE UN CONCURSO ESTÁ DUPLICADO SI COINCIDE EXPEDIENTE

		$link = dblink();

		// COMPROBAR EXPEDIENTE Y FECHA LIMITE
		$sql = 'SELECT *
				FROM 4887_concursos
				WHERE expediente = "' . trim($arr_expediente['expediente']) . '"
					AND f_recepcion_ofertas = "'.$arr_expediente['f_recepcion_ofertas'].'" 
					AND id_concurso != "'. $arr_expediente['id_concurso'] . '"';

		$res = mysqli_query($link, $sql);
		if( mysqli_num_rows($res) > 0){
			return true;
		}

		return false;

	}

	//FUNCION VIEJA
	/*
	function comprueba_si_duplicado($arr_expediente){

		// DEVUELVE TRUE SI ESTÁ DUPLICADO, FALSE EN OTRO CASO

		// CONSIDERAMOS QUE UN CONCURSO ESTÁ DUPLICADO SI COINCIDEN EXPEDIENTE Y TÍTULO
		// Ó EXPEDIENTE Y IMPORTE, CON IMPORTE > 0

		$link = dblink();

		// COMPROBAR TÍTULO Y EXPEDIENTE
		$sql = 'SELECT id_concurso
				FROM 4887_concursos
				WHERE expediente COLLATE utf8_general_ci = "' . trim($arr_expediente['expediente']) . '"
					AND titulo COLLATE utf8_general_ci = "' . trim($arr_expediente['titulo']) . '"
				LIMIT 1';

		$res = mysqli_query($link, $sql);
		if( mysqli_num_rows($res) > 0){
			return true;
		}

		// COMPROBAR TÍTULO E IMPORTE
		$sql = 'SELECT id_concurso
				FROM 4887_concursos
				WHERE expediente COLLATE utf8_general_ci = "' . trim($arr_expediente['expediente']) . '"
					AND importe = "' . (double) $arr_expediente['importe'] . '"
					AND importe > 0
				LIMIT 1';

		$res = mysqli_query($link, $sql);
		if( mysqli_num_rows($res) > 0){
			return true;
		}

		return false;

	}
	*/

	function devuelve_jerarquia_max($arr_expediente){

		// Devuelve la jerarquía (núm. orden) máxima de un expediente,
		// según el expediente e importe del mismo

		$link = dblink();

		$max_num_orden = 0;

		$sql = 'SELECT id_concurso, origen
				FROM 4887_concursos
				WHERE expediente COLLATE utf8_general_ci = "' . trim($arr_expediente['expediente']) . '"
					AND titulo COLLATE utf8_general_ci = "' . trim($arr_expediente['titulo']) . '"';

		$res = mysqli_query($link, $sql);
		while($e = mysqli_fetch_array($res)){

			$num_orden = coger_campo_misma_tabla($link, 'num_orden', '4887_origenes', 'origen', $e['origen']);

			if($num_orden == 40){
				return 40;
			}else{

				if($num_orden > $max_num_orden){
					$max_num_orden = $num_orden;
				}

			}

		}

		$sql = 'SELECT id_concurso, origen
				FROM 4887_concursos
				WHERE expediente COLLATE utf8_general_ci = "' . trim($arr_expediente['expediente']) . '"
					AND importe = "' . (double) $arr_expediente['importe'] . '"
					AND importe > 0';

		$res = mysqli_query($link, $sql);
		while($e = mysqli_fetch_array($res)){

			$num_orden = coger_campo_misma_tabla($link, 'num_orden', '4887_origenes', 'origen', $e['origen']);
			
			if($num_orden == 40){

				return 40;

			}else{

				if($num_orden > $max_num_orden){
					$max_num_orden = $num_orden;
				}

			}

		}

		return $max_num_orden;

	}



	/**********************************************/
	/******* FUNCIONES DE HISTORIFICACIÓN *********/
	/**********************************************/
	function comprueba_historificable($link, $id_concurso){

		// DETERMINA SI EL CONCURSO ES HISTORIFICABLE O NO

		// comprobar si el concurso está revisado y formalizado, o es antiguo
		/*
		$sql = 'SELECT id_concurso
					FROM 4887_concursos
					WHERE id_concurso = ' . $id_concurso . '
						AND (
								( f_formalizacion != "000-00-00" OR nombre_adjudicatario != "" OR cif_adjudicatario != "" OR importe_adjudicacion > 0 )
								OR (
									(
										( f_publicacion < ( NOW() - INTERVAL 6 MONTH ) AND f_publicacion != "0000-00-00" ) OR
										( f_recepcion_ofertas < ( NOW() - INTERVAL 3 MONTH ) AND f_recepcion_ofertas != "0000-00-00" ) OR
										( f_apertura_ofertas < ( NOW() - INTERVAL 3 MONTH ) AND f_apertura_ofertas != "0000-00-00" ) OR
										( f_entrada < ( NOW() - INTERVAL 6 MONTH ) AND f_entrada != "0000-00-00" )
									)
									AND (f_recepcion_ofertas < NOW() - INTERVAL 3 DAY AND f_recepcion_ofertas != "0000-00-00")
								)
						)

				';
		*/

		$sql = 'SELECT id_concurso
				FROM 4887_concursos
				WHERE
				id_concurso = ' . $id_concurso . '
				AND (
					f_formalizacion != "000-00-00" OR
					f_adjudicacion != "000-00-00" OR
					nombre_adjudicatario != "" OR
					cif_adjudicatario != "" OR
					importe_adjudicacion > 0
				)
				AND (
					( f_formalizacion < ( NOW( ) - INTERVAL 6 MONTH ) AND f_formalizacion != "0000-00-00" ) OR
					( f_adjudicacion < ( NOW( ) - INTERVAL 6 MONTH )  AND f_adjudicacion != "0000-00-00")
				);';

		$res = mysqli_query($link, $sql);

		if( mysqli_num_rows($res) == 0 ){
			i('Concurso no revisado o no formalizado');
			return false;
		}

		if( concurso_tiene_soportes_asociados($link, $id_concurso) == true ){
			return false;
		}


		return true;

	} // fin comprueba_historificable


	function historificar_concurso($link, $id_concurso){

		// Mueve un concurso de la tabla de concursos a la de concursos historificados
		$status_transaccion = 'ok';

		start_transaction($link);

		$id_concurso_doc = 0;

		if( (int) $id_concurso == 0 ){return false;}

		// copiar documentos
		$sql = 'SELECT *
				FROM 4887_concursos_docs
				WHERE id_concurso = ' . $id_concurso;

		$res = mysqli_query($link, $sql);
		while($e = mysqli_fetch_array($res)){

			$id_concurso_doc = $e['id_concurso_doc'];

		    if( copiar_registro($link, '4887_concursos_docs', '4887_archivo_concursos_docs', 'id_concurso_doc', $id_concurso_doc ) == 0 ){$status_transaccion = 'ko';}
			if( sql_delete( $link, '4887_concursos_docs', ' id_concurso_doc=' . $id_concurso_doc ) == false){ $status_transaccion = 'ko';}
		}

		if( copiar_registro($link, '4887_concursos', '4887_archivo_concursos', 'id_concurso', $id_concurso) == 0 ){$status_transaccion = 'ko';}
		if( sql_delete( $link, '4887_concursos', ' id_concurso=' . $id_concurso) == false ){ $status_transaccion = 'ko';}


		if($status_transaccion == 'ok'){
			commit_transaction($link);
			return true;
		}else{
			rollback_transaction($link);
			return false;
		}


	} // fin historificar_concurso

	function coger_concursos_para_historificar($link){

		// Determina los concursos que hay que historificar
		$arr_concursos = array();
		$sql = 'SELECT id_concurso
					FROM 4887_concursos
					WHERE
							( f_formalizacion != "000-00-00" OR nombre_adjudicatario != "" OR cif_adjudicatario != "" OR importe_adjudicacion > 0 )
								OR (
									(
										( f_publicacion < ( NOW() - INTERVAL 9 MONTH ) AND f_publicacion != "0000-00-00" ) OR
										( f_recepcion_ofertas < ( NOW() - INTERVAL 6 MONTH ) AND f_recepcion_ofertas != "0000-00-00" ) OR
										( f_entrada < ( NOW() - INTERVAL 9 MONTH ) AND f_entrada != "0000-00-00" )
									)
									AND (f_recepcion_ofertas < NOW() - INTERVAL 3 DAY AND f_recepcion_ofertas != "0000-00-00")
								)
						';

		$res = mysqli_query($link, $sql);
		while($e = mysqli_fetch_array($res)){
			array_push($arr_concursos, $e['id_concurso']);
		}

		return $arr_concursos;

	} // fin concursos para historificar


	function concurso_tiene_soportes_asociados($link, $id_concurso){

		// comprobar si hay solicitudes de soporte asociadas al concurso
		$sql = 'SELECT id_concurso
					FROM 4887_soportes
					WHERE id_concurso = ' . $id_concurso;

		$res = mysqli_query($link, $sql);

		if( mysqli_num_rows($res) > 0 ){
			i('Concurso con solicitudes de soporte asociadas');
			return true;
		}

		return false;

	}

//// Funciones Debug ////

   function escribir_en_log( $mensaje, $opcion = 'sin_opcion' ){

        file_put_contents( /* BASE_FILE . 'xml/logs/log_fotocasa_'*/ './log_fotocasa_' . $opcion . '.txt', $mensaje, FILE_APPEND | LOCK_EX );

    }


    function ver_en_pantalla($algo, $mensaje = 'Salida: '){

        echo '<h3>' . $mensaje . '</h3>';

        if(gettype($algo) == 'array' || gettype($algo) == 'object') {   

            foreach($algo as $k => $v){ 

                echo $k . ': ';

                if(is_array($v)){

                    foreach($v as $a => $b){ echo $a . ' // ' . $b . '<br>'; }
            
                }
                else if(is_object($v)){
                    
                    foreach($v as $a => $b){ echo $a . ' - ' . $b . '<br>'; }
                
                }   else {

                    echo $v;

                }

                echo '<br>';

            } 

        } else {    

            echo nl2br( $algo ) . '<br>';    

        }

    }


?>
