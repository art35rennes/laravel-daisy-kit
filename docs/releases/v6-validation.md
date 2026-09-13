# v6 release validation

This is the pre-publication acceptance record. The GitHub stable release includes a publication receipt with final commits, tag checks and public installation verification. Successful earlier candidates do not replace checks on the final stable commit.

## Baseline and candidate

- Package baseline: `c1fbea024bd09587454f627ff00266f584b45a51` on `dev`.
- Demo baseline: `a030860d83753a9c7916b78f1d5d37d6ea8132dc` on `dev`; the five existing modified files were preserved and reviewed.
- Initial accepted runtime candidate: `0a90b5c4835a09d0808e3a1384bb7433254ef0f7`.
- Public package promotion: https://github.com/art35rennes/laravel-daisy-kit/pull/1.
- The demo uses Composer VCS without a path repository or vendor edits. Its SemVer requirement and exact tag/source in `composer.lock` are both checked; the first public stable delivery is `v6.0.1`.

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

Local demo candidate `04ae7bee33138ac13d21a8d370dbf83ab0a8bb9d` with package `0a90b5c4835a09d0808e3a1384bb7433254ef0f7` passed `composer run test:release`: **59 Feature/Architecture tests / 316 assertions; 157 browser tests / 1015 assertions**. No TIA or accessibility-rule exclusions were used. The contrast changes received independent review without remaining findings.

## Socket review

[The targeted dependency assessment](v6-socket-review.md) records both High/Warn obfuscation findings, verified archive integrity and signatures, inspected upstream sources and actual runtime exposure. No Socket rule or alert has been suppressed. The exact detection locations were obtained from the public Socket pages and fully inspected: both findings are classified benign for the locked versions. The analysis covers textarea implementation and robust orientation arithmetic, including util.js; it does not rely on passing audit/CI checks.

## Patch required after the initial tag

The immutable `v6.0.0` package tag points to `5e39be7bcd72d73fe562587f58fc902aa6ca3e23`. Its main and tag Linux checks passed, as did fresh remote-tag installation and anonymous Composer resolution. Its GitHub release remained a draft.

A subsequent demo run exposed a race: a pending debounced search could rerender an open Table cell editor and restore its initial value, losing unsaved text. The original demo test also failed to await its filtered result before editing. The patch preserves the editing draft through rerenders and adds a controlled-timer regression; the demo separately awaits the actual filtered result. This is a correctness fix, with no new public interface.

The tag is not moved. `v6.0.1` receives the correction and the complete release gates again, then becomes the first stable public GitHub release of v6. The final publication receipt records its exact package/demo commits and CI runs. `v6.0.0` is affected and consumers should update to at least `v6.0.1`.
