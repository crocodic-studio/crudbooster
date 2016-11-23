<script type="text/javascript">
  $(function() {
      function format(icon) {          
          var originalOption = icon.element;
          var label = $(originalOption).text();
          var val = $(originalOption).val();
          if(!val) return label;
          var $resp = $('<span><i style="margin-top:5px" class="pull-right ' + $(originalOption).val() + '"></i> ' + $(originalOption).data('label') + '</span>');
          return $resp;
      }
      $('#list-icon').select2({
          width: "100%",
          templateResult: format,
          templateSelection: format
      });
  })  
</script>

<select id='list-icon' class="form-control" name="icon" style="font-family: 'FontAwesome', Helvetica;">
   <option value="">** Select an Icon</option>
   @foreach($fontawesome as $font)
    <option value='fa fa-{{$font}}' {{ ($row->icon == "fa fa-$font")?"selected":"" }} data-label='{{$font}}'>{{$font}}</option>
   @endforeach
</select>