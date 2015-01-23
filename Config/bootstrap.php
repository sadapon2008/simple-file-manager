<?php

Configure::write('SimpleFileManager', array(
    'controlFlag' => false,
    'controlSessionKey' => 'SimpleFileManager.available',
    'uploadDir' => WWW_ROOT . 'upload',
    'uploadUrl' => 'upload',
    'uploadField' => 'file',
    'titleForLayout' => 'SimpleFileManager',
    'pageLimit' => 10,
    'pageMaxLimit' => 10,
    'fileMask' => 0666,
    'allowedExt' => array('jpg', 'jpeg', 'gif', 'png'),
    'fileSizeMax' => false,
));
