<?
namespace stradivari\core {
	abstract class Director {
		public static $lowerCase = true;
		public static $regexpFlags = '';
		
		public static function executeRulesFile($rulesFile, $execute, $redirectType = null) {
			$converter = new \stradivari\data_converter\FileConverter($rulesFile);
			$converter->array();
			static::executeRules($converter->data, $execute, $redirectType);
		}
        public static function executeRules($rules, $execute, $redirectType = null) {
			if ( !$rules ) {
				return;
			}
			$url = App::$pool['input']['url'];
			if ( static::$lowerCase ) {
				foreach ( $url as &$part ) {
					$part = strtolower($part);
				}
				unset($part);
			}
			foreach ( $rules as $regexp => $rule ) {
				static::executeRule($regexp, $rule, $url, $execute, $redirectType);
			}
		}
		protected static function executeRule($regexp, $rule, $url, $execute, $redirectType = null) {
			foreach (array('regexp', 'rule') as $param) {
				if ( is_string($$param) ) {
					$$param = static::prepareExpression($$param, $url);
				}
			}
			$regexp = '/' . str_replace('/', '\/', $regexp) . '/';
			$regexp .= static::$regexpFlags;
			if ( @preg_match($regexp, $url[$execute], $matches) ) {
				if ( is_string($rule) ) {
					$rule = static::prepareExpression($rule, $matches);
					static::execute($rule, $redirectType);
				} else {
					static::executeRules($rule, $execute, $redirectType);
				}
			}
		}
		protected static function prepareExpression($expression, $rules) {
			foreach ( $rules as $key => &$part ) {
				$expression = str_replace("##{$key}##", $part, $expression);
			}
			return $expression;
		}
		protected static function execute($rule, $redirectType) {}
	}
}