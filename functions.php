<?if ( !function_exists('d') ) {    function d($message = '', $break = 1) {        if ( $message !== '' ) {            echo '<pre>' . print_r($message, 1) . '</pre>' . "\n";        }        if ( $break ) {            die();        }    }}if (!function_exists('getallheaders')){    function getallheaders()    {           $headers = '';       foreach ($_SERVER as $name => $value)       {           if (substr($name, 0, 5) == 'HTTP_')           {               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;           }       }       return $headers;    }}if (!function_exists('array_merge_recursive_distinct')) {    /**     * array_merge_recursive does indeed merge arrays, but it converts values with duplicate     * keys to arrays rather than overwriting the value in the first array with the duplicate     * value in the second array, as array_merge does. I.e., with array_merge_recursive,     * this happens (documented behavior):     *     * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));     *     => array('key' => array('org value', 'new value'));     *     * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.     * Matching keys' values in the second array overwrite those in the first array, as is the     * case with array_merge, i.e.:     *     * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));     *     => array('key' => array('new value'));     *     * Parameters are passed by reference, though only for performance reasons. They're not     * altered by this function.     *     * @param $array1     * @param $array2     * @return array     * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>     * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>     * @joint_author Penkov Alexey <forzi (at) mail333 (dot) com>     */    function array_merge_recursive_distinct ($array1, $array2)    {      $merged = $array1;            foreach ( $array2 as $key => &$value )      {        if ( (is_array($value) || $value instanceof \Traversable) && isset($merged[$key]) && (is_array($merged[$key]) || $merged[$key] instanceof \Traversable) )        {          $merged[$key] = array_merge_recursive_distinct($merged[$key], $value);        }        else        {          $merged[$key] = $value;        }      }      unset($key);      unset($value);      return $merged;    }}if ( !function_exists('array_intersect_key_recursive') ) {    function array_intersect_key_recursive($array, $pattern)    {        $result = array();        foreach ( $pattern as $key => &$value )         {            if ( isset($array[$key]) )             {                if ( (is_array($value) || $value instanceof \Traversable) && (is_array($array[$key]) || $array[$key] instanceof \Traversable) )                 {                    $result[$key] = array_intersect_key_recursive($array[$key], $value);                }                else                {                    $result[$key] = $array[$key];                }            }        }        unset($key);        unset($value);        return $result;    }}if (!function_exists('right_cut')) {	function right_cut($str = '', $to_cut = '') {		$cut_len = strlen($to_cut);		$str_len = strlen($str);		if ( substr($str, -$cut_len) == $to_cut ) {			$str = substr($str, 0, $str_len - $cut_len);		}		return $str;	}}if (!function_exists('left_cut')) {	function left_cut($str = '', $to_cut = '') {		$cut_len = strlen($to_cut);		$str_len = strlen($str);		if ( substr($str, 0, $cut_len) == $to_cut ) {			$str = substr($str, $cut_len);		}		return $str;	}}if (!function_exists('cut')) {	function cut($str = '', $to_cut = '') {		$str = right_cut($str, $to_cut);		$str = left_cut($str, $to_cut);		return $str;	}}