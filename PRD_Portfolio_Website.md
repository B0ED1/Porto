# Product Requirement Document (PRD)
## Interactive & Modern Personal Portfolio Website

---

## 1. Document Overview & Executive Summary

* **Project Name:** Personal Interactive Portfolio & Showcase Website
* **Document Version:** v1.0
* **Author:** M Arif Budi Prakoso
* **Target Deployment Platform:** Vercel (via GitHub Desktop)
* **Primary Objective:** Create a visually captivating, highly interactive, and lightning-fast single-page web portfolio to showcase software engineering projects, database architectures, and photography. This website will serve as the primary landing link featured on the GitHub Profile `README.md`.

---

## 2. Target Audience & Core User Persona

1. **Tech Recruiters & Talent Acquisition:** Seeking quick access to key technical skills, project live demos, source code repositories, and contact info.
2. **Engineering Managers / Tech Leads:** Interested in code quality, tech stack depth, system architecture capabilities (e.g., PL/SQL, web development), and real-world project implementations.
3. **Collaborators & Clients:** Looking for a professional creative profile and photography portfolio.

---

## 3. Selected Tech Stack & Architecture

To achieve optimal performance, fluid interactivity, and seamless Vercel deployment, the recommended stack is **Client-Side Jamstack (HTML + Tailwind CSS + Modern JavaScript)**:

| Category | Technology | Rationale |
| :--- | :--- | :--- |
| **Markup & Structure** | HTML5 (Semantic HTML) | High SEO compatibility, accessibility (a11y), and fast initial page load. |
| **Styling & Design System** | Tailwind CSS (v3 / CDN or CLI) | Rapid utility-first styling, responsive grid system, dark mode native support. |
| **Interactivity & Logic** | JavaScript (ES6+) / Alpine.js | Lightweight, framework-free client-side reactivity for modals, filters, and dynamic DOM manipulation without build overhead. |
| **Icons & Visual Assets** | Lucide Icons & SVG Graphics | Crisp, scalable vector icons with zero heavy font dependency. |
| **Typography** | Google Fonts (Inter & JetBrains Mono) | Clean modern aesthetics for body text paired with developer-focused monospace typography. |
| **Hosting & Deployment** | Vercel via GitHub Desktop | Automated continuous integration (CI/CD). Pushing code via GitHub Desktop triggers instant deployment. |

---

## 4. Key Functional Features & Interactivity Requirements

### 4.1 Navigation & Header
* **Sticky Glassmorphism Bar:** Semi-transparent navbar with `backdrop-blur` effect that stays pinned during scrolling.
* **Smooth Scrolling Navigation:** Clicking section links (`#about`, `#skills`, `#projects`, `#gallery`, `#contact`) smoothly animates to the respective section.
* **Theme Mode Switcher:** Toggle between Dark Mode (default) and Light Mode with setting persisted in `localStorage`.
* **Interactive Mobile Menu:** Responsive hamburger toggle menu for small screens.

### 4.2 Dynamic Hero Section
* **Interactive Intro Badge:** Animated status indicator (e.g., `🟢 Available for opportunities / projects`).
* **Headline & Subtitle:** Bold typography emphasizing software engineering, database management, and photography.
* **Interactive Call to Action (CTA):** 
  * "Explore Projects" button with smooth scroll.
  * "Get in Touch" button opening contact modal or smooth scroll to contact section.
* **Social Quick Links:** Hover-animated buttons linking directly to GitHub, LinkedIn, and Email.

### 4.3 Interactive Skills & Tech Stack Grid
* **Categorized Badge System:**
  * **Frontend & Web:** HTML5, CSS3, Tailwind CSS, JavaScript (ES6+).
  * **Backend & Database:** Node.js, PL/SQL, Oracle, MySQL, Database Architecture.
  * **Tools & Workflow:** Git, GitHub Desktop, VS Code, Vercel.
  * **Creative:** Photography, Composition, Color Grading.
* **Interactive Tooltips / Hover Effects:** Hovering over skill cards highlights related projects or displays proficiency details.

### 4.4 Interactive Project Showcase Grid
* **Category Filtering Tabs:** Filter buttons (e.g., `All`, `Web Systems`, `Database`, `UI/UX`) that dynamically show/hide project cards with smooth fade transitions.
* **Interactive Project Cards:**
  * **Visual Preview Card:** Project cover thumbnail with zoom-on-hover effect.
  * **Project Meta:** Title, clear problem-solution description, and tech stack badges.
  * **Action Buttons:** Direct links to **Live Demo** and **GitHub Repository**.
* **Featured Projects Included:**
  1. *Concert Ticket Reservation System* (Database Architecture & Logic).
  2. *Supporter Digital Management System* (Centralized Supporter Data Platform).
  3. *Restaurant Digital Menu System (WARMAKKICAUAN)* (Interactive Web Menu Interface).

### 4.5 Photography & Visual Gallery
* **Responsive Masonry Grid:** Visual display of photography work (Landscapes, Cityscapes, Street Photography).
* **Interactive Lightbox Modal:** Clicking any photograph opens a full-screen high-resolution modal viewer with close/navigation controls and photo details (caption, location).
* **Hover Overlay:** Displays photo title and location tag on hover.

### 4.6 Contact Section & Interactive Widgets
* **Interactive Form / Direct Email Trigger:** User-friendly form with validation.
* **One-Click Email Copy Button:** Clickable button to copy email address directly to clipboard with a visual toast notification ("Copied!").
* **Footer:** Sleek footer featuring copyright, live status, and social media icon links.

---

## 5. Non-Functional Requirements & Performance Goals

* **Performance & Speed:** Google Lighthouse score > 90 across Performance, Accessibility, Best Practices, and SEO.
* **Fully Responsive:** Tested across mobile (iOS/Android), tablet, and widescreen desktop displays.
* **Zero External Server Overhead:** 100% static client-side execution for instant page loading without backend cold starts.
* **SEO & Social Sharing:** Includes OpenGraph (OG) meta tags and Twitter Card metadata for attractive preview link previews when shared on GitHub README or social media.

---

## 6. Deployment & CI/CD Workflow via GitHub Desktop

1. **Local Setup:** Development using HTML, CSS (Tailwind), and JS in VS Code.
2. **Version Control:**
   * Open **GitHub Desktop**.
   * Review code changes and write a commit message (e.g., `feat: add interactive project filter`).
   * Click **Push origin** to sync with the GitHub repository.
3. **Automated Vercel Deployment:**
   * Vercel repository webhook automatically detects the git push.
   * Build & edge distribution occurs automatically in < 15 seconds.
   * Updated site live on custom Vercel domain (`.vercel.app`).

---

## 7. Implementation Roadmap & Milestones

* [x] **Phase 1:** PRD Definition & Architecture Design.
* [ ] **Phase 2:** HTML Structure & Tailwind CSS Layout Implementation.
* [ ] **Phase 3:** Adding JavaScript Interactivity (Filters, Lightbox, Copy-to-clipboard, Theme Switch).
* [ ] **Phase 4:** Content Population (Project descriptions, Photography assets, Social links).
* [ ] **Phase 5:** Repository Sync via GitHub Desktop & Vercel Deployment.
* [ ] **Phase 6:** Embedding Portfolio Badge into GitHub Profile `README.md`.
