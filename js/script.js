jQuery(document).ready(function($) {
  
  function toBinary(string) {
    utf8Bytes = encodeURIComponent(string).replace(/%([0-9A-F]{2})/g, function (match, p1) {
      return String.fromCharCode('0x' + p1);
    });

    return btoa(utf8Bytes);
  }
  
  if (typeof $.datepicker !== 'undefined') {
  
    $.datepicker.setDefaults(
      $.extend(
        {'dateFormat':'dd.mm.yy'},
        $.datepicker.regional['ru']
      )
    );
    
    $('input[name="date_birth"], input[name="date_death"], input[name="date_dburial"]').datepicker();
  
  }
  
  $(document).on('click', '.delete', function(e) {
    e.preventDefault();
    
    let url = $(this).attr('href');
    
    swal({
      title: "Вы действительно хотите удалить?",
      text: "",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Удалить!",
      cancelButtonText: "Отменить!",
      closeOnConfirm: false,
      closeOnCancel: false
    },
    function(isConfirm){
      if (isConfirm) {
        window.location = url;
      } else {
        swal("Отменено", "", "error");
      }
    });
  });
  
  $(document).on('submit', '.form-import', function(e) {
    e.preventDefault();
    let rows = [];
    let countRows = 1000;
    let countIterations = 0;
    let counter = 0;
    
    function truncateRows() {
      $('.form-import__button').prop('disabled', true);
      $('div#log').html('');
      $('div#log').append('<div>Выполняется очитка базы</div>');
      $.ajax({
        url: kw_script_data.url,
        method: 'POST',
        dataType: 'json',
        data: {
          action: kw_script_data.action_truncate,
          truncate: 1
        },
        success: function(data) {
          $('div#log').append('<div>Выполняется интеграция данных в базу</div>');
          handleRows(counter);
          console.log("Data 1 --->>", data);
        }
      });
    }
    
    function handleRows() {
      
      if (counter >= countIterations) {
        $('div#log').append('<div>Выполняется генерация кладбищ</div>');
        handleCemeteries();
        return;
      };
      
      let slicedRows = rows.slice(counter * countRows, counter * countRows + countRows);

      $.ajax({
        url: kw_script_data.url,
        method: 'POST',
        dataType: 'json',
        data: {
          action: kw_script_data.action,
          sliced_rows: toBinary(JSON.stringify(slicedRows))
        },
        success: function(data) {
          counter++;
          console.log("Data 2 --->>", data);
          let progress = Math.floor(counter / countIterations * 100);
          
          $('#progress').text(progress + '%');
          $('#bar').css('width', progress + '%');
          handleRows();
        }
      });
    }
    
    function handleCemeteries() {
      $.ajax({
        url: kw_script_data.url,
        method: 'POST',
        dataType: 'json',
        data: {
          action: kw_script_data.action_cemeteries,
          cemeteries: 1
        },
        success: function(data) {
          console.log("Data 3 --->>", data);
          $('.form-import__button').prop('disabled', false);
          $('div#log').append('<div>Выполнено</div>');
          swal("Выполнено!", "Импорт данных успешно выполнено!", "success");
        }
      });
    }
    
    let file = $(this).find('input[type="file"]')[0].files[0];
    
    if (file == undefined) {
      swal("Ошибка!", "Выберете csv файл для интеграции!", "error");
      return;
    }
    
    let fileReader = new FileReader(); 
      fileReader.readAsText(file); 
      fileReader.onload = function() {
        let result = fileReader.result;
        rows = fileReader.result.split('\n');
        
        rows = rows.filter((item) => item.trim() != '')
        
        rows = rows.slice(1, rows.length);
        
        countIterations = Math.ceil(rows.length / countRows);             
        truncateRows();
      }; 
      fileReader.onerror = function() {
        console.log(fileReader.error);
      }; 
  });
});
