<?php
/**
 * Displays header search for desktop
 * Included in nav.php
 */
?>

<div class="nav-search hidden-xs hidden-sm">
    <ul class="nav navbar-nav navbar-right">
        <li><a href="#" id="toggle-search" aria-label="<?php _e( "Display search bar", 'bagels' ); ?>"><i class="far fa-search"></i></a></li>
    </ul>
    <div class="nav-search-field">
        <a href="#" class="nav-search-close"><i class="fal fa-times"></i></a>

        <form action="<?php echo get_site_url() ?>" method="GET">
            <div class="input-group">
                <input type="search" name="s" class="form-control" placeholder="<?php _e( 'Search', 'bagels' ); ?>" autofocus required>
                <div class="input-group-btn">
                    <button class="btn btn-default" type="submit"><i class="far fa-search"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>