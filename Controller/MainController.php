<?php

App::uses('SimpleFileManagerAppController', 'SimpleFileManager.Controller');
App::uses('Validation', 'Utility');

class MainController extends SimpleFileManagerAppController {

    public $layout = 'SimpleFileManager.default';

    public $uses = array(
        'SimpleFileManager.SimpleUploadFile',
    );

    public $components = array(
        'Session',
        'Paginator',
    );

    public $helpers = array(
        'Session',
        'Html' => array('className' => 'BoostCake.BoostCakeHtml'),
        'Form' => array('className' => 'BoostCake.BoostCakeForm'),
        'Paginator' => array('className' => 'BoostCake.BoostCakePaginator'),
    );

    public function beforeFilter() {
        parent::beforeFilter();

        $controlFlag = Configure::read('SimpleFileManager.controlFlag');
        if($controlFlag === true) {
            $controlSessionKey = Configure::read('SimpleFileManager.controlSessionKey');
            if(!$this->Session->check($controlSessionKey) || ($this->Session->read($controlSessionKey) === false)) {
                throw new NotFoundException();
            }
        }
    }

    public function index() {
        if($this->request->is('post') || $this->request->is('put')) {
            $db = $this->SimpleUploadFile->getDataSource();
            $db->begin();
            if($this->SimpleUploadFile->upload($this->request->data)) {
                $db->commit();
                $message = 'アップロードしました';
                $this->Session->setFlash(
                    $message,
                    'alert',
                    array(
                        'plugin' => 'BoostCake',
                        'class' => 'alert-success',
                    ),
                    'simple_file_manager'
                );
            } else {
                $db->rollback();
                $message = 'アップロードに失敗しました';
                $this->Session->setFlash(
                    $message,
                    'alert',
                    array(
                        'plugin' => 'BoostCake',
                        'class' => 'alert-danger',
                    ),
                    'simple_file_manager'
                );
            }
        }

        $settings = array(
            'order' => array(
                'SimpleUploadFile.id' => 'DESC',
            ),
            'limit' => Configure::read('SimpleFileManager.pageLimit'),
            'maxLimit' => Configure::read('SimpleFileManager.pageMaxLimit'),
        );

        $this->Paginator->settings = $settings;
        $data = $this->Paginator->paginate('SimpleUploadFile');
        $this->set(compact('data'));
    }

    public function delete($id = null) {
        if(!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        if(empty($id) || !Validation::naturalNumber($id)) {
            throw new NotFoundException();
        }
        if(!$this->SimpleUploadFile->exists($id)) {
            throw new NotFoundException();
        }
        $db = $this->SimpleUploadFile->getDataSource();
        $db->begin();
        if($this->SimpleUploadFile->deleteFile($id) && $this->SimpleUploadFile->delete($id)) {
            $db->commit();
            $message = '削除しました';
            $this->Session->setFlash(
                $message,
                'alert',
                array(
                    'plugin' => 'BoostCake',
                    'class' => 'alert-success',
                ),
                'simple_file_manager'
            );
        } else {
            $db->rollback();
            $message = '削除に失敗しました';
            $this->Session->setFlash(
                $message,
                'alert',
                array(
                    'plugin' => 'BoostCake',
                    'class' => 'alert-danger',
                ),
                'simple_file_manager'
            );
        }
        $this->redirect(array('action' => 'index'));
    }
}
