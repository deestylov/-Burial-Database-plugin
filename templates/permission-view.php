<div class="row justify-between items-end pt-40">
              <div class="col-auto">
                <div class="row x-gap-20 y-gap-20 items-center">
                  <div class="col-auto">
                    <h1 class="text-26 fw-600"><?php echo $row['surname']; ?> <?php echo $row['name']; ?> <?php echo $row['patronymic']; ?></h1>
                  </div>
                </div>
                <div class="row x-gap-20 y-gap-20 items-center">
                  <div class="col-auto">
                    <div class="text-15 text-light-1">Регистрационный номер: <span class="text-22 text-dark-1 fw-500"><?php echo $row['registration_number']; ?></span></div>
                    <div class="text-15 text-light-1">Дата рождения: <span class="text-22 text-dark-1 fw-500"><?php echo $row['date_birth']; ?></span></div>
                    <div class="text-15 text-light-1">Дата смерти: <span class="text-22 text-dark-1 fw-500"><?php echo $row['date_death']; ?></span></div>
                    <div class="text-15 text-light-1">Дата захоронения: <span class="text-22 text-dark-1 fw-500"><?php echo $row['date_dburial']; ?></span></div>
                    <div class="text-15 text-light-1">Возраст: <span class="text-22 text-dark-1 fw-500"><?php echo $row['age']; ?></span></div>
                    <div class="text-15 text-light-1">Кладбище: <span class="text-22 text-dark-1 fw-500"><?php echo $row['cemetery_name']; ?></span></div>
                    <div class="text-15 text-light-1">Участок: <span class="text-22 text-dark-1 fw-500"><?php echo $row['site']; ?></span></div>
                    <div class="text-15 text-light-1">Ряд: <span class="text-22 text-dark-1 fw-500"><?php echo $row['row']; ?></span></div>
                    <?php if (!empty($row['grave'])) : ?>
                    <div class="text-15 text-light-1">Могила: <span class="text-22 text-dark-1 fw-500"><?php echo $row['grave']; ?></span></div>
                    <?php endif; ?>
                    <?php if (!empty($row['comment'])) : ?>
                    <div class="text-15 text-light-1">Комментарий: <span class="text-22 text-dark-1 fw-500"><?php echo $row['comment']; ?></span></div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
         </div>
         <div>
            <div class="px-30 py-30 border-light rounded-4">
              <!-- <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3Adc3c5dbb0dc3da8c0e3eeeb91e01218daf745bc435a5e2793ecbcc0f73ebb266&amp;source=constructor" width="100%" height="220" frameborder="0"></iframe> -->
                <?php 
        if (!empty($row['map'])) {
            echo stripcslashes($row['map']);
        } else {
            ?>
        <img src="<?php echo $row['map_cemetery'] ; ?>">
        <?php } 
            if (empty($row['map']) && empty($row['map_cemetery'])){
            echo "Карта захоронения не указана!";
            }
        ?>
              <div class="row y-gap-10">
                <div class="col-12">
                  <div class="d-flex items-center">
                    <!-- <i class="icon-pedestrian text-20 text-blue-1"></i>
                    <div class="text-14 fw-500 ml-10">Магадан, ул. Российская, 137/8</div> -->
                  </div>
                </div>
              </div> 
            </div>
          </div>






