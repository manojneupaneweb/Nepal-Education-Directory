# Shiksha Link UI audit

Baseline commit `75abe93`, with uncommitted directory pages, college logos, and the website-sections migration left in place.

## Problems

- Public pages used emoji in the header, mobile menu, and footer instead of one icon style.
- Directory heroes were full-bleed bright blue with large type, while inner pages used a different light layout.
- The footer was a dark block against a light site, which made the page feel split in two.
- Schools and colleges repeated the same filter and card markup.
- Focus states were easy to miss. Decorative motion on the home hero did not follow reduced-motion.
- Admin used the same bright navy as the public footer, with little status hierarchy.

## Design system

- Type: Source Sans 3, which is already loaded and remains readable with Nepali text through the system font fallback.
- Color: navy `#163a6b` for actions, off-white `#f3f5f8` page background, charcoal text, emerald and amber only for government/private and status.
- Shape: 12px cards, light borders, shadow only on hover or on the header.
- Icons: one inline SVG set in `resources/views/components/icon.blade.php`. No new package.

## Pages in this pass

Header, footer, home hero, schools, colleges, and the admin frame. Other public pages keep their content and pick up the shared type, color, header, and footer.
