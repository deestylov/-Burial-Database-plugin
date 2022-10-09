
    <div class="tabs__pane -tab-item-1 is-tab-el-active">
        <div class="mainSearch -w-900 bg-white px-10 py-10 lg:px-20 lg:pt-5 lg:pb-20 rounded-100">
        <div class="button-grid items-center">

        <form id="ritual-search" class="" style=" display: contents; " @submit.prevent="onSubmit(false)">
        <div class="px-30 lg:py-20 lg:px-0">
            <div>
                <h4 class="text-15 fw-500 ls-2 lh-16">Фамилия</h4>
                    <div class="text-15 text-light-1 ls-2 lh-16">
                         <input v-model="surname" class="js-search js-dd-focus" type="text" placeholder="Введите фамилию" required>
                    </div>
            </div>
        </div>
        <div class="px-30 lg:py-20 lg:px-0">
            <div>
                <h4 class="text-15 fw-500 ls-2 lh-16">Имя</h4>
                <div class="text-15 text-light-1 ls-2 lh-16">
                 <input v-model="name" class="js-search js-dd-focus" type="text" placeholder="Имя умершего">
                </div>
            </div>
        </div> 
        <div class="px-30 lg:py-20 lg:px-0">
            <div>
                <h4 class="text-15 fw-500 ls-2 lh-16">Отчество</h4>
                <div class="text-15 text-light-1 ls-2 lh-16">
                    <input v-model="patronymic"class="js-search js-dd-focus" type="text" placeholder="Отчество">
                </div>
            </div>
        </div>  

        <div data-x-dd-click="searchMenu-loc">
                <h4 class="text-15 fw-500 ls-2 lh-16">Кладбище</h4>
                <div class="text-15 text-light-1 ls-2 lh-16">
                <select v-model="cemeteryName" class="filter__select">
                    <option value="0">Название</option>
                    <?php foreach($list_cemeteries as $item): ?>
                    <option value="<?php echo $item; ?>"><?php echo $item; ?></option>
                    <?php endforeach; ?>
                </select>
                </div>
            </div>
            <div class="button-item">
            <button class="mainSearch__submit button -dark-1 h-60 px-35 col-12 rounded-100 bg-blue-1 text-white" type="submit" value="Найти захоронение" onclick="document.getElementById('ritualResults').scrollIntoView({behavior: 'smooth',block: 'start'});"><i class="fa fa-search" aria-hidden="true"></i> Поиск</button>
            </div>
        </form>
    </div>
    </div>
    </div>



