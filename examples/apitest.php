#!/usr/bin/php
<?php

    include( __DIR__."/vendor/autoload.php" );

    $server = "localhost:8000";
    $appsecret = "<your_app_secret>";
    $accesstoken = "<your_access_token>";
    $file = __DIR__."/testfile.txt"; // path to the file you want to review

    // get an API client instance - this object will be responsible for making and signing API calls
    $pt = new \grt\pt\apiclient( $server, $appsecret, $accesstoken );
    $pt->setProto( "http" );

    // get the AIMGR service instance - this object will be responsible for managing AI tasks
    $mgr = new \grt\pt\service\aimgr( $pt );

    // create a new AI task for content review
    // the task will be created with a unique GUID, which can be used to track the task later
    $task = $mgr->createTask( "ContentReviewTask", "aicontentreview", [] );
    print( "TASK_ID:".$task->guid()."\n" );
    print_r( $task->getData() );

    if ( $task === false ) {
        print( "Error creating task\n" );
        exit( 1 );
    }

    // add a file to the task - this is the content that will be reviewed by the AI
    try {
        $task->addFile( 'media', $file );
    }
    catch ( \Exception $e ) {
        print( "Error adding file: ".$e->getMessage()."\n" );
    }
    // enqueue the task - this will start the task processing
    $task->enqueue();

    // wait for the task to finish - this is a simple loop that checks the task status every second
    // if you add a webook parameter to the task, you can also receive notifications when the task is done
    while ( $task->getStatus() != "ok" && $task->getStatus() != "error" ) {
        print( "Task is still running, waiting...\n" );
        sleep( 1 );
        $task->refresh();
    }
    
    // print the task status and result
    print_r( $task->getStatus() );
    print_r( $task->getResult() );
