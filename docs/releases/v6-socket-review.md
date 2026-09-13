# Socket dependency review for v6

Read-only package/source review, 13 September 2026. No dependency changed, installed or upgraded; no GitHub comment posted and no Socket alert suppressed. Downloaded registry/source evidence is under `/tmp/v6-socket-source/`.

## Alerts actually observed

GitHub PR 1 has a Socket warning comment for diff scan `22749892-bd46-4214-92f0-432fe6876111`:

- `jsdom@30.0.1`: **High**, action **Warn**, `Obfuscated code`, confidence **0.90**. [Alert](https://socket.dev/dashboard/org/perso-wtufz/diff-scan/22749892-bd46-4214-92f0-432fe6876111/alert/Qi17fzo_d1NlNQWtGnlmR0ojJmUHfwwYaBfuxfyvOE7s).
- `robust-predicates@3.0.3`: **High**, action **Warn**, `Obfuscated code`, confidence **0.90**. [Alert](https://socket.dev/dashboard/org/perso-wtufz/diff-scan/22749892-bd46-4214-92f0-432fe6876111/alert/QdTTYMKCXBgG0x9aVxpiE6jwSSJw3uXocvpwTFgy9XlY).

The bot comment identifies package versions and lockfile/dependency paths, but no triggering filename. The initial HTTP fetches returned 403. A subsequent public-page review through the browser, reported by the coordinating agent, resolved both exact locations without the private dashboard:

- [jsdom public alert](https://socket.dev/npm/package/jsdom/alerts/30.0.1?alert_name=obfuscatedFile): `lib/jsdom/living/nodes/HTMLTextAreaElement-impl.js`. Socket's analysis notes describe a normal textarea implementation and legitimate hard wrapping, without malicious activity or I/O.
- [robust-predicates public alert](https://socket.dev/npm/package/robust-predicates/alerts/3.0.3?alert_name=obfuscatedFile): `esm/orient2d.js`. Socket's analysis notes describe legitimate adaptive 2D orientation and recommend checking `util.js`.

The reviewer then read the complete exact-version textarea implementation, `esm/orient2d.js` and `esm/util.js` locally. Their archive integrity was established below. The 90% score is detector confidence in obfuscation, not a demonstrated probability of malware. The initial uncertainty about triggering files is now resolved.

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

**Exact trigger review:** `HTMLTextAreaElement-impl.js` is handwritten, readable CommonJS, not a generated binding or minified payload. It imports normal jsdom element, constraint-validation, event, text and form helpers. Its methods implement value/defaultValue, CRLF normalization, selection ranges, replacement text, readonly/disabled constraints, validation and cloning. The concluding `textareaWrappingTransformation` scans newline-delimited text and inserts line breaks for `wrap="hard"`; its branches, counters and string slices perform precisely that text transformation. The only timer dispatches the normal DOM `select` event. There is no network/filesystem access, process execution, dynamic evaluation, hidden string-decoder or concealed execution in this file.

This exact file belongs to the 373 files byte-identical to the upstream commit. **Classification: benign textarea implementation; the obfuscation alert is a false positive for malicious concealment.** The earlier hypothesis about generated bindings is superseded by the identified file. This conclusion is scoped to the reported alert, not a claim that jsdom is a security sandbox or free of every possible defect.

Its npm manifest has generation/test scripts, including `prepare: wireit`, but no `preinstall`, `install` or `postinstall` hook. Registry-installed prepared output is what was compared; no source generator or lifecycle script was executed for this review.

### robust-predicates 3.0.3

The five published files also present in GitHub source match exactly; the other **15 files are generated ESM/UMD variants**, including explicitly named `umd/*.min.js`. For example `umd/predicates.min.js` is a roughly 25 KB single line. These UMD files are not the actual alert location: the public alert identifies readable `esm/orient2d.js`. Their presence must not be used as the explanation for this alert.

The readable ESM files implement numerical error bounds and floating-point expansion arithmetic (`orient2d`, `orient3d`, `incircle`, `insphere`). Upstream `compile.js` expands arithmetic macros such as `Two_Sum`, `Two_Product`, `Split` and `Cross_Product`; this explains short mathematical variables and long repetitive arithmetic sequences. The generation script was read, not run. The generated output was not independently regenerated byte for byte.

Search across the exact package JavaScript found no `eval`, `new Function`, `atob`, base64 decoder, `child_process` or HTTP URL constructs. The examined ESM orientation routine consists of arithmetic, arrays and imported numerical helpers; no network/filesystem side effect was found. The manifest has no production dependencies and no install lifecycle hooks; build/prepublish scripts are upstream development tasks.

**Exact trigger and helper review:** `esm/orient2d.js` imports only `epsilon`, `splitter`, `resulterrbound`, `estimate`, `vec` and `sum` from `./util.js`. Its fast path computes the signed two-dimensional determinant and compares it with an error bound. The adaptive path splits products/differences into high/low floating-point terms, sums expansions and returns the last expansion term when the sign cannot be resolved safely by the fast path. The small variable names (`ahi`, `alo`, `bvirt`, `s0`, `s1`, etc.) denote numerical intermediates, not decoded instructions.

The complete `util.js` defines numerical constants, zero-eliminating expansion sum/scale operations, sign negation, an estimate sum and `vec(n) = new Float64Array(n)`. It imports nothing. No external resource, decoder, dynamic code generation, process access or network/filesystem operation appears in either file. Module-scope typed arrays are scratch space for those calculations. `orient2d.js` is generated from arithmetic macros in the inspected upstream compiler; the helper participates in the same transparent numerical algorithm.

**Classification: benign adaptive computational-geometry implementation.** The exact alert is a false positive for malicious concealment, supported by the complete trigger/helper review, expected dependency use, integrity/provenance checks and Socket's own detailed notes. This is not a numerical proof of correctness for every floating-point input, nor a blanket approval of unrelated versions.

## Actual exposure in Laravel Daisy Kit

### jsdom: development/test environment

`package.json` declares jsdom as a **devDependency**, `package-lock.json` marks it `dev: true`, and `vitest.config.js` selects the jsdom test environment. `npm ls` also shows Vitest 4.1.11 using the same jsdom instance. It is executed during JS tests, so it matters to developer/CI trust; being development-only does not eliminate supply-chain risk.

It is not required by Composer consumers. A no-write Vite build (`build.write=false`, `emptyOutDir=false`) with a module-inventory hook showed **no jsdom module in any emitted runtime chunk**. No tracked output was written.

### robust-predicates: real browser runtime

The actual 3.0.3 path is:

`@turf/boolean-intersects@7.4.0` → `@turf/boolean-disjoint@7.4.0` → `@turf/boolean-point-in-polygon@7.4.0` → `point-in-polygon-hao@1.2.4` → `robust-predicates@3.0.3`.

`resources/js/map/sources.js:406` dynamically imports Turf boolean-intersects for spatial selection. `point-in-polygon-hao/dist/esm/index.js` imports `orient2d` from robust-predicates. The no-write Vite module inventory confirms 3.0.3 ESM util/orientation/incircle/insphere code contributes to `chunks/esm-TI1_uB6p.js`. **The UMD minified distribution is not the selected module entry.** This package does reach consumers through the tracked Map runtime; it cannot be dismissed as development-only.

There is also a distinct `robust-predicates@2.0.4` at the top-level node_modules path through Turf line-intersect. Its presence was recorded to avoid inspecting the wrong package. The Socket warning under review is for nested **3.0.3**; the detailed archive/source comparison above intentionally targets that version.

## Final release disposition and limitations

- **Both obfuscation findings have a documented benign classification for these exact versions and exact trigger files.** The former lack of file details is resolved. No further user input or private dashboard access is needed for this targeted assessment.
- No archive substitution, installed-byte tampering, hidden downloader, decoder or malicious execution was found. The conclusion rests on actual trigger-file review, not package reputation, npm audit results, test success or speculation about minified UMD output.
- Retaining `jsdom@30.0.1` and `robust-predicates@3.0.3` for this release is supported by the reviewed evidence; these two specific obfuscation warnings do not establish a reason to block publication or replace the dependencies.
- Preserve this review and public alert links in the release evidence. No Socket ignore command, global rule suppression or GitHub comment has been issued. The dashboard classification itself is not claimed to have changed.
- Exposure remains different: jsdom executes in developer/CI tests, while robust-predicates ESM reaches the Map runtime. The benign finding does not erase those trust boundaries.
- Scope remains targeted, not exhaustive verification of jsdom's approximately 7 MB distribution or every transitive dependency. Full Sigstore transparency/certificate verification and upstream build reproduction were not performed. Registry signatures, SRI, installed files and shared upstream source bytes were verified as stated above. Public Socket detail observations were provided by the coordinating agent's browser inspection and then independently checked against the full local trigger files.
