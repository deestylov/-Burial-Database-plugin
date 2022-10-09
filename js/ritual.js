jQuery(document).ready(function($) {
let indata = []

  var app = new Vue({
    el: '#app',
    data: {
      baseUrl: kw_script_data.baseUrl,
      surname: null,
      name: null,
      patronymic: null,
      cemeteryName: null,
      rows: [],
      pageNumber: 1,
      countRows: 0,
      limit: 0,
      totalPages: 0,
      pagination: []
    },
    methods: {
      onSubmit: function(scroll=false) {
        let self = this;
        
        $.ajax({
          url: kw_script_data.url,
          method: 'POST',
          dataType: 'json',
          data: {
            action: kw_script_data.action,
            surname: self.surname,
            name: self.name,
            patronymic: self.patronymic,
            cemetery_name: self.cemeteryName,
            page_number: self.pageNumber
          }, 
          success: function(data) {
            console.log("Data ----> ", data);
            self.rows = data['rows'];
            indata.push(data['rows']);
            self.countRows = data['count_rows'];
            self.limit = data['limit'];
            self.totalPages = Math.ceil(self.countRows / self.limit);
            if (data['count_rows'] > 0) {
              $('.ritualResults p').hide();
              self.makePagination();
              var offset = $('.ritualResults').offset().top - 50;
              $('html, body').animate({scrollTop: offset});
            } else {
              $('.ritualResults p').html( "Захоронений не найдено! Попробуйте изменить данные поиска.");
              swal("Захоронений не найдено!");
            }
            
          }
        });
      },
      makePagination: function() {

        this.pagination = [];
        
        if (this.pageNumber != 1) {
          this.pagination.push({pageNumber: 1, text: '<<'});
        }
        
        if (this.pageNumber != 1) {
          this.pagination.push({pageNumber: (this.pageNumber - 1), text: '<'});
        }
        
        for (let i = 5; i > 0; i--) {
          if (this.pageNumber - i > 0) {
            this.pagination.push({pageNumber: (this.pageNumber - i), text: (this.pageNumber - i)});
          }
          
        }
        
        this.pagination.push({pageNumber:  this.pageNumber, text: this.pageNumber});
        
        console.log(this.totalPages)
        for (let i = 1; i <= 5; i++) {
          if (this.pageNumber + i <= this.totalPages) {
            this.pagination.push({pageNumber: (this.pageNumber + i), text: (this.pageNumber + i)});
          }
          
        }
        
        if (this.pageNumber != this.totalPages) {
          this.pagination.push({pageNumber: (this.pageNumber + 1), text: '>'});
        }
        
        if (this.pageNumber != this.totalPages) {
          this.pagination.push({pageNumber: this.totalPages, text: '>>'});
        }
      },
      updateRows: function(pageNumber) {
        if (this.pageNumber == pageNumber) return;
        
        this.pageNumber = pageNumber;
                
        this.onSubmit(true);
      },
      generateURLMapImage: function(coords, width, height, zoom=15) {
        let arrCoords = JSON.parse(coords);;
        
        let url = 'https://static-maps.yandex.ru/1.x/?';
        url += `ll=${encodeURIComponent(arrCoords[1] + ',' + arrCoords[0])}`;
        url += `&z=${encodeURIComponent(zoom)}`;
        url += `&l=${encodeURIComponent('map')}`;
        url += `&pt=${encodeURIComponent(arrCoords[1] + ',' + arrCoords[0])}`;
        url += `&size=${encodeURIComponent(width + ',' + height)}`
        return url;
      },
      isJson(text) {
        try {
          console.log(text);
          JSON.parse(text);
        } catch (e) {
            return false;
        }
        return false;
      }
    }
    
    
  });
  
  



  
});
