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
            $defaultSettings['defaultNamespace'] = '\\' . self::$pool['settings']['company'] . '\\' . self::$pool['settings']['product'];
            $defaultSettings['modelNamespace'] = $defaultSettings['defaultNamespace'] . '\model';
            $defaultSettings['viewNamespace'] = $defaultSettings['defaultNamespace'] . '\view';
            $defaultSettings['controllerNamespace'] = $defaultSettings['defaultNamespace'] . '\router';
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
			self::$pool['input']['url'] = self::serverUrl();
			foreach ( array('redirector', 'router') as $director ) {
				foreach ( self::$pool['input']['url'] as $key => &$part ) {
					$filePath = Autoloader::searchFile(self::$pool['settings']['defaultSubDir'] . "/{$director}_{$key}.yaml");
					if ( $filePath ) {
						$class = '\stradivari\core\\' . ucfirst($director);
						$class::executeRulesFile($filePath, $key);
					}
				}
				unset($part);
			}
            try {
				Router::routeServer(self::$pool['input']['server']['REQUEST_URI']);
			} catch ( exception\RequestException $e ) {
				throw new exception\NoSuchPage();
			}
		}
		private static function serverUrl() {
			$result = array();
			$result['scheme'] = strtolower($_SERVER['SERVER_PROTOCOL']);
			$result['scheme'] = explode('/', $result['scheme']);
			$result['scheme'] = array_combine(array('name', 'version'), $result['scheme']);
			$result['scheme'] = $result['scheme']['name'];
			$result['host'] = $_SERVER['HTTP_HOST'];
			$result['port'] = $_SERVER['SERVER_PORT'];
			$result['part'] = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : '/';
			$questionPosition = strpos($result['part'], '?');
			if ( $questionPosition === false ) {
				$result['path'] = $result['part'];
				$result['query'] = '';
			} else {
				$result['path'] = substr($result['part'], 0, $questionPosition);
				$result['query'] = substr($result['part'], $questionPosition);
			}
			return $result;
		}
        private static function console() {
            $arguments = self::$pool['input']['argv'];
            array_shift($arguments);
            Router::route(implode(' ', $arguments));
        }
	}
}
