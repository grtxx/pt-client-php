<?php

namespace grt\pt\service {

    use GuzzleHttp\Psr7;

    class aimgr extends \grt\pt\service\base {

        public function createTask( $name, $type, $params = [], $prio=10 ) {
            $res = $this->ptapi->signedRequest( [
                'method' => 'POST', 
                'url' => 'task/create', 
                'body' => json_encode( [ 'name' => $name, 'type' => $type, 'params' => $params, 'prio' => $prio ] ),
                'signed' => true
            ] );
            if ( $res['status'] == 200 ) {
                return new \grt\pt\service\aimgr\task( $this->ptapi, $res['data']['data']['task'] );
            }
            return $res;
        }

    }
}