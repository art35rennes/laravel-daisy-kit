# v6 release validation

Status: candidate validation, **not published**. This record is updated as promotion gates finish; successful earlier candidates do not replace checks on the final stable commit.

## Baseline and candidate

- Package baseline: `c1fbea024bd09587454f627ff00266f584b45a51` on `dev`.
- Demo baseline: `a030860d83753a9c7916b78f1d5d37d6ea8132dc` on `dev`; the five existing modified files were preserved and reviewed.
- Current runtime candidate: `0a90b5c4835a09d0808e3a1384bb7433254ef0f7`.
- Public package promotion: https://github.com/art35rennes/laravel-daisy-kit/pull/1.
- The demo uses the remote VCS branch with an exact source commit in `composer.lock`, without a path repository or vendor edits. Its final requirement must become exact `v6.0.0` after tag verification.

## Completed evidence

The [business outcome inventory](v6-outcome-evidence.md) links the eleven modules to outcome tests and states their limits.

[Linux quality run 34755129324](https://github.com/art35rennes/laravel-daisy-kit/actions/runs/34755129324) passed on PR head `17ae400784eedb1163baa5733d2bf0a97b00cb9f` and synthetic merge `a2f57d6130d89dd6b2086a1309b6d964e3766343`:

- Clean Composer/npm installs; `composer validate --strict`.
- `npm run build`, followed by `git diff --exit-code -- dist`: reproducible tracked output.
- Workbench build, Pint verification, PHPStan, 100% type coverage.
- Pest without TIA: 125 PHP tests / 882 assertions and 39 browser tests / 397 assertions.
- Vitest: 199 tests; statements 86.99%, branches 74.85%, functions 87.97%, lines 90.65%.
- Fresh Composer/Vite consumer installation, build and served module workflows, including detached checkout support.
- Composer and npm audits: no reported vulnerabilities.

Later candidate `51ba83ca89210368802651378c56613276397a66` also passed [Linux quality run 34755561619](https://github.com/art35rennes/laravel-daisy-kit/actions/runs/34755561619). The current runtime candidate `0a90b5c4835a09d0808e3a1384bb7433254ef0f7` adds the table header text contrast correction and passed [Linux quality run 34755615057](https://github.com/art35rennes/laravel-daisy-kit/actions/runs/34755615057).

Local demo testing found and corrected secondary-text contrast in Cupcake, theme-transition timing, a stale Truncate test locator and a Signature background that made black ink unreadable in dark mode. The full browser suite is rerun after corrections: its prior failures are not treated as acceptance evidence. Coverage spans three themes and 320/768/1024/1440 px, with additional narrow Map/File Preview cases.

## Socket review

[The targeted dependency assessment](v6-socket-review.md) records both High/Warn obfuscation findings, verified archive integrity and signatures, inspected upstream sources and actual runtime exposure. No Socket rule or alert has been suppressed. The exact detection locations require authenticated dashboard access and remain unverified. Passing audit/CI checks do not close these warnings.

## Remaining promotion gates

1. Record green full demo and final candidate Linux results, and finish the Socket disposition.
2. Promote both repositories through reviewed PRs to `main`, preserving `dev` alignment and historical tags.
3. Validate final package `main`, create immutable `v6.0.0`, and keep the GitHub release draft until remote-tag verification succeeds.
4. Install the remote tag in a fresh host and demo, rerun required controls, then tag the validated demo.
5. Publish stable GitHub Latest; verify anonymous repository/tag/archive access and Composer resolution. No Packagist/npm publication or demo hosting.

After a public tag exists, never move it. A failed published version is corrected by a new patch version with explicit release notes.
