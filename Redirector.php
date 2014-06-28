<?
namespace stradivari\core {
    abstract class Redirector {
		public static $defaultRedirect = 301;
        
        public static function executeRulesFile($rulesFile = null, $redirectType = null) {
			$converter = new \stradivari\data_converter\FileConverter($rulesFile);
			$converter->array();
			self::executeRules($converter->data, $redirectType);
		}
        public static function executeRules($rules, $redirectType = null) {
			if ( !$rules ) {
				return;
			}
			$uri = App::$pool['input']['server']['REQUEST_URI'];
			$url = App::$pool['input']['server']['HTTP_HOST'] . $uri;
			foreach ( $rules as $regexp => $redirect ) {
				foreach (array('regexp', 'redirect') as $param) {
					if ( is_string($$param) ) {
						$$param = str_replace('##host##', App::$pool['input']['server']['HTTP_HOST'], $$param);
						$$param = str_replace('##url##', $url, $$param);
						$$param = str_replace('##uri##', $uri, $$param);
					}
				}
                $regexp = '/' . str_replace('/', '\/', $regexp) . '/';
				if ( @preg_match($regexp, $url, $matches) ) {
					if ( is_string($redirect) ) {
                        foreach($matches as $key => $match) {
                            $redirect = str_replace("##{$key}##", $match, $redirect);
                        }
						self::redirect($redirect, $redirectType);
					} else { 
						self::executeRules($redirect, $redirectType);
					}
				}
			}
		}
        public static function redirect($redirect, $redirectType = null) {
			$redirectType = $redirectType == null ? self::$defaultRedirect : $redirectType;
			header('Location: ' . $redirect, true, $redirectType);
			exit;
		}
    }
}
