<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
/*
Update:2020-08-24: V 1.0.1 Multithread
 */

//error_reporting( 1 );
$version = '1.0.1';

use PHPMailer\PHPMailer\PHPMailer;

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

require 'db_conf.php';

date_default_timezone_set( 'Asia/Dhaka' );
$number_of_threads=4;

$g = new stdClass();
$g->number_of_threads = $number_of_threads;

if ( $g->number_of_threads < 1 || $g->number_of_threads > 9 ) {
    $g->number_of_threads = 1;
}

$g->num_emails_per_attempt = 25;

// set current time for compare
$g->duration_time = time() + 3600;
$g->hourly_limit = false;
$g->mail_limit = 0;
$g->domain_path = "/usr/local/apache2/htdocs/eblaster/";
$g->campaign_attachments_dir = "/usr/local/apache2/htdocs/eblaster/public/uploads/";
$genLogFlag = true;

for ( $i = 1; $i <= $g->number_of_threads; $i++ ) {

    $pid = pcntl_fork();

    if ( $pid == 0 ) {
        api_child_process( $i );
        exit;
    }

}

while ( 1 ) {
    sleep( 60 );
}

function api_child_process( $thread_num ) {
    global $g;
    $t = time();
    db_conn();
    $g->db_keep_alive_time = $t;

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Timeout = 60; // set the timeout (seconds)
    $mail->SMTPKeepAlive = true;

    //Enable SMTP debugging

    // 0 = off (for production use)

    // 1 = client messages
    // 2 = client and server messagesg duplicate emails by another thread
    $mail->SMTPDebug = 0;

    //$mail->Host = 'smtp.robi.com.bd';
    //$mail->Port = 25;
    $mail->Host = '123.231.118.153';
    $mail->Port = 1003;

    $mail->SMTPAuth = true; //   Fro google its true.
    $mail->CharSet = 'UTF-8';
    $mail->Username = 'gplexemail3';
    $mail->Password = 'gPlex!reL@yMai1';
    $mail->XMailer = 'gPlex Mailer 1.0.01 (www.gplex.com)';

    while ( 1 ) {
        schedule_process();
        $sleep = 5;
        $query = "SELECT id, from_email_name, from_email, from_email_subject, email_template_id, email_limit FROM campaign_profile WHERE status='P' ORDER BY RAND() LIMIT 1";
		
        $campaigns = db_select_array_utf( $query, 1 );

        $logTxt = "Start log: ".date('Y-m-d H:i:s.u')."\n";
        genLog($logTxt);

        if ( $campaigns ) {

            foreach ( $campaigns as $campaign ) {
				//dd($campaign);
                if ( $campaign ) {

                    if( !empty($campaign->email_limit) ) {
                        $g->hourly_limit = true;
                        $g->mail_limit = $campaign->email_limit;
                    }

                    $query = "SELECT * FROM campaign_attachments WHERE campaign_id='$campaign->id'";
                    $campaign_attachments = db_select_array( $query, 1 );

                    $mail_body = '';
                    $sql = "select content from template_blocks where template_id = '$campaign->email_template_id' ORDER BY id";
                    $mail_contents = db_select_array( $sql, 1 );

                    if ( is_array( $mail_contents ) ) {

                        foreach ( $mail_contents as $mcontent ) {
                            $mail_body .= $mcontent->content;
                            //$mail_body .= utf8_decode($mcontent->content);
                        }

                    }

                    if ( empty( $mail_body ) ) {
                        // Template/Content empty
                        $sql = "UPDATE campaign_profile SET status='E', updated_at=NOW() WHERE id='$campaign->id'";
                        db_update( $sql );
                        //exit;
                    } else {
                        // Status : 0 -> new, 1 -> Sent, 7 -> Invalid email address, 8 -> Error, 9 -> Selected for sent
                        $sql = "UPDATE leads SET thread_id=$thread_num, status=9 WHERE campaign_id='$campaign->id' AND status=0 AND thread_id=0 limit " . $g->num_emails_per_attempt;

                        if ( db_update( $sql ) ) {
                            $limit = $g->num_emails_per_attempt;
                            $sql = "SELECT name, email from leads where campaign_id='$campaign->id' and status=9 AND thread_id=$thread_num limit " . $limit;
                            $email_leads = db_select_array( $sql, 1 );

                            if ( is_array( $email_leads ) ) {

                                $num_sent = 0;
                                $num_error = 0;
                                $sleep = 1;

                                 //Read an HTML message body from an external file, convert referenced images to embedded,
                                //Attach an image file
                                preg_match_all( '/src="(.*)"/Uims', $mail_body, $matches );
                                //var_dump($matches);
                                $search = [];
                                $replace = [];

                                if ( !empty( $matches[1] ) ) {
                                    $n = 1;

                                    foreach ( $matches[1] as $key ) {
                                        $tmp_path = trim( $key );
                                        //var_dump($tmp_path);
                                        $name = explode( '/', $tmp_path );
                                        //var_dump(end($name));
                                        $path = explode( 'public/storage', $tmp_path );

                                       //var_dump(end($path));
                                        //var_dump($domain_path.'public/storage'.end($path));
                                        $mail->AddEmbeddedImage( $g->domain_path . 'public/storage' . end( $path ), 'campaign_img_' . $n, end( $name ) );
                                        $search[] = "src=\"$tmp_path\"";
                                        $replace[] = "src=\"cid:campaign_img_" . $n++ . "\"";
                                    }

                                }

                                $mail_body = str_replace( $search, $replace, $mail_body );

                                foreach ( $email_leads as $elead ) {

                                    if ( !filter_var( trim( $elead->email ), FILTER_VALIDATE_EMAIL ) ) {
                                        db_update( "UPDATE leads SET status=7, updated_at=NOW() WHERE campaign_id='$campaign->id' AND email='$elead->email' and status=9 AND thread_id=$thread_num LIMIT 1" );
                                        continue;
                                    }

                                    $mail->clearAddresses();
                                    //$mail->clearAttachments();
                                    $mail->setFrom( $campaign->from_email, $campaign->from_email_name );

                                    //Set an alternative reply-to address

                                    //$mail->addReplyTo('replyto@example.com', 'First Last');

                                    //Set who the message is to be sent to
                                    if ( empty( $elead->name ) ) {
                                        $mail->addAddress( $elead->email );
                                    } else {
                                        $mail->addAddress( $elead->email, $elead->name );
                                    }

                                    //Set the subject line
                                    $mail->Subject = $campaign->from_email_subject;
                                    $mail->msgHTML( $mail_body, __DIR__ );
                                    $send_status = 9;

                                    if ( is_array( $campaign_attachments ) ) {

                                        foreach ( $campaign_attachments as $att ) {

                                            if ( !empty( $att->filename ) && file_exists( $g->campaign_attachments_dir . $att->filename ) ) {
                                                $mail->AddAttachment( $g->campaign_attachments_dir . $att->filename );
                                                //echo 'Attaching file - ' . $g->campaign_attachments_dir . $att->filename;
                                            } else {
                                                //echo 'Error [Not found]: Attaching file - ' . $g->campaign_attachments_dir . $att->filename;
                                            }

                                        }

                                    }

                                    try {

                                        if ( !$mail->send() ) {
                                            $logTxt = "Campaign Id: {$campaign->id} Email: {$elead->email}, Status: Error, Time: ".date('Y-m-d H:i:s.u')."\n";
                                            genLog($logTxt); 

                                            $send_status = 8;
                                            $num_error++;
                                            db_update( "UPDATE campaign_profile SET last_err_msg='$mail->ErrorInfo', last_err_email='$elead->email', updated_at=NOW() WHERE id='$campaign->id' LIMIT 1" );
                                        } else {
                                            $logTxt = "Campaign Id: {$campaign->id} Email: {$elead->email}, Status: Sent, Time: ".date('Y-m-d H:i:s.u')."\n";
                                            genLog($logTxt);

                                            $send_status = 1;
                                            $num_sent++;
                                        }

                                    } catch ( phpmailerAppException $e ) {
                                        $logTxt = "Campaign Id: {$campaign->id} Email: {$elead->email}, Status: Failed, Time: ".date('Y-m-d H:i:s.u')."\n";
                                        genLog($logTxt);

                                        $send_status = 8;
                                        $num_error++;
                                        db_update( "UPDATE campaign_profile SET last_err_msg='" . $e->errorMessage() . "', last_err_email='$elead->email', updated_at=NOW() WHERE id='$campaign->id' LIMIT 1" );
                                    }

                                    db_update( "UPDATE leads SET status=$send_status, updated_at=NOW() WHERE campaign_id='$campaign->id' AND email='$elead->email' and status=9 AND thread_id=$thread_num LIMIT 1" );


                                    // --- here will be the break statement from mail limit
									$hour = date("H");
									$start_time = date("Y-m-d")." ".$hour . ":00:00";
									$end_time = date("Y-m-d")." ".$hour.":59:59";

									$count_query = "SELECT COUNT(*) AS mail_count FROM leads WHERE STATUS=1 AND campaign_id='{$campaign->id}' AND updated_at BETWEEN '{$start_time}' AND '{$end_time}'";
									
									$count_data = db_select_array($count_query, 1);
									//dd($count_data, false);

									//dd($start_time, false);
									//dd($end_time, false);
									//dd("Limit Count ".$g->mail_limit, false);
									//dd($count_data, false);
									if($count_data[0]->mail_count >= $g->mail_limit) {
										//dd("Limit Over, Num sent ".$num_sent, false);
										
										// -- update status 9 to 0
										$sql2 = "UPDATE leads SET STATUS=0, thread_id=0, updated_at=NULL WHERE STATUS=9 AND campaign_id='$campaign->id'";
										db_update( $sql2 );
										
										$sql3 = "UPDATE campaign_profile SET STATUS='P' WHERE id='$campaign->id' LIMIT 1";
										db_update( $sql3 );
										
										break;
									}
                                    

                                }
                                // -- end loop
                                $sql = "UPDATE campaign_profile SET send=send+$num_sent, send_error=send_error+$num_error WHERE id='$campaign->id'";
                                db_update( $sql );
                            }


                        } else {

                            $sql = "UPDATE campaign_profile SET status='D', updated_at=NOW(), end_time=NOW() WHERE id='$campaign->id'";
                            db_update( $sql );

                        }

                        

                    }

                }

            }

        }

        $logTxt = "Child Thread no: ".$thread_num."\n";
        genLog($logTxt);

        $logTxt = "End log: ".date('Y-m-d H:i:s.u')."\n";
        genLog($logTxt);

      //   echo "Child # " . $thread_num . "\n";
        sleep( $sleep );
    }

}

function schedule_process() {
    global $g;
    $t = time();

   # DB Keep alive
    if ( $t - $g->db_keep_alive_time > 300 ) { # 5 min;
        mysql_keep_alive();
        $g->db_keep_alive_time = $t;
    }

}
function genLog($logTxt){
    global $genLogFlag;
    if($genLogFlag == true){
            $logFile = fopen("/usr/local/eblaster/eblaster_log.txt", "a+") or die("Unable to open log file!");
            fwrite($logFile, $logTxt); 
            fclose($logFile);
    } 
}

function dd($data, $loop=true) {
	echo "<pre>";
		print_r($data);
	echo "</pre>";
	if($loop==true) {
		exit;
	}
}
