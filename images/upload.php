<?php 
    # This code is written by Tradepage Pty Ltd for Ellies Pty Ltd (the 'parties' mentioned below)
    # The code is provided based on the the terms specified within the agreed NDA between both parties.
    # Both parties have agreed the code is strictly confidential
    # and only by mutal agreement of both parties may the code be exposed to outside parties.
    #
    # Any changes made to the code other than by Tradepage Pty Ltd during the NDA Agreement void support of the code
    #
    /* --------------------------------------------------------------------------
    * This source code contains confidential information that is proprietary to
    * CloudGroup (Pty) Ltd. No part of its contents may be used,
    * copied, disclosed or conveyed to any party in any manner whatsoever
    * without prior written permission from CloudGroup(Pty) Ltd.
    * No part of this source code may be used, reproduced, stored in a retrieval system,
    * or transmitted, in any form or by any means, electronic, mechanical,
    * photocopying, recording or otherwise, without the prior written permission
    * of the copyright owners.
    * --------------------------------------------------------------------------
    * Copyright CloudGroup (Pty) Ltd
    */
    session_start();
    if (isset($_POST['step']) && intval($_POST['step']) == 1) {
        if (isset($_SESSION['new_image'])){
            @unlink("images/data/" . $_SESSION['new_image']);
        }
        uploadFile($_FILES['myfile'], "images/data/".session_id());
    }
    print "<form  action='upload.php' method='POST' id='formid' enctype='multipart/form-data'>";
    print "<input type='hidden' name='step' value='1' />";
    print "<input type='file' id='fileToUpload' name='myfile' />"; //style='text-align:right; -moz-opacity: 90; filter:alpha(opacity: 90); opacity: 90; z-index:2; width: 80px'
    print "</form>";
    
    function uploadFile ($fileobj, $destinationAndNewFilenameWithoutExtenstion) {
        if ($destinationAndNewFilenameWithoutExtenstion != "") {
            $allowedExtensions = array("jpg", "jpeg", "png", "gif");
            $extension         = end(explode(".", basename($fileobj['name'])));
            if (!in_array(strtolower($extension), $allowedExtensions)) return;
            
            if (move_uploaded_file($fileobj['tmp_name'], $destinationAndNewFilenameWithoutExtenstion . "_0." . $extension)) {
                createThumbnail($destinationAndNewFilenameWithoutExtenstion . "_0." . $extension, $destinationAndNewFilenameWithoutExtenstion . "." . $extension);
                unlink($destinationAndNewFilenameWithoutExtenstion . "_0." . $extension);
                $_SESSION['new_image'] = basename($destinationAndNewFilenameWithoutExtenstion . "." . $extension);
            }
        }
    }
    
    function createThumbnail ($source, $dest, $thumb_size = 162, $jpg_quality = 100) {
	$size   = getimagesize($source);
	$width  = $size[0];
	$height = $size[1];
	
	if ($width > $height) {
            $x = ceil(($width - $height) / 2 );	
            $y = 0;
            $width = $height;
	} else {
            $x = 0;
            $y = ceil(($height - $width) / 2);
            $height = $width;
	}
	
        switch( strtolower(preg_replace('/^.*\./', '', $source)) ) {
            case 'jpg':
            case 'jpeg':
                $im = imagecreatefromjpeg($source);
            break;
            case 'png':
                $im = imagecreatefrompng($source);
            break;
            case 'gif':
                $im = imagecreatefromgif($source);
            break;
            default:
                // Unsupported format
                return false;
            break;
        }
	$new_im = imagecreatetruecolor($thumb_size,$thumb_size);
	if (imagecopyresampled($new_im,$im,0,0,$x,$y,$thumb_size,$thumb_size,$width,$height)) {
            switch( strtolower(preg_replace('/^.*\./', '', $source)) ) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($new_im, $dest, $jpg_quality);
                break;
                case 'png':
                    imagepng($new_im, $dest);
                break;
                case 'gif':
                    imagegif($new_im, $dest);
                break;
                default:
                    // Unsupported format
                    return false;
                break;
            }
	}
    }
?>