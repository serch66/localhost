<div id="sync-block">
    <?php if (!\Qsoft\Committees\Controller\Sync::checkIfUploading()) {?>
        <input type="button" value="Начать импорт данных" id="sync_start" class="adm-btn-green">
    <?php } ?>
    <input type="button" value="Прервать" id="sync_cancel" class="adm-btn-red">
    <div id="progress"></div>
    <div id="notify"></div>
</div>
<script>
  new QSoft.Committees.Sync();
</script>
