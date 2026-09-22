# Umblätterer

A WordPress theme for [Der Umblätterer](https://umblaetterer.de) — *in der Halbwelt des Feuilletons* — set in two inks on ivory stock, after the German press of the twentieth century.

## The idea

The masthead is a blackletter wordmark in a deep navy. That one asset decides everything else. A Fraktur logo belongs to a particular kind of object: a German broadsheet, folded, printed in one colour plus an occasional second, and made almost entirely of type. So the theme is built the way such a page was built.

That turns out to suit the archive rather than fight it. There are 1,600 posts here going back to May 2007, essentially none of them with a featured image, none with a hand-written excerpt, and 105 categories that behave not like tags but like standing columns — *Buchbuch*, *Kaffeehaus des Monats*, *Vossianische Antonomasie*, *100 Seiten*. A card grid would have nothing to put in the cards. A newspaper page has exactly the right furniture for this: rubrics, datelines, column rules, and a register.

## The design

**Two inks on ivory.** The paper is `#f4efe1` with a fine fractal-noise grain multiplied over it and a warm vignette at the edges. The first ink is the wordmark's `#112e60`. The second is a muted stamp-red, used perhaps four times in the whole theme — on the *Neu* flag, on link hover, and nowhere that would dilute it. Body copy is not black but a warm `#1d1a14`, the colour of letterpress that has sunk slightly into rag paper.

**Three faces, each with one job.**

| | | |
|---|---|---|
| **Old Standard TT** | headlines | a Didone modelled on the typesetting of the late-19th / early-20th-century press |
| **Spectral** | running text | sturdy on screen, good German diacritics, survives justification |
| **Jost\*** | rubrics, datelines, navigation | a Futura revival; Renner drew Futura in Frankfurt in 1927 |

All three are self-hosted from `assets/fonts` under the SIL Open Font License. Nothing is fetched from a third party — a German publication should not hand its readers to a CDN for the privilege of being read.

**Print habits, kept.** Justified German with `hyphens: auto` (ragged right below 600px, where justification only tears rivers). Old-style figures in running text, lining figures in datelines. Drop caps on the opening paragraph. Hairline rules *between* columns rather than gaps. Dot leaders in every index. A fleuron closing each article. The thick-over-thin rule pair that opens and closes a page.

**Two editions.** *Tagausgabe* and *Nachtausgabe*. The morning edition is always what a reader gets first: the ivory is the point of the theme, and a dark operating system is not a request for a dark newspaper, so `prefers-color-scheme` is deliberately not consulted. The night edition is reached only through the masthead button; the choice is then remembered and re-applied in `<head>` before first paint, so a returning reader never sees the wrong edition flash. The wordmark is inlined as SVG so it inherits `currentColor` and turns with the palette instead of being stranded as a fixed navy image.

## Templates

| File | What it sets |
|---|---|
| `home.php` | The front page: one *Aufmacher* with a drop-capped standfirst, a companion column beside it, three ruled columns, and a register of everything else |
| `single.php` | One column at a 36rem measure; *Umblättern* navigation; *Zuschriften* set as a correspondence column |
| `archive.php`, `search.php` | Registers with dot leaders — with 514 posts in *Buchbuch*, a feed would be unusable and a table of contents is a reference work |
| `404.php` | *Diese Seite fehlt im Blatt* |
| `template-parts/rail-rubriken.php` | The rubric and volume registers that flank the listings |

The companion column beside the lead prefers the lead's own standing column. But the rubric is chosen for being *rare* — the more specific term is the more informative kicker — and a rare rubric sometimes holds exactly one piece. So it falls back to the oldest trick in the trade: **Vor zehn Jahren**, what stood in this paper ten years ago.

## Development

Requires [Node.js](https://nodejs.org/) and [Composer](https://getcomposer.org/).

```sh
composer install
npm install
```

CSS is authored in `sass/` and compiled with Dart Sass (already present as a dependency of `@wordpress/scripts`; `node-sass` cannot build against a current Node).

```sh
npm run compile:css      # sass/style.scss      → style.css
npm run compile:editor   # sass/editor.scss     → assets/css/editor.css
npm run watch            # recompile style.css on change
npm run compile:rtl      # style.css            → style-rtl.css
```

The editor stylesheet shares the palette, the `@font-face` rules and the type scale with the front end, so a piece is written on the same paper it is published on.

Linting and packaging:

```sh
composer lint:wpcs       # PHP against the WordPress coding standards
composer lint:php        # PHP syntax
composer make-pot        # languages/umblaetterer.pot
npm run lint:scss
npm run lint:js
npm run bundle           # ../umblaetterer.zip
```

### Notes for whoever comes next

- **The wordmark** is read from `assets/Umblaetterer.svg` by `umblaetterer_wordmark()`, which strips the embedded `fill` so the paths take `currentColor`. A logo set in the Customizer overrides it — and loses that behaviour.
- **The issue number** in the dateline bar is the count of published posts, cached for a day and flushed on publish.
- **Dates** are written out by `umblaetterer_datum()` rather than `date_i18n()`, which would depend on the install's locale being German.
- **Reading time** counts letter-runs with a Unicode regex; `str_word_count()` splits German words at every umlaut and inflates the figure by a third.
- **Teasers** drop a leading block quotation before cutting. The house style opens pieces with a pulled quotation, and a teaser made of somebody else's words tells the reader nothing about the piece.

## Credits

Umblätterer is based on [Underscores](https://underscores.me/), © 2012–2020 Automattic, Inc., distributed under the GNU GPL v2 or later. Normalizing styles owe their existence to [normalize.css](https://necolas.github.io/normalize.css/) by Nicolas Gallagher and Jonathan Neal.

Licensed under the GPL v2 or later.
