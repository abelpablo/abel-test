<?php
class Common_Db_Table extends Zend_Db_Table_Abstract {
	
	/**
	 * Método para construcción del where para filtrar la grilla DataTables
	 * 
	 * @param Array $filters        	
	 */
	public function buildWhere($filters = array()) {
		$sql = '';
		foreach ( $filters as $campo => $opciones ) {
			$valores = $opciones ['values'];
			$operador = $opciones ['op'];
			// conversion de operadores relacionales
			// EQ NE GT LT GE LE
			switch ($operador) {
				case 'EQ' :
					// igual, la lista de valores es igual al campo (Sirve para comparar un sólo valor)
					// ya que un campo no puede tener mas de un valor
					$operador = '= ';
					break;
				case 'NE' :
					// No es igual o diferente a todos los elementos listados
					$operador = '!= ';
					break;
				case 'GT' :
					// El campo es mayor al del listado de valores
					$operador = '> ';
					break;
				case 'LT' :
					// El campo es menor al del listado de valores
					$operador = '< ';
					break;
				case 'GE' :
					// El campo es mayor o igual al del listado de valores
					$operador = '>=';
					break;
				case 'LE' :
					// El campo es menor al del listado de valores
					$operador = '<=';
					break;
				case 'IN' :
					$operador = 'IN';
					break;
				case 'BETWEEN' :
					$operador = 'BETWEEN';
					break;
				case 'LIKE' :
					$operador = 'LIKE';
					break;
				default :
					throw new Exception ( 'Operador de filtro avanzado no soportado' );
					break;
			}
			
			if (is_array ( $valores )) {
				if ($operador === 'BETWEEN') {
					$sql .= ' AND ' . $campo . ' ' . $operador . ' ';
				} elseif ($operador === 'LIKE') {
					$sql .= ' AND LOWER(' . $campo . ') ' . $operador . " '%";
				} else {
					$sql .= ' AND ' . $campo . ' ' . $operador . ' (';
				}
				$removeChars = $operador === 'BETWEEN' ? 5 : 1;
				foreach ( $valores as $valor ) {
					// detectar si es fecha
					$patron = "/[0-9]{2}-[0-9]{2}-[0-9]{4}$/";
					// Detecto si es fecha y agrego TO_DATE('27-06-2009','DD-MM-YYYY')
					$valor = preg_match ( $patron, $valor ) ? "'" . $this->convFechaSQL ( $valor ) . "'" : (is_numeric ( $valor ) ? $valor : ($operador !== 'LIKE' ? "'" . $valor . "'" : strtolower ( $valor )));
					// si el operador es LIKE el separador es % y debo añadirselos si la cadena tiene espacios
					$valor = $operador === 'LIKE' ? str_replace ( ' ', '%', $valor ) : $valor;
					$separador = $operador === 'BETWEEN' ? ' AND ' : ($operador === 'LIKE' ? '%' : ',');
					$sql .= $valor . $separador;
				}
				$sql = substr ( $sql, 0, ($removeChars * - 1) );
				if ($operador === 'BETWEEN') {
					$sql .= ' ';
				} elseif ($operador === 'LIKE') {
					$sql .= "%' ";
				} else {
					$sql .= ') ';
				}
			} else {
				if ($operador === 'BETWEEN') {
					$sql .= ' AND ' . $campo . ' ' . $operador . ' ';
				} elseif ($operador === 'LIKE') {
					$sql .= ' AND LOWER(' . $campo . ') ' . $operador . " '%";
				} else {
					// verificar si el valor es una fecha y aplicar al campo DATE_FORMAT para que la comparacion funcione
					$patron = "/[0-9]{2}-[0-9]{2}-[0-9]{4}$/";
					$campo = preg_match ( $patron, $valores ) ? "DATE_FORMAT($campo, '%Y-%m-%d')" : $campo;
					$sql .= ' AND ' . $campo . ' ' . $operador;
				}
				$valor = $valores;
				$patron = "/[0-9]{2}-[0-9]{2}-[0-9]{4}$/";
				$valor = preg_match ( $patron, $valor ) ? "'" . $this->convFechaSQL ( $valor ) . "'" : (is_numeric ( $valor ) ? $valor : ($operador !== 'LIKE' ? "'" . $valor . "'" : strtolower ( $valor )));
				$separador = $operador;
				$sql .= $valor;
			}
		}
		return $sql;
	}
	/**
	 * Invierte el orden de una fecha
	 * 
	 * @param string $fecha        	
	 * @return string
	 */
	private function convFechaSQL($fecha) {
		if (! empty ( $fecha )) {
			$fechas = explode ( "-", $fecha );
			return $fechas [2] . "-" . $fechas [1] . "-" . $fechas [0];
		} else {
			return "";
		}
	}
	
	/**
	 * Imprime el Json para alimentar a jqueryDatatables
	 * 
	 * @param array $dataCursor
	 *        	espera un array con tres claves "cursor", "count", "countWhere"
	 */
	public function printJsonGrid($dataCursor, $sEcho) {
		$i = 0;
		$cursor = $dataCursor ['cursor'];
		$totalRegistros = $dataCursor ['count'];
		$totalRegistrosWere = $dataCursor ['countWhere'];
		
		echo '{"aaData": [';
		while ( $row = $cursor->fetch ( Zend_Db::FETCH_NUM ) ) {
			
			if ($i <= 5000) {
				if ($i > 0) {
					echo ",";
				}
				$json = '{"DT_RowId": "' . $row [0] . '", ';
				$PK = array_shift ( $row ); // se asume que la primer fila tiene PK
				                         // $json = Zend_Json_Encoder::encode($row);
				foreach ( $row as $k => $v ) {
					$json .= '"' . $k . '":' . Zend_Json_Encoder::encode ( $v ) . ',';
				}
				$json = substr ( $json, 0, - 1 );
				$json .= '}';
				echo $json;
				$i ++;
			} else {
				break;
			}
		}
		echo "],";
		echo '"sEcho": ' . $sEcho . ',';
		echo '"iTotalRecords":' . '"' . $totalRegistros . '",';
		echo '"iTotalDisplayRecords": "' . $totalRegistrosWere . '"';
		echo "}";
	}
}