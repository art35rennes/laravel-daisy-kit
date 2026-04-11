import { describe, it, expect } from 'vitest';
import { TILE_PROVIDERS } from '../../../resources/js/leaflet/index.js';

describe('TILE_PROVIDERS lookup', () => {
    it('contains the osm provider', () => {
        expect(TILE_PROVIDERS).toHaveProperty('osm');
        expect(TILE_PROVIDERS.osm.url).toContain('openstreetmap.org');
        expect(TILE_PROVIDERS.osm.options.attribution).toBeTruthy();
    });

    it('contains cartodb.positron', () => {
        expect(TILE_PROVIDERS).toHaveProperty('cartodb.positron');
        expect(TILE_PROVIDERS['cartodb.positron'].url).toContain('basemaps.cartocdn.com');
        expect(TILE_PROVIDERS['cartodb.positron'].url).toContain('light_all');
    });

    it('contains cartodb.darkmatter', () => {
        expect(TILE_PROVIDERS).toHaveProperty('cartodb.darkmatter');
        expect(TILE_PROVIDERS['cartodb.darkmatter'].url).toContain('dark_all');
    });

    it('contains cartodb.voyager', () => {
        expect(TILE_PROVIDERS).toHaveProperty('cartodb.voyager');
        expect(TILE_PROVIDERS['cartodb.voyager'].url).toContain('voyager');
    });

    it('contains stamen.toner', () => {
        expect(TILE_PROVIDERS).toHaveProperty('stamen.toner');
        expect(TILE_PROVIDERS['stamen.toner'].url).toContain('stamen_toner');
    });

    it('contains stamen.watercolor', () => {
        expect(TILE_PROVIDERS).toHaveProperty('stamen.watercolor');
        expect(TILE_PROVIDERS['stamen.watercolor'].url).toContain('stamen_watercolor');
    });

    it('all providers have url and options with attribution', () => {
        for (const [name, provider] of Object.entries(TILE_PROVIDERS)) {
            expect(provider.url, `${name} should have a url`).toBeTruthy();
            expect(provider.options, `${name} should have options`).toBeTruthy();
            expect(provider.options.attribution, `${name} should have attribution`).toBeTruthy();
        }
    });

    it('all provider URLs contain tile template placeholders', () => {
        for (const [name, provider] of Object.entries(TILE_PROVIDERS)) {
            expect(provider.url, `${name} URL should contain {z}`).toContain('{z}');
            expect(provider.url, `${name} URL should contain {x}`).toContain('{x}');
            expect(provider.url, `${name} URL should contain {y}`).toContain('{y}');
        }
    });
});
