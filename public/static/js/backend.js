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
setTimeoutToastHide();

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

$(document).ready(function() {
  $('.select2').select2({
    theme: "bootstrap-5",
  });
});

function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();

  window.tips('已复制');
}

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$('#fresnsUpgrade').click(function() {
  console.log(123);
  $.ajax({
    method:'post',
    url: $(this).data('action'),
    success:function(response){
      window.tips(response.message)
    }
  });
});

$('.preview-image').click(function() {
  let url = $(this).siblings('.inputUrl').val();
  $('#imageZoom').find('img').attr('src', url);
  $('#imageZoom').modal('show');
  console.log(123);
})

$('#adminConfig .update-backend-url').change(function() {
  let domain = $('#adminConfig').find('input[name=domain]').val();
  let path = $('#adminConfig').find('input[name=path]').val();
  $('#adminConfig').find('#backendUrl').text(domain + '/fresns/' + path);
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
    name = button.data('name'),
    action = button.data('action');

  $(this).find('form').attr('action', action);
  $(this).find('.app-id').text(appId);
  $(this).find('.modal-title').text(name)
});

// delete session key
$("#deleteKey").on('show.bs.modal', function (e) {
  let button = $(e.relatedTarget),
    appId = button.data('app_id'),
    name = button.data('name'),
    action = button.data('action');

  $(this).find('form').attr('action', action);
  $(this).find('.app-id').text(appId);
  $(this).find('.modal-title').text(name)
});

$('.select-continent').change(function() {
  let areas = $(this).data('children');
  let continent = $(this).val();
  areas = areas.filter(area => {
    if (area.continentId == continent) {
      return true;
    }
    return false;
  });

  let childrenSelect = $(this).next();
  childrenSelect.find('option').remove();

  areas.map(area => {
    childrenSelect.append('<option value="'+area.code+'">'+area.name+'</option>')
  });
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

$(document).on('click', 'input.rank-num', function() {
  return false;
});

$(document).on('change', 'input.rank-num', function() {
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
  return false;
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

  let continentSelect = $(this).find('select[name=continent_id]');
  continent = language.continentId
  let areas = continentSelect.data('children');
  areas = areas.filter(area => {
    if (area.continentId == continent) {
      return true;
    }
    return false;
  });

  let childrenSelect = continentSelect.next();
  childrenSelect.find('option').remove();

  areas.map(area => {
    childrenSelect.append('<option value="'+area.code+'">'+area.name+'</option>')
  });

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

$('#updateLanguageForm').submit(function() {
  $('#updateLanguage').modal('hide');
  let content =  $(this).find('textarea[name=content]').val();
  let langTag = $(this).find('input[name=lang_tag]').val();
  $.ajax({
    method:'post',
    url: $(this).attr('action'),
    data: {
      lang_tag: langTag,
      content: content,
      _method: 'put',
    },
    success:function(response){
      $('#policyTabContent').find("[data-lang_tag='" + langTag+ "']").data('content', content)
      window.tips(response.message)
    }
  });
  return false;
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
  let params = button.data('params');

  $(this).parent('form').trigger("reset");
  let configParams = button.data('config_params');

  if(params.icon_file_url){
	  $(this).find('input[name=icon_file_url]').val(params.icon_file_url);
	  $(this).find('input[name=icon_file_url]').removeAttr('style');
	   $(".showSelectTypeName").text('图片地址');
	  $(".inputFile").css('display','none');
  }
  $(this).find('input[name=rank_num]').val(params.rank_num);
  $(this).find('select[name=plugin_unikey]').val(params.plugin_unikey)
  $(this).find('select[name=parameter]').val(params.parameter)
  $(this).find('input[name=app_id]').val(configParams.appId);
  $(this).find('input[name=app_key]').val(configParams.appKey);
  $(this).find('input:radio[name=is_enable][value="'+params.is_enable+'"]').prop('checked', true).click();
});

$('#configLangModal').on('show.bs.modal', function(e) {
  let button = $(e.relatedTarget),
    languages = button.data('languages'),
    itemKey = button.data('item_key'),
    action = button.data('action');

  $(this).find('form').attr('action', action);
  $(this).find('form').trigger("reset");
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
  $(this).parent('form').find('input[name=update_name]').val(0);
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
      $this.parent('form').find('input[name=update_name]').val(1);
      $(parent).modal('show');
    }
  });
});

$(".description-lang-modal").on('show.bs.modal', function (e) {
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
      $this.parent('form').find('input[name=update_description]').val(1);
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

  $(".inputUrl").css('display','none');
  $(".inputFile").removeAttr('style');
  $(".showSelectTypeName").text('上传图片');
  if(params.image_file_url){
    $(this).find('input[name=image_file_url]').val(params.image_file_url);
    $(this).find('input[name=image_file_url]').removeAttr('style');
    $(".showSelectTypeName").text('图片地址');
    $(".inputFile").css('display','none');
  } else {
    $(this).find('input[name=image_file_url]').val('');
  }

  $(this).find('input[name=rank_num]').val(params.rank_num);
  $(this).find('input[name=code]').val(params.code);
  $(this).find('.name-button').text(params.name);
  $(this).find('input:radio[name=is_enable][value="'+params.is_enable+'"]').prop('checked', true).click();
});

$('#offcanvasEmoji').on('show.bs.offcanvas', function(e) {
  let button = $(e.relatedTarget);
  let emojis = button.data('emojis');
  let parent_id = button.data('parent_id');

  $('#emojiList').empty();
  $(this).parent('form').find('input[name=parent_id]').val(parent_id)
  $('#offcanvasEmojiLabel button').data('parent_id', parent_id)

  if (!emojis) {
    return;
  }
  let template = $($('#emojiData').html());

  emojis.map((emoji) => {
    let emojiTemplate = template.clone();

    emojiTemplate.find('input.emoji-rank').attr('name', 'rank_num['+emoji.id+']').val(emoji.rank_num);

    emojiTemplate.find('.emoji-img').attr('src', emoji.image_file_url);
    emojiTemplate.find('.emoji-code').html(emoji.code);

    emojiTemplate.find('input.emoji-enable').attr('name', 'enable['+emoji.id+']');
    if (emoji.is_enable) {
      emojiTemplate.find('input.emoji-enable').attr('checked', 'checked');
    }
    $('#emojiList').append(emojiTemplate);
  });
});


$('#emojiModal').on('show.bs.modal', function(e) {
  let button = $(e.relatedTarget);
  let parent_id = button.data('parent_id');

  $(this).find('input[name=parent_id]').val(parent_id);
});

$(document).on('click', '.delete-emoji', function() {
  $(this).closest('tr').remove();
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

// memberROles
$('#emptyColor').change(function() {
  if ($(this).is(':checked')) {
    $('.choose-color').hide();
  } else {
    $('.choose-color').show();
  }
});

$('#createRoleModal').on('show.bs.modal', function(e) {
  let button = $(e.relatedTarget);
  let params = button.data('params');

  $('.choose-color').show();

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

  $(".inputUrl").css('display','none');
  $(".inputFile").removeAttr('style');
  $(".showSelectTypeName").text('上传图片');
  if(params.icon_file_url){
    $(this).find('input[name=icon_file_url]').val(params.icon_file_url);
    $(this).find('input[name=icon_file_url]').removeAttr('style');
    $(".showSelectTypeName").text('图片地址');
    $(".inputFile").css('display','none');
  } else {
    $(this).find('input[name=icon_file_url]').val('');
  }

  if (params.nickname_color) {
    $(this).find('input[name=nickname_color]').val(params.nickname_color);

    $('.choose-color').show();
    $(this).find('input[name=no_color]').prop('checked', false);
  } else {
    $('.choose-color').hide();
    $(this).find('input[name=no_color]').prop('checked', true);
  }
  $(this).find('input:radio[name=is_enable][value="'+params.is_enable+'"]').prop('checked', true).click();
});

$('#deleteRoleModal').on('show.bs.modal', function(e){
  let button = $(e.relatedTarget);
  let action = button.data('action');
  let params = button.data('params');

  $(this).find('form').attr('action', action);

  $(this).find('input[name=name]').val(params.name);
  $(this).find('#chooseRole').children('.role-option').prop('disabled', false);
  $(this).find('#chooseRole').find('option[value='+params.id+']').prop('disabled', true);
});

$('#addCustomPerm').click(function(){
  console.log(123);
  let template = $('#customPerm').clone();
  console.log(template);
  $('#addCustomPermTr').before(template.contents());
});

$('.delete-custom-perm').click(function(){
  $(this).closest('tr').remove();
});

// config post select
$("#post_limit_type").change(function(){
	var value = $("#post_limit_type  option:selected").val();
	if(value == 1){
		$('#post_time_setting').css('display','none');
		$('#post_date_setting').removeAttr('style');
	}else{
		$('#post_time_setting').removeAttr('style');
		$('#post_date_setting').css('display','none');
	}
});

$("#comment_limit_type").change(function(){
	var value = $("#comment_limit_type  option:selected").val();
	if(value == 1){
		$('#comment_time_setting').css('display','none');
		$('#comment_date_setting').removeAttr('style');
	}else{
		$('#comment_time_setting').removeAttr('style');
		$('#comment_date_setting').css('display','none');
	}
});

// plugin setting
$('.uninstall-plugin').click(function(){
  $.ajax({
    method:'post',
    url: $(this).data('action'),
    data: {
      _method: 'delete',
    },
    success:function(response){
      window.tips(response.message)
      location.reload();
    }
  });
});

$('.plugin-update').click(function(){
  $.ajax({
    method:'post',
    url: $(this).data('action'),
    data: {
      _method: 'patch',
      is_enable: $(this).data('enable')
    },
    success:function(response){
      window.tips(response.message)
      location.reload();
    }
  });
});

$('#themeSetting').on('show.bs.modal', function(e){
  let button = $(e.relatedTarget);
  let action = button.data('action');
  let params = button.data('params');
  let pcPlugin= button.data('pc_plugin');
  let mobilePlugin= button.data('mobile_plugin');


  $(this).find('form').attr('action', action);
  $(this).find('#pcTheme').attr('name', params.unikey + '_Pc');
  $(this).find('#mobileTheme').attr('name', params.unikey + '_Mobile');

  $(this).find('#pcTheme').val(pcPlugin);
  $(this).find('#mobileTheme').val(mobilePlugin);

});

// change default homepage
$('.update-config').change(function() {
  $.ajax({
    method:'post',
    url: $(this).data('action'),
    data: {
      _method: 'put',
      item_value: $(this).data('item_value')
    },
    success:function(response){
      window.tips(response.message)
    }
  });
});

// menu edit
$('#menuEdit').on('show.bs.modal', function(e) {
  let button = $(e.relatedTarget),
    isEnable = button.data('is_enable'),
    noConfig = button.data('no_config');
    action = button.data('action');
    config = button.data('config');

  if (noConfig) {
    $(this).find('.default-config').hide();
  } else {
    $(this).find('.default-config').show();
  }

  $(this).find('form').attr('action', action);
  $(this).find('textarea[name=config]').val(JSON.stringify(config));
  $(this).find('input:radio[name=is_enable][value="'+isEnable+'"]').prop('checked', true);
});

$('#menuLangModal').on('shown.bs.modal', function (e) {
  let button = $(e.relatedTarget),
  languages = button.data('languages'),
  action = button.data('action');

  $(this).find('form').trigger("reset");
  $(this).find('form').attr('action', action);

  if (languages) {
    languages.map((language, index) => {
      $(this).find("input[name='languages["+language.lang_tag+"]'").val(language.lang_content);
    });
  }
});
$('#menuLangTextareaModal').on('shown.bs.modal', function (e) {
  let button = $(e.relatedTarget),
  languages = button.data('languages'),
  action = button.data('action');

  $(this).find('form').trigger("reset");
  $(this).find('form').attr('action', action);

  if (languages) {
    languages.map((language, index) => {
      $(this).find("textarea[name='languages["+language.lang_tag+"]'").val(language.lang_content);
    });
  }
});

$(document).on('click', '.delete-lang-pack', function() {
  $(this).closest('tr').remove();
})

$('#addLangPack').click(function() {
  let template = $('#languagePackTemplate')
  $('.lang-pack-box').append(template.html());
});

// wallet pay
$('.wallet-modal').on('show.bs.modal', function(e) {
  let button = $(e.relatedTarget);
  let params = button.data('params');

  if (!params) {
    return;
  }

  $(".inputUrl").css('display','none');
  $(".inputFile").removeAttr('style');
  $(".showSelectTypeName").text('上传图片');
  if(params.icon_file_url){
    $(this).find('input[name=icon_file_url]').val(params.icon_file_url);
    $(this).find('input[name=icon_file_url]').removeAttr('style');
    $(".showSelectTypeName").text('图片地址');
    $(".inputFile").css('display','none');
  } else {
    $(this).find('input[name=icon_file_url]').val('');
  }
  $(this).find('input[name=rank_num]').val(params.rank_num);
  $(this).find('select[name=plugin_unikey]').val(params.plugin_unikey);
  $(this).find('input[name=parameter]').val(params.parameter);
  if (params.name) {
    $(this).find('.name-button').text(params.name);
  }
  $(this).find('input:radio[name=is_enable][value="'+params.is_enable+'"]').prop('checked', true).click();
});

// group edit
$('.edit-group-category').click(function() {
  let action = $(this).data('action');
  let params = $(this).data('params');

  $('#createCategoryModal').parent('form').find('input[name=_method]').val(params ? 'put' : 'post');
  $('#createCategoryModal').parent('form').attr('action', action);
  $('#createCategoryModal').parent('form').trigger("reset");

  if (params) {
    $('#createCategoryModal').find('input[name=rank_num]').val(params.rank_num)
    $('#createCategoryModal').find('input:radio[name=is_enable][value="'+params.is_enable+'"]').prop('checked', true);
	  $(".showSelectTypeName").text('图片地址');
	  $(".inputFile").css('display','none');
	  $('#createCategoryModal').find('input[name=cover_file_url]').val(params.cover_file_url);
	  $('#createCategoryModal').find('input[name=cover_file_url]').css('display','block');
	  $('#createCategoryModal').find('input[name=banner_file_url]').val(params.banner_file_url);
	  $('#createCategoryModal').find('input[name=banner_file_url]').css('display','block');

    let names = $(this).data('names');
    let descriptions = $(this).data('descriptions');
    if ( names ) {
      names.map(name => {
        $('#createCategoryModal').parent('form').find("input[name='names["+name.lang_tag+"]'").val(name.lang_content);
      });
    }
    if ( descriptions) {
      descriptions.map(description=> {
        $('#createCategoryModal').parent('form').find("textarea[name='descriptions["+description.lang_tag+"]'").val(description.lang_content);
      });
    }
  }
  $('#createCategoryModal').modal('show');

  return false;
});

$('.delete-group-category').click(function() {
  $.ajax({
    method:'post',
    url: $(this).data('action'),
    data: {
      _method: 'delete',
    },
    success:function(response){
      window.tips(response.message)
      location.reload();
    }
  });
  return false;
});

$('#moveModal').on('shown.bs.modal', function(e) {
  let button = $(e.relatedTarget);
  let action = button.data('action');

  $(this).find('form').attr('action', action);
});

$('#groupModal').on('shown.bs.modal', function(e) {
  if ($(this).data('is_back')) {
    return;
  }
  let button = $(e.relatedTarget);
  let action = button.data('action');
  let params = button.data('params');

  let form = $(this).parents('form');

  form.attr('action', action);

  form.find('select[name=parent_id]').val(params.parent_id);
  form.find('input[name=rank_num]').val(params.rank_num);
  form.find('select[name=plugin_unikey]').val(params.plugin_unikey);

  let names = button.data('names');
  let descriptions = button.data('descriptions');
  $(".showSelectTypeName").text('图片地址');
  $(".inputFile").css('display','none');
  form.find('input[name=cover_file_url]').val(params.cover_file_url);
  form.find('input[name=cover_file_url]').css('display','block');
  form.find('input[name=banner_file_url]').val(params.banner_file_url);
  form.find('input[name=banner_file_url]').css('display','block');
  form.find('input:radio[name=type_mode][value="'+params.type_mode+'"]').prop('checked', true).click();

  form.find('select[name=plugin_unikey]').val(params.plugin_unikey);
  form.find('select[name="permission[admin_members][]"]').select2('val', params.permission.admin_members);
  form.find('input:radio[name=type_find][value="'+params.type_find+'"]').prop('checked', true).click();
  form.find('input:radio[name=type_follow][value="'+params.type_follow+'"]').prop('checked', true).click();
  form.find('input:radio[name=is_recommend][value="'+params.is_recommend+'"]').prop('checked', true).click();

  let permission = params.permission;
  let publishPost = permission.publish_post ? 1 : 0;
  let publishPostReview = permission.publish_post_review ? 1 : 0;
  let publishComment = permission.publish_comment ? 1 : 0;
  let publishCommentReview = permission.publish_comment_review ? 1 : 0;

  form.find('input:radio[name="permission[publish_post]"][value="'+publishPost+'"]').prop('checked', true).click();
  form.find('input:radio[name="permission[publish_post_review]"][value="'+publishPostReview+'"]').prop('checked', true).click();
  form.find('input:radio[name="permission[publish_comment]"][value="'+publishComment+'"]').prop('checked', true).click();
  form.find('input:radio[name="permission[publish_comment_review]"][value="'+publishCommentReview+'"]').prop('checked', true).click();

  form.find('select[name="permission[publish_post_roles][]"]').select2('val', params.permission.publish_post_roles);
  form.find('select[name="permission[publish_comment_roles][]"]').select2('val', params.permission.publish_comment_roles);
  form.find('input:radio[name=is_enable][value="'+params.is_enable+'"]').prop('checked', true).click();

  if ( names ) {
    names.map(name => {
      form.find("input[name='names["+name.lang_tag+"]'").val(name.lang_content);
    });
  }
  if ( descriptions) {
    descriptions.map(description=> {
      form.find("textarea[name='descriptions["+description.lang_tag+"]'").val(description.lang_content);
    });
  }
});

//expend-edit-modal
$('.expend-edit-modal').on('show.bs.modal', function(e) {
  let button = $(e.relatedTarget);
  let params = button.data('params');

  if (!params) {
    return;
  }
  $("#inlineCheckbox1").removeAttr("checked");
  $("#inlineCheckbox2").removeAttr("checked");
  $(this).find('input[name=rank_num]').val(params.rank_num);
  $(this).find('select[name=plugin_unikey]').val(params.plugin_unikey);
  $(this).find('input[name=parameter]').val(params.parameter);
  $(this).find('input[name=editor_number]').val(params.editor_number);

  $(".inputUrl").css('display','none');
  $(".inputFile").removeAttr('style');
  $(".showSelectTypeName").text('上传图片');
  if(params.icon_file_url){
	  $(this).find('input[name=icon_file_url]').val(params.icon_file_url);
	  $(this).find('input[name=icon_file_url]').removeAttr('style');
	   $(".showSelectTypeName").text('图片地址');
	  $(".inputFile").css('display','none');
  }

  if(params.member_roles){
	with (document.getElementById('member_roles')) {
	    for (var i=0; i<options.length; i++) {
	        options[i].selected = (','+params.member_roles+',').indexOf(','+options[i].value+',')>-1;
	    }
	}
  }
  scene = params.scene.split(",");
  for (var i=0; i<scene.length; i++) {
	if(scene[i] == 1){
 		$("#inlineCheckbox1").attr("checked","checked");
	}
	if(scene[i] == 2){
 		$("#inlineCheckbox2").attr("checked","checked");
	}
  }

  if (params.name) {
    $(this).find('.name-button').text(params.name);
  }
  $(this).find('input:radio[name=is_enable][value="'+params.is_enable+'"]').prop('checked', true).click();
});



//expend-manage-modal
$('.expend-manage-modal').on('show.bs.modal', function(e) {
  let button = $(e.relatedTarget);
  let params = button.data('params');

  if (!params) {
    return;
  }
  	$("#inlineCheckbox1").removeAttr("checked");
	$("#inlineCheckbox2").removeAttr("checked");
	$("#inlineCheckbox3").removeAttr("checked");

  $(this).find('input[name=rank_num]').val(params.rank_num);
  $(this).find('select[name=plugin_unikey]').val(params.plugin_unikey);
  $(this).find('input[name=parameter]').val(params.parameter);

  $(".inputUrl").css('display','none');
  $(".inputFile").removeAttr('style');
  $(".showSelectTypeName").text('上传图片');
  if(params.icon_file_url){
	$(this).find('input[name=icon_file_url]').val(params.icon_file_url);
	$(this).find('input[name=icon_file_url]').removeAttr('style');
	 $(".showSelectTypeName").text('图片地址');
	$(".inputFile").css('display','none');
  }

  if(params.member_roles){
	with (document.getElementById('member_roles')) {
	    for (var i=0; i<options.length; i++) {
	        options[i].selected = (','+params.member_roles+',').indexOf(','+options[i].value+',')>-1;
	    }
	}
  }
  scene = params.scene.split(",");
  for (var i=0; i<scene.length; i++) {
	if(scene[i] == 1){
 		$("#inlineCheckbox1").attr("checked","checked");
	}
	if(scene[i] == 2){
 		$("#inlineCheckbox2").attr("checked","checked");
	}
	if(scene[i] == 3){
 		$("#inlineCheckbox3").attr("checked","checked");
	}
  }

  if (params.name) {
    $(this).find('.name-button').text(params.name);
  }
  $(this).find('input:radio[name=is_enable][value="'+params.is_enable+'"]').prop('checked', true).click();

  $(this).find('input:radio[name=is_group_admin][value="'+params.is_group_admin+'"]').prop('checked', true).click();

	if(params.is_group_admin == 1){
		$("#usage_setting").addClass("show");
	}else{
		$("#usage_setting").removeClass("show");
	}
});


//expend-profile-modal
$('.expend-profile-modal').on('show.bs.modal', function(e) {
  let button = $(e.relatedTarget);
  let params = button.data('params');

  if (!params) {
    return;
  }
  $(this).find('input[name=rank_num]').val(params.rank_num);
  $(this).find('select[name=plugin_unikey]').val(params.plugin_unikey);
  $(this).find('input[name=parameter]').val(params.parameter);

  $(".inputUrl").css('display','none');
  $(".inputFile").removeAttr('style');
  $(".showSelectTypeName").text('上传图片');
  if(params.icon_file_url){
	$(this).find('input[name=icon_file_url]').val(params.icon_file_url);
	$(this).find('input[name=icon_file_url]').removeAttr('style');
	 $(".showSelectTypeName").text('图片地址');
	$(".inputFile").css('display','none');
  }

  if(params.member_roles){
	with (document.getElementById('member_roles')) {
	    for (var i=0; i<options.length; i++) {
	        options[i].selected = (','+params.member_roles+',').indexOf(','+options[i].value+',')>-1;
	    }
	}
  }

  if (params.name) {
    $(this).find('.name-button').text(params.name);
  }
  $(this).find('input:radio[name=is_enable][value="'+params.is_enable+'"]').prop('checked', true).click();
});

//expend-feature-modal
$('.expend-feature-modal').on('show.bs.modal', function(e) {
  let button = $(e.relatedTarget);
  let params = button.data('params');

  if (!params) {
    return;
  }
  $(this).find('input[name=rank_num]').val(params.rank_num);
  $(this).find('select[name=plugin_unikey]').val(params.plugin_unikey);
  $(this).find('input[name=parameter]').val(params.parameter);

  $(".inputUrl").css('display','none');
  $(".inputFile").removeAttr('style');
  $(".showSelectTypeName").text('上传图片');
  if(params.icon_file_url){
	$(this).find('input[name=icon_file_url]').val(params.icon_file_url);
	$(this).find('input[name=icon_file_url]').removeAttr('style');
	 $(".showSelectTypeName").text('图片地址');
	$(".inputFile").css('display','none');
  }

  if(params.member_roles){
	with (document.getElementById('member_roles')) {
	    for (var i=0; i<options.length; i++) {
	        options[i].selected = (','+params.member_roles+',').indexOf(','+options[i].value+',')>-1;
	    }
	}
  }

  if (params.name) {
    $(this).find('.name-button').text(params.name);
  }
  $(this).find('input:radio[name=is_enable][value="'+params.is_enable+'"]').prop('checked', true).click();
});



$('#parent_group_id').on('change',function(){
	$('.groupall option').each(function(){
		$(this).prop("selected",'');
	});
	let parent_group_id = $('#parent_group_id option:selected').val();
	$('.groupall').css('display','none');
	if(parent_group_id){
		$('#child'+parent_group_id).removeAttr('style');
	}
});


$('#search_group_id').on('change',function(){
	$('.groupallsearch option').each(function(){
		$(this).prop("selected",'');
	});
	$('.alloption').css('display','none');
	let search_group_id = $('#search_group_id option:selected').val();
	if(search_group_id){
		$('.childsearch'+search_group_id).removeAttr('style');
	}
});



//expend-group-modal
$('.expend-group-modal').on('show.bs.modal', function(e) {
	let button = $(e.relatedTarget);
	let params = button.data('params');
	if (!params) {
		return;
	}

	$('.groupall').css('display','none');
	$(".groupall").prop("checked",'');

	var parent_id ='';
	var flag = false;
	$('#selectGroup option').each(function(){
		if( $(this).val() == params.group_id){
			parent_id = $(this).parent().data('parent-id');
			$('#child'+parent_id).removeAttr('style');
			$('#child'+parent_id).val(params.group_id);
			if(parent_id){
				flag = true;
			}

		}
	});
	if(parent_id){
		$(this).find('select[name=parent_group_id]').val(parent_id);
	}
	if(!flag){
		 $('#parent_group_id').val(params.group_id);
	}


	$(this).find('input[name=rank_num]').val(params.rank_num);
	$(this).find('select[name=plugin_unikey]').val(params.plugin_unikey);
	$(this).find('input[name=parameter]').val(params.parameter);

	$(".inputUrl").css('display','none');
	$(".inputFile").removeAttr('style');
	$(".showSelectTypeName").text('上传图片');
	if(params.icon_file_url){
		$(this).find('input[name=icon_file_url]').val(params.icon_file_url);
		$(this).find('input[name=icon_file_url]').removeAttr('style');
		 $(".showSelectTypeName").text('图片地址');
		$(".inputFile").css('display','none');
	}

	if(params.member_roles){
		with (document.getElementById('member_roles')) {
		for (var i=0; i<options.length; i++) {
			options[i].selected = (','+params.member_roles+',').indexOf(','+options[i].value+',')>-1;
			}
		}
	}

	if (params.name) {
		$(this).find('.name-button').text(params.name);
	}
	$(this).find('input:radio[name=is_enable][value="'+params.is_enable+'"]').prop('checked', true).click();
});

//upload file change
// $(".infoli li").click(function() {
// 	let inputname = $(this).data('name');
// 	$('#showIcon').text($(this).text());
// 	$("#showIcon").siblings('input').css('display','none');
// 	$('#showIcon1').text($(this).text());
// 	$("#showIcon1").siblings('input').css('display','none');
// 	$("."+inputname).removeAttr('style');
// });

// explan type edit
$('#createTypeModal').on('show.bs.modal', function(e) {
	let button = $(e.relatedTarget);
	let params = button.data('params');
	if (!params) {
		return;
	}

  let dataSources = params.data_sources;
  let postList = dataSources.postLists ? dataSources.postLists.pluginUnikey : null;
  let postFollow =  dataSources.postFollows ? dataSources.postFollows.pluginUnikey : null;
  let postNearby =  dataSources.postNearbys ? dataSources.postNearbys.pluginUnikey : null;

  $(this).find('input[name=rank_num]').val(params.rank_num);
  $(this).find('select[name=plugin_unikey]').val(params.plugin_unikey);
  if (postList) {
    $(this).find('select[name=post_list]').val(postList);
  }
  if (postFollow) {
    $(this).find('select[name=post_follow]').val(postFollow);
  }
  if (postNearby) {
    $(this).find('select[name=post_nearby]').val(postNearby);
  }

	$(this).find('input:radio[name=is_enable][value="'+params.is_enable+'"]').prop('checked', true);
});


// panel types edit
$('#sortNumberModal').on('show.bs.modal', function(e) {
  if ($(this).data('is_back')) {
    return;
  }
	let button = $(e.relatedTarget);
	let params = button.data('params');
	let action = button.data('action');

  $(this).find('.sort-item').remove();
  $(this).find('form').attr('action', action);

  let template = $('#sortTemplate').contents();
  params = JSON.parse(params);
  $(this).data('languages', null);
  console.log(params);
  console.log(params);


  params.map(param => {

    let titles = new Object;
    let descriptions = new Object;
    param.intro.map(item => {
      titles[item.langTag] = item.title
      descriptions[item.langTag] = item.description
    });

    let sortTemplate = template.clone();
    sortTemplate.find('input[name="ids[]"]').val(param.id);
    sortTemplate.find('.sort-title').data('languages', param.intro);
    sortTemplate.find('input[name="titles[]"]').val(JSON.stringify(titles));
    sortTemplate.find('.sort-description').data('languages', param.intro);
    sortTemplate.find('input[name="descriptions[]"]').val(JSON.stringify(descriptions));

    sortTemplate.insertBefore($(this).find('.add-sort-tr'));
  })

  $('#sortNumberTitleLangModal')
});


//selectImageTyle
$(".selectImageTyle li").click(function() {
	let inputname = $(this).data('name');

	$(this).parent().siblings('.showSelectTypeName').text($(this).text());
	$(this).parent().siblings('input').css('display','none');
	$(this).parent().siblings('.'+inputname).removeAttr('style');
});

$('#sortNumberModal .add-sort').click(function() {
  let template = $('#sortTemplate').clone();

  $(template.html()).insertBefore($('#sortNumberModal').find('.add-sort-tr'));
});

$(document).on('click', '.delete-sort-number', function() {
  $(this).closest('tr').remove();
});

$("#sortNumberTitleLangModal").on('show.bs.modal', function (e) {
	let button = $(e.relatedTarget);
  let languages = button.data('languages');
  $(this).find('form').trigger("reset");
  $(this).data('button', e.relatedTarget)

  if (! languages) {
    return;
  }

  languages.map(language => {
    $(this).find("input[name="+language.langTag).val(language.title);
  });
});

$("#sortNumberTitleLangModal").on('hide.bs.modal', function (e) {
  let button = $($(this).data('button'));
  $('#sortNumberModal').data('is_back', true);
  $('#sortNumberModal').modal('show');

  let titles = $(this).find('form').serializeArray();
  let data = new Object;
  titles.map(title => {
    data[title.name] = title.value
  })
  button.siblings('input').val(JSON.stringify(data));
});

$("#sortNumberDescLangModal").on('show.bs.modal', function (e) {
	let button = $(e.relatedTarget);
  let languages = button.data('languages');
  $(this).find('form').trigger("reset");
  $(this).data('button', e.relatedTarget)

  if (! languages) {
    return;
  }

  languages.map(language => {
    $(this).find("input[name="+language.langTag).val(language.description);
  });
});

$("#sortNumberDescLangModal").on('hide.bs.modal', function (e) {
  let button = $($(this).data('button'));
  $('#sortNumberModal').data('is_back', true);
  $('#sortNumberModal').modal('show');

  let descriptions = $(this).find('form').serializeArray();
  let data = new Object;
  descriptions.map(description => {
    data[description.name] = description.value
  })
  button.siblings('input').val(JSON.stringify(data));
});

$('#sortNumberModal').on('hide.bs.modal', function(e) {
  $(this).data('is_back', false)
});

// panel types edit end


// operation group eidt
$('#groupModal').on('show.bs.modal', function(e) {
  let button = $(e.relatedTarget),
    params = button.data('params'),
    action = button.data('action');

});
// operation group eidt end

