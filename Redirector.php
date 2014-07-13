<?
namespace stradivari\core {
    abstract class Redirector extends Director {
		public static $defaultRedirect = 301;
        
		protected static function execute($redirect, $redirectType) {
			static::redirect($redirect, $redirectType);
		}
        public static function redirect($redirect, $redirectType = null) {
			$redirectType = $redirectType == null ? static::$defaultRedirect : $redirectType;
			header('Location: ' . $redirect, true, $redirectType);
			exit;
		}
    }
}
