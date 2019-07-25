@if ($crud->hasAccess('update'))



<?php




if (!$entry->finished )
{
    ?>

    <a href="{{ url($crud->route.'').'/'.$entry->getKey() }}/finish" onclick="return confirm('Are you sure to finish poll #{{$entry->getKey() }}?')" class="btn btn-xs btn-warning">
        <i class="fa fa-check"></i> Finish
    </a>

    <?php
} else{
    echo'<span class="btn btn-xs btn-success">finished</span>';
}
?>



@endif