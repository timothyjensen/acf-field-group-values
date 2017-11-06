# ACF Field Group Values
This component provides a convenient alternative to ACF's `get_field()` function.  It can be installed as a WordPress plugin, or required as a dependency (preferred) within your theme or plugin.

## Installation
Download the latest release and install it like a typical WordPress plugin. 

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
