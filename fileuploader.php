<?php
/*
File Uploader Class
author: atuballas@github.com
Sample Usage:

$oFileUpload = new FileUploader( 'input_file_field_name', true ); // true generates a new file name

// set file types accepted
$oFileUpload->setAcceptedFileTypes( array( 'jpg', 'jpeg', 'png' ) );

// set max upload size limit (in MB)
$oFileUpload->setMaxFileUploadSize( 1 );

// set server upload path (must have trailing slash)
$oFileUpload->setUploadPath( 'upload_path/' );

// do the upload with no resize
$oFileUpload->doUpload();

// do the upload with resize
$oFileUpload->doUpload( true, array(
										// width, height, resize server upload path, crop
										array( 100, 100, 'uplimg/100px/', true ),
										array( 150, 150, 'uplimg/150px/', true ),
										array( 200, 200, 'uplimg/200px/', true ),
										array( 250, 250, 'uplimg/250px/', true ),
										array( 300, 300, 'uplimg/300px/', true ),
										array( 350, 350, 'uplimg/350px/', true ),
										array( 400, 400, 'uplimg/400px/', true ),
										array( 450, 450, 'uplimg/450px/', true ),
										array( 500, 500, 'uplimg/500px/', true )
								    ) 
					  );

// check if upload is successfull
if( $oFileUpload->getUploadStatus() ){
	echo 'Success'
}

// to get upload status description
echo $oFileUpload->getUploadStatusDescription();

*/
class FileUploader{
	
	private $benchmark = true;
	private $input_name;
	private $new_filename = false;
	private $upload_status = true;
	private $upload_status_description;
	private $max_upload_size = 0;
	private $upload_path = '';
	private $file_types = array();
	
	/*
	Function: __construct()
	@param:
		$input_name(string) - name of the input element
		$new_filename(boolean) - determines whether to generate new filename of the uploaded file
			- true (generates new filename)
	@output:
		none
	*/
	public function __construct( $input_name, $new_filename = false ){
		if( $this->benchmark ){
			$this->start_time = microtime( true );
		}
		$this->input_name = $input_name;
		$this->new_filename = $new_filename;
	}
	
	public function __destruct(){
		if( $this->benchmark ){
			$this->end_time = microtime( true );
			$benchmark_elapsed_time = $this->end_time - $this->start_time;
			echo '<br><br>Benchmark Time: ' . $benchmark_elapsed_time;
		}
	}
	
	/*
	Function: setAcceptedFileTypes()
	@param:
		$file_types(array) - file extensions accepted 
	@output:
		none
	*/
	public function setAcceptedFileTypes( $file_types ){
		$this->file_types = $file_types;
	}
	
	/*
	Function: setMaxFileUploadSize()
	@param:
		$max_upload_size(integer) - max upload size limit
	@output:
		none
	*/
	public function setMaxFileUploadSize( $max_upload_size ){
		$this->max_upload_size = $max_upload_size;
	}
	
	/*
	Function: setUploadPath()
	@param:
		$upload_path(string) - server upload path where uploaded files are stored
	@output:
		none
	*/
	public function setUploadPath( $upload_path ){
		$this->upload_path = $upload_path;
	}
	
	/*
	Function: getUploadStatus()
	@param:
		none
	@output:
		boolean - upload result status
	*/
	public function getUploadStatus(){
		return $this->upload_status;
	}
	
	/*
	Function: getUploadStatusDescription()
	@param:
		none
	@output:
		string - upload result description
	*/
	public function getUploadStatusDescription(){
		return $this->upload_status_description;
	}
	
	/*
	Function: doUpload()
	@param:
		none
	@output:
		none
	*/
	public function doUpload( $resize = false, $resize_options = array() ){
		if( empty( $this->file_types ) ){
			$this->upload_status = false;
			$this->upload_status_description = 'File extensions to accept are not set.';
		}
		if( $this->max_upload_size == 0 ){
			$this->upload_status = false;
			$this->upload_status_description = 'Max upload size is not set.';
		}
		if( empty( $this->upload_path ) ){
			$this->upload_status = false;
			$this->upload_status_description = 'Server upload path is not set.';
		}
		
		if( $this->upload_status ){
			if( ! empty( $_FILES ) ){
				if( ! empty( $_FILES[$this->input_name]['name'] ) ){
					$ext = explode( '.', $_FILES[$this->input_name]['name'] );
					$ext = end( $ext );
					if( in_array( strtolower( $ext ), $this->file_types ) ){
						$size = ( $_FILES[$this->input_name]['size'] / 1024 ) / 1024;
						if( $size <= $this->max_upload_size ){
							if( is_dir( $this->upload_path ) ){
								$file = $_FILES[$this->input_name]['tmp_name'];
								if( $this->new_filename ){
									$file = base64_encode( $file ) . '.' . $ext;
								}else{
									$file = $_FILES[$this->input_name]['name'];
								}
								if( ! $resize ){
									if( move_uploaded_file( $_FILES[$this->input_name]['tmp_name'], $this->upload_path . $file ) ){
										$this->upload_status = true;
										$this->upload_status_description = 'Upload is successful.';
									}else{
										$this->upload_status = false;
										$this->upload_status_description = 'Something went wrong during file upload. Try again later.';
									}
								}else{
									if( $this->doResize( $_FILES[$this->input_name]['tmp_name'], $file, $resize_options ) ){
										$this->upload_status = true;
										$this->upload_status_description = 'Upload and resize is successful.';
									}else{
										$this->upload_status = false;
										$this->upload_status_description = 'Something went wrong during file upload and resizing. Try again later.';
									}
								}
							}else{
								$this->upload_status = false;
								$this->upload_status_description = 'Upload path does not exists.';
							}
						}else{
							$this->upload_status = false;
							$this->upload_status_description = 'File uploaded exceeds to upload size limit.';
						}
					}else{
						$this->upload_status = false;
						$this->upload_status_description = 'File uploaded is not accepted.';
					}
				}else{
					// error 500 
				}
				
			}else{
				// error 500 
			}
		}
	}
	
	private function doResize( $src_file, $file, $resize_options ){
		$resizing = true;
		foreach( $resize_options as $ro ){
			$width = $ro[0];
			$height = $ro[1];
			$path = $ro[2];
			$crop = $ro[3];
			if( ! is_dir( $path ) ){
				mkdir( $path );
			}
			if( $this->imageResizer( $src_file, $path . $file, $width, $height, 90, $crop ) ){
				$resizing = true;
			}else{
				$resizing = false;
				break;
			}
		}
		return $resizing;
	}
	
	/*
	Function: imageResizer()
	@param:
		$source_image
		$destination_filename
		$width = 200
		$height = 150
		$quality = 70
		$crop = true
	@output:
		none
	I do not own this function, I took it somewhere in the web. Copyright of the function belongs to the owner	
	*/
	private function imageResizer( $source_image, $destination_filename, $width = 200, $height = 150, $quality = 70, $crop = true ){
		if( ! $image_data = getimagesize( $source_image ) ){
			return false;
        }
		switch( $image_data['mime'] ){
			case 'image/gif':
					$get_func = 'imagecreatefromgif';
					$suffix = ".gif";
			break;
			case 'image/jpeg';
					$get_func = 'imagecreatefromjpeg';
					$suffix = ".jpg";
			break;
			case 'image/png':
					$get_func = 'imagecreatefrompng';
					$suffix = ".png";
			break;
        }
		$img_original = call_user_func( $get_func, $source_image );
        $old_width = $image_data[0];
        $old_height = $image_data[1];
        $new_width = $width;
        $new_height = $height;
        $src_x = 0;
        $src_y = 0;
        $current_ratio = round( $old_width / $old_height, 2 );
        $desired_ratio_after = round( $width / $height, 2 );
        $desired_ratio_before = round( $height / $width, 2 );
		
		if( $old_width < $width || $old_height < $height ){
			/**
			 * The desired image size is bigger than the original image. 
			 * Best not to do anything at all really.
			 */
			return false;
        }

        /**
         * If the crop option is left on, it will take an image and best fit it
         * so it will always come out the exact specified size.
         */
        if( $crop ){
			/**
			 * create empty image of the specified size
			 */
			$new_image = imagecreatetruecolor( $width, $height );

			/**
			 * Landscape Image
			 */
			if( $current_ratio > $desired_ratio_after ){
				$new_width = $old_width * $height / $old_height;
			}

			/**
			 * Nearly square ratio image.
			 */
			if( $current_ratio > $desired_ratio_before && $current_ratio < $desired_ratio_after ){
				if( $old_width > $old_height )
				{
						$new_height = max( $width, $height );
						$new_width = $old_width * $new_height / $old_height;
				}
				else
				{
						$new_height = $old_height * $width / $old_width;
				}
			}

			/**
			 * Portrait sized image
			 */
			if( $current_ratio < $desired_ratio_before  ){
				$new_height = $old_height * $width / $old_width;
			}

			/**
			 * Find out the ratio of the original photo to it's new, thumbnail-based size
			 * for both the width and the height. It's used to find out where to crop.
			 */
			$width_ratio = $old_width / $new_width;
			$height_ratio = $old_height / $new_height;

			/**
			 * Calculate where to crop based on the center of the image
			 */
			$src_x = floor( ( ( $new_width - $width ) / 2 ) * $width_ratio );
			$src_y = round( ( ( $new_height - $height ) / 2 ) * $height_ratio );
        }
        /**
         * Don't crop the image, just resize it proportionally
         */
        else{
			if( $old_width > $old_height ){
					$ratio = max( $old_width, $old_height ) / max( $width, $height );
			}else{
					$ratio = max( $old_width, $old_height ) / min( $width, $height );
			}

			$new_width = $old_width / $ratio;
			$new_height = $old_height / $ratio;

			$new_image = imagecreatetruecolor( $new_width, $new_height );
        }

        /**
         * Where all the real magic happens
         */
        imagecopyresampled( $new_image, $img_original, 0, 0, $src_x, $src_y, $new_width, $new_height, $old_width, $old_height );

        /**
         * Save it as a JPG File with our $destination_filename param.
         */
        imagejpeg( $new_image, $destination_filename, $quality  );

        /**
         * Destroy the evidence!
         */
        imagedestroy( $new_image );
        imagedestroy( $img_original );

        /**
         * Return true because it worked and we're happy. Let the dancing commence!
         */
        return true;
	}
	
}
?>