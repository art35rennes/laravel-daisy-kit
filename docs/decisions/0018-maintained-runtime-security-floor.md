# ADR-018: Patched runtimes and maintained CI trust

Accepted — 2026-10-24

Require PHP ^8.4.27 and exclude PHP 8.5.0–8.5.11. These floors address the
October 2026 PHP security releases without moving the Laravel 13 public API.
Contributors build with Node 24.22.1 or a newer patched Node 24 release.
The CI matrix covers PHP 8.4 and 8.5 with the latest patch in each branch.

Before any checkout or dependency download, GitHub-hosted Ubuntu jobs remove
all emSign roots and the AAA Certificate Services root used by the compromised
intermediates' cross-signatures. Node actions use the filtered OpenSSL trust
store; PHP, Composer and curl use that same store. This deliberately rejects
otherwise legitimate chains anchored in those roots. TLS verification remains
mandatory. Never add the excluded roots back to work around a download failure.

Pin CI actions to reviewed release commits and maintain them with Dependabot.
Weekly locked-dependency audits run independently of functional tests.

Sources:
- https://www.php.net/releases/8_4_27.php
- https://nodejs.org/en/blog/vulnerability/october-2026-security-releases
- https://support.apple.com/en-us/149555
- https://emudhra.we-have-a-problem.com/
