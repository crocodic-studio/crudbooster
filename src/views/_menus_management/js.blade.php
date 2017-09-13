<script type="text/javascript">
    $(function () {
        var id_cms_privileges = '{{$id_cms_privileges}}';
        var sortactive = $(".draggable-menu").sortable({
            group: '.draggable-menu',
            delay: 200,
            isValidTarget: function ($item, container) {
                var depth = 1, // Start with a depth of one (the element itself)
                    maxDepth = 2,
                    children = $item.find('ul').first().find('li');

                // Add the amount of parents to the depth
                depth += container.el.parents('ul').length;

                // Increment the depth for each time a child
                while (children.length) {
                    depth++;
                    children = children.find('ul').first().find('li');
                }

                return depth <= maxDepth;
            },
            onDrop: function ($item, container, _super) {

                if ($item.parents('ul').hasClass('draggable-menu-active')) {
                    var isActive = 1;
                    var data = $('.draggable-menu-active').sortable("serialize").get();
                    var jsonString = JSON.stringify(data, null, ' ');
                } else {
                    var isActive = 0;
                    var data = $('.draggable-menu-inactive').sortable("serialize").get();
                    var jsonString = JSON.stringify(data, null, ' ');
                    $('#inactive_text').remove();
                }

                $.post("{{route('AdminMenusControllerPostSaveMenu')}}", {
                    menus: jsonString,
                    isActive: isActive
                }, function (resp) {
                    $('#menu-saved-info').fadeIn('fast').delay(1000).fadeOut('fast');
                });

                _super($item, container);
            }
        });


    });
</script>