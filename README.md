# Neighborly

Neighborly is a production-grade **Residential Community Management System** designed to solve the transparency and trust issues common in housing cooperatives and building communities. Unlike simple CRUD applications, Neighborly enforces strict **Role-Based Access Control (RBAC)**, **contextual data scoping**, and **immutable audit trails** to ensure secure and accountable management of community resources.

Built with **Laravel 12**, **Vue 3**, and **Inertia.js**, it demonstrates a security-first architecture where authorization is strictly enforced on the backend while maintaining a seamless, modern frontend experience.

## Core Features

- **Authentication & Identity**: Robust manual account verification workflow. New registrations require Admin approval before accessing any community data.
- **Strict Role-Based Access Control (RBAC)**: Granular permissions for Admins, Board Members, Building Managers, Accountants, Residents, and Service Providers.
- **Hierarchical Data Scoping**: Data is strictly scoped at the **Community**, **Building**, and **Unit** levels. Users can never access data outside their assigned scope.
- **Targeted Communication**:
  - **Announcements**: Publish news to specific audiences (e.g., specific buildings, roles, or the entire community).
  - **Polls**: Conduct voting with strict eligibility rules (e.g., only residents of Building A can vote on Building A repairs).
- **Maintenance Workflow**: Ticket submission, assignment to service providers, and status tracking (Open, In Progress, Resolved, Rejected).
- **Financial & Document Management**: Secure access to financial overviews and community documents, restricted by user role.
- **Immutable Audit Logging**: Critical actions (status changes, deletions, sensitive edits) are recorded in tamper-evident logs for transparency.

## Role & Access Matrix

The system enforces a deny-by-default security model. Access is granted explicitly via Policies.

| Role | Scope | Key Capabilities |
| :--- | :--- | :--- |
| **Admin** | Global | Full system access. User verification. Audit log review. Configuration. |
| **Board Member** | Community | Manage all buildings/units in their community. Create/Manage announcements & polls. View maintenance/finances. |
| **Building Manager** | **Building** | Manage *only assigned* buildings. Create announcements/polls *only* for their buildings. View residents of *their* buildings. |
| **Accountant** | Community | View/Upload financial records. Read-only access to resident lists for billing purposes. |
| **Resident** | Unit | View community news/polls. Vote on eligible polls. Submit maintenance requests. View own docs. |
| **Service Provider** | Job | View assigned maintenance tickets. Update ticket status. (No access to resident data). |

> **Note on Scoping**: A Building Manager assigned to "Building A" receives a `403 Forbidden` if they attempt to access "Building B", even if they manipulate the ID in the URL. This is enforced by backend Policies, not just frontend hiding.

## Permission Architecture

Neighborly prioritizes **Backend Authority**. The frontend UI is treated as untrusted.

1.  **Policies & Gates**: Every controller action is guarded by a Laravel Policy (e.g., `AnnouncementPolicy`, `BuildingPolicy`).
2.  **Model Scopes**: Data visibility is enforced via Eloquent Scopes (e.g., `Announcement::visibleTo($user)`). This ensures queries never return unauthorized records, preventing data leakage.
3.  **Middleware**: `auth`, `verified`, and custom role middleware protect routes at the entry point.
4.  **UI State**: The frontend receives only the data allowed by the user's role. Buttons and links are hidden conditional on permissions, but this is purely for UX.

## Data Model Overview

The domain model reflects real-world property structures:

- **Community**: The top-level entity (e.g., "Sunshine Estates").
- **Building**: Physical structures belonging to a Community.
- **Unit**: Individual apartments/houses within a Building.
- **User**: The actor.
  - Users belong to a **Community**.
  - Users can be assigned to **Units** (Residents/Owners).
  - Building Managers are assigned to **Buildings** via a `building_user` pivot table.

## Security & Auditability

- **Immutable Logs**: A dedicated `audit_logs` table tracks who did what and when. This is critical for resolving disputes in community management.
- **Data Redaction**: Sensitive user data is redacted in logs and API responses unless the requester has specific clearance.
- **CSRF & XSS Protection**: leverages Laravel's built-in protections.
- **Validation**: Strict server-side validation using Form Requests.

## Frontend Architecture

The frontend is a Monolith-like Single Page Application (SPA) using **Inertia.js**.

- **Vue 3 (Composition API)**: Clean, reactive components using `<script setup>`.
- **Inertia.js**: Acts as the bridge, allowing us to build a modern SPA without building a separate API. Routing is server-driven.
- **Tailwind CSS**: Utility-first styling for a custom, professional, and responsive design.
- **No External UI Kits**: All components (Tables, Modals, Forms) are built from scratch to ensure full control and zero bloat.
- **Type Safety**: TypeScript is used for critical configurations and component props.

> **Design Choice**: We intentionally avoid `Ziggy` or `route()` helpers in Vue to decouple the frontend build from backend route names, preferring explicit path definitions or passed props.

## Internationalization (i18n)

The application fully supports **English (EN)** and **Polish (PL)**.

- **Backend-Driven**: Translation strings live in Laravel's `lang/` directory.
- **Shared Props**: Inertia shares the translation catalogue (or relevant subsets) with the frontend.
- **Usage**: All user-facing text uses a `t('key')` helper. No hardcoded strings exist in the views.

## Tech Stack

| Component | Technology |
| :--- | :--- |
| **Framework** | Laravel 12 |
| **Language** | PHP 8.3+ |
| **Frontend** | Vue 3 + TypeScript |
| **Glue** | Inertia.js |
| **Database** | MySQL / SQLite (Testing) |
| **Styling** | Tailwind CSS |
| **Authorization** | Spatie Laravel Permission + Native Policies |

## Project Status & Roadmap

- [x] **Core Authentication & Verification**
- [x] **Community/Building/Unit Data Structure**
- [x] **Role-Based Access Control Implementation**
- [x] **Maintenance Requests Module**
- [x] **Announcement & Polling System (with Targeting)**
- [x] **Building Manager Scope Enforcement**
- [x] **Audit Logging**
- [ ] *Notification System (Email/SMS)*
- [ ] *Mobile App (API Development)*
- [ ] *Stripe Integration for Rent Payments*

---
*Neighborly is a portfolio project demonstrating advanced Laravel architecture patterns.*
