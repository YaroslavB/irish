# Security Policy

## Supported Versions

| Version | Supported |
| ------- | --------- |
| main    | ✓         |

## Reporting a Vulnerability

**Please do not report security vulnerabilities through public GitHub issues.**

To report a vulnerability, email the maintainer directly at the address listed in the repository profile. Include as much of the following as possible:

- Type of vulnerability (e.g. SQL injection, XSS, authentication bypass)
- Full paths of affected source files
- Step-by-step reproduction instructions
- Proof-of-concept or exploit code (if available)
- Impact assessment — what an attacker could achieve

You should receive an acknowledgement within **48 hours** and a status update within **7 days**. If you have not heard back, follow up to ensure the original message was received.

## Scope

This project is a Symfony 6 / PHP 8.1 e-commerce application. Areas of particular interest:

- Authentication and session handling (`/login`, `/register`, cart session)
- Order placement and payment flow
- Admin panel (`/admin`) access controls and privilege escalation
- File upload handling (product images)
- Personally identifiable information (user addresses, emails, phone numbers)

## Out of Scope

- Vulnerabilities in third-party dependencies that have a published upstream fix — please report those to the relevant project
- Findings from automated scanners with no demonstrated impact
- Denial-of-service attacks requiring significant resources

## Preferred Languages

English or Ukrainian.
