<?php

namespace ContentTranslate;

class Content Extends Entity\Translate
{
    public function __construct()
    {
    }
}


/*


// Load our function when hook is set
add_action( 'pre_get_posts', 'rc_modify_query_get_posts_by_date' );

function rc_modify_query_get_posts_by_date( $query ) {

    // Check if on frontend and main query is modified
    if( ! is_admin() && $query->is_main_query() ) {

        $today = getdate();
        $query->set( 'year', $today['year'] );
        $query->set( 'monthnum', $today['mon'] );
        $query->set( 'day', $today['mday'] );

    }

}

*/
