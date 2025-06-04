# LorPHP Schema System

This directory contains the entity schema definitions for your application. Each entity (User, Organization, Role, etc.) is defined in its own JSON file. These schemas are used to generate models, interfaces, migrations, and other code artifacts.

## Directory Structure

- `bakedInFieldsDefinition.json`: Common fields (like `id`, `created_at`, etc.) shared by all entities.
- `User.json`, `Organization.json`, etc.: One file per entity, describing its fields and relationships.

## How It Works

- Each entity file describes the fields, types, relationships, and options for that entity.
- The generator scripts read all JSON files in this directory and merge them to build the full schema.
- Changes to these files will be reflected in generated code after you rerun the generators.

## Field Properties

Each field in an entity schema can have the following properties:

- `type`: The data type (e.g., `String`, `Boolean`, `DateTime`, `Integer`, or another entity name for relationships).
- `attributes`: Array of special attributes (e.g., `@id`, `@unique`, `@default(...)`).
- `nullable`: Boolean, whether the field can be null.
- `isForeignKey`: Boolean, whether the field is a foreign key.
- `relationship`: For relationship fields, one of `one-to-one`, `one-to-many`, `many-to-one`, or `many-to-many`.
- `relationDetails`: For relationships, describes the linking fields and references.
- `description`: Human-readable description of the field.
- `default`: Default value for the field (if any).
- `methods`: (For relationships) Custom methods to generate for this relationship.

## Example: User.json

```json
{
  "description": "Represents a user in the system",
  "fields": {
    "name": { "type": "String" },
    "email": { "type": "String", "attributes": ["@unique"] },
    "password": { "type": "String" },
    "role_id": { "type": "String", "nullable": true },
    "organization_id": { "type": "String", "isForeignKey": true },
    "role": {
      "type": "Role",
      "relationship": "many-to-one",
      "relationDetails": {
        "fields": ["role_id"],
        "references": ["id"]
      },
      "description": "The role of this user"
    },
    "organization": {
      "type": "Organization",
      "relationship": "many-to-one",
      "relationDetails": {
        "fields": ["organization_id"],
        "references": ["id"]
      },
      "description": "The organization this user belongs to"
    }
  },
  "useBakedInFields": true
}
```

## Relationships

- `one-to-one`: Each record in this entity relates to one record in the target entity.
- `one-to-many`: This entity can have multiple related records in the target entity.
- `many-to-one`: Many records in this entity relate to one record in the target entity.
- `many-to-many`: Many records in this entity relate to many records in the target entity.

### Example Relationship

```json
"organization": {
  "type": "Organization",
  "relationship": "many-to-one",
  "relationDetails": {
    "fields": ["organization_id"],
    "references": ["id"]
  },
  "description": "The organization this user belongs to"
}
```

## Adding or Modifying Entities

1. Create or edit the relevant JSON file in this directory.
2. Follow the structure shown above for fields and relationships.
3. Run the generator script (e.g., `php bin/generate-interfaces.php`) to update code artifacts.

## Notes

- All entity files must be valid JSON.
- The generator will ignore files that do not end with `.json` or are not valid entity definitions.
- `bakedInFieldsDefinition.json` is automatically included in all entities that set `"useBakedInFields": true`.

---

For more details, see the generator scripts or ask your team lead.
