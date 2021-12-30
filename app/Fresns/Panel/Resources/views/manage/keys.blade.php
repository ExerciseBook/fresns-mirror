@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::manage.sidebar')
@endsection

@section('content')
  <!--设置区域 开始-->
  <div class="row mb-4 border-bottom">
    <div class="col-lg-7">
      <h3>接口密钥</h3>
      <p class="text-secondary">密钥凭证很重要，请勿轻易透露给其他人。</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createKey"><i class="bi bi-plus-circle-dotted"></i> 新增密钥</button>
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
  </div>
  <!--密钥列表-->
  <div class="table-responsive">
    <table class="table table-hover align-middle text-nowrap">
      <thead>
        <tr class="table-info">
          <th scope="col">平台</th>
          <th scope="col">名称</th>
          <th scope="col">App ID</th>
          <th scope="col">App Key</th>
          <th scope="col">密钥类型</th>
          <th scope="col">启用状态</th>
          <th scope="col">操作</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" class="py-3">PC Web</th>
          <td>独立网站</td>
          <td>twvhl69n2uqygnox</td>
          <td>5rmahjlqpe9q69mljrcvd6xr2upidmdb</td>
          <td>主程序</td>
          <td><i class="bi bi-check-lg text-success"></i></td>
          <td>
            <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#createKey">编辑</button>
            <button type="button" class="btn btn-outline-primary btn-sm mx-2" data-bs-toggle="modal" data-bs-target="#resetModal">重置 Key</button>
            <button type="button" class="btn btn-link btn-sm text-danger fresns-link" data-bs-toggle="modal" data-bs-target="#deleteModal">删除</button>
          </td>
        </tr>
        <tr>
          <th scope="row" class="py-3">PC Web</th>
          <td>控制面板</td>
          <td>twvhl69n2uqygnox</td>
          <td>5rmahjlqpe9q69mljrcvd6xr2upidmdb</td>
          <td>插件 <span class="badge bg-light text-dark">Fresns CP API</span></td>
          <td><i class="bi bi-dash-lg text-secondary"></i></td>
          <td>
            <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#createKey">编辑</button>
            <button type="button" class="btn btn-outline-primary btn-sm mx-2" data-bs-toggle="modal" data-bs-target="#resetModal">重置 Key</button>
            <button type="button" class="btn btn-link btn-sm text-danger fresns-link" data-bs-toggle="modal" data-bs-target="#deleteModal">删除</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <!--设置区域 结束-->


  <!-- Modal -->
  <div class="modal fade" id="createKey" tabindex="-1" aria-labelledby="createKeyLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createKeyLabel">创建密钥</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!--创建密钥表单 开始-->
          <form>
            <div class="input-group mb-3">
              <span class="input-group-text">平台</span>
              <select class="form-select" id="key_platform">
                <option selected disabled>选择密钥应用平台</option>
                <option value="1">Other</option>
                <option value="2">PC Web</option>
                <option value="3">Mobile Web</option>
                <option value="4">Responsive Web</option>
                <option value="5">iOS App</option>
                <option value="6">Android App</option>
                <option value="7">WeChat Web</option>
                <option value="8">WeChat MiniProgram</option>
                <option value="9">QQ MiniProgram</option>
                <option value="10">Alipay MiniApp</option>
                <option value="11">ByteDance MicroApp</option>
                <option value="12">Quick App</option>
                <option value="13">Baidu SmartProgram</option>
                <option value="14">360 MiniApp</option>
              </select>
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text">名称</span>
              <input type="text" class="form-control" id="key_name">
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text">类型</span>
              <div class="form-control bg-white">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="key_type" id="fresns_key" value="false" data-bs-toggle="collapse" data-bs-target="#key_plugin_setting.show" aria-expanded="false" aria-controls="key_plugin_setting" checked>
                  <label class="form-check-label" for="fresns_key">主程序</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="key_type" id="admin_key" value="false" data-bs-toggle="collapse" data-bs-target="#key_plugin_setting.show" aria-expanded="false" aria-controls="key_plugin_setting">
                  <label class="form-check-label" for="admin_key">管理功能</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="key_type" id="plugin_key" value="true" data-bs-toggle="collapse" data-bs-target="#key_plugin_setting:not(.show)" aria-expanded="false" aria-controls="key_plugin_setting">
                  <label class="form-check-label" for="plugin_key">插件</label>
                </div>
              </div>
            </div>
            <!--类型设置 开始-->
            <div class="input-group mb-3 collapse" id="key_plugin_setting">
              <span class="input-group-text">关联插件<i class="bi bi-info-circle ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="该密钥不允许请求主程序 API"></i></span>
              <select class="form-select" id="key_plugin">
                <option selected disabled>选择密钥用于哪个插件</option>
                <option value="1">xx插件</option>
                <option value="2">zz插件</option>
              </select>
            </div>
            <!--类型设置 结束-->
            <div class="input-group mb-3">
              <span class="input-group-text">状态</span>
              <div class="form-control bg-white">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
                  <label class="form-check-label" for="inlineRadio1">启用</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                  <label class="form-check-label" for="inlineRadio2">停用</label>
                </div>
              </div>
            </div>

            <div class="text-center">
              <button type="submit" class="btn btn-primary">提交创建</button>
            </div>
          </form>
          <!--创建密钥表单 结束-->
        </div>
      </div>
    </div>
  </div>

  <!-- Reset Modal -->
  <div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">该名称名称</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>App ID: twvhl69n2uqygnox</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-dismiss="modal">重置 Key</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">该名称名称</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>App ID: twvhl69n2uqygnox</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-dismiss="modal">确认删除</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
        </div>
      </div>
    </div>
  </div>
@endsection
