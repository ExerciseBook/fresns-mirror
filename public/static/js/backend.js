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

$(document).on('click', '.delete-connect', function() {
  $(this).parent().remove();
})
