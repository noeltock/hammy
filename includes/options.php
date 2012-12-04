<?php

/**
 * Register Settings
 */
function hammy_options_init() {

        register_setting( 'hammy_options', 'hammy_options' );
        add_settings_section( 'hammy_section_main', '', 'hammy_options_header', 'hammy_section' );
        add_settings_field( 'hammy_parent', '', 'hammy_field_parent', 'hammy_section', 'hammy_section_main' );
        add_settings_field( 'hammy_breakpoints', '', 'hammy_field_breakpoints', 'hammy_section', 'hammy_section_main' );
        add_settings_field( 'hammy_ignores', '', 'hammy_field_ignores', 'hammy_section', 'hammy_section_main' );

}

/**
 * Setting Fields
 */

function hammy_options_header () {}

function hammy_field_parent() {

        $options = get_option( 'hammy_options' );
        $value = $options['hammy_parent'];

        ?>

        <h3>1) Enter your Content Container</h3>
        <p>Add the content class or ID that is consistent across all templates (i.e. on every page and post), oftentimes named <em>#content</em> or <em>.entry-content</em>, etc. Make sure you use the approriate prefix.</p>

        <input name="hammy_options[hammy_parent]" class="hammy-content" type="text" value="<?php echo $value; ?>" placeholder=".your_content_class" />

        <?php

}


function hammy_field_breakpoints() {

        $options = get_option( 'hammy_options' );
        $value = $options['hammy_breakpoints'];

        ?>

        <h3>2) Add your Breakpoints</h3>
        <p>Your breakpoints should be relevant to the container above. See the example on the <a href="h" target="_blank">FAQ</a> to get a better understanding.</p>

        <div class="hammy-bp-wrap" data-type="breakpoints">
            <input id='hammy-breakpoints-val' name='hammy_options[hammy_breakpoints]' type='hidden' value='<?php echo $value; ?>' />
        </div>

        <input id="hammy-add-breakpoint" class="hammy-input" type="text" placeholder="Add another Breakpoint" /> <input id="hammy-add-breakpoint-button" type="button" value="Add Breakpoint" />

        <?php

}

function hammy_field_ignores() {

    $options = get_option( 'hammy_options' );
    $value = $options['hammy_ignores'];

    ?>

    <h3>3) Add Classes to Ignore</h3>

    <div class="hammy-ignore-wrap" data-type="ignores">
        <input id='hammy-ignores-val' name='hammy_options[hammy_ignores]' type='hidden' value='<?php echo $value; ?>' />
    </div>

    <input id="hammy-add-ignore" class="hammy-input" type="text" placeholder="Add another Class to ignore" /> <input id="hammy-add-ignore-button" type="button" value="Add Ignore" />

    <?php

}

/**
 * Register Page
 */
function hammy_add_page() {

        add_options_page( 'Hammy', 'Hammy', 'manage_options', 'hammy', 'hammy_options_page' );

    }

/**
 * Display Page
 */
function hammy_options_page() {
    ?>

	<div class="wrap hammy-page">
	
		<h2>Hammy</h2>
		
		<p>Welcome to Hammy. Go through the steps below for optimized content images:</p>

        <form action="options.php" method="post">
			<?php settings_fields( 'hammy_options' ); ?>
			<?php do_settings_sections( 'hammy_section' ); ?>
			<br />
			<input name="Submit" type="submit" style="margin-top:20px" value="<?php esc_attr_e('Save changes'); ?>" />
		</form>

	</div>

    <script>

        jQuery(document).ready(function($) {

            var breakpoints = $('input#hammy-breakpoints-val');
            var ignores = $('input#hammy-ignores-val');

            // functions

            var add_value = function(field, value) {

                var data = $(field).val();
                var array = [];

                if (typeof data != 'string') {data = '';}

                if ( data.indexOf(',') >= 0 ) {

                    array = data.split(',');

                } else {

                    if ( data.length > 0 ) {

                        array.push(data);

                    }

                }

                array.push(value);
                var output = array.join(',');
                $(field).val(output);

            }


            var add_numeric_value = function(field, value) {

                var data = $(field).val();
                var array = [];

                if (typeof data != 'string') {data = '';}

                if ( data.indexOf(',') >= 0 ) {

                    array = $.map(data.split(','), Number);

                } else {

                    if (data > 0 ) {

                        array.push(parseInt(data));

                    }
                }

                array.push(parseInt(value));
                array.sort(function(a,b){
                    return a-b;

                });

                var output = array.join(',');
                $(field).val(output);

            }

            var remove_value = function(field, value) {

                var data = $(field).val();
                if (typeof data != 'string') {data = '';}

                var array = data.split(',');

                array = $.grep(array, function(v) {
                    return v != value;
                });

                var output = array.join(',');
                $(field).val(output);

            }

            var update_view = function(field) {

                var data = $(field).val();

                if (typeof data != 'string' || data == '') {return;}

                var array = data.split(',');
                $(field).parent().find('.hammy-data').remove();
                $.each(array, function() {
                    $(field).parent().append('<div class="hammy-data" data-value="' + this + '">' + this + '<span class="hammy-delete">x</span></div>');
                });

            }

            // init

            update_view('#hammy-breakpoints-val');
            update_view('#hammy-ignores-val');

            // click breakpoint

            $('#hammy-add-breakpoint-button').click(function() {
                add_numeric_value(breakpoints, $('input#hammy-add-breakpoint').val());
                update_view(breakpoints);
                $('input#hammy-add-breakpoint').val("");
            });

            $('#hammy-add-ignore-button').click(function() {
                add_value(ignores, $('input#hammy-add-ignore').val());
                update_view(ignores);
                $('input#hammy-add-ignore').val("");
            });

            $('body').delegate(".hammy-delete", "click", function(event){
                var value = $(this).parent().data('value');
                var field = $(this).parent().parent().find('input');
                var type = $(this).parent().parent().data('type');
                $(this).parent().fadeOut().remove();
                remove_value(field, value);
                update_view(type);
            });

        });

    </script>

<?php

} ?>