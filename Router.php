<?
namespace stradivari\core {
    abstract class Router {
        public static $controllerNamespace = "\\stradivari\\stradivari_default\\controller";
        public static $defaultRulesFile = null;
        private static $defaultRulesFileName = 'stradivari/stradivari_default/router_rules.yaml';

        public static function executeRulesFile($rulesFile = null) {
			self::$defaultRulesFile = self::$defaultRulesFile ? self::$defaultRulesFile : Autoloader::searchFile(self::$defaultRulesFileName);
			$rulesFile = $rulesFile ? $rulesFile : self::$defaultRulesFile;
			$converter = new \stradivari\data_converter\FileConverter($rulesFile);
			$converter->array();
			self::executeRules($converter->data);
		}
        public static function executeRules($rules) {
			if ( !$rules ) {
				return;
			}
			$uri = App::$input['server']['REQUEST_URI'];
			$url = App::$input['server']['HTTP_HOST'] . $uri;
			foreach ( $rules as $regexp => $rule ) {
                if ( is_string($regexp) ) {
                    $regexp = str_replace('##host##', App::$input['server']['HTTP_HOST'], $regexp);
                    $regexp = str_replace('##url##', $url, $regexp);
                    $regexp = str_replace('##uri##', $uri, $regexp);
                }
				if ( @preg_match($regexp, $url, $matches) ) {
                    $rule = is_array($rule) ? self::parseRestful($rule) : $rule;
                    foreach($matches as $key => $match) {
                        $rule = str_replace("##{$key}##", $match, $rule);
                    }
                    self::route($rule);
				}
			}
        }
        public static function route($route) {
            App::$input['route'] = explode(' ', $route);
            self::routeArray(App::$input['route']);
        }
        private static function routeArray($arguments) {
			$calledClass = self::validateRouteClass(array_shift($arguments));
			$calledMethod = self::validateRouteMethod(array_shift($arguments));
            self::request($calledClass, $calledMethod, $arguments);
        }
        public static function routeServer($uri) {
            $arguments = explode('&', $uri);
			$arguments = $arguments[0];
			$arguments = explode('/', $arguments);
            array_shift($arguments);
			$calledClass = self::validateServerClass(array_shift($arguments));
			$calledMethod = self::validateServerMethod(array_shift($arguments));
            self::request($calledClass, $calledMethod, $arguments);
        }
        private static function validateServerClass($calledClass) {
            $calledClass = ucfirst(strtolower($calledClass));
            if ( $calledClass == 'Main' ) {
				throw new exception\NoSuchRequest();
			}
            $calledClass = $calledClass ? $calledClass : "Main";
            $calledClass = self::$controllerNamespace . "\\" . $calledClass;
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
            return $calledClass ? $calledClass : self::$controllerNamespace . "\\" . "Main";
        }
        private static function validateRouteMethod($calledMethod) {
            return $calledMethod = $calledMethod ? $calledMethod : "main";
        }
        private static function request($calledClass, $calledMethod, $arguments) {
			App::$input['arguments'] = $arguments;
            if ( !class_exists($calledClass) ) {
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
