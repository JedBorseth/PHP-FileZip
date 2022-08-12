<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Assignment 9 - Zipping files</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<h1 class="text-5xl text-center">Assignment 9 - Jed Borseth</h1>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data" class="m-20 w-1/3 mx-auto text-center flex justify-center items-center border">
    <input type="file" name="fileToUpload" id="fileToUpload" >
    <input type="submit" value="Upload ZIP" name="submit" class="bg-blue-500 text-white rounded p-4 m-4 cursor-pointer">
    <?php
    include_once("./config.php");
    if (isset($extractFolder)) {
        echo downloadFinal($extractFolder);
    } ?>
</form>
<!--Form Response -->
<div class="grid grid-cols-4 place-items-center"><?php

    if (isset($nameOfArchiveFolder) & isset($downloadFolder)) {
        cleanUp($nameOfArchiveFolder, $downloadFolder);
    }
    global $errorMessage, $message;

    function handleUploads($ArchiveFolder, $extractFolder): bool
    {
        global $errorMessage, $message;
        if(!isset($_FILES["fileToUpload"])) {
            $errorMessage = "Please Upload a zip to get started";
            return false;
        }
        $temp = $_FILES["fileToUpload"]["tmp_name"];
        $fileName = $_FILES["fileToUpload"]["name"];
        $uploadFolder = $_SERVER["DOCUMENT_ROOT"] . "/$ArchiveFolder";
        if(mime_content_type($temp) !== "application/zip") {
            $errorMessage = "Uploaded file isn't a zip file";
            return false;
        } else {
            $message = "Successfully Uploaded $fileName";
            move_uploaded_file($temp, $uploadFolder . "/" . $fileName);
            if (!openZips($uploadFolder . "/" . $fileName, $extractFolder)) {
                return false;
            } else {
                $message = "UnZipped: $fileName";
                if (modifyImages($extractFolder)) {
                    if (!displayImages($extractFolder)) {
                        return false;
                    }
                } else {
                    return false;
                }

            }
        }

        return true;
    }
    function openZips($unzipMe, $folder): bool
    {
        global $errorMessage, $message;
        $zip = new ZipArchive();
        if ($zip->open($unzipMe)) {
            $zip->extractTo($folder);
            $zip->close();
        } else {
            $errorMessage = "Unzip Failed";
            return false;
        }
        return true;
    }
    function modifyImages($unzipFolder): bool
    {
        global $errorMessage, $message;
        $tempArr = array();
        $selectedImages = array();
        $folderToScan = $_SERVER["DOCUMENT_ROOT"] . "/$unzipFolder";
        try {
            $overlay = new Imagick("./images/" . WATERMARK);
        } catch (ImagickException $e) {
        }
        if (is_dir($folderToScan)) {
            $tempArr = scandir($folderToScan);
        } else {
            $errorMessage = "$folderToScan is not found a folder";
            return false;
        }
        foreach ($tempArr as $items) {
            if (!preg_match("/^\./", $items ) && mime_content_type("$folderToScan/$items") === "image/jpeg") {
                $items = new Imagick("$folderToScan/$items");
                $selectedImages[] = $items;
            }
        }

        foreach ($selectedImages as $openedImages) {
            try {
                $openedImages->compositeImage($overlay, Imagick::COMPOSITE_DEFAULT, 15, 15);
            } catch (ImagickException $e) {
                $errorMessage = "Could not overlay on an Imagick object";
                return false;
            }
            try {
                $name = $openedImages->getImageFilename();
            } catch (ImagickException $e) {
                $errorMessage = "Could not get name of Imagick object";
                return false;
            }
            try {
                $openedImages->writeImage($name);
            } catch (ImagickException $e) {
                $errorMessage = "Could not write to Imagick pictures";
                return false;
            }
        }
        return true;
    }
    function displayImages($editedPicturesDir): bool
    {
        global $errorMessage, $message;
        if (is_dir($editedPicturesDir)) {
            $tempArr = scandir($editedPicturesDir);
        } else {
            $errorMessage = "$editedPicturesDir is not found a folder";
            return false;
        }
        foreach ($tempArr as $items) {
            if (!preg_match("/^\./", $items ) && mime_content_type("$editedPicturesDir/$items") === "image/jpeg") {
                $imgEl = "<img src='./$editedPicturesDir/$items' alt='uploaded images with a watermark' class='border-t-4 border-blue-500 w-1/2'>";
                echo $imgEl;
            }
        }
        $message = "Finished Modifying " . $_FILES["fileToUpload"]["name"];
        return true;
    }
    function cleanUp($folder1, $folder2): void
    {
        if (is_dir($folder1) & is_dir($folder2)) {
            $f1 = scandir($folder1);
            $f2 = scandir($folder2);
            delete($f1, $folder1);
            delete($f2, $folder2);
        }
    }
    function delete($folder, $parent): void
    {
        foreach ($folder as $files) {
            if (is_file("$parent/$files")) {
                unlink("$parent/$files");
            }
        }
    }
    function downloadFinal($parentFolder)
    {
        global $downloadFolder;
        if (isset($downloadFolder) && is_dir($downloadFolder)) {
            if (is_dir($parentFolder)) {
                $scan = scandir($parentFolder);
                foreach ($scan as $items) {
                    if (!preg_match("/^\./", $items ) && mime_content_type("$parentFolder/$items") === "image/jpeg") {
                        $zip = new ZipArchive();
                        $zip->open("$downloadFolder/download.zip", ZipArchive::CREATE);
                        $zip->addFile("$parentFolder/$items");
                        $zip->close();
                        return "<a href='$downloadFolder/download.zip' class='bg-red-500 text-white rounded p-4 m-4 cursor-pointer'> Download</a>";
                    }
                }
            }
        }
    }

    if (isset($nameOfArchiveFolder, $extractFolder)) {
        if (!handleUploads($nameOfArchiveFolder, $extractFolder)) {
            echo "<p class='text-center col-span-full p-5 w-full'>$errorMessage</p>";
        } else {
            echo "<p class='text-center col-span-full p-5 w-full'>$message </p>";

        }

    } else {
        die("<h1> Error in config.php could not find 'nameOfArchiveFolder' or 'extractFolder'</h1>");
    }

    ?></div>

</body>
</html>



<?php
if (isset($extractFolder) & isset($nameOfArchiveFolder)) {
//    Change nothing to $extractFolder to delete pictures in the zip folder, unable to display on screen when deleted
    cleanUp("nothing", $nameOfArchiveFolder);
}
