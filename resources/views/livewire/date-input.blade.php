<div>
    <input wire:model="{{$name}}"
    type="text"
    class="form-control datepicker"
    name="{{ $name }}"
    id="{{ $id }}"
    placeholder="{{ $placeholder }}"
    autocomplete="off" />
</div>

@script
<script>
    $(document).ready(function() {
        $('.datepicker').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd',
            todayHighlight: true,
        }).on("change", function(e){
            @this.set("{{ $id }}", e.target.value);
        });
    });

</script>
@endscript
