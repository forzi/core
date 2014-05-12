<?
namespace stradivari\core {
    abstract class AbstractController extends \stradivari\controller\AbstractController {
        public static $viewNamespace = "\\stradivari\\stradivari_default\\view";

        protected static function searchFile($file) {
            $filepath = parent::searchFile($file);
            $filepath = $filepath ? $filepath : Autoloader::searchPhpFile($file);
            return $filepath;
        }
	}
}
