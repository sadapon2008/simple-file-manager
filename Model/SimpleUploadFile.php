<?php

App::uses('SimpleFileManagerAppModel', 'SimpleFileManager.Model');

/**
 * SimpleUploadFile Model
 *
 */
class SimpleUploadFile extends AppModel {

    public $validate = array();

    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);

        $uploadField = Configure::read('SimpleFileManager.uploadField');
        $allowedExt = Configure::read('SimpleFileManager.allowedExt');
        $fileSizeMax = Configure::read('SimpleFileManager.fileSizeMax');

        $this->validate[$uploadField] = array(
            'validateUploadError' => array(
                'rule' => array('validateUploadError'),
                'required' => true,
                'last' => true,
                'message' => 'アップロードエラーが発生しました',
            ),                
            'notEmptyFile' => array(
                'rule' => array('notEmptyFile'),
                'required' => true,
                'last' => true,
                'message' => '空のファイルはアップロードできません',
            ),
            'validateFileSize' => array(
                'rule' => array('validateFileSize', $fileSizeMax),
                'required' => true,
                'last' => true,
                'message' => sprintf('ファイルサイズの上限は%sバイトです', number_format($fileSizeMax)),
            ),
            'validateExt' => array(
                'rule' => array('validateExt', $allowedExt),
                'required' => true,
                'last' => true,
                'message' => sprintf('アップロード可能なファイルの種類は%sです', implode(',', $allowedExt)),
            ),
        );
    }

    public function upload($post_data) {
        $uploadField = Configure::read('SimpleFileManager.uploadField');
        $uploadDir = Configure::read('SimpleFileManager.uploadDir');
        $fileMask = Configure::read('SimpleFileManager.fileMask');

        $this->create();
        $this->set($post_data);
        if(!$this->validates()) {
            return false;
        }

        $post_data['SimpleUploadFile']['file_name'] = $post_data['SimpleUploadFile'][$uploadField]['name'];
        $post_data['SimpleUploadFile']['file_content_type'] = $post_data['SimpleUploadFile'][$uploadField]['type'];
        $post_data['SimpleUploadFile']['file_size'] = $post_data['SimpleUploadFile'][$uploadField]['size'];

        $result = $this->save($post_data, array('validate' => false));
        if(empty($result)) {
            return false;
        }

        // move file

        $id = $this->getLastInsertID();

        $path_parts = pathinfo($post_data['SimpleUploadFile'][$uploadField]['name']);
        $file_ext = mb_convert_case($path_parts['extension'], MB_CASE_LOWER);

        $newFilePath = $uploadDir . DS . $id . '.' . $file_ext;


        if(!file_exists($uploadDir)) {
            return false;
        }

        if(!move_uploaded_file($post_data['SimpleUploadFile'][$uploadField]['tmp_name'], $newFilePath)) {
            return false;
        }

        $mask = umask();
        @umask(0);
        @chmod($newFilePath, $fileMask);
        @umask($mask);

        return true;
    }

    public function deleteFile($id) {
        $uploadDir = Configure::read('SimpleFileManager.uploadDir');

        $data = $this->findById($id);
        if(empty($data)) {
            return false;
        }

        $path_parts = pathinfo($data['SimpleUploadFile']['file_name']);
        $file_ext = mb_convert_case($path_parts['extension'], MB_CASE_LOWER);

        $filePath = $uploadDir . DS . $data['SimpleUploadFile']['id'] . '.' . $file_ext;

        if(!file_exists($filePath)) {
            return false;
        }

        if(!@unlink($filePath)) {
            return false;
        }

        return true;
    }

    public function validateUploadError($field) {
        $value = array_shift($field);

        if(!is_array($value)) {
            return false;
        }
        if(!array_key_exists('name', $value)
           || !array_key_exists('type', $value)
           || !array_key_exists('tmp_name', $value)
           || !array_key_exists('size', $value)) {
            return false;
        }

        if($value['error'] !== 0) {
            return false;
        }
        return true;
    }

    public function notEmptyFile($field) {
        $value = array_shift($field);
        if(empty($value['size'])) {
            return false;
        }
        return true;
    }

    public function validateFileSize($field, $fileSizeMax) {
        $value = array_shift($field);

        if($fileSizeMax === false) {
            return true;
        }

        if($value['size'] > $fileSizeMax) {
            return false;
        }
        return true;
    }

    public function validateExt($field, $allowedExt) {
        $value = array_shift($field);

        if(empty($allowedExt)) {
            return true;
        }

        if(!is_array($allowedExt)) {
            $allowedExt = array($allowedExt);
        }

        $path_parts = pathinfo($value['name']);
        $file_ext = mb_convert_case($path_parts['extension'], MB_CASE_LOWER);
        if(mb_strlen($file_ext) == 0) {
            return false;
        }

        foreach($allowedExt as $ext) {
            if($file_ext === $ext) {
                return true;
            }
        }

        return false;
    }
}
