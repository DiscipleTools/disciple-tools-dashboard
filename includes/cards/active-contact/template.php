<div class="card-header">
    <?php echo esc_html( $card->label ) ?>
</div>
<div class="card-body">
    <span class="numberCircle">&nbsp;<span id="active_contacts">-</span>&nbsp;</span>
    <a class="view-all button" href="<?php echo esc_url( home_url( '/' ) ) . "contacts/new" ?>">
        <?php esc_html_e( "Add a contact", 'disciple-tools-dashboard' ) ?>
    </a>
</div>
