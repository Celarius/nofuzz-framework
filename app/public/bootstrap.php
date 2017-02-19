<?php
/**
 * REST Application bootstrap file
 *
 * All HTTP requests land here.
 *
 * @package  [Application]
 */

##############################################################################

  # Composer related autoloads
  require_once __DIR__ . '/../vendor/autoload.php';

  # Create Global Nofuzz $app Variable
  $app = new \Nofuzz\Application( realpath(__DIR__.'/../') );

  # Load application globals (optional)
  if (file_exists(__DIR__ . '/../app/globals.php'))
    require_once __DIR__ . '/../app/globals.php';

##############################################################################

  try {
    # Run the application
    if ( !$app->run() )
    {
      # if Application did NOT produce an output, let us do it instead
      if (response()->getStatusCode()==0) {
        response()->setStatusCode(404)->setBody('');
      }
    }

  } catch (Exception $e) {
    # Handle Any/All Exceptions
    logger()->critical( $e->getMessage(), ['trace'=>$e->getTraceAsString()] );
    // error_log( $e->getMessage().PHP_EOL.'trace='.$e->getTraceAsString() );

    # Some nice messages
    $msg[] = 'All those moments will be lost in time, like tears in rain. Time to die.  - Blade Runner (1982)';
    $msg[] = 'I\'m melting! Melting! Oh, what a world! What a world!Who ever thought a little girl like you could destroy my beautiful wickedness?! Ah, I\'m going! Ahhh!  - The Wizard of Oz (1939)';
    $msg[] = 'Mother of mercy, is this the end of Rico?  - Little Caesar (1931)';
    $msg[] = 'We win, Gracie  - Armageddon (1998)';
    $msg[] = 'I know now why you cry. But it\'s something I can never do  - Terminator 2 (1991)';
    $msg[] = '...heaven, I\'m in heaven ...  - The Green Mile (1999)';

    # Create response
    response()->errorJson(500,'',$msg[mt_rand(0,count($msg)-1)]);

  } finally {
    # Send response back to client
    response()->send();

  }
