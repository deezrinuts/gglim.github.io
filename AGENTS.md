# AGENTS.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

Static website for **Green Light** (Зелёный Свет) — a lighting manufacturing company (subsidiary of GSEG). Pure HTML/CSS/JS, no build tools or frameworks.

## Development

Preview locally by opening HTML files in browser:
```bash
open index.html
```

For live reload during development, use any static server:
```bash
python3 -m http.server 8000
# or
npx serve .
```

## Architecture

- **index.html** — Main landing page with sections: hero, about, advantages, products preview, contacts with form
- **pages/products.html** — Product catalog with download links for brochures (PDFs go to `assets/docs/`)
- **pages/gallery.html** — Project gallery (images go to `assets/images/gallery/`)
- **assets/css/style.css** — All styles with CSS variables for theming
- **assets/js/main.js** — Mobile menu toggle, contact form (mailto), smooth scroll

## Styling

Brand colors defined in `:root` CSS variables:
- `--color-primary: #98C454` (green)
- `--color-text: #404040` (dark gray)
- `--color-text-light: #6D6E71` (gray)

Responsive breakpoints: 992px (tablet), 768px (mobile)

## Key Patterns

- Inner pages use `../` prefix for asset paths
- Contact form submits via `mailto:info@gglim.ru` (no backend)
- Logo expected at `assets/images/logo.png`
- Product brochures: add PDFs and update `href` in product cards

## Language

Site content is in Russian. Keep all user-facing text in Russian.
