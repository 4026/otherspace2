<?php
/**
 * Configuration file for otherspace.
 */

return [

    // The difference in degrees between the latitude of the top of the tile and the bottom of the tile.
    'tile_height_deg' => 0.005,

    // The number of gradations in each dimension of each tile that item markers may spawn at.
    'tile_grid_resolution' => 10,

    // The maximum distance, in metres, that a user may be from a marker to collect an item from it.
    'item_marker_collect_radius' => 100,

    // The number of item markers that should be generated on each map tile.
    'item_markers_per_tile' => 4,

];
