<style type="text/css">
    body.dragging, body.dragging * {
        cursor: move !important;
    }

    .dragged {
        position: absolute;
        opacity: 0.7;
        z-index: 2000;
    }

    .draggable-menu {
        padding: 0 0 0 0;
        margin: 0 0 0 0;
    }

    .draggable-menu li ul {
        margin-top: 6px;
    }

    .draggable-menu li div {
        padding: 5px;
        border: 1px solid #cccccc;
        background: #eeeeee;
        cursor: move;
    }

    .draggable-menu li .is-dashboard {
        background: #fff6e0;
    }

    .draggable-menu li .icon-is-dashboard {
        color: #ffb600;
    }

    .draggable-menu li {
        list-style-type: none;
        margin-bottom: 4px;
        min-height: 35px;
    }

    .draggable-menu li.placeholder {
        position: relative;
        border: 1px dashed #b7042c;
        background: #ffffff;
        /** More li styles **/
    }

    .draggable-menu li.placeholder:before {
        position: absolute;
        /** Define arrowhead **/
    }
</style>