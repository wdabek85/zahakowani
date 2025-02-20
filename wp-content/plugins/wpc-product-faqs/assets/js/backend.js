'use strict';

(function($) {
  $(function() {
    wpcpf_arrange();
    wpcpf_select2();
    wpcpf_active_type();
    wpcpf_terms_init();
  });

  // type
  $(document).on('change', '.wpcpf_type', function() {
    wpcpf_active_type();
    wpcpf_terms_init();
  });

  // search terms
  $(document).on('change', '.wpcpf_terms_select', function() {
    var $this = $(this);
    var val = $this.val();
    var type = $('.wpcpf_type').val();

    $this.data(type, val.join());
  });

  $(document).on('click touch', '.wpcpf-add', function() {
    var wpcpf_new = $(this);
    var wpcpf_type = $('.wpcpf-new-faq-type').val();
    var wpcpf_editor = 'wpcpf_editor_' + Date.now().toString();

    wpcpf_new.prop('disabled', true);

    var data = {
      action: 'wpcpf_add_faq', editor: wpcpf_editor, type: wpcpf_type,
    };

    $.post(ajaxurl, data, function(response) {
      $('.wpcpf-faqs').append(response);

      if (wpcpf_type == 'custom') {
        wp.editor.initialize(wpcpf_editor, {
          mediaButtons: true, tinymce: {
            wpautop: true,
            plugins: 'charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview',
            toolbar1: 'formatselect bold italic | bullist numlist | blockquote | alignleft aligncenter alignright | link unlink | wp_more | spellchecker',
          }, quicktags: true,
        });
      }

      wpcpf_arrange();
      wpcpf_select2();
      wpcpf_new.prop('disabled', false);
    });
  });

  $(document).on('click touch', '.wpcpf-faq-remove', function() {
    var r = confirm('Do you want to remove this FAQ? This action cannot undo.');

    if (r == true) {
      $(this).closest('.wpcpf-faq').remove();
    }
  });

  function wpcpf_arrange() {
    $('.wpcpf-faqs').sortable({
      handle: '.wpcpf-faq-move',
    });
  }

  function wpcpf_select2() {
    $('.wpcpf-faq-search').selectWoo({
      ajax: {
        url: ajaxurl, dataType: 'json', delay: 250, data: function(params) {
          return {
            q: params.term, action: 'wpcpf_search_faq',
          };
        }, processResults: function(data) {
          var options = [];

          if (data) {
            $.each(data, function(index, text) {
              options.push({id: text[0], text: text[1]});
            });
          }

          return {
            results: options,
          };
        }, cache: true,
      }, minimumInputLength: 1,
    });
  }

  function wpcpf_terms_init() {
    var $terms = $('.wpcpf_terms_select');
    var type = $('.wpcpf_type').val();

    $terms.selectWoo({
      ajax: {
        url: ajaxurl, dataType: 'json', delay: 250, data: function(params) {
          return {
            q: params.term, action: 'wpcpf_search_term', taxonomy: type,
          };
        }, processResults: function(data) {
          var options = [];

          if (data) {
            $.each(data, function(index, text) {
              options.push({id: text[0], text: text[1]});
            });
          }

          return {
            results: options,
          };
        }, cache: true,
      }, minimumInputLength: 1,
    });

    if ((typeof $terms.data(type) === 'string' || $terms.data(type) instanceof
        String) && $terms.data(type) !== '') {
      $terms.val($terms.data(type).split(',')).change();
    } else {
      $terms.val([]).change();
    }
  }

  function wpcpf_active_type() {
    var type = $('select[name="wpcpf_type"]').val();

    $('.wpcpf_type_row').hide();
    $('.wpcpf_type_' + type).show();

    if (type !== 'all' && type !== 'none') {
      // terms
      $('.wpcpf_type_terms').show();
    }
  }
})(jQuery);