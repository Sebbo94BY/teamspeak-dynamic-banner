<script type="module">
    // Get X-Y-Coordinates when clicking on the image
    $(document).ready(function() {
        $('img').click(function(e) {
            let offset = $(this).offset();

            let x_padding_offset = $(this).innerWidth() - $(this).width();
            let x_coordinate = ((e.pageX - offset.left) * (this.naturalWidth / $(this).width())) - x_padding_offset;

            $('input[name=x_coordinate_preview]').val(Math.floor(x_coordinate));

            let y_padding_offset = $(this).innerHeight() - $(this).height();
            let y_coordinate = ((e.pageY - offset.top) * (this.naturalHeight / $(this).height())) - y_padding_offset;

            $('input[name=y_coordinate_preview]').val(Math.floor(y_coordinate));
        });
    });

    // On page load, remove the `name` and `required` attributes for the hidden DIV to avoid
    // issues by the form validation when it's not filled out.
    $(document).ready(function(){
        $('div.d-none > div > div > input').each(function() {
            $(this).removeAttr("name");
            $(this).prop("required", false);
        });
    });

    /**
     * Converts the `id` attribute of an HTML input to the respective snake-case for the input `name` attribute.
     *
     * Example: id='validationXCoordinate' => name='x_coordinate'
     */
    function convert_input_id_to_snake_case_input_name(input_id_value) {
        let input_id_without_validation_prefix = input_id_value.replace(/validation/, '');
        let input_id_in_snake_case = input_id_without_validation_prefix.replace(/[A-Z]/g, letter => `_${letter.toLowerCase()}`);
        return input_id_in_snake_case.replace(/^\_/, ''); // returns without underscore prefix
    }

    // Add additional configuration row, if requested by the user
    $("#add-config-row").click(function () {
        // Find the last DIV with the ID "new-config-row-<NUMBER>"
        let $config_row = $('[id^="new-config-row"]:last');

        if ($config_row.prop("class").match(/d-none/g)) {
            // Add the `name` and `required` attributes to be able to submit the data and
            // to enforce the form validation
            $('div.d-none > div > div > input').each(function() {
                $(this).prop("required", true);
                $(this).attr("name", "configuration[" + convert_input_id_to_snake_case_input_name($(this).attr('id')) + "][]");
            });

            // Unhide the row
            $config_row.removeClass("d-none");
        } else {
            // Otherwise clone the DIV
            // Get the NUMBER from the DIV and increment it by one
            let next_number = parseInt($config_row.prop("id").match(/\d+/g), 10) + 1;

            // Clone the last DIV and replace the ID to make it unique
            let $clone = $config_row.clone().prop('id', 'new-config-row-' + next_number);

            // Insert the cloned DIV at the end
            $config_row.after($clone);
        }
    });

    // Remove existing, but not yet saved configuration row, if requested by the user
    $(document).on('click', '#remove-config-row', function () {
        $(this).closest('[id^="new-config-row"]').remove();
    })
</script>
