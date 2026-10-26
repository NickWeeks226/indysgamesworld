# Hattie's Worlds

Nick works on this website with his daughter Hattie, who comes up with the ideas.
Hattie's Worlds is her business idea: designing imaginary worlds and bringing them to life.

## How the project works

- `site/index.html` is the landing page: it asks which world to join (George's or Hattie's) and
  shows how many times each has been picked. The shared totals come from `site/counter.php`, which
  keeps them in `counts.json` on the server (that file is not in the repo).
- Hattie's world is one file: `site/hattie.html` (HTML, CSS and JavaScript together).
- George's world is `site/george.html`, a "coming soon" page for now.
- When a change is committed and pushed to `main`, GitHub Actions (`.github/workflows/deploy.yml`)
  uploads `site/` to the IONOS webspace folder `/hattiesworlds`. Do not edit the workflow.
- The site runs on Hattie's iPad as a home-screen web app, so it must work well on touch screens
  and on an iPad in both orientations.

## Rules for every change

1. Only edit the page the change is for unless Nick asks otherwise.
2. Keep each page a single self-contained file. No new external scripts, and no images loaded from other
   websites. Draw pictures with inline SVG or embed them as data URIs.
3. Keep the look consistent: the Gaegu font, wobbly hand-drawn boxes with dark ink outlines, and
   Hattie's favourite colours (pink, purple and blue) on the light blue background.
4. Never break her saved data. These localStorage keys must keep the same names and formats:
   `hattie-worlds-saved`, `hattie-mixer`, `hattie-ninja`, `hattie-stacker`, `hattie-jar`,
   `hattie-drawings`, `hattie-orders`. If a format has to change, migrate old data on load.
5. Everything must be friendly and suitable for a young child: no scary, violent or grown-up
   content, no links to other websites, and nothing that collects personal information.
6. Wrap new storage reads and writes in try/catch, and respect `prefers-reduced-motion`.
7. Before finishing, check the page still loads without JavaScript errors.

## Talking about changes

- Explain what you changed in short, simple, cheerful sentences, so Nick can read them out to Hattie.
- If an idea is unclear, ask one simple question before building it.

## Publishing

Do not commit or push. When a change is ready, say so. Nick reviews it and publishes it himself
with GitHub Desktop (Commit, then Push), which sends it live about a minute later.
