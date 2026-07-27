# Illuminate Challenge - Stage 3: Centroid of Chaos

This project implements Stage 3 of the Bi-Tech Senior Laravel Hiring Challenge.

## Data Source

The data is sourced from a remote PostgreSQL repository accessed via SSH tunnel. The data includes neighborhoods with polygon boundaries and incidents with geographic coordinates.

## Commands Available

### 1. Import Data
```bash
php artisan import:data
```
Imports neighborhoods and incidents from the remote PostgreSQL repository into the local SQLite database. This command calculates polygon centroids and handles coordinate conversion.

### 2. Get Flag
```bash
php artisan flag:get
```
Retrieves incidents within the donut around neighborhood NB-7A2F, orders them by distance, and concatenates their codes to reveal the flag.

## Custom Relationship

The project uses a custom Eloquent relationship `DonutRelation` that extends Laravel's `Relation` class. This custom relationship filters incidents based on geographic distance from a neighborhood centroid within specified inner and outer radii.

## Helper

The `IncidentHelper` class provides utility functions for geographic calculations, specifically the Haversine formula for calculating distances between geographic coordinates. This helper is used as a tool during the specific use case of distance-based filtering.

## Donut Parameters

- **Target neighborhood**: NB-7A2F
- **Inner radius**: 0.5 km
- **Outer radius**: 2.0 km

## Technical Notes

- PostgreSQL PostGIS uses (longitude, latitude) coordinate order
- Local SQLite database stores coordinates in (latitude, longitude) order
- Distance calculations use the Haversine formula with Earth radius of 6371 km