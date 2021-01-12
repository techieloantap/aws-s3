<?php
namespace aw2\aws_s3;

/**
	Desc : This will return the file content
	Input: 
		path   : <string>
		config : <array>
**/

\aw2_library::add_service('aws_s3.get_file_contents','File Get Contents',['namespace'=>__NAMESPACE__]);
function get_file_contents($atts,$content=null,$shortcode){
	
	if(\aw2_library::pre_actions('all',$atts,$content,$shortcode)==false)return;
	extract( shortcode_atts( array(
		'config'=>'',
		'path'=>''
		), $atts) );	
		
	    try{
			$client=connectS3($config);
			$client->registerStreamWrapper();
			
			if (file_exists($path)) {		

				$content = file_get_contents($path);
				
				return array("status"=>"success","message"=> "File exists.",'data'=>$content);
			}
			else{
				return array("status"=>"error","message"=> "File not exists.");
			}
		}catch(S3Exception $e) {
			return array("status"=>"error","message"=> $e->getMessage());

		}
}

/**
	Desc : This will write data into the file
	Input: 
		path    : <string>
		config  : <array>
		content : <string>
**/

\aw2_library::add_service('aws_s3.put_file_contents','File Put Contents',['namespace'=>__NAMESPACE__]);

function put_file_contents($atts,$content=null,$shortcode){
	
	if(\aw2_library::pre_actions('all',$atts,$content,$shortcode)==false)return;
	extract( shortcode_atts( array(
		'config'=>'',
		'path'=>'',
		'contents'=>''
		), $atts) );
		
		try
		{	 

			$client=connectS3($config);
			$client->registerStreamWrapper();
			
			if($stream=fopen($path, 'w')){
			fwrite($stream, $contents);
			fclose($stream); 	
			
			return array("status"=>"success","message"=> "file uploaded successfully.");
			}else{
					return array("status"=>"error","message"=> "file not uploaded successfully.");
			}
			
		}catch(S3Exception $e) {
			return array("status"=>"error","message"=> $e->getMessage());

		}

}

/**
	Desc : This will delete the file
	Input: 
		bucket_name : <string>
		config      : <array>
		path      : <string>
		file_name   : 
**/

\aw2_library::add_service('aws_s3.delete_file','Delete File',['namespace'=>__NAMESPACE__]);

function delete_file($atts,$content=null,$shortcode){
	
	if(\aw2_library::pre_actions('all',$atts,$content,$shortcode)==false)return;
	extract( shortcode_atts( array(
		'config'=>'',
		'bucket_name'=>'',
		'path'=>'',
		'file_name'=>'',
		), $atts) );
		
		$client=connectS3($config);
	
	try
	{			
		//check file exists
		$info = $client->doesObjectExist($bucket_name,$path);
		if($info){	
		
		$result = $client->deleteObject(['Bucket' => $bucket_name,
		'Key'    => $path]);

		return array("status"=>"success","message"=> "deleted successfully.");
			
		
		}else{
			return array("status"=>"error","message"=> "file does not exists");
		}
	}
	catch (S3Exception $e) {
		return array("status"=>"error","message"=> $e->getMessage());
		
	}			

}

/**
	Desc : This will delete all file
	Input: 
		bucket_name : <string>
		config      : <array>
		path      : <string>
**/

\aw2_library::add_service('aws_s3.delete_all_files','Delete File',['namespace'=>__NAMESPACE__]);

function delete_all_files($atts,$content=null,$shortcode){
	
	if(\aw2_library::pre_actions('all',$atts,$content,$shortcode)==false)return;
	extract( shortcode_atts( array(
		'config'=>'',
		'bucket_name'=>'',
		'path'=>''		
		), $atts) );
		
		$client=connectS3($config);
		
	try 
	{
		$client->registerStreamWrapper();
		$ctr=0;
		if(!is_dir("s3://{$bucket_name}/$path")){
			return array("status"=>"error","message"=> "Directory not found");
		}		
		
		// Get the object.
			$keys = $client->listObjects([
				'Bucket' => $bucket_name,
				'Prefix' => $path
			]); 
			foreach ($keys['Contents'] as $key)
			{
				$ctr++;
				$client->deleteObjects([
					'Bucket'  => $bucket_name,
					'Delete' => [
						'Objects' => [
							[

								'Key' => $key['Key']
							]
						]
					]
				]);
			}
			
		
		
		if($ctr===0){
			return array("status"=>"error","message"=> "Nothing to delete");
		}
		return array("status"=>"success","message"=> "files deleted successfully.");
		
	}
	catch (S3Exception $e) {
		return array("status"=>"error","message"=> $e->getMessage());
		
	}			

}

/**
	Desc : This will read the file to display on browser
	Input: 
		bucket_name : <string>
		config      : <array>
		source      : <string>
		file_name   : 
**/

\aw2_library::add_service('aws_s3.read_file','Read File',['namespace'=>__NAMESPACE__]);
function read_file($atts,$content=null,$shortcode){
	
	if(\aw2_library::pre_actions('all',$atts,$content,$shortcode)==false)return;
	extract( shortcode_atts( array(
		'config'=>'',
		'bucket_name'=>'',
		'path'=>'',
		), $atts) );
	
		$client=connectS3($config);
		
		try {
		
		$info = $client->doesObjectExist($bucket_name,$path);
		if(!$info){
			return array("status"=>"error","message"=> "File not found");
		}
		
		$file_info = pathinfo($path);
			
		// Get the object.
		$result = $client->getObject([
			'Bucket' => $bucket_name,
			'Key'    => $file_info['dirname']."/".$file_info['basename'],
			'Prefix'=>$file_info['dirname']
		]);


		header("Content-Type: {$result['ContentType']}");

		echo  $result['Body'];


		} catch (S3Exception $e) {
		return array("status"=>"error","message"=> $e->getMessage());
		}
}

/**
	Desc : This will download the file
	Input: 
		bucket_name : <string>
		config      : <array>
		path 		: <string>
**/

\aw2_library::add_service('aws_s3.download_file','Download File',['namespace'=>__NAMESPACE__]);
function download_file($atts,$content=null,$shortcode){
	
	
	if(\aw2_library::pre_actions('all',$atts,$content,$shortcode)==false)return;
	extract( shortcode_atts( array(
		'config'=>'',
		'bucket_name'=>'',
		'path'=>''
		
		), $atts) );
		
				
		 $file_info = pathinfo($path);
		$client=connectS3($config);

		try {
			$info = $client->doesObjectExist($bucket_name,$path);
			if(!$info){
				return array("status"=>"error","message"=> "File not found");
			}
		// Get the object.
		$result = $client->getObject([
			'Bucket' => $bucket_name,
			'Key'    => $file_info['dirname']."/".$file_info['basename'],
			'Prefix'=>$file_info['dirname']
		]);
	
		
		//turn off output buffering to decrease cpu usage
	    //@ob_end_clean(); 
		header("Content-Description: File Transfer"); 
		header("Content-Type: application/octet-stream"); 
		header('Content-Length: ' .$result['ContentLength']);
		header("Content-Transfer-Encoding: binary");
	    header('Accept-Ranges: bytes');
		header("Content-Disposition: attachment; filename=".$file_info['basename']);
		
		echo $result['Body'];
		die;
		
		
		
	} catch (S3Exception $e) {
		return array("status"=>"error","message"=> $e->getMessage());
	}
}

/**
	Desc : This will Download All Files
	Input: 
		bucket_name : <string>
		config      : <array>
		path 		: <string>
**/

\aw2_library::add_service('aws_s3.download_all','Download All Files',['namespace'=>__NAMESPACE__]);
function download_all($atts,$content=null,$shortcode){
	
	$zip = new \ZipArchive();
	$time=time();
	
	
	if(\aw2_library::pre_actions('all',$atts,$content,$shortcode)==false)return;
	extract( shortcode_atts( array(
		'config'=>'',
		'bucket_name'=>'',
		'path'=>'',
		'download_path'=>''
		
		), $atts) );
			
	$zip->open($download_path, \ZipArchive::CREATE);
		
	$client=connectS3($config);
		
	try {
			$client->registerStreamWrapper();
			
			// Get the object.
			$objects = $client->getIterator('ListObjects', array(
			'Bucket' => $bucket_name,
			'Prefix' => $path
			));
		
		if(!is_dir("s3://{$bucket_name}/$path")){
			return array("status"=>"error","message"=> "Directory not found");
		}		
		
		foreach ($objects as $object) {
			
		$contents = file_get_contents("s3://{$bucket_name}/{$object['Key']}"); // get file
		$zip->addFromString($object['Key'], $contents); // add file contents in zip
		}
		
		$zip->close();
		
		// Download de zip file
		header("Content-Description: File Transfer"); 
		header("Content-Type: application/octet-stream"); 
		header("Content-Disposition: attachment; filename=".basename($download_path));
		header('Content-Length: ' . filesize($download_path));
		ob_clean();
		readfile ($download_path); 
		die;
		
		
	} catch (S3Exception $e) {
		return array("status"=>"error","message"=> $e->getMessage());
	}
}

/**
	Desc : This get list of files
	Input: 
		bucket_name : <string>
		config      : <array>
		source      : <string>
**/

\aw2_library::add_service('aws_s3.get_files','Get All Files from bucket',['namespace'=>__NAMESPACE__]);
function get_files($atts,$content=null,$shortcode){
	
	if(\aw2_library::pre_actions('all',$atts,$content,$shortcode)==false)return;
	extract( shortcode_atts( array(
		'config'=>'',
		'bucket_name'=>'',
		'path'=>'',
		'search'=>''
		), $atts) );
	
	$client=connectS3($config);
	$files=array();
	
	try {
		
		$client->registerStreamWrapper();
		if(!is_dir("s3://{$bucket_name}/$path")){
			return array("status"=>"error","message"=> "Directory not found");
		}		
		
		$objects = $client->listObjects([
		'Bucket' => $bucket_name,
		'Key'=>$path."/",
		'Delimiter'=>'/',
		'Prefix'=>$path."/"
	]);
	
		
	foreach ($objects['Contents']  as $object) {
		$file_name=substr($object['Key'], strrpos($object['Key'], '/') + 1);
		if(!empty($search)){
			preg_match("/$search/", $file_name, $matches);			
			if(isset($matches[0]) && ($matches[0]!='') ){				
				$files[]= array('basename'=>$file_name,"filesize"=>$object['Size']);
			}
		}else{
			$files[]= array('basename'=>$file_name,"filesize"=>$object['Size']);
		}
		 
	} 
	
	return array("status"=>"success","message"=> 'found','files'=>$files);
	
	} catch (S3Exception $e) {		
		return array("status"=>"error","message"=> $e->getMessage());
	}
}

/**
	Desc : This will check object is exists
	Input: 
		bucket_name : <string>
		config      : <array>
		key         : <string>
**/

\aw2_library::add_service('aws_s3.does_object_exists','check object is exists or not',['namespace'=>__NAMESPACE__]);
function does_object_exists($atts,$content=null,$shortcode){
	
	if(\aw2_library::pre_actions('all',$atts,$content,$shortcode)==false)return;
	extract( shortcode_atts( array(
		'config'=>'',
		'bucket_name'=>'',
		'key'=>''
		), $atts) );
	
	
	$client=connectS3($config);
	
	try{
		
		$info = $client->doesObjectExist($bucket_name, $key);
		if($info){	
					
				$headers = $client->headObject(['Bucket' => $bucket_name, 'Key' => $key] );
				$headers=$headers->toArray();
				$file_info['last_modified_date']=json_decode(json_encode($headers['LastModified']), true);
				$file_info['content_length']=$headers['ContentLength'];
			
			//getObject is also available to file details.
			
			return array("status"=>"success","message"=> 'found','file_info'=>$file_info);
			
		}else{
			return array("status"=>"error","message"=> 'Not found');
		}		
	}
	catch (S3Exception $e) {
		return array("status"=>"error","message"=> $e->getMessage());
	} 
}

/**
	Desc : upload file on s3 bucket
	Input: 
		bucket_name         : <string>
		config              : <array>
		destination         : <string>
		source			    : <string>
**/

\aw2_library::add_service('aws_s3.move_file_to_bucket','upload file on s3 bucket',['namespace'=>__NAMESPACE__]);
function move_file_to_bucket($atts,$content=null,$shortcode=null){
	
	if(\aw2_library::pre_actions('all',$atts,$content,$shortcode)==false)return;
	extract( shortcode_atts( array(
		'config'=>'',
		'bucket_name'=>'',
		'source'=>'',
		'destination'=>''		
		), $atts) );
	
	$client=connectS3($config);
	
	//check is file exists
	if(file_exists($source) && is_file($source) ){		
		return move_file($client,$bucket_name,$source,$destination);
	}else{
		return array("status"=>"error","message"=> 'File not found');
	}
	
	
}

\aw2_library::add_service('aws_s3.move_directory_to_bucket','upload directory on s3 bucket',['namespace'=>__NAMESPACE__]);
function move_directory_to_bucket($atts,$content=null,$shortcode=null){
	
	if(\aw2_library::pre_actions('all',$atts,$content,$shortcode)==false)return;
	extract( shortcode_atts( array(
		'config'=>'',
		'bucket_name'=>'',
		'source'=>'',
		'destination'=>''		
		), $atts) );
	
	$client=connectS3($config);	
	
	if(!is_dir($source)){
		return array("status"=>"error","message"=> 'No directory found');
	}
	
	$files = scandir($source, 1);
	$ctr=0;
	
	foreach($files as $key){
		if (!in_array($key,array(".",".."))){	
			$ctr++;	
			$file_name=$source."/".$key;
			
			$msg[]=move_file($client,$bucket_name,$file_name,$destination);
			
		}
	}
	if($ctr==0){
			return array("status"=>"error","message"=> 'No documents found');
	}else{
		return array("status"=>"success","message"=> 'Files moved successfully');
	}
	
}

function move_file($client,$bucket_name,$file_name,$destination){
	
	
	
	try{
		$client->putObject(array(
			'Bucket'     => $bucket_name,
			'Key'        => $destination."/".basename($file_name),//$folder_name.'/'.$key, 
			'Body'   => fopen($file_name, 'r')
		));
		
		return array("status"=>"success","message"=> 'File moved successfully');
		
	}catch(S3Exception $e) {
		return array("status"=>"error","message"=> $e->getMessage());
	}
}


\aw2_library::add_service('aws_s3.download_public_file','download public files from s3 bucket',['namespace'=>__NAMESPACE__]);
function download_public_file($atts,$content=null,$shortcode=null){
	
	if(\aw2_library::pre_actions('all',$atts,$content,$shortcode)==false)return;
		extract( shortcode_atts( array(
		'config'=>'',
		'bucket_name'=>'',
		'destination'=>'',
		'source'=>''
		), $atts) );
		$file_info = pathinfo($source);
		
		
	    try{
			$client=connectS3($config);
			$client->registerStreamWrapper();
			
			$info = $client->doesObjectExist($bucket_name, $source);
			echo $destination;
		if($info){				
					
				$cmd = $client->getCommand('GetObject', [
				'Bucket' => $bucket_name,
				'Key' => $source	
				]);
				
				$request = $client->createPresignedRequest($cmd, '+7 days');			
				
				$presignedUrl = (string)$request->getUri();			
			
				
				if($stream=fopen($destination, 'w')){
				fwrite($stream, file_get_contents($presignedUrl));
				fclose($stream); 	
			
					if(file_exists($destination)){
						return array("status"=>"success","message"=> 'file created successfully');
					}
				}else{
					return array("status"=>"error","message"=> 'file not created.');
				}			
			}
			
			else{
			return array("status"=>"error","message"=> 'File Not found');
			}
			 
			
		}catch(S3Exception $e) {
			return array("status"=>"error","message"=> $e->getMessage());

		}
	
}
