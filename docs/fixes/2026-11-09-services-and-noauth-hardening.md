# Harden `services` and the other NOAUTH pages

2026-11-09, branch `hotfix/services-noauth-hardening`, based on `master` @ `1d8e111`. No code change is needed in R2P2
or EM Manager. The REDCap Notifications API module needs one configuration change, made **before** this deploys
([steps](#deploy)).

## Problem

Our Apache config lets `/api/?…&NOAUTH` and `/redcap_vX/ExternalModules/?…&NOAUTH` through OIDC, so the module's four
`no-auth-pages` can be reached from the internet. Each item below was reproduced locally first, on `1d8e111`. R2P2
traffic went to a local stand-in, never to the real portal.

| Where | Before |
| --- | --- |
| `cron/portal/process_ems` | Anyone could run it. It made the server ask R2P2 to replicate charges and process RMA charges, and push custom charges |
| `cron/entity/check_overdue_payments` | Anyone could run it, for any project, and it wrote overdue-payment rows, including for development projects |
| `ajax/notifications/get_projects` | Anyone could list projects by fee and RMA status, and make the server call R2P2 once per project |
| Module constructor | Every request to one of these pages, even a refused one, made the server call R2P2 for the URL's `pid` |
| `services`: IP allowlist | Any request carrying `magic_skip_cidr` skipped it. Even without that, a blocked request was answered anyway: `returnError()` relies on `exitAfterHook()`, which doesn't end a page request |
| `services`: token | Compared with `==` after `FILTER_SANITIZE_STRING`. When `project-portal-api-token` was unset, an empty token was accepted |
| `services` → `get_redcap_record` | Returned a record from any project named in the URL |
| `services` → `update_charge_ids` | The IDs went into the SQL unquoted (`classes/Entity.php`), so a caller could run SQL. A timing probe proved it without changing data |
| `views/index.php` | Loaded a script from `polyfill.io` (a domain hijacked in 2024), and printed the project's IRB number into JS unescaped |

## What R2P2 sends

From R2P2's client code:
- It always POSTs form data with `secret_token`, and never sends `magic_skip_cidr`.
- The requests it uses are `webroot_path`, `users`, `get_redcap_enabled_ems`, `get_em_monthly_charges`,
  `update_charge_ids`, `custom_survey` and `get_redcap_record`.
- `update_charge_ids` items are `{"redcap_charge_id": <int>, "r2p2_charge_id": <int>}`.
- `get_redcap_record` is only called for R2P2's survey projects, and it reads their `portal_summarize` field.

`ajax/notifications/get_projects` is called by the REDCap Notifications API module. Each of its rules POSTs a JSON
"Request body" to the URL in "URL to Endpoint to pull Project IDs list". Those rules are **project** settings, so the
token they carry is a separate, low-privilege token rather than R2P2's.

## Fix

| File | Change |
| --- | --- |
| `ProjectPortal.php` | `isValidToken()` compares with `hash_equals()` and fails closed when the expected token is empty. `isAuthorizedServiceRequest($settingKey)` means a POST whose `secret_token` (form field or JSON body) matches the setting |
| `ProjectPortal.php` | `services`: the raw token goes to `verifyToken()`. The `magic_skip_cidr` skip is gone, and a blocked IP now ends the request |
| `ProjectPortal.php` | `get_redcap_record` only serves projects that have a `portal_summarize` field |
| `ProjectPortal.php` | The crons POST the token to their own pages (`postToSelf()`) instead of using a bare GET |
| `ProjectPortal.php` | The constructor no longer calls R2P2 for NOAUTH requests |
| `ProjectPortal.php` | `getNotificationProjects()` only accepts `project_status` 0, 1 or 2, and binds it |
| `classes/Entity.php` | `update_charge_ids` validates both IDs as positive integers and binds them. It reports when a row was not changed |
| `classes/Entity.php` | The overdue-payment queries are bound |
| `cron/portal/process_ems.php` | 403 unless authorized with `project-portal-api-token` |
| `cron/entity/check_overdue_payments.php` | 403 unless authorized with `project-portal-api-token`. Skips projects that aren't in production, and loads the R2P2 data it needs after the check |
| `ajax/notifications/get_projects.php` | 403 unless authorized with the new `notifications-api-token` |
| `config.json` | New system setting `notifications-api-token` |
| `views/index.php` | `polyfill.io` removed. The IRB number and status are written with `json_encode()` |

## Verification

All checks ran locally on REDCap 17.5.9 with PHP 8.3. Each one was run before the fix and again after it.

| Check | Before | After |
| --- | --- | --- |
| Anonymous `process_ems` | 200; replicate, process-rma and push reached R2P2 | 403, no R2P2 call |
| `process_ems` with the token (the cron's call) | 200 | 200, same R2P2 calls |
| Anonymous `check_overdue_payments` on a dev project | row created | 403, no row |
| Same, with the token | row created | skipped: not a production project |
| Anonymous `get_projects` | project list | 403 |
| `get_projects` with the right token / wrong token / invalid status | no check made | list / 403 / rejected |
| `get_redcap_record`, survey project / other project | record / record | record / refused |
| `update_charge_ids` legitimate update / SQL probe | applied / ran (15 s delay) | applied / rejected in under 0.1 s |
| IP blocked: without the skip field, with it, and with a valid token | the last two answered | all refused |
| Token unset, empty token sent | 200 | refused |
| Both crons, through `ExternalModules::callCronMethod()` | ok | ok, same R2P2 calls |
| R2P2 calls caused by refused anonymous requests | 3 per request | 0 |
| R2P2's own read requests (`webroot_path`, `users`, `get_em_monthly_charges`, `get_redcap_enabled_ems`), old code against new | (same) | identical status and response shape |

- R2P2 dashboard on desktop and iPhone 13: it loads without requesting `polyfill.io`, the tabs render, **Update
  External Modules List** works, and there are no page errors or 5xx responses.
- go_prod: the check list runs and the RMA rule answers.
- `php -l` is clean on all changed files.
- In the REDCap Module Security Scan, the `get_projects` SQL taint is gone and the `FILTER_SANITIZE_STRING` warnings
  are halved. The scan does not treat `json_encode()` as a sanitizer, so it still lists the two `views/index.php`
  lines.
- Every setting, row and project status the tests touched was restored byte-for-byte.

<a id="deploy"></a>
## Deploy (every instance that runs rit_dashboard)

Pushing to `master` deploys the dev instances within minutes. So do steps 0–3 on the dev instances as well, or expect
cron errors there.

0. **Check the IP allowlist, because it now really blocks.** Control Center → External Modules → Research IT Dashboard
   → Configure → IP Whitelist.
   - If it's empty or `0.0.0.0/0`, nothing changes.
   - Otherwise, look in the module's error log (and the alert email address, if one is set) for "Invalid source IP"
     or "Unauthorized IP trying to access". Until now those requests were still answered. After this deploy they are
     refused.
   - Behind the load balancer, REDCap sees the balancer's address, not R2P2's. If any of those entries come from
     R2P2, fix or clear the list first.
1. **Check the R2P2 token.** Control Center → External Modules → Research IT Dashboard → Configure. "Token to be used
   to get user projects or update REDCap projects with Project Portal`s link" must be set, and must equal R2P2's
   `REDCAP_TOKEN`. R2P2's calls already need it. After this change the two crons need it too; without it they fail
   with the cron-failure email.
2. **Before the deploy,** generate a new random token (40+ characters) for the Notifications API.
3. In the notifications project, open External Modules → REDCap Notifications API → Configure. In every rule whose
   "URL to Endpoint to pull Project IDs list" points at `ajax/notifications/get_projects`, add `"secret_token":
   "<new token>"` to "Request body (has to be a valid JSON Object)", keeping any keys already there. The current code
   ignores the body, so this is safe to do early.
4. Deploy this commit.
5. **Right after the deploy,** open Control Center → External Modules → Research IT Dashboard → Configure and paste
   the same value into "Token for the REDCap Notifications API module". Until you do, `get_projects` refuses those
   rules, and their project lists stop refreshing.
6. **Check from a browser that isn't logged in.** Use the instance's host, and the `pid` of any project, e.g. 16000
   on som-prod.
   - `https://<host>/redcap_vX/ExternalModules/?prefix=rit_dashboard&page=cron%2Fportal%2Fprocess_ems&NOAUTH&pid=<pid>`
     must answer `{"status":"error","message":"Forbidden"}`.
   - `https://<host>/api/?type=module&prefix=rit_dashboard&page=ajax%2Fnotifications%2Fget_projects&NOAUTH` must
     answer the same.
7. **The next day,** open Control Center → Cron Jobs, history for that date. `rit_dashboard` / `process_em_charges` and
   `project_rma_check` should have no errors. Each notification rule's project list should have refreshed (the
   REDCap Notifications API log shows "Project list"). In R2P2, survey finalize and the EM charge sync should still
   work.
