# Translation Management Service

A high-performance Laravel API service for managing multilingual translations with advanced features like tagging, search, and optimized exports.

## Features

-   ✅ Multi-locale translation storage (en, fr, es, etc.)
-   ✅ Context-based tagging system (web, mobile, desktop)
-   ✅ RESTful API with full CRUD operations
-   ✅ Advanced search and filtering
-   ✅ Optimized JSON export for frontend applications
-   ✅ Token-based API authentication
-   ✅ High-performance caching layer
-   ✅ Bulk operations support
-   ✅ Comprehensive test coverage
-   ✅ PSR-12 compliant code

## Quick Start

All API endpoints require authentication using Bearer tokens.

```bash
# Include in headers
Authorization: Bearer apiToken@12345
```

### Endpoints

#### 1. List Translations (with search/filter)

```http
GET /api/v1/translations
```

**Query Parameters:**

-   `locale` (string): Filter by language (en, fr, es, etc.)
-   `tag` (string): Filter by tag (web, mobile, desktop)
-   `key_search` (string): Search in translation keys
-   `content_search` (string): Search in translation content
-   `per_page` (int): Results per page (default: 15, max: 100)

**Example:**

```bash
curl -H "Authorization: Bearer apiToken@12345" \
     "http://localhost:8000/api/v1/translations?locale=en&tag=web&per_page=20"
```

#### 2. Create Translation

```http
POST /api/v1/translations
```

**Body:**

```json
{
    "key": "nav.home",
    "locale": "en",
    "content": "Home",
    "tags": ["web", "mobile"]
}
```

#### 3. Get Single Translation

```http
GET /api/v1/translations/{id}
```

#### 4. Update Translation

```http
PUT /api/v1/translations/{id}
```

**Body:**

```json
{
    "content": "Updated content",
    "tags": ["web", "mobile", "desktop"]
}
```

#### 5. Delete Translation

```http
DELETE /api/v1/translations/{id}
```

#### 6. Export Translations for Frontend

```http
GET /api/v1/translations/export/{locale}
```

**Example Response:**

```json
{
    "locale": "en",
    "translations": {
        "nav.home": {
            "content": "Home",
            "tags": ["web", "mobile"]
        },
        "nav.about": {
            "content": "About Us",
            "tags": ["web"]
        }
    },
    "generated_at": "2025-08-06T10:30:00.000000Z"
}
```


## Architecture & Design Decisions

### SOLID Principles Implementation

1. **Single Responsibility Principle**: Each class has a single, well-defined purpose

    - `TranslationService` handles business logic
    - Controllers handle HTTP requests/responses
    - Models handle data representation

2. **Open/Closed Principle**: Service contracts allow easy extension

    - `TranslationServiceInterface` defines the contract
    - New implementations can be easily swapped

3. **Liskov Substitution Principle**: Interfaces are properly implemented
4. **Interface Segregation**: Focused, minimal interfaces
5. **Dependency Inversion**: Dependencies injected through constructor

## Testing

### Run Tests

```bash
# All tests
php artisan test

# With coverage
php artisan test --coverage

# Specific test suites
php artisan test tests/Feature/TranslationApiTest.php
php artisan test tests/Unit/TranslationServiceTest.php
```

### Performance Testing

```bash
# Test with large dataset
php artisan translations:populate 100000
php artisan test tests/Feature/TranslationApiTest.php::test_performance_with_large_dataset
```



## Deployment

### Production Environment Setup

1. **Environment Variables**

```env
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
CACHE_DRIVER=redis
REDIS_HOST=your-redis-host
API_TOKEN=your-secure-api-token
```

### Bulk Import from CSV

```php
// Custom command for CSV import
php artisan make:command ImportTranslationsFromCsv

// In the command
$csv = Reader::createFromPath($csvPath);
$records = $csv->getRecords(['key', 'locale', 'content', 'tags']);

$translations = [];
foreach ($records as $record) {
    $translations[] = [
        'key' => $record['key'],
        'locale' => $record['locale'],
        'content' => $record['content'],
        'tags' => json_encode(explode(',', $record['tags'])),
        'created_at' => now(),
        'updated_at' => now(),
    ];
}

$this->translationService->bulkCreate($translations);
```


### OpenAPI/Swagger Documentation (swagger.yaml)

```yaml
openapi: 3.0.0
info:
    title: Translation Management API
    description: High-performance API for managing multilingual translations
    version: 1.0.0
    contact:
        name: API Support
        email: support@translationservice.com

servers:
    - url: http://localhost:8000/api/v1
      description: Development server

security:
    - bearerAuth: []

components:
    securitySchemes:
        bearerAuth:
            type: http
            scheme: bearer
            bearerFormat: JWT

    schemas:
        Translation:
            type: object
            properties:
                id:
                    type: integer
                    example: 1
                key:
                    type: string
                    example: "nav.home"
                locale:
                    type: string
                    example: "en"
                content:
                    type: string
                    example: "Home"
                tags:
                    type: array
                    items:
                        type: string
                    example: ["web", "mobile"]
                created_at:
                    type: string
                    format: date-time
                updated_at:
                    type: string
                    format: date-time

        TranslationInput:
            type: object
            required:
                - key
                - locale
                - content
            properties:
                key:
                    type: string
                    example: "nav.home"
                    maxLength: 191
                locale:
                    type: string
                    example: "en"
                    maxLength: 10
                content:
                    type: string
                    example: "Home"
                tags:
                    type: array
                    items:
                        type: string
                    example: ["web", "mobile"]

        Error:
            type: object
            properties:
                message:
                    type: string
                errors:
                    type: object

paths:
    /translations:
        get:
            summary: List translations with search and filtering
            parameters:
                - name: locale
                  in: query
                  schema:
                      type: string
                  description: Filter by locale
                - name: tag
                  in: query
                  schema:
                      type: string
                  description: Filter by tag
                - name: key_search
                  in: query
                  schema:
                      type: string
                  description: Search in translation keys
                - name: content_search
                  in: query
                  schema:
                      type: string
                  description: Search in translation content
                - name: per_page
                  in: query
                  schema:
                      type: integer
                      minimum: 1
                      maximum: 100
                      default: 15
                  description: Results per page
            responses:
                "200":
                    description: Successful response
                    content:
                        application/json:
                            schema:
                                type: object
                                properties:
                                    data:
                                        type: array
                                        items:
                                            $ref: "#/components/schemas/Translation"
                                    meta:
                                        type: object
                                        properties:
                                            current_page:
                                                type: integer
                                            last_page:
                                                type: integer
                                            per_page:
                                                type: integer
                                            total:
                                                type: integer

        post:
            summary: Create a new translation
            requestBody:
                required: true
                content:
                    application/json:
                        schema:
                            $ref: "#/components/schemas/TranslationInput"
            responses:
                "201":
                    description: Translation created successfully
                    content:
                        application/json:
                            schema:
                                type: object
                                properties:
                                    data:
                                        $ref: "#/components/schemas/Translation"
                                    message:
                                        type: string

    /translations/{id}:
        parameters:
            - name: id
              in: path
              required: true
              schema:
                  type: integer

        get:
            summary: Get a specific translation
            responses:
                "200":
                    description: Translation found
                    content:
                        application/json:
                            schema:
                                type: object
                                properties:
                                    data:
                                        $ref: "#/components/schemas/Translation"
                "404":
                    description: Translation not found

        put:
            summary: Update a translation
            requestBody:
                required: true
                content:
                    application/json:
                        schema:
                            $ref: "#/components/schemas/TranslationInput"
            responses:
                "200":
                    description: Translation updated successfully
                "404":
                    description: Translation not found

        delete:
            summary: Delete a translation
            responses:
                "200":
                    description: Translation deleted successfully
                "404":
                    description: Translation not found

    /translations/export/{locale}:
        parameters:
            - name: locale
              in: path
              required: true
              schema:
                  type: string
              example: "en"

        get:
            summary: Export all translations for a specific locale
            responses:
                "200":
                    description: Successful export
                    content:
                        application/json:
                            schema:
                                type: object
                                properties:
                                    locale:
                                        type: string
                                    translations:
                                        type: object
                                        additionalProperties:
                                            type: object
                                            properties:
                                                content:
                                                    type: string
                                                tags:
                                                    type: array
                                                    items:
                                                        type: string
                                    generated_at:
                                        type: string
                                        format: date-time
```


