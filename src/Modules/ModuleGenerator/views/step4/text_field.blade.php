<div class="col-sm-12">
    <div class="form-group">
        <label>Title Field Candidate</label>
        <input type="text" name="title_field" value="{{$config['title_field']}}" class='form-control'>
    </div>
</div>


<div class="col-sm-5">
    <div class="form-group">
        <label>Limit Data</label>
        <input type="number" name="limit" value="{{$config['limit']}}" class='form-control'>
    </div>
</div>


<div class="col-sm-7">
    <div class="form-group">
        <label>Order By</label>
        <?php
        if (is_array($config['orderby'])) {
            $orderby = [];
            foreach ($config['orderby'] as $k => $v) {
                $orderby[] = $k.','.$v;
            }
            $orderby = implode(";", $orderby);
        } else {
            $orderby = $config['orderby'];
        }
        ?>
        <input type="text" name="orderby" value="{{ $orderby }}" class='form-control'>
        <div class="help-block">E.g : column_name,desc</div>
    </div>
</div>
                