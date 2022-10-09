<div class="wrap">
    <div class="wrap-form">
        <h1><?php echo $title; ?></h1>
        <form class="ritual__form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $action; ?>">
            <?php settings_errors($slug); ?>
            
            <div class="ritual__group">
                <label class="ritual__label">Наименование кладбища:</label>
                <input class="ritual__input" name="name" type="text" placeholder="Наименование кладбища" value="<?php echo $name; ?>" required="">
            </div>
            
             <div class="ritual__group">
                <label class="ritual__label">Широта и долгота (необязательно для заполнния):</label>
                <input class="ritual__input" name="iframe" type="text" placeholder="Пример: [55.967917881421215,37.18952619141869]" value="<?php echo $iframe; ?>">
            </div>
            
          
            <p>Для определении широты и долготы перейдите по ссылке <a target="_blank" href="https://yandex.ru/map-constructor/location-tool/">https://yandex.ru/map-constructor/location-tool/</a></p>
                        
            <button class="ritual__button button button-primary" type="submit">Сохранить запись</button>
        </form>
    </div>
</div>
