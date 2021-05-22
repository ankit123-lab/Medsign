<div style="display: none" id="loading" class="loading"></div>
<script type="text/javascript">
    var no_result_found = "<?= lang('common_no_result_matched'); ?>";
    var change_language_error_message = "<?= lang('common_error_in_change_language'); ?>";
    var get_csrf_token_name = "<?= $this->security->get_csrf_token_name() ?>";
    var get_method = '<?= $this->router->method; ?>';
    jQuery(document).ready(function () {
        jQuery('.selectpicker').selectpicker({
            noneResultsText: no_result_found,
            size: '5',
            selectAllText: '<?= lang("common_select_all") ?>',
            deselectAllText: '<?= lang("common_deselect_all") ?>',
            countSelectedText: function (num) {
                if (num == 1) {
                    return "{0} <?= lang('common_items_selected'); ?>";
                } else {
                    return "{0} <?= lang('common_items_selected'); ?>";
                }
            },
            liveSearchPlaceholder: "<?= lang('common_search_placeholder'); ?>"
        });

        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
            $('.selectpicker').selectpicker('mobile');
        }
    });
</script>
<script src="<?php echo ASSETS_PATH; ?>js/pages/common.js"></script>
</body>
</html>