// this service maps osm categories to fontawesome icons

import {VenueTag} from "@/types/venue";

const osmCategoryToIcon: Record<string, Record<string, string>> = {
    'amenity': {
        'restaurant': 'fa-utensils',
        'cafe': 'fa-coffee',
        'bar': 'fa-glass-martini-alt',
        'pub': 'fa-whiskey-glass',
        'fast_food': 'fa-hamburger',
        'ice_cream': 'fa-ice-cream',
        'biergarten': 'fa-beer-mug-empty',
        'museum': 'fa-building-columns',
        'theatre': 'fa-masks-theater',
        'cinema': 'fa-film',
        'nightclub': 'fa-music',
        'arts_centre': 'fa-paint-brush',
        'casino': 'fa-dice',
        'internet_cafe': 'fa-at',
        'public_bookcase': 'fa-book',
        'public_bath': 'fa-person-swimming',
        'toilets': 'fa-restroom',
        'waste_basket': 'fa-trash-can',
        'waste_disposal': 'fa-trash-can',
        'vending_machine': 'fa-store',
        'bench': 'fa-chair',
        'shelter': 'fa-person-shelter',
        'drinking_water': 'fa-faucet',
        'fountain': 'fa-faucet',
        'bbq': 'fa-burger',
        'shower': 'fa-shower',
        'bank': 'fa-money-bills',
        'atm': 'fa-money-bills',
        'bureau_de_change': 'fa-money-bill-transfer',
        'pharmacy': 'fa-prescription',
        'hospital': 'fa-hospital',
        'doctors': 'fa-user-doctor',
        'clinic': 'fa-hostpital',
        'dentist': 'fa-tooth',
        'veterinary': 'fa-paw',
        'post_box': 'fa-envelope',
        'post_office': 'fa-envelope',
        'parcel_locker': 'fa-envelopes-bulk',
        'telephone': 'fa-phone',
        'parking': 'fa-square-parking',
        'fuel': 'fa-gas-pump',
        'bicycle_parking': 'fa-bicycle',
        'bus_station': 'fa-bus',
        'bicycle_rental': 'fa-bicycle',
        'taxi': 'fa-taxi',
        'charging_station': 'fa-charging-station',
        'car_rental': 'fa-car',
        'parking_entrance': 'fa-square-parking',
        'ferry_terminal': 'fa-ferry',
        'motorcycle_parking': 'fa-motorcycle',
        'bicycle_repair_station': 'fa-bicycle',
        'boat_rental': 'fa-sailboat',
        'police': 'fa-handcuffs',
        'townhall': 'fa-building-columns',
        'fire_station': 'fa-fire-extinguisher',
        'social_facility': 'fa-users',
        'courthouse': 'fa-scale-balanced',
        'place_of_worship': 'fa-place-of-worship',
        'marketplace': 'fa-shopping-basket',
        'car_wash': 'fa-car-tunnel',
        'vehicle_inspection': 'fa-car-on',
        'driving_school': 'fa-car-burst',
        'nursing_home': 'fa-hospital-user',
        'childcare': 'fa-baby',
        'hunting_stand': 'fa-person-rifle',
        'college': 'fa-building-columns',
    },
    'shop': {
        'supermarket': 'fa-shopping-cart',
        'bakery': 'fa-bread-slice',
        'butcher': 'fa-drumstick-bite',
        'coffee': 'fa-coffee',
        'convenience': 'fa-store-alt',
        'jewelry': 'fa-gem',
        'chocolate': 'fa-cookie-bite',
        'books': 'fa-book',
        'tobacco': 'fa-smoking',
        'chemist': 'fa-prescription',
        'clothes': 'fa-shirt',
        'cosmetics': 'fa-paintbrush',
        'fashion_accessories': 'fa-shirt',
        'ticket': 'fa-ticket',
        'kiosk': 'fa-store',
        'hairdresser': 'fa-scissors'
    },
    'tourism': {
        'artwork': 'fa-paint-brush',
        'community_centre': 'fa-users',
        'library': 'fa-book-open-reader',
        'gallery': 'fa-palette',
        'hotel': 'fa-bed',
        'attraction': 'fa-camera',
        'information': 'fa-info',
    },
    'leisure': {
        'outdoor_seating': 'fa-chair',
        'amusement_arcade': 'fa-gamepad',
        'park': 'fa-tree',
        'playground': 'fa-child',
        'sports_centre': 'fa-futbol',
    },
    'building': {
        'school': 'fa-school',
        'university': 'fa-building-columns',
        'kindergarten': 'fa-baby',
    },
    'healthcare': {
        'hospital': 'fa-hospital',
        'clinic': 'fa-clinic-medical',
        'doctors': 'fa-user-md',
    },
    'historic': {
        'memorial': 'fa-monument',
        'monument': 'fa-monument',
        'archeological_site': 'fa-person-digging',
        'wayside_shrine': 'fa-place-of-worhsip',
        'castle': 'fa-chess-rook',
    },
    'highway': {
        'bus_stop': 'fa-bus',
    },
    'railway': {
        'station': 'fa-train',
        'subway_entrance': 'fa-train-subway',
        'tram_stop': 'fa-train-tram',
    },
    'office': {
        'lawyer': 'fa-section'
    }
}

const fallbackIcons: Record<string, string> = {
    'shop': 'fa-store',
    'tourism': 'fa-camera',
    'leisure': 'fa-tree',
    'building': 'fa-building',
    'historic': 'fa-monument',
    'public_transport': 'fa-bus',
    'office': 'fa-building',
}

export function osmCategoryToIconMapper(category: string, subcategory: string): string {
    if (osmCategoryToIcon[category] && osmCategoryToIcon[category][subcategory]) {
        return osmCategoryToIcon[category][subcategory];
    }
    return 'fa-map-marker';
}

export function getFallbackIcon(category: string): string {
    if (fallbackIcons[category]) {
        return fallbackIcons[category];
    }
    return 'fa-map-marker';
}

export function getIconFromTags(tags: VenueTag[]): string {
    for (const tag of tags) {
        let icon = osmCategoryToIconMapper(tag.key, tag.value);
        if (icon !== 'fa-map-marker') {
            return icon;
        }
    }
    for (const tag of tags) {
        let icon = getFallbackIcon(tag.key);
        if (icon !== 'fa-map-marker') {
            return icon;
        }
    }
    return 'fa-map-marker';
}
