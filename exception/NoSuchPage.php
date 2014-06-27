<?
namespace stradivari\core\exception {
    class NoSuchPage extends RequestException implements \stradivari\interfaces\WebException {
        public function __construct() {
            parent::__construct();
            $this->code = 404;
            $this->message = 'Not Found';
        }
        public function headers() {
            return array();
        }
    }
}
