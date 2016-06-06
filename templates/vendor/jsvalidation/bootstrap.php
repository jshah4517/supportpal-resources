<script>
    jQuery(document).ready(function(){

        /**
         * Don't ignore hidden elements. Overrides:
         *  - Fixed #189 - :hidden elements are now ignored by default
         *    see: http://jquery.bassistance.de/validate/changelog.txt
         */
        $.validator.setDefaults({
            ignore: []
        });

        /**
         * Check whether a child element is overflowing its parent container.
         * Example: $('.parent_container').isChildOverflowing('#child-element')
         *
         * @param child
         * @return boolean
         */
        jQuery.fn.isChildOverflowing = function (child) {
            var p = jQuery(this).get(0);
            var el = jQuery(child).get(0);

            // Check we actually have valid DOM elements otherwise .offsetTop etc will throw an error
            if (typeof p !== 'undefined' && typeof el !== 'undefined') {
                return (el.offsetTop < p.offsetTop || el.offsetLeft < p.offsetLeft) ||
                    (el.offsetTop + el.offsetHeight > p.offsetTop + p.offsetHeight
                        || el.offsetLeft + el.offsetWidth > p.offsetLeft + p.offsetWidth);
            }

            return false;
        };

        $("<?php echo $validator['selector']; ?>").validate({
            errorElement: 'span',
            errorClass: 'field-error',

            errorPlacement: function(error, element) {
                var postion = element;

                // If it's redactor, codemirror, show/hide button, a checkbox or radio, add after parent
                if (element.parent('.redactor-box').length || element.parent('.merge-field_container').length ||
                    element.parent('.hideShowPassword-wrapper').length || element.parent('.input-group').length ||
                    element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                    postion = element.parent();
                }

                // Show error
                var displayingError = error.insertAfter(postion);

                // If the form field is too big, put the error below
                if ($('.desk_content_padding').isChildOverflowing('#'+displayingError.prop('id'))) {
                    displayingError.addClass('field-error-below');
                }
            },
            highlight: function(element) {
                $(element).closest('.form-row').addClass('has-error'); // add the Bootstrap error class to the control group
            },

            /*
             // Uncomment this to mark as validated non required fields
             unhighlight: function(element) {
             $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
             },
             */
            success: function(element) {
                $(element).closest('.form-row').removeClass('has-error').addClass('has-success'); // remove the Boostrap error class from the control group
            },

            focusInvalid: false, // do not focus the last invalid input
            <?php if (Config::get('jsvalidation.focus_on_error')): ?>
            invalidHandler: function(form, validator) {

                if (!validator.numberOfInvalids())
                    return;

                $('html, body').animate({
                    scrollTop: $(validator.errorList[0].element).offset().top - 46
                }, <?php echo Config::get('jsvalidation.duration_animate') ?>);
                $(validator.errorList[0].element).focus();

            },
            <?php endif; ?>

            rules: <?php echo json_encode($validator['rules']); ?>
        })
    })
</script>