# Dossier: T-20260503-031-antigravity-refactor-about-docs

- **Task ID**: `T-20260503-031`
- **Owner**: `🏗️ Antigravity`
- **Scope**: `pi-store-webapp` (AboutPage, DocsPage, messages.js)
- **Status**: `active`
- **Priority**: `high`

## 🎯 Goal
Refactor and modernize the About and Docs pages in `pi-store-webapp`. Replace hardcoded "old/irrelevant" content with professional, i18n-driven content that aligns with the SaigonHouse branding and current project roadmap.

## 🏛️ Context
- The `AboutPage` currently contains generic "Pi Ecosystem" text.
- The `DocsPage` is a simple placeholder list.
- User wants these updated to reflect the actual project state (SaigonHouse developing Pi Ecosystem).

## 🏗️ Proposed Changes
1.  **[MODIFY] [messages.js](file:///c:/Users/Administrator/Local%20Sites/saigonhouse/app/public/wp-content/pi-store-webapp/src/i18n/messages.js)**: Add `about` and `docs` sections to the dictionary.
2.  **[MODIFY] [AboutPage.jsx](file:///c:/Users/Administrator/Local%20Sites/saigonhouse/app/public/wp-content/pi-store-webapp/src/pages/public/AboutPage.jsx)**: Refactor to use `useLocale()` and display content from `messages.js`.
3.  **[MODIFY] [DocsPage.jsx](file:///c:/Users/Administrator/Local%20Sites/saigonhouse/app/public/wp-content/pi-store-webapp/src/pages/public/DocsPage.jsx)**: Refactor to use `useLocale()` and modernize the UI.

## 🧪 Quality Gates
- `npm run build` in `pi-store-webapp`.
- Manual verification of UI layout.

## 📅 Timeline
- Start: 2026-05-03 22:20
- Estimated Completion: 2026-05-03 22:45
