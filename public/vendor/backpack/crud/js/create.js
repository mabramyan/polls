/*
*
* Backpack Crud / Create
*
*/

jQuery(function ($) {

    'use strict';

    if ($('.changed_team').length) {

        $('.changed_team').on('change', function (obj) {
            if (obj.target.value) {
                $('.target_team').val('');
                $.ajax({
                    url: '/admin/question/' + obj.target.value,
                    success: function (response) {
                        if (response.id) {

                            $('.target_team option').each(function (a, b) {
                                if ($(b).val() == response.team_1 || $(b).val() == response.team_2) {
                                    $(b).removeAttr('disabled');
                                 
                                } else {
                                    
   $(b).attr('disabled', 'disabled')
                                }
                            });
                        }
                    },
                    dataType: 'json'
                });
            }

        });

        $('.target_team').val('');

        if ( $('.changed_team').val()) {
            $.ajax({
                url: '/admin/question/' + $('.changed_team').val(),
                success: function (response) {
                    if (response.id) {
                        $('.target_team option').each(function (a, b) {
                            if ($(b).val() == response.team_1 || $(b).val() == response.team_2) {
                                $(b).removeAttr('disabled');
                             
                            } else {
                                
$(b).attr('disabled', 'disabled')
                            }
                        });
                    }
                },
                dataType: 'json'
            });
        }


    }
});
