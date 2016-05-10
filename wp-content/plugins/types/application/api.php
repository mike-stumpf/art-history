<?php

// get all field group ids by post type
add_filter( 'types_filter_get_field_group_ids_by_post_type', array( 'Types_Api_Helper', 'get_field_group_ids_by_post_type' ), 10, 2 );