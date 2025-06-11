<?php
    
namespace grt\pt {

    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\RequestException;
    use GuzzleHttp\Pool;
    use GuzzleHttp\Psr7\Request;
    use GuzzleHttp\Psr7\Response;


    class apiclient {
        
        protected $servername;
        protected $appsecret;
        protected $accesstoken;
        protected $accessKey;
        protected $lastResult;
        protected $proto;
        
        public function __construct( $servername, $appsecret, $accesstoken = "" ) {
            $this->servername = $servername;
            $this->appsecret = $appsecret;
            $this->accesstoken = $accesstoken;
            $this->proto = "https";
        }
        
        public function setAccessToken( $token ) {
            $this->accesstoken = $token;
        }


        public function setProto( $proto ) {
            if ( in_array( strtolower( $proto ), [ 'http', 'https' ] ) ) {
                $this->proto = strtolower( $proto );
            }
            else {
                throw( new Exception( "Invalid protocol: ".$proto ) );
            }
        }

        
        public function getServiceClient( $className ) {
            try {
                $className = "\\grt\\ptclient\\service\\".$className;
                $sr = new $className();
                $sr->setPTAPI( $this );
                return $sr;
            }
            catch( Exception $e ) {
                throw( new Exception( "Class not exist or not compatible" ) );
            }
        }

        public function getService( $name ) {
            $rf = new ReflectionClass( "ptapiclient".$name );
            $obj = $rf->newInstanceArgs();
            $obj->setPTAPI( $this );
            return $obj;
        }

        /**
         * Ez a funkció általánosan használható guid-okat ad vissza a standard 
         * kötőjeles hexa formában. Ez a preferált út guid-ok létrehozására. 
         * A funkció az openssl_random_pseudo_bytes funkciót használja a véletlen 
         * számok előállítására így elvileg az egymás után generált guid-ok nem 
         * megjósolhatók.
         * @param type $v
         * @return type
         */
        function createGuid( $v = 4 ) {
            switch( $v ) {
                default:
                    if ( function_exists( 'com_create_guid' ) === true ) {
                        return trim(com_create_guid(), '{}');
                    }
                    $data = openssl_random_pseudo_bytes(16);
                    $data[6] = chr( ord( $data[6] ) & 0x0f | 0x40 );
                    $data[8] = chr( ord( $data[8] ) & 0x3f | 0x80 );
                    $uuid = vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split( bin2hex( $data ), 4 ) );
                    break;
            }
            return $uuid;
        }


        public function signedRequest( $params = [] ) {
            $headers = [];
            /*
            {
                'url' => 'http://example.com/api/v1/endpoint',
                'method' => 'post'
                'body' => 'this is the request body. Can be used in POST and PUT requests',
                'signed' => true,
                'withkey' => false,
            }
            */

            // setting up auth headers
            if ( ( isset( $params['withkey'] ) ? $params['withkey'] : false ) ) {
                $rq->setHeader( 'X-AccessKey', $this->accesskey );
            }
            if ( ( isset( $params['signed'] ) ? $params['signed'] : false ) ) {
                $requestGuid = $this->createGuid();
                $signature = base64_encode( sha1( $this->accesstoken."--".$requestGuid."--".$this->appsecret, true ) );
                $headers['X-Accesstoken'] = $this->accesstoken;
                $headers['X-Signature'] =  $signature;
                $headers['X-Queryguid'] = $requestGuid;
            }

            $lastError = "";
            try {
                $client = new \GuzzleHttp\Client();
                if ( ! isset( $params['method'] ) ) {
                    $params['method'] = 'GET';
                }
                $qparams = [
                    'headers' => $headers,
                ];
                if ( isset( $params['data'] ) ) {
                    $qparams['json'] = $params['data'];
                }
                if ( isset( $params['body'] ) ) {
                    $qparams['body'] = $params['body'];
                }
                if ( isset( $params['multipart'] ) ) {
                    $qparams['multipart'] = $params['multipart'];
                }
                $res = $client->request( strToUpper( trim( $params['method'] ) ), $this->proto."://".$this->servername."/api/v1/".$params['url'], $qparams );
            }
            catch( ClientErrorResponseException $e ) {
                $res = $e->getResponse();
            }
            catch( ServerErrorResponseException $e ) {
                $res = $e->getResponse();
            }
            catch( RequestException $e ) {
                $res = $e->getResponse();
            }
            catch( Exception $e ) {
                $lastError = $e->getMessage();
                $res = null;
            }

            if ( $res == null ) {
                $this->lastResult = [ 'status' => 5000, 'data' => [ 'error' => 'Client side error: '.$lastError ] ];
                return $this->lastResult;
            }
            $body = $res->getBody();

            if ( $res->getStatusCode() != 200 || json_decode( $body, true ) == "" ) {
                $this->lastResult = [ 
                    'status' => $res->getStatusCode(),
                    'data' => json_decode( $body, true ),
                    'body' => (string)$body,
                    'raw' => $res
                ];            
            }
            else {
                $this->lastResult = [
                    'status' => $res->getStatusCode(),
                    'data' => json_decode( $body, true ),
                ];
            }
            return $this->lastResult;
        }

    }

}
