<div class="wrap">
    <div class="wrap-import">
        <h1>Импорт csv файла</h1>
        <hr>
        <?php settings_errors($slug); ?>
        <?php foreach ($messages as $message): ?>
        <div id="setting-error-notice" class="notice notice-<?php echo $message['type']; ?> settings-error is-dismissible"> 
            <p><strong><?php echo $message['text']; ?></strong></p>
        </div>
        <?php endforeach; ?>
        <p>Выполнено: <span id="progress">0%</span></p>
        <div id="progressbar">
            <div id="bar"></div>
        </div>
        <form class="form-import" method="POST" action="" enctype="multipart/form-data">
            <div class="form-import__group">
                <label for="import">Выберете csv файл</label>
                <input type="file" name="import" id="import">
            </div>
            <input type="submit" class="form-import__button button button-primarty" value="Загрузить">
        </form>
        
        <div id="log">
        
        </div>
    </div>
</div>
