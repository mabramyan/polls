<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li><a href="{{ backpack_url('dashboard') }}"><i class="fa fa-dashboard"></i> <span>{{ trans('backpack::base.dashboard') }}</span></a></li>
{{-- <li><a href="{{ backpack_url('elfinder') }}"><i class="fa fa-files-o"></i> <span>{{ trans('backpack::crud.file_manager') }}</span></a></li> --}}
<li><a href='{{ backpack_url('campaign') }}'><i class='fa fa-tag'></i> <span>Campaign</span></a></li>
<li><a href='{{ backpack_url('poll') }}'><i class='fa fa-tag'></i> <span>Polls</span></a></li>
<li><a href='{{ backpack_url('question') }}'><i class='fa fa-tag'></i> <span>Questions</span></a></li>
<li><a href='{{ backpack_url('answer') }}'><i class='fa fa-tag'></i> <span>Answers</span></a></li>
{{-- <li><a href='{{ url(config('backpack.base.route_prefix', 'admin') . '/setting') }}'><i class='fa fa-cog'></i> <span>Settings</span></a></li> --}}
<li><a href='{{ backpack_url('user_answer') }}'><i class='fa fa-tag'></i> <span>Users Answer</span></a></li>
<li><a href='{{ backpack_url('team') }}'><i class='fa fa-tag'></i> <span>Teams</span></a></li>
<li><a href='{{ url(config('backpack.base.route_prefix', 'admin').'/log') }}'><i class='fa fa-terminal'></i> <span>Logs</span></a></li>