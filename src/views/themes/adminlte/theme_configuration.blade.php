
<div class="form-group">
    <label for="">Theme Skin</label>
    <select name="adminlte_theme_skin" class="form-control">
        <?php
            $skins = [
                "skin-black",
                "skin-black-light",
                "skin-blue",
                "skin-blue-light",
                "skin-green",
                "skin-green-light",
                "skin-purple",
                "skin-purple-light",
                "skin-red",
                "skin-red-light",
                "skin-yellow",
                "skin-yellow-light"
            ];
        ?>
        @foreach($skins as $skin)
            <option {{ getSetting("adminlte_theme_skin","skin-green")==$skin?"selected":"" }} value="{{$skin}}">{{ $skin }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="">Additional CSS</label>
    <textarea name="adminlte_additional_css" placeholder="body { color: #000000 }" rows="5" class="form-control">{{ getSetting("adminlte_additional_css") }}</textarea>
</div>