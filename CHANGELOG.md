# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.0] - 2026-02-28

### Added

- Phase 1 Indigenous cultural tree and public pages with teachings, clans, and ceremonies
- Laravel Horizon for queue processing
- Admin CRUD pages, controllers, and routes for events, groups, and teachings
- Sidebar navigation for admin sections
- Model factories and database seeders for events, groups, and teachings
- Home page wired to display real article data from the backend
- NorthCloud content integration for Redis-based article ingestion
- Drumbeat homepage with hero, news, events, teachings, community, and language sections
- Public layout redesigned with Drumbeat navigation and footer
- Drumbeat color palette, typography tokens, and brand fonts (DM Serif Display + Source Sans 3)
- Diidjaaheer branding replacing Laravel Starter Kit defaults
- ESLint configuration and automated Wayfinder route generation
- CLAUDE.md project guidance
- Deployment pipeline for diidjaaheer.live with Caddy, shared database, and service management
- TypeScript type-checking and Laravel Boost MCP setup
- Bootstrap of Diidjaaheer Anishinaabe site

### Changed

- NorthCloud channels updated to reflect Indigenous terminology and structure
- NorthCloud channels narrowed to Anishinaabe-only, removing articles:default
- Deployment configuration updated from coforge.xyz to diidjaaheer.live
- Caddy configured with ACME TLS for CT compliance

### Fixed

- Caddy log path corrected to /var/log/caddy with improved reload error handling
- CI updated to use Composer Deployer (vendor/bin/dep) instead of phar
- ESLint parsing error in ArticleForm Select components resolved
- Deploy config corrected for CI/CD SSH and host resolution
