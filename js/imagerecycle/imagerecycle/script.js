/* Image Recycle */
var $j = jQuery.noConflict();
$j(document).ready(function() {

    initButtons = function() {

        //Optimize image via ajax
        $j('.optimize.ir-action').unbind('click').bind('click', function(e) {

            e.preventDefault();
            var $this = $j(e.target);
            var image = $this.data('image-realpath');
            if (!image || $this.hasClass('disabled')) {
                return false;
            }

            $j.ajax({
                url: optimize_url,
                data: {image: image, form_key: window.FORM_KEY},
                type: 'post',
                dataType: 'json',
                beforeSend: function() {
                    $this.addClass('disabled');
                    var $status = $this.closest('tr').find('.ir-status');
                    $status.html($j("#ir-setting-loader").html());
                },
                success: function(response) {

                    var $status = $this.closest('tr').find('.ir-status');
                    if (response.status === true) {
                        $status.addClass('msg-success');
                        $status.empty().text(response.datas.msg);
                        if (response.datas.newSize) {
                            $this.closest('tr').find('.filesize').empty().text(response.datas.newSize);
                        }
                        $this.removeClass('disabled optimize').addClass('revert').text('Revert to original');
                        initButtons();
                    }
                    else {
                        $status.addClass('msg-error');
                        $status.empty().text(response.datas.msg);
                        setTimeout(function() {
                            $this.removeClass('disabled');
                            $status.empty();
                        }, 5000);
                    }


                }
            });
        });

        $j('.revert.ir-action').unbind('click').bind('click', function(e) {
            e.preventDefault();

            var $this = $j(e.target);
            var image = $this.data('image-realpath');
            if (!image || $this.hasClass('disabled')) {
                return false;
            }

            $j.ajax({
                url: revert_url,
                data: {image: image, form_key: window.FORM_KEY},
                //async : false,
                type: 'post',
                dataType: 'json',
                beforeSend: function() {
                    $this.addClass('disabled');
                    var $status = $this.closest('tr').find('.ir-status');
                    $status.html($j("#ir-setting-loader").html());
                },
                success: function(response) {

                    var $status = $this.closest('tr').find('.ir-status');
                    if (response.status === true) {
                        $status.addClass('msg-success');
                        $status.empty().text(response.datas.msg);
                        if (response.datas.newSize) {
                            $this.closest('tr').find('.filesize').empty().text(response.datas.newSize);
                        }
                        $this.removeClass('disabled revert').addClass('optimize').text('Optimize');
                        initButtons();
                    }
                    else {
                        $status.addClass('msg-error');
                        $status.empty().text(response.datas.msg);
                        setTimeout(function() {
                            $this.removeClass('disabled');
                            $status.empty();
                        }, 5000);
                    }

                }
            });
        })
    };
    initButtons();

    //Change the setting
    $j('#ir-setting-save').bind('click', function(e) {
        e.preventDefault();
        var data = {
            form_key: window.FORM_KEY,            
            api_key: $j('#api_key').val(),
            api_secret: $j('#api_secret').val(),
            min_size: $j('#min_size').val(),
            exclude_folders :  $j('#exclude_folders').val(),
            resize_auto :    $j('#resize_auto').val(),
            resize_image :     $j('#resize_image').val(),
            min_size :  $j('#min_size').val(),
            max_size :  $j('#max_size').val(),
            compression_type_pdf :  $j('#compression_type_pdf').val(),
            compression_type_png :  $j('#compression_type_png').val(),
            compression_type_jpg :  $j('#compression_type_jpg').val(),
            compression_type_gif :  $j('#compression_type_gif').val(),
        };
      
        $j.ajax({
            url: saveConfig_url,
            data: data,
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                $j('#ir-setting-loader').show();
            },
            success: function(response) {
                $j('#ir-setting-loader').hide();
                if (response.success === true) {
                    $j('#ir-setting-msg').empty().text(response.msg).fadeIn(200).delay(2000)
                            .fadeOut(200, function() {
                                $j('.ir-setting').fadeOut(200);
                            });
                }
            }
        })
    });

    //Checked or not all checkbox
    $j('.ir-checkbox.check-all').bind('click', function(e) {
        var $status = $j(this).is(':checked');
        $j('.ir-checkbox').each(function(i, ck) {
            $j(ck).prop('checked', $status);
        });
    });

    // Pagination include form data
    $j('ul.pagination > li > a').bind('click', function(e) {
        e.preventDefault();
        var href = $j(this).attr('href');
        window.location.href = href + '?' + $j('#irForm').serialize();
    });

    // Setting action
    $j('#ir-setting').bind('click', function(e) {
        $j('.ir-setting').fadeToggle();
    });

    // Do bulk action
    $j('.do-bulk-action').bind('click', function(e) {
        e.preventDefault();
        if ($j('.ir-bulk-action').val() == '-1') {

            var ir_bulk_action = $j('.ir-bulk-action');
            var border_color = '#DDD';
            ir_bulk_action.css('border-color', '#FF3300');
            setTimeout(function() {
                ir_bulk_action.css('border-color', border_color);
            }, 300);
        }
        else if ($j('.ir-bulk-action').val() !== 'optimize_all') {

            if ($j('.ir-checkbox:checked').length < 1) {
                alert("No image selected");
            } else {

                $j('.ir-checkbox:checked').each(function(i) {
                    $j(this).parents('tr').find('.ir-action.optimize').click();
                });
            }

        } else {

            $j('#anchor-content').prepend('<div id="mageio_wait"><div>Please wait during optimization<br/><span></span></div></div>');
            $j('#mageio_wait').click(function() {
                window.location.reload();
            });
            optimizeAll();
        }
    });

    optimizeAll = function() {
        $j.ajax({
            url: optimizeall_url,
            data: {form_key: window.FORM_KEY},
            type: 'post',
            dataType: 'json',
            success: function(response) {
                if (typeof (response.status) !== 'undefined' && response.status === true) {
                    if (response.datas.continue === true) {
                        $j('#mageio_wait span').html(response.datas.totalOptimizedImages + ' optimized images / ' + response.datas.totalImages + ' images');
                        optimizeAll();
                    } else {
                        window.location.reload();
                    }
                }
            }
        })
    }


    // Prevent disabled elem to default action
    $j('.disabled').bind('click', function(e) {
        e.preventDefault();
    });
    
});
