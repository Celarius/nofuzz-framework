<?php
/**
 * Application class
 *
 * @package Nofuzz
 */
#################################################################################################################################

namespace Nofuzz;

class Application
{
  const NofuzzVersion = '0.6.x';

  protected $request = null;
  protected $response = null;
  protected $cacheManager = null;
  protected $logger = null;
  protected $config = null;
  protected $connectionManager = null;

  /** @var string       Application BasePath */
  protected $basePath = '';
  protected $routeGroups = null;          // RouteGroups array
  protected $beforeMiddleware = array();  // common Before Middleware
  protected $afterMiddleware = array();   // common After Middleware

  protected $code = '';
  protected $name = '';
  protected $version = '';
  protected $environment = '';

  protected $setErrorLevel = E_ALL;

  # Dependencies
  protected $container = array(); // Container for Dependencies

  /**
   * Constructor
   *
   * @param string $basePath        Application folder (where "/app" is located)
   */
  public function __construct( string $basePath )
  {
    #
    # Require the Globals
    #
    require __DIR__ . '/Globals.php';

    # Set basic properties
    $this->basePath = $basePath;
    $this->routeGroups = array();

    # Create Config
    # v0.5.7: (KS) Made loading the config based on environment variable
    $env = strtolower(env('ENVIRONMENT','dev'));
    $config_file = str_replace('${environment}', $env, $this->basePath.'/app/Config/config-${environment}.json');

    # Create the config
    $this->config = new \Nofuzz\Config\Config($config_file);

    # Set Timezone - default to UTC
    $timeZone = $this->getConfig()->get('application.global.timezone','UTC');
    date_default_timezone_set($timeZone);

    # Get Code,Name,Version and Environment
    $this->code = $this->getConfig()->get('application.code','');
    $this->name = $this->getConfig()->get('application.name','');
    $this->version = $this->getConfig()->get('application.version');
    $this->environment = strtolower( env('ENVIRONMENT') ?? $this->getConfig()->get('application.global.environment') ?? 'dev' );

    # Create Logger
    $this->createLogger( $this->code );

    # Decode HTTP Request (to a Guzzle ServerRequest)
    $this->request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();

    # HTTP Response
    $this->response = (new \Nofuzz\Http\HttpResponse())->setStatusCode(0);
    $this->response->setStatusCode(0);

    #
    # Initialzie CacheManager & Cache
    #
    $this->cacheManager = new \Nofuzz\SimpleCache\CacheManager();

    # Create a Cache Driver (if specified)
    if ( $this->getConfig()->get('cache.driver') != null ) {
      $driver = $this->getConfig()->get('cache.driver') ?? '';
      if (strlen($driver)>0) {
        $driverClass = $this->cacheManager->createCache( $driver, $this->getConfig()->get('cache.options.'.$driver) ) ;
        if (!is_null($driverClass)) {
          $this->getLogger()->debug('Failed to create Cache',['driver'=>$driver]);
        } else {
          $this->getLogger()->warning('Created Cache',['driver'=>$driver]);
        }
      }
    }

    #
    # DB Manager
    #
    $this->connectionManager = new \Nofuzz\Database\ConnectionManager();
  }

  /**
   * Creates and Initializes the Logger
   *
   * @return self
   */
  protected function createLogger(string $loggerName)
  {
    # Create the Logger Object
    $this->logger = new \Nofuzz\Log\Logger($loggerName);

    # Get the conf options
    $logLevel = $this->getConfig()->get('log.level','error');
    $logDriver = $this->getConfig()->get('log.driver','file');

    $logDateFormat = $this->getConfig()->get('log.drivers.'.$logDriver.'.line_datetime','Y-m-d H:i:s');
    $logLineFormat = $this->getConfig()->get('log.drivers.'.$logDriver.'.line_format','%datetime% > %level_name% > %message% %context% %extra%');

    if ( strcasecmp($logDriver,"file")==0 ) {
      $logFilePath = $this->getConfig()->get('log.drivers.'.$logDriver.'.file_path','storage/log');
      $logFileFormat = $this->getConfig()->get('log.drivers.'.$logDriver.'.file_format','Y-m-d');
      # Create the Log Handler
      $handler = new \Monolog\Handler\StreamHandler(
        realpath($this->basePath.'/'.$logFilePath).'/'.date($logFileFormat).'.log',
        $this->getLogger()->toMonologLevel($logLevel)
      );

    } else if ( strcasecmp($logDriver,"php")==0 ) {
      # Create the Log Handler
      $handler = new \Monolog\Handler\ErrorLogHandler (
        \Monolog\Handler\ErrorLogHandler::OPERATING_SYSTEM,
        $this->getLogger()->toMonologLevel($logLevel)
      );

    }
    # finally, create a formatter for it
    $formatter = new \Monolog\Formatter\LineFormatter($logLineFormat, $logDateFormat);
    $handler->setFormatter($formatter);

    # Push the handler to the logger
    $this->logger->pushHandler($handler);

    # Set our own error handlers
    $this->setErrorHandlers();

    return $this;
  }


  /**
   * Set the Error Handler
   *
   * @return bool
   */
  private function setErrorHandlers()
  {
    # Report all PHP errors (see changelog)
    $this->setErrorLevel = error_reporting( E_ALL | E_STRICT);

    # set to the user defined error handler
    $old_error_handler = set_error_handler(array($this,'errorHandler'), E_ALL);
    $old_exception_handler = set_exception_handler(array($this,'exceptionHandler'));

    return true;
  }


  /**
   * Error Handler
   *
   * Handles all errors from the code. This is set as the default
   * error handler.
   *
   * @param  [type] $errNo       [description]
   * @param  [type] $errStr      [description]
   * @param  [type] $errFile     [description]
   * @param  [type] $errLine     [description]
   * @param  array  $errContext  [description]
   * @return bool
   */
  public function errorHandler($errNo, $errStr, $errFile, $errLine, array $errContext)
  {
    if (!(error_reporting() & $errNo)) {
        // This error code is not included in error_reporting, so let it fall
        // through to the standard PHP error handler
        // Example all @ prefixed functions
        return false;
    }

    switch ($errNo) {
      # Emergency

      # Alert

      # Critical
      case E_STRICT:
        $this->getLogger()->critical("$errStr in file $errFile on line $errLine",$errContext);
        exit(1);
        break;

      # Error
      case E_ERROR:
      case E_USER_ERROR:
        $this->getLogger()->error("$errStr in file $errFile on line $errLine",$errContext);
          exit(1);
          break;

      # Warning
      case E_WARNING:
      case E_USER_WARNING:
        $this->getLogger()->warning("$errStr in file $errFile on line $errLine",$errContext);
        break;

      # Notice
      case E_NOTICE:
      case E_USER_NOTICE:
        $this->getLogger()->notice("$errStr in file $errFile on line $errLine",$errContext);
        break;

      # Info
      case E_RECOVERABLE_ERROR:
      case E_DEPRECATED:
      case E_USER_DEPRECATED:
        $this->getLogger()->info("$errStr in file $errFile on line $errLine",$errContext);
        break;
      default:
        $this->getLogger()->emergency("$errStr in file $errFile on line $errLine",$errContext);
        break;
    }

    # Don't execute PHP internal error handler
    return true;
  }

  /**
   * Exception Handler
   *
   * Handles any Exceptions from the application. This is set as the
   * default exception handler for all exceptions.
   *
   * @param  [type] $exception [description]
   * @return [type]            [description]
   */
  public function exceptionHandler($exception)
  {
    # Set 500 error code as well as something unexpected happened
    $this->getResponse()->setStatusCode(500);

    # Log the exception
    $this->getLogger()->critical(
      $exception->getMessage().' in file '.$exception->getFile().' on line '.$exception->getLine(),
      $exception->getTrace()
    );

    # Output a response
    $this->getResponse()->send();
  }


  /**
   * Run the application
   *
   * @param  mixed $args              [description]
   * @param  string $routesFilename   Name of routers file to load (empty by default)
   * @return bool                     False if request failed, True for success
   */
  public function run( string $routesFilename=''  )
  {
    #
    # Load the Routes
    #
    if ( empty($routesFilename) ) {
      $routesFilename = $this->getBasePath().'/app/Config/routes.json';
    }
    $this->loadRoutes( $routesFilename );


    # Get Method and URI
    $httpMethod = $this->getRequest()->getMethod();
    $path = $this->getRequest()->getUri()->getPath();
    $routeInfo = null;

    # Find route match in groups
    foreach ($this->getRouteGroups() as $routeGroup)
    {
      # Match the METHOD and URI to the routes in this group
      $routeInfo = $routeGroup->matchRoute($httpMethod,$path);

      if ( count($routeInfo)>0 ) {
        $beforeResult = true; // assume all before handlers succeed
        $routeResult = false;
        $afterResult = true; // assume all after handlers succeed

        # Debug log
        $this->getLogger()->debug('Route matched ',['handler'=>$routeInfo['handler']]);

        #
        # Run the Common AND Groups Before Middlewares (ServerRequestInterface)
        #
        $beforeMiddleware = array_merge($this->beforeMiddleware, $routeGroup->getBeforeMiddleware());

        foreach ($beforeMiddleware as $middleware)
        {
          if (class_exists($middleware) ) {
            $beforeHandler = new $middleware($routeInfo['args']);

            # Debug log
            $this->getLogger()->debug('Running Before middleware',['rid'=>app('requestId'),'middleware'=>$middleware]);

            if (!$beforeHandler->handle($routeInfo['args'])) {
              # Record outcome
              $beforeResult = false;
              # Stop processing more middleware
              break;
            }
          } else {
            # Log
            $this->getLogger()->warning('Before Middleware not found',['rid'=>app('requestId'),'middleware'=>$middleware]);
          }
        }

        #
        # Create & Run the Handler Class - If the Before Middlewares where ok!
        #
        if ($beforeResult) {
          # Extract class & method
          $arr = explode('@',$routeInfo['handler']);
          $handlerClass = $arr[0];
          $handlerMethod = ($arr[1] ?? 'handle');

          # Check existance of handler class
          if (class_exists($handlerClass))
          {
            # Create the class
            $routeHandler = new $handlerClass( $routeInfo['args'] );

            # Check method existance
            if ($routeHandler && method_exists($routeHandler,'initialize') && method_exists($routeHandler,$handlerMethod))
            {
              # Debug log
              $this->getLogger()->debug('Running controller->initialize()',['rid'=>app('requestId'),'controller'=>$handlerClass]);

              # Initialize
              $routeHandler->initialize($routeInfo['args']);

              # Debug log
              $this->getLogger()->debug('Running controller->handle()',['rid'=>app('requestId'),'method'=>$handlerMethod]);

              # Run handler
              $routeResult = $routeHandler->$handlerMethod($routeInfo['args']);
            } else {
              # Log
              $this->getLogger()->error('Method not found in controller ',['rid'=>app('requestId'),'controller'=>$handlerClass,'method'=>$handlerMethod]);
            }
          } else {
            # Debug log
            $this->getLogger()->debug('Controller not found ',['rid'=>app('requestId'),'controller'=>$handlerClass]);
          }
        }

        #
        # Run the After Middlewares (ServerRequestInterface)
        #
        $afterMiddleware = array_merge($this->afterMiddleware,$routeGroup->getAfterMiddleware());
        foreach ($afterMiddleware as $middleware)
        {
          if (class_exists($middleware) ) {
            $afterHandler = new $middleware($routeInfo['args']);

            # Debug log
            $this->getLogger()->debug('Running After middleware',['rid'=>app('requestId'),'middleware'=>$middleware]);

            if (!$afterHandler->handle($routeInfo['args'])) {
              return false;
            }
          } else {
            # Log
            $this->getLogger()->warning('After Middleware not found',['rid'=>app('requestId'),'middleware'=>$middleware]);
          }
        }

        return $routeResult;

      } else {
        # No route matched the URI in this group, we'll look in the next group or exit the foreach()
        $routeInfo = null;
      }

    }

    # Output debug info
    if (is_null($routeInfo)) {
      # Debug log
      $this->getLogger()->debug('No route matched request',['rid'=>app('requestId'),'method'=>$httpMethod,'path'=>$path]);
    }

    return false; // default response
  }


  /**
   * Load the "routes.json" and create all RouteGroups
   *
   * @param  string $filename   [description]
   * @return bool
   */
  protected function loadRoutes(string $filename)
  {
    $filename = realpath($filename);

    if ( file_exists($filename) ) {
      $routesFile = json_decode( file_get_contents($filename), true );
      if ($routesFile) {
        $routeGroups = $routesFile['groups'] ?? [];
        # Add each RouteGroup
        foreach ($routeGroups as $routeGroupDef)
        {
          # Create new Route Group
          $routeGroup = new \Nofuzz\Route\Group($routeGroupDef);

          # Add to list
          $this->routeGroups[] = $routeGroup;
        }

        # Common Middlewares
        $this->beforeMiddleware = ($routesFile['common']['before'] ?? []);
        $this->afterMiddleware = ($routesFile['common']['after'] ?? []);
      } else {
        throw new \RunTimeException('Invalid JSON file "'.$filename.'"');

      }

      # Debug log
      $this->getLogger()->debug('Loaded routes',['file'=>$filename]);

      return true; // routes added
    }

    return false; // file not found
  }

  /**
   * Get a RouteGroup by Name
   *
   * @param  string $groupName [description]
   * @return null | object
   */
  public function getRouteGroup(string $groupName): \Nofuzz\Route\Group
  {
    foreach ($this->routeGroups as $routeGroup)
    {
      if ( strcasecmp($routeGroup->getName(),$groupName)==0 ) {
        return $routeGroup;
      }
    }

    return null;
  }

  /**
   * Get all RouteGroups
   *
   * @return null | array
   */
  public function getRouteGroups()
  {
    return $this->routeGroups;
  }

  /**
   * Checks if a RouteGroup exists
   *
   * @param  string $groupName [description]
   * @return bool
   */
  public function existsRouteGroup(string $groupName): bool
  {
    foreach ($this->routeGroups as $routeGroup)
    {
      if ( strcasecmp($routeGroup->getName(),$groupName)==0 ) {
        return true;
      }
    }

    return false;
  }

  /**
   * Returns a property if it exists
   *
   * @param  string $property     The property name, or container name to return
   * @return mixed|null           Null if nothing was found
   */
  public function getProperty(string $property)
  {
    if (property_exists(__CLASS__, $property)) {
      return $this->$property;
    }

    return $this->container($property) ?? null;
  }

  /**
   * Get the Application Code
   *
   * @return string
   */
  public function getCode(): string
  {
    return $this->code;
  }

  /**
   * Get the Application Name
   *
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * Get the Application Version
   *
   * @return string
   */
  public function getVersion(): string
  {
    return $this->version;
  }


  /**
   * Get the Request (ServerRequest)
   *
   * @return object
   */
  public function getRequest()
  {
    return $this->request;
  }

  /**
   * Get the Response (ServerResponse)
   *
   * @return object
   */
  public function getResponse()
  {
    return $this->response;
  }

  /**
   * Get the Logger
   *
   * @return object
   */
  public function getLogger()
  {
    return $this->logger;
  }

  /**
   * Get the Config
   *
   * @return object
   */
  public function getConfig()
  {
    return $this->config;
  }

  /**
   * Get the DB Manager
   *
   * @return object
   */
  public function getConnectionManager()
  {
    return $this->connectionManager;
  }

  /**
   * Get the Cache
   *
   * @return object
   */
  public function getCache(string $driverName='')
  {
    return $this->cacheManager->getCache($driverName);
  }

  /**
   * Get the basePath
   *
   * @return object
   */
  public function getBasePath()
  {
    return $this->basePath;
  }

  /**
   * Get the Environment
   *
   * @return object
   */
  public function getEnvironment()
  {
    return $this->environment;
  }

  /**
   * Get or Set a Container value.
   *
   * @param  string     $name       Dependency name
   * @param  mixed|null $value      Value to SET. if Omitted, then $name is returned (if found)
   * @return mixed|null
   */
  public function container(string $name, $value=null)
  {
    # Getting or Setting the value?
    if (is_null($value)) {
      # Return what $name has stored in $container array
      $value = $this->container[$name] ?? null;

    } else {
      # Setting the container value $name to $value
      $this->container[$name] = $value;

    }

    return $value;
  }

}
