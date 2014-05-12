<?
namespace stradivari\core {
	Autoloader::$vendor = realpath(__DIR__ . '/../../');
	abstract class Autoloader {
		public static $vendor;
		public static $extensions = array('php', 'inc');
		public static $fileMap = array('files', 'classmap', 'psr0' => 'namespaces', 'psr4');
		public static $environments = array(
			'files' => array(),
			'classmap' => array(),
			'psr0' => array(),
			'psr4' => array()
		);
		
		public static function inheritComposer() {
			foreach ( self::$fileMap as $key => $file ) {
				if ( !is_string($key) ) {
					$key = $file;
				}
				$filename = self::$vendor . '/composer/autoload_' . $file . '.php';
				if ( file_exists($filename) ) {
					self::$environments[$key] += include($filename);
				}
			}
		}
		public static function register() {
			self::loadFiles();
			spl_autoload_register('self::autoloader', true, true);
		}
		public static function unregister() {
			spl_autoload_unregister('self::autoloader');
		}
		private static function autoloader($class) {
			if ( self::loadClassmap($class) ) {
				return;
			}
			if ( self::loadNamespace($class) ) {
				return;
			}
			throw new exception\NoSuchClass($class);
		}
		private static function loadFiles() {
			foreach ( self::$environments['files'] as $file ) {
				include_once $file;
			}
		}
		private static function loadNamespace($class) {
			$path = explode('\\', $class);
			$last = array_pop($path);
			$last = explode('_', $last);
			$path = array_merge($path, $last);
			$path = implode('/', $path);
			@include_once self::searchPhpFile($path);
            return class_exists($class, false);
		}
		private static function loadClassmap($class) {
			if ( array_key_exists($class, self::$environments['classmap']) && file_exists(self::$environments['classmap'][$class]) ) {
				@include_once self::$environments['classmap'][$class];
				return class_exists($class, false);
			}
			return false;
		}
        public static function searchPhpFile($path) {
            foreach (self::$extensions as $extension) {
				$file = self::searchFile("{$path}.{$extension}");
				if ( $file ) {
					return $file;
				}
			}
			return false;
        }
		public static function searchFile($path) {
			$path = str_replace('\\', '/', $path);
			foreach (array('psr0', 'psr4') as $curMap) {
				$result = self::psrSearch($path, $curMap);
				if ( $result ) {
					return $result;
				}
			}
			return false;
		}
		private static function psrSearch($path, $map) {
			foreach (self::$environments[$map] as $prefix => $environments ) {
				$prefix = str_replace('\\', '/', $prefix);
				foreach ($environments as $environment) {
					$prefixPos = $prefix ? strpos($path, $prefix) : 0;
					if ( $prefixPos === 0 ) {
						$method = "{$map}Search";
						$file = self::$method($prefix, $environment.'/', $path);
						if ( $file ) {
							return $file;
						}
					}
				}
			}
			return false;
		}
		private static function psr0Search($prefix, $environment, $path) {
			return realpath("{$environment}/{$path}");
		}
		private static function psr4Search($prefix, $environment, $path) {
			return realpath(str_replace($prefix, $environment, $path));
		}
	}
}
