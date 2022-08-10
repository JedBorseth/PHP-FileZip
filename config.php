<?php

/* ---- UPDATE THE FOLLOWING VALUES TO MATCH YOUR SERVER FOLDER STRUCTURE----- */

/*
this file contains some CONSTANTS and $variables
that should be used by your script when 
application specific paths are need.
You may add additional variables and CONSTANTS as you deem neccessary

NOTE: you will need to update the values of these two:
    $fullPathToSource
    $fullPathToDestination    
so that they reflect the actual loaction of the scripts on your localhost server    
eg $_SERVER['DOCUMENT_ROOT'] will provide the path to the htdocs server root,
then add whatever additional folders as needed

for example, if the following path is used:
    $fullPathToSource       =  $_SERVER['DOCUMENT_ROOT'] . "/session09/assignment09/";
it will result in some thing like
    C:/MAMP/htdocs/sessiob09/assignment09/    
or on MAC OS    
    Applications/MAMP/htdocs/session09/assignment09/    
*/

//imagick will need a full path to read or write images
$nameOfArchiveFolder = "_archive_upload";
$extractFolder = "_unzipped_archive";
const WEB_IMAGE_FORMATS = array("image/jpeg","image/jpg","image/gif","image/png");
$downloadFolder = "_result";

//Only used these ^^^



$fullPathToSource       =  $_SERVER['DOCUMENT_ROOT'] . "/session09/assignment09/";
$fullPathToDestination  =  $_SERVER['DOCUMENT_ROOT'] . "/session09/assignment09/";


//some constants for important folder and file locations
const ZIP_UPLOAD_DIRECTORY 			= "_zip_uploads/";
const ZIP_DOWNLOAD_DIERCTORY 		= "_zip_downloads/";
const WATERMARKED_IMAGES_DIRECTORY	= "_watermarked_images/";
const WATERMARK			 			= "watermark.png";

//an array of acceptable file types
//use mime_content_type() to determine
//if uploaded files are ok to use as images
//by seeing if the uploaded file is in this array



?>
