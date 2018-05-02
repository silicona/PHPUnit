<?php

function limpiar_stop_words($cadena){
	//stop words en español SIN acentos
	$stop_words = array(
		'a',
		'aca',
		'ajena',
		'ajenas',
		'ajeno',
		'ajenos',
		'al',
		'algo',
		'algún',
		'alguna',
		'algunas',
		'alguno',
		'algunos',
		'alla',
		'alli',
		'ambos',
		'ampleamos',
		'ante',
		'antes',
		'aquel',
		'aquella',
		'aquellas',
		'aquello',
		'aquellos',
		'aqui',
		'arriba',
		'asi',
		'atras',
		'aun',
		'aunque',
		'bajo',
		'bastante',
		'bien',
		'cabe',
		'cada',
		'casi',
		'cierta',
		'ciertas',
		'cierto',
		'ciertos',
		'como',
		'con',
		'conmigo',
		'conseguimos',
		'conseguir',
		'consigo',
		'consigue',
		'consiguen',
		'consigues',
		'contigo',
		'contra',
		'cual',
		'cuales',
		'cualquier',
		'cualquiera',
		'cualquieras',
		'cuan',
		'cuando',
		'cuanta',
		'cuantas',
		'cuanto',
		'cuantos',
		'de',
		'dejar',
		'del',
		'demas',
		'demasiada',
		'demasiadas',
		'demasiado',
		'demasiados',
		'dentro',
		'desde',
		'donde',
		'dos',
		'e',
		'el',
		'ella',
		'ellas',
		'ello',
		'ellos',
		'empleais',
		'emplean',
		'emplear',
		'empleas',
		'empleo',
		'en',
		'encima',
		'entonces',
		'entre',
		'era',
		'eramos',
		'eran',
		'eras',
		'eres',
		'es',
		'esa',
		'esas',
		'ese',
		'eso',
		'esos',
		'esta',
		'estaba',
		'estado',
		'estais',
		'estamos',
		'estan',
		'estar',
		'estas',
		'este',
		'esto',
		'estos',
		'estoy',
		'etc',
		'fin',
		'fue',
		'fueron',
		'fui',
		'fuimos',
		'gueno',
		'ha',
		'hace',
		'haceis',
		'hacemos',
		'hacen',
		'hacer',
		'haces',
		'hacia',
		'hago',
		'hasta',
		'incluso',
		'intenta',
		'intentais',
		'intentamos',
		'intentan',
		'intentar',
		'intentas',
		'intento',
		'i',
		'ir',
		'jamás',
		'junto',
		'juntos',
		'la',
		'largo',
		'las',
		'lo',
		'los',
		'mas',
		'me',
		'menos',
		'mi',
		'mia',
		'mias',
		'mientras',
		'mio',
		'mios',
		'mis',
		'misma',
		'mismas',
		'mismo',
		'mismos',
		'modo',
		'mucha',
		'muchas',
		'muchisima',
		'muchisimas',
		'muchisimo',
		'muchisimos',
		'mucho',
		'muchos',
		'muy',
		'nada',
		'ni',
		'ningun',
		'ninguna',
		'ningunas',
		'ninguno',
		'ningunos',
		'no',
		'nos',
		'nosotras',
		'nosotros',
		'nuestra',
		'nuestras',
		'nuestro',
		'nuestros',
		'nunca',
		'o',
		'os',
		'otra',
		'otras',
		'otro',
		'otros',
		'para',
		'parecer',
		'per',
		'pero',
		'poca',
		'pocas',
		'poco',
		'pocos',
		'podeis',
		'podemos',
		'poder',
		'podria',
		'podriais',
		'podriamos',
		'podrian',
		'podrias',
		'porque',
		'primero',
		'puede',
		'pueden',
		'puedo',
		'pues',
		'que',
		'querer',
		'quien',
		'quienes',
		'quienesquiera',
		'quienquiera',
		'quiza',
		'quizas',
		'sabe',
		'sabeis',
		'sabemos',
		'saben',
		'saber',
		'sabes',
		'sala',
		'se',
		'segun',
		'ser',
		'si',
		'siempre',
		'siendo',
		'sin',
		'sino',
		'so',
		'sobre',
		'sois',
		'solamente',
		'solo',
		'somos',
		'soy',
		'sr',
		'sra',
		'sres',
		'sta',
		'su',
		'sus',
		'suya',
		'suyas',
		'suyo',
		'suyos',
		'tal',
		'tales',
		'tambien',
		'tampoco',
		'tan',
		'tanta',
		'tantas',
		'tanto',
		'tantos',
		'te',
		'teneis',
		'tenemos',
		'tener',
		'tengo',
		'ti',
		'tiempo',
		'tiene',
		'tienen',
		'toda',
		'todas',
		'todo',
		'todos',
		'tomar',
		'trabaja',
		'trabajais',
		'trabajamos',
		'trabajan',
		'trabajar',
		'trabajas',
		'trabajo',
		'tras',
		'tu',
		'tus',
		'tuya',
		'tuyo',
		'tuyos',
		'ultimo',
		'u',
		'un',
		'una',
		'unas',
		'uno',
		'unos',
		'usa',
		'usais',
		'usamos',
		'usan',
		'usar',
		'usas',
		'uso',
		'usted',
		'ustedes',
		'va',
		'vais',
		'valor',
		'vamos',
		'van',
		'varias',
		'varios',
		'vaya',
		'verdad',
		'verdadera',
		'vosotras',
		'vosotros',
		'voy',
		'vuestra',
		'vuestras,',
		'vuestro',
		'vuestros',
		'y',
		'ya',
		'yo',
		'-',
		'S.A.',
		's.a.'
	);

	$arr_palabras = explode(' ', $cadena);
	
	$arr = array();

	foreach ($arr_palabras as $value) {
		$clave = array_search($value, $stop_words) ;

		if($clave === false){
			array_push($arr, $value);
		}
	}

	return implode(',', $arr) . ',';

}


function comprueba_si_fecha_mysql($fecha){

	// devuelve true si una fecha es una fecha de mysql válida
	
	if($fecha == '0000-00-00'){return true;}

	if(strlen($fecha) != 10){return false;}

	$arr_fecha = explode('-', $fecha);

	$dia = $arr_fecha[2];
	$mes = $arr_fecha[1];
	$ano = $arr_fecha[0];

	if(strlen($dia) != 2){ return false; }
	if(strlen($mes) != 2){ return false; }
	if(strlen($ano) != 4){ return false; }

	return checkdate( (int) $mes, (int) $dia, (int) $ano ) ;

}


function dejar_solo_numeros($string){

	return preg_replace("/[^0-9]/","",$string);
}


function sql_a_excel($link, $sql){

	// coge una consulta y genera un archivo descargable con excel 
	
	ini_set('memory_limit','256M');

	require_once BASE_FILE . 'lib/PHPExcel.php';

	$arr_campos = devuelve_arr_campos($link, $sql);

	$objPHPExcel = new PHPExcel(); 
	$objPHPExcel -> setActiveSheetIndex(0); 
	
	// PONER TÍTULOS
	for($i = 65; $i < 91; $i++){
		$objPHPExcel->getActiveSheet()->SetCellValue( chr($i) . '1', $arr_campos[$i - 65]);
	}
	if( count($arr_campos) > 26 ){
		for($i = 65; $i < 91; $i++){
			$objPHPExcel->getActiveSheet()->SetCellValue( 'A' . chr($i) . '1', $arr_campos[$i - 65 + 26]); 
		}
	}

	$cont = 2; 
	$res = mysqli_query($link, $sql);
	while($e = mysqli_fetch_array($res)){

		for($i = 65; $i < 91; $i++){
			$objPHPExcel->getActiveSheet()->SetCellValue( chr($i) . $cont, $e[$arr_campos[$i - 65]]); 
		}
		
		if( count($arr_campos) > 26 ){
			for($i = 65; $i < 91; $i++){
				$objPHPExcel->getActiveSheet()->SetCellValue( 'A' . chr($i) . $cont, $e[$arr_campos[$i - 65 + 26]]); 
			}
		}

	    $cont++;
	}

	$nombre_archivo = time() . '_oclemconcursos.xls';

	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
	
	$objWriter -> save( BASE_FILE . 'api/excel/' . $nombre_archivo); 

	header("Location: ". BASE_URL . 'api/excel/' . $nombre_archivo);

}



if (!function_exists('mb_str_replace')) {
	function mb_str_replace($search, $replace, $subject, &$count = 0) {
		if (!is_array($subject)) {
			// Normalize $search and $replace so they are both arrays of the same length
			$searches = is_array($search) ? array_values($search) : array($search);
			$replacements = is_array($replace) ? array_values($replace) : array($replace);
			$replacements = array_pad($replacements, count($searches), '');
			foreach ($searches as $key => $search) {
				$parts = mb_split(preg_quote($search), $subject);
				$count += count($parts) - 1;
				$subject = implode($replacements[$key], $parts);
			}
		} else {
			// Call mb_str_replace for each subject in array, recursively
			foreach ($subject as $key => $value) {
				$subject[$key] = mb_str_replace($search, $replace, $value, $count);
			}
		}
		return $subject;
	}
}


	function eur_to_double($euros){

		// Convierte un texto de número tipo 788.040,27€ en un double 788040.27

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

		

		return (double) $euros;
	}


	function str_check_oblig($string_check, $min_chars, $nombre_elem){

		// devuelve vacío si se cumple, texto si no,
		if( strlen($string_check) < $min_chars){
			return '(*) Debe escribir un ' . $nombre_elem . ' válido.<br>';
		}

		return '';
	}


function echo_jsonp($arr){

	// hace un echo del array con los headers correspondientes para jsonp
	header('content-type: application/javascript; charset=utf-8');
	header("access-control-allow-origin: *");

	echo $_GET['callback'] . '('. json_encode($arr) . ')';

}

function echo_json($arr){

	echo json_encode($arr);

}

function remove_cdata($string){
	$string = str_replace("<![CDATA[]]>","",$string);
	$string = str_replace("//<![CDATA[","",$string);
	$string = str_replace("//]]>","",$string);
	$string = str_replace("<![CDATA[","",$string);
	$string = str_replace("]]>","",$string);
	return $string;
}

function object2array($object)
{

    return json_decode(json_encode($object), TRUE); 
}

function limpia_undefined($array){
	
	if( !is_array($array) ){
		return array();
	}

	// cambia los "undefined" por ""
	foreach ($array as $key => $value) {
		if($value == 'undefined'){
			$array[$value] = '';
		}
	}

	return $array;
	
}

function res_urlencode($arr_textos){
	//coge un array con textos o cadena de texto y lo devuelve formateadito para url legible

	if(is_array($arr_textos)){
		$cadena = implode('-', array_unique($arr_textos) );
	}else{
		$cadena = (string) $arr_textos;
	}

	$cadena = str_replace('/', '',  $cadena);
	$cadena = str_replace('ñ', 'n', $cadena);
	$cadena = str_replace('Ñ', 'n', $cadena);
	$cadena = str_replace('--', '-', $cadena);

	$cadena = urlencode(strtolower(quitar_acentos($cadena)));

	return str_replace('+', '-', $cadena);

}

function quitar_undefined($array){
	// itera a través de un array asociativo y cambia 'undefined' por '';
	foreach($array as $key => $val) {
      if((string) $val == 'undefined') {
        $array[$key] = '';
      }
    }

    return $array;
}

function quitar_ultimo_caracter($cadena){
	// quita el último caracter de una cadena de texto
	
	if(strlen($cadena) > 0){$cadena = substr($cadena,0,($largo - 1));}
	
	return $cadena;
	
}

function quitar_espacios($cadena){
	
	$cadena = str_replace('&nbsp;', '', $cadena);
	return preg_replace('/\s+/', '', $cadena);
}

function quitar_espacios_unix($string){
	return str_replace([' ',"\xc2\xa0"], '', $string );
}

function si_no($valor){
	// devuelve Sí si $valor = 1, $no en otro caso
	 ($valor == 1) ? $si_no = 'Sí' : $si_no = 'No';
	 return $si_no;
}

function array_a_option($array,$valor_sele='',$assoc = ''){
    
	/*
     * TRANSFORMA UN ARRAY BIDIMENSIONAL TIPO:
     *  id_localidad =1, localidad = Torrevieja
     * En <option selected value="1">Torrevieja</option>
     */
    
    $i=0;
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

function devuelve_j($array, $valor_i)
{
    //devuelve la segunda columna de un array bidimensional cuando concuerdan
    for($i=0;$i<count($array);$i++){
        if( $array[$i][0] == $valor_i){
            $valor_j = $array[$i][1];
            break;
        }
    }
        return $valor_j;
}

function devuelve_i($array, $valor_j)
{
    //devuelve la primera columna de un array bidimensional cuando concuerdan
    for($i=0;$i<count($array);$i++){
        if( $array[$i][1] == $valor_j){
            $valor_i = $array[$i][0];
            break;
        }
    }
        return $valor_i;
}



function shorten_string($string, $wordsreturned)
	/*  Returns the first $wordsreturned out of $string.  If string
	contains fewer words than $wordsreturned, the entire string
	is returned.
	*/
	{
	$retval = $string;	//	Just in case of a problem
	$array = explode(" ", $string);
	if (count($array)<=$wordsreturned)
	/*  Already short enough, return the whole thing
	*/
	{
	$retval = $string;
	}
	else
	/*  Need to chop of some words
	*/
	{
	array_splice($array, $wordsreturned);
	$retval = implode(" ", $array)." ...";
	}
	return $retval;
}

/*email*/
function enviar_email($email_enviar_a, $nombre_enviar_a, $asunto, $html_mensaje, $archivos = '', $tipo_soporte='', $cc='', $cco=''){

	//$email_enviar_a = 'ifernandez@abogadosyarbitraje.com';
		

		// envía un email. $op es la operación 
		require_once '../lib/PHPMailer/PHPMailerAutoload.php';
		require_once '../lib/config.php'; //para que base_url esté definido
	    
	    $from_email  =  'no-reply@oclemconcursos.com';
	    $from_name   = 'Oclem Concursos';
	    
	    $img_firma = 'firma_no-reply.jpg';

	    if($tipo_soporte == 'baja')           { $img_firma = 'firma_baja.jpg'; }
	    if($tipo_soporte == 'tecnico')        { $img_firma = 'firma_tecnico.jpg'; }
		if($tipo_soporte == 'administrativo') { $img_firma = 'firma_administrativo.jpg'; }
	    if($tipo_soporte == 'juridico')       { $img_firma = 'firma_juridico.jpg'; }

	    $url_firma = BASE_URL . 'email/' . $img_firma;
	    
	    if($ruta_adjunto != ''){
	        $ruta_adjunto = BASE_FILE . $ruta_adjunto;
	    }

	    // 4.- ENVIAR EMAIL AL CLIENTE CON PDF ADJUNTO
	    $mail = new PHPMailer(); // defaults to using php "mail()"
	    $mail -> CharSet = "UTF-8";
	    
	    
	    // HABILITAR EN PRODUCCIÓN //
	    $mail -> isSMTP();                                        // Set mailer to use SMTP

	    try {

	    	$mail -> Host       = 'smtp.gmail.com';         	   // Specify main and backup server
			$mail -> SMTPAuth   = true;                            // Enable SMTP authentication
			
			$mail -> Username   = 'no-reply@oclemconcursos.com';   // SMTP username
			$mail -> Password   = 'Oclem8751qdr74-2484';                // SMTP password
			
			$mail -> SMTPSecure = 'ssl';                           // Enable encryption, 'ssl' also accepted
			$mail -> Port       = 465;
			
			/*
			$mail -> Host       = 'smtp-relay.gmail.com';         // Specify main and backup server
			$mail -> SMTPAuth   = true;                           // Enable SMTP authentication
			
			$mail -> Username   = 'no-reply@oclemconcursos.com';  // SMTP username
			$mail -> Password   = 'Oclem8751qdr74-2484';               // SMTP password
			
			$mail -> SMTPSecure = 'tls';                          // Enable encryption, 'ssl' also accepted
			$mail -> Port       = 587;
			*/

			// $mail -> SMTPDebug  = 2;                     // enables SMTP debug information (for testing) 

		    $mail -> isHTML(true);
			$mail -> setLanguage('es', '/language/');

		    $mail -> From     = "no-reply@oclemconcursos.com";
		    $mail -> FromName = "Oclem Concursos";
		    
		    $mail -> AddAddress($email_enviar_a, $nombre_enviar_a);
		    
		    $mail -> AddReplyTo( $mail -> From, $mail -> FromName);
			
		    $mail -> Subject    = $asunto;
		    
		    if($co  != ''){ $mail -> AddCC(  $cc,  $cc);}
		    if($cco != ''){ $mail -> AddBCC( $cco, $cco);}
		    
		    $mai -> AltBody    = "Para ver este email, debe usar un cliente de correo que soporte HTML"; // optional, comment out and test
		    
		    $plantilla = file_get_contents(BASE_URL .'email/email_plantilla.html');

		    $plantilla = str_replace('<%fecha%>', 				        date('d/m/Y'),               $plantilla);
		    $plantilla = str_replace('<%empresa%>', 			        'Oclem Concursos',           $plantilla);
		    $plantilla = str_replace('<%nombre_empresa%>', 		        'Grupo Oclem',               $plantilla);
		    $plantilla = str_replace('<%razon_social%>', 		        'Grupo Oclem SL',            $plantilla);
		    $plantilla = str_replace('<%cif%>', 		                'B86679594',                 $plantilla);
		    $plantilla = str_replace('<%datos_registro_mercantil%>', 	'Registro Mercantil de Madrid, Tomo 30893, Folio 130, Sección 8, Hoja 556017',      $plantilla);
		    $plantilla = str_replace('<%email_empresa%>', 		        'info@oclemconcursos.com',   $plantilla);
		    $plantilla = str_replace('<%web%>', 		                'http://oclemconcursos.com', $plantilla);
		    $plantilla = str_replace('<%asunto%>', 			         	$asunto,                     $plantilla);
		    $plantilla = str_replace('<%html_mensaje%>', 		        $html_mensaje,               $plantilla);
		    $plantilla = str_replace('<%url_firma%>',   		        $url_firma,                  $plantilla);

		    $mail -> MsgHTML($plantilla);
		    
		    if($archivos != ''){

		    	$arr_archivos = explode(',', $archivos);
		    	for ($i=0; $i<count($arr_archivos);$i++ ) {

		    		$mail -> AddAttachment( BASE_FILE . 'api/uploads/' . $arr_archivos[$i] );      // attachment	
		    	}
		        
		    }

		    $resultado_envio = $mail -> Send();
		    
	    } catch (phpmailerException $e) {

			i($e->errorMessage() , 'Error php mailer'); //Pretty error messages from PHPMailer
			
		} catch (Exception $e) {

			echo i($e->getMessage(), 'Error otros' ); //Boring error messages from anything else!
		}

		i($email_enviar_a, 'email');
		i($resultado_envio, 'resultado envio');

	    return $resultado_envio; 
	
	}

function cambiaf_a_normal($fecha,$devuelve_blanco = ''){
        
	// coge una fecha de mysql y la devuelve como fecha en español
        // si existe el parámetro $devuelve_blanco, devolvemos ''
	// si no, devuelve 00/00/0000
	
	
	list($dia,$mes,$ano) = explode("-",$fecha);
	
	$lafecha             = $ano.'/'.$mes.'/'.$dia;
        
	if(($lafecha == '00/00/0000') && ($devuelve_blanco != '')){
		return '';
	}else{
		return $lafecha;
	}
	
	
}

function anade_ceros_fecha($fecha, $separador = '.'){

	
	
	return $fecha;


}

function cambiaf_a_mysql($fecha){
    
    // coge una fecha de formato --/--/---- y la pone para formato mysql

	$fecha = substr($fecha, 0, 10);

    list($dia,$mes,$ano)=explode("/",$fecha);
    $lafecha = $ano.'-'.$mes.'-'.$dia;
        
	return $lafecha;
}

function cambiaf_larga_a_mysql($fecha){

	// Coge una fecha tipo "15 de Julio de 2015" y la convierte en 2015-07-15

	$fecha = strtolower( $fecha );
	$fecha = quitar_acentos( $fecha );

	if( substr($fecha,2,1) == '/'){
		return cambiaf_a_mysql( $fecha );
	}

	if( substr($fecha,2,1) == '-'){
		// ej 22-01-2015
		$fecha = str_replace('-','/', $fecha);
		return cambiaf_a_mysql( $fecha );	
	}

	$arr_textos = array('.','del', 'de', ',', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo');
	$arr_valores = array('', '','','','','','','','','','');

	$fecha = str_replace( $arr_textos, $arr_valores, $fecha);
	
	// obtener el mes
	$arr_textos = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
	$arr_valores = array('01','02','03','04','05','06','07','08','09','10','11','12');

	// quitar espacios
	$arr_fecha = explode(' ',$fecha);
	//$arr_fecha = array_map('trim', $arr_fecha);
	$arr_fecha = array_values( array_filter($arr_fecha) );

	$dia = (int) substr($arr_fecha[0], 0, 2);
	if($dia < 10){
		$dia = '0' . $dia;
	}

	$mes = str_replace( $arr_textos, $arr_valores, $arr_fecha[1]);	
	
	return end($arr_fecha) . '-' . $mes . '-' . $dia;
	
}

function cambiaf_larga_a_normal( $fecha ){

	// Coge una fecha tipo "15 de Julio de 2015" y la convierte en 15/07/2015
	return cambiaf_a_normal( cambiaf_larga_a_mysql( $fecha ) );

}

function devuelve_num_mes_ceros($texto_mes){

	// devuelve 01 si enero, 02 si febrero, etc.

	$texto_mes = strtolower( quitar_espacios( quitar_acentos( $texto_mes ) ) );

	switch ($texto_mes) {
		
		case 'enero':
			return '01';
			break;
		
		case 'febrero':
			return '02';
			break;
		
		case 'marzo':
			return '03';
			break;
		
		case 'abril':
			return '04';
			break;
		
		case 'mayo':
			return '05';
			break;
		
		case 'junio':
			return '06';
			break;
		
		case 'julio':
			return '07';
			break;
		
		case 'agosto':
			return '08';
			break;
		
		case 'septiembre':
			return '09';
			break;
		
		case 'octubre':
			return '10';
			break;
		
		case 'noviembre':
			return '11';
			break;
		
		case 'diciembre':
			return '12';
			break;
		
		default:
			
			return '00';
			break;
	}

}

function euros($valor){
	//coge un valor y lo devuelve formateadito y con el símbolo del euro.
	//formateado con coma en millares si inglés
	// venta_mes_semana puede ser '', 'mes' (para alquileres) en su idioma, 'semana' (para temporada) en su idioma
	
	if($valor == 0){
		$mis_euros = '0.00&euro;';
	}else{
		
		if ($eng==''){
			$mis_euros = number_format((double) $valor,2,",",".").'&euro;';
		}else{
			$mis_euros = number_format((double) $valor,2,".",",").'&euro;';
		}
	}
	return $mis_euros;
}

    
function listar_directorio($directorio,$subdirectorios=''){
        //recorre un directorio y devuelve un array con el contenido
	
	//subdirectorios puede ser: '' = sin subdirectorios, '1' = listar subdirectorios, 'all' = listar todo
	
	$results = array();
		
	if (is_dir($directorio)==true){
		
		$handler = opendir($directorio);
	
		while ($file = readdir($handler)) {
		    
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
        
	
	return $results;

}
    
    
 function recursive_remove_directory($directory, $empty=FALSE){
	
	//http://lixlpixel.org/recursive_function/php/recursive_directory_delete/
	
	// ------------ lixlpixel recursive PHP functions -------------
	// recursive_remove_directory( directory to delete, empty )
	// expects path to directory and optional TRUE / FALSE to empty
	// of course PHP has to have the rights to delete the directory
	// you specify and all files and folders inside the directory
	// ------------------------------------------------------------
       
	// to use this function to totally remove a directory, write:
	// recursive_remove_directory('path/to/directory/to/delete');
       
	// to use this function to empty a directory, write:
	// recursive_remove_directory('path/to/full_directory',TRUE);
 
 
     // if the path has a slash at the end we remove it here
     if(substr($directory,-1) == '/'){
         $directory = substr($directory,0,-1);
     }
  
     // if the path is not valid or is not a directory ...
     if(!file_exists($directory) || !is_dir($directory)){
         return FALSE;
     }elseif(!is_readable($directory)){
         return FALSE;
     }else{
	$handle = opendir($directory);
        while (FALSE !== ($item = readdir($handle))){
             if($item != '.' && $item != '..'){
                 
		 $path = $directory.'/'.$item;
  
                 if(is_dir($path)) {
                     recursive_remove_directory($path);
                 }else{
                     unlink($path);
                 }
             }
         }
         
         closedir($handle);
  
         if($empty == FALSE){
             if(!rmdir($directory)){
                return FALSE;
             }
         }
         return TRUE;
     }
     // ------------------------------------------------------------
 }
 
function createRandomPassword() {

	$chars = "abcdefghijkmnopqrstuvwxyz023456789";
	srand((double)microtime()*1000000);
    
	$i = 0;
	$pass = '' ;
	while ($i <= 7) {
	    $num = rand() % 33;
	    $tmp = substr($chars, $num, 1);
	    $pass = $pass . $tmp;
	    $i++;
	}
	
	return $pass;

}



function hoy(){
	$hoy = date('d/m/Y');
	return $hoy;
}

function quitar_acentos($texto){
    
    $miTexto=$texto;
    
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
    
    return $miTexto;

}

function quitar_no_alfanumerico($string){
	return preg_replace("/[^A-Za-z0-9áéíóúÁÉÍÓÚñÑ ]/", '', $string);
}


function quitar_no_numerico($string){
	return preg_replace("/[^0-9]/","",$string);
}

function quitar_numerico($string){
	return preg_replace('/[0-9]+/', '', $string);
}


function number_format_idioma($numero,$decimales,$idioma){
	if(($idioma=='eng') || ($idioma=='en')){
		$mi_numero = number_format($numero,$decimales,'.',',');
	}else{
		$mi_numero = number_format($numero,$decimales,',','.');
	}
	return $mi_numero;
}


function mayusculas($cadena){

	$cadena = strtoupper($cadena);
	$cadena = str_replace("á", "Á", $cadena);
	$cadena = str_replace("é", "É", $cadena);
	$cadena = str_replace("í", "Í", $cadena);
	$cadena = str_replace("ó", "Ó", $cadena);
	$cadena = str_replace("ú", "Ú", $cadena);
	return $cadena;

}

function limpiar_comillas($cadena){

	$cadena = str_replace("'", "", $cadena);
	$cadena = str_replace('"', '', $cadena);
	$cadena = str_replace(chr(39), chr(34), $cadena);
	$cadena = str_replace('|', '', $cadena);

	return addslashes( $cadena );

}



// XML //
function xml_child_exists($xml, $childpath){
    $result = $xml->xpath($childpath); 
    return (bool) (count($result));
}

function quitar_caracteres_no_imprimibles($string){
	return preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $string);
}

//get country by ip 
function get_country_by_ip($ip){
	
	$url     = 'http://api.hostip.info/country.php?ip='.$ip;  /** Prepare the URL to hostip.info **/
	$data    = file_get_contents($url);
	
	$mi_pais = strtolower($data);
	return $mi_pais;
	
}


	function i($mensaje='',$etiqueta=''){
		
		if(INM_ENTORNO === 'pruebas'){
			
			if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== false)
			{
			    // User agent is Google Chrome
			    require_once('PhpConsole/__autoload.php');
			    return PhpConsole\Handler::getInstance()->debug($mensaje, $etiqueta);

			}else{
				require_once 'RES_FirePHP.php';
				return FB::info($mensaje,$etiqueta);
			}
			
		}else{

			return false;
		
		}
		
	}


?>