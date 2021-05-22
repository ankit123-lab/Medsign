<?php

require 'awssdk/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

function uploadfilesS3($dir, $file_name) {
    try {
        $s3 = S3Client::factory(
                        array(
                            'signature' => 'v4',
                            "region" => "ap-south-1",
                            "version" => "2006-03-01",
                            "credentials" => array(
                                'key' => BUCKET_ACCESS_KEY,
                                'secret' => BUCKET_ACCESS_SECRET
                            )
							//,'debug'=>true
        ));
		
		$ContentType = mime_content_type($dir);
		$result = $s3->putObject(array(
            'Bucket' => BUCKET_NAME_DOWNLOAD,
            'Key' => $file_name,
            'ContentType' => $ContentType,
            'SourceFile' => $dir,
            'ACL' => 'public-read',
        ));
		
		return true;
    } catch (S3Exception $e) {
		return FALSE;
    }
}

function registerStreamWrap($path) { 
    $client = S3Client::factory(array(
        'signature' => 'v4',
        "region" => "ap-south-1",
        "version" => "2006-03-01",
        'credentials' => array(
            'key'    => $GLOBALS['ENV_VARS']['LIVE_BUCKET_ACCESS_KEY'],
            'secret' => $GLOBALS['ENV_VARS']['LIVE_BUCKET_ACCESS_SECRET'],
        )
    )); 
    $client->registerStreamWrapper();
    $context = stream_context_create(array(
            's3' => array(
                'seekable' => true
            )
        )); 
    return $stream = fopen($path, 'r', false, $context);
    // return $stream = file_get_contents($path);        
}

function uploadimage($source, $destination) {
    try {
        $s3 = S3Client::factory(
                        array(
                            'signature' => 'v4',
                            "region" => "ap-south-1",
                            "version" => "2006-03-01",
                            "credentials" => array(
                                'key' => BUCKET_ACCESS_KEY,
                                'secret' => BUCKET_ACCESS_SECRET
                            )
        ));
        $s3->putObject(
                array(
                    'Bucket' => BUCKET_NAME_DOWNLOAD,
                    'Key' => $destination,
                    'SourceFile' => $source,
                    'ACL' => 'public-read'
                )
        );

        return true;
    } catch (S3Exception $e) {
        echo 'erro';
        return FALSE;
    }
}

function deleteSingleFileS3($filepath) {
    try {
        $s3 = S3Client::factory(
                        array(
                            'signature' => 'v4',
                            "region" => "ap-south-1",
                            "version" => "2006-03-01",
                            "credentials" => array(
                                'key' => BUCKET_ACCESS_KEY,
                                'secret' => BUCKET_ACCESS_SECRET
                            )
        ));

        $result = $s3->deleteObject(array(
            'Bucket' => BUCKET_NAME_DOWNLOAD,
            'Key' => $filepath,
        ));

        return $result;
    } catch (S3Exception $e) {
        return FALSE;
    }
}

function checkResource($resourceName, $bucket_name = '') {
    try {
        $s3 = S3Client::factory(
                        array(
                            'signature' => 'v4',
                            "region" => "ap-south-1",
                            "version" => "2006-03-01",
                            "credentials" => array(
                                'key' => BUCKET_ACCESS_KEY,
                                'secret' => BUCKET_ACCESS_SECRET
                            )
        ));
        if(!empty($bucket_name))
            $bucket = $bucket_name;
        else
            $bucket = BUCKET_NAME_DOWNLOAD;
        $response = $s3->doesObjectExist($bucket, $resourceName);

        return $response;
    } catch (S3Exception $e) {
        return FALSE;
    }
}

/**
 * Not working now
 * @param type $source
 * @param type $destination
 * @return boolean
 */
function copyObject($source, $destination) {
    try {
        $s3 = S3Client::factory(
                        array(
                            'signature' => 'v4',
                            "region" => "ap-south-1",
                            "version" => "2006-03-01",
                            "credentials" => array(
                                'key' => BUCKET_ACCESS_KEY,
                                'secret' => BUCKET_ACCESS_SECRET
                            )
        ));
        $s3->copyObject([
            'BUCKET' => BUCKET_NAME_DOWNLOAD,
            'KEY' => BUCKET_ACCESS_KEY,
            'CopySource'
        ]);
        $s3->copy(BUCKET_NAME_DOWNLOAD . '/' . $source, BUCKET_ACCESS_KEY, BUCKET_NAME_DOWNLOAD . '/' . $destination, BUCKET_ACCESS_KEY, 'public');
        return true;
    } catch (S3Exception $e) {
        return false;
    }
}

function transfer_file($source, $dest_path) {
    try {

        $s3 = S3Client::factory(
                        array(
                            'signature' => 'v4',
                            "region" => "ap-south-1",
                            "version" => "2006-03-01",
                            "credentials" => array(
                                'key' => BUCKET_ACCESS_KEY,
                                'secret' => BUCKET_ACCESS_SECRET
                            )
        ));

        $dest = 's3://' . BUCKET_NAME_DOWNLOAD . '/' . $dest_path;

        $manager = new \Aws\S3\Transfer($s3, $source, $dest, [
            'before' => function (\Aws\CommandInterface $command) {
                if (in_array($command->getName(), ['PutObject', 'CreateMultipartUpload'])) {
                    $command['ACL'] = 'public-read';
                }
            },
                ]);
                $manager->transfer();
                return true;
            } catch (S3Exception $ex) {
                return false;
            }
}