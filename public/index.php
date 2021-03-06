<?php
define('SCRIPT_START', microtime(true));
define('PROJECT_NAME', 'Boilerplate');
date_default_timezone_set("Europe/London");
ini_set('default_charset', 'UTF-8');
mb_internal_encoding("UTF-8");

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
if (! defined('ENVIRONMENT')) {
    $environment = 'production';
    if (getenv('ENVIRONMENT') == 'development') {
        $environment = 'development';
    }
    if (isset($_SERVER['DEVELOPMENT']) && $_SERVER['DEVELOPMENT'] === 'true') {
        $environment = 'development';
    }
    if (gethostname() == 'server2') {
        $environment = 'development';
    }
    define('ENVIRONMENT', $environment);
    unset ($environment);
}

/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */

if (defined('ENVIRONMENT')) {
    switch (ENVIRONMENT) {
        case 'development':
            error_reporting(E_ALL);
            break;

        case 'testing':
        case 'production':
            error_reporting(E_ALL);
            break;

        default:
            exit('The application environment is not set correctly.');
    }
}

/*
 *---------------------------------------------------------------
 * SYSTEM FOLDER NAME
 *---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" folder.
 * Include the path if the folder is not in the same  directory
 * as this file.
 *
 */
$system_path = '../system';

/*
 *---------------------------------------------------------------
 * APPLICATION FOLDER NAME
 *---------------------------------------------------------------
 *
 * If you want this front controller to use a different "application"
 * folder then the default one you can set its name here. The folder
 * can also be renamed or relocated anywhere on your server.  If
 * you do, use a full server path. For more info please see the user guide:
 * http://codeigniter.com/user_guide/general/managing_apps.html
 *
 * NO TRAILING SLASH!
 *
 */
$application_folder = '../application';

/*
 * --------------------------------------------------------------------
 * DEFAULT CONTROLLER
 * --------------------------------------------------------------------
 *
 * Normally you will set your default controller in the routes.php file.
 * You can, however, force a custom routing by hard-coding a
 * specific controller class/function here.  For most applications, you
 * WILL NOT set your routing here, but it's an option for those
 * special instances where you might want to override the standard
 * routing in a specific front controller that shares a common CI installation.
 *
 * IMPORTANT:  If you set the routing here, NO OTHER controller will be
 * callable. In essence, this preference limits your application to ONE
 * specific controller.  Leave the function name blank if you need
 * to call functions dynamically via the URI.
 *
 * Un-comment the $routing array below to use this feature
 *
 */
// The directory name, relative to the "controllers" folder.  Leave blank
// if your controller is not in a sub-folder within the "controllers" folder
// $routing['directory'] = '';

// The controller class file name.  Example:  Mycontroller
// $routing['controller'] = '';

// The controller function you wish to be called.
// $routing['function']	= '';


/*
 * -------------------------------------------------------------------
 *  CUSTOM CONFIG VALUES
 * -------------------------------------------------------------------
 *
 * The $assign_to_config array below will be passed dynamically to the
 * config class when initialized. This allows you to set custom config
 * items or override any default config values found in the config.php file.
 * This can be handy as it permits you to share one application between
 * multiple front controller files, with each file containing different
 * config values.
 *
 * Un-comment the $assign_to_config array below to use this feature
 *
 */
// $assign_to_config['name_of_config_item'] = 'value of config item';


// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */

// Set the current directory correctly for CLI requests
if (defined('STDIN')) {
    chdir(dirname(__FILE__));
}

if (realpath($system_path) !== false) {
    $system_path = realpath($system_path) . '/';
}

// ensure there's a trailing slash
$system_path = rtrim($system_path, '/') . '/';

// Is the system path correct?
if (! is_dir($system_path)) {
    exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: " . pathinfo(
            __FILE__,
            PATHINFO_BASENAME
        ));
}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
// The name of THIS file
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

// The PHP file extension
// this global constant is deprecated.
define('EXT', '.php');

// Path to the system folder
define('BASEPATH', str_replace("\\", "/", $system_path));

// Path to the front controller (this file)
define('FCPATH', str_replace(SELF, '', __FILE__));

// Name of the "system folder"
define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));


// The path to the "application" folder
if (is_dir($application_folder)) {
    define('APPPATH', $application_folder . '/');
} else {
    if (! is_dir(BASEPATH . $application_folder . '/')) {
        exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: " . SELF);
    }

    define('APPPATH', BASEPATH . $application_folder . '/');
}

define('PROJECT_ROOT', realpath(BASEPATH . '/../') . '/');

define('VENDOR_DIR', realpath('../vendor') . '/');
include_once VENDOR_DIR . '/autoload.php';

$al = new \Composer\Autoload\ClassLoader();
// $al->set('CustomNamespace', PROJECT_ROOT);
$al->register();

/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 *
 */
register_shutdown_function('shutdown_handler');
if (! function_exists('my_exception_handler')) {
    /**
     * @param Exception $e
     */
    function my_exception_handler($e)
    {
        require_once(BASEPATH . 'core/Exceptions.php');
        require_once(APPPATH . '/core/MY_Exceptions.php');
        if (class_exists('MY_Exceptions')) {
            $_error = new MY_Exceptions();
            $_error->show_exception($e);
        }
    }

    set_exception_handler('my_exception_handler');
}
require_once(VENDOR_DIR . '/ircmaxell/password-compat/lib/password.php');
require_once(APPPATH . '/third_party/compat.php');

require_once BASEPATH . 'core/CodeIgniter.php';
exit();

/**
 * Handler to try and catch errors that would not be caught by normal error handling
 * Note: You MUST NOT assume that anything is available here - (eg. CI_Controller, constants, that get_instance works)
 */
function shutdown_handler()
{
    $lastError = error_get_last();
    if (! (is_array($lastError) && array_key_exists('type', $lastError))) {
        return;
    }
    $reportLevels = array(E_PARSE, E_COMPILE_ERROR, E_COMPILE_WARNING, E_CORE_ERROR, E_CORE_WARNING, E_ERROR);
    if (! in_array($lastError['type'], $reportLevels)) {
        return;
    }

    // We provide a fallback value for DEVELOPER_EMAILS just in case it's not defined for any reason
    $emails = 'user@example.com';
    if (defined('DEVELOPER_EMAILS')) {
        $emails = DEVELOPER_EMAILS;
    }

    $backtrace = print_r(debug_backtrace(), true);

    $msg = "Shutdown Handler Error Report\n"
        . "Error:\n" . print_r($lastError, true) . "\n\n"
        . "Backtrace:\n" . $backtrace . "\n\n"
        . "_SESSION:\n" . (isset($_SESSION) ? print_r($_SESSION, true) : 'unset') . "\n\n"
        . "_SERVER:\n" . print_r($_SERVER, true) . "\n\n"
        . "--- EOM ---\n";
    @mail($emails, PROJECT_NAME . " Shutdown PHP Error", $msg);

    if (defined('ERROR_HANDLER_LOG')) {
        file_put_contents(ERROR_HANDLER_LOG, $msg, FILE_APPEND);
    }
}

function append_include_path($path)
{
    return set_include_path(get_include_path() . PATH_SEPARATOR . $path);
}

/* End of file index.php */
/* Location: ./index.php */