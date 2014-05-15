<?
namespace stradivari\core {
    abstract class AbstractController extends \stradivari\controller\AbstractController {
        public static $viewNamespace = "\\stradivari\\stradivari_default\\view";
        
        protected static function searchFile($view) {
            $filepath = parent::searchFile($view);
            if ( $filepath ) {
                return $filepath;
            }
            $file = left_cut(static::$viewNamespace, "\\") . "/{$view}";
            $filepath = Autoloader::searchPhpFile($file);
            return $filepath;
        }
	}
}
