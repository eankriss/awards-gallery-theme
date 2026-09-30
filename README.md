# Awards Gallery — WordPress Theme

Custom theme for the Awards Gallery site. Classic PHP templates + dynamic
Gutenberg blocks, styled with Tailwind CSS v3.

---

## Stack

- Classic PHP templates (`header.php`, `footer.php`, `index.php`, `page.php`,
  `single.php`, `404.php`)
- No site search: front-end `?s=` requests return the 404 page
- Dynamic, server-rendered Gutenberg blocks (React `edit` + PHP `render.php`),
  auto-registered from `assets/js/blocks/*`
- Tailwind CSS v3

---

## Install / build

```bash
npm install
npm run build      # builds every block + compiles main.min.css & critical.min.css
npm run dev        # watch mode (blocks + css)
```

Then activate **Awards Gallery Theme** in WordPress.

> Built assets (`assets/**/build/`, `*.min.css`) are gitignored — run
> `npm run build` after cloning.

## Creating a block

```bash
npm run create-block my-block
```

Blocks use the `awards-gallery/` namespace.

---

## Branches & deployment

- `main` — production-ready code
- `staging` — every push runs `.github/workflows/deploy-staging.yml`, which
  builds the theme and rsyncs it to the staging server.

Work on `staging`, then open a pull request `staging → main`.

Required repository secrets (**Settings → Secrets and variables → Actions**):

| Secret            | Value                                   |
|-------------------|-----------------------------------------|
| `SSH_HOST`        | Staging server host / IP                |
| `SSH_USER`        | CloudPanel site user                    |
| `SSH_DOMAIN`      | Staging domain (folder under `htdocs/`) |
| `SSH_PRIVATE_KEY` | Private key with SSH access             |

## License

GPL-2.0-or-later
