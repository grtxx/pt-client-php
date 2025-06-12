<?php

namespace grt\pt\service\aimgr {

    class task extends \grt\pt\apiobject {

        protected $data;

        function addFile( $role, $localfilename, $aifilename="" ) {
            $data = [
                [
                    'name' => $role,
                    'contents' => \guzzlehttp\Psr7\Utils::tryFopen( localfilename, "r" ),
                    'filename' => basename( ( $aifilename != "" ? $aifilename : localfilename ) ),
                    'headers' => [
                        'Content-type' => mime_content_type( $localfilename )
                    ]
                ]
            ];
            $res = $this->ptapi->signedRequest( [
                'method' => 'POST',
                'url' => 'task/'.$this->data['guid']."/uploadfile",
                'multipart' => $data,
                'signed' => true
            ] );
            if ( $res['status'] == 200 || $res['status'] == 100 ) {
                $this->data = $res['data']['data']['task'];
                return true;
            }
            else {
                return false;
            }

        }


        function enqueue() {
            $res = $this->ptapi->signedRequest( [
                'method' => 'PATCH',
                'url' => 'task/'.$this->data['guid']."/enqueue",
                'signed' => true
            ] );
            if ( $res['status'] == 200 || $res['status'] == 100 ) {
                $this->data = $res['data']['data']['task'];
                return true;
            }
            else {
                return false;
            }
        }


        function getStatus() {
            return $this->data['status'];
        }


        function getResult() {
            if ( isset( $this->data['result'] ) ) {
                return $this->data['result'];
            }
            return null;
        }


        function refresh() {
            $res = $this->ptapi->signedRequest( [
                'method' => 'GET',
                'url' => 'task/'.$this->data['guid'],
                'signed' => true
            ] );
            if ( $res['status'] == 200 || $res['status'] == 100 ) {
                $this->data = $res['data']['data']['task'];
                return true;
            }
            else {
                return false;
            }
        }

    }

}