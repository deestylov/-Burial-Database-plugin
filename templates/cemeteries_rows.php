<div class="wrap">
    <h2>Список кладбищ</h2>
    <?php settings_errors($slug); ?>
    
    <?php foreach ($messages as $message): ?>
    <div id="setting-error-notice" class="notice notice-<?php echo $message['type']; ?> settings-error is-dismissible"> 
        <p><strong><?php echo $message['text']; ?></strong></p>
    </div>
    <?php endforeach; ?>
        
    <hr>
    <p>Число кладбищ в базе: <?php echo $count_rows; ?></p>
    <hr>
    <a class="button button-primary" href="<?php $_SERVER['PHP_SELF']?>?page=<?php echo $_GET['page']; ?>&action=add">Добавить запись</a>
    <hr>
    
    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <th style="width: 30px;">No п/п</th>
                <th>Наименование</th>
                <th style="width: 30px;">&nbsp;</th>
                <th style="width: 30px;">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($rows as $row): ?>
            <tr>
               <td><?php echo $row['id']; ?></td>
               <td><?php echo $row['name']; ?></td>
               <td>
                   <a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $_GET['page']; ?>&action=edit&id=<?php echo $row['id']; ?>" title="Редактировать"><span class="dashicons dashicons-welcome-write-blog"></span></a>
                </td>
               <td><a class="delete" href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $_GET['page']; ?>&action=delete&id=<?php echo $row['id']; ?>" title="Удалить"><span class="dashicons dashicons-remove"></span></a>
               </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php echo $pagination->get(); ?>
</div>
