# SimpleFileManager

## Example

### 1. create upload directory

```shell
$ mkdir app/webroot/upload
$ chmod a+w app/webroot/upload
```

### 2. load plugin

app/Config/bootstra.php:
```php
CakePlugin::load('Migrations');
CakePlugin::load('BoostCake');
CakePlugin::load('SimpleFileManager', array('bootstrap' => true));
```

### 3. create database table

```shell
$ ./app/Console/cake Migrations.migration run all -p SimpleFileManager
```

### 4. example controller & view

app/Controller/PostsController:
```php
<?php

App::uses('AppController');

class PostsController extends AppController {

  public $uses = array('Post');
  
  public $helpers = array('SimpleFileManager.SimpleFileManager');
  
  public function index() {
    if($this->request->is('post') || $this->request->is('put')) {
      $this->Post->create();
      $this->Post->save($this->request->data);
    }
  }
}
```
app/View/Posts/index.ctp:
```php
<?php $this->start('script'); ?>
<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
<script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>

<?php echo $this->SimpleFileManager->script(); ?>
<script type="text/javascript">
$(function() {

  tinymce.init({
    selector: 'textarea',
    plugins: ['image'],
    file_browser_callback: <?php echo $this->SimpleFileManager->getFileBrowserCallBack(); ?>,
    width: '620',
    height: '480',
    relative_urls : false
 });
});
</script>
<?php $this->end(); ?>

<?php echo $this->Form->create('Post'); ?>
<?php echo $this->Form->input('content', array('type' => 'textarea')); ?>
<?php echo $this->Form->end(); ?>
```
