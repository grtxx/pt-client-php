#!/usr/bin/php
<?php

    include( __DIR__."/vendor/autoload.php" );


    $pt = new \grt\pt\apiclient( "localhost:8000", "2396bb62-74d4-473d-8223-fb694fa91861", "d23a3280-04da-4c9e-a586-f45cadf8c68b" );
    $pt->setProto( "http" );

    $mgr = new \grt\pt\service\aimgr( $pt );

    $task = $mgr->createTask( "ContentReviewTask", "aicontentreview", [] );
    print( "TASK_ID:".$task->guid()."\n" );
    print_r( $task->getData() );
    exit();

    if ( $task === false ) {
        print( "Error creating task\n" );
        exit( 1 );
    }
    try {
    $task->addFile( 'media', __DIR__."/examplefiles/video.mp4" );
    }
    catch ( \Exception $e ) {
        print( "Error adding file: ".$e->getMessage()."\n" );
    }
    $task->enqueue();
    while ( $task->getStatus() != "ok" && $task->getStatus() != "error" ) {
        print( "Task is still running, waiting...\n" );
        sleep( 1 );
        $task->refresh();
    }
    
    print_r( $task->getStatus() );
    print_r( $task->getResult() );

    print( "END\n" );