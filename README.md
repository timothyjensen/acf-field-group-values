[![PHP Version](https://img.shields.io/badge/php-7.0%2B-blue.svg)](https://packagist.org/packages/timothyjensen/acf-field-group-values)
[![Build Status](https://travis-ci.org/timothyjensen/acf-field-group-values.svg?branch=master)](https://travis-ci.org/timothyjensen/acf-field-group-values)
[![codecov](https://codecov.io/gh/timothyjensen/acf-field-group-values/branch/master/graph/badge.svg)](https://codecov.io/gh/timothyjensen/acf-field-group-values)

# ACF Field Group Values
This component provides a convenient alternative to ACF's `get_field()` function.  It can be installed as a WordPress plugin, or required as a dependency (preferred) within your theme or plugin.

## Requirements
- PHP 7+

## Installation
The recommended way to install this component is with Composer:

```
composer require timothyjensen/acf-field-group-values
```

Alternatively, you can download the latest release and install it like a typical WordPress plugin.

## Usage

As of version 2.0.0 the `$config` argument must contain all data from the acf field group export. This is a breaking change. Also, you should now pass `$config` instead of `$config['fields']`. This is especially important when working with clone fields.

1. [Configure ACF](https://www.advancedcustomfields.com/resources/local-json/) to save field group JSON files within your theme or plugin. Next, convert the ACF field group JSON to an array that will be passed to the helper function `get_all_custom_field_meta()`:

    ```php
    <?php

    // Replace with the name of your field group JSON.
    $field_group_json = 'group_59e226a200966.json';

    $config = json_decode( file_get_contents( PATH_TO_ACF_JSON . $field_group_json ), true );
    ```

1. Build an array containing all post meta for the specified field group:

    ```php
    <?php

    $acf_post_meta = get_all_custom_field_meta( get_the_ID(), $config );
    ```

1. Build an array containing all option values for the specified field group:

    ```php
    <?php

    $acf_option_values = get_all_custom_field_meta( 'option', $config );
    ```

1. Build an array containing all term meta for the specified field group:

    ```php
    <?php

    $term_id = 'term_2';

    $acf_term_values = get_all_custom_field_meta( $term_id, $config );
    ```

1. Build an array containing all user meta for the specified field group:

    ```php
    <?php

    $user_id = 'user_2';

    $acf_user_values = get_all_custom_field_meta( $user_id, $config );
    ```

1. In order to retrieve values for clone fields you must pass a third argument: all field group arrays that contain the fields that will be cloned.

    ```php
    <?php

    // Replace with the names of your field group JSONs.
    $clone_json_1 = 'group_59e226a200967.json';
    $clone_json_2 = 'group_59e226a200968.json';

    $clone_fields = [
    	json_decode( file_get_contents( PATH_TO_ACF_JSON . $clone_json_1 ), true ),
    	json_decode( file_get_contents( PATH_TO_ACF_JSON . $clone_json_2 ), true )
    ];

    $acf_post_meta = get_all_custom_field_meta( get_the_ID(), $config, $clone_fields );
    ```

1. As of version 3.2 you can include field labels along with values in the results.

    ```php
    <?php

    // Passing 'true' as the fourth argument will include field labels in the results.
    $acf_post_meta = get_all_custom_field_meta( get_the_ID(), $config, [], true );

    /* The above results in:
    'group' => [
        'group_1'  => [
            'label' => 'Group 1 Label',
            'value' => 'Group 1',
        ],
        'group_2'  => [
            'label' => 'Group 2 Label',
            'value' => 'Group 2',
        ],
        'subgroup' => [
            'subgroup1' => [
                'label' => 'Subgroup 1 Label',
                'value' => 'Subgroup 1',
            ],
            'subgroup2' => [
                'label' => 'Subgroup 2 Label',
                'value' => 'Subgroup 2',
            ],
        ],
    ]
    */
    ```

## Example Results

In the test results below `get_all_custom_field_meta()` was 600% faster than `get_field()` and required 19 fewer database queries.  Note that the returned values are raw meta values as opposed to formatted values that can be returned by `get_field()`.

```php
<?php

$results = [
    'group'            => [
        'group_1'  => 'Group 1',
        'group_2'  => 'Group 2',
        'subgroup' => [
            'subgroup1' => 'Subgroup 1',
            'subgroup2' => 'Subgroup 2',
        ],
    ],
    'repeater'         => [
        [
            'repeater_sub_field' => 'Sub Field',
            'repeater_2'         => [
                [
                    'repeater_2_subfield' => 'Level 2 subfield',
                ],
                [
                    'repeater_2_subfield' => 'Level 2 subfield',
                ],
            ],
        ],
    ],
    'flexible_content' => [
        [
            'acf_fc_layout'      => 'flex_content_type_1',
            'flex_content_field' => 'Flex content type 1',
        ],
        [
            'acf_fc_layout'      => 'flex_content_type_2',
            'flex_content_field' => 'Flex content type 2',
        ],
    ],
    // The following are cloned fields.
    'group_1'          => 'Cloned group 1',
    'subgroup'         => [
        'subgroup1' => 'Cloned subgroup 1',
        'subgroup2' => 'Cloned subgroup 2',
    ],
];
```
