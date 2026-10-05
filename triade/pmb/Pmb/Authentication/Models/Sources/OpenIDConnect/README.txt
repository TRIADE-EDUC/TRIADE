-----------
ATTENTION :
-----------

La classe \Jumbojett\OpenIDConnectClient est modifiee pour des soucis de sessions cote PMB

...
   /**
     * Use session to manage a nonce
     */
    protected function startSession() {
        
        if ( (PHP_SESSION_DISABLED !== session_status()) && (PHP_SESSION_ACTIVE !== session_status()) ) {
            session_start();
        }        

    }
...

