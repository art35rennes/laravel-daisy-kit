# Maintenance — 24 October 2026

Scope: `art35rennes/laravel-daisy-kit` and `art35rennes/laravel-daisy-kit-demo`.

## Security update

Both Composer locks now resolve Laravel 13.38.0, CommonMark 2.10.4 and Symfony
HTTP Foundation/Yaml 8.1.9. The demo consumes the published kit v6.1.3 from its
Git VCS repository. The kit's own updated assets still need a package release
before they can reach that consuming application.

Vite is 8.3.5 in both projects and the fresh-host fixture. Vitest and coverage-v8
are updated together to 4.1.12. Compatible Composer and npm updates were applied;
no direct Composer updates remain within the existing major constraints.

PHP must be at least 8.4.27; PHP 8.5.0–8.5.11 is explicitly excluded. Node builds
use 24.22.1. These are the security releases of the week, not a claim that updating
a lock file updates an installed interpreter or a PHP-FPM service.

## eMudhra trust policy

CI removes all emSign roots, including emSign ECC Root CA - G3, and the AAA
Certificate Services root used by the affected intermediates' cross-signatures.
It validates the resulting trust store before checkout. Node actions use
`--use-openssl-ca`; PHP's `openssl.cafile`/`curl.cainfo`, Composer and npm point at
that filtered store. Removing AAA Certificate Services can also reject legitimate
chains; replace those certificates rather than disable TLS validation.

Local dependency downloads after the incident warning used a task-scoped CA bundle
filtered by certificate subject to exclude eMudhra/emSign and AAA Certificate
Services. PHP's actual Herd CA file was then filtered as well, because the Herd
runtime's curl CA selection did not follow the task's scanned-ini override. Its
original was backed up to `/tmp/daisy-herd-ca-before-filtering.pem`. A Herd update
may replace this bundle, so verify it again after updates. Homebrew PHP 8.5 also
uses its own filtered `ca-no-emudhra.pem`, configured in
`/opt/homebrew/etc/php/8.5/conf.d/99-emudhra-distrust.ini`. The global macOS trust
store was not changed; that requires separate operating-system maintenance.

Before deployment, update the operating system/container CA package and verify the
actual trust store used by PHP-FPM, Composer, curl and Node. Apple framework
revocations do not establish that a standalone OpenSSL/Herd bundle is safe.
Never use `NODE_EXTRA_CA_CERTS` to re-add these authorities, `strict-ssl=false`,
`verify=false`, `secure-http=false`, or `NODE_TLS_REJECT_UNAUTHORIZED=0`.

## CI and release procedure

Checkout 7.0.1, setup-node 7.1.0, upload-artifact 7.0.2 and setup-php 2.40.0 are
pinned to their verified upstream release commits. Existing inputs are supported;
the repositories use GitHub-hosted Ubuntu runners and no registry token fallback
or unsafe pull-request checkout. Checkout credentials are not persisted.

Quality runs against the latest PHP 8.4 and 8.5 patches and rejects known unpatched
PHP versions. Weekly Monday lock audits run separately from functional tests.
Dependabot checks Composer, npm, GitHub Actions and the kit's npm fixture weekly;
major updates remain separate from grouped compatible updates.

1. Review and commit each repository's maintenance patch, including tracked kit
   `dist/` assets. Run the new quality matrix on GitHub before merging.
2. Release a kit patch from the validated commit, then update the demo's Composer
   lock to that immutable tag and repeat its full release tests. Its current
   v6.1.3 lock does not contain this uncommitted maintenance patch.
3. Update deployment PHP CLI and PHP-FPM to 8.4.27+ or 8.5.12+, rebuild dependencies
   with the filtered trust store, and deploy the validated demo artifact.
4. Check interpreter versions, platform requirements and TLS rejection from the
   deployed runtime. Keep a previous **patched** release artifact for rollback;
   never restore a vulnerable interpreter or trust bundle.

Only quality/TIA workflows exist in the inspected repositories; no deployment
provider or production credentials were available to validate a server rollout.
The existing remote main pipelines were successful before these local changes.
GitHub's Dependabot alert endpoints returned HTTP 403 for both repositories,
reporting alerts disabled and missing `admin:repo_hook` authorization. The new
Dependabot configuration does not itself enable those repository settings;
enable dependency graph/security alerts and security updates in GitHub settings.
The independent weekly Composer/npm audit jobs remain the repository-owned gate.

## Remaining runtime work

Herd eventually completed its asynchronous updates: installed CLI and FPM binaries
now report PHP 8.4.27 and PHP 8.5.12. The demo's normal Composer invocation
initially selected separate Homebrew PHP 8.5.8. Homebrew PHP was also upgraded to
8.5.12, which its normal `php` and Composer invocations now select. Final full
tests explicitly select Herd PHP 8.4.27 for Composer and every subprocess; the
kit's 138 non-browser tests additionally pass on Herd PHP 8.5.12. The demo's 59
feature/architecture tests also pass on the updated Homebrew PHP 8.5.12.
Ensure the developer shell and the deployment service select patched binaries;
merely having a patched binary installed is insufficient.

No production service or remote PHP-FPM process was inspected or restarted.
The installed Herd FPM binaries report 8.4.27 and 8.5.12. Check and restart any
already-running service separately so it loads its newly installed binary.

Homebrew installed PHP 8.5.12 successfully, but its secondary `pkgconf` reinstall
failed because local Xcode 26.1.1 does not meet the required Xcode 27.0 floor.
PHP version checks pass. Plan the Xcode major update separately, then rerun
`brew reinstall pkgconf`; do not remove Xcode or change the developer-tool selection
as part of this repository patch.

Node 24.22.1 was downloaded from nodejs.org and its archive SHA-256 was verified
against the official release checksum list. It is now installed in Herd's existing
nvm and selected as its default. Existing shells may retain the old PATH: run
`nvm use` in each repository, using the committed `.nvmrc`, or open a new shell.
Composer was updated from 2.10.2 to 2.10.3 using its signature-verifying self-updater.

## Planned migrations

Apply one migration per reviewable change, after the security patch is released.
The existing locks and tests are the rollback baseline; every acceptance gate runs
with TIA disabled.

| Order | Migration | Work and acceptance gate |
| --- | --- | --- |
| 1 | Vitest and coverage-v8 4.1.12 → 5.0.5 | Read the v5 migration guide, upgrade the pair, review mock cleanup defaults and changed test-name matching, verify all 248 JavaScript cases and coverage output, then rerun browser suites. Node/Vite prerequisites are already satisfied. |
| 2 | match-sorter-utils 8.19.4 → 9.2.6 | Review ranking/type/API changes. Preserve Combobox and Transfer List search ranking, accent/case handling, keyboard selection and selected-value submission through existing outcome tests. Rebuild dist and validate a fresh Composer/Vite host. |
| 3 | Prettier PHP plugin 0.25.0 → 0.26.0 | Treat this pre-1.0 minor as a breaking change. Review the standalone/browser entry and worker/node imports; validate PHP formatting, selection preservation, lazy loading and CSP in Code Editor. |
| 4 | PHP 8.5 as the default | First obtain 8.5.12+ on developer and server runtimes, pass the added 8.5 CI matrix, inspect deprecations and extension support, then consider changing the default. Preserve PHP 8.4 support unless a separate public-contract decision removes it. |
| 5 | Node 26 | Reassess when the official release schedule marks it LTS. Trial a separate CI matrix, verify engines and reproducible dist output, then update both projects and their version files together. Node 24 remains supported LTS now. |

## Validation record

- Composer and npm audits: zero reported advisories in both projects, including
  the kit's separate npm fixture lock; no advisories were ignored.
- Both production frontend builds pass using Node 24.22.1.
- Kit: 248 JavaScript tests, 138 non-browser PHP tests and 68 browser tests; Larastan passes and type
  coverage is 100%. Type analysis now explicitly has a 1 GiB memory budget.
- Demo: 59 feature/architecture tests and 157 browser tests pass; Pint passes.
- Fresh VCS host integration passes for the original committed package HEAD
  `f1b2d7f58e0e52af9d56dc53ee57b796863c9bee`. The integration intentionally tests
  committed source; rerun it after committing this patch to validate the new dist.
- Node 24.22.1 rejected both public incident test chains with `CERT_REVOKED`;
  patched Herd PHP with the filtered CA bundle rejected both with curl error 60.
  The macOS system curl test was inconclusive and is not counted as proof of
  remediation.
- Workflow syntax, expressions and action inputs checked with actionlint 1.7.12.
  The actionlint download was verified against its GitHub-published SHA-256.
- The new GitHub workflows have not run remotely for this uncommitted patch.

## Sources

- [PHP 8.4.27 security release](https://www.php.net/releases/8_4_27.php)
- [Node October security releases](https://nodejs.org/en/blog/vulnerability/october-2026-security-releases)
- [Apple's eMudhra guidance](https://support.apple.com/en-us/149555)
- [Incident report and cross-signed test chains](https://emudhra.we-have-a-problem.com/)
- [Vitest 5 migration](https://vitest.dev/guide/migration/)
- [Node release status](https://nodejs.org/en/about/previous-releases)

## Release preparation

Maintenance was synchronized with the latest remote main before the v6.1.4
release. This preserves previously published table, Combobox, agent-resource and
thirteen-page demo changes. CI keeps the upstream Ubuntu 24.04 pin and concurrency
settings. Full validation is repeated on this combined source and its committed
fresh-host installation before publication.
