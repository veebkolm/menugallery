jQuery(function(jQuery) {
    
    var file_frame,
    sg = {
        ul: '',
        init: function() {
            this.ul = jQuery('.sbox');
            this.ul.sortable({
                placeholder: '',
				revert: true,
            });			
			
            /**
			 * Add Slide Callback Funtion
			 */
            jQuery('#add-new-images').on('click', function(event) {
                event.preventDefault();
                if (file_frame) {
                    file_frame.open();
                    return;
                }
                file_frame = wp.media.frames.file_frame = wp.media({
                    multiple: true
                });

                file_frame.on('select', function() {
                    var images = file_frame.state().get('selection').toJSON(),
                            length = images.length;
                    for (var i = 0; i < length; i++) {
                        sg.get_thumbnail(images[i]['id']);
                    }
                });
                file_frame.open();
            });

            /**
             * Delete Slide Callback Function
             */
            this.ul.on('click', '#remove-image', function() {
                if (confirm('Are sure to delete this images?')) {
                    jQuery(this).parent().fadeOut(700, function() {
                        jQuery(this).remove();
                    });
                }
                return false;
            });
            
            /**
             * Delete All Slides Callback Function
             */
            jQuery('#remove-all-images').on('click', function() {
                if (confirm('Are sure to delete all images?')) {
                    sg.ul.empty();
                }
                return false;
            });

        },
        get_thumbnail: function(id, cb) {
            cb = cb || function() {
            };
            var data = {
                action: 'sg_js',
                SGimageid: id
            };
            jQuery.post(ajaxurl, data, function(response) {
                sg.ul.append(response);
                cb();
            });
        }
    };
    sg.init();
});