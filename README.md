# InuarAttributeGroupReadOnlyBundle

A Symfony bundle for **Akeneo PIM Community Edition** that allows you to mark attribute groups as read-only, preventing users from editing any attribute values belonging to those groups on the
product edit form.

---

## Features

- Toggle any attribute group as **read-only** directly from the attribute group edit page (same UI pattern as Data Quality Insights activation).
- Read-only enforcement is **visual** (fields are disabled in the product edit form) and **backend** (values stripped before save, protecting against API and CLI writes).
- The read-only state is stored in a dedicated database table and survives cache clears.
- Automatically cleans up the read-only flag when an attribute group is deleted.

---

## Requirements

| Dependency       | Version     |
|-----------------|-------------|
| PHP             | ^8.1        |
| Symfony         | ^5.4 / ^6.x |
| Akeneo PIM CE   | ^7.0        |
| Doctrine DBAL   | ^3.x        |

---

## Installation

---
1. Install the bundle
2. Register the bundle

Add the bundle to config/bundles.php:
```php
return [
    // ... other bundles
    Inuar\Bundle\AttributeGroupReadOnlyBundle\InuarAttributeGroupReadOnlyBundle::class => ['all' => true],
];
```
---
3. Register the routes

Add the following to config/routes/routes.yml:
```yml
inuar_attribute_group_readonly:
    resource: "@InuarAttributeGroupReadOnlyBundle/Resources/config/routing.yml"
```
---
4. Run the database migration
```bash
bin/console doctrine:migrations:migrate
```
This creates the inuar_readonly_attribute_group table used to store which attribute groups are flagged as read-only.

---
5. Install assets and rebuild the frontend

```bash
bin/console assets:install --symlink
bin/console pim:installer:dump-require-paths
yarn run webpack
bin/console cache:clear
```
---
Usage

1. Go to Settings → Attribute Groups in the Akeneo UI.
2. Open any attribute group.
3. In the Properties tab, find the Read-Only section.
4. Toggle the switch to enable or disable read-only mode for that group.
5. All attributes belonging to that group will be immediately locked on the product edit form — visually disabled with a notice, and protected at the backend level.

---
How it works

Backend enforcement

The bundle decorates Akeneo's pim_catalog.updater.product service. On every product save (UI, API, or import), it queries the read-only attribute groups, resolves all attribute codes belonging
to those groups, and strips those values from the update payload before delegating to the original updater.

Frontend enforcement

A form extension listens to the pim_enrich:form:field:extension:add event on the product edit form. For each field whose attribute belongs to a read-only group, it calls
field.setEditable(false) and appends a footer note: "This attribute is read-only and cannot be edited."

Database

A single table inuar_readonly_attribute_group stores the codes of read-only attribute groups:
```sql
CREATE TABLE inuar_readonly_attribute_group (
    attribute_group_code VARCHAR(100) NOT NULL,
    PRIMARY KEY (attribute_group_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```
