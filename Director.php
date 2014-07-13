<?
namespace stradivari\core {
	abstract class Director {
		 public static function executeRulesFile($rulesFile, $redirectType = null) {
			$converter = new \stradivari\data_converter\FileConverter($rulesFile);
			$converter->array();
			static::executeRules($converter->data, $redirectType);
		}
        public static function executeRules($rules, $redirectType = null) {
			if ( !$rules ) {
				return;
			}
			$uri = App::$pool['input']['server']['REQUEST_URI'];
			$url = App::$pool['input']['server']['HTTP_HOST'] . $uri;
			$query = App::$pool['input']['server']['QUERY_STRING'];
			$params = left_cut($uri, $query);
			foreach ( $rules as $regexp => $rule ) {
				foreach (array('regexp', 'rule') as $param) {
					if ( is_string($$param) ) {
						$$param = str_replace('##host##', App::$pool['input']['server']['HTTP_HOST'], $$param);
						$$param = str_replace('##url##', $url, $$param);
						$$param = str_replace('##uri##', $uri, $$param);
						$$param = str_replace('##query##', $query, $$param);
						$$param = str_replace('##params##', $params, $$param);
					}
				}
                $regexp = '/' . str_replace('/', '\/', $regexp) . '/';
				if ( @preg_match($regexp, $query, $matches) ) {
					if ( is_string($rule) ) {
                        foreach($matches as $key => $match) {
                            $rule = str_replace("##{$key}##", $match, $rule);
                        }
						static::execute($rule, $redirectType);
					} else {
						static::executeRules($rule, $redirectType);
					}
				}
			}
		}
		protected static function execute($rule, $redirectType) {}
	}
}