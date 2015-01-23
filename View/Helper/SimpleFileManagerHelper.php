<?php

App::uses('SimpleFileManagerAppHelper', 'SimpleFileManager.View/Helper');

class SimpleFileManagerHelper extends SimpleFileManagerAppHelper {

    public function script() {
        $result = '
<script type="text/javascript">
  function fileBrowserCallBack(field_name, url, type, win) {
    browserField = field_name;
    browserWin = win;
    window.open(\'' . h(Router::url(array('plugin' => 'simple_file_manager', 'controller' => 'main', 'action' => 'index'))) . '\', \'browserWindow\', \'modal,width=800,height=400,scrollbars=yes\');
  }
</script>
';
        return $result;
    }

    public function getFileBrowserCallBack() {
        return 'fileBrowserCallBack';
    }
}
