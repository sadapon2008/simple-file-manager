<div class="container">
  <div class="row">
    <div class="col-md-12">
      <?php echo $this->Session->flash('simple_file_manager'); ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <?php
        echo $this->Form->create(
          'SimpleUploadFile',
          array(
            'class' => 'well form-horizontal',
            'type' => 'file',
            'inputDefaults' => array(
              'div' => 'form-group',
              'label' => array(
                'class' => 'col col-md-3 control-label'
              ),
              'wrapInput' => 'col col-md-9',
              'class' => 'form-control',
            ),
          )
        );
      ?>
      <?php echo $this->Form->input(Configure::read('SimpleFileManager.uploadField'), array('type' => 'file', 'label' => array('text' => 'アップロードファイル'))); ?>
      <div class="form-group">
        <div class="col col-md-9 col-md-offset-3">
          <?php echo $this->Form->submit('アップロード', array('div' => false, 'class' => 'btn btn-primary')); ?>
        </div>
      </div>
      <?php echo $this->Form->end(); ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="clearfix">
        <div class="pull-left">
          <?php echo number_format((int)$this->Paginator->counter(array('format' => '%count%'))); ?>件の該当がありました
        </div>
        <div class="pull-left">
          (該当中
          <?php echo $this->Paginator->counter(array('format' => '%start%')); ?>-<?php echo $this->Paginator->counter(array('format' => '%end%')); ?>
          件を表示しています)
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th class="text-center" style="width: 10%;">ID</th>
            <th class="text-center" style="width: 40%;">画像</th>
            <th class="text-center" style="width: 20%;">元ファイル名</th>
            <th class="text-center" style="width: 20%;">ファイルサイズ(バイト)</th>
            <th class="text-center" style="width: 10%;">操作</th>
          </tr>
        </thead>
        <tbody>
<?php foreach($data as $value): ?>
<?php   $path_parts = pathinfo($value['SimpleUploadFile']['file_name']); ?>
<?php   $file_ext = mb_convert_case($path_parts['extension'], MB_CASE_LOWER); ?>
<?php   $url = Router::url('/', true) . Configure::read('SimpleFileManager.uploadUrl') . DS . $value['SimpleUploadFile']['id'] . '.' . $file_ext; ?>
          <tr>
            <td class="text-center"><?php echo $value['SimpleUploadFile']['id']; ?></td>
            <td>
              <img src="<?php echo h($url); ?>" width="100" height="100" alt="">
            </td>
            <td><?php echo $value['SimpleUploadFile']['file_name']; ?></td>
            <td class="text-right"><?php echo number_format($value['SimpleUploadFile']['file_size']); ?></td>
            <td class="text-center">
              <a href="#" class="select_file" data-app-url="<?php echo h($url); ?>">選択</a>
              &nbsp;&nbsp;
              <?php echo $this->Form->postLink('削除', array('action' => 'delete', $value['SimpleUploadFile']['id']), array(), '削除しますか？'); ?>
            </td>
          </tr>
<?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="col-md-12">
      <?php echo $this->Paginator->pagination(array('ul' => 'pagination')); ?>
    </div>
  </div>
</div>
<?php $this->start('script'); ?>
<script type="text/javascript">
$(function() {
  $('.select_file').each(function(i) {
    $(this).on('click', function(e) {
      var url = $(this).data().appUrl;
      field = window.top.opener.jQuery('#' + window.top.opener.browserField);
      $(field).val(url);
      if (field.onchange != null) field.onchange();
      window.top.close(); 
      window.top.opener.browserWin.focus();
      return false;
    });
  });
});
</script>
<?php $this->end(); ?>
