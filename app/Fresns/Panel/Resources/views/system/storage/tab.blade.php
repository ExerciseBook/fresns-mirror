<ul class="nav nav-tabs nav-fill">
  <li class="nav-item"><a class="nav-link  {{ \Route::is('panel.storage.image.show') ? 'active' : ''}}" href="{{ route('panel.storage.image.show') }}">图片存储设置</a></li>
  <li class="nav-item"><a class="nav-link {{ \Route::is('panel.storage.video.show') ? 'active' : ''}}" href="{{ route('panel.storage.video.show') }}">视频存储设置</a></li>
  <li class="nav-item"><a class="nav-link {{ \Route::is('panel.storage.audio.show') ? 'active' : ''}}" href="{{ route('panel.storage.audio.show') }}">音频存储设置</a></li>
  <li class="nav-item"><a class="nav-link {{ \Route::is('panel.storage.doc.show') ? 'active' : ''}}" href="{{ route('panel.storage.doc.show') }}">文档存储设置</a></li>
  <li class="nav-item"><a class="nav-link {{ \Route::is('panel.storage.repair.show') ? 'active' : ''}}" href="{{ route('panel.storage.repair.show') }}">补位图设置</a></li>
</ul>
