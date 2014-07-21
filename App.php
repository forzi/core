<?
namespace stradivari\core {
	abstract class App {
		public static $pool = null;
        public static $creator = null;
		public static function execute() {
            self::$pool['settings'] += self::defaultSettings();
            Router::$controllerNamespace = self::$pool['settings']['controllerNamespace'];
            AbstractController::$viewNamespace = self::$pool['settings']['viewNamespace'];
            if ( self::$pool['settings']['sessionName'] ) {
                ini_set('session.name', self::$pool['settings']['sessionName']);
                session_start();
            }
            if ( isset(self::$pool['input']['argv']) ) {
                self::console();
            } else {
                self::server();
            }
		}
        private static function defaultSettings() {
            $defaultSettings['sessionName'] = 'sSid';
            $defaultSettings['defaultSubDir'] = self::$pool['settings']['company'] . '/' . self::$pool['settings']['product'];
            $defaultSettings['redirectQueryFile'] = Autoloader::searchFile($defaultSettings['defaultSubDir'] . '/redirect_query.yaml');
			$defaultSettings['redirectUriFile'] = Autoloader::searchFile($defaultSettings['defaultSubDir'] . '/redirect_uri.yaml');
			$defaultSettings['routeQueryFile'] = Autoloader::searchFile($defaultSettings['defaultSubDir'] . '/route_query.yaml');
			$defaultSettings['routeUriFile'] = Autoloader::searchFile($defaultSettings['defaultSubDir'] . '/route_uri.yaml');
            $defaultSettings['defaultNamespace'] = '\\' . self::$pool['settings']['company'] . '\\' . self::$pool['settings']['product'];
            $defaultSettings['modelNamespace'] = $defaultSettings['defaultNamespace'] . '\model';
            $defaultSettings['viewNamespace'] = $defaultSettings['defaultNamespace'] . '\view';
            $defaultSettings['controllerNamespace'] = $defaultSettings['defaultNamespace'] . '\controller';
            return $defaultSettings;
        }
        private static function server() {
			self::$pool['input']['get'] = $_GET;
			self::$pool['input']['post'] = $_POST;
			self::$pool['input']['request'] = $_REQUEST;
			self::$pool['input']['input'] = file_get_contents("php://input");
			self::$pool['input']['headers'] = getallheaders();
			self::$pool['input']['server'] = $_SERVER;
            self::$pool['input']['env'] = $_ENV;
            self::$pool['input']['files'] = $_FILES;
            if ( isset($_SESSION) ) {
                self::$pool['input']['session'] = &$_SESSION;
            }
			Redirector::executeRulesFile(self::$pool['settings']['redirectQueryFile'], false);
			Redirector::executeRulesFile(self::$pool['settings']['redirectUriFile'], true);
            Router::executeRulesFile(self::$pool['settings']['routeQueryFile'], false);
			Router::executeRulesFile(self::$pool['settings']['routeUriFile'], true);
            try {
				Router::routeServer(self::$pool['input']['server']['REQUEST_URI']);
			} catch ( exception\RequestException $e ) {
				throw new exception\NoSuchPage();
			}
		}
        private static function console() {
            $arguments = self::$pool['input']['argv'];
            array_shift($arguments);
            Router::route(implode(' ', $arguments));
        }
	}
}
