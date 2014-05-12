<?
namespace stradivari\core {
	abstract class App {
		public static $input = array();
		public static $pool = null;
		public static function execute() {
			try {
				if ( isset(self::$input['argv']) ) {
					self::console();
				} else {
					self::server();
				}
			} catch ( \Exception $exception ) {
				ExceptionInterceptor::execute($exception);
			}
		}
		private static function server() {
			self::$input['get'] = $_GET;
			self::$input['post'] = $_POST;
			self::$input['request'] = $_REQUEST;
			self::$input['input'] = file_get_contents("php://input");
			self::$input['headers'] = getallheaders();
			self::$input['server'] = $_SERVER;
			Redirector::executeRulesFile();
            Router::executeRulesFile();
            try {
				Router::routeServer(self::$input['server']['REQUEST_URI']);
			} catch ( exception\RequestException $e ) {
				throw new exception\NoSuchPage();
			}
		}
        private static function console() {
            $arguments = self::$input['argv'];
            array_shift($arguments);
            Router::route(implode(' ', $arguments));
        }
	}
}
