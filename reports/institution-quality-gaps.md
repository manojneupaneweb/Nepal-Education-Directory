# Institution quality gaps

Date: 2026-11-09  
Source: `php artisan institutions:quality-gaps` on the local directory database. No institution field was filled in.

| Gap | Count |
| --- | --- |
| Published institutions | 22,579 |
| Published schools | 21,154 |
| Schools without a public phone or email | 19,417 |
| Schools without an http(s) official website | 20,897 |
| Schools whose description is under 80 characters | 13,556 |
| Same municipality and same normalized name | 0 groups |

These numbers are missing stored fields. They are not a list of facts to invent.

## How a sourced fact can be stored

`InstitutionEvidenceRecorder` keeps a proposal in `institution_field_evidence` with its source URL and `last_checked_at`. The institution column or contact stays unchanged until `accept()` is called by a reviewer.

Acceptance writes the value only when that field is empty. A different stored value is left as it is. The change log records the source URL. The institution becomes `source_matched`, not `institution_verified`.

Allowed fields: official website, address, description, phone, email, and a named principal. Each value has to be usable, and the source has to be an http or https URL. There is no command that crawls the web or drafts a description.

A query-builder bulk update does not exist for institutions. Model create, update, delete, and restore still clear the directory count cache through the existing observer.
