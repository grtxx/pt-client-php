<?php

namespace grt\pt\service {

    interface iPTAPIClientService {
        public function setPTAPI( \grt\pt\apiclient $api );        
    }

    class base implements iPTAPIClientService {
        
        protected $ptapi;
        public $lastCode = 200;
            
        
        public function __construct( \grt\pt\apiclient $api ) {
            $this->setPTAPI( $api );
        }

        public function setPTAPI( \grt\pt\apiclient $api ) {
            $this->ptapi = $api;
        }
    }
    
}