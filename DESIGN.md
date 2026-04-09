# Design System Document: The Modern Heritage

## 1. Overview & Creative North Star

### The Creative North Star: "The Modern Heritage"
This design system is not merely a collection of components; it is a digital sanctuary. We are moving away from the cold, clinical nature of standard medical apps toward a "Modern Heritage" aesthetic—an editorial experience that blends the timeless wisdom of traditional Indian maternal care with the precision of modern high-end design.

To achieve this, we break the "standard template" look by embracing **intentional asymmetry**, **breathable editorial whitespace**, and **tonal depth**. We treat the screen like a premium textile or a piece of handcrafted parchment, where every element feels placed with intention rather than snapped to a rigid, predictable grid.

---

## 2. Colors & Tonal Architecture

Our palette is rooted in the earth and the skin. It avoids the "Corporate Blue" of healthcare, opting instead for the warmth of terracotta and the purity of cream.

### The Color Palette (Material Design Tokens)
*   **Primary (Terracotta):** `#93452d` — Used for brand authority and primary actions.
*   **Secondary (Dusty Rose/Taupe):** `#725951` — For supportive elements and grounding.
*   **Tertiary (Turmeric Gold):** `#755700` — For accents, highlights, and "moments of joy."
*   **Background (Cream):** `#fef8f3` — The base of our entire experience.

### The "No-Line" Rule
Standard UI relies on 1px borders to separate content. **In this design system, visible 1px solid borders are strictly prohibited for sectioning.** 
Boundaries must be defined solely through:
1.  **Background Color Shifts:** Placing a `surface-container-low` (`#f8f3ee`) section against a `surface` (`#fef8f3`) background.
2.  **Tonal Transitions:** Using subtle shifts in the surface hierarchy to define where one thought ends and another begins.

### Surface Hierarchy & Nesting
Treat the UI as physical layers of fine paper. 
*   **Base:** `surface` (`#fef8f3`)
*   **De-emphasized content:** `surface-container-low` (`#f8f3ee`)
*   **Card/Component Base:** `surface-container` (`#f2ede8`)
*   **High Prominence:** `surface-container-highest` (`#e6e2dd`)

### Signature Textures & Glassmorphism
To create a premium feel, use **Glassmorphism** for floating headers or navigation bars. Use a semi-transparent `surface` color with a `backdrop-blur` of 20px. 
*   **CTAs:** Enhance primary buttons with a subtle gradient transitioning from `primary` (`#93452d`) to `primary_container` (`#b25d43`) to give them a "soul" and dimension that flat colors cannot provide.

---

## 3. Typography: The Editorial Voice

Our typography is a conversation between the old world and the new.

### Headings: Noto Serif
The Serif represents the "Daimaa"—the experienced, wise, and trustworthy caregiver.
*   **Display-lg (3.5rem):** Use for "Hero" moments only. Ensure tracking is slightly tightened (-2%) for a premium look.
*   **Headline-md (1.75rem):** The standard for section titles. It should feel graceful and approachable.

### Body: Plus Jakarta Sans
The Sans-serif represents modern trust and clarity.
*   **Body-lg (1rem):** Used for all long-form reading. We prioritize line-height (leading) at 1.6x to ensure a restful reading experience for new mothers.
*   **Label-md (0.75rem):** Used for micro-copy and metadata.

---

## 4. Elevation & Depth: Tonal Layering

We convey hierarchy through "Tonal Layering" rather than traditional drop shadows.

*   **The Layering Principle:** Depth is achieved by stacking surface tiers. Place a `surface_container_lowest` (`#ffffff`) card on a `surface_container_low` (`#f8f3ee`) background to create a soft, natural lift.
*   **Ambient Shadows:** If a shadow is required for a floating action, use an "Ambient Shadow." 
    *   **Spec:** Blur: 40px, Spread: -5px, Opacity: 6%. 
    *   **Color:** Use a tinted version of `on-surface` (warm brown/grey) rather than pure black to mimic natural light hitting organic material.
*   **The "Ghost Border" Fallback:** If a border is required for accessibility, use `outline-variant` (`#dac1ba`) at **20% opacity**. Never use 100% opaque lines.
*   **Arched Framing:** Borrowing from the logo's "D" and traditional Indian architecture, use the `full` roundedness token on one or two corners of an image or container to create a "Soft Arch" motif.

---

## 5. Components

### Buttons
*   **Primary:** Rounded `full` (9999px). Background: `primary`. Text: `on-primary`. No shadows unless hovering.
*   **Secondary:** Rounded `full`. Background: `secondary_container` (`#fad8cf`). A soft, tonal alternative.
*   **Tertiary:** Text-only using `primary` color, with a soft `surface_container` background on hover.

### Cards & Lists
*   **Forbid Dividers:** Do not use horizontal lines between list items. Use vertical white space (1.5rem to 2rem) or a `surface-container` shift to separate items.
*   **Cards:** Use `lg` (1rem) or `xl` (1.5rem) corner radii. Cards should feel like soft pillows of information.

### Input Fields
*   **Style:** Minimalist. No heavy borders. Use a `surface-container-high` background with a subtle "Ghost Border" bottom line.
*   **States:** On focus, the bottom line transitions to `primary` (Terracotta) and the label floats gracefully using `label-md`.

### Specialized Components: "The Care Progress Arch"
For a maternal care app, we use a custom progress component: a semi-circular arch (mimicking the logo’s curvature) using a `tertiary` (Gold) stroke to track pregnancy or recovery milestones.

---

## 6. Do’s and Don'ts

### Do:
*   **Embrace Asymmetry:** Place images slightly off-center or overlapping container edges to create a bespoke, high-end editorial feel.
*   **Use Warmth:** Ensure every white space is actually `surface` (`#fef8f3`). Pure #FFFFFF should be reserved only for the highest-level "lifted" cards.
*   **Respect the "Daimaa" Arch:** Use the soft curves of the logo as inspiration for container shapes.

### Don't:
*   **Don't use 90-degree corners:** They feel aggressive. The minimum radius should be `sm` (0.25rem), but `lg` is preferred.
*   **Don't use Corporate Blue or Neon:** Any blue or vibrant green will break the "Nurturing/Traditional" psychological safety of the brand.
*   **Don't crowd the content:** If a screen feels busy, add more `surface` background. New mothers need interfaces that are calm and easy to process.
*   **Don't use high-contrast dividers:** If you need a line, it has failed. Re-evaluate your tonal layering.