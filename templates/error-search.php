<div class="wrap">
    <h2>Результат поиска</h2>
        
    <hr>
    
    <form class="filter__form" method="GET" action="<?php $_SERVER['PHP_SELF']?>">
        <input type="hidden" name="page" value="kw_ritual/kw_ritual.php">
        <input type="hidden" name="action" value="search">
        <div class="filter__group">
            <label class="filter__label">Фамилия/№:</label>
            <input class="filter__input" name="surname" type="text" placeholder="Фамилия/№" value="<?php echo $surname;?>" required>
        </div>
        
        <div class="filter__group">
            <label class="filter__label">Имя:</label>
            <input class="filter__input" name="name" type="text" placeholder="Имя" value="<?php echo $name;?>">
        </div>
        
        <div class="filter__group">
            <label class="filter__label">Отчество:</label>
            <input class="filter__input" name="patronymic" type="text" placeholder="Отчество" value="<?php echo $patronymic;?>">
        </div>
        
        <div class="filter__group">
            <label class="filter__label">Кладбище:</label>
            <select class="filter__select" name="cemetery_name">
                <option value="">Искать на всех кладбищах</option>
                <?php foreach($list_cemeteries as $item): ?>
                <?php if ($cemetery_name == $item): ?>
                <option value="<?php echo $item; ?>" selected><?php echo $item; ?></option>
                <?php else: ?>
                <option value="<?php echo $item; ?>"><?php echo $item; ?></option>
                <?php endif; ?>
                
                <?php endforeach; ?>
            </select>
        </div>
        
        <button class="filter__button button button-primary" type="submit" value="Найти захоронение">Найти захоронение</button>
    </form>
    
    <hr>
    <p>Число захоронений в базе: 0</p>
    <hr>
    <a class="button button-primary">Добавить запись</a>

</div>
