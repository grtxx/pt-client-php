<?php

namespace grt\pt {

    abstract class apiObject {
    
        protected $ptapi;
        protected $data;


        public function __construct( $ptapi, $data = null ) {
            $this->setPTAPI( $ptapi );
            $this->data = $data;
        }
        

        public function setPTAPI( $ptapi ) {
            if ( !is_object( $ptapi ) || !method_exists( $ptapi, 'signedRequest' ) ) {
                throw( new \Exception( "Invalid PTAPI object" ) );
            }
            $this->ptapi = $ptapi;
        }
        

        public function getPTAPI() {
            return $this->ptapi;
        }


        public function getData() {
            return $this->data;
        }


        public function getId() {
            if ( isset( $this->data['id'] ) ) {
                return $this->data['id'];
            }
            return null;
        }

        
        public function setData( $data ) {
            $this->data = $data;
        }

    }

}