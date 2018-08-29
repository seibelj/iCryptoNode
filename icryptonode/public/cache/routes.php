<?php $o = array();

// ** THIS IS AN AUTO GENERATED FILE. DO NOT EDIT MANUALLY ** 

//==================== v1 ====================

$o['v1'] = array();

//==== v1 vpn/status ====

$o['v1']['vpn/status'] = array (
    'GET' => 
    array (
        'url' => 'vpn/status',
        'className' => 'Vpn',
        'path' => 'vpn',
        'methodName' => 'status',
        'arguments' => 
        array (
            'id' => 0,
            'date' => 1,
        ),
        'defaults' => 
        array (
            0 => NULL,
            1 => NULL,
        ),
        'metadata' => 
        array (
            'description' => 'Endpoint for getting VPN status',
            'longDescription' => '',
            'url' => 'GET status',
            'return' => 
            array (
                'type' => 'mixed',
                'description' => '',
            ),
            'scope' => 
            array (
                '*' => '',
            ),
            'resourcePath' => 'vpn/',
            'param' => 
            array (
                0 => 
                array (
                    'name' => 'id',
                    'label' => 'Id',
                    'default' => NULL,
                    'required' => true,
                    'children' => 
                    array (
                    ),
                    'properties' => 
                    array (
                        'from' => 'query',
                    ),
                    'type' => 'string',
                ),
                1 => 
                array (
                    'name' => 'date',
                    'label' => 'Date',
                    'default' => NULL,
                    'required' => false,
                    'children' => 
                    array (
                    ),
                    'properties' => 
                    array (
                        'from' => 'query',
                    ),
                    'type' => 'string',
                ),
            ),
        ),
        'accessLevel' => 0,
    ),
);

//==================== apiVersionMap ====================

$o['apiVersionMap'] = array();

//==== apiVersionMap Vpn ====

$o['apiVersionMap']['Vpn'] = array (
    1 => 'Vpn',
);
return $o;