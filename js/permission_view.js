jQuery(document).ready(function($) {
  $('.wz-open-modal').click(function (e) {
    e.preventDefault();
    var currentModal = $(this).attr('href');
    $(currentModal).fadeIn();
    $('body').append('<div class="wz-overlay"><div class="wz-close"></div></div>');
    
  });
  
  $(document).on('click', '.wz-overlay', function () {
    $('.wz-js-modal').fadeOut(function () {
      $('.wz-overlay').remove();
    });
  });
});
