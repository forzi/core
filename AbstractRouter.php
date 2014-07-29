<?
namespace stradivari\core {
    abstract class AbstractRouter extends \stradivari\controller\AbstractRouter {
        public static function __callStatic($calledMethod, $arguments = array()) {
            try {
                return parent::__callStatic($calledMethod, $arguments);
            } catch ( \stradivari\controller\exception\NoSuchRequestMethod $exception ) {
                throw new \stradivari\core\exception\NoSuchRequestMethod($exception->getMessage());
            }
        }
	}
}
