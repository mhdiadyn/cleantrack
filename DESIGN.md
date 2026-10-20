---
name: Technolife Corporate
colors:
surface: '#fbf9f8'
surface-dim: '#dbd9d9'
surface-bright: '#fbf9f8'
surface-container-lowest: '#ffffff'
surface-container-low: '#f5f3f3'
surface-container: '#efeded'
surface-container-high: '#eae8e7'
surface-container-highest: '#e4e2e2'
on-surface: '#1b1c1c'
on-surface-variant: '#5c3f3f'
inverse-surface: '#303030'
inverse-on-surface: '#f2f0f0'
outline: '#916f6e'
outline-variant: '#e6bdbc'
surface-tint: '#bf0030'
primary: '#b1002c'
on-primary: '#ffffff'
primary-container: '#dc143c'
on-primary-container: '#fff1f0'
inverse-primary: '#ffb3b3'
secondary: '#5f5e5e'
on-secondary: '#ffffff'
secondary-container: '#e5e2e1'
on-secondary-container: '#656464'
tertiary: '#006622'
on-tertiary: '#ffffff'
tertiary-container: '#00822d'
on-tertiary-container: '#daffd5'
error: '#ba1a1a'
on-error: '#ffffff'
error-container: '#ffdad6'
on-error-container: '#93000a'
primary-fixed: '#ffdad9'
primary-fixed-dim: '#ffb3b3'
on-primary-fixed: '#40000a'
on-primary-fixed-variant: '#920022'
secondary-fixed: '#e5e2e1'
secondary-fixed-dim: '#c8c6c5'
on-secondary-fixed: '#1c1b1b'
on-secondary-fixed-variant: '#474646'
tertiary-fixed: '#83fc8e'
tertiary-fixed-dim: '#66df75'
on-tertiary-fixed: '#002106'
on-tertiary-fixed-variant: '#00531a'
background: '#fbf9f8'
on-background: '#1b1c1c'
surface-variant: '#e4e2e2'
typography:
h1:
fontFamily: Poppins
fontSize: 48px
fontWeight: '600'
lineHeight: '1.2'
h1-mobile:
fontFamily: Poppins
fontSize: 32px
fontWeight: '600'
lineHeight: '1.2'
h2:
fontFamily: Poppins
fontSize: 36px
fontWeight: '600'
lineHeight: '1.3'
h2-mobile:
fontFamily: Poppins
fontSize: 28px
fontWeight: '600'
lineHeight: '1.3'
h3:
fontFamily: Poppins
fontSize: 28px
fontWeight: '600'
lineHeight: '1.4'
h4:
fontFamily: Poppins
fontSize: 22px
fontWeight: '600'
lineHeight: '1.4'
body:
fontFamily: Inter
fontSize: 16px
fontWeight: '400'
lineHeight: 160%
caption:
fontFamily: Inter
fontSize: 14px
fontWeight: '400'
lineHeight: 160%
label:
fontFamily: Inter
fontSize: 14px
fontWeight: '600'
lineHeight: 100%
letterSpacing: 0.02em
rounded:
sm: 0.25rem
DEFAULT: 0.5rem
md: 0.75rem
lg: 1rem
xl: 1.5rem
full: 9999px
spacing:
base: 8px
xs: 4px
sm: 8px
md: 16px
lg: 24px
xl: 32px
xxl: 48px
max-width: 1280px
gutter: 24px
margin-mobile: 16px
Brand & Style
The design system is engineered for a premium corporate environment, prioritizing clarity, authority, and modern professionalism. It utilizes a Corporate / Modern aesthetic that balances high-impact brand color with a disciplined, systematic layout.
The visual narrative is driven by generous whitespace, a strict adherence to a grid, and a "flat-plus" depth model—where elements are primarily flat but utilize subtle elevation to denote interactivity and hierarchy. The emotional goal is to project stability and innovation through precision and high-quality typographic detailing.
Colors
The palette is anchored by Crimson (#DC143C), used strategically for primary actions and brand emphasis to ensure high energy within a professional framework.
Primary: Crimson is reserved for key CTAs and active states.
Surface & Background: A pure white background provides a clean canvas, while the off-white surface color creates subtle containment for secondary content areas.
Typography: Deep charcoal (#121212) provides maximum legibility for headings, while a softer grey (#4A4A4A) is used for body text to reduce visual fatigue.
Functional: Success states utilize a standard corporate green (#28A745) to ensure immediate semantic recognition.
Typography
This design system employs a dual-typeface strategy to distinguish between brand voice and functional content.
Poppins is used for all headlines (H1–H4) at a SemiBold weight to establish a confident, modern presence. Inter is the workhorse for body copy and labels, chosen for its exceptional readability and neutral tone. All body text maintains a generous 160% line height to ensure optimal scanning and a premium, airy feel. On mobile devices, H1 and H2 scales are reduced to maintain visual balance and prevent awkward line breaks.
Layout & Spacing
The layout is built on a rigorous 8-point grid system, ensuring all dimensions, padding, and margins are multiples of 8.
Grid: A 12-column fluid grid is used for desktop, constrained to a maximum content width of 1280px.
Gutters: Standard horizontal spacing between columns is 24px.
Responsive Behavior:
Desktop (>1024px): 12 columns, 24px gutters, centered layout.
Tablet (768px - 1023px): 8 columns, 24px gutters, 24px side margins.
Mobile (<767px): 4 columns, 16px gutters, 16px side margins.
Elevation & Depth
Hierarchy is established through Tonal Layering and Soft Shadows.
Flat Layer: The default state for background and non-interactive surface containers.
Subtle Elevation (Level 1): Used for cards and input fields. Defined by a 1px border (#EAEAEA) and a very soft, diffused shadow: `0px 4px 12px rgba(0, 0, 0, 0.05)`.
Active Elevation (Level 2): Used for hovered states or modals. Defined by a slightly deeper shadow: `0px 8px 24px rgba(0, 0, 0, 0.08)`.
Gradients and glassmorphism are strictly prohibited to maintain a grounded, professional appearance.
Shapes
The design system utilizes a Rounded shape language to soften the corporate aesthetic and make the interface feel more approachable.
The primary radius is set to 12px (0.75rem) for all major components including cards, buttons, and input fields. Smaller elements like checkboxes or tags may use a reduced radius of 4px for visual consistency. Icons should follow a consistent line-weight of 1.5px or 2px with rounded terminals to match the UI's geometry.
Components
Buttons
Primary: Crimson background, white text, 12px radius. Height: 48px for large, 40px for medium.
Secondary: Transparent background, Crimson 1.5px border, Crimson text.
Hover States: Primary buttons darken by 10%; secondary buttons gain a very light Crimson tint background (5% opacity).
Cards
White background, 1px #EAEAEA border, 12px border-radius.
Use Level 1 shadow for default state; transition to Level 2 shadow on hover.
Input Fields
Surface color (#F5F5FA) or White background with 1px border (#E5E5E5).
12px border-radius.
Focus state: Border color changes to Crimson (#DC143C) with a subtle 2px outer glow.
Lists & Navigation
Items use Inter Regular 16px.
Active navigation links use a SemiBold weight and a Crimson bottom indicator (2px).
Imagery & Icons
Photography: High-quality, bright corporate environments with natural lighting.
Icons: Minimalist line icons with a 2px stroke width, consistently using the Neutral (#4A4A4A) or Primary color.