<?php

  $http_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;
  $allowed_domains = array("https://auth.reestoc.com", "https://dashboard.reestoc.com");
  foreach ($allowed_domains as $value) {if ($http_origin === $value) {header('Access-Control-Allow-Origin: ' . $http_origin);}}
  header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
  header("Access-Control-Allow-Headers: Content-Type, Authorization, origin, Accept-Language, Range, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");
  require '../../../config/connect_to_me.php';
  include_once "../../../config/functions.php";

  class genericClass {
    public $engineMessage = 0;
    public $engineError = 0;
    public $engineErrorMessage;
    public $resultData;
    public $filteredData;
  }


  $data = json_decode(file_get_contents("php://input"), true);

  $functions = new Functions();

  if ($connected) {

    try {
      $conn->beginTransaction();

      $date_added = $functions->today;
      $active = $functions->active;
      $role = "Owner";
      $image_count = $functions->check_length_1;

      function compressImage($source, $destination, $quality) {
         $info = getimagesize($source);

         if ($info['mime'] == 'image/jpeg')
           $image = imagecreatefromjpeg($source);

         elseif ($info['mime'] == 'image/gif')
           $image = imagecreatefromgif($source);

         elseif ($info['mime'] == 'image/png')
           $image = imagecreatefrompng($source);

         if(imagejpeg($image, $destination, $quality)){
           return true;
         };
       };

      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $vendor_unique_id = isset($_GET['vendor_unique_id']) ? $_GET['vendor_unique_id'] : $data['vendor_unique_id'];
      $upload_type = "Compress";

      $path_to_upload = "../../../../images/cover_images/";
      // $path_to_delete = $_SERVER['DOCUMENT_ROOT']."/images/cover_images"; // For online own
      $path_to_delete = $_SERVER['DOCUMENT_ROOT']."/cerotics_store/images/cover_images"; // For offline own
      // $path_to_save = "https://www.reestoc.com/images/cover_images/";
      $path_to_save = "https://www.reestoc.com/images/cover_images/";

      $sqlSearchUser = "SELECT unique_id FROM vendor_users WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND role=:role AND status=:status";
      $querySearchUser = $conn->prepare($sqlSearchUser);
      $querySearchUser->bindParam(":unique_id", $user_unique_id);
      $querySearchUser->bindParam(":vendor_unique_id", $vendor_unique_id);
      $querySearchUser->bindParam(":role", $role);
      $querySearchUser->bindParam(":status", $active);
      $querySearchUser->execute();

      if ($querySearchUser->rowCount() > 0) {

        $sql2 = "SELECT unique_id, cover_image_file FROM vendors WHERE unique_id=:unique_id AND status=:status";
        $query2 = $conn->prepare($sql2);
        $query2->bindParam(":unique_id", $vendor_unique_id);
        $query2->bindParam(":status", $active);
        $query2->execute();

        $files_count = !empty($_FILES['file']['name']) ? count($_FILES['file']['name']) : null;

        if (empty($_FILES['file']['name'])) {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = 'Upload Failed. File empty!';
        }
        else if ($files_count > $image_count) {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "Not more than 1 file allowed";
        }
        else if ($query2->rowCount() > 0) {

          $the_vendor_cover_image_details = $query2->fetch();
          $old_vendor_cover_image = $the_vendor_cover_image_details[0];

          $allowed_ext = array('jpg','JPG','jpeg','JPEG','png','PNG','webp','WEBP','gif','GIF');
            if(!empty($_FILES['file']['name'])){
              $count = count($_FILES['file']['name']);
              $countErrors = 0;
              foreach ($_FILES['file']['size'] as $key => $filesize){
                if ($filesize > 30000000) {
                  $countErrors += 1;
                }
              }

              if ($countErrors == 0) {
                $countErrorsExtentions = 0;
                foreach ($_FILES['file']['name'] as $key => $filename){
                  $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
                  if (!in_array($file_ext,$allowed_ext)) {
                    $countErrorsExtentions += 1;
                  }
                }

                if ($countErrorsExtentions == 0) {
                  if ($upload_type == 'Compress') {
                    foreach ($_FILES['file']['name'] as $key => $filename){

                      $filename_ = $_FILES['file']['name'][$key];
                      $fileSize = $_FILES["file"]["size"][$key];
                      $fileType = $_FILES['file']['type'][$key];
                      $fileError = $_FILES["file"]["error"][$key];
                      $file_ext = pathinfo($filename_, PATHINFO_EXTENSION);

                      $string = $filename;
                      $pattern = '/[ +#(),*!"<>~`;:?]/i';
                      $replacement = '_';

                      $the_time = time();

                      $newFilename = $the_time . "_" . preg_replace($pattern, $replacement, $string);

                      $path = $path_to_upload . $newFilename;

                      $image_webp_name = $filename . ".webp";

                      if(($fileType == 'image/jpeg')  || ($fileType == 'image/jpg') || ($fileType == 'image/png')){

                    		// CHECKING THE IMAGE FILE BEFORE UPLOADING APPROXIMATELY 10MB
                    		if($fileSize < 30000000){

                    			$fileSizeInMb = round(($fileSize/1000000), 1) . " MB";

                    			// CHECK IF THERE'S AN ERROR BEFORE UPLOADING
                    			if($fileError <= 0){

                    				// CHECK IF FILE EXIST IN FOLDER
                    				if(file_exists($path)){
                              $returnvalue = new genericClass();
                              $returnvalue->engineError = 2;
                              $returnvalue->engineErrorMessage = "Image already exist";
                    				}
                    				else {
                    					$fileURL = $path;
                    					$image_webp_url = $path_to_upload . $image_webp_name;

                              if((isset($newFilename)) && ($newFilename != "Image already exist")){
                            	  $sourcePath = $_FILES["file"]["tmp_name"][$key]; // Storing source path of the file in a variable
                                $targetPath = $path; // Target path where file is to be stored
                            		if(compressImage($sourcePath, $path, 60) == 1){
                                 $file = $path;
                            			$image = imagecreatefromstring(file_get_contents($file));
                            			ob_start();
                            			imagejpeg($image,NULL,60);
                            			$cont = ob_get_contents();
                            			ob_end_clean();
                            			imagedestroy($image);
                            			$content = imagecreatefromstring($cont);

                                  $string = pathinfo($_FILES['file']['name'][$key], PATHINFO_FILENAME);
                                  $pattern = '/[ +#(),*!"<>~`;:?]/i';
                                  $replacement = '_';

                                  $newFilenameWithoutExtension = time() . "_" . preg_replace($pattern, $replacement, $string);

                            			$output = $path_to_upload .$the_time . '.webp';
                            			$image_webp_url = $path_to_save .$the_time . ".webp";
                            			imagewebp($content,$output);
                            			imagedestroy($content);

                                  $data['cover_image'] = $image_webp_url;
                                  $data['cover_image_file'] = $the_time . '.webp';
                                  // $data['cover_image'] = $path_to_save.$output_file;
                                  $data['file_size'] = $fileSize;

                                  $sql = "UPDATE vendors SET cover_image=:cover_image, cover_image_file=:cover_image_file, last_modified=:last_modified
                                  WHERE unique_id=:unique_id";
                                  $query = $conn->prepare($sql);
                                  $query->bindParam(":unique_id", $vendor_unique_id);
                                  $query->bindParam(":cover_image", $data['cover_image']);
                                  $query->bindParam(":cover_image_file", $data['cover_image_file']);
                                  $query->bindParam(":last_modified", $date_added);
                                  $query->execute();

                                  if($query){
                                    if($count > 1){
                                      if($old_vendor_cover_image !== null){unlink($path_to_delete."/".$old_vendor_cover_image);}
                                      $returnvalue = new genericClass();
                                      $returnvalue->engineMessage = 1;
                                      $returnvalue->resultData = 'Images Uploaded Successfully';
                                    }
                                    else{
                                      if($old_vendor_cover_image !== null){unlink($path_to_delete."/".$old_vendor_cover_image);}
                                      $returnvalue = new genericClass();
                                      $returnvalue->engineMessage = 1;
                                      $returnvalue->resultData = 'Image Uploaded Successfully';
                                    }
                                  }
                                  else{
                                    if($count > 1){
                                      $returnvalue = new genericClass();
                                      $returnvalue->engineError = 2;
                                      $returnvalue->engineErrorMessage = 'Images Uploaded but not Saved';
                                    }
                                    else{
                                      $returnvalue = new genericClass();
                                      $returnvalue->engineError = 2;
                                      $returnvalue->engineErrorMessage = 'Image Uploaded but not Saved';
                                    }
                                  }

                                  $output_file = $newFilename;
                                  $data_to_delete = $output_file;

                                  unlink($path_to_delete."/".$data_to_delete);

                            		}
                            		else {
                                  $counter_now = $key;
                                  $returnvalue = new genericClass();
                                  $returnvalue->engineError = 2;
                                  $returnvalue->engineErrorMessage = $counter_now." index Compression failed";
                            		}
                            	}
                            	else {
                                $counter_now = $key;
                                $returnvalue = new genericClass();
                                $returnvalue->engineError = 2;
                                $returnvalue->engineErrorMessage = $counter_now." don't know what happened";
                            	}

                    				}
                    			}
                    			else {
                            $counter_now = $key;
                            $returnvalue = new genericClass();
                            $returnvalue->engineError = 2;
                            $returnvalue->engineErrorMessage = $counter_now." index, Oops! An error occured on our server!";
                    			}
                    		}
                    		else {
                          $counter_now = $key;
                          $returnvalue = new genericClass();
                          $returnvalue->engineError = 2;
                          $returnvalue->engineErrorMessage = $counter_now." index, Image file is too large";
                    		}
                    	}
                    	else {

                        if ($file_ext == "webp" || $file_ext =="WEBP" || $file_ext == "gif" || $file_ext =="GIF") {

                          $newFilename = $the_time.".".$file_ext;

                          $path = $path_to_upload . $newFilename;

                          if(move_uploaded_file($_FILES['file']['tmp_name'][$key], $path)){

                            $image_url = $path_to_save .$the_time.".".$file_ext;

                            $data['cover_image'] = $image_url;
                            // $data['cover_image'] = $path_to_save.$output_file;
                            $data['cover_image_file'] = $the_time.".".$file_ext;
                            $data['file_size'] = $fileSize;

                            $sql = "UPDATE vendors SET cover_image=:cover_image, cover_image_file=:cover_image_file, last_modified=:last_modified
                            WHERE unique_id=:unique_id";
                            $query = $conn->prepare($sql);
                            $query->bindParam(":unique_id", $vendor_unique_id);
                            $query->bindParam(":cover_image", $data['cover_image']);
                            $query->bindParam(":cover_image_file", $data['cover_image_file']);
                            $query->bindParam(":last_modified", $date_added);
                            $query->execute();

                            if($query){
                              if($count > 1){
                                if($old_vendor_cover_image !== null){unlink($path_to_delete."/".$old_vendor_cover_image);}
                                $returnvalue = new genericClass();
                                $returnvalue->engineMessage = 1;
                                $returnvalue->resultData = 'Images Uploaded Successfully';
                              }
                              else{
                                if($old_vendor_cover_image !== null){unlink($path_to_delete."/".$old_vendor_cover_image);}
                                $returnvalue = new genericClass();
                                $returnvalue->engineMessage = 1;
                                $returnvalue->resultData = 'Image Uploaded Successfully';
                              }
                            }
                            else{
                              if($count > 1){
                                $returnvalue = new genericClass();
                                $returnvalue->engineError = 2;
                                $returnvalue->engineErrorMessage = 'Images Uploaded but not Saved';
                              }
                              else{
                                $returnvalue = new genericClass();
                                $returnvalue->engineError = 2;
                                $returnvalue->engineErrorMessage = 'Image Uploaded but not Saved';
                              }
                            }

                          }
                          else {
                            $returnvalue = new genericClass();
                            $returnvalue->engineError = 2;
                            $returnvalue->engineErrorMessage = "Can't complete upload of ".$file_ext." image";
                          }

                        }
                        else {
                          $counter_now = $key;
                          $returnvalue = new genericClass();
                          $returnvalue->engineError = 2;
                          $returnvalue->engineErrorMessage = $counter_now." index, Invalid image format";
                        }

                    	}

                      sleep(1);

                    }
                  }
                  else if ($upload_type == 'Plain') {
                    foreach ($_FILES['file']['name'] as $key => $filename){

                      $filename_ = $_FILES['file']['name'][$key];
                      $fileSize = $_FILES["file"]["size"][$key];
                      $fileType = $_FILES['file']['type'][$key];
                      $fileError = $_FILES["file"]["error"][$key];
                      $file_ext = pathinfo($filename_, PATHINFO_EXTENSION);

                      $string = $filename;
                      $pattern = '/[ +#(),*!"<>~`;:?]/i';
                      $replacement = '_';

                      $the_time = time();

                      $newFilename = $the_time . "_" . preg_replace($pattern, $replacement, $string);

                      $path = $path_to_upload . $newFilename;

                      $image_webp_name = $filename . ".webp";

                      $newFilename = $the_time.".".$file_ext;

                      $path = $path_to_upload . $newFilename;

                      if(move_uploaded_file($_FILES['file']['tmp_name'][$key], $path)){

                        $image_url = $path_to_save .$the_time.".".$file_ext;

                        $data['cover_image'] = $image_url;
                        // $data['cover_image'] = $path_to_save.$output_file;
                        $data['cover_image_file'] = $the_time.".".$file_ext;
                        $data['file_size'] = $fileSize;

                        $sql = "UPDATE vendors SET cover_image=:cover_image, cover_image_file=:cover_image_file, last_modified=:last_modified
                        WHERE unique_id=:unique_id";
                        $query = $conn->prepare($sql);
                        $query->bindParam(":unique_id", $vendor_unique_id);
                        $query->bindParam(":cover_image", $data['cover_image']);
                        $query->bindParam(":cover_image_file", $data['cover_image_file']);
                        $query->bindParam(":last_modified", $date_added);
                        $query->execute();

                        if($query){
                          if($count > 1){
                            if($old_vendor_cover_image !== null){unlink($path_to_delete."/".$old_vendor_cover_image);}
                            $returnvalue = new genericClass();
                            $returnvalue->engineMessage = 1;
                            $returnvalue->resultData = 'Images Uploaded Successfully';
                          }
                          else{
                            if($old_vendor_cover_image !== null){unlink($path_to_delete."/".$old_vendor_cover_image);}
                            $returnvalue = new genericClass();
                            $returnvalue->engineMessage = 1;
                            $returnvalue->resultData = 'Image Uploaded Successfully';
                          }
                        }
                        else{
                          if($count > 1){
                            $returnvalue = new genericClass();
                            $returnvalue->engineError = 2;
                            $returnvalue->engineErrorMessage = 'Images Uploaded but not Saved';
                          }
                          else{
                            $returnvalue = new genericClass();
                            $returnvalue->engineError = 2;
                            $returnvalue->engineErrorMessage = 'Image Uploaded but not Saved';
                          }
                        }

                      }
                      else {
                        $returnvalue = new genericClass();
                        $returnvalue->engineError = 2;
                        $returnvalue->engineErrorMessage = "Can't complete upload of ".$file_ext." image";
                      }

                      sleep(1);

                    }
                  }
                  else {
                    $returnvalue = new genericClass();
                    $returnvalue->engineError = 2;
                    $returnvalue->engineErrorMessage = 'Invalid / Empty Upload Type';
                  }
                }
                else {
                  if ($countErrorsExtentions == 1) {
                    $returnvalue = new genericClass();
                    $returnvalue->engineError = 2;
                    $returnvalue->engineErrorMessage = $countErrorsExtentions.' image format is not supported';
                  }
                  else{
                    $returnvalue = new genericClass();
                    $returnvalue->engineError = 2;
                    $returnvalue->engineErrorMessage = $countErrorsExtentions.' images format is not supported';
                  }
                }
              }
              else {
                if ($countErrors == 1) {
                  $returnvalue = new genericClass();
                  $returnvalue->engineError = 2;
                  $returnvalue->engineErrorMessage = $countErrors.' image have size over 30mb';
                }
                else{
                  $returnvalue = new genericClass();
                  $returnvalue->engineError = 2;
                  $returnvalue->engineErrorMessage = $countErrors.' images has sizes over 30mb';
                }
              }
            }
            else{
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = 'Upload Failed. File empty!';
            }

        }
        else {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "Vendor not found";
        }

      }
      else {
        $returnvalue = new genericClass();
        $returnvalue->engineError = 2;
        $returnvalue->engineErrorMessage = "Vendor owner not found";
      }

      $conn->commit();
    } catch (PDOException $e) {
      $conn->rollback();
      throw $e;
    }

  }
  else {
    $returnvalue = new genericClass();
    $returnvalue->engineError = 2;
    $returnvalue->engineErrorMessage = "No connection";
  }

  echo json_encode($returnvalue);

?>
