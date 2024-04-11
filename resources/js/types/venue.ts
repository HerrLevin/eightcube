export type Venue = {
    id: number,
    name: string,
    latitude: number,
    longitude: number,
    distance: number|null,
    tags: VenueTag[]
}

export type VenueTag = {
    key: string,
    value: string
}
