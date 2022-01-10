@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::operation.sidebar')
@endsection

@section('content')

  <div class="row mb-4 border-bottom">
    <div class="col-lg-7">
      <h3>表情配置</h3>
      <p class="text-secondary">自定义配置表情图，在不配置的情况下，用户也可以通过表情键盘输入 Emoji 表情。</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#emojiGroupCreateModal"><i class="bi bi-plus-circle-dotted"></i> 新增表情组</button>
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
  </div>
  <!--操作列表-->
  <div class="table-responsive">
    <table class="table table-hover align-middle text-nowrap">
      <thead>
        <tr class="table-info">
          <th scope="col" style="width:6rem;">显示顺序</th>
          <th scope="col">表情组名称</th>
          <th scope="col">表情数量</th>
          <th scope="col">启用状态</th>
          <th scope="col" style="width:13rem;">操作</th>
        </tr>
      </thead>
      <tbody>
        @foreach($groups as $group)
          <tr>
            <td><input type="number" class="form-control input-number" value="{{ $group->rank_num }}"></td>
            <td><img src="{{ $group->image_file_url }}" width="24" height="24"> {{ $group->name }}</td>
            <td>{{ $group->emojis->count() }}</td>
            <td>
              @if($group->is_enable)
                <i class="bi bi-check-lg text-success"></i>
              @else
                <i class="bi bi-dash-lg text-secondary"></i>
              @endif
            </td>
            <td>
              <form action="{{ route('panel.emojiGroups.destroy', ['emojiGroup' => $group->id]) }}" method="post">
                @csrf
                @method('delete')
                <button type="button" class="btn btn-outline-primary btn-sm"
                  data-bs-toggle="modal"
                  data-action="{{ route('panel.emojiGroups.update', ['emojiGroup' => $group->id]) }}"
                  data-names="{{ $group->names->toJson() }}"
                  data-params="{{ json_encode($group->attributesToArray()) }}"
                  data-bs-target="#emojiGroupCreateModal">修改</button>
                <button type="button" class="btn btn-outline-info btn-sm ms-1" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEmoji" aria-controls="offcanvasEmoji">配置表情图</button>
                <button type="submit" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>


  <!-- Modal -->
  <div class="modal fade" id="emojiGroupCreateModal" tabindex="-1" aria-labelledby="emojiGroupCreateModal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">表情组管理</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">显示顺序</label>
              <div class="col-sm-9">
                <input type="number" class="form-control input-number">
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">表情组图标</label>
              <div class="col-sm-9">
                <input type="file" class="form-control" id="inputGroupFile01">
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">表情组名称</label>
              <div class="col-sm-9">
                <button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start"
                  data-bs-toggle="modal"
                  data-parent="#emojiGroupCreateModal"
                  data-bs-target="#langModal">名称</button>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">启用状态</label>
              <div class="col-sm-9 pt-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="status" id="status_true" value="true" checked>
                  <label class="form-check-label" for="status_true">启用</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="status" id="status_false" value="false">
                  <label class="form-check-label" for="status_false">不启用</label>
                </div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label"></label>
              <div class="col-sm-9"><button type="submit" class="btn btn-primary">提交</button></div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Language Modal -->
  <div class="modal fade" id="langModal" tabindex="-1" aria-labelledby="langModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">多语言设置</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="table-responsive">
              <table class="table table-hover align-middle text-nowrap">
                <thead>
                  <tr class="table-info">
                    <th scope="col" class="w-25">语言标签</th>
                    <th scope="col" class="w-25">语言名称</th>
                    <th scope="col" class="w-50">内容</th>
                  </tr>
                </thead>
                <tbody>

                  @foreach($optionalLanguages as $lang)
                    <?php
                      $langName = $lang['langName'];
                      if ($lang['areaCode']) {
                        $langName .= '('. optional($areaCodes->where('code', $lang['areaCode'])->first())['localName'] .')';
                      }
                    ?>
                  <tr>
                    <td>{{ $lang['langTag'] }}</td>
                    <td>{{ $langName }}</td>
                    <td><input type="text" class="form-control" name="languages[{{ $lang['langTag'] }}]" value=""></td>
                  </tr>
                @endforeach
                </tbody>
              </table>
            </div>
            <!--保存按钮-->
            <div class="text-center">
              <button type="button" class="btn btn-success" data-bs-dismiss="modal" aria-label="Close">确认</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Offcanvas -->
  <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasEmoji" aria-labelledby="offcanvasEmojiLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="offcanvasEmojiLabel">表情管理<button class="btn btn-info btn-sm ms-3" type="button"><i class="bi bi-plus-circle-dotted"></i> 新增表情图</button></h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <form>
        <div class="table-responsive">
          <table class="table table-hover align-middle text-nowrap">
            <thead>
              <tr class="table-info">
                <th scope="col" style="width:6rem;">顺序</th>
                <th scope="col">表情图</th>
                <th scope="col">表情符号</th>
                <th scope="col">是否启用</th>
                <th scope="col">操作</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><input type="number" class="form-control input-number" value="1"></td>
                <td><img src="../assets/images/emoji/default/smile.gif" width="28" height="28"></td>
                <td>[smile]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="2"></td>
                <td><img src="../assets/images/emoji/default/sad.gif" width="28" height="28"></td>
                <td>[lol]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="3"></td>
                <td><img src="../assets/images/emoji/default/biggrin.gif" width="28" height="28"></td>
                <td>[biggrin]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="4"></td>
                <td><img src="../assets/images/emoji/default/cry.gif" width="28" height="28"></td>
                <td>[cry]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="5"></td>
                <td><img src="../assets/images/emoji/default/huffy.gif" width="28" height="28"></td>
                <td>[huffy]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="6"></td>
                <td><img src="../assets/images/emoji/default/shocked.gif" width="28" height="28"></td>
                <td>[shocked]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="7"></td>
                <td><img src="../assets/images/emoji/default/tongue.gif" width="28" height="28"></td>
                <td>[tongue]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="8"></td>
                <td><img src="../assets/images/emoji/default/shy.gif" width="28" height="28"></td>
                <td>[shy]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="9"></td>
                <td><img src="../assets/images/emoji/default/titter.gif" width="28" height="28"></td>
                <td>[titter]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="10"></td>
                <td><img src="../assets/images/emoji/default/sweat.gif" width="28" height="28"></td>
                <td>[sweat]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="11"></td>
                <td><img src="../assets/images/emoji/default/mad.gif" width="28" height="28"></td>
                <td>[mad]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="12"></td>
                <td><img src="../assets/images/emoji/default/lol.gif" width="28" height="28"></td>
                <td>[lol]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="13"></td>
                <td><img src="../assets/images/emoji/default/loveliness.gif" width="28" height="28"></td>
                <td>[loveliness]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="14"></td>
                <td><img src="../assets/images/emoji/default/funk.gif" width="28" height="28"></td>
                <td>[funk]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="15"></td>
                <td><img src="../assets/images/emoji/default/curse.gif" width="28" height="28"></td>
                <td>[curse]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="16"></td>
                <td><img src="../assets/images/emoji/default/dizzy.gif" width="28" height="28"></td>
                <td>[dizzy]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="17"></td>
                <td><img src="../assets/images/emoji/default/shutup.gif" width="28" height="28"></td>
                <td>[shutup]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="18"></td>
                <td><img src="../assets/images/emoji/default/sleepy.gif" width="28" height="28"></td>
                <td>[sleepy]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="19"></td>
                <td><img src="../assets/images/emoji/default/hug.gif" width="28" height="28"></td>
                <td>[hug]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="20"></td>
                <td><img src="../assets/images/emoji/default/victory.gif" width="28" height="28"></td>
                <td>[victory]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="21"></td>
                <td><img src="../assets/images/emoji/default/time.gif" width="28" height="28"></td>
                <td>[time]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="22"></td>
                <td><img src="../assets/images/emoji/default/kiss.gif" width="28" height="28"></td>
                <td>[kiss]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="23"></td>
                <td><img src="../assets/images/emoji/default/handshake.gif" width="28" height="28"></td>
                <td>[handshake]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
              <tr>
                <td><input type="number" class="form-control input-number" value="24"></td>
                <td><img src="../assets/images/emoji/default/call.gif" width="28" height="28"></td>
                <td>[call]</td>
                <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked></div></td>
                <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
              </tr>
            </tbody>
          </table>
        </div>
        <!--保存按钮-->
        <div class="text-center mb-4">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close">保存</button>
        </div>
      </form>
    </div>
  </div>
@endsection
