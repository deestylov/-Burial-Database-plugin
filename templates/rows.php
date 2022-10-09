<div class="wrap">
    <h2>Записи</h2>
    <?php settings_errors($slug); ?>
    
    <?php foreach ($messages as $message): ?>
    <div id="setting-error-notice" class="notice notice-<?php echo $message['type']; ?> settings-error is-dismissible"> 
        <p><strong><?php echo $message['text']; ?></strong></p>
    </div>
    <?php endforeach; ?>
        
    <hr>
    
    <form class="filter__form" method="GET" action="<?php $_SERVER['PHP_SELF']?>">
        <input type="hidden" name="page" value="<?php echo $slug; ?>">
        <input type="hidden" name="action" value="search">
        <div class="filter__group">
            <label class="filter__label">Фамилия/№:</label>
            <input class="filter__input" name="surname" type="text" placeholder="Фамилия/№">
        </div>
        
        <div class="filter__group">
            <label class="filter__label">Имя:</label>
            <input class="filter__input" name="name" type="text" placeholder="Имя">
        </div>
        
        <div class="filter__group">
            <label class="filter__label">Отчество:</label>
            <input class="filter__input" name="patronymic" type="text" placeholder="Отчество">
        </div>
        
        <div class="filter__group">
            <label class="filter__label">Кладбище:</label>
            <select class="filter__select" name="cemetery_name">
                <option value="0">Искать на всех кладбищах</option>
                <?php foreach($list_cemeteries as $item): ?>
                <option value="<?php echo $item; ?>"><?php echo $item; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button class="filter__button button button-primary" type="submit" value="Найти захоронение">Найти захоронение</button>
    </form>
    
    <hr>
    <p>Число захоронений в базе: <?php echo $count_rows; ?></p>
    <hr>
    <a class="button button-primary" href="<?php $_SERVER['PHP_SELF']?>?page=<?php echo $_GET['page']; ?>&action=add">Добавить запись</a>
    <hr>
    
    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <th style="width: 30px;">No п/п</th>
                <th>Регистрационный номер</th>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Отчество</th>
                <th>Кладбище</th>
                <th style="width: 30px;">&nbsp;</th>
                <th style="width: 30px;">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($rows as $row): ?>
            <tr>
               <td><?php echo $row['id']; ?></td>
               <td><?php echo $row['registration_number']; ?></td>
               <td><?php echo $row['surname']; ?></td>
               <td><?php echo $row['name']; ?></td>
               <td><?php echo $row['patronymic']; ?></td>
               <td><?php echo $row['cemetery_name']; ?></td>
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
