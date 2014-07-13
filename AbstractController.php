<?
namespace stradivari\core {
    abstract class AbstractController extends \stradivari\controller\AbstractController {
        public static $viewNamespace;
        
        protected static function searchFile($view) {
            $filepath = parent::searchFile($view);
            if ( $filepath ) {
                return $filepath;
            }
            $file = left_cut(static::$viewNamespace, "\\") . "/{$view}";
            $filepath = Autoloader::searchPhpFile($file);
            return $filepath;
        }
        public static function __callStatic($calledMethod, $arguments = array()) {
            try {
                return parent::__callStatic($calledMethod, $arguments);
            } catch ( \stradivari\controller\exception\NoSuchRequestMethod $exception ) {
                throw new \stradivari\core\exception\NoSuchRequestMethod($exception->getMessage());
            }
        }
	}
}
