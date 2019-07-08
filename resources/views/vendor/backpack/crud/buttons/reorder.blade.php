<?php
$additional = !empty(request('question_id'))?'?question_id='.request('question_id'):'';
?>
@if ($crud->reorder)
	@if ($crud->hasAccess('reorder'))
	  <a href="{{ url($crud->route.'/reorder'.$additional)  }}" onclick="getExportLocation(this,event);" class="btn btn-default ladda-button" data-style="zoom-in"><span class="ladda-label"><i class="fa fa-arrows"></i> {{ trans('backpack::crud.reorder') }} {{ $crud->entity_name_plural }}</span></a>
	@endif
@endif
<script>
    function getExportLocation(ob,event)
    {
        ob.href = "{{ url($crud->route.'/reorder') }}" + window.location.search;
    }
</script>