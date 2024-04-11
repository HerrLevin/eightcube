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

export type User = {
    id: number,
    name: string,
}

export type Status = {
    id: number,
    body: string|null,
    user: User,
    venue: Venue,
    created_at: string,
    updated_at: string
}
