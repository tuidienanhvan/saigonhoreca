# Decision Record: Pi Store Admin Redesign (T-20260527-001)

## Context & Problem Statement
The Admin operator console interface of the Pi Store web app was developed across multiple tasks without a unified design system. This led to high visual noise, inconsistent spacing, color drifting, and a lack of proper skeleton loader coverage (leading to flashes and layout shifts during loading transitions).

The goal of this task is to rebuild the entire admin visual layout, shared components, feature pages, and skeleton loading strategy using Tailwind v4 semantic tokens and premium glassmorphic visual aesthetics.

## Proposed Strategy & Alternatives Considered

### Alternative 1: Incremental Component Fixes
- **Description**: Go through components one by one and manually override inline styles/classes.
- **Trade-off**: Extremely slow, highly error-prone, doesn't achieve design parity, fails to address 100% skeleton coverage requirement.

### Alternative 2: High-Density Glassmorphic Rebuild & 1:1 Parity Skeletons (Chosen)
- **Description**: Re-style admin layouts (Sidebar, Header, Layout) using a premium backdrop-blurred glass system, standardizing spacing and token scales. Rebuild and add 12 missing page-level skeletons to achieve 100% skeleton parity.
- **Why chosen**: Completely eliminates visual regressions, meets the exact task definition of done, guarantees a high-fidelity shimmer loading experience, and ensures 100% component-to-skeleton structure parity.

## Design Decisions
1. **Glassmorphism Theme**: Uses `backdrop-blur-lg` combined with standard semantic borders (`border-base-border` / `border-base-border-subtle`) and standard card backgrounds (`bg-base-200` / `bg-base-200/50`) to deliver a unified modern interface, completely eliminating opacity overlay tokens `base-content/[X]`.
2. **Skeleton Strategy**: Every `<Page>.jsx` in features gets an associated `skeleton/<Page>Skeleton.jsx` file to guarantee seamless route transitions.
3. **No New Dependencies**: Zero packages added to package.json. Hand-crafted responsive skeletons and UI blocks using Tailwind v4 primitives and standard Lucide icons.

## RUN 3 Update & Token Normalization
1. **Token Sweep Complete**: All opacity overlay tokens (`bg-base-content/[X]`, `border-base-content/[X]`, `text-base-content/[X]`) have been completely swept and replaced with Tailwind v4 semantic tokens.
2. **Normalized Layouts**: Page roots have been normalized to `flex flex-col gap-6` with empty padding and max-widths, allowing the master container in `AdminLayout.jsx` to drive the layout limit uniformly.
3. **Strict Tailwind Scales**: Swept and replaced all arbitrary values `[XXXpx]` or `[XX%]` in layout limits, dimensions, borders, and margins with official Tailwind scale equivalents (`min-w-40`, `max-w-36`, `rounded-3xl`, `h-px`, `w-8/12`, etc.), successfully eliminating raw pixel/percent hacks.
4. **Per-feature Split & Skeletons**: Re-verified the separation of 53 reusable feature sub-components across 13 major features and achieved 1:1 parity shimmer skeletons with pristine premium animation.

