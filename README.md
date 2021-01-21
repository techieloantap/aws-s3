
# aws-s3
Aws S3 shortcode which will handle all the operation of aws s3 like fetch get etc 

# You require awesome-enterprise to use this https://github.com/WPoets/awesome-enterprise 
``` composer require loantap/aws-s3 ```

## Fetch all files from s3 bucket folder
	
   ###### Input:
	   config : <array>
	   path   :  <string>
	   search : <string>
	   bucket_name : <string>

	   ```
		[aws_s3.get_files config=<config-array> path={template.path} search="<search>" bucket_name="<bucket-name>" o.set=template.ack/]
	  ```
## Fetch single file content
	
	###### Input:
	   config : <array>
	   path   :  <string>
	   bucket_name : <string>

	   ```
	   	 [aws_s3.get_file_contents config="<config-array>" path=<path>  o.set=template.ack/]
	   ```
## Save file
	
	###### Input:
	   config : <array>
	   path   :  <string>
	   contents : <string>

	```
		[aws_s3.put_file_contents config="<config-array>" path="<path>" contents="<content>" o.set=template.ack/]
	```
## Delete single file
	
	###### Input:
	   config : <array>
	   path   :  <string>
	  bucket_name : <string>

	```
	[aws_s3.delete_file config=<config-array> path={template.path} bucket_name="<bucket-name>" o.set=template.ack/]	
	```

## Delete all files
	
	###### Input:
	   config : <array>
	   path   :  <string>
	  bucket_name : <string>
	```
  [aws_s3.delete_all_files config=<config-array> path={template.path} bucket_name="<bucket-name>" o.set=template.ack/]
  
	```

## Download single file
	
  ###### Input:
	   config : <array>
	   path   :  <string>
	  bucket_name : <string>
	```	
  	[aws_s3.download_file config=<config-array> path={template.path} bucket_name="<bucket-name>" o.set=template.ack/] 
    ```

 ## Download all files 

 	###### Input:
	   config : <array>
	   path   :  <string>
	   bucket_name : <string>
	   download_path :<string>

 	```
 		[aws_s3.download_all config=<config-array> path={template.path} bucket_name="<bucket-name>" download_path="/var/tmp/cibil/{token}.zip" o.set=template.ack/] 
 	```

 ## presigned url and get file data

 	###### Input:
	   config : <array>
	   source   :  <string>
	   bucket_name : <string>
	   destination :<string>

 	```
 	[aws_s3.download_public_file config=<config-array> source="<source>" destination="{destination}" bucket_name="<bucket-name>" o.set=template.ack/]
 	```

 ## Display file on browswer 

 	###### Input:
	   config : <array>
	   path   :  <string>
	   bucket_name : <string>
	  
 	
 	```
 		[aws_s3.read_file config=<config-array> path=<template.path> bucket_name="<bucket-name>" o.set=template.ack/] 
 	```

 ## Move local file to s3

 	###### Input:
	   config : <array>
	   source   :  <string>
	   destination : <string>
	   bucket_name : <string>

 	```
 		 [aws_s3.move_file_to_bucket source="{template.local_path}/{template.file_name}" destination="<template.s3_path>" config=<config-array> bucket_name="<bucket-name>" o.set=template.ack/]
 	```