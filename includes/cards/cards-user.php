<?php


class DT_Dashboard_Plugin_User_Cards extends DT_Dashboard_Plugin_Cards
{
    public function is_card_visible( $handle) {
        $user_option = get_user_option( static::CARD_VISIBLE_OPTION_PREFIX . $handle, get_current_user_id() );
        if ($user_option === false) {
            return parent::is_card_visible( $handle );
        }
        return $user_option !== '0';
    }

    public function set_card_visibility( $handle, $visibility) {
        update_user_option( get_current_user_id(), static::CARD_VISIBLE_OPTION_PREFIX . $handle, $visibility ? 1 : 0 );
    }

    public function sort( $handles) {
        update_user_option( get_current_user_id(), static::CARD_SORT_OPTION, json_encode( $handles ) );
    }

    protected function get_card_sort_option() {
        $sort = get_user_option( static::CARD_SORT_OPTION, get_current_user_id() );
        if ( !$sort) {
            return parent::get_card_sort_option();
        }
        return $sort;
    }
}
