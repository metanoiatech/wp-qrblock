jQuery(document).ready(function($) {
    function set_param_enable_fields(selectedPageID) {
        $.ajax({
            url: param_enable.ajax_url,
            type: 'POST',
            dataType: 'json', 
            data: {
                action: 'change_select_page',
                nonce: param_enable.nonce,
                page_id: selectedPageID
            },
            success: function(response) {
                if (response.success) {
                    if (response.data.param_enable) {
                        var team_id_enable          = response.data.param_enable.team_id_enable ? response.data.param_enable.team_id_enable : 0;
                        var minecraft_id_enable     = response.data.param_enable.minecraft_id_enable ? response.data.param_enable.minecraft_id_enable : 0;
                        var server_id_enable        = response.data.param_enable.server_id_enable ? response.data.param_enable.server_id_enable : 0;
                        var game_id_enable          = response.data.param_enable.game_id_enable ? response.data.param_enable.game_id_enable : 0;
                        var group_id_enable         = response.data.param_enable.group_id_enable ? response.data.param_enable.group_id_enable : 0;
                        var gamipress_ranks_enable  = response.data.param_enable.gamipress_ranks_enable ? response.data.param_enable.gamipress_ranks_enable : 0;
                        var gamipress_points_enable = response.data.param_enable.gamipress_points_enable ? response.data.param_enable.gamipress_points_enable : 0;

                        $(`#acf-${param_enable.team_id_enable_field_key}`).val(team_id_enable);
                        $(`#acf-${param_enable.minecraft_id_enable_field_key}`).val(minecraft_id_enable);
                        $(`#acf-${param_enable.server_id_enable_field_key}`).val(server_id_enable);
                        $(`#acf-${param_enable.game_id_enable_field_key}`).val(game_id_enable);
                        $(`#acf-${param_enable.group_id_enable_field_key}`).val(group_id_enable);
                        $(`#acf-${param_enable.gamipress_ranks_enable_field_key}`).val(gamipress_ranks_enable);
                        $(`#acf-${param_enable.gamipress_points_enable_field_key}`).val(gamipress_points_enable);
                    }
                } else {
                    alert('AJAX Error: ' + response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + error);
            }
        });
    }

    $(`#acf-${param_enable.pages_field_key}`).on('change', function() {
        var selectedValue = $(this).val();
        set_param_enable_fields(selectedValue);
    });

    $(`#acf-${param_enable.same_all_instances_field_key}`).on('change', function(e) {
        if (e.target.value === "0") {
            $(`#acf-${param_enable.pages_field_key}`).attr('disabled', false);
        } else if (e.target.value === "1") {
            $(`#acf-${param_enable.pages_field_key}`).attr('disabled', true);
        }
    });

    var same_all_instances = $(`#acf-${param_enable.same_all_instances_field_key}`).val();
    if (same_all_instances === "1") {
        $(`#acf-${param_enable.pages_field_key}`).attr('disabled', true);
    } else if (same_all_instances === "0") {
        $(`#acf-${param_enable.pages_field_key}`).attr('disabled', false);
    }
    
    var selectedValue = $(`#acf-${param_enable.pages_field_key}`).val();
    // set_param_enable_fields(selectedValue);
});