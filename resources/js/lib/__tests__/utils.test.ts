import { describe, expect, it } from 'vitest';

import { cn, toUrl } from '../utils';

describe('cn', () => {
    it('merges class names correctly', () => {
        expect(cn('foo', 'bar')).toBe('foo bar');
    });

    it('handles conditional classes', () => {
        expect(cn('foo', false && 'bar', 'baz')).toBe('foo baz');
    });

    it('merges tailwind classes correctly', () => {
        expect(cn('px-2 py-1', 'px-4')).toBe('py-1 px-4');
    });
});

describe('toUrl', () => {
    it('returns string href as-is', () => {
        expect(toUrl('/home')).toBe('/home');
    });

    it('extracts url from route object', () => {
        expect(toUrl({ url: '/home', method: 'get' })).toBe('/home');
    });
});
