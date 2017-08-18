<?php

namespace Includes\Frontend;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
/**
 * Handles the cart activities on frontend part.
 */
class QuoteupHandleFileUpload
{
    /**
     * @var Singleton The reference to *Singleton* instance of this class
     */
    private static $instance;

    /**
     * @var int Max File Size allowed to be uploaded. Right now it is set to 5MB
     */
    public $max_file_size = 5242880; //5 MB

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * This function is used to validate file upload. If this fails ajax call will not save enquiry as well.
     *
     * @return [type] [description]
     */
    public function validateFileUpload()
    {
        $validMediaTypes = array(
        'image/png',
        'image/gif',
        'image/jpeg',
        'image/pjpeg',
        'application/pdf',
        'application/x-pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv',
        );

        $validMediaTypes = apply_filters('quoteup_valid_media_types', $validMediaTypes);

        //continue only if $_POST is set and it is a Ajax request
        if (isset($_POST) && isset($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) && strtolower($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) == 'xmlhttprequest') {
            $validMedia = true;
            foreach ($_FILES as $key => $value) {
                //uploaded file info we need to proceed
                $mediaSize = $value[ 'size' ]; //file size
                $mediaTemp = $value[ 'tmp_name' ]; //file temp
                if ($mediaSize == 0) {
                    $message = __('Blank File. Please upload another file type', QUOTEUP_TEXT_DOMAIN);
                    $this->_errorMessage($message);
                    return false;
                }
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mediaType = finfo_file($finfo, $mediaTemp);
                if (in_array($mediaType, $validMediaTypes)) {
                    $validMedia = true;
                } else {
                    $message = __('Invalid Media File! Please select proper media file.', QUOTEUP_TEXT_DOMAIN);
                    $this->_errorMessage($message);
                    return false;
                }
                unset($key);
            }
            return $validMedia;
        }
    }

    /**
     * This function is used to uplaod files on server.
     */
    public function quoteupUploadFiles($enquiryID)
    {
        $upload_dir = wp_upload_dir();
        $path = $upload_dir[ 'basedir' ].'/QuoteUp_Files/';
        if (!file_exists($path.$enquiryID)) {
            $success = wp_mkdir_p($path.$enquiryID);
            if (!$success) {
                $this->_deleteEnquiry($enquiryID);
                $message = __('Could not create directory to store files', QUOTEUP_TEXT_DOMAIN);
                $this->_errorMessage($message);
            }
        }
        $folder_path = $path.$enquiryID.'/';
        foreach ($_FILES as $key => $value) {
            //uploaded file info we need to proceed
            $mediaName = $value[ 'name' ]; //file name
            $mediaSize = $value[ 'size' ]; //file size
            $mediaTemp = $value[ 'tmp_name' ]; //file temp
            //Get file extension and name to construct new file name
            $mediaInfo = pathinfo($mediaName);
            $mediaExtension = strtolower($mediaInfo[ 'extension' ]); //media extension
            $mediaNameOnly = strtolower($mediaInfo[ 'filename' ]); //file name only, no extension

            if ($this->checkFileUploadedName($mediaNameOnly) === false) {
                $this->_deleteEnquiry($enquiryID);
                $message = __('Invalid File Name', QUOTEUP_TEXT_DOMAIN);
                $this->_errorMessage($message);
            }

            if ($mediaSize > $this->max_file_size) {
                $this->_deleteEnquiry($enquiryID);
                $message = sprintf(__('Size of file you are trying to upload is %s which is too large. Max file size allowed is %s', QUOTEUP_TEXT_DOMAIN), $this->formatFileSizeUnits($mediaSize), $this->formatFileSizeUnits($this->max_file_size));
                $this->_errorMessage($message);
            }

            

            //create a random name for new media Eg: fileName_293749.jpg
            if (file_exists($folder_path.$mediaNameOnly.'.'.$mediaExtension)) {
                $new_file_name = $mediaNameOnly.'_'.rand(0, 9999999999).'.'.$mediaExtension;
            } else {
                $new_file_name = $mediaNameOnly.'.'.$mediaExtension;
            }

            $media_save_folder = $folder_path.$new_file_name;

            if (move_uploaded_file($mediaTemp, $media_save_folder) === false) {
                $this->_deleteEnquiry($enquiryID);
                $message = __('File Could not be uploaded. Please Contact Network Administrator.', QUOTEUP_TEXT_DOMAIN);
                $this->_errorMessage($message);
            }
            unset($key);
        }
        return true;
    }

    /**
     * Returns Error Message.
     *
     * @param type $message
     */
    public function _errorMessage($message)
    {
        echo json_encode(
            array(
            'status' => 'failed',
            'message' => $message,
            )
        );
        die();
    }

    /**
     * Check $_FILES[][name].
     *
     * @param string $filename - Uploaded file name.
     *
     * @return bool If file name is invalid, returns FALSE
     */
    private function checkFileUploadedName($filename)
    {
        return (bool) (preg_match('/^[^\/\:\*\?\"\<\>\|\.]+$/', $filename));
    }

    /**
     * Converts Number of Bytes to Human Readable Format.
     *
     * @param type $bytes Number of Bytes
     *
     * @return string Conversion of Number to Human Readable format
     */
    private function formatFileSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2).' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2).' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes.' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes.' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    private function _deleteEnquiry($enquiryID)
    {
        global $wpdb;
        $wpdb->delete(getEnquiryDetailsTable(), array('enquiry_id' => $enquiryID), array('%d'));
        $wpdb->delete(getEnquiryHistoryTable(), array('enquiry_id' => $enquiryID), array('%d'));
        $wpdb->delete(getEnquiryMetaTable(), array('enquiry_id' => $enquiryID), array('%d'));
        $wpdb->delete(getEnquiryProductsTable(), array('enquiry_id' => $enquiryID), array('%d'));
        $wpdb->delete(getQuotationProductsTable(), array('enquiry_id' => $enquiryID), array('%d'));
        $wpdb->delete(getEnquiryThreadTable(), array('enquiry_id' => $enquiryID), array('%d'));
        $wpdb->delete(getVersionTable(), array('enquiry_id' => $enquiryID), array('%d'));
    }
}
$this->QuoteupFileUpload = QuoteupHandleFileUpload::getInstance();
