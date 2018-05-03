<?php

require_once 'RES_FirePHP.php';
require_once 'funciones.php';

function devuelve_arr_campos($link, $sql){

	// Devuelve un array normal con los campos de una consulta
	$arr_campos = array();

	$res = mysqli_query($link, $sql);

	foreach (mysqli_fetch_array($res) as $key => $value) {
		if( !is_long($key) ){
			array_push($arr_campos, $key);
		}
	}

	return $arr_campos;

}



function tabla_a_array($link,$tabla,$campo_id,$campo_valor,$id_idioma = '',$assoc = '', $where_consulta = ''){
    
    /**
     *
     * Toma una tabla, id y valor, y devuelve los valores de esa tabla, 
     * en forma de array bidimensional
     * 
     * Si hay id_idioma, se añade a la consulta
     * 
     * ej. SELECT id_tipo_propiedad, tipo_propiedad FROM 4887_tipos_propiedades WHERE id_idioma = 1
     * 
     */
    
    if($id_idioma != ''){$where = ' WHERE id_idioma = '.$id_idioma;}
    if($where_consulta != ''){$where = ' WHERE '.$where_consulta; }

        $sql='SELECT ' .$campo_id . ', '. $campo_valor.' 
                    FROM '.$tabla 
                    .$where . '
					ORDER BY '.$campo_valor;
        $i=0;
        $tabla = mysqli_query($link, $sql);
        while($e = mysqli_fetch_array($tabla)){

        	if($assoc == ''){
	            $mi_array[$i][0] = $e[$campo_id];
	            $mi_array[$i][1] = $e[$campo_valor];
	            $i++;
	        }else{
	            $mi_array[$e[$campo_id]] = $e[$campo_valor];
	        }
            
        }
        
        return $mi_array;
        
}

function registro_a_array($link, $tabla, $campo_id, $valor_id, $solo_texto = ''){
	// Devuelve un array con todos los campos de la tabla y su valor correspondiente para el id

	$sql = 'SELECT * FROM ' . $tabla .
			' WHERE ' . $campo_id . ' = "' . $valor_id . '"
			LIMIT 1';
	//i($sql, 'registro_a_array');

	$res = mysqli_query($link, $sql);
	

	if($solo_texto != ''){
		
		while($e = mysqli_fetch_array($res)){
		
		    $arr_registro = $e;
		    
		}

		if(is_array($arr_registro) ){
			foreach ($arr_registro as $key => $value) {
				if( is_numeric($key) ){
					unset( $arr_registro[$key] );
				}
			}
		}
		
		return $arr_registro;	
	}

	return mysqli_fetch_array($res);

	
}

function copiar_registro($link, $tabla_origen, $tabla_destino, $campo_id, $valor_id){

	// copia el registro correspondiente al campo id
	// desde la tabla origen a la tabla destino

	if( (int) $valor_id == 0 ){
		return 0;
	}

	$campos = array();
	$valores = array();
 
	$arr_registro = registro_a_array($link, $tabla_origen, $campo_id, $valor_id, 'solo_texto');
	
	foreach ($arr_registro as $key => $value) {
		array_push($campos, $key);
		array_push($valores, $value);
	}
	
	return sql_insert($link, $tabla_destino, $campos, $valores) !== false;
	

}

function conectarse($db_host, $db_nombre, $db_user, $db_pass){
    
	$link = mysqli_connect($db_host, $db_user, $db_pass, $db_nombre) or die("No se pudo conectar: " . mysqli_connect_error());
	mysqli_query($link, "SET NAMES 'utf8'"); //para que los nombres con acentos y demás salgan bien
	return $link;
    
}

        
function dblink(){
	return conectarse(DB_HOST, DB_NOMBRE, DB_USER, DB_PASS);	
}

function start_transaction($link){
	$sql = 'START TRANSACTION;';
	$tabla = mysqli_query($link, $sql);
}

function commit_transaction($link){
	$sql = 'COMMIT;';
	$tabla = mysqli_query($link, $sql);
}

function rollback_transaction($link){
	$sql = 'ROLLBACK;';
	$tabla = mysqli_query($link, $sql);
}

function max_id($link,$campo,$tabla){
	
	// mysqli_query($link, "SET NAMES 'utf8'"); //para que los nombres con acentos y demás salgan bien
	
	$sql = 'SELECT MAX('.$campo.') as max_id FROM '.$tabla;
	
	$res = mysqli_query($link, $sql);
	while($e = mysqli_fetch_array($res)){
		$mi_max_id = $e['max_id'];
	}
	return $mi_max_id;
}

function sql_insert($link,$tabla,$campos,$valores){
	
	//GENERA UNA CONSULTA INSERT A PARTIR DE UNA TABLA, CAMPOS, TIPO DE DATO(NUMERICO O TEXTO) Y VALORES	
	//INSERT INTO `mueblesviuda`.`categorias` (`id_categoria`, `categoria`, `titulo_categoria`, `subtitulo_categoria`, `texto_categoria`, `foto_categoria`, `id_idioma`, `num_orden`) VALUES ('4', 'Proyectos', 'Título proyectos', 'Subtitulo proyectos', 'texto proyectos', '', '1', '4');
	
	// mysqli_query($link, "SET NAMES 'utf8'"); //para que los nombres con acentos y demás salgan bien
	
	$sql_insert.= 'INSERT INTO '.$tabla.' (';
	
	for($i=0;$i<count($campos);$i++){
		$sql_insert.= '`'. quitar_espacios($campos[$i]) . '`, ';
	}

	$sql_insert=substr($sql_insert,0,strlen($sql_insert)-2) . ' ';
	
	$sql_insert.= ') VALUES (';
	
	for($i=0;$i<count($campos);$i++){
		
		if( (is_string( $valores[$i]) || ($valores[$i] == '') ) ){
			$sql_insert.= '"' . limpiar_comillas($valores[$i]) . '", ';
		}else{
			$sql_insert.= $valores[$i] . ', ';
		}

		$valores[$i] ;
	}
	
	$sql_insert=substr($sql_insert,0,strlen($sql_insert)-2) . ' ';
	
	$sql_insert.= ');';
	i($sql_insert);
	
	$mi_tabla_insert = mysqli_query($link, $sql_insert);
	
	if($mi_tabla_insert == false){
		echo $sql_insert. '<br/>'.chr(13).mysqli_error($link).chr(13).' Error al insertar./n/r<br>';
		return false;
	}else{
		return mysqli_insert_id($link);
	}
	
}

function sql_update($link, $tabla, $campos, $valores, $where){
	
	//GENERA UNA CONSULTA UPDATE A PARTIR DE UNA TABLA, CAMPOS, TIPO DE DATO(NUMERICO O TEXTO) Y VALORES
	
	// mysqli_query($link, "SET NAMES 'utf8'"); //para que los nombres con acentos y demás salgan bien
	
	$sql_update .= 'UPDATE '.$tabla.' ';
        $sql_update .= ' SET ';
	
	for($i=0;$i<count($campos);$i++){
		$sql_update.= '`'.$campos[$i].'` = "';
		$sql_update.= $valores[$i];
		//if($tipos[$i]=='t'){$sql_update.= "'";}
		$sql_update.= '", ';
	}
	
	$sql_update=substr($sql_update,0,strlen($sql_update)-2) . ' ';
	
	$sql_update.= $where .';';
	 i($sql_update);
	
	$mi_tabla_update = mysqli_query($link, $sql_update);
	
	//echo $sql_update.'<br/>';
	//exit();
	
	if(($mi_tabla_update == false) || ($where == '')){
		return false;
	}else{
		return true;
	}
	
}

function sql_delete($link, $tabla, $where , $limit=''){
	
	//Elimina datos de una tabla según un criterio
	
	if($limit!=''){$mi_limit=' LIMIT '.$limit;}
	
	$sql_delete .= 'DELETE FROM '.$tabla.' ';
    $sql_delete .= ' WHERE ' . $where;
	$sql_delete .= $limit;
	// i($sql_delete);

	//echo $sql_delete.'<br/>';
	$mi_tabla_delete = mysqli_query($link, $sql_delete);
	
	if($mi_tabla_delete == false){
		echo $sql_delete. '<br/>'.chr(13).mysqli_error($link).chr(13).' Error al eliminar.';
		return false;
	}else{
		return true;
	}
	
}

function check_exists($link, $tabla, $campo, $valor){
njh	
	//Devuelve el número de registros de la tabla para el campo y valor dados
	// mysqli_query($link, "SET NAMES 'utf8'"); //para que los nombres con acentos y demás salgan bien
	
	$sql='SELECT '.$campo.'
        FROM '.$tabla.'
        WHERE '.$campo.' = "'.$valor.'"
        LIMIT 1';
        
	$res = mysqli_query($link, $sql);
    
	if(mysqli_num_rows($res) == 0){
		return false;
	}else{
		return true;
	}
	
}

function check_rows($link,$tabla, $campo, $valor){
	
	//Devuelve el número de registros de la tabla para el campo y valor dados
	// mysqli_query($link, "SET NAMES 'utf8'"); //para que los nombres con acentos y demás salgan bien
	
	$sql='SELECT '.$campo.'
        FROM '.$tabla.'
        WHERE '.$campo.' = "'.$valor.'"';
        
	//echo $sql;
	$res = mysqli_query($link, $sql);
        
	return mysqli_num_rows($res);
	
}

function number_rows($link,$tabla, $id, $criterios=''){

	//devuelve el número de filas de una tabla
	//según los criterios
	
	if($criterios!=''){$mis_criterios = ' WHERE '.$criterios;}
	
	$sql='SELECT '.$id.'
        FROM '.$tabla
        .$mis_criterios;
        $res = mysqli_query($link, $sql);
        
        return mysqli_num_rows($res);
	

}


function coger_dato($link, $campo_objetivo, $tabla, $campo_origen, $valor_campo_origen){

	return coger_campo_misma_tabla($link, $campo_objetivo, $tabla, $campo_origen, $valor_campo_origen);
	
}

function coger_campo_misma_tabla($link, $campo_objetivo, $tabla, $campo_origen, $valor_campo_origen){
	
	// mysqli_query($link, "SET NAMES 'utf8'"); //para que los nombres con acentos y demás salgan bien
	
	$sql='SELECT '.$campo_objetivo.'
	    FROM '.$tabla.' 
	    WHERE '.$campo_origen.' = "'.$valor_campo_origen.'"
	    LIMIT 1;';
	
	$tabla = mysqli_query($link, $sql);
	while($e = mysqli_fetch_array($tabla)){
	    $mi_campo=$e[0];
	}
	
	return $mi_campo;

}

function coger_array_misma_tabla($link, $arr_campos, $tabla, $campo_origen, $valor_campo_origen, $assoc = ''){
	
	// mysqli_query($link, "SET NAMES 'utf8'"); //para que los nombres con acentos y demás salgan bien
	
	$arr_valores = array();
	$campos = implode(',' , $arr_campos);
	
	$sql='SELECT '.$campos.'
	    FROM '.$tabla.' 
	    WHERE '.$campo_origen.' = "'.$valor_campo_origen.'"
	    LIMIT 1;';
	
 	$tabla = mysqli_query($link, $sql);
	while($elementos = mysqli_fetch_array($tabla)){
	    
	    for($i=0;$i<count($arr_campos);$i++){

	    	if($assoc == ''){
	    		array_push($arr_valores, $elementos[$i]);
	    	}else{
	    		$arr_valores[$arr_campos[$i]] = $elementos[$i];
	    	}
			
		}
	}
	
	return $arr_valores;

}

function devuelve_ids($link,$tabla,$campo_id,$where='',$modo=''){
	
	$arr_valores = array();
	
	if($where != ''){$where = ' WHERE ' . $where; }
	$sql='SELECT '.$campo_id.'
	    FROM '.$tabla .
	    $where;
	
	$tabla = mysqli_query($link, $sql);
	while($e = mysqli_fetch_array($tabla)){
		array_push($arr_valores,$e[0]); 
	}
	
	if($modo=='array'){
		return $arr_valores;
	}
	
	return implode(',', $arr_valores);
	
}



?>