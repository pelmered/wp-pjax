<?php
global $pagenow;
?>

<div class="wrap">
    <h2>Leveranssegmentering Settings</h2>

    <?php
    if ( 'true' == esc_attr( $_GET['updated'] ) ) echo '<div class="updated" ><p>Theme Settings updated.</p></div>';

    $tabs = array(
        /*
        'convert' => array(
            'label'     => 'Convert images',
            'template'  => 'convert.php'
        ),
        */
        'settings' => array(
            'label'     => 'Settings',
            'template'  => 'settings.php'
        )
    );

    if( isset ( $_GET['tab'] ) && in_array( $_GET['tab'], array_keys( $tabs ) ) )
    {
        $current_tab = $_GET['tab'];
    }
    else
    {
        $current_tab = array_keys($tabs)[0];
    }

    $links = array();
    ?>
    <div id="icon-themes" class="icon32"><br></div>
    <h2 class="nav-tab-wrapper">

    <?php foreach($tabs as $tab_slug => $tab ) :
        $class = ( $tab_slug == $current_tab ) ? ' nav-tab-active' : '';
        ?>
        <a class='nav-tab<?php echo $class; ?>' href='?page=sl-delivery-segments&tab=<?php echo $tab_slug; ?>'><?php echo $tab['label']; ?></a>
    <?php endforeach; ?>
    </h2>

    <div id="poststuff">
        <?php
        if( file_exists( sl_delivery_segments::get_config('plugin_path') . 'templates/' . $tabs[$current_tab]['template'] ) )
        {
            include sl_delivery_segments::get_config('plugin_path') . 'templates/' . $tabs[$current_tab]['template'];
        }
        ?>
    </div>

</div>