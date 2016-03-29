<?php

$settings_data = $settings->get();

var_dump($settings_data);

$sections = $settings->get_settings_fields( true );

?>

<form method="post" action="<?php echo $form_post_url ?>">

    <?php
    wp_nonce_field( "sl-delivery-segments-settings" );
    ?>
    <input type="hidden" name="sl-delivery-segments-settings-submit" value="1" />

    <div id="icon-tools" class="icon32"></div>

        <?php foreach( $sections AS $section_label => $fields ) : ?>

            <h3><?php echo $section_label; ?></h3>

            <table class="form-table">
                <tbody>
            <?php foreach( $fields AS $key => $field ) : ?>

                <?php $settings->generate_field( $key, $field, $settings_data ); ?>

            <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>


    <p class="submit" style="clear: both;">
        <input type="submit" name="Submit"  class="button-primary" value="Update Settings" />
    </p>

</form>
