<?php
class Common_Utils {
	/**
	 * Abreviatura de Zend_Debug::dump()
	 * Imprime informacion de depuracion 
	 * @param unknown $data
	 */
	public static function d($data){
		Zend_Debug::dump($data);
		
	}
	
	/**
	 * Reemplaza todos los acentos por sus equivalentes sin ellos
	 * Elimina los espacios
	 * @param string $string la cadena a sanear
	 * @return string $string cadena saneada
	 */
	public static function formatear_string($string)
	{
	
		$string = trim($string);
	
		$string = str_replace(
				array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
				array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
				$string
		);
	
		$string = str_replace(
				array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
				array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
				$string
		);
	
		$string = str_replace(
				array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
				array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
				$string
		);
	
		$string = str_replace(
				array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
				array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
				$string
		);
	
		$string = str_replace(
				array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
				array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
				$string
		);
	
		$string = str_replace(
				array('ñ', 'Ñ', 'ç', 'Ç'),
				array('n', 'N', 'c', 'C',),
				$string
		);
	
		//Esta parte se encarga de eliminar cualquier caracter extraño
		$string = str_replace(
				array("\\", "¨", "º", "-", "~",
						"#", "@", "|", "!", "\"",
						"·", "$", "%", "&", "/",
						"(", ")", "?", "'", "¡",
						"¿", "[", "^", "`", "]",
						"+", "}", "{", "¨", "´",
						">", "<", ";", ",", ":",
						".", " "),
				'',
				$string
		);
	
	
		return $string;
	}	
	
	/**
	 * ss (Sanitize String)
	 * Abreviatura de filter_var con el filtro FILTER_SANITIZE_STRING
	 * usado para no guardar ni mostrar código en la db
	 * @param string $cadena string a sanear
	 * @return string Cadena saneada
	 */
	public static function ss($cadena){
		try {
			return filter_var($cadena, FILTER_SANITIZE_STRING);
		} catch (Exception $exc) {
			throw new Exception('Error, caracteres especiales no pudieron ser convertidos');
		}
	
	
	}
	
	


	/**
	 * Detecta si el browser es IE
	 * @return boolean
	 */
	public static function isIE(){
		if (isset($_SERVER['HTTP_USER_AGENT']) &&
		(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
			return true;
		else
			return false;
	}
	/**
	 * Agrega puntos suspensivos a una cadena si excede el $max de caracteres permitidos
	 *
	 * @param string $cadena
	 * @param integer $max numero maximo de caracteres
	 * @return string si la $cadena no excede el tamaño maximo retorna la cadena entera, si no la cadena agregando los puntos suspensivos
	 */
	public static function recortar_cadena($cadena, $max){
		if(strlen($cadena) > $max ){
			return substr($cadena, 0, $max) . '...';
		} else {
			return $cadena;
		}
	}
	/**
	 *
	 * @param string $string
	 * @param string $limit cantidad de caracteres
	 * @param string $break caracter de corte, generalmente espacios
	 * @param string $pad lo que se agrega al final
	 * @return string
	 */
	public static function truncateString($string, $limit, $break=" ", $pad="...") {
		// return with no change if string is shorter than $limit
		if(strlen($string) <= $limit)
			return $string;
		// is $break present between $limit and the end of the string?
		if(false !== ($breakpoint = strpos($string, $break, $limit))) {
			if($breakpoint < strlen($string) - 1) {
				$string = substr($string, 0, $breakpoint) . $pad;
			}
		}
		return $string;
	}
	
	/**
	 * 
	 * @param unknown $errno
	 * @param unknown $errstr
	 * @param unknown $errfile
	 * @param unknown $errline
	 * @param array $errcontext
	 * @throws ErrorException
	 * @return boolean
	 */
	public static function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
	{
		// error was suppressed with the @-operator
		if (0 === error_reporting()) {
			return false;
		}
	
		throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
	
	
	/**
	 * Comparador de arreglos
	 * @param array $a
	 * @param array $b
	 * @return boolean
	 */
	function array_equal_values(array $a, array $b) {
		return array_count_values($a) == array_count_values($b);
	}
	
	/**
	 * Verifica si el proceso esta corriendo en consola
	 * @param string $proceso Nombre del proceso a verificar, puede ser cualquiera
	 * @return boolean
	 */
	public static function isRunning( $proceso ){
		$first = substr($proceso, 0,1);
		$rest  = substr($proceso, 1);
		$proceso = '['.$first.']'.$rest;
		$runCommand = 'ps -A x | grep '. $proceso;
		// el comando verifica los procesos de todos los usuarios
		$output = array();
		exec( $runCommand, $output );
		if(empty($output)){
			return false; // no esta corriendo
		} else {
			return true;
		}
	}
	/**
	 * Quita los saltos de Linea de un string
	 * @param String $string
	 * @return string
	 */
	public static function quitarSaltos($string){
		$string = str_replace(
				array("\n","\r","\t"),
				' ',
				$string
		);
	
		return trim($string);
	}
	
	/**
	 * Limpia la cadena y añade el TS al final de la misma
	 * @param nombre de archivo $string
	 * @return string
	 */
	public static function codificar_filename($string)
	{
		$string = trim($string);
	
		$string = str_replace(
				array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
				array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
				$string
		);
	
		$string = str_replace(
				array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
				array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
				$string
		);
	
		$string = str_replace(
				array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
				array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
				$string
		);
	
		$string = str_replace(
				array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
				array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
				$string
		);
	
		$string = str_replace(
				array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
				array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
				$string
		);
	
		$string = str_replace(
				array('ñ', 'Ñ', 'ç', 'Ç'),
				array('n', 'N', 'c', 'C',),
				$string
		);
	
		//Esta parte se encarga de eliminar cualquier caracter extraño
		$string = str_replace(
				array("\\", "¨", "º", "-", "~",
						"#", "@", "|", "!", "\"",
						"·", "$", "%", "&", "/",
						"(", ")", "?", "'", "¡",
						"¿", "[", "^", "`", "]",
						"+", "}", "{", "¨", "´",
						">", "<", ";", ",", ":"),
				'',
				$string
		);
		$string = str_replace(" ", "_", $string);
	
		$file_name = strtolower( $string );
		$file_name .= date('Ymdhis');
		return $file_name;
	}
	
}



?>