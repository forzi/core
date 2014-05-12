<?
namespace stradivari\core {
    abstract class ExceptionInterceptor {
        public static function execute(\Exception $exception) {
            echo '<pre>';
            throw $exception;
        }
    }
}
