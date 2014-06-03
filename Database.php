<?php
defined('BASEPATH') or define('BASEPATH', rtrim(str_replace(pathinfo(__FILE__, PATHINFO_BASENAME), '', __FILE__), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
defined('ENVIRONMENT') or define('ENVIRONMENT', 'development');

/**
 * Database
 *
 * Class extraida do Codeigniter
 */
class Database
{
    private static $instance;

    /**
     * Constructor
     */
    public function __construct()
    {
        self::$instance =& $this;
    }

    public static function &getInstance()
    {
        return self::$instance;
    }

    public function connect($params = '', $return = false, $active_record = null)
    {
        if ($return === true) {
            return $this->DB($params, $active_record);
        }

        // Initialize the db variable.  Needed to prevent
        // reference errors with some configurations
        $this->db = '';

        // Load the DB class
        $this->db =& $this->DB($params, $active_record);
    }

    private function &DB($params = '', $active_record_override = null)
    {
        if (is_string($params)) {

            // parse the URL from the DSN string
            // Database settings can be passed as discreet
            // parameters or as a data source name in the first
            // parameter. DSNs must have this prototype:
            // $dsn = 'driver://username:password@hostname/database';

            if (($dns = @parse_url($params)) === false) {
                show_error('Invalid DB Connection String');
            }

            $params = array(
                'dbdriver'  => $dns['scheme'],
                'hostname'  => (isset($dns['host'])) ? rawurldecode($dns['host']) : '',
                'username'  => (isset($dns['user'])) ? rawurldecode($dns['user']) : '',
                'password'  => (isset($dns['pass'])) ? rawurldecode($dns['pass']) : '',
                'database'  => (isset($dns['path'])) ? rawurldecode(substr($dns['path'], 1)) : ''
            );

            // were additional config items set?
            if (isset($dns['query'])) {
                parse_str($dns['query'], $extra);

                foreach ($extra as $key => $val) {
                    // booleans please
                    if (strtoupper($val) == "true") {
                        $val = true;
                    } elseif (strtoupper($val) == "false") {
                        $val = false;
                    }

                    $params[$key] = $val;
                }
            }
        }

        // No DB specified yet?  Beat them senseless...
        if (!isset($params['dbdriver']) OR $params['dbdriver'] == '') {
            show_error('You have not selected a database type to connect to.');
        }

        // Load the DB classes.  Note: Since the active record class is optional
        // we need to dynamically create a class that extends proper parent class
        // based on whether we're using the active record class or not.
        // Kudos to Paul for discovering this clever use of eval()

        if ($active_record_override !== null) {
            $active_record = $active_record_override;
        }

        require_once(BASEPATH.'database/DB_driver.php');

        if (!isset($active_record) OR $active_record == true) {
            require_once(BASEPATH.'database/DB_active_rec.php');

            if (!class_exists('CI_DB')) {
                eval('class CI_DB extends CI_DB_active_record { }');
            }
        } else {
            if (!class_exists('CI_DB')) {
                eval('class CI_DB extends CI_DB_driver { }');
            }
        }

        require_once(BASEPATH.'database/drivers/'.$params['dbdriver'].'/'.$params['dbdriver'].'_driver.php');

        // Instantiate the DB adapter
        $driver = 'CI_DB_'.$params['dbdriver'].'_driver';
        $DB = new $driver($params);

        if ($DB->autoinit == true) {
            $DB->initialize();
        }

        if (isset($params['stricton']) && $params['stricton'] == true) {
            $DB->query('SET SESSION sql_mode="STRICT_ALL_TABLES"');
        }

        return $DB;
    }
}

// ---------------------------------------------- //

if (!function_exists('get_instance')) {
    function &get_instance()
    {
        return Database::getInstance();
    }
}

if (!function_exists('show_error')) {
    function show_error($message, $status_code = 500, $heading = 'An Error Was Encountered')
    {
        if (defined('ENVIRONMENT') && ENVIRONMENT == 'development') {
            echo $heading . '<br>';
            echo $message . '<br>';
            echo $status_code . '<br>';
            exit;
        }
    }
}

if (!function_exists('log_message')) {
    function log_message($level = 'error', $message, $php_error = false)
    {
        if (defined('ENVIRONMENT') && ENVIRONMENT == 'development') {
            echo '-------------------------------------------------<br>';
            echo $level . '<br>';
            echo $message . '<br>';
            echo $php_error . '<br>';
            echo '-------------------------------------------------<br>';
        }
    }
}
