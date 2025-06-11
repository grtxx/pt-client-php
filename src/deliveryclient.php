<?php

namespace grt\pt\service;

    use GuzzleHttp\Psr7;

    class deliveryClient extends \grt\ptclient\ptclient {

        public function createCampaign( $campaign ) {
            $res = $this->ptapi->signedRequest( [
                'method' => 'PUT', 
                'url' => 'delivery/campaign/create', 
                'body' => json_encode( [ 'campaign' => $campaign ] ),
                'signed' => true
            ] );
            if ( $res['status'] == 200 ) {
                return $res['data']['campaign'];
            }
        }

        public function getCampaignByName( $name ) {
            $res = $this->ptapi->signedRequest( [
                'method' => 'GET', 
                'url' => 'delivery/campaign/byname',
                'signed' => true
            ] );
            if ( $res['status'] == 200 ) {
                return $res['data']['campaign'];
            }
            return null;
        }


        public function getSpot( $spot_id ) {
            $res = $this->ptapi->signedRequest( [
                'method' => 'GET', 
                'url' => 'delivery/spot/'.$spot_id,
                'signed' => true 
            ] );
            if ( $res['status'] == 200 ) {
                return $res['data']['spot'];
            }
            return null;
        }

        public function createSpot( $spot ) {
            $res = $this->ptapi->signedRequest( [
                'method' => 'PUT', 
                'url' => 'delivery/spot/create', 
                'body' => json_encode( [ 'spot' => $spot ] ),
                'signed' => true
            ] );
            if ( $res['status'] == 200 ) {
                return $res['data']['spot'];
            }
            else {
                return $res['status'];
            }
        }

        public function getChannelsForSpot( $id ) {
            $res = $this->ptapi->signedRequest( [
                'method' => 'GET', 
                'url' => 'delivery/spot/'.$id.'/channels',
                'signed' => true
            ] );
            if ( $res['status'] == 200 ) {
                return $res['data']['channels'];
            }
            else {
                return $res['status'];
            }
        }

        public function setChannelsForSpot( $id, $channel_names ) {
            $res = $this->ptapi->signedRequest( [
                'method' => 'PUT', 
                'url' => 'delivery/spot/'.$id.'/channels', 
                'body' => json_encode( [ 'channels' => $channel_names ] ),
                'signed' => true 
            ] );
            if ( $res['status'] == 200 ) {
                return $res['data']['channels'];
            }
            else {
                return false;
            }
        }

        public function setStatusForSpot( $id, $status ) {
            $res = $this->ptapi->signedRequest( [
                'method' => 'PUT',
                'url' => 'delivery/spot/'.$id.'/status', 
                'body' => json_encode( [ 'status' => $status ] ),
                'signed' => true
            ] );
            if ( $res['status'] == 200 ) {
                return $res['data']['spot'];
            }
            else {
                return false;
            }
        }

        public function findMediaForSpot( $id ) {
            $res = $this->ptapi->signedRequest( [
                'method' => 'POST', 
                'url' => 'delivery/spot/'.$id.'/findmedia',
                'body' => '',
                'signed' => true,
            ] );
            if ( $res['status'] == 200 ) {
                return true;
            }
            else {
                return false;
            }
        }

        public function addMediaForSpot( $id, $filename ) {
            $data = [
                [
                    'name' => 'mediafile',
                    'contents' => Psr7\Utils::tryFopen( $filename, "r" ),
                    'filename' => basename( $filename ),
                    'headers' => [
                        'Content-type' => mime_content_type( $filename )
                    ]
                ]
            ];
            $res = $this->ptapi->signedRequest( [
                'method' => 'POST',
                'url' => 'delivery/spot/'.$id.'/media',
                'multipart' => $data,
                'signed' => true
            ] );
            if ( $res['status'] == 200 || $res['status'] == 100 ) {
                return $res['data']['spot'];
            }
            else {
                return false;
            }
        }
    }
