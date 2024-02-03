@extends("admin::layouts.master")
@section("content")

    <div class="row">
        <div class="col-md-12 ibox float-e-margins">

            <div class="ibox-title">
                <div class="col-md-6">
                    <i class="fa fa-bars"></i>
                    <?php echo trans("admin::common.sidebar_order"); ?>
                </div>
                <div class="col-md-6">
                    <button style="margin-top: -8px;" data-loading-text="<?php echo trans("admin::common.saving"); ?>"
                            class="btn btn-primary pull-right"
                            id="save-order"><?php echo trans("admin::common.save_order"); ?></button>
                </div>
            </div>
            <?php

            function get_menu_items($items, $depth = 0) {
            foreach ($items as $item) {
            $key = isset($item["key"]) ? $item["key"] : "UNKOWN";
            $icon = isset($item["icon"]) ? $item["icon"] : "";
            $url = isset($item["url"]) ? $item["url"] : "";
            $item['children'] = isset($item['children']) ? $item['children'] : array();
            ?>

            <li class="group iterate-item dd-item" id="list-<?php echo $key ?>" data-icon="<?php echo $icon; ?>"
                data-url="<?php echo $url; ?>">
                <div class="panel-heading dd-handle">
                        <span calass="panel-title">
                            <i class="fa <?php echo $icon; ?>"></i>
                            <?php echo Navigation::lang($key); ?>
                        </span>
                </div>
                <?php if (count($item["children"])) { ?>
                <ol>
                    <?php get_menu_items($item["children"], $depth++); ?>
                </ol>
                <?php } ?>
            </li>
            <?php
            }
            }
            ?>
            <div class="ibox-content" style="overflow:hidden">
                <div class="col-md-12">
                    <ol id="menu-items" class="ui-sortable">
                        <?php get_menu_items(Navigation::get("sidebar")->items());
                        ?>
                    </ol>

                </div>

            </div>
            @stop

            @section('footer')
                <script src="<?php echo assets("admin::") ?>/js/plugins/nestable/jquery.mjs.nestedSortable.js"></script>

                <script>
                    $(document).ready(function () {

                        $('#menu-items').nestedSortable({
                            handle: 'div',
                            items: 'li',
                            toleranceElement: '> div',
                            rtl: true,
                            relocate: function (ev, obj) {
                                console.log('sdf');
                                var item = obj.item;
                                var newLevel = item.parents('ol').length;
                                var newParentLevel = parseInt(newLevel) - 1;
                                var newParent = item.parents().find($("[data-level='" + newParentLevel + "']"));
                                newParent = newParent.attr('data-item-id');
                                newParent = (typeof newParent === 'undefined') ? 0 : newParent;
                                item.attr('data-level', newLevel);
                                item.attr('data-parent', newParent);
                            }
                        });

                        $('#save-order').click(function (e) {
                            var items = $('#menu-items').nestedSortable('toHierarchy', {startDepthCount: 0});

                            $('#save-order').button('loading');
                            $.post("<?php echo admin_url('sidebar'); ?>", {"items": JSON.stringify(items)}, function (result) {
                                $('#save-order').button('reset');
                            }, "json");
                        });
                        $('#s').click(function (ev) {
                            var ordered = [];
                            var navigationId = $('#menu-items').attr('data-navigation-id');
                            $('.iterate-item').each(function (key, value) {
                                ordered.push({
                                    'itemId': $(value).attr('data-item-id'),
                                    'level': $(value).attr('data-level'),
                                    'parent': $(value).attr('data-parent'),
                                    'type': $(value).attr('data-type'),
                                    'value': $(value).attr('data-type-value'),
                                    'name': $(value).attr('data-name')
                                });
                            });
                            var items = $('#menu-items').toHierarchy();
                            console.log(items);
                            return;
                            var deletedItems = '';
                            if ($('.deleted-items').length) {
                                deletedItems = $('.deleted-items').serializeArray();
                                console.log(deletedItems);
                            }
                            $(this).prop('disabled', true);
                            $.ajax({
                                url: '{!! admin_url("navigations/reorder") !!}',
                                type: 'GET', dataType: 'html',
                                data: {
                                    order: ordered,
                                    navigationId: navigationId,
                                    deletedItems: deletedItems
                                },
                                complete: function () {
                                    $('#save-order').prop('disabled', false);
                                    location.reload();
                                }
                            });
                        });
                    });

                </script>
@stop
