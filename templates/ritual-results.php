<div class="result__item" v-for="row in rows">
            <div class="result__description">
                <div>
                    <a  class="result__name" :href="baseUrl + '/permission-view/' + row.id" target="_blank">{{ row.surname }} {{ row.name }} {{ row.patronymic }}</a>
                </div>
                <div><b>Регистрационный номер1:</b> {{ row.registration_number }}</div>
                <div><b>Годы жизни:</b> {{ row.date_birth }} - {{ row.date_death }}</div>
                <div><b>Дата захоронения::</b> {{ row.date_dburial }}</div>
                <div><b>Кладбище:</b> {{ row.cemetery_name }}</div>
                <div><b>Участок:</b> {{ row.site }}</div>
                <div><b>Ряд:</b> {{ row.row }}</div>
                <div>
                    <a class="result__link mainSearch__submit button -dark-1 h-60 px-35 col-12 rounded-100 bg-blue-1 text-white" :href="baseUrl + '/permission-view/' + row.id" target="_blank">Страница захоронения</a>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.all.min.js"></script>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.min.css'>

    <div id="kw-pagination">
        <a v-for="item in pagination" @click.prevent="updateRows(item.pageNumber)" :class="{'active': pageNumber == item.pageNumber}" href="#">{{ item.text }}</a>
    </div>

