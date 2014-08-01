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
			self::$pool['input'] = isset(self::$pool['input']) ? self::$pool['input'] : array();
			self::$pool['input'] += parseServerParams();
			self::$pool['input']['url'] = new \stradivari\url\Url(self::$pool['input']['url']);
			foreach ( array('redirector', 'router') as $director ) {
				foreach ( self::$pool['input']['url'] as $key => $part ) {
					$filePath = Autoloader::searchFile(self::$pool['settings']['defaultSubDir'] . "/{$director}_{$key}.yaml");
					if ( $filePath ) {
						$class = '\stradivari\core\\' . ucfirst($director);
						$class::executeRulesFile($filePath, $key);
					}
				}
				unset($part);
			}
            try {
				Router::routeServer(self::$pool['input']['url']['path']);
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
