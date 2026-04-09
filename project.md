Act as a senior startup CTO, Laravel architect, product manager, system designer, database architect, UI/UX strategist, and full-stack engineering lead.

Your job is to design a complete, production-oriented, full-fledged web application blueprint for our startup: daimaaa.com.

You must think like a real engineering lead building a startup MVP that can go live on normal cPanel shared hosting without SSH access.

==================================================
CRITICAL DEPLOYMENT CONSTRAINT
==================================================

This project will be deployed on:
- normal cPanel shared hosting
- no SSH access
- no terminal access
- file upload via cPanel File Manager or FTP
- MySQL via cPanel
- phpMyAdmin for database import if needed

Therefore, the architecture MUST be compatible with this environment.

Do NOT suggest:
- Next.js
- separate React frontend
- microservices
- docker-only workflows
- kubernetes
- queues that require supervisor setup for MVP
- architecture that depends on running server commands after deployment
- systems that require Redis, Node server, websocket server, or background workers as mandatory launch requirements

==================================================
REQUIRED TECH STACK
==================================================

Use this stack for the MVP:

- Backend: Laravel latest stable
- Frontend: Blade + Livewire + Alpine.js
- Language: PHP
- Styling: Tailwind CSS
- Database: MySQL
- Admin panel: inside the same Laravel application
- Authentication: Laravel starter auth or simple Laravel auth flow
- Architecture: Laravel monolith
- Hosting target: cPanel shared hosting without SSH

Important:
- prefer simplicity, maintainability, and deployment compatibility
- use Livewire where dynamic behavior is needed
- use Blade where pages are mostly static/content-driven
- design the app so it can later be upgraded to VPS/cloud without major rewrite

==================================================
BUSINESS OVERVIEW
==================================================

Startup name/domain:
daimaaa.com

Business concept:
We provide traditional Indian pre-pregnancy and post-pregnancy care services for mothers and newborn babies through experienced “Daimaa” caregivers. We offer services such as:
- mother massage
- newborn baby massage
- baby bath
- post-pregnancy care
- mother and baby combo sessions
- add-on services
- bundles and packages
- home visit based care

Our brand is about:
- trust
- warmth
- mother care
- newborn care
- traditional Indian wisdom
- experienced daimaa caregivers
- safety, hygiene, and comfort at home

==================================================
PRODUCT GOALS
==================================================

The application should support these three sides of the business:

1. CUSTOMER SIDE
- discover services
- view packages
- select city/pincode
- book service
- choose session/package/add-ons
- submit address
- schedule appointment
- pay or mark booking as pending/manual confirmation
- track booking status
- reschedule or cancel
- submit rating/review

2. DAIMAA SIDE
- register profile
- full name
- mobile number
- year of experience
- address
- service area
- services offered
- availability
- upload KYC documents
- admin verification
- assigned bookings
- session status updates
- payout visibility

3. ADMIN / OPERATIONS SIDE
- manage daimaas
- verify KYC
- manage customers
- manage services
- manage packages
- manage add-ons
- manage bundles
- manage price rules
- manage booking lifecycle
- assign daimaas
- manage pincode/city serviceability
- manage coupons
- manage static content/CMS
- manage testimonials/FAQs
- review reports
- maintain audit history

==================================================
PLANNING INSTRUCTIONS
==================================================

Generate a full implementation blueprint, not just ideas.

Be specific.
Use practical names.
Use real database tables.
Use realistic route names.
Use status enums.
Use modular architecture.
Use startup-friendly decisions.
Mark MVP vs phase 2 clearly.

Assume:
- first launch in one city, then multiple cities later
- Indian users are mobile-first
- trust and verification matter a lot
- manual operations support may be needed in the beginning
- customer support may initially happen over phone/WhatsApp
- some bookings may require admin confirmation
- some pricing may be manually controlled in phase 1

==================================================
DELIVERABLES - GENERATE IN THIS ORDER
==================================================

1. EXECUTIVE SUMMARY
Provide:
- product summary
- why this architecture is best for no-SSH cPanel hosting
- MVP goal
- technical direction
- what should be built first

2. RECOMMENDED FINAL STACK
Explain why the following stack is best:
- Laravel
- Blade
- Livewire
- Alpine.js
- Tailwind CSS
- MySQL
- same-app admin panel

Also explain:
- what Blade should handle
- what Livewire should handle
- what should NOT be overbuilt in MVP

3. USER ROLES
Define all roles in detail:
- guest
- customer
- daimaa applicant
- verified daimaa
- admin
- operations manager
- super admin

For each role include:
- permissions
- access boundaries
- dashboard visibility
- actions allowed

4. MVP VS PHASE 2 TABLE
Create a table:
- feature
- MVP or phase 2
- priority
- business reason
- technical note

5. INFORMATION ARCHITECTURE
List all screens and pages grouped by:

A. Public website
- home
- about
- how it works
- services
- packages
- cities
- pincode availability
- daimaa trust page
- FAQs
- testimonials
- contact
- privacy policy
- terms
- refund policy
- SEO landing pages

B. Customer area
- register/login
- profile
- addresses
- bookings
- booking detail
- reschedule/cancel
- reviews

C. Daimaa area
- registration
- profile completion
- KYC upload
- availability
- assigned bookings
- session updates
- payouts

D. Admin area
- dashboard
- customers
- daimaas
- KYC review
- services
- packages
- add-ons
- bundles
- bookings
- assignments
- payments
- payouts
- coupons
- pincodes
- CMS pages
- FAQs
- testimonials
- settings
- audit logs

6. COMPLETE USER FLOWS
Write step-by-step flows for:
- customer registration
- customer login
- customer booking a package
- customer selecting add-ons
- customer applying coupon
- customer payment flow
- booking confirmation
- rescheduling
- cancellation
- daimaa registration
- KYC verification
- admin approving daimaa
- admin creating service
- admin creating package
- admin creating bundle
- admin assigning daimaa
- daimaa marking session started/completed
- customer review flow

7. DATABASE DESIGN
Create production-ready relational database design.

For each table provide:
- table name
- purpose
- columns
- data types
- nullable or required
- default values
- indexes
- foreign keys
- enum values/statuses

Include at minimum these tables:
- users
- roles
- user_roles
- customer_profiles
- daimaa_profiles
- addresses
- cities
- pincodes
- service_categories
- services
- packages
- package_services
- add_ons
- package_add_ons
- bundles
- bundle_items
- bookings
- booking_items
- booking_sessions
- booking_assignments
- booking_status_histories
- payments
- refunds
- payouts
- reviews
- coupons
- coupon_usages
- documents
- availability_slots
- cms_pages
- faqs
- testimonials
- notifications
- settings
- audit_logs

Also include:
- suggested status enums
- soft delete usage
- auditing fields
- useful indexes
- ERD-style text explanation of relationships

8. BUSINESS RULES
Define explicit rules for:
- who can book
- who can be assigned
- verified daimaa requirement
- serviceability by city/pincode
- package price calculation
- add-on logic
- bundle discount logic
- booking statuses
- session statuses
- payment statuses
- refund statuses
- payout statuses
- cancellation windows
- reschedule policy
- duplicate booking prevention
- review eligibility
- assignment rules
- manual approval scenarios
- manual booking support by admin

9. BACKEND ARCHITECTURE
Recommend a Laravel app structure suitable for shared hosting deployment.

Include:
- Models
- Controllers
- Livewire components
- Blade views
- Form Requests
- Service classes
- Actions
- Policies
- Middleware
- Enums
- Helpers
- Notifications

Use practical feature-based organization.

10. LIVEWIRE AND BLADE SPLIT
Clearly define what should be built with Blade and what should be built with Livewire.

Example expectations:
- Blade for public marketing pages and content pages
- Livewire for forms, filters, booking flow, dashboards, CRUD tables, assignment panels, profile editing, and admin pages

11. ROUTE PLAN
Generate route plan with:
- public routes
- auth routes
- customer routes
- daimaa routes
- admin routes

For each route group include:
- prefix
- middleware
- route names
- controller or Livewire component targets

12. CONTROLLERS / COMPONENTS PLAN
Create a list of:
- controller names
- Livewire component names
- responsibility of each
- related views
- when to use controller vs Livewire

13. FORM VALIDATION RULES
Define detailed validation rules for:
- customer registration
- daimaa registration
- mobile number
- year of experience
- address create/update
- city/pincode selection
- KYC upload
- service create/update
- package create/update
- add-on create/update
- bundle create/update
- booking create
- booking reschedule
- cancellation
- coupon apply
- review submit

14. ADMIN PANEL SPECIFICATION
Design the admin panel in detail.

For each module provide:
- screen list
- filters
- search fields
- table columns
- create/edit form fields
- row actions
- bulk actions
- permissions
- status workflow

Modules:
- dashboard
- customers
- daimaas
- KYC
- services
- packages
- add-ons
- bundles
- bookings
- assignments
- payments
- payouts
- coupons
- cities and pincodes
- CMS
- FAQs
- testimonials
- settings
- audit logs

15. UI/UX PLAN
Create full UX direction for:
- homepage hero
- trust-first messaging
- service cards
- package cards
- add-on upsell
- booking flow
- caregiver profile/trust card
- FAQs
- testimonials
- customer dashboard
- daimaa dashboard
- admin dashboard

Also define:
- color palette direction
- typography direction
- tone of voice
- icon direction
- mobile-first UX
- loading states
- empty states
- error states
- trust signals

16. BRANDING AND LOGO CONCEPTS
Suggest:
- 5 logo concepts
- color direction
- font direction
- tagline ideas
- brand adjectives
- visual style suggestions

The logo should reflect:
- mother care
- newborn care
- safety
- trust
- Indian tradition
- warmth
- premium but approachable feeling

17. SEO AND CONTENT PLAN
Suggest:
- primary SEO pages
- location landing pages
- keyword clusters
- slug structure
- metadata pattern
- FAQ schema ideas
- local SEO content
- blog ideas
- trust-building content pages

18. SECURITY AND COMPLIANCE
Design startup-friendly security:
- role-based access
- CSRF protection
- validation
- secure file uploads
- basic audit logs
- login throttling
- password reset
- consent capture
- privacy considerations for mother/baby related services
- safe admin permissions

19. PAYMENT AND NOTIFICATION ARCHITECTURE
Design how the app should support:
- online payment readiness
- manual payment confirmation if needed
- payment status handling
- refund workflow
- payout workflow
- email notifications
- SMS/WhatsApp-ready trigger points
- booking reminders
- admin alerts

20. DEPLOYMENT PLAN FOR NO SSH HOSTING
Provide a deployment plan specifically for:
- local development
- local build
- exporting database
- preparing production files
- uploading via cPanel file manager
- public folder handling
- .env configuration
- MySQL creation in cPanel
- phpMyAdmin import
- storage handling
- caching considerations
- common shared hosting risks
- how to structure the app so manual deployment is manageable

21. IMPLEMENTATION ROADMAP
Break development into milestones:
- milestone 1: project setup
- milestone 2: auth and roles
- milestone 3: database and seeders
- milestone 4: public website
- milestone 5: booking engine
- milestone 6: daimaa onboarding
- milestone 7: admin panel
- milestone 8: payments and notifications
- milestone 9: QA and deployment

For each milestone include:
- goals
- deliverables
- dependencies
- risks
- definition of done

22. CODE GENERATION ORDER
At the end, provide the exact sequence for coding this app.
Example:
1. install Laravel
2. configure auth
3. create roles
4. create migrations
5. create models
6. create seeders
7. create public pages
8. build customer booking flow
9. build daimaa onboarding
10. build admin modules
11. add coupons and reviews
12. add payment integration hooks
13. test and deploy

23. OUTPUT STYLE RULES
- write in clean markdown
- use headings
- use tables where useful
- be implementation ready
- avoid vague advice
- prefer concrete names and examples
- do not ask clarifying questions unless absolutely blocked
- make intelligent assumptions and clearly label them
- think like this will be handed to a developer to build immediately

==================================================
IMPORTANT QUALITY BAR
==================================================

Your output must be detailed enough that:
- a founder can use it as a product blueprint
- a Laravel developer can start implementation immediately
- Cursor can later generate modules one by one from this document
- the app can realistically be built and deployed on cPanel shared hosting without SSH

After generating the full blueprint, stop and wait for the next instruction.
Do not generate the actual code unless asked.