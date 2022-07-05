<div id="sync-block">
    <input type="button" value="Начать импорт данных" id="sync_start" class="adm-btn-green">
    <input type="button" value="Прервать" id="sync_cancel" class="adm-btn-red">
    <input type="button" value="Продолжить" id="sync_continue" class="adm-btn-red">
    <div id="progress"></div>
    <div id="notify"></div>
</div>
<script>
  new QSoft.Personal.Sync();
</script>
