<?
namespace stradivari\core {
    abstract class Router extends Director {
        public static $controllerNamespace;
		
		protected static function execute($route, $redirectType) {
			static::route($route);
		}
		public static function route($route) {
            App::$pool['input']['route'] = explode(' ', $route);
            static::routeArray(App::$pool['input']['route']);
        }
        private static function routeArray($arguments) {
			$calledClass = static::validateRouteClass(array_shift($arguments));
			$calledMethod = static::validateRouteMethod(array_shift($arguments));
            static::request($calledClass, $calledMethod, $arguments);
        }
        public static function routeServer($uri) {
            $arguments = explode('&', $uri);
			$arguments = $arguments[0];
			$arguments = explode('/', $arguments);
            array_shift($arguments);
			$calledClass = static::validateServerClass(array_shift($arguments));
			$calledMethod = static::validateServerMethod(array_shift($arguments));
            static::request($calledClass, $calledMethod, $arguments);
        }
        private static function validateServerClass($calledClass) {
            $calledClass = ucfirst(strtolower($calledClass));
            if ( $calledClass == 'Main' ) {
				throw new exception\NoSuchRequest();
			}
            $calledClass = $calledClass ? $calledClass : "Main";
            $calledClass = static::$controllerNamespace . "\\" . $calledClass;
            return $calledClass;
        }
        private static function validateServerMethod($calledMethod) {
            $calledMethod = strtolower($calledMethod);
            if ( $calledMethod == 'main' ) {
				throw new exception\NoSuchRequestMethod();
			}
            $calledMethod = $calledMethod ? $calledMethod : 'main';
            return $calledMethod;
        }
        private static function validateRouteClass($calledClass) {
            return $calledClass ? $calledClass : static::$controllerNamespace . "\\" . "Main";
        }
        private static function validateRouteMethod($calledMethod) {
            return $calledMethod = $calledMethod ? $calledMethod : "main";
        }
        private static function request($calledClass, $calledMethod, $arguments) {
			App::$pool['input']['arguments'] = $arguments;
            try {
                class_exists($calledClass);
            } catch ( exception\NoSuchClass $e ) {
                throw new exception\NoSuchRequest($calledClass);
            }
            if ( call_user_func_array("{$calledClass}::{$calledMethod}", $arguments) !== false ) {
                exit;
            }
            if ( call_user_func_array(array(new $calledClass(), $calledMethod), $arguments) !== false ) {
                exit;
            }
			throw new exception\NoSuchRequestMethod($calledClass . '::' . $calledMethod);
		}
    }
}
