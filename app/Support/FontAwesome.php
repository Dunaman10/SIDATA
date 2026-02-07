<?php

namespace App\Support;

class FontAwesome
{
    /**
     * Get icons for Select component with HTML preview
     * Returns array of 'icon-class' => '<i class="icon-class"></i> Label'
     */
    public static function getIconsWithPreview(): array
    {
        $icons = self::getIcons();
        $result = [];
        
        foreach ($icons as $class => $label) {
            $result[$class] = '<i class="' . $class . '" style="margin-right: 8px;"></i> ' . $label;
        }
        
        return $result;
    }

    /**
     * Get plain icons list
     * Returns array of 'icon-class' => 'Label'
     */
    public static function getIcons(): array
    {
        return [
            // Icons from landing page
            'fa-solid fa-mosque' => 'Mosque',
            'fa-solid fa-house' => 'House',
            'fa-solid fa-chalkboard-user' => 'Private Class',
            'fa-solid fa-book-quran' => 'Al-Quran',
            'fa-solid fa-people-group' => 'People Group',
            'fa-solid fa-futbol' => 'Futsal',
            'fa-solid fa-volleyball' => 'Volleyball',
            'fa-solid fa-basketball' => 'Basketball',
            
            // Common icons
            'fa-solid fa-user' => 'User',
            'fa-solid fa-users' => 'Users',
            'fa-solid fa-check' => 'Check',
            'fa-solid fa-download' => 'Download',
            'fa-solid fa-image' => 'Image',
            'fa-solid fa-phone' => 'Phone',
            'fa-solid fa-envelope' => 'Envelope',
            'fa-solid fa-star' => 'Star',
            'fa-solid fa-location-dot' => 'Location Dot',
            'fa-solid fa-music' => 'Music',
            'fa-solid fa-heart' => 'Heart',
            'fa-solid fa-cloud' => 'Cloud',
            'fa-solid fa-comment' => 'Comment',
            'fa-solid fa-comments' => 'Comments',
            'fa-solid fa-calendar-days' => 'Calendar Days',
            'fa-solid fa-bell' => 'Bell',
            'fa-solid fa-cart-shopping' => 'Cart Shopping',
            'fa-solid fa-clipboard' => 'Clipboard',
            'fa-solid fa-circle-info' => 'Circle Info',
            'fa-solid fa-bolt' => 'Bolt',
            'fa-solid fa-car' => 'Car',
            'fa-solid fa-mug-hot' => 'Mug Hot',
            'fa-solid fa-circle-user' => 'Circle User',
            'fa-solid fa-pen' => 'Pen',
            'fa-solid fa-gift' => 'Gift',
            'fa-solid fa-gear' => 'Gear',
            'fa-solid fa-gears' => 'Gears',
            'fa-solid fa-trash' => 'Trash',
            'fa-solid fa-lock' => 'Lock',
            'fa-solid fa-headphones' => 'Headphones',
            'fa-solid fa-tag' => 'Tag',
            'fa-solid fa-book' => 'Book',
            'fa-solid fa-bookmark' => 'Bookmark',
            'fa-solid fa-print' => 'Print',
            'fa-solid fa-camera' => 'Camera',
            'fa-solid fa-video' => 'Video',
            'fa-solid fa-droplet' => 'Droplet',
            'fa-solid fa-plus' => 'Plus',
            'fa-solid fa-minus' => 'Minus',
            'fa-solid fa-share' => 'Share',
            'fa-solid fa-fire' => 'Fire',
            'fa-solid fa-eye' => 'Eye',
            'fa-solid fa-plane' => 'Plane',
            'fa-solid fa-hand' => 'Hand',
            'fa-solid fa-folder' => 'Folder',
            'fa-solid fa-folder-open' => 'Folder Open',
            'fa-solid fa-money-bill' => 'Money Bill',
            'fa-solid fa-thumbs-up' => 'Thumbs Up',
            'fa-solid fa-thumbs-down' => 'Thumbs Down',
            'fa-solid fa-key' => 'Key',
            'fa-solid fa-paper-plane' => 'Paper Plane',
            'fa-solid fa-code' => 'Code',
            'fa-solid fa-globe' => 'Globe',
            'fa-solid fa-truck' => 'Truck',
            'fa-solid fa-city' => 'City',
            'fa-solid fa-tree' => 'Tree',
            'fa-solid fa-wifi' => 'Wifi',
            'fa-solid fa-bicycle' => 'Bicycle',
            'fa-solid fa-brush' => 'Brush',
            'fa-solid fa-flask' => 'Flask',
            'fa-solid fa-briefcase' => 'Briefcase',
            'fa-solid fa-compass' => 'Compass',
            'fa-solid fa-person' => 'Person',
            'fa-solid fa-person-dress' => 'Person Dress',
            'fa-solid fa-address-book' => 'Address Book',
            'fa-solid fa-bath' => 'Bath',
            'fa-solid fa-handshake' => 'Handshake',
            'fa-solid fa-snowflake' => 'Snowflake',
            'fa-solid fa-earth-americas' => 'Earth Americas',
            'fa-solid fa-cloud-arrow-up' => 'Cloud Arrow Up',
            'fa-solid fa-palette' => 'Palette',
            'fa-solid fa-layer-group' => 'Layer Group',
            'fa-solid fa-gamepad' => 'Gamepad',
            'fa-solid fa-feather' => 'Feather',
            'fa-solid fa-sun' => 'Sun',
            'fa-solid fa-moon' => 'Moon',
            'fa-solid fa-link' => 'Link',
            'fa-solid fa-fish' => 'Fish',
            'fa-solid fa-bug' => 'Bug',
            'fa-solid fa-shop' => 'Shop',
            'fa-solid fa-landmark' => 'Landmark',
            'fa-solid fa-shirt' => 'Shirt',
            'fa-solid fa-anchor' => 'Anchor',
            'fa-solid fa-bag-shopping' => 'Bag Shopping',
            'fa-solid fa-brain' => 'Brain',
            'fa-solid fa-cookie' => 'Cookie',
            'fa-solid fa-leaf' => 'Leaf',
            'fa-solid fa-carrot' => 'Carrot',
            'fa-solid fa-kaaba' => 'Kaaba',
            'fa-solid fa-hands-praying' => 'Hands Praying',
            'fa-solid fa-person-praying' => 'Person Praying',
            'fa-solid fa-bed' => 'Bed',
            'fa-solid fa-school' => 'School',
            'fa-solid fa-graduation-cap' => 'Graduation Cap',
            'fa-solid fa-building' => 'Building',
            'fa-solid fa-building-columns' => 'Building Columns',
            'fa-solid fa-utensils' => 'Utensils',
            'fa-solid fa-shower' => 'Shower',
            'fa-solid fa-tv' => 'TV',
            'fa-solid fa-couch' => 'Couch',
            'fa-solid fa-dumbbell' => 'Dumbbell',
            'fa-solid fa-swimmer' => 'Swimmer',
            'fa-solid fa-running' => 'Running',
            'fa-solid fa-child' => 'Child',
            'fa-solid fa-children' => 'Children',
            'fa-solid fa-hospital' => 'Hospital',
            'fa-solid fa-stethoscope' => 'Stethoscope',
            'fa-solid fa-cross' => 'Cross',
            'fa-solid fa-quran' => 'Quran (Alt)',
        ];
    }
}
