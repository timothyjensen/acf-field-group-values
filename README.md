# ACF Field Group Values
This component provides a convenient alternative to ACF's `get_field()` function.  It can be installed as a WordPress plugin, or required as a dependency (preferred) within your theme or plugin.

## Installation
The recommended way to install this component is with Composer:
```
composer require timothyjensen/acf-field-group-values
```

Alternatively, you can download the latest release and install it like a typical WordPress plugin. 

## Usage

Convert the ACF field group JSON to an array that will be passed to the helper function `get_all_custom_field_meta()`:
```php
<?php

// Replace with the name of your field group JSON.
$field_group_json = 'group_59e226a200966.json';

$field_group_array = json_decode( file_get_contents( PATH_TO_ACF_JSON . $field_group_json ), true );

$config = $field_group_array['fields'];
```

Build an array containing all post meta for the specified field group:
```php
<?php

$post_id = get_the_ID();

$acf_post_meta = get_all_custom_field_meta( $post_id, $config );
```

Build an array containing all option values for the specified field group:
```php
<?php

$acf_option_values = get_all_custom_field_meta( 'option', $config );
```

## Example Results

In the test results below `get_all_custom_field_meta()` was 600% faster than `get_field()` and required 19 fewer database queries.

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
	'clone'            => [
		'group_1'  => 'Cloned group 1',
		'subgroup' => [
			'subgroup1' => 'Cloned subgroup 1',
			'subgroup2' => 'Cloned subgroup 2',
		],
	],
];
```
