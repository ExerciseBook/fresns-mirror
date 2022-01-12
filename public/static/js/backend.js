/* Tooltips */
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

// set timeout toast hide
const setTimeoutToastHide = () => {
    $(".toast.show").each( (k, v) => {
        setTimeout(function () {
            $(v).hide();
        }, 1500);
    });
}
//setTimeoutToastHide();

// tips
window.tips = function (message, code = 200) {
    let html =
        `<div aria-live="polite" aria-atomic="true" class="position-fixed top-50 start-50 translate-middle" style="z-index:99">
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <img src="/static/images/fresns-icon.png" width="20px" height="20px" class="rounded me-2" alt="Fresns">
                    <strong class="me-auto">Fresns</strong>
                    <small>${ code }</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">${ message }</div>
            </div>
        </div>`
    $('div.fresns-tips').prepend(html);
    setTimeoutToastHide();
}


$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// update session key
$("#updateKey").on('show.bs.modal', function (e) {
  let button = $(e.relatedTarget),
    id = button.data('id'),
    name = button.data('name'),
    type = button.data('type'),
    isEnable = button.data('is_enable'),
    pluginUnikey = button.data('plugin_unikey'),
    action = button.data('action'),
    platformId = button.data('platform_id');

  $(this).find('form').attr('action', action);
  $(this).find('#key_platform').val(platformId);
  $(this).find('#key_name').val(name);
  $(this).find('input:radio[name=type][value="'+type+'"]').prop('checked', true).click();
  $(this).find('input:radio[name=is_enable][value="'+isEnable+'"]').prop('checked', true);
  $(this).find('#key_plugin').val(pluginUnikey);
});

// reset session key
$("#resetKey").on('show.bs.modal', function (e) {
  let button = $(e.relatedTarget),
    appId = button.data('app_id'),
    action = button.data('action');

  $(this).find('form').attr('action', action);
  $(this).find('.app-id').text(appId);
});

// delete session key
$("#deleteKey").on('show.bs.modal', function (e) {
  let button = $(e.relatedTarget),
    appId = button.data('app_id'),
    action = button.data('action');

  $(this).find('form').attr('action', action);
  $(this).find('.app-id').text(appId);
});

// set default language
$('input[name="default_language"]').change(function() {
  $.ajax({
    method:'post',
    url: $(this).data('action'),
    data: {
      default_language: $(this).val(),
      _method: 'put',
    },
    success:function(response){
      window.tips(response.message)
    }
  });
});

$('input.rank-num').change(function() {
  $.ajax({
    method:'post',
    url: $(this).data('action'),
    data: {
      rank_num: $(this).val(),
      _method: 'put',
    },
    success:function(response){
      window.tips(response.message)
    }
  });
});

// update language menu
$("#updateLanguageMenu").on('show.bs.modal', function (e) {
  let button = $(e.relatedTarget),
    language = button.data('language'),
    action = button.data('action');

  let status = language.areaStatus == 'true' ? 1 : 0;
  let isEnable = language.isEnable == 'true' ? 1 : 0;

  $(this).find('form').attr('action', action);
  $(this).find('input[name=rank_num]').val(language.rankNum);
  $(this).find('input[name=old_lang_tag]').val(language.langTag);
  $(this).find('select[name=lang_code]').val(language.langCode);
  $(this).find('input:radio[name=area_status][value="'+status+'"]').prop('checked', true).click();
  $(this).find('select[name=continent_id]').val(language.continentId);
  $(this).find('select[name=area_code]').val(language.areaCode);
  $(this).find('select[name=length_units]').val(language.lengthUnits);
  $(this).find('select[name=date_format]').val(language.dateFormat);
  $(this).find('input[name=time_format_minute]').val(language.timeFormatMinute);
  $(this).find('input[name=time_format_hour]').val(language.timeFormatHour);
  $(this).find('input[name=time_format_day]').val(language.timeFormatDay);
  $(this).find('input[name=time_format_month]').val(language.timeFormatMonth);
  $(this).find('input:radio[name=is_enable][value="'+isEnable+'"]').prop('checked', true).click();

});

// update language
$("#updateLanguage").on('show.bs.modal', function (e) {
  let button = $(e.relatedTarget),
    langTag = button.data('lang_tag'),
    langTagDesc = button.data('lang_tag_desc'),
    content = button.data('content'),
    action = button.data('action');

  $(this).find('form').attr('action', action);
  $(this).find('.lang-label').text(langTagDesc);
  $(this).find('input[name=lang_tag]').val(langTag);
  $(this).find('textarea[name=content]').val(content);
});

// user connect
$('#addConnect').click(function() {
  let template = $('#connectTemplate');
  $('.connect-box').append(template.html());
});

// use config delete  connect
$(document).on('click', '.delete-connect', function() {
  $(this).parent().remove();
})

// map config
$('#createMap').on('show.bs.modal', function(e) {
  if ($(this).data('is_back')) {
    return;
  }

  let button = $(e.relatedTarget);
  let action = button.data('action');
  let params = button.data('params');

  $(this).parent('form').attr('action', action);

  $(this).parent('form').find('input[name=_method]').val(params ? 'put' : 'post');

  if (!params) {
    $(this).parent('form').trigger("reset");
    return;
  }
  let configParams = button.data('config_params');
  let languages = button.data('languages');
  $(this).data('languages', languages);

  $(this).find('input[name=rank_num]').val(params.rank_num);
  $(this).find('select[name=plugin_unikey]').val(params.plugin_unikey)
  $(this).find('select[name=parameter]').val(params.parameter)
  $(this).find('input[name=app_id]').val(configParams.appId);
  $(this).find('input[name=app_key]').val(configParams.appKey);
  $(this).find('input:radio[name=is_enable][value="'+params.is_enable+'"]').prop('checked', true).click();
});

$('#createMap').on('hide.bs.modal', function(e) {
  $(this).data('is_back', false)
});

$("#mapLangModal").on('show.bs.modal', function (e) {
  let button = $(e.relatedTarget);

  var parent = button.data('parent');
  if (!parent) {
    return;
  }

  let languages = $(parent).data('languages');

  var $this = $(this)
  if (languages) {
    languages.map(function(language, index) {
      $this.find("input[name='languages["+language.lang_tag+"]'").val(language.lang_content);
    });
  }

  $(this).on('hide.bs.modal', function (e) {
    if (parent) {
      $(parent).data('is_back', true)
      $(parent).modal('show');
    }
  });
});

$('#configLangModal').on('show.bs.modal', function(e) {
  let button = $(e.relatedTarget),
    languages = button.data('languages'),
    itemKey = button.data('item_key'),
    action = button.data('action');

  $(this).find('form').attr('action', action);
  $(this).find('input[name=update_config]').val(itemKey);

  if (languages) {
    let $this = $(this);
    languages.map(function(language, index) {
      $this.find("input[name='languages["+language.lang_tag+"]'").val(language.lang_content);
    });
  }
});

// 通用处理，名称多语言 start
$('.name-lang-parent').on('show.bs.modal', function(e) {
  if ($(this).data('is_back')) {
    return;
  }

  let button = $(e.relatedTarget);
  let action = button.data('action');
  let params = button.data('params');
  let names = button.data('names');

  $(this).parent('form').attr('action', action);

  $(this).parent('form').find('input[name=_method]').val(params ? 'put' : 'post');

  if (!params) {
    $(this).parent('form').trigger("reset");
    $(this).find('.name-button').text('名称');
    return;
  }

  if (names) {
    names.map((name, index) => {
      $(this).parent('form').find("input[name='names["+name.lang_tag+"]'").val(name.lang_content);
    });
  }
});

$('.name-lang-parent').on('hide.bs.modal', function(e) {
  $(this).data('is_back', false)
});

$(".name-lang-modal").on('show.bs.modal', function (e) {
  if ($(this).data('is_back')) {
    return;
  }

  let button = $(e.relatedTarget);
  var parent = button.data('parent');
  if (!parent) {
    return;
  }

  var $this = $(this)
  $(this).on('hide.bs.modal', function (e) {
    if (parent) {
      $(parent).data('is_back', true)
      $(parent).modal('show');
    }
  });
});
// 通用处理，名称多语言 end


$('#emojiGroupCreateModal').on('show.bs.modal', function(e) {
  let button = $(e.relatedTarget);
  let params = button.data('params');

  if (!params) {
    return;
  }

  $(this).find('input[name=rank_num]').val(params.rank_num);
  $(this).find('input[name=code]').val(params.code);
  $(this).find('.name-button').text(params.name);
  $(this).find('input:radio[name=is_enable][value="'+params.is_enable+'"]').prop('checked', true).click();
});

$('#offcanvasEmoji').on('show.bs.offcanvas', function(e) {
  let button = $(e.relatedTarget);
  let emojis = button.data('emojis');
  $('#emojiList').empty();

  if (!emojis) {
    return;
  }

  let template = $('#emojiData').contents();
  emojis.map((emoji) => {
    let emojiTemplate = template.clone();
    emojiTemplate.find('input[name=rank_num]').val(emoji.rank_num);
    emojiTemplate.find('.emoji-img').attr('src', emoji.image_file_url);
    emojiTemplate.find('.emoji-code').html(emoji.code);
    if (emoji.is_enable) {
      emojiTemplate.find('input[name=is_enable]').attr('checked', 'checked');
    }
    $('#emojiList').append(emojiTemplate);
  });
});


$('#createStopWordModal').on('show.bs.modal', function(e) {
  let button = $(e.relatedTarget);
  let params = button.data('params');
  let action = button.data('action');

  $(this).find('form').attr('action', action);
  $(this).find('form').find('input[name=_method]').val(params ? 'put' : 'post');

  if (!params) {
    $(this).find('form').trigger("reset");
    return;
  }

  $(this).find('input[name=word]').val(params.word);
  $(this).find('input[name=replace_word]').val(params.replace_word);
  $(this).find('select[name=content_mode]').val(params.content_mode);
  $(this).find('select[name=member_mode]').val(params.member_mode);
  $(this).find('select[name=dialog_mode]').val(params.dialog_mode);
});

$('#createRoleModal').on('show.bs.modal', function(e) {
  let button = $(e.relatedTarget);
  let params = button.data('params');

  if (!params) {
    return;
  }

  $(this).find('select[name=type]').val(params.type);
  $(this).find('input[name=rank_num]').val(params.rank_num);
  $(this).find('input[name=name]').val(params.name);
  if (params.is_display_name) {
    $(this).find('input[name=is_display_name]').attr('checked', 'checked');
  }
  if (params.is_display_icon) {
    $(this).find('input[name=is_display_icon]').attr('checked', 'checked');
  }

  if (params.nickname_color) {
    $(this).find('input[name=nickname_color]').val(params.nickname_color);
  } else {
    $(this).find('input[name=no_color]').attr('checked', 'checked');
  }
  $(this).find('input:radio[name=is_enable][value="'+params.is_enable+'"]').prop('checked', true).click();
});
