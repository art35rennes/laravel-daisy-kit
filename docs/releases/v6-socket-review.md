# Socket dependency review for v6

Read-only package/source review, 13 September 2026. No dependency changed, installed or upgraded; no GitHub comment posted and no Socket alert suppressed. Downloaded registry/source evidence is under `/tmp/v6-socket-source/`.

## Alerts actually observed

GitHub PR 1 has a Socket warning comment for diff scan `22749892-bd46-4214-92f0-432fe6876111`:

- `jsdom@30.0.1`: **High**, action **Warn**, `Obfuscated code`, confidence **0.90**. [Alert](https://socket.dev/dashboard/org/perso-wtufz/diff-scan/22749892-bd46-4214-92f0-432fe6876111/alert/Qi17fzo_d1NlNQWtGnlmR0ojJmUHfwwYaBfuxfyvOE7s).
- `robust-predicates@3.0.3`: **High**, action **Warn**, `Obfuscated code`, confidence **0.90**. [Alert](https://socket.dev/dashboard/org/perso-wtufz/diff-scan/22749892-bd46-4214-92f0-432fe6876111/alert/QdTTYMKCXBgG0x9aVxpiE6jwSSJw3uXocvpwTFgy9XlY).

The bot comment identifies package versions and lockfile/dependency paths, but no triggering filename or code excerpt. The jsdom package page and authenticated-dashboard alert URL returned HTTP 403; the robust package page was also unavailable through the browser fetch. Therefore the exact Socket detection location remains unverified. The 90% score is the detector's confidence in obfuscation, not a demonstrated 90% probability of malware.

## Verified provenance and integrity

The exact archives were retrieved from npm's public registry, SHA-512 computed locally, compared to both `package-lock.json` and registry metadata, then compared file by file to the installed package directories.

| Package | Registry archive and lock integrity | Installed bytes | Upstream source reference |
| --- | --- | --- | --- |
| jsdom 30.0.1 | `https://registry.npmjs.org/jsdom/-/jsdom-30.0.1.tgz`; `sha512-52v7mUVUfNQVYYqE1lcdaymWL0njO7lTLUog6ZvW2U5KsbiLk/GnZlVJ+qx0xfNJZ6Gn+KSpPNE52vurbxZwrA==` | All **657 files identical**, zero mismatches, to `node_modules/jsdom` | [jsdom/jsdom commit 6584485f094d5b271553005b68804c93a455c002](https://github.com/jsdom/jsdom/tree/6584485f094d5b271553005b68804c93a455c002) |
| robust-predicates 3.0.3 | `https://registry.npmjs.org/robust-predicates/-/robust-predicates-3.0.3.tgz`; `sha512-NS3levdsRIUOmiJ8FZWCP7LG3QpJyrs/TE0Zpf1yvZu8cAJJ6QMW92H1c7kWpdIHo8RvmLxN/o2JXTKHp74lUA==` | All **20 files identical**, zero mismatches, to `node_modules/point-in-polygon-hao/node_modules/robust-predicates` | [mourner/robust-predicates commit 8bed7fadb4284911e1111876e54a6f8acfa445cd](https://github.com/mourner/robust-predicates/tree/8bed7fadb4284911e1111876e54a6f8acfa445cd) |

Registry ECDSA signatures were cryptographically verified with the public key returned by `https://registry.npmjs.org/-/npm/v1/keys`, key id `SHA256:DhQ8wR5APBvFHLF/+Tc+AYvPOdTpcIDqOhxsBHRwC7U`. Both jsdom signatures and the robust-predicates signature validate over `name@version:integrity`.

The jsdom registry exposes a SLSA provenance bundle. Its decoded subject digest matches the downloaded archive and identifies `refs/tags/v30.0.1`, the above commit, `.github/workflows/publish.yml`, and [GitHub Actions run 30421908712](https://github.com/jsdom/jsdom/actions/runs/30421908712/attempts/1). This review decoded the attestation and checked its subject/reference, but did **not** independently verify the full Sigstore certificate/transparency-log chain. Registry signatures were verified separately as described above. Registry metadata for robust-predicates did not advertise a provenance bundle.

Integrity/provenance show that these are the expected published bytes, not that the upstream packages are infallible or non-malicious.

## Source-level assessment

### jsdom 30.0.1

The package's 373 files also present in the exact GitHub source archive match byte for byte; there are **no modified shared files**. The other **284 files are generated output**, principally `lib/generated/idl/*.js`, generated CSS property definitions/descriptors and support data. The repository contains the generation pipeline, including `scripts/webidl/convert.js` using WebIDL2JS and CSS/global generation scripts. These build-time generators were inspected but not executed.

The distributed JavaScript examined is readable, with descriptive identifiers and ordinary DOM/WebIDL implementation. Longest-line inspection found a maximum of 743 characters in generated CSS data; it did not reveal a packed single-line runtime loader. Generated bindings contain repetitive checks and plumbing, consistent with their generator. Dynamic evaluation/base64-related occurrences were inspected in context: `Window.js` implements `atob`, string helpers transform character case, script/navigation implementations execute supplied DOM scripts, and generated IDL utilities obtain JavaScript intrinsics. These are capabilities expected of a DOM emulator, not by themselves proof of hidden execution.

Potential classification candidates are the generated bindings/data. **These are hypotheses, not confirmed Socket trigger files.** No concealed downloader, encoded payload decoder or unexpected install hook was identified in the inspected code. jsdom does have script/resource execution capabilities by design and must not be treated as a security sandbox.

Its npm manifest has generation/test scripts, including `prepare: wireit`, but no `preinstall`, `install` or `postinstall` hook. Registry-installed prepared output is what was compared; no source generator or lifecycle script was executed for this review.

### robust-predicates 3.0.3

The five published files also present in GitHub source match exactly; the other **15 files are generated ESM/UMD variants**, including explicitly named `umd/*.min.js`. For example `umd/predicates.min.js` is a roughly 25 KB single line. Such minified distributions are an evident possible source of the warning, but Socket's specific filename remains unavailable.

The readable ESM files implement numerical error bounds and floating-point expansion arithmetic (`orient2d`, `orient3d`, `incircle`, `insphere`). Upstream `compile.js` expands arithmetic macros such as `Two_Sum`, `Two_Product`, `Split` and `Cross_Product`; this explains short mathematical variables and long repetitive arithmetic sequences. The generation script was read, not run. The generated output was not independently regenerated byte for byte.

Search across the exact package JavaScript found no `eval`, `new Function`, `atob`, base64 decoder, `child_process` or HTTP URL constructs. The examined ESM orientation routine consists of arithmetic, arrays and imported numerical helpers; no network/filesystem side effect was found. The manifest has no production dependencies and no install lifecycle hooks; build/prepublish scripts are upstream development tasks.

Conclusion for this package: the visible compression/generation is consistent with a computational-geometry library. That is materially different from a concealed payload, but cannot establish the exact Socket warning as a false positive without its trigger details.

## Actual exposure in Laravel Daisy Kit

### jsdom: development/test environment

`package.json` declares jsdom as a **devDependency**, `package-lock.json` marks it `dev: true`, and `vitest.config.js` selects the jsdom test environment. `npm ls` also shows Vitest 4.1.11 using the same jsdom instance. It is executed during JS tests, so it matters to developer/CI trust; being development-only does not eliminate supply-chain risk.

It is not required by Composer consumers. A no-write Vite build (`build.write=false`, `emptyOutDir=false`) with a module-inventory hook showed **no jsdom module in any emitted runtime chunk**. No tracked output was written.

### robust-predicates: real browser runtime

The actual 3.0.3 path is:

`@turf/boolean-intersects@7.4.0` → `@turf/boolean-disjoint@7.4.0` → `@turf/boolean-point-in-polygon@7.4.0` → `point-in-polygon-hao@1.2.4` → `robust-predicates@3.0.3`.

`resources/js/map/sources.js:406` dynamically imports Turf boolean-intersects for spatial selection. `point-in-polygon-hao/dist/esm/index.js` imports `orient2d` from robust-predicates. The no-write Vite module inventory confirms 3.0.3 ESM util/orientation/incircle/insphere code contributes to `chunks/esm-TI1_uB6p.js`. **The UMD minified distribution is not the selected module entry.** This package does reach consumers through the tracked Map runtime; it cannot be dismissed as development-only.

There is also a distinct `robust-predicates@2.0.4` at the top-level node_modules path through Turf line-intersect. Its presence was recorded to avoid inspecting the wrong package. The Socket warning under review is for nested **3.0.3**; the detailed archive/source comparison above intentionally targets that version.

## Release disposition and limitations

- No evidence of archive substitution, installed-byte tampering or concealed malicious payload was found in this targeted assessment.
- Observed code generation/minification provides a plausible benign explanation, strongest for robust-predicates' explicit minified UMD files. **Neither alert has been conclusively mapped to its Socket trigger**, so neither is marked a confirmed false positive.
- Keep both alerts visible and attach this assessment to the release evidence. Do not issue Socket ignore comments or globally suppress the rule on the basis of package reputation, npm audit results or a passing test suite.
- If the release policy requires resolving every High/Warn classification, obtain the exact triggering files through the authenticated Socket dashboard before declaring that requirement satisfied. No technical need for an emergency dependency replacement was established by the source evidence currently available.
- This is targeted source/provenance review, not exhaustive formal verification of jsdom's approximately 7 MB distribution or every transitive dependency. It does not claim full Sigstore verification or upstream build reproducibility. The blocked Socket detail view is an explicit remaining limitation.
