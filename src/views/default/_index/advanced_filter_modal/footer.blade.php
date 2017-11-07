<div class="modal-footer" align="right">
    <button class="btn btn-default" type="button" data-dismiss="modal">{{cbTrans("button_close")}}</button>
    <button class="btn btn-default btn-reset" type="reset" onclick='location.href="{{Request::get("lasturl")}}"'>{{cbTrans("button_reset")}}</button>
    <button class="btn btn-primary btn-submit" type="submit">{{cbTrans("button_submit")}}</button>
</div>