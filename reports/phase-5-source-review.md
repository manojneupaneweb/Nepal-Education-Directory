# Phase 5 source review

Date: 2026-11-09

These counts come from `php artisan institutions:quality-gaps`. No website was crawled and no institution field was filled by the command.

## Queue

Priority is the order a reviewer should open records that already have an official website. It is not a calculated score.

1. Published schools with a website and no public phone: 33
2. Published schools with a website and no public email: 154
3. Published schools with a description under 80 characters: 13556
4. Published colleges with a website and no public phone: 85
5. Published colleges with a website and no public email: 169

Schools without a website: 20897. Those stay out of this review queue.

## Evidence

| Count | Value |
| --- | --- |
| Institutions with at least one proposal | 0 |
| Source pages stored | 0 |
| Proposals | 0 |
| Pending | 0 |
| Accepted | 0 |
| Rejected | 0 |
| Accepted by field | None |

A proposal that finds the destination already filled stays pending. That outcome is not a separate counter; it remains in the pending total.

## Directory

Published institutions: 22579. Schools: 21154. Colleges: 1424. Same municipality and normalized name groups: 0.
