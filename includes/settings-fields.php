<?php

return array(

    'Postcodes' => array(
        'post_code_repeater' => array(
            'label'     => __('Postkodszoner', 'sl-delivery-segments'),
            'type'      => 'repeater',
            'sub_fields'    => array(

                'post_code_zones' => array(

                    'label'     => __('Postkodszon', 'sl-delivery-segments'),
                    'type'      => 'repeater',
                    'sub_fields'    => array(

                        'postcode_start' => array(
                            'label'     => __('Postkod start', 'sl-delivery-segments'),
                            'type'      => 'text',
                            'desc'      => 'Start på postkodsintervall.'
                        ),
                        'postcode_end' => array(
                            'label'     => __('Postkod slut', 'sl-delivery-segments'),
                            'type'      => 'text',
                            'desc'      => 'Start på postkodsintervall.'
                        )
                    )
                ),

                'label' => array(
                    'label'     => __('Namn på segment', 'sl-delivery-segments'),
                    'type'      => 'text',
                    'desc'      => ''
                ),
                'delivery_day' => array(
                    'label'     => __('Leveransdag', 'sl-delivery-segments'),
                    'type'      => 'text',
                    'desc'      => 'Veckodag. 0-6. 0 = söndag, 6 = lördag'
                ),
            ),
            'desc'      => 'The 8 bit only decoders are a little faster and consumes less memory(16 MB instead of 32 MB by default) but does not support big images. Normal is recommended and default.'
        ),
    ),


    /*
    'Frontend' => array(
        'decoder' => array(
            'label'     => __('Javascript decoder', 'sl-delivery-segments'),
            'type'      => 'select',
            'options'   => array(
                'bpgdec8'   => 'Normal',
                'bpgdec'    => '8 bit only',
                'bpgdec8a'  => 'Normal with support for animations'
            ),
            'desc'      => 'The 8 bit only decoders are a little faster and consumes less memory(16 MB instead of 32 MB by default) but does not support big images. Normal is recommended and default.'
        ),
    ),
    'Encoder' => array(
        'algorithm' => array(
            'label'     => __('Algorithm', 'sl-delivery-segments'),
            'type'      => 'select',
            'options'   => array(
                'x265'      => 'x265',
                'jctvc'     =>'JCTVC'
            ),
            'desc'      => 'JCTVC is more efficient, but also much slower. x265 is default.'
        ),
        'speed' => array(
            'label'     => __('Speed', 'sl-delivery-segments'),
            'type'      => 'select',
            'options'   => range( 1, 9 ),
            'desc'      => 'Encoding speed. Lower = faster but encoding, but larger images. This is only used with the x265 algorithm. Default is 6.'
        ),
        'keep-metadata' => array(
            'label'     => __('Keep metadata', 'sl-delivery-segments'),
            'type'      => 'checkbox',
            'options'   => array(
                'x265'      => 'x265',
                'jctvc'     =>'JCTVC'
            ),
            'desc'      => ''
        ),
    )
    */
);