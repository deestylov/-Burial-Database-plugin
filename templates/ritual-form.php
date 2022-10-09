<div class="wrap">
    <div class="wrap-form">
        <h1><?php echo $title; ?></h1>
        <form class="ritual__form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $action; ?>">
            <?php settings_errors($slug); ?>
            
            <div class="ritual__group">
                <label class="ritual__label">Регистрационный номер:</label>
                <input class="ritual__input" name="registration_number" type="text" placeholder="Регистрационный номер" value="<?php echo $registration_number; ?>" required="">
            </div>
            
            <div class="ritual__group">
                <label class="ritual__label">Фамилия/№:</label>
                <input class="ritual__input" name="surname" type="text" placeholder="Фамилия/№" value="<?php echo $surname; ?>" required="">
            </div>
            
            <div class="ritual__group">
                <label class="ritual__label">Имя:</label>
                <input class="ritual__input" name="name" type="text" placeholder="Имя" value="<?php echo $name; ?>" required="">
            </div>
            
            <div class="ritual__group">
                <label class="ritual__label">Отчество:</label>
                <input class="ritual__input" name="patronymic" type="text" placeholder="Отчество" value="<?php echo $patronymic; ?>" required="">
            </div>
            
            <div class="ritual__group">
                <label class="ritual__label">Дата рождения:</label>
                <input class="ritual__input" name="date_birth" type="text" placeholder="Дата рождения" value="<?php echo $date_birth; ?>" required="">
            </div>
            
            <div class="ritual__group">
                <label class="ritual__label">Дата смерти:</label>
                <input class="ritual__input" name="date_death" type="text" placeholder="Дата смерти" value="<?php echo $date_death; ?>" required="">
            </div>
            
            <div class="ritual__group">
                <label class="ritual__label">Дата захоронения:</label>
                <input class="ritual__input" name="date_dburial" type="text" placeholder="Дата захоронения" value="<?php echo $date_dburial; ?>" required="">
            </div>
            
            <div class="ritual__group">
                <label class="ritual__label">Кладбище:</label>
                <select class="ritual__select" name="cemetery_name" required="">
                    <option value="" disabled selected>Выбрать кладбище</option>
                    <?php foreach($list_cemeteries as $item): ?>
                    <?php if ($cemetery_name == $item): ?>
                    <option value="<?php echo $item; ?>" selected><?php echo $item; ?></option>
                    <?php else: ?>
                    <option value="<?php echo $item; ?>"><?php echo $item; ?></option>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="ritual__group">
                <label class="ritual__label">Участок:</label>
                <input class="ritual__input" name="site" type="text" placeholder="Дата Участок" value="<?php echo $site; ?>" required="">
            </div>
            
            <div class="ritual__group">
                <label class="ritual__label">Ряд (необязательное поле):</label>
                <input class="ritual__input" name="row" type="text" placeholder="Ряд" value="<?php echo $row; ?>">
            </div>

            <div class="ritual__group">
                <label class="ritual__label">Могила (необязательное поле):</label>
                <input class="ritual__input" name="grave" type="text" placeholder="grave" value="<?php echo $grave; ?>">
            </div>

            <div class="ritual__group">
                <label class="ritual__label">Комментарий (необязательное поле):</label>
                <input class="ritual__input" name="comment" type="text" placeholder="Комментарий" value="<?php echo $comment; ?>">
            </div>

            <div class="ritual__group">
                <label class="ritual__label">Код персональной карты (Java Script) / (необязательно для заполнния):</label>
                <textarea class="ritual__input" name="map" rows="10" cols="45" placeholder="Код карты JS - Яндекс Карты" value="<?php echo $map ?>"><?php echo $map ?></textarea>
            </div>

            <button class="ritual__button button button-primary" type="submit">Сохранить запись</button>
        </form>
    </div>
</div>
